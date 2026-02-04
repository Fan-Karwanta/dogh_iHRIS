<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DtrApproval extends CI_Controller
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
        $this->load->model('DtrEditRequestModel', 'dtrEditModel');
        $this->load->model('HierarchyApprovalModel', 'hierarchyModel');

        if (!$this->session->userdata('user_logged_in')) {
            redirect('userauth/login');
        }

        $this->data['current_user'] = $this->userAccountModel->get_user($this->session->userdata('user_account_id'));
        $this->data['notifications'] = $this->userAccountModel->get_notifications($this->session->userdata('user_account_id'), 5);
        $this->data['unread_count'] = $this->userAccountModel->get_unread_notification_count($this->session->userdata('user_account_id'));
    }

    public function index()
    {
        $this->data['title'] = 'DTR Edit Approvals';
        $personnel_id = $this->session->userdata('user_personnel_id');
        $this->data['pending_requests'] = $this->dtrEditModel->get_pending_requests_for_approver($personnel_id);
        $this->data['content'] = $this->load->view('user_portal/dtr_approval/index', $this->data, TRUE);
        $this->load->view('user_portal/layout', $this->data);
    }

    public function view($id)
    {
        $this->data['title'] = 'Review DTR Edit Request';
        $personnel_id = $this->session->userdata('user_personnel_id');
        $request = $this->dtrEditModel->get_request($id);

        if (!$request) {
            $this->session->set_flashdata('error', 'Request not found');
            redirect('dtrapproval');
        }

        $approvees = $this->hierarchyModel->get_approvees_for_personnel($personnel_id);
        $approvee_ids = array_map(function($a) { return $a->personnel_id; }, $approvees);

        if (!in_array($request->personnel_id, $approvee_ids)) {
            $this->session->set_flashdata('error', 'You are not authorized to approve this request');
            redirect('dtrapproval');
        }

        $this->data['request'] = $request;
        $this->data['items'] = $this->dtrEditModel->get_items_grouped_by_date($id);
        
        // Get personnel DTR data for full preview
        $personnel = $this->personnelModel->getpersonnel($request->personnel_id);
        $this->data['personnel'] = $personnel;
        
        // Get DTR data for the month
        $month = $request->request_month;
        $month_num = date('m', strtotime($month . '-01'));
        $year = date('Y', strtotime($month . '-01'));
        
        $this->db->where('bio_id', $personnel->bio_id);
        $this->db->where('MONTH(date)', $month_num);
        $this->db->where('YEAR(date)', $year);
        $this->db->order_by('date', 'ASC');
        $this->data['dtr_data'] = $this->db->get('biometrics')->result();
        $this->data['selected_month'] = $month;
        
        $this->data['content'] = $this->load->view('user_portal/dtr_approval/view', $this->data, TRUE);
        $this->load->view('user_portal/layout', $this->data);
    }

    public function approve($id)
    {
        $personnel_id = $this->session->userdata('user_personnel_id');
        $request = $this->dtrEditModel->get_request($id);
        $remarks = $this->input->post('remarks');

        if (!$request || $request->status !== 'pending') {
            $this->session->set_flashdata('error', 'Invalid request');
            redirect('dtrapproval');
        }

        $approvees = $this->hierarchyModel->get_approvees_for_personnel($personnel_id);
        $approvee_ids = array_map(function($a) { return $a->personnel_id; }, $approvees);

        if (!in_array($request->personnel_id, $approvee_ids)) {
            $this->session->set_flashdata('error', 'Unauthorized');
            redirect('dtrapproval');
        }

        $this->dtrEditModel->approve_request($id, $personnel_id, false, $remarks);
        $this->session->set_flashdata('success', 'Request approved successfully');
        redirect('dtrapproval');
    }

    public function reject($id)
    {
        $personnel_id = $this->session->userdata('user_personnel_id');
        $request = $this->dtrEditModel->get_request($id);
        $remarks = $this->input->post('remarks');

        if (!$request || $request->status !== 'pending') {
            $this->session->set_flashdata('error', 'Invalid request');
            redirect('dtrapproval');
        }

        $approvees = $this->hierarchyModel->get_approvees_for_personnel($personnel_id);
        $approvee_ids = array_map(function($a) { return $a->personnel_id; }, $approvees);

        if (!in_array($request->personnel_id, $approvee_ids)) {
            $this->session->set_flashdata('error', 'Unauthorized');
            redirect('dtrapproval');
        }

        $this->dtrEditModel->reject_request($id, $personnel_id, false, $remarks);
        $this->session->set_flashdata('success', 'Request rejected');
        redirect('dtrapproval');
    }
}
