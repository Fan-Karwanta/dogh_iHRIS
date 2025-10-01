<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AuditTrailModel extends CI_Model
{
    var $table = 'audit_trail';
    var $column_order = array(null, 'created_at', 'personnel_name', 'table_name', 'action_type', 'field_name', 'admin_name', 'reason'); 
    var $column_search = array('personnel_name', 'personnel_email', 'table_name', 'action_type', 'field_name', 'admin_name', 'reason', 'old_value', 'new_value'); 
    var $order = array('created_at' => 'desc'); 

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Log an audit trail entry
     */
    public function log_audit($data)
    {
        // Get current user info
        $user = $this->ion_auth->user()->row();
        
        // Prepare audit data
        $audit_data = array(
            'table_name' => $data['table_name'],
            'record_id' => $data['record_id'],
            'action_type' => $data['action_type'],
            'field_name' => isset($data['field_name']) ? $data['field_name'] : null,
            'old_value' => isset($data['old_value']) ? $data['old_value'] : null,
            'new_value' => isset($data['new_value']) ? $data['new_value'] : null,
            'personnel_email' => isset($data['personnel_email']) ? $data['personnel_email'] : null,
            'personnel_name' => isset($data['personnel_name']) ? $data['personnel_name'] : null,
            'admin_user_id' => $user->id,
            'admin_name' => $user->first_name . ' ' . $user->last_name,
            'reason' => isset($data['reason']) ? $data['reason'] : null,
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'created_at' => date('Y-m-d H:i:s')
        );

        return $this->db->insert($this->table, $audit_data);
    }

    /**
     * Log biometric changes for audit trail
     * Only logs UPDATE actions - CREATE and DELETE are not tracked
     * Excludes undertime_hours and undertime_minutes from logging
     */
    public function log_biometric_change($biometric_id, $action, $old_data = null, $new_data = null, $reason = null)
    {
        // Only log UPDATE actions
        if ($action !== 'UPDATE') {
            return false;
        }

        // Get personnel info from biometric record
        $this->db->select('b.*, p.firstname, p.lastname, p.email');
        $this->db->from('biometrics b');
        $this->db->join('personnels p', 'p.bio_id = b.bio_id', 'left');
        $this->db->where('b.id', $biometric_id);
        $biometric = $this->db->get()->row();

        if (!$biometric) {
            return false;
        }

        $personnel_email = $biometric->email ?? 'unknown@email.com';
        $personnel_name = trim(($biometric->firstname ?? '') . ' ' . ($biometric->lastname ?? ''));

        // Fields to exclude from audit trail logging
        $excluded_fields = ['undertime_hours', 'undertime_minutes'];

        // Compare old and new data to identify changed fields
        $changed_fields = [];
        if ($old_data && $new_data) {
            foreach ($new_data as $field => $new_value) {
                // Skip excluded fields
                if (in_array($field, $excluded_fields)) {
                    continue;
                }
                
                $old_value = isset($old_data[$field]) ? $old_data[$field] : null;
                if ($old_value != $new_value) {
                    $changed_fields[] = [
                        'field' => $field,
                        'old_value' => $old_value,
                        'new_value' => $new_value
                    ];
                }
            }
        }

        // Log each changed field
        foreach ($changed_fields as $change) {
            $audit_data = [
                'table_name' => 'biometrics',
                'record_id' => $biometric_id,
                'action_type' => $action,
                'field_name' => $change['field'],
                'old_value' => $change['old_value'],
                'new_value' => $change['new_value'],
                'personnel_email' => $personnel_email,
                'personnel_name' => $personnel_name,
                'admin_user_id' => $this->ion_auth->get_user_id(),
                'admin_name' => $this->ion_auth->user()->row()->username ?? 'system',
                'reason' => $reason,
                'ip_address' => $this->input->ip_address(),
                'user_agent' => $this->input->user_agent(),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('audit_trail', $audit_data);
        }

        return true;
    }

    /**
     * Log attendance changes for audit trail
     * Only logs UPDATE actions - CREATE and DELETE are not tracked
     */
    public function log_attendance_change($attendance_id, $action, $old_data = null, $new_data = null, $reason = null)
    {
        // Only log UPDATE actions
        if ($action !== 'UPDATE') {
            return false;
        }

        // Get personnel info from attendance record
        $this->db->select('a.*, p.firstname, p.lastname, p.email');
        $this->db->from('attendance a');
        $this->db->join('personnels p', 'p.id = a.personnel_id', 'left');
        $this->db->where('a.id', $attendance_id);
        $attendance = $this->db->get()->row();

        if (!$attendance) {
            return false;
        }

        $personnel_email = $attendance->email ?? 'unknown@email.com';
        $personnel_name = trim(($attendance->firstname ?? '') . ' ' . ($attendance->lastname ?? ''));

        // Compare old and new data to identify changed fields
        $changed_fields = [];
        if ($old_data && $new_data) {
            foreach ($new_data as $field => $new_value) {
                $old_value = isset($old_data[$field]) ? $old_data[$field] : null;
                if ($old_value != $new_value) {
                    $changed_fields[] = [
                        'field' => $field,
                        'old_value' => $old_value,
                        'new_value' => $new_value
                    ];
                }
            }
        }

        // Log each changed field
        foreach ($changed_fields as $change) {
            $audit_data = [
                'table_name' => 'attendance',
                'record_id' => $attendance_id,
                'action_type' => $action,
                'field_name' => $change['field'],
                'old_value' => $change['old_value'],
                'new_value' => $change['new_value'],
                'personnel_email' => $personnel_email,
                'personnel_name' => $personnel_name,
                'admin_user_id' => $this->ion_auth->get_user_id(),
                'admin_name' => $this->ion_auth->user()->row()->username ?? 'system',
                'reason' => $reason,
                'ip_address' => $this->input->ip_address(),
                'user_agent' => $this->input->user_agent(),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('audit_trail', $audit_data);
        }

        return true;
    }

    /**
     * Get personnel info by attendance record
     */
    private function get_personnel_by_attendance($attendance_id, $attendance_data = null)
    {
        if ($attendance_data && isset($attendance_data['email'])) {
            $email = $attendance_data['email'];
        } else {
            // Get email from attendance record
            $this->db->select('email');
            $this->db->where('id', $attendance_id);
            $attendance = $this->db->get('attendance')->row();
            $email = $attendance ? $attendance->email : null;
        }

        if ($email) {
            $this->db->select('firstname, lastname, middlename, email');
            $this->db->where('email', $email);
            $personnel = $this->db->get('personnels')->row();
            
            if ($personnel) {
                return array(
                    'email' => $personnel->email,
                    'name' => $personnel->lastname . ', ' . $personnel->firstname . ' ' . $personnel->middlename
                );
            }
        }

        return array('email' => $email, 'name' => 'Unknown Personnel');
    }

    /**
     * DataTables server-side processing
     */
    private function _get_datatables_query($personnel_email = '', $date_from = '', $date_to = '')
    {
        $this->db->select('*');
        $this->db->from($this->table);

        // Apply filters
        if (!empty($personnel_email)) {
            $this->db->where('personnel_email', $personnel_email);
        }

        if (!empty($date_from)) {
            $this->db->where('DATE(created_at) >=', $date_from);
        }

        if (!empty($date_to)) {
            $this->db->where('DATE(created_at) <=', $date_to);
        }

        // Handle search if POST data exists
        if (isset($_POST['search']) && !empty($_POST['search']['value'])) {
            $i = 0;
            foreach ($this->column_search as $item) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if (count($this->column_search) - 1 == $i) {
                    $this->db->group_end();
                }
                $i++;
            }
        }

        // Handle ordering if POST data exists
        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function get_datatables($personnel_email = '', $date_from = '', $date_to = '')
    {
        $this->_get_datatables_query($personnel_email, $date_from, $date_to);
        if (isset($_POST['length']) && $_POST['length'] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered($personnel_email = '', $date_from = '', $date_to = '')
    {
        $this->_get_datatables_query($personnel_email, $date_from, $date_to);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($personnel_email = '', $date_from = '', $date_to = '')
    {
        $this->db->from($this->table);
        
        if (!empty($personnel_email)) {
            $this->db->where('personnel_email', $personnel_email);
        }

        if (!empty($date_from)) {
            $this->db->where('DATE(created_at) >=', $date_from);
        }

        if (!empty($date_to)) {
            $this->db->where('DATE(created_at) <=', $date_to);
        }

        return $this->db->count_all_results();
    }

    /**
     * Get audit trail for specific personnel
     */
    public function get_personnel_audit_history($personnel_email, $limit = 50, $offset = 0)
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('personnel_email', $personnel_email);
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit, $offset);
        
        return $this->db->get()->result();
    }

    /**
     * Get recent audit activities
     */
    public function get_recent_activities($limit = 100)
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit);
        
        return $this->db->get()->result();
    }

    /**
     * Get audit statistics
     */
    public function get_audit_statistics($date_from = null, $date_to = null)
    {
        $where_clause = '';
        if ($date_from && $date_to) {
            $where_clause = "WHERE DATE(created_at) BETWEEN '$date_from' AND '$date_to'";
        } elseif ($date_from) {
            $where_clause = "WHERE DATE(created_at) >= '$date_from'";
        } elseif ($date_to) {
            $where_clause = "WHERE DATE(created_at) <= '$date_to'";
        }

        // Total edits by action type
        $sql = "SELECT action_type, COUNT(*) as count FROM {$this->table} {$where_clause} GROUP BY action_type";
        $action_stats = $this->db->query($sql)->result();

        // Most edited personnel
        $sql = "SELECT personnel_name, personnel_email, COUNT(*) as edit_count 
                FROM {$this->table} 
                {$where_clause} 
                GROUP BY personnel_email 
                ORDER BY edit_count DESC 
                LIMIT 10";
        $personnel_stats = $this->db->query($sql)->result();

        // Most active admins
        $sql = "SELECT admin_name, admin_user_id, COUNT(*) as edit_count 
                FROM {$this->table} 
                {$where_clause} 
                GROUP BY admin_user_id 
                ORDER BY edit_count DESC 
                LIMIT 10";
        $admin_stats = $this->db->query($sql)->result();

        // Daily edit counts
        $sql = "SELECT DATE(created_at) as date, COUNT(*) as count 
                FROM {$this->table} 
                {$where_clause} 
                GROUP BY DATE(created_at) 
                ORDER BY date DESC 
                LIMIT 30";
        $daily_stats = $this->db->query($sql)->result();

        return array(
            'action_stats' => $action_stats,
            'personnel_stats' => $personnel_stats,
            'admin_stats' => $admin_stats,
            'daily_stats' => $daily_stats
        );
    }

    /**
     * Get personnel edit frequency
     */
    public function get_personnel_edit_frequency($personnel_email)
    {
        $sql = "SELECT 
                    COUNT(*) as total_edits,
                    COUNT(CASE WHEN action_type = 'CREATE' THEN 1 END) as creates,
                    COUNT(CASE WHEN action_type = 'UPDATE' THEN 1 END) as updates,
                    COUNT(CASE WHEN action_type = 'DELETE' THEN 1 END) as deletes,
                    MIN(created_at) as first_edit,
                    MAX(created_at) as last_edit
                FROM {$this->table} 
                WHERE personnel_email = ?";
        
        return $this->db->query($sql, array($personnel_email))->row();
    }
}
