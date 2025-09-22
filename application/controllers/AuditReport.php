<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AuditReport extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('AuditTrailModel', 'auditModel');
        $this->load->model('PersonnelModel', 'personnelModel');
        $this->load->library('base');
    }

    /**
     * Generate comprehensive audit report
     */
    public function index()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        $data['title'] = 'Audit Reports & Analytics';
        $data['personnel_list'] = $this->personnelModel->personnels();

        // Get date range from input or default to current month
        $date_from = $this->input->get('date_from') ?: date('Y-m-01');
        $date_to = $this->input->get('date_to') ?: date('Y-m-d');

        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;

        // Get comprehensive statistics
        $data['statistics'] = $this->auditModel->get_audit_statistics($date_from, $date_to);
        
        // Get additional analytics
        $data['analytics'] = $this->get_advanced_analytics($date_from, $date_to);

        $this->load->view('templates/default', array('content' => $this->load->view('audit_trail/reports', $data, true)));
    }

    /**
     * Get advanced analytics data
     */
    private function get_advanced_analytics($date_from, $date_to)
    {
        // Most frequently edited fields
        $sql = "SELECT field_name, COUNT(*) as count 
                FROM audit_trail 
                WHERE DATE(created_at) BETWEEN ? AND ? 
                AND field_name IS NOT NULL 
                GROUP BY field_name 
                ORDER BY count DESC 
                LIMIT 10";
        $field_stats = $this->db->query($sql, array($date_from, $date_to))->result();

        // Hourly edit distribution
        $sql = "SELECT HOUR(created_at) as hour, COUNT(*) as count 
                FROM audit_trail 
                WHERE DATE(created_at) BETWEEN ? AND ? 
                GROUP BY HOUR(created_at) 
                ORDER BY hour";
        $hourly_stats = $this->db->query($sql, array($date_from, $date_to))->result();

        // Personnel with most edit requests (all edits for personnel)
        $sql = "SELECT at.personnel_name, at.personnel_email, p.bio_id, COUNT(*) as request_count 
                FROM audit_trail at
                LEFT JOIN personnels p ON p.email = at.personnel_email
                WHERE DATE(at.created_at) BETWEEN ? AND ? 
                AND at.personnel_email IS NOT NULL
                GROUP BY at.personnel_email 
                ORDER BY request_count DESC 
                LIMIT 10";
        $request_stats = $this->db->query($sql, array($date_from, $date_to))->result();

        // Edit reasons analysis
        $sql = "SELECT reason, COUNT(*) as count 
                FROM audit_trail 
                WHERE DATE(created_at) BETWEEN ? AND ? 
                AND reason IS NOT NULL 
                AND reason != '' 
                GROUP BY reason 
                ORDER BY count DESC 
                LIMIT 15";
        $reason_stats = $this->db->query($sql, array($date_from, $date_to))->result();

        return array(
            'field_stats' => $field_stats,
            'hourly_stats' => $hourly_stats,
            'request_stats' => $request_stats,
            'reason_stats' => $reason_stats
        );
    }

    /**
     * Generate detailed PDF report
     */
    public function generate_pdf()
    {
        if (!$this->ion_auth->is_admin()) {
            show_error('Access denied', 403);
        }

        $this->load->library('pdf');
        
        $date_from = $this->input->get('date_from') ?: date('Y-m-01');
        $date_to = $this->input->get('date_to') ?: date('Y-m-d');

        $data['statistics'] = $this->auditModel->get_audit_statistics($date_from, $date_to);
        $data['analytics'] = $this->get_advanced_analytics($date_from, $date_to);
        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;

        $html = $this->load->view('audit_trail/pdf_report', $data, true);
        
        $this->pdf->loadHtml($html);
        $this->pdf->setPaper('A4', 'portrait');
        $this->pdf->render();
        
        $filename = 'audit_report_' . $date_from . '_to_' . $date_to . '.pdf';
        $this->pdf->stream($filename);
    }

    /**
     * Get chart data for analytics
     */
    public function get_chart_data()
    {
        $date_from = $this->input->post('date_from') ?: date('Y-m-01');
        $date_to = $this->input->post('date_to') ?: date('Y-m-d');
        $chart_type = $this->input->post('chart_type');

        $data = array();

        switch ($chart_type) {
            case 'daily_edits':
                $sql = "SELECT DATE(created_at) as date, COUNT(*) as count 
                        FROM audit_trail 
                        WHERE DATE(created_at) BETWEEN ? AND ? 
                        GROUP BY DATE(created_at) 
                        ORDER BY date";
                $result = $this->db->query($sql, array($date_from, $date_to))->result();
                
                $data = array(
                    'labels' => array_map(function($row) { return date('M j', strtotime($row->date)); }, $result),
                    'data' => array_map(function($row) { return $row->count; }, $result)
                );
                break;

            case 'action_distribution':
                $sql = "SELECT action_type, COUNT(*) as count 
                        FROM audit_trail 
                        WHERE DATE(created_at) BETWEEN ? AND ? 
                        GROUP BY action_type";
                $result = $this->db->query($sql, array($date_from, $date_to))->result();
                
                $data = array(
                    'labels' => array_map(function($row) { return $row->action_type; }, $result),
                    'data' => array_map(function($row) { return $row->count; }, $result)
                );
                break;

            case 'hourly_distribution':
                $sql = "SELECT HOUR(created_at) as hour, COUNT(*) as count 
                        FROM audit_trail 
                        WHERE DATE(created_at) BETWEEN ? AND ? 
                        GROUP BY HOUR(created_at) 
                        ORDER BY hour";
                $result = $this->db->query($sql, array($date_from, $date_to))->result();
                
                // Fill in missing hours with 0
                $hourly_data = array_fill(0, 24, 0);
                foreach ($result as $row) {
                    $hourly_data[$row->hour] = $row->count;
                }
                
                $data = array(
                    'labels' => array_map(function($hour) { return sprintf('%02d:00', $hour); }, range(0, 23)),
                    'data' => array_values($hourly_data)
                );
                break;
        }

        echo json_encode($data);
    }

    /**
     * Personnel edit frequency analysis
     */
    public function personnel_analysis()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        $data['title'] = 'Personnel Edit Frequency Analysis';
        
        // Get all personnel with their edit counts
        $sql = "SELECT 
                    p.id, p.firstname, p.lastname, p.middlename, p.email, p.position,
                    COALESCE(audit_counts.total_edits, 0) as total_edits,
                    COALESCE(audit_counts.creates, 0) as creates,
                    COALESCE(audit_counts.updates, 0) as updates,
                    COALESCE(audit_counts.deletes, 0) as deletes,
                    audit_counts.first_edit,
                    audit_counts.last_edit
                FROM personnels p
                LEFT JOIN (
                    SELECT 
                        personnel_email,
                        COUNT(*) as total_edits,
                        SUM(CASE WHEN action_type = 'CREATE' THEN 1 ELSE 0 END) as creates,
                        SUM(CASE WHEN action_type = 'UPDATE' THEN 1 ELSE 0 END) as updates,
                        SUM(CASE WHEN action_type = 'DELETE' THEN 1 ELSE 0 END) as deletes,
                        MIN(created_at) as first_edit,
                        MAX(created_at) as last_edit
                    FROM audit_trail 
                    GROUP BY personnel_email
                ) audit_counts ON p.email = audit_counts.personnel_email
                ORDER BY total_edits DESC";
        
        $data['personnel_analysis'] = $this->db->query($sql)->result();

        $this->load->view('templates/default', array('content' => $this->load->view('audit_trail/personnel_analysis', $data, true)));
    }
}
