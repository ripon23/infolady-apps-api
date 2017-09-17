<?php

/**
 * Description of Reports
 *
 * @author Rafiqul Islam <rafiqul.islam@dnet.org.bd>
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Reports extends CI_Controller {
    
    public function __construct() {
         parent::__construct();
         
         if(!$this->mamalib->isLoggedIn()) {
             redirect('/login', 'location');
         }
         
         $this->load->model('reportsmodel');
    }
    
    public function index() {
        
        echo date('n') . '-' . date('Y');
        
        redirect('reports/summary/'.date('n').'/'. date('Y'), 'location');
    }
    
    public function summary($month='', $year='') {
        
        $data['months']     = $this->mamalib->listMonths();
        $data['month']      = !empty($month) ? $month : date('n');
        $data['year']       = !empty($year) ? $year : date('Y');
        $data['currYear']   = date('Y');
        
        $data['success_msg'] = $this->session->flashdata('success_msg');
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data['month']      = trim($this->input->post('month', true));
            $data['year']       = trim($this->input->post('year', true));
            
            redirect('reports/summary/'.$data['month'].'/'.$data['year'], 'location');
        }
        
        $data['reports']    = $this->reportsmodel->listSummaryReportById(
                                                      $this->session->userdata('id')
                                                    , $data['month']
                                                    , $data['year']
                                                );
        
        
        $data['totReport'] = array(
            'date' => 'Grand Total', 
            'verified' => 0,
            'approved' => 0,
            'synced' => 0,
            'failed' => 0,
            'denied' => 0,
            'queued' => 0,
            'parked' => 0,
            'tot' => 0
        );
        
        //$this->mamalib->dumpArray($data['reports']); die ('**');
        
        foreach($data['reports'] as $key=>$report) {
            $data['reports'][$key]['tot']    = $report['verified'] + $report['approved'] + $report['synced'] + $report['failed'] + $report['denied'] + $report['queued'] + $report['parked'];
            
            $data['totReport']['verified']  += $report['verified'];
            $data['totReport']['approved']  += $report['approved'];
            $data['totReport']['synced']    += $report['synced'];
            $data['totReport']['failed']    += $report['failed'];
            $data['totReport']['denied']    += $report['denied'];
            $data['totReport']['queued']    += $report['queued'];
            $data['totReport']['parked']    += $report['parked'];
            $data['totReport']['tot']       += $data['reports'][$key]['tot'];
        }
        //$this->mamalib->dumpArray($data); die ('**');
        
        
        
        $data['page']          = 'reports/summary';       
        $data['urlSummary']    = site_url('reports/summary/'.$data['month'].'/'. $data['year']);
        $data['urlDetails']    = site_url( 'reports/details/'. date('d-m-Y') .'/'. date('d-m-Y') .'/All' );
        //$data['urlDetails']    = site_url( 'reports/details/'. $this->mamalib->getFirstDateOfMonth() .'/'. $this->mamalib->getLastDateOfMonth() );
        
        //$this->mamalib->dumpArray($data);
        
        $this->load->view('template/layout1', $data);
    }
    
    public function details($from='', $to='', $status='', $page=1) {
        
        $data['months']     = $this->mamalib->listMonths();
        $data['statuses']   = $this->mamalib->listFormStatuses();
                
        //$data['date_from']      = !empty($from) ? $from : $this->mamalib->getFirstDateOfMonth();
        //$data['date_to']        = !empty($to)   ? $to   : $this->mamalib->getLastDateOfMonth();
        $data['date_from']      = !empty($from) ? $from : date('d-m-Y');
        $data['date_to']        = !empty($to)   ? $to   : date('d-m-Y');
        $data['status']         = !empty($status) ? $status : 'All';
        $data['page']           = $page;
        
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') { 
            $data['date_from']  = $this->_formatDateInput( trim($this->input->post('date_from', true)) );
            $data['date_to']    = $this->_formatDateInput( trim($this->input->post('date_to', true)) );
            
            if(empty($data['date_to'])) {
                $data['date_to'] = date('d-m-Y');
            }
            
            $data['status']     = $this->input->post('status', true);
            
            redirect('reports/details/'.$data['date_from'].'/'.$data['date_to'] .'/'.$data['status'], 'location');
        }
        
        if($this->mamalib->countDays($data['date_from'], $data['date_to'])>14){
            $data['error']      = 'Date range should not be no more than 2 weeks!';
            $data['reports']    = array();
            $data['pagination'] = '';
        }
        else{
            $data['reports']    = $this->reportsmodel->listDetailsReports(
                                      $this->session->userdata('id')
                                    , $this->mamalib->dateMysqlFormat($data['date_from'])
                                    , $this->mamalib->dateMysqlFormat($data['date_to'])
                                    , $data['status']
                                    , $data['page']
                                  );
            foreach($data['reports'] as $key=>$report) {
                $tmp = $this->reportsmodel->getStatusMessage($report['Id']);
                $data['reports'][$key]['error_code']    = $tmp['error_code'];
                $data['reports'][$key]['reason']        = $tmp['reason'];
                
                if($this->reportsmodel->isFakeMobile($report['MobNum'])) {
                    $data['reports'][$key]['fake']   = 1;
                }
            }
            // $this->mamalib->dumpArray($data['reports']); exit;
            
            $this->load->library('pagination');
            $config['base_url']         = site_url( 'reports/details/'. $data['date_from'] .'/'. $data['date_to'] .'/'. $data['status']);
            $config['total_rows']       = $this->reportsmodel->totRowsDetailsReports(
                                            $this->session->userdata('id')
                                          , $this->mamalib->dateMysqlFormat($data['date_from'])
                                          , $this->mamalib->dateMysqlFormat($data['date_to'])
                                          , $data['status']
                                        );
            $config['per_page']         = PER_PAGE; 
            $config['uri_segment']      = 6;
            $config['use_page_numbers'] = TRUE;
            $this->pagination->initialize($config); 

            $data['pagination']    =  $this->pagination->create_links();
        }
        $data['page_no'] = $page;
        $data['page']           = 'reports/details';
        $data['urlSummary']     = site_url('reports/summary/'.date('n').'/'. date('Y'));
        $data['urlDetails']     = site_url( 'reports/details/'. $data['date_from'] .'/'. $data['date_to'].'/All' );
        
        //$this->mamalib->dumpArray($data); exit;
        
        
        $this->load->view('template/layout1', $data);
    }
    
    private function _formatDateInput($date) {
        return str_replace('/', '-', $date);
    }
}

