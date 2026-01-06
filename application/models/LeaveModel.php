<?php
defined('BASEPATH') or exit('No direct script access allowed');

class LeaveModel extends CI_Model
{
    protected $table = 'leave_applications';
    protected $credits_table = 'leave_credits';
    protected $logs_table = 'leave_application_logs';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Generate unique application number
     */
    public function generate_application_number()
    {
        $year = date('Y');
        $prefix = 'LA-' . $year . '-';
        
        $this->db->select_max('id');
        $result = $this->db->get($this->table)->row();
        $next_id = ($result->id ?? 0) + 1;
        
        return $prefix . str_pad($next_id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Create new leave application
     */
    public function create($data)
    {
        if (!isset($data['application_number'])) {
            $data['application_number'] = $this->generate_application_number();
        }
        
        $this->db->insert($this->table, $data);
        $insert_id = $this->db->insert_id();
        
        if ($insert_id) {
            $this->log_action($insert_id, 'created', $data['personnel_id'], 'Leave application created');
        }
        
        return $insert_id;
    }

    /**
     * Update leave application
     */
    public function update($id, $data, $user_id = null, $remarks = null)
    {
        $old_data = $this->get($id);
        
        $this->db->where('id', $id);
        $result = $this->db->update($this->table, $data);
        
        if ($result && $user_id) {
            $old_status = $old_data ? $old_data->status : null;
            $new_status = isset($data['status']) ? $data['status'] : $old_status;
            $this->log_action($id, 'updated', $user_id, $remarks, $old_status, $new_status);
        }
        
        return $result;
    }

    /**
     * Get single leave application
     */
    public function get($id)
    {
        $this->db->select('leave_applications.*, 
            personnels.lastname, personnels.firstname, personnels.middlename,
            personnels.position, personnels.email, personnels.salary_grade as personnel_sg,
            personnels.department_id');
        $this->db->from($this->table);
        $this->db->join('personnels', 'personnels.id = leave_applications.personnel_id', 'left');
        $this->db->where('leave_applications.id', $id);
        return $this->db->get()->row();
    }

    /**
     * Get leave application with full details
     */
    public function get_with_details($id)
    {
        $this->db->select('leave_applications.*, 
            personnels.lastname, personnels.firstname, personnels.middlename,
            personnels.position, personnels.email, personnels.salary_grade as personnel_sg,
            personnels.department_id, personnels.bio_id,
            departments.name as department_name,
            certifier.first_name as certifier_fname, certifier.last_name as certifier_lname,
            recommender.first_name as recommender_fname, recommender.last_name as recommender_lname,
            approver.first_name as approver_fname, approver.last_name as approver_lname');
        $this->db->from($this->table);
        $this->db->join('personnels', 'personnels.id = leave_applications.personnel_id', 'left');
        $this->db->join('departments', 'departments.id = personnels.department_id', 'left');
        $this->db->join('users as certifier', 'certifier.id = leave_applications.certified_by_id', 'left');
        $this->db->join('users as recommender', 'recommender.id = leave_applications.recommended_by_id', 'left');
        $this->db->join('users as approver', 'approver.id = leave_applications.approved_by_id', 'left');
        $this->db->where('leave_applications.id', $id);
        return $this->db->get()->row();
    }

    /**
     * Get all leave applications with optional filters
     */
    public function get_all($filters = array())
    {
        $this->db->select('leave_applications.*, 
            personnels.lastname, personnels.firstname, personnels.middlename,
            personnels.position, personnels.email,
            departments.name as department_name');
        $this->db->from($this->table);
        $this->db->join('personnels', 'personnels.id = leave_applications.personnel_id', 'left');
        $this->db->join('departments', 'departments.id = personnels.department_id', 'left');

        if (!empty($filters['personnel_id'])) {
            $this->db->where('leave_applications.personnel_id', $filters['personnel_id']);
        }
        if (!empty($filters['status'])) {
            if (is_array($filters['status'])) {
                $this->db->where_in('leave_applications.status', $filters['status']);
            } else {
                $this->db->where('leave_applications.status', $filters['status']);
            }
        }
        if (!empty($filters['leave_type'])) {
            $this->db->where('leave_applications.leave_type', $filters['leave_type']);
        }
        if (!empty($filters['office_department'])) {
            $this->db->where('leave_applications.office_department', $filters['office_department']);
        }
        if (!empty($filters['date_from'])) {
            $this->db->where('leave_applications.date_of_filing >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $this->db->where('leave_applications.date_of_filing <=', $filters['date_to']);
        }
        if (!empty($filters['year'])) {
            $this->db->where('YEAR(leave_applications.date_of_filing)', $filters['year']);
        }

        $this->db->order_by('leave_applications.created_at', 'DESC');
        
        return $this->db->get()->result();
    }

    /**
     * Get leave applications for DataTables
     */
    public function get_datatables($filters = array())
    {
        $this->_get_datatables_query($filters);
        if (isset($_POST['length']) && $_POST['length'] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        return $this->db->get()->result();
    }

    private function _get_datatables_query($filters = array())
    {
        $this->db->select('leave_applications.*, 
            personnels.lastname, personnels.firstname, personnels.middlename,
            personnels.position,
            departments.name as department_name');
        $this->db->from($this->table);
        $this->db->join('personnels', 'personnels.id = leave_applications.personnel_id', 'left');
        $this->db->join('departments', 'departments.id = personnels.department_id', 'left');

        if (!empty($filters['personnel_id'])) {
            $this->db->where('leave_applications.personnel_id', $filters['personnel_id']);
        }
        if (!empty($filters['status'])) {
            if (is_array($filters['status'])) {
                $this->db->where_in('leave_applications.status', $filters['status']);
            } else {
                $this->db->where('leave_applications.status', $filters['status']);
            }
        }

        if (!empty($_POST['search']['value'])) {
            $search = $_POST['search']['value'];
            $this->db->group_start();
            $this->db->like('personnels.lastname', $search);
            $this->db->or_like('personnels.firstname', $search);
            $this->db->or_like('leave_applications.application_number', $search);
            $this->db->group_end();
        }

        $this->db->order_by('leave_applications.created_at', 'DESC');
    }

    public function count_filtered($filters = array())
    {
        $this->_get_datatables_query($filters);
        return $this->db->count_all_results();
    }

    public function count_all($filters = array())
    {
        $this->db->from($this->table);
        if (!empty($filters['personnel_id'])) {
            $this->db->where('personnel_id', $filters['personnel_id']);
        }
        if (!empty($filters['status'])) {
            if (is_array($filters['status'])) {
                $this->db->where_in('status', $filters['status']);
            } else {
                $this->db->where('status', $filters['status']);
            }
        }
        return $this->db->count_all_results();
    }

    /**
     * Submit leave application (change status from draft to pending)
     */
    public function submit($id, $user_id)
    {
        $data = array(
            'status' => 'pending',
            'applicant_signature_date' => date('Y-m-d H:i:s')
        );
        return $this->update($id, $data, $user_id, 'Leave application submitted');
    }

    /**
     * Certify leave credits (HR action)
     */
    public function certify($id, $certification_data, $user_id)
    {
        $data = array(
            'status' => 'certified',
            'certification_as_of' => $certification_data['as_of'],
            'vacation_leave_total_earned' => $certification_data['vl_earned'],
            'vacation_leave_less_application' => $certification_data['vl_less'],
            'vacation_leave_balance' => $certification_data['vl_balance'],
            'sick_leave_total_earned' => $certification_data['sl_earned'],
            'sick_leave_less_application' => $certification_data['sl_less'],
            'sick_leave_balance' => $certification_data['sl_balance'],
            'certified_by_id' => $user_id,
            'certified_date' => date('Y-m-d H:i:s')
        );
        return $this->update($id, $data, $user_id, 'Leave credits certified');
    }

    /**
     * Recommend leave application
     */
    public function recommend($id, $recommendation, $reason, $user_id)
    {
        $data = array(
            'status' => 'recommended',
            'recommendation' => $recommendation,
            'recommendation_disapproval_reason' => $recommendation == 'for_disapproval' ? $reason : null,
            'recommended_by_id' => $user_id,
            'recommended_date' => date('Y-m-d H:i:s')
        );
        return $this->update($id, $data, $user_id, 'Leave application ' . ($recommendation == 'for_approval' ? 'recommended for approval' : 'recommended for disapproval'));
    }

    /**
     * Approve leave application
     */
    public function approve($id, $approval_data, $user_id)
    {
        $data = array(
            'status' => 'approved',
            'approved_days_with_pay' => $approval_data['days_with_pay'] ?? null,
            'approved_days_without_pay' => $approval_data['days_without_pay'] ?? null,
            'approved_others' => $approval_data['others'] ?? null,
            'approved_by_id' => $user_id,
            'approved_date' => date('Y-m-d H:i:s')
        );
        
        $result = $this->update($id, $data, $user_id, 'Leave application approved');
        
        if ($result) {
            // Deduct from leave credits
            $leave = $this->get($id);
            if ($leave) {
                $this->deduct_leave_credits($leave->personnel_id, $leave->leave_type, $leave->working_days_applied);
            }
        }
        
        return $result;
    }

    /**
     * Disapprove leave application
     */
    public function disapprove($id, $reason, $user_id)
    {
        $data = array(
            'status' => 'disapproved',
            'disapproval_reason' => $reason,
            'approved_by_id' => $user_id,
            'approved_date' => date('Y-m-d H:i:s')
        );
        return $this->update($id, $data, $user_id, 'Leave application disapproved: ' . $reason);
    }

    /**
     * Cancel leave application
     */
    public function cancel($id, $user_id, $reason = null)
    {
        $data = array('status' => 'cancelled');
        return $this->update($id, $data, $user_id, 'Leave application cancelled' . ($reason ? ': ' . $reason : ''));
    }

    /**
     * Log action for audit trail
     */
    public function log_action($leave_id, $action, $user_id, $remarks = null, $old_status = null, $new_status = null)
    {
        $data = array(
            'leave_application_id' => $leave_id,
            'action' => $action,
            'action_by_id' => $user_id,
            'action_date' => date('Y-m-d H:i:s'),
            'remarks' => $remarks,
            'old_status' => $old_status,
            'new_status' => $new_status
        );
        return $this->db->insert($this->logs_table, $data);
    }

    /**
     * Get action logs for a leave application
     */
    public function get_logs($leave_id)
    {
        $this->db->select('leave_application_logs.*, users.first_name, users.last_name');
        $this->db->from($this->logs_table);
        $this->db->join('users', 'users.id = leave_application_logs.action_by_id', 'left');
        $this->db->where('leave_application_id', $leave_id);
        $this->db->order_by('action_date', 'ASC');
        return $this->db->get()->result();
    }

    // ==================== Leave Credits Management ====================

    /**
     * Get leave credits for a personnel
     */
    public function get_leave_credits($personnel_id, $year = null)
    {
        if (!$year) {
            $year = date('Y');
        }
        
        $this->db->where('personnel_id', $personnel_id);
        $this->db->where('year', $year);
        return $this->db->get($this->credits_table)->result();
    }

    /**
     * Get specific leave credit
     */
    public function get_leave_credit($personnel_id, $leave_type, $year = null)
    {
        if (!$year) {
            $year = date('Y');
        }
        
        $this->db->where('personnel_id', $personnel_id);
        $this->db->where('leave_type', $leave_type);
        $this->db->where('year', $year);
        return $this->db->get($this->credits_table)->row();
    }

    /**
     * Leave credit configuration
     * - Recurring leaves: VL and SL earn +1.25/month, cumulative (not reset yearly)
     * - Special Privilege: 3 days, reset yearly
     * - Mandatory/Forced Leave: Uses VL balance (max 5 days)
     * - Others: Non-recurring, based on specific entitlements
     */
    public static function get_leave_credit_config()
    {
        return array(
            'vacation' => array(
                'monthly_accrual' => 1.25,
                'yearly_reset' => false,
                'max_days' => null,
                'description' => 'Vacation Leave (+1.25/month, cumulative)'
            ),
            'sick' => array(
                'monthly_accrual' => 1.25,
                'yearly_reset' => false,
                'max_days' => null,
                'description' => 'Sick Leave (+1.25/month, cumulative)'
            ),
            'special_privilege' => array(
                'monthly_accrual' => 0,
                'yearly_reset' => true,
                'max_days' => 3,
                'description' => 'Special Privilege Leave (3 days/year, reset yearly)'
            ),
            'mandatory_forced' => array(
                'monthly_accrual' => 0,
                'yearly_reset' => true,
                'max_days' => 5,
                'uses_vacation_balance' => true,
                'description' => 'Mandatory/Forced Leave (max 5 days, uses VL balance)'
            ),
            'solo_parent' => array(
                'monthly_accrual' => 0,
                'yearly_reset' => true,
                'max_days' => 7,
                'description' => 'Solo Parent Leave (7 days/year)'
            ),
            'vawc' => array(
                'monthly_accrual' => 0,
                'yearly_reset' => true,
                'max_days' => 10,
                'description' => 'VAWC Leave (10 days/year)'
            ),
            'maternity' => array(
                'monthly_accrual' => 0,
                'yearly_reset' => false,
                'max_days' => 105,
                'description' => 'Maternity Leave (105 days)'
            ),
            'paternity' => array(
                'monthly_accrual' => 0,
                'yearly_reset' => true,
                'max_days' => 7,
                'description' => 'Paternity Leave (7 days)'
            ),
            'study' => array(
                'monthly_accrual' => 0,
                'yearly_reset' => false,
                'max_days' => 180,
                'description' => 'Study Leave (up to 6 months)'
            ),
            'rehabilitation' => array(
                'monthly_accrual' => 0,
                'yearly_reset' => false,
                'max_days' => 180,
                'description' => 'Rehabilitation Privilege (up to 6 months)'
            ),
            'special_women' => array(
                'monthly_accrual' => 0,
                'yearly_reset' => false,
                'max_days' => 60,
                'description' => 'Special Leave Benefits for Women (up to 2 months)'
            ),
            'calamity' => array(
                'monthly_accrual' => 0,
                'yearly_reset' => true,
                'max_days' => 5,
                'description' => 'Special Emergency (Calamity) Leave (up to 5 days/year)'
            ),
            'adoption' => array(
                'monthly_accrual' => 0,
                'yearly_reset' => false,
                'max_days' => 60,
                'description' => 'Adoption Leave'
            )
        );
    }

    /**
     * Get leave type instructions for the application form
     */
    public static function get_leave_type_instructions()
    {
        return array(
            'vacation_leave' => 'File 5 days in advance when possible. Indicate location (within PH or abroad) for travel authority and clearance purposes.',
            'mandatory_forced_leave' => 'Annual 5-day vacation leave forfeited if not taken. Uses Vacation Leave balance. Cancelled leave due to exigency of service will not be deducted.',
            'sick_leave' => 'File immediately upon return. If filed in advance or exceeding 5 days, attach medical certificate. If no medical consultation, execute an affidavit.',
            'maternity_leave' => '105 days. Attach proof of pregnancy (ultrasound/doctor\'s certificate). Complete CS Form No. 6a if allocating credits.',
            'paternity_leave' => '7 days. Attach birth certificate, medical certificate, and marriage contract.',
            'special_privilege_leave' => '3 days/year. File at least 1 week prior except emergencies. Indicate location for travel authority purposes.',
            'solo_parent_leave' => '7 days/year. File 5 days in advance when possible. Attach updated Solo Parent ID Card.',
            'study_leave' => 'Up to 6 months. Meet agency requirements. Requires contract between agency head and employee.',
            'vawc_leave' => '10 days. File in advance or immediately upon return. Attach BPO/TPO/PPO, certification, or police report with medical certificate.',
            'rehabilitation_privilege' => 'Up to 6 months. Apply within 1 week of accident. Attach police report, medical certificate, and government physician concurrence.',
            'special_leave_benefits_women' => 'Up to 2 months. File 5 days prior to surgery or immediately upon return. Attach medical certificate with clinical summary and operative details.',
            'special_emergency_calamity' => 'Up to 5 days (straight or staggered within 30 days of calamity). Once per year. Head of office verifies residence in declared calamity area.',
            'adoption_leave' => 'Attach authenticated Pre-Adoptive Placement Authority from DSWD.',
            'others' => 'Specify the type of leave and attach required documentation.'
        );
    }

    /**
     * Initialize leave credits for a personnel
     * VL and SL: Start with current month's accrual (1.25 * months worked)
     * SPL: 3 days (reset yearly)
     * Others: Initialize only when applicable
     */
    public function initialize_leave_credits($personnel_id, $year = null, $start_month = null)
    {
        if (!$year) {
            $year = date('Y');
        }
        if (!$start_month) {
            $start_month = 1; // Default to January
        }
        
        $current_month = date('n');
        $months_worked = ($year == date('Y')) ? $current_month : 12;
        
        // Recurring leave types to initialize
        $leave_types = array(
            'vacation' => array(
                'earned' => $months_worked * 1.25, // 1.25 per month
                'cumulative' => true
            ),
            'sick' => array(
                'earned' => $months_worked * 1.25, // 1.25 per month
                'cumulative' => true
            ),
            'special_privilege' => array(
                'earned' => 3, // 3 days per year, reset yearly
                'cumulative' => false
            )
        );
        
        foreach ($leave_types as $type => $config) {
            $existing = $this->get_leave_credit($personnel_id, $type, $year);
            if (!$existing) {
                // For cumulative types, check previous year's balance
                $carried_over = 0;
                if ($config['cumulative']) {
                    $prev_year_credit = $this->get_leave_credit($personnel_id, $type, $year - 1);
                    if ($prev_year_credit) {
                        $carried_over = $prev_year_credit->balance;
                    }
                }
                
                $total_earned = $config['earned'] + $carried_over;
                
                $this->db->insert($this->credits_table, array(
                    'personnel_id' => $personnel_id,
                    'leave_type' => $type,
                    'year' => $year,
                    'earned' => $total_earned,
                    'used' => 0,
                    'balance' => $total_earned,
                    'carried_over' => $carried_over
                ));
            }
        }
    }

    /**
     * Add monthly accrual for VL and SL (run monthly via cron or manual)
     * VL: +1.25/month
     * SL: +1.25/month
     */
    public function add_monthly_accrual($personnel_id = null, $year = null, $month = null)
    {
        if (!$year) $year = date('Y');
        if (!$month) $month = date('n');
        
        $accrual_types = array('vacation', 'sick');
        $monthly_rate = 1.25;
        
        // Get personnel list
        if ($personnel_id) {
            $personnel_ids = array($personnel_id);
        } else {
            $this->db->select('id');
            $this->db->from('personnels');
            $this->db->where('status', 1);
            $personnel = $this->db->get()->result();
            $personnel_ids = array_map(function($p) { return $p->id; }, $personnel);
        }
        
        $updated = 0;
        foreach ($personnel_ids as $pid) {
            foreach ($accrual_types as $type) {
                $credit = $this->get_leave_credit($pid, $type, $year);
                if ($credit) {
                    $new_earned = $credit->earned + $monthly_rate;
                    $new_balance = $new_earned - $credit->used;
                    
                    $this->db->where('id', $credit->id);
                    $this->db->update($this->credits_table, array(
                        'earned' => $new_earned,
                        'balance' => $new_balance
                    ));
                    $updated++;
                } else {
                    // Initialize if not exists
                    $this->initialize_leave_credits($pid, $year);
                }
            }
        }
        
        return $updated;
    }

    /**
     * Reset yearly leave credits (run at start of year)
     * SPL: Reset to 3 days
     * VL/SL: Carry over balance to new year
     */
    public function reset_yearly_credits($year = null)
    {
        if (!$year) $year = date('Y');
        $prev_year = $year - 1;
        
        // Get all personnel
        $this->db->select('id');
        $this->db->from('personnels');
        $this->db->where('status', 1);
        $personnel = $this->db->get()->result();
        
        foreach ($personnel as $person) {
            // Carry over VL and SL
            foreach (array('vacation', 'sick') as $type) {
                $prev_credit = $this->get_leave_credit($person->id, $type, $prev_year);
                $existing = $this->get_leave_credit($person->id, $type, $year);
                
                if (!$existing) {
                    $carried_over = $prev_credit ? $prev_credit->balance : 0;
                    $this->db->insert($this->credits_table, array(
                        'personnel_id' => $person->id,
                        'leave_type' => $type,
                        'year' => $year,
                        'earned' => $carried_over,
                        'used' => 0,
                        'balance' => $carried_over,
                        'carried_over' => $carried_over
                    ));
                }
            }
            
            // Reset SPL to 3 days
            $existing_spl = $this->get_leave_credit($person->id, 'special_privilege', $year);
            if (!$existing_spl) {
                $this->db->insert($this->credits_table, array(
                    'personnel_id' => $person->id,
                    'leave_type' => 'special_privilege',
                    'year' => $year,
                    'earned' => 3,
                    'used' => 0,
                    'balance' => 3,
                    'carried_over' => 0
                ));
            }
        }
    }

    /**
     * Deduct from leave credits
     * Note: Mandatory/Forced Leave uses Vacation Leave balance
     */
    public function deduct_leave_credits($personnel_id, $leave_type, $days)
    {
        // Map leave application types to credit types
        // Mandatory/Forced Leave uses Vacation Leave balance
        $credit_type_map = array(
            'vacation_leave' => 'vacation',
            'mandatory_forced_leave' => 'vacation', // Uses VL balance
            'sick_leave' => 'sick',
            'special_privilege_leave' => 'special_privilege',
            'solo_parent_leave' => 'solo_parent',
            'vawc_leave' => 'vawc'
        );
        
        $credit_type = isset($credit_type_map[$leave_type]) ? $credit_type_map[$leave_type] : null;
        
        if (!$credit_type) {
            return false; // Leave type doesn't have credits to deduct
        }
        
        $year = date('Y');
        $credit = $this->get_leave_credit($personnel_id, $credit_type, $year);
        
        if ($credit) {
            $new_used = $credit->used + $days;
            $new_balance = $credit->earned - $new_used;
            
            $this->db->where('id', $credit->id);
            return $this->db->update($this->credits_table, array(
                'used' => $new_used,
                'balance' => $new_balance
            ));
        }
        
        return false;
    }

    /**
     * Check if personnel has sufficient leave credits
     */
    public function has_sufficient_credits($personnel_id, $leave_type, $days)
    {
        $credit_type_map = array(
            'vacation_leave' => 'vacation',
            'mandatory_forced_leave' => 'vacation',
            'sick_leave' => 'sick',
            'special_privilege_leave' => 'special_privilege',
            'solo_parent_leave' => 'solo_parent',
            'vawc_leave' => 'vawc'
        );
        
        $credit_type = isset($credit_type_map[$leave_type]) ? $credit_type_map[$leave_type] : null;
        
        if (!$credit_type) {
            return true; // Leave types without credits don't need balance check
        }
        
        // Special check for mandatory/forced leave (max 5 days)
        if ($leave_type == 'mandatory_forced_leave' && $days > 5) {
            return false;
        }
        
        $year = date('Y');
        $credit = $this->get_leave_credit($personnel_id, $credit_type, $year);
        
        if (!$credit) {
            return false;
        }
        
        return $credit->balance >= $days;
    }

    /**
     * Get available balance for a leave type
     */
    public function get_available_balance($personnel_id, $leave_type)
    {
        $credit_type_map = array(
            'vacation_leave' => 'vacation',
            'mandatory_forced_leave' => 'vacation',
            'sick_leave' => 'sick',
            'special_privilege_leave' => 'special_privilege',
            'solo_parent_leave' => 'solo_parent',
            'vawc_leave' => 'vawc'
        );
        
        $credit_type = isset($credit_type_map[$leave_type]) ? $credit_type_map[$leave_type] : null;
        
        if (!$credit_type) {
            return null; // Leave types without credits
        }
        
        $year = date('Y');
        $credit = $this->get_leave_credit($personnel_id, $credit_type, $year);
        
        if (!$credit) {
            return 0;
        }
        
        // For mandatory/forced leave, cap at 5 days or VL balance, whichever is lower
        if ($leave_type == 'mandatory_forced_leave') {
            return min(5, $credit->balance);
        }
        
        return $credit->balance;
    }

    /**
     * Add leave credits
     */
    public function add_leave_credits($personnel_id, $leave_type, $days, $year = null)
    {
        if (!$year) {
            $year = date('Y');
        }
        
        $credit = $this->get_leave_credit($personnel_id, $leave_type, $year);
        
        if ($credit) {
            $new_earned = $credit->earned + $days;
            $new_balance = $new_earned - $credit->used;
            
            $this->db->where('id', $credit->id);
            return $this->db->update($this->credits_table, array(
                'earned' => $new_earned,
                'balance' => $new_balance
            ));
        } else {
            return $this->db->insert($this->credits_table, array(
                'personnel_id' => $personnel_id,
                'leave_type' => $leave_type,
                'year' => $year,
                'earned' => $days,
                'used' => 0,
                'balance' => $days
            ));
        }
    }

    /**
     * Get statistics for dashboard
     */
    public function get_statistics($filters = array())
    {
        $stats = new stdClass();
        
        // Total applications
        $this->db->from($this->table);
        if (!empty($filters['year'])) {
            $this->db->where('YEAR(date_of_filing)', $filters['year']);
        }
        $stats->total = $this->db->count_all_results();
        
        // By status
        $statuses = array('pending', 'certified', 'recommended', 'approved', 'disapproved', 'cancelled');
        foreach ($statuses as $status) {
            $this->db->from($this->table);
            $this->db->where('status', $status);
            if (!empty($filters['year'])) {
                $this->db->where('YEAR(date_of_filing)', $filters['year']);
            }
            $stats->$status = $this->db->count_all_results();
        }
        
        return $stats;
    }

    /**
     * Get leave type label
     */
    public static function get_leave_type_label($type)
    {
        $labels = array(
            'vacation_leave' => 'Vacation Leave',
            'mandatory_forced_leave' => 'Mandatory/Forced Leave',
            'sick_leave' => 'Sick Leave',
            'maternity_leave' => 'Maternity Leave',
            'paternity_leave' => 'Paternity Leave',
            'special_privilege_leave' => 'Special Privilege Leave',
            'solo_parent_leave' => 'Solo Parent Leave',
            'study_leave' => 'Study Leave',
            'vawc_leave' => '10-Day VAWC Leave',
            'rehabilitation_privilege' => 'Rehabilitation Privilege',
            'special_leave_benefits_women' => 'Special Leave Benefits for Women',
            'special_emergency_calamity' => 'Special Emergency (Calamity) Leave',
            'adoption_leave' => 'Adoption Leave',
            'others' => 'Others'
        );
        
        return isset($labels[$type]) ? $labels[$type] : $type;
    }

    /**
     * Get status badge class
     */
    public static function get_status_badge($status)
    {
        $badges = array(
            'draft' => 'badge-secondary',
            'pending' => 'badge-warning',
            'certified' => 'badge-info',
            'recommended' => 'badge-primary',
            'approved' => 'badge-success',
            'disapproved' => 'badge-danger',
            'cancelled' => 'badge-dark'
        );
        
        return isset($badges[$status]) ? $badges[$status] : 'badge-secondary';
    }

    /**
     * Delete leave application
     */
    public function delete($id)
    {
        // First delete logs
        $this->db->where('leave_application_id', $id);
        $this->db->delete($this->logs_table);
        
        // Then delete application
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }
}
