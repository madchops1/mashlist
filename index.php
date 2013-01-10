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
* Karl Steltenpohl Development LLC
* Web Business Framework
* Version 1.0
* Copyright 2010 Karl Steltenpohl Development All Rights Reserved
*
* Commercially Licensed 
* View License At: http://www.karlsdevelopment.com/web-business-framework/license
*
*************************************************************************************************************************************/
(include'includes/config.php') or die('<Br>WES Setup Error</b> <br><i>Error Message: No configuration file found.</i>');
$CMS = new CMS();
$CMS->buildPage();
if($_SETTINGS["show_page_time"] == '1')
{
   $mtime = microtime();
   $mtime = explode(" ",$mtime);
   $mtime = $mtime[1] + $mtime[0];
   $endtime = $mtime;
   $totaltime = ($endtime - $starttime);
   echo "This page was created in ".$totaltime." seconds";
}
?>

