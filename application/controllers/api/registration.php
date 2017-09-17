<?php
class Registration extends CI_Controller {

	private $bridge_check_subscriber_url = "http://vendorapi.aponjon.com.bd/lcprep/chksubscriber/";
	private $bridge_new_subscription_url = "http://vendorapi.aponjon.com.bd/lcprep/subscribe/";
	private $bridge_renew_subscription_url = "http://vendorapi.aponjon.com.bd/lcprep/renew/";
	private $bridge_update_subscriber_url = "http://vendorapi.aponjon.com.bd/lcprep/update/";
	
	 private $_httpHeader = array(
        'Accept'            => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Encoding'   => 'gzip, deflate',
        'Accept-Language'   => 'en-US,en;q=0.5',
        'Connection'        => 'keep-alive',
        'Referer'           => 'http://api.aponjon.com.bd/',
        'Content-Type'      => 'application/x-www-form-urlencoded;charset=UTF-8',
    );
    private $_user_agent = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:19.0) Gecko/20100101 Firefox/19.0";
    private $_cookie_file = "cookie.txt";
	
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
				
		
		
		$this->form_validation->set_rules(array(
			array(
				'field' => 'subscriber_type', 	// [p,b] 	Field: STID
				'label' => 'Subscriber Type',
				'rules' => 'trim|required'
			),
			array(
				'field' => 'subscription_type',	// Required Subscription type 	Field: MobNum
				'label' => 'Subscription type [PAYGO/PREPAID]',
				'rules' => 'trim|required'
			),
			array(
				'field' => 'msisdn',			// Required Mobile No 	Field: MobNum
				'label' => 'MSISDN',
				'rules' => 'trim|required'
			),
			array(
				'field' => 'designeted_date',	// Required LMP/DOB Date 	Field: LMD, DOB, dt_lmd_dob
				'label' => 'LMP/DOB Date',
				'rules' => 'trim|required'
			),
			array(
				'field' => 'delivery_channel',	// s/r1/r2/r3/r4   	Field: OutdialPref
				'label' => 'Delivery Channel',
				'rules' => 'trim|required'
			),
			array(
				'field' => 'subscriber_name',	// Name 	Field: Name
				'label' => 'Subscriber Name',
				'rules' => 'trim|required'
			),
			array(
				'field' => 'age',				// Age Field: Age
				'label' => 'Age',
				'rules' => 'trim|required'
			),
			array(
				'field' => 'loc_district',		// Required District
				'label' => 'District',
				'rules' => 'trim|required'
			),
			array(
				'field' => 'loc_upazila',		// Required Upazila
				'label' => 'Upazila',
				'rules' => 'trim|required'
			),
			array(
				'field' => 'loc_union',			// Required Union
				'label' => 'Union',
				'rules' => 'trim'
			),
			array(
				'field' => 'want_guardian',		// Required Want guardian
				'label' => 'Guardian want one voice call?',
				'rules' => 'trim|required'
			),
			array(
				'field' => 'user_id',			// Required User id
				'label' => 'Tab User Id',
				'rules' => 'trim|required'
			)			
			
		));
		
		/************************************
        $json=	'{"card_info":[{"type":"SP|SB","amount":"500","service_period":"1000","serial":"100120"}],"success":1,"message":"Successful"}';
				
		$response = json_decode($json, true);
		echo "<pre>";
		print_r($response);
		echo "</pre>";
		
		if($response['success'] == 1){		
		echo $response['card_info'][0]['service_period'];			
		}		
		die();
		*************************************/
		
		
		//$subscriber_type=base64_encode('61435643');
		//echo $subscriber_type;
		 
		//die(); // p => cA== b=> Yg==   2017-07-01=>MjAxNy0wNy0wMQ==   01675794194=>MDE2NzU3OTQxOTQ=  r2=>cjI=
		# 23.8045332  =>MjMuODA0NTMzMg==   90.3513023 =>OTAuMzUxMzAyMw==<br />
		# PAYGO = UEFZR08=    PREPAID = UFJFUEFJRA==  URBAN = VVJCQU4=   123456 = MTIzNDU2   61435643 = NjE0MzU2NDM=
		
		
		## Run form validation
		if ($this->form_validation->run() === TRUE)
		{				
			# username=Q0MyNjE2MDQ4MTE= Password=Q0MyNjE2MDQ4MTE=
			
			# Required Field
			$subscriber_type=base64_decode($this->input->post('subscriber_type', TRUE)); // [p/b]
			$subscription_type=base64_decode($this->input->post('subscription_type', TRUE)); // [PAYGO/PREPAID]
			$dialect= base64_decode($this->input->post('dialect', TRUE)); // [RURAL/URBAN/CTG/SHY]
			$msisdn=base64_decode($this->input->post('msisdn', TRUE));
			$designeted_date=base64_decode($this->input->post('designeted_date', TRUE));						
			$delivery_channel=base64_decode($this->input->post('delivery_channel', TRUE));
			$subscriber_name=base64_decode($this->input->post('subscriber_name', TRUE));
			$age=base64_decode($this->input->post('age', TRUE));
			$loc_district=base64_decode($this->input->post('loc_district', TRUE));
			$loc_upazila=base64_decode($this->input->post('loc_upazila', TRUE));
			$loc_union=base64_decode($this->input->post('loc_union', TRUE));
			$want_guardian=base64_decode($this->input->post('want_guardian', TRUE));
			$user_id=base64_decode($this->input->post('user_id', TRUE));
			$latitude= base64_decode($this->input->post('latitude', TRUE));
			$longitude=base64_decode($this->input->post('longitude', TRUE));
			$pin =base64_decode($this->input->post('pin', TRUE));
			//$user_id=$this->input->post('user_id', TRUE);

			$district_info=$this->general_model->get_all_table_info_by_id('t_districts', 'int_district_key', $loc_district);
			$old_division_key=$district_info->old_division_key;
			
			if($subscriber_type=='p')
			{
			$lmp=$designeted_date;
			$dob=NULL;
			$lmp=date("dm", strtotime($lmp));
			$dt_lmd_dob=$designeted_date;
			}
			else
			{
			$dob=$designeted_date;	
			$lmp=NULL;
			$dob=date("dm", strtotime($dob));
			$dt_lmd_dob=$designeted_date;
			}									
			
			## VALID MSISDN
			if(!$this->subscriber->isValidMobile($msisdn)){
				$response["success"] = 0;
				$response["message"] = 'INVALID MSISDN!';
				echo json_encode($response);
				$is_error=1;
				$error_msg='INVALID MSISDN!';
				die();
			}
			
			## LMD/DOB VALIDATION
			if($subscriber_type=='b'){
				if(!$this->subscriber->isValidDegisnatedDate($dt_lmd_dob, 'b')){                    
					$response["success"] = 0;
					$response["message"] = 'INVALID DOB DATE';
					echo json_encode($response);
					$is_error=1;
					$error_msg='INVALID DOB DATE';
					die();
				}				
			}
			elseif($subscriber_type=='p')
			{
				if(!$this->subscriber->isValidDegisnatedDate($dt_lmd_dob, 'p')){                    
					$response["success"] = 0;
					$response["message"] = 'INVALID LMP DATE';
					echo json_encode($response);
					$is_error=1;
					$error_msg='INVALID LMP DATE';
					die();
				}
			}
			
			
			# Optional Field
			$guardian_msisdn=base64_decode($this->input->post('guardian_msisdn', TRUE));
			$guardian_msisdn  = empty($guardian_msisdn) ? NULL : $guardian_msisdn;
			
			$relation_with_guardian=base64_decode($this->input->post('relation_with_guardian', TRUE));
			$relation_with_guardian  = empty($relation_with_guardian) ? NULL : $relation_with_guardian;
			
			$owner_of_msisdn=base64_decode($this->input->post('owner_of_msisdn', TRUE));
			$owner_of_msisdn  = empty($owner_of_msisdn) ? NULL : $owner_of_msisdn;
			
			$phone_type=base64_decode($this->input->post('phone_type', TRUE));
			$phone_type  = empty($phone_type) ? NULL : $phone_type;
			
			$is_subscriber_family_head=base64_decode($this->input->post('is_subscriber_family_head', TRUE));
			$is_subscriber_family_head  = empty($is_subscriber_family_head) ? NULL : $is_subscriber_family_head;
			
			$is_subscriber_family_head=='Yes'? 'HHHead':'NonHHHead';
			
			$dialect= empty($dialect) ? 'URBAN' : $dialect;			
			
			$subscriber_occupation=base64_decode($this->input->post('subscriber_occupation', TRUE));
			$subscriber_occupation  = empty($subscriber_occupation) ? NULL : $subscriber_occupation;
			
			$subscriber_education_level=base64_decode($this->input->post('subscriber_education_level', TRUE));
			$subscriber_education_level  = empty($subscriber_education_level) ? NULL : $subscriber_education_level;
			
			$family_head_occupation=base64_decode($this->input->post('family_head_occupation', TRUE));
			$family_head_occupation  = empty($family_head_occupation) ? NULL : $family_head_occupation;
			
			$subscriber_nid=base64_decode($this->input->post('subscriber_nid', TRUE));
			$subscriber_nid  = empty($subscriber_nid) ? NULL : $subscriber_nid;
			
			$subscriber_nhid=base64_decode($this->input->post('subscriber_nhid', TRUE));
			$subscriber_nhid  = empty($subscriber_nhid) ? NULL : $subscriber_nhid;
			
			$family_monthly_income=base64_decode($this->input->post('family_monthly_income', TRUE));
			$family_monthly_income  = empty($family_monthly_income) ? NULL : $family_monthly_income;
			
			$family_monthly_expense=base64_decode($this->input->post('family_monthly_expense', TRUE));
			$family_monthly_expense  = empty($family_monthly_expense) ? NULL : $family_monthly_expense;
			
			$school_going_children_earning=base64_decode($this->input->post('school_going_children_earning', TRUE));
			$school_going_children_earning  = empty($school_going_children_earning) ? NULL : $school_going_children_earning;
			
			$latitude=base64_decode($this->input->post('latitude', TRUE));
			$latitude= empty($latitude) ? NULL : $latitude;
						
			$longitude=base64_decode($this->input->post('longitude', TRUE));
			$longitude= empty($longitude) ? NULL : $longitude;
			
			if($school_going_children_earning=='Yes')
			$HasChildlabour=1;
			else
			$HasChildlabour=0;
			
			$GVCode=NULL;
			$GuardiansNationalID=NULL;
			$OSIDFamilyHead=NULL;
			$BloodGroup=NULL;
			$YearsOfEducation=NULL;
			$sanitaryLatrinAvailability=NULL;
			$SrcDrnkWater=NULL;
			$NumberOfMobilePhoneAtHome=NULL;
			
			if ( ! $user = $this->account_model->get_by_username($user_id))   // Table : t_community_agent_login
			{
			$response["success"] = 0;
			$response["message"] = 'Username does not exist';
			echo json_encode($response);
			}
			else
			{
				######################## Check availability ###################
				$query="SELECT COUNT(*) AS total_rows FROM `t_subscribers` WHERE tx_mobile='".$msisdn."' AND tx_status='Registered'";	
				$total_rows=$this->general_model->count_total_rows($query);
				if($total_rows>0)
				{
				# Already exists
				$update_info=array(
				'error_msg'=>'Mobile already exists',  // in pmrs t_subscribers table
				'comments'=>'ERROR',
				'status' =>1
				);
				
				$this->update_apps_all_requests_table($request_id,$update_info);
				
				$response["success"] = 0;
				$response["message"] = "Mobile already exists";
				echo json_encode($response);
				die();	
				}
				######################### END Check availability ################
				
				$date = date('Y-m-d H:i:s');
				
				if($subscription_type=='PAYGO')
				{	
				$insert = array(
                          'MobNum'                      => $msisdn
                        , 'STID'                        => $subscriber_type
						, 'subscription_type'			=> $subscription_type
						, 'DialectID'					=> $dialect
                        , 'LMD'                         => $lmp
                        , 'DOB'                         => $dob
						, 'dt_lmd_dob'					=> $dt_lmd_dob
                        , 'OutdialPref'                 => $delivery_channel
                        , 'GuardianNum'                 => $guardian_msisdn
                        , 'HWID'                        => $user_id
                        , 'GVCode'                      => $GVCode
                        , 'RelationWithGuardian'        => $relation_with_guardian
                        , 'Name'                        => $subscriber_name
                        , 'Age'                         => $age
                        , 'SubsNationalID'              => $subscriber_nid
                        , 'HSID'                        => $is_subscriber_family_head
                        , 'DID'                         => $old_division_key
                        , 'DSID'                        => $loc_district
                        , 'UID'                         => $loc_upazila
						, 'UNID'                        => $loc_union
                        , 'OSID'                        => $subscriber_occupation
                        , 'TFIncome'                    => $family_monthly_income
                        , 'MExpense'                    => $family_monthly_expense
                        , 'GuardiansNationalID'         => $GuardiansNationalID
                        , 'OSIDFamilyHead'              => $OSIDFamilyHead
                        , 'BloodGroup'                  => $BloodGroup
                        , 'EDID'                        => $subscriber_education_level
						, 'HasChildlabour'				=> $HasChildlabour
                        , 'YearsOfEducation'            => $YearsOfEducation
                        , 'sanitaryLatrinAvailability'  => $sanitaryLatrinAvailability
                        , 'SrcDrnkWater'                => $SrcDrnkWater
                        , 'NumberOfMobilePhoneAtHome'   => $NumberOfMobilePhoneAtHome
						, 'MobileType'   				=> $phone_type
                        , 'WhichCellWillReceiveContent' => $owner_of_msisdn
                        , 'DialectID'                   => 'RURAL'                    
                        , 'Status'                      => 'Verified'
                        , 'is_active'                   => 1
                        , 'dtt_mod'                     => $date
                        , 'dtt_create'                  => $date
                        , 'int_mod_user_key'            => 100001 // for tab user use this number as previous, dont know why
                        , 'tx_data_source'            	=> 'tab'
						, 'latitude'            		=> $latitude
						, 'longitude'            		=> $longitude
                );
			
				//print_r($insert);
				//die();
				
				$e_insert_id=$this->general_model->save_into_table_and_return_insert_id('e_subscriber', $insert);
				
				$status_insert = array(
							  'int_subscriber_id'   => $e_insert_id
							, 'tx_status'           => 'Verified'
							, 'int_mod_user'        => 100001  // for tab user use this number as previous, dont know why 
							, 'dtt_mod'             => $date
					);
				$this->general_model->save_into_table('e_subscriber_status', $status_insert);
				
				$update_data=array(						
						'server_reg_id'=>$e_insert_id,
						'status'=>1,						
						'comments'=>'e_subscriber'	
					);			
				$this->general_model->update_table('apps_all_requests', $update_data, 'request_id', $request_id);				
				
				$response["reg_info"] = array(); 						
				$profile = array();						
				$profile["server_reg_id"] ="e_".$e_insert_id;
				$profile["insert_datetime"] =$date;
				array_push($response["reg_info"], $profile);																
				$response["success"] = 1;									
				// JSON response
				echo json_encode($response);
				}
				
				if($subscription_type=='PREPAID')
				{
					
					
					$json_response = $this->_isValidPincode($pin, $msisdn);
					
					$api_response = json_decode($json_response, true);					
					
					//print_r($api_response);
					//die();
					
					if($api_response['success'] == 1){													
					//echo $api_response['card_info'][0]['service_period'];																								
					
					$service_period= $api_response['card_info'][0]['service_period'];
					$subscriber_type='Primary';
					$subscription_date=mdate('%Y-%m-%d %H:%i:%s', now());
					$deactivation_date=date('Y-m-d 23:59:59', strtotime($subscription_date. "+{$service_period} days"));
					
					$package=$subscriber_type=='b'?'sb':'sp';
					
					$json = '{"MSISDN": "'.$msisdn.'",
					"Designated_Date": "'.$designeted_date.'",
					"Package": "'.$package.'", 
					"Dialect": "'.$dialect.'", 
					"Timeslot": "'.$delivery_channel.'", 
					"Subscriber_Type": "'.$subscriber_type.'",
					"Subscription_Date": "'.$subscription_date.'", 
					"Deactivation_Date": "'.$deactivation_date.'"}';
									
					$bridge_response = $this->bridgePost($this->bridge_new_subscription_url, $json);
					$start_pos = strpos($bridge_response, '{');
					$end_pos = strrpos($bridge_response, '}');
					$bridge_response=substr($bridge_response, $start_pos, $end_pos-$start_pos+1);
					$tmp = json_decode($bridge_response, true);
					
					//print_r($tmp);
					
					if(200 == $tmp['errorCode']){
					$bridge_primary_reg_id = sprintf('%0.0f', $tmp['sub_id']);																													
					}
					else
					{
					$response["success"] = 0;				
					$response["message"] = $tmp['errorDescription'].'. Unable to register in bridge database';
					
					
					# Update status in apps_all_requests table
					$error_msg_data=array(						
							'error_msg'=>$tmp['errorDescription'].'. Unable to register in bridge database',
							'comments'=>'ERROR',
							'status' =>1
					);
					$this->general_model->update_table('apps_all_requests', $error_msg_data, 'request_id', $request_id);
					
					$response["success"] = 0;
					$response["message"] = $tmp['errorDescription'].'. Unable to register in bridge database';
					echo json_encode($response);
					die();	
					}
					
					##### t_subscriber insert #################
					# Register to t_subscriber
					if($bridge_primary_reg_id)
					{				
						if($dob)
						{
						$dt_child_birth=$dt_lmd_dob;	
						$dt_lmp=NULL;
						$t_dob=$dob.substr($dt_lmd_dob,2,2); //2017-08-30
						$dt_dob=$dt_lmd_dob;
						$int_subscriber_type_key=2; //b
						}
						else
						{
						$dt_child_birth=NULL;	
						$dt_lmp=$dt_lmd_dob;
						$dt_dob=NULL;
						$t_dob=NULL;
						$t_lmp=$lmp.substr($dt_lmd_dob,2,2); //2017-08-30
						$int_subscriber_type_key=1; //p
						}
						if($delivery_channel='s')
						{
						$tx_distribution_channel='SMS';	
						$int_timeslot=NULL;
						}
						else
						{
						$tx_distribution_channel='IVR';	
						$int_timeslot=str_replace('r', '', $delivery_channel);	
						}
						
						
						$primary_entry = array(
						'tx_reg_id' => $bridge_primary_reg_id,
						'tx_ref_reg_id' => null,
						'tx_name'=>$subscriber_name,
						'tx_mobile' => $msisdn,
						'tx_gurdian_mobile' => null,
						'tx_last_menstrual_period' => $t_lmp,
						'dt_last_menstrual_period' => $dt_lmp,
						'tx_child_birth' => $t_dob,
						'dt_child_birth' => $dt_dob,
						'dtt_week_base' => $dt_child_birth,
						'dtt_registration' => $subscription_date,
						'dtt_service_deactivation' => $deactivation_date,
						'dt_prepaid_service_from' => $subscription_date,
						'dt_prepaid_service_upto' => $deactivation_date,
						'subscription_type' => 'PREPAID',
						'tx_distribution_channel' => $tx_distribution_channel,
						'int_timeslot' => $int_timeslot,
						'tx_dialect' => strtoupper($dialect),
						'int_subscriber_type_key' => $int_subscriber_type_key,
						'is_active' => 1,
						'int_guardian_content_same_mobile' => 0,
						'tx_status' => 'Registered',
						'tx_healthworker_id'=>$user_id,
						'base_reg_id' => $bridge_primary_reg_id,
						);
						
					$t_insert_id=$this->general_model->save_into_table_and_return_insert_id('t_subscribers', $primary_entry);
										
					$update_data=array(						
						'server_reg_id'=>$t_insert_id,
						'status'=>1,						
						'comments'=>'t_subscriber'	
					);											
					$this->update_apps_all_requests_table($request_id,$update_data);
					
					
					$json_response = $this->_pinCodeRechargeRequest($pin, $msisdn);
					
					$api_response = json_decode($json_response, true);					
					
					if($api_response['success'] == 1){						
					$response["reg_info"] = array(); 						
					$profile = array();						
					$profile["server_reg_id"] ="t_".$t_insert_id;
					$profile["insert_datetime"] =$date;
					array_push($response["reg_info"], $profile);																
					$response["success"] = 1;									
					// JSON response
					echo json_encode($response);	
					}
					
					
					}
					
					
					############################################
					### Card Recharde
					
					
					
					### Card recharge END
					
					} // Recharge is successful
					else
					{
					
					# Update status in apps_all_requests table
					$error_msg_data=array(						
							'error_msg'=>"PIN is wrong",
							'comments'=>'ERROR',
							'status' =>1
					);
					$this->general_model->update_table('apps_all_requests', $error_msg_data, 'request_id', $request_id);	
					
					$response["success"] = 0;
					$response["message"] = "PIN is wrong";
					echo json_encode($response);
					}
				}
			}
		}
		else
		{
			$response["success"] = 0;
			$response["message"] = "Error: ".str_replace("\n",'',strip_tags(validation_errors()));
			echo json_encode($response);
		}
	
	}
	
	public function update_apps_all_requests_table($request_id,$update_info)
	{	
	$success = $this->general_model->update_table('apps_all_requests', $update_info, 'request_id', $request_id);		
	if($success)
	return true;
	else
	return false;
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
	
	## PREPAID API 1
	private function _isValidPincode($pin, $msisdn='')
	{
		$url = 'http://prepaid.aponjon.com.bd/api/prepaid/card_validation';
		$data = array(
			'pin'       => $pin,
			'api_key'   => 'APONJON02301120170213V1',
		);
		
		$ch = curl_init($url);
        $options = array(
            CURLOPT_CONNECTTIMEOUT 		=> 3000,
            CURLOPT_USERAGENT 			=> $this->_user_agent,
            CURLOPT_AUTOREFERER 		=> true,
            CURLOPT_RETURNTRANSFER 		=> true,            
            CURLOPT_FOLLOWLOCATION 		=> true,
            CURLOPT_POST                => true,
            CURLOPT_POSTFIELDS          => $data,
            CURLOPT_COOKIESESSION       => true,
            CURLOPT_COOKIEFILE 			=> $this->_cookie_file,
            CURLOPT_COOKIEJAR 			=> $this->_cookie_file,
            CURLOPT_SSL_VERIFYPEER 		=> false,
            CURLOPT_SSL_VERIFYHOST 		=> 0,
            CURLOPT_ENCODING            => "UTF-8",
            CURLOPT_HTTPHEADER          => $this->_httpHeader,
        );
        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        curl_close($ch);
        return $content;				
	}
	
	## PREPAID API 2
	private function _pinCodeRechargeRequest($pin, $msisdn){
		$url = 'http://prepaid.aponjon.com.bd/api/prepaid/card_recharge';
		$data = array(
			'api_key'   => 'APONJON02301120170213V1',
			'pin'       => $pin,
			'mobile'    => $msisdn,
		);
		
		
		//$json = $this->utility->httpPost($url, $data);
		
		$ch = curl_init($url);
        $options = array(
            CURLOPT_CONNECTTIMEOUT 		=> 3000,
            CURLOPT_USERAGENT 			=> $this->_user_agent,
            CURLOPT_AUTOREFERER 		=> true,
            CURLOPT_RETURNTRANSFER 		=> true,            
            CURLOPT_FOLLOWLOCATION 		=> true,
            CURLOPT_POST                => true,
            CURLOPT_POSTFIELDS          => $data,
            CURLOPT_COOKIESESSION       => true,
            CURLOPT_COOKIEFILE 			=> $this->_cookie_file,
            CURLOPT_COOKIEJAR 			=> $this->_cookie_file,
            CURLOPT_SSL_VERIFYPEER 		=> false,
            CURLOPT_SSL_VERIFYHOST 		=> 0,
            CURLOPT_ENCODING            => "UTF-8",
            CURLOPT_HTTPHEADER          => $this->_httpHeader,
        );
        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        curl_close($ch);
        return $content;		
		//$response = json_decode($content);
		
		
	}	
				
}

/* End of file api.php */
