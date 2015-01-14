<?php
require_once('db.php');
class logger
{
	public static function logOperation($operationName,$desc)
	{
		$con = self::getLoggerDB();
		$operationName = $con->real_escape_string($operationName);
		$result = $con->query('SELECT permitionsOperationsID FROM permitionsOperations WHERE permitionsOperationsName = "'.$operationName.'"');
		
		$row = mysqli_fetch_array($result,MYSQL_ASSOC);
		$operationID = $row['permitionsOperationsID'];
		
		$data = array(  
				'logOperationID' => $operationID,
			    'logUserID'      => -1,
				'logDesc'        => $desc
				);
		foreach (array_values($data) as $value)
		{
			isset($vals) ? $vals .= ',' : $vals = '';
			$value = $con->real_escape_string($value);
			$vals .= '"'.$value.'"';
		}
		$con->query('INSERT INTO log (logOperationID,logUserID,logDesc) VALUES ('.$vals.')');
	}
	
	private static function getLoggerDB()
	{
		$mysqli = new mysqli("localhost", USER, PASSWORD, DATABASE);
		
		if (mysqli_connect_errno()) {
			printf("Connect failed: %s\n", mysqli_connect_error());
			exit();
		}
		$mysqli->set_charset("utf8");
		$mysqli->query("SET NAMES 'utf8'");
		
		return $mysqli;
	}
}