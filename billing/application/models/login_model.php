<?php

class login_model extends CI_Model { 

		

    function __construct() {
        parent::__construct();
        $this->load->database();

    }
    
    public function login($email,$password) {
        
        $hashedPass = hash('SHA256', $password, false);
        
        $sql = "select * from users where userEmail=? and userPassword=?";
            
        $query = $this->db->query($sql,array($email,$hashedPass));

        $result =$query->result();		

        return $result?$result[0]:false;
    }

	





	

}