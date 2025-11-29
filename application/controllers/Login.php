<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->library('session');
    }
    
    public function index() {
        // If already logged in, redirect to home
        if ($this->session->userdata('id_user')) {
            redirect('home');
            return;
        }
        $this->load->view('login'); // loads application/views/login.php
    }
}
