<?php
defined('BASEPATH') or exit('No direct script access allowed');

class BiometricsModel extends CI_Model
{
    var $table = 'biometrics';
    var $column_order = array(null, 'firstname', 'lastname', 'middlename', 'date', 'am_in', 'am_out', 'pm_in', 'pm_out', 'undertime_hours', 'undertime_minutes'); //set column field database for datatable orderable
    var $column_search = array('firstname', 'lastname', 'middlename', 'date', 'am_in', 'am_out', 'pm_in', 'pm_out', 'undertime_hours', 'undertime_minutes'); //set column field database for datatable searchable 
    var $order = array('biometrics.date' => 'desc'); // default order 

    public function __contruct()
    {
        $this->load->database();
    }

    private function _get_datatables_query($date = '')
    {
        if ($date) {
            $month = date('m', strtotime($date));
            $year = date('Y', strtotime($date));
        }


        $this->db->select('*, biometrics.id as id');
        $this->db->from($this->table);

        $i = 0;

        foreach ($this->column_search as $item) // loop column 
        {
            if ($_POST['search']['value']) // if datatable send POST for search
            {

                if ($i === 0) // first loop
                {
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if (count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }
        $this->db->join('personnels ', 'personnels.bio_id=biometrics.bio_id');

        if ($date) {
            $this->db->where('MONTH(biometrics.date)',  $month);
            $this->db->where('YEAR(biometrics.date)',  $year);
        } else {
            $this->db->where('MONTH(biometrics.date)',  date('m'));
            $this->db->where('YEAR(biometrics.date)',  date('Y'));
        }


        if (isset($_POST['order'])) // here order processing
        {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables($date = '')
    {
        $this->_get_datatables_query($date);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered($date = '')
    {
        if ($date) {
            $month = date('m', strtotime($date));
            $year = date('Y', strtotime($date));
        }

        $this->_get_datatables_query($date);
        if ($date) {
            $this->db->where('MONTH(biometrics.date)',  $month);
            $this->db->where('YEAR(biometrics.date)',  $year);
        } else {
            $this->db->where('MONTH(biometrics.date)',  date('m'));
            $this->db->where('YEAR(biometrics.date)',  date('Y'));
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($date = '')
    {
        if ($date) {
            $month = date('m', strtotime($date));
            $year = date('Y', strtotime($date));
        }
        $this->db->from($this->table);

        if ($date) {
            $this->db->where('MONTH(biometrics.date)',  $month);
            $this->db->where('YEAR(biometrics.date)',  $year);
        } else {
            $this->db->where('MONTH(biometrics.date)',  date('m'));
            $this->db->where('YEAR(biometrics.date)',  date('Y'));
        }

        return $this->db->count_all_results();
    }

    public function bio($date)
    {
        if ($date) {
            $this->db->where('biometrics.date', $date);
        } else {
            $this->db->where('biometrics.date', date('Y-m-d'));
        }
        $this->db->join('personnels', 'personnels.bio_id=biometrics.bio_id');
        $this->db->order_by('personnels.lastname', 'ASC');
        $query = $this->db->get('biometrics');
        return $query->result();
    }

    public function save($data, $reason = null)
    {
        $result = $this->db->insert('biometrics', $data);
        
        // CREATE actions are not logged in audit trail
        
        return $this->db->affected_rows();
    }
    public function update($data, $id, $reason = null)
    {
        // Get old data before update for audit trail
        $old_data = $this->getBiometric($id);
        $old_array = $old_data ? (array)$old_data : null;
        
        $this->db->update('biometrics', $data, "id='$id'");
        $affected_rows = $this->db->affected_rows();
        
        // Log audit trail for UPDATE action
        if ($affected_rows > 0) {
            $this->load->model('AuditTrailModel', 'auditModel');
            $this->auditModel->log_biometric_change($id, 'UPDATE', $old_array, $data, $reason);
        }
        
        return $affected_rows;
    }
    public function delete($id, $reason = null)
    {
        // DELETE actions are not logged in audit trail
        
        $this->db->where('id', $id);
        $this->db->delete('biometrics');
        $affected_rows = $this->db->affected_rows();
        
        return $affected_rows;
    }

    public function getBiometric($id)
    {
        $this->db->select('*, biometrics.id as id');
        $this->db->join('personnels', 'personnels.bio_id=biometrics.bio_id');
        $this->db->where('biometrics.id', $id);
        $query = $this->db->get('biometrics');
        return $query->row();
    }
    public function getBio($id, $date)
    {
        $this->db->where('biometrics.bio_id', $id);
        $this->db->where('biometrics.date', $date);
        $query = $this->db->get('biometrics');
        return $query->row();
    }

    public function checkPersonnelExists($bio_id)
    {
        $this->db->where('bio_id', $bio_id);
        $query = $this->db->get('personnels');
        return $query->num_rows() > 0;
    }

    /**
     * Get failure to clock in/out summary for all employees
     * Returns records where at least one time slot has data but others are missing
     * Excludes weekends and holidays, and full-day absences (all 4 slots empty)
     */
    public function getFailureToClockSummary($start_date = null, $end_date = null)
    {
        // Default to current month if no dates provided
        if (!$start_date) {
            $start_date = date('Y-m-01');
        }
        if (!$end_date) {
            $end_date = date('Y-m-t');
        }

        // Get all biometric records with at least one time entry (not full-day absence)
        $this->db->select('biometrics.*, personnels.firstname, personnels.lastname, personnels.middlename, personnels.bio_id');
        $this->db->from('biometrics');
        $this->db->join('personnels', 'personnels.bio_id = biometrics.bio_id');
        $this->db->where('biometrics.date >=', $start_date);
        $this->db->where('biometrics.date <=', $end_date);
        
        // Only get records where at least one time slot has data (not full-day absence)
        $this->db->group_start();
        $this->db->where('biometrics.am_in IS NOT NULL');
        $this->db->or_where('biometrics.am_out IS NOT NULL');
        $this->db->or_where('biometrics.pm_in IS NOT NULL');
        $this->db->or_where('biometrics.pm_out IS NOT NULL');
        $this->db->group_end();
        
        $this->db->order_by('biometrics.date', 'ASC');
        $this->db->order_by('personnels.lastname', 'ASC');
        
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Get dashboard chart data for edits and missing logs
     * Returns total edits count and missing logs count for specific months
     * @param string $edits_month - Month for counting edits (YYYY-MM)
     * @param string $dtr_month - Month for counting DTR missing logs (YYYY-MM)
     */
    public function getDashboardChartData($edits_month = null, $dtr_month = null)
    {
        if (!$edits_month) {
            $edits_month = date('Y-m');
        }
        if (!$dtr_month) {
            $dtr_month = date('Y-m');
        }
        
        // Parse edits month
        list($edit_year, $edit_month) = explode('-', $edits_month);
        $edit_start_date = $edit_year . '-' . $edit_month . '-01';
        $edit_end_date = date('Y-m-t', strtotime($edit_start_date));
        
        // Parse DTR month
        list($dtr_year, $dtr_month_num) = explode('-', $dtr_month);
        $dtr_start_date = $dtr_year . '-' . $dtr_month_num . '-01';
        $dtr_end_date = date('Y-m-t', strtotime($dtr_start_date));
        
        // Get total edits count from audit_trail for biometrics updates
        $this->db->select('COUNT(*) as total_edits');
        $this->db->from('audit_trail');
        $this->db->where('table_name', 'biometrics');
        $this->db->where('action_type', 'UPDATE');
        $this->db->where('DATE(created_at) >=', $edit_start_date);
        $this->db->where('DATE(created_at) <=', $edit_end_date);
        $edit_query = $this->db->get();
        $total_edits = $edit_query->row()->total_edits;
        
        // Get missing logs count (failures to clock in/out)
        // Get all biometric records with at least one time entry (not full-day absence)
        $this->db->select('biometrics.*');
        $this->db->from('biometrics');
        $this->db->join('personnels', 'personnels.bio_id = biometrics.bio_id');
        $this->db->where('biometrics.date >=', $dtr_start_date);
        $this->db->where('biometrics.date <=', $dtr_end_date);
        
        // Only get records where at least one time slot has data (not full-day absence)
        $this->db->group_start();
        $this->db->where('biometrics.am_in IS NOT NULL');
        $this->db->or_where('biometrics.am_out IS NOT NULL');
        $this->db->or_where('biometrics.pm_in IS NOT NULL');
        $this->db->or_where('biometrics.pm_out IS NOT NULL');
        $this->db->group_end();
        
        $query = $this->db->get();
        $records = $query->result();
        
        // Pre-fetch all holidays for the date range (single query)
        $this->load->model('HolidayModel', 'holidayModel');
        $holidays_query = $this->db->query("
            SELECT date FROM holidays 
            WHERE date BETWEEN '$dtr_start_date' AND '$dtr_end_date'
            AND status = 1
        ");
        $holidays = [];
        foreach ($holidays_query->result() as $h) {
            $holidays[$h->date] = true;
        }
        
        // Count missing logs (same logic as failure_summary)
        $missing_logs_count = 0;
        foreach ($records as $record) {
            $date = $record->date;
            $day_of_week = date('w', strtotime($date));
            
            // Skip weekends (Sunday = 0, Saturday = 6)
            if ($day_of_week == 0 || $day_of_week == 6) {
                continue;
            }
            
            // Check if it's a holiday (using pre-fetched data)
            if (isset($holidays[$date])) {
                continue;
            }
            
            // Count missing entries (each missing field is one incident)
            if (empty($record->am_in)) {
                $missing_logs_count++;
            }
            if (empty($record->am_out)) {
                $missing_logs_count++;
            }
            if (empty($record->pm_in)) {
                $missing_logs_count++;
            }
            if (empty($record->pm_out)) {
                $missing_logs_count++;
            }
        }
        
        return array(
            'total_edits' => (int)$total_edits,
            'missing_logs' => $missing_logs_count,
            'edits_month' => date('F Y', strtotime($edit_start_date)),
            'dtr_month' => date('F Y', strtotime($dtr_start_date))
        );
    }

    /**
     * Check if a date is a Philippine holiday
     * Uses database-driven holidays from HolidayModel
     */
    private function isPhilippineHoliday($date, $department_id = null)
    {
        $this->load->model('HolidayModel', 'holidayModel');
        return $this->holidayModel->is_holiday($date, $department_id);
    }
}
