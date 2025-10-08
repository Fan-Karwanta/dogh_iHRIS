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
		// Default: Current month for edits, previous month for DTR records
		$data['chart_data'] = $this->biometricsModel->getDashboardChartData(
			date('Y-m'), // Current month for edits
			date('Y-m', strtotime('-1 month')) // Previous month for DTR records
		);

		$data['title'] = 'Dashboard';

		$this->base->load('default', 'dashboard', $data);
	}
}
