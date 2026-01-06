<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
{

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		if (!$this->ion_auth->logged_in()) {
			// redirect them to the login page
			redirect('auth/login', 'refresh');
		}

		// Basic stats
		$data['person'] = $this->dashboardModel->personnel();
		$data['today_attendance'] = $this->dashboardModel->todayAttendance();
		$data['today_biometrics'] = $this->dashboardModel->todayBiometrics();
		$data['monthly_attendance'] = $this->dashboardModel->monthlyAttendance();
		$data['monthly_biometrics'] = $this->dashboardModel->monthlyBiometrics();
		$data['attendance_rate'] = $this->dashboardModel->getAttendanceRate();
		$data['daily_stats'] = $this->dashboardModel->getAttendanceByDay(7);
		$data['monthly_stats'] = $this->dashboardModel->getMonthlyStats();
		$data['top_attendees'] = $this->dashboardModel->getTopAttendees();
		$data['recent_activity'] = $this->dashboardModel->getRecentActivity();
		
		// Get chart data for edits vs missing logs
		$data['chart_data'] = $this->biometricsModel->getDashboardChartData(
			date('Y-m'),
			date('Y-m', strtotime('-1 month'))
		);

		// New comprehensive analytics data
		$data['department_distribution'] = $this->dashboardModel->getDepartmentDistribution();
		$data['compliance_stats'] = $this->dashboardModel->getComplianceStats();
		$data['late_early_trend'] = $this->dashboardModel->getLateEarlyTrend(14);
		$data['department_compliance'] = $this->dashboardModel->getDepartmentCompliance();
		$data['undertime_by_dept'] = $this->dashboardModel->getUndertimeByDepartment();
		$data['personnel_status'] = $this->dashboardModel->getPersonnelStatusDistribution();
		$data['missing_entries'] = $this->dashboardModel->getMissingEntriesBreakdown();
		$data['audit_summary'] = $this->dashboardModel->getAuditActivitySummary(30);
		$data['attendance_comparison'] = $this->dashboardModel->getAttendanceTrendComparison();
		$data['peak_hours'] = $this->dashboardModel->getPeakAttendanceHours();
		$data['top_attendees_filtered'] = $this->dashboardModel->getTopAttendeesFiltered();
		$data['perfect_attendance'] = $this->dashboardModel->getPerfectAttendance();
		$data['attendance_by_day'] = $this->dashboardModel->getAttendanceByDayOfWeek(null, null, null);

		$data['title'] = 'Dashboard';

		$this->base->load('default', 'dashboard', $data);
	}

	/**
	 * AJAX endpoint for filtered dashboard data
	 */
	public function get_filtered_data()
	{
		if (!$this->ion_auth->logged_in()) {
			header('Content-Type: application/json');
			echo json_encode(['error' => 'Unauthorized']);
			return;
		}

		try {
			$start_date = $this->input->get('start_date') ?: date('Y-m-01');
			$end_date = $this->input->get('end_date') ?: date('Y-m-t');
			$department_id = $this->input->get('department_id');

			$response = [
				'compliance_stats' => $this->dashboardModel->getComplianceStats($start_date, $end_date),
				'department_compliance' => $this->dashboardModel->getDepartmentCompliance($start_date, $end_date),
				'undertime_by_dept' => $this->dashboardModel->getUndertimeByDepartment($start_date, $end_date),
				'missing_entries' => $this->dashboardModel->getMissingEntriesBreakdown($start_date, $end_date),
				'peak_hours' => $this->dashboardModel->getPeakAttendanceHours($start_date, $end_date),
				'top_attendees' => $this->dashboardModel->getTopAttendeesFiltered($start_date, $end_date),
				'perfect_attendance' => $this->dashboardModel->getPerfectAttendance($start_date, $end_date),
				'attendance_by_day' => $this->dashboardModel->getAttendanceByDayOfWeek(null, $start_date, $end_date)
			];

			header('Content-Type: application/json');
			echo json_encode($response);
		} catch (Exception $e) {
			header('Content-Type: application/json');
			echo json_encode(['error' => $e->getMessage()]);
		}
	}

	/**
	 * AJAX endpoint for missing entries progression data
	 */
	public function get_missing_entries_progression()
	{
		if (!$this->ion_auth->logged_in()) {
			header('Content-Type: application/json');
			echo json_encode(['error' => 'Unauthorized']);
			return;
		}

		try {
			$from_month = $this->input->get('from_month') ?: date('Y-m', strtotime('-5 months'));
			$to_month = $this->input->get('to_month') ?: date('Y-m');

			$data = $this->dashboardModel->getMissingEntriesProgression($from_month, $to_month);

			header('Content-Type: application/json');
			echo json_encode(['success' => true, 'data' => $data]);
		} catch (Exception $e) {
			header('Content-Type: application/json');
			echo json_encode(['error' => $e->getMessage()]);
		}
	}
}
