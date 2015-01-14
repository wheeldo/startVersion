<?php

require_once('db.php');
require_once('complexData.php');
/**
 *
 * @author Kenneth
 *        
 */
class linkingTable implements complexData{
	private $data;
	private $linkedTableName;
	private $linkedTableLinkedVarName;
	private $linkedTableLinkedToVarName;
	private $ID;
	private $dataClassName;
	private $con;
	
	/**
	 * creates a new linkingTable object. i.e. describes belonging relationship
	 * example: all users belonging to a team.
	 * 
	 * @param string $linkedTableName table which contains the linking info i.e. teamsUsers in example
	 * @param string $linkedTableLinkedVarName variable name which describes the owner in the table i.e. teamUserTeamID in example
	 * @param string $linkedTableLinkedToVarName variable name which describes the owned in the table i.e. teamUserUserID in example
	 * @param int $ID ID of the object which contains the data i.e. the specific team ID in example
	 * @param string $dataClassName the name of the class which is owned i.e. user in the example
	 * @param array $data data to insert
	 */
	function __construct($linkedTableName,$linkedTableLinkedVarName,$linkedTableLinkedToVarName,$ID,$dataClassName,array $data = null) {
		$this->linkedTableName = $linkedTableName;
		$this->linkedTableLinkedVarName = $linkedTableLinkedVarName;
		$this->linkedTableLinkedToVarName = $linkedTableLinkedToVarName;
		$this->ID = $ID;
		$this->dataClassName = $dataClassName;
		if(!isset($data))
			$this->data = array();
		else
			$this->data = $data;
		$this->con = db::getDefaultAdapter();;
	}
	
	/* (non-PHPdoc)
	 * @see \trunk\modules\complexData::store()
	 */
	 public function store() {
	 	
		foreach($this->data as $insert)
		{
			$arr = array($this->linkedTableLinkedVarName   => $this->ID,
					
			);
			if(is_array($insert))
				$merge = $insert;
			else
				$merge = array(	$this->linkedTableLinkedToVarName => $insert);
			$arr = array_merge($arr,$merge);
			if(!$this->con->insert($this->linkedTableName,$arr))
				return 0;
			return 1;
		}
	}

	/**
	 * @param int $ID ID of the owner
	 */
	public function setID($ID) {
		$this->ID = $ID;
	}
        
        public function getID() {
		return $this->ID;
	}

	/* (non-PHPdoc)
	 * @see \trunk\modules\complexData::get()
	 */public function get() {
		$arr = array();
		foreach($this->data as $insert)
		{
			if(is_array($insert))
				$arr [] = new $this->dataClassName(null,$insert[$this->linkedTableLinkedToVarName]);
			else
				$arr [] = new $this->dataClassName(null,$insert);
		}
		return $arr;
	 }
	 
	 /**
	  * returns all of the ids of the items linked to
	  * @return array
	  */
	 public function getRaw() {
	 	return $this->data;
	 }

	 /**
	  * compares whether two strings are equal or not
	  * @param string $a
	  * @param string $b
	  * @return number return 0 if equal otherwise another number corresponding to size of difference
	  */
	 private function compare($a,$b)
	 {
	 	
	 	if(is_array($a) && is_array($b)){
	 		$keys = array_keys($a);
	 		foreach($keys as $key)
	 		{
	 			$res = strcasecmp($a[$key],$b[$key]);
	 			if($res != 0)
	 				return $res;
	 		}
	 		
	 		return 0;
	 	}
	 	else{
	 		return strcasecmp($a,$b);
	 	}
	 }
	 
	 
	/* (non-PHPdoc)
	 * @see \trunk\modules\complexData::update()
	 */
	 public function update($data) {
		$intersection = array_uintersect($data, $this->data,array(&$this,'compare'));
		$remove = array_udiff($this->data,$intersection,array(&$this,'compare'));
		$add = array_udiff($data,$intersection,array(&$this,'compare'));
		foreach($remove as $rem)
		{
			$this->remove($rem);
		}
		foreach($add as $toAdd)
		{
			$this->add($toAdd);
		}
		
	}
	
	public function delete()
	{
		
		$delete = $this->con->delete()->from($this->linkedTableName)->where($this->linkedTableLinkedVarName.' = ?',$this->ID);
		$this->con->query($delete);
	}
	
	/**
	 * Gets the data attached to one of the objects
	 * @param int $dataID ID of the data to get
	 * @param string $dataName Name of data to get
	 */
	public function getData($dataID,$dataName)
	{
		foreach($this->data as $dat) {
			if($dat[$this->linkedTableLinkedToVarName] == $dataID){
				return $dat[$dataName];
				break;
			}
		}
	}
	
	/**
	 * Edits the data attached to one of the objects
	 * @param int $dataID ID of the data to edit
	 * @param string $dataName Name of data to edit
	 * @param mixed $dataVal value to enter to data
	 */
	public function editData($dataID,$dataName,$dataVal)
	{
		foreach($this->data as $dat) {
			if($dat[$this->linkedTableLinkedToVarName] == $dataID){
				
				$ID = $dat;
				break;	
			}
		}
		$this->remove($ID);
		$ID[$dataName] = $dataVal;
		
		$this->add($ID);
		
	}
	/**
	 * removes a specific item from the data
	 * @param int $removeID ID of item to remove
	 */
	public function remove($removeID)
	{
		if(is_array($removeID))
			$remove = $removeID[$this->linkedTableLinkedToVarName];
		else
			$remove = $removeID;
		$delete = $this->con->delete()->from($this->linkedTableName)->where($this->linkedTableLinkedToVarName.' = ?',$remove)->where($this->linkedTableLinkedVarName.' = ?',$this->ID);
		$this->con->query($delete);
		if(is_array($removeID))
		{
			foreach($this->data as $dat) {
				if($dat[$this->linkedTableLinkedToVarName] == $removeID[$this->linkedTableLinkedToVarName]){
					$removeID = $dat;
					break;	
				}
			}
		}
		elseif(is_array($this->data[0]))
		{
			foreach($this->data as $dat) {
				if($dat[$this->linkedTableLinkedToVarName] == $removeID){
					$removeID = $dat;
					break;
				}
			}
		}
		$keysToRemove = array_keys($this->data,$removeID);
		foreach($keysToRemove as $k) {
		    unset($this->data[$k]);
		}
	}
	
	/**
	 * adds an item to the data
	 * @param int $addID id of item to add
	 */
	public function add($addID)
	{
		$arr = array($this->linkedTableLinkedVarName   => $this->ID,
		);
		if(is_array($addID))
			$merge = $addID;
		else 
			$merge = array(	$this->linkedTableLinkedToVarName => $addID);
		$arr = array_merge($merge,$arr);
		$this->con->insert($this->linkedTableName,$arr);
		
		$this->data [] = $addID;
	}
	
	public function load()
	{
		$this->data = array();
		$res = $this->con->query('SHOW INDEX FROM '.$this->linkedTableName.' WHERE Key_name = "PRIMARY"');
		$row = $res->fetch_array();
		$primary = $row['Column_name'];
		$select = $this->con->select()->from($this->linkedTableName)->where($this->linkedTableLinkedVarName.' = ?',$this->ID);
		$result = $this->con->query($select);
		while($row = $result->fetch_array())
		{
			unset($row[$primary]);
			unset($row[$this->linkedTableLinkedVarName]);
			$this->data[] = $row;
		}
		return 1;
		
	}
	
	public function __toString()
	{
		return "this is a linkingTable object";
	}

}

?>