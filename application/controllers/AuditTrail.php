<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AuditTrail extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('AuditTrailModel', 'auditModel');
        $this->load->model('PersonnelModel', 'personnelModel');
        $this->load->library('base');
    }

    /**
     * Main audit trail page - General history
     */
    public function index()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        $data['title'] = 'Audit Trail - Edit History';
        $data['personnel_list'] = $this->personnelModel->personnels();
        
        // Get audit statistics for dashboard
        $data['statistics'] = $this->auditModel->get_audit_statistics();
        $data['recent_activities'] = $this->auditModel->get_recent_activities(20);

        $this->load->view('templates/default', array('content' => $this->load->view('audit_trail/index', $data, true)));
    }

    /**
     * Personnel-specific audit history
     */
    public function personnel($personnel_id = null)
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        if (!$personnel_id) {
            redirect('audit_trail', 'refresh');
        }

        // Get personnel information
        $personnel = $this->personnelModel->getpersonnel($personnel_id);
        if (!$personnel) {
            show_404();
        }

        $data['title'] = 'Audit Trail - ' . $personnel->lastname . ', ' . $personnel->firstname;
        $data['personnel'] = $personnel;
        
        // Get personnel audit statistics
        $data['edit_frequency'] = $this->auditModel->get_personnel_edit_frequency($personnel->email);
        $data['audit_history'] = $this->auditModel->get_personnel_audit_history($personnel->email, 50);

        $this->load->view('templates/default', array('content' => $this->load->view('audit_trail/personnel', $data, true)));
    }

    /**
     * Personnel-specific audit history by email (backward compatibility)
     */
    public function personnel_by_email($email = null)
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        if (!$email) {
            show_404();
        }

        // Decode the email from URL
        $email = urldecode($email);
        
        // Get personnel info by email
        $this->db->where('email', $email);
        $personnel = $this->db->get('personnels')->row();
        
        if (!$personnel) {
            show_404();
        }

        $data['title'] = 'Audit Trail - ' . $personnel->lastname . ', ' . $personnel->firstname;
        $data['personnel'] = $personnel;
        
        // Get personnel audit statistics
        $data['edit_frequency'] = $this->auditModel->get_personnel_edit_frequency($personnel->email);
        $data['audit_history'] = $this->auditModel->get_personnel_audit_history($personnel->email, 50);

        $this->load->view('templates/default', array('content' => $this->load->view('audit_trail/personnel', $data, true)));
    }

    /**
     * Personnel-specific audit history by bio_id
     */
    public function personnel_by_bio_id($bio_id = null)
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        if (!$bio_id) {
            show_404();
        }
        
        // Get personnel info by bio_id
        $this->db->where('bio_id', $bio_id);
        $personnel = $this->db->get('personnels')->row();
        
        if (!$personnel) {
            show_404();
        }

        $data['title'] = 'Audit Trail - ' . $personnel->lastname . ', ' . $personnel->firstname;
        $data['personnel'] = $personnel;
        
        // Get personnel audit statistics
        $data['edit_frequency'] = $this->auditModel->get_personnel_edit_frequency($personnel->email);
        $data['audit_history'] = $this->auditModel->get_personnel_audit_history($personnel->email, 50);

        $this->load->view('templates/default', array('content' => $this->load->view('audit_trail/personnel', $data, true)));
    }

    /**
     * Get audit data for DataTables AJAX
     */
    public function get_audit_data()
    {
        // Set JSON header
        header('Content-Type: application/json');
        
        try {
            $personnel_email = $this->input->post('personnel_email');
            $date_from = $this->input->post('date_from');
            $date_to = $this->input->post('date_to');

            // Get audit records directly from database
            $this->db->select('*');
            $this->db->from('audit_trail');
            
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
            
            $this->db->order_by('created_at', 'DESC');
            $query = $this->db->get();
            $list = $query->result();
            
            $data = array();
            $no = 0;

        foreach ($list as $audit) {
            $no++;
            $row = array();
            
            // Format date
            $row[] = date('M j, Y g:i A', strtotime($audit->created_at));
            
            // Personnel name with link to personnel audit
            if ($audit->personnel_email && $audit->personnel_name) {
                $personnel_link = '<a href="' . site_url('audit_trail/personnel_by_email/' . urlencode($audit->personnel_email)) . '" class="text-primary">' . 
                    htmlspecialchars($audit->personnel_name) . '</a>';
            } else {
                $personnel_link = 'N/A';
            }
            $row[] = $personnel_link;
            
            // Table name with icon
            $table_icon = $audit->table_name == 'attendance' ? '<i class="fas fa-calendar-check text-info"></i>' : '<i class="fas fa-table text-secondary"></i>';
            $row[] = $table_icon . ' ' . ucfirst($audit->table_name);
            
            // Action type with badge
            $action_class = '';
            switch($audit->action_type) {
                case 'CREATE': $action_class = 'badge-success'; break;
                case 'UPDATE': $action_class = 'badge-warning'; break;
                case 'DELETE': $action_class = 'badge-danger'; break;
                default: $action_class = 'badge-secondary';
            }
            $action_badge = '<span class="badge ' . $action_class . '">' . $audit->action_type . '</span>';
            $row[] = $action_badge;
            
            // Field name
            $row[] = $audit->field_name ? ucfirst(str_replace('_', ' ', $audit->field_name)) : 'All Fields';
            
            // Changes (old -> new)
            if ($audit->action_type == 'UPDATE') {
                $changes = '<strong>From:</strong> ' . ($audit->old_value ?: '<em>empty</em>') . 
                          '<br><strong>To:</strong> ' . ($audit->new_value ?: '<em>empty</em>');
            } elseif ($audit->action_type == 'CREATE') {
                $changes = '<strong>New:</strong> ' . ($audit->new_value ?: '<em>empty</em>');
            } elseif ($audit->action_type == 'DELETE') {
                $changes = '<strong>Deleted:</strong> ' . ($audit->old_value ?: '<em>empty</em>');
            } else {
                $changes = 'N/A';
            }
            $row[] = $changes;
            
            // Admin name
            $row[] = htmlspecialchars($audit->admin_name);
            
            // Reason
            $reason = $audit->reason ? htmlspecialchars($audit->reason) : '<span class="text-muted">No reason provided</span>';
            $row[] = $reason;
            
            // Actions
            $actions = '<button type="button" class="btn btn-sm btn-info" onclick="viewAuditDetails(' . $audit->id . ')" title="View Details">
                        <i class="fas fa-eye"></i>
                      </button>';
            $row[] = $actions;

            $data[] = $row;
        }

            $output = array(
                "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
                "recordsTotal" => count($list),
                "recordsFiltered" => count($list),
                "data" => $data,
            );
            
            echo json_encode($output);
            
        } catch (Exception $e) {
            // Return error response for DataTables
            $output = array(
                "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => array(),
                "error" => $e->getMessage()
            );
            
            echo json_encode($output);
        }
    }

    /**
     * Get personnel-specific audit data for DataTables AJAX
     */
    public function get_personnel_audit_data()
    {
        $personnel_email = $this->input->post('personnel_email');
        
        if (!$personnel_email) {
            echo json_encode(array("data" => array()));
            return;
        }

        // Get audit records directly from database
        $this->db->select('*');
        $this->db->from('audit_trail');
        $this->db->where('personnel_email', $personnel_email);
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get();
        $list = $query->result();
        
        $data = array();

        foreach ($list as $audit) {
            $row = array();
            
            // Date
            $row[] = date('M j, Y g:i A', strtotime($audit->created_at));
            
            // Action with badge
            $row[] = $this->get_action_badge($audit->action_type);
            
            // Field changed
            $row[] = $audit->field_name ? ucfirst(str_replace('_', ' ', $audit->field_name)) : 'Record';
            
            // Changes
            $row[] = $this->format_changes($audit->old_value, $audit->new_value, $audit->action_type);
            
            // Admin
            $row[] = htmlspecialchars($audit->admin_name);
            
            // Reason
            $row[] = $audit->reason ? htmlspecialchars($audit->reason) : '<span class="text-muted">No reason</span>';
            
            // IP Address
            $row[] = htmlspecialchars($audit->ip_address);

            $data[] = $row;
        }

        echo json_encode(array("data" => $data));
    }


    /**
     * Get audit details via AJAX
     */
    public function get_audit_details()
    {
        $audit_id = $this->input->post('audit_id');
        
        $this->db->where('id', $audit_id);
        $audit = $this->db->get('audit_trail')->row();
        
        if ($audit) {
            echo json_encode(array('success' => true, 'data' => $audit));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Audit record not found'));
        }
    }

    /**
     * Export audit trail to CSV
     */
    public function export_csv()
    {
        if (!$this->ion_auth->is_admin()) {
            show_error('Access denied', 403);
        }

        $personnel_email = $this->input->get('personnel_email');
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');

        // Get all audit records based on filters
        $this->db->select('*');
        $this->db->from('audit_trail');
        
        if ($personnel_email) {
            $this->db->where('personnel_email', $personnel_email);
        }
        if ($date_from) {
            $this->db->where('DATE(created_at) >=', $date_from);
        }
        if ($date_to) {
            $this->db->where('DATE(created_at) <=', $date_to);
        }
        
        $this->db->order_by('created_at', 'DESC');
        $audit_records = $this->db->get()->result();

        // Set CSV headers
        $filename = 'audit_trail_' . date('Y-m-d_H-i-s') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        
        // CSV column headers
        fputcsv($output, array(
            'Date/Time', 'Personnel Name', 'Personnel Email', 'Table', 'Action', 
            'Field', 'Old Value', 'New Value', 'Admin', 'Reason', 'IP Address'
        ));

        // CSV data rows
        foreach ($audit_records as $record) {
            fputcsv($output, array(
                $record->created_at,
                $record->personnel_name,
                $record->personnel_email,
                $record->table_name,
                $record->action_type,
                $record->field_name,
                $record->old_value,
                $record->new_value,
                $record->admin_name,
                $record->reason,
                $record->ip_address
            ));
        }

        fclose($output);
    }

    /**
     * Helper function to get action badge HTML
     */
    private function get_action_badge($action_type)
    {
        switch ($action_type) {
            case 'CREATE':
                return '<span class="badge badge-success">Created</span>';
            case 'UPDATE':
                return '<span class="badge badge-warning">Updated</span>';
            case 'DELETE':
                return '<span class="badge badge-danger">Deleted</span>';
            default:
                return '<span class="badge badge-secondary">' . $action_type . '</span>';
        }
    }

    /**
     * Helper function to format changes display
     */
    private function format_changes($old_value, $new_value, $action_type)
    {
        if ($action_type == 'CREATE') {
            return '<span class="text-success">Record Created</span>';
        } elseif ($action_type == 'DELETE') {
            return '<span class="text-danger">Record Deleted</span>';
        } elseif ($old_value !== null && $new_value !== null) {
            $old_display = $old_value ? htmlspecialchars($old_value) : '<em>empty</em>';
            $new_display = $new_value ? htmlspecialchars($new_value) : '<em>empty</em>';
            return '<span class="text-muted">' . $old_display . '</span> → <span class="text-primary">' . $new_display . '</span>';
        } else {
            return '<span class="text-muted">No changes recorded</span>';
        }
    }
}
