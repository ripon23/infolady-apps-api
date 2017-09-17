<?php

/**
 * Writes Log
 *
 * @author Rafiqul Islam <rafiqul.islam@dnet.org.bd>
 */

class Logger {
    
    public $obj     = null;
    private $file   = null;
    
    public function __construct() {
        
        $this->obj =& get_instance();
    }
    
    public function setLogFile($file){
		
        $this->file     = $file;
        $this->confirmDirExists();        
    }
        
    
    private function confirmDirExists() {
    
        $dirName = dirname($this->file);
        
        if (!is_dir($dirName)) {
            mkdir($dirName, 0777, true);
        }
    }
    
    public function setInfoLog($msg) {
        file_put_contents(
            $this->file, 
            date('Y-m-d H:i:s'). ' INFO : ' . $msg . PHP_EOL, 
            FILE_APPEND
        );
    }
    
    public function setErrLog($msg) {
        file_put_contents(
            $this->file, 
            date('Y-m-d H:i:s'). ' ERROR : ' . $msg . PHP_EOL, 
            FILE_APPEND
        );
    }
    
    public function setNoticeLog($msg) {
        file_put_contents(
            $this->file, 
            date('Y-m-d H:i:s'). ' NOTICE : ' . $msg . PHP_EOL, 
            FILE_APPEND
        );
    }
    
    public function setWarnLog($msg) {
        file_put_contents(
            $this->file, 
            date('Y-m-d H:i:s'). ' WARNING : ' . $msg . PHP_EOL, 
            FILE_APPEND
        );
    }
}
