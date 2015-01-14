<?php
require_once('db.php');
require_once('logger.php');
require_once('errorLogger.php');
require_once('complexData.php');
abstract class DataStructure{
		
		protected $data;
		protected $ID;
		protected $con;
		
		/**
		 * creates a new data structure
		 * 
		 * $data and $ID are mutually exclusive. Initialize either but not both.
		 * @param array $Data data to be put into datastructure can be null
		 * @param int $ID ID of datastructure in table can be null
		 */
		function __construct($Data,$ID = null) {
			$this->data = $Data;
			$this->ID = $ID;
			$this->con = db::getDefaultAdapter();
			if(isset($this->data[$this->getVarName('ID')])){
				$this->ID = $this->data[$this->getVarName('ID')];
				unset($this->data[$this->getVarName('ID')]);
			}
			if(!isset($Data) && !isset($ID))
				errorLogger::logOperationError(get_class($this).'construct', 'invalidArgumentSupplied', 'Not enough arguments supplied to constructor');
		}
		/**
		 * return table name for the dataStructure. i.e. users for dataStructure user
		 */
		abstract protected function getTable();
		/**
		 * return a variale name in the table of the dataStructure i.e. for getVaName('Name') in users 
		 * function would return userName
		 */
		abstract protected function getVarName($name);
		
		/**
		 * returns the id of the dataStructure
		 */
		public function getID() {
			if(!isset($this->ID))
				$this->store();
			return $this->ID;
		}
	
		
		public function str(){
			return var_dump($this->data).', ID  '.$this->ID.'   ';
		}
		/**
		 * return data from dataStructure
		 * @param $dataName name of data to return
		 */
		public function getData($dataName){
			if(strcasecmp($dataName, $this->getVarName('ID')) == 0)
				return $this->getID();
			if(!isset($this->data)){
				if(!$this->loadData())
					return 0;
			}
			if(!isset($this->ID))
				$this->store();
			if(!array_key_exists($dataName, $this->data))
				return null;
			if($this->data[$dataName] instanceof complexData)
			{
				return $this->data[$dataName]->getRaw();
			}
			return $this->data[$dataName];
		}
		
		/**
		 * Checks if user is owner of the dataStructure
		 * @param User $user 
		 * @return boolean true if owner false otherwise
		 */
		public function isOwner(User $user)
		{
			$thisUser = $this->getData($this->getVarName('UserID'));
			$otherUser = $user->getID();
			if(isset($thisUser) && isset($otherUser))
				if(strcasecmp($thisUser,$otherUser) == 0)
					return 1;
			return 0;
		}
		/**
		 * Checks if a user can belongs to same organization as dataStructure
		 * @param User $user user to check
		 * @return true if same organization false otherwise
		 */
		public function isSameOrg(User $user)
		{
			if(!($thisOrg = $this->getData('organizationID')))
				$thisOrg = $this->getData($this->getVarName('OrganizationID'));
			$otherOrg = $user->getData($user->getVarName('OrganizationID'));
			if(isset($thisOrg) && isset($otherOrg))
				if(strcasecmp($thisOrg,$otherOrg) == 0)
					return 1;
			return 0;
		}
		
		
		/**
		 * updates dataStructure data in database
		 * @param $array array of all values to update
		 */
		public function update($array){
			if(!isset($this->data))
			{
				if(!$this->loadData())
					return 0;
			}
			if(!isset($this->ID))
				$this->store();
			foreach($array as $key => $val){
				$array[$key] =$val;
			}
				
			$keys = array_keys($array);
			$complex = array();
			foreach($keys as $key){
				if(array_key_exists($key,$this->data)){
					if($this->data[$key] instanceof complexData)
					{
						
						$this->data[$key]->update($array[$key]);
						$complex[] = $this->data[$key];
					}
					else
					{
						$this->data[$key] = $array[$key];
					}
				}
			}
			$update = $this->con->update()->table($this->getTable())->set(array_diff($array,$complex))->where($this->getVarName('ID').' = ?',$this->ID);
			$success = $this->con->query($update);
			if(!$success)
			{
				errorLogger::logOperationError(get_class($this).'update', 'dbOperationFailed', 'Could not update '.get_class($this).' id='.$this->ID);
				return 0;
			}
			return 1;
		}
		 
		/**
		 * deletes dataStructure from DB
		 */
		public function delete(){
			foreach($this->data as $dat)
			{
				if($dat instanceof complexData)
					$dat->delete();
			}
			$delete = $this->con->delete()->from($this->getTable())->where($this->getVarName('ID').' = ?',$this->ID);
			$success = $this->con->query($delete);
			if(!$success)
			{
				errorLogger::logOperationError(get_class($this).'delete', 'dbOperationFailed', 'Could not delete '.get_class($this).' id='.$this->ID);
				return 0;
			}
			return 1;
		}
		 
		/**
		 * Creates a new dataStructure in db
		 */
		public function store()
		{
			$this->con = db::getDefaultAdapter();
			$complex = array();
			foreach($this->data as $dat)
			{
				if($dat instanceof complexData){
					$complex[] = $dat;
				}
			}
			if(!$this->con->insert($this->getTable(),array_diff($this->data,$complex)))
				return 0;
			$this->ID = $this->con->getInsertedID();
			foreach($complex as $com)
			{
				$com->setID($this->ID);
				if(!$com->store())
					return 0;
			}
			logger::logOperation(get_class($this).'create', 'Created a new '.get_class($this));
			return 1;
		}
		/**
		 * loads data from database. Used when instantiating a dataStructure from ID/token
		 * @see DataStructure::loadData()
		 */
		protected function loadData()
		{
			$select = $this->con->select()->from($this->getTable())->where($this->getVarName('ID').' = ?',$this->ID);
			$result = $this->con->query($select);
			if($result->num_rows > 0){
				$row = $result->fetch_array();
				unset($row[$this->getVarName('ID')]);
				$this->data = $row;
				return 1;
			}
			else{
				errorLogger::logOperationError(get_class($this).'update', 'dbRecordNotFound', 'Could not find data matching id '.$this->ID.' in table '.$this->getTable());
				return 0;
			}
		}
		
		public function getArray()
		{
			return $this->data;
		}
		 
	
	
}

?>