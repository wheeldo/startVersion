<?php

class UserKindsOperations {
	protected $operations;
	
	private function __construct($operations)
	{
		$this->operations = $operations;
	}
	
	/**
	 * returns a userKindOperations object which contains info on which operations a user can perform
	 * @param Auth $auth an auth token of the user
	 * @return UserKindsOperations an object with which to check specific operations
	 */
	public static function getPermissions(Auth $auth)
	{
		
		$con = db::getDefaultAdapter();
		$select = $con->select()->from('userKindOperations');
		$res = $con->query($select);
		$pages = array();
		$userKind = $auth->getUserKindID();
		
		while($row  = $res->fetch_array())
		{
			if(isset($pages[$row['userKindOperationOperationID']]))
				if($pages[$row['userKindOperationOperationID']])
					continue;
			$pages[$row['userKindOperationOperationID']] = ($row['userKindOperationUserKindID'] == $userKind) ? true : false;
		}
	
		$instance = new self($pages);
		return $instance;
	}
	/**
	 * returns whether user has permission to perform a single operation
	 * @param Auth $auth auth token of user
	 * @param int $operationID ID of operation to check
	 * @return boolean
	 */
	
	public static function getSinglePagePermissions(Auth $auth,$operationID)
	{
		$con = db::getDefaultAdapter();
		$select = $con->select()->from('userKindOperations')->where('userKindOperationOperationID = ?',$operationID);
		$res = $con->query($select);
		$authorized = false;
		$userKind = $auth->getUserKindID();
                
                //var_dump($auth);
		while($row  = $res->fetch_array())
		{
			//var_dump($row);
                        //echo $row['userKindOperationUserKindID']."====".$userKind;
			if($row['userKindOperationUserKindID'] == $userKind)
				return true;
	
		}
		return false;
	}
	
	/**
	 * checks whether a user can perform an operation
	 * @param number $operationID the ID of the operation
	 * @return boolean
	 */
	public function hasPermission($operationID)
	{
		if(isset($this->operations[$operationID]))
			return $this->operations[$operationID];
		
		return false;
	}
	
	
	public function getNumOfPerms()
	{
		return count($this->operations);
	}
}

?>