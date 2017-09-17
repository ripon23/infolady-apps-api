<?php
class User_login extends CI_Controller {

	
	function __construct()
	{
		parent::__construct();

		// Load the necessary stuff...
		$this->load->helper(array('language', 'url', 'form', 'date'));		
		$this->load->model(array('general_model','account/account_model'));	
		$this->load->library(array('form_validation'));			
		date_default_timezone_set('Asia/Dhaka');  // set the time zone UTC+6
		
		
	}
	
	/*
	function md5password()
	{
		$userinfo=$this->general_model->get_all_table_info_asc_desc('t_community_agent_login', 'int_community_agent_login_key', 'asc');
		foreach($userinfo as $info)
		{			
			
			$table_data=array(
			'tx_community_agent_password'=>md5($info->tx_community_agent_id)
			);
			
			$this->general_model->update_table('t_community_agent_login', $table_data,'int_community_agent_login_key', $info->int_community_agent_login_key);
		}
		
	}
	*/
	
	
	function index()
	{
		
		// Enable SSL?
		//maintain_ssl($this->config->item("ssl_enabled"));

		// Setup form validation
		//$this->form_validation->set_error_delimiters('<span class="field_error">', '</span>');
		
		$this->form_validation->set_rules(array(
			array(
				'field' => 'user_name',
				'label' => 'Username',
				'rules' => 'trim|required'
			),
			array(
				'field' => 'password',
				'label' => 'Password',
				'rules' => 'trim|required'
			)
		));

		// Run form validation
		if ($this->form_validation->run() === TRUE)
		{	
			// Get user by username / email
			
			/*$username=base64_encode($this->input->post('user_name', TRUE));
			$password=base64_encode($this->input->post('password', TRUE));
			echo "username=".$username." Password=".$password;
			die();
			*/
			//username=Q0MyNjE2MDQ4MTE= Password=Q0MyNjE2MDQ4MTE=
			//CC261604728 username=Q0MyNjE2MDQ3Mjg= Password=Q0MyNjE2MDQ3Mjg=
			
			$username=base64_decode($this->input->post('user_name', TRUE));
			$password=base64_decode($this->input->post('password', TRUE));
			
			if ( ! $user = $this->account_model->get_by_username($username))
			{
				$response["success"] = 0;
				$response["message"] = 'Username does not exist';
				echo json_encode($response);
			}
			else
			{				
				//base64_decode($this->input->post('password', TRUE))
				if ( ! $this->account_model->password_varification($username, $password))
				{					
					$response["success"] = 0;
					$response["message"] = 'Wrong username and password';
					echo json_encode($response);
					
				}
				else
				{
				//echo "Success";
				//die();
				
									
				$user_info = $this->general_model->get_all_table_info_by_id('t_outreach_partner_agents', 'tx_agent_id', $username);
					//print_r($data['all_site']);
					 $response["user_info"] = array(); 
						
						$profile = array();
						$profile["fullname"] = $user_info->tx_agent_name_en;
						$profile["mobile"] =  $user_info->tx_agent_mobile_no;
						$profile["division"] =$this->account_model->get_location_name_by_id($user_info->int_division_key,'DV');
						$profile["district"] =$this->account_model->get_location_name_by_id($user_info->int_district_key,'DT');
						$profile["upazilla"] =$this->account_model->get_location_name_by_id($user_info->int_upazilla_key,'UP');
						$profile["union"] =$this->account_model->get_location_name_by_id($user_info->int_union_key,'UN');
						$profile["organization"] =$this->account_model->get_partner_name_by_id($user_info->int_partner_key);
						$profile["education_level"] =  $user_info->int_education_level_key;
						$profile["expire_on"] =date('Y-m-d H:i:s', strtotime('1 hour'));
						array_push($response["user_info"], $profile);
						
										
					$response["success"] = 1;									
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
	
	function change_password()
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
				'field' => 'old_password',
				'label' => 'Old Password',
				'rules' => 'trim|required'
			),
			array(
				'field' => 'new_password',
				'label' => 'New Password',
				'rules' => 'trim|required|min_length[6]'
			)
		));

		// Run form validation
		if ($this->form_validation->run() === TRUE)
		{							
			$username=$this->input->post('user_name', TRUE);
			$old_password=$this->input->post('old_password', TRUE);
			$new_password=$this->input->post('new_password', TRUE);
			
				if ( ! $user = $this->account_model->get_by_username($username))
				{
					$response["success"] = 0;
					$response["message"] = 'Username does not exist';
					echo json_encode($response);
				}
				else
				{	
				
					if ( ! $this->account_model->password_varification($username, $old_password))
					{					
					$response["success"] = 0;
					$response["message"] = 'Old password is incorrect';
					echo json_encode($response);
										
					$success_data=array(
					'error_msg'=>'Old password is incorrect',
					'status'=>1,
					'comments'=>'ERROR'
					);
					$this->general_model->update_table('apps_all_requests', $success_data, 'request_id', $request_id);
					die();
					}
					else
					{
				
					$table_data=array(						
					'tx_community_agent_password'=>md5($new_password),
					'int_mod_user_key'=>100001, // for tab user use this number as previous, dont know why
					'dtt_mod'=>mdate('%Y-%m-%d %H:%i:%s', now())
					);				
				
					$this->general_model->update_table('t_community_agent_login', $table_data,'tx_community_agent_id', $username);
					}
																
					$response["success"] = 1;
					$response["message"] = 'Password changed successfully';
					// echo JSON response				
					echo json_encode($response);
				
				
					# Update status
					$success_data=array(
					'error_msg'=>'Password changed successfully',
					'status'=>1,
					'comments'=>'SUCCESS'
					);
					$this->general_model->update_table('apps_all_requests', $success_data, 'request_id', $request_id);
					die();
							
				}
			
			}
			else
			{
				$response["success"] = 0;
				$response["message"] = "Requerd field is empty";
				echo json_encode($response);
				
				$success_data=array(
					'error_msg'=>'Requerd field is empty',
					'status'=>1,
					'comments'=>'ERROR'
					);
				$this->general_model->update_table('apps_all_requests', $success_data, 'request_id', $request_id);
				die();
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
	
	
	function forgot_password()
	{
	$this->form_validation->set_rules(array(
			array(
				'field' => 'user_name',
				'label' => 'Username',
				'rules' => 'trim|required'
			)
		));
		
		if ($this->form_validation->run() === TRUE)
		{
			$username=base64_decode($this->input->post('user_name', TRUE));
			
			if ( ! $user = $this->account_model->get_by_username($username))
			{
				$response["success"] = 0;
				$response["message"] = 'Username does not exist';
				echo json_encode($response);
			}
			else
			{
			$searchterm="Select * FROM password_reset_request WHERE username='$username' AND status=0";		
				if($this->general_model->is_exist_in_a_table_querystring($searchterm))
				{
					$response["success"] = 0;
					$response["message"] = 'You already place a request. Need not to place again.';
					echo json_encode($response);	
				}
				else
				{
				$PassData = array(
									  'username'    		=> $username
									, 'request_datetime' 	=> mdate('%Y-%m-%d %H:%i:%s', now())
									, 'view_status'        	=> 0
									, 'status'             	=> 0
								);
								
				$success_or_fail=$this->general_model->save_into_table('password_reset_request', $PassData);
					if($success_or_fail)
					{
					$response["success"] = 1;
					$response["message"] = 'Password reset request successfully placed.';
					echo json_encode($response);	
					}
					else
					{
					$response["success"] = 0;
					$response["message"] = 'Something went wrong! Please try again later.';
					echo json_encode($response);		
					}
				}
			}			
		}
	}
				
}

/* End of file api.php */
