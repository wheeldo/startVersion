<?php


class organization extends DataStructure
{
	function __construct($data,$ID = null) {
		$generateCode = false;
		if(isset($data) && !isset($data[self::TABLEPRENOM.'Key']))
		{
			$character_set_array = array( );
			$character_set_array[ ] = array( 'count' => 20, 'characters' => 'abcdefghijklmnopqrstuvwxyz' );
			$character_set_array[ ] = array( 'count' => 20, 'characters' => '0123456789' );
			$character_set_array[ ] = array( 'count' => 20, 'characters' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' );
			$temp_array = array( );
			foreach ( $character_set_array as $character_set )
			{
				for ( $i = 0; $i < $character_set[ 'count' ]; $i++ )
				{
					$temp_array[ ] = $character_set[ 'characters' ][ rand( 0, strlen( $character_set[ 'characters' ] ) - 1 ) ];
				}
			}
			shuffle( $temp_array );
			$data[self::TABLEPRENOM.'Key'] = implode( '', $temp_array );
			$generateCode = true;
		}
		
		parent::__construct($data,$ID);
	}
		const TABLE = 'organizations';
	const TABLEPRENOM = 'organization';
	
	protected  function getTable() {
		return self::TABLE;
	}

	protected function getVarName($name) {
		return self::TABLEPRENOM.$name;
	}
	
	
	/**
	 * returns an array of data of organizations and joining any tables as specified
	 * @param $where where clause in mysql format "fieldName1 = x and fieldName2 = y"
	 * @param $ordeyby ORDER BY clause in mysql format "fieldName options"
	 * @param $join join clause mysql format array('tableName' => 'fieldNameTable1 = fieldNameTable2')
	 * @return returns a mysqli result object
	 */
	public static function organizationArray($where = '',$whereVals,$orderby = '',array $join = null){
	
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
	
	
}