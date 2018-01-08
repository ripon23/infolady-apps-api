<?php
class Deregistration extends CI_Controller {

	protected $urlBridgePrepaidUpdate = "http://vendorapi.aponjon.com.bd/lcprep/update/";
	protected $urlTokenApi = 'http://bridge.aponjon.com.bd/api/v1/login';
    protected $urlSearchApi = 'http://bridge.aponjon.com.bd/api/v1/is-mobile-exist/';
    protected $urlRegistrationApi = 'http://bridge.aponjon.com.bd/api/v1/subscriber/register';
    protected $urlDegistrationApi = 'http://bridge.aponjon.com.bd/api/v1/subscriber/deregister';
    protected $urlPrepaidDegistrationApi = 'http://vendorapi.aponjon.com.bd/lcprep/deregister/';
		
	function __construct()
	{
		parent::__construct();

		// Load the necessary stuff...
		$this->load->helper(array('language', 'url', 'form', 'date'));
		$this->load->model(array('general_model','account/account_model'));
		$this->load->library(array('form_validation','date', 'subscriber','mamalib'));
		date_default_timezone_set('Asia/Dhaka');  // set the time zone UTC+6
	}
	
	
	function index()
	{
		
		$is_error=0;
		$api_key=$this->input->post('api_key', TRUE);		
		
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
			
			$msisdn=$this->input->post('msisdn', TRUE);
			$msisdn  = empty($msisdn) ? NULL : $msisdn;
			
			$dereg_reason=$this->input->post('reason', TRUE);
			$msisdn  = empty($msisdn) ? NULL : $msisdn;
			
			
			## VALID MSISDN
			if(!$this->subscriber->isValidMobile($msisdn)){
				$response["success"] = 0;
				$response["message"] = 'INVALID MSISDN!';
				echo json_encode($response);
				$is_error=1;
				$error_msg='INVALID MSISDN!';
			}															
			
			## VALID REASON
			if(!$dereg_reason){
				$response["success"] = 0;
				$response["message"] = 'REASON SHOULD NOT EMPTY';
				echo json_encode($response);
				$is_error=1;
				$error_msg='REASON SHOULD NOT EMPTY';
			}
			
			
			if($is_error==1)
			{			
				$error_msg_data=array(						
					'error_msg'=>$error_msg,
					'status'=>1,						
					'comments'=>'ERRORS'	
				);			
				$this->general_model->update_table('apps_all_requests', $error_msg_data, 'request_id', $request_id);	
				die();
			}
			else
			{
			$error_msg=NULL;	
			}											
			
			
			## exists in t_subscribers
			$searchterm="SELECT * FROM t_subscribers WHERE tx_mobile='".$msisdn."' AND tx_status = 'Registered'";
			$exists_in_t_subscribers=$this->general_model->is_exist_in_a_table_querystring($searchterm);
			
			
			if($exists_in_t_subscribers)
			{
			# Prepaid or Paygo
			$searchterm2="Select * FROM t_subscribers WHERE tx_mobile='".$msisdn."' AND tx_status = 'Registered'";
			$all_sub_info=$this->general_model->get_all_single_row_querystring($searchterm2);
				if($all_sub_info->subscription_type=='PREPAID')
				{
				# Prepaid								
				$jp = array(
					'Subscriber_Id' =>  $all_sub_info->base_reg_id,
					'Subscriber_Type' => "Primary", 					
				);
				
				$json = json_encode($jp);
				$response = $this->bridgePost($this->urlPrepaidDegistrationApi, $json);
				$response = $this->cleanJson($response);
				$tmp = json_decode($response, true);
				
				if($tmp['errorCode']==200){					
					$tmp['errorDescription'];										
				  	
					$upd_data = array(
						'tx_status'=>'Deregistered',
						'tx_deregreason'=>$dereg_reason,
						'dtt_deregistration' => mdate('%Y-%m-%d %H:%i:%s', now()),
					  	'dtt_mod' => mdate('%Y-%m-%d %H:%i:%s', now()),
						'int_mod_user_key' => 999999, // for API
				  	);					
					
					$success=$this->general_model->update_table('t_subscribers', $upd_data, 'base_reg_id', $all_sub_info->base_reg_id);
						if($success)
						{
						$response1["success"] = 1;
						$response1["message"] = "Successfully deregistered";
												
						$success_msg_data=array(						
						'error_msg'=>'Successfully deregistered (Prepaid)',
						'status'=>1,						
						'comments'=>'SUCCESS'
						);			
						$this->general_model->update_table('apps_all_requests', $success_msg_data, 'request_id', $request_id);
						
						echo json_encode($response1);  // Output the success message
						
						}
						else
						{
						$response['success'] = 0;
						$response['message'] = "Database error in PMRS (t_subscribers)";
						echo json_encode($response);
						die();
						}
					
					}
	
				}
				else
				{
				# PAYGO
				$this->getApiToken();
				$data['request_channel'] = 'SUBSCRIBER DEREGISTRATION';
				$data['dereg_reason']  = 'SUBSCRIBER DEREGISTRATION';
				$data['mapping_reason'] = 'SUBSCRIBER DEREGISTRATION';								
		
					## DEREGISTER Primary Subscriber from Bridge if found
					if($this->isSubscriberAvailableInBridge($msisdn))
					{
						$request_channel="Primary";
						$json = $this->_deregisterFromBridge($msisdn, $request_channel, 'SUBSCRIBER DEREGISTRATION');
						$tmp = json_decode($json, true);
							if(isset($tmp['status']) && $tmp['status'] == 'success'){
								
								foreach($tmp['info'] as $t){
							   									
							   	if($msisdn == $t['msisdn']){								   									
								   	
								 	## 3. Update t_subscribers									
									
									$sql = "UPDATE t_subscribers
											SET tx_deregreason = '".$dereg_reason."'												
												, dtt_deregistration = '".date('Y-m-d H:i:s')."'
												, dtt_mod = '".date('Y-m-d H:i:s')."'
											WHERE int_subscriber_key = ".$all_sub_info->int_subscriber_key;
									
									$this->db->query($sql) or die($sql);
									 
									## Output
									$response1["success"] = 1;
									$response1["message"] = "Successfully deregistered";												
															
									$success_msg_data=array(						
									'error_msg'=>'Successfully deregistered (Paygo)',
									'status'=>1,						
									'comments'=>'SUCCESS'
									);			
									$this->general_model->update_table('apps_all_requests', $success_msg_data, 'request_id', $request_id);
									
									echo json_encode($response1);  // Output the success message
							   	}
								else
								{
								$response2["success"] = 0;
								$response2["message"] = "Opps! Something want wrong!";	
								echo json_encode($response2);
								}
							}	 
						}
					}
					else
					{
						$response["success"] = 0;
						$response["message"] = 'Data not found in bridge';
						echo json_encode($response);
						die();
					}
					
				}
				
			}
			else
			{							
			$error_msg_data=array(						
					'error_msg'=>'MSISDN is not exists or deregistered in PMRS',
					'status'=>1,						
					'comments'=>'ERRORS'	
			);			
			$this->general_model->update_table('apps_all_requests', $error_msg_data, 'request_id', $request_id);	
			$response["success"] = 0;
			$response["message"] = 'MSISDN is not exists or deregistered in PMRS (t_subscribers)';
			echo json_encode($response);
			die();	
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
	
	private function getApiToken()
    {
        if(!$this->session->userdata('api_token')){
            $json = $this->submitPostData($this->urlTokenApi, array('username'=>'bridge' ,'password'=>'6digit'));
            $tmp = json_decode($json, true);
            if(isset($tmp['_token'])){
                $this->session->set_userdata('api_token', $tmp['_token']);
            }
        }
    }
	
	public function isSubscriberAvailableInBridge($msisdn)
    {
        $json = $this->mamalib->submitPostData($this->urlSearchApi.$msisdn);
        $tmp = json_decode($json, true);
        return isset($tmp['status']) ? $tmp['status']: false;
    }
	
		
	
	public function cleanJson($raw_json){
        $start_pos = strpos($raw_json, '{');
        $end_pos = strrpos($raw_json, '}');
        return substr($raw_json, $start_pos, $end_pos-$start_pos+1);
    }	
	
	
	public function bridgePost($url, $json_string) 
    {
        $curl = curl_init($url);
        
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, "aponjonprepaid:aponjonprepaid123");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_string);
        curl_setopt($curl, CURLOPT_HTTPHEADER,
            array('Content-Type:application/json',
                'Content-Length: ' . strlen($json_string))
        );
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //IMP if the url has https and you don't want to verify source certificate
        
        $content = curl_exec($curl);
        curl_close($curl);
        return $content;
    }
				
				
}

/* End of file api.php */
