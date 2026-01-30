<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * MainBiometrics Controller
 * 
 * Handles biometric imports from Main department's hardware.
 * This is separate from the existing Biometrics controller which handles
 * Dialysis and Admin department imports.
 * 
 * CSV Format Expected:
 * No., Staff Code, Name, Department, User ID, Week, Date, Time, Machine ID, Remark1, Remark2
 * 
 * Fields to capture:
 * - Staff Code: Last 4 digits (ignore leading zeros)
 * - Department: Department name
 * - Week: Day of week
 * - Date: MM/DD/YYYY format
 * - Time: HH:MM:SS format
 * - Remark2: IN or OUT
 */
class MainBiometrics extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MainBiometricsModel', 'mainBioModel');
        $this->load->model('BiometricsModel', 'biometricsModel');
        $this->load->library('session');
        $this->load->helper('url');
        
        // Check if user is logged in
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
    }

    /**
     * Main index page - shows import history and statistics
     */
    public function index()
    {
        $data['title'] = 'Main Department Biometrics';
        $data['import_history'] = $this->mainBioModel->getImportHistory(10);
        $data['statistics'] = $this->mainBioModel->getStatistics();
        $data['unmatched'] = $this->mainBioModel->getUnmatchedStaffCodes();
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('main_biometrics/index', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Import CSV from Main department biometrics hardware
     */
    public function importCSV()
    {
        $config = array(
            'upload_path' => "./assets/uploads/CSV/",
            'allowed_types' => "csv",
            'encrypt_name' => TRUE,
            'max_size' => 10240 // 10MB max
        );

        // Create upload directory if not exists
        if (!is_dir('./assets/uploads/CSV/')) {
            mkdir('./assets/uploads/CSV/', 0755, true);
        }

        $this->load->library('upload', $config);
        
        $sync_to_attendance = $this->input->post('sync_to_attendance') == '1';
        $override_existing = $this->input->post('override_existing') == '1';

        if (!$this->upload->do_upload('import_file')) {
            $this->session->set_flashdata('success', 'danger');
            $this->session->set_flashdata('message', $this->upload->display_errors());
            redirect($_SERVER['HTTP_REFERER'], 'refresh');
            return;
        }

        $file = $this->upload->data();
        $filepath = "./assets/uploads/CSV/" . $file['file_name'];
        
        // Generate batch ID for tracking
        $batch_id = 'MAIN_' . date('YmdHis') . '_' . substr(md5(uniqid()), 0, 6);
        
        // Initialize counters
        $total_records = 0;
        $imported_records = 0;
        $matched_personnel = 0;
        $unmatched_personnel = 0;
        $duplicate_skipped = 0;
        
        // Open and parse CSV
        $handle = fopen($filepath, "r");
        
        if (!$handle) {
            $this->session->set_flashdata('success', 'danger');
            $this->session->set_flashdata('message', 'Unable to open the uploaded file.');
            redirect($_SERVER['HTTP_REFERER'], 'refresh');
            return;
        }

        $row_number = 0;
        $batch_data = array();
        $batch_size = 100; // Insert in batches of 100

        while (($row = fgetcsv($handle, 10000, ",")) !== FALSE) {
            $row_number++;
            
            // Skip header row
            if ($row_number == 1) {
                continue;
            }
            
            // Validate row has enough columns
            if (count($row) < 11) {
                continue;
            }
            
            $total_records++;
            
            // Parse CSV columns
            // 0: No., 1: Staff Code, 2: Name, 3: Department, 4: User ID, 
            // 5: Week, 6: Date, 7: Time, 8: Machine ID, 9: Remark1, 10: Remark2
            
            $staff_code_raw = trim($row[1]);
            $department = trim($row[3]);
            $week_day = trim($row[5]);
            $date_raw = trim($row[6]);
            $time_raw = trim($row[7]);
            $remark = strtoupper(trim($row[10]));
            
            // Skip if essential fields are empty
            if (empty($staff_code_raw) || empty($date_raw) || empty($time_raw) || empty($remark)) {
                continue;
            }
            
            // Validate remark is IN or OUT
            if ($remark !== 'IN' && $remark !== 'OUT') {
                continue;
            }
            
            // Parse staff code - get last 4 digits and remove leading zeros
            // e.g., "00000001" -> 1, "00000099" -> 99
            $staff_code = (int)$staff_code_raw; // This automatically removes leading zeros
            
            // Parse date from MM/DD/YYYY to YYYY-MM-DD
            $date_parts = explode('/', $date_raw);
            if (count($date_parts) != 3) {
                continue;
            }
            $log_date = sprintf('%04d-%02d-%02d', $date_parts[2], $date_parts[0], $date_parts[1]);
            
            // Parse time - ensure HH:MM:SS format
            $time_parts = explode(':', $time_raw);
            if (count($time_parts) < 2) {
                continue;
            }
            $log_time = sprintf('%02d:%02d:%02d', 
                $time_parts[0], 
                $time_parts[1], 
                isset($time_parts[2]) ? $time_parts[2] : 0
            );
            
            // Check for duplicate
            if ($this->mainBioModel->punchExists($staff_code, $log_date, $log_time, $remark)) {
                $duplicate_skipped++;
                continue;
            }
            
            // Check if personnel exists with this bio_id
            $personnel = $this->mainBioModel->getPersonnelByBioId($staff_code);
            $personnel_id = $personnel ? $personnel->id : null;
            
            if ($personnel_id) {
                $matched_personnel++;
            } else {
                $unmatched_personnel++;
            }
            
            // Prepare data for insert
            $batch_data[] = array(
                'staff_code' => $staff_code,
                'personnel_id' => $personnel_id,
                'department' => $department,
                'week_day' => $week_day,
                'log_date' => $log_date,
                'log_time' => $log_time,
                'remark' => $remark,
                'import_batch' => $batch_id
            );
            
            // Insert in batches
            if (count($batch_data) >= $batch_size) {
                $inserted = $this->mainBioModel->saveBatch($batch_data);
                $imported_records += $inserted;
                $batch_data = array();
            }
        }
        
        // Insert remaining records
        if (!empty($batch_data)) {
            $inserted = $this->mainBioModel->saveBatch($batch_data);
            $imported_records += $inserted;
        }
        
        fclose($handle);
        
        // Save import history
        $import_history = array(
            'batch_id' => $batch_id,
            'filename' => $file['orig_name'],
            'total_records' => $total_records,
            'imported_records' => $imported_records,
            'matched_personnel' => $matched_personnel,
            'unmatched_personnel' => $unmatched_personnel,
            'duplicate_skipped' => $duplicate_skipped,
            'imported_by' => $this->ion_auth->user()->row()->id
        );
        $this->mainBioModel->saveImportHistory($import_history);
        
        // Sync to attendance if requested
        $sync_result = null;
        if ($sync_to_attendance && $imported_records > 0) {
            $sync_result = $this->mainBioModel->syncToAttendance(null, $override_existing);
        }
        
        // Build success message
        $message = "Import completed! Batch: {$batch_id}<br>";
        $message .= "Total records: {$total_records}<br>";
        $message .= "Imported: {$imported_records}<br>";
        $message .= "Matched to personnel: {$matched_personnel}<br>";
        $message .= "Unmatched (no personnel): {$unmatched_personnel}<br>";
        $message .= "Duplicates skipped: {$duplicate_skipped}";
        
        if ($sync_result) {
            $message .= "<br><br><strong>Attendance Sync:</strong><br>";
            $message .= "Synced: {$sync_result['synced']} records<br>";
            $message .= "Skipped: {$sync_result['skipped']} records";
        }
        
        $this->session->set_flashdata('success', 'success');
        $this->session->set_flashdata('message', $message);
        
        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }

    /**
     * Sync imported logs to main attendance system
     */
    public function syncToAttendance()
    {
        $date = $this->input->post('sync_date');
        $override = $this->input->post('override_existing') == '1';
        
        $result = $this->mainBioModel->syncToAttendance($date, $override);
        
        $message = "Attendance sync completed!<br>";
        $message .= "Synced: {$result['synced']} records<br>";
        $message .= "Skipped: {$result['skipped']} records";
        
        $this->session->set_flashdata('success', 'success');
        $this->session->set_flashdata('message', $message);
        
        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }

    /**
     * Re-match unlinked logs to personnel
     */
    public function rematchPersonnel()
    {
        $matched = $this->mainBioModel->rematchAllPersonnel();
        
        $this->session->set_flashdata('success', 'success');
        $this->session->set_flashdata('message', "Re-matched {$matched} staff codes to personnel records.");
        
        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }

    /**
     * View logs for a specific date range
     */
    public function viewLogs()
    {
        $start_date = $this->input->get('start_date') ?: date('Y-m-01');
        $end_date = $this->input->get('end_date') ?: date('Y-m-d');
        $staff_code = $this->input->get('staff_code');
        
        $data['title'] = 'Main Department Biometric Logs';
        $data['logs'] = $this->mainBioModel->getLogsByDateRange($start_date, $end_date, $staff_code);
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['staff_code'] = $staff_code;
        $data['departments'] = $this->mainBioModel->getUniqueDepartments();
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('main_biometrics/logs', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Get logs as JSON for AJAX requests
     */
    public function getLogsJson()
    {
        $start_date = $this->input->post('start_date') ?: date('Y-m-01');
        $end_date = $this->input->post('end_date') ?: date('Y-m-d');
        $staff_code = $this->input->post('staff_code');
        
        $logs = $this->mainBioModel->getLogsByDateRange($start_date, $end_date, $staff_code);
        
        echo json_encode(array(
            'success' => true,
            'data' => $logs,
            'count' => count($logs)
        ));
    }

    /**
     * Get statistics as JSON
     */
    public function getStatisticsJson()
    {
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        
        $stats = $this->mainBioModel->getStatistics($start_date, $end_date);
        
        echo json_encode(array(
            'success' => true,
            'data' => $stats
        ));
    }
}
