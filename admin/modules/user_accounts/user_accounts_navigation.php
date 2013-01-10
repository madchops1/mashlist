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

?>
<li><a class="Search <? if($_REQUEST['ADDNEW'] == "" AND $_REQUEST['PERMISSIONS'] == "" AND $_REQUEST['ADDNEWPERMISSION'] == ""){ ?> active <? } ?>" href="?VIEW=<?=$_GET['VIEW']?>">Accounts</a></li>
<li><a class="Add <? if($_REQUEST['ADDNEW'] == "1"){ ?> active <? } ?>" href="?VIEW=<?=$_GET['VIEW']?>&ADDNEW=1">New Account</a></li>

<?
// IF CMS
if(checkActiveModule('0000000')){
	?>
	<li><a class="Permissions <? if($_REQUEST['PERMISSIONS'] == "1" AND $_REQUEST['ADDNEWPERMISSION'] == ""){ ?> active <? } ?>" href="?VIEW=<?=$_GET['VIEW']?>&PERMISSIONS=1">Permissions</a></li>
	<?
	/*
	// IF PERMISSIONS
	if($_REQUEST['PERMISSIONS'] == '1' OR $_REQUEST['peid'] != ""){
		?>
		<li><a class="Add <? if($_REQUEST['ADDNEWPERMISSION'] == "2"){ ?> active <? } ?>" href="?VIEW=<?=$_GET['VIEW']?>&ADDNEWPERMISSION=1">New Permission</a></li>
		<?
	}
	*/
}
?>

<!-- <li><a class="Info" href="?VIEW=HELP&SUB=VISIITORS">Help</a></li> -->











<?
// IF CMS
if(checkActiveModule('0000000')){
	// IF PERMISSIONS
	if($_REQUEST['PERMISSIONS'] == '1' OR $_REQUEST['peid'] != ""){
		?>
		<p>
		You can create permission levels for your vistors. This lets you allow or restrict access for to specific pages or your website.
		</p>

		<p>
		In the CMS section; apply a permission level to a page in order to restrict user access to that page.
		</p>

		<p>
		In the permissions section; drag &amp; drop the permissions on the right to sort/order your user vistor permission level hierarchy.
		</p>
		<?
	}
}
?>