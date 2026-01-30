<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * MainBiometricsModel
 * 
 * Handles raw biometric logs from Main department's hardware.
 * This is separate from the existing BiometricsModel which handles
 * processed attendance records for Dialysis and Admin departments.
 */
class MainBiometricsModel extends CI_Model
{
    var $table = 'main_biometrics_logs';
    var $import_table = 'main_biometrics_imports';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Save a single biometric log entry
     */
    public function save($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Save multiple biometric log entries in batch
     */
    public function saveBatch($data)
    {
        if (empty($data)) {
            return 0;
        }
        $this->db->insert_batch($this->table, $data);
        return $this->db->affected_rows();
    }

    /**
     * Check if a punch record already exists (to prevent duplicates)
     */
    public function punchExists($staff_code, $log_date, $log_time, $remark)
    {
        $this->db->where('staff_code', $staff_code);
        $this->db->where('log_date', $log_date);
        $this->db->where('log_time', $log_time);
        $this->db->where('remark', $remark);
        $query = $this->db->get($this->table);
        return $query->num_rows() > 0;
    }

    /**
     * Get personnel ID by bio_id (staff_code)
     * Handles the matching of staff_code (e.g., 99) to personnel bio_id (e.g., 99)
     */
    public function getPersonnelByBioId($bio_id)
    {
        $this->db->where('bio_id', $bio_id);
        $query = $this->db->get('personnels');
        return $query->row();
    }

    /**
     * Save import history record
     */
    public function saveImportHistory($data)
    {
        $this->db->insert($this->import_table, $data);
        return $this->db->insert_id();
    }

    /**
     * Update import history record
     */
    public function updateImportHistory($batch_id, $data)
    {
        $this->db->where('batch_id', $batch_id);
        $this->db->update($this->import_table, $data);
        return $this->db->affected_rows();
    }

    /**
     * Get import history with pagination
     */
    public function getImportHistory($limit = 20, $offset = 0)
    {
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit, $offset);
        $query = $this->db->get($this->import_table);
        return $query->result();
    }

    /**
     * Get logs by date range
     */
    public function getLogsByDateRange($start_date, $end_date, $staff_code = null)
    {
        $this->db->where('log_date >=', $start_date);
        $this->db->where('log_date <=', $end_date);
        if ($staff_code) {
            $this->db->where('staff_code', $staff_code);
        }
        $this->db->order_by('log_date', 'ASC');
        $this->db->order_by('log_time', 'ASC');
        $query = $this->db->get($this->table);
        return $query->result();
    }

    /**
     * Get logs by staff code
     */
    public function getLogsByStaffCode($staff_code, $limit = 100)
    {
        $this->db->where('staff_code', $staff_code);
        $this->db->order_by('log_date', 'DESC');
        $this->db->order_by('log_time', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get($this->table);
        return $query->result();
    }

    /**
     * Get daily summary for a staff member
     * Groups IN/OUT punches by date
     */
    public function getDailySummary($staff_code, $date)
    {
        $this->db->where('staff_code', $staff_code);
        $this->db->where('log_date', $date);
        $this->db->order_by('log_time', 'ASC');
        $query = $this->db->get($this->table);
        return $query->result();
    }

    /**
     * Get all unique departments from imported data
     */
    public function getUniqueDepartments()
    {
        $this->db->distinct();
        $this->db->select('department');
        $this->db->where('department IS NOT NULL');
        $this->db->where('department !=', '');
        $this->db->order_by('department', 'ASC');
        $query = $this->db->get($this->table);
        return $query->result();
    }

    /**
     * Get statistics for dashboard
     */
    public function getStatistics($start_date = null, $end_date = null)
    {
        if (!$start_date) {
            $start_date = date('Y-m-01');
        }
        if (!$end_date) {
            $end_date = date('Y-m-t');
        }

        // Total logs
        $this->db->where('log_date >=', $start_date);
        $this->db->where('log_date <=', $end_date);
        $total_logs = $this->db->count_all_results($this->table);

        // Unique staff
        $this->db->distinct();
        $this->db->select('staff_code');
        $this->db->where('log_date >=', $start_date);
        $this->db->where('log_date <=', $end_date);
        $unique_staff = $this->db->count_all_results($this->table);

        // Matched personnel
        $this->db->where('log_date >=', $start_date);
        $this->db->where('log_date <=', $end_date);
        $this->db->where('personnel_id IS NOT NULL');
        $matched = $this->db->count_all_results($this->table);

        // Unmatched personnel
        $this->db->where('log_date >=', $start_date);
        $this->db->where('log_date <=', $end_date);
        $this->db->where('personnel_id IS NULL');
        $unmatched = $this->db->count_all_results($this->table);

        return array(
            'total_logs' => $total_logs,
            'unique_staff' => $unique_staff,
            'matched_personnel' => $matched,
            'unmatched_personnel' => $unmatched,
            'start_date' => $start_date,
            'end_date' => $end_date
        );
    }

    /**
     * Process raw logs and create/update attendance records in biometrics table
     * This syncs Main department logs to the main attendance system
     */
    public function syncToAttendance($date = null, $override = false)
    {
        $this->load->model('BiometricsModel', 'biometricsModel');
        
        // Get all logs for the date, grouped by staff_code
        $this->db->select('staff_code, log_date, personnel_id');
        $this->db->where('personnel_id IS NOT NULL');
        if ($date) {
            $this->db->where('log_date', $date);
        }
        $this->db->group_by(array('staff_code', 'log_date'));
        $query = $this->db->get($this->table);
        $staff_dates = $query->result();

        $synced = 0;
        $skipped = 0;

        foreach ($staff_dates as $sd) {
            // Get all punches for this staff on this date
            $punches = $this->getDailySummary($sd->staff_code, $sd->log_date);
            
            if (empty($punches)) {
                continue;
            }

            // Separate IN and OUT punches
            $in_punches = array();
            $out_punches = array();
            
            foreach ($punches as $punch) {
                if ($punch->remark == 'IN') {
                    $in_punches[] = $punch->log_time;
                } else {
                    $out_punches[] = $punch->log_time;
                }
            }

            // Sort chronologically
            sort($in_punches);
            sort($out_punches);

            // Assign to time slots based on time of day
            $am_in = null;
            $am_out = null;
            $pm_in = null;
            $pm_out = null;

            // Process IN punches
            foreach ($in_punches as $time) {
                $hour = (int)date('H', strtotime($time));
                if ($hour < 12 && !$am_in) {
                    $am_in = $time;
                } elseif ($hour >= 12 && !$pm_in) {
                    $pm_in = $time;
                }
            }

            // Process OUT punches
            foreach ($out_punches as $time) {
                $hour = (int)date('H', strtotime($time));
                if ($hour <= 13 && !$am_out) {
                    $am_out = $time;
                } elseif ($hour > 13 && !$pm_out) {
                    $pm_out = $time;
                }
            }

            // Check if attendance record exists
            $existing = $this->biometricsModel->getBio($sd->staff_code, $sd->log_date);

            if ($existing) {
                if ($override) {
                    // Update existing record
                    $update_data = array();
                    if ($am_in) $update_data['am_in'] = $am_in;
                    if ($am_out) $update_data['am_out'] = $am_out;
                    if ($pm_in) $update_data['pm_in'] = $pm_in;
                    if ($pm_out) $update_data['pm_out'] = $pm_out;
                    
                    if (!empty($update_data)) {
                        $this->biometricsModel->update($update_data, $existing->id);
                        $synced++;
                    }
                } else {
                    // Fill only empty slots
                    $update_data = array();
                    if ($am_in && empty($existing->am_in)) $update_data['am_in'] = $am_in;
                    if ($am_out && empty($existing->am_out)) $update_data['am_out'] = $am_out;
                    if ($pm_in && empty($existing->pm_in)) $update_data['pm_in'] = $pm_in;
                    if ($pm_out && empty($existing->pm_out)) $update_data['pm_out'] = $pm_out;
                    
                    if (!empty($update_data)) {
                        $this->biometricsModel->update($update_data, $existing->id);
                        $synced++;
                    } else {
                        $skipped++;
                    }
                }
            } else {
                // Create new attendance record
                $new_record = array(
                    'date' => $sd->log_date,
                    'bio_id' => $sd->staff_code,
                    'am_in' => $am_in,
                    'am_out' => $am_out,
                    'pm_in' => $pm_in,
                    'pm_out' => $pm_out
                );
                $this->biometricsModel->save($new_record);
                $synced++;
            }
        }

        return array(
            'synced' => $synced,
            'skipped' => $skipped
        );
    }

    /**
     * Get unmatched staff codes (staff without personnel records)
     */
    public function getUnmatchedStaffCodes()
    {
        $this->db->distinct();
        $this->db->select('staff_code, department, COUNT(*) as punch_count');
        $this->db->where('personnel_id IS NULL');
        $this->db->group_by(array('staff_code', 'department'));
        $this->db->order_by('punch_count', 'DESC');
        $query = $this->db->get($this->table);
        return $query->result();
    }

    /**
     * Update personnel_id for all logs with matching staff_code
     */
    public function linkPersonnel($staff_code, $personnel_id)
    {
        $this->db->where('staff_code', $staff_code);
        $this->db->update($this->table, array('personnel_id' => $personnel_id));
        return $this->db->affected_rows();
    }

    /**
     * Re-match all unlinked logs to personnel
     */
    public function rematchAllPersonnel()
    {
        // Get all unique unmatched staff codes
        $this->db->distinct();
        $this->db->select('staff_code');
        $this->db->where('personnel_id IS NULL');
        $query = $this->db->get($this->table);
        $unmatched = $query->result();

        $matched_count = 0;
        foreach ($unmatched as $row) {
            $personnel = $this->getPersonnelByBioId($row->staff_code);
            if ($personnel) {
                $this->linkPersonnel($row->staff_code, $personnel->id);
                $matched_count++;
            }
        }

        return $matched_count;
    }
}
