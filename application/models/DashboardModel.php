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
            $month = date('m');
            $year = date('Y');
            
            // Single query to get all basic stats
            $query = $this->db->query("
                SELECT 
                    (SELECT COUNT(*) FROM personnels WHERE status = 1) as personnel_count,
                    (SELECT COUNT(*) FROM attendance WHERE date = '$today') as today_attendance,
                    (SELECT COUNT(*) FROM biometrics WHERE date = '$today') as today_biometrics,
                    (SELECT COUNT(*) FROM attendance WHERE MONTH(date) = $month AND YEAR(date) = $year) as monthly_attendance,
                    (SELECT COUNT(*) FROM biometrics WHERE MONTH(date) = $month AND YEAR(date) = $year) as monthly_biometrics
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
        
        // Single query for attendance counts grouped by date
        $attendance_query = $this->db->query("
            SELECT date, COUNT(*) as count 
            FROM attendance 
            WHERE date BETWEEN '$start_date' AND '$end_date' 
            GROUP BY date
        ");
        $attendance_counts = [];
        foreach ($attendance_query->result() as $row) {
            $attendance_counts[$row->date] = $row->count;
        }
        
        // Single query for biometrics counts grouped by date
        $biometrics_query = $this->db->query("
            SELECT date, COUNT(*) as count 
            FROM biometrics 
            WHERE date BETWEEN '$start_date' AND '$end_date' 
            GROUP BY date
        ");
        $biometrics_counts = [];
        foreach ($biometrics_query->result() as $row) {
            $biometrics_counts[$row->date] = $row->count;
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
        
        // Single query for attendance counts grouped by month
        $attendance_query = $this->db->query("
            SELECT DATE_FORMAT(date, '%Y-%m') as month_key, COUNT(*) as count 
            FROM attendance 
            WHERE date BETWEEN '$start_date' AND '$end_date' 
            GROUP BY DATE_FORMAT(date, '%Y-%m')
        ");
        $attendance_counts = [];
        foreach ($attendance_query->result() as $row) {
            $attendance_counts[$row->month_key] = $row->count;
        }
        
        // Single query for biometrics counts grouped by month
        $biometrics_query = $this->db->query("
            SELECT DATE_FORMAT(date, '%Y-%m') as month_key, COUNT(*) as count 
            FROM biometrics 
            WHERE date BETWEEN '$start_date' AND '$end_date' 
            GROUP BY DATE_FORMAT(date, '%Y-%m')
        ");
        $biometrics_counts = [];
        foreach ($biometrics_query->result() as $row) {
            $biometrics_counts[$row->month_key] = $row->count;
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
        $this->db->select('p.firstname, p.lastname, COUNT(*) as total_days');
        $this->db->from('attendance a');
        $this->db->join('personnels p', 'p.email = a.email');
        $this->db->where('MONTH(a.date)', date('m'));
        $this->db->where('YEAR(a.date)', date('Y'));
        $this->db->group_by('a.email');
        $this->db->order_by('total_days', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get();
        return $query->result();
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
        $this->db->select('a.date, p.firstname, p.lastname, a.morning_in, a.afternoon_out');
        $this->db->from('attendance a');
        $this->db->join('personnels p', 'p.email = a.email');
        $this->db->order_by('a.date', 'DESC');
        $this->db->order_by('a.id', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get();
        return $query->result();
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
}
