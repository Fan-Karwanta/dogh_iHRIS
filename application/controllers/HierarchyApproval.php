<?php
defined('BASEPATH') or exit('No direct script access allowed');

class HierarchyApproval extends CI_Controller
{
    public $data = [];

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation', 'session']);
        $this->load->helper(['url']);
        $this->load->model('HierarchyApprovalModel', 'hierarchyModel');
        $this->load->model('PersonnelModel', 'personnelModel');

        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        if (!$this->ion_auth->is_admin()) {
            show_error('You must be an administrator to access this page.');
        }

        $this->hierarchyModel->ensure_table_exists();
    }

    public function index()
    {
        $this->data['title'] = 'Hierarchy Approval';
        $this->data['message'] = $this->session->flashdata('message');
        $this->data['success'] = $this->session->flashdata('success');
        $this->data['tree'] = $this->hierarchyModel->get_tree_structure();
        $this->data['available_personnel'] = $this->hierarchyModel->get_available_personnel();
        $this->data['all_personnel'] = $this->hierarchyModel->get_all_personnel();

        $this->base->load('default', 'hierarchy/index', $this->data);
    }

    public function get_tree()
    {
        header('Content-Type: application/json');
        $tree = $this->hierarchyModel->get_tree_structure();
        echo json_encode(['success' => true, 'data' => $tree]);
    }

    public function get_nodes()
    {
        header('Content-Type: application/json');
        $nodes = $this->hierarchyModel->get_all_nodes();
        echo json_encode(['success' => true, 'data' => $nodes]);
    }

    public function get_available_personnel()
    {
        header('Content-Type: application/json');
        $personnel = $this->hierarchyModel->get_available_personnel();
        echo json_encode(['success' => true, 'data' => $personnel]);
    }

    public function add_node()
    {
        header('Content-Type: application/json');

        $personnel_id = $this->input->post('personnel_id');
        $parent_id = $this->input->post('parent_id');

        if (!$personnel_id) {
            echo json_encode(['success' => false, 'message' => 'Personnel ID is required']);
            return;
        }

        $data = [
            'personnel_id' => $personnel_id,
            'parent_id' => $parent_id ? $parent_id : null
        ];

        $result = $this->hierarchyModel->add_node($data);

        if ($result) {
            $node = $this->hierarchyModel->get_node($result);
            echo json_encode(['success' => true, 'message' => 'Node added successfully', 'data' => $node]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add node. Personnel may already be in hierarchy.']);
        }
    }

    public function update_node()
    {
        header('Content-Type: application/json');

        $node_id = $this->input->post('node_id');
        $parent_id = $this->input->post('parent_id');
        $position_order = $this->input->post('position_order');

        if (!$node_id) {
            echo json_encode(['success' => false, 'message' => 'Node ID is required']);
            return;
        }

        $data = [];
        if ($parent_id !== null) {
            $data['parent_id'] = $parent_id ?: null;
        }
        if ($position_order !== null) {
            $data['position_order'] = $position_order;
        }

        $result = $this->hierarchyModel->update_node($node_id, $data);
        echo json_encode(['success' => true, 'message' => 'Node updated successfully']);
    }

    public function move_node()
    {
        header('Content-Type: application/json');

        $node_id = $this->input->post('node_id');
        $new_parent_id = $this->input->post('new_parent_id');
        $new_position = $this->input->post('new_position');

        if (!$node_id) {
            echo json_encode(['success' => false, 'message' => 'Node ID is required']);
            return;
        }

        $result = $this->hierarchyModel->move_node(
            $node_id,
            $new_parent_id ?: null,
            $new_position ?: 0
        );

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Node moved successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to move node. Invalid operation.']);
        }
    }

    public function delete_node()
    {
        header('Content-Type: application/json');

        $node_id = $this->input->post('node_id');

        if (!$node_id) {
            echo json_encode(['success' => false, 'message' => 'Node ID is required']);
            return;
        }

        $result = $this->hierarchyModel->delete_node($node_id);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Node removed successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to remove node']);
        }
    }

    public function save_tree()
    {
        header('Content-Type: application/json');

        $tree_data = $this->input->post('tree_data');

        if (!$tree_data) {
            echo json_encode(['success' => false, 'message' => 'Tree data is required']);
            return;
        }

        if (is_string($tree_data)) {
            $tree_data = json_decode($tree_data, true);
        }

        $result = $this->hierarchyModel->save_tree_structure($tree_data);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Tree structure saved successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save tree structure']);
        }
    }

    public function get_approvers($personnel_id)
    {
        header('Content-Type: application/json');
        $approvers = $this->hierarchyModel->get_approvers_for_personnel($personnel_id);
        echo json_encode(['success' => true, 'data' => $approvers]);
    }

    public function get_approvees($personnel_id)
    {
        header('Content-Type: application/json');
        $approvees = $this->hierarchyModel->get_approvees_for_personnel($personnel_id);
        echo json_encode(['success' => true, 'data' => $approvees]);
    }

    public function debug()
    {
        header('Content-Type: application/json');
        
        // Check if personnels table exists
        $personnels_exists = $this->db->table_exists('personnels');
        
        // Count all personnel
        $total_personnel = 0;
        $personnel_sample = [];
        if ($personnels_exists) {
            $total_personnel = $this->db->count_all('personnels');
            $personnel_sample = $this->db->limit(5)->get('personnels')->result();
        }
        
        // Check hierarchy table
        $hierarchy_exists = $this->db->table_exists('approval_hierarchy');
        
        // Get available personnel
        $available = $this->hierarchyModel->get_available_personnel();
        
        echo json_encode([
            'personnels_table_exists' => $personnels_exists,
            'total_personnel' => $total_personnel,
            'personnel_sample' => $personnel_sample,
            'hierarchy_table_exists' => $hierarchy_exists,
            'available_personnel_count' => count($available),
            'available_personnel' => $available
        ]);
    }
}
