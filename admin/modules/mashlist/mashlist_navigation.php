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

?>
<li><a class="Search Mashlists<? if($_REQUEST['SUB'] == "" AND $_REQUEST['bid'] == "" AND $_REQUEST['mid'] == ""){ ?> active <? } ?>" href="?VIEW=<?=$_GET['VIEW']?>">Mashlists</a></li>
<br>
<li><a class="Search Background<? if($_REQUEST['SUB'] == 'BACKGROUNDS'){ ?> active <? } ?>" href="?VIEW=<?=$_GET['VIEW']?>&SUB=BACKGROUNDS">Backgrounds</a></li>
<li><a class="Add Background<? if($_REQUEST['SUB'] == 'ADDBACKGROUND'){ ?> active <? } ?>" href="?VIEW=<?=$_GET['VIEW']?>&SUB=ADDBACKGROUND">Add Background</a></li>