<?php
class Profile_update extends CI_Controller {

	
	function __construct()
	{
		parent::__construct();

		// Load the necessary stuff...
		$this->load->helper(array('language', 'url', 'form', 'date'));		
		$this->load->model(array('general_model','account/account_model'));	
		$this->load->library(array('form_validation'));			
		date_default_timezone_set('Asia/Dhaka');  // set the time zone UTC+6
		
		
	}			
	
	function index()
	{
		
		$api_key=$this->input->post('api_key', TRUE);
		
		if($api_key=='C02301120170823APONJONAPPSV2')
		{
		$data_source='APONJONAPPSV2';
		$raw_data=array(
			'raw_params'=>var_export($_POST, true),			
			'status'=>0,
			'data_source'=>$data_source,
			'received_datetime'=>mdate('%Y-%m-%d %H:%i:%s', now())					
			);
		
		$request_id=$this->general_model->save_into_table_and_return_insert_id('apps_all_requests', $raw_data);	
		
		$this->form_validation->set_rules(array(
			array(
				'field' => 'user_name',
				'label' => 'Username',
				'rules' => 'trim|required'
			),
			array(
				'field' => 'api_key',
				'label' => 'API key',
				'rules' => 'trim|required'
			)
		));

			// Run form validation
			if ($this->form_validation->run() === TRUE)
			{	
									
				$username=$this->input->post('user_name', TRUE);
				$fullname=$this->input->post('name', TRUE);
				$mobile=$this->input->post('mobile', TRUE);
				$dob=$this->input->post('dob', TRUE);
				$email=$this->input->post('email', TRUE);
				$occupation=$this->input->post('occupation', TRUE);
				$address=$this->input->post('address', TRUE);
				$gender=$this->input->post('gender', TRUE);								
				
					
				if ( ! $user = $this->account_model->get_by_username($username))
				{
					$response["success"] = 0;
					$response["message"] = 'Username does not exist';
					echo json_encode($response);
				}
				else
				{
					// Date of Birth Validation
					if(!$this->validateDate($dob))
					{
					$response["success"] = 0;
					$response["message"] = 'Date of Bitrh is not valid';
					echo json_encode($response);
					die();	
					}
				
				$updated_info=array(						
					'tx_community_agent_name'=>$fullname,
					'dtt_mod'=>mdate('%Y-%m-%d %H:%i:%s', now()),
					'int_mod_user_key'=>999999
				);
				$this->general_model->update_table('t_community_agent_login', $updated_info, 'tx_community_agent_id', $username);
				
				$updated_info=array(						
					'tx_agent_name_en'=>$fullname,
					'tx_agent_mobile_no'=>$mobile,						
					'dtt_agent_date_of_birth'=>$dob,
					'email'=>$email,
					'occupation'=>$occupation,
					'address'=>$address,
					'gender'=>$gender,
					'dtt_mod'=>mdate('%Y-%m-%d %H:%i:%s', now()),
					'int_mod_user_key'=>999999
				);	
				$succed=$this->general_model->update_table('t_outreach_partner_agents', $updated_info, 'tx_agent_id', $username);	
																																											
					if($succed)
					{						
					$response["success"] = 1;									
					$response["message"] = 'Update successfully';
					// echoing JSON response
					echo json_encode($response);
					}
					else
					{
					$response["success"] = 0;									
					$response["message"] = 'Update unsuccessful';
					// echoing JSON response
					echo json_encode($response);	
					}
				}
					
			}
			else
			{
				$response["success"] = 0;
				$response["message"] = "Requerd field is empty";
				echo json_encode($response);
			}
		
		}
		else
		{
		$response["success"] = 0;
		$response["message"] = 'Wrong API Key';
		echo json_encode($response);
		die();	
		}
	
	}
	
	
	function validateDate($date)
	{
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
	}
	
	
				
}

/* End of file api.php */
