<?php
defined('BASEPATH') or exit('No direct script access allowed');

class HolidayModel extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
    }

    /**
     * Get all holidays with optional filters
     * For recurring holidays, year filter is ignored (they apply every year)
     */
    public function get_all_holidays($filters = array())
    {
        $this->db->select('h.*, GROUP_CONCAT(hd.department_id) as department_ids');
        $this->db->from('holidays h');
        $this->db->join('holiday_departments hd', 'hd.holiday_id = h.id', 'left');
        
        if (isset($filters['status'])) {
            $this->db->where('h.status', $filters['status']);
        }
        
        // For year filter: show recurring holidays always, filter non-recurring by year
        if (isset($filters['year'])) {
            $this->db->group_start();
            $this->db->where('h.recurring', 1); // Always show recurring holidays
            $this->db->or_where('YEAR(h.date)', $filters['year']); // Or non-recurring for specific year
            $this->db->group_end();
        }
        
        if (isset($filters['month'])) {
            $this->db->where('MONTH(h.date)', $filters['month']);
        }
        
        $this->db->group_by('h.id');
        $this->db->order_by('MONTH(h.date)', 'ASC');
        $this->db->order_by('DAY(h.date)', 'ASC');
        
        return $this->db->get()->result();
    }

    /**
     * Get single holiday by ID
     */
    public function get_holiday($id)
    {
        $this->db->select('h.*, GROUP_CONCAT(hd.department_id) as department_ids');
        $this->db->from('holidays h');
        $this->db->join('holiday_departments hd', 'hd.holiday_id = h.id', 'left');
        $this->db->where('h.id', $id);
        $this->db->group_by('h.id');
        
        return $this->db->get()->row();
    }

    /**
     * Get holidays for a specific date
     * Returns holidays that apply to a given employee based on their department
     */
    public function get_holidays_for_date($date, $department_id = null)
    {
        $month_day = date('m-d', strtotime($date));
        $year = date('Y', strtotime($date));
        
        $this->db->select('h.*');
        $this->db->from('holidays h');
        $this->db->where('h.status', 1);
        
        // Match either exact date or recurring holidays with same month-day
        $this->db->group_start();
        $this->db->where('h.date', $date);
        $this->db->or_group_start();
        $this->db->where('h.recurring', 1);
        $this->db->where("DATE_FORMAT(h.date, '%m-%d') =", $month_day);
        $this->db->group_end();
        $this->db->group_end();
        
        $holidays = $this->db->get()->result();
        
        // Filter by department if specified
        if ($department_id !== null) {
            $filtered = array();
            foreach ($holidays as $holiday) {
                if ($holiday->applies_to_all == 1) {
                    $filtered[] = $holiday;
                } else {
                    // Check if this holiday applies to the department
                    $this->db->where('holiday_id', $holiday->id);
                    $this->db->where('department_id', $department_id);
                    if ($this->db->count_all_results('holiday_departments') > 0) {
                        $filtered[] = $holiday;
                    }
                }
            }
            return $filtered;
        }
        
        return $holidays;
    }

    /**
     * Check if a specific date is a holiday for a given department
     */
    public function is_holiday($date, $department_id = null)
    {
        $holidays = $this->get_holidays_for_date($date, $department_id);
        return count($holidays) > 0;
    }

    /**
     * Get holiday name for a specific date
     */
    public function get_holiday_name($date, $department_id = null)
    {
        $holidays = $this->get_holidays_for_date($date, $department_id);
        if (count($holidays) > 0) {
            return $holidays[0]->name;
        }
        return null;
    }

    /**
     * Create a new holiday
     */
    public function create_holiday($data, $department_ids = array())
    {
        $this->db->insert('holidays', $data);
        $holiday_id = $this->db->insert_id();
        
        // If not applying to all departments, add specific department mappings
        if (isset($data['applies_to_all']) && $data['applies_to_all'] == 0 && !empty($department_ids)) {
            foreach ($department_ids as $dept_id) {
                $this->db->insert('holiday_departments', array(
                    'holiday_id' => $holiday_id,
                    'department_id' => $dept_id
                ));
            }
        }
        
        return $holiday_id;
    }

    /**
     * Update holiday
     */
    public function update_holiday($id, $data, $department_ids = array())
    {
        $this->db->where('id', $id);
        $this->db->update('holidays', $data);
        
        // Update department mappings
        $this->db->where('holiday_id', $id);
        $this->db->delete('holiday_departments');
        
        if (isset($data['applies_to_all']) && $data['applies_to_all'] == 0 && !empty($department_ids)) {
            foreach ($department_ids as $dept_id) {
                $this->db->insert('holiday_departments', array(
                    'holiday_id' => $id,
                    'department_id' => $dept_id
                ));
            }
        }
        
        return $this->db->affected_rows();
    }

    /**
     * Delete holiday
     */
    public function delete_holiday($id)
    {
        // Department mappings will be deleted automatically due to CASCADE
        $this->db->where('id', $id);
        $this->db->delete('holidays');
        return $this->db->affected_rows();
    }

    /**
     * Get departments for a holiday
     */
    public function get_holiday_departments($holiday_id)
    {
        $this->db->select('d.*');
        $this->db->from('departments d');
        $this->db->join('holiday_departments hd', 'hd.department_id = d.id');
        $this->db->where('hd.holiday_id', $holiday_id);
        return $this->db->get()->result();
    }

    /**
     * Get holidays for a specific month/year range
     * Used for DTR generation
     */
    public function get_holidays_for_month($year, $month, $department_id = null)
    {
        $start_date = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01';
        $end_date = date('Y-m-t', strtotime($start_date));
        
        $this->db->select('h.*');
        $this->db->from('holidays h');
        $this->db->where('h.status', 1);
        
        // Get holidays within the date range OR recurring holidays that match the month
        $this->db->group_start();
        $this->db->where('h.date >=', $start_date);
        $this->db->where('h.date <=', $end_date);
        $this->db->or_group_start();
        $this->db->where('h.recurring', 1);
        $this->db->where('MONTH(h.date)', $month);
        $this->db->group_end();
        $this->db->group_end();
        
        $holidays = $this->db->get()->result();
        
        // Build a lookup array by day
        $holiday_lookup = array();
        foreach ($holidays as $holiday) {
            $day = (int)date('j', strtotime($holiday->date));
            $month_day = date('m-d', strtotime($holiday->date));
            
            // For recurring holidays, use the month-day to determine the day in current month
            if ($holiday->recurring == 1) {
                $recurring_day = (int)date('j', strtotime($year . '-' . $month_day));
                $key = $recurring_day;
            } else {
                $key = $day;
            }
            
            // Check department filter
            $applies = true;
            if ($department_id !== null && $holiday->applies_to_all == 0) {
                $this->db->where('holiday_id', $holiday->id);
                $this->db->where('department_id', $department_id);
                $applies = $this->db->count_all_results('holiday_departments') > 0;
            }
            
            if ($applies) {
                if (!isset($holiday_lookup[$key])) {
                    $holiday_lookup[$key] = array();
                }
                $holiday_lookup[$key][] = $holiday;
            }
        }
        
        return $holiday_lookup;
    }

    /**
     * Check if a date is a holiday (simplified version for views)
     * Returns holiday info or false
     */
    public function check_holiday($date, $department_id = null)
    {
        $month_day = date('m-d', strtotime($date));
        $full_date = date('Y-m-d', strtotime($date));
        
        $this->db->select('h.*');
        $this->db->from('holidays h');
        $this->db->where('h.status', 1);
        
        $this->db->group_start();
        // Exact date match
        $this->db->where('h.date', $full_date);
        // Or recurring with same month-day
        $this->db->or_group_start();
        $this->db->where('h.recurring', 1);
        $this->db->where("DATE_FORMAT(h.date, '%m-%d') =", $month_day);
        $this->db->group_end();
        $this->db->group_end();
        
        $holidays = $this->db->get()->result();
        
        foreach ($holidays as $holiday) {
            // Check if applies to all or specific department
            if ($holiday->applies_to_all == 1) {
                return $holiday;
            }
            
            if ($department_id !== null) {
                $this->db->where('holiday_id', $holiday->id);
                $this->db->where('department_id', $department_id);
                if ($this->db->count_all_results('holiday_departments') > 0) {
                    return $holiday;
                }
            }
        }
        
        return false;
    }

    /**
     * Duplicate holidays for a new year (for recurring holidays)
     */
    public function duplicate_for_year($source_year, $target_year)
    {
        $this->db->where('YEAR(date)', $source_year);
        $this->db->where('recurring', 1);
        $holidays = $this->db->get('holidays')->result();
        
        $count = 0;
        foreach ($holidays as $holiday) {
            // Check if already exists for target year
            $new_date = $target_year . date('-m-d', strtotime($holiday->date));
            
            $this->db->where('name', $holiday->name);
            $this->db->where('date', $new_date);
            if ($this->db->count_all_results('holidays') == 0) {
                $data = array(
                    'name' => $holiday->name,
                    'date' => $new_date,
                    'holiday_type' => $holiday->holiday_type,
                    'recurring' => $holiday->recurring,
                    'description' => $holiday->description,
                    'applies_to_all' => $holiday->applies_to_all,
                    'status' => $holiday->status
                );
                $this->db->insert('holidays', $data);
                $count++;
            }
        }
        
        return $count;
    }
}
