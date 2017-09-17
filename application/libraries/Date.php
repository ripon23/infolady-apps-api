<?php

/**
 * Description of Date
 *
 * @author Md. Rafiqul Islam <rafiq.kuet@gmail.com>
 * @date March 01, 2017 15:01
 */
class Date {
    public function isValidDate($date)
    {   
        //return (\DateTime::createFromFormat('Y-m-d', $date) !== false) ? true : false;
        return (bool)strtotime($date);
    }
    
    public function isValidDateTime24HrFormat($date_time)
    {
        return (\DateTime::createFromFormat('Y-m-d H:i:s', $date_time) !== false) ? true : false;
    }
    
    public function isValidDateTime12HrFormat($date_time)
    {
        return (\DateTime::createFromFormat('Y-m-d h:i:s a', $date_time) !== false) ? true : false;
    }
    
    public function countDays($date1, $date2)
    {
        $date1 = new \DateTime($date1);
        $date1 = $date1->setTime(0,0,0); // reset time part, to prevent partial comparison
        
        $date2 = new \DateTime($date2);
        $date2 = $date2->setTime(0,0,0); // reset time part, to prevent partial comparison

        $interval = $date1->diff($date2);
        
        return $interval->days;
    }
    
    public function isFutureDate($date)
    {
        $today = new \DateTime(); // This object represents current date/time
        $today->setTime(0,0,0); // reset time part, to prevent partial comparison

        $match_date = \DateTime::createFromFormat( "Y-m-d", $date );
        $match_date->setTime(0,0,0); // reset time part, to prevent partial comparison

        $diff = $today->diff($match_date);
        $diffDays = (integer)$diff->format("%R%a"); // Extract days count in interval
        
        return $diffDays > 0 ? true : false;
    }
    
    public function isFutureDateTime($date_time)
    {
//        $formatted_time = new \DateTime($date_time);
//        $now = new \DateTime();
//        return $formatted_time > $now ? true : false;
        $now = time();
        return strtotime($date_time) > $now ? true : false;
    }
    
    public function isValidDateTimeString($str_dt, $str_dateformat) {
        $date = DateTime::createFromFormat($str_dateformat, $str_dt);
        return $date && DateTime::getLastErrors()["warning_count"] == 0 && DateTime::getLastErrors()["error_count"] == 0;
    }
    
    /**
     * Converts 02/27/2017 to 2017-27-02
     * @param string $date
     */
    public function dateDbFormat($date)
    {
        //$tmp = explode('/', $date);
        //return (isset($tmp[2])) ? $tmp[2].'-'.$tmp[1].'-'.$tmp[0] : '';
        return date('Y-m-d', strtotime($date));
    }
    
    /**
     * Converts 270217 to 2017-27-02
     */
    public function parseDateFromUserInput($str)
    {
        /*if(strlen($str)==5){
            $month = (int)substr($str,-4,2);
            if($month>=1 && $month<=12){
                $str = '0'.$str;
            } else{
                $str = substr($str,0,2).'0'.substr($str,2);
            }
        }elseif(strlen($str)==4){
            $str = '0'.substr($str,0,1).'0'.substr($str,1);
        }*/
        
        if(strlen($str)==4){
            $str = '0'.substr($str,0,1).'0'.substr($str,1);
        }
        
        return '20'.substr($str,4,2).'-'.substr($str,2,2).'-'.substr($str,0,2);
    }
}
