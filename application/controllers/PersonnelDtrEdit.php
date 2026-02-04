<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PersonnelDtrEdit extends CI_Controller
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
        $this->load->model('BiometricsModel', 'biometricsModel');
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

        $selected_month = $this->input->get('month') ? $this->input->get('month') : date('Y-m', strtotime('first day of last month'));
        $this->data['selected_month'] = $selected_month;

        $month = date('m', strtotime($selected_month . '-01'));
        $year = date('Y', strtotime($selected_month . '-01'));

        $this->data['dtr_data'] = [];
        $this->data['has_pending_request'] = false;

        if ($personnel && $personnel->bio_id) {
            $this->db->select('*');
            $this->db->from('biometrics');
            $this->db->where('bio_id', $personnel->bio_id);
            $this->db->where('MONTH(date)', $month);
            $this->db->where('YEAR(date)', $year);
            $this->db->order_by('date', 'ASC');
            $this->data['dtr_data'] = $this->db->get()->result();

            $this->data['has_pending_request'] = $this->dtrEditModel->has_pending_request($personnel_id, $selected_month);
        }

        $this->data['my_requests'] = $this->dtrEditModel->get_requests_by_personnel($personnel_id);

        $this->data['content'] = $this->load->view('user_portal/dtr_edit/index', $this->data, TRUE);
        $this->load->view('user_portal/layout', $this->data);
    }

    public function get_dtr_data()
    {
        header('Content-Type: application/json');

        $personnel_id = $this->session->userdata('user_personnel_id');
        $personnel = $this->personnelModel->getpersonnel($personnel_id);
        $month = $this->input->get('month');

        if (!$personnel || !$personnel->bio_id || !$month) {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }

        $month_num = date('m', strtotime($month . '-01'));
        $year = date('Y', strtotime($month . '-01'));

        $this->db->select('*');
        $this->db->from('biometrics');
        $this->db->where('bio_id', $personnel->bio_id);
        $this->db->where('MONTH(date)', $month_num);
        $this->db->where('YEAR(date)', $year);
        $this->db->order_by('date', 'ASC');
        $records = $this->db->get()->result();

        $dtr_by_date = [];
        foreach ($records as $record) {
            $dtr_by_date[$record->date] = [
                'date' => $record->date,
                'am_in' => $record->am_in,
                'am_out' => $record->am_out,
                'pm_in' => $record->pm_in,
                'pm_out' => $record->pm_out,
                'has_data' => !empty($record->am_in) || !empty($record->am_out) || !empty($record->pm_in) || !empty($record->pm_out)
            ];
        }

        echo json_encode(['success' => true, 'data' => $dtr_by_date]);
    }

    public function submit_request()
    {
        header('Content-Type: application/json');

        $personnel_id = $this->session->userdata('user_personnel_id');
        $personnel = $this->personnelModel->getpersonnel($personnel_id);

        $input = json_decode(file_get_contents('php://input'), true);
        $month = isset($input['month']) ? $input['month'] : null;
        $changes = isset($input['changes']) ? $input['changes'] : [];
        $reason = isset($input['reason']) ? $input['reason'] : '';

        if (!$personnel || !$month || empty($changes)) {
            echo json_encode(['success' => false, 'message' => 'Invalid request data']);
            return;
        }

        if ($this->dtrEditModel->has_pending_request($personnel_id, $month)) {
            echo json_encode(['success' => false, 'message' => 'You already have a pending request for this month']);
            return;
        }

        $request_id = $this->dtrEditModel->create_request($personnel_id, $month, $reason);

        if (!$request_id) {
            echo json_encode(['success' => false, 'message' => 'Failed to create request']);
            return;
        }

        $month_num = date('m', strtotime($month . '-01'));
        $year = date('Y', strtotime($month . '-01'));

        $this->db->select('*');
        $this->db->from('biometrics');
        $this->db->where('bio_id', $personnel->bio_id);
        $this->db->where('MONTH(date)', $month_num);
        $this->db->where('YEAR(date)', $year);
        $existing_records = $this->db->get()->result();

        $existing_by_date = [];
        foreach ($existing_records as $rec) {
            $existing_by_date[$rec->date] = $rec;
        }

        foreach ($changes as $change) {
            $date = $change['date'];
            $field = $change['field'];
            $new_value = $change['value'];
            
            // Use the edit_type sent from frontend - it correctly tracks repositioned vs manual
            // based on the actual editing actions (drag-drop vs manual entry on empty cell)
            $edit_type = isset($change['type']) ? $change['type'] : 'manual';

            $original_value = null;
            if (isset($existing_by_date[$date])) {
                $field_map = [
                    'morning_in' => 'am_in',
                    'morning_out' => 'am_out',
                    'afternoon_in' => 'pm_in',
                    'afternoon_out' => 'pm_out'
                ];
                
                if (isset($field_map[$field])) {
                    $db_field = $field_map[$field];
                    $original_value = $existing_by_date[$date]->$db_field;
                }
            }

            $this->dtrEditModel->add_item($request_id, [
                'date' => $date,
                'field' => $field,
                'original_value' => $original_value,
                'new_value' => $new_value,
                'edit_type' => $edit_type
            ]);
        }

        echo json_encode([
            'success' => true, 
            'message' => 'DTR edit request submitted successfully. Waiting for approval.',
            'request_id' => $request_id
        ]);
    }

    public function view_request($id)
    {
        $this->data['title'] = 'View DTR Edit Request';
        
        $personnel_id = $this->session->userdata('user_personnel_id');
        $request = $this->dtrEditModel->get_request($id);

        if (!$request || $request->personnel_id != $personnel_id) {
            $this->session->set_flashdata('error', 'Request not found');
            redirect('personneldtredit');
        }

        $this->data['request'] = $request;
        $this->data['items'] = $this->dtrEditModel->get_items_grouped_by_date($id);

        // Get personnel DTR data for full preview (same as approver view)
        $personnel = $this->personnelModel->getpersonnel($personnel_id);
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

        $this->data['content'] = $this->load->view('user_portal/dtr_edit/view_request', $this->data, TRUE);
        $this->load->view('user_portal/layout', $this->data);
    }

    public function cancel_request($id)
    {
        $personnel_id = $this->session->userdata('user_personnel_id');
        $request = $this->dtrEditModel->get_request($id);

        if (!$request || $request->personnel_id != $personnel_id) {
            $this->session->set_flashdata('error', 'Request not found');
            redirect('personneldtredit');
        }

        if ($request->status !== 'pending') {
            $this->session->set_flashdata('error', 'Only pending requests can be cancelled');
            redirect('personneldtredit');
        }

        $this->dtrEditModel->cancel_request($id);
        $this->session->set_flashdata('success', 'Request cancelled successfully');
        redirect('personneldtredit');
    }

    public function my_requests()
    {
        $this->data['title'] = 'My DTR Edit Requests';
        
        $personnel_id = $this->session->userdata('user_personnel_id');
        $this->data['requests'] = $this->dtrEditModel->get_requests_by_personnel($personnel_id);

        $this->data['content'] = $this->load->view('user_portal/dtr_edit/my_requests', $this->data, TRUE);
        $this->load->view('user_portal/layout', $this->data);
    }

    public function print_dtr($id)
    {
        $personnel_id = $this->session->userdata('user_personnel_id');
        $request = $this->dtrEditModel->get_request($id);

        if (!$request || $request->personnel_id != $personnel_id) {
            $this->session->set_flashdata('error', 'Request not found');
            redirect('personneldtredit/my_requests');
        }

        if ($request->status !== 'approved') {
            $this->session->set_flashdata('error', 'Only approved requests can be printed');
            redirect('personneldtredit/my_requests');
        }

        $personnel = $this->personnelModel->getpersonnel($personnel_id);
        $this->data['personnel'] = $personnel;
        $this->data['request'] = $request;
        $this->data['items'] = $this->dtrEditModel->get_items_grouped_by_date($id);

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

        // Get approver info
        if ($request->approver_id) {
            $approver = $this->personnelModel->getpersonnel($request->approver_id);
            $this->data['approver'] = $approver;
        } else {
            $this->data['approver'] = null;
        }

        $this->load->view('user_portal/dtr_edit/print_dtr', $this->data);
    }
}
