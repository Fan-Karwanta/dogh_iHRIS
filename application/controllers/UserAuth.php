<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * UserAuth Controller
 * Handles authentication for regular users (employees)
 */
class UserAuth extends CI_Controller
{
    public $data = [];

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['form_validation', 'session']);
        $this->load->helper(['url', 'language']);
        $this->load->model('UserAccountModel', 'userAccountModel');
        $this->load->model('PersonnelModel', 'personnelModel');
    }

    /**
     * User type selection page (landing page)
     */
    public function index()
    {
        // If already logged in as user, redirect to user dashboard
        if ($this->session->userdata('user_logged_in')) {
            redirect('user/dashboard');
        }
        
        // If already logged in as admin, redirect to admin dashboard
        if ($this->ion_auth->logged_in()) {
            redirect('dashboard');
        }
        
        $this->data['title'] = 'Select User Type';
        $this->load->view('auth/user_type_selection', $this->data);
    }

    /**
     * User login page
     */
    public function login()
    {
        // If already logged in, redirect to dashboard
        if ($this->session->userdata('user_logged_in')) {
            redirect('user/dashboard');
        }

        $this->data['title'] = 'User Login';
        $this->data['message'] = $this->session->flashdata('message');

        // Validate form input
        $this->form_validation->set_rules('identity', 'Username or Email', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() === TRUE) {
            $identity = $this->input->post('identity');
            $password = $this->input->post('password');
            $remember = (bool) $this->input->post('remember');

            // Check login attempts
            $ip_address = $this->input->ip_address();
            $attempts = $this->userAccountModel->get_login_attempts_count($ip_address, $identity);
            
            if ($attempts >= 5) {
                $this->session->set_flashdata('message', 'Too many login attempts. Please try again later.');
                redirect('userauth/login');
            }

            // Verify credentials
            $user = $this->userAccountModel->verify_login($identity, $password);

            if ($user) {
                // Check account status
                if ($user->status === 'pending') {
                    $this->session->set_flashdata('message', 'Your account is pending approval. Please wait for admin approval.');
                    redirect('userauth/login');
                } elseif ($user->status === 'disapproved') {
                    $this->session->set_flashdata('message', 'Your account registration was disapproved. Please contact the administrator.');
                    redirect('userauth/login');
                } elseif ($user->status === 'blocked') {
                    $this->session->set_flashdata('message', 'Your account has been blocked. Please contact the administrator.');
                    redirect('userauth/login');
                }

                // Login successful
                $this->userAccountModel->clear_login_attempts($ip_address, $identity);
                $this->userAccountModel->update_last_login($user->id);

                // Set session data
                $session_data = array(
                    'user_logged_in' => TRUE,
                    'user_account_id' => $user->id,
                    'user_personnel_id' => $user->personnel_id,
                    'user_username' => $user->username,
                    'user_email' => $user->email,
                    'user_type' => 'employee'
                );
                $this->session->set_userdata($session_data);

                // Handle remember me
                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    $this->userAccountModel->update($user->id, array('remember_token' => $token));
                    $this->input->set_cookie('user_remember', $token, 86400 * 30);
                }

                redirect('user/dashboard');
            } else {
                // Login failed
                $this->userAccountModel->record_login_attempt($ip_address, $identity);
                $this->session->set_flashdata('message', 'Invalid username/email or password.');
                redirect('userauth/login');
            }
        }

        $this->load->view('auth/user_login', $this->data);
    }

    /**
     * User registration page
     */
    public function register()
    {
        // If already logged in, redirect to dashboard
        if ($this->session->userdata('user_logged_in')) {
            redirect('user/dashboard');
        }

        $this->data['title'] = 'User Registration';
        $this->data['message'] = $this->session->flashdata('message');

        // Set validation rules
        $this->form_validation->set_rules('firstname', 'First Name', 'required|trim');
        $this->form_validation->set_rules('lastname', 'Last Name', 'required|trim');
        $this->form_validation->set_rules('middlename', 'Middle Name', 'trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
        $this->form_validation->set_rules('position', 'Position', 'required|trim');
        $this->form_validation->set_rules('employment_type', 'Employment Type', 'required');
        $this->form_validation->set_rules('username', 'Username', 'required|trim|min_length[5]|alpha_dash');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');

        if ($this->form_validation->run() === TRUE) {
            // Check if username already exists
            if ($this->userAccountModel->username_exists($this->input->post('username'))) {
                $this->session->set_flashdata('message', 'Username already exists. Please choose a different one.');
                redirect('userauth/register');
            }

            // Check if email already exists in user_accounts
            if ($this->userAccountModel->email_exists($this->input->post('email'))) {
                $this->session->set_flashdata('message', 'Email already registered. Please use a different email or login.');
                redirect('userauth/register');
            }

            // Handle profile image upload
            $profile_image = null;
            if (!empty($_FILES['profile_image']['name'])) {
                $config['upload_path'] = './assets/uploads/profile_images/';
                $config['allowed_types'] = 'jpg|jpeg|png|gif';
                $config['max_size'] = 2048;
                $config['encrypt_name'] = TRUE;

                // Create directory if not exists
                if (!is_dir($config['upload_path'])) {
                    mkdir($config['upload_path'], 0755, true);
                }

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('profile_image')) {
                    $upload_data = $this->upload->data();
                    $profile_image = $upload_data['file_name'];
                }
            }

            // Create personnel record first
            $personnel_data = array(
                'firstname' => strtoupper($this->input->post('firstname')),
                'lastname' => strtoupper($this->input->post('lastname')),
                'middlename' => strtoupper($this->input->post('middlename')),
                'email' => $this->input->post('email'),
                'position' => $this->input->post('position'),
                'role' => $this->input->post('role'),
                'employment_type' => $this->input->post('employment_type'),
                'salary_grade' => $this->input->post('salary_grade'),
                'schedule_type' => $this->input->post('schedule_type') ?: '8:00 AM - 5:00 PM',
                'fb' => $this->input->post('fb'),
                'status' => 1,
                'profile_image' => $profile_image,
                'created_at' => date('Y-m-d H:i:s')
            );

            $this->personnelModel->create_personnel($personnel_data);
            $personnel_id = $this->db->insert_id();

            if (!$personnel_id) {
                $this->session->set_flashdata('message', 'Registration failed. Please try again.');
                redirect('userauth/register');
            }

            // Create user account
            $account_data = array(
                'personnel_id' => $personnel_id,
                'username' => $this->input->post('username'),
                'password' => $this->input->post('password'),
                'email' => $this->input->post('email'),
                'status' => 'pending',
                'profile_image' => $profile_image
            );

            $user_id = $this->userAccountModel->register($account_data);

            if ($user_id) {
                $this->session->set_flashdata('message', 'Registration successful! Please wait for admin approval before you can login.');
                $this->session->set_flashdata('success', true);
                redirect('userauth/login');
            } else {
                // Rollback personnel creation
                $this->personnelModel->delete($personnel_id);
                $this->session->set_flashdata('message', 'Registration failed. Please try again.');
                redirect('userauth/register');
            }
        }

        $this->load->view('auth/user_register', $this->data);
    }

    /**
     * User logout
     */
    public function logout()
    {
        // Clear remember token
        $user_id = $this->session->userdata('user_account_id');
        if ($user_id) {
            $this->userAccountModel->update($user_id, array('remember_token' => null));
        }

        // Clear session
        $this->session->unset_userdata('user_logged_in');
        $this->session->unset_userdata('user_account_id');
        $this->session->unset_userdata('user_personnel_id');
        $this->session->unset_userdata('user_username');
        $this->session->unset_userdata('user_email');
        $this->session->unset_userdata('user_type');

        // Clear cookie
        delete_cookie('user_remember');

        $this->session->set_flashdata('message', 'You have been logged out successfully.');
        redirect('userauth/login');
    }

    /**
     * Check registration status
     */
    public function check_status()
    {
        $this->data['title'] = 'Check Registration Status';
        $this->data['message'] = $this->session->flashdata('message');

        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');

        if ($this->form_validation->run() === TRUE) {
            $user = $this->userAccountModel->get_user_by_email($this->input->post('email'));
            
            if ($user) {
                $this->data['user_status'] = $user->status;
                $this->data['admin_notes'] = $user->admin_notes;
            } else {
                $this->data['not_found'] = true;
            }
        }

        $this->load->view('auth/check_status', $this->data);
    }
}
