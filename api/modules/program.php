<?php

require_once ('dataStructure.php');

/**
 *
 * @author Kenneth
 *        
 */
class program extends DataStructure {
	const TABLE = 'programs';
	const TABLEPRENOM = 'program';
	
	function __construct($Data,$ID = null) {
		
		if(isset($Data['programTeams'])){
			
			$Data['programTeams'] = new linkingTable('programTeams','programTeamProgramID','programTeamTeamID',$this->ID,'Team',$Data['programTeams']);
		}
		if(isset($Data['programAppCopies']))
			$Data['programAppCopies'] = new linkingTable('programAppCopies','programAppCopyProgramID','programAppCopyAppCopyID',$this->ID,'AppCopy',$Data['programAppCopies']);
		parent::__construct($Data,$ID);
	}
	
	protected  function getTable() {
		return self::TABLE;
	}
	
	protected function getVarName($name) {
		return self::TABLEPRENOM.$name;
	}
	
	/**
	 * Returns a new program with same values but different owner
	 * @param int $ownerID the ID of the new owner
	 */
	public function duplicate($ownerID,$programName)
	{
		if(!isset($this->data))
			$this->loadData();
		$data = $this->data;
		$data['programName'] = $programName;
		if(isset($ownerID))
			$data[$this->getVarName('UserID')] = $ownerID;
		$data['programTeams'] = array();
		$newCopies = array();
		$currentCopies = $this->data['programAppCopies']->getRaw();
		foreach($currentCopies as $currentCopy)
		{
			$copy = new AppCopy(null,$currentCopy['programAppCopyAppCopyID']);
			$newCopy = $copy->duplicate();
			$newCopies[] = array('programAppCopyAppCopyID' => $newCopy->getID(),
								 'programAppCopyDelay'	   => $currentCopy['programAppCopyDelay']);
		}
		$data['programAppCopies'] = $newCopies;
		$instance = new self($data);
		$instance->store();
		return $instance;
	}
	
	
	public function store()
	{
		parent::store();
		if(!isset($this->data['programTeams']))
			$this->data['programTeams'] = new linkingTable('programTeams','programTeamProgramID','programTeamTeamID',$this->ID,'Team');
		if(!isset($this->data['programAppCopies']))
			$this->data['programAppCopies'] = new linkingTable('programAppCopies','programAppCopyProgramID','programAppCopyAppCopyID',$this->ID,'AppCopy');
	}
	
	/**
	 * Registers a team for the program
	 * @param int $teamID ID of the team to register
	 * @param string $TeamStartDate string of date to start the program at format (Y-m-d H)
	 */
	public function registerTeam($teamID,$TeamStartDate,$teamInactive)
	{
		$array = array(
				'programTeamTeamID' => $teamID,
				'programTeamstartDate' => $TeamStartDate,
				'programTeamInactive'	=> $teamInactive,
				);
		$this->data['programTeams']->add($array);
	}
	
	/**
	 * Removes a team from the program
	 * @param int $teamID The ID of the team to remove
	 */
	public function removeTeam($teamID)
	{
		$this->data['programTeams']->remove(array('programTeamTeamID' => $teamID));
	}
	
	/**
	 * Sets a program team inactive
	 * @param int $teamID ID of team to set
	 * @param int $Inactive status to set 1 - Inactive, 0 - active
	 */
	public function setTeamInactive($teamID,$Inactive)
	{
		$this->data['programTeams']->editData($teamID,'programTeamInactive',$Inactive);
	}
	
	/**
	 * gets all app copies in program.
	 */
	
	public function getAppCopies()
	{
                if(!isset($this->data))
			$this->loadData();
		return $this->data['programAppCopies']->get();
	}
	
	/**
	 * gets a program team  inactive
	 * @param int $teamID ID of team to get
	 */
	
	public function getTeamInactive($teamID)
	{
		return $this->data['programTeams']->getData($teamID,'programTeamInactive');
	}
        
        
        /**
	 * gets a program team  inactive
	 * @param int $teamID ID of team to get
	 */
	
	public function getTeamProgramTeams($teamID)
	{
		return $this->data['programTeams']->getID();
	}
	
	/**
	 * Gets the start date for a certain team
	 * @param int $teamID ID of team to get start date for
	 */
	public function getTeamStartDate($teamID)
	{
		return $this->data['programTeams']->getData($teamID,'programTeamstartDate');
	}
	
	/**
	 * Sets the start date for a certain team
	 * @param int $teamID ID of team to set start date for
	 * @param string $startDate string of date to start the program at format (Y-m-d H)
	 */
	public function setTeamStartDate($teamID,$startDate)
	{
		$this->data['programTeams']->editData($teamID,'programTeamstartDate',$startDate);
	}
	
	/**
	 * Adds an app copy to the program
	 * @param int $appCopyID ID of AppCopy to add to program
	 * @param string $delay How long to  delay from start of program (d H) first app should be 0 0
	 * @param int order the place in the program the app copy is, if not set will be added at end
	 */
	public function addAppCopy($appCopyID,$delay,$order = -1)
	{
		if($order == -1)
		{
			$rawAppCopies = $this->data['programAppCopies']->getRaw();
			$high = 0;
			foreach($rawAppCopies as $appCopy)
			{
				echo 'some : '.$appCopy['programAppCopyOrder'].'<br>';
				if(intval($appCopy['programAppCopyOrder']) > $high)
				{
					
					$high = intval($appCopy['programAppCopyOrder']);
				}
			}
			$high++;
		}
		else
			$high = $order;
		$array = array(
				'programAppCopyAppCopyID' 	=> $appCopyID,
				'programAppCopyDelay' 		=> $delay,
				'programAppCopyOrder'		=> $high,
		);
		$this->data['programAppCopies']->add($array);
	}
	
	/**
	 * Removes an AppCopy from the program
	 * @param int $AppCopyID The ID of the AppCopy to remove
	 */
	public function removeAppCopy($teamID)
	{
		$this->data['programAppCopies']->remove(array('programAppCopyAppCopyID' => $teamID));
	}
	
	/**
	 * Sets the delay for a certain appCopy
	 * @param int $appCopyID ID of app copy
	 * @param string $delay How long to  delay from start of program (d H) first app should be 0 0
	 */
	public function setAppCopyDelay($appCopyID,$delay)
	{
		$this->data['programAppCopies']->editData($appCopyID,'programAppCopyDelay',$delay);
	}
	
	/**
	 * Gets the delay for a certain appCopy
	 * @param int $appCopyID ID of app copy
	 */
	public function getAppCopyDelay($appCopyID)
	{
		return $this->data['programAppCopies']->getData($appCopyID,'programAppCopyDelay');
	}
	
	/**
	 * Sets the order for a certain app copy
	 * @param int $appCopyID ID of app copy
	 * @param string $order the place in the order to set it
	 */
	public function setAppCopyOrder($appCopyID,$order)
	{
		$this->data['programAppCopies']->editData($appCopyID,'programAppCopyOrder',$order);
	}
	
	/**
	 * gets the order for a certain app copy
	 * @param int $appCopyID ID of app copy
	 */
	public function getAppCopyOrder($appCopyID)
	{
		return $this->data['programAppCopies']->getData($appCopyID,'programAppCopyOrder');
	}
	
	
	protected function loadData()
	{
		$ret = parent::loadData();
		if(!isset($this->ID))
			$ret = $ret && $this->store();
		$this->data['programTeams'] = new linkingTable('programTeams','programTeamProgramID','programTeamTeamID',$this->ID,'Team');
		$this->data['programAppCopies'] = new linkingTable('programAppCopies','programAppCopyProgramID','programAppCopyAppCopyID',$this->ID,'AppCopy');
		return $ret && $this->data['programTeams']->load() && $this->data['programAppCopies']->load();
	}

	
	/**
	 * returns an array of data of aprograms and joining any tables as specified
	 * @param $where where clause in mysql format "fieldName1 = x and fieldName2 = y"
	 * @param $ordeyby ORDER BY clause in mysql format "fieldName options"
	 * @param $join join clause mysql format array('tableName' => 'fieldNameTable1 = fieldNameTable2')
	 * @return returns a mysqli result object
	 */
	public static function programArray($where = '',$whereVals,$orderby = '',array $join = null){
	
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