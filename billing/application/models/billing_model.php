<?php

class billing_model extends CI_Model { 
    private $billingdb;

    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->billingdb=$this->load->database('billingdb',TRUE);

    }
    
    public function shopper_exists($userID) {
            
        $sql = "select * from bluesnap_shoppers where userID=?";
            
        $query = $this->db->query($sql,array($userID));

        $result =$query->result();		

        return isset($result[0])?true:false;
       
    }
    
    public function get_shopper($userID) {
            
        $sql = "select * from bluesnap_shoppers where userID=?";
            
        $query = $this->db->query($sql,array($userID));

        $result =$query->result();		

        return isset($result[0])?$result[0]:false;
       
    } 
    
    public function saveShopper($data) {
        
        $sql = "INSERT INTO bluesnap_shoppers (userID,organizationID,shopper_id,firstName,lastName,email,address1,city,state,country,zipCode,phone,insert_time) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";  

        $r=$this->db->query($sql,$data);

        return  $this->db->insert_id();
    }
    
    public function get_all_shppers() {
        $sql = "select * from bluesnap_shoppers";
            
        $query = $this->db->query($sql);

        $result =$query->result();
        
        return $result;
    }
    
    public function get_contracts() {
        $sql = "select * from bluesnap_contracts";
            
        $query = $this->db->query($sql);

        $result =$query->result();
        
        return $result; 
    }
    
    public function insert_history($userID,$shopperId,$type,$response) {
        
        $response=mysql_real_escape_string($response);
        
        $t=time();
        
        $sql = "INSERT INTO billing_history (id,userID,shopperId,type,response,time) VALUES (NULL,$userID,$shopperId,$type,$response,$t)";  

        $r=$this->db->query($sql);

        return  $this->db->insert_id();
    }

	





	

}