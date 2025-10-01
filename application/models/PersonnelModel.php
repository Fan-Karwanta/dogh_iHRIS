<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PersonnelModel extends CI_Model
{

    public function __contruct()
    {
        $this->load->database();
    }

    public function personnels($id = "")
    {
        if ($id) {
            $this->db->where('id', $id);
        }
        $query = $this->db->get('personnels');
        return $query->result();
    }

    public function getpersonnel($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('personnels');
        return $query->row();
    }

    public function create_personnel($data)
    {
        // Add timestamp if not provided
        if (!isset($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        
        $this->db->insert('personnels', $data);
        return $this->db->affected_rows();
    }

    public function create_personnel_batch($data_array)
    {
        if (empty($data_array)) {
            return 0;
        }
        
        // Add timestamps to all records
        foreach ($data_array as &$data) {
            if (!isset($data['created_at'])) {
                $data['created_at'] = date('Y-m-d H:i:s');
            }
        }
        
        $this->db->insert_batch('personnels', $data_array);
        return $this->db->affected_rows();
    }

    public function get_personnel_by_bio_id($bio_id)
    {
        $this->db->where('bio_id', $bio_id);
        $query = $this->db->get('personnels');
        return $query->row();
    }

    public function get_personnel_statistics()
    {
        $stats = new stdClass();
        
        // Total personnel count
        $stats->total = $this->db->count_all('personnels');
        
        // Count by employment type
        $this->db->select('employment_type, COUNT(*) as count');
        $this->db->group_by('employment_type');
        $employment_stats = $this->db->get('personnels')->result();
        
        $stats->regular = 0;
        $stats->contract = 0;
        
        foreach ($employment_stats as $stat) {
            if ($stat->employment_type == 'Regular') {
                $stats->regular = $stat->count;
            } else {
                $stats->contract += $stat->count;
            }
        }
        
        // Active personnel (status = 1)
        $this->db->where('status', 1);
        $stats->active = $this->db->count_all_results('personnels');
        
        return $stats;
    }


    public function update($data, $id)
    {
        $this->db->update('personnels', $data, "id='$id'");
        return $this->db->affected_rows();
    }

    public function checkPersonnel($email)
    {
        $this->db->where('email', $email);
        $query = $this->db->get('personnels');
        return $query->num_rows();
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('personnels');
        return $this->db->affected_rows();
    }
}
