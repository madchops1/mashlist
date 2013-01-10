<?php
/*******************************************************************
*
* Karl Steltenpohl Development 
* Web Business Framework
* Version 1.0
* Copyright 2009 Karl Steltenpohl Development All Rights Reserved
*
*******************************************************************/

/*** DEMO DATA FUNCTIONS - Delete Demo Data ***/
	function deleteDemoData(){
		/*** SETTINGS ***/
		$result = doQuery("DELETE FROM settings WHERE session_id!=''") or die("err 1");
		
		/*** ADMIN USERS ***/
		$result = doQuery("DELETE FROM admin WHERE session_id!=''") or die("err 2");
			
		/*** CMS ***/
		$result = doQuery("DELETE FROM pages WHERE session_id!=''") or die("err 3");
		$result = doQuery("DELETE FROM content WHERE session_id!=''") or die("err 4");
		
		//$result = doQuery("DELETE FROM blog WHERE session_id!=''") or die("err 5");
		//$result = doQuery("DELETE FROM blog_alert WHERE session_id!=''") or die("err 6");
		//$result = doQuery("DELETE FROM blog_comment WHERE session_id!=''") or die("err 7");
		//$result = doQuery("DELETE FROM blog_category WHERE session_id!=''") or die("err 8");
		//$result = doQuery("DELETE FROM blog_category_relational WHERE session_id!=''") or die("err 9");
	}
	
	
	function createDemoDataSettings(){
	
		return true;
		
		$sql = "
			INSERT INTO `settings` VALUES('', 'CMS Demo/', 'Theme', 'The name of the directory containing the Theme', '".session_id()."', 'Textbox');
			INSERT INTO `settings` VALUES('', '28', 'Homepage', 'The id of the desired homepage (Required for CMS)', '".session_id()."', 'Textbox');
			INSERT INTO `settings` VALUES('', '?page_id=40', 'Blog URL', 'Only required if the blog is installed. Used for RSS item links, to determine where the blog is located', '".session_id()."', 'Textbox');
			INSERT INTO `settings` VALUES('', 'https://p3nlmysqladm001.secureserver.net/nl50/255/index.php?uniqueDnsEntry=ksdframework1.db.4170043.hostedresource.com', 'Database Link', 'The URL of the database admin, usually phpmyadmin.', '".session_id()."', 'Textbox');
			INSERT INTO `settings` VALUES('', 'madchops11@yahoo.com', 'Email', 'The email address for contact form and other website related email messages.', '".session_id()."', 'Textbox');
			INSERT INTO `settings` VALUES('', 'KSD Web Business Framework', 'Site Name', 'The name of your website', '".session_id()."', 'Textbox');
			INSERT INTO `settings` VALUES('', 'KSD | ', 'Title Prefix', 'This is a prefix to your page titles. It will appear on all pages.', '".session_id()."', 'Textbox');
			INSERT INTO `settings` VALUES('', ' | Web Business Framework', 'Title Suffix', 'This is a suffix to your page titles. It will appear on all pages', '".session_id()."', 'Textbox');
			INSERT INTO `settings` VALUES('', '', 'Title Default', 'Overrides all other title settings.', '".session_id()."', 'Textbox');
			INSERT INTO `settings` VALUES('', 'Admin | KSD Web Business Framework', 'Admin Title', 'This is the administration page title. It will appear on all admin pages.', '".session_id()."', 'Textbox');
			INSERT INTO `settings` VALUES('', '<style>\r\n/*** THIS IS SOME JAVASCRIPT TRCAKING CODE ***/\r\n</style>', 'Tracking Code', 'Google or Other website monitoring or tracking code.', '".session_id()."', 'Textarea');
			INSERT INTO `settings` VALUES('', 'http://www.karlsteltenpohldevelopment.com/dev/ksd framework 1/admin/images/ksd-logo.png', 'Admin Logo', 'The Url src of the Admin Logo image.', '".session_id()."', 'Textbox');
			INSERT INTO `settings` VALUES('', '0', 'Demo Mode', 'Do not Use This... It should be set to 0.\r\n\r\n', '".session_id()."', 'Textbox');
		";
		
		doQuery($sql);
	}
		
	function createDemoDataCMS(){
	
		return true;
	
	}
	
	/*** DEMO DATA FUNCTIONS - Create Demo Data ***/
	function createDemoData(){

			$this->createDemoDataSettings();
			$this->createDemoDataCMS();
			
	}
?>