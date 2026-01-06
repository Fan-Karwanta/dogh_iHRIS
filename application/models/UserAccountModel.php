<?php
defined('BASEPATH') or exit('No direct script access allowed');

class UserAccountModel extends CI_Model
{
    protected $table = 'user_accounts';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get all user accounts with personnel data
     */
    public function get_all_users($status = null)
    {
        $this->db->select('user_accounts.*, personnels.firstname, personnels.lastname, personnels.middlename, 
                          personnels.position, personnels.email as personnel_email, personnels.bio_id,
                          personnels.employment_type, personnels.profile_image as personnel_profile_image');
        $this->db->from($this->table);
        $this->db->join('personnels', 'personnels.id = user_accounts.personnel_id', 'left');
        
        if ($status) {
            $this->db->where('user_accounts.status', $status);
        }
        
        $this->db->order_by('user_accounts.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get pending registrations for admin approval
     */
    public function get_pending_registrations()
    {
        return $this->get_all_users('pending');
    }

    /**
     * Get approved users
     */
    public function get_approved_users()
    {
        return $this->get_all_users('approved');
    }

    /**
     * Get blocked users
     */
    public function get_blocked_users()
    {
        return $this->get_all_users('blocked');
    }

    /**
     * Get user by ID with personnel data
     */
    public function get_user($id)
    {
        $this->db->select('user_accounts.*, personnels.firstname, personnels.lastname, personnels.middlename, 
                          personnels.position, personnels.email as personnel_email, personnels.bio_id,
                          personnels.employment_type, personnels.salary_grade, personnels.schedule_type,
                          personnels.role, personnels.fb, personnels.profile_image as personnel_profile_image');
        $this->db->from($this->table);
        $this->db->join('personnels', 'personnels.id = user_accounts.personnel_id', 'left');
        $this->db->where('user_accounts.id', $id);
        return $this->db->get()->row();
    }

    /**
     * Get user by username
     */
    public function get_user_by_username($username)
    {
        $this->db->select('user_accounts.*, personnels.firstname, personnels.lastname, personnels.middlename, 
                          personnels.position, personnels.email as personnel_email, personnels.bio_id,
                          personnels.profile_image as personnel_profile_image');
        $this->db->from($this->table);
        $this->db->join('personnels', 'personnels.id = user_accounts.personnel_id', 'left');
        $this->db->where('user_accounts.username', $username);
        return $this->db->get()->row();
    }

    /**
     * Get user by email
     */
    public function get_user_by_email($email)
    {
        $this->db->select('user_accounts.*, personnels.firstname, personnels.lastname, personnels.middlename, 
                          personnels.position, personnels.email as personnel_email, personnels.bio_id,
                          personnels.profile_image as personnel_profile_image');
        $this->db->from($this->table);
        $this->db->join('personnels', 'personnels.id = user_accounts.personnel_id', 'left');
        $this->db->where('user_accounts.email', $email);
        return $this->db->get()->row();
    }

    /**
     * Get user by personnel ID
     */
    public function get_user_by_personnel_id($personnel_id)
    {
        $this->db->where('personnel_id', $personnel_id);
        return $this->db->get($this->table)->row();
    }

    /**
     * Check if username exists
     */
    public function username_exists($username, $exclude_id = null)
    {
        $this->db->where('username', $username);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        return $this->db->count_all_results($this->table) > 0;
    }

    /**
     * Check if email exists
     */
    public function email_exists($email, $exclude_id = null)
    {
        $this->db->where('email', $email);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        return $this->db->count_all_results($this->table) > 0;
    }

    /**
     * Register new user account
     */
    public function register($data)
    {
        // Hash password
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Update user account
     */
    public function update($id, $data)
    {
        // Hash password if being updated
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        } else {
            unset($data['password']);
        }
        
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Approve user registration
     */
    public function approve_user($id, $admin_id, $notes = null)
    {
        $data = array(
            'status' => 'approved',
            'approved_at' => date('Y-m-d H:i:s'),
            'approved_by' => $admin_id,
            'admin_notes' => $notes
        );
        
        $this->db->where('id', $id);
        $result = $this->db->update($this->table, $data);
        
        if ($result) {
            // Send notification to user
            $this->add_notification($id, 'Account Approved', 'Your account has been approved. You can now login to the system.', 'success');
        }
        
        return $result;
    }

    /**
     * Disapprove user registration
     */
    public function disapprove_user($id, $admin_id, $notes = null)
    {
        $data = array(
            'status' => 'disapproved',
            'approved_by' => $admin_id,
            'admin_notes' => $notes
        );
        
        $this->db->where('id', $id);
        $result = $this->db->update($this->table, $data);
        
        if ($result) {
            // Send notification to user
            $message = 'Your account registration has been disapproved.';
            if ($notes) {
                $message .= ' Reason: ' . $notes;
            }
            $this->add_notification($id, 'Account Disapproved', $message, 'danger');
        }
        
        return $result;
    }

    /**
     * Block user account
     */
    public function block_user($id, $admin_id, $notes = null)
    {
        $data = array(
            'status' => 'blocked',
            'admin_notes' => $notes
        );
        
        $this->db->where('id', $id);
        $result = $this->db->update($this->table, $data);
        
        if ($result) {
            // Send notification to user
            $message = 'Your account has been blocked.';
            if ($notes) {
                $message .= ' Reason: ' . $notes;
            }
            $this->add_notification($id, 'Account Blocked', $message, 'danger');
        }
        
        return $result;
    }

    /**
     * Unblock user account
     */
    public function unblock_user($id, $admin_id, $notes = null)
    {
        $data = array(
            'status' => 'approved',
            'admin_notes' => $notes
        );
        
        $this->db->where('id', $id);
        $result = $this->db->update($this->table, $data);
        
        if ($result) {
            $this->add_notification($id, 'Account Unblocked', 'Your account has been unblocked. You can now login again.', 'success');
        }
        
        return $result;
    }

    /**
     * Delete user account
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    /**
     * Verify login credentials
     */
    public function verify_login($identity, $password)
    {
        // Check by username or email
        $this->db->group_start();
        $this->db->where('username', $identity);
        $this->db->or_where('email', $identity);
        $this->db->group_end();
        
        $user = $this->db->get($this->table)->row();
        
        if ($user && password_verify($password, $user->password)) {
            return $user;
        }
        
        return false;
    }

    /**
     * Update last login time
     */
    public function update_last_login($id)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, array('last_login' => date('Y-m-d H:i:s')));
    }

    /**
     * Add notification for user
     */
    public function add_notification($user_account_id, $title, $message, $type = 'info')
    {
        $data = array(
            'user_account_id' => $user_account_id,
            'title' => $title,
            'message' => $message,
            'type' => $type
        );
        
        return $this->db->insert('user_notifications', $data);
    }

    /**
     * Get user notifications
     */
    public function get_notifications($user_account_id, $limit = 10, $unread_only = false)
    {
        $this->db->where('user_account_id', $user_account_id);
        
        if ($unread_only) {
            $this->db->where('is_read', 0);
        }
        
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit);
        
        return $this->db->get('user_notifications')->result();
    }

    /**
     * Mark notification as read
     */
    public function mark_notification_read($notification_id)
    {
        $this->db->where('id', $notification_id);
        return $this->db->update('user_notifications', array(
            'is_read' => 1,
            'read_at' => date('Y-m-d H:i:s')
        ));
    }

    /**
     * Get unread notification count
     */
    public function get_unread_notification_count($user_account_id)
    {
        $this->db->where('user_account_id', $user_account_id);
        $this->db->where('is_read', 0);
        return $this->db->count_all_results('user_notifications');
    }

    /**
     * Record login attempt
     */
    public function record_login_attempt($ip_address, $login)
    {
        $data = array(
            'ip_address' => $ip_address,
            'login' => $login,
            'time' => time()
        );
        
        return $this->db->insert('user_login_attempts', $data);
    }

    /**
     * Get recent login attempts count
     */
    public function get_login_attempts_count($ip_address, $login, $time_period = 600)
    {
        $this->db->where('ip_address', $ip_address);
        $this->db->where('login', $login);
        $this->db->where('time >', time() - $time_period);
        
        return $this->db->count_all_results('user_login_attempts');
    }

    /**
     * Clear old login attempts
     */
    public function clear_login_attempts($ip_address, $login)
    {
        $this->db->where('ip_address', $ip_address);
        $this->db->where('login', $login);
        return $this->db->delete('user_login_attempts');
    }

    /**
     * Get statistics for admin dashboard
     */
    public function get_statistics()
    {
        $stats = new stdClass();
        
        // Total users
        $stats->total = $this->db->count_all($this->table);
        
        // Pending registrations
        $this->db->where('status', 'pending');
        $stats->pending = $this->db->count_all_results($this->table);
        
        // Approved users
        $this->db->where('status', 'approved');
        $stats->approved = $this->db->count_all_results($this->table);
        
        // Blocked users
        $this->db->where('status', 'blocked');
        $stats->blocked = $this->db->count_all_results($this->table);
        
        // Disapproved users
        $this->db->where('status', 'disapproved');
        $stats->disapproved = $this->db->count_all_results($this->table);
        
        return $stats;
    }

    /**
     * Admin creates a user account
     */
    public function admin_create_user($personnel_data, $account_data, $admin_id)
    {
        $this->db->trans_start();
        
        // First create personnel record
        $this->load->model('PersonnelModel', 'personnelModel');
        $this->personnelModel->create_personnel($personnel_data);
        $personnel_id = $this->db->insert_id();
        
        if (!$personnel_id) {
            $this->db->trans_rollback();
            return false;
        }
        
        // Create user account
        $account_data['personnel_id'] = $personnel_id;
        $account_data['status'] = 'approved';
        $account_data['approved_at'] = date('Y-m-d H:i:s');
        $account_data['approved_by'] = $admin_id;
        $account_data['password'] = password_hash($account_data['password'], PASSWORD_BCRYPT);
        
        $this->db->insert($this->table, $account_data);
        $user_id = $this->db->insert_id();
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            return false;
        }
        
        return $user_id;
    }
}
