<?php
/********************************************************************************************************/
/*
 * Author 		: Jitendra Khatri 
 * Package 		: API package for creating Web Services for your site with some basic API's.
 * Description  : Now a days we have to manage a central database for Mobile Apps and Websites and we have to develop Web services for this purpose,
 * 				  This Package basically contructed on this scenario, It also comprises with some basic necessary web Services. 
 * Liscense 	: GNU-GPL
 * Contact		: jkhatri6@gmail.com
 * Version		: 1.0
 * Create Date	: Nov-2014
 * Website		: http://www.jitendrakhatri.com
 * Credits		: 
 */
/********************************************************************************************************/

Class MyWebservices
{
	protected $postData	= array();
	private	$apiKey		= "4ea60ff02ae3ce36d7e7eb54882209f3";
	private $db			= "";
	private $dbName		= "webservices";
	private $dbHost		= "localhost";
	private $dbUser		= "root";
	private $dbPassword = "password";
	private $dbPrefix	= 'site_';

	// For loading required functions and libraries on creation of object of API
	function __construct()
	{
		// Filters out all post data
		$this->postData = $this->filterPostData();
		
		// Loads DB Class if not Loaded.
		if(!class_exists("MySQLDB"))
		{
			require_once("mysql_class.php");
		}
		
		$this->db =new MySQLDB($this->dbName, $this->dbHost, $this->dbUser, $this->dbPassword);
		
		if(($this->postData['entity'] == 'user')
			 && ($this->postData['action'] == "login" || $this->postData['action'] == "register"))
		{
			$this->validateAPIKey($this->postData['api_key']);
		}
		else
		{
			$this->validateAuthKey($this->postData['auth_key'], $this->postData['user_id']);
		}
	}
	
	/*
	 * Author 	: Jitendra Khatri
	 * Usage	: Remove preceding and trailing " "(Spaces) from values recieved from API Request
	 * Returns	: array()
	 */
	public function filterPostData()
	{
		$postData = array();

		if(is_array($_POST) && !empty($_POST))
		{
			foreach($_POST as $index => $value)
			{
				$postData[$index] = trim($value, " ");
			}
		}
		else
		{
			$response = array('error'   => "Invalid post data",
							  'status'  => 0,
							  'msg'		=> 'Unable to process request.'
						);
						
			$this->exitWebservice($response);
		}
		
		return $postData;
	}
	
    /*
     * Use			: For Validating API Key from Post.
     * @author		: Jitendra Khatri
     * @Parameter	: $apiKey	: API Key recieved from $_POST
     * Returns		: True : If Valid API key, Array with Status + Error Message : If invalid API key
     */
    public function validateAPIKey($apiKey)
    {
    	if($this->apiKey == $apiKey)
    	{
    		return true;
    	}
    	
    	$this->exitWebservice(array('status' => '0', 'errors' => 'Invalid API key.', 'msg' => 'Invalid Request')); 
    }
    
    
    /*
     * Use			: For Validating API Key from Post.
     * @author		: Jitendra Khatri
     * @Parameter	: $apiKey	: API Key recieved from $_POST
     * Returns		: True : If Valid API key, Array with Status + Error Message : If invalid API key
     */
    public function validateAuthKey($authKey, $userId)
    {
    	$query	= "SELECT `auth_key` FROM `".$this->dbPrefix."users` WHERE `id` = '$userId'";
		$this->db->query($query);
		$userData = $m->fetchRow(2);
		
		if(count($userData) > 0)
		{
			$userAuthKey = $userData[0]['auth_key'];
			if($userAuthKey == $authKey)
			{
    			return true;
			}
		}
    	
    	$this->exitWebservice(array('status' => '0', 'errors' => 'Invalid API key.', 'msg' => 'Invalid Request')); 
    }
    
    /*
     * Use			: For giving final output for the API call
     * @author		: Jitendra Khatri
     * @Parameter	: $reponse : Array of data to send into as Response of API Call.
     * Returns		: JSON Encoded string of $reponse
     */
	public function exitWebservice($response = array())
	{
		print_r(json_encode($response));
		die;
	}
	
	/*
	 * Author 	: Jitendra Khatri
	 * Usage	: Main entry point for API, Handles all request
	 * Returns	: Appropriate Response to Request.
	 */
	public function handleApiRequest()
	{
		$entity = $this->postData['entity'];

		switch($entity)
		{
			case 'user' :
				$action = $this->postData['action'];
				switch($action)
				{
					case 'login' :
						// Apply validation
						// match api key
						// fetch user details from DB
						// prepare proper response
						// responed with auth_key
						break;
					case 'register' :
						// validate mandatory fields
						$this->validateRegistration($this->postData);
						// Check for user exists or not
						$userRecord 		= $this->db->query("SELECT * FROM `".$this->dbPrefix."users` WHERE `email`='".$this->postData['email']."'");
						$userRecord 		= $this->db->fetchRow($type=2);
						// generate auth_key and ecrypted pass
						$authKey 			= base64_encode(md5(time()));
						$md5Password 		= md5($this->postData['password']);
						$encryptedPassword 	= base64_encode($this->postData['password']);
						
						$query = "  INSERT INTO `".$this->dbPrefix."users` (`id`, `firstName`, `lastName`, `username`, `email`, `password`, `registerDate`, `authKey`, `encryptPassword`) 
									VALUES 								   ('',
																			'".$this->postData['first_name']."',
																			'".$this->postData['last_name']."',
																			'".$this->postData['username']."',
																			'".$this->postData['email']."',
																			'".$md5Password."',
																			'".date('Y-m-d H:i:s')."',
																			'".$authKey."',
																			'".$encryptedPassword."')";
						
						$this->db->query($query);
						// prepare query
						// fire query on db
						// get details of saved user$format
						// prepare an email
						// shoot an email to admin and user registered in request.
						// prepare proper response
						// responed.  
						break;
					case 'reset-password' :
						break;
					case 'change-password' :
						break;
					case 'get-user-info' :
						break;
				}
				break;
				
			case 'conversation' :
				$action = $this->postData['action'];
				
				switch($action)
				{
					case 'create' :
						break;
						
					case 'reply' :
						break;
				}
				break;
							
			case 'dashboard' :
				break;
		}
	}

	/*
	 * 
	 */	
	public function validateRegistration($postData)
	{
		$fieldsToValidate = array('first_name', 'username', 'email', 'password', 'confirm_password');
		
		foreach($fieldsToValidate as $index => $fieldName)
		{
			if(!empty($postData[$fieldName]))
			{
				continue;
			}
			
			$response = array('error'   => "Invalid data posted for $fieldName",
							  'status'  => 0,
							  'msg'		=> "Unable to process request, $fieldName is required."
						);
						
			$this->exitWebservice($response);
		}
		
	}
}

$api = new MyWebservices();
$api->handleApiRequest(); //$response = $api->handleApiRequest();
//$api->exitWebservice($response);
?>