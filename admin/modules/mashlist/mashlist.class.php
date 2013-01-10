<?
 /*************************************************************************************************************************************
*
*   Copyright (c) 2011 Karl Steltenpohl Development LLC. All Rights Reserved.
*	
*	This file is part of Karl Steltenpohl Development LLC's WES (Website Enterprise Software).
*	Authored By Karl Steltenpohl
*	Commercial License
*	http://www.wescms.com/license
*
*	http://www.wescms.com
*	http://www.webksd.com/wes
* 	http://www.karlsteltenpohl.com/wes
*
*************************************************************************************************************************************/

/*** CMS Class ***/
class Mashlist {
	
	//var $theme;
	var $website_path;
	
	// CONSTRUCTOR
	function Mashlist()
	{
		global $_SETTINGS;
		//$this->theme = $this->activeTheme();
		$this->website_path = $_SETTINGS['website_path'];
	}
	
}
?>