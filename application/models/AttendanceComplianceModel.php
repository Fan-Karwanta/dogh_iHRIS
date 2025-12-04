<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * AttendanceComplianceModel
 * 
 * Handles schedule compliance calculations for employees
 * Standard Schedule: 8:00 AM - 5:00 PM with 12:00 PM - 1:00 PM lunch break
 * 
 * A "complete schedule" means all 4 time slots are filled:
 * - AM IN (arrival before/at 8:00 AM)
 * - AM OUT (lunch break out around 12:00 PM)
 * - PM IN (lunch break return around 1:00 PM)
 * - PM OUT (departure at/after 5:00 PM)
 */
class AttendanceComplianceModel extends CI_Model
{
    // Standard work schedule times
    const SCHEDULE_AM_IN = '08:00:00';
    const SCHEDULE_AM_OUT = '12:00:00';
    const SCHEDULE_PM_IN = '13:00:00';
    const SCHEDULE_PM_OUT = '17:00:00';
    
    // Grace period in minutes for being "on time"
    const GRACE_PERIOD_MINUTES = 15;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get all departments with personnel count
     */
    public function get_departments()
    {
        $sql = "SELECT d.*, 
                COUNT(p.id) as personnel_count
                FROM departments d
                LEFT JOIN personnels p ON p.department_id = d.id AND p.status = 1
                WHERE d.status = 1
                GROUP BY d.id
                ORDER BY d.name ASC";
        return $this->db->query($sql)->result();
    }

    /**
     * Get compliance statistics for all employees within a date range
     * 
     * UPDATED: Working days are now calculated dynamically based on actual attendance.
     * A day is counted as a "working day" if the employee has at least one clock-in entry.
     * This provides more accurate compliance rates per employee.
     * 
     * @param string $start_date Start date (Y-m-d)
     * @param string $end_date End date (Y-m-d)
     * @param int|null $department_id Filter by department (optional)
     * @return array Array of employee compliance data
     */
    public function get_compliance_statistics($start_date, $end_date, $department_id = null)
    {
        // Get all active personnel
        $this->db->select('p.id, p.bio_id, p.firstname, p.lastname, p.middlename, p.email, p.position, p.department_id, d.name as department_name, d.color as department_color');
        $this->db->from('personnels p');
        $this->db->join('departments d', 'd.id = p.department_id', 'left');
        $this->db->where('p.status', 1);
        
        if ($department_id) {
            $this->db->where('p.department_id', $department_id);
        }
        
        $this->db->order_by('p.lastname', 'ASC');
        $personnel = $this->db->get()->result();
        
        $results = array();
        
        foreach ($personnel as $person) {
            // Get biometric records for this person in the date range
            $this->db->select('*');
            $this->db->from('biometrics');
            $this->db->where('bio_id', $person->bio_id);
            $this->db->where('date >=', $start_date);
            $this->db->where('date <=', $end_date);
            $this->db->order_by('date', 'ASC');
            $records = $this->db->get()->result();
            
            // Calculate compliance metrics (includes dynamic working days count)
            $metrics = $this->calculate_compliance_metrics($records, $start_date, $end_date);
            
            // UPDATED: Working days = days with at least one clock-in entry
            // This replaces the static calendar-based working days calculation
            $working_days = $metrics['actual_working_days'];
            
            // Calculate calendar-based working days for reference (optional display)
            $calendar_working_days = $this->count_working_days($start_date, $end_date);
            
            $results[] = array(
                'personnel_id' => $person->id,
                'bio_id' => $person->bio_id,
                'name' => $person->lastname . ', ' . $person->firstname . ' ' . (!empty($person->middlename) ? substr($person->middlename, 0, 1) . '.' : ''),
                'firstname' => $person->firstname,
                'lastname' => $person->lastname,
                'position' => $person->position,
                'email' => $person->email,
                'department_id' => $person->department_id,
                'department_name' => $person->department_name ?: 'Unassigned',
                'department_color' => $person->department_color ?: '#6c757d',
                'working_days' => $working_days, // Now based on actual attendance
                'calendar_working_days' => $calendar_working_days, // Static calendar reference
                'days_with_records' => $metrics['days_with_records'],
                'complete_days' => $metrics['complete_days'],
                'incomplete_days' => $metrics['incomplete_days'],
                'absent_days' => max(0, $calendar_working_days - $metrics['days_with_records']), // Based on calendar for absent calculation
                'compliance_rate' => $working_days > 0 ? round(($metrics['complete_days'] / $working_days) * 100, 2) : 0,
                'attendance_rate' => $calendar_working_days > 0 ? round(($metrics['days_with_records'] / $calendar_working_days) * 100, 2) : 0,
                'missing_am_in' => $metrics['missing_am_in'],
                'missing_am_out' => $metrics['missing_am_out'],
                'missing_pm_in' => $metrics['missing_pm_in'],
                'missing_pm_out' => $metrics['missing_pm_out'],
                'total_missing_entries' => $metrics['total_missing_entries'],
                'late_arrivals' => $metrics['late_arrivals'],
                'early_departures' => $metrics['early_departures'],
                'failure_details' => $metrics['failure_details']
            );
        }
        
        // Sort by compliance rate descending (top performers first)
        usort($results, function($a, $b) {
            if ($a['compliance_rate'] == $b['compliance_rate']) {
                // Secondary sort by attendance rate
                return $b['attendance_rate'] - $a['attendance_rate'];
            }
            return $b['compliance_rate'] - $a['compliance_rate'];
        });
        
        return $results;
    }

    /**
     * Calculate compliance metrics for a set of biometric records
     * 
     * UPDATED: Now counts "actual working days" based on days with at least one clock-in.
     * A working day is defined as any day where the employee has at least one time entry
     * (AM IN or PM IN), regardless of whether it's a weekend or holiday.
     */
    private function calculate_compliance_metrics($records, $start_date, $end_date)
    {
        $metrics = array(
            'days_with_records' => 0,
            'complete_days' => 0,
            'incomplete_days' => 0,
            'missing_am_in' => 0,
            'missing_am_out' => 0,
            'missing_pm_in' => 0,
            'missing_pm_out' => 0,
            'total_missing_entries' => 0,
            'late_arrivals' => 0,
            'early_departures' => 0,
            'failure_details' => array(),
            'actual_working_days' => 0 // NEW: Count of days with at least one clock-in
        );
        
        $processed_dates = array();
        
        foreach ($records as $record) {
            $date = $record->date;
            
            // Skip if already processed (shouldn't happen but safety check)
            if (in_array($date, $processed_dates)) {
                continue;
            }
            $processed_dates[] = $date;
            
            // Check if at least one clock-in entry exists (AM IN or PM IN)
            // This determines if this is an "actual working day" for this employee
            $has_clock_in = !empty($record->am_in) || !empty($record->pm_in);
            
            if (!$has_clock_in) {
                continue; // Skip days without any clock-in
            }
            
            // Count this as an actual working day (has at least one clock-in)
            $metrics['actual_working_days']++;
            
            // Check if at least one time slot has data (for days_with_records)
            $has_any_entry = !empty($record->am_in) || !empty($record->am_out) || 
                            !empty($record->pm_in) || !empty($record->pm_out);
            
            if ($has_any_entry) {
                $metrics['days_with_records']++;
            }
            
            // Check completeness (all 4 time entries present)
            $is_complete = !empty($record->am_in) && !empty($record->am_out) && 
                          !empty($record->pm_in) && !empty($record->pm_out);
            
            if ($is_complete) {
                $metrics['complete_days']++;
                
                // Check for late arrival (after 8:00 AM + grace period)
                $am_in_time = strtotime($record->am_in);
                $schedule_am_in = strtotime(self::SCHEDULE_AM_IN) + (self::GRACE_PERIOD_MINUTES * 60);
                if ($am_in_time > $schedule_am_in) {
                    $metrics['late_arrivals']++;
                }
                
                // Check for early departure (before 5:00 PM)
                $pm_out_time = strtotime($record->pm_out);
                $schedule_pm_out = strtotime(self::SCHEDULE_PM_OUT);
                if ($pm_out_time < $schedule_pm_out) {
                    $metrics['early_departures']++;
                }
            } else {
                $metrics['incomplete_days']++;
                
                $failures = array();
                
                // Track which entries are missing
                if (empty($record->am_in)) {
                    $metrics['missing_am_in']++;
                    $metrics['total_missing_entries']++;
                    $failures[] = 'AM IN';
                }
                if (empty($record->am_out)) {
                    $metrics['missing_am_out']++;
                    $metrics['total_missing_entries']++;
                    $failures[] = 'AM OUT';
                }
                if (empty($record->pm_in)) {
                    $metrics['missing_pm_in']++;
                    $metrics['total_missing_entries']++;
                    $failures[] = 'PM IN';
                }
                if (empty($record->pm_out)) {
                    $metrics['missing_pm_out']++;
                    $metrics['total_missing_entries']++;
                    $failures[] = 'PM OUT';
                }
                
                // Store failure details for this date
                $metrics['failure_details'][] = array(
                    'date' => $date,
                    'date_formatted' => date('M d, Y (D)', strtotime($date)),
                    'failures' => $failures,
                    'am_in' => $record->am_in,
                    'am_out' => $record->am_out,
                    'pm_in' => $record->pm_in,
                    'pm_out' => $record->pm_out
                );
            }
        }
        
        return $metrics;
    }

    /**
     * Count working days between two dates (excluding weekends and holidays)
     */
    public function count_working_days($start_date, $end_date)
    {
        $count = 0;
        $current = strtotime($start_date);
        $end = strtotime($end_date);
        
        while ($current <= $end) {
            $day_of_week = date('w', $current);
            $date_str = date('Y-m-d', $current);
            
            // Skip weekends (Sunday = 0, Saturday = 6)
            if ($day_of_week != 0 && $day_of_week != 6) {
                // Skip holidays
                if (!$this->isPhilippineHoliday($date_str)) {
                    $count++;
                }
            }
            
            $current = strtotime('+1 day', $current);
        }
        
        return $count;
    }

    /**
     * Get top performers (100% compliance rate)
     */
    public function get_top_performers($start_date, $end_date, $department_id = null, $limit = 10)
    {
        $all_stats = $this->get_compliance_statistics($start_date, $end_date, $department_id);
        
        // Filter for 100% compliance
        $perfect = array_filter($all_stats, function($emp) {
            return $emp['compliance_rate'] == 100 && $emp['working_days'] > 0;
        });
        
        return array_slice($perfect, 0, $limit);
    }

    /**
     * Get employees with most failures
     */
    public function get_most_failures($start_date, $end_date, $department_id = null, $limit = 10)
    {
        $all_stats = $this->get_compliance_statistics($start_date, $end_date, $department_id);
        
        // Sort by total missing entries descending
        usort($all_stats, function($a, $b) {
            return $b['total_missing_entries'] - $a['total_missing_entries'];
        });
        
        // Filter out those with no failures
        $with_failures = array_filter($all_stats, function($emp) {
            return $emp['total_missing_entries'] > 0;
        });
        
        return array_slice(array_values($with_failures), 0, $limit);
    }

    /**
     * Get summary statistics by department
     */
    public function get_department_summary($start_date, $end_date)
    {
        $departments = $this->get_departments();
        $summary = array();
        
        foreach ($departments as $dept) {
            $stats = $this->get_compliance_statistics($start_date, $end_date, $dept->id);
            
            $total_employees = count($stats);
            $perfect_employees = 0;
            $total_compliance = 0;
            $total_missing = 0;
            
            foreach ($stats as $emp) {
                if ($emp['compliance_rate'] == 100) {
                    $perfect_employees++;
                }
                $total_compliance += $emp['compliance_rate'];
                $total_missing += $emp['total_missing_entries'];
            }
            
            $summary[] = array(
                'department_id' => $dept->id,
                'department_name' => $dept->name,
                'department_code' => $dept->code,
                'department_color' => $dept->color,
                'department_icon' => $dept->icon,
                'total_employees' => $total_employees,
                'perfect_employees' => $perfect_employees,
                'average_compliance' => $total_employees > 0 ? round($total_compliance / $total_employees, 2) : 0,
                'total_missing_entries' => $total_missing
            );
        }
        
        // Sort by average compliance descending
        usort($summary, function($a, $b) {
            return $b['average_compliance'] - $a['average_compliance'];
        });
        
        return $summary;
    }

    /**
     * Get detailed failure report for a specific employee
     * 
     * UPDATED: Now uses actual working days (days with clock-in) for compliance calculation
     */
    public function get_employee_failure_details($bio_id, $start_date, $end_date)
    {
        // Get personnel info
        $this->db->select('p.*, d.name as department_name, d.color as department_color');
        $this->db->from('personnels p');
        $this->db->join('departments d', 'd.id = p.department_id', 'left');
        $this->db->where('p.bio_id', $bio_id);
        $personnel = $this->db->get()->row();
        
        if (!$personnel) {
            return null;
        }
        
        // Get biometric records
        $this->db->select('*');
        $this->db->from('biometrics');
        $this->db->where('bio_id', $bio_id);
        $this->db->where('date >=', $start_date);
        $this->db->where('date <=', $end_date);
        $this->db->order_by('date', 'DESC');
        $records = $this->db->get()->result();
        
        $calendar_working_days = $this->count_working_days($start_date, $end_date);
        $metrics = $this->calculate_compliance_metrics($records, $start_date, $end_date);
        
        // Use actual working days (days with clock-in) for compliance rate
        $working_days = $metrics['actual_working_days'];
        
        return array(
            'personnel' => $personnel,
            'name' => $personnel->lastname . ', ' . $personnel->firstname . ' ' . (!empty($personnel->middlename) ? substr($personnel->middlename, 0, 1) . '.' : ''),
            'department_name' => $personnel->department_name ?: 'Unassigned',
            'department_color' => $personnel->department_color ?: '#6c757d',
            'working_days' => $working_days, // Actual working days based on attendance
            'calendar_working_days' => $calendar_working_days, // Calendar reference
            'metrics' => $metrics,
            'compliance_rate' => $working_days > 0 ? round(($metrics['complete_days'] / $working_days) * 100, 2) : 0,
            'records' => $records
        );
    }

    /**
     * Get overall statistics for the dashboard cards
     */
    public function get_overall_statistics($start_date, $end_date, $department_id = null)
    {
        $all_stats = $this->get_compliance_statistics($start_date, $end_date, $department_id);
        
        $total_employees = count($all_stats);
        $perfect_employees = 0;
        $total_compliance = 0;
        $total_missing = 0;
        $total_complete_days = 0;
        $total_working_days = 0;
        
        foreach ($all_stats as $emp) {
            if ($emp['compliance_rate'] == 100 && $emp['working_days'] > 0) {
                $perfect_employees++;
            }
            $total_compliance += $emp['compliance_rate'];
            $total_missing += $emp['total_missing_entries'];
            $total_complete_days += $emp['complete_days'];
            $total_working_days += $emp['working_days'];
        }
        
        return array(
            'total_employees' => $total_employees,
            'perfect_employees' => $perfect_employees,
            'average_compliance' => $total_employees > 0 ? round($total_compliance / $total_employees, 2) : 0,
            'total_missing_entries' => $total_missing,
            'total_complete_days' => $total_complete_days,
            'total_working_days' => $total_working_days,
            'overall_compliance' => $total_working_days > 0 ? round(($total_complete_days / $total_working_days) * 100, 2) : 0
        );
    }

    /**
     * Check if a date is a Philippine holiday
     */
    private function isPhilippineHoliday($date)
    {
        $year = date('Y', strtotime($date));
        $month_day = date('m-d', strtotime($date));
        
        // Fixed Philippine holidays
        $fixed_holidays = array(
            '01-01', // New Year's Day
            '02-25', // EDSA People Power Revolution Anniversary
            '04-09', // Araw ng Kagitingan (Day of Valor)
            '05-01', // Labor Day
            '06-12', // Independence Day
            '08-21', // Ninoy Aquino Day
            '08-25', // National Heroes Day (last Monday of August - approximation)
            '10-28', // Davao Occ Araw
            '10-31', // All Souls' Evening
            '11-30', // Bonifacio Day
            '12-25', // Christmas Day
            '12-30', // Rizal Day
            '12-31'  // New Year's Eve
        );
        
        if (in_array($month_day, $fixed_holidays)) {
            return true;
        }
        
        // Variable holidays
        if ($year == 2024 && ($month_day == '04-18' || $month_day == '04-19')) {
            return true;
        }
        if ($year == 2025 && ($month_day == '04-17' || $month_day == '04-18')) {
            return true;
        }
        if ($year == 2026 && ($month_day == '04-02' || $month_day == '04-03')) {
            return true;
        }
        
        return false;
    }
}
