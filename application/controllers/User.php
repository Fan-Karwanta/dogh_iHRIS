<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * User Controller
 * Handles user dashboard and DTR data for regular employees
 */
class User extends CI_Controller
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
        $this->load->model('UserProfileModel', 'profileModel');

        // Check if user is logged in
        if (!$this->session->userdata('user_logged_in')) {
            redirect('userauth/login');
        }

        // Get current user data
        $this->data['current_user'] = $this->userAccountModel->get_user($this->session->userdata('user_account_id'));
        $this->data['notifications'] = $this->userAccountModel->get_notifications($this->session->userdata('user_account_id'), 5);
        $this->data['unread_count'] = $this->userAccountModel->get_unread_notification_count($this->session->userdata('user_account_id'));
    }

    /**
     * User Dashboard
     */
    public function dashboard()
    {
        $this->data['title'] = 'My Dashboard';
        
        $user = $this->data['current_user'];
        $personnel_id = $this->session->userdata('user_personnel_id');
        
        // Get personnel data
        $personnel = $this->personnelModel->getpersonnel($personnel_id);
        $this->data['personnel'] = $personnel;

        if ($personnel && $personnel->email) {
            // Get selected month and year
            $selected_month = $this->input->get('month') ? $this->input->get('month') : date('m');
            $selected_year = $this->input->get('year') ? $this->input->get('year') : date('Y');

            // Get monthly statistics
            $this->data['monthly_stats'] = $this->profileModel->getMonthlyStats($personnel->email, $selected_month, $selected_year);
            
            // Get yearly statistics
            $this->data['yearly_stats'] = $this->profileModel->getYearlyStats($personnel->email, $selected_year);
            
            // Get attendance trends (last 6 months)
            $this->data['attendance_trends'] = $this->profileModel->getAttendanceTrends($personnel->email, 6);
            
            // Get recent attendance records
            $this->data['recent_attendance'] = $this->profileModel->getRecentAttendance($personnel->email, 10);
            
            // Get performance summary
            $this->data['performance'] = $this->profileModel->getPerformanceSummary($personnel->email, $selected_month, $selected_year);

            // Get missing clock-ins for this user
            $this->data['missing_clockins'] = $this->get_user_missing_clockins($personnel->bio_id, $selected_month, $selected_year);

            $this->data['selected_month'] = $selected_month;
            $this->data['selected_year'] = $selected_year;
        }

        // Load dashboard content into layout
        $this->data['content'] = $this->load->view('user_portal/dashboard_content', $this->data, TRUE);
        $this->load->view('user_portal/layout', $this->data);
    }

    /**
     * Get missing clock-ins for a specific user
     */
    private function get_user_missing_clockins($bio_id, $month, $year)
    {
        if (!$bio_id) return array();

        $start_date = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01';
        $end_date = date('Y-m-t', strtotime($start_date));

        $this->db->select('biometrics.*');
        $this->db->from('biometrics');
        $this->db->where('biometrics.bio_id', $bio_id);
        $this->db->where('biometrics.date >=', $start_date);
        $this->db->where('biometrics.date <=', $end_date);
        
        // Get records with at least one time entry
        $this->db->group_start();
        $this->db->where('biometrics.am_in IS NOT NULL');
        $this->db->or_where('biometrics.am_out IS NOT NULL');
        $this->db->or_where('biometrics.pm_in IS NOT NULL');
        $this->db->or_where('biometrics.pm_out IS NOT NULL');
        $this->db->group_end();

        $this->db->order_by('biometrics.date', 'DESC');
        $records = $this->db->get()->result();

        $missing = array();
        foreach ($records as $record) {
            $day_of_week = date('w', strtotime($record->date));
            
            // Skip weekends
            if ($day_of_week == 0 || $day_of_week == 6) {
                continue;
            }

            $missing_fields = array();
            if (empty($record->am_in)) $missing_fields[] = 'AM In';
            if (empty($record->am_out)) $missing_fields[] = 'AM Out';
            if (empty($record->pm_in)) $missing_fields[] = 'PM In';
            if (empty($record->pm_out)) $missing_fields[] = 'PM Out';

            if (!empty($missing_fields)) {
                $missing[] = array(
                    'date' => $record->date,
                    'missing' => $missing_fields,
                    'am_in' => $record->am_in,
                    'am_out' => $record->am_out,
                    'pm_in' => $record->pm_in,
                    'pm_out' => $record->pm_out
                );
            }
        }

        return $missing;
    }

    /**
     * View DTR records
     */
    public function dtr()
    {
        $this->data['title'] = 'My DTR Records';
        
        $personnel_id = $this->session->userdata('user_personnel_id');
        $personnel = $this->personnelModel->getpersonnel($personnel_id);
        $this->data['personnel'] = $personnel;

        // Get selected month and year
        $selected_month = $this->input->get('month') ? $this->input->get('month') : date('m');
        $selected_year = $this->input->get('year') ? $this->input->get('year') : date('Y');

        if ($personnel && $personnel->bio_id) {
            $start_date = $selected_year . '-' . str_pad($selected_month, 2, '0', STR_PAD_LEFT) . '-01';
            $end_date = date('Y-m-t', strtotime($start_date));

            $this->db->select('biometrics.*');
            $this->db->from('biometrics');
            $this->db->where('biometrics.bio_id', $personnel->bio_id);
            $this->db->where('biometrics.date >=', $start_date);
            $this->db->where('biometrics.date <=', $end_date);
            $this->db->order_by('biometrics.date', 'ASC');
            
            $this->data['dtr_records'] = $this->db->get()->result();
        } else {
            $this->data['dtr_records'] = array();
        }

        $this->data['selected_month'] = $selected_month;
        $this->data['selected_year'] = $selected_year;

        // Load DTR content into layout
        $this->data['content'] = $this->load->view('user_portal/dtr_content', $this->data, TRUE);
        $this->load->view('user_portal/layout', $this->data);
    }

    /**
     * View profile
     */
    public function profile()
    {
        $this->data['title'] = 'My Profile';
        $this->data['message'] = $this->session->flashdata('message');
        $this->data['success'] = $this->session->flashdata('success');

        $personnel_id = $this->session->userdata('user_personnel_id');
        $this->data['personnel'] = $this->personnelModel->getpersonnel($personnel_id);

        // Load profile content into layout
        $this->data['content'] = $this->load->view('user_portal/profile_content', $this->data, TRUE);
        $this->load->view('user_portal/layout', $this->data);
    }

    /**
     * Update profile
     */
    public function update_profile()
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('firstname', 'First Name', 'required|trim');
        $this->form_validation->set_rules('lastname', 'Last Name', 'required|trim');

        if ($this->form_validation->run() === TRUE) {
            $personnel_id = $this->session->userdata('user_personnel_id');
            $user_id = $this->session->userdata('user_account_id');

            // Handle profile image upload
            $profile_image = null;
            if (!empty($_FILES['profile_image']['name'])) {
                $config['upload_path'] = './assets/uploads/profile_images/';
                $config['allowed_types'] = 'jpg|jpeg|png|gif';
                $config['max_size'] = 2048;
                $config['encrypt_name'] = TRUE;

                if (!is_dir($config['upload_path'])) {
                    mkdir($config['upload_path'], 0755, true);
                }

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('profile_image')) {
                    $upload_data = $this->upload->data();
                    $profile_image = $upload_data['file_name'];
                }
            }

            // Update personnel data
            $personnel_data = array(
                'firstname' => strtoupper($this->input->post('firstname')),
                'lastname' => strtoupper($this->input->post('lastname')),
                'middlename' => strtoupper($this->input->post('middlename')),
                'fb' => $this->input->post('fb')
            );

            if ($profile_image) {
                $personnel_data['profile_image'] = $profile_image;
            }

            $this->personnelModel->update($personnel_data, $personnel_id);

            // Update user account profile image if changed
            if ($profile_image) {
                $this->userAccountModel->update($user_id, array('profile_image' => $profile_image));
            }

            $this->session->set_flashdata('success', true);
            $this->session->set_flashdata('message', 'Profile updated successfully!');
        } else {
            $this->session->set_flashdata('message', validation_errors());
        }

        redirect('user/profile');
    }

    /**
     * Change password
     */
    public function change_password()
    {
        $this->data['title'] = 'Change Password';
        $this->data['message'] = $this->session->flashdata('message');
        $this->data['success'] = $this->session->flashdata('success');

        $this->load->library('form_validation');

        $this->form_validation->set_rules('current_password', 'Current Password', 'required');
        $this->form_validation->set_rules('new_password', 'New Password', 'required|min_length[8]');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[new_password]');

        if ($this->form_validation->run() === TRUE) {
            $user_id = $this->session->userdata('user_account_id');
            $user = $this->userAccountModel->get_user($user_id);

            // Verify current password
            if (password_verify($this->input->post('current_password'), $user->password)) {
                $this->userAccountModel->update($user_id, array(
                    'password' => $this->input->post('new_password')
                ));

                $this->session->set_flashdata('success', true);
                $this->session->set_flashdata('message', 'Password changed successfully!');
            } else {
                $this->session->set_flashdata('message', 'Current password is incorrect.');
            }

            redirect('user/change_password');
        }

        // Load change password content into layout
        $this->data['content'] = $this->load->view('user_portal/change_password_content', $this->data, TRUE);
        $this->load->view('user_portal/layout', $this->data);
    }

    /**
     * View notifications
     */
    public function notifications()
    {
        $this->data['title'] = 'My Notifications';
        
        $user_id = $this->session->userdata('user_account_id');
        $this->data['all_notifications'] = $this->userAccountModel->get_notifications($user_id, 50);

        // Load notifications content into layout
        $this->data['content'] = $this->load->view('user_portal/notifications_content', $this->data, TRUE);
        $this->load->view('user_portal/layout', $this->data);
    }

    /**
     * Mark notification as read
     */
    public function mark_notification_read($id)
    {
        $this->userAccountModel->mark_notification_read($id);
        
        if ($this->input->is_ajax_request()) {
            echo json_encode(array('success' => true));
        } else {
            redirect('user/notifications');
        }
    }

    /**
     * Attendance history
     */
    public function attendance_history()
    {
        $this->data['title'] = 'Attendance History';
        
        $personnel_id = $this->session->userdata('user_personnel_id');
        $personnel = $this->personnelModel->getpersonnel($personnel_id);
        $this->data['personnel'] = $personnel;

        if ($personnel && $personnel->email) {
            // Get all attendance records
            $this->data['attendance_history'] = $this->profileModel->getRecentAttendance($personnel->email, 100);
        }

        // Load attendance history content into layout
        $this->data['content'] = $this->load->view('user_portal/attendance_history_content', $this->data, TRUE);
        $this->load->view('user_portal/layout', $this->data);
    }
}
