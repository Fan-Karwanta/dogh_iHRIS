<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AdminDtrApproval extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'session']);
        $this->load->helper(['url']);
        $this->load->model('DtrEditRequestModel', 'dtrEditModel');
        $this->load->model('UserAccountModel', 'userAccountModel');
        $this->load->model('PersonnelModel', 'personnelModel');
        $this->load->model('ClockChangeRequestModel', 'clockChangeModel');

        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
    }

    public function index()
    {
        $data['title'] = 'DTR Edit Requests - Admin';
        $data['pending_requests'] = $this->dtrEditModel->get_all_pending_requests();
        $this->base->load('default', 'admin/dtr_approval/index', $data);
    }

    public function view($id)
    {
        $this->load->model('PersonnelModel', 'personnelModel');
        
        $data['title'] = 'Review DTR Edit Request';
        $request = $this->dtrEditModel->get_request($id);
        $data['request'] = $request;
        $data['items'] = $this->dtrEditModel->get_items_grouped_by_date($id);
        
        // Get personnel DTR data for full preview
        $personnel = $this->personnelModel->getpersonnel($request->personnel_id);
        $data['personnel'] = $personnel;
        
        // Get DTR data for the month
        $month = $request->request_month;
        $month_num = date('m', strtotime($month . '-01'));
        $year = date('Y', strtotime($month . '-01'));
        
        $this->db->where('bio_id', $personnel->bio_id);
        $this->db->where('MONTH(date)', $month_num);
        $this->db->where('YEAR(date)', $year);
        $this->db->order_by('date', 'ASC');
        $data['dtr_data'] = $this->db->get('biometrics')->result();
        $data['selected_month'] = $month;

        // Get clock change embedded entries for blue highlighting
        $data['clock_change_entries'] = $this->clockChangeModel->get_embedded_entries($personnel->bio_id, $month);
        
        $this->base->load('default', 'admin/dtr_approval/view', $data);
    }

    public function approve($id)
    {
        $admin_id = $this->ion_auth->user()->row()->id;
        $remarks = $this->input->post('remarks');
        $request = $this->dtrEditModel->get_request($id);
        $this->dtrEditModel->approve_request($id, $admin_id, true, $remarks);

        // Notify the employee
        if ($request) {
            $this->db->where('personnel_id', $request->personnel_id);
            $requester_account = $this->db->get('user_accounts')->row();
            if ($requester_account) {
                $month_label = date('F Y', strtotime($request->request_month . '-01'));
                $msg = 'Your DTR edit request for ' . $month_label . ' has been approved by the Administrator.';
                if ($remarks) $msg .= ' Remarks: ' . $remarks;
                $this->userAccountModel->add_notification($requester_account->id, 'DTR Edit Request Approved', $msg, 'success');
            }
        }

        $this->session->set_flashdata('success', 'Request approved');
        redirect('admindtrapproval');
    }

    public function reject($id)
    {
        $admin_id = $this->ion_auth->user()->row()->id;
        $remarks = $this->input->post('remarks');
        $request = $this->dtrEditModel->get_request($id);
        $this->dtrEditModel->reject_request($id, $admin_id, true, $remarks);

        // Notify the employee
        if ($request) {
            $this->db->where('personnel_id', $request->personnel_id);
            $requester_account = $this->db->get('user_accounts')->row();
            if ($requester_account) {
                $month_label = date('F Y', strtotime($request->request_month . '-01'));
                $msg = 'Your DTR edit request for ' . $month_label . ' has been rejected by the Administrator.';
                if ($remarks) $msg .= ' Reason: ' . $remarks;
                $this->userAccountModel->add_notification($requester_account->id, 'DTR Edit Request Rejected', $msg, 'danger');
            }
        }

        $this->session->set_flashdata('success', 'Request rejected');
        redirect('admindtrapproval');
    }
}
