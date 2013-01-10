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
* 	wes Version 1.0 Copyright 2010 Karl Steltenpohl Development LLC. All Rights Reserved.
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

?>
<li><a class="Settings <? if($_REQUEST['SUB'] == "SETTINGS" OR $_REQUEST['SUB'] == ''){ ?> active <? } ?>" href="?SUB=SETTINGS&VIEW=<?=$_GET['VIEW']?>">Settings</a></li>
<li><a class="Form <? if($_REQUEST['SUB'] == "CONTACTFORM"){ ?> active <? } ?>" href="?SUB=CONTACTFORM&VIEW=<?=$_GET['VIEW']?>">Contact Form</a></li>

	<?
	if($_REQUEST['SUB'] == "CONTACTFORM" OR $_REQUEST['SUB'] == 'ADDNEWFIELD'){
	?>
		<li><a class="Add <? if($_REQUEST['SUB'] == "ADDNEWFIELD"){ ?> active <? } ?>" href="?SUB=ADDNEWFIELD&VIEW=<?=$_GET['VIEW']?>">Add New Field</a></li>
	<?
	}
	?>
	
<li><a class="Info" href="">Help</a></li>