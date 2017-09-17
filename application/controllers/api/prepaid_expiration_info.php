<?php
class Prepaid_expiration_info extends CI_Controller {	
		
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
				$dob_limit_in_week=$this->config->item("dob_limit_in_week");
				$sql="SELECT DATEDIFF(SUB.`dtt_service_deactivation`, NOW()) AS remaining_days, SUB.`dtt_service_deactivation`, 
SUB.`int_subscriber_key` AS t_subscriber_id, SUB.`tx_name`, SUB.`tx_mobile`,  
SUB.`dt_last_menstrual_period` AS lmp,
ST.`tx_subscriber_type_name` AS subscriber_type,
D.tx_district_name, U.`tx_upazilla_name`,  UN.`tx_union_name`, PA.`tx_agent_name_en`
FROM `t_subscribers` AS `SUB` 
LEFT JOIN `t_outreach_partner_agents` AS `PA` ON PA.int_outreach_partner_agent_key = SUB.int_outreach_partner_agent_key 
LEFT JOIN `t_outreach_partners` AS `P` ON P.int_outreach_partner_key = PA.int_partner_key
LEFT JOIN `t_districts` AS D ON D.int_district_key=SUB.`int_district_key`
LEFT JOIN `t_upazillas` AS U ON U.`int_upazilla_key`=SUB.`int_upazilla_key`      
LEFT JOIN `t_unions` AS UN ON UN.`int_union_key`=SUB.`int_union_key`
LEFT JOIN `t_subscriber_types` AS ST ON ST.`int_subscriber_type_key`= SUB.`int_subscriber_type_key`
WHERE `tx_healthworker_id`='".$username."' AND `tx_status`='Registered' AND SUB.`subscription_type`='PREPAID'
AND SUB.`dtt_service_deactivation` BETWEEN DATE_SUB(NOW(), INTERVAL 7 DAY) AND DATE_ADD(NOW(), INTERVAL 7 DAY)
ORDER BY remaining_days ASC;";
				$result=$this->general_model->get_all_querystring_result($sql);
				
					if($result)
					{
					$response["subscriber_info"] = $result; 						
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
				
}

/* End of file api.php */
