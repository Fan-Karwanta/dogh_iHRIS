<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AttendanceModel extends CI_Model
{
    var $table = 'attendance';
    var $column_order = array(null, 'firstname', 'lastname', 'middlename', 'date', 'attendance.email', 'morning_in', 'morning_out', 'afternoon_in', 'afternoon_out'); //set column field database for datatable orderable
    var $column_search = array('firstname', 'lastname', 'middlename', 'date', 'attendance.email', 'morning_in', 'morning_out', 'afternoon_in', 'afternoon_out'); //set column field database for datatable searchable 
    var $order = array('attendance.date' => 'desc'); // default order 

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


        $this->db->select('*, attendance.id as id');
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
        $this->db->join('personnels ', 'personnels.email=attendance.email');

        if ($date) {
            $this->db->where('MONTH(attendance.date)',  $month);
            $this->db->where('YEAR(attendance.date)',  $year);
        } else {
            $this->db->where('MONTH(attendance.date)',  date('m'));
            $this->db->where('YEAR(attendance.date)',  date('Y'));
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
            $this->db->where('MONTH(attendance.date)',  $month);
            $this->db->where('YEAR(attendance.date)',  $year);
        } else {
            $this->db->where('MONTH(attendance.date)',  date('m'));
            $this->db->where('YEAR(attendance.date)',  date('Y'));
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
            $this->db->where('MONTH(attendance.date)',  $month);
            $this->db->where('YEAR(attendance.date)',  $year);
        } else {
            $this->db->where('MONTH(attendance.date)',  date('m'));
            $this->db->where('YEAR(attendance.date)',  date('Y'));
        }

        return $this->db->count_all_results();
    }


    public function attendance($id = "")
    {
        $this->db->select('*, attendance.id as id');
        $this->db->join('personnels', 'personnels.email=attendance.email');
        $query = $this->db->get('attendance');
        return $query->result();
    }

    public function getAttendance($id)
    {
        $this->db->select('*, attendance.id as id');
        $this->db->join('personnels', 'personnels.email=attendance.email');
        $this->db->where('attendance.id', $id);
        $query = $this->db->get('attendance');
        return $query->row();
    }

    public function getmyAttendance($id, $date)
    {
        $this->db->select('*, attendance.id as id');
        $this->db->join('personnels', 'personnels.email=attendance.email');

        if ($date) {
            $month = date('m', strtotime($date));
            $year = date('Y', strtotime($date));

            $this->db->where('personnels.id', $id);
            $this->db->where('MONTH(attendance.date)',  $month);
            $this->db->where('YEAR(attendance.date)',  $year);
        } else {
            $this->db->where('personnels.id', $id);
            $this->db->where('MONTH(attendance.date)',  date('m'));
            $this->db->where('YEAR(attendance.date)',  date('Y'));
        }

        $query = $this->db->get('attendance');
        return $query->result();
    }

    public function getAttend($email, $date)
    {
        $this->db->where('attendance.email', $email);
        $this->db->where('attendance.date', $date);
        $query = $this->db->get('attendance');
        return $query->row();
    }

    public function save($data, $reason = null)
    {
        $result = $this->db->insert('attendance', $data);
        
        // Log audit trail for CREATE action
        if ($result && $this->db->affected_rows() > 0) {
            $this->load->model('AuditTrailModel', 'auditModel');
            $attendance_id = $this->db->insert_id();
            $this->auditModel->log_attendance_change($attendance_id, 'CREATE', null, $data, $reason);
        }
        
        return $this->db->affected_rows();
    }

    public function update($data, $id, $reason = null)
    {
        // Get old data before update for audit trail
        $old_data = $this->getAttendance($id);
        $old_array = $old_data ? (array)$old_data : null;
        
        $this->db->update('attendance', $data, "id='$id'");
        $affected_rows = $this->db->affected_rows();
        
        // Log audit trail for UPDATE action
        if ($affected_rows > 0) {
            $this->load->model('AuditTrailModel', 'auditModel');
            $this->auditModel->log_attendance_change($id, 'UPDATE', $old_array, $data, $reason);
        }
        
        return $affected_rows;
    }

    public function checkPersonnel($email)
    {
        $this->db->where('email', $email);
        $query = $this->db->get('personnels');
        return $query->num_rows();
    }

    public function delete($id, $reason = null)
    {
        // Get data before deletion for audit trail
        $old_data = $this->getAttendance($id);
        
        $this->db->where('id', $id);
        $this->db->delete('attendance');
        $affected_rows = $this->db->affected_rows();
        
        // Log audit trail for DELETE action
        if ($affected_rows > 0 && $old_data) {
            $this->load->model('AuditTrailModel', 'auditModel');
            $old_array = (array)$old_data;
            $this->auditModel->log_attendance_change($id, 'DELETE', $old_array, null, $reason);
        }
        
        return $affected_rows;
    }
}
