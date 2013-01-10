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
if($_REQUEST['SUB'] == 'POINTOFSALE')			{ 	$_SESSION['menu'] = 'POINTOFSALE'; 	}
if($_REQUEST['SUB'] == 'CATEGORIES')			{ 	$_SESSION['menu'] = 'CATEGORIES'; 	}
if($_REQUEST['SUB'] == 'PRODUCTS')			{ 	$_SESSION['menu'] = 'PRODUCTS'; 	}
if($_REQUEST['SUB'] == 'SETTINGS')			{ 	$_SESSION['menu'] = 'SETTINGS';		}
if($_REQUEST['SUB'] == 'COUPONS')			{	$_SESSION['menu'] = 'COUPONS'; 		}
if($_REQUEST['SUB'] == 'SHIPPINGMETHODS')		{ 	$_SESSION['menu'] = 'SHIPPING'; 	}
if($_REQUEST['SUB'] == 'ATTRIBUTES')			{ 	$_SESSION['menu'] = 'ATTRIBUTES'; 	}
?>


<li><a class="Cart2 <? if($_REQUEST['SUB'] == ""){ ?> active <? } ?>" href="?VIEW=<?=$_GET['VIEW']?>">Orders</a></li>

<li><a class="Settings <? if($_REQUEST['SUB'] == "POINTOFSALE"){ ?> active <? } ?>" href="?VIEW=<?=$_GET['VIEW']?>&SUB=POINTOFSALE">Point of Sale</a></li>

<li><a class="Form <? if($_REQUEST['SUB'] == "PRODUCTS"){ ?> active <? } ?>" href="?SUB=PRODUCTS&VIEW=<?=$_GET['VIEW']?>">Products</a></li>

<?
if($_SESSION['menu'] == 'PRODUCTS'){
?>
<li><a class="Add <? if($_REQUEST['SUB'] == "NEWPRODUCT"){ ?> active <? } ?>" href="?SUB=NEWPRODUCT&VIEW=<?=$_GET['VIEW']?>">New Product</a></li>
<li><a class="Add <? if($_REQUEST['SUB'] == "IMPORTPRODUCTS"){ ?> active <? } ?>" href="?SUB=IMPORTPRODUCTS&VIEW=<?=$_GET['VIEW']?>">Import Products</a></li>
<? } ?>

<li><a class="Form <? if($_REQUEST['SUB'] == "REVIEWS"){ ?> active <? } ?>" href="?SUB=REVIEWS&VIEW=<?=$_GET['VIEW']?>">Products Reviews</a></li>

<li><a class="Add <? if($_REQUEST['SUB'] == "ATTRIBUTES"){ ?> active <? } ?>" href="?SUB=ATTRIBUTES&VIEW=<?=$_GET['VIEW']?>">Attributes</a></li>
<li><a class="Category <? if($_REQUEST['SUB'] == "CATEGORIES"){ ?> active <? } ?>" href="?SUB=CATEGORIES&VIEW=<?=$_GET['VIEW']?>">Categories</a></li>

<? /*
if($_SESSION['menu'] == 'CATEGORIES'){
?>
<li><a class="Add <? if($_REQUEST['SUB'] == "NEWCATEGORY"){ ?> active <? } ?>" href="?SUB=NEWCATEGORY&VIEW=<?=$_GET['VIEW']?>">New Category</a></li>
<? }
*/ ?>


<li><a class="Search <? if($_REQUEST['SUB'] == "COUPONS"){ ?> active <? } ?>" href="?SUB=COUPONS&VIEW=<?=$_GET['VIEW']?>">Coupon Codes</a></li>

<li><a class="Wallet <? if($_REQUEST['SUB'] == "PAYMENTMETHODS"){ ?> active <? } ?>" href="?SUB=PAYMENTMETHODS&VIEW=<?=$_GET['VIEW']?>">Payment Methods</a></li>

<li><a class="Box <? if($_REQUEST['SUB'] == "SHIPPINGMETHODS"){ ?> active <? } ?>" href="?SUB=SHIPPINGMETHODS&VIEW=<?=$_GET['VIEW']?>">Shipping Methods</a></li>

<li><a class="Taxes <? if($_REQUEST['SUB'] == "SALESTAX"){ ?> active <? } ?>" href="?SUB=SALESTAX&VIEW=<?=$_GET['VIEW']?>">Sales Tax</a></li>

<? /*
<li><a class="Email <? if($_REQUEST['SUB'] == "AUTOMATEDEMAILS"){ ?> active <? } ?>" href="?SUB=AUTOMATEDEMAILS&VIEW=<?=$_GET['VIEW']?>">Automated Emails</a></li>
*/ ?>

<li><a class="Search <? if($_REQUEST['SUB'] == "LOCATIONS"){ ?> active <? } ?>" href="?SUB=LOCATIONS&VIEW=<?=$_GET['VIEW']?>">Locations</a></li>

<li><a class="Settings <? if($_REQUEST['SUB'] == "SETTINGS"){ ?> active <? } ?>" href="?SUB=SETTINGS&VIEW=<?=$_GET['VIEW']?>">Settings</a></li>