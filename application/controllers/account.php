<?php 
class account extends MY_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->urlaccess = get_class($this);
        $this->setAccessRole();
        $this->load->model('model_account');
    }

    function index(){
        $role = $this->session->userdata('group');
        if ($role == 101) {
            $data['account'] = $this->model_account->getAccountAgen(); 
        } elseif ($role == 102 || $role == 103) {
            $data['account'] = $this->model_account->getAccountCustomer(); 
        }

        $this->load->view('view_account',$data); 
    }


}