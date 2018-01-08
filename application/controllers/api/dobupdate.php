<?php
class Dobupdate extends CI_Controller {

	protected $urlBridgePrepaidUpdate = "http://vendorapi.aponjon.com.bd/lcprep/update/";
	protected $urlTokenApi = 'http://bridge.aponjon.com.bd/api/v1/login';
    protected $urlSearchApi = 'http://bridge.aponjon.com.bd/api/v1/is-mobile-exist/';
    protected $urlRegistrationApi = 'http://bridge.aponjon.com.bd/api/v1/subscriber/register';
    protected $urlDegistrationApi = 'http://bridge.aponjon.com.bd/api/v1/subscriber/deregister';
		
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
		$api_key=$this->input->post('api_key', TRUE);  // QzAyMzAxMTIwMTcwNzExQ0hBVEJPVA==
		
		if(($api_key=='C02301120170823APONJONAPPSV2'))
		{				
		$data_source='APONJONAPPSV2';			
		
		## 	Sec:1 
		##	If API Key match insert the raw request in TABLE: chatbot_all_requests
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
			
			// ref_reg_id may not have few occasion
			// $ref_reg_id=$this->input->post('ref_reg_id', TRUE);  
			// $ref_reg_id  = empty($ref_reg_id) ? NULL : $ref_reg_id;							
			
			$dob=$this->input->post('dob', TRUE);
			$dob  = empty($dob) ? NULL : $dob;
			
			$msisdn=$this->input->post('msisdn', TRUE);
			$msisdn  = empty($msisdn) ? NULL : $msisdn;
			
			$current_package='b';
									
			## REGISTRATION ID IS REQUIRED
			/*
			if(!$ref_reg_id)
			{
				$response["success"] = 0;
				$response["message"] = 'REGISTRATION ID IS REQUIRED!';
				echo json_encode($response);
				$is_error=1;
				$error_msg='REGISTRATION ID IS REQUIRED!';
			}																																											
			*/
			
			## INVALID REGISTRATION ID;
			/*
			if($ref_reg_id && $is_error!=1)
			{
				if(!$this->general_model->is_exist_in_a_table('chatbot_action_requests','success_reg_id',$ref_reg_id))
				{
				$response["success"] = 0;
				$response["message"] = 'REGISTRATION ID NOT FOUND!';
				echo json_encode($response);
				$is_error=1;
				$error_msg='REGISTRATION ID NOT FOUND!';
				}
			}
			*/
			
			## DOB IS REQUIRED
			if(!$dob && $is_error!=1)
			{
			$response["success"] = 0;
			$response["message"] = 'DOB IS REQUIRED!';
			echo json_encode($response);
			$is_error=1;
			$error_msg='DOB IS REQUIRED!';
			}
			
			## DOB VALIDATION
			if($current_package=='b' && $is_error!=1){
				if(!$this->subscriber->isValidDegisnatedDate($dob, 'b')){                    
					$response["success"] = 0;
					$response["message"] = 'INVALID DOB DATE';
					echo json_encode($response);
					$is_error=1;
					$error_msg='INVALID DOB DATE';
				}				
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
					'Sub_Id' =>  $all_sub_info->base_reg_id,
					'MSISDN' => $msisdn, 
					'Package' => $all_sub_info->int_subscriber_type_key==7?'sp':'sb',
					'New_Value' => array(
						'Name' => '', 
						'Designated_Date' => $dob.' 00:00:00', 
						'Subscriber_Type' => '', 
						'Timeslot' => '',
						'Dialect' => '', 
						'Package' => 'sb',
					),
				);
				
				$json = json_encode($jp);
				$response = $this->bridgePost($this->urlBridgePrepaidUpdate, $json);
				$response = $this->cleanJson($response);
				$tmp = json_decode($response, true);
				
				if($tmp['errorCode']==200){
					$data['package_expire_date'] = isset($tmp['package_expire_date']) ? $tmp['package_expire_date'] : null;
					//$this->subscribermodel->updPrepaidSubscriberInfo($data, 'Primary');
					
					$int_subscriber_type_key = 8; // 8=sb
				  	$upd_data = array(
						'int_subscriber_type_key' => $int_subscriber_type_key,
						'tx_child_birth' => substr($dob, 8,2).substr($dob, 5,2).substr($dob, 2,2),
            			'dt_child_birth' => $dob,
					  	'dtt_mod' => mdate('%Y-%m-%d %H:%i:%s', now()),
						'int_mod_user_key' => 999999, // for API
				  	);					
					
					$success=$this->general_model->update_table('t_subscribers', $upd_data, 'base_reg_id', $all_sub_info->base_reg_id);
						if($success)
						{
						$response1["success"] = 1;
						$response1["message"] = "DOB Updated Successfully";												
												
						$success_msg_data=array(						
						'error_msg'=>'DOB Updated Successfully (Prepaid)',
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
				$data['request_channel'] = 'SUBSCRIBER UPDATE';
				$data['dereg_reason']  = 'SUBSCRIBER UPDATE';
				$data['mapping_reason'] = 'SUBSCRIBER UPDATE';
				
				$newRegInfo = array();
				$apiErrors = '';
				$chkDateInvalidResaon = '';
		
					## DEREGISTER Primary Subscriber from Bridge if found
					if($this->isSubscriberAvailableInBridge($msisdn))
					{
						$request_channel="Primary";
						$json = $this->_deregisterFromBridge($msisdn, $request_channel, 'DOB UPDATE');
						$tmp = json_decode($json, true);
							if(isset($tmp['status']) && $tmp['status'] == 'success'){
								foreach($tmp['info'] as $t){
							   	## RE-REGISTER in Bridge
							   	if($msisdn == $t['msisdn']){
								   	
								$data['MSISDN']= $msisdn;
								$data['Designated_Date']=$dob.' 00:00:00';								
								$data['Package']="sb";
								$data['Subscriber_Type']= "Primary";																
								
								$data['Dialect']= $all_sub_info->tx_dialect;
								$data['Timeslot']= 'R'.$all_sub_info->int_timeslot;
								$data['Subscription_Date']= $all_sub_info->dtt_registration;
								$data['Deactivation_Date']= $all_sub_info->dtt_service_deactivation;
	
									## 2.1 Re-Register Primary Subscriber in Bridge
								   	$newRegInfo['primary'] = $this->_registerPrimarySubscriberToBridge($data, '2.1');
								   	
								 	## 3. Update subscriber's new RegID
									//$this->subscribermodel->updSubsNewRegId($data, $newRegInfo, $date);
									
									$tx_last_menstrual_period = '';
                					$tx_child_birth = substr($dob, 8,2).substr($dob, 5,2).substr($dob, 2,2);
                					$dt_last_menstrual_period = null;
                					$dt_child_birth = $dob;
									
									$sql = "UPDATE t_subscribers
											SET tx_reg_id = '".$this->sanitiseRegId($newRegInfo['primary']['regId'])."'
												, tx_ref_reg_id = '".$this->sanitiseRegId($newRegInfo['guardian']['regId'])."'
												, $tx_child_birth = '".$tx_child_birth."'
												, $dt_child_birth = '".$dob."'
												, dtt_week_base = '".$dob." 00:00:00'
												, int_subscriber_type_key = 2
												, dtt_update_preg_to_baby = '".date('Y-m-d H:i:s')."'
												, dtt_mod = '".date('Y-m-d H:i:s')."'
											WHERE int_subscriber_key = ".$all_sub_info->int_subscriber_key;
									
									$this->db->query($sql) or die($sql);
									 
									## Output
									$response1["success"] = 1;
									$response1["message"] = "DOB Updated Successfully";												
															
									$success_msg_data=array(						
									'error_msg'=>'DOB Updated Successfully (Paygo)',
									'status'=>1,						
									'comments'=>'SUCCESS'
									);			
									$this->general_model->update_table('apps_all_requests', $success_msg_data, 'request_id', $request_id);
									
									echo json_encode($response1);  // Output the success message
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
			
			
			
			
			## ALL VALID
			/*$server_reg_id=mdate('%Y%m%d%H%i%s', now()).$request_id;
				
				$raw_data=array(
				'request_id'=>$request_id,	
				'ref_reg_id'=>$ref_reg_id,						
				'dob'=>$dob,
				'current_package'=>$current_package,
				'api_channel'=>'DOB_UPDATE',
				'request_status'=>'NEW',
				'success_reg_id'=>$server_reg_id,
				'create_datetime'=>mdate('%Y-%m-%d %H:%i:%s', now())
				);		
				$if_succeed=$this->general_model->save_into_table_and_return_insert_id('chatbot_action_requests', $raw_data);							
				
				if($if_succeed)
				{
				$response["reg_info"] = array(); 						
				$profile = array();						
				$profile["server_reg_id"] =$server_reg_id;				
				array_push($response["reg_info"], $profile);																
				$response["success"] = 1;									
				// JSON response
				echo json_encode($response);
				
				## UPDATE TABLE: chatbot_all_requests
				$error_data=array(
					'error_msg'=>json_encode($response),			
					'status'=>1					
				);
				$this->general_model->update_table('chatbot_all_requests', $error_data, 'request_id', $request_id);																	
				
				}*/
				
				
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
	
	function dobcancel()
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
			$hw_user_id = $this->input->post('user_id', TRUE);
			$hw_user_id = empty($hw_user_id) ? NULL : $hw_user_id;
			
			$msisdn= $this->input->post('msisdn', TRUE);
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
			if(!$hw_user_id){
				$response["success"] = 0;
				$response["message"] = 'USERID SHOULD NOT EMPTY';
				echo json_encode($response);
				$is_error=1;
				$error_msg='USERID SHOULD NOT EMPTY';
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
				  	
					$upd_data = array(
						'dobupdatecancel_byhw'=>$hw_user_id,
						'isdobupdatecancel'=>1,
						'dobupdatecancel_reason'=>$dereg_reason,
						'dobupdatecancel_time' => mdate('%Y-%m-%d %H:%i:%s', now()),
						'int_mod_user_key' => 999999, // for API
				  	);					
					
					$success=$this->general_model->update_table('t_subscribers', $upd_data, 'base_reg_id', $all_sub_info->base_reg_id);
						if($success)
						{
						$response1["success"] = 1;
						$response1["message"] = "Successfully Cancel";
												
						$success_msg_data=array(						
						'error_msg'=>'Successfully Cancel',
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
	
	
	private function _registerPrimarySubscriberToBridge($data, $sl='')
    {
        $newRegInfo = array('regId' => '', 'msisdn' => '');
        $regData = $this->prepNewRegSubscriberBridgeData($data, 'NEW_MOTHER', 'primary');        
        $json = $this->submitPostData($this->urlRegistrationApi, $regData);
       
        $tmp = json_decode($json, true);
        if($tmp['status']=='success'){
            $newRegInfo = array(
                'regId' => "{$tmp['subscriber_id']}", 
                'msisdn' => $tmp['msisdn']
            );
        }
        
        return $newRegInfo;
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
	
	private function _deregisterFromBridge($msisdn, $reqChannel, $deregReason, $sl='')
    {
        $deregData = array(
            '_token'            => $this->session->userdata('api_token'),
            'msisdn'            => $msisdn,
            'request_channel'   => $reqChannel,
            'dereg_reason'      => $deregReason,
        );
        
        $json = $this->submitPostData($this->urlDegistrationApi, $deregData);        
        return $json;
    }
	
	
	
	
	public function isSubscriberAvailableInBridge($msisdn)
    {
        $json = $this->mamalib->submitPostData($this->urlSearchApi.$msisdn);
        $tmp = json_decode($json, true);
        return isset($tmp['status']) ? $tmp['status']: false;
    }
	
	
	public function submitPostData($url, $postData=array(), $isNewSession=0) 
	{
        
        $ch = curl_init($url);        
        $options = array(
            CURLOPT_CONNECTTIMEOUT 	=> 3000,
            CURLOPT_USERAGENT 		=> "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:19.0) Gecko/20100101 Firefox/19.0",
            CURLOPT_AUTOREFERER 	=> true,
            CURLOPT_RETURNTRANSFER 	=> true,            
            CURLOPT_FOLLOWLOCATION 	=> true,
            //CURLOPT_POST              => true,
            //CURLOPT_POSTFIELDS        => $postData,
            CURLOPT_COOKIESESSION       => true,
            CURLOPT_COOKIEFILE 		=> getcwd().'/request_cookie.txt',
            CURLOPT_COOKIEJAR 		=> getcwd().'/request_cookie.txt',
            CURLOPT_SSL_VERIFYPEER 	=> false,
            CURLOPT_SSL_VERIFYHOST 	=> 0,
            CURLOPT_ENCODING            => "UTF-8",
            CURLOPT_HTTPHEADER          => array(
                                                'Accept'            => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                                                'Accept-Encoding'   => 'gzip, deflate',
                                                'Accept-Language'   => 'en-US,en;q=0.5',
                                                'Connection'        => 'keep-alive',
                                                'Referer'           => 'http://forms.aponjon.com.bd/',
                                                'Content-Type'      => 'application/x-www-form-urlencoded;charset=UTF-8',
                                            ),
        );
        
        if(!empty($postData)){
            $options[CURLOPT_POST] = 'CURLOPT_POST'; //true;
            $options[CURLOPT_POSTFIELDS] = $postData;
        }
        
        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        curl_close($ch);

        return $content;
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
	
	private function sanitiseRegId($regId)
    {
        if(strlen($regId)>0){
            $prefix = strtolower(substr($regId, 0, 3));
            if($prefix !== 'reg'){
                $regId = sprintf('%0.0f', $regId);
            }
        }
        return $regId;
    }
	
	private function prepNewRegSubscriberBridgeData($data, $package, $type='primary')
    {
        
        if($type=='primary'){
            $arr = array(
                '_token'                      => $this->session->userdata('api_token'),
                'request_channel'             => $data['request_channel'], // (string)    length: 45, Example: form, tab etc
                'name'                        => !empty($data['subs_name']) ? $data['subs_name'] : '?', // (string)    length: 45
                'msisdn'                      => $data['subs_mobile_number'], // (string)    length: 11, Example: 01717500400
                'primary_msisdn'              => '', // (string)    length: 11, Example: 01717500400
                'designated_date'             => $data['dt_lmd_dob'],
                'package'                     => $package, // 'NEW_MOTHER', // (Enum)      PREG, PREG_GUARDIAN, NEW_MOTHER, NEW_MOTHER_GUARDIAN, SHOISHOB
                'age'                         => (int) $data['subs_age'], // (int)   
                'dialect'                     => $data['dialect'], // (Enum)      URBAN, RURAL
                'subscriber_type'             => 'PRIMARY', // (Enum)      PRIMARY, GUARDIAN
                'is_free'                     => 0, // (bool)      1, 0
                'receive_guardian_content'    => $data['subs_mobile']==$data['guardian_mobile'] ? 1: 0, // (bool)      1, 0    
                'delivery_channel'            => $data['channel'], // (Enum)      SMS, IVR
                'timeslot'                    => $data['channel']=='SMS' ? 'SMS' : 'R'.$data['timeslot'], // (Enum)      SMS, R1, R2, R3, R4
            );
        } else{
            $arr = array(
                '_token'                      => $this->session->userdata('api_token'),
                'request_channel'             => $data['request_channel'], // (string)    length: 45, Example: form, tab etc
                'name'                        => !empty($data['gurdian_name']) ? $data['gurdian_name'] : '?', // (string)    length: 45
                'msisdn'                      => $data['guardian_mobile_number'], // (string)    length: 11, Example: 01717500400
                'primary_msisdn'              => $data['subs_mobile_number'], // (string)    length: 11, Example: 01717500400
                'designated_date'             => $data['dt_lmd_dob'],
                'package'                     => $package, // 'NEW_MOTHER_GUARDIAN', // (Enum)      PREG, PREG_GUARDIAN, NEW_MOTHER, NEW_MOTHER_GUARDIAN, SHOISHOB
                'age'                         => 0, // (int)   
                'dialect'                     => $data['dialect'], // (Enum)      URBAN, RURAL
                'subscriber_type'             => 'GUARDIAN', // (Enum)      PRIMARY, GUARDIAN
                'is_free'                     => 0, // (bool)      1, 0
                'receive_guardian_content'    => 0, // (bool)      1, 0    
                'delivery_channel'            => $data['channel'], // (Enum)      SMS, IVR
                'timeslot'                    =>  $data['channel']=='SMS' ? 'SMS' : 'R'.$data['timeslot'], // (Enum)      SMS, R1, R2, R3, R4
            );
        }
        
        return $arr;
    }
				
}

/* End of file api.php */
