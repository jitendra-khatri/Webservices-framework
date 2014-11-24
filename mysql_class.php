<?php
/*------------------------------------------------------------------------------------------------------------------
Php Version	:	php5
File Name	:	mysql_class.php
Author		:	Rishikesh Khodke
Email		:	rishikeshkhodke@gmail.com
Version		:	1.0
Create Date	:	11-Aug-2008
Description	:	Class to Connect to specified mysql database server and to perform variase databse related task. 
-------------------------------------------------------------------------------------------------------------------*/
class MySQLDB
{
	protected $mysqlHost;
	protected $mysqlUser;
	protected $mysqlPassword;
	protected $mysqlDatabase;
	protected $connectionHandle;
	protected $query;
	protected $mysqlDataArray;
	protected $resultSet;
	protected $resultRow;

	/*------------------------------------------------------------------------------------------------------------------
	Method Name	:	__construct
	Param		:	$host as Databse Host Name ,$user as Database User Name, $password as Databse Password	
					$databseName as default databse
	Return		:	Exception if any
	Description	:	MySQLDB class constructor to assign parameter to class properties and Establish Databse Connection.
	-------------------------------------------------------------------------------------------------------------------*/		
	public function __construct($databseName,$host = "localhost",$user = "root", $password = "") {
		/*----------------------------------
		Assign Parameter to class properties
		-----------------------------------*/
		$this->mysqlHost= $host;
		$this->mysqlUser= $user;
		$this->mysqlPassword= $password;
		$this->mysqlDatabase=$databseName;

		/*----------------------------------
		Connect to Database Host Server
		-----------------------------------*/
		$this->connectionHandle = @mysql_connect($this->mysqlHost, $this->mysqlUser, $this->mysqlPassword);
		
		if ( !$this->connectionHandle ) {
			throw new Exception("Error: " . mysql_error());
		}

		/*----------------------------------
		Selecting Databse
		-----------------------------------*/	
		$this->selectDb($this->mysqlDatabase);
	}

	/*------------------------------------------------------------------------------------------------------------------
	Method Name	:	__destruct
	Param		:	None	
	Return		:	Exception if any
	Description	:	Function to free the connecion object.
	----------------------------------------------------------------------------------------------------------------------*/	
	public function __destruct() {

		/*----------------------------------
		free resultSet if containing resource
		-----------------------------------*/		
		if ( is_resource($this->resultSet) ) {
			mysql_free_result($this->resultSet);
		}
		
		/*----------------------------------
		Calling disconnect function
		-----------------------------------*/		
		$this->disconnect();
	}

	/*------------------------------------------------------------------------------------------------------------------
	Method Name	:	disconnect
	Param		:	None	
	Return		:	Exception if any
	Description	:	Function to close the connecion.
	----------------------------------------------------------------------------------------------------------------------*/
	public function disconnect() {
		/*----------------------------------
		if connection is exsist 
		-----------------------------------*/			
		if ( $this->connectionHandle ) {
			/*----------------------------------
			closing connection
			-----------------------------------*/	
			if ( !@mysql_close($this->connectionHandle) ) {
				throw new Exception("Error: " . mysql_error());
			}
		}
	}

	/*------------------------------------------------------------------------------------------------------------------
	Method Name	:	selectDb
	Param		:	$databseName as Databse Name	
	Return		:	Result under resultSet or Exception if any
	Description	:	Function to change current db wih Given Db From Database List of Server.
	----------------------------------------------------------------------------------------------------------------------*/	
	public function selectDb($databseName){

		/*----------------------------------
		Assigning Databse name to class property
		-----------------------------------*/
		$this->mysqlDatabase=$databseName;
		/*----------------------------------
		Selecting Databse
		-----------------------------------*/		
		if(!@mysql_select_db($this->mysqlDatabase,$this->connectionHandle)){
			throw new Exception("Error: " . mysql_error());
		}
	}

	/*------------------------------------------------------------------------------------------------------------------
	Method Name	:	query
	Param		:	$query as Databse Query	
	Return		:	Exception if any
	Description	:	Function to execute the given query.
	----------------------------------------------------------------------------------------------------------------------*/	
	public function query($query){
		/*----------------------------------
		execute the given query.
		-----------------------------------*/	
		if(!$this->resultSet=@mysql_query($query,$this->connectionHandle)){
			throw new Exception("Error: " . mysql_error());
		}
		
	}


	/*--------------------------------------------------------------------------------------------------------------------------------
	Method Name	:	insertRow
	Param		:	$dataArray as associative array with key as field name and value as field value	,$tableName as databse table name
	Return		:	Exception if any
	Description	:	Function to execute the insert query with secure method of non SQl Injection attacks.
	----------------------------------------------------------------------------------------------------------------------------------*/	
	public function insertRow($dataArray,$tableName){

		/*-------------------------------------------------
		storing key of dataArray in local array varibale.
		--------------------------------------------------*/
		$CheckKey=array_keys($dataArray);

		/*----------------------------------------------------------------
		if dataArray contain values and the available key is string.
		-----------------------------------------------------------------*/
		if(count($dataArray)>0 && is_string($CheckKey[0])==true){
			
			/*----------------------------------------------------------------
			Storing first part of INSERt query into $this->query , where 
			implode method fetching array keys as field name
			-----------------------------------------------------------------*/
			$this->query="INSERT INTO ".$tableName." (".implode(",",array_keys($dataArray)).") VALUES(";
			
			/*----------------------------------------------------------------
			Storing values for field 
			-----------------------------------------------------------------*/
			foreach($dataArray as $value)
			{
				$this->query.=is_string($value)? "'".mysql_real_escape_string($value, $this->connectionHandle)."',": $value.",";
			}

			/*----------------------------------------------------------------
			removing extra "," from end of line 
			-----------------------------------------------------------------*/
			$this->query=substr_replace($this->query,"",-1).")";;
			
			/*----------------------------------
			execute the given query.
			-----------------------------------*/
			if(!@mysql_query($this->query,$this->connectionHandle)){
				throw new Exception("Error: ". $this->query . mysql_error());
			}
		}
		else{
				throw new Exception("Error: Please pass array with key as field name and value as field value");
		}
	}

	/*------------------------------------------------------------------------------------------------------------------
	Method Name	:	updateRow
	Param		:	$dataArray as associative array with key as field name and value as field value, 
					$tableName as databse table name, $condition as where condition	
	Return		:	Exception if any
	Description	:	Function to execute the update query with secure method of non SQl Injection attacks.

	NOTE		:	Please use SQL standard condition format to pass condition
					eg. id=3 AND firstname="'Lokesh'" etc.
	----------------------------------------------------------------------------------------------------------------------*/	
	public function updateRow($dataArray,$tableName,$condition=null){

		/*-------------------------------------------------
		storing key of dataArray in local array varibale.
		--------------------------------------------------*/
		$CheckKey=array_keys($dataArray);

		/*----------------------------------------------------------------
		if dataArray contain values and the available key is string.
		-----------------------------------------------------------------*/
		if(count($dataArray)>0 && is_string($CheckKey[0])==true){

			/*----------------------------------------------------------------
			Storing first part of UPDATE query into $this->query
			-----------------------------------------------------------------*/
			$this->query="UPDATE ".$tableName;

			/*----------------------------------------------------------------
			Storing values for field 
			-----------------------------------------------------------------*/
			$this->query.=" SET ";
			foreach($dataArray as $key=>$value)
			{
				$this->query.=$key."=";
				$this->query.=is_string($value)? "'".mysql_real_escape_string($value, $this->connectionHandle)."',": $value.",";
			}

			/*----------------------------------------------------------------
			removing extra "," from end of line 
			-----------------------------------------------------------------*/
				$this->query=substr_replace($this->query,"",-1);

			/*----------------------------------------------------------------
			if  condition exist the seting the condition
			-----------------------------------------------------------------*/
			if(!is_null($condition)){
				$this->query.=" WHERE ".$condition;
			}
			
			/*----------------------------------
			execute the given query.
			-----------------------------------*/
			if(!@mysql_query($this->query,$this->connectionHandle)){
				throw new Exception("Error: ". $this->query . mysql_error());
			}
		}
		else{
				throw new Exception("Error: Please pass array with key as field name and value as field value");
		}
	}

	/*------------------------------------------------------------------------------------------------------------------
	Method Name	:	fetchRow
	Param		:	$type as fetch type eg. 1=> BOTH , 2=>MYSQL_ASSOC , 3=>MYSQL_NUM
	Return		:	Result Row or Exception if any
	Description	:	Function  Fetch a result row as an associative array, a numeric array, or both.
	----------------------------------------------------------------------------------------------------------------------*/	
	public function fetchRow($type=2){
	
		/*----------------------------------
		local variable to set Mysql Fetch Type.
		-----------------------------------*/		
		$fetchType=null;

		/*----------------------------------
		Setting Mysql Fetch Type.
		-----------------------------------*/		
		switch($type)
		{
			case 1: $fetchType=MYSQL_BOTH;
					break;
			case 2: $fetchType=MYSQL_ASSOC;
					break;	
			case 3: $fetchType=MYSQL_NUM;
					break;
			default: $fetchType=MYSQL_ASSOC;
					break;
		}

		/*----------------------------------
		Fetching a result row.
		-----------------------------------*/	
		 if(!$this->resultRow=@mysql_fetch_array($this->resultSet,$fetchType))
		{
			if(mysql_errno()>0)
			throw new Exception("Error: Unable to Fetch Row".mysql_errno());
		}

		return $this->resultRow;
	}

	/*------------------------------------------------------------------------------------------------------------------
	Method Name	:	rowCount
	Return		:	Row count or Exception if any
	Description	:	Function  returns number of rows.
	----------------------------------------------------------------------------------------------------------------------*/	
	public function rowCount(){
	
		/*----------------------------------
		Fetching a result row.
		-----------------------------------*/	
		return @mysql_num_rows($this->resultSet);	
		if(mysql_errno()>0)
			throw new Exception("Error: Unable to Count Row".mysql_error());
	}

	/*------------------------------------------------------------------------------------------------------------------
	Method Name	:	fetchRow
	Return		:	Row count or Exception if any
	Description	:	Function  returns number of rows.
	----------------------------------------------------------------------------------------------------------------------*/	
	public function fieldValue($fieldName){
	
		/*----------------------------------
		Fetching a result row.
		-----------------------------------*/	
		return @$this->resultRow[$fieldName];	
		//if(mysql_errno()>0)
			throw new Exception("Error: Unable to Count Row".mysql_error());
	}
}//end of class
?>