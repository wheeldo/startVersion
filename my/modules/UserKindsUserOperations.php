<?php

class userKindsUserOperations {
	protected $operations;
	
	private function __construct($operations)
	{
		$this->operations = $operations;
	}
	/**
	 * returns a userKindUserOperations object which contains info on which operations a user can perform
	 * @param Auth $auth an auth token of the user
	 * @return UserKindsOperations an object with which to check specific operations
	 */
	public static function getPermissions(Auth $auth)
	{
		$con = db::getDefaultAdapter();
		$select = $con->select()->from('userKindUserOperations');
		$res = $con->query($select);
		$pages = array();
		$userKind = $auth->getUserKindID();
		while($row  = $res->fetch_array())
		{
			if(!isset($pages[$row['userKindUserOperationUserOperationID']]))
				$pages[$row['userKindUserOperationUserOperationID']] = array();
			$pages[$row['userKindUserOperationUserOperationID']][] = (($row['userKindUserOperationUserKindID'] == $userKind) ? $row['userKindUserOperationPerformedOnUserKindID'] : -1);
		}
	
		$instance = new self($pages);
		return $instance;
	}
	
	/**
	 * Checks wether user can perform a single operation
	 * @param Auth $auth Auth object to use
	 * @param number $operationID ID of operation to check
	 * @param number $userKindID Kind of user to perform operation on
	 * @return boolean
	 */
	public static function getSinglePagePermission(Auth $auth,$operationID,$userKindID)
	{
		$con = db::getDefaultAdapter();
		$select = $con->select()->from('userKindUserOperations')->where('userKindUserOperationUserOperationID = ?',$operationID);
		$res = $con->query($select);
		if($res->num_rows > 0)
		{
                    
			$authorized = false;
			$userKind = $auth->getUserKindID();
			while($row  = $res->fetch_array())
			{
				if($row['userKindUserOperationUserKindID'] == $userKind && $row['userKindUserOperationPerformedOnUserKindID'] == $userKindID)
					return true;
			}
		}
		else{
			errorLogger::logOperationError('userKindUserOperationsgetSinglePagePermissions', 'dbRecordNotFound', 'user Operation of id'.$operationID.' not found in db');
		}
		return false;
		
	}
	
	/**
	 * Checks wether user had permission to perform operation or not
	 * @param number $operationID ID of operation to check
	 * @param number $userKindID ID of user to perform operation on
	 * @return boolean
	 */
	public function hasPermission($operationID,$userKindID)
	{
		if(isset($this->operations[$operationID]))
			return in_array($userKindID,$this->operations[$operationID]);
		return false;
	}
	
}

?>