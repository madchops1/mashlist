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


	// Session Class
	class Session {
					
		var $auth;						
		var $sessionid;					
		var $admin;						
		
		/*************************
		* Class Constructor
		*************************/
		function Session() {
			$this->auth 		= false;
			$this->sessionid 	= session_id();
			$this->admin 		= new Admin();
		}
		
	}
	
	/**
	 *
	 * Default Classes & Functions 
	 *
	 *
	 */
	include $_SERVER['DOCUMENT_ROOT'].$_SETTINGS['website_path']."admin/includes/breadcrumbs.class.php";
	include $_SERVER['DOCUMENT_ROOT'].$_SETTINGS['website_path']."admin/includes/admin.class.php";
	include $_SERVER['DOCUMENT_ROOT'].$_SETTINGS['website_path']."admin/includes/pagination.class.php";
	
	/**
	*
	* FPDF GENERATION 
	*
	*/
	require_once($_SERVER['DOCUMENT_ROOT'].$_SETTINGS['website_path']."admin/scripts/fpdf/fpdf.php");
	//require_once($_SERVER['DOCUMENT_ROOT'].$_SETTINGS['website_path']."admin/scripts/fpdf/Table/class.fpdf_table.php");
	//require_once($_SERVER['DOCUMENT_ROOT'].$_SETTINGS['website_path']."admin/scripts/fpdf/Table/header_footer.inc");    
	//require_once($_SERVER['DOCUMENT_ROOT'].$_SETTINGS['website_path']."admin/scripts/fpdf/Table/table_def.inc");
	
	/**
	 * 
	 * WYSIWYG Classes & Functions 
	 *
	 *
	 */
	include $_SERVER['DOCUMENT_ROOT'].$_SETTINGS['website_path']."admin/scripts/wysiwygPro/wysiwygPro.class.php";
	
	/**
	 * 
	 *
	 * Rmail Classes & Functions
	 *
	 *
	 */
	include $_SERVER['DOCUMENT_ROOT'].$_SETTINGS['website_path']."admin/scripts/Rmail/Rmail.php";
	
	/**
	 * 
	 *
	 * Captcha
	 *
	 *
	 */
	//$cryptinstall = $_SETTINGS['website']."admin/scripts/captcha_2011/cryptographp.fct.php";
	//echo "$cryptinstall";
	//include $_SERVER['DOCUMENT_ROOT'].$_SETTINGS['website_path']."admin/scripts/captcha_2011/cryptographp.fct.php"; 
	//include $_SERVER['DOCUMENT_ROOT'].$_SETTINGS['website_path']."admin/scripts/captcha_2011/cryptographp.fct.php"; 
	require_once($_SERVER['DOCUMENT_ROOT'].$_SETTINGS['website_path']."admin/scripts/recaptcha-php-1.11/recaptchalib.php");
	$recaptchapublickey = "6LdqAMISAAAAACC52nJDsVTSGlrjmonZbesSQ7ha"; // you got this from the signup page
	$recaptchaprivatekey = "6LdqAMISAAAAAPu8CcsYr48rhEJeMMw2iG_wJlbX"; // you got this from the signup page
   

	
?>
