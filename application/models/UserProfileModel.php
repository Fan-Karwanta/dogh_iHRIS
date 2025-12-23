<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * UserProfileModel
 * 
 * Handles comprehensive user profile data and DTR analytics
 * Provides detailed statistics and insights for user attendance records
 */
class UserProfileModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get user profile data with personnel information
     */
    public function getUserProfile($user_id)
    {
        $this->db->select('u.*, p.id as personnel_id, p.firstname, p.lastname, p.middlename, p.position, p.role, p.bio_id, p.employment_type, p.salary_grade, p.schedule_type, p.fb, d.name as department_name, d.color as department_color');
        $this->db->from('users u');
        $this->db->join('personnels p', 'p.email = u.email', 'left');
        $this->db->join('departments d', 'd.id = p.department_id', 'left');
        $this->db->where('u.id', $user_id);
        $query = $this->db->get();
        return $query->row();
    }

    /**
     * Get monthly DTR statistics for a user
     */
    public function getMonthlyStats($email, $month = null, $year = null)
    {
        if (!$month) $month = date('m');
        if (!$year) $year = date('Y');

        $stats = [
            'total_days' => 0,
            'present_days' => 0,
            'absent_days' => 0,
            'late_days' => 0,
            'early_departures' => 0,
            'undertime_days' => 0,
            'complete_days' => 0,
            'incomplete_days' => 0,
            'total_hours_worked' => 0,
            'total_undertime_hours' => 0,
            'mode_arrival_time' => null,
            'mode_departure_time' => null,
            'avg_late_arrival_time' => null,
            'avg_early_departure_time' => null,
            'perfect_attendance_days' => 0
        ];

        // Get employee schedule
        $this->db->select('schedule_type');
        $this->db->from('personnels');
        $this->db->where('email', $email);
        $personnel = $this->db->get()->row();
        $schedule = $personnel && $personnel->schedule_type ? $personnel->schedule_type : '8:00 AM - 5:00 PM';
        
        // Parse schedule times
        $schedule_parts = explode('-', $schedule);
        $schedule_start = isset($schedule_parts[0]) ? trim($schedule_parts[0]) : '8:00 AM';
        $schedule_end = isset($schedule_parts[1]) ? trim($schedule_parts[1]) : '5:00 PM';
        
        // Get attendance records
        $this->db->select('*');
        $this->db->from('attendance');
        $this->db->where('email', $email);
        $this->db->where('MONTH(date)', $month);
        $this->db->where('YEAR(date)', $year);
        $this->db->order_by('date', 'ASC');
        $attendance_records = $this->db->get()->result();

        // Get biometric records
        $this->db->select('b.*, p.bio_id');
        $this->db->from('biometrics b');
        $this->db->join('personnels p', 'p.bio_id = b.bio_id');
        $this->db->where('p.email', $email);
        $this->db->where('MONTH(b.date)', $month);
        $this->db->where('YEAR(b.date)', $year);
        $this->db->order_by('b.date', 'ASC');
        $biometric_records = $this->db->get()->result();

        // Combine records
        $all_records = array_merge($attendance_records, $biometric_records);

        // Calculate working days in month (excluding weekends)
        $working_days = $this->countWorkingDays($year, $month);
        $stats['total_days'] = $working_days;

        $arrival_times = [];
        $departure_times = [];
        $late_arrival_minutes = [];
        $early_departure_minutes = [];

        foreach ($all_records as $record) {
            $stats['present_days']++;

            // Check for attendance record
            if (isset($record->morning_in)) {
                $morning_in = $record->morning_in;
                $morning_out = $record->morning_out;
                $afternoon_in = $record->afternoon_in;
                $afternoon_out = $record->afternoon_out;
            } else {
                // Biometric record
                $morning_in = $record->am_in;
                $morning_out = $record->am_out;
                $afternoon_in = $record->pm_in;
                $afternoon_out = $record->pm_out;
            }

            // Check if complete
            if ($morning_in && $morning_out && $afternoon_in && $afternoon_out) {
                $stats['complete_days']++;
                
                // Check if perfect (on time)
                if (strtotime($morning_in) <= strtotime('08:15:00') && 
                    strtotime($afternoon_out) >= strtotime('17:00:00')) {
                    $stats['perfect_attendance_days']++;
                }
            } else {
                $stats['incomplete_days']++;
            }

            // Check for late arrival based on employee schedule (no grace period)
            $schedule_start_time = strtotime($schedule_start);
            if ($morning_in && strtotime($morning_in) > $schedule_start_time) {
                $stats['late_days']++;
                // Calculate minutes late for average
                $late_minutes = round((strtotime($morning_in) - $schedule_start_time) / 60);
                $late_arrival_minutes[] = $late_minutes;
            }
            
            // Check for early departure based on employee schedule
            $schedule_end_time = strtotime($schedule_end);
            if ($afternoon_out && strtotime($afternoon_out) < $schedule_end_time) {
                $stats['early_departures']++;
                // Calculate minutes early for average
                $early_minutes = round(($schedule_end_time - strtotime($afternoon_out)) / 60);
                $early_departure_minutes[] = $early_minutes;
            }

            // Calculate hours worked
            if ($morning_in && $morning_out && $afternoon_in && $afternoon_out) {
                $morning_hours = (strtotime($morning_out) - strtotime($morning_in)) / 3600;
                $afternoon_hours = (strtotime($afternoon_out) - strtotime($afternoon_in)) / 3600;
                $stats['total_hours_worked'] += ($morning_hours + $afternoon_hours);
            }

            // Track undertime
            if (isset($record->undertime_hours)) {
                $stats['total_undertime_hours'] += $record->undertime_hours + ($record->undertime_minutes / 60);
                if ($record->undertime_hours > 0 || $record->undertime_minutes > 0) {
                    $stats['undertime_days']++;
                }
            }

            // Collect times for averages
            if ($morning_in) {
                $arrival_times[] = strtotime($morning_in);
            }
            if ($afternoon_out) {
                $departure_times[] = strtotime($afternoon_out);
            }
        }

        $stats['absent_days'] = $working_days - $stats['present_days'];

        // Calculate MODE (most frequent time) instead of average
        if (count($arrival_times) > 0) {
            $stats['mode_arrival_time'] = $this->calculateMode($arrival_times);
        }
        if (count($departure_times) > 0) {
            $stats['mode_departure_time'] = $this->calculateMode($departure_times);
        }
        
        // Calculate average late arrival time (only for days that were late)
        if (count($late_arrival_minutes) > 0) {
            $avg_late_minutes = round(array_sum($late_arrival_minutes) / count($late_arrival_minutes));
            $stats['avg_late_arrival_time'] = $avg_late_minutes;
        }
        
        // Calculate average early departure time (only for days that left early)
        if (count($early_departure_minutes) > 0) {
            $avg_early_minutes = round(array_sum($early_departure_minutes) / count($early_departure_minutes));
            $stats['avg_early_departure_time'] = $avg_early_minutes;
        }

        return $stats;
    }
    
    /**
     * Calculate MODE (most frequent value) for time data
     * Groups times into 15-minute intervals to find the most common time range
     */
    private function calculateMode($timestamps)
    {
        if (empty($timestamps)) {
            return null;
        }
        
        // Round timestamps to 15-minute intervals
        $rounded_times = [];
        foreach ($timestamps as $timestamp) {
            $minutes = date('i', $timestamp);
            $rounded_minutes = floor($minutes / 15) * 15;
            $rounded_time = strtotime(date('Y-m-d H:', $timestamp) . str_pad($rounded_minutes, 2, '0', STR_PAD_LEFT) . ':00');
            $rounded_times[] = $rounded_time;
        }
        
        // Count frequency of each rounded time
        $frequency = array_count_values($rounded_times);
        
        // Find the most frequent time
        arsort($frequency);
        $mode_timestamp = key($frequency);
        
        return date('H:i:s', $mode_timestamp);
    }

    /**
     * Get yearly DTR statistics for a user
     */
    public function getYearlyStats($email, $year = null)
    {
        if (!$year) $year = date('Y');

        $yearly_data = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthly_stats = $this->getMonthlyStats($email, str_pad($month, 2, '0', STR_PAD_LEFT), $year);
            $yearly_data[] = [
                'month' => date('F', mktime(0, 0, 0, $month, 1)),
                'month_num' => $month,
                'present_days' => $monthly_stats['present_days'],
                'absent_days' => $monthly_stats['absent_days'],
                'late_days' => $monthly_stats['late_days'],
                'early_departures' => $monthly_stats['early_departures'],
                'complete_days' => $monthly_stats['complete_days'],
                'attendance_rate' => $monthly_stats['total_days'] > 0 ? round(($monthly_stats['present_days'] / $monthly_stats['total_days']) * 100, 1) : 0
            ];
        }

        return $yearly_data;
    }

    /**
     * Get recent attendance records for a user
     */
    public function getRecentAttendance($email, $limit = 10)
    {
        // Build first subquery for attendance records
        $this->db->select('date, morning_in as am_in, morning_out as am_out, afternoon_in as pm_in, afternoon_out as pm_out, "attendance" as source');
        $this->db->from('attendance');
        $this->db->where('email', $email);
        $subquery1 = $this->db->get_compiled_select();
        
        // Reset query builder
        $this->db->reset_query();
        
        // Build second subquery for biometric records
        $this->db->select('b.date, b.am_in, b.am_out, b.pm_in, b.pm_out, "biometric" as source');
        $this->db->from('biometrics b');
        $this->db->join('personnels p', 'p.bio_id = b.bio_id');
        $this->db->where('p.email', $email);
        $subquery2 = $this->db->get_compiled_select();
        
        // Combine with UNION and execute
        $query = $this->db->query("($subquery1) UNION ($subquery2) ORDER BY date DESC LIMIT $limit");
        return $query->result();
    }

    /**
     * Get attendance comparison with department average
     */
    public function getDepartmentComparison($email, $month = null, $year = null)
    {
        if (!$month) $month = date('m');
        if (!$year) $year = date('Y');

        // Get user's department
        $this->db->select('p.department_id');
        $this->db->from('personnels p');
        $this->db->where('p.email', $email);
        $user_dept = $this->db->get()->row();

        if (!$user_dept || !$user_dept->department_id) {
            return null;
        }

        // Get all personnel in the same department
        $this->db->select('p.email');
        $this->db->from('personnels p');
        $this->db->where('p.department_id', $user_dept->department_id);
        $this->db->where('p.status', 1);
        $dept_personnel = $this->db->get()->result();

        $dept_stats = [
            'total_personnel' => count($dept_personnel),
            'avg_attendance_rate' => 0,
            'avg_late_days' => 0,
            'avg_complete_days' => 0
        ];

        $total_attendance_rate = 0;
        $total_late_days = 0;
        $total_complete_days = 0;

        foreach ($dept_personnel as $person) {
            $stats = $this->getMonthlyStats($person->email, $month, $year);
            $attendance_rate = $stats['total_days'] > 0 ? ($stats['present_days'] / $stats['total_days']) * 100 : 0;
            $total_attendance_rate += $attendance_rate;
            $total_late_days += $stats['late_days'];
            $total_complete_days += $stats['complete_days'];
        }

        if (count($dept_personnel) > 0) {
            $dept_stats['avg_attendance_rate'] = round($total_attendance_rate / count($dept_personnel), 1);
            $dept_stats['avg_late_days'] = round($total_late_days / count($dept_personnel), 1);
            $dept_stats['avg_complete_days'] = round($total_complete_days / count($dept_personnel), 1);
        }

        return $dept_stats;
    }

    /**
     * Get attendance trends (filtered from September 2025 onwards)
     */
    public function getAttendanceTrends($email, $months = 6)
    {
        $trends = [];
        $current_date = new DateTime();
        $cutoff_date = new DateTime('2025-09-01');

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = clone $current_date;
            $date->modify("-$i months");
            
            // Skip months before September 2025
            if ($date < $cutoff_date) {
                continue;
            }
            
            $month = $date->format('m');
            $year = $date->format('Y');

            $stats = $this->getMonthlyStats($email, $month, $year);
            
            $trends[] = [
                'month' => $date->format('M Y'),
                'month_short' => $date->format('M'),
                'present_days' => $stats['present_days'],
                'absent_days' => $stats['absent_days'],
                'early_departures' => $stats['early_departures'],
                'complete_days' => $stats['complete_days'],
                'attendance_rate' => $stats['total_days'] > 0 ? round(($stats['present_days'] / $stats['total_days']) * 100, 1) : 0,
                'punctuality_rate' => $stats['present_days'] > 0 ? round((($stats['present_days'] - $stats['late_days']) / $stats['present_days']) * 100, 1) : 0
            ];
        }

        return $trends;
    }

    /**
     * Get user audit trail
     */
    public function getUserAuditTrail($email, $limit = 15)
    {
        $this->db->select('a.*, u.first_name, u.last_name');
        $this->db->from('audit_trail a');
        $this->db->join('users u', 'u.id = a.admin_user_id', 'left');
        $this->db->where('a.personnel_email', $email);
        $this->db->order_by('a.created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    /**
     * Count working days in a month (excluding weekends)
     */
    private function countWorkingDays($year, $month)
    {
        $working_days = 0;
        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        for ($day = 1; $day <= $days_in_month; $day++) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $day_of_week = date('w', strtotime($date));
            
            // Skip weekends (Sunday = 0, Saturday = 6)
            if ($day_of_week != 0 && $day_of_week != 6) {
                $working_days++;
            }
        }

        return $working_days;
    }

    /**
     * Get performance summary
     */
    public function getPerformanceSummary($email, $month = null, $year = null)
    {
        if (!$month) $month = date('m');
        if (!$year) $year = date('Y');

        $stats = $this->getMonthlyStats($email, $month, $year);
        
        $performance = [
            'attendance_rate' => $stats['total_days'] > 0 ? round(($stats['present_days'] / $stats['total_days']) * 100, 1) : 0,
            'punctuality_rate' => $stats['present_days'] > 0 ? round((($stats['present_days'] - $stats['late_days']) / $stats['present_days']) * 100, 1) : 0,
            'completion_rate' => $stats['present_days'] > 0 ? round(($stats['complete_days'] / $stats['present_days']) * 100, 1) : 0,
            'average_hours_per_day' => $stats['present_days'] > 0 ? round($stats['total_hours_worked'] / $stats['present_days'], 2) : 0,
            'grade' => 'A',
            'status' => 'Excellent'
        ];

        // Calculate grade based on attendance rate
        if ($performance['attendance_rate'] >= 95) {
            $performance['grade'] = 'A+';
            $performance['status'] = 'Outstanding';
        } elseif ($performance['attendance_rate'] >= 90) {
            $performance['grade'] = 'A';
            $performance['status'] = 'Excellent';
        } elseif ($performance['attendance_rate'] >= 85) {
            $performance['grade'] = 'B+';
            $performance['status'] = 'Very Good';
        } elseif ($performance['attendance_rate'] >= 80) {
            $performance['grade'] = 'B';
            $performance['status'] = 'Good';
        } elseif ($performance['attendance_rate'] >= 75) {
            $performance['grade'] = 'C+';
            $performance['status'] = 'Satisfactory';
        } elseif ($performance['attendance_rate'] >= 70) {
            $performance['grade'] = 'C';
            $performance['status'] = 'Fair';
        } else {
            $performance['grade'] = 'D';
            $performance['status'] = 'Needs Improvement';
        }

        return $performance;
    }

    /**
     * Get detailed history for late arrivals
     */
    public function getLateArrivalsHistory($email, $month = null, $year = null)
    {
        if (!$month) $month = date('m');
        if (!$year) $year = date('Y');

        // Get employee schedule
        $this->db->select('schedule_type');
        $this->db->from('personnels');
        $this->db->where('email', $email);
        $personnel = $this->db->get()->row();
        $schedule = $personnel && $personnel->schedule_type ? $personnel->schedule_type : '8:00 AM - 5:00 PM';
        
        // Parse schedule times
        $schedule_parts = explode('-', $schedule);
        $schedule_start = isset($schedule_parts[0]) ? trim($schedule_parts[0]) : '8:00 AM';
        $schedule_start_time = strtotime($schedule_start);

        $history = [];

        // Get attendance records
        $this->db->select('date, morning_in, morning_out, afternoon_in, afternoon_out');
        $this->db->from('attendance');
        $this->db->where('email', $email);
        $this->db->where('MONTH(date)', $month);
        $this->db->where('YEAR(date)', $year);
        $this->db->order_by('date', 'DESC');
        $attendance_records = $this->db->get()->result();

        // Get biometric records
        $this->db->select('b.date, b.am_in as morning_in, b.am_out as morning_out, b.pm_in as afternoon_in, b.pm_out as afternoon_out');
        $this->db->from('biometrics b');
        $this->db->join('personnels p', 'p.bio_id = b.bio_id');
        $this->db->where('p.email', $email);
        $this->db->where('MONTH(b.date)', $month);
        $this->db->where('YEAR(b.date)', $year);
        $this->db->order_by('b.date', 'DESC');
        $biometric_records = $this->db->get()->result();

        // Combine records
        $all_records = array_merge($attendance_records, $biometric_records);

        foreach ($all_records as $record) {
            if ($record->morning_in && strtotime($record->morning_in) > $schedule_start_time) {
                $late_minutes = round((strtotime($record->morning_in) - $schedule_start_time) / 60);
                $history[] = [
                    'date' => $record->date,
                    'scheduled_time' => $schedule_start,
                    'actual_time' => $record->morning_in,
                    'late_by_minutes' => $late_minutes,
                    'status' => 'Late Arrival'
                ];
            }
        }

        return $history;
    }

    /**
     * Get detailed history for early departures
     */
    public function getEarlyDeparturesHistory($email, $month = null, $year = null)
    {
        if (!$month) $month = date('m');
        if (!$year) $year = date('Y');

        // Get employee schedule
        $this->db->select('schedule_type');
        $this->db->from('personnels');
        $this->db->where('email', $email);
        $personnel = $this->db->get()->row();
        $schedule = $personnel && $personnel->schedule_type ? $personnel->schedule_type : '8:00 AM - 5:00 PM';
        
        // Parse schedule times
        $schedule_parts = explode('-', $schedule);
        $schedule_end = isset($schedule_parts[1]) ? trim($schedule_parts[1]) : '5:00 PM';
        $schedule_end_time = strtotime($schedule_end);

        $history = [];

        // Get attendance records
        $this->db->select('date, morning_in, morning_out, afternoon_in, afternoon_out');
        $this->db->from('attendance');
        $this->db->where('email', $email);
        $this->db->where('MONTH(date)', $month);
        $this->db->where('YEAR(date)', $year);
        $this->db->order_by('date', 'DESC');
        $attendance_records = $this->db->get()->result();

        // Get biometric records
        $this->db->select('b.date, b.am_in as morning_in, b.am_out as morning_out, b.pm_in as afternoon_in, b.pm_out as afternoon_out');
        $this->db->from('biometrics b');
        $this->db->join('personnels p', 'p.bio_id = b.bio_id');
        $this->db->where('p.email', $email);
        $this->db->where('MONTH(b.date)', $month);
        $this->db->where('YEAR(b.date)', $year);
        $this->db->order_by('b.date', 'DESC');
        $biometric_records = $this->db->get()->result();

        // Combine records
        $all_records = array_merge($attendance_records, $biometric_records);

        foreach ($all_records as $record) {
            if ($record->afternoon_out && strtotime($record->afternoon_out) < $schedule_end_time) {
                $early_minutes = round(($schedule_end_time - strtotime($record->afternoon_out)) / 60);
                $history[] = [
                    'date' => $record->date,
                    'scheduled_time' => $schedule_end,
                    'actual_time' => $record->afternoon_out,
                    'early_by_minutes' => $early_minutes,
                    'status' => 'Early Departure'
                ];
            }
        }

        return $history;
    }

    /**
     * Get detailed history for present days
     */
    public function getPresentDaysHistory($email, $month = null, $year = null)
    {
        if (!$month) $month = date('m');
        if (!$year) $year = date('Y');

        $history = [];

        // Get attendance records
        $this->db->select('date, morning_in, morning_out, afternoon_in, afternoon_out');
        $this->db->from('attendance');
        $this->db->where('email', $email);
        $this->db->where('MONTH(date)', $month);
        $this->db->where('YEAR(date)', $year);
        $this->db->order_by('date', 'DESC');
        $attendance_records = $this->db->get()->result();

        // Get biometric records
        $this->db->select('b.date, b.am_in as morning_in, b.am_out as morning_out, b.pm_in as afternoon_in, b.pm_out as afternoon_out');
        $this->db->from('biometrics b');
        $this->db->join('personnels p', 'p.bio_id = b.bio_id');
        $this->db->where('p.email', $email);
        $this->db->where('MONTH(b.date)', $month);
        $this->db->where('YEAR(b.date)', $year);
        $this->db->order_by('b.date', 'DESC');
        $biometric_records = $this->db->get()->result();

        // Combine records
        $all_records = array_merge($attendance_records, $biometric_records);

        foreach ($all_records as $record) {
            $history[] = [
                'date' => $record->date,
                'morning_in' => $record->morning_in,
                'morning_out' => $record->morning_out,
                'afternoon_in' => $record->afternoon_in,
                'afternoon_out' => $record->afternoon_out,
                'status' => 'Present'
            ];
        }

        return $history;
    }

    /**
     * Get detailed history for absent days
     */
    public function getAbsentDaysHistory($email, $month = null, $year = null)
    {
        if (!$month) $month = date('m');
        if (!$year) $year = date('Y');

        $history = [];

        // Get all working days in the month
        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $working_days = [];
        
        for ($day = 1; $day <= $days_in_month; $day++) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $day_of_week = date('w', strtotime($date));
            
            // Skip weekends (Sunday = 0, Saturday = 6)
            if ($day_of_week != 0 && $day_of_week != 6) {
                $working_days[] = $date;
            }
        }

        // Get all attendance records for the month
        $this->db->select('date');
        $this->db->from('attendance');
        $this->db->where('email', $email);
        $this->db->where('MONTH(date)', $month);
        $this->db->where('YEAR(date)', $year);
        $attendance_dates = array_column($this->db->get()->result_array(), 'date');

        // Get biometric records
        $this->db->select('b.date');
        $this->db->from('biometrics b');
        $this->db->join('personnels p', 'p.bio_id = b.bio_id');
        $this->db->where('p.email', $email);
        $this->db->where('MONTH(b.date)', $month);
        $this->db->where('YEAR(b.date)', $year);
        $biometric_dates = array_column($this->db->get()->result_array(), 'date');

        // Combine all present dates
        $present_dates = array_unique(array_merge($attendance_dates, $biometric_dates));

        // Find absent days
        foreach ($working_days as $working_day) {
            if (!in_array($working_day, $present_dates)) {
                $history[] = [
                    'date' => $working_day,
                    'status' => 'Absent',
                    'day_of_week' => date('l', strtotime($working_day))
                ];
            }
        }

        // Sort by date descending
        usort($history, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return $history;
    }

    /**
     * Get detailed history for complete DTR days
     */
    public function getCompleteDaysHistory($email, $month = null, $year = null)
    {
        if (!$month) $month = date('m');
        if (!$year) $year = date('Y');

        $history = [];

        // Get attendance records
        $this->db->select('date, morning_in, morning_out, afternoon_in, afternoon_out');
        $this->db->from('attendance');
        $this->db->where('email', $email);
        $this->db->where('MONTH(date)', $month);
        $this->db->where('YEAR(date)', $year);
        $this->db->order_by('date', 'DESC');
        $attendance_records = $this->db->get()->result();

        // Get biometric records
        $this->db->select('b.date, b.am_in as morning_in, b.am_out as morning_out, b.pm_in as afternoon_in, b.pm_out as afternoon_out');
        $this->db->from('biometrics b');
        $this->db->join('personnels p', 'p.bio_id = b.bio_id');
        $this->db->where('p.email', $email);
        $this->db->where('MONTH(b.date)', $month);
        $this->db->where('YEAR(b.date)', $year);
        $this->db->order_by('b.date', 'DESC');
        $biometric_records = $this->db->get()->result();

        // Combine records
        $all_records = array_merge($attendance_records, $biometric_records);

        foreach ($all_records as $record) {
            // Check if complete (all 4 time entries present)
            if ($record->morning_in && $record->morning_out && $record->afternoon_in && $record->afternoon_out) {
                $history[] = [
                    'date' => $record->date,
                    'morning_in' => $record->morning_in,
                    'morning_out' => $record->morning_out,
                    'afternoon_in' => $record->afternoon_in,
                    'afternoon_out' => $record->afternoon_out,
                    'status' => 'Complete DTR'
                ];
            }
        }

        return $history;
    }

    /**
     * Get detailed breakdown for Total Hours Worked
     */
    public function getTotalHoursBreakdown($email, $month = null, $year = null)
    {
        if (!$month) $month = date('m');
        if (!$year) $year = date('Y');

        $breakdown = [];
        $total_hours = 0;

        // Get attendance records
        $this->db->select('date, morning_in, morning_out, afternoon_in, afternoon_out');
        $this->db->from('attendance');
        $this->db->where('email', $email);
        $this->db->where('MONTH(date)', $month);
        $this->db->where('YEAR(date)', $year);
        $this->db->order_by('date', 'DESC');
        $attendance_records = $this->db->get()->result();

        // Get biometric records
        $this->db->select('b.date, b.am_in as morning_in, b.am_out as morning_out, b.pm_in as afternoon_in, b.pm_out as afternoon_out');
        $this->db->from('biometrics b');
        $this->db->join('personnels p', 'p.bio_id = b.bio_id');
        $this->db->where('p.email', $email);
        $this->db->where('MONTH(b.date)', $month);
        $this->db->where('YEAR(b.date)', $year);
        $this->db->order_by('b.date', 'DESC');
        $biometric_records = $this->db->get()->result();

        // Combine records
        $all_records = array_merge($attendance_records, $biometric_records);

        foreach ($all_records as $record) {
            $morning_hours = 0;
            $afternoon_hours = 0;
            $daily_total = 0;

            if ($record->morning_in && $record->morning_out) {
                $morning_hours = (strtotime($record->morning_out) - strtotime($record->morning_in)) / 3600;
            }

            if ($record->afternoon_in && $record->afternoon_out) {
                $afternoon_hours = (strtotime($record->afternoon_out) - strtotime($record->afternoon_in)) / 3600;
            }

            $daily_total = $morning_hours + $afternoon_hours;
            $total_hours += $daily_total;

            if ($daily_total > 0) {
                $breakdown[] = [
                    'date' => $record->date,
                    'morning_in' => $record->morning_in,
                    'morning_out' => $record->morning_out,
                    'afternoon_in' => $record->afternoon_in,
                    'afternoon_out' => $record->afternoon_out,
                    'morning_hours' => round($morning_hours, 2),
                    'afternoon_hours' => round($afternoon_hours, 2),
                    'daily_total' => round($daily_total, 2)
                ];
            }
        }

        return [
            'breakdown' => $breakdown,
            'total_hours' => round($total_hours, 2),
            'total_days' => count($breakdown)
        ];
    }

    /**
     * Get detailed breakdown for Mode Arrival calculation
     */
    public function getModeArrivalBreakdown($email, $month = null, $year = null)
    {
        if (!$month) $month = date('m');
        if (!$year) $year = date('Y');

        $arrival_times = [];
        $all_arrivals = [];

        // Get attendance records
        $this->db->select('date, morning_in');
        $this->db->from('attendance');
        $this->db->where('email', $email);
        $this->db->where('MONTH(date)', $month);
        $this->db->where('YEAR(date)', $year);
        $this->db->where('morning_in IS NOT NULL');
        $this->db->order_by('date', 'DESC');
        $attendance_records = $this->db->get()->result();

        // Get biometric records
        $this->db->select('b.date, b.am_in as morning_in');
        $this->db->from('biometrics b');
        $this->db->join('personnels p', 'p.bio_id = b.bio_id');
        $this->db->where('p.email', $email);
        $this->db->where('MONTH(b.date)', $month);
        $this->db->where('YEAR(b.date)', $year);
        $this->db->where('b.am_in IS NOT NULL');
        $this->db->order_by('b.date', 'DESC');
        $biometric_records = $this->db->get()->result();

        // Combine records
        $all_records = array_merge($attendance_records, $biometric_records);

        foreach ($all_records as $record) {
            if ($record->morning_in) {
                $timestamp = strtotime($record->morning_in);
                $arrival_times[] = $timestamp;
                
                // Round to 15-minute intervals for grouping
                $minutes = date('i', $timestamp);
                $rounded_minutes = floor($minutes / 15) * 15;
                $rounded_time = strtotime(date('Y-m-d H:', $timestamp) . str_pad($rounded_minutes, 2, '0', STR_PAD_LEFT) . ':00');
                $rounded_time_str = date('g:i A', $rounded_time);
                
                $all_arrivals[] = [
                    'date' => $record->date,
                    'actual_time' => date('g:i A', $timestamp),
                    'rounded_interval' => $rounded_time_str
                ];
            }
        }

        // Calculate mode
        $mode_time = null;
        $frequency_map = [];
        
        if (!empty($arrival_times)) {
            // Round timestamps to 15-minute intervals
            $rounded_times = [];
            foreach ($arrival_times as $timestamp) {
                $minutes = date('i', $timestamp);
                $rounded_minutes = floor($minutes / 15) * 15;
                $rounded_time = strtotime(date('Y-m-d H:', $timestamp) . str_pad($rounded_minutes, 2, '0', STR_PAD_LEFT) . ':00');
                $rounded_times[] = $rounded_time;
            }
            
            // Count frequency of each rounded time
            $frequency = array_count_values($rounded_times);
            arsort($frequency);
            $mode_timestamp = key($frequency);
            $mode_time = date('g:i A', $mode_timestamp);
            
            // Build frequency map for display
            foreach ($frequency as $time => $count) {
                $frequency_map[] = [
                    'time_interval' => date('g:i A', $time),
                    'count' => $count,
                    'is_mode' => ($time == $mode_timestamp)
                ];
            }
        }

        return [
            'arrivals' => $all_arrivals,
            'mode_time' => $mode_time,
            'frequency_map' => $frequency_map,
            'total_records' => count($all_arrivals)
        ];
    }

    /**
     * Get detailed breakdown for Mode Departure calculation
     */
    public function getModeDepartureBreakdown($email, $month = null, $year = null)
    {
        if (!$month) $month = date('m');
        if (!$year) $year = date('Y');

        $departure_times = [];
        $all_departures = [];

        // Get attendance records
        $this->db->select('date, afternoon_out');
        $this->db->from('attendance');
        $this->db->where('email', $email);
        $this->db->where('MONTH(date)', $month);
        $this->db->where('YEAR(date)', $year);
        $this->db->where('afternoon_out IS NOT NULL');
        $this->db->order_by('date', 'DESC');
        $attendance_records = $this->db->get()->result();

        // Get biometric records
        $this->db->select('b.date, b.pm_out as afternoon_out');
        $this->db->from('biometrics b');
        $this->db->join('personnels p', 'p.bio_id = b.bio_id');
        $this->db->where('p.email', $email);
        $this->db->where('MONTH(b.date)', $month);
        $this->db->where('YEAR(b.date)', $year);
        $this->db->where('b.pm_out IS NOT NULL');
        $this->db->order_by('b.date', 'DESC');
        $biometric_records = $this->db->get()->result();

        // Combine records
        $all_records = array_merge($attendance_records, $biometric_records);

        foreach ($all_records as $record) {
            if ($record->afternoon_out) {
                $timestamp = strtotime($record->afternoon_out);
                $departure_times[] = $timestamp;
                
                // Round to 15-minute intervals for grouping
                $minutes = date('i', $timestamp);
                $rounded_minutes = floor($minutes / 15) * 15;
                $rounded_time = strtotime(date('Y-m-d H:', $timestamp) . str_pad($rounded_minutes, 2, '0', STR_PAD_LEFT) . ':00');
                $rounded_time_str = date('g:i A', $rounded_time);
                
                $all_departures[] = [
                    'date' => $record->date,
                    'actual_time' => date('g:i A', $timestamp),
                    'rounded_interval' => $rounded_time_str
                ];
            }
        }

        // Calculate mode
        $mode_time = null;
        $frequency_map = [];
        
        if (!empty($departure_times)) {
            // Round timestamps to 15-minute intervals
            $rounded_times = [];
            foreach ($departure_times as $timestamp) {
                $minutes = date('i', $timestamp);
                $rounded_minutes = floor($minutes / 15) * 15;
                $rounded_time = strtotime(date('Y-m-d H:', $timestamp) . str_pad($rounded_minutes, 2, '0', STR_PAD_LEFT) . ':00');
                $rounded_times[] = $rounded_time;
            }
            
            // Count frequency of each rounded time
            $frequency = array_count_values($rounded_times);
            arsort($frequency);
            $mode_timestamp = key($frequency);
            $mode_time = date('g:i A', $mode_timestamp);
            
            // Build frequency map for display
            foreach ($frequency as $time => $count) {
                $frequency_map[] = [
                    'time_interval' => date('g:i A', $time),
                    'count' => $count,
                    'is_mode' => ($time == $mode_timestamp)
                ];
            }
        }

        return [
            'departures' => $all_departures,
            'mode_time' => $mode_time,
            'frequency_map' => $frequency_map,
            'total_records' => count($all_departures)
        ];
    }

    /**
     * Get detailed breakdown for Average Late Arrival calculation
     */
    public function getAvgLateArrivalBreakdown($email, $month = null, $year = null)
    {
        if (!$month) $month = date('m');
        if (!$year) $year = date('Y');

        // Get employee schedule
        $this->db->select('schedule_type');
        $this->db->from('personnels');
        $this->db->where('email', $email);
        $personnel = $this->db->get()->row();
        $schedule = $personnel && $personnel->schedule_type ? $personnel->schedule_type : '8:00 AM - 5:00 PM';
        
        // Parse schedule times
        $schedule_parts = explode('-', $schedule);
        $schedule_start = isset($schedule_parts[0]) ? trim($schedule_parts[0]) : '8:00 AM';
        $schedule_start_time = strtotime($schedule_start);

        $late_arrivals = [];
        $total_late_minutes = 0;

        // Get attendance records
        $this->db->select('date, morning_in');
        $this->db->from('attendance');
        $this->db->where('email', $email);
        $this->db->where('MONTH(date)', $month);
        $this->db->where('YEAR(date)', $year);
        $this->db->where('morning_in IS NOT NULL');
        $this->db->order_by('date', 'DESC');
        $attendance_records = $this->db->get()->result();

        // Get biometric records
        $this->db->select('b.date, b.am_in as morning_in');
        $this->db->from('biometrics b');
        $this->db->join('personnels p', 'p.bio_id = b.bio_id');
        $this->db->where('p.email', $email);
        $this->db->where('MONTH(b.date)', $month);
        $this->db->where('YEAR(b.date)', $year);
        $this->db->where('b.am_in IS NOT NULL');
        $this->db->order_by('b.date', 'DESC');
        $biometric_records = $this->db->get()->result();

        // Combine records
        $all_records = array_merge($attendance_records, $biometric_records);

        foreach ($all_records as $record) {
            if ($record->morning_in && strtotime($record->morning_in) > $schedule_start_time) {
                $late_minutes = round((strtotime($record->morning_in) - $schedule_start_time) / 60);
                $total_late_minutes += $late_minutes;
                
                $late_arrivals[] = [
                    'date' => $record->date,
                    'scheduled_time' => $schedule_start,
                    'actual_time' => date('g:i A', strtotime($record->morning_in)),
                    'late_by_minutes' => $late_minutes,
                    'late_by_formatted' => $this->formatMinutes($late_minutes)
                ];
            }
        }

        $avg_late_minutes = count($late_arrivals) > 0 ? round($total_late_minutes / count($late_arrivals)) : 0;

        return [
            'late_arrivals' => $late_arrivals,
            'avg_late_minutes' => $avg_late_minutes,
            'avg_late_formatted' => $this->formatMinutes($avg_late_minutes),
            'total_late_days' => count($late_arrivals),
            'total_late_minutes' => $total_late_minutes,
            'scheduled_start' => $schedule_start
        ];
    }

    /**
     * Get detailed breakdown for Average Early Departure calculation
     */
    public function getAvgEarlyDepartureBreakdown($email, $month = null, $year = null)
    {
        if (!$month) $month = date('m');
        if (!$year) $year = date('Y');

        // Get employee schedule
        $this->db->select('schedule_type');
        $this->db->from('personnels');
        $this->db->where('email', $email);
        $personnel = $this->db->get()->row();
        $schedule = $personnel && $personnel->schedule_type ? $personnel->schedule_type : '8:00 AM - 5:00 PM';
        
        // Parse schedule times
        $schedule_parts = explode('-', $schedule);
        $schedule_end = isset($schedule_parts[1]) ? trim($schedule_parts[1]) : '5:00 PM';
        $schedule_end_time = strtotime($schedule_end);

        $early_departures = [];
        $total_early_minutes = 0;

        // Get attendance records
        $this->db->select('date, afternoon_out');
        $this->db->from('attendance');
        $this->db->where('email', $email);
        $this->db->where('MONTH(date)', $month);
        $this->db->where('YEAR(date)', $year);
        $this->db->where('afternoon_out IS NOT NULL');
        $this->db->order_by('date', 'DESC');
        $attendance_records = $this->db->get()->result();

        // Get biometric records
        $this->db->select('b.date, b.pm_out as afternoon_out');
        $this->db->from('biometrics b');
        $this->db->join('personnels p', 'p.bio_id = b.bio_id');
        $this->db->where('p.email', $email);
        $this->db->where('MONTH(b.date)', $month);
        $this->db->where('YEAR(b.date)', $year);
        $this->db->where('b.pm_out IS NOT NULL');
        $this->db->order_by('b.date', 'DESC');
        $biometric_records = $this->db->get()->result();

        // Combine records
        $all_records = array_merge($attendance_records, $biometric_records);

        foreach ($all_records as $record) {
            if ($record->afternoon_out && strtotime($record->afternoon_out) < $schedule_end_time) {
                $early_minutes = round(($schedule_end_time - strtotime($record->afternoon_out)) / 60);
                $total_early_minutes += $early_minutes;
                
                $early_departures[] = [
                    'date' => $record->date,
                    'scheduled_time' => $schedule_end,
                    'actual_time' => date('g:i A', strtotime($record->afternoon_out)),
                    'early_by_minutes' => $early_minutes,
                    'early_by_formatted' => $this->formatMinutes($early_minutes)
                ];
            }
        }

        $avg_early_minutes = count($early_departures) > 0 ? round($total_early_minutes / count($early_departures)) : 0;

        return [
            'early_departures' => $early_departures,
            'avg_early_minutes' => $avg_early_minutes,
            'avg_early_formatted' => $this->formatMinutes($avg_early_minutes),
            'total_early_days' => count($early_departures),
            'total_early_minutes' => $total_early_minutes,
            'scheduled_end' => $schedule_end
        ];
    }

    /**
     * Helper function to format minutes into hours and minutes
     */
    private function formatMinutes($minutes)
    {
        if ($minutes < 60) {
            return $minutes . ' min' . ($minutes != 1 ? 's' : '');
        } else {
            $hours = floor($minutes / 60);
            $mins = $minutes % 60;
            $result = $hours . ' hr' . ($hours != 1 ? 's' : '');
            if ($mins > 0) {
                $result .= ' ' . $mins . ' min' . ($mins != 1 ? 's' : '');
            }
            return $result;
        }
    }
}
