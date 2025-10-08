<?php
defined('BASEPATH') or exit('No direct script access allowed');

class TimeChangesModel extends CI_Model
{
    var $table = 'biometrics';
    var $column_order = array(null, 'date', 'am_in', 'am_out', 'pm_in', 'pm_out', 'undertime_hours', 'undertime_minutes');
    var $column_search = array('date', 'am_in', 'am_out', 'pm_in', 'pm_out');
    var $order = array('biometrics.date' => 'desc');

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private function _get_datatables_query($bio_id, $date = '')
    {
        $this->db->select('biometrics.*, personnels.firstname, personnels.lastname, personnels.middlename');
        $this->db->from($this->table);
        $this->db->join('personnels', 'personnels.bio_id = biometrics.bio_id');
        $this->db->where('biometrics.bio_id', $bio_id);

        if ($date) {
            $month = date('m', strtotime($date));
            $year = date('Y', strtotime($date));
            $this->db->where('MONTH(biometrics.date)', $month);
            $this->db->where('YEAR(biometrics.date)', $year);
        }

        $i = 0;
        foreach ($this->column_search as $item) {
            if (isset($_POST['search']['value']) && $_POST['search']['value']) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if (count($this->column_search) - 1 == $i) {
                    $this->db->group_end();
                }
            }
            $i++;
        }

        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables($bio_id, $date = '')
    {
        $this->_get_datatables_query($bio_id, $date);
        if (isset($_POST['length']) && $_POST['length'] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered($bio_id, $date = '')
    {
        $this->_get_datatables_query($bio_id, $date);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($bio_id, $date = '')
    {
        $this->db->from($this->table);
        $this->db->where('bio_id', $bio_id);

        if ($date) {
            $month = date('m', strtotime($date));
            $year = date('Y', strtotime($date));
            $this->db->where('MONTH(date)', $month);
            $this->db->where('YEAR(date)', $year);
        }

        return $this->db->count_all_results();
    }

    public function get_personnel_biometrics($bio_id, $limit = null, $offset = null)
    {
        $this->db->select('biometrics.*');
        $this->db->from($this->table);
        $this->db->where('bio_id', $bio_id);
        $this->db->order_by('date', 'DESC');
        
        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }
        
        $query = $this->db->get();
        return $query->result();
    }
}
