<?php

class users_model extends CI_Model { 

		

    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->billingdb=$this->load->database('default',TRUE);

    }
    
    public function get_user($userID) {

        
        $sql = "select * from users where userID=?";
            
        $query = $this->db->query($sql,array($userID));

        $result =$query->result();		

        return $result?$result[0]:false;
    }
    
    public function get_users_kind2() {

        
        $sql = "SELECT
                users.userName,
                users.userEmail,
                organizations.organizationName,
                users.userID,
                organizations.organizationID
                FROM
                users
                INNER JOIN organizations ON users.userOrganizationIdSelect = organizations.organizationID
                WHERE
                users.userUserKindID > ?";
            
        $query = $this->db->query($sql,array(1));

        $result =$query->result();		

        return $result;
    }

	





	

}