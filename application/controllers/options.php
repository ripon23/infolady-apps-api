<?php

/**
 * Description of Reports
 *
 * @author Rafiqul Islam <rafiqul.islam@dnet.org.bd>
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Options extends CI_Controller 
{
    public function __construct() 
    {
        parent::__construct();
        if(!$this->mamalib->isLoggedIn()) {
           $this->session->set_userdata('login_ret_url', "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
           redirect('/login', 'location');
           exit;
        }
        $this->load->model('optionsmodel');
    }
    
    public function config_info_source()
    {
        if($this->session->userdata('role')!='3'){
            $this->session->set_userdata('login_ret_url', "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
           redirect('/login', 'location');
           exit;
        }
        $data['inf'] =  $this->optionsmodel->listDataSourceOptions();
        
        $data['page'] = 'options/config_info_source';        
        $this->load->view('template/layout1', $data);
    }
}
