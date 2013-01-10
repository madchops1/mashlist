<?php
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

ob_start();

// Set Error Reporting Level
ini_set('display_errors', 1); 
error_reporting(E_ERROR);
//error_reporting(E_ALL);

// Database Setings
$_SETTINGS['dbHost'] 		= '|dbHost|';								// Database Host
$_SETTINGS['dbName']		= '|dbName|';								// Database Name
$_SETTINGS['dbUser'] 		= '|dbUser|';								// Database User
$_SETTINGS['dbPass'] 		= '|dbPass|';								// Database Password

// Website Path 
$_SETTINGS['website_path']	= "|website_path|";

/*----------- IMPORTANT! - PLEASE DO NOT CHANGE ANYTHING BELOW THIS LINE, IT MAY AFFECT YOUR SUPPORT CONTRACT AND/OR CAUSE PROBLEMS WITH YOUR WEBSITE, IF YOUR HAVING PROBLEMS THEN EMAIL karl@karlsdevelopment.com -----------*/























































// SET DEBUG 1 or 0
$_SETTINGS['debug']	= "0";

/**
 *
 * Connect to the Database
 *
 */
mysql_connect($_SETTINGS['dbHost'], $_SETTINGS['dbUser'], $_SETTINGS['dbPass']);
mysql_select_db($_SETTINGS['dbName']);
//unset($_SETTINGS['dbPass']);	

/**
 *
 * Include the Main Functions
 *
 */
include_once $_SERVER['DOCUMENT_ROOT'].$_SETTINGS['website_path']."admin/includes/framework_functions.php";

/**
 *
 * Legacy Settings
 *
 */
$_SETTINGS['homePage'] 		= lookupDbValue('settings', 'value', 'Homepage', 'name');			// Homepage ID
$_SETTINGS['email'] 		= lookupDbValue('settings', 'value', 'Email', 'name');				// Email where information is sent to
$_SETTINGS['siteName']		= lookupDbValue('settings', 'value', 'Site Name', 'name');			// Site Name
$_SETTINGS['titlePrefix']	= lookupDbValue('settings', 'value', 'Title Prefix', 'name');		// Prepended to page title
$_SETTINGS['titleSuffix']	= lookupDbValue('settings', 'value', 'Title Suffix', 'name');		// Appended to page title
$_SETTINGS['titleDefault']	= lookupDbValue('settings', 'value', 'Title Default', 'name');		// Default Page Name if non specified
$_SETTINGS['adminTitle']	= lookupDbValue('settings', 'value', 'Admin Title', 'name');		// Admin Default Page Name
$_SETTINGS['databaseLink']	= lookupDbValue('settings', 'value', 'Database Link', 'name');		// Database Link
$_SETTINGS['logoImage'] 	= lookupDbValue('settings', 'value', 'Admin Logo', 'name');			// Admin Logo 
$_SETTINGS['websiteDown'] 	= lookupDbValue('settings', 'value', 'Website Down', 'name');		// Website Down

/**
 *
 * Website Path Settings
 *
 */
 
if ($_SERVER['HTTPS'] != "on") {
    //$url = "https://". $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    //header("Location: $url");
	$_SETTINGS['website'] 		= "http://".$_SERVER["SERVER_NAME"].$_SETTINGS['website_path'];
	//echo $_SETTINGS['website'];
}  else {
	$_SETTINGS['website'] 		= "https://".$_SERVER["SERVER_NAME"].$_SETTINGS['website_path'];
	//echo $_SETTINGS['website'];
}


$_SETTINGS["DOC_ROOT"] 		= $_DOC_ROOT = $_SERVER["DOCUMENT_ROOT"].$_SETTINGS['website_path'];

/**
 *
 * Main Classes
 *
 */
include_once $_SERVER['DOCUMENT_ROOT'].$_SETTINGS['website_path']."admin/includes/framework_classes.php";			// Grab Global Classes File

/**
 *
 * Site Functions & Classes
 *
 */
include_once $_SERVER['DOCUMENT_ROOT'].$_SETTINGS['website_path']."includes/site_functions.php";					// Grab Site Specific Function File
include_once $_SERVER['DOCUMENT_ROOT'].$_SETTINGS['website_path']."includes/site_classes.php";						// Grab Site Specific Classes File																

/**
 *
 * Include Functions and Classes for Installed Modules
 *
 */
$selectmodule = "SELECT * FROM wes_modules WHERE active='1' AND status='Installed'";
$resultmodule = doQuery($selectmodule);
$imodule = 0;
$nummodule = mysql_num_rows($resultmodule);
while($imodule<$nummodule){
	$rowmodule = mysql_fetch_array($resultmodule);
	if(file_exists($_SERVER['DOCUMENT_ROOT'].$_SETTINGS['website_path']."admin/modules/".$rowmodule['filename']."/".$rowmodule['filename'].".class.php")){
		include_once $_SERVER['DOCUMENT_ROOT'].$_SETTINGS['website_path']."admin/modules/".$rowmodule['filename']."/".$rowmodule['filename'].".class.php";
	}
	$imodule++;
}

/**
 *
 * Start Sessions
 *
 */
session_start();

/**
 *
 * Demo Mode
 *
 */
include_once $_SERVER['DOCUMENT_ROOT'].$_SETTINGS['website_path']."admin/includes/demo_functions.php";				// Grab Global Demo File
$_SETTINGS['demo'] = lookupDbValue('settings', 'value', 'Demo Mode', 'name');
if($_SETTINGS['demo'] == "1")
{
	if($_SESSION['democheck1'] != 1)
	{
		//createDemoData();
	}
	$_SETTINGS['demosql'] = ",session_id='".session_id()."'";
	$_SETTINGS['demosqland'] = "AND session_id='".session_id()."'";
	$_SESSION['democheck1']	= 1;
	$_SESSION['democheck2']  = 0;
} else {
	if($_SESSION['democheck2'] != 1)
	{
		deleteDemoData();
	}
	$_SETTINGS['demosql'] = "";
	$_SETTINGS['demosqland'] = "";
	$_SESSION['democheck2']  = 1;
}

/**
 *
 * Check and Set the Session Class
 *
 */
if($_SESSION['website'] != $_SETTINGS['website']){	
	unset($_SESSION['UserAccount']); 
	session_unset();
	session_destroy();
	$_SESSION["session"] = new Session();
}
if( !isset($_SESSION["session"])){
	$_SESSION["session"] = new Session();
}
$_SESSION['website'] = $_SETTINGS['website'];

/**
 *
 * Site Global and Universal Settings
 *
 */
$setselect 	= "SELECT * FROM settings";
$setresult 	= doQuery($setselect);
$setnum		= mysql_num_rows($setresult);
$seti		= 0;
while($seti<$setnum){
	$setrow = mysql_fetch_array($setresult);
	$setrowname = strtolower(str_replace(" ","_",$setrow['name']));
	
	/**
	 *
	 * User Specific Settings
	 *
	 */
	if(isset($_SESSION["session"])){
		if($setrow['user_setting'] == '1'){
			$setselect1 = "SELECT * FROM settings_user WHERE active='1' AND admin_user_id='".$_SESSION['session']->admin->userid."' AND setting_id='".$setrow['id']."' LIMIT 1";
			$setresult1 = doQuery($setselect1);
			$setrow1 = mysql_fetch_array($setresult1);
			$setrow['value'] = $setrow1['value'];
		}
	}
	
	$_SETTINGS[$setrowname] = $setrow['value'];
	$seti++;
	
}

/**
 *
 * ADMIN NAVIGATION BAR ARRAY
 * array(
 *	'Display Name',
 *	accessLevel (not used),
 *	'url Name',
 *	'file name',
 *	active,
 *	'sub nav file name',
 *	'iconpath',
 *	'external link',
 *	'description',
 *	'unique identifier'
 *	);
 */
$_ADMIN = array();
		
$selectmodule = "SELECT * FROM wes_modules WHERE active='1' AND status='Installed'";
$resultmodule = doQuery($selectmodule);
$imodule = 0;
$nummodule = mysql_num_rows($resultmodule);
while($imodule<$nummodule){
	$rowmodule = mysql_fetch_array($resultmodule);
	$newarray = array(
					''.$rowmodule['name'].'',
					1,
					''.$rowmodule['url_name'].'',
					'modules/'.$rowmodule['filename'].'/'.$rowmodule['filename'].'.php',
					1,
					'modules/'.$rowmodule['filename'].'/'.$rowmodule['filename'].'_navigation.php',
					''.$rowmodule['icon_path'].'',
					'',
					''.$rowmodule['description'].'',
					''.$rowmodule['unique_identifier'].''
				);
	array_push($_ADMIN, $newarray);
	$imodule++;
}
			
/**
 *
 * PUT THE ADMIN ARRAY IN A SESSION FOR ACCESS ANYWHERE
 *
 */
$_SESSION['AdminArray'] = $_ADMIN;	
?>