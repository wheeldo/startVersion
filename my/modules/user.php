<?php 

require_once('dataStructure.php');
require_once('db.php');

class User extends DataStructure {
	const TABLE = 'users';
	const TABLEPRENOM = 'user';
	
	function __construct($userData,$ID = null) {
		$generateCode = false;
		if(isset($userData[self::TABLEPRENOM.'Password']))
		{
			$userData[self::TABLEPRENOM.'Password'] = hash('SHA256', $userData[self::TABLEPRENOM.'Password'],false);
		}
		parent::__construct($userData,$ID);
	}
	/**
	 * returns a new user from token value
	 * @param string $token token value
	 * @return User
	 */
	public static function userFromToken($token){
		$con = db::getDefaultAdapter();
		$select = $con->select()->from('tokens')->where('tokenVal = "'.$token.'"');
		$result = $con->query($select);
		$row = $result->fetch_array();
		return new self(null,$row['tokenUserID']);
	}
	protected  function getTable() {
		return self::TABLE;
	}

	protected function getVarName($name) {
		return self::TABLEPRENOM.$name;
	}
        
        public function getUserRow() {
            $userID=$this->ID;

            $con = db::getDefaultAdapter();
            $select = $con->select()->from('users')->where('userID = ?',array($userID));
            $result = $con->query($select);
            return $result->fetch_array();

        }
	
	/*public function isSameOrg($user) 
	{
		if(strcasecmp($user->getData($this->getVarName('UserKindID')),'3') == 0)
		{
			$con = db::getDefaultAdapter();
			$select = $con->select()->from('proffesionalsOrganizations')->cols(array('proffesionalOrganizationOrganizationID'))->where('proffesionalOrganizationUserID = ?', $user->getID());
			$res = $con->query($select);
			while($row = mysqli_fetch_array($res,MYSQL_ASSOC))
				if(strcasecmp($this->getData($this->getData($this->getVarName('OrganizationID'))),$row['proffesionalOrganizationOrganizationID']) == 0)
					return true;
			return false;
		}
		return parent::isSameOrg($user);
	}*/
	
	public function update($array){
		if(isset($array[$this->getVarName('Password')]))
			$array[$this->getVarName('Password')] = hash('SHA256', $array[$this->getVarName('Password')],false);
		parent::update($array);
	}
	/**
	 * returns an array of data of users and joining any tables as specified
	 * @param $where where clause in mysql format "fieldName1 = x and fieldName2 = y"
	 * @param $ordeyby ORDER BY clause in mysql format "fieldName options" 
	 * @param $join join clause mysql format array('tableName' => 'fieldNameTable1 = fieldNameTable2')
	 * @return returns a mysqli result object
	 */
	public static function userArray($where = '',$whereVals,$orderby = '',array $join = null){			 
		$con = db::getDefaultAdapter();
		$select = $con->select()->from(self::TABLE);
		if($where != '')
			$select->where($where,$whereVals);
		if($orderby != '')
			$select->orderBy($orderby);
		if(isset($join))
			foreach($join as $join => $where)
				$select->join($join, $where);
		$result = $con->query($select);
		
		$arr = array();
		while($row = $result->fetch_array())
		{
			$arr[] = $row;
		}
		return $arr;
	}
	
	
	public function delete(){
		$arr = array($this->getVarName('Inactive') => 1);
		$con = db::getDefaultAdapter();
		$update = $con->update()->table($this->getTable())->set($arr)->where($this->getVarName('ID').' = ?', $this->getID());
		$con->query($update);
	}

}



?>