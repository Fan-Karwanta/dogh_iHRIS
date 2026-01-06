<?php
defined('BASEPATH') or exit('No direct script access allowed');

class LeaveApplication extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('LeaveModel', 'leaveModel');
        $this->load->model('PersonnelModel', 'personnelModel');
    }

    /**
     * Main page - List user's leave applications
     */
    public function index()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        $user = $this->ion_auth->user()->row();
        $data['title'] = 'My Leave Applications';
        
        // Get personnel linked to this user
        $personnel = $this->get_user_personnel($user);
        $data['personnel'] = $personnel;
        
        if ($personnel) {
            $data['leaves'] = $this->leaveModel->get_all(array('personnel_id' => $personnel->id));
            $data['leave_credits'] = $this->leaveModel->get_leave_credits($personnel->id);
        } else {
            $data['leaves'] = array();
            $data['leave_credits'] = array();
        }

        $this->base->load('default', 'leave/my_leaves', $data);
    }

    /**
     * Create new leave application form
     */
    public function create()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        $user = $this->ion_auth->user()->row();
        $personnel = $this->get_user_personnel($user);
        
        if (!$personnel) {
            $this->session->set_flashdata('error', 'Your account is not linked to any personnel record. Please contact HR.');
            redirect('leave_application', 'refresh');
        }

        $data['title'] = 'Application for Leave';
        $data['personnel'] = $personnel;
        $data['leave_credits'] = $this->leaveModel->get_leave_credits($personnel->id);
        
        // Initialize leave credits if not exists
        if (empty($data['leave_credits'])) {
            $this->leaveModel->initialize_leave_credits($personnel->id);
            $data['leave_credits'] = $this->leaveModel->get_leave_credits($personnel->id);
        }

        $this->base->load('default', 'leave/application_form', $data);
    }

    /**
     * Save leave application
     */
    public function save()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        $user = $this->ion_auth->user()->row();
        $personnel = $this->get_user_personnel($user);
        
        if (!$personnel) {
            $this->session->set_flashdata('error', 'Your account is not linked to any personnel record.');
            redirect('leave_application', 'refresh');
        }

        // Validation rules
        $this->form_validation->set_rules('office_department', 'Office/Department', 'required');
        $this->form_validation->set_rules('salary_grade', 'Salary Grade', 'required|integer|greater_than[0]|less_than[34]');
        $this->form_validation->set_rules('leave_type', 'Type of Leave', 'required');
        $this->form_validation->set_rules('working_days_applied', 'Number of Working Days', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('inclusive_date_from', 'Inclusive Date From', 'required');
        $this->form_validation->set_rules('inclusive_date_to', 'Inclusive Date To', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('leave_application/create', 'refresh');
        }

        // Prepare data
        $data = array(
            'personnel_id' => $personnel->id,
            'office_department' => $this->input->post('office_department'),
            'date_of_filing' => date('Y-m-d'),
            'salary_grade' => $this->input->post('salary_grade'),
            'leave_type' => $this->input->post('leave_type'),
            'leave_type_others' => $this->input->post('leave_type_others'),
            
            // Details of Leave
            'vacation_special_within_ph' => $this->input->post('vacation_special_within_ph'),
            'vacation_special_abroad' => $this->input->post('vacation_special_abroad'),
            'sick_in_hospital' => $this->input->post('sick_in_hospital'),
            'sick_out_patient' => $this->input->post('sick_out_patient'),
            'special_women_illness' => $this->input->post('special_women_illness'),
            'study_completion_masters' => $this->input->post('study_completion_masters') ? 1 : 0,
            'study_bar_review' => $this->input->post('study_bar_review') ? 1 : 0,
            'other_purpose_monetization' => $this->input->post('other_purpose_monetization') ? 1 : 0,
            'other_purpose_terminal_leave' => $this->input->post('other_purpose_terminal_leave') ? 1 : 0,
            
            // Working days and dates
            'working_days_applied' => $this->input->post('working_days_applied'),
            'inclusive_date_from' => $this->input->post('inclusive_date_from'),
            'inclusive_date_to' => $this->input->post('inclusive_date_to'),
            
            // Commutation
            'commutation_requested' => $this->input->post('commutation_requested') ? 1 : 0,
            
            // Status
            'status' => $this->input->post('submit_type') == 'submit' ? 'pending' : 'draft',
            'applicant_signature_date' => $this->input->post('submit_type') == 'submit' ? date('Y-m-d H:i:s') : null
        );

        $leave_id = $this->input->post('leave_id');
        
        if ($leave_id) {
            // Update existing
            $result = $this->leaveModel->update($leave_id, $data, $user->id, 'Leave application updated');
            $message = 'Leave application updated successfully!';
        } else {
            // Create new
            $result = $this->leaveModel->create($data);
            $message = $this->input->post('submit_type') == 'submit' 
                ? 'Leave application submitted successfully!' 
                : 'Leave application saved as draft.';
        }

        if ($result) {
            $this->session->set_flashdata('success', $message);
        } else {
            $this->session->set_flashdata('error', 'Failed to save leave application. Please try again.');
        }

        redirect('leave_application', 'refresh');
    }

    /**
     * View leave application details
     */
    public function view($id)
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        $user = $this->ion_auth->user()->row();
        $personnel = $this->get_user_personnel($user);
        
        $leave = $this->leaveModel->get_with_details($id);
        
        if (!$leave || ($leave->personnel_id != $personnel->id && !$this->ion_auth->is_admin())) {
            $this->session->set_flashdata('error', 'Leave application not found or access denied.');
            redirect('leave_application', 'refresh');
        }

        $data['title'] = 'View Leave Application';
        $data['leave'] = $leave;
        $data['personnel'] = $personnel;
        $data['logs'] = $this->leaveModel->get_logs($id);

        $this->base->load('default', 'leave/view_application', $data);
    }

    /**
     * Edit leave application (only for draft status)
     */
    public function edit($id)
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        $user = $this->ion_auth->user()->row();
        $personnel = $this->get_user_personnel($user);
        
        $leave = $this->leaveModel->get($id);
        
        if (!$leave || $leave->personnel_id != $personnel->id) {
            $this->session->set_flashdata('error', 'Leave application not found or access denied.');
            redirect('leave_application', 'refresh');
        }

        if ($leave->status != 'draft') {
            $this->session->set_flashdata('error', 'Only draft applications can be edited.');
            redirect('leave_application', 'refresh');
        }

        $data['title'] = 'Edit Leave Application';
        $data['leave'] = $leave;
        $data['personnel'] = $personnel;
        $data['leave_credits'] = $this->leaveModel->get_leave_credits($personnel->id);

        $this->base->load('default', 'leave/application_form', $data);
    }

    /**
     * Submit draft leave application
     */
    public function submit($id)
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        $user = $this->ion_auth->user()->row();
        $personnel = $this->get_user_personnel($user);
        
        $leave = $this->leaveModel->get($id);
        
        if (!$leave || $leave->personnel_id != $personnel->id) {
            $this->session->set_flashdata('error', 'Leave application not found or access denied.');
            redirect('leave_application', 'refresh');
        }

        if ($leave->status != 'draft') {
            $this->session->set_flashdata('error', 'Only draft applications can be submitted.');
            redirect('leave_application', 'refresh');
        }

        if ($this->leaveModel->submit($id, $user->id)) {
            $this->session->set_flashdata('success', 'Leave application submitted successfully!');
        } else {
            $this->session->set_flashdata('error', 'Failed to submit leave application.');
        }

        redirect('leave_application', 'refresh');
    }

    /**
     * Cancel leave application
     */
    public function cancel($id)
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        $user = $this->ion_auth->user()->row();
        $personnel = $this->get_user_personnel($user);
        
        $leave = $this->leaveModel->get($id);
        
        if (!$leave || $leave->personnel_id != $personnel->id) {
            $this->session->set_flashdata('error', 'Leave application not found or access denied.');
            redirect('leave_application', 'refresh');
        }

        if (!in_array($leave->status, array('draft', 'pending'))) {
            $this->session->set_flashdata('error', 'Only draft or pending applications can be cancelled.');
            redirect('leave_application', 'refresh');
        }

        if ($this->leaveModel->cancel($id, $user->id)) {
            $this->session->set_flashdata('success', 'Leave application cancelled successfully!');
        } else {
            $this->session->set_flashdata('error', 'Failed to cancel leave application.');
        }

        redirect('leave_application', 'refresh');
    }

    /**
     * Print leave application (CS Form No. 6)
     */
    public function print_form($id)
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        $user = $this->ion_auth->user()->row();
        $personnel = $this->get_user_personnel($user);
        
        $leave = $this->leaveModel->get_with_details($id);
        
        if (!$leave || ($leave->personnel_id != $personnel->id && !$this->ion_auth->is_admin())) {
            $this->session->set_flashdata('error', 'Leave application not found or access denied.');
            redirect('leave_application', 'refresh');
        }

        $data['title'] = 'Print Leave Application';
        $data['leave'] = $leave;
        $data['personnel'] = $personnel;

        $this->load->view('leave/print_form', $data);
    }

    /**
     * AJAX: Get personnel data for autocomplete
     */
    public function get_personnel()
    {
        if (!$this->ion_auth->logged_in()) {
            echo json_encode(array('success' => false, 'message' => 'Unauthorized'));
            return;
        }

        $search = $this->input->get('search');
        
        $this->db->select('id, lastname, firstname, middlename, position, salary_grade, email');
        $this->db->from('personnels');
        $this->db->where('status', 1);
        
        if ($search) {
            $this->db->group_start();
            $this->db->like('lastname', $search);
            $this->db->or_like('firstname', $search);
            $this->db->or_like('email', $search);
            $this->db->group_end();
        }
        
        $this->db->order_by('lastname', 'ASC');
        $this->db->limit(20);
        
        $personnel = $this->db->get()->result();
        
        echo json_encode(array('success' => true, 'data' => $personnel));
    }

    /**
     * AJAX: Get leave credits for a personnel
     */
    public function get_credits($personnel_id)
    {
        if (!$this->ion_auth->logged_in()) {
            echo json_encode(array('success' => false, 'message' => 'Unauthorized'));
            return;
        }

        $credits = $this->leaveModel->get_leave_credits($personnel_id);
        
        echo json_encode(array('success' => true, 'data' => $credits));
    }

    /**
     * Helper: Get personnel linked to user
     */
    private function get_user_personnel($user)
    {
        // First try to find by email
        $this->db->where('email', $user->email);
        $personnel = $this->db->get('personnels')->row();
        
        if ($personnel) {
            return $personnel;
        }
        
        // Try to find by user_id if there's a link
        $this->db->where('user_id', $user->id);
        $personnel = $this->db->get('personnels')->row();
        
        return $personnel;
    }
}
