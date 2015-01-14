<?php

require_once ('dataStructure.php');
require_once ('user.php');
require_once ('linkingTable.php');
/**
 *
 * @author Kenneth
 *        
 */
class team extends DataStructure {
	const TABLE = 'teams';
	const TABLEPRENOM = 'team';
	private $usersArray;
	
	function __construct($Data,$ID = null) {
		if(isset($Data['teamUsers'])){
			$Data['teamUsers'] = new linkingTable('teamsUsers','teamUserTeamID','teamUserUserID',$this->ID,'User',$Data['teamUsers']);
		}
		parent::__construct($Data,$ID);
	}
	
	protected  function getTable() {
		return self::TABLE;
	}
	
	protected function getVarName($name) {
		return self::TABLEPRENOM.$name;
	}
	
	public function store()
	{
		parent::store();
		if(!isset($this->data['teamUsers']))
			$this->data['teamUsers'] = new linkingTable('teamsUsers','teamUserTeamID','teamUserUserID',$this->ID,'User');
	}
	/**
	 * Adds a user to the team
	 * @param int $userID the ID of the user to add
	 */

	public function isUserTeam($userID)
	{	
		if(!(isset($this->data['teamUsers'])))
		{
			$this->loadData();
		}
		if(!(isset($this->usersArray)))
		{
			$tempArray = $this->data['teamUsers']->getRaw();
			$usersArray = array();
			foreach ($tempArray as &$value) {
				$usersArray[] = $value['teamUserUserID'];
			}
			$this->usersArray = $usersArray;
		}
		
		if (in_array($userID,$this->usersArray)){
			return true;
		}
		
		return false;
	}

	public function teamNumUsers()
	{	
		if(!(isset($this->data['teamUsers'])))
		{
			$this->loadData();
		}
		
		if(!(isset($this->usersArray)))
		{
			$tempArray = $this->data['teamUsers']->getRaw();
			$usersArray = array();
			foreach ($tempArray as &$value) {
				$usersArray[] = $value['teamUserUserID'];
			}
			$this->usersArray = $usersArray;
		}
			return sizeof($this->usersArray);
	}

	public function addUser($userID)
	{	
		if(!(isset($this->data['teamUsers'])))
		{
			$this->loadData();
		}
		$this->data['teamUsers']->add($userID);
	}
	
	/**
	 * Removes a user from the team
	 * @param int $userID the ID of the user to remove
	 */
	public function removeUser($userID)
	{
		if(!(isset($this->data['teamUsers'])))
		{
			$this->loadData();
		}
		$this->data['teamUsers']->remove($userID);
	}
	protected function loadData()
	{
		$ret = parent::loadData();
		if(!isset($this->ID))
			$ret = $ret && $this->store();
		$this->data['teamUsers'] = new linkingTable('teamsUsers','teamUserTeamID','teamUserUserID',$this->ID,'User');
		return $ret && $this->data['teamUsers']->load();
	}
	
	/**
	 * returns an array of data of teams and joining any tables as specified
	 * @param $where where clause in mysql format "fieldName1 = x and fieldName2 = y"
	 * @param $ordeyby ORDER BY clause in mysql format "fieldName options"
	 * @param $join join clause mysql format array('tableName' => 'fieldNameTable1 = fieldNameTable2')
	 * @return returns a mysqli result object
	 */
	public static function teamArray($where = '',$whereVals,$orderby = '',array $join = null){
	
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
        
        public static function teamUsersC($teamID) {
            global $dbop;
            $ans=$dbop->selectDB("teamsUsers","WHERE `teamUserTeamID`='{$teamID}'");
            return $ans['n'];
        }
	
}

?>
