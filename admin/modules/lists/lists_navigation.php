<?
/************************************************************************************************************************************* 
*
* 	This file is part of KSD's Wes software.
*   Copyright (c) 2011 Karl Steltenpohl Development LLC. All Rights Reserved.
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

<li><a class="Search <? if($_REQUEST['SUB'] == "LISTS"){ ?> active <? } ?>" href="?VIEW=<?=$_GET['VIEW']?>&SUB=SAVEDLISTS">Saved Lists</a></li>
<li><a class="Add <? if($_REQUEST['SUB'] == "NEWLIST"){ ?> active <? } ?>" href="?VIEW=<?=$_GET['VIEW']?>&SUB=NEWLIST">New</a></li>
<?

?>
