<?php
class Sync extends CI_Controller {

	
	
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
	echo "Wrong URL";	
	}
	
	
	function sync_status()
	{
										
		$data_source='APONJONAPPSV2';			
		
		## 	Sec:1 
		##	If API Key match insert the raw request in TABLE: apps_all_requests
		$raw_data=array(
			'raw_params'=>var_export($_POST, true),			
			'status'=>1,
			'data_source'=>$data_source,
			'comments'=>'Sync status req.',
			'received_datetime'=>mdate('%Y-%m-%d %H:%i:%s', now())					
			);
		
		$request_id=$this->general_model->save_into_table_and_return_insert_id('apps_all_requests', $raw_data);		
				
		
		
		$this->form_validation->set_rules(array(
			array(
				'field' => 'api_key',
				'label' => 'API Key',
				'rules' => 'trim|required'
			),			
			array(
				'field' => 'user_id',			// Required User id
				'label' => 'Tab User Id',
				'rules' => 'trim|required'
			)			
			
		));
		
		/************************************/        						
		
		//$subscriber_type=base64_decode('cGluTnVtYmVy');
		//echo $subscriber_type;
		 
		//die(); 
		# cc265005757 = Y2MyNjUwMDU3NTc=    , C02301120170823APONJONAPPSV2 =QzAyMzAxMTIwMTcwODIzQVBPTkpPTkFQUFNWMg==
		
		
		## Run form validation
		if ($this->form_validation->run() === TRUE)
		{				
			# username=Q0MyNjE2MDQ4MTE= Password=Q0MyNjE2MDQ4MTE=
			
			# Required Field
			
			$user_id=base64_decode($this->input->post('user_id', TRUE));
			$api_key =base64_decode($this->input->post('api_key', TRUE));									
			
			if ( ! $user = $this->account_model->get_by_username($user_id))   // Table : t_community_agent_login
			{
			$response["success"] = 0;
			$response["message"] = 'Username does not exist';
			echo json_encode($response);
			}
			else
			{
				if($api_key=='C02301120170823APONJONAPPSV2')
				{
				// cc265005757
				$query="SELECT s.`Status` as sync_status, COUNT(*) as sync_count FROM `e_subscriber` s WHERE s.`HWID`='".$user_id."' GROUP BY s.`Status`";
				$result=$this->general_model->get_all_querystring_result($query);						
					
					$response["data_sync_info"] = array(); 						
					$profile = array();						
					
					/*foreach($result as $re)
					{										
					$profile[$re->sync_status] =$re->sync_count;					
					array_push($response["data_sync_info"], $profile);																
					$response["success"] = 1;	
					}*/
					echo json_encode($result,true);
					die();	
					
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
