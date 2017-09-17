<?php
class Faq extends CI_Controller {	
		
	function __construct()
	{
		parent::__construct();

		// Load the necessary stuff...
		$this->load->helper(array('language', 'url', 'form', 'date'));		
		$this->load->model(array('general_model','account/account_model'));	
		$this->load->library(array('form_validation','date', 'subscriber'));			
		date_default_timezone_set('Asia/Dhaka');  // set the time zone UTC+6				
	}		
	
	
	function index()
	{
	$response["success"] = 0;
	$response["message"] = 'Wrong URL';
	echo json_encode($response);
	}
	
	
	function faq_list()
	{
		
		$is_error=0;
		$api_key=$this->input->post('api_key', TRUE);  // QzAyMzAxMTIwMTcwNzExQ0hBVEJPVA==
		
		if(($api_key=='C02301120170823APONJONAPPSV2'))
		{				
		$data_source='APONJONAPPSV2';			
		
		## 	Sec:1 
		##	If API Key match insert the raw request in TABLE: apps_all_requests
		$raw_data=array(
			'raw_params'=>var_export($_POST, true),			
			'status'=>0,
			'data_source'=>$data_source,
			'received_datetime'=>mdate('%Y-%m-%d %H:%i:%s', now())					
			);
		
		$request_id=$this->general_model->save_into_table_and_return_insert_id('apps_all_requests', $raw_data);
				
		##	END Sec:1
		
		##	Sec:2 
		##	Parse all params and insert in to TABLE: chatbot_action_requests 
		## 	Validate all data
			if($request_id)
			{
				
				$username=$this->input->post('user_name', TRUE);
				if ( ! $user = $this->account_model->get_by_username($username))
				{
					$response["success"] = 0;
					$response["message"] = 'Username does not exist';
					echo json_encode($response);
				}
				else
				{
				# CA17700002 CC261604755	 				
				$sql="SELECT s.`faq_id`, c.`category_name`, s.`faq_title`, s.`faq_description`, 
				s.`update_date` AS last_update_at, s.`version`, s.`is_active` FROM `faq` s 
				LEFT JOIN `faq_category` AS `c` ON c.`category_id` = s.`faq_category`
				ORDER BY s.`faq_category` ASC;";
				$result=$this->general_model->get_all_querystring_result($sql);
				
					if($result)
					{
					$response["faq_info"] = $result; 						
					//array_push($response["subscriber_info"], $result);
					$response["success"] = 1;									
					echo json_encode($response);						
					}
					else
					{
					$response["success"] = 1;
					$response["message"] = 'No Data Found';
					echo json_encode($response);	
					}
				}
			
			
			}
			else
			{
			$response["success"] = 0;
			$response["message"] = 'Database server might be down! Please try again';
			echo json_encode($response);
			die();
			}
		
		
		## 	END Sec:2	
		}
		else
		{
		$response["success"] = 0;
		$response["message"] = 'Wrong API Key';
		echo json_encode($response);
		die();	
		}
	
	}
	
	
	function faq_new_update_list()
	{
		$is_error=0;
		$api_key=$this->input->post('api_key', TRUE);  // QzAyMzAxMTIwMTcwNzExQ0hBVEJPVA==
		
		if(($api_key=='C02301120170823APONJONAPPSV2'))
		{				
		$data_source='APONJONAPPSV2';			
		
		## 	Sec:1 
		##	If API Key match insert the raw request in TABLE: apps_all_requests
		$raw_data=array(
			'raw_params'=>var_export($_POST, true),			
			'status'=>0,
			'data_source'=>$data_source,
			'received_datetime'=>mdate('%Y-%m-%d %H:%i:%s', now())					
			);
		
		$request_id=$this->general_model->save_into_table_and_return_insert_id('apps_all_requests', $raw_data);
				
		##	END Sec:1
		
		##	Sec:2 
		##	Parse all params and insert in to TABLE: chatbot_action_requests 
		## 	Validate all data
			if($request_id)
			{
				
				$username=$this->input->post('user_name', TRUE);
				if ( ! $user = $this->account_model->get_by_username($username))
				{
					$response["success"] = 0;
					$response["message"] = 'Username does not exist';
					echo json_encode($response);
				}
				else
				{
					# CA17700002 CC261604755
					if($this->validateDate($this->input->post('max_date', TRUE)))
					{
					
					$max_date=$this->input->post('max_date', TRUE)." 00:00:00";										
										
					$sql="SELECT s.`faq_id`, c.`category_name`, s.`faq_title`, s.`faq_description`, 
					s.`update_date` AS last_update_at, s.`version`, 
					s.`is_active` FROM `faq` s 
					LEFT JOIN `faq_category` AS `c` ON c.`category_id` = s.`faq_category`
					WHERE s.`update_date`>'".$max_date."' ORDER BY s.`faq_category` ASC;";
					$result=$this->general_model->get_all_querystring_result($sql);
					
						if($result)
						{
						$response["faq_info"] = $result; 						
						$response["success"] = 1;									
						echo json_encode($response);						
						}
						else
						{
						$response["success"] = 1;
						$response["message"] = 'No Data Found';
						echo json_encode($response);	
						}
					}
					else
					{
					$response["success"] = 0;
					$response["message"] = 'Date is not valid';
					echo json_encode($response);
					die();	
					}
				}
			
			
			}
			else
			{
			$response["success"] = 0;
			$response["message"] = 'Database server might be down! Please try again';
			echo json_encode($response);
			die();
			}
		
		
		## 	END Sec:2	
		}
		else
		{
		$response["success"] = 0;
		$response["message"] = 'Wrong API Key';
		echo json_encode($response);
		die();	
		}		
	
	}
	
	function single_faq()
	{
		$is_error=0;
		$api_key=$this->input->post('api_key', TRUE);  // QzAyMzAxMTIwMTcwNzExQ0hBVEJPVA==
		
		if(($api_key=='C02301120170823APONJONAPPSV2'))
		{				
		$data_source='APONJONAPPSV2';			
		
		## 	Sec:1 
		##	If API Key match insert the raw request in TABLE: apps_all_requests
		$raw_data=array(
			'raw_params'=>var_export($_POST, true),			
			'status'=>0,
			'data_source'=>$data_source,
			'received_datetime'=>mdate('%Y-%m-%d %H:%i:%s', now())					
			);
		
		$request_id=$this->general_model->save_into_table_and_return_insert_id('apps_all_requests', $raw_data);
				
		##	END Sec:1
		
		##	Sec:2 
		##	Parse all params and insert in to TABLE: chatbot_action_requests 
		## 	Validate all data
			if($request_id)
			{
				
				$username=$this->input->post('user_name', TRUE);
				if ( ! $user = $this->account_model->get_by_username($username))
				{
					$response["success"] = 0;
					$response["message"] = 'Username does not exist';
					echo json_encode($response);
				}
				else
				{
					# CA17700002 CC261604755
					$faq_id=$this->input->post('faq_id', TRUE);	
					
					if($this->general_model->is_exist_in_a_table('faq','faq_id',$faq_id))
					{																													
					$sql="SELECT s.`faq_id`, c.`category_name`, s.`faq_title`, s.`faq_description`, 
					s.`update_date` AS last_update_at, s.`version`, 
					s.`is_active` FROM `faq` s 
					LEFT JOIN `faq_category` AS `c` ON c.`category_id` = s.`faq_category`
					WHERE s.`faq_id`=".$faq_id;															
					 
					$result=$this->general_model->get_all_single_row_querystring($sql);
					
						if($result)
						{
						$response["faq_info"] = $result; 						
						$response["success"] = 1;									
						echo json_encode($response);						
						}
						else
						{
						$response["success"] = 1;
						$response["message"] = 'No Data Found';
						echo json_encode($response);	
						}
					}
					else
					{
					$response["success"] = 0;
					$response["message"] = 'FAQ ID not found';
					echo json_encode($response);
					die();	
					}
				}
			
			
			}
			else
			{
			$response["success"] = 0;
			$response["message"] = 'Database server might be down! Please try again';
			echo json_encode($response);
			die();
			}
		
		
		## 	END Sec:2	
		}
		else
		{
		$response["success"] = 0;
		$response["message"] = 'Wrong API Key';
		echo json_encode($response);
		die();	
		}		
	
	}
	
	
	
	
	public function validateDate($date)
	{
		$d = DateTime::createFromFormat('Y-m-d', $date);
		return $d && $d->format('Y-m-d') === $date;
	}		
				
}

/* End of file api.php */
