<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DashboardModel extends CI_Model
{

    public function __contruct()
    {
        $this->load->database();
    }

    // Cache for dashboard stats to avoid repeated queries
    private $dashboard_stats_cache = null;
    
    private function getDashboardStatsCache()
    {
        if ($this->dashboard_stats_cache === null) {
            $today = date('Y-m-d');
            $month_start = date('Y-m-01');
            $month_end = date('Y-m-t');
            
            // Check if required tables exist
            $personnels_exists = $this->db->table_exists('personnels');
            $attendance_exists = $this->db->table_exists('attendance');
            $biometrics_exists = $this->db->table_exists('biometrics');
            
            // Build query based on existing tables
            $personnel_count = $personnels_exists ? "(SELECT COUNT(*) FROM personnels WHERE status = 1)" : "0";
            $today_attendance = $attendance_exists ? "(SELECT COUNT(*) FROM attendance WHERE date = '$today')" : "0";
            $today_biometrics = $biometrics_exists ? "(SELECT COUNT(*) FROM biometrics WHERE date = '$today')" : "0";
            $monthly_attendance = $attendance_exists ? "(SELECT COUNT(*) FROM attendance WHERE date BETWEEN '$month_start' AND '$month_end')" : "0";
            $monthly_biometrics = $biometrics_exists ? "(SELECT COUNT(*) FROM biometrics WHERE date BETWEEN '$month_start' AND '$month_end')" : "0";
            
            // Single query to get all basic stats (using date ranges for index optimization)
            $query = $this->db->query("
                SELECT 
                    $personnel_count as personnel_count,
                    $today_attendance as today_attendance,
                    $today_biometrics as today_biometrics,
                    $monthly_attendance as monthly_attendance,
                    $monthly_biometrics as monthly_biometrics
            ");
            $this->dashboard_stats_cache = $query->row();
        }
        return $this->dashboard_stats_cache;
    }
    
    public function personnel()
    {
        return $this->getDashboardStatsCache()->personnel_count;
    }

    public function todayAttendance()
    {
        return $this->getDashboardStatsCache()->today_attendance;
    }

    public function todayBiometrics()
    {
        return $this->getDashboardStatsCache()->today_biometrics;
    }

    public function monthlyAttendance()
    {
        return $this->getDashboardStatsCache()->monthly_attendance;
    }

    public function monthlyBiometrics()
    {
        return $this->getDashboardStatsCache()->monthly_biometrics;
    }

    public function getAttendanceByDay($days = 7)
    {
        $start_date = date('Y-m-d', strtotime("-" . ($days - 1) . " days"));
        $end_date = date('Y-m-d');
        
        $attendance_counts = [];
        $biometrics_counts = [];
        
        // Single query for attendance counts grouped by date (with table check)
        if ($this->db->table_exists('attendance')) {
            $attendance_query = $this->db->query("
                SELECT date, COUNT(*) as count 
                FROM attendance 
                WHERE date BETWEEN '$start_date' AND '$end_date' 
                GROUP BY date
            ");
            if ($attendance_query) {
                foreach ($attendance_query->result() as $row) {
                    $attendance_counts[$row->date] = $row->count;
                }
            }
        }
        
        // Single query for biometrics counts grouped by date (with table check)
        if ($this->db->table_exists('biometrics')) {
            $biometrics_query = $this->db->query("
                SELECT date, COUNT(*) as count 
                FROM biometrics 
                WHERE date BETWEEN '$start_date' AND '$end_date' 
                GROUP BY date
            ");
            if ($biometrics_query) {
                foreach ($biometrics_query->result() as $row) {
                    $biometrics_counts[$row->date] = $row->count;
                }
            }
        }
        
        // Build result array
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $attendance = isset($attendance_counts[$date]) ? $attendance_counts[$date] : 0;
            $biometrics = isset($biometrics_counts[$date]) ? $biometrics_counts[$date] : 0;
            
            $data[] = [
                'date' => date('M j', strtotime($date)),
                'attendance' => $attendance,
                'biometrics' => $biometrics,
                'total' => $attendance + $biometrics
            ];
        }
        return $data;
    }

    public function getMonthlyStats()
    {
        $start_date = date('Y-m-01', strtotime("-11 months"));
        $end_date = date('Y-m-t'); // Last day of current month
        
        $attendance_counts = [];
        $biometrics_counts = [];
        
        // Single query for attendance counts grouped by month (with table check)
        if ($this->db->table_exists('attendance')) {
            $attendance_query = $this->db->query("
                SELECT DATE_FORMAT(date, '%Y-%m') as month_key, COUNT(*) as count 
                FROM attendance 
                WHERE date BETWEEN '$start_date' AND '$end_date' 
                GROUP BY DATE_FORMAT(date, '%Y-%m')
            ");
            if ($attendance_query) {
                foreach ($attendance_query->result() as $row) {
                    $attendance_counts[$row->month_key] = $row->count;
                }
            }
        }
        
        // Single query for biometrics counts grouped by month (with table check)
        if ($this->db->table_exists('biometrics')) {
            $biometrics_query = $this->db->query("
                SELECT DATE_FORMAT(date, '%Y-%m') as month_key, COUNT(*) as count 
                FROM biometrics 
                WHERE date BETWEEN '$start_date' AND '$end_date' 
                GROUP BY DATE_FORMAT(date, '%Y-%m')
            ");
            if ($biometrics_query) {
                foreach ($biometrics_query->result() as $row) {
                    $biometrics_counts[$row->month_key] = $row->count;
                }
            }
        }
        
        // Build result array
        $stats = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = date('Y-m-01', strtotime("-$i months"));
            $month_key = date('Y-m', strtotime($date));
            $attendance = isset($attendance_counts[$month_key]) ? $attendance_counts[$month_key] : 0;
            $biometrics = isset($biometrics_counts[$month_key]) ? $biometrics_counts[$month_key] : 0;
            
            $stats[] = [
                'month' => date('M Y', strtotime($date)),
                'attendance' => $attendance,
                'biometrics' => $biometrics,
                'total' => $attendance + $biometrics
            ];
        }
        return $stats;
    }

    public function getTopAttendees($limit = 5)
    {
        // Check if required tables exist
        if (!$this->db->table_exists('attendance') || !$this->db->table_exists('personnels')) {
            return [];
        }
        
        $month = date('m');
        $year = date('Y');
        $start_date = "$year-$month-01";
        $end_date = date('Y-m-t');
        
        // Optimized query using date range instead of MONTH/YEAR functions
        $query = $this->db->query("
            SELECT p.firstname, p.lastname, COUNT(*) as total_days
            FROM attendance a
            INNER JOIN personnels p ON p.email = a.email
            WHERE a.date BETWEEN '$start_date' AND '$end_date'
            GROUP BY a.email
            ORDER BY total_days DESC
            LIMIT $limit
        ");
        return $query ? $query->result() : [];
    }

    public function getAttendanceRate()
    {
        $working_days = date('j'); // Current day of month
        $stats = $this->getDashboardStatsCache();
        
        $expected_records = $stats->personnel_count * $working_days;
        $total_actual = $stats->monthly_attendance + $stats->monthly_biometrics;
        
        if ($expected_records > 0) {
            return round(($total_actual / $expected_records) * 100, 1);
        }
        return 0;
    }

    public function getRecentActivity($limit = 5)
    {
        // Check if required tables exist
        if (!$this->db->table_exists('attendance') || !$this->db->table_exists('personnels')) {
            return [];
        }
        
        $this->db->select('a.date, p.firstname, p.lastname, a.morning_in, a.afternoon_out');
        $this->db->from('attendance a');
        $this->db->join('personnels p', 'p.email = a.email');
        $this->db->order_by('a.date', 'DESC');
        $this->db->order_by('a.id', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get();
        return $query ? $query->result() : [];
    }

    public function update($data, $id = 1)
    {
        // Debug: Log the update query
        log_message('debug', 'Updating systems table with ID: ' . $id);
        log_message('debug', 'Update data: ' . print_r($data, true));
        
        // Check if record exists first
        $this->db->where('id', $id);
        $existing = $this->db->get('systems');
        
        if ($existing->num_rows() == 0) {
            log_message('error', 'No record found with ID: ' . $id);
            return 0;
        }
        
        $current_data = $existing->row_array();
        log_message('debug', 'Current data: ' . print_r($current_data, true));
        
        // Perform the update
        $this->db->where('id', $id);
        $this->db->update('systems', $data);
        
        $affected_rows = $this->db->affected_rows();
        log_message('debug', 'Affected rows: ' . $affected_rows);
        
        // If no rows affected, it might be because data is identical
        if ($affected_rows == 0) {
            // Check if the data is actually different
            $data_changed = false;
            foreach ($data as $key => $value) {
                if (isset($current_data[$key]) && $current_data[$key] != $value) {
                    $data_changed = true;
                    break;
                }
            }
            
            if (!$data_changed) {
                log_message('debug', 'No changes detected - data is identical');
                // Return 1 to indicate success even if no changes
                return 1;
            }
        }
        
        return $affected_rows;
    }

    /**
     * Get department attendance distribution
     */
    public function getDepartmentDistribution()
    {
        if (!$this->db->table_exists('departments') || !$this->db->table_exists('personnels')) {
            return [];
        }
        
        $query = $this->db->query("
            SELECT d.id, d.name, d.code, d.color, COUNT(p.id) as personnel_count
            FROM departments d
            LEFT JOIN personnels p ON p.department_id = d.id AND p.status = 1
            WHERE d.status = 1
            GROUP BY d.id
            ORDER BY personnel_count DESC
        ");
        
        $result = $query ? $query->result() : [];
        
        // Add unassigned count
        $unassigned_query = $this->db->query("
            SELECT COUNT(*) as count FROM personnels 
            WHERE (department_id IS NULL OR department_id = 0) AND status = 1
        ");
        $unassigned = $unassigned_query ? $unassigned_query->row()->count : 0;
        
        if ($unassigned > 0) {
            $result[] = (object)[
                'id' => 0,
                'name' => 'Unassigned',
                'code' => 'UNASSIGNED',
                'color' => '#95a5a6',
                'personnel_count' => $unassigned
            ];
        }
        
        return $result;
    }

    /**
     * Get overall schedule compliance statistics
     */
    public function getComplianceStats($start_date = null, $end_date = null)
    {
        if (!$start_date) $start_date = date('Y-m-01');
        if (!$end_date) $end_date = date('Y-m-t');
        
        if (!$this->db->table_exists('biometrics')) {
            return ['compliance_rate' => 0, 'complete_days' => 0, 'incomplete_days' => 0, 'total_records' => 0];
        }
        
        // Count complete records (all 4 time slots filled)
        $complete_query = $this->db->query("
            SELECT COUNT(*) as count FROM biometrics 
            WHERE date BETWEEN '$start_date' AND '$end_date'
            AND am_in IS NOT NULL AND am_in != ''
            AND am_out IS NOT NULL AND am_out != ''
            AND pm_in IS NOT NULL AND pm_in != ''
            AND pm_out IS NOT NULL AND pm_out != ''
            AND DAYOFWEEK(date) NOT IN (1, 7)
        ");
        $complete = $complete_query ? $complete_query->row()->count : 0;
        
        // Count total records with at least one entry (excluding weekends)
        $total_query = $this->db->query("
            SELECT COUNT(*) as count FROM biometrics 
            WHERE date BETWEEN '$start_date' AND '$end_date'
            AND (am_in IS NOT NULL OR am_out IS NOT NULL OR pm_in IS NOT NULL OR pm_out IS NOT NULL)
            AND DAYOFWEEK(date) NOT IN (1, 7)
        ");
        $total = $total_query ? $total_query->row()->count : 0;
        
        $compliance_rate = $total > 0 ? round(($complete / $total) * 100, 1) : 0;
        
        return [
            'compliance_rate' => $compliance_rate,
            'complete_days' => $complete,
            'incomplete_days' => $total - $complete,
            'total_records' => $total
        ];
    }

    /**
     * Get late arrivals and early departures trend
     */
    public function getLateEarlyTrend($days = 30)
    {
        if (!$this->db->table_exists('biometrics')) {
            return [];
        }
        
        $start_date = date('Y-m-d', strtotime("-" . ($days - 1) . " days"));
        $end_date = date('Y-m-d');
        
        $query = $this->db->query("
            SELECT 
                DATE(date) as record_date,
                SUM(CASE WHEN TIME(am_in) > '08:15:00' AND am_in IS NOT NULL THEN 1 ELSE 0 END) as late_arrivals,
                SUM(CASE WHEN TIME(pm_out) < '17:00:00' AND pm_out IS NOT NULL AND pm_out != '' THEN 1 ELSE 0 END) as early_departures
            FROM biometrics
            WHERE date BETWEEN '$start_date' AND '$end_date'
            AND DAYOFWEEK(date) NOT IN (1, 7)
            GROUP BY DATE(date)
            ORDER BY date ASC
        ");
        
        return $query ? $query->result() : [];
    }

    /**
     * Get department compliance comparison
     */
    public function getDepartmentCompliance($start_date = null, $end_date = null)
    {
        if (!$start_date) $start_date = date('Y-m-01');
        if (!$end_date) $end_date = date('Y-m-t');
        
        if (!$this->db->table_exists('departments') || !$this->db->table_exists('biometrics') || !$this->db->table_exists('personnels')) {
            return [];
        }
        
        $query = $this->db->query("
            SELECT 
                d.id,
                d.name,
                d.color,
                COUNT(DISTINCT b.id) as total_records,
                SUM(CASE 
                    WHEN b.am_in IS NOT NULL AND b.am_in != '' 
                    AND b.am_out IS NOT NULL AND b.am_out != ''
                    AND b.pm_in IS NOT NULL AND b.pm_in != ''
                    AND b.pm_out IS NOT NULL AND b.pm_out != ''
                    THEN 1 ELSE 0 
                END) as complete_records
            FROM departments d
            LEFT JOIN personnels p ON p.department_id = d.id AND p.status = 1
            LEFT JOIN biometrics b ON b.bio_id = p.bio_id 
                AND b.date BETWEEN '$start_date' AND '$end_date'
                AND DAYOFWEEK(b.date) NOT IN (1, 7)
            WHERE d.status = 1
            GROUP BY d.id
            HAVING total_records > 0
            ORDER BY (SUM(CASE 
                    WHEN b.am_in IS NOT NULL AND b.am_in != '' 
                    AND b.am_out IS NOT NULL AND b.am_out != ''
                    AND b.pm_in IS NOT NULL AND b.pm_in != ''
                    AND b.pm_out IS NOT NULL AND b.pm_out != ''
                    THEN 1 ELSE 0 
                END) / COUNT(DISTINCT b.id)) DESC
        ");
        
        $results = $query ? $query->result() : [];
        
        // Calculate compliance rate for each department
        foreach ($results as &$dept) {
            $dept->compliance_rate = $dept->total_records > 0 
                ? round(($dept->complete_records / $dept->total_records) * 100, 1) 
                : 0;
        }
        
        return $results;
    }

    /**
     * Get attendance by day of week (supports both weeks parameter and date range)
     */
    public function getAttendanceByDayOfWeek($weeks = null, $start_date = null, $end_date = null)
    {
        if (!$this->db->table_exists('biometrics')) {
            return [];
        }
        
        // Handle different parameter combinations
        if ($weeks !== null) {
            // Original behavior - use weeks parameter
            $start_date = date('Y-m-d', strtotime("-" . ($weeks * 7) . " days"));
            $end_date = date('Y-m-d');
            
            $query = $this->db->query("
                SELECT 
                    DAYOFWEEK(date) as day_num,
                    DAYNAME(date) as day_name,
                    WEEK(date) as week_num,
                    COUNT(*) as attendance_count,
                    SUM(CASE 
                        WHEN am_in IS NOT NULL AND am_in != '' 
                        AND am_out IS NOT NULL AND am_out != ''
                        AND pm_in IS NOT NULL AND pm_in != ''
                        AND pm_out IS NOT NULL AND pm_out != ''
                        THEN 1 ELSE 0 
                    END) as complete_count
                FROM biometrics
                WHERE date BETWEEN '$start_date' AND '$end_date'
                AND DAYOFWEEK(date) NOT IN (1, 7)
                GROUP BY DAYOFWEEK(date), WEEK(date)
                ORDER BY WEEK(date), DAYOFWEEK(date)
            ");
        } else {
            // New behavior - use date range parameters
            if (!$start_date) $start_date = date('Y-m-01');
            if (!$end_date) $end_date = date('Y-m-t');
            
            $query = $this->db->query("
                SELECT 
                    DAYOFWEEK(date) as day_num,
                    DAYNAME(date) as day_name,
                    COUNT(DISTINCT id) as total_records,
                    SUM(CASE 
                        WHEN am_in IS NOT NULL AND am_in != '' 
                        AND pm_out IS NOT NULL AND pm_out != ''
                        THEN 1 ELSE 0 
                    END) as complete_records
                FROM biometrics
                WHERE date BETWEEN '$start_date' AND '$end_date'
                AND DAYOFWEEK(date) NOT IN (1, 7)
                GROUP BY DAYOFWEEK(date), DAYNAME(date)
                ORDER BY DAYOFWEEK(date)
            ");
        }
        
        return $query ? $query->result() : [];
    }

    /**
     * Get undertime analysis by department
     */
    public function getUndertimeByDepartment($start_date = null, $end_date = null)
    {
        if (!$start_date) $start_date = date('Y-m-01');
        if (!$end_date) $end_date = date('Y-m-t');
        
        if (!$this->db->table_exists('departments') || !$this->db->table_exists('biometrics') || !$this->db->table_exists('personnels')) {
            return [];
        }
        
        $query = $this->db->query("
            SELECT 
                d.id,
                d.name,
                d.color,
                COALESCE(SUM(b.undertime_hours), 0) as total_undertime_hours,
                COALESCE(SUM(b.undertime_minutes), 0) as total_undertime_minutes,
                COUNT(DISTINCT p.id) as personnel_count
            FROM departments d
            LEFT JOIN personnels p ON p.department_id = d.id AND p.status = 1
            LEFT JOIN biometrics b ON b.bio_id = p.bio_id 
                AND b.date BETWEEN '$start_date' AND '$end_date'
            WHERE d.status = 1
            GROUP BY d.id
            ORDER BY (COALESCE(SUM(b.undertime_hours), 0) * 60 + COALESCE(SUM(b.undertime_minutes), 0)) DESC
        ");
        
        $results = $query ? $query->result() : [];
        
        // Convert to total hours
        foreach ($results as &$dept) {
            $total_minutes = ($dept->total_undertime_hours * 60) + $dept->total_undertime_minutes;
            $dept->total_undertime = round($total_minutes / 60, 1);
        }
        
        return $results;
    }

    /**
     * Get personnel status distribution
     */
    public function getPersonnelStatusDistribution()
    {
        if (!$this->db->table_exists('personnels')) {
            return ['active' => 0, 'inactive' => 0, 'regular' => 0, 'contract' => 0, 'total' => 0];
        }
        
        $query = $this->db->query("
            SELECT 
                SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN status = 0 OR status IS NULL THEN 1 ELSE 0 END) as inactive,
                SUM(CASE WHEN employment_type = 'Regular' AND status = 1 THEN 1 ELSE 0 END) as regular,
                SUM(CASE WHEN (employment_type != 'Regular' OR employment_type IS NULL) AND status = 1 THEN 1 ELSE 0 END) as contract,
                COUNT(*) as total
            FROM personnels
        ");
        
        $result = $query ? $query->row() : null;
        
        return $result ? (array)$result : ['active' => 0, 'inactive' => 0, 'regular' => 0, 'contract' => 0, 'total' => 0];
    }

    /**
     * Get missing entries breakdown
     */
    public function getMissingEntriesBreakdown($start_date = null, $end_date = null)
    {
        if (!$start_date) $start_date = date('Y-m-01');
        if (!$end_date) $end_date = date('Y-m-t');
        
        if (!$this->db->table_exists('biometrics')) {
            return ['am_in' => 0, 'am_out' => 0, 'pm_in' => 0, 'pm_out' => 0];
        }
        
        $query = $this->db->query("
            SELECT 
                SUM(CASE WHEN (am_in IS NULL OR am_in = '') AND (am_out IS NOT NULL OR pm_in IS NOT NULL OR pm_out IS NOT NULL) THEN 1 ELSE 0 END) as missing_am_in,
                SUM(CASE WHEN (am_out IS NULL OR am_out = '') AND (am_in IS NOT NULL OR pm_in IS NOT NULL OR pm_out IS NOT NULL) THEN 1 ELSE 0 END) as missing_am_out,
                SUM(CASE WHEN (pm_in IS NULL OR pm_in = '') AND (am_in IS NOT NULL OR am_out IS NOT NULL OR pm_out IS NOT NULL) THEN 1 ELSE 0 END) as missing_pm_in,
                SUM(CASE WHEN (pm_out IS NULL OR pm_out = '') AND (am_in IS NOT NULL OR am_out IS NOT NULL OR pm_in IS NOT NULL) THEN 1 ELSE 0 END) as missing_pm_out
            FROM biometrics
            WHERE date BETWEEN '$start_date' AND '$end_date'
            AND DAYOFWEEK(date) NOT IN (1, 7)
            AND (am_in IS NOT NULL OR am_out IS NOT NULL OR pm_in IS NOT NULL OR pm_out IS NOT NULL)
        ");
        
        $result = $query ? $query->row() : null;
        
        return $result ? [
            'am_in' => (int)$result->missing_am_in,
            'am_out' => (int)$result->missing_am_out,
            'pm_in' => (int)$result->missing_pm_in,
            'pm_out' => (int)$result->missing_pm_out
        ] : ['am_in' => 0, 'am_out' => 0, 'pm_in' => 0, 'pm_out' => 0];
    }

    /**
     * Get audit activity summary
     */
    public function getAuditActivitySummary($days = 30)
    {
        if (!$this->db->table_exists('audit_trail')) {
            return ['total_edits' => 0, 'daily_data' => [], 'top_editors' => []];
        }
        
        $start_date = date('Y-m-d', strtotime("-" . ($days - 1) . " days"));
        $end_date = date('Y-m-d');
        
        // Total edits
        $total_query = $this->db->query("
            SELECT COUNT(*) as total FROM audit_trail 
            WHERE DATE(created_at) BETWEEN '$start_date' AND '$end_date'
        ");
        $total = $total_query ? $total_query->row()->total : 0;
        
        // Daily edits
        $daily_query = $this->db->query("
            SELECT DATE(created_at) as edit_date, COUNT(*) as edit_count
            FROM audit_trail
            WHERE DATE(created_at) BETWEEN '$start_date' AND '$end_date'
            GROUP BY DATE(created_at)
            ORDER BY edit_date ASC
        ");
        $daily_data = $daily_query ? $daily_query->result() : [];
        
        // Top editors
        $editors_query = $this->db->query("
            SELECT admin_name, COUNT(*) as edit_count
            FROM audit_trail
            WHERE DATE(created_at) BETWEEN '$start_date' AND '$end_date'
            GROUP BY admin_user_id
            ORDER BY edit_count DESC
            LIMIT 5
        ");
        $top_editors = $editors_query ? $editors_query->result() : [];
        
        return [
            'total_edits' => $total,
            'daily_data' => $daily_data,
            'top_editors' => $top_editors
        ];
    }

    /**
     * Get attendance trend comparison (current vs previous period)
     */
    public function getAttendanceTrendComparison()
    {
        if (!$this->db->table_exists('biometrics')) {
            return ['current' => 0, 'previous' => 0, 'change' => 0, 'change_percent' => 0];
        }
        
        $current_start = date('Y-m-01');
        $current_end = date('Y-m-d');
        $previous_start = date('Y-m-01', strtotime('-1 month'));
        $previous_end = date('Y-m-t', strtotime('-1 month'));
        
        // Current month attendance
        $current_query = $this->db->query("
            SELECT COUNT(DISTINCT CONCAT(bio_id, '-', date)) as count 
            FROM biometrics 
            WHERE date BETWEEN '$current_start' AND '$current_end'
            AND (am_in IS NOT NULL OR pm_in IS NOT NULL)
        ");
        $current = $current_query ? $current_query->row()->count : 0;
        
        // Previous month attendance
        $previous_query = $this->db->query("
            SELECT COUNT(DISTINCT CONCAT(bio_id, '-', date)) as count 
            FROM biometrics 
            WHERE date BETWEEN '$previous_start' AND '$previous_end'
            AND (am_in IS NOT NULL OR pm_in IS NOT NULL)
        ");
        $previous = $previous_query ? $previous_query->row()->count : 0;
        
        $change = $current - $previous;
        $change_percent = $previous > 0 ? round(($change / $previous) * 100, 1) : 0;
        
        return [
            'current' => $current,
            'previous' => $previous,
            'change' => $change,
            'change_percent' => $change_percent
        ];
    }

    /**
     * Get peak attendance hours
     */
    public function getPeakAttendanceHours($start_date = null, $end_date = null)
    {
        if (!$start_date) $start_date = date('Y-m-01');
        if (!$end_date) $end_date = date('Y-m-t');
        
        if (!$this->db->table_exists('biometrics')) {
            return [];
        }
        
        $query = $this->db->query("
            SELECT 
                HOUR(am_in) as hour,
                COUNT(*) as count,
                'AM IN' as type
            FROM biometrics
            WHERE date BETWEEN '$start_date' AND '$end_date'
            AND am_in IS NOT NULL AND am_in != ''
            GROUP BY HOUR(am_in)
            
            UNION ALL
            
            SELECT 
                HOUR(pm_out) as hour,
                COUNT(*) as count,
                'PM OUT' as type
            FROM biometrics
            WHERE date BETWEEN '$start_date' AND '$end_date'
            AND pm_out IS NOT NULL AND pm_out != ''
            GROUP BY HOUR(pm_out)
            
            ORDER BY hour
        ");
        
        return $query ? $query->result() : [];
    }

    /**
     * Get top attendees for a specific date range (filter-based)
     */
    public function getTopAttendeesFiltered($start_date = null, $end_date = null, $limit = 5)
    {
        if (!$this->db->table_exists('biometrics') || !$this->db->table_exists('personnels')) {
            return [];
        }
        
        if (!$start_date) $start_date = date('Y-m-01');
        if (!$end_date) $end_date = date('Y-m-t');
        
        $query = $this->db->query("
            SELECT 
                p.id,
                p.firstname,
                p.lastname,
                p.profile_image,
                d.name as department_name,
                d.color as department_color,
                COUNT(DISTINCT b.date) as total_days,
                SUM(CASE 
                    WHEN b.am_in IS NOT NULL AND b.am_in != '' 
                    AND b.pm_out IS NOT NULL AND b.pm_out != ''
                    THEN 1 ELSE 0 
                END) as complete_days
            FROM personnels p
            INNER JOIN biometrics b ON b.bio_id = p.bio_id
            LEFT JOIN departments d ON d.id = p.department_id
            WHERE b.date BETWEEN '$start_date' AND '$end_date'
            AND DAYOFWEEK(b.date) NOT IN (1, 7)
            AND p.status = 1
            GROUP BY p.id
            ORDER BY complete_days DESC, total_days DESC
            LIMIT $limit
        ");
        
        return $query ? $query->result() : [];
    }

    /**
     * Get personnel with perfect attendance for a specific date range
     */
    public function getPerfectAttendance($start_date = null, $end_date = null, $limit = 10)
    {
        if (!$this->db->table_exists('biometrics') || !$this->db->table_exists('personnels')) {
            return [];
        }
        
        if (!$start_date) $start_date = date('Y-m-01');
        if (!$end_date) $end_date = date('Y-m-t');
        
        // Calculate working days in the period (excluding weekends)
        $working_days_query = $this->db->query("
            SELECT COUNT(*) as working_days
            FROM (
                SELECT DATE('$start_date') + INTERVAL seq DAY as date
                FROM (
                    SELECT @row := @row + 1 as seq
                    FROM (SELECT 0 UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) a,
                         (SELECT 0 UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) b,
                         (SELECT @row := -1) r
                ) numbers
                WHERE DATE('$start_date') + INTERVAL seq DAY <= '$end_date'
            ) dates
            WHERE DAYOFWEEK(date) NOT IN (1, 7)
        ");
        
        $working_days = 0;
        if ($working_days_query && $working_days_query->num_rows() > 0) {
            $working_days = $working_days_query->row()->working_days;
        }
        
        // If we can't calculate working days, estimate based on date range
        if ($working_days == 0) {
            $start = new DateTime($start_date);
            $end = new DateTime($end_date);
            $interval = $start->diff($end);
            $total_days = $interval->days + 1;
            $working_days = floor($total_days * 5 / 7); // Rough estimate
        }
        
        $query = $this->db->query("
            SELECT 
                p.id,
                p.firstname,
                p.lastname,
                p.profile_image,
                p.bio_id,
                d.name as department_name,
                d.color as department_color,
                COUNT(DISTINCT b.date) as days_present,
                SUM(CASE 
                    WHEN b.am_in IS NOT NULL AND b.am_in != '' 
                    AND b.am_out IS NOT NULL AND b.am_out != ''
                    AND b.pm_in IS NOT NULL AND b.pm_in != ''
                    AND b.pm_out IS NOT NULL AND b.pm_out != ''
                    THEN 1 ELSE 0 
                END) as complete_days,
                $working_days as required_days
            FROM personnels p
            INNER JOIN biometrics b ON b.bio_id = p.bio_id
            LEFT JOIN departments d ON d.id = p.department_id
            WHERE b.date BETWEEN '$start_date' AND '$end_date'
            AND DAYOFWEEK(b.date) NOT IN (1, 7)
            AND p.status = 1
            GROUP BY p.id
            HAVING complete_days >= $working_days
            ORDER BY complete_days DESC, days_present DESC
            LIMIT $limit
        ");
        
        return $query ? $query->result() : [];
    }

    /**
     * Get missing entries progression by month
     */
    public function getMissingEntriesProgression($from_month = null, $to_month = null)
    {
        if (!$this->db->table_exists('biometrics')) {
            return [];
        }
        
        // Default to last 6 months if not specified
        if (!$from_month) $from_month = date('Y-m', strtotime('-5 months'));
        if (!$to_month) $to_month = date('Y-m');
        
        $start_date = $from_month . '-01';
        $end_date = date('Y-m-t', strtotime($to_month . '-01'));
        
        $query = $this->db->query("
            SELECT 
                DATE_FORMAT(date, '%Y-%m') as month,
                DATE_FORMAT(date, '%b %Y') as month_label,
                SUM(CASE WHEN (am_in IS NULL OR am_in = '') THEN 1 ELSE 0 END) as am_in_missing,
                SUM(CASE WHEN (am_out IS NULL OR am_out = '') THEN 1 ELSE 0 END) as am_out_missing,
                SUM(CASE WHEN (pm_in IS NULL OR pm_in = '') THEN 1 ELSE 0 END) as pm_in_missing,
                SUM(CASE WHEN (pm_out IS NULL OR pm_out = '') THEN 1 ELSE 0 END) as pm_out_missing
            FROM biometrics
            WHERE date BETWEEN '$start_date' AND '$end_date'
            AND DAYOFWEEK(date) NOT IN (1, 7)
            GROUP BY DATE_FORMAT(date, '%Y-%m')
            ORDER BY month ASC
        ");
        
        return $query ? $query->result() : [];
    }
}
