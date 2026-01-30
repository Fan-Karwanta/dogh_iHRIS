<?php
defined('BASEPATH') or exit('No direct script access allowed');

class HierarchyApprovalModel extends CI_Model
{
    protected $table = 'approval_hierarchy';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_all_nodes()
    {
        $this->db->select('ah.*, p.firstname, p.lastname, p.middlename, p.position, p.email, p.bio_id, p.profile_image');
        $this->db->from($this->table . ' ah');
        $this->db->join('personnels p', 'p.id = ah.personnel_id', 'left');
        $this->db->where('ah.is_active', 1);
        $this->db->order_by('ah.level', 'ASC');
        $this->db->order_by('ah.position_order', 'ASC');
        return $this->db->get()->result();
    }

    public function get_tree_structure()
    {
        $nodes = $this->get_all_nodes();
        return $this->build_tree($nodes);
    }

    private function build_tree($nodes, $parent_id = null)
    {
        $tree = [];
        foreach ($nodes as $node) {
            if ($node->parent_id == $parent_id) {
                $children = $this->build_tree($nodes, $node->id);
                $node->children = $children;
                $tree[] = $node;
            }
        }
        return $tree;
    }

    public function get_node($id)
    {
        $this->db->select('ah.*, p.firstname, p.lastname, p.middlename, p.position, p.email, p.bio_id, p.profile_image');
        $this->db->from($this->table . ' ah');
        $this->db->join('personnels p', 'p.id = ah.personnel_id', 'left');
        $this->db->where('ah.id', $id);
        return $this->db->get()->row();
    }

    public function get_node_by_personnel($personnel_id)
    {
        $this->db->where('personnel_id', $personnel_id);
        return $this->db->get($this->table)->row();
    }

    public function add_node($data)
    {
        $existing = $this->get_node_by_personnel($data['personnel_id']);
        if ($existing) {
            return false;
        }

        if (isset($data['parent_id']) && $data['parent_id']) {
            $parent = $this->get_node($data['parent_id']);
            $data['level'] = $parent ? $parent->level + 1 : 0;
        } else {
            $data['level'] = 0;
            $data['parent_id'] = null;
        }

        $max_order = $this->db->select_max('position_order')
            ->where('parent_id', $data['parent_id'])
            ->get($this->table)
            ->row();
        $data['position_order'] = ($max_order && $max_order->position_order !== null) ? $max_order->position_order + 1 : 0;

        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update_node($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        $this->db->update($this->table, $data);
        return $this->db->affected_rows();
    }

    public function move_node($node_id, $new_parent_id, $new_position = 0)
    {
        $node = $this->get_node($node_id);
        if (!$node) {
            return false;
        }

        if ($new_parent_id == $node_id) {
            return false;
        }

        if ($new_parent_id && $this->is_descendant($new_parent_id, $node_id)) {
            return false;
        }

        $new_level = 0;
        if ($new_parent_id) {
            $parent = $this->get_node($new_parent_id);
            if ($parent) {
                $new_level = $parent->level + 1;
            }
        }

        $level_diff = $new_level - $node->level;

        $this->db->where('id', $node_id);
        $this->db->update($this->table, [
            'parent_id' => $new_parent_id,
            'position_order' => $new_position,
            'level' => $new_level,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if ($level_diff != 0) {
            $this->update_descendant_levels($node_id, $level_diff);
        }

        return true;
    }

    private function is_descendant($potential_descendant_id, $ancestor_id)
    {
        $node = $this->get_node($potential_descendant_id);
        while ($node && $node->parent_id) {
            if ($node->parent_id == $ancestor_id) {
                return true;
            }
            $node = $this->get_node($node->parent_id);
        }
        return false;
    }

    private function update_descendant_levels($parent_id, $level_diff)
    {
        $children = $this->db->where('parent_id', $parent_id)->get($this->table)->result();
        foreach ($children as $child) {
            $this->db->where('id', $child->id);
            $this->db->update($this->table, [
                'level' => $child->level + $level_diff
            ]);
            $this->update_descendant_levels($child->id, $level_diff);
        }
    }

    public function delete_node($id)
    {
        $node = $this->get_node($id);
        if (!$node) {
            return false;
        }

        $this->db->where('parent_id', $id);
        $this->db->update($this->table, ['parent_id' => $node->parent_id]);

        $this->db->where('id', $id);
        $this->db->delete($this->table);
        return $this->db->affected_rows();
    }

    public function save_tree_structure($tree_data)
    {
        $this->db->trans_start();

        foreach ($tree_data as $node) {
            $this->db->where('id', $node['id']);
            $this->db->update($this->table, [
                'parent_id' => isset($node['parent_id']) ? $node['parent_id'] : null,
                'position_order' => isset($node['position_order']) ? $node['position_order'] : 0,
                'level' => isset($node['level']) ? $node['level'] : 0,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function get_approvers_for_personnel($personnel_id)
    {
        $node = $this->get_node_by_personnel($personnel_id);
        if (!$node) {
            return [];
        }

        $approvers = [];
        $current = $this->get_node($node->parent_id);
        
        while ($current) {
            $approvers[] = $current;
            $current = $current->parent_id ? $this->get_node($current->parent_id) : null;
        }

        return $approvers;
    }

    public function get_approvees_for_personnel($personnel_id)
    {
        $node = $this->get_node_by_personnel($personnel_id);
        if (!$node) {
            return [];
        }

        return $this->get_all_descendants($node->id);
    }

    private function get_all_descendants($node_id)
    {
        $descendants = [];
        $children = $this->db->select('ah.*, p.firstname, p.lastname, p.middlename, p.position, p.email, p.bio_id, p.profile_image')
            ->from($this->table . ' ah')
            ->join('personnels p', 'p.id = ah.personnel_id', 'left')
            ->where('ah.parent_id', $node_id)
            ->where('ah.is_active', 1)
            ->get()
            ->result();

        foreach ($children as $child) {
            $descendants[] = $child;
            $child_descendants = $this->get_all_descendants($child->id);
            $descendants = array_merge($descendants, $child_descendants);
        }

        return $descendants;
    }

    public function get_available_personnel()
    {
        $this->db->select('p.*');
        $this->db->from('personnels p');
        // Include personnel with status = 1 or NULL (active personnel)
        $this->db->group_start();
        $this->db->where('p.status', 1);
        $this->db->or_where('p.status IS NULL', null, false);
        $this->db->group_end();
        
        // Exclude personnel already in hierarchy
        if ($this->db->table_exists($this->table)) {
            $this->db->where("p.id NOT IN (SELECT COALESCE(personnel_id, 0) FROM {$this->table} WHERE is_active = 1)", null, false);
        }
        
        $this->db->order_by('p.lastname', 'ASC');
        $this->db->order_by('p.firstname', 'ASC');
        return $this->db->get()->result();
    }

    public function get_all_personnel()
    {
        $this->db->select('p.*');
        $this->db->from('personnels p');
        // Include personnel with status = 1 or NULL (active personnel)
        $this->db->group_start();
        $this->db->where('p.status', 1);
        $this->db->or_where('p.status IS NULL', null, false);
        $this->db->group_end();
        $this->db->order_by('p.lastname', 'ASC');
        $this->db->order_by('p.firstname', 'ASC');
        return $this->db->get()->result();
    }

    public function ensure_table_exists()
    {
        if (!$this->db->table_exists($this->table)) {
            $sql = "CREATE TABLE IF NOT EXISTS `{$this->table}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `personnel_id` int(11) NOT NULL,
                `parent_id` int(11) DEFAULT NULL,
                `position_order` int(11) DEFAULT 0,
                `level` int(11) DEFAULT 0,
                `is_active` tinyint(1) DEFAULT 1,
                `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `unique_personnel` (`personnel_id`),
                KEY `idx_parent_id` (`parent_id`),
                KEY `idx_level` (`level`),
                KEY `idx_is_active` (`is_active`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            $this->db->query($sql);
            return true;
        }
        return false;
    }
}
