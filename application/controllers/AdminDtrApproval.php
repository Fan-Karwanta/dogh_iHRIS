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
        
        $this->base->load('default', 'admin/dtr_approval/view', $data);
    }

    public function approve($id)
    {
        $admin_id = $this->ion_auth->user()->row()->id;
        $remarks = $this->input->post('remarks');
        $this->dtrEditModel->approve_request($id, $admin_id, true, $remarks);
        $this->session->set_flashdata('success', 'Request approved');
        redirect('admindtrapproval');
    }

    public function reject($id)
    {
        $admin_id = $this->ion_auth->user()->row()->id;
        $remarks = $this->input->post('remarks');
        $this->dtrEditModel->reject_request($id, $admin_id, true, $remarks);
        $this->session->set_flashdata('success', 'Request rejected');
        redirect('admindtrapproval');
    }
}
