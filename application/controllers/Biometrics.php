<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Biometrics extends CI_Controller
{
    public function index()
    {
        if (!$this->ion_auth->logged_in()) {
            // redirect them to the login page
            redirect('auth/login', 'refresh');
        }

        $data['title'] = 'Biometrics Attendance Management';

        $data['attendance'] = $this->attendanceModel->attendance();
        $data['person'] = $this->personnelModel->personnels();

        $this->base->load('default', 'bio/manage', $data);
    }

    public function get_bio()
    {
        $date = $_POST['date'];

        $list = $this->biometricsModel->get_datatables($date);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $bio) {
            $no++;
            $row = array();
            $row[] = date('m/d/Y', strtotime($bio->date));
            $row[] = $bio->lastname . ', ' . $bio->firstname . ' ' . $bio->middlename;
            $row[] = empty($bio->am_in) ? null : date('h:i A', strtotime($bio->am_in));
            $row[] = empty($bio->am_out) ? null : date('h:i A', strtotime($bio->am_out));
            $row[] = empty($bio->pm_in) ? null : date('h:i A', strtotime($bio->pm_in));
            $row[] = empty($bio->pm_out) ? null : date('h:i A', strtotime($bio->pm_out));
            $row[] = !empty($bio->device_code) ? $bio->device_code : 'N/A';
            $row[] = '
                 <div class="form-button-action">
                    <a type="button" href="' . $bio->fb . '" data-toggle="tooltip" class="btn btn-link btn-primary mt-1 p-1" data-original-title="Facebook URL" target="_blank">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a type="button" href="#editBio" data-toggle="modal" class="btn btn-link btn-success mt-1 p-1" title="Edit Biometrics" data-id="' . $bio->id . '" onclick="editBio(this)">
                        <i class="fa fa-edit"></i>
                    </a>
                    <a type="button" href="' . site_url("biometrics/delete/" . $bio->id) . '" data-toggle="tooltip" onclick="return confirm(&quot;Are you sure you want to delete this biometrics attendance?&quot);" class="btn btn-link btn-danger mt-1 p-1" data-original-title="Remove">
                        <i class="fa fa-times"></i>
                    </a>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->biometricsModel->count_all($date),
            "recordsFiltered" => $this->biometricsModel->count_filtered($date),
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    public function create()
    {
        $this->session->set_flashdata('success', 'danger');
        $this->form_validation->set_rules('date', 'Date', 'trim|required');
        $this->form_validation->set_rules('bio_id', 'Personnel Biometrics ID', 'trim|required');
        $this->form_validation->set_rules('am_in', 'Morning In', 'trim|required');

        if ($this->form_validation->run() == FALSE) {

            $this->session->set_flashdata('message', validation_errors());
        } else {

            $data = array(
                'date' => $this->input->post('date'),
                'am_in' => $this->input->post('am_in'),
                'am_out' => $this->input->post('am_out'),
                'pm_in' => $this->input->post('pm_in'),
                'pm_out' => $this->input->post('pm_out'),
                'bio_id' => $this->input->post('bio_id'),
            );

            $insert =  $this->biometricsModel->save($data);

            if ($insert) {
                $this->session->set_flashdata('success', 'success');
                $this->session->set_flashdata('message', 'Biometrics attendance has been created!');
            } else {
                $this->session->set_flashdata('message', 'Something went wrong. Please try again!');
            }
        }
        redirect('biometrics', 'refresh');
    }

    public function generate_bioreport()
    {
        if (!$this->ion_auth->logged_in()) {
            // redirect them to the login page
            redirect('auth/login', 'refresh');
        }

        $date = '';
        if (isset($_GET['date'])) {
            $date = $_GET['date'];
        }
        $data['bio'] = $this->biometricsModel->bio($date);

        $data['title'] = 'Biometrics Report';

        $this->base->load('default', 'bio/generate_bio', $data);
    }

    public function update()
    {
        $this->session->set_flashdata('success', 'danger');
        $this->form_validation->set_rules('date', 'Date', 'trim|required');
        $this->form_validation->set_rules('bio_id', 'Personnel Biometrics ID', 'trim|required');
        $this->form_validation->set_rules('am_in', 'Morning In', 'trim|required');

        if ($this->form_validation->run() == FALSE) {

            $this->session->set_flashdata('message', validation_errors());
        } else {

            $id = $this->input->post('id');
            $data = array(
                'date' => $this->input->post('date'),
                'am_in' => $this->input->post('am_in'),
                'am_out' => $this->input->post('am_out'),
                'pm_in' => $this->input->post('pm_in'),
                'pm_out' => $this->input->post('pm_out'),
                'bio_id' => $this->input->post('bio_id'),
            );

            $update =  $this->biometricsModel->update($data, $id);

            if ($update) {
                $this->session->set_flashdata('success', 'success');
                $this->session->set_flashdata('message', 'Biometrics attendance has been updated!');
            } else {
                $this->session->set_flashdata('message', 'No changes has been made!');
            }
        }
        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }

    public function importCSV()
    {
        $config = array(
            'upload_path' => "./assets/uploads/CSV/",
            'allowed_types' => "csv",
            'encrypt_name' => TRUE,
        );

        $date =  $this->input->post('from');

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

            if ($data) {
                // Initialize $importData_arr Array
                while (($filedata = fgetcsv($data, 1000, ",")) !== FALSE) {

                    // Skip first row (header) and empty rows - start from row 2 (index 1)
                    if ($i > 0 && !empty($filedata[0]) && !empty($filedata[2]) && trim($filedata[0]) != '' && is_numeric(trim($filedata[0]))) {
                        // New CSV structure: Employee No., Name, Attendance log, Device Code
                        $employee_no = trim($filedata[0]);
                        $attendance_log = trim($filedata[2]);
                        $device_code = isset($filedata[3]) ? trim($filedata[3]) : '';
                        
                        // Parse the attendance log datetime
                        $datetime = DateTime::createFromFormat('m/d/Y H:i', $attendance_log);
                        if ($datetime) {
                            $log_date = $datetime->format('Y-m-d');
                            $log_time = $datetime->format('H:i:s');
                            
                            // Filter by date if specified
                            if (!empty($date)) {
                                if ($date == $log_date) {
                                    $importRes[] = array(
                                        'employee_no' => $employee_no,
                                        'date' => $log_date,
                                        'time' => $log_time,
                                        'device_code' => $device_code
                                    );
                                }
                            } else {
                                $importRes[] = array(
                                    'employee_no' => $employee_no,
                                    'date' => $log_date,
                                    'time' => $log_time,
                                    'device_code' => $device_code
                                );
                            }
                        }
                    }
                    $i++;
                }

                fclose($data);

                // Remove duplicates within 3-minute intervals and group by date
                $filtered_entries = array();
                $grouped_by_date = array();
                
                // Group entries by employee and date
                foreach ($importRes as $data) {
                    $employee_no = $data['employee_no'];
                    $date = $data['date'];
                    $time = $data['time'];
                    $device_code = $data['device_code'];
                    
                    $key = $employee_no . '_' . $date;
                    if (!isset($grouped_by_date[$key])) {
                        $grouped_by_date[$key] = array();
                    }
                    
                    $grouped_by_date[$key][] = array(
                        'employee_no' => $employee_no,
                        'date' => $date,
                        'time' => $time,
                        'device_code' => $device_code,
                        'timestamp' => strtotime($date . ' ' . $time)
                    );
                }
                
                // Remove duplicates with 1-minute interval and process chronologically
                foreach ($grouped_by_date as $entries) {
                    // Sort by timestamp
                    usort($entries, function($a, $b) {
                        return $a['timestamp'] - $b['timestamp'];
                    });
                    
                    $last_timestamp = 0;
                    
                    foreach ($entries as $entry) {
                        // Skip if within 1 minute (60 seconds) of last entry
                        if ($entry['timestamp'] - $last_timestamp >= 60) {
                            $filtered_entries[] = $entry;
                            $last_timestamp = $entry['timestamp'];
                        }
                    }
                }
                
                // Insert data with smart time slot assignment
                $count = 0;
                $skipped = 0;
                $duplicates_removed = count($importRes) - count($filtered_entries);
                
                // Group filtered entries by employee and date for sequential assignment
                $daily_entries = array();
                foreach ($filtered_entries as $entry) {
                    $key = $entry['employee_no'] . '_' . $entry['date'];
                    if (!isset($daily_entries[$key])) {
                        $daily_entries[$key] = array();
                    }
                    $daily_entries[$key][] = $entry;
                }
                
                foreach ($daily_entries as $day_entries) {
                    $employee_no = $day_entries[0]['employee_no'];
                    $date = $day_entries[0]['date'];
                    $device_code = $day_entries[0]['device_code'];
                    
                    // Check if personnel exists in database
                    $personnel_exists = $this->biometricsModel->checkPersonnelExists($employee_no);
                    
                    if (!$personnel_exists) {
                        $skipped++;
                        continue; // Skip if personnel doesn't exist
                    }
                    
                    // Sort entries by time for this date
                    usort($day_entries, function($a, $b) {
                        return $a['timestamp'] - $b['timestamp'];
                    });
                    
                    $checkAttend = $this->biometricsModel->getBio($employee_no, $date);
                    
                    // Use smart time assignment based on actual time values
                    $assigned_times = $this->smartTimeAssignment($day_entries);
                    
                    if (!empty($checkAttend)) {
                        // Update existing record
                        $update_data = array('device_code' => $device_code);
                        
                        foreach ($assigned_times as $slot => $time) {
                            if (empty($checkAttend->$slot)) {
                                $update_data[$slot] = $time;
                            }
                        }
                        
                        if (count($update_data) > 1) {
                            $this->biometricsModel->update($update_data, $checkAttend->id);
                        }
                    } else {
                        // Create new record
                        $logs = array(
                            'date' => $date,
                            'bio_id' => $employee_no,
                            'device_code' => $device_code
                        );
                        
                        foreach ($assigned_times as $slot => $time) {
                            $logs[$slot] = $time;
                        }
                        
                        $this->biometricsModel->save($logs);
                    }
                    $count++;
                }
                $this->session->set_flashdata('success', 'success');
                $message = 'File Imported Successfully! ' . $count . ' records processed from ' . count($importRes) . ' total entries.';
                if ($duplicates_removed > 0) {
                    $message .= ' ' . $duplicates_removed . ' duplicate entries removed (within 1-minute intervals).';
                }
                if ($skipped > 0) {
                    $message .= ' ' . $skipped . ' entries skipped (personnel not found).';
                }
                $this->session->set_flashdata('message', $message);
            } else {
                $this->session->set_flashdata('message', 'Unable to open the file! Please contact support');
            }
        }

        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }

    private function determineTimeSlot($time)
    {
        $hour = (int)date('H', strtotime($time));
        $minute = (int)date('i', strtotime($time));
        $total_minutes = ($hour * 60) + $minute;
        
        // Morning In: Before 8:00 AM (480 minutes)
        if ($total_minutes < 480) {
            return 'am_in';
        }
        // Morning Out: Between 12:00 PM (720 minutes) to 1:00 PM (780 minutes)
        elseif ($total_minutes >= 720 && $total_minutes <= 780) {
            return 'am_out';
        }
        // Afternoon In: Between 12:00 PM (720 minutes) to 1:00 PM (780 minutes)
        elseif ($total_minutes >= 720 && $total_minutes <= 780) {
            return 'pm_in';
        }
        // Afternoon Out: 5:00 PM (1020 minutes) onwards
        elseif ($total_minutes >= 1020) {
            return 'pm_out';
        }
        // Default fallback - determine based on time ranges
        else {
            // Between 8:00 AM - 12:00 PM, likely morning out
            if ($total_minutes >= 480 && $total_minutes < 720) {
                return 'am_out';
            }
            // Between 1:00 PM - 5:00 PM, likely afternoon out
            elseif ($total_minutes > 780 && $total_minutes < 1020) {
                return 'pm_out';
            }
            // Default to am_out for edge cases
            else {
                return 'am_out';
            }
        }
    }

    private function smartTimeAssignment($entries)
    {
        $assigned_times = array(
            'am_in' => null,
            'am_out' => null, 
            'pm_in' => null,
            'pm_out' => null
        );
        
        // Sort all entries chronologically first
        usort($entries, function($a, $b) {
            return $a['timestamp'] - $b['timestamp'];
        });
        
        // Separate times into categories based on time ranges
        $morning_in_candidates = array();   // Before 8:00 AM
        $lunch_time_candidates = array();   // 12:00 PM - 1:00 PM
        $afternoon_out_candidates = array(); // 5:00 PM onwards
        $other_times = array();             // Everything else
        
        foreach ($entries as $entry) {
            $hour = (int)date('H', strtotime($entry['time']));
            $minute = (int)date('i', strtotime($entry['time']));
            $total_minutes = ($hour * 60) + $minute;
            
            if ($total_minutes < 480) {
                // Before 8:00 AM - Morning In
                $morning_in_candidates[] = $entry;
            } elseif ($total_minutes >= 720 && $total_minutes <= 780) {
                // 12:00 PM - 1:00 PM - Could be Morning Out or Afternoon In
                $lunch_time_candidates[] = $entry;
            } elseif ($total_minutes >= 1020) {
                // 5:00 PM onwards - Afternoon Out
                $afternoon_out_candidates[] = $entry;
            } else {
                $other_times[] = $entry;
            }
        }
        
        // Assign Morning In (earliest time before 8:00 AM)
        if (!empty($morning_in_candidates)) {
            $assigned_times['am_in'] = $morning_in_candidates[0]['time'];
        }
        
        // Assign Afternoon Out (latest time after 5:00 PM)
        if (!empty($afternoon_out_candidates)) {
            $assigned_times['pm_out'] = end($afternoon_out_candidates)['time'];
        }
        
        // Handle lunch time entries (12:00-13:00)
        if (!empty($lunch_time_candidates)) {
            if (count($lunch_time_candidates) >= 2) {
                // If we have 2+ lunch time entries, assign first as am_out, second as pm_in
                $assigned_times['am_out'] = $lunch_time_candidates[0]['time'];
                $assigned_times['pm_in'] = $lunch_time_candidates[1]['time'];
            } else {
                // Only one lunch time entry - determine if it's more likely am_out or pm_in
                $time_entry = $lunch_time_candidates[0];
                $hour = (int)date('H', strtotime($time_entry['time']));
                $minute = (int)date('i', strtotime($time_entry['time']));
                $total_minutes = ($hour * 60) + $minute;
                
                // If closer to 12:00, it's likely am_out; if closer to 13:00, it's pm_in
                if ($total_minutes <= 750) { // 12:30 PM or earlier
                    $assigned_times['am_out'] = $time_entry['time'];
                } else {
                    $assigned_times['pm_in'] = $time_entry['time'];
                }
            }
        }
        
        // Handle other times (8:00 AM - 12:00 PM and 1:00 PM - 5:00 PM)
        if (!empty($other_times)) {
            foreach ($other_times as $entry) {
                $hour = (int)date('H', strtotime($entry['time']));
                $minute = (int)date('i', strtotime($entry['time']));
                $total_minutes = ($hour * 60) + $minute;
                
                if ($total_minutes >= 480 && $total_minutes < 720) {
                    // 8:00 AM - 12:00 PM - likely morning out if not already assigned
                    if (empty($assigned_times['am_out'])) {
                        $assigned_times['am_out'] = $entry['time'];
                    }
                } elseif ($total_minutes > 780 && $total_minutes < 1020) {
                    // 1:00 PM - 5:00 PM - likely afternoon out if not already assigned
                    if (empty($assigned_times['pm_out'])) {
                        $assigned_times['pm_out'] = $entry['time'];
                    }
                }
            }
        }
        
        // Final validation: ensure we don't have impossible combinations
        // If we have am_out but no am_in, and there's an early time, use it for am_in
        if (!empty($assigned_times['am_out']) && empty($assigned_times['am_in'])) {
            foreach ($entries as $entry) {
                $entry_timestamp = strtotime($entry['date'] . ' ' . $entry['time']);
                $am_out_timestamp = strtotime($entry['date'] . ' ' . $assigned_times['am_out']);
                
                if ($entry_timestamp < $am_out_timestamp) {
                    $assigned_times['am_in'] = $entry['time'];
                    break;
                }
            }
        }
        
        return $assigned_times;
    }

    public function getBio()
    {
        $validator = array('data' => array());

        $id = $this->input->post('id');

        $validator['data'] = $this->biometricsModel->getBiometric($id);

        echo json_encode($validator);
    }

    public function delete($id)
    {

        $delete = $this->biometricsModel->delete($id);
        $this->session->set_flashdata('success', 'danger');

        if ($delete) {
            $this->session->set_flashdata('message', 'Biometrics attendance has been deleted!');
        } else {
            $this->session->set_flashdata('message', 'Something went wrong. This attendance cannot be deleted!');
        }
        redirect('biometrics', 'refresh');
    }
}
