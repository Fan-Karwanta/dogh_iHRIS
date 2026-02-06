<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ClockChangeApproval extends CI_Controller
{
    public $data = [];

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['session']);
        $this->load->helper(['url']);
        $this->load->model('UserAccountModel', 'userAccountModel');
        $this->load->model('PersonnelModel', 'personnelModel');
        $this->load->model('ClockChangeRequestModel', 'clockChangeModel');
        $this->load->model('HierarchyApprovalModel', 'hierarchyModel');

        $this->clockChangeModel->ensure_tables_exist();

        if (!$this->session->userdata('user_logged_in')) {
            redirect('userauth/login');
        }

        $this->data['current_user'] = $this->userAccountModel->get_user($this->session->userdata('user_account_id'));
        $this->data['notifications'] = $this->userAccountModel->get_notifications($this->session->userdata('user_account_id'), 5);
        $this->data['unread_count'] = $this->userAccountModel->get_unread_notification_count($this->session->userdata('user_account_id'));
    }

    /**
     * List pending clock change requests for this approver
     */
    public function index()
    {
        $this->data['title'] = 'Clock Change Approvals';
        $personnel_id = $this->session->userdata('user_personnel_id');
        $this->data['pending_requests'] = $this->clockChangeModel->get_pending_requests_for_approver($personnel_id);
        $this->data['content'] = $this->load->view('user_portal/forgot_clockin/approval_index', $this->data, TRUE);
        $this->load->view('user_portal/layout', $this->data);
    }

    /**
     * View/review a specific request
     */
    public function view($id)
    {
        $this->data['title'] = 'Review Clock Change Request';
        $personnel_id = $this->session->userdata('user_personnel_id');
        $request = $this->clockChangeModel->get_request($id);

        if (!$request) {
            $this->session->set_flashdata('error', 'Request not found');
            redirect('clockchangeapproval');
        }

        // Verify this approver has authority
        $approvees = $this->hierarchyModel->get_approvees_for_personnel($personnel_id);
        $approvee_ids = array_map(function ($a) {
            return $a->personnel_id;
        }, $approvees);

        if (!in_array($request->personnel_id, $approvee_ids)) {
            $this->session->set_flashdata('error', 'You are not authorized to approve this request');
            redirect('clockchangeapproval');
        }

        $this->data['request'] = $request;
        $this->data['items'] = $this->clockChangeModel->get_request_items($id);
        $this->data['personnel'] = $this->personnelModel->getpersonnel($request->personnel_id);

        // Get supervisor info (the current approver)
        $this->data['supervisor'] = $this->personnelModel->getpersonnel($personnel_id);

        $this->data['content'] = $this->load->view('user_portal/forgot_clockin/approval_view', $this->data, TRUE);
        $this->load->view('user_portal/layout', $this->data);
    }

    /**
     * Approve a request
     */
    public function approve($id)
    {
        $personnel_id = $this->session->userdata('user_personnel_id');
        $request = $this->clockChangeModel->get_request($id);
        $remarks = $this->input->post('remarks');

        if (!$request || $request->status !== 'pending') {
            $this->session->set_flashdata('error', 'Invalid request');
            redirect('clockchangeapproval');
        }

        $approvees = $this->hierarchyModel->get_approvees_for_personnel($personnel_id);
        $approvee_ids = array_map(function ($a) {
            return $a->personnel_id;
        }, $approvees);

        if (!in_array($request->personnel_id, $approvee_ids)) {
            $this->session->set_flashdata('error', 'Unauthorized');
            redirect('clockchangeapproval');
        }

        $this->clockChangeModel->approve_request($id, $personnel_id, false, $remarks);

        // Notify the employee
        $this->db->where('personnel_id', $request->personnel_id);
        $requester_account = $this->db->get('user_accounts')->row();
        if ($requester_account) {
            $approver = $this->personnelModel->getpersonnel($personnel_id);
            $approver_name = $approver ? $approver->lastname . ', ' . $approver->firstname : 'Your supervisor';
            $msg = 'Your Failure to Clock / Time Changes request (Control No: ' . $request->control_no . ') has been approved by ' . $approver_name . '.';
            if ($remarks) $msg .= ' Remarks: ' . $remarks;
            $this->userAccountModel->add_notification($requester_account->id, 'Clock Change Request Approved', $msg, 'success');
        }

        $this->session->set_flashdata('success', 'Request approved successfully');
        redirect('clockchangeapproval');
    }

    /**
     * Reject a request
     */
    public function reject($id)
    {
        $personnel_id = $this->session->userdata('user_personnel_id');
        $request = $this->clockChangeModel->get_request($id);
        $remarks = $this->input->post('remarks');

        if (!$request || $request->status !== 'pending') {
            $this->session->set_flashdata('error', 'Invalid request');
            redirect('clockchangeapproval');
        }

        $approvees = $this->hierarchyModel->get_approvees_for_personnel($personnel_id);
        $approvee_ids = array_map(function ($a) {
            return $a->personnel_id;
        }, $approvees);

        if (!in_array($request->personnel_id, $approvee_ids)) {
            $this->session->set_flashdata('error', 'Unauthorized');
            redirect('clockchangeapproval');
        }

        $this->clockChangeModel->reject_request($id, $personnel_id, false, $remarks);

        // Notify the employee
        $this->db->where('personnel_id', $request->personnel_id);
        $requester_account = $this->db->get('user_accounts')->row();
        if ($requester_account) {
            $approver = $this->personnelModel->getpersonnel($personnel_id);
            $approver_name = $approver ? $approver->lastname . ', ' . $approver->firstname : 'Your supervisor';
            $msg = 'Your Failure to Clock / Time Changes request (Control No: ' . $request->control_no . ') has been rejected by ' . $approver_name . '.';
            if ($remarks) $msg .= ' Reason: ' . $remarks;
            $this->userAccountModel->add_notification($requester_account->id, 'Clock Change Request Rejected', $msg, 'danger');
        }

        $this->session->set_flashdata('success', 'Request rejected');
        redirect('clockchangeapproval');
    }

    /**
     * Print view for approver
     */
    public function print_request($id)
    {
        $personnel_id = $this->session->userdata('user_personnel_id');
        $request = $this->clockChangeModel->get_request($id);

        if (!$request) {
            redirect('clockchangeapproval');
        }

        $data['request'] = $request;
        $data['items'] = $this->clockChangeModel->get_request_items($id);
        $data['personnel'] = $this->personnelModel->getpersonnel($request->personnel_id);
        $data['supervisor'] = $this->personnelModel->getpersonnel($personnel_id);

        if ($request->approver_id) {
            $data['approver'] = $this->personnelModel->getpersonnel($request->approver_id);
        }

        $this->load->view('user_portal/forgot_clockin/print', $data);
    }
}
