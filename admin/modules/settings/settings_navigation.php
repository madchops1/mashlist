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


<li><a class="Search<? if($_REQUEST['SUB'] == "SETTINGS"){ ?> active <? } ?>" href="?SUB=SETTINGS&VIEW=<?=$_GET['VIEW']?>" href="">Settings</a></li>
<li><a class="Add<? if($_REQUEST['SUB'] == "NEWSETTING"){ ?> active <? } ?>" href="?SUB=NEWSETTING&VIEW=<?=$_GET['VIEW']?>" href="">New Setting</a></li>

<? /*
<li><a class="Backup<? if($_REQUEST['SUB'] == "BACKUP"){ ?> active <? } ?>" href="?SUB=BACKUP&VIEW=<?=$_GET['VIEW']?>" href="">Website Backups</a></li>
<li><a class="Update<? if($_REQUEST['SUB'] == "UPDATE"){ ?> active <? } ?>" href="?SUB=UPDATE&VIEW=<?=$_GET['VIEW']?>" href="">Update DB Content</a></li>
*/ ?>

<li><a class="Fix<? if($_REQUEST['SUB'] == "FIX"){ ?> active <? } ?>" href="?SUB=FIX&VIEW=<?=$_GET['VIEW']?>" href="">Fix Absolute Paths</a></li>
