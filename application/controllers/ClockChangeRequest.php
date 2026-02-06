<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ClockChangeRequest extends CI_Controller
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

        // Ensure tables exist
        $this->clockChangeModel->ensure_tables_exist();

        if (!$this->session->userdata('user_logged_in')) {
            redirect('userauth/login');
        }

        $this->data['current_user'] = $this->userAccountModel->get_user($this->session->userdata('user_account_id'));
        $this->data['notifications'] = $this->userAccountModel->get_notifications($this->session->userdata('user_account_id'), 5);
        $this->data['unread_count'] = $this->userAccountModel->get_unread_notification_count($this->session->userdata('user_account_id'));
    }

    /**
     * List all my clock change requests
     */
    public function index()
    {
        $this->data['title'] = 'Failure to Clock / Time Changes';
        $personnel_id = $this->session->userdata('user_personnel_id');
        $this->data['requests'] = $this->clockChangeModel->get_requests_by_personnel($personnel_id);
        $this->data['content'] = $this->load->view('user_portal/forgot_clockin/index', $this->data, TRUE);
        $this->load->view('user_portal/layout', $this->data);
    }

    /**
     * Show the create form
     */
    public function create()
    {
        $this->data['title'] = 'New Failure to Clock / Time Changes Request';
        $personnel_id = $this->session->userdata('user_personnel_id');
        $personnel = $this->personnelModel->getpersonnel($personnel_id);
        $this->data['personnel'] = $personnel;

        // Get immediate supervisor
        $supervisor = $this->clockChangeModel->get_immediate_supervisor($personnel_id);
        $this->data['supervisor'] = $supervisor;

        // Generate next control number for preview
        $this->data['control_no'] = $this->clockChangeModel->generate_control_no();

        $this->data['content'] = $this->load->view('user_portal/forgot_clockin/create', $this->data, TRUE);
        $this->load->view('user_portal/layout', $this->data);
    }

    /**
     * Save the request (AJAX)
     */
    public function save()
    {
        $personnel_id = $this->session->userdata('user_personnel_id');
        $items = $this->input->post('items');

        if (empty($items)) {
            echo json_encode(['success' => false, 'message' => 'Please add at least one entry.']);
            return;
        }

        // Server-side validation
        $duplicate_check = [];
        $today = date('Y-m-d');
        foreach ($items as $item) {
            $date = isset($item['date']) ? $item['date'] : '';
            $am_pm = isset($item['am_pm']) ? $item['am_pm'] : '';
            $time_in = !empty($item['time_in']) ? 1 : 0;
            $time_out = !empty($item['time_out']) ? 1 : 0;
            $time_change = isset($item['time_change']) ? trim($item['time_change']) : '';

            if (empty($date)) continue;

            // Must check at least IN or OUT
            if (!$time_in && !$time_out) {
                echo json_encode(['success' => false, 'message' => "Row $date: Please check at least IN or OUT."]);
                return;
            }

            // Time Change is required
            if (empty($time_change)) {
                echo json_encode(['success' => false, 'message' => "Row $date: Time Change value is required."]);
                return;
            }

            // Validate time format (HH:MM, 12-hour, 1-12 hours)
            if (!preg_match('/^(\d{1,2}):(\d{2})$/', $time_change, $tm)) {
                echo json_encode(['success' => false, 'message' => "Row $date: Invalid time format '$time_change'. Use HH:MM format."]);
                return;
            }
            $h = intval($tm[1]);
            $mi = intval($tm[2]);
            if ($h < 1 || $h > 12 || $mi < 0 || $mi > 59) {
                echo json_encode(['success' => false, 'message' => "Row $date: Hours must be 1-12 and minutes 0-59."]);
                return;
            }

            // No future dates
            if ($date > $today) {
                echo json_encode(['success' => false, 'message' => "Row $date: Date cannot be in the future."]);
                return;
            }

            // Duplicate check
            if ($time_in) {
                $key = $date . '_' . $am_pm . '_IN';
                if (isset($duplicate_check[$key])) {
                    echo json_encode(['success' => false, 'message' => "Duplicate entry: $date $am_pm IN appears more than once."]);
                    return;
                }
                $duplicate_check[$key] = true;
            }
            if ($time_out) {
                $key = $date . '_' . $am_pm . '_OUT';
                if (isset($duplicate_check[$key])) {
                    echo json_encode(['success' => false, 'message' => "Duplicate entry: $date $am_pm OUT appears more than once."]);
                    return;
                }
                $duplicate_check[$key] = true;
            }
        }

        // Create the request
        $request_id = $this->clockChangeModel->create_request($personnel_id);

        if (!$request_id) {
            echo json_encode(['success' => false, 'message' => 'Failed to create request.']);
            return;
        }

        // Add items
        foreach ($items as $item) {
            $this->clockChangeModel->add_item($request_id, [
                'date' => $item['date'],
                'am_pm' => $item['am_pm'],
                'time_in' => !empty($item['time_in']) ? 1 : 0,
                'time_out' => !empty($item['time_out']) ? 1 : 0,
                'time_change' => !empty($item['time_change']) ? trim($item['time_change']) : null,
                'reason' => !empty($item['reason']) ? trim($item['reason']) : null
            ]);
        }

        // Notify approver(s)
        $personnel = $this->personnelModel->getpersonnel($personnel_id);
        $approvers = $this->hierarchyModel->get_approvers_for_personnel($personnel_id);
        $requester_name = $personnel->lastname . ', ' . $personnel->firstname;

        foreach ($approvers as $approver) {
            $this->db->where('personnel_id', $approver->personnel_id);
            $approver_account = $this->db->get('user_accounts')->row();
            if ($approver_account) {
                $this->userAccountModel->add_notification(
                    $approver_account->id,
                    'New Failure to Clock Request',
                    $requester_name . ' submitted a Failure to Clock / Time Changes request. Please review and take action.',
                    'info'
                );
            }
        }

        echo json_encode([
            'success' => true,
            'message' => 'Request submitted successfully. Waiting for approval.',
            'request_id' => $request_id
        ]);
    }

    /**
     * View a specific request
     */
    public function view($id)
    {
        $this->data['title'] = 'View Clock Change Request';
        $personnel_id = $this->session->userdata('user_personnel_id');
        $request = $this->clockChangeModel->get_request($id);

        if (!$request || $request->personnel_id != $personnel_id) {
            $this->session->set_flashdata('error', 'Request not found or unauthorized.');
            redirect('clockchangerequest');
        }

        $this->data['request'] = $request;
        $this->data['items'] = $this->clockChangeModel->get_request_items($id);
        $this->data['personnel'] = $this->personnelModel->getpersonnel($personnel_id);

        // Get supervisor info
        $supervisor = $this->clockChangeModel->get_immediate_supervisor($personnel_id);
        $this->data['supervisor'] = $supervisor;

        // Get approver info if approved/rejected
        if ($request->approver_id) {
            $this->data['approver'] = $this->personnelModel->getpersonnel($request->approver_id);
        }

        $this->data['content'] = $this->load->view('user_portal/forgot_clockin/view', $this->data, TRUE);
        $this->load->view('user_portal/layout', $this->data);
    }

    /**
     * Print view
     */
    public function print_request($id)
    {
        $personnel_id = $this->session->userdata('user_personnel_id');
        $request = $this->clockChangeModel->get_request($id);

        if (!$request || $request->personnel_id != $personnel_id) {
            $this->session->set_flashdata('error', 'Request not found or unauthorized.');
            redirect('clockchangerequest');
        }

        $data['request'] = $request;
        $data['items'] = $this->clockChangeModel->get_request_items($id);
        $data['personnel'] = $this->personnelModel->getpersonnel($personnel_id);

        // Get supervisor info
        $supervisor = $this->clockChangeModel->get_immediate_supervisor($personnel_id);
        $data['supervisor'] = $supervisor;

        // Get approver info
        if ($request->approver_id) {
            $data['approver'] = $this->personnelModel->getpersonnel($request->approver_id);
        }

        $this->load->view('user_portal/forgot_clockin/print', $data);
    }

    /**
     * Cancel a pending request
     */
    public function cancel($id)
    {
        $personnel_id = $this->session->userdata('user_personnel_id');
        $request = $this->clockChangeModel->get_request($id);

        if (!$request || $request->personnel_id != $personnel_id || $request->status !== 'pending') {
            $this->session->set_flashdata('error', 'Cannot cancel this request.');
            redirect('clockchangerequest');
        }

        $this->clockChangeModel->cancel_request($id);
        $this->session->set_flashdata('success', 'Request cancelled successfully.');
        redirect('clockchangerequest');
    }
}
