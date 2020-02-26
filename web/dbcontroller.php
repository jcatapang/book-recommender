<?php
class DBController {
	//mysql://b85ae70d061c29:ec1880d3@us-cdbr-iron-east-04.cleardb.net/heroku_4c1507adeb10f27?reconnect=true
	private $host = "us-cdbr-iron-east-04.cleardb.net";
	private $user = "b85ae70d061c29";
	private $password = "ec1880d3";
	private $database = "heroku_4c1507adeb10f27";
	private $conn;
	
	function __construct() {
		$this->conn = $this->connectDB();
	}
	
	function connectDB() {
		$conn = mysqli_connect($this->host,$this->user,$this->password,$this->database,);
		return $conn;
	}
	
	function runInsert($query) {
		$result = mysqli_query($this->conn,$query);
	}

	function runDelete($query) {
		$result = mysqli_query($this->conn,$query);
	}

	function runQuery($query) {
		$result = mysqli_query($this->conn,$query);
		while($row=mysqli_fetch_assoc($result)) {
			$resultset[] = $row;
		}		
		if(!empty($resultset))
			return $resultset;
	}
	
	function numRows($query) {
		$result  = mysqli_query($this->conn,$query);
		$rowcount = mysqli_num_rows($result);
		return $rowcount;	
	}
}
?>