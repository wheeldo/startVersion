<?php

require_once ('db.php');
class Report {
	
	private $result;
	/**
	 * creates a new report
	 * @param string $reportName the name of the report as it's in the DB
	 * @param array $array required report values
	 * @throws Exception if there are values missing
	 */
	public function __construct($reportName,array $array)
	{
		$con = db::getDefaultAdapter();
		$select = $con->select()->from('reports')->where('reportName = ?', $reportName);
		$res = $con->query($select);
		$row = $res->fetch_array();
		
		$reqs = explode(',',$row['reportUserSuppliedInput']);
		
		$correctedOrder = array();
		foreach($reqs as $req)
		{
			if(!isset($array[$req]))
				throw new Exception('Missing requirements');
			$correctedOrder [] = $array[$req];
		
		}
		
		$select = $con->select()->from($row['reportTableName']);
		if(isset($row['reportCols']))
			$select->cols(explode(',',$row['reportCols']));
		if(isset($row['reportWhere']))
			$select->where($row['reportWhere'],$correctedOrder);
		if(isset($row['reportJoin']) && isset($row['reportJoinWhere']))
			$select->outer()->join($row['reportJoin'], $row['reportJoinWhere']);
		if(isset($row['reportOrderBy']))
			$select->orderBy($row['reportOrderBy']);
		if(isset($row['reportGroupBy']))
			$select->groupBy($row['reportGroupBy']);
		
		$res = $con->query($select);
		$result = array();
		while($row = $res->fetch_array())
		{
			$result [] = $row;
		}
		
		$this->result = $result;
	}
	
	/**
	 * returns the result of the report
	 * @return array
	 */
	public function getResult()
	{
		return $this->result;
	}
	
}

?>