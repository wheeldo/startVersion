<?php
require_once (ROOT .DS . 'modules' . DS . 'dataStructure.php');

/**
 *
 * @author Kenneth
 *        
 */
class AppCopy extends DataStructure {
	const TABLE = 'appCopies';
	const TABLEPRENOM = 'appCopy';
	
	/**
	 * creates duplicate of the appCopy belonging to a new owner and stores it in the database
	 * @param int $ownerID the ID of the owner of the new copy
	 */
	public function duplicate($ownerID = null)
	{
		if(!isset($this->data))
			$this->loadData();
		$data = $this->data;
		if(isset($ownerID))
			$data[$this->getVarName('UserID')] = $ownerID;
		$app = new App(null,$data['appCopyAppID']);
		$appName = $app->getData('appName');
		$sRoot = $_SERVER['SERVER_ADDR'];
		$instance = new self($data);
		$instance->store();
		$data = array('newID' => $instance->getID(),
					  'oldID' => $this->getID());
                $appAdress=$app->getData('appAddress');
                if(AvbDevPlatform::isLocalMachine()) {
                    $appAdress=  str_replace(".com",".com.loc",$appAdress);
                }
                
                if($app->getData('appDuplicate')!='') {
                    $url = 'http://'.$appAdress.$app->getData('appDuplicate');
                    $url=str_replace("[old]",$this->getID(),$url);
                    $url=str_replace("[new]",$instance->getID(),$url);
                }
                else {
                    $url = 'http://'.$appAdress."duplicate.php?".http_build_query($data,"","&");
                    //$url = 'http://'.$appAdress."duplicate.php?oldID=".$app->getData('original_copy')."&newID=".$instance->getID();
                }
                
		file_get_contents($url);

		return $instance;
	}
	
	protected  function getTable() {
		return self::TABLE;
	}
	
	protected function getVarName($name) {
		return self::TABLEPRENOM.$name;
	}

	
	/**
	 * returns an array of data of appCopies and joining any tables as specified
	 * @param $where where clause in mysql format "fieldName1 = x and fieldName2 = y"
	 * @param $ordeyby ORDER BY clause in mysql format "fieldName options"
	 * @param $join join clause mysql format array('tableName' => 'fieldNameTable1 = fieldNameTable2')
	 * @return returns a mysqli result object
	 */
	public static function appCopyArray($where = '',$whereVals,$orderby = '',array $join = null){
	
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