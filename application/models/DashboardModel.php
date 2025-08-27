<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DashboardModel extends CI_Model
{

    public function __contruct()
    {
        $this->load->database();
    }

    public function personnel()
    {
        $this->db->where('status', 1);
        $query = $this->db->get('personnels');
        return $query->num_rows();
    }

    public function todayAttendance()
    {
        $this->db->where('date', date('Y-m-d'));
        $query = $this->db->get('attendance');
        return $query->num_rows();
    }

    public function todayBiometrics()
    {
        $this->db->where('date', date('Y-m-d'));
        $query = $this->db->get('biometrics');
        return $query->num_rows();
    }

    public function monthlyAttendance()
    {
        $this->db->where('MONTH(date)', date('m'));
        $this->db->where('YEAR(date)', date('Y'));
        $query = $this->db->get('attendance');
        return $query->num_rows();
    }

    public function monthlyBiometrics()
    {
        $this->db->where('MONTH(date)', date('m'));
        $this->db->where('YEAR(date)', date('Y'));
        $query = $this->db->get('biometrics');
        return $query->num_rows();
    }

    public function getAttendanceByDay($days = 7)
    {
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $this->db->where('date', $date);
            $attendance = $this->db->get('attendance')->num_rows();
            
            $this->db->where('date', $date);
            $biometrics = $this->db->get('biometrics')->num_rows();
            
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
        $stats = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = date('Y-m-01', strtotime("-$i months"));
            $month = date('m', strtotime($date));
            $year = date('Y', strtotime($date));
            
            $this->db->where('MONTH(date)', $month);
            $this->db->where('YEAR(date)', $year);
            $attendance = $this->db->get('attendance')->num_rows();
            
            $this->db->where('MONTH(date)', $month);
            $this->db->where('YEAR(date)', $year);
            $biometrics = $this->db->get('biometrics')->num_rows();
            
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
        $total_personnel = $this->personnel();
        $working_days = date('j'); // Current day of month
        $expected_records = $total_personnel * $working_days;
        
        $this->db->where('MONTH(date)', date('m'));
        $this->db->where('YEAR(date)', date('Y'));
        $actual_records = $this->db->get('attendance')->num_rows();
        
        $this->db->where('MONTH(date)', date('m'));
        $this->db->where('YEAR(date)', date('Y'));
        $biometric_records = $this->db->get('biometrics')->num_rows();
        
        $total_actual = $actual_records + $biometric_records;
        
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
