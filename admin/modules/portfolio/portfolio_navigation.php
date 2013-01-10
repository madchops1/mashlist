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
<li><a class="Search" href="?VIEW=<?=$_GET['VIEW']?>">Portfolio Items</a></li>

<li><a class="Add" href="?VIEW=<?=$_GET['VIEW']?>&SUB=NEWITEM">New Item</a></li>

<li><a class="Category" href="?VIEW=<?=$_GET['VIEW']?>&SUB=CATEGORIES">Categories</a></li>

<? if($_REQUEST['SUB'] == 'CATEGORIES' || $_REQUEST['SUB'] == 'NEWCATEGORY' || $_REQUEST['cid'] != ""){ ?>
<li><a class="Add" href="?VIEW=<?=$_GET['VIEW']?>&SUB=NEWCATEGORY">New Category</a></li>
<? } ?>

<li><a class="Settings" href="?SUB=SETTINGS&VIEW=<?=$_GET['VIEW']?>">Settings</a></li>

<li><a class="Info" href="?VIEW=HELP&SUB=PORTFOLIO">Help</a></li>

<? if($_REQUEST['SUB'] == 'CATEGORIES'){ ?>
<p>
To sort, drag and drop the categories on the right.
</p>
<? } ?>

<p>
When you upload a photo WES automatically 
creates thumbnails at 94, 150, 300, 600, and 1024 pixels.
</p>

<p>
<strong>Select the 150px <u>thumbnail</u> for best results with the portfolio.</strong>
</p>
