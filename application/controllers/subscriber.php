<?php

/**
 * Description of subscriber
 *
 * @author Rafiqul Islam <rafiqul.islam@dnet.org.bd>
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Subscriber extends CI_Controller 
{
    protected $dataSources = array(
        //, 'App'
         'Call Center'
        //, 'form'
        , 'Govt Data'
        , 'Outreach'
        , 'QA & QC Team'
        , 'Sms'
        //, 'Tab'
        , 'Web'
    );
    protected $dataSourceTypes = array('self', 'assisted');
    protected $informationSources = array(
        'Aponjon BP'
      , 'Bill board'
      , 'Bus Branding'
      , 'Campaign'
      , 'DOB update'
      , 'Facebook'
      , 'Health worker'
      , 'Leaflet'
      , 'List from FWC'
      , 'Newspaper'
      , 'Old Subscriber'
      , 'Scratch Card'
      , 'Relative'
      , 'RDC'
      , 'TVC'
      , 'Youtube'
      , 'Others'
    );
	public $aponjonHW = array(
        'SA265005147', 		// DoB Update for QA & QC team
        'CC264805365', 		// New Registration/personal Request for QA & QC team
        'CA264806184', 		// DoB Update for Call Center
        'SA269706185', 		// New Registration/personal Request for Call Center
    );
    protected $timeSlots = array(
        1 => 'Morning (08:00-12:00)',
        2 => 'Noon (12:00-16:00)',
        3 => 'Afternoon (16:00-20:00)',
        4 => 'Night (20:00-23:00)',
    );
    protected $isFree = array(0=>'Paid',1=> 'Free');    
    protected $subscriberActions = array(
            1 => "DOB update",
            2 => "Deregistration (Primary and Guardian)",
            3 => "Deregistration (Guardian only)",
            4 => "Change LMD/DOB",
            5 => "Change subscriber's mobile number",
            6 => "Change guardian's mobile number",
            7 => "Change channel",
            8 => "Change time-slot",
        );
    protected $subscription_types = array(
        'PAYGO' => 'Pay As You Go',
        'PREPAID' => 'Prepaid',
    );
    
    protected $subscriber_types = array(
        1 => 'Primary',
        2 => 'Primary',
        3 => 'Gatekeeper_Generic',
        4 => 'Gatekeeper_Husband',
        5 => 'Gatekeeper_Mother',
        6 => 'Gatekeeper_MotherInLaw',
        7 => 'Primary',
        8 => 'Primary',
        9 => 'Primary',
    );
    
    protected $dialects = array(
        'RURAL' => 'Rural',
        'URBAN' => 'Urban',
        'CTG' => 'Chittagonian',
        'SHY' => 'Sylhety',
    );
    
    ## LIVE
    protected $urlTokenApi = 'http://bridge.aponjon.com.bd/api/v1/login';
    protected $urlSearchApi = 'http://bridge.aponjon.com.bd/api/v1/is-mobile-exist/';
    protected $urlRegistrationApi = 'http://bridge.aponjon.com.bd/api/v1/subscriber/register';
    protected $urlDegistrationApi = 'http://bridge.aponjon.com.bd/api/v1/subscriber/deregister';
    
    
    /*## TEST
    protected $urlTokenApi = 'http://bridge-test.aponjon.com.bd/api/v1/login';
    protected $urlSearchApi = 'http://bridge-test.aponjon.com.bd/api/v1/is-mobile-exist/';
    protected $urlRegistrationApi = 'http://bridge-test.aponjon.com.bd/api/v1/subscriber/register';
    protected $urlDegistrationApi = 'http://bridge-test.aponjon.com.bd/api/v1/subscriber/deregister';
    */
    
    ## PREPEIAD BRIGE API URL
    protected $urlBridgePrepaidDereg = "http://vendorapi.aponjon.com.bd/lcprep/deregister/";
    protected $urlBridgePrepaidUpdate = "http://vendorapi.aponjon.com.bd/lcprep/update/";
    
    
    
    protected $searhOpsLogFile = null;  // Log file for DOB UPD, DEREG, REG through search opeartion
    protected $packages = array('PREG', 'PREG_GUARDIAN', 'NEW_MOTHER', 'NEW_MOTHER_GUARDIAN', 'SHOISHOB');

    public function __construct() {
        parent::__construct();

        if(!$this->mamalib->isLoggedIn()) {
            $this->session->set_userdata('login_ret_url', "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
            redirect('/login', 'location');
            exit;
        }
        $this->load->model('subscribermodel');
        $this->load->model('optionsmodel');

        //$this->load->library('logger');
        //$this->logger->setLogFile('/var/www/html/forms/logs/form_'.date('Ymd').'.log');
    }
    
    public function index() {
        redirect('subscriber/add', 'location');
        exit;
    }
    
    public function add() {
       
        ## Confirm authentic user is accessing this page
        if(!in_array($this->session->userdata('page_access_token'), array('1','3'))){
            redirect($this->config->item('base_url').'subscriber/search', 'location');
            exit;
        }
       
       $data = array (
            'ca_id' => '',
            'subs_type' => '',
            'subs_type_rad' => '', // 'p',
            'subs_name' => '',
            'subs_age' => '', 
            'gurdian_name' => '', 
            'village' => '', 
            'post_office' => '', 
            'division' => '', 
            'district' => '', 
            'upazilla' => '', 
            'union' => '', 
            'subs_pos_fam' => '', 
            'subs_pos_fam_rad' => 'NonHHHead', 
            'subs_ed' => '', 
            'subs_ed_rad' => '', 
            'subs_ed_yr' => '', 
            'subs_ed_lvl' => '', 
            'subs_ed_lvl_rad' => '', 
            'monthly_income' => '', 
            'monthly_expense' => '', 
            'oc_fam_hd' => '', 
            'oc_fam_hd_rad' => '', 
            'oc_subsc' => '', 
            'oc_subsc_rad' => '', 
            'schoolgoing_labour' => '', 
            'schoolgoing_labour_rad' => 'NO', 
            'mb_date' => array ( 
                                0 => '', 
                                1 => '', 
                                2 => '', 
                                3 => '', 
                                4 => '', 
                                5 => '', 
                                6 => '', 
                                7 => '', 
                            ), 
            'dt_mb_date' => '', 
            'subs_mobile' => array ( 
                                0 => '', 
                                1 => '', 
                                2 => '', 
                                3 => '', 
                                4 => '', 
                                5 => '', 
                                6 => '', 
                                7 => '', 
                                8 => '', 
                                9 => '', 
                                10 => '', 
                                11 => '', 
                            ),
            'subs_mobile_number' => '',
            'service_model' => '', 
            'service_model_rad' => '', 
            'srvc_schd' => '', 
            'srvc_schd_rad' => '', 
            'fam_mem_rcv_inf_w' => '', 
            'fam_mem_rcv_inf_w_rad' => '', 
            'fam_mem_mobile' => array ( 
                                0 => '', 
                                1 => '', 
                                2 => '', 
                                3 => '', 
                                4 => '', 
                                5 => '', 
                                6 => '', 
                                7 => '', 
                                8 => '', 
                                9 => '', 
                                10 => '', 
                                11 => '', 
                            ), 
            'fam_mem_mobile_number' => '', 
            'same_subs_mobile_chk' => '', 
            'fam_mem_relation' => '', 
            'fam_mem_relation_rad' => '', 
            'tot_home_phone' => '', 
            'mobile_phone_owner' => '', 
            'mobile_phone_owner_rad' => 'Woman', 
            'blood_grp' => '', 
            'sant_lt_avl_hm' => '', 
            'sant_lt_avl_hm_rad' => '',
            'drinking_water_src' => '', 
            'drinking_water_src_rad' => '', 
            'subs_national_id' => '', 
            'gurdian_national_id' => '', 
            'dialect' => '', 
            'dialect_rad' => 'RURAL',
            'is_free' => '',
            'gv_code' => '',
            'ds_gateway' => '',
            'dt_medium' => 'Paper',
            'ds_reg_type' => 'Assisted',
            'ds_information_source' => '',
            'req_cancel' => '',
            'req_cncl_sts' => '2',
        );
        $data['inf_src'] = $this->optionsmodel->listDataSourceOptions();
        $data['param'] = @unserialize(base64_decode($_GET['param']));
        if(!$data['param']){
            //do nothig
        } else{
            $data['ca_id'] = $data['param']['hwid'];
            $data['subs_mobile'] = str_split($data['param']['msisdn']);
        }
        
	$data['aponjonHW'] = $this->aponjonHW;
        $data['success_msg'] = $this->session->flashdata('success_msg');

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sbtInputs = $this->input->post(NULL, TRUE);
            // convert inputs into integer
            $sbtInputs['mb_date'] = $this->mamalib->convertArrayToInt($sbtInputs['mb_date']);
            $data = array_merge($data, $sbtInputs);
                        
            $data = $this->mamalib->trimArray($data);
            if($data['req_cancel']==1){
                ## Validate
                if($data['req_cncl_sts'] == 2 && $data['no_reason']==''){
                    $data['err']['no_reason'] = 'Please select a reason.';
                }
                elseif($data['req_cncl_sts'] == 3 && $data['park_reason']==''){
                    $data['err']['park_reason'] = 'Please select a reason.';
                }
                elseif($data['req_cncl_sts'] == 4 && ($data['apt_date']=='' || $data['apt_time']=='')){
                    $data['err']['apt_date'] = 'Please select appointment.';
                }
                if(!empty($data['err'])){
                    $data['error_msg'] = 'Please enter data correctly!';
                }
                
                ## Save
                $this->_updRegRequestBySms($data['param']['id'], $data['req_cncl_sts'], $data['no_reason'], $data['park_reason'], $data['apt_date'], $data['apt_time'], $data['remarks']);
                redirect($data['param']['ret_url'], 'location');
                exit;
            } else{
                ## YYYY-MM-DD
                $data['mysql_mb_date']          = $data['mb_date'][4].$data['mb_date'][5].$data['mb_date'][6].$data['mb_date'][7].'-'.$data['mb_date'][2].$data['mb_date'][3].'-'.$data['mb_date'][0].$data['mb_date'][1];

                ## DDMM
                $data['dt_mb_date']             = $data['mb_date'][0].$data['mb_date'][1].$data['mb_date'][2].$data['mb_date'][3];
                $data['subs_mobile_number']     = implode('', $data['subs_mobile']);
                $data['fam_mem_mobile_number']  = implode('', $data['fam_mem_mobile']);

                ## set HasChildlabour = 1 for Special CA ID 'CA262604525'.
                ## Requested by Ibrahim Khalilullah Faisal on 10 March 2014.
                if($data['ca_id'] == 'CA262604525') {
                    $data['schoolgoing_labour_rad'] = 'YES';
                }
                if(isset($data['data_source_type']) && $data['data_source_type']!='self') {
                    $data['data_source_type']='assisted';
                }

                $data['err'] = $this->_validateFormInputs($data);

                if(empty($data['err'])) {
                    $resp = $this->subscribermodel->saveFormData($data);
                    if(isset($data['param']['id'])){
                        $this->subscribermodel->updateRegRequest($data['param']['id'], $resp['eSubscriberId'], $data['param']['msisdn'], $data['subs_mobile_number'], $data['ca_id']);
                        redirect($data['param']['ret_url'], 'location');
                        exit;
                    } else{
                        $this->session->set_flashdata('success_msg', 'Data entered successfully!<br /><b>Token : '.$resp['token'].'</b>');
                        redirect('', 'location');
                        exit;
                    }
                }
                else{
                    $data['error_msg'] = 'Please enter data correctly!';
                }
            }   
        }
        
        $data['divisions']              = $this->MamaModel->listDivisions();
        $data['districts']              = $this->MamaModel->listDistrictsByDivisionId($data['division']);
        $data['upazillas']              = $this->MamaModel->listUpazillasByDistId($data['district']);
        $data['unions']                 = $this->MamaModel->listUnionsByUpazillaId($data['upazilla'] );
        
        $data['bloodGroups']            = $this->mamalib->listBloodGroups();
        $data['subscriberTypes']        = $this->mamalib->listPaygoSubscriberTypes();
        $data['subsPosInFam']           = $this->mamalib->listSubsPosInFam();
        $data['yesNo']                  = $this->mamalib->listYesNo();
        $data['educationLevels']        = $this->mamalib->listEducationLevels();
        $data['occupations']            = $this->mamalib->listOccupations();
        $data['serviceModels']          = $this->mamalib->listServiceModels($this->session->userdata('organization'));
        $data['serviceSchedules']       = $this->mamalib->listServiceSchedule();
        $data['relationships']          = $this->mamalib->listRelationships();
        $data['mobilePhoneOwners']      = $this->mamalib->listMobilePhoneOwners();
        $data['drinkingWaterSources']   = $this->mamalib->listDrinkingWaterSources();
        $data['dialects']               = $this->mamalib->listDialects();
        
        $data['dataSources']            = $this->dataSources;
        $data['dataSourceTypes']        = $this->dataSourceTypes;
        $data['informationSources']     = $this->informationSources;
        
        $data['reasonsNotSubscribed']   = $this->MamaModel->listRegRequestReasons('NO');
        $data['reasonsParked']          = $this->MamaModel->listRegRequestReasons('PARKED');
        
		$data['page'] = 'subscriber/add';
        
        $this->load->view('template/layout1', $data);
    }
    
    public function edit($id) {
        
        $data = $this->subscribermodel->getFormDataById($id);       
        $data['inf_src'] = $this->optionsmodel->listDataSourceOptions();
        if(empty($data)) {
            $data['unauthicated'] = 'Access denied!';
        }
        else {
                $data['mb_date'] = array(
                $data['mb_date_str'][8],
                $data['mb_date_str'][9],
                $data['mb_date_str'][5],
                $data['mb_date_str'][6],
                $data['mb_date_str'][0],
                $data['mb_date_str'][1],
                $data['mb_date_str'][2],
                $data['mb_date_str'][3],
            );
            
            $data['bloodGroups']            = $this->mamalib->listBloodGroups();
            $data['subscriberTypes']        = $this->mamalib->listSubscriberTypes();
            $data['subsPosInFam']           = $this->mamalib->listSubsPosInFam();
            $data['yesNo']                  = $this->mamalib->listYesNo();
            $data['educationLevels']        = $this->mamalib->listEducationLevels();
            $data['occupations']            = $this->mamalib->listOccupations();
            $data['serviceModels']          = $this->mamalib->listServiceModels($this->session->userdata('organization'));
            $data['serviceSchedules']       = $this->mamalib->listServiceSchedule();
            $data['relationships']          = $this->mamalib->listRelationships();
            $data['mobilePhoneOwners']      = $this->mamalib->listMobilePhoneOwners();
            $data['drinkingWaterSources']   = $this->mamalib->listDrinkingWaterSources();
            $data['dialects']               = $this->mamalib->listDialects();
            
            $data['dataSources']            = $this->dataSources;
            $data['dataSourceTypes']        = $this->dataSourceTypes;
            $data['informationSources']     = $this->informationSources;

            $data['subs_mobile']    = str_split($data['MobNum']);
            $data['fam_mem_mobile'] = str_split($data['GuardianNum']);
            $data['subs_type']      = '';
            $data['gurdian_name']   = '';
            $data['village']        = '';
            $data['post_office']    = '';
            $data['subs_pos_fam']   = '';
            $data['subs_ed']        = '';
            $data['subs_ed_rad']    = !empty($data['subs_ed_lvl_rad']) ? 'YES' : 'NO';
            $data['subs_ed_lvl']    = '';
            $data['oc_fam_hd']      = '';
            $data['oc_subsc']       = '';
            $data['schoolgoing_labour'] = '';
            $data['service_model']  = '';
            $data['srvc_schd']      = '';

            if(in_array($data['OutdialPref'], array('r1', 'r2', 'r3', 'r4'))){
                $data['service_model_rad'] = 'r';
                $data['srvc_schd_rad'] = $data['OutdialPref'];
            }
            else {
                $data['service_model_rad'] = $data['OutdialPref'];
                $data['srvc_schd_rad'] = $data['OutdialPref'];
            }
            $data['fam_mem_rcv_inf_w'] = '';
            if(!empty($data['GuardianNum'])) {
                $data['fam_mem_rcv_inf_w_rad'] = 'YES';
            }
            else{
                $data['fam_mem_rcv_inf_w_rad'] = 'NO';
            }
            $data['same_subs_mobile_chk'] = $data['MobNum']==$data['GuardianNum'] ? 1 : 0;
            $data['fam_mem_relation'] = '';
            $data['mobile_phone_owner'] = '';
            $data['sant_lt_avl_hm'] = '';
            $data['drinking_water_src'] = '';
            $data['dialect'] = '';
            //$data['tx_data_source'] = 
            $data['information_source_others'] = !in_array($data['information_source'], $this->informationSources) ? $data['information_source'] : '';
            $data['information_source'] = !empty($data['information_source_others']) ? 'Others' : $data['information_source'];
            
            $data['divisions']              = $this->MamaModel->listDivisions();
            $data['districts']              = $this->MamaModel->listDistrictsByDivisionId($data['division']);
            $data['upazillas']              = $this->MamaModel->listUpazillasByDistId($data['district']);
            $data['unions']                 = $this->MamaModel->listUnionsByUpazillaId($data['upazilla'] );

            $data['success_msg'] = $this->session->flashdata('success_msg'); 
            
            if($_SERVER['REQUEST_METHOD'] === 'POST') {
                $sbtInputs                      = $this->input->post(NULL, TRUE);
                $data                           = array_merge($data, $sbtInputs);
                
                $data = $this->mamalib->trimArray($data);
                
//                ## YYYY-MM-DD
//                $data['mysql_mb_date']          = $data['mb_date'][4].$data['mb_date'][5].$data['mb_date'][6].$data['mb_date'][7].'-'.$data['mb_date'][2].$data['mb_date'][3].'-'.$data['mb_date'][0].$data['mb_date'][1];
//
//                ## DDMM
//                $data['dt_mb_date']             = $data['mb_date'][0].$data['mb_date'][1].$data['mb_date'][2].$data['mb_date'][3];
                
                
                 ## YYYY-MM-DD
                $data['mysql_mb_date']          = $data['mb_date'][4].$data['mb_date'][5].$data['mb_date'][6].$data['mb_date'][7].'-'.$data['mb_date'][2].$data['mb_date'][3].'-'.$data['mb_date'][0].$data['mb_date'][1];

                ## DDMM
                $data['dt_mb_date']             = $data['mb_date'][0].$data['mb_date'][1].$data['mb_date'][2].$data['mb_date'][3];
             
                //exit;
                
                $data['subs_mobile_number']     = implode('', $data['subs_mobile']);
                $data['fam_mem_mobile_number']  = implode('', $data['fam_mem_mobile']);
                
                
                ## set HasChildlabour = 1 for Special CA ID 'CA262604525'.
                ## Requested by Ibrahim Khalilullah Faisal on 10 March 2014.
                
                if($data['ca_id'] == 'CA262604525') {
                    $data['schoolgoing_labour_rad'] = 'YES';
                }
                
                if($data['data_source_type']!='self') {
                    $data['data_source_type']= 'assisted';
                }
                
                $data['err'] = $this->_validateFormInputs($data, $id);

                if(empty($data['err'])) {
                    $this->subscribermodel->updateFormData($data, $id);
                    $this->session->set_flashdata('success_msg', 'Data editted successfully!');
                    redirect('reports/summary/', 'location');
                    exit;
                }
                else{
                    $data['error_msg'] = 'Please enter data correctly!';
                }
            }
        }
        
	$data['page'] = 'subscriber/edit';
        $this->load->view('template/layout1', $data);
    }
    
    public function isSubscriberAvailableInBridge($msisdn)
    {
        $json = $this->mamalib->submitPostData($this->urlSearchApi.$msisdn);
        $tmp = json_decode($json, true);
        return isset($tmp['status']) ? $tmp['status']: false;
    }
    
    /**
     * Validates date 
     * @param type $dateArray = array(D,D,M,M,Y,Y,Y,Y)
     * @param type $dateMysqlFormat = YYYY-MM-DD 
     * @param type $subscriberType = 1(Pregnant), 2(New Mother)
     */
    protected function chkDateValid($dateArray, $dateMysqlFormat, $subscriberType)
    {
        $error = '';
        if(!$this->mamalib->isValidDate($dateArray)) {
            $error = 'Blank or Invalid Date!';
        } else if($this->mamalib->ifTodayExceeds($dateMysqlFormat)) {
            $error = 'Date value should not exceed today\'s date!';
        } else {
            $totDays = $this->mamalib->countDays($dateMysqlFormat, date('Y-m-d'));
            if($subscriberType==1 && $totDays<42) { ## 6 weeks = 42 days
                $error = 'LMD should not be less than 6 weeks! Your input is <b>'.ceil($totDays/7).' weeks</b>.';
            } else if($subscriberType==1 && $totDays>294) { ## 42 weeks = 294 days
                $error = 'LMD should not exceed 42 weeks! Your input is <b>'.ceil($totDays/7).' weeks</b>.';
            } else if($subscriberType==2 && $totDays>365) {
                $error = 'Child\'s DOB should not exceed 1 year!';
            } else if($subscriberType==3) {
                if($totDays < 365) {
                    $errors = 'DOB should not be less than 1 year! Your input is <b>'.ceil($totDays/30).' month</b>.';
                }
                else if($totDays > 1766) {
                    $errors = 'DOB should not exceed 4 years 10 months! Your input is <b>'.ceil($totDays/365).' year </b>.';
                }
            }
        }
        return $error;
    }
    
    private function _isRegisteredInBridge($mobile)
    {        
        $json = file_get_contents($this->urlSearchApi.$mobile);        
        $data = json_decode($json);
        if($data->status){
            return true;
        }
        return false;
    }
    
    private function _deregisterFromBridge($msisdn, $reqChannel, $deregReason, $sl='')
    {
        $deregData = array(
            '_token'            => $this->session->userdata('api_token'),
            'msisdn'            => $msisdn,
            'request_channel'   => $reqChannel,
            'dereg_reason'      => $deregReason,
        );
        $this->logger->setInfoLog($sl.' DEREG REQ > ' . json_encode($deregData));
        $json = $this->mamalib->submitPostData($this->urlDegistrationApi, $deregData);
        $this->logger->setInfoLog($sl.' DEREG RESP > ' . $json);
        return $json;
    }
    
    private function _registerPrimarySubscriberToBridge($data, $sl='')
    {
        $newRegInfo = array('regId' => '', 'msisdn' => '');
        $regData = $this->prepNewRegSubscriberBridgeData($data, 'NEW_MOTHER', 'primary');
        $this->logger->setInfoLog($sl.' BRIDGE DOB UPD PRIMARY REG REQ > ' . json_encode($regData));
        $json = $this->mamalib->submitPostData($this->urlRegistrationApi, $regData);
        $this->logger->setInfoLog($sl.' BRIDGE DOB UPD PRIMARY REG RESP > ' . $json);
        $tmp = json_decode($json, true);
        if($tmp['status']=='success'){
            $newRegInfo = array(
                'regId' => "{$tmp['subscriber_id']}", 
                'msisdn' => $tmp['msisdn']
            );
        }
        
        return $newRegInfo;
    }
    
    private function _registerGuardianToBridge($data, $sl)
    {
        $newRegInfo = array('regId' => '', 'msisdn' => '');
        $regData = $this->prepNewRegSubscriberBridgeData($data, 'NEW_MOTHER_GUARDIAN', 'guardian');
        $this->logger->setInfoLog($sl.' BRIDGE DOB UPD GUARDIAN REG REQ > ' . json_encode($regData));
        $json = $this->mamalib->submitPostData($this->urlRegistrationApi, $regData);
        $this->logger->setInfoLog($sl.' BRIDGE DOB UPD GUARDIAN REG RESP > ' . $json);
        $tmp = json_decode($json, true);
        if($tmp['status']=='success'){
            $newRegInfo = array(
                //'regId' => !empty($tmp['subscriber_id']) ? (int)$tmp['subscriber_id'] : '', 
                'regId' => "{$tmp['subscriber_id']}", 
                'msisdn' => $tmp['msisdn']
            );
        }
        
        return $newRegInfo;
    }
    
    public function processDobUpdateForSearchData($data)
    {   
        $reqChannel = 'DOB UPDATE';
        $deregReason = 'DOB UPDATE';
        $mappingReason = 'DOB UPDATE';
        $successMsg = 'DOB updated successfully.';
        $newRegInfo = array();
        $date = date('Y-m-d H:i:s');
        $apiErrors = '';
        
        ## YYYY-MM-DD
        $data['mysql_mb_date'] = $data['mb_date'][4].$data['mb_date'][5].$data['mb_date'][6].$data['mb_date'][7].'-'.$data['mb_date'][2].$data['mb_date'][3].'-'.$data['mb_date'][0].$data['mb_date'][1];
        ## DDMM
        $data['dt_mb_date'] = $data['mb_date'][0].$data['mb_date'][1].$data['mb_date'][2].$data['mb_date'][3];
        $chkDateInvalidResaon = $this->chkDateValid($data['mb_date'], $data['mysql_mb_date'], 2);
       
        if(empty($chkDateInvalidResaon)){ // Data is OK
            if($this->isSubscriberAvailableInBridge($data['msisdn'])){  // Data found in Bridge 
                $newRegInfo = array();
                ## 1. Deregister from Bridge
                $json = $this->_deregisterFromBridge($data['msisdn'], $reqChannel, $deregReason, '1.');
                $tmp = json_decode($json, true);
                
                if(isset($tmp['status']) && $tmp['status'] == 'success'){
                    foreach($tmp['info'] as $t){
                       ## RE-REGISTER in Bridge
                       if($data['srch_rslt']['subscriber_msisdn'] == $t['msisdn']){
                           ## 2.1 Re-Register Primary Subscriber in Bridge
                           $newRegInfo['primary'] = $this->_registerPrimarySubscriberToBridge($data, '2.1');
                       }
                       else if($data['srch_rslt']['guardian_msisdn'] == $t['msisdn']) {
                            ## 2.2 Re-Register Guardian in Bridge
                           $newRegInfo['guardian'] = $this->_registerGuardianToBridge($data, '2.2');
                       }
                    }
                    
                    ## 3. Update subscriber's new RegID
                    $this->subscribermodel->updSubsNewRegId($data, $newRegInfo, $date);
                    $this->logger->setInfoLog('3. PMRS DOB UPD > Update Subscriber Data with newRegId and DOB');

                    ## 4. Map
                    $this->subscribermodel->impMappingHistory($data['subscriber_base_reg_id'], $newRegInfo['primary']['regId'], $mappingReason, $date);
                    $this->logger->setInfoLog('4.1 PMRS DOB UPD MappingHistory > Primary Subscriber new RegId updated.');
                    
                    if(!empty($newRegInfo['guardian']['regId'])){
                        $this->subscribermodel->impMappingHistory($data['guardian_base_reg_id'], $newRegInfo['guardian']['regId'], $mappingReason, $date);
                        $this->logger->setInfoLog('4.2 PMRS DOB UPD MappingHistory > Guardian new RegId updated.');
                    }
                } else{
                    ## TODO
                    // DEREGISTRAION IS NOT SUCCESSFULL
                    $this->logger->setErrLog('DEREGISTRAION IS NOT SUCCESSFULL.');
                   
                }
            } else{ // Data not found in Bridge
                $this->logger->setWarnLog('BRIDGE DOB UPD > SUBSCRIBER "'.$data['msisdn'].'" NOT FOUND IN BRIDGE.');
                $this->logger->setWarnLog('BRIDGE DOB UPD > REGISTER "'.$data['msisdn'].'" AS NEW SUBSCRIBER IN BRIDGE.');
                $newRegInfo = array();
                
                ## Register
                if(isset($data['srch_rslt']['subscriber_msisdn'])){
                    ## 1.1 Register Primary Subscriber in Bridge
                    $newRegInfo['primary'] = $this->_registerPrimarySubscriberToBridge($data, '1.1');
                }
                if(isset($data['srch_rslt']['guardian_msisdn']) && !empty($data['srch_rslt']['guardian_msisdn'])){
                    ## 1.3 Register Guardian in Bridge
                    $newRegInfo['guardian'] = $this->_registerGuardianToBridge($data, '1.2');
                }
                
                ## 2. Update subscriber's new RegID
                $this->subscribermodel->updSubsNewRegId($data, $newRegInfo, $date);
                $this->logger->setInfoLog('2. PMRS DOB UPD > Update Subscriber Data with newRegId and DOB');
                
                ## 3. Map
                $this->subscribermodel->impMappingHistory($newRegInfo['primary']['regId'], $mappingReason, $date);
                $this->logger->setInfoLog('3.1 PMRS DOB UPD MappingHistory > Primary Subscriber new RegId updated.');
                
                if(!empty($newRegInfo['guardian']['regId'])){
                    $this->subscribermodel->impMappingHistory($newRegInfo['guardian']['regId'], $mappingReason, $date);
                    $this->logger->setInfoLog('3.2 PMRS DOB UPD MappingHistory > Guardian new RegId updated.');
                }
            }
            
            ### DOB UPD SUCCESS, REDIRECT FROM HERE
            $errorMsg = isset($tmp['errors']) ? implode(", ", $tmp['errors']) : '';
            $this->subscribermodel->impCallCenterReq($data, $newRegInfo, $mappingReason, $date, $errorMsg);
                       
            $this->session->set_flashdata('success_msg', 'DOB updated successfully.');
            redirect('subscriber/search?msisdn='.$data['msisdn'], 'location');
            exit;
            //return 'DOB updated successfully';
        
        }else{
            // Data is not OK
            ### INVALID DOB. STAY ON SAME PAGE
            return $chkDateInvalidResaon;
        }
    }
    
    private function processLmdDobUpdateForSearchData($data){        
        
        $data['mysql_mb_date'] = $data['mb_date'][4].$data['mb_date'][5].$data['mb_date'][6].$data['mb_date'][7].'-'.$data['mb_date'][2].$data['mb_date'][3].'-'.$data['mb_date'][0].$data['mb_date'][1];
         ## DDMM
        $data['dt_mb_date'] = $data['mb_date'][0].$data['mb_date'][1].$data['mb_date'][2].$data['mb_date'][3];
        $chkDateInvalidResaon = $this->chkDateValid($data['mb_date'], $data['mysql_mb_date'], 2);
         
        if(empty($chkDateInvalidResaon)){ // Data is OK
            if($this->isSubscriberAvailableInBridge($data['msisdn'])){  // Data found in Bridge 
                $newRegInfo = array();
                ## 1. Deregister from Bridge
//                $json = $this->_deregisterFromBridge($data['msisdn'], $reqChannel, $deregReason, '1.');
//                $tmp = json_decode($json, true);
//                
//                if(isset($tmp['status']) && $tmp['status'] == 'success'){
//                    foreach($tmp['info'] as $t){
//                       ## RE-REGISTER in Bridge
//                       if($data['srch_rslt']['subscriber_msisdn'] == $t['msisdn']){
//                           ## 2.1 Re-Register Primary Subscriber in Bridge
//                           $newRegInfo['primary'] = $this->_registerPrimarySubscriberToBridge($data, '2.1');
//                       }
//                       else if($data['srch_rslt']['guardian_msisdn'] == $t['msisdn']) {
//                            ## 2.2 Re-Register Guardian in Bridge
//                           $newRegInfo['guardian'] = $this->_registerGuardianToBridge($data, '2.2');
//                       }
//                    }
//                    
//                    ## 3. Update subscriber's new RegID
//                    $this->subscribermodel->updSubsNewRegId($data, $newRegInfo, $date);
//                    $this->logger->setInfoLog('3. PMRS DOB UPD > Update Subscriber Data with newRegId and DOB');
//
//                    ## 4. Map
//                    $this->subscribermodel->impMappingHistory($data['subscriber_base_reg_id'], $newRegInfo['primary']['regId'], $mappingReason, $date);
//                    $this->logger->setInfoLog('4.1 PMRS DOB UPD MappingHistory > Primary Subscriber new RegId updated.');
//                    
//                    if(!empty($newRegInfo['guardian']['regId'])){
//                        $this->subscribermodel->impMappingHistory($data['guardian_base_reg_id'], $newRegInfo['guardian']['regId'], $mappingReason, $date);
//                        $this->logger->setInfoLog('4.2 PMRS DOB UPD MappingHistory > Guardian new RegId updated.');
//                    }
//                } else{
//                    ## TODO
//                    // DEREGISTRAION IS NOT SUCCESSFULL
//                    $this->logger->setErrLog('DEREGISTRAION IS NOT SUCCESSFULL.');
//                    //var_dump($tmp); die;
//                }
            } else{ // Data not found in Bridge
                $this->logger->setWarnLog('BRIDGE DOB UPD > SUBSCRIBER "'.$data['msisdn'].'" NOT FOUND IN BRIDGE.');
                $this->logger->setWarnLog('BRIDGE DOB UPD > REGISTER "'.$data['msisdn'].'" AS NEW SUBSCRIBER IN BRIDGE.');
                $newRegInfo = array();
                
                ## Register
                if(isset($data['srch_rslt']['subscriber_msisdn'])){
                    ## 1.1 Register Primary Subscriber in Bridge
                    $newRegInfo['primary'] = $this->_registerPrimarySubscriberToBridge($data, '1.1');
                }
                
                if(isset($data['srch_rslt']['guardian_msisdn']) && !empty($data['srch_rslt']['guardian_msisdn'])){
                    ## 1.3 Register Guardian in Bridge
                    $newRegInfo['guardian'] = $this->_registerGuardianToBridge($data, '1.2');
                }
                
                ## 2. Update subscriber's new RegID
                $this->subscribermodel->updSubsNewRegId($data, $newRegInfo, $date);
                $this->logger->setInfoLog('2. PMRS DOB UPD > Update Subscriber Data with newRegId and DOB');
                
                ## 3. Map
                $this->subscribermodel->impMappingHistory($newRegInfo['primary']['regId'], $mappingReason, $date);
                $this->logger->setInfoLog('3.1 PMRS DOB UPD MappingHistory > Primary Subscriber new RegId updated.');
                
                if(!empty($newRegInfo['guardian']['regId'])){
                    $this->subscribermodel->impMappingHistory($newRegInfo['guardian']['regId'], $mappingReason, $date);
                    $this->logger->setInfoLog('3.2 PMRS DOB UPD MappingHistory > Guardian new RegId updated.');
                }
            }
            
            ### DOB UPD SUCCESS, REDIRECT FROM HERE
            $errorMsg = isset($tmp['errors']) ? implode(", ", $tmp['errors']) : '';
            $this->subscribermodel->impCallCenterReq($data, $newRegInfo, $mappingReason, $date, $errorMsg);
                       
            $this->session->set_flashdata('success_msg', 'DOB updated successfully.');
            redirect('subscriber/search?msisdn='.$data['msisdn'], 'location');
            exit;
            //return 'DOB updated successfully';
        
        }else{
            // Data is not OK
            ### INVALID DOB. STAY ON SAME PAGE
            return $chkDateInvalidResaon;
        }
        
        
    }
    
    private function processSubscriberMobileData($data){
        $data['tx_mobile'] = $data['subs_mobile'][0].$data['subs_mobile'][1].$data['subs_mobile'][2].$data['subs_mobile'][3].$data['subs_mobile'][4].$data['subs_mobile'][5].$data['subs_mobile'][6].$data['subs_mobile'][7].$data['subs_mobile'][8].$data['subs_mobile'][9].$data['subs_mobile'][10];
        
    }
    
    private function processGuardianMobileData($data){
       $data['tx_fam_mem_mobile'] = $data['fam_mem_mobile'][0].$data['fam_mem_mobile'][1].$data['fam_mem_mobile'][2].$data['fam_mem_mobile'][3].$data['fam_mem_mobile'][4].$data['fam_mem_mobile'][5].$data['fam_mem_mobile'][6].$data['fam_mem_mobile'][7].$data['fam_mem_mobile'][8].$data['fam_mem_mobile'][9].$data['fam_mem_mobile'][10];
    
    }
    
    private function processChangeChanelData($data){
            print_r($data['service_model_rad'] . $data['srvc_schd_rad']);die;
    } 
    
    private function processChangeTimeslotData($data){
        print_r($data['service_model_rad'] . $data['srvc_schd_rad']);die;
    }
  
    private function prepTimeslotData($channel, $intTimeSlot)
    {   
        $ts = 'SMS';
        if($channel=='IVR'){
            if(empty($intTimeSlot) || $intTimeSlot>4){
                $ts = 'R1';
            } else{
                $ts = 'R'.$intTimeSlot;
            }
        }   
        return $ts;
    }
    
    private function prepNewRegSubscriberBridgeData($data, $package, $type='primary')
    {
        
        if($type=='primary'){
            $arr = array(
                '_token'                      => $this->session->userdata('api_token'),
                'request_channel'             => $data['request_channel'], // (string)    length: 45, Example: form, tab etc
                'name'                        => !empty($data['subs_name']) ? $data['subs_name'] : '?', // (string)    length: 45
                'msisdn'                      => $data['subs_mobile'], // (string)    length: 11, Example: 01717500400
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
                'msisdn'                      => $data['guardian_mobile'], // (string)    length: 11, Example: 01717500400
                'primary_msisdn'              => $data['subs_mobile'], // (string)    length: 11, Example: 01717500400
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

    private function _paygoSearchPrimaryDeregistration($data)
    {        
        $errMsg = '';
        $date = date('Y-m-d H:i:s');
        
        $req = array(
            '_token'            => $this->session->userdata('api_token'),
            'msisdn'            => $data['subs_mobile_number'],
            'request_channel'   => 'DEREG PRIMARY SUBSCRIBER',
            'dereg_reason'      => $data['dereg_reason'],
        );
        $this->logger->setInfoLog('PAYGO BRIDGE DEREG PRIMARY API URL > ' . $this->urlDegistrationApi);
        $this->logger->setInfoLog('PAYGO BRIDGE DEREG PRIMARY REQ > ' . json_encode($req));  
        
        $json = $this->mamalib->submitPostData($this->urlDegistrationApi, $req);
        $this->logger->setInfoLog('PAYGO BRIDGE DEREG PRIMARY RESP > ' . $json);
        
        $tmp = json_decode($json, true);
        
        if(isset($tmp['status']) && $tmp['status'] == 'success'){
            ## Update Primary Subscriber Deregistration Status in PMRS db
            $this->subscribermodel->updPmrsDeregStatus($data['subscriber_base_reg_id'], $data['subs_mobile_number'], $data['dereg_reason']);
            // Update GUARDIAN Deregistration Status in PMRS db
            if(!(empty($data['guardian_base_reg_id']) || is_null($data['guardian_base_reg_id']))) {
                $this->subscribermodel->updPmrsDeregStatus($data['guardian_base_reg_id'], $data['guardian_mobile_number'], $data['dereg_reason']);
            }
            $errMsg = '';
        } else{
            $errMsg = isset($tmp['errors']) ? implode(", ", $tmp['errors']) : 'Unknown';
        }
        
        $this->subscribermodel->impCallCenterReq($data, array(), 'DEREG PRIMARY SUBSCRIBER', $date, $errMsg);
    }
    
    private function _paygoSearchGguardianDeregistration($data)
    {
        $date = date('Y-m-d H:i:s');
        $errMsg = '';
        
        $req = array(
            '_token'            => $this->session->userdata('api_token'),
            'msisdn'            => $data['guardian_mobile_number'],
            'request_channel'   => 'DEREG GUARDIAN',
            'dereg_reason'      => $data['dereg_reason'],
        );
        
        $this->logger->setInfoLog('PAYGO BRIDGE DEREG GUARDIAN URL > ' . $this->urlDegistrationApi);
        $this->logger->setInfoLog('PAYGO BRIDGE DEREG GUARDIAN REQ > ' . json_encode($req));
        
        $json = $this->mamalib->submitPostData($this->urlDegistrationApi, $req);
        $this->logger->setInfoLog('PAYGO BRIDGE DEREG GUARDIAN RESP > ' . $json);
        
        $tmp = json_decode($json, true);
        
        if(isset($tmp['status']) && $tmp['status'] == 'success'){
            ## Update Guardian Deregistration Status in PMRS db
            $this->subscribermodel->updPmrsDeregStatus($data['guardian_base_reg_id'], $data['guardian_mobile_number'], $data['dereg_reason']);
            $errMsg = '';
        } else{
            $errMsg = isset($tmp['errors']) ? implode(", ", $tmp['errors']) : 'Unknown';
        }
        
        $this->subscribermodel->impCallCenterReq($data, array(), 'DEREG GUARDIAN', $date, $errMsg);
    }
    
    private function _prepaidBridgeUpdateForSearchData($data, $old_data)
    {
        $jp = array(
            'Sub_Id' =>  $data['subscriber_base_reg_id'], 
            'MSISDN' => $data['subs_mobile'], 
            'Package'=> $old_data['subs_type'], 
            'New_Value'=> array(
                'Name'=> $data['subs_name']!=$old_data['subs_name'] ? $data['subs_name'] : '', 
                'Designated_Date'=> $data['dt_lmd_dob']!=$old_data['dt_lmd_dob'] 
                                        ? $data['dt_lmd_dob'].' 00:00:00' : '', 
                'Subscriber_Type'=> $data['subscriber_type']!=$old_data['subscriber_type'] 
                                        ? isset($this->subscriber_types[$data['subscriber_type']]) ? $this->subscriber_types[$data['subscriber_type']] : 'Primary'
                                        : '', 
                'Timeslot'=> $data['timeslot']<>$old_data['timeslot'] ? 'R'.$data['timeslot'] : '',
                'Dialect'=> $data['dialect']<>$old_data['dialect']
                                    ? isset($this->dialects[$data['dialect']]) ? $this->dialects[$data['dialect']] : 'Rural'
                                    : '', 
                'Package'=> $data['subs_type']<>$old_data['subs_type'] ? strtolower($data['subs_type']) : '',
            ),
        );
        $json = json_encode($jp);
        $this->logger->setInfoLog('BRIDGE UPD REQ PRIMARY > ' . $json);
        
		$response = $this->mamalib->bridgePost($this->urlBridgePrepaidUpdate, $json);
        $response = $this->mamalib->cleanJson($response);
        $this->logger->setInfoLog('BRIDGE UPD RESP PRIMARY > ' . $response);
		$tmp = json_decode($response, true);
        
        if($tmp['errorCode']==200){
            $data['package_expire_date'] = isset($tmp['package_expire_date']) ? $tmp['package_expire_date'] : null;
            $this->subscribermodel->updPrepaidSubscriberInfo($data, 'Primary');
            
        }
        
        if(!(empty($data['guardian_base_reg_id']) || is_null($data['guardian_base_reg_id']))) {
            $jg = array(
                'Sub_Id' =>  $data['guardian_base_reg_id'], 
                'MSISDN' => $data['guardian_mobile'], 
                'Package'=> $old_data['subs_type'], 
                'New_Value'=> array(
                    'Name'=> $data['guardian_name']!=$old_data['guardian_name'] ? $data['guardian_name'] : '', 
                    'Designated_Date'=> $data['dt_lmd_dob']!=$old_data['dt_lmd_dob'] 
                                            ? $data['dt_lmd_dob'].' 00:00:00' : '', 
                    'Subscriber_Type'=> $data['guardian_type']!=$old_data['guardian_type'] 
                                            ? isset($this->subscriber_types[$data['guardian_type']]) ? $this->subscriber_types[$data['guardian_type']] : 'Gatekeeper_Generic'
                                            : '', 
                    'Timeslot'=> $data['timeslot']<>$old_data['timeslot'] ? 'R'.$data['timeslot'] : '',
                    'Dialect'=> $data['dialect']<>$old_data['dialect']
                                        ? isset($this->dialects[$data['dialect']]) ? $this->dialects[$data['dialect']] : 'Rural'
                                        : '', 
                    'Package'=> $data['subs_type']<>$old_data['subs_type'] 
                                        ? $data['subs_type'] : '',
                ),
            );
            $json = json_encode($jg);
            $this->logger->setInfoLog('BRIDGE UPD REQ GUARDIAN > ' . $json);
            $response = $this->mamalib->bridgePost($this->urlBridgePrepaidUpdate, $json);
            $response = $this->mamalib->cleanJson($response);
            $this->logger->setInfoLog('BRIDGE UPD RESP GUARDIAN > ' . $response);
            $tmp = json_decode($response, true);
            if($tmp['errorCode']==200){
                $data['package_expire_date'] = isset($tmp['package_expire_date']) ? $tmp['package_expire_date'] : null;
                $this->subscribermodel->updPrepaidSubscriberInfo($data, 'Guardian');
            }
        }
    }
    
    
    private function _prepaidSearchPrimaryDeregistration($data)
    {
        $j = array(
            'Subscriber_Id' => $data['subscriber_base_reg_id'],
            'Subscriber_Type' => 'Primary',
        );
        if(!(empty($data['guardian_base_reg_id']) || is_null($data['guardian_base_reg_id']))) {
            $j['Gurdian_Id'] = $data['guardian_base_reg_id'];
            $j['Gurdian_Type'] = 'Gatekeeper_Generic';
        }
        
        $json = json_encode($j);
        $this->logger->setInfoLog('PREPAID BRIDGE PRIMARY DEREG REQ > ' . $json);
		$response = $this->mamalib->bridgePost($this->urlBridgePrepaidDereg, $json);
        ## REMOVING EXTRA CHARACTERS FROM JSON
        $response = $this->mamalib->cleanJson($response);
        $this->logger->setInfoLog('PREPAID BRIDGE PRIMARY DEREG RESP > ' . $response);
        
		$tmp = json_decode($response, true);
        if(isset($tmp['errorCode']) && $tmp['errorCode']==200){
            // DEREG PRIMARY
            $this->subscribermodel->updPmrsDeregStatus($data['subscriber_base_reg_id'], $data['subs_mobile'], $data['dereg_reason']);
            // DEREG GUARDIAN
            if(!(empty($data['guardian_base_reg_id']) || is_null($data['guardian_base_reg_id']))) {
                $this->subscribermodel->updPmrsDeregStatus($data['guardian_base_reg_id'], $data['guardian_mobile'], $data['dereg_reason']);
            }
        }
    }
    
    
    private function _prepaidSearchGguardianDeregistration($data)
    {
        if(!(empty($data['guardian_base_reg_id']) || is_null($data['guardian_base_reg_id']))) {
            $j = array(
                'Subscriber_Id' => $data['guardian_base_reg_id'],
                'Subscriber_Type' => 'Gatekeeper_Generic',
            );
            $json = json_encode($j);
            $this->logger->setInfoLog('PREPAID BRIDGE GUARDIAN DEREG REQ > ' . $json);
            $response = $this->mamalib->bridgePost($this->urlBridgePrepaidDereg, $json);
            ## REMOVING EXTRA CHARACTERS FROM JSON
            $response = $this->mamalib->cleanJson($response);
            $this->logger->setInfoLog('PREPAID BRIDGE GUARDIAN DEREG RESP > ' . $response);
            $tmp = json_decode($response, true);
            if(isset($tmp['errorCode']) && $tmp['errorCode']==200){
                $this->subscribermodel->updPmrsDeregStatus($data['guardian_base_reg_id'], $data['guardian_mobile'], $data['dereg_reason']);
                //$this->subscribermodel->removePrimaryGuardianLink($data['subscriber_base_reg_id'], $data['guardian_base_reg_id']);
            }
        }
    }
    
    private function getApiToken()
    {
        if(!$this->session->userdata('api_token')){
            $json = $this->mamalib->submitPostData($this->urlTokenApi, array('username'=>'bridge' ,'password'=>'6digit'));
            $tmp = json_decode($json, true);
            if(isset($tmp['_token'])){
                $this->session->set_userdata('api_token', $tmp['_token']);
            }
        }
    }
    
    public function search()
    {
        ## Confirm authentic user is accessing this page
        if(!in_array($this->session->userdata('page_access_token'), array('2','3'))){
            redirect($this->config->item('base_url').'subscriber/add', 'location');
            exit;
        }
        $this->load->library('logger');
        $this->logger->setLogFile('/var/www/html/forms/logs/search/'.date('Y').'/'.date('m').'/search_'.date('Ymd').'.log');
        
        $data = array();
        $data['err'] = array();
        
        $data['subs_mobile'] ='';
        $data['guardian_mobile'] ='';
        $this->getApiToken();
        
        $data['success_msg'] = $this->session->flashdata('success_msg');
        $data['error_msg'] = isset($data['error_msg']) ? $data['error_msg']: $this->session->flashdata('error_msg');

        $data['msisdn'] = $this->input->get('msisdn');
        $data['type'] = $this->input->get('type');
        $data['id'] = $this->input->get('id');
        $old_data = $this->subscribermodel->listSubscriberDataBySearch($data['id'], $data['msisdn'], $data['type']);
        $data = array_merge($data, $old_data);
        
//        echo '<pre>';
//        var_export($old_data);
//        exit;
        
        if(!empty($data['id']) && empty($old_data)){
            redirect($this->config->item('base_url').'subscriber/search', 'location');
            exit;
        }
        
        //$subscription_type = $data['subscription_type'];
        
        $data['subs_guard_same_mobile'] = $data['subs_mobile']==$data['guardian_mobile'] ? 1: 0;
        
        $data['subscriber_types'] = $this->mamalib->listSubscriberTypes();
        //$data['subs_type'] = $this->input->post('subs_type');
        
        $data['division'] = isset($data['division']) ? $data['division'] : null;
        $data['district'] = isset($data['district']) ? $data['district'] : null;
        $data['upazilla'] = isset($data['upazilla']) ? $data['upazilla'] : null;
        
        $data['divisions'] = $this->MamaModel->listDivisions2();
        $data['districts'] = $this->MamaModel->listDistrictsByDivisionId2($data['division']);
        $data['upazillas'] = $this->MamaModel->listUpazillasByDistId2($data['district']);
        $data['unions'] = $this->MamaModel->listUnionsByUpazillaId2($data['upazilla'] );
        $data['serviceModels'] = $this->mamalib->listServiceModels();
        $data['relationships'] = $this->mamalib->listRelationships2();
        $data['yes_no'] = $this->mamalib->listYesNo();
        $data['dialects'] = $this->mamalib->listDialects();
        $data['subscription_types'] = $this->subscription_types;

        $data['dereg_opt'] = $this->input->post('dereg_opt', TRUE);
        
        if($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['search'])) {
            $data['msisdn'] = $this->input->post('msisdn', TRUE);
            $data['type'] = $this->input->post('type', TRUE);
            $id = $this->subscribermodel->getIdByMsisdnAndType($data['msisdn'], $data['type']);
            redirect($this->config->item('base_url').'subscriber/search'."?id={$id}&msisdn={$data['msisdn']}&type={$data['type']}", 'location');
            exit;
        }
        
        if($this->input->server('REQUEST_METHOD') == 'POST' && !empty($_POST['search_submit'])) {
            $data['msisdn'] = $this->input->get('msisdn');
            $data['type'] = $this->input->get('type');
            $data['id'] = $this->input->get('id');
            $sbtInputs = $this->input->post(NULL, TRUE);
            $sbtInputs['mb_date'] = $this->mamalib->convertArrayToInt($sbtInputs['tx_lmd_dob']);
            
            $data = array_merge($data, $sbtInputs);
            $data = $this->mamalib->trimArray($data);
            //$data['subscription_type'] = $subscription_type;
            
            $data['subs_id'] = $old_data['subs_id'];
            $data['guardian_id'] = $old_data['guardian_id'];
            $data['subscriber_base_reg_id'] = $old_data['subscriber_base_reg_id'];
            $data['guardian_base_reg_id'] = $old_data['guardian_base_reg_id'];
            
             ## YYYY-MM-DD
            $data['dt_lmd_dob'] = $data['tx_lmd_dob'][4].$data['tx_lmd_dob'][5].$data['tx_lmd_dob'][6].$data['tx_lmd_dob'][7].'-'.$data['tx_lmd_dob'][2].$data['tx_lmd_dob'][3].'-'.$data['tx_lmd_dob'][0].$data['tx_lmd_dob'][1];
            
            if($old_data['subscription_type']=='PAYGO'){
                $data['subs_mobile_number'] = implode('', $data['subs_mobile']);
                $data['guardian_mobile_number'] = implode('', $data['guardian_mobile']);
            }
            $data['subs_mobile_number_old'] = $old_data['subs_mobile'];
            $data['guardian_mobile_number_old'] = $old_data['guardian_mobile'];
            
            $data['timeslot'] = (int) $this->input->post('timeslot');
            
            $data['err'] = $this->_validateSearchFormInputs($data, $old_data['subscription_type']);
            
            // data after validation
            if($old_data['subscription_type']=='PAYGO'){
                //$data['subs_mobile'] = implode('', $data['subs_mobile']);
                //$data['guardian_mobile'] = implode('', $data['guardian_mobile']);
            }
            $data['guardian_rcv_info'] = strlen($data['guardian_mobile_number'])==11 ? 'YES' : 'NO';
            $data['subs_guard_same_mobile'] = $data['subs_mobile_number']==$data['guardian_mobile_number'] ? 1 : 0;
            if(empty($data['err'])) {
                $msg = $this->_processSearchOperation($data['id'], $old_data, $data);
                $this->session->set_flashdata('success_msg', $msg);
                redirect($this->config->item('base_url').'subscriber/search'."?id={$data['id']}&msisdn={$data['msisdn']}&type={$data['type']}", 'location');
                exit;
            } else{
                $data['error_msg'] = 'Please enter data correctly!';
            }
        }
        
        $data['time_slots'] = $this->timeSlots;
        
        /*$data['subscriber_actions'] = $this->subscriberActions;
        if(isset($data['srch_rslt']['subscriber_type']) && $data['srch_rslt']['subscriber_type']=='New Mother'){
            unset($data['subscriber_actions'][1]);
        }*/
        
        $data['page'] = $this->_getsearch_page_template(
            isset($data['status']) ? $data['status'] : '',
            isset($data['subscription_type']) ? $data['subscription_type'] : ''
        );
        
//        die($data['page']);
                
        $this->load->view('template/layout1', $data);
    }
    
    private function _searchPostOperationPrepaid($id, $msisdn, $type){
        
    }
    
    private function _searchPostOperationPaygo($id, $msisdn, $type){
        
    }
    
    private function _getsearch_page_template($status, $subscription_type)
    {
        $page = 'subscriber/search';
        
        if($status == 'Registered'){
            $page = $subscription_type=='PREPAID' ? 'subscriber/search_prepaid' : 'subscriber/search_paygo';
        } else{
            $page = 'subscriber/search_inactive';
        }
        
		return $page;
    }
    
    private function _chkBridgeParamChanged($old, $new){
        $modified = false;
        
        if($old['subs_type'] != $new['subs_type']){
            $modified = true;
        }
        elseif($old['subs_name'] != $new['subs_name']){
            $modified = true;
        }
        elseif($old['guardian_name'] != $new['guardian_name']){
            $modified = true;
        }
        elseif($old['dt_lmd_dob'] != $new['dt_lmd_dob']){
            $modified = true;
        }
        elseif($old['subs_mobile'] != $new['subs_mobile']){
            $modified = true;
        }
        elseif($old['guardian_mobile'] != $new['guardian_mobile']){
            $modified = true;
        }
        elseif($old['guardian_type'] != $new['guardian_type']){
            $modified = true;
        }
        elseif($old['channel'] != $new['channel']){
            $modified = true;
        }
        elseif($new['channel']=='IVR' && $old['timeslot'] != $new['timeslot']){
            $modified = true;
        }
        elseif($old['dialect'] != $new['dialect']){
            $modified = true;
        }
        
        return $modified;
    }
    
    private function _processSearchOperation($id, $old_data, $new_data){
        
        $new_data['now'] = date('Y-m-d H:i:s');
        $msg = '';
        
//        echo __METHOD__;
//        echo '<hr>';
//        echo 'id: '. $id;
//        echo '<hr>';
//        echo '$old_data: <br>';
//        echo '<pre>'; var_export($old_data); echo '</pre>';
//        echo '<hr>';
//        echo '$new_data: <br>';
//        echo '<pre>'; var_export($new_data); echo '</pre>';
//        exit;
        
        if($old_data['subscription_type'] == 'PREPAID'){
            if(!empty($new_data['dereg_opt'])){
                if($new_data['dereg_opt']==1){
                    $this->_prepaidSearchPrimaryDeregistration($new_data);
                    $msg = 'Subscriber deleted successfully';
                }
                elseif($new_data['dereg_opt']==2){
                    $this->_prepaidSearchGguardianDeregistration($new_data);
                    $msg = 'Subscriber deleted successfully';
                }
            } else{
                $subscription_param_modified = $this->_chkBridgeParamChanged($old_data, $new_data);
                if($subscription_param_modified){
                    $this->_prepaidBridgeUpdateForSearchData($new_data, $old_data);
                    $msg = 'Subscriber information updated successfully';
                }
            }
        } else{ // PAYGO
            if(!empty($new_data['dereg_opt'])){
                if($new_data['dereg_opt']==1){
                    $this->_paygoSearchPrimaryDeregistration($new_data);
                    $msg = 'Subscriber deleted successfully';
                }
                elseif($new_data['dereg_opt']==2){
                    $this->_paygoSearchGguardianDeregistration($new_data);
                    $msg = 'Subscriber deleted successfully';
                }
            } else{
                $subscription_param_modified = $this->_chkBridgeParamChanged($old_data, $new_data);
                //echo '$subscription_param_modified: ';
                //var_dump($subscription_param_modified);
                //exit;
                if($subscription_param_modified){
                    $this->processBridgeUpdateForSearchData($new_data, $old_data);
                    $msg = 'Subscriber information updated successfully';
                }
            }
        }
        
        return $msg;
    }
    
    public function getPackage($subscriber_type, $is_primary=false, $guardian_type=3){
        $package = null;
        
        if($is_primary){
            switch ($subscriber_type) {
                case 'p':
                    $package = 'PREG'; break;
                case 'b':
                    $package = 'NEW_MOTHER'; break;
                case 'sp-1000':
                    $package = 'SHOGORBHA_1000_PREG'; break;
                case 'sb-1000':
                    $package = 'SHOGORBHA_1000_BABY'; break;
                case 's':
                    $package = 'SHOISHOB_2_5'; break;
            }
        } else{
            switch ($subscriber_type) {
                case 'p':
                    $package = 'PREG_GUARDIAN'; break;
                case 'b':
                    $package = 'NEW_MOTHER_GUARDIAN'; break;
                case 'sp-1000':
                    $package = 'SHOGORBHA_1000_PREG_GUARDIAN'; break;
                case 'sb-1000':
                    $package = 'SHOGORBHA_1000_BABY_GUARDIAN'; break;
            }
        }
        
        return $package;
    }
    
    public function processBridgeUpdateForSearchData($data, $old_data)
    {
        $data['request_channel'] = 'SUBSCRIBER UPDATE';
        $data['dereg_reason']  = 'SUBSCRIBER UPDATE';
        $data['mapping_reason'] = 'SUBSCRIBER UPDATE';
        $successMsg = 'Subscriber information updated successfully.';
        $newRegInfo = array();
        $apiErrors = '';
        
        $chkDateInvalidResaon = '';
        
//        echo __METHOD__;
//        echo '<br>';
//        echo '<pre>$data'; var_export($data);
        ## DEREGISTER FROMBRIDGE
        if($this->isSubscriberAvailableInBridge($data['subs_mobile_number'])){  // Data found in Bridge 
            $json = $this->_deregisterFromBridge($data['subs_mobile_number'], $data['request_channel'], $data['dereg_reason']);
        }
        if($this->isSubscriberAvailableInBridge($data['guardian_mobile_number'])){  // Data found in Bridge 
            $json = $this->_deregisterFromBridge($data['guardian_mobile_number'], $data['request_channel'], $data['dereg_reason']);
        }
        /*if($this->isSubscriberAvailableInBridge($data['subs_mobile_number_old'])){  // Data found in Bridge 
            $json = $this->_deregisterFromBridge($data['subs_mobile_number_old'], $data['request_channel'], $data['dereg_reason']);
        }
        if($this->isSubscriberAvailableInBridge($data['guardian_mobile_number_old'])){  // Data found in Bridge 
            $json = $this->_deregisterFromBridge($data['guardian_mobile_number_old'], $data['request_channel'], $data['dereg_reason']);
        }*/
        
        //$newRegInfo['primary'] = $this->_registerPrimarySubscriberToBridge($data);
        //Register Primary Subscriber in Bridge
        $package = $this->getPackage($data['subs_type'], true);
        $primary_reg_data = $this->prepNewRegSubscriberBridgeData($data, $package, 'primary');
        echo '<pre>$primary_reg_data: ';var_export($primary_reg_data); echo '</pre>'; //exit;
        $this->logger->setInfoLog('PRIMARY REG REQ > ' . json_encode($primary_reg_data));
        $json = $this->mamalib->submitPostData($this->urlRegistrationApi, $primary_reg_data);
        $this->logger->setInfoLog('PRIMARY REG RESP > ' . $json);
        $tmp = json_decode($json, true);
        if($tmp['status']=='success'){
            $newRegInfo['primary'] = array(
                'regId'     => "{$tmp['subscriber_id']}", 
                'msisdn'    => $tmp['msisdn'],
            );
        }
        
        if(isset($newRegInfo['primary']) 
                && !empty($data['guardian_mobile_number']) 
                && $data['subs_mobile_number'] != $data['guardian_mobile_number']
        ){
            $package = $this->getPackage($data['subs_type'], false);
            $guardian_reg_data = $this->prepNewRegSubscriberBridgeData($data, $package, 'guardian');
            echo '<pre>$guardian_reg_data: ';var_export($guardian_reg_data); echo '</pre>';
            
            $this->logger->setInfoLog('GUARDIAN REG REQ > ' . json_encode($guardian_reg_data));
            $json = $this->mamalib->submitPostData($this->urlRegistrationApi, $guardian_reg_data);
            $this->logger->setInfoLog('GUARDIAN REG RESP > ' . $json);
            $tmp = json_decode($json, true);
            if($tmp['status']=='success'){
                $newRegInfo['guardian'] = array(
                    'regId'     => "{$tmp['subscriber_id']}", 
                    'msisdn'    => $tmp['msisdn'],
                );
            }
        }
        
        $data['int_subscriber_type_key_primary'] = $this->subscribermodel->subscriberTypeKey($data['subs_type'], 'Primary');
        $data['int_subscriber_type_key_guardian'] = $this->subscribermodel->subscriberTypeKey($data['subs_type'], 'Gatekeeper');
        
        echo '<hr>$newRegInfo: ';
        echo '<pre>'; var_export($newRegInfo); echo '</pre>';
        echo '<pre>$old_data: ';var_export($old_data); echo '</pre>';
        echo '<pre>$data: ';var_export($data); echo '</pre>';
        exit;
        
        ## 3. Update subscriber's new RegID
        $this->subscribermodel->updSubsNewRegId($data, $newRegInfo, $data['now']);
        $this->logger->setInfoLog('3. PMRS DOB UPD > Update Subscriber Data with newRegId and DOB');
        
        
         ## 4. Map
         $this->subscribermodel->impMappingHistory($data['subscriber_base_reg_id'], $newRegInfo['primary']['regId'], $data['mapping_reason'], $data['now']);
         $this->logger->setInfoLog('4.1 PMRS DOB UPD MappingHistory > Primary Subscriber new RegId updated.');

         if(!empty($newRegInfo['guardian']['regId'])){
             $this->subscribermodel->impMappingHistory($data['guardian_base_reg_id'], $newRegInfo['guardian']['regId'], $data['mapping_reason'], $data['now']);
             $this->logger->setInfoLog('4.2 PMRS DOB UPD MappingHistory > Guardian new RegId updated.');
         }

        ### DOB UPD SUCCESS, REDIRECT FROM HERE
        $errorMsg = isset($tmp['errors']) ? implode(", ", $tmp['errors']) : '';
        $this->subscribermodel->impCallCenterReq($data, $newRegInfo, $data['mapping_reason'], $data['now'], $errorMsg);
        exit;
        
        $this->session->set_flashdata('success_msg', 'Subscriber information updated successfully.');
        redirect('subscriber/search?msisdn='.$data['msisdn'], 'location');
        exit;
        //return 'DOB updated successfully';
    }
    
    private function _validateFormInputs($data, $excludeId = 0) {
        $errors = array();
        
        if(empty($data['ds_gateway'])) {
            $errors['ds_gateway'] = 'Gateway is empty!';
        }
        if(empty($data['dt_medium'])) {
            $errors['dt_medium'] = 'Medium is empty!';
        }
        if(empty($data['ds_reg_type'])) {
            $errors['ds_reg_type'] = 'Registration Type is empty!';
        }
        if($data['ds_reg_type']=='Self' && empty($data['ds_information_source'])) {
            $errors['ds_information_source'] = 'Information Source is empty!';
        }
		
        if(empty($data['ca_id'])) {
            $errors['ca_id'] = 'CA ID is empty!';
        }
        else if(!$this->subscribermodel->isCaIdExists($data['ca_id'])) {
            $errors['ca_id'] = 'CA ID does not exist!';
        }       
     
        if(empty($data['subs_type'])) {
            $errors['subs_type'] = 'Subscriber Type is empty!';
        }
        
        if(empty($data['subs_name'])) {
            $errors['subs_name'] = 'Subscriber\'s Name is empty!';
        }
        /*
        if(empty($data['subs_age'])) {
            $errors['subs_age'] = 'Subscriber\'s Age is empty!';
        }*/
        
        ## 11. Validate mb_date
        
        if(!$this->mamalib->isValidDate($data['mb_date'])) {
            $errors['mb_date'] = 'Blank or Invalid Date!';
        }
        else if($this->mamalib->ifTodayExceeds($data['mysql_mb_date'])) {
            $errors['mb_date'] = 'Date value should not exceed today\'s date!';
        }else {
            $totDays = $this->mamalib->countDays( $data['mysql_mb_date'], date('Y-m-d') );
            $errors =  $this->mamalib->dateValidity($totDays, $data['subs_type_rad']);
                       
         }
        
        ## 12. Validate subscriber mobile number
        if(!$this->mamalib->isValidMobile($data['subs_mobile'])) {
            $errors['subs_mobile'] = 'Blank / Invalid Mobile Number!';
        }else if($this->subscribermodel->isFakeMobile($data['subs_mobile_number'])) {
                $errors['subs_mobile'] = 'This number is Fake!';
        }else if($this->subscribermodel->isMobileNumberExists($data['subs_mobile_number'], $excludeId)) {
            $errors['subs_mobile'] = "Number Exists.";
        }
        
        ## 12(a). Validate Family Member's mobile number
        if(!empty($data['fam_mem_mobile_number'])) {
            if(!$this->mamalib->isValidMobile($data['fam_mem_mobile'])) {
                $errors['fam_mem_mobile'] = 'Blank / Invalid Mobile Number!';
            }
			else if($this->subscribermodel->isFakeMobile($data['fam_mem_mobile_number'])) {
				$errors['fam_mem_mobile'] = 'This number is Fake!';
			}
            else if($this->subscribermodel->isMobileNumberExists($data['fam_mem_mobile_number'], $excludeId)) {
                $errors['fam_mem_mobile'] = "Number Exists.";
            }
        }
        
        //13.  service_model_rad == r >> ISSET srvc_schd_rad 
        if(empty($data['service_model_rad'])) {
            $errors['service_model'] = 'Service Model is Empty!';
        }
        else if($data['service_model_rad']=='r' && empty($data['srvc_schd_rad'])) {
            $errors['srvc_schd_rad'] = 'Service Schedule is Empty!';
        }
        
        //14. fam_mem_rcv_inf_w_rad  >> VALIDATE fam_mem_mobile & fam_mem_relation_rad
        if(empty($data['fam_mem_rcv_inf_w_rad'])) {
            $errors['fam_mem_rcv_inf_w_rad'] = 'Empty field!';
        }
        else if($data['fam_mem_rcv_inf_w_rad']=='YES') {
            if(!$this->mamalib->isValidMobile($data['fam_mem_mobile'])) {
                $errors['fam_mem_mobile'] = 'Blank / Invalid Mobile Number!';
            }
            if(empty($data['fam_mem_relation_rad'])){
                $errors['fam_mem_relation_rad'] = 'Relationship field is Empty!';
            }
        }
        
        // Validate GV_CODE (only in ADD mode. GV validation is not required in Subscriber data EDIT mode.)
        if($excludeId==0){
            $tmp  = $this->mamalib->chkGvValidity($data['gv_code'], $data['subs_type_rad']);
            if(!empty($tmp)){
                $errors['gv_code'] = $tmp;
            }
        }
       
        return $errors;
    }
    
    private function _validateSearchFormInputs($data, $subscription_type) 
    {
        $errors = array();
        
        if(!empty($data['dereg_opt'])){
            if(empty($data['dereg_reason'])){
                $errors['dereg_reason'] = 'Deregistration reason is empty!';
            }
        } else{
            if($subscription_type=='PAYGO'){
                ## Validate subscriber's mobile number
                if(!$this->mamalib->isValidMobile($data['subs_mobile'])) {
                    $errors['subs_mobile'] = 'Invalid Mobile Number!';
                } elseif($this->subscribermodel->existsInPmrsCenterTable($data['subs_mobile_number'], $data['id'])) {
                    $errors['subs_mobile'] =  'Number Exists!';
                }

                ## Validate guardian's mobile number
                if(isset($data['guardian_mobile_number']) && !empty($data['guardian_mobile_number'])) {
                    if(!$this->mamalib->isValidMobile($data['guardian_mobile'])) {
                        $errors['guardian_mobile'] = 'Invalid Mobile Number!';
                    } elseif($data['subs_mobile_number']!=$data['guardian_mobile_number'] && $this->subscribermodel->existsInPmrsCenterTable($data['guardian_mobile_number'], $data['guardian_id'])) {
                        $errors['guardian_mobile'] =  'Number Exists!';
                    }
                    
                    if(empty($data['guardian_type'])){
                        $errors['guardian_type'] = 'Guardian relationship type is empty!';
                    }
                }
                
                ## Validate Channel
                if(empty($data['channel'])){
                    $data['channel'] = 'Channel is empty!';
                }
            }
            
            if(empty($data['subs_type'])) {
                $errors['subs_type'] = 'Subscriber type is empty!';
            } elseif(!in_array($data['subs_type'], array('p', 'b', 'sp', 'sb', 'sh'))){
                $errors['subs_type'] = 'Invalid Subscriber Type!';
            }
            
            if(empty($data['subs_name'])) {
                $errors['subs_name'] = 'Subscriber name is empty!';
            }

            ## Validate mb_date if it is not Deregistration Request
            if(empty($data['dereg_opt'])){
                if(!$this->mamalib->isValidDate($data['tx_lmd_dob'])) {
                    $errors['mb_date'] = 'Blank or Invalid Date!';
                } else if($this->mamalib->ifTodayExceeds($data['dt_lmd_dob'])) {
                    $errors['mb_date'] = 'Date value should not exceed today\'s date!';
                } else {
                    $totDays = $this->mamalib->countDays($data['dt_lmd_dob'], date('Y-m-d'));
                    $tmp = $this->mamalib->dateValidity($totDays, $data['subs_type']);                       
                    if(!empty($tmp)){
                        $errors['mb_date'] = $tmp;
                    }
                }
            }

            if($data['channel']!='SMS' //&& empty($data['timeslot'])
                && !in_array($data['timeslot'], array(1,2,3,4))
            ) {
                $errors['timeslot'] = 'Invalid Timeslot!';
            }
        }
            
        return $errors;
    }
    
    /*private function saveLog($data) {
        //$urlFirstParm   = "http://202.22.194.71/M4H/RegistrationTab.php?username=apidnet&password=aponjon@123";
        //$urlSecondParam = "{$res['STID']}|{$res['LMD_DOB']}|{$res['OutdialPref']}|{$res['GuardianNum']}|{$res['HWID']}|{$res['GVCode']}|{$res['RelationWithGuardian']}|{$res['Name']}|{$res['Age']}|{$res['SubsNationalID']}|{$res['HSID']}|{$res['DID']}|{$res['DSID']}|{$res['UID']}|{$res['UNID']}|{$res['OSID']}|{$res['TFIncome']}|{$res['MExpense']}|{$res['HasChildlabour']}|{$res['BloodGroup']}|{$res['GuardiansNationalID']}|{$res['EDID']}|{$res['YearsOfEducation']}|{$res['OSIDFamilyHead']}|{$res['sanitaryLatrinAvailability']}|{$res['SrcDrnkWater']}|{$res['NumberOfMobilePhoneAtHome']}|{$res['WhichCellWillReceiveContent']}|{$res['DialectID']}";
        //$url            = $urlFirstParm.'&mn='.$res['MobNum'].'&msg='.str_replace(' ', '%20', $urlSecondParam);
        $lmd            = $data['subs_type_rad']=='p'      ? $data['dt_mb_date'] : '';
        $dob            = $data['subs_type_rad']=='b'      ? $data['dt_mb_date'] : '';
        $OutdialPref    = $data['service_model_rad']=='r'  ? $data['srvc_schd_rad'] : $data['service_model_rad'];
        $HasChildlabour = $data['schoolgoing_labour_rad']==='YES' ? '1' : '';
        
        $requests = "{$res['STID']}|{$res['LMD_DOB']}|{$res['OutdialPref']}|{$res['GuardianNum']}|{$res['HWID']}|{$res['GVCode']}|{$res['RelationWithGuardian']}|{$res['Name']}|{$res['Age']}|{$res['SubsNationalID']}|{$res['HSID']}|{$res['DID']}|{$res['DSID']}|{$res['UID']}|{$res['UNID']}|{$res['OSID']}|{$res['TFIncome']}|{$res['MExpense']}|{$res['HasChildlabour']}|{$res['BloodGroup']}|{$res['GuardiansNationalID']}|{$res['EDID']}|{$res['YearsOfEducation']}|{$res['OSIDFamilyHead']}|{$res['sanitaryLatrinAvailability']}|{$res['SrcDrnkWater']}|{$res['NumberOfMobilePhoneAtHome']}|{$res['WhichCellWillReceiveContent']}|{$res['DialectID']}";
    }*/
    
    private function _isCaIdValid($id) {
        return $this->subscribermodel->isCaIdExists($id) ? true : false;
    }
    
    public function dist_by_div() 
    {
        $divId      = $this->input->post('div_id');        
        $districts  = $this->MamaModel->listDistrictsByDivisionId($divId);
        
        echo '<option value="">Select</option>'.PHP_EOL;        
        foreach($districts as $key=>$val) 
        {
            echo '<option value="'.$key.'">'.$val.'</option>'.PHP_EOL;
        }
    }
    
    public function upz_by_dist() 
    {
        $dstId     = $this->input->post('dst_id');        
        $upazillas  = $this->MamaModel->listUpazillasByDistId($dstId);
        
        echo '<option value="">Select</option>'.PHP_EOL;
        foreach($upazillas as $key=>$val) 
        {
            echo '<option value="'.$key.'">'.$val.'</option>'.PHP_EOL;
        }
    }
    
    public function union_by_upz() 
    {
        $upzId      = $this->input->post('upz_id');        
        $unions     = $this->MamaModel->listUnionsByUpazillaId($upzId);
        
        echo '<option value="">Select</option>'.PHP_EOL;
        foreach($unions as $key=>$val) 
        {
            echo '<option value="'.$key.'">'.$val.'</option>'.PHP_EOL;
        }
    }
    
    public function checkNumberExists() {
        $mno    = trim($this->input->post('mno', true));        
        $mbArr 	= array_fill(0, 11, '');
        
        for($i=0; $i<strlen($mno); $i++){
            $mbArr[$i] = $mno[$i];
        }
        
        if(!$this->mamalib->isValidMobile($mbArr)) {
            echo '<div style="color:red;">Blank / Invalid Mobile Number!</div>';
        }
        else{
            if($this->subscribermodel->isMobileNumberExists($mno)) {
                echo '<div style="color:red;">Number Exists!</div>';
            }
            else if($this->subscribermodel->isFakeMobile($mno)) {
                echo '<div style="color:red;">Fake Number!</div>';
            }
            else {
                echo '<div style="color:green;">Number available for registration.</div>';
            }
        }
    }
    
    public function validate_gv($code = '', $subscriberType='') {
        if(empty($code)){
            echo 'Card is Empty';
        } else{
            $validity = $this->mamalib->chkGvValidity($code, $subscriberType);
            echo !empty($validity) ? $validity : 'Card is Valid';
        }
    }
    
    private function _updRegRequestBySms($id, $isSubscribed, $noReason, $parkReason, $aptDate, $aptTime, $remarks) {
        $tmp = $this->_getReqHandledAndReasonBySubscription($isSubscribed, $noReason, $parkReason);
        $data = array(
            'is_request_handled'        => $tmp['requestHandled'],
            'remarks'                   => $remarks,
            'is_subscribed'             => $isSubscribed,
            'reason_non_subscription'   => $tmp['reason'],
        );
        
        $aptTimeParam = '';
        if($isSubscribed == 4) {  // Appointed
            $aptDate = $this->_prepAppointmentDate($aptDate, $aptTime);
            if(!empty($aptDate)){
                $data['appointment_time'] = $aptDate;
            }
            $aptTimeParam = "a.appointment_time = ".$this->db->escape($aptDate).",";
        }

        // Save request data
        $this->db->update('t_reg_requests', $data, "reg_request_id = $id");

        // Save Audit data
        $sql = "UPDATE t_reg_requests_audit a
                JOIN t_reg_requests r ON r.reg_request_id = a.reg_request_id AND r.int_counter = a.int_counter
                SET a.is_request_handled = ".$this->db->escape($tmp['requestHandled']).",
                ".$aptTimeParam."
                a.is_subscribed = ".$this->db->escape($isSubscribed).",
                a.reason_non_subscription = ".$this->db->escape($tmp['reason']).",
                a.remarks = ".$this->db->escape($remarks)."
                WHERE a.reg_request_id = ".$this->db->escape($id);
        $this->db->query($sql);
    }
    
    private function _getReqHandledAndReasonBySubscription($isSubscribed, $noReason, $parkReason){
        switch($isSubscribed){
            case 1: 
                $ret['requestHandled'] =  1;
                $ret['reason'] = '';
                break;

            case 2: 
                $ret['requestHandled'] =  1;
                $ret['reason'] = $noReason;
                break;

            case 3: 
                $ret['requestHandled'] =  0;
                $ret['reason'] = $parkReason;
                break;

            default: 
                $ret['requestHandled'] =  0;
                $ret['reason'] = '';
                break;
        }
        return $ret;
    }
    
    private function _prepAppointmentDate($date, $time){
        $dateTime = trim($date .' '. $time);
        if(empty($dateTime)) 
            return '';        
        return date("Y-m-d H:i:s", strtotime($dateTime));
    }
}
