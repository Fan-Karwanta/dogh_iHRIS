<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * UserManagement Controller
 * Admin panel for managing user registrations and accounts
 */
class UserManagement extends CI_Controller
{
    public $data = [];

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation', 'session']);
        $this->load->helper(['url']);
        $this->load->model('UserAccountModel', 'userAccountModel');
        $this->load->model('PersonnelModel', 'personnelModel');

        // Check if admin is logged in
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        if (!$this->ion_auth->is_admin()) {
            show_error('You must be an administrator to access this page.');
        }
    }

    /**
     * User management dashboard
     */
    public function index()
    {
        $this->data['title'] = 'User Management';
        $this->data['message'] = $this->session->flashdata('message');
        $this->data['success'] = $this->session->flashdata('success');

        // Get statistics
        $this->data['stats'] = $this->userAccountModel->get_statistics();

        // Get all users grouped by status
        $this->data['pending_users'] = $this->userAccountModel->get_pending_registrations();
        $this->data['approved_users'] = $this->userAccountModel->get_approved_users();
        $this->data['blocked_users'] = $this->userAccountModel->get_blocked_users();

        $this->base->load('default', 'admin/user_management/index', $this->data);
    }

    /**
     * View pending registrations
     */
    public function pending()
    {
        $this->data['title'] = 'Pending Registrations';
        $this->data['message'] = $this->session->flashdata('message');
        $this->data['success'] = $this->session->flashdata('success');

        $this->data['users'] = $this->userAccountModel->get_pending_registrations();

        $this->base->load('default', 'admin/user_management/pending', $this->data);
    }

    /**
     * View approved users
     */
    public function approved()
    {
        $this->data['title'] = 'Approved Users';
        $this->data['message'] = $this->session->flashdata('message');
        $this->data['success'] = $this->session->flashdata('success');

        $this->data['users'] = $this->userAccountModel->get_approved_users();

        $this->base->load('default', 'admin/user_management/approved', $this->data);
    }

    /**
     * View blocked users
     */
    public function blocked()
    {
        $this->data['title'] = 'Blocked Users';
        $this->data['message'] = $this->session->flashdata('message');
        $this->data['success'] = $this->session->flashdata('success');

        $this->data['users'] = $this->userAccountModel->get_blocked_users();

        $this->base->load('default', 'admin/user_management/blocked', $this->data);
    }

    /**
     * View user details
     */
    public function view($id)
    {
        $this->data['title'] = 'User Details';
        $this->data['message'] = $this->session->flashdata('message');
        $this->data['success'] = $this->session->flashdata('success');

        $this->data['user'] = $this->userAccountModel->get_user($id);

        if (!$this->data['user']) {
            $this->session->set_flashdata('message', 'User not found.');
            redirect('usermanagement');
        }

        // Get personnel data
        if ($this->data['user']->personnel_id) {
            $this->data['personnel'] = $this->personnelModel->getpersonnel($this->data['user']->personnel_id);
        }

        $this->base->load('default', 'admin/user_management/view', $this->data);
    }

    /**
     * Approve user registration
     */
    public function approve($id)
    {
        $user = $this->userAccountModel->get_user($id);

        if (!$user) {
            $this->session->set_flashdata('message', 'User not found.');
            redirect('usermanagement');
        }

        $admin_id = $this->ion_auth->user()->row()->id;
        $notes = $this->input->post('notes');

        if ($this->userAccountModel->approve_user($id, $admin_id, $notes)) {
            $this->session->set_flashdata('success', true);
            $this->session->set_flashdata('message', 'User account has been approved successfully.');
        } else {
            $this->session->set_flashdata('message', 'Failed to approve user account.');
        }

        redirect('usermanagement');
    }

    /**
     * Disapprove user registration
     */
    public function disapprove($id)
    {
        $user = $this->userAccountModel->get_user($id);

        if (!$user) {
            $this->session->set_flashdata('message', 'User not found.');
            redirect('usermanagement');
        }

        $admin_id = $this->ion_auth->user()->row()->id;
        $notes = $this->input->post('notes');

        if ($this->userAccountModel->disapprove_user($id, $admin_id, $notes)) {
            $this->session->set_flashdata('success', true);
            $this->session->set_flashdata('message', 'User registration has been disapproved.');
        } else {
            $this->session->set_flashdata('message', 'Failed to disapprove user registration.');
        }

        redirect('usermanagement');
    }

    /**
     * Block user account
     */
    public function block($id)
    {
        $user = $this->userAccountModel->get_user($id);

        if (!$user) {
            $this->session->set_flashdata('message', 'User not found.');
            redirect('usermanagement');
        }

        $admin_id = $this->ion_auth->user()->row()->id;
        $notes = $this->input->post('notes');

        if ($this->userAccountModel->block_user($id, $admin_id, $notes)) {
            $this->session->set_flashdata('success', true);
            $this->session->set_flashdata('message', 'User account has been blocked.');
        } else {
            $this->session->set_flashdata('message', 'Failed to block user account.');
        }

        redirect('usermanagement');
    }

    /**
     * Unblock user account
     */
    public function unblock($id)
    {
        $user = $this->userAccountModel->get_user($id);

        if (!$user) {
            $this->session->set_flashdata('message', 'User not found.');
            redirect('usermanagement');
        }

        $admin_id = $this->ion_auth->user()->row()->id;
        $notes = $this->input->post('notes');

        if ($this->userAccountModel->unblock_user($id, $admin_id, $notes)) {
            $this->session->set_flashdata('success', true);
            $this->session->set_flashdata('message', 'User account has been unblocked.');
        } else {
            $this->session->set_flashdata('message', 'Failed to unblock user account.');
        }

        redirect('usermanagement');
    }

    /**
     * Delete user account
     */
    public function delete($id)
    {
        $user = $this->userAccountModel->get_user($id);

        if (!$user) {
            $this->session->set_flashdata('message', 'User not found.');
            redirect('usermanagement');
        }

        // Also delete personnel record if exists
        if ($user->personnel_id) {
            $this->personnelModel->delete($user->personnel_id);
        }

        if ($this->userAccountModel->delete($id)) {
            $this->session->set_flashdata('success', true);
            $this->session->set_flashdata('message', 'User account has been deleted.');
        } else {
            $this->session->set_flashdata('message', 'Failed to delete user account.');
        }

        redirect('usermanagement');
    }

    /**
     * Add new user (admin creates user)
     */
    public function add()
    {
        $this->data['title'] = 'Add New User';
        $this->data['message'] = $this->session->flashdata('message');

        $this->form_validation->set_rules('firstname', 'First Name', 'required|trim');
        $this->form_validation->set_rules('lastname', 'Last Name', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
        $this->form_validation->set_rules('position', 'Position', 'required|trim');
        $this->form_validation->set_rules('username', 'Username', 'required|trim|min_length[5]|alpha_dash');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');

        if ($this->form_validation->run() === TRUE) {
            // Check if username already exists
            if ($this->userAccountModel->username_exists($this->input->post('username'))) {
                $this->session->set_flashdata('message', 'Username already exists.');
                redirect('usermanagement/add');
            }

            // Check if email already exists
            if ($this->userAccountModel->email_exists($this->input->post('email'))) {
                $this->session->set_flashdata('message', 'Email already registered.');
                redirect('usermanagement/add');
            }

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

            // Prepare personnel data
            $personnel_data = array(
                'firstname' => strtoupper($this->input->post('firstname')),
                'lastname' => strtoupper($this->input->post('lastname')),
                'middlename' => strtoupper($this->input->post('middlename')),
                'email' => $this->input->post('email'),
                'position' => $this->input->post('position'),
                'role' => $this->input->post('role'),
                'employment_type' => $this->input->post('employment_type') ?: 'Regular',
                'salary_grade' => $this->input->post('salary_grade'),
                'schedule_type' => $this->input->post('schedule_type') ?: '8:00 AM - 5:00 PM',
                'bio_id' => $this->input->post('bio_id'),
                'fb' => $this->input->post('fb'),
                'status' => 1,
                'profile_image' => $profile_image
            );

            // Prepare account data
            $account_data = array(
                'username' => $this->input->post('username'),
                'password' => $this->input->post('password'),
                'email' => $this->input->post('email'),
                'profile_image' => $profile_image
            );

            $admin_id = $this->ion_auth->user()->row()->id;
            $user_id = $this->userAccountModel->admin_create_user($personnel_data, $account_data, $admin_id);

            if ($user_id) {
                $this->session->set_flashdata('success', true);
                $this->session->set_flashdata('message', 'User account created successfully.');
                redirect('usermanagement');
            } else {
                $this->session->set_flashdata('message', 'Failed to create user account.');
                redirect('usermanagement/add');
            }
        }

        $this->base->load('default', 'admin/user_management/add', $this->data);
    }

    /**
     * Edit user account
     */
    public function edit($id)
    {
        $this->data['title'] = 'Edit User';
        $this->data['message'] = $this->session->flashdata('message');

        $this->data['user'] = $this->userAccountModel->get_user($id);

        if (!$this->data['user']) {
            $this->session->set_flashdata('message', 'User not found.');
            redirect('usermanagement');
        }

        $this->form_validation->set_rules('firstname', 'First Name', 'required|trim');
        $this->form_validation->set_rules('lastname', 'Last Name', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');

        if ($this->form_validation->run() === TRUE) {
            $user = $this->data['user'];

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
                'email' => $this->input->post('email'),
                'position' => $this->input->post('position'),
                'role' => $this->input->post('role'),
                'employment_type' => $this->input->post('employment_type'),
                'salary_grade' => $this->input->post('salary_grade'),
                'schedule_type' => $this->input->post('schedule_type'),
                'bio_id' => $this->input->post('bio_id'),
                'fb' => $this->input->post('fb')
            );

            if ($profile_image) {
                $personnel_data['profile_image'] = $profile_image;
            }

            $this->personnelModel->update($personnel_data, $user->personnel_id);

            // Update user account
            $account_data = array(
                'email' => $this->input->post('email')
            );

            if ($this->input->post('password')) {
                $account_data['password'] = $this->input->post('password');
            }

            if ($profile_image) {
                $account_data['profile_image'] = $profile_image;
            }

            $this->userAccountModel->update($id, $account_data);

            $this->session->set_flashdata('success', true);
            $this->session->set_flashdata('message', 'User account updated successfully.');
            redirect('usermanagement');
        }

        $this->base->load('default', 'admin/user_management/edit', $this->data);
    }

    /**
     * AJAX: Get user details
     */
    public function ajax_get_user($id)
    {
        $user = $this->userAccountModel->get_user($id);
        
        if ($user) {
            echo json_encode(array('success' => true, 'user' => $user));
        } else {
            echo json_encode(array('success' => false, 'message' => 'User not found'));
        }
    }

    /**
     * AJAX: Approve user
     */
    public function ajax_approve()
    {
        $id = $this->input->post('id');
        $notes = $this->input->post('notes');
        $admin_id = $this->ion_auth->user()->row()->id;

        if ($this->userAccountModel->approve_user($id, $admin_id, $notes)) {
            echo json_encode(array('success' => true, 'message' => 'User approved successfully'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Failed to approve user'));
        }
    }

    /**
     * AJAX: Disapprove user
     */
    public function ajax_disapprove()
    {
        $id = $this->input->post('id');
        $notes = $this->input->post('notes');
        $admin_id = $this->ion_auth->user()->row()->id;

        if ($this->userAccountModel->disapprove_user($id, $admin_id, $notes)) {
            echo json_encode(array('success' => true, 'message' => 'User disapproved'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Failed to disapprove user'));
        }
    }

    /**
     * AJAX: Block user
     */
    public function ajax_block()
    {
        $id = $this->input->post('id');
        $notes = $this->input->post('notes');
        $admin_id = $this->ion_auth->user()->row()->id;

        if ($this->userAccountModel->block_user($id, $admin_id, $notes)) {
            echo json_encode(array('success' => true, 'message' => 'User blocked'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Failed to block user'));
        }
    }

    /**
     * AJAX: Unblock user
     */
    public function ajax_unblock()
    {
        $id = $this->input->post('id');
        $notes = $this->input->post('notes');
        $admin_id = $this->ion_auth->user()->row()->id;

        if ($this->userAccountModel->unblock_user($id, $admin_id, $notes)) {
            echo json_encode(array('success' => true, 'message' => 'User unblocked'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Failed to unblock user'));
        }
    }
}
