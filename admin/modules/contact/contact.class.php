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

class Contact {

	//var $auth;

	/**
	 *
	 * CLASS CONSTRUCTOR
	 *
	 */
	function Contact()	{
		//$this->auth = 0;
	}

	/**
	 *
	 * FORM ACTION
	 *
	 */
	function ContactFormAction()
	{
		global $_SETTINGS;
		global $_REQUEST;
		global $_POST;
		global $_COOKIE;
		global $_SESSION;		
		return processForm('1');
	}
	
	/**
	 *
	 * FORM DISPLAY
	 * Display contact form
	 *
	 */
	function ContactForm($heading=0)
	{
		global $_SETTINGS;
		global $_REQUEST;
		global $_POST;
		global $_COOKIE;
		global $_SESSION;
			
		$flag = $_SETTINGS['contact_page_clean_url'];
		if($flag == $_REQUEST['page']){
			if($heading == 1){ echo "<span class='accounttitle'>Contact Us</span>"; } 			
			buildForm('1');				
		}
	}

}
?>