<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base Controller
 * 
 * This is the base controller class that all other controllers should extend.
 * You can add common functionality here that will be available to all controllers.
 */
class MY_Controller extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Load session library by default
        $this->load->library('session');
    }
}

