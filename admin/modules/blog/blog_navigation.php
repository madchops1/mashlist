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
<li><a class="Search" href="<?=$_SETTINGS['blog_url']?>" target="_blank">Go To Blog</a></li>
<li><a class="Search <? if($_REQUEST['SUB'] == "" || $_REQUEST['SUB'] == "POSTS"){ ?> active <? } ?>" href="?VIEW=<?=$_GET['VIEW']?>&SUB=POSTS">View Posts</a></li>
<li><a class="Add <? if($_REQUEST['SUB'] == "NEWPOST"){ ?> active <? } ?>" href="?VIEW=<?=$_GET['VIEW']?>&SUB=ADDNEWPOST">New Post</a></li>
<li><a class="Category <? if($_REQUEST['SUB'] == "CATEGORIES"){ ?> active <? } ?>" href="?VIEW=<?=$_GET['VIEW']?>&SUB=CATEGORIES">View Categories</a></li>
<li><a class="Add <? if($_REQUEST['SUB'] == "NEWCATEGORY"){ ?> active <? } ?>" href="?VIEW=<?=$_GET['VIEW']?>&SUB=ADDNEWCATEGORY">New Category</a></li>
<li><a class="Search <? if($_REQUEST['SUB'] == "COMMENTS"){ ?> active <? } ?>" href="?VIEW=<?=$_GET['VIEW']?>&SUB=COMMENTS">View Comments</a></li>
<li><a class="Search <? if($_REQUEST['SUB'] == "SUBSCRIBERS"){ ?> active <? } ?>" href="?VIEW=<?=$_GET['VIEW']?>&SUB=SUBSCRIBERS">View Subscribers/Alerts</a></li>