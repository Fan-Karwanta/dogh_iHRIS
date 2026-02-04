<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DtrEdit extends CI_Controller
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
        $this->data['title'] = 'Edit My DTR';
        $personnel_id = $this->session->userdata('user_personnel_id');
        $personnel = $this->personnelModel->getpersonnel($personnel_id);
        $this->data['personnel'] = $personnel;

        $selected_month = $this->input->get('month') ? $this->input->get('month') : date('m');
        $selected_year = $this->input->get('year') ? $this->input->get('year') : date('Y');
        $this->data['selected_month'] = $selected_month;
        $this->data['selected_year'] = $selected_year;

        if ($personnel && $personnel->bio_id) {
            $start_date = $selected_year . '-' . str_pad($selected_month, 2, '0', STR_PAD_LEFT) . '-01';
            $end_date = date('Y-m-t', strtotime($start_date));
            $this->db->where('bio_id', $personnel->bio_id);
            $this->db->where('date >=', $start_date);
            $this->db->where('date <=', $end_date);
            $this->db->order_by('date', 'ASC');
            $this->data['dtr_records'] = $this->db->get('biometrics')->result();
        } else {
            $this->data['dtr_records'] = [];
        }

        $month_str = $selected_year . '-' . str_pad($selected_month, 2, '0', STR_PAD_LEFT);
        $this->data['pending_request'] = $this->dtrEditModel->get_pending_request($personnel_id, $month_str);
        $this->data['my_requests'] = $this->dtrEditModel->get_personnel_requests($personnel_id, 10);

        $this->data['content'] = $this->load->view('user_portal/dtr_edit_content', $this->data, TRUE);
        $this->load->view('user_portal/layout', $this->data);
    }

    public function submit_edit()
    {
        header('Content-Type: application/json');
        $personnel_id = $this->session->userdata('user_personnel_id');
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (empty($input['items'])) {
            echo json_encode(['success' => false, 'message' => 'No changes to submit']);
            return;
        }

        $month = $input['month'];
        $reason = isset($input['reason']) ? $input['reason'] : '';
        $items = [];

        foreach ($input['items'] as $item) {
            $items[] = [
                'date' => $item['date'],
                'field' => $item['field'],
                'original_value' => isset($item['original_value']) ? $item['original_value'] : null,
                'new_value' => $item['new_value'],
                'edit_type' => $item['edit_type'],
                'reason' => $reason
            ];
        }

        $request_id = $this->dtrEditModel->create_request($personnel_id, $month, $items);
        
        if ($request_id) {
            echo json_encode(['success' => true, 'message' => 'DTR edit request submitted for approval', 'request_id' => $request_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to submit request']);
        }
    }

    public function my_requests()
    {
        $this->data['title'] = 'My DTR Edit Requests';
        $personnel_id = $this->session->userdata('user_personnel_id');
        $this->data['requests'] = $this->dtrEditModel->get_personnel_requests($personnel_id);
        
        $this->data['content'] = $this->load->view('user_portal/dtr_requests_content', $this->data, TRUE);
        $this->load->view('user_portal/layout', $this->data);
    }

    public function view_request($request_id)
    {
        $this->data['title'] = 'View DTR Edit Request';
        $personnel_id = $this->session->userdata('user_personnel_id');
        $request = $this->dtrEditModel->get_request_with_items($request_id);
        
        if (!$request || $request->personnel_id != $personnel_id) {
            show_error('Request not found or access denied');
            return;
        }
        
        $this->data['request'] = $request;
        $this->data['content'] = $this->load->view('user_portal/dtr_request_view_content', $this->data, TRUE);
        $this->load->view('user_portal/layout', $this->data);
    }

    public function approvals()
    {
        $this->data['title'] = 'DTR Edit Approvals';
        $personnel_id = $this->session->userdata('user_personnel_id');
        
        $this->data['pending_requests'] = $this->dtrEditModel->get_pending_for_approver($personnel_id);
        $this->data['content'] = $this->load->view('user_portal/dtr_approvals_content', $this->data, TRUE);
        $this->load->view('user_portal/layout', $this->data);
    }

    public function approve($request_id)
    {
        header('Content-Type: application/json');
        $personnel_id = $this->session->userdata('user_personnel_id');
        $input = json_decode(file_get_contents('php://input'), true);
        $remarks = isset($input['remarks']) ? $input['remarks'] : null;

        $approvees = $this->hierarchyModel->get_approvees_for_personnel($personnel_id);
        $request = $this->dtrEditModel->get_request_with_items($request_id);
        
        if (!$request) {
            echo json_encode(['success' => false, 'message' => 'Request not found']);
            return;
        }

        $can_approve = false;
        foreach ($approvees as $a) {
            if ($a->personnel_id == $request->personnel_id) {
                $can_approve = true;
                break;
            }
        }

        if (!$can_approve) {
            echo json_encode(['success' => false, 'message' => 'You are not authorized to approve this request']);
            return;
        }

        if ($this->dtrEditModel->approve_request($request_id, $personnel_id, $remarks)) {
            echo json_encode(['success' => true, 'message' => 'Request approved successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to approve request']);
        }
    }

    public function reject($request_id)
    {
        header('Content-Type: application/json');
        $personnel_id = $this->session->userdata('user_personnel_id');
        $input = json_decode(file_get_contents('php://input'), true);
        $remarks = isset($input['remarks']) ? $input['remarks'] : null;

        if ($this->dtrEditModel->reject_request($request_id, $personnel_id, $remarks)) {
            echo json_encode(['success' => true, 'message' => 'Request rejected']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to reject request']);
        }
    }
}
