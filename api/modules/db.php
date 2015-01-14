<?php
require_once('logger.php');
require_once('errorLogger.php');
require_once('dbInterface.php');

if(AvbDevPlatform::isLocalMachine()) {
    define("USER","root");
    define("PASSWORD","");
    define("DATABASE","wheeldo_db");
}
else {
    define("USER","wheeldo_user");
    define("PASSWORD","wheeldodb2013");
    define("DATABASE","wheeldo_db");  
}


Class db {
	/**
	 * db is a singleton class. 
	 * @var db the instance of the class
	 */
	private static $instance;
	
	static  function filteq($string){
	$string = str_replace('"', "&quot;", $string);
	return str_replace("'", "&#39;", $string);
	}

	static function filteq_o($string){
		$string = str_replace("&quot;", '"',  $string);
		return str_replace("&#39;", "'",  $string);
	}
	
	
	/**
	 * creates a new db instance
	 */
	private function __construct()
	{
		dbInterface::connect();
	}
	
	/**
	 * executes a query
	 * @param query|string $query query to execute
	 * @param array $vals 
	 * @return mysqli_result|boolean result on success false otherwise
	 */
	public function query($query,$vals = null)
	{
            
		if(is_a($query, 'query'))
		{
                    
			$Str = $query->getQueryString();
			$vals = $query->getVals();
			
			$re=  dbInterface::query($Str,$vals);
			//$result = $re->get_result();
			
			if(!$re)
				return 0;
			$result = new dbRes($re);
                        
			
			return $result;
		}
		else if(is_string($query))
		{

			if(!isset($vals))
				return dbInterface::$db->query($query);
			$re=  dbInterface::query($query,$vals);
			$result = mysqli_stmt_get_result($re);
			if(is_bool($result) && !$result)
			{
				
				errorLogger::logOperationError('dbquery', 'dbOperationFailed', 'DB operation failed statement :'.$Str.' Error :'.mysqli_error($re));
			}
			
			return $result;
		}
		else
		{
			echo 'heeeeeeeeeeee';
		}
		errorLogger::logOperationError('dbquery', 'invalidArgumentSupplied', 'Expected string or query found '.gettype($query));
	}
	/**
	 * returns the ID of the last value inserted into table
	 */
	public function getInsertedID()
	{
		return dbInterface::insert_id();
	}
	
	/**
	 * returns the instance of this class
	 * @return db the instance
	 */
	static function getDefaultAdapter(){
            
		if(!isset(self::$instance))
		{
			self::$instance = new self();
		}
		return self::$instance;
		
	}
	
	/**
	 * creates and returns a new select statement linked to this connection
	 * @return select
	 */
	public function select(){
		return new select($this);
	}
	
	/**
	 * either creates a new insert query and/or executes one
	 * if $table and $insert are specified than create and execute
	 * otherwise create
	 * @param string $table table to insert to
	 * @param array $insert array of values to insert. organized as columnName => value
	 * @return Ambigous <mysqli_result, boolean>|insert returns result if executes insert query otherwise
	 */
	public function insert($table = null,$insert = null){
		if(isset($table) && isset($insert))
			return $this->query(new insert($this,$table,$insert));
		return new insert($this,$table,$insert);
	}
	
	/**
	 * creates and returns a new update statement linked to this connection
	 * @return update
	 */
	public function update(){
		return new update($this);
	}
	/**
	 * creates and returns a new delete statement linked to this connection
	 * @return delete
	 */
	public function delete(){
		return new delete($this);
	}
	
	/**
	 * returns the escaped string from the db connector
	 * @param string $string escaped string
	 */
	public function real_escape_string($string)
	{
		return dbInterface::real_escape_string($string);
	}
	
}

class dbRes
{
	/**
	 * statements which houses result
	 * @var mysqli_stmt
	 */
	private $statement;
	/**
	 * number of rows in result set
	 * @var int
	 */
	public $num_rows;
	
	/**
	 * creates a new db result object 
	 * @param mysqli_stmt $stmt statement for the result
	 */
	public function __construct(mysqli_stmt $stmt)
	{
		
		$this->statement = $stmt;
		$this->num_rows = $this->statement->num_rows;
	}
	
	/**
	 * binds params by proper name to array fields
	 * @param array $row refrence to array to bind data two
	 * @return boolean return true on success false otherwise
	 */
	private function bind_array(&$row)
	{
		$md = $this->statement->result_metadata();
		if($md)
		{
			$params = array();
			
			while($field = $md->fetch_field()) {
				$params[] = &$row[$field->name];
			}
			
			call_user_func_array(array($this->statement, 'bind_result'), $params);
			return true;
		}
		else
			return false;
	}
	
	/**
	 * fetches a single row from the array
	 * @param int $resultType currently only fetch_assoc supported, exists to maintain compatability with mysqli_result object
	 * @return array|null returns array if there is another row in dataset null otherwise
	 */
	public function fetch_array($resultType = null)
	{
		
		$res = $this->bind_array($row);
		if($res)
		{
			if($this->statement->fetch())
			{
				/*
				 * makes data into values instead of refrences
				*/
				$return = array();
				
				foreach($row as $key => $val)
				{
					$return[$key] = ($val);
				}
				return $return;
			}
		}
		else
		{
			$this->statement->errno;
		}
	}
	
	/**
	 * fetches a single row from result set in assoc mode
	 * @return array|null returns array if there is another row in dataset null otherwise
	 */
	public function fetch_assoc()
	{
		return $this->fetch_array();
	}
}
abstract class query
{
	/**
	 * 
	 * @var string name of the table to perform on
	 */
	protected $table;
	/**
	 * 
	 * @var array array of where statements to use in query. joined together by AND
	 */
	protected $where;
	/**
	 * 
	 * @var array values to insert into query when using preapered statements
	 */
	protected $whereVals;
	/**
	 * 
	 * @var array array of tables to be joined in query
	 */
	protected $join;
	/**
	 * 
	 * @var array array of specifying of where to join each table
	 */
	protected $joinWhere;
	/**
	 *
	 * @var boolean whether to use and outer or inner join
	 */
	protected $outerJoin;
	/**
	 * 
	 * @var db a refrence to the db connection
	 */
	protected $con;
	/**
	 * 
	 * @var array array containing which columns to choose
	 */
	protected $cols;
	/**
	 * 
	 * @var string how to order results
	 */
	protected $orderBy;

	/**
	 * creates a new query
	 * @param db $con connection to use
	 */
	public function __construct($con){
		$this->con = $con;
		$this->table     = null;
		$this->where     = null;
		$this->join      = null;
		$this->joinWhere = null; 
		$this->cols      = null;
		$this->orderBy   = null;
		$this->outerJoin = false;
		
	}
	
	/**
	 * function to sanitize input
	 * @param string $str string to sanitize
	 */
	protected function sanitize($str)
	{
		return $this->con->real_escape_string($str); 
	}
	/**
	 * returns the string describing the query
	 */
	public abstract function getQueryString();
	
	/**
	 * adds a where operator to the statement
	 * if called multiple times all where operators will be used 
	 * with an AND operator between them
	 * @param string $where where statement. format "columnA = ?"
	 * @param mixed|array $val values to use in preapered statement
	 * @return query
	 */
	public function where($where,$val)
	{
		if(is_string($where)){
			if(!isset($this->where))
				$this->where = array();
			$this->where[] = $where;
			
			if(!isset($this->whereVals))
				$this->whereVals = array();
			if(is_array($val))
				foreach($val as $v)
					$this->whereVals [] = $v;
			else
				$this->whereVals[] = $val;
		}
		else
			errorLogger::logOperationError('querywhere', 'invalidArgumentSupplied', 'expected string found '.gettype($where));
		return $this;
	
	}
	
	/**
	 * returns the array of ordered values to be plugged into the the preapered query
	 * @return array
	 */
	public function getVals()
	{
		return $this->whereVals;
	}
	
	/**
	 * adds a join operator to query
	 * @param string $join name of table to join
	 * @param string $joinWhere specific of where to join tables. format "columnA = columnB"
	 * @return query
	 */
	public function join($join,$joinWhere)
	{
		if(!isset($this->join))
			$this->join = array();
		if(!isset($this->joinWhere))
			$this->joinWhere = array();
		if(is_string($join) && is_string($joinWhere)){
			$this->join [] = $join;
			$this->joinWhere [] = $joinWhere;
		}
		else
			errorLogger::logOperationError('queryjoin', 'invalidArgumentSupplied', 'expected strings found '.gettype($join).' and '.gettype($joinWhere));
		return $this;
	}
	
	/**
	 * Specifies which columns to select or insert to in query
	 * @param array $cols array containing names of all columns to choose
	 * @return query
	 */
	public function cols(array $cols)
	{
		for($i = 0; $i<count($cols);$i++)
		{
		$cols[$i] = $this->sanitize($cols[$i]);
		}
		$this ->cols = $cols;
		return $this;
	}
	
	/**
	 * returns the JOIN statements in the correct format
	 * @return string
	 */
	protected function printJoin()
	{
		$queryString = '';
		if(isset($this->join))
		{
			if($this->outerJoin)
				$queryString = ' LEFT';
			$queryString .= ' JOIN (';
			$queryString .= implode(',', $this->join).')';
			$queryString .= ' ON (';
			$queryString .= implode(' AND ', $this->joinWhere).')';
	
		}
		return $queryString;
	
	}
	
	/**
	 * returns the where statements in the correcy format
	 * @return string
	 */
	
	protected function printWhere()
	{
		$queryString = '';
		if(isset($this->where))
		{
			$queryString .= ' WHERE ';
			$queryString .= implode(' AND ', $this->where);
		}
		return $queryString;
	
	}
	
	/**
	 * returns the specifying of columns in correct format
	 * @return string
	 */
	protected function printCols()
	{
		if(isset($this->cols))
		{
			return implode(',', $this->cols);
		}
		else
		{
			errorLogger::logOperationError('queryprintCols', 'variableNotSet', 'variable cols was not set');
		}
	}
	
	/**
	 * adds an order by operator to query
	 * @param string $orderBy string describing how to order by
	 * @return query
	 */
	public function orderBy($orderBy)
	{
		if(is_string($orderBy))
			$this->orderBy = $orderBy;
		else
			errorLogger::logOperationError('queryorderby', 'invalidArgumentSupplied', 'expected string found '.gettype($orderBy));
		return $this;
	}
	
	/**
	 * returns order by in the correct format
	 * @return string
	 */
	protected function printOrderBy()
	{
		$queryString = '';
		if(isset($this->orderBy))
		{
			$queryString .= ' ORDER BY '.$this->orderBy;
		}
		return $queryString;
	
	}
	
	/**
	 * specifies that join statement is outer
	 * default is inner
	 * @return query
	 */
	public function outer()
	{
		$this->outerJoin = true;
		return $this;
	}
	
	/**
	 * specifies join statement is inner
	 * default inner
	 * @return query
	 */
	public function inner()
	{
		$this->outerJoin = false;
		return $this;
	}
	
	
}

class select extends query
{

	/**
	 * specifies how to group the results of the statement 
	 * @var string
	 */
	private $groupBy;
	public function __construct($con){
		parent::__construct($con);
		$this->groupBy = null;
	}
	
	/**
	 * selects which table to select from
	 * @param string $str table name
	 * @return select
	 */
	public function from($str)
	{
		if(is_string($str))
			$this->table = $str;
		else
			errorLogger::logOperationError('selectfrom', 'invalidArgumentSupplied', 'expected string found '.gettype($str));
		return $this;
	}
	

	protected function printCols()
	{
		if(isset($this->cols))
		{
			return parent::printCols();
		}
		else
		{
			return '*';
		}
	}
	
	/**
	 * Adds a group by operator to the query
	 * @param string $groupBy
	 */
	public function groupBy($groupBy)
	{
		if(is_string($groupBy))
			$this->groupBy = $groupBy;
		else
			errorLogger::logOperationError('selectgroupBy', 'invalidArgumentSupplied', 'expected string found '.gettype($str));
		return $this;
	}
	
	/**
	 * returns group by in the correct format
	 * @return string
	 */
	protected function printGroupBy()
	{
		if(!isset($this->groupBy))
			return '';
		else 
			return ' GROUP BY '.$this->groupBy;
	}
	public function getQueryString()
	{
		$queryString ='SELECT ';
		$queryString .=$this->printCols();
		$queryString .= ' FROM '.$this->table;
		$queryString .= $this->printJoin();
		$queryString .= $this->printWhere();
		$queryString .= $this->printGroupBy();
		$queryString .= $this->printOrderBy();
		return $queryString;
	}
	
}

class insert extends query
{
	/**
	 * 
	 * @var array values to be inserted
	 */
	private $values;
	
	/**
	 * creates a new insert statement. 
	 * @param db $con
	 * @param string $table table to insert to
	 * @param array $insert array with ordered pairs to insert into table
	 */
	public function __construct($con,$table = null,$insert = null){
		parent::__construct($con);
		$this->values      = null;
		if(isset($table))
		{
			$this->table = $table;
		}
		if(isset($insert))
		{
			$this->values($insert);
		}
	}
	
	/**
	 * specifies which table to insert to
	 * @param string $table table name
	 * @return insert
	 */
	public function table($table)
	{
		$this->table = $this->sanitize($table);
		return $this;
	}

	/**
	 * specifies which values to insert into table
	 * @param array $values array with ordered pairs to insert
	 */
	public function values(array $values)
	{
		$cols = array();
		$vals = array();
		foreach($values as $key => $val)
		{
			$key = $this->sanitize($key);
			
			$cols[] = $key;
			$vals[] = $val;
		}
		
		$this->cols($cols);
		$this->values = $vals;
		
	}
	
	public function getVals()
	{
		return $this->values;
	}	

	public function getQueryString()
	{
		$queryString ='INSERT INTO '.$this->table;
		$queryString .= ' ('.$this->printCols().') ';
		$queryString .= ' VALUES (';
		for($i = 0; $i < count($this->values); $i++)
			$queryString .='?,';
		$queryString =substr($queryString, 0,strlen($queryString) - 1);
		$queryString .= ')';
		return $queryString;
	}

}

class update extends query
{
	/**
	 * 
	 * @var array containing which columns to update
	 */
	private $set;
	/**
	 * 
	 * @var array containing what values to set these values to
	 */
	private $equals;
	
	public function __construct($con){
		parent::__construct($con);
		$this->set      = null;
		$this->equals   = null;
	}

	/**
	 * Sets multiple values to use in UPDATE
	 * @param array $arr array of ordered pairs to update
	 * @return update
	 */
	public function set(array $arr)
	{
		foreach($arr as $key => $val)
		{
			$this->setVal($key,$val);
		}
		return $this;	
	}
	
	/**
	 * specifies which table to update
	 * @param string $table
	 * @return update
	 */
	public function table($table)
	{
		$this->table = $this->sanitize($table);
		return $this;
	}

	/*
	 * (non-PHPdoc)
	 * @see query::getVals()
	 */
	public function getVals()
	{
		if(!isset($this->whereVals))
			$this->whereVals = array();
		return array_merge($this->equals,$this->whereVals);
	}
	
	/**
	 * Sets a single value to update
	 * @param string $column name of column to update
	 * @param mixed $value value to set to column
	 * @return update
	 */
	public function setVal($column,$value)
	{
		if(!isset($this->set))
			$this->set = array();
		if(!isset($this->equals))
			$this->equals = array();
		if(is_string($column) && is_string($value)){
			$column    = $this->sanitize($column);
			$this->set [] = $column;
			$this->equals [] = $value;
		}
		else
		{
			$column = strval($column);
			$value = strval($value);
			$column    = $this->sanitize($column);
			$this->set [] = $column;
			$this->equals [] = $value;
				
		}
		return $this;
	}
	
	/**
	 * returns the set in the correct format
	 * @return string
	 */
	private function printSet()
	{
		$arr = array();
		for($i = 0; $i<count($this->set);$i++)
		{
		$arr[] = $this->set[$i].' = ?';
		}
		
		if(isset($this->set))
		{
			return implode(',', $arr);
		}
		else
		{
			errorLogger::logOperationError('insertprintSet', 'variableNotSet', 'variable set was not set');
		}
	}


	public function getQueryString()
	{
		$queryString ='UPDATE '.$this->table;
		$queryString .= ' SET '.$this->printSet();
		$queryString .= $this->printWhere();
		$queryString .= $this->printOrderBy();
		return $queryString;
	}

}

class delete extends query
{
	
	public function __construct($con){
		parent::__construct($con);
		$this->set      = null;
		$this->equals   = null;
	}

	/**
	 * sets which table to delete from
	 * @param string $from
	 * @return delete
	 */
	public function from($from)
	{
		$this->table = $this->sanitize($from);
		return $this;
	}


	public function getQueryString()
	{
		if(isset($this->join))
			$queryString ='DELETE '.$this->table.','.implode(',',$this->join).'  FROM '.$this->table;
		else
			$queryString ='DELETE FROM '.$this->table;
		$queryString .= $this->printJoin();
		$queryString .= $this->printWhere();
		$queryString .= $this->printOrderBy();
		return $queryString;
	}

}


?>