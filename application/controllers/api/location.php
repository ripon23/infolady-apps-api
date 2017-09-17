<?php
class Location extends CI_Controller {

	
	function __construct()
	{
		parent::__construct();

		// Load the necessary stuff...
		$this->load->helper(array('language', 'url', 'form'));		
		$this->load->model(array('general_model','account/account_model'));	
		$this->load->library(array('form_validation'));			
		date_default_timezone_set('Asia/Dhaka');  // set the time zone UTC+6				
	}		
	
	
	function index()
	{		
	echo "URL Not Correct";		
	}
	
	
	public function division()
	{		
	$divisions=$this->general_model->get_all_table_info_asc_desc('t_divisions', 'int_division_key', 'ASC');	
  	$json = json_encode($divisions);
	echo $json;
	}
		
	public function district()	
	{
	$districts=$this->general_model->get_all_table_info_asc_desc('t_districts', 'int_division_key', 'ASC');	
  	$json = json_encode($districts);
	echo $json;	
	}
	
	public function upazilla()
	{
	$upazillas=$this->general_model->get_all_table_info_asc_desc('t_upazillas', 'int_upazilla_key', 'ASC');	
  	$json = json_encode($upazillas);
	echo $json;		
	}
		
	public function union()	
	{
	$unions=$this->general_model->get_all_table_info_asc_desc('t_unions', 'int_union_key', 'ASC');	
  	$json = json_encode($unions);
	echo $json;	
	}			
}

/* End of file location.php */
