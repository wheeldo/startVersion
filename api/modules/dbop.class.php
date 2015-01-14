<?php

define("DB_HOST","localhost");

class dbop {
	protected function cleanQuery($query) {		
		return  mysql_real_escape_string($this->cleanBadWords($query));
		}
		
	protected function cleanBadWords($query) {
		$badWords = array("/delete/i","/alter/i","/select/i", "/update/i","/union/i","/insert/i","/drop/i","/--/i");
		//return preg_replace($badWords, "", $query);
		return $query;
	}
		
	public function __construct() {
		$sqldb=@mysql_connect(DB_HOST,USER,PASSWORD) or die("error - unable to connect to database server");
		@mysql_select_db(DATABASE,$sqldb) or die("error - unable to select database");
		@mysql_query("SET NAMES utf8");
	}
	
	public function insertDB($tname,$fields,$setAutoId=true) {
		$tname=$this->cleanQuery($tname);	
		$sql="INSERT INTO `$tname` SET ";
		$c=0;
		foreach($fields as $fieldName => $fieldValue) {
			$fieldName=$this->cleanQuery($fieldName);
			$fieldValue=$this->cleanQuery($fieldValue);
			if($fieldName=="op") continue;
			$c++;
                        if(!$setAutoId && count($fields)==$c)
                            $sql.=" `$fieldName`='$fieldValue'  ";
                        else 
                            $sql.=" `$fieldName`='$fieldValue' , ";
                        
		}
		if($setAutoId) $sql.=" `id`=NULL  ";
		//echo $sql;
		$result=mysql_query($sql);
		return ($result) ? mysql_insert_id() : false;		
	}
	
	public function updateDB($tname,$fields,$id,$altFieldID=false) {
		$tname=$this->cleanQuery($tname);
		$id=$this->cleanQuery($id);		
		$sql="UPDATE `$tname` SET ";
		$c=0;
		foreach($fields as $fieldName => $fieldValue) {
		$c++;
			$fieldName=$this->cleanQuery($fieldName);
			$fieldValue=$this->cleanQuery($fieldValue);
			if($fieldName=="op" || $fieldName=="id") continue;
			$sql.=" `$fieldName`='$fieldValue' ";
			if($c==count($fields)) break;
			$sql.=",";
		}
		if(!$altFieldID) $sql.=" WHERE `id`='$id'  ";
                else $sql.=" WHERE `$altFieldID`='$id'  ";
                
		$result=mysql_query($sql);
		//echo "<div style='direction:ltr;text-align:left;'>".$sql."</div>";
		return ($result) ? true : false;
	}
	
	public function deleteDB($tname,$id,$altId=null) {
                
		$tname=$this->cleanQuery($tname);
		$id=$this->cleanQuery($id);
                $idName="id";
                if($altId!=null)
                   $idName=$altId;
		$sql="DELETE FROM `$tname` WHERE `$idName` = '$id'";
		$result=mysql_query($sql);
		return ($result) ? true : false;
	}
	
	public function deleteChiledDB($tname,$parend_id) {
		$tname=$this->cleanQuery($tname);
		$parend_id=$this->cleanQuery($parend_id);
		$sql="DELETE FROM `$tname` WHERE `parent_id` = '$parend_id'";
		$result=mysql_query($sql);
		return ($result) ? true : false;
	}
	
	public function selectDB($tname,$wherecase="") {
	/*
	$ans=$dbop->selectDB($tname);
	for($i=0;$i<$ans['n'];$i++) {
		$row=mysql_fetch_assoc($ans['p']);
	}
	*/
	
		$ans=array();
		$tname=$this->cleanQuery($tname);
		$wherecase=$this->cleanBadWords($wherecase);
		$sql="SELECT * FROM `$tname` $wherecase";
		//echo $sql;
		$result=mysql_query($sql);
		$ans['p']=mysql_query($sql);
		$ans['n']=mysql_num_rows($result);		
		return ($result) ? $ans : false;
	}
        
        public function selectDBDistinct($tname,$distinct,$wherecase="") {
	/*
	$ans=$dbop->selectDB($tname);
	for($i=0;$i<$ans['n'];$i++) {
		$row=mysql_fetch_assoc($ans['p']);
	}
	*/
	
		$ans=array();
		$tname=$this->cleanQuery($tname);
		$wherecase=$this->cleanBadWords($wherecase);
		$sql="SELECT * FROM `$tname` $wherecase";
		//echo $sql;
		$result=mysql_query($sql);
		$ans['p']=mysql_query($sql);
		$ans['n']=mysql_num_rows($result);		
		return ($result) ? $ans : false;
	}
	
	public function selectAssocRow($tname,$wherecase="") {	

		$tname=$this->cleanQuery($tname);
		$wherecase=$this->cleanBadWords($wherecase);
		$sql="SELECT * FROM `$tname` $wherecase";
                // echo "<p>". $sql ."</p>";
		$result=mysql_query($sql);
		if($result) $ans=mysql_fetch_assoc($result);		
		return ($result) ? $ans : false;
	}
}
$dbop = new dbop();