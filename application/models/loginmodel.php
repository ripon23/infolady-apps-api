<?php

/**
 * Description of LoginModel
 *
 * @author Rafiqul Islam <rafiqul.islam@dnet.org.bd>
 */
class LoginModel extends CI_Model {
    
    function __construct()
    {
        parent::__construct();
    }
    
    public function getLoggedInfo($uid, $pwd) {
        
        $data = array();
        
        $sql = "SELECT 
                    U.id
                  , U.role_id
                  , U.tx_name
                  , U.tx_organization
                  , U.tx_designation
                  , D.tx_level_token
                  , D.page_access_id
                  FROM users                          U
                  INNER JOIN e_dataentry_operators    D ON D.int_user_key = U.id
                  WHERE U.login   = ".$this->db->escape($uid)."
                    AND U.password  = MD5(CONCAT(".$this->db->escape($pwd).",U.salt))
                    AND U.is_active = 1
                  LIMIT 1";
        $q = $this->db->query($sql);
        if ($q->num_rows() > 0) {			
			$row = $q->result_array();
			$data = $row[0];
		}
		
		$q->free_result();
        
        return $data;
    }
    
    private function _getSalt($uid) {
        
        $salt = '';
        
        $this->db->select('salt');
		$q = $this->db->get_where('users', array('login'=>$uid));
		
		if ($q->num_rows() > 0) {
			$row = $q->result_array();
            $salt = $row[0]['salt'];
		}
		
		$q->free_result();
        
		return $salt;
    }
}