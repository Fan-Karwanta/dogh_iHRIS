<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DtrEditRequestModel extends CI_Model
{
    protected $table = 'dtr_edit_requests';
    protected $items_table = 'dtr_edit_request_items';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function create_request($personnel_id, $month, $reason = null)
    {
        $data = [
            'personnel_id' => $personnel_id,
            'request_month' => $month,
            'reason' => $reason,
            'status' => 'pending'
        ];
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function add_item($request_id, $item_data)
    {
        $item_data['request_id'] = $request_id;
        $this->db->insert($this->items_table, $item_data);
        return $this->db->insert_id();
    }

    public function get_request($id)
    {
        $this->db->select('r.*, p.firstname, p.lastname, p.middlename, p.position, p.bio_id');
        $this->db->from($this->table . ' r');
        $this->db->join('personnels p', 'p.id = r.personnel_id', 'left');
        $this->db->where('r.id', $id);
        return $this->db->get()->row();
    }

    public function get_request_items($request_id)
    {
        $this->db->where('request_id', $request_id);
        $this->db->order_by('date', 'ASC');
        $this->db->order_by('field', 'ASC');
        return $this->db->get($this->items_table)->result();
    }

    public function get_requests_by_personnel($personnel_id, $status = null)
    {
        $this->db->where('personnel_id', $personnel_id);
        if ($status) {
            $this->db->where('status', $status);
        }
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get($this->table)->result();
    }

    public function get_pending_requests_for_approver($approver_personnel_id)
    {
        $this->load->model('HierarchyApprovalModel', 'hierarchyModel');
        $approvees = $this->hierarchyModel->get_approvees_for_personnel($approver_personnel_id);
        
        if (empty($approvees)) {
            return [];
        }

        $approvee_ids = array_map(function($a) { return $a->personnel_id; }, $approvees);
        
        $this->db->select('r.*, p.firstname, p.lastname, p.middlename, p.position, p.bio_id');
        $this->db->from($this->table . ' r');
        $this->db->join('personnels p', 'p.id = r.personnel_id', 'left');
        $this->db->where('r.status', 'pending');
        $this->db->where_in('r.personnel_id', $approvee_ids);
        $this->db->order_by('r.created_at', 'ASC');
        return $this->db->get()->result();
    }

    public function get_all_pending_requests()
    {
        $this->db->select('r.*, p.firstname, p.lastname, p.middlename, p.position, p.bio_id');
        $this->db->from($this->table . ' r');
        $this->db->join('personnels p', 'p.id = r.personnel_id', 'left');
        $this->db->where('r.status', 'pending');
        $this->db->order_by('r.created_at', 'ASC');
        return $this->db->get()->result();
    }

    public function approve_request($request_id, $approver_id, $is_admin = false, $remarks = null)
    {
        $data = [
            'status' => 'approved',
            'approved_at' => date('Y-m-d H:i:s'),
            'approval_remarks' => $remarks
        ];
        
        if ($is_admin) {
            $data['approved_by_admin'] = 1;
        }
        $data['approver_id'] = $approver_id;

        $this->db->where('id', $request_id);
        $this->db->update($this->table, $data);

        // Apply the changes to biometrics
        $this->apply_approved_changes($request_id);

        return $this->db->affected_rows() > 0;
    }

    public function reject_request($request_id, $approver_id, $is_admin = false, $remarks = null)
    {
        $data = [
            'status' => 'rejected',
            'approved_at' => date('Y-m-d H:i:s'),
            'approval_remarks' => $remarks
        ];
        
        if ($is_admin) {
            $data['approved_by_admin'] = 1;
        }
        $data['approver_id'] = $approver_id;

        $this->db->where('id', $request_id);
        $this->db->update($this->table, $data);

        return $this->db->affected_rows() > 0;
    }

    public function cancel_request($request_id)
    {
        $this->db->where('id', $request_id);
        $this->db->update($this->table, ['status' => 'cancelled']);
        return $this->db->affected_rows() > 0;
    }

    private function apply_approved_changes($request_id)
    {
        $request = $this->get_request($request_id);
        $items = $this->get_request_items($request_id);

        if (!$request || empty($items)) return;

        $grouped_by_date = [];
        foreach ($items as $item) {
            if (!isset($grouped_by_date[$item->date])) {
                $grouped_by_date[$item->date] = [];
            }
            $grouped_by_date[$item->date][$item->field] = $item->new_value;
        }

        foreach ($grouped_by_date as $date => $fields) {
            $update_data = [];
            $field_mapping = [
                'morning_in' => 'am_in',
                'morning_out' => 'am_out',
                'afternoon_in' => 'pm_in',
                'afternoon_out' => 'pm_out'
            ];

            foreach ($fields as $field => $value) {
                if (isset($field_mapping[$field])) {
                    $db_field = $field_mapping[$field];
                    $update_data[$db_field] = $value ?: null;
                }
            }

            if (!empty($update_data)) {
                $existing = $this->db->where('bio_id', $request->bio_id)
                    ->where('date', $date)
                    ->get('biometrics')
                    ->row();

                if ($existing) {
                    $this->db->where('bio_id', $request->bio_id);
                    $this->db->where('date', $date);
                    $this->db->update('biometrics', $update_data);
                } else {
                    $update_data['bio_id'] = $request->bio_id;
                    $update_data['date'] = $date;
                    $this->db->insert('biometrics', $update_data);
                }
            }
        }
    }

    public function has_pending_request($personnel_id, $month)
    {
        $this->db->where('personnel_id', $personnel_id);
        $this->db->where('request_month', $month);
        $this->db->where('status', 'pending');
        return $this->db->count_all_results($this->table) > 0;
    }

    public function get_items_grouped_by_date($request_id)
    {
        $items = $this->get_request_items($request_id);
        $grouped = [];
        
        foreach ($items as $item) {
            if (!isset($grouped[$item->date])) {
                $grouped[$item->date] = [
                    'date' => $item->date,
                    'fields' => [],
                    'has_repositioned' => false,
                    'has_manual' => false
                ];
            }
            $grouped[$item->date]['fields'][$item->field] = $item;
            if ($item->edit_type === 'repositioned') {
                $grouped[$item->date]['has_repositioned'] = true;
            } else {
                $grouped[$item->date]['has_manual'] = true;
            }
        }
        
        return $grouped;
    }
}
