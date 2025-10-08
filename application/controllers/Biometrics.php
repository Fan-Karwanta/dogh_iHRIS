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
            $row[] = isset($bio->undertime_hours) ? $bio->undertime_hours : '0';
            $row[] = isset($bio->undertime_minutes) ? $bio->undertime_minutes : '0';
            $row[] = '
                 <div class="form-button-action">
                
                <!-- Comment out FB button as of now
                 <a type="button" href="' . $bio->fb . '" data-toggle="tooltip" class="btn btn-link btn-primary mt-1 p-1" data-original-title="Facebook URL" target="_blank">
                        <i class="fab fa-facebook"></i>
                    </a> -->
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

            $am_in = $this->input->post('am_in');
            $am_out = $this->input->post('am_out');
            $pm_in = $this->input->post('pm_in');
            $pm_out = $this->input->post('pm_out');
            
            // Calculate undertime
            $undertime = $this->calculateUndertime($am_in, $am_out, $pm_in, $pm_out);
            
            $data = array(
                'date' => $this->input->post('date'),
                'am_in' => $am_in,
                'am_out' => $am_out,
                'pm_in' => $pm_in,
                'pm_out' => $pm_out,
                'bio_id' => $this->input->post('bio_id')
            );
            
            // Handle full-day absence (null undertime) vs partial attendance
            if ($undertime === null) {
                // Full day absence - store null values for blank entry
                $data['undertime_hours'] = null;
                $data['undertime_minutes'] = null;
            } else {
                // Partial attendance - store calculated undertime
                $data['undertime_hours'] = $undertime['hours'];
                $data['undertime_minutes'] = $undertime['minutes'];
            }

            $reason = $this->input->post('reason');
            $insert =  $this->biometricsModel->save($data, $reason);

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
            $am_in = $this->input->post('am_in');
            $am_out = $this->input->post('am_out');
            $pm_in = $this->input->post('pm_in');
            $pm_out = $this->input->post('pm_out');
            
            // Check if manual undertime values are provided
            $manual_undertime_hours = $this->input->post('undertime_hours');
            $manual_undertime_minutes = $this->input->post('undertime_minutes');
            
            $data = array(
                'date' => $this->input->post('date'),
                'am_in' => $am_in,
                'am_out' => $am_out,
                'pm_in' => $pm_in,
                'pm_out' => $pm_out,
                'bio_id' => $this->input->post('bio_id')
            );
            
            if ($manual_undertime_hours !== null && $manual_undertime_minutes !== null) {
                // Use manual undertime values
                $data['undertime_hours'] = (int)$manual_undertime_hours;
                $data['undertime_minutes'] = (int)$manual_undertime_minutes;
            } else {
                // Calculate undertime automatically
                $undertime = $this->calculateUndertime($am_in, $am_out, $pm_in, $pm_out);
                
                if ($undertime === null) {
                    // Full day absence - store null values for blank entry
                    $data['undertime_hours'] = null;
                    $data['undertime_minutes'] = null;
                } else {
                    // Partial attendance - store calculated undertime
                    $data['undertime_hours'] = $undertime['hours'];
                    $data['undertime_minutes'] = $undertime['minutes'];
                }
            }

            $reason = $this->input->post('reason');
            $update =  $this->biometricsModel->update($data, $id, $reason);

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
                    if ($i > 0 && !empty($filedata[0]) && !empty($filedata[1]) && trim($filedata[0]) != '' && is_numeric(trim($filedata[0]))) {
                        // CSV structure: bio_id, biometrics_time
                        $bio_id = trim($filedata[0]);
                        $biometrics_time = trim($filedata[1]);
                        $device_code = ''; // No device code in this CSV format
                        
                        // Parse the biometrics_time datetime
                        $datetime = DateTime::createFromFormat('m/d/Y H:i', $biometrics_time);
                        if ($datetime) {
                            $log_date = $datetime->format('Y-m-d');
                            $log_time = $datetime->format('H:i:s');
                            
                            // Filter by date if specified
                            if (!empty($date)) {
                                if ($date == $log_date) {
                                    $importRes[] = array(
                                        'bio_id' => $bio_id,
                                        'date' => $log_date,
                                        'time' => $log_time,
                                        'device_code' => $device_code
                                    );
                                }
                            } else {
                                $importRes[] = array(
                                    'bio_id' => $bio_id,
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
                    $bio_id = $data['bio_id'];
                    $date = $data['date'];
                    $time = $data['time'];
                    $device_code = $data['device_code'];
                    
                    $key = $bio_id . '_' . $date;
                    if (!isset($grouped_by_date[$key])) {
                        $grouped_by_date[$key] = array();
                    }
                    
                    $grouped_by_date[$key][] = array(
                        'bio_id' => $bio_id,
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
                    $key = $entry['bio_id'] . '_' . $entry['date'];
                    if (!isset($daily_entries[$key])) {
                        $daily_entries[$key] = array();
                    }
                    $daily_entries[$key][] = $entry;
                }
                
                foreach ($daily_entries as $day_entries) {
                    $bio_id = $day_entries[0]['bio_id'];
                    $date = $day_entries[0]['date'];
                    $device_code = $day_entries[0]['device_code'];
                    
                    // Check if personnel exists in database
                    $personnel_exists = $this->biometricsModel->checkPersonnelExists($bio_id);
                    
                    if (!$personnel_exists) {
                        $skipped++;
                        continue; // Skip if personnel doesn't exist
                    }
                    
                    // Sort entries by time for this date
                    usort($day_entries, function($a, $b) {
                        return $a['timestamp'] - $b['timestamp'];
                    });
                    
                    $checkAttend = $this->biometricsModel->getBio($bio_id, $date);
                    
                    // Use smart time assignment based on actual time values
                    $assigned_times = $this->smartTimeAssignment($day_entries);
                    
                    // Handle cases where no time entries exist for this date (full day absence)
                    // This can happen when CSV has employee records but no actual time logs for certain days
                    if (empty($assigned_times['am_in']) && empty($assigned_times['am_out']) && 
                        empty($assigned_times['pm_in']) && empty($assigned_times['pm_out'])) {
                        // Skip creating records for full day absences during CSV import
                        // This prevents creating blank entries that would show "8-0" undertime
                        continue;
                    }
                    
                    if (!empty($checkAttend)) {
                        // Update existing record
                        $update_data = array('device_code' => $device_code);
                        
                        foreach ($assigned_times as $slot => $time) {
                            if (empty($checkAttend->$slot)) {
                                $update_data[$slot] = $time;
                            }
                        }
                        
                        if (count($update_data) > 1) {
                            // Calculate undertime with updated data
                            $final_am_in = !empty($update_data['am_in']) ? $update_data['am_in'] : $checkAttend->am_in;
                            $final_am_out = !empty($update_data['am_out']) ? $update_data['am_out'] : $checkAttend->am_out;
                            $final_pm_in = !empty($update_data['pm_in']) ? $update_data['pm_in'] : $checkAttend->pm_in;
                            $final_pm_out = !empty($update_data['pm_out']) ? $update_data['pm_out'] : $checkAttend->pm_out;
                            
                            $undertime = $this->calculateUndertime($final_am_in, $final_am_out, $final_pm_in, $final_pm_out);
                            
                            if ($undertime === null) {
                                // Full day absence - store null values for blank entry
                                $update_data['undertime_hours'] = null;
                                $update_data['undertime_minutes'] = null;
                            } else {
                                // Partial attendance - store calculated undertime
                                $update_data['undertime_hours'] = $undertime['hours'];
                                $update_data['undertime_minutes'] = $undertime['minutes'];
                            }
                            
                            $this->biometricsModel->update($update_data, $checkAttend->id);
                        }
                    } else {
                        // Create new record
                        $logs = array(
                            'date' => $date,
                            'bio_id' => $bio_id,
                            'device_code' => $device_code
                        );
                        
                        foreach ($assigned_times as $slot => $time) {
                            $logs[$slot] = $time;
                        }
                        
                        // Calculate undertime for new record
                        $undertime = $this->calculateUndertime(
                            $assigned_times['am_in'] ?? null,
                            $assigned_times['am_out'] ?? null,
                            $assigned_times['pm_in'] ?? null,
                            $assigned_times['pm_out'] ?? null
                        );
                        
                        if ($undertime === null) {
                            // Full day absence - store null values for blank entry
                            $logs['undertime_hours'] = null;
                            $logs['undertime_minutes'] = null;
                        } else {
                            // Partial attendance - store calculated undertime
                            $logs['undertime_hours'] = $undertime['hours'];
                            $logs['undertime_minutes'] = $undertime['minutes'];
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
        
        $entry_count = count($entries);
        
        // SPECIAL HANDLING FOR 2 ENTRIES - HALF DAY DETECTION
        if ($entry_count == 2) {
            $first_time = $this->timeToMinutes($entries[0]['time']);
            $second_time = $this->timeToMinutes($entries[1]['time']);
            
            // Determine if it's morning half-day or afternoon half-day
            // If both times are before 2:00 PM (840 minutes), it's likely morning half-day
            if ($second_time <= 840) {
                // Morning half-day: assign as am_in and am_out
                $assigned_times['am_in'] = $entries[0]['time'];
                $assigned_times['am_out'] = $entries[1]['time'];
            } 
            // If first time is after 11:00 AM (660 minutes), it's likely afternoon half-day
            else if ($first_time >= 660) {
                // Afternoon half-day: assign as pm_in and pm_out
                $assigned_times['pm_in'] = $entries[0]['time'];
                $assigned_times['pm_out'] = $entries[1]['time'];
            }
            // If times span across lunch (first before 12:00, second after 1:00), it's full day with missing lunch
            else if ($first_time < 720 && $second_time > 780) {
                // Full day with missing lunch break: assign as am_in and pm_out
                $assigned_times['am_in'] = $entries[0]['time'];
                $assigned_times['pm_out'] = $entries[1]['time'];
            }
            // Default fallback for edge cases
            else {
                // Default to morning half-day
                $assigned_times['am_in'] = $entries[0]['time'];
                $assigned_times['am_out'] = $entries[1]['time'];
            }
            
            return $assigned_times;
        }
        
        // ORIGINAL LOGIC FOR 1, 3, OR 4+ ENTRIES
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

    /**
     * Calculate undertime based on standard work schedule
     * Standard: 8:00 AM - 5:00 PM (8 hours total)
     * Morning: 8:00 AM - 12:00 PM (4 hours)
     * Lunch Break: 12:00 PM - 1:00 PM (excluded)
     * Afternoon: 1:00 PM - 5:00 PM (4 hours)
     * 
     * ABSENCE RULES:
     * - Full day absence (no time entries): Return null for blank database entry
     * - Half day absence (missing one complete session): 4 hours undertime
     * - Full day present: Calculate actual undertime based on late/early times
     */
    private function calculateUndertime($am_in, $am_out, $pm_in, $pm_out)
    {
        // Check for full day absence - all time entries are empty
        $has_am_in = !empty($am_in);
        $has_am_out = !empty($am_out);
        $has_pm_in = !empty($pm_in);
        $has_pm_out = !empty($pm_out);
        
        // If completely absent (no time entries at all), return null to indicate blank entry
        if (!$has_am_in && !$has_am_out && !$has_pm_in && !$has_pm_out) {
            return null; // This will be handled as blank in database and DTR
        }
        
        $undertime_minutes = 0;
        
        // Standard work times in minutes from midnight
        $standard_am_in = 8 * 60;      // 8:00 AM = 480 minutes
        $standard_am_out = 12 * 60;    // 12:00 PM = 720 minutes
        $standard_pm_in = 13 * 60;     // 1:00 PM = 780 minutes
        $standard_pm_out = 17 * 60;    // 5:00 PM = 1020 minutes
        
        // Convert time strings to minutes from midnight
        $actual_am_in = $has_am_in ? $this->timeToMinutes($am_in) : null;
        $actual_am_out = $has_am_out ? $this->timeToMinutes($am_out) : null;
        $actual_pm_in = $has_pm_in ? $this->timeToMinutes($pm_in) : null;
        $actual_pm_out = $has_pm_out ? $this->timeToMinutes($pm_out) : null;
        
        // Check if we have complete sessions
        $has_complete_morning = $has_am_in && $has_am_out;
        $has_complete_afternoon = $has_pm_in && $has_pm_out;
        
        // Calculate morning session undertime
        if ($has_complete_morning) {
            // Complete morning session - calculate based on actual times
            // Late arrival (after 8:00 AM)
            if ($actual_am_in > $standard_am_in) {
                $undertime_minutes += ($actual_am_in - $standard_am_in);
            }
            
            // Early departure (before 12:00 PM)
            if ($actual_am_out < $standard_am_out) {
                $undertime_minutes += ($standard_am_out - $actual_am_out);
            }
        } else if ($has_am_in || $has_am_out) {
            // Incomplete morning session (only in or only out) = 4 hours undertime
            $undertime_minutes += 240; // 4 hours = 240 minutes
        } else {
            // No morning session at all = 4 hours undertime
            $undertime_minutes += 240; // 4 hours = 240 minutes
        }
        
        // Calculate afternoon session undertime
        if ($has_complete_afternoon) {
            // Complete afternoon session - calculate based on actual times
            // Late arrival (after 1:00 PM)
            if ($actual_pm_in > $standard_pm_in) {
                $undertime_minutes += ($actual_pm_in - $standard_pm_in);
            }
            
            // Early departure (before 5:00 PM)
            if ($actual_pm_out < $standard_pm_out) {
                $undertime_minutes += ($standard_pm_out - $actual_pm_out);
            }
        } else if ($has_pm_in || $has_pm_out) {
            // Incomplete afternoon session (only in or only out) = 4 hours undertime
            $undertime_minutes += 240; // 4 hours = 240 minutes
        } else {
            // No afternoon session at all = 4 hours undertime
            $undertime_minutes += 240; // 4 hours = 240 minutes
        }
        
        // Convert total undertime minutes to hours and minutes
        $undertime_hours = intval($undertime_minutes / 60);
        $remaining_minutes = $undertime_minutes % 60;
        
        return array(
            'hours' => $undertime_hours,
            'minutes' => $remaining_minutes,
            'total_minutes' => $undertime_minutes
        );
    }
    
    /**
     * Convert time string (HH:MM:SS or HH:MM) to minutes from midnight
     */
    private function timeToMinutes($time_string)
    {
        $time_parts = explode(':', $time_string);
        $hours = intval($time_parts[0]);
        $minutes = intval($time_parts[1]);
        
        return ($hours * 60) + $minutes;
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

        $reason = $this->input->post('reason') ?: 'Record deleted by admin';
        $delete = $this->biometricsModel->delete($id, $reason);
        $this->session->set_flashdata('success', 'danger');

        if ($delete) {
            $this->session->set_flashdata('message', 'Biometrics attendance has been deleted!');
        } else {
            $this->session->set_flashdata('message', 'Something went wrong. This attendance cannot be deleted!');
        }
        redirect('biometrics', 'refresh');
    }

    
    public function generate_bulk_dtr()
    {
        if (!$this->ion_auth->logged_in()) {
            // redirect them to the login page
            redirect('auth/login', 'refresh');
        }

        // Get all personnel
        $data['person'] = $this->personnelModel->personnels();

        $data['title'] = 'Generate Bulk DTR';

        $this->base->load('default', 'attendance/generate_bulk_dtr', $data);
    }

    /**
     * Summary of Failure to Clock In/Out
     * Shows employees who failed to clock in or out on workdays where they have at least one record
     */
    public function failure_summary()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        // Check if user is admin
        if (!$this->ion_auth->is_admin()) {
            $this->session->set_flashdata('success', 'danger');
            $this->session->set_flashdata('message', 'You do not have permission to access this page.');
            redirect('admin/dashboard', 'refresh');
        }

        $data['title'] = 'Summary of Failure to Clock In/Out';

        // Get date range from query parameters or default to current month
        $start_date = $this->input->get('start_date') ?: date('Y-m-01');
        $end_date = $this->input->get('end_date') ?: date('Y-m-t');

        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;

        // Get failure data from model
        $records = $this->biometricsModel->getFailureToClockSummary($start_date, $end_date);

        // Process the data to identify failures
        $data['failure_data'] = $this->processFailureData($records);

        $this->base->load('default', 'bio/failure_summary', $data);
    }

    /**
     * Process biometric records to identify failure to clock in/out
     * Groups by date and identifies missing time slots
     */
    private function processFailureData($records)
    {
        $grouped_by_date = array();

        foreach ($records as $record) {
            $date = $record->date;
            $day_of_week = date('w', strtotime($date));
            
            // Skip weekends (Sunday = 0, Saturday = 6)
            if ($day_of_week == 0 || $day_of_week == 6) {
                continue;
            }

            // Check if it's a Philippine holiday
            if ($this->isPhilippineHoliday($date)) {
                continue;
            }

            // Initialize date array if not exists
            if (!isset($grouped_by_date[$date])) {
                $grouped_by_date[$date] = array();
            }

            // Identify failures for this record
            $failures = array();
            
            if (empty($record->am_in)) {
                $failures[] = 'AM IN';
            }
            if (empty($record->am_out)) {
                $failures[] = 'AM OUT';
            }
            if (empty($record->pm_in)) {
                $failures[] = 'PM IN';
            }
            if (empty($record->pm_out)) {
                $failures[] = 'PM OUT';
            }

            // Only add if there are failures
            if (!empty($failures)) {
                $grouped_by_date[$date][] = array(
                    'employee_name' => $record->lastname . ', ' . $record->firstname . ' ' . (!empty($record->middlename) ? substr($record->middlename, 0, 1) . '.' : ''),
                    'bio_id' => $record->bio_id,
                    'failures' => $failures,
                    'am_in' => $record->am_in,
                    'am_out' => $record->am_out,
                    'pm_in' => $record->pm_in,
                    'pm_out' => $record->pm_out
                );
            }
        }

        // Sort by date descending
        krsort($grouped_by_date);

        return $grouped_by_date;
    }

    /**
     * Check if a date is a Philippine holiday
     */
    private function isPhilippineHoliday($date)
    {
        $year = date('Y', strtotime($date));
        $month_day = date('m-d', strtotime($date));
        
        // Fixed Philippine holidays
        $fixed_holidays = array(
            '01-01', // New Year's Day
            '02-25', // EDSA People Power Revolution Anniversary
            '04-09', // Araw ng Kagitingan (Day of Valor)
            '05-01', // Labor Day
            '06-12', // Independence Day
            '08-21', // Ninoy Aquino Day
            '08-25', // National Heroes Day (last Monday of August - approximation)
            '11-30', // Bonifacio Day
            '12-25', // Christmas Day
            '12-30', // Rizal Day
            '12-31'  // New Year's Eve
        );
        
        // Check fixed holidays
        if (in_array($month_day, $fixed_holidays)) {
            return true;
        }
        
        // Variable holidays (simplified - you may need to adjust these)
        // Maundy Thursday and Good Friday (varies each year)
        if ($year == 2024 && ($month_day == '04-18' || $month_day == '04-19')) {
            return true;
        }
        if ($year == 2025 && ($month_day == '04-17' || $month_day == '04-18')) {
            return true;
        }
        if ($year == 2026 && ($month_day == '04-02' || $month_day == '04-03')) {
            return true;
        }
        
        return false;
    }

    /**
     * AJAX endpoint to get dashboard chart data
     * Returns JSON with total edits and missing logs for specific months
     */
    public function get_dashboard_chart_data()
    {
        // Get month parameters from GET or POST
        $edits_month = $this->input->get('edits_month') ?: $this->input->post('edits_month') ?: date('Y-m');
        $dtr_month = $this->input->get('dtr_month') ?: $this->input->post('dtr_month') ?: date('Y-m');
        
        // Get data from model
        $chart_data = $this->biometricsModel->getDashboardChartData($edits_month, $dtr_month);
        
        // Return as JSON
        header('Content-Type: application/json');
        echo json_encode($chart_data);
    }
}
