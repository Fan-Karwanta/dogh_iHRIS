<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AdminClockChangeApproval extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'session']);
        $this->load->helper(['url']);
        $this->load->model('ClockChangeRequestModel', 'clockChangeModel');
        $this->load->model('UserAccountModel', 'userAccountModel');
        $this->load->model('PersonnelModel', 'personnelModel');

        $this->clockChangeModel->ensure_tables_exist();

        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
    }

    public function index()
    {
        $data['title'] = 'Clock Change Requests - Admin';
        $data['pending_requests'] = $this->clockChangeModel->get_all_pending_requests();
        $this->base->load('default', 'admin/clock_change_approval/index', $data);
    }

    public function view($id)
    {
        $data['title'] = 'Review Clock Change Request';
        $request = $this->clockChangeModel->get_request($id);
        $data['request'] = $request;
        $data['items'] = $this->clockChangeModel->get_request_items($id);

        $personnel = $this->personnelModel->getpersonnel($request->personnel_id);
        $data['personnel'] = $personnel;

        // Get supervisor
        $supervisor = $this->clockChangeModel->get_immediate_supervisor($request->personnel_id);
        $data['supervisor'] = $supervisor;

        $this->base->load('default', 'admin/clock_change_approval/view', $data);
    }

    public function approve($id)
    {
        $admin_id = $this->ion_auth->user()->row()->id;
        $remarks = $this->input->post('remarks');
        $request = $this->clockChangeModel->get_request($id);
        $this->clockChangeModel->approve_request($id, $admin_id, true, $remarks);

        if ($request) {
            $this->db->where('personnel_id', $request->personnel_id);
            $requester_account = $this->db->get('user_accounts')->row();
            if ($requester_account) {
                $msg = 'Your Failure to Clock / Time Changes request (Control No: ' . $request->control_no . ') has been approved by the Administrator.';
                if ($remarks) $msg .= ' Remarks: ' . $remarks;
                $this->userAccountModel->add_notification($requester_account->id, 'Clock Change Request Approved', $msg, 'success');
            }
        }

        $this->session->set_flashdata('success', 'Request approved');
        redirect('adminclockchangeapproval');
    }

    public function reject($id)
    {
        $admin_id = $this->ion_auth->user()->row()->id;
        $remarks = $this->input->post('remarks');
        $request = $this->clockChangeModel->get_request($id);
        $this->clockChangeModel->reject_request($id, $admin_id, true, $remarks);

        if ($request) {
            $this->db->where('personnel_id', $request->personnel_id);
            $requester_account = $this->db->get('user_accounts')->row();
            if ($requester_account) {
                $msg = 'Your Failure to Clock / Time Changes request (Control No: ' . $request->control_no . ') has been rejected by the Administrator.';
                if ($remarks) $msg .= ' Reason: ' . $remarks;
                $this->userAccountModel->add_notification($requester_account->id, 'Clock Change Request Rejected', $msg, 'danger');
            }
        }

        $this->session->set_flashdata('success', 'Request rejected');
        redirect('adminclockchangeapproval');
    }
}
