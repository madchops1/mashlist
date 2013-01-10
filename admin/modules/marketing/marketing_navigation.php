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
<li><a class="Search" href="?SUB=PROMOS&VIEW=<?=$_GET['VIEW']?>">Promotions</a></li>
<?
    // IF SUB BIRTHDAY PROMOS
    if($_REQUEST['SUB'] == "BIRTHDAYPROMO"){
        echo "<li><a class='Search' href='?SUB=PROMOHISTORY'>Promo Email History</a></li>";
    }
?>
<li><a class="Settings <? if($_REQUEST['SUB'] == "SETTINGS"){ ?> active <? } ?>" href="?SUB=SETTINGS&VIEW=<?=$_GET['VIEW']?>">Settings</a></li>
<!--
<li><a class="Birthday Promo" href="?SUB=BIRTHDAYPROMO&VIEW=<?=$_GET['VIEW']?>">Birthday Promo</a></li>
<li><a class="Analytics" href="?SUB=ANALYTICS&VIEW=<?=$_GET['VIEW']?>"">Analytics</a></li>
<li><a class="Settings" href="?SUB=SETTINGS&VIEW=<?=$_GET['VIEW']?>"">Settings</a></li>
<li><a class="Info" href="">Help</a></li>
-->

<!--
<p>
Once you have connected WES to Google, select which account you wish to view. Use the page, keyword, and motion chart tools to track and research website activity and performance.
</p>

<p>
Data provided by Google&trade;.
</p>
-->
