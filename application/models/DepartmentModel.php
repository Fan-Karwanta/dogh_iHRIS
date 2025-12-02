<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DepartmentModel extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
    }

    /**
     * Get all departments
     */
    public function get_all_departments($active_only = false)
    {
        if ($active_only) {
            $this->db->where('status', 1);
        }
        $this->db->order_by('name', 'ASC');
        return $this->db->get('departments')->result();
    }

    /**
     * Get single department by ID
     */
    public function get_department($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('departments')->row();
    }

    /**
     * Get department by code
     */
    public function get_department_by_code($code)
    {
        $this->db->where('code', $code);
        return $this->db->get('departments')->row();
    }

    /**
     * Create a new department
     */
    public function create_department($data)
    {
        $this->db->insert('departments', $data);
        return $this->db->insert_id();
    }

    /**
     * Update department
     */
    public function update_department($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('departments', $data);
        return $this->db->affected_rows();
    }

    /**
     * Delete department (soft delete by setting status to 0)
     */
    public function delete_department($id)
    {
        // First, unassign all personnel from this department
        $this->db->where('department_id', $id);
        $this->db->update('personnels', array('department_id' => null));
        
        // Then delete the department
        $this->db->where('id', $id);
        $this->db->delete('departments');
        return $this->db->affected_rows();
    }

    /**
     * Get all personnel grouped by department
     */
    public function get_personnel_by_department()
    {
        $result = array();
        
        // Get all departments
        $departments = $this->get_all_departments(true);
        
        foreach ($departments as $dept) {
            $this->db->where('department_id', $dept->id);
            $this->db->where('status', 1);
            $this->db->order_by('lastname', 'ASC');
            $personnel = $this->db->get('personnels')->result();
            
            $result[$dept->id] = array(
                'department' => $dept,
                'personnel' => $personnel,
                'count' => count($personnel)
            );
        }
        
        // Get unassigned personnel
        $this->db->where('department_id IS NULL', null, false);
        $this->db->where('status', 1);
        $this->db->order_by('lastname', 'ASC');
        $unassigned = $this->db->get('personnels')->result();
        
        $result['unassigned'] = array(
            'department' => (object) array(
                'id' => null,
                'name' => 'Unassigned',
                'code' => 'UNASSIGNED',
                'color' => '#95a5a6',
                'icon' => 'fas fa-user-slash'
            ),
            'personnel' => $unassigned,
            'count' => count($unassigned)
        );
        
        return $result;
    }

    /**
     * Get personnel for a specific department
     */
    public function get_department_personnel($department_id)
    {
        if ($department_id === null || $department_id === 'unassigned') {
            $this->db->where('department_id IS NULL', null, false);
        } else {
            $this->db->where('department_id', $department_id);
        }
        $this->db->where('status', 1);
        $this->db->order_by('lastname', 'ASC');
        return $this->db->get('personnels')->result();
    }

    /**
     * Assign personnel to department
     */
    public function assign_personnel_to_department($personnel_id, $department_id)
    {
        $this->db->where('id', $personnel_id);
        $this->db->update('personnels', array('department_id' => $department_id));
        return $this->db->affected_rows();
    }

    /**
     * Bulk assign personnel to department
     */
    public function bulk_assign_personnel($personnel_ids, $department_id)
    {
        if (empty($personnel_ids)) {
            return 0;
        }
        
        $this->db->where_in('id', $personnel_ids);
        $this->db->update('personnels', array('department_id' => $department_id));
        return $this->db->affected_rows();
    }

    /**
     * Get department statistics
     */
    public function get_department_statistics()
    {
        $stats = array();
        
        // Get count per department
        $this->db->select('d.id, d.name, d.code, d.color, d.icon, COUNT(p.id) as personnel_count');
        $this->db->from('departments d');
        $this->db->join('personnels p', 'p.department_id = d.id AND p.status = 1', 'left');
        $this->db->where('d.status', 1);
        $this->db->group_by('d.id');
        $stats['departments'] = $this->db->get()->result();
        
        // Get unassigned count
        $this->db->where('department_id IS NULL', null, false);
        $this->db->where('status', 1);
        $stats['unassigned_count'] = $this->db->count_all_results('personnels');
        
        // Get total personnel
        $this->db->where('status', 1);
        $stats['total_personnel'] = $this->db->count_all_results('personnels');
        
        return $stats;
    }

    /**
     * Search personnel across all departments
     */
    public function search_personnel($search_term)
    {
        $this->db->select('p.*, d.name as department_name, d.code as department_code, d.color as department_color');
        $this->db->from('personnels p');
        $this->db->join('departments d', 'd.id = p.department_id', 'left');
        $this->db->where('p.status', 1);
        $this->db->group_start();
        $this->db->like('p.firstname', $search_term);
        $this->db->or_like('p.lastname', $search_term);
        $this->db->or_like('p.middlename', $search_term);
        $this->db->or_like('p.email', $search_term);
        $this->db->or_like('p.position', $search_term);
        $this->db->group_end();
        $this->db->order_by('p.lastname', 'ASC');
        return $this->db->get()->result();
    }
}
