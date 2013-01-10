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
* Karl Steltenpohl Development LLC
* Web Business Framework
* Version 1.0
* Copyright 2010 Karl Steltenpohl Development All Rights Reserved
*
* Commercially Licensed 
* View License At: http://www.karlsdevelopment.com/web-business-framework/license
*
*************************************************************************************************************************************/

?>
<li><a class="Search <? if($_REQUEST['SUB'] == ""){ ?> active <? } ?>" href="?VIEW=<?=$_GET['VIEW']?>">Slides</a></li>
<li><a class="Add <? if($_REQUEST['SUB'] == "NEWSLIDE"){ ?> active <? } ?>" href="?VIEW=<?=$_GET['VIEW']?>&SUB=NEWSLIDE">New Slide</a></li>

