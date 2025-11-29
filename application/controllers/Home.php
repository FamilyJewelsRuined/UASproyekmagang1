<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('model_app_userlogin');
    }

    public function index()
    {
        // Check if user is logged in
        if (!$this->session->userdata('id_user')) {
            redirect('login');
        }
        $this->load->view('layout/index');   // ini layout
    }

    public function content()
    {
        // Check if user is logged in
        if (!$this->session->userdata('id_user')) {
            redirect('login');
        }
        $this->load->view('home/index');     // ini isi halaman
    }

    /**
     * Render the PPA approval dashboard.
     */
    public function view_ppa_approve()
    {
        if (!$this->session->userdata('id_user')) {
            redirect('login');
        }

        // Only allow privileged group (hardcoded legacy behaviour from menu.php)
        if ($this->session->userdata('group') !== '100') {
            show_error('Anda tidak memiliki akses ke halaman ini.', 403);
        }

        $this->load->view('view_ppa_approve');
    }

    /**
     * Handle login authentication
     */
    public function login()
    {
        // Check if already logged in
        if ($this->session->userdata('id_user')) {
            redirect('home');
            return;
        }

        // Get POST data
        $username = $this->input->post('username');
        $password = $this->input->post('password');

        // Set JSON header
        $this->output->set_content_type('application/json');
        
        // Validate input
        if (empty($username) || empty($password)) {
            $this->output->set_output(json_encode(false));
            return;
        }

        // Verify credentials
        $user = $this->model_app_userlogin->verifyLogin($username, $password);

        if ($user) {
            $filterColumn = isset($user['filter_user_column']) ? $user['filter_user_column'] : null;
            $linkedEntityId = isset($user['linked_entity_id']) ? $user['linked_entity_id'] : null;
            $linkedEntityType = isset($user['linked_entity_type']) ? $user['linked_entity_type'] : null;

            // Set session data
            $session_data = array(
                'id_user' => $user['id'],
                'username' => $user['username'],
                'nama' => $user['nama'],
                'fullname' => $user['nama'], // Used by layout/index.php navbar
                'role' => $user['role'],
                'group' => $user['role'], // Backward compatibility with existing code
                'logged_in' => true,
                'linked_entity_id' => $linkedEntityId,
                'linked_entity_type' => $linkedEntityType,
                'filter_user_column' => in_array($filterColumn, ['agen_id', 'customer_id']) ? $filterColumn : null,
                'filter_user_id' => in_array($filterColumn, ['agen_id', 'customer_id']) ? $linkedEntityId : null,
            );
            $this->session->set_userdata($session_data);

            // Log login history
            $this->model_app_userlogin->logLogin($user['id']);

            // Return success
            $this->output->set_output(json_encode(true));
        } else {
            // Return failure
            $this->output->set_output(json_encode(false));
        }
    }

    /**
     * Handle logout
     */
    public function logout()
    {
        $this->session->sess_destroy();
        redirect('login');
    }
}
