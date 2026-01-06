<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Leaves extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('LeaveModel', 'leaveModel');
        $this->load->model('PersonnelModel', 'personnelModel');
        
        // Check if user is admin
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        
        if (!$this->ion_auth->is_admin()) {
            $this->session->set_flashdata('error', 'Access denied. Admin privileges required.');
            redirect('admin/dashboard', 'refresh');
        }
    }

    /**
     * Dashboard - Overview of all leave applications
     */
    public function index()
    {
        $data['title'] = 'Leave Management';
        
        // Get statistics
        $data['stats'] = $this->leaveModel->get_statistics(array('year' => date('Y')));
        
        // Get recent pending applications
        $data['pending_leaves'] = $this->leaveModel->get_all(array(
            'status' => array('pending', 'certified', 'recommended')
        ));
        
        $this->base->load('default', 'leaves/dashboard', $data);
    }

    /**
     * List all leave applications with filters
     */
    public function all()
    {
        $data['title'] = 'All Leave Applications';
        
        $filters = array();
        
        if ($this->input->get('status')) {
            $filters['status'] = $this->input->get('status');
        }
        if ($this->input->get('department')) {
            $filters['office_department'] = $this->input->get('department');
        }
        if ($this->input->get('leave_type')) {
            $filters['leave_type'] = $this->input->get('leave_type');
        }
        if ($this->input->get('year')) {
            $filters['year'] = $this->input->get('year');
        }
        
        $data['leaves'] = $this->leaveModel->get_all($filters);
        $data['filters'] = $filters;
        
        $this->base->load('default', 'leaves/list', $data);
    }

    /**
     * Pending applications for processing
     */
    public function pending()
    {
        $data['title'] = 'Pending Leave Applications';
        $data['leaves'] = $this->leaveModel->get_all(array('status' => 'pending'));
        
        $this->base->load('default', 'leaves/pending', $data);
    }

    /**
     * View single leave application
     */
    public function view($id)
    {
        $leave = $this->leaveModel->get_with_details($id);
        
        if (!$leave) {
            $this->session->set_flashdata('error', 'Leave application not found.');
            redirect('leaves', 'refresh');
        }
        
        $data['title'] = 'View Leave Application';
        $data['leave'] = $leave;
        $data['logs'] = $this->leaveModel->get_logs($id);
        $data['leave_credits'] = $this->leaveModel->get_leave_credits($leave->personnel_id);
        
        $this->base->load('default', 'leaves/view', $data);
    }

    /**
     * Certify leave credits (HR action)
     */
    public function certify($id)
    {
        $leave = $this->leaveModel->get($id);
        
        if (!$leave) {
            $this->session->set_flashdata('error', 'Leave application not found.');
            redirect('leaves', 'refresh');
        }
        
        if ($leave->status != 'pending') {
            $this->session->set_flashdata('error', 'Only pending applications can be certified.');
            redirect('leaves/view/' . $id, 'refresh');
        }
        
        $user = $this->ion_auth->user()->row();
        
        // Get current leave credits
        $vl_credit = $this->leaveModel->get_leave_credit($leave->personnel_id, 'vacation');
        $sl_credit = $this->leaveModel->get_leave_credit($leave->personnel_id, 'sick');
        
        // Calculate less this application based on leave type
        $vl_less = 0;
        $sl_less = 0;
        
        if (in_array($leave->leave_type, ['vacation_leave', 'mandatory_forced_leave', 'special_privilege_leave'])) {
            $vl_less = $leave->working_days_applied;
        } elseif ($leave->leave_type == 'sick_leave') {
            $sl_less = $leave->working_days_applied;
        }
        
        $certification_data = array(
            'as_of' => date('Y-m-d'),
            'vl_earned' => $vl_credit ? $vl_credit->earned : 0,
            'vl_less' => $vl_less,
            'vl_balance' => $vl_credit ? ($vl_credit->balance - $vl_less) : 0,
            'sl_earned' => $sl_credit ? $sl_credit->earned : 0,
            'sl_less' => $sl_less,
            'sl_balance' => $sl_credit ? ($sl_credit->balance - $sl_less) : 0
        );
        
        if ($this->leaveModel->certify($id, $certification_data, $user->id)) {
            $this->session->set_flashdata('success', 'Leave credits certified successfully.');
        } else {
            $this->session->set_flashdata('error', 'Failed to certify leave credits.');
        }
        
        redirect('leaves/view/' . $id, 'refresh');
    }

    /**
     * Recommend leave application
     */
    public function recommend($id)
    {
        $leave = $this->leaveModel->get($id);
        
        if (!$leave) {
            $this->session->set_flashdata('error', 'Leave application not found.');
            redirect('leaves', 'refresh');
        }
        
        if ($leave->status != 'certified') {
            $this->session->set_flashdata('error', 'Only certified applications can be recommended.');
            redirect('leaves/view/' . $id, 'refresh');
        }
        
        $user = $this->ion_auth->user()->row();
        $recommendation = $this->input->post('recommendation');
        $reason = $this->input->post('disapproval_reason');
        
        if ($this->leaveModel->recommend($id, $recommendation, $reason, $user->id)) {
            $this->session->set_flashdata('success', 'Recommendation submitted successfully.');
        } else {
            $this->session->set_flashdata('error', 'Failed to submit recommendation.');
        }
        
        redirect('leaves/view/' . $id, 'refresh');
    }

    /**
     * Approve leave application
     */
    public function approve($id)
    {
        $leave = $this->leaveModel->get($id);
        
        if (!$leave) {
            $this->session->set_flashdata('error', 'Leave application not found.');
            redirect('leaves', 'refresh');
        }
        
        if ($leave->status != 'recommended') {
            $this->session->set_flashdata('error', 'Only recommended applications can be approved.');
            redirect('leaves/view/' . $id, 'refresh');
        }
        
        $user = $this->ion_auth->user()->row();
        
        $approval_data = array(
            'days_with_pay' => $this->input->post('days_with_pay'),
            'days_without_pay' => $this->input->post('days_without_pay'),
            'others' => $this->input->post('others')
        );
        
        if ($this->leaveModel->approve($id, $approval_data, $user->id)) {
            $this->session->set_flashdata('success', 'Leave application approved successfully.');
        } else {
            $this->session->set_flashdata('error', 'Failed to approve leave application.');
        }
        
        redirect('leaves/view/' . $id, 'refresh');
    }

    /**
     * Disapprove leave application
     */
    public function disapprove($id)
    {
        $leave = $this->leaveModel->get($id);
        
        if (!$leave) {
            $this->session->set_flashdata('error', 'Leave application not found.');
            redirect('leaves', 'refresh');
        }
        
        if (!in_array($leave->status, ['certified', 'recommended'])) {
            $this->session->set_flashdata('error', 'This application cannot be disapproved at this stage.');
            redirect('leaves/view/' . $id, 'refresh');
        }
        
        $user = $this->ion_auth->user()->row();
        $reason = $this->input->post('disapproval_reason');
        
        if (empty($reason)) {
            $this->session->set_flashdata('error', 'Please provide a reason for disapproval.');
            redirect('leaves/view/' . $id, 'refresh');
        }
        
        if ($this->leaveModel->disapprove($id, $reason, $user->id)) {
            $this->session->set_flashdata('success', 'Leave application disapproved.');
        } else {
            $this->session->set_flashdata('error', 'Failed to disapprove leave application.');
        }
        
        redirect('leaves/view/' . $id, 'refresh');
    }

    /**
     * Cancel leave application (admin)
     */
    public function cancel($id)
    {
        $leave = $this->leaveModel->get($id);
        
        if (!$leave) {
            $this->session->set_flashdata('error', 'Leave application not found.');
            redirect('leaves', 'refresh');
        }
        
        $user = $this->ion_auth->user()->row();
        $reason = $this->input->post('cancel_reason') ?? 'Cancelled by admin';
        
        if ($this->leaveModel->cancel($id, $user->id, $reason)) {
            $this->session->set_flashdata('success', 'Leave application cancelled.');
        } else {
            $this->session->set_flashdata('error', 'Failed to cancel leave application.');
        }
        
        redirect('leaves', 'refresh');
    }

    /**
     * Print leave application
     */
    public function print_form($id)
    {
        $leave = $this->leaveModel->get_with_details($id);
        
        if (!$leave) {
            $this->session->set_flashdata('error', 'Leave application not found.');
            redirect('leaves', 'refresh');
        }
        
        $data['title'] = 'Print Leave Application';
        $data['leave'] = $leave;
        
        $this->load->view('leave/print_form', $data);
    }

    /**
     * Leave credits management
     */
    public function credits()
    {
        $data['title'] = 'Leave Credits Management';
        
        // Get all personnel with their leave credits
        $this->db->select('personnels.*, departments.name as department_name');
        $this->db->from('personnels');
        $this->db->join('departments', 'departments.id = personnels.department_id', 'left');
        $this->db->where('personnels.status', 1);
        $this->db->order_by('personnels.lastname', 'ASC');
        $data['personnel'] = $this->db->get()->result();
        
        // Get credits for each personnel
        foreach ($data['personnel'] as &$person) {
            $person->credits = $this->leaveModel->get_leave_credits($person->id);
        }
        
        $this->base->load('default', 'leaves/credits', $data);
    }

    /**
     * Initialize leave credits for a personnel
     */
    public function init_credits($personnel_id)
    {
        $this->leaveModel->initialize_leave_credits($personnel_id);
        $this->session->set_flashdata('success', 'Leave credits initialized successfully.');
        redirect('leaves/credits', 'refresh');
    }

    /**
     * Update leave credits (AJAX)
     */
    public function update_credits()
    {
        if (!$this->input->is_ajax_request()) {
            show_error('Direct access not allowed', 403);
        }
        
        $personnel_id = $this->input->post('personnel_id');
        $leave_type = $this->input->post('leave_type');
        $earned = $this->input->post('earned');
        $used = $this->input->post('used');
        $year = $this->input->post('year') ?? date('Y');
        
        $credit = $this->leaveModel->get_leave_credit($personnel_id, $leave_type, $year);
        
        if ($credit) {
            $this->db->where('id', $credit->id);
            $result = $this->db->update('leave_credits', array(
                'earned' => $earned,
                'used' => $used,
                'balance' => $earned - $used
            ));
        } else {
            $result = $this->db->insert('leave_credits', array(
                'personnel_id' => $personnel_id,
                'leave_type' => $leave_type,
                'year' => $year,
                'earned' => $earned,
                'used' => $used,
                'balance' => $earned - $used
            ));
        }
        
        echo json_encode(array('success' => $result));
    }

    /**
     * Bulk initialize credits for all personnel
     */
    public function bulk_init_credits()
    {
        $this->db->select('id');
        $this->db->from('personnels');
        $this->db->where('status', 1);
        $personnel = $this->db->get()->result();
        
        $count = 0;
        foreach ($personnel as $person) {
            $this->leaveModel->initialize_leave_credits($person->id);
            $count++;
        }
        
        $this->session->set_flashdata('success', "Leave credits initialized for {$count} personnel.");
        redirect('leaves/credits', 'refresh');
    }

    /**
     * Add monthly accrual for VL and SL (+1.25 each)
     */
    public function add_monthly_accrual()
    {
        $updated = $this->leaveModel->add_monthly_accrual();
        $this->session->set_flashdata('success', "Monthly accrual added. {$updated} credit records updated (+1.25 VL/SL each).");
        redirect('leaves/credits', 'refresh');
    }

    /**
     * Reset yearly credits (SPL to 3 days, carry over VL/SL)
     */
    public function reset_yearly()
    {
        $this->leaveModel->reset_yearly_credits();
        $this->session->set_flashdata('success', "Yearly credits reset. VL/SL carried over, SPL reset to 3 days.");
        redirect('leaves/credits', 'refresh');
    }

    /**
     * Reports
     */
    public function reports()
    {
        $data['title'] = 'Leave Reports';
        
        $year = $this->input->get('year') ?? date('Y');
        $data['year'] = $year;
        
        // Get statistics by month
        $data['monthly_stats'] = array();
        for ($m = 1; $m <= 12; $m++) {
            $this->db->select('COUNT(*) as total, status');
            $this->db->from('leave_applications');
            $this->db->where('YEAR(date_of_filing)', $year);
            $this->db->where('MONTH(date_of_filing)', $m);
            $this->db->group_by('status');
            $data['monthly_stats'][$m] = $this->db->get()->result();
        }
        
        // Get statistics by leave type
        $this->db->select('leave_type, COUNT(*) as total, SUM(working_days_applied) as total_days');
        $this->db->from('leave_applications');
        $this->db->where('YEAR(date_of_filing)', $year);
        $this->db->where('status', 'approved');
        $this->db->group_by('leave_type');
        $data['type_stats'] = $this->db->get()->result();
        
        // Get statistics by department
        $this->db->select('office_department, COUNT(*) as total, SUM(working_days_applied) as total_days');
        $this->db->from('leave_applications');
        $this->db->where('YEAR(date_of_filing)', $year);
        $this->db->where('status', 'approved');
        $this->db->group_by('office_department');
        $data['dept_stats'] = $this->db->get()->result();
        
        $this->base->load('default', 'leaves/reports', $data);
    }

    /**
     * AJAX: Get leave applications for DataTables
     */
    public function get_datatables()
    {
        $filters = array();
        
        if ($this->input->post('status')) {
            $filters['status'] = $this->input->post('status');
        }
        
        $list = $this->leaveModel->get_datatables($filters);
        $data = array();
        
        foreach ($list as $leave) {
            $row = array();
            $row[] = $leave->application_number;
            $row[] = $leave->lastname . ', ' . $leave->firstname;
            $row[] = $leave->office_department;
            $row[] = $this->leaveModel->get_leave_type_label($leave->leave_type);
            $row[] = date('M d, Y', strtotime($leave->date_of_filing));
            $row[] = number_format($leave->working_days_applied, 1);
            $row[] = '<span class="badge ' . $this->leaveModel->get_status_badge($leave->status) . '">' . ucfirst($leave->status) . '</span>';
            $row[] = '<a href="' . site_url('leaves/view/' . $leave->id) . '" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>';
            
            $data[] = $row;
        }
        
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->leaveModel->count_all($filters),
            "recordsFiltered" => $this->leaveModel->count_filtered($filters),
            "data" => $data,
        );
        
        echo json_encode($output);
    }

    /**
     * Delete leave application
     */
    public function delete($id)
    {
        $leave = $this->leaveModel->get($id);
        
        if (!$leave) {
            $this->session->set_flashdata('error', 'Leave application not found.');
            redirect('leaves', 'refresh');
        }
        
        if ($this->leaveModel->delete($id)) {
            $this->session->set_flashdata('success', 'Leave application deleted successfully.');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete leave application.');
        }
        
        redirect('leaves/all', 'refresh');
    }
}
