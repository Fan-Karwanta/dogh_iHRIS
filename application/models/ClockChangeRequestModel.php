<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ClockChangeRequestModel extends CI_Model
{
    protected $table = 'clock_change_requests';
    protected $items_table = 'clock_change_request_items';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Generate next control number in format: FCTC-YYYY-NNNN
     */
    public function generate_control_no()
    {
        $year = date('Y');
        $prefix = 'FCTC-' . $year . '-';

        $this->db->select_max('control_no');
        $this->db->like('control_no', $prefix, 'after');
        $result = $this->db->get($this->table)->row();

        if ($result && $result->control_no) {
            $last_num = (int) substr($result->control_no, strlen($prefix));
            $next_num = $last_num + 1;
        } else {
            $next_num = 1;
        }

        return $prefix . str_pad($next_num, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new clock change request
     */
    public function create_request($personnel_id)
    {
        $control_no = $this->generate_control_no();
        $data = [
            'control_no' => $control_no,
            'personnel_id' => $personnel_id,
            'status' => 'pending'
        ];
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Add an item (row) to a request
     */
    public function add_item($request_id, $item_data)
    {
        $item_data['request_id'] = $request_id;
        $this->db->insert($this->items_table, $item_data);
        return $this->db->insert_id();
    }

    /**
     * Add multiple items at once
     */
    public function add_items($request_id, $items)
    {
        foreach ($items as &$item) {
            $item['request_id'] = $request_id;
        }
        return $this->db->insert_batch($this->items_table, $items);
    }

    /**
     * Get a single request with personnel data
     */
    public function get_request($id)
    {
        $this->db->select('r.*, p.firstname, p.lastname, p.middlename, p.position, p.bio_id, p.email');
        $this->db->from($this->table . ' r');
        $this->db->join('personnels p', 'p.id = r.personnel_id', 'left');
        $this->db->where('r.id', $id);
        return $this->db->get()->row();
    }

    /**
     * Get request items
     */
    public function get_request_items($request_id)
    {
        $this->db->where('request_id', $request_id);
        $this->db->order_by('date', 'ASC');
        return $this->db->get($this->items_table)->result();
    }

    /**
     * Get all requests for a specific personnel
     */
    public function get_requests_by_personnel($personnel_id, $status = null)
    {
        $this->db->where('personnel_id', $personnel_id);
        if ($status) {
            $this->db->where('status', $status);
        }
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get($this->table)->result();
    }

    /**
     * Get pending requests for an approver (based on hierarchy)
     */
    public function get_pending_requests_for_approver($approver_personnel_id)
    {
        $this->load->model('HierarchyApprovalModel', 'hierarchyModel');
        $approvees = $this->hierarchyModel->get_approvees_for_personnel($approver_personnel_id);

        if (empty($approvees)) {
            return [];
        }

        $approvee_ids = array_map(function ($a) {
            return $a->personnel_id;
        }, $approvees);

        $this->db->select('r.*, p.firstname, p.lastname, p.middlename, p.position, p.bio_id');
        $this->db->from($this->table . ' r');
        $this->db->join('personnels p', 'p.id = r.personnel_id', 'left');
        $this->db->where('r.status', 'pending');
        $this->db->where_in('r.personnel_id', $approvee_ids);
        $this->db->order_by('r.created_at', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Get all pending requests (for admin)
     */
    public function get_all_pending_requests()
    {
        $this->db->select('r.*, p.firstname, p.lastname, p.middlename, p.position, p.bio_id');
        $this->db->from($this->table . ' r');
        $this->db->join('personnels p', 'p.id = r.personnel_id', 'left');
        $this->db->where('r.status', 'pending');
        $this->db->order_by('r.created_at', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Get all requests (for admin) with optional status filter
     */
    public function get_all_requests($status = null)
    {
        $this->db->select('r.*, p.firstname, p.lastname, p.middlename, p.position, p.bio_id');
        $this->db->from($this->table . ' r');
        $this->db->join('personnels p', 'p.id = r.personnel_id', 'left');
        if ($status) {
            $this->db->where('r.status', $status);
        }
        $this->db->order_by('r.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Approve a request and auto-embed times into biometrics
     */
    public function approve_request($request_id, $approver_id, $is_admin = false, $remarks = null)
    {
        $data = [
            'status' => 'approved',
            'approved_at' => date('Y-m-d H:i:s'),
            'approval_remarks' => $remarks,
            'approver_id' => $approver_id
        ];

        if ($is_admin) {
            $data['approved_by_admin'] = 1;
        }

        $this->db->where('id', $request_id);
        $this->db->update($this->table, $data);

        // Auto-embed approved times into biometrics
        $this->apply_approved_changes($request_id);

        return $this->db->affected_rows() > 0;
    }

    /**
     * Apply approved clock change items to the biometrics table
     * Maps: AM+IN->am_in, AM+OUT->am_out, PM+IN->pm_in, PM+OUT->pm_out
     * time_change value is the actual time to embed
     */
    private function apply_approved_changes($request_id)
    {
        $request = $this->get_request($request_id);
        $items = $this->get_request_items($request_id);

        if (!$request || empty($items) || !$request->bio_id) return;

        $bio_id = $request->bio_id;

        // Ensure tracking table exists
        $this->ensure_embedded_table_exists();

        // Group items by date to batch updates
        $updates_by_date = [];
        foreach ($items as $item) {
            if (empty($item->time_change)) continue;

            $date = $item->date;
            if (!isset($updates_by_date[$date])) {
                $updates_by_date[$date] = [];
            }

            // Map am_pm + time_in/time_out to biometric field
            $fields = [];
            if ($item->time_in) {
                $fields[] = ($item->am_pm == 'AM') ? 'am_in' : 'pm_in';
            }
            if ($item->time_out) {
                $fields[] = ($item->am_pm == 'AM') ? 'am_out' : 'pm_out';
            }

            $parsed_time = $this->parse_time_change($item->time_change, $item->am_pm);
            if ($parsed_time) {
                foreach ($fields as $field) {
                    $updates_by_date[$date][$field] = $parsed_time;
                }
            }
        }

        // Apply updates to biometrics and track in clock_change_embedded
        foreach ($updates_by_date as $date => $field_updates) {
            if (empty($field_updates)) continue;

            // Check if biometric record exists
            $existing = $this->db->where('bio_id', $bio_id)
                ->where('date', $date)
                ->get('biometrics')
                ->row();

            if ($existing) {
                $this->db->where('bio_id', $bio_id);
                $this->db->where('date', $date);
                $this->db->update('biometrics', $field_updates);
            } else {
                $insert_data = $field_updates;
                $insert_data['bio_id'] = $bio_id;
                $insert_data['date'] = $date;
                $this->db->insert('biometrics', $insert_data);
            }

            // Track each embedded field for highlighting
            foreach ($field_updates as $field => $time_value) {
                // Use REPLACE to handle unique constraint
                $this->db->query(
                    "REPLACE INTO `clock_change_embedded` (`bio_id`, `date`, `field`, `time_value`, `request_id`, `created_at`) 
                     VALUES (?, ?, ?, ?, ?, NOW())",
                    [$bio_id, $date, $field, $time_value, $request_id]
                );
            }
        }
    }

    /**
     * Parse time_change input into a valid TIME format (HH:MM:SS)
     * Converts 12-hour time (from form) + AM/PM into 24-hour format
     * e.g., "07:51" + "AM" => "07:51:00", "05:01" + "PM" => "17:01:00"
     */
    private function parse_time_change($time_str, $am_pm = null)
    {
        $time_str = trim($time_str);
        if (empty($time_str)) return null;

        // Match HH:MM pattern (12-hour format from form)
        if (preg_match('/^(\d{1,2}):(\d{2})$/', $time_str, $m)) {
            $h = intval($m[1]);
            $min = intval($m[2]);

            // Convert 12-hour to 24-hour using the AM/PM field
            if ($am_pm) {
                $am_pm = strtoupper($am_pm);
                if ($am_pm == 'PM' && $h != 12) {
                    $h += 12;
                } elseif ($am_pm == 'AM' && $h == 12) {
                    $h = 0;
                }
            }

            return sprintf('%02d:%02d:00', $h, $min);
        }

        // Fallback: try strtotime
        $ts = strtotime($time_str);
        if ($ts !== false) {
            return date('H:i:s', $ts);
        }

        return null;
    }

    /**
     * Get clock change embedded entries for a specific bio_id and month
     * Returns array keyed by date_field (e.g., "2026-01-15_am_in")
     */
    public function get_embedded_entries($bio_id, $month)
    {
        $this->ensure_embedded_table_exists();

        $month_start = $month . '-01';
        $month_end = date('Y-m-t', strtotime($month_start));

        $this->db->where('bio_id', $bio_id);
        $this->db->where('date >=', $month_start);
        $this->db->where('date <=', $month_end);
        $results = $this->db->get('clock_change_embedded')->result();

        $lookup = [];
        foreach ($results as $row) {
            $key = $row->date . '_' . $row->field;
            $lookup[$key] = $row;
        }
        return $lookup;
    }

    /**
     * Ensure the clock_change_embedded tracking table exists
     */
    private function ensure_embedded_table_exists()
    {
        if (!$this->db->table_exists('clock_change_embedded')) {
            $sql = "CREATE TABLE IF NOT EXISTS `clock_change_embedded` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `bio_id` int(11) NOT NULL,
                `date` date NOT NULL,
                `field` enum('am_in','am_out','pm_in','pm_out') NOT NULL,
                `time_value` time DEFAULT NULL,
                `request_id` int(11) NOT NULL,
                `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `unique_bio_date_field` (`bio_id`, `date`, `field`),
                KEY `idx_bio_date` (`bio_id`, `date`),
                KEY `idx_request_id` (`request_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            $this->db->query($sql);
        }
    }

    /**
     * Reject a request
     */
    public function reject_request($request_id, $approver_id, $is_admin = false, $remarks = null)
    {
        $data = [
            'status' => 'rejected',
            'approved_at' => date('Y-m-d H:i:s'),
            'approval_remarks' => $remarks,
            'approver_id' => $approver_id
        ];

        if ($is_admin) {
            $data['approved_by_admin'] = 1;
        }

        $this->db->where('id', $request_id);
        $this->db->update($this->table, $data);

        return $this->db->affected_rows() > 0;
    }

    /**
     * Cancel a request
     */
    public function cancel_request($request_id)
    {
        $this->db->where('id', $request_id);
        $this->db->update($this->table, ['status' => 'cancelled']);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Get approver personnel info for a request
     */
    public function get_approver_info($request_id)
    {
        $request = $this->get_request($request_id);
        if (!$request || !$request->approver_id) {
            return null;
        }

        $this->load->model('PersonnelModel', 'personnelModel');
        return $this->personnelModel->getpersonnel($request->approver_id);
    }

    /**
     * Get immediate supervisor for a personnel
     */
    public function get_immediate_supervisor($personnel_id)
    {
        $this->load->model('HierarchyApprovalModel', 'hierarchyModel');
        $approvers = $this->hierarchyModel->get_approvers_for_personnel($personnel_id);
        return !empty($approvers) ? $approvers[0] : null;
    }

    /**
     * Ensure tables exist
     */
    public function ensure_tables_exist()
    {
        if (!$this->db->table_exists($this->table)) {
            $sql = "CREATE TABLE IF NOT EXISTS `{$this->table}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `control_no` varchar(20) NOT NULL,
                `personnel_id` int(11) NOT NULL,
                `status` enum('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending',
                `approver_id` int(11) DEFAULT NULL,
                `approved_at` datetime DEFAULT NULL,
                `approval_remarks` text DEFAULT NULL,
                `approved_by_admin` tinyint(1) DEFAULT 0,
                `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `unique_control_no` (`control_no`),
                KEY `idx_personnel_id` (`personnel_id`),
                KEY `idx_status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            $this->db->query($sql);
        }

        if (!$this->db->table_exists($this->items_table)) {
            $sql = "CREATE TABLE IF NOT EXISTS `{$this->items_table}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `request_id` int(11) NOT NULL,
                `date` date NOT NULL,
                `am_pm` enum('AM','PM') NOT NULL DEFAULT 'AM',
                `time_in` tinyint(1) DEFAULT 0,
                `time_out` tinyint(1) DEFAULT 0,
                `time_change` varchar(100) DEFAULT NULL,
                `reason` text DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_request_id` (`request_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            $this->db->query($sql);
        }
    }
}
