<?php

/**
 * Description of mama Library
 *
 * @author Rafiqul Islam <rafiqul.islam@dnet.org.bd>
 */
class MamaLib {
    
    public $obj = null;
    
    public function __construct()
    {
        $this->obj =& get_instance();
    }
    
    public function printArray($data)
    {
        echo '<pre>'; print_r($data); echo '<pre>';
    }
    
    public function dumpArray($data)
    {
        echo '<pre>'; print_r($data); echo '<pre>'; die;
    }
    
    
    public function listSubsPosInFam() 
    {
        $data = array(
            1 => array(
                'name'  => 'Household Head',
                'dbval' => 'HHHead',
            ),
            2 => array(
                'name'  => 'Non-Household Head',
                'dbval' => 'NonHHHead',
            ),
        );
        
        return $data;
    }
    
    public function listBloodGroups() 
    {
        $data = array(
            'Apos'    => 'A+', 
            'Aneg'    => 'A-', 
            'Bpos'    => 'B+', 
            'Bneg'    => 'B-', 
            'ABpos'   => 'AB+', 
            'ABneg'   => 'AB-', 
            'Opos'    => 'O+', 
            'Oneg'    => 'O-', 
        );
        
        return $data;
    }
    
    public function listSubscriberTypes()
    {
        $data = array(
            1 => array(
                'name'  => 'Pregnant',
                'dbval' => 'p',
            ),
            2 => array(
                'name'  => 'Baby',
                'dbval' => 'b',
            ),
            3 => array(
                'name'  => 'Shoishob',
                'dbval' => 's',
            ),
             4 => array(
                'name'  => 'Shogorbha-1000 Pregnant',
                'dbval' => 'sp1000',
            ),
             5 => array(
                'name'  => 'Shogorbha-1000 Baby',
                'dbval' => 'sb1000',
            ),
        );
        
        return $data;
    }
    
    public function listPaygoSubscriberTypes()
    {
        $data = array(
            1 => array(
                'name'  => 'Pregnant',
                'dbval' => 'p',
            ),
            2 => array(
                'name'  => 'Baby',
                'dbval' => 'b',
            ),
        );
        
        return $data;
    }
    
    public function listPrepaidSubscriberTypes()
    {
        $data = array(
            7 => array(
                'name'  => 'Shoishob',
                'dbval' => 's',
            ),
            8 => array(
                'name'  => 'Shogorbha-1000 Pregnant',
                'dbval' => 'sp',
            ),
            8 => array(
                'name'  => 'Shogorbha-1000 Baby',
                'dbval' => 'sb',
            ),
        );
        
        return $data;
    }
    
    public function listEducationLevels() {
        
        $data = array(
            1 => array(
                'name'  => 'Incomplete Primary',
                'dbval' => 'ClsVbelow',
            ),
            2 => array(
                'name'  => 'Primary Completed',
                'dbval' => 'ClsVIIIbelow',
            ),
            3 => array(
                'name'  => 'Incomplete Secondary',
                'dbval' => 'ClsX below',
            ),
            4 => array(
                'name'  => 'Completed Higher Secondary',
                'dbval' => 'ClsXIIabove',
            ),
            5 => array(
                'name'  => 'Completed Secondary',
                'dbval' => 'ClsXIIbelow',
            ),
            6 => array(
                'name'  => 'Higher Secondary Completed',
                'dbval' => 'EDID000001',
            ),
            7 => array(
                'name'  => 'Above Higher Secondary',
                'dbval' => 'EDID000002',
            ),
            8 => array(
                'name'  => 'None',
                'dbval' => 'None',
            ),
        );
        
        return $data;
    }
    
    public function listOccupations() {
        
         $data = array(
            1 => array(
                'name'  => 'Farmer',
                'dbval' => 'Farmer',
            ),
            2 => array(
                'name'  => 'House wife',
                'dbval' => 'Housewife',
            ),
            3 => array(
                'name'  => 'Teacher',
                'dbval' => 'Teacher',
            ),
            4 => array(
                'name'  => 'Day Labor',
                'dbval' => 'DayLabor',
            ),
            5 => array(
                'name'  => 'Businessman',
                'dbval' => 'Businessman',
            ),
            6 => array(
                'name'  => 'Government worker',
                'dbval' => 'GovWorker',
            ),
            7 => array(
                'name'  => 'Non-Government worker',
                'dbval' => 'NonGovWorker',
            ),
            8 => array(
                'name'  => 'NGO worker',
                'dbval' => 'NGOWorker',
            ),
            9 => array(
                'name'  => 'Working Outside hometown/village',
                'dbval' => 'WorkingOutside',
            ),
            10 => array(
                'name'  => 'Overseas worker',
                'dbval' => 'OverseasWorker',
            ),
            0 => array(
                'name'  => 'Other',
                'dbval' => 'OSID000001',
            ),
        );
        
        return $data;
    }
    
    public function listYesNo() {
        
        $data = array(
            1 => array(
                'name'  => 'YES',
                'dbval' => 'YES',
            ),
            2 => array(
                'name'  => 'NO',
                'dbval' => 'NO',
            ),
        );
        
        return $data;
    }
    
    public function listRelationships() {
        
        $data = array(
            1 => array(
                'name'  => 'Husband',
                'dbval' => 'Husband',
            ),
            2 => array(
                'name'  => 'Mother-in-law',
                'dbval' => 'MotherInLaw',
            ),
            3 => array(
                'name'  => 'Mother',
                'dbval' => 'Mother',
            ),
            4 => array(
                'name'  => 'Other',
                'dbval' => 'Guardian',
            ),
        );
        
        return $data;
    }
    
    public function listRelationships2() {
        
        $data = array(
            4 => array(
                'name'  => 'Husband',
                'dbval' => 'Husband',
            ),
            5 => array(
                'name'  => 'Mother',
                'dbval' => 'Mother',
            ),
            6 => array(
                'name'  => 'Mother-in-law',
                'dbval' => 'MotherInLaw',
            ),
            3 => array(
                'name'  => 'Generic Guardian',
                'dbval' => 'Guardian',
            ),
        );
        
        return $data;
    }
    
    public function listServiceModels($organizationName = '') {
        
        
        if($organizationName == 'Call Center') {
            $data = array(
                1 => array(
                    'name'  => 'SMS',
                    'dbval' => 's',
                ),
                2 => array(
                    'name'  => 'Voice (Dial)',
                    'dbval' => 'd',
                ),
                3 => array(
                    'name'  => 'Voice (Receive)',
                    'dbval' => 'r',
                ),
            );
        }
        else{
            $data = array(
                1 => array(
                    'name'  => 'SMS',
                    'dbval' => 's',
                ),
                2 => array(
                    'name'  => 'Voice (Receive)',
                    'dbval' => 'r',
                ),
            );
        }
        
        return $data;
    }
    
    public function listServiceSchedule() {
        
        $data = array(
            1 => array(
                'name'  => 'Morning (08:00-12:00)',
                'dbval' => 'r1',
            ),
            2 => array(
                'name'  => 'Noon (12:00-16:00)',
                'dbval' => 'r2',
            ),
            3 => array(
                'name'  => 'Afternoon (16:00-20:00)',
                'dbval' => 'r3',
            ),
            4 => array(
                'name'  => 'Night (20:00-23:00)',
                'dbval' => 'r4',
            ),
        );
        
        return $data;
    }
    
    public function listDrinkingWaterSources() {
        
        $data = array(
           1 => array(
                'name'  => 'Tube well',
                'dbval' => 'Tube well',
            ),
           2 => array(
                'name'  => 'Well',
                'dbval' => 'Well',
            ),
           3 => array(
                'name'  => 'River',
                'dbval' => 'River',
            ),
           4 => array(
                'name'  => 'Pond',
                'dbval' => 'Pond',
            ),
           5 => array(
                'name'  => 'Others',
                'dbval' => 'Others',
            ),
        );
        
        return $data;
    }
    
    public function listMobilePhoneOwners() {
        
        $data = array(            
           1 => array(
                'name'  => 'Guardian',
                'dbval' => 'Guardian',
            ),            
           2 => array(
                'name'  => 'Neighbor',
                'dbval' => 'Neighbor',
            ),          
           3 => array(
                'name'  => 'Woman',
                'dbval' => 'Woman',
            ),  
        );
        
        return $data;
    }
    
    public function listDialects()
    {
        $data = array(
           1 => array(
                'name'  => 'RURAL AREA',
                'dbval' => 'RURAL',
            ),
           2 => array(
                'name'  => 'URBAN AREA',
                'dbval' => 'URBAN',
            ),
           3 => array(
                'name'  => 'Chittagong',
                'dbval' => 'CTG',
            ),
           4 => array(
                'name'  => 'Sylhet',
                'dbval' => 'SHY',
            ),
        );
        
        return $data;
    }
    
    public function listDialects2()
    {
        $data = array(
            'RURAL' => 'RURAL AREA',
            'URBAN' => 'URBAN AREA',
            'CTG' => 'Chittagong',
            'SHY' => 'Sylhet',
        );
        
        return $data;
    }
    
    public function isLoggedIn() {
        
        return $this->obj->session->userdata('id') ? true : false;
    }
    
    public function isValidDate($dt=array()) {
        $day    = intval($dt[0].$dt[1]);
        $month  = intval($dt[2].$dt[3]);
        $year   = intval($dt[4].$dt[5].$dt[6].$dt[7]);
        
        return checkdate($month, $day, $year);
    }
	
    public function isValidMobile($mobile=array()) {
        $status = true;
        $prmittedValPos3 = array('6','7','8','9');
        
        ## replace 0 with *
        foreach($mobile as $k=>$v) {
            if($mobile[$k] == '0') {
                $mobile[$k] = '*';
            }
        }
        
        ## check if any field is empty
        foreach($mobile as $k=>$v) {
            $tmp = trim($mobile[$k]);
            if(empty($tmp)){
                $status = false;
                break;
            }
        }
        
        ## validate in certain positions
        if($status === true) {
            if( $mobile[0]!=='*' || $mobile[1]!=='1' || !in_array($mobile[2], $prmittedValPos3) ) {
                $status = false;
            }
        }
        
        return $status;
    }
    
    ## Checks if today exceeds
    public function ifTodayExceeds($date){
        
        $diff = strtotime(date('Y-m-d')) - strtotime($date);
        return $diff < 0 ? true : false;
    }

    ## count days between 2 dates
    public function countDays($date1, $date2){
        
        return ( strtotime($date2) - strtotime($date1) ) / 86400  + 1;    // 1 day = 86400 seconds
    }
    
    /*
     * @param $date is mysql formatted date 
     * $date = '2013-12-23'
     */
    public function getFirstDateOfMonth($date=null) {
        
        if(empty($date)){
            $date = date('Y-m-d');
        }
        
        return date('01-m-Y', strtotime($date) );
    }
    
    /*
     * @param $date is mysql formatted date 
     * $date = '2013-12-23'
     */
    public function getLastDateOfMonth($date=null) {
        
        if(empty($date)){
            $date = date('Y-m-d');
        }
        
        return date('t-m-Y', strtotime($date) );
    }
    
    public function listMonths() {
        
        $data = array(
             1  => 'January',
             2  => 'February',
             3  => 'March',
             4  => 'April',
             5  => 'May',
             6  => 'June',
             7  => 'July',
             8  => 'August',
             9  => 'September',
             10 => 'October',
             11 => 'November',
             12 => 'December',
        );
        
        return $data;
    }
    
    public function listFormStatuses() {
        
        $data = array(
            'Verified'  => 'Verified',
            'Approved'  => 'Approved',
            'Synced'    => 'Synced',
            'Failed'    => 'Failed',
            'Denied'    => 'Denied',
            'Tried'     => 'Queued',
            'Parked'     => 'Parked',
        );
        
        return $data;
    }
    
    public function listGvPrefixes() {
        return $prefixes = array('PP', 'MM', 'PM');
    }

    /*
     * Converts date into mysql format
     * Input  : 01/25/2007 or 01-25-2007
     * Output : 2007-01-25
     */
    public function dateMysqlFormat($date) {
        
        return preg_replace("/(\d+)[-\/](\d+)[-\/](\d+)/", "$3-$2-$1", $date);
    }
    
    public function convertArrayToInt($array) {
        foreach($array as $key=>$val) {
            $array[$key] = (int)$val;
        }
        return $array;
    }
    
    public function chkGvValidity($code, $subscriberType='') {
        $code = trim($code);
        $status = '';
        
        $cardPrefix = substr($code, 0, 2);
         
        if(!empty($code)){
            ## Card is invalid
            if(strlen($code)!=9) {
                $status = 'Card is Invalid.';
            } elseif(!in_array($cardPrefix, $this->listGvPrefixes())) {
                $status = 'Card is Invalid.';
            } elseif(!empty($subscriberType) && !$this->isGvMatchedSubscriberType($cardPrefix, $subscriberType)) {
                $status = 'Mismatch between Card and Subscriber Type.';
            }

            ## Card is already used on <DATE>
            elseif(!$this->obj->MamaModel->isAvailableGV($code)){
                $status = 'Card is already used on '. $this->obj->MamaModel->whenUsedGV($code);
            } 
            ## Card is expired
            elseif($this->obj->MamaModel->isGvExpired($code)){
                $status = 'Card is expired.';
            }
        }
        
        return $status;
    }
    
    public function isGvMatchedSubscriberType($cardPrefix, $subscriberType) {
        $ret = true;
        if($subscriberType=='p' && !in_array($cardPrefix, array('PP', 'PM'))){
            $ret = false;
        }
        elseif($subscriberType=='b' && $cardPrefix!='MM'){
            $ret = false;
        }
        return $ret;
    }
    
    public function subscriberTypeKey($subscriberTypeCode, $guardianType, $subscriberTypeCat='Primary'){
        $key = 0;
        
        if($subscriberTypeCat=='Primary'){
            $key = '';
        } else{
            $key = '';
        }
        
        return $key;
    }
    
    /**
    * Trims a entire array recursivly.
    */
    function trimArray($arr){
        if (!is_array($arr)){ return $arr; }

        while (list($key, $value) = each($arr)){
            if (is_array($value)){
                $arr[$key] = $this->trimArray($value);
            }
            else {
                $arr[$key] = trim($value);
            }
        }
        return $arr;
    }
    
    public function submitPostData($url, $postData=array(), $isNewSession=0) {
        
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
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = $postData;
        }
        
        curl_setopt_array($ch, $options);
        
        $content = curl_exec($ch);
        curl_close($ch);

        return $content;
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
    
    public function cleanJson($raw_json){
        $start_pos = strpos($raw_json, '{');
        $end_pos = strrpos($raw_json, '}');
        return substr($raw_json, $start_pos, $end_pos-$start_pos+1);
    }
    
    public function dateValidity($totDays, $type){
        $err_msg = '';
      
        if($type == 'p') {
            ## 6 weeks = 42 days
            ## 42 weeks = 294 days
            if($totDays < 42) {
                $err_msg = 'LMD should not be less than 6 weeks! Your input is <b>'.ceil($totDays/7).' weeks</b>.';
            }
            elseif($totDays > 294) {
                $err_msg = 'LMD should not exceed 42 weeks! Your input is <b>'.ceil($totDays/7).' weeks</b>.';
            }
        }
        elseif($type == 'b' && $totDays>365) {
            $err_msg = 'Child\'s DOB should not exceed 1 year!';
        }
        elseif($type == 's'){
            if($totDays < 365) {
                $err_msg = 'DOB should not be less than 1 year! Your input is <b>'.ceil($totDays/30).' month</b>.';
            }
            elseif($totDays > 1766) {
                $err_msg = 'DOB should not exceed 4 years 10 months! Your input is <b>'.ceil($totDays/365).' year </b>.';
            }
        }
//        elseif($type == 'sp1000'){
//            if($totDays < 42) {
//              $err_msg = 'LMD should not be less than 6 weeks! Your input is <b>'.ceil($totDays/7).' weeks</b>.';
//              } elseif($totDays > 238) {
//                  $err_msg = 'LMD should not exceed 34 weeks! Your input is <b>'.ceil($totDays/7).' weeks</b>.';
//             }
//        }elseif($type == 'sb1000'){
//           if($totDays>730) {
//                $err_msg = 'Child\'s DOB should not exceed 2 year!';
//            } 
//        }
        elseif($type == 'sp'){
            if($totDays < 42) {
              $err_msg = 'LMD should not be less than 6 weeks! Your input is <b>'.ceil($totDays/7).' weeks</b>.';
              }elseif($totDays > 294) {
                  $err_msg = 'LMD should not exceed 42 weeks! Your input is <b>'.ceil($totDays/7).' weeks</b>.';
             }
        }elseif($type == 'sb'){
           if($totDays>730) {  // 365*2 = 730
                $err_msg = 'Child\'s DOB should not exceed 2 years!';
            } 
        }

        return $err_msg;
    }
}
