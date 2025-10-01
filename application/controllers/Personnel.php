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
                        
                        // Check for duplicate email
                        $checkEmail = $this->personnelModel->checkPersonnel($email);
                        
                        // Check for duplicate bio_id
                        $checkBioId = $this->personnelModel->get_personnel_by_bio_id($bio_id);
                        
                        if ($checkEmail == 1) {
                            $duplicates[] = "Row $i: Duplicate email address - $email";
                        } elseif ($checkBioId) {
                            $duplicates[] = "Row $i: Duplicate biometrics ID - $bio_id";
                        } else {
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

                // Report duplicates and errors
                if (!empty($duplicates) || !empty($errors)) {
                    $message = '';
                    if (!empty($errors)) {
                        $message .= "Errors found:\n" . implode("\n", $errors) . "\n\n";
                    }
                    if (!empty($duplicates)) {
                        $message .= "Duplicates found:\n" . implode("\n", $duplicates);
                    }
                    $this->session->set_flashdata('message', $message);
                    redirect($_SERVER['HTTP_REFERER'], 'refresh');
                }

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
                    
                    $this->session->set_flashdata('success', 'success');
                    $this->session->set_flashdata('message', "Successfully imported $count personnel records!");
                } else {
                    $this->session->set_flashdata('message', 'No valid records found to import.');
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
}
