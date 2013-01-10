<?
/*******************************************************************
*
* Karl Steltenpohl Development 
* Web Business Framework
* Version 1.0
* Copyright 2009 Karl Steltenpohl Development All Rights Reserved
*
*******************************************************************/
?>
<li><a class="Search <? if($_REQUEST['ADDNEW'] == "" AND $_REQUEST['xid']==""){ ?> active <? } ?>" href="?VIEW=<?=$_GET['VIEW']?>">Users</a></li>
<li><a class="Add <? if($_REQUEST['ADDNEW'] != ""){ ?> active <? } ?>" href="?VIEW=<?=$_GET['VIEW']?>&ADDNEW=1">New</a></li>