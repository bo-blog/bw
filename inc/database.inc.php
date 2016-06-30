<?php 
/**
* 
* @link http://bw.bo-blog.com
* @copyright (c) 2014 bW Development Team
* @license MIT
*/
if (!defined ('P')) {
	die ('Access Denied.');
} 

class bwDatabase extends PDO {
	private $silent;
	private $errorMsg;
	public $qNum;
	private $dbh;
	public function __construct ()
	{
		include_once (P . 'conf/dbcon.php');
		$errorMsg = array();
		$this -> silent = false;
		$this -> qNum = 0;

		try {
			if (strtolower (DBTYPE) == 'sqlite') {
				$this -> dbh = parent :: __construct ('sqlite:' . DBNAME);
			} elseif  (strtolower (DBTYPE) == 'mysql') {
				$this -> dbh = parent :: __construct ('mysql:dbname=' . DBNAME . ';host=' . DBADDR, DBUSERNAME, DBPASSWORD, array (PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"));
				$this -> setAttribute (PDO::ATTR_EMULATE_PREPARES, false);	
			}
			else
			{
				die ('Database not supported.');
			}
		} 
		catch (PDOException $e) {
			$this -> errorMsg[] = $e -> getCode();
			$this -> errorMsg[] = $e -> getMessage();
			stopError ('Database Error: ' . implode(', ', $this -> errorMsg));
		} 
	} 

	public function getSingleRow ($query, $bindarray = null, $defaultmode = false)
	{
		$stmt = $this -> dbExec ($query, $bindarray);
		$return = $this -> getSingleRowByStmt ($stmt, $defaultmode);
		return $return;
	} 

	public function getRows ($query, $bindarray = null, $defaultmode = false)
	{
		$stmt = $this -> dbExec ($query, $bindarray);
		$return = $this -> getRowsByStmt ($stmt, $defaultmode);
		return $return;
	} 

	public function getColumns ($query, $bindarray = null)
	{
		$stmt = $this -> dbExec ($query, $bindarray);
		$return = $this -> getColumnsByStmt ($stmt);
		return $return;
	} 

	public function countRows ($query, $bindarray = null)
	{
		$stmt = $this -> dbExec ($query, $bindarray);
		$stmt -> setFetchMode (PDO :: FETCH_NUM);
		$return = $stmt -> fetchAll ();
		return count($return);
	} 

	private function getSingleRowByStmt ($statement, $defaultmode = false)
	{
		if (!$defaultmode) {
			$statement -> setFetchMode (PDO :: FETCH_ASSOC);
		} 
		$return = $statement -> fetch ();
		return $return;
	} 

	private function getRowsByStmt ($statement, $defaultmode = false)
	{
		if (!$defaultmode) {
			$statement -> setFetchMode (PDO :: FETCH_ASSOC);
		} 
		$return = $statement -> fetchAll ();
		return $return;
	} 

	private function getColumnsByStmt ($statement)
	{
		$array = $this -> getRowsByStmt ($statement);
		if (!$array) {
			return $array;
		} 
		$result = array();
		for ($i = 0; $i < count($array); $i++) {
			foreach ($array[$i] as $key => $val) {
				$result[$key][$i] = $val;
			} 
		} 
		return $result;
	} 

	public function dbExec ($queryStr, $bindarray = null)
	{
		$stmt = $this -> prepare ($queryStr);
		if ($stmt) {
			$return = $stmt -> execute ($bindarray);
			$this -> qNum++;
		} else {
			$this -> errorMsg = $this -> errorInfo();
			$this -> throwError ();
		} 
		if ($this -> errorCode() != '00000') {
			$this -> errorMsg = $this -> errorInfo();
			$this -> errorMsg[] = $stmt -> queryString;
			$this -> throwError ();
		} 
		return $stmt;
	} 

	public function dbExecBatch ($queryStr, $bindarrays)
	{
		$stmt = $this -> prepare ($queryStr);
		if ($stmt && is_array($bindarrays)) {
			foreach ($bindarrays as $bindarray) {
				$stmt -> execute ($bindarray);
				$this -> qNum++;
			} 
		} else {
			$this -> errorMsg = $this -> errorInfo();
			$this -> throwError ();
		} 
		if ($this -> errorCode() != '00000') {
			$this -> errorMsg = $this -> errorInfo();
			$this -> errorMsg[] = $stmt -> queryString;
			$this -> throwError ();
		} 
		return $stmt;
	} 

	public function dbLastInsertId ()
	{
		return $this -> lastInsertId();
	}

	private function throwError ()
	{
		if (!$this -> silent) {
			stopError ('Database Error: ' . implode(', ', $this -> errorMsg));
			exit ();
		}
	} 

	public function silentError ($status) 
	{
		$this -> silent = $status ? true : false;
	} 
} 
