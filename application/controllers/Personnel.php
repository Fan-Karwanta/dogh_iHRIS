<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Personnel extends CI_Controller
{

    public function index()
    {
        if (!$this->ion_auth->logged_in()) {
            // redirect them to the login page
            redirect('auth/login', 'refresh');
        }

        $data['title'] = 'Personnel Management';

        $data['person'] = $this->personnelModel->personnels();
        $data['statistics'] = $this->personnelModel->get_personnel_statistics();

        $this->base->load('default', 'personnel/manage', $data);
    }

    public function personnel_profile($id)
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        $data['title'] = 'Personnel Profile & Analytics';

        // Get personnel data
        $personnel = $this->personnelModel->getpersonnel($id);
        
        if (!$personnel) {
            $this->session->set_flashdata('error', 'Personnel not found');
            redirect('personnel', 'refresh');
        }

        // Find user account linked to this personnel email
        $this->db->where('email', $personnel->email);
        $user = $this->db->get('users')->row();

        if ($user) {
            // User account exists, redirect to comprehensive user profile
            redirect('auth/user_profile/' . $user->id, 'refresh');
        } else {
            // No user account, show personnel-only view with limited analytics
            $this->load->model('UserProfileModel', 'profileModel');

            // Get selected month and year from query params
            $selected_month = $this->input->get('month') ? $this->input->get('month') : date('m');
            $selected_year = $this->input->get('year') ? $this->input->get('year') : date('Y');

            // Get analytics data based on personnel email
            $data['monthly_stats'] = $this->profileModel->getMonthlyStats($personnel->email, $selected_month, $selected_year);
            $data['attendance_trends'] = $this->profileModel->getAttendanceTrends($personnel->email, 6);
            $data['recent_attendance'] = $this->profileModel->getRecentAttendance($personnel->email, 10);
            $data['performance'] = $this->profileModel->getPerformanceSummary($personnel->email, $selected_month, $selected_year);
            $data['audit_trail'] = $this->profileModel->getUserAuditTrail($personnel->email, 15);
            
            $data['personnel'] = $personnel;
            $data['selected_month'] = $selected_month;
            $data['selected_year'] = $selected_year;
            $data['has_user_account'] = false;

            $this->base->load('default', 'personnel/personnel_profile', $data);
        }
    }

    public function personnel_attendance($id)
    {
        if (!$this->ion_auth->logged_in()) {
            // redirect them to the login page
            redirect('auth/login', 'refresh');
        }
        $date = '';
        if (isset($_GET['date'])) {
            $date = $_GET['date'];
        }

        $data['person'] = $this->personnelModel->getpersonnel($id);

        $data['title'] = 'Personnel Attendance Management';

        $data['attendance'] = $this->attendanceModel->getmyAttendance($id, $date);
        $data['id'] = $id;

        $this->base->load('default', 'personnel/personnel_attendance', $data);
    }

    public function create()
    {
        $this->session->set_flashdata('success', 'danger');
        $this->form_validation->set_rules('fname', 'First Name', 'trim|required');
        $this->form_validation->set_rules('mname', 'Middle Name', 'trim|required');
        $this->form_validation->set_rules('lname', 'Last Name', 'trim|required');
        $this->form_validation->set_rules('email', 'Email Address', 'trim|required|valid_email|is_unique[personnels.email]');
        $this->form_validation->set_rules('position', 'Personnel Position', 'trim|required');
        $this->form_validation->set_rules('bio', 'Personnel Biometrics ID', 'trim|required|is_unique[personnels.bio_id]');
        $this->form_validation->set_rules('employment_type', 'Employment Type', 'trim|required');
        $this->form_validation->set_rules('salary_grade', 'Salary Grade', 'trim|numeric');
        $this->form_validation->set_rules('schedule_type', 'Schedule Type', 'trim|required');

        if ($this->form_validation->run() == FALSE) {

            $this->session->set_flashdata('message', validation_errors());
        } else {

            $data = array(
                'firstname' => $this->input->post('fname'),
                'lastname' => $this->input->post('lname'),
                'middlename' => $this->input->post('mname'),
                'position' => $this->input->post('position'),
                'email' => $this->input->post('email'),
                'fb' => $this->input->post('fb_url'),
                'bio_id' => $this->input->post('bio'),
                'employment_type' => $this->input->post('employment_type'),
                'salary_grade' => $this->input->post('salary_grade') ? intval($this->input->post('salary_grade')) : null,
                'schedule_type' => $this->input->post('schedule_type'),
            );

            $insert =  $this->personnelModel->create_personnel($data);

            if ($insert) {
                $this->session->set_flashdata('success', 'success');
                $this->session->set_flashdata('message', 'Personnel has been created!');
            } else {
                $this->session->set_flashdata('message', 'Something went wrong please try again');
            }
        }
        redirect('personnel', 'refresh');
    }

    public function update()
    {
        $this->session->set_flashdata('success', 'danger');
        $this->form_validation->set_rules('fname', 'First Name', 'trim|required');
        $this->form_validation->set_rules('mname', 'Middle Name', 'trim|required');
        $this->form_validation->set_rules('lname', 'Last Name', 'trim|required');
        $this->form_validation->set_rules('email', 'Email Address', 'trim|required|valid_email');
        $this->form_validation->set_rules('position', 'Personnel Position', 'trim|required');
        $this->form_validation->set_rules('bio', 'Personnel Biometrics ID', 'trim|required');
        $this->form_validation->set_rules('employment_type', 'Employment Type', 'trim|required');
        $this->form_validation->set_rules('salary_grade', 'Salary Grade', 'trim|numeric');
        $this->form_validation->set_rules('schedule_type', 'Schedule Type', 'trim|required');

        if ($this->form_validation->run() == FALSE) {

            $this->session->set_flashdata('message', validation_errors());
        } else {
            $id = $this->input->post('id');
            $data = array(
                'firstname' => $this->input->post('fname'),
                'lastname' => $this->input->post('lname'),
                'middlename' => $this->input->post('mname'),
                'position' => $this->input->post('position'),
                'email' => $this->input->post('email'),
                'fb' => $this->input->post('fb_url'),
                'status' => $this->input->post('status'),
                'bio_id' => $this->input->post('bio'),
                'employment_type' => $this->input->post('employment_type'),
                'salary_grade' => $this->input->post('salary_grade') ? intval($this->input->post('salary_grade')) : null,
                'schedule_type' => $this->input->post('schedule_type'),
            );

            $insert =  $this->personnelModel->update($data, $id);

            if ($insert) {
                $this->session->set_flashdata('success', 'success');
                $this->session->set_flashdata('message', 'Personnel has been updated!');
            } else {
                $this->session->set_flashdata('message', 'No changes has been made!');
            }
        }
        redirect('personnel', 'refresh');
    }

    public function importCSV()
    {
        $config = array(
            'upload_path' => "./assets/uploads/CSV/",
            'allowed_types' => "csv",
            'encrypt_name' => TRUE,
        );

        $this->load->library('upload', $config);
        $this->form_validation->set_rules('import_file', 'CSV File', 'required');
        $this->session->set_flashdata('success', 'danger');

        if (!$this->upload->do_upload('import_file')) {
            $this->session->set_flashdata('message',  $this->upload->display_errors());
        } else {
            $file = $this->upload->data();

            // Reading file
            $data = fopen("./assets/uploads/CSV/" . $file['file_name'], "r");
            $i = 0;

            $importRes = array();
            $duplicates = array();
            $errors = array();
            
            // Track IDs within the CSV to detect duplicates in the file itself
            $csv_emails = array();
            $csv_bio_ids = array();

            if ($data) {
                // Initialize $importData_arr Array
                while (($filedata = fgetcsv($data, 1000, ",")) !== FALSE) {
                    // Skip first row & check number of fields
                    if ($i > 0) {
                        // New CSV structure: Timestamp,Biometrics ID,Employee ID,Last Name,First Name,Middle Name,Type of Employment,Position,Salary Grade,Email Address,Type of Schedule
                        
                        if (count($filedata) < 11) {
                            $errors[] = "Row $i: Insufficient columns. Expected 11, got " . count($filedata);
                            $i++;
                            continue;
                        }

                        $email = trim($filedata[9]); // Email Address is now at index 9
                        $bio_id = trim($filedata[1]); // Biometrics ID
                        
                        $is_duplicate = false;
                        
                        // Check for duplicate email in database
                        $checkEmail = $this->personnelModel->checkPersonnel($email);
                        if ($checkEmail == 1) {
                            $duplicates[] = "Row $i: Duplicate email in database - $email (skipped)";
                            $is_duplicate = true;
                        }
                        
                        // Check for duplicate bio_id in database
                        if (!$is_duplicate) {
                            $checkBioId = $this->personnelModel->get_personnel_by_bio_id($bio_id);
                            if ($checkBioId) {
                                $duplicates[] = "Row $i: Duplicate biometrics ID in database - $bio_id (skipped)";
                                $is_duplicate = true;
                            }
                        }
                        
                        // Check for duplicate email within the CSV file
                        if (!$is_duplicate && in_array(strtolower($email), $csv_emails)) {
                            $duplicates[] = "Row $i: Duplicate email in CSV file - $email (skipped)";
                            $is_duplicate = true;
                        }
                        
                        // Check for duplicate bio_id within the CSV file
                        if (!$is_duplicate && in_array($bio_id, $csv_bio_ids)) {
                            $duplicates[] = "Row $i: Duplicate biometrics ID in CSV file - $bio_id (skipped)";
                            $is_duplicate = true;
                        }
                        
                        // If not a duplicate, add to import list and track the IDs
                        if (!$is_duplicate) {
                            // Track this record's IDs to detect duplicates within CSV
                            $csv_emails[] = strtolower($email);
                            $csv_bio_ids[] = $bio_id;
                            
                            // Parse timestamp
                            $timestamp = !empty($filedata[0]) ? date('Y-m-d H:i:s', strtotime($filedata[0])) : null;
                            
                            // Parse employment type
                            $employment_type = trim($filedata[6]);
                            if (!in_array($employment_type, ['Regular', 'Contract of Service', 'COS / JO'])) {
                                $employment_type = 'Regular'; // Default value
                            }
                            
                            // Parse salary grade
                            $salary_grade = is_numeric($filedata[8]) ? intval($filedata[8]) : null;
                            
                            $importRes[$i] = array(
                                'timestamp' => $timestamp,
                                'bio_id' => $bio_id,
                                'lastname' => trim($filedata[3]),
                                'firstname' => trim($filedata[4]),
                                'middlename' => trim($filedata[5]),
                                'employment_type' => $employment_type,
                                'position' => trim($filedata[7]),
                                'salary_grade' => $salary_grade,
                                'email' => $email,
                                'schedule_type' => trim($filedata[10])
                            );
                        }
                    }
                    $i++;
                }

                fclose($data);

                // Insert data using batch insert for better performance
                if (!empty($importRes)) {
                    $personnel_data = array();
                    
                    foreach ($importRes as $data) {
                        $personnel_data[] = array(
                            'timestamp' => $data['timestamp'],
                            'bio_id' => $data['bio_id'],
                            'firstname' => $data['firstname'],
                            'lastname' => $data['lastname'],
                            'middlename' => $data['middlename'],
                            'employment_type' => $data['employment_type'],
                            'position' => $data['position'],
                            'salary_grade' => $data['salary_grade'],
                            'email' => $data['email'],
                            'schedule_type' => $data['schedule_type'],
                            'status' => 1 // Active by default
                        );
                    }
                    
                    $count = $this->personnelModel->create_personnel_batch($personnel_data);
                    
                    // Build success message with duplicate info if any
                    $message = "Successfully imported $count personnel records!";
                    if (!empty($duplicates)) {
                        $message .= "\n\nSkipped " . count($duplicates) . " duplicate(s):\n" . implode("\n", $duplicates);
                    }
                    if (!empty($errors)) {
                        $message .= "\n\nErrors:\n" . implode("\n", $errors);
                    }
                    
                    $this->session->set_flashdata('success', 'success');
                    $this->session->set_flashdata('message', $message);
                } else {
                    // No valid records - show why
                    $message = 'No valid records found to import.';
                    if (!empty($duplicates)) {
                        $message .= "\n\nAll records were duplicates:\n" . implode("\n", $duplicates);
                    }
                    if (!empty($errors)) {
                        $message .= "\n\nErrors:\n" . implode("\n", $errors);
                    }
                    $this->session->set_flashdata('message', $message);
                }
            } else {
                $this->session->set_flashdata('message', 'Unable to open the file! Please contact support');
            }
        }

        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }

    public function getPersonnel()
    {
        $validator = array('data' => array());

        $id = $this->input->post('id');

        $validator['data'] = $this->personnelModel->getpersonnel($id);

        echo json_encode($validator);
    }

    public function delete($id)
    {

        $delete = $this->personnelModel->delete($id);
        $this->session->set_flashdata('success', 'danger');

        if ($delete) {
            $this->session->set_flashdata('message', 'Personnel has been deleted!');
        } else {
            $this->session->set_flashdata('message', 'Something went wrong. This borrower cannot be deleted!');
        }
        redirect('personnel', 'refresh');
    }

    public function upload_profile_image()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        $personnel_id = $this->input->post('personnel_id');
        
        if (!$personnel_id) {
            $this->session->set_flashdata('success', 'danger');
            $this->session->set_flashdata('message', 'Invalid personnel ID');
            redirect($_SERVER['HTTP_REFERER'], 'refresh');
        }

        $config = array(
            'upload_path' => './assets/uploads/profile_images/',
            'allowed_types' => 'jpg|jpeg|png|gif',
            'max_size' => 2048,
            'max_width' => 2000,
            'max_height' => 2000,
            'encrypt_name' => TRUE,
        );

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('profile_image')) {
            $this->session->set_flashdata('success', 'danger');
            $this->session->set_flashdata('message', $this->upload->display_errors());
        } else {
            $upload_data = $this->upload->data();
            $old_image = $this->personnelModel->get_profile_image($personnel_id);
            
            if ($old_image) {
                $old_file_path = './assets/uploads/profile_images/' . $old_image;
                if (file_exists($old_file_path)) {
                    unlink($old_file_path);
                }
            }

            $update = $this->personnelModel->update_profile_image($personnel_id, $upload_data['file_name']);

            if ($update || $this->db->affected_rows() >= 0) {
                $this->session->set_flashdata('success', 'success');
                $this->session->set_flashdata('message', 'Profile image uploaded successfully!');
            } else {
                $this->session->set_flashdata('success', 'danger');
                $this->session->set_flashdata('message', 'Failed to update profile image');
            }
        }

        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }

    public function delete_profile_image($personnel_id)
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        $delete = $this->personnelModel->delete_profile_image($personnel_id);

        if ($delete) {
            $this->session->set_flashdata('success', 'success');
            $this->session->set_flashdata('message', 'Profile image deleted successfully!');
        } else {
            $this->session->set_flashdata('success', 'danger');
            $this->session->set_flashdata('message', 'Failed to delete profile image');
        }

        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }

    /**
     * Show attendance history for a specific metric
     */
    public function attendance_history($id, $metric)
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        $data['title'] = 'Attendance History';

        // Get personnel data
        $personnel = $this->personnelModel->getpersonnel($id);
        
        if (!$personnel) {
            $this->session->set_flashdata('error', 'Personnel not found');
            redirect('personnel', 'refresh');
        }

        $this->load->model('UserProfileModel', 'profileModel');

        // Get selected month and year from query params
        $selected_month = $this->input->get('month') ? $this->input->get('month') : date('m');
        $selected_year = $this->input->get('year') ? $this->input->get('year') : date('Y');

        $data['personnel'] = $personnel;
        $data['selected_month'] = $selected_month;
        $data['selected_year'] = $selected_year;
        $data['metric'] = $metric;

        // Get history based on metric type
        switch ($metric) {
            case 'present_days':
                $data['history'] = $this->profileModel->getPresentDaysHistory($personnel->email, $selected_month, $selected_year);
                $data['metric_title'] = 'Present Days';
                break;
            case 'absent_days':
                $data['history'] = $this->profileModel->getAbsentDaysHistory($personnel->email, $selected_month, $selected_year);
                $data['metric_title'] = 'Absent Days';
                break;
            case 'late_arrivals':
                $data['history'] = $this->profileModel->getLateArrivalsHistory($personnel->email, $selected_month, $selected_year);
                $data['metric_title'] = 'Late Arrivals';
                break;
            case 'early_departures':
                $data['history'] = $this->profileModel->getEarlyDeparturesHistory($personnel->email, $selected_month, $selected_year);
                $data['metric_title'] = 'Early Departures';
                break;
            case 'complete_dtr':
                $data['history'] = $this->profileModel->getCompleteDaysHistory($personnel->email, $selected_month, $selected_year);
                $data['metric_title'] = 'Complete DTR';
                break;
            default:
                $this->session->set_flashdata('error', 'Invalid metric type');
                redirect('personnel/personnel_profile/' . $id, 'refresh');
        }

        $this->base->load('default', 'personnel/attendance_history', $data);
    }

    /**
     * Show justification page for analytics metrics
     */
    public function analytics_justification($id, $metric)
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        $data['title'] = 'Analytics Justification';

        // Get personnel data
        $personnel = $this->personnelModel->getpersonnel($id);
        
        if (!$personnel) {
            $this->session->set_flashdata('error', 'Personnel not found');
            redirect('personnel', 'refresh');
        }

        $this->load->model('UserProfileModel', 'profileModel');

        // Get selected month and year from query params
        $selected_month = $this->input->get('month') ? $this->input->get('month') : date('m');
        $selected_year = $this->input->get('year') ? $this->input->get('year') : date('Y');

        $data['personnel'] = $personnel;
        $data['selected_month'] = $selected_month;
        $data['selected_year'] = $selected_year;
        $data['metric'] = $metric;

        // Get justification data based on metric type
        switch ($metric) {
            case 'total_hours':
                $data['breakdown'] = $this->profileModel->getTotalHoursBreakdown($personnel->email, $selected_month, $selected_year);
                $data['metric_title'] = 'Total Hours Worked';
                break;
            case 'mode_arrival':
                $data['breakdown'] = $this->profileModel->getModeArrivalBreakdown($personnel->email, $selected_month, $selected_year);
                $data['metric_title'] = 'Mode Arrival Time';
                break;
            case 'mode_departure':
                $data['breakdown'] = $this->profileModel->getModeDepartureBreakdown($personnel->email, $selected_month, $selected_year);
                $data['metric_title'] = 'Mode Departure Time';
                break;
            case 'avg_late_arrival':
                $data['breakdown'] = $this->profileModel->getAvgLateArrivalBreakdown($personnel->email, $selected_month, $selected_year);
                $data['metric_title'] = 'Average Time of Late Arrivals';
                break;
            case 'avg_early_departure':
                $data['breakdown'] = $this->profileModel->getAvgEarlyDepartureBreakdown($personnel->email, $selected_month, $selected_year);
                $data['metric_title'] = 'Average Time of Early Departures';
                break;
            default:
                $this->session->set_flashdata('error', 'Invalid metric type');
                redirect('personnel/personnel_profile/' . $id, 'refresh');
        }

        $this->base->load('default', 'personnel/analytics_justification', $data);
    }
}
