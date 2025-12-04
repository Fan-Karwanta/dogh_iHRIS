<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * AttendanceCompliance Controller
 * 
 * Handles the Schedule Compliance Report feature
 * Shows top employees with complete schedules and failure analysis
 */
class AttendanceCompliance extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('AttendanceComplianceModel', 'complianceModel');
        $this->load->model('PersonnelModel', 'personnelModel');
        $this->load->library('base');
    }

    /**
     * Main page - Schedule Compliance Report
     */
    public function index()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        $data['title'] = 'Schedule Compliance Report';
        
        // Get date range from query parameters or default to current month
        $data['start_date'] = $this->input->get('start_date') ?: date('Y-m-01');
        $data['end_date'] = $this->input->get('end_date') ?: date('Y-m-t');
        $data['department_id'] = $this->input->get('department_id') ?: '';
        
        // Get departments for filter dropdown
        $data['departments'] = $this->complianceModel->get_departments();
        
        // Get overall statistics
        $data['overall_stats'] = $this->complianceModel->get_overall_statistics(
            $data['start_date'], 
            $data['end_date'],
            $data['department_id'] ?: null
        );
        
        // Get department summary for chart
        $data['department_summary'] = $this->complianceModel->get_department_summary(
            $data['start_date'], 
            $data['end_date']
        );
        
        // Get top performers (100% compliance)
        $data['top_performers'] = $this->complianceModel->get_top_performers(
            $data['start_date'], 
            $data['end_date'],
            $data['department_id'] ?: null,
            10
        );
        
        // Get all compliance data for the main table
        $data['compliance_data'] = $this->complianceModel->get_compliance_statistics(
            $data['start_date'], 
            $data['end_date'],
            $data['department_id'] ?: null
        );

        $this->load->view('templates/default', array('content' => $this->load->view('reports/schedule_compliance', $data, true)));
    }

    /**
     * AJAX endpoint to get compliance data
     */
    public function get_compliance_data()
    {
        if (!$this->ion_auth->logged_in()) {
            echo json_encode(array('error' => 'Unauthorized'));
            return;
        }

        $start_date = $this->input->post('start_date') ?: date('Y-m-01');
        $end_date = $this->input->post('end_date') ?: date('Y-m-t');
        $department_id = $this->input->post('department_id') ?: null;

        $data = $this->complianceModel->get_compliance_statistics($start_date, $end_date, $department_id);
        
        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => true,
            'data' => $data
        ));
    }

    /**
     * AJAX endpoint to get employee failure details
     */
    public function get_employee_details()
    {
        if (!$this->ion_auth->logged_in()) {
            echo json_encode(array('error' => 'Unauthorized'));
            return;
        }

        $bio_id = $this->input->post('bio_id');
        $start_date = $this->input->post('start_date') ?: date('Y-m-01');
        $end_date = $this->input->post('end_date') ?: date('Y-m-t');

        if (!$bio_id) {
            echo json_encode(array('error' => 'Bio ID required'));
            return;
        }

        $data = $this->complianceModel->get_employee_failure_details($bio_id, $start_date, $end_date);
        
        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => true,
            'data' => $data
        ));
    }

    /**
     * AJAX endpoint to get department summary
     */
    public function get_department_summary()
    {
        if (!$this->ion_auth->logged_in()) {
            echo json_encode(array('error' => 'Unauthorized'));
            return;
        }

        $start_date = $this->input->post('start_date') ?: date('Y-m-01');
        $end_date = $this->input->post('end_date') ?: date('Y-m-t');

        $data = $this->complianceModel->get_department_summary($start_date, $end_date);
        
        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => true,
            'data' => $data
        ));
    }

    /**
     * AJAX endpoint to get overall statistics
     */
    public function get_overall_stats()
    {
        if (!$this->ion_auth->logged_in()) {
            echo json_encode(array('error' => 'Unauthorized'));
            return;
        }

        $start_date = $this->input->post('start_date') ?: date('Y-m-01');
        $end_date = $this->input->post('end_date') ?: date('Y-m-t');
        $department_id = $this->input->post('department_id') ?: null;

        $data = $this->complianceModel->get_overall_statistics($start_date, $end_date, $department_id);
        
        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => true,
            'data' => $data
        ));
    }

    /**
     * Export compliance report to CSV
     */
    public function export_csv()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        $start_date = $this->input->get('start_date') ?: date('Y-m-01');
        $end_date = $this->input->get('end_date') ?: date('Y-m-t');
        $department_id = $this->input->get('department_id') ?: null;

        $data = $this->complianceModel->get_compliance_statistics($start_date, $end_date, $department_id);

        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="schedule_compliance_' . $start_date . '_to_' . $end_date . '.csv"');

        $output = fopen('php://output', 'w');

        // CSV Header
        fputcsv($output, array(
            'Rank',
            'Employee Name',
            'Department',
            'Position',
            'Working Days (Actual)',
            'Calendar Working Days',
            'Complete Days',
            'Incomplete Days',
            'Absent Days',
            'Compliance Rate (%)',
            'Attendance Rate (%)',
            'Missing AM IN',
            'Missing AM OUT',
            'Missing PM IN',
            'Missing PM OUT',
            'Total Missing Entries'
        ));

        // CSV Data
        $rank = 1;
        foreach ($data as $emp) {
            fputcsv($output, array(
                $rank++,
                $emp['name'],
                $emp['department_name'],
                $emp['position'],
                $emp['working_days'],
                isset($emp['calendar_working_days']) ? $emp['calendar_working_days'] : $emp['working_days'],
                $emp['complete_days'],
                $emp['incomplete_days'],
                $emp['absent_days'],
                $emp['compliance_rate'],
                $emp['attendance_rate'],
                $emp['missing_am_in'],
                $emp['missing_am_out'],
                $emp['missing_pm_in'],
                $emp['missing_pm_out'],
                $emp['total_missing_entries']
            ));
        }

        fclose($output);
        exit;
    }

    /**
     * Print-friendly view
     */
    public function print_report()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        $data['title'] = 'Schedule Compliance Report - Print View';
        
        $data['start_date'] = $this->input->get('start_date') ?: date('Y-m-01');
        $data['end_date'] = $this->input->get('end_date') ?: date('Y-m-t');
        $data['department_id'] = $this->input->get('department_id') ?: '';
        
        // Get department name if filtered
        $data['department_name'] = 'All Departments';
        if ($data['department_id']) {
            $departments = $this->complianceModel->get_departments();
            foreach ($departments as $dept) {
                if ($dept->id == $data['department_id']) {
                    $data['department_name'] = $dept->name;
                    break;
                }
            }
        }
        
        $data['overall_stats'] = $this->complianceModel->get_overall_statistics(
            $data['start_date'], 
            $data['end_date'],
            $data['department_id'] ?: null
        );
        
        $data['compliance_data'] = $this->complianceModel->get_compliance_statistics(
            $data['start_date'], 
            $data['end_date'],
            $data['department_id'] ?: null
        );

        $this->load->view('reports/schedule_compliance_print', $data);
    }
}
