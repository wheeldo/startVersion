<?php
require_once('db.php');
class errorLogger
{
	public static function logOperationError($operationName,$errorName,$desc)
	{
		$con = self::getLoggerDB();
		$result = $con->query('SELECT errorID FROM errors WHERE errorName = "'.$errorName.'"');
		$row = mysqli_fetch_array($result,MYSQL_ASSOC);
		$errorID = $row['errorID'];
		$data = array(
				'errorLogOperationName' => $operationName,
				'errorLogErrorID'       => $errorID,
				'errorLogUserID'        => -1,
				'errorLogDesc'          => $desc
		);
		foreach (array_values($data) as $value)
		{
			isset($vals) ? $vals .= ',' : $vals = '';
			$value = $con->real_escape_string($value);
			$vals .= '"'.$value.'"';
		}
		$con->query('INSERT INTO errorLog (errorLogOperationName,errorLogErrorID,errorLogUserID,errorLogDesc) VALUES ('.$vals.')');
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