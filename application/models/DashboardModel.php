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
