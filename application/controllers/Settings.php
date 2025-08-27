<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Settings extends CI_Controller
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
     **/

    public function backup()
    {

        $this->load->dbutil();

        $prefs = array(
            'format'      => 'zip',
            'filename'    => 'loanapp.sql',
            'ignore'        => array('users', 'groups', 'users_groups', 'login_attempts'),
            'foreign_key_checks' => FALSE
        );

        $backup = $this->dbutil->backup($prefs);

        $db_name = 'evaluation-backup-on-' . date("Y-m-d-H-i-s") . '.zip';
        $save = 'pathtobkfolder/' . $db_name;

        $this->load->helper('file');
        write_file($save, $backup);

        $this->load->helper('download');
        force_download($db_name, $backup);
    }

    public function restore()
    {

        $config['upload_path'] = './assets/backup/';
        $config['allowed_types'] = '*';

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('backup_file')) {

            $this->session->set_flashdata('errors',  $this->upload->display_errors());
        } else {
            $file = $this->upload->data();

            $sql = file_get_contents('./assets/backup/' . $file['file_name']);
            $string_query = rtrim($sql, '\n;');
            $array_query = explode(';', $sql);

            foreach ($array_query as $query) {
                $this->db->query($query);
            }
            $this->session->set_flashdata('message', 'Database Restored!');
        }

        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }

    public function save_settings()
    {
        // Ensure upload directory exists and is writable
        $upload_path = FCPATH . 'assets/uploads/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }
        
        // Make sure directory is writable
        if (!is_writable($upload_path)) {
            chmod($upload_path, 0755);
        }
        
        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = 'jpg|png|jpeg|gif';
        $config['encrypt_name'] = TRUE;
        $config['max_size'] = 2048; // 2MB
        $config['max_width'] = 2000;
        $config['max_height'] = 2000;

        $this->load->library('upload', $config);

        $this->session->set_flashdata('success', 'danger');
        $id = $this->input->post('id');
        
        // Debug: Log the ID value
        if (empty($id)) {
            $id = 1; // Default to 1 if no ID is provided
        }
        
        // Debug: Log all POST data
        log_message('debug', 'POST data: ' . print_r($this->input->post(), true));
        log_message('debug', 'Files data: ' . print_r($_FILES, true));

        if ($this->ion_auth->is_admin()) {

            $this->form_validation->set_rules('sys_name', 'System Name', 'required|trim');
            $this->form_validation->set_rules('sys_acronym', 'System Acronym', 'required|trim');

            if ($this->form_validation->run() == FALSE) {

                $this->session->set_flashdata('message', validation_errors());
            } else {

                // Check if a file was uploaded
                if (empty($_FILES['sys_logo']['name'])) {
                    // No file uploaded, update only text fields
                    $data = array(
                        'system_name' => $this->input->post('sys_name'),
                        'system_acronym' => $this->input->post('sys_acronym'),
                    );
                    log_message('debug', 'No file uploaded, updating text only');
                } else if (!$this->upload->do_upload('sys_logo')) {
                    // File upload failed
                    $upload_errors = $this->upload->display_errors();
                    log_message('error', 'Upload failed: ' . $upload_errors);
                    $this->session->set_flashdata('message', 'Upload failed: ' . $upload_errors);
                    redirect($_SERVER['HTTP_REFERER'], 'refresh');
                    return;
                } else {
                    $file = $this->upload->data();
                    log_message('debug', 'File uploaded: ' . print_r($file, true));
                    
                    //Resize and Compress Image
                    $config_img = array();
                    $config_img['image_library'] = 'gd2';
                    $config_img['source_image'] = $upload_path . $file['file_name'];
                    $config_img['create_thumb'] = FALSE;
                    $config_img['maintain_ratio'] = TRUE;
                    $config_img['quality'] = '60%';
                    $config_img['new_image'] = $upload_path . $file['file_name'];

                    $this->load->library('image_lib', $config_img);
                    if (!$this->image_lib->resize()) {
                        log_message('error', 'Image resize failed: ' . $this->image_lib->display_errors());
                    }

                    $data = array(
                        'system_logo' => $file['file_name'],
                        'system_name' => $this->input->post('sys_name'),
                        'system_acronym' => $this->input->post('sys_acronym'),
                    );
                }
                
                // Debug: Check what data we're trying to update
                log_message('debug', 'Update data: ' . print_r($data, true));
                log_message('debug', 'Update ID: ' . $id);
                
                $update = $this->dashboardModel->update($data, $id);
                
                // Debug: Check the result
                log_message('debug', 'Update result: ' . $update);

                if ($update > 0) {
                    $this->session->set_flashdata('success', 'success');
                    $this->session->set_flashdata('message', 'System has been updated!');
                } else {
                    $this->session->set_flashdata('message', 'No changes has been made!');
                }
            }
        } else {
            $this->session->set_flashdata('message', 'Your not an admin!');
        }

        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }
}
