<?php

/**
 * Description of Utility
 * 
 * @author Md. Rafiqul Islam <rafiq.kuet@gmail.com>
 * @date Feb 28, 2017 01:01
 */

defined('BASEPATH') OR exit('No direct script access allowed');


class Subscriber {
    public $obj = null;
    
    public function __construct()
    {
        $this->obj =& get_instance();
    }
    
    public function prepaidPackages()
    {
        return [
            'SP' => 'Shogorbha Pregnant',
            'SB' => 'Shogorbha Baby',
            'SH' => 'Shoishob',
        ];
    }
    
    public function listSubscriberTypes()
    {
        $data = array(
            'Primary' => array(
                1 => 'P',
                2 => 'B',
                7 => 'SP',
                8 => 'SB',
                9 => 'SH',
            ),
            'Gatekeeper' => array(
                3 => 'Generic Guardian',
                4 => 'Husband',
                5 => 'Husband',
                6 => 'Mother-in-Law',
            ),
        );
        
        return $data;
    }
    
    public function timeSlots(){
        return [
            //$this->config->item('SMS_CHANNEL') => ['S'],
            $this->obj->config->item('IVR_CHANNEL') => ['R1', 'R2', 'R3', 'R4'],
        ];
    }
    
    public function isValidMobile($mobile)
	{
		preg_match('/^01[798651][0-9]{8}$/', $mobile, $matches);
        return count($matches)>0 ? TRUE : FALSE;
	}
    
    public function isSubscriberRegistered($msisdn, $package='sb', $subscriber_type='Primary')
    {
        $url = $this->obj->config->item('BRIDGE_CHECK_SUBSCRIBER_URL');
        $json = '{"MSISDN": "'.$msisdn.'",
"Package": "'.$package.'",
"Subscriber_Type": "'.$subscriber_type.'"}';

        $response = $this->obj->utility->bridgePost($url, $json);
        $response = json_decode($response, TRUE);
        
        return $response['sub_exists']==1 ? TRUE : FALSE;
    }
    
    public function getIsFreeParam($subscription_type) {
        $is_free_param = $subscription_type == 'PREPAID' ? 1 : 0;
        return $is_free_param;
    }
    
    public function getPackageName($package_id){
        $packages = $this->prepaidPackages();
        return isset($packages[$package_id]) ? $packages[$package_id] : '';
    }
    
    public function getBridgePackageName($package_id, $subscriber_type='primary'){
        $packages = [
          'SP' => [
              'primary' => 'SHOGORBHA_1000_PREG',
              'guardian' => 'SHOGORBHA_1000_PREG_GUARDIAN',
          ],
          'SB' => [
              'primary' => 'SHOGORBHA_1000_BABY',
              'guardian' => 'SHOGORBHA_1000_BABY_GUARDIAN',
          ],
          'SH' => [
              'primary' => 'SHOISHOB_2_5',
              'guardian' => NULL,   // No guardian Shoishob
          ],
        ];
        
        return isset($packages[$package_id][$subscriber_type]) ? $packages[$package_id][$subscriber_type] : NULL;
    }
    
    public function isValidPackage($package){
        $packages = array_keys($this->prepaidPackages());
        return in_array($package, $packages) ? true : false;
    }
    
    public function isValidTimeslot($timeslot){
        $timeslots = $this->timeSlots();
        foreach($timeslots as $ts){
            if(in_array($timeslot, $ts)){
                return true;
            }
        }
        return false;
    }
    
    public function isValidDegisnatedDate($date, $package){
        $retval = true;
        if(!$this->obj->date->isValidDate($date)){
            $retval = false;
        } elseif($this->obj->date->isFutureDate($date)){
            $retval = false;
        } else{
            $days = $this->obj->date->countDays($date, date('Y-m-d'));
            
            if('SP' == $package && ($days < 42 || $days > 238)){
                // LMD should be less than 6 weeks
                // LMD should not exceed 34 weeks
                $retval = false;
            }
            elseif('SB' == $package && $days > 365){
                // DOB should not exceed 1 year (previous value was 2 years :  $days > 728)
                $retval = false;
            }
            elseif('SH' == $package && ($days < 730 || $days > 1766)){
                // DOB should be less than 2 Years
                // DOB should not exceed 4 years 11 months
                $retval = false;
            }
			elseif('p' == $package && ($days < 42 || $days > 238)){
                // LMD should be less than 6 weeks
                // LMD should not exceed 34 weeks
                $retval = false;
            }
            elseif('b' == $package && $days > 365){
                // DOB should not exceed 1 year (previous value was 2 years :  $days > 728)
                $retval = false;
            }
        }
        
        return $retval;
    }
}
