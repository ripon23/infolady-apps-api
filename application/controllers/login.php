<?php

/**
 * Description of login
 *
 * @author Rafiqul Islam <rafiqul.islam@dnet.org.bd>
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {
    
    public function __construct() {
        
         parent::__construct();
         $this->load->model('loginmodel');
         
         if($this->mamalib->isLoggedIn()) {
             redirect('', 'location');
             exit;
         }
    }
    
    public function index() {
        if($this->mamalib->isLoggedIn()) {
             redirect('', 'location');
             exit;
         }
        
        $data = array();
         
        $data['uid'] = trim($this->input->post('uid'));
        $data['pwd'] = trim($this->input->post('pwd'));
         
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data['logged_info'] = $this->loginmodel->getLoggedInfo($data['uid'], $data['pwd']);
            if(!empty($data['logged_info'])) {
                $this->session->set_userdata('id'          , $data['logged_info']['id']);
                $this->session->set_userdata('uid'         , $data['uid']);
                $this->session->set_userdata('role'        , $data['logged_info']['role_id']);
                $this->session->set_userdata('name'        , $data['logged_info']['tx_name']);
                $this->session->set_userdata('organization', $data['logged_info']['tx_organization']);
                $this->session->set_userdata('designation' , $data['logged_info']['tx_designation']);
                $this->session->set_userdata('status_role' , $data['logged_info']['tx_level_token']);
                
                $this->session->set_userdata('page_access_token' , $data['logged_info']['page_access_id']);
//                var_dump($data['logged_info']['page_access_id']); 
//                echo '<br>';
//                var_dump($this->config->item('base_url'));
//                exit;
                if($data['logged_info']['page_access_id']=='2'){
                    $retUrl = $this->config->item('base_url').'subscriber/search';
                } else{
                    $retUrl = $this->session->userdata('login_ret_url') ? $this->session->userdata('login_ret_url') : '';
                }
                var_dump($data['logged_info']['page_access_id']); 
//                echo '<br>';
//                echo 'retUrl: ' . $retUrl;
//                die;
                
                redirect($retUrl, 'location');
                exit;
            }
        }
        $this->load->view('template/login', $data);
    }
}
