<?php
defined('BASEPATH') or exit('No direct script access allowed');

class TimeChanges extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('TimeChangesModel', 'timeChangesModel');
        $this->load->model('PersonnelModel', 'personnelModel');
        $this->load->model('BiometricsModel', 'biometricsModel');
    }

    public function index()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        $data['title'] = 'Failure to Clock & Time Changes';
        $data['personnel'] = $this->personnelModel->personnels();

        $this->base->load('default', 'time_changes/index', $data);
    }

    public function personnel_biometrics($bio_id)
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        // Get personnel details
        $personnel = $this->personnelModel->get_personnel_by_bio_id($bio_id);
        
        if (!$personnel) {
            $this->session->set_flashdata('success', 'danger');
            $this->session->set_flashdata('message', 'Personnel not found!');
            redirect('timechanges', 'refresh');
        }

        // Get selected month from URL parameter or session, default to current month
        $selected_month = $this->input->get('month');
        if ($selected_month) {
            // Store in session for persistence
            $this->session->set_userdata('timechanges_selected_month_' . $bio_id, $selected_month);
        } else {
            // Try to get from session
            $selected_month = $this->session->userdata('timechanges_selected_month_' . $bio_id);
            if (!$selected_month) {
                $selected_month = date('Y-m');
            }
        }

        $data['title'] = 'Time Changes - ' . $personnel->lastname . ', ' . $personnel->firstname;
        $data['personnel'] = $personnel;
        $data['person'] = $this->personnelModel->personnels();
        $data['selected_month'] = $selected_month;

        $this->base->load('default', 'time_changes/personnel_biometrics', $data);
    }

    public function get_personnel_bio()
    {
        $bio_id = $_POST['bio_id'];
        $date = isset($_POST['date']) ? $_POST['date'] : '';

        $list = $this->timeChangesModel->get_datatables($bio_id, $date);
        $data = array();
        $no = $_POST['start'];
        
        foreach ($list as $bio) {
            $no++;
            $row = array();
            $row[] = '<input type="checkbox" class="bio-checkbox" data-id="' . $bio->id . '">';
            $row[] = date('m/d/Y', strtotime($bio->date));
            $row[] = empty($bio->am_in) ? '<span class="text-muted">--:--</span>' : date('h:i A', strtotime($bio->am_in));
            $row[] = empty($bio->am_out) ? '<span class="text-muted">--:--</span>' : date('h:i A', strtotime($bio->am_out));
            $row[] = empty($bio->pm_in) ? '<span class="text-muted">--:--</span>' : date('h:i A', strtotime($bio->pm_in));
            $row[] = empty($bio->pm_out) ? '<span class="text-muted">--:--</span>' : date('h:i A', strtotime($bio->pm_out));
            $row[] = isset($bio->undertime_hours) ? $bio->undertime_hours : '0';
            $row[] = isset($bio->undertime_minutes) ? $bio->undertime_minutes : '0';
            $row[] = '
                <div class="form-button-action">
                    <button type="button" class="btn btn-success btn-sm edit-bio-btn" title="Edit Time" data-id="' . $bio->id . '">
                        <i class="fa fa-edit"></i> Edit
                    </button>
                    <button type="button" class="btn btn-danger btn-sm ml-1 delete-bio-btn" title="Delete" data-id="' . $bio->id . '" data-bio-id="' . $bio->bio_id . '">
                        <i class="fa fa-times"></i>
                    </button>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->timeChangesModel->count_all($bio_id, $date),
            "recordsFiltered" => $this->timeChangesModel->count_filtered($bio_id, $date),
            "data" => $data,
        );
        
        echo json_encode($output);
    }

    public function update()
    {
        // Check if it's an AJAX request
        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('date', 'Date', 'trim|required');
            $this->form_validation->set_rules('bio_id', 'Personnel Biometrics ID', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                echo json_encode(['success' => false, 'message' => validation_errors()]);
                return;
            }

            $id = $this->input->post('id');
            $am_in = $this->input->post('am_in');
            $am_out = $this->input->post('am_out');
            $pm_in = $this->input->post('pm_in');
            $pm_out = $this->input->post('pm_out');
            
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
                $data['undertime_hours'] = (int)$manual_undertime_hours;
                $data['undertime_minutes'] = (int)$manual_undertime_minutes;
            } else {
                $undertime = $this->calculateUndertime($am_in, $am_out, $pm_in, $pm_out);
                
                if ($undertime === null) {
                    $data['undertime_hours'] = null;
                    $data['undertime_minutes'] = null;
                } else {
                    $data['undertime_hours'] = $undertime['hours'];
                    $data['undertime_minutes'] = $undertime['minutes'];
                }
            }

            $reason = $this->input->post('reason') ?: 'Time change adjustment';
            $update = $this->biometricsModel->update($data, $id, $reason);

            if ($update) {
                echo json_encode(['success' => true, 'message' => 'Time record has been updated!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No changes were made!']);
            }
        } else {
            // Fallback for non-AJAX requests
            $this->session->set_flashdata('success', 'danger');
            $this->form_validation->set_rules('date', 'Date', 'trim|required');
            $this->form_validation->set_rules('bio_id', 'Personnel Biometrics ID', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', validation_errors());
            } else {
                $id = $this->input->post('id');
                $am_in = $this->input->post('am_in');
                $am_out = $this->input->post('am_out');
                $pm_in = $this->input->post('pm_in');
                $pm_out = $this->input->post('pm_out');
                
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
                    $data['undertime_hours'] = (int)$manual_undertime_hours;
                    $data['undertime_minutes'] = (int)$manual_undertime_minutes;
                } else {
                    $undertime = $this->calculateUndertime($am_in, $am_out, $pm_in, $pm_out);
                    
                    if ($undertime === null) {
                        $data['undertime_hours'] = null;
                        $data['undertime_minutes'] = null;
                    } else {
                        $data['undertime_hours'] = $undertime['hours'];
                        $data['undertime_minutes'] = $undertime['minutes'];
                    }
                }

                $reason = $this->input->post('reason') ?: 'Time change adjustment';
                $update = $this->biometricsModel->update($data, $id, $reason);

                if ($update) {
                    $this->session->set_flashdata('success', 'success');
                    $this->session->set_flashdata('message', 'Time record has been updated!');
                } else {
                    $this->session->set_flashdata('message', 'No changes has been made!');
                }
            }
            redirect($_SERVER['HTTP_REFERER'], 'refresh');
        }
    }

    public function bulk_update()
    {
        $ids = $this->input->post('ids');
        $field = $this->input->post('field');
        $value = $this->input->post('value');
        $reason = $this->input->post('reason') ?: 'Bulk time change adjustment';

        if (empty($ids) || !is_array($ids)) {
            echo json_encode(['success' => false, 'message' => 'No records selected']);
            return;
        }

        $updated = 0;
        foreach ($ids as $id) {
            $data = array($field => $value);
            
            // Recalculate undertime if time fields are changed
            if (in_array($field, ['am_in', 'am_out', 'pm_in', 'pm_out'])) {
                $bio = $this->biometricsModel->getBiometric($id);
                
                $am_in = $field == 'am_in' ? $value : $bio->am_in;
                $am_out = $field == 'am_out' ? $value : $bio->am_out;
                $pm_in = $field == 'pm_in' ? $value : $bio->pm_in;
                $pm_out = $field == 'pm_out' ? $value : $bio->pm_out;
                
                $undertime = $this->calculateUndertime($am_in, $am_out, $pm_in, $pm_out);
                
                if ($undertime === null) {
                    $data['undertime_hours'] = null;
                    $data['undertime_minutes'] = null;
                } else {
                    $data['undertime_hours'] = $undertime['hours'];
                    $data['undertime_minutes'] = $undertime['minutes'];
                }
            }
            
            if ($this->biometricsModel->update($data, $id, $reason)) {
                $updated++;
            }
        }

        if ($updated > 0) {
            echo json_encode(['success' => true, 'message' => $updated . ' record(s) updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No records were updated']);
        }
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
            
            $undertime = $this->calculateUndertime($am_in, $am_out, $pm_in, $pm_out);
            
            $data = array(
                'date' => $this->input->post('date'),
                'am_in' => $am_in,
                'am_out' => $am_out,
                'pm_in' => $pm_in,
                'pm_out' => $pm_out,
                'bio_id' => $this->input->post('bio_id')
            );
            
            if ($undertime === null) {
                $data['undertime_hours'] = null;
                $data['undertime_minutes'] = null;
            } else {
                $data['undertime_hours'] = $undertime['hours'];
                $data['undertime_minutes'] = $undertime['minutes'];
            }

            $reason = $this->input->post('reason') ?: 'Manual time entry';
            $insert = $this->biometricsModel->save($data, $reason);

            if ($insert) {
                $this->session->set_flashdata('success', 'success');
                $this->session->set_flashdata('message', 'Time record has been created!');
            } else {
                $this->session->set_flashdata('message', 'Something went wrong. Please try again!');
            }
        }
        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }

    public function delete($id, $bio_id)
    {
        $reason = 'Record deleted via Time Changes page';
        $delete = $this->biometricsModel->delete($id, $reason);

        if ($delete) {
            $this->session->set_flashdata('success', 'success');
            $this->session->set_flashdata('message', 'Record has been deleted!');
        } else {
            $this->session->set_flashdata('success', 'danger');
            $this->session->set_flashdata('message', 'Failed to delete record!');
        }
        
        // Get the selected month from session to preserve it
        $selected_month = $this->session->userdata('timechanges_selected_month_' . $bio_id);
        $redirect_url = 'timechanges/personnel_biometrics/' . $bio_id;
        if ($selected_month) {
            $redirect_url .= '?month=' . $selected_month;
        }
        
        redirect($redirect_url, 'refresh');
    }

    public function getBio()
    {
        $validator = array('data' => array());
        $id = $this->input->post('id');
        $validator['data'] = $this->biometricsModel->getBiometric($id);
        echo json_encode($validator);
    }

    private function calculateUndertime($am_in, $am_out, $pm_in, $pm_out)
    {
        $has_am_in = !empty($am_in);
        $has_am_out = !empty($am_out);
        $has_pm_in = !empty($pm_in);
        $has_pm_out = !empty($pm_out);
        
        if (!$has_am_in && !$has_am_out && !$has_pm_in && !$has_pm_out) {
            return null;
        }
        
        $undertime_minutes = 0;
        
        $standard_am_in = 8 * 60;
        $standard_am_out = 12 * 60;
        $standard_pm_in = 13 * 60;
        $standard_pm_out = 17 * 60;
        
        $actual_am_in = $has_am_in ? $this->timeToMinutes($am_in) : null;
        $actual_am_out = $has_am_out ? $this->timeToMinutes($am_out) : null;
        $actual_pm_in = $has_pm_in ? $this->timeToMinutes($pm_in) : null;
        $actual_pm_out = $has_pm_out ? $this->timeToMinutes($pm_out) : null;
        
        $has_complete_morning = $has_am_in && $has_am_out;
        $has_complete_afternoon = $has_pm_in && $has_pm_out;
        
        if ($has_complete_morning) {
            if ($actual_am_in > $standard_am_in) {
                $undertime_minutes += ($actual_am_in - $standard_am_in);
            }
            if ($actual_am_out < $standard_am_out) {
                $undertime_minutes += ($standard_am_out - $actual_am_out);
            }
        } else if ($has_am_in || $has_am_out) {
            $undertime_minutes += 240;
        } else {
            $undertime_minutes += 240;
        }
        
        if ($has_complete_afternoon) {
            if ($actual_pm_in > $standard_pm_in) {
                $undertime_minutes += ($actual_pm_in - $standard_pm_in);
            }
            if ($actual_pm_out < $standard_pm_out) {
                $undertime_minutes += ($standard_pm_out - $actual_pm_out);
            }
        } else if ($has_pm_in || $has_pm_out) {
            $undertime_minutes += 240;
        } else {
            $undertime_minutes += 240;
        }
        
        $undertime_hours = intval($undertime_minutes / 60);
        $remaining_minutes = $undertime_minutes % 60;
        
        return array(
            'hours' => $undertime_hours,
            'minutes' => $remaining_minutes,
            'total_minutes' => $undertime_minutes
        );
    }
    
    private function timeToMinutes($time_string)
    {
        $time_parts = explode(':', $time_string);
        $hours = intval($time_parts[0]);
        $minutes = intval($time_parts[1]);
        return ($hours * 60) + $minutes;
    }
}
