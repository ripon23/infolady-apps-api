<?php
/**
 * Description of logout
 *
 * @author Rafiqul Islam <rafiqul.islam@dnet.org.bd>
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logout extends CI_Controller {
    
    public function __construct() {
        
         parent::__construct();
    }
    
    public function index()
	{
		$this->session->sess_destroy();
		redirect('login','location');
	}
}
