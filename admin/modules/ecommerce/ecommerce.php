<?
/*************************************************************************************************************************************
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


/***	AJAX SORT IMAGES			********************************************************/
if (isset($_POST['SORT_IMAGES'])){
	// FOR AJAX
	@require_once '../../../includes/config.php';
	
	//
	// GET SORT ARRAY
	//
	$sortarray = $_POST['sortarray'];
	$sortarray = explode(",",$sortarray);
	
	// GET BOTTOM MOST SORT LEVEL
	$select = "SELECT sort_level FROM ecommerce_product_images WHERE product_id='".$_POST['pid']."' ORDER BY sort_level DESC";
	//echo $select."<br>";
	$result = doQuery($select);
	// THE NUMBER OF TOP LEVEL CATEGORIES
	$num = mysql_num_rows($result);
	$i = 1;
	foreach($sortarray AS $image){
		$select = "UPDATE ecommerce_product_images SET sort_level='".$i."' WHERE image_id='".$image."'";
		echo $select."<br>";
		$result = doQuery($select);
		$i++;
	}
	
	echo "true";
	exit;
}

/***	AJAX SORT CATEGORIES		********************************************************/
if (isset($_POST['SORT_CATEGORIES'])){
	// FOR AJAX
	@require_once '../../../includes/config.php';
	
	//
	// GET SORT ARRAY
	//
	$sortarray = $_POST['sortarray'];
	$sortarray = explode(",",$sortarray);
	
	// UPDATE CATEGORY 1 SORT ORDER
	if($_POST['LEVEL'] == 'cat1'){
		/*
		// GET BOTTOM MOST SORT LEVEL OF CATEGORY LEVEL 1
		$select = "SELECT sort_level FROM ecommerce_product_categories WHERE active='1' AND parent_id='' ORDER BY sort_level DESC";
		echo $select."<br>";
		$result = doQuery($select);
		// THE NUMBER OF TOP LEVEL CATEGORIES
		$num = mysql_num_rows($result);
		*/
		$i = 1;
		foreach($sortarray AS $category){
			$select = "UPDATE ecommerce_product_categories SET sort_level='".$i."' WHERE category_id='".$category."'";
			echo $select."<br>";
			$result = doQuery($select);
			$i++;
		}
	}
	
	// UPDATE CATEGORY 2 SORT ORDER
	if($_POST['LEVEL'] == 'cat2'){
		/*
		// GET THE PARENT CATEGORIES
		$select = "SELECT * FROM ecommerce_product_categories WHERE active='1' AND parent_id=''";
		$result = doQuery($select);
		$q = "";
		$num = mysql_num_rows($result);
		$i = 0;
		while($i<$num){
			$row = mysql_fetch_array($result);
			$q .= " parent_id='".$row['category_id']."' OR";
			$i++;
		}
		$q = trim($q,"OR");
	
		// GET BOTTOM MOST SORT LEVEL OF CATEGORY LEVEL 2
		$select = "SELECT sort_level FROM ecommerce_product_categories WHERE active='1' AND (".$q.")";
		echo $select."<br>";
		$result = doQuery($select);
		// THE NUMBER OF 2 LEVEL CATEGORIES
		$num = mysql_num_rows($result);
		*/
		$i = 1;
		foreach($sortarray AS $category){
			$select = "UPDATE ecommerce_product_categories SET sort_level='".$i."' WHERE category_id='".$category."'";
			echo $select."<br>";
			$result = doQuery($select);
			$i++;
		}
	}
	
	// UPDATE CATEGORY 3 SORT ORDER
	if($_POST['LEVEL'] == 'cat3'){
		/*
		// GET THE PARENT CATEGORIES
		$select = "SELECT * FROM ecommerce_product_categories WHERE active='1' AND parent_id=''";
		$result = doQuery($select);
		$q = "";
		$num = mysql_num_rows($result);
		$i = 0;
		while($i<$num){
			$row = mysql_fetch_array($result);
			$q .= " parent_id='".$row['category_id']."' OR";
			$i++;
		}
		$q = trim($q,"OR");
		
		// GET THE 2 LEVEL CATEGORIES
		$select = "SELECT * FROM ecommerce_product_categories WHERE active='1' AND (".$q.")";
		$result = doQuery($select);
		$q = "";
		$num = mysql_num_rows($result);
		$i = 0;
		while($i<$num){
			$row = mysql_fetch_array($result);
			$q .= " parent_id='".$row['category_id']."' OR";
			$i++;
		}
		$q = trim($q,"OR");
		
		// GET BOTTOM MOST SORT LEVEL OF CATEGORY LEVEL 3
		$select = "SELECT sort_level FROM ecommerce_product_categories WHERE active='1' AND (".$q.")";
		echo $select."<br>";
		$result = doQuery($select);
		// THE NUMBER OF 3 LEVEL CATEGORIES
		$num = mysql_num_rows($result);
		*/
		$i = 1;
		foreach($sortarray AS $category){
			$select = "UPDATE ecommerce_product_categories SET sort_level='".$i."' WHERE category_id='".$category."'";
			echo $select."<br>";
			$result = doQuery($select);
			$i++;
		}
	}
	
	echo "true";
	exit;
}

/***	AJAX SORT PRODUCTS			********************************************************/
if (isset($_POST['SORT_PRODUCTS'])){
	// FOR AJAX
	@require_once '../../../includes/config.php';
	
	//
	// GET SORT ARRAY
	//
	$sortarray = $_POST['sortarray'];
	$sortarray = explode(",",$sortarray);
	
		/*
		// GET BOTTOM MOST SORT LEVEL
		$select = 	"SELECT sort_level FROM ecommerce_products ".
					"LEFT JOIN ecommerce_product_category_relational ON ecommerce_products.product_id=ecommerce_product_category_relational.product_id ".
					"LEFT JOIN ecommerce_product_categories ON ecommerce_product_category_relational.category_id=ecommerce_product_categories.category_id ".
					"WHERE ecommerce_products.active='1' AND ecommerce_product_categories.category_id='".$_POST['CATEGORY']."' ORDER BY ecommerce_products.sort_level DESC";
		
		// GET BOTTOM MOST SORT LEVEL FROM RELATIONAL
		$select = 	"SELECT sort_level FROM ";
					
		echo $select."<br>";
		$result = doQuery($select);
		// THE NUMBER OF PRODUCTS
		$num = mysql_num_rows($result);
		*/
		
		if($_POST['RELATIONAL_ID'] != ""){
			$update = "UPDATE ecommerce_product_category_relational SET category_id='".$_POST['NEW_CATEGORY']."' WHERE item_id=".$_POST['RELATIONAL_ID']."";
			doQuery($update);
		}
		
		$i = 1;
		foreach($sortarray AS $product){
			$select = "UPDATE ecommerce_product_category_relational SET sort_level='".$i."' WHERE item_id='".$product."'";
			echo $select."<br>";
			$result = doQuery($select);
			$i++;
		}
	
	echo "true";
	exit;
}

/*** 	AJAX DELETE PRODUCT IMAGE		********************************************************/
if (isset($_POST["DELETE_IMAGE"])){
	// FOR AJAX
	@require_once '../../../includes/config.php';
	
	$select = "DELETE FROM ecommerce_product_images WHERE image_id='".$_POST['iid']."'";
	doQuery($select);
	echo $select."<br>";
	echo "true";
	exit;
}

//
// Declare Class and Report Function
//
$Ecommerce = new Ecommerce();
$Settings = new Settings();
report($_REQUEST['REPORT'],$_REQUEST['SUCCESS']);

/***	DELETE PRODUCT					********************************************************/
if (isset($_REQUEST["DELETE_PRODUCT"])){
	// REMOVE PRODUCT
	doQuery("UPDATE ecommerce_products SET active='0' WHERE product_id=".$_REQUEST["pid"]." ".$_SETTINGS['demosqland']."");
	
	$report = "Product Deleted Successfully";
	$success = "1";
	if($_REQUEST['SUB'] == ""){ $_REQUEST['SUB'] = "PRODUCTS"; }
	header("Location: ".$_SERVER["PHP_SELF"]."?VIEW=".$_REQUEST["VIEW"]."&SUB=".$_REQUEST['SUB']."&KEYWORDS=".$_REQUEST['KEYWORDS']."&COLUMN=".$_REQUEST['COLUMN']."&REPORT=".$report."&SUCCESS=".$success."&page=".$_REQUEST['page']."");
	exit();
}

/***	DELETE MULTIPLE PRODUCTS		********************************************************/
if (isset($_REQUEST["DELETE_PRODUCTS"])){
	
	$productstring = rtrim($_REQUEST['items'], ','); 
	$productArray = explode(",",$productstring);
	
	foreach($productArray AS $product_id){
		//echo "".$cartitem."<br>";
		$sel = 	"UPDATE ecommerce_products SET active='0' WHERE ".
				"product_id='".$product_id."'";
		doQuery($sel);
	}
	
	$report = "Products Deleted Successfully";
	$success = "1";
	header("Location: ".$_SERVER["PHP_SELF"]."?VIEW=".$_REQUEST["VIEW"]."&SUB=".$_REQUEST['SUB']."&KEYWORDS=".$_REQUEST['KEYWORDS']."&COLUMN=".$_REQUEST['COLUMN']."&REPORT=".$report."&SUCCESS=".$success."&page=".$_REQUEST['page']."");
	exit();
}

/***	DELETE CATEGORY					********************************************************/
if (isset($_REQUEST["DELETE_CATEGORY"])){

	doQuery("UPDATE ecommerce_product_categories SET active='0' WHERE category_id=".$_REQUEST["cid"]." ".$_SETTINGS['demosqland']."");
	
	$report = "Category Deleted Successfully";
	$success = "1";
	if($_REQUEST['SUB'] == ""){ $_REQUEST['SUB'] = "CATEGORIES"; }
	header("Location: ".$_SERVER["PHP_SELF"]."?VIEW=".$_REQUEST["VIEW"]."&SUB=".$_REQUEST['SUB']."&REPORT=".$report."&SUCCESS=".$success."");
	exit();
}

/***	ADD PRODUCT						********************************************************/
if (isset($_POST["ADD_PRODUCT"])){
	$error = 0;
	
	if($_POST['name'] == ""){ 			$error = 1; ReportError("Enter a Product Name"); 	}
	if($_POST['product_type'] == ""){ 	$error = 1; ReportError("Enter a Product Type"); 	}
	if($_POST['categories'] == ""){ 	$error = 1; ReportError("Select a Category"); 		}
	
	if($error == 0)	{
		$_POST2 = $_POST; // post 2 is for the multiselect table
		$_POST = escape_smart_array($_POST);			
		
		$date = DateTimeIntoTimestamp($_POST['date']);
		
		// INSERT RECORD 
		$next = nextId('ecommerce_products');
		$select =	"INSERT INTO ecommerce_products SET ".
					"name='".$_POST['name']."',".
					"image='".$_POST['image']."',".
					"description='".$_POST['description']."',".
					"price='".$_POST['price']."',".
					"flat_discount='".$_POST['flat_discount']."',".
					"rate_discount='".$_POST['rate_discount']."',".
					"weight='".$_POST['weight']."',".
					"status='".$_POST['status']."',".
					"inventory='".$_POST['inventory']."',".
					"take_inventory='".$_POST['take_inventory']."',".
					"take_off_inventory_empty='".$_POST['take_off_inventory_empty']."',".
					"date='".$date."',".
					"naming_convention='".$_POST['naming_convention']."',".					
					"product_type='".$_POST['product_type']."',".
					"product_number='".$_POST['product_number']."',".
					"active=1,".
					"hidden_promo='".$_POST['hidden_promo']."',".
					"created=NULL".
					"".$_SETTINGS['demosql']."";			
		doQuery($select);
		
		// IF NEW CATEGORY
		if($_POST['newcategory'] != ""){ 
			$catid = nextId('ecommerce_product_categories');
			$select = 	"INSERT INTO ecommerce_product_categories SET ".
						"parent_id='".$_POST['newcategoryparent']."',".
						"name='".$_POST['newcategory']."',".
						"active='1',".
						"created=NULL";
			doQuery($select);		
			// MAKE THE NEW CATEGORY CONNECTION
			$select = 	"INSERT INTO ecommerce_product_category_relation SET ".
						"product_id='".$next."',".
						"category_id='".$catid."'";
		} else {
			// ELSE CONNECT CATEGORIES
			$test=$_POST2['categories'];
			if($test){
			 foreach ($test as $t){		 
				$select = 	"INSERT INTO ecommerce_product_category_relational SET ".
							"product_id='".$next."',".
							"category_id='".$t."' ";				
				doQuery($select);	
				//echo $select."<br>"; 
				}
			}
		}
		
		// CONNECT ATTRIBUTES
		//doQuery("DELETE FROM ecommerce_product_attribute_relational WHERE product_id='".$_POST['pid']."'");
		$attributes=$_POST2['attributes'];
		if($attributes){
			foreach($attributes as $a){
				$select = 	"INSERT INTO ecommerce_product_attribute_relational SET ".
							"product_id='".$_POST['pid']."',".
							"attribute_id='".$a."' ";
				doQuery($select);							
			}
		}
		
		// INSERT IMAGE
		$select =	"INSERT INTO ecommerce_product_images SET ".
					"product_id='".$next."',".
					"name='".$_POST['imagename']."',".
					"description='".$_POST['imagedescription']."',".
					"image='".basename($_POST['image'])."',".
					"sort_level='1',".
					"created=NULL,".
					"active='1'".
					"".$_SETTINGS['demosql']."";	
		doQuery($select);
		
		// GET IMAGE COLORS 
		$select = 	"";
		$result =   "";
		
		$report = "Product Created Successfully";
		header("Location: ?REPORT=".$report."&SUCCESS=1&pid=".$next."&VIEW=".$_REQUEST['VIEW']."");
		exit();	
	}
	
}

/***	UPDATE PRODUCT 					********************************************************/
if (isset($_POST["UPDATE_PRODUCT"])){
	$error = 0;
	
	if($_POST['name'] == ""){ $error = 1; ReportError("Enter a Product Name"); }
	if($_POST['product_type'] == ""){ $error = 1; ReportError("Enter a Product Type"); }
	if($_POST['categories'] == ""){ $error = 1; ReportError("Select a Category"); }
	//if($_POST['newcategory'] != ""){
	//	if($_POST['newcategoryparent'] == ""){ $error=1; ReportError("Select a Parent Category"); }
	//}
	
	if($error == 0){
		
		// CONNECT CATEGORIES BEFORE ESCAPE		
		doQuery("DELETE FROM ecommerce_product_category_relational WHERE product_id='".$_POST['pid']."'");
		$test=$_POST['categories'];
		if($test){
		 foreach($test as $t){		 
			$select = 	"INSERT INTO ecommerce_product_category_relational SET ".
						"product_id='".$_POST['pid']."',".
						"category_id='".$t."' ";				
			doQuery($select);	
			//echo $select."<br>"; 
			}
		}
		
		// CONNECT ATTRIBUTES BEFORE ESCAPE
		doQuery("DELETE FROM ecommerce_product_attribute_relational WHERE product_id='".$_POST['pid']."'");
		$attributes=$_POST['attributes'];
		if($attributes){
			foreach($attributes as $a){
				$select = 	"INSERT INTO ecommerce_product_attribute_relational SET ".
							"product_id='".$_POST['pid']."',".
							"attribute_id='".$a."' ";
				doQuery($select);							
			}
		}
					
		// ESCAPE POST
		$_POST = escape_smart_array($_POST);
		
		$date = DateTimeIntoTimestamp($_POST['date']);
		
		// UPDATE 
		$select =	"UPDATE ecommerce_products SET ".
					"name='".$_POST['name']."',".
					"image='".$_POST['image']."',".
					"description='".$_POST['description']."',".
					"price='".$_POST['price']."',".
					"flat_discount='".$_POST['flat_discount']."',".
					"rate_discount='".$_POST['rate_discount']."',".
					"weight='".$_POST['weight']."',".
					"product_number='".$_POST['product_number']."',".
					"status='".$_POST['status']."',".
					"inventory='".$_POST['inventory']."',".
					"take_inventory='".$_POST['take_inventory']."',".
					"take_off_inventory_empty='".$_POST['take_off_inventory_empty']."',".
					"date='".$date."',".
					"naming_convention='".$_POST['naming_convention']."',".
					"user_permission='".$_POST['user_permission']."',".
					"hidden_promo='".$_POST['hidden_promo']."',".
					"product_type='".$_POST['product_type']."'".
					"".$_SETTINGS['demosql']."".					
					" WHERE product_id='".$_POST["pid"]."'";
		doQuery($select);
		
		// UPDATE IMAGES		
		$select =	"SELECT * FROM ecommerce_product_images WHERE product_id='".$_POST['pid']."' AND active='1' ORDER BY sort_level ASC";
		$result = 	doQuery($select);
		$num 	= 	mysql_num_rows($result);
		$i 		=	0;
		while($i<$num){
			$row = mysql_fetch_array($result);
			
			$sel = 	"UPDATE ecommerce_product_images SET ".
					"image='".basename($_POST['image'.$i.''])."',".
					"name='".$_POST['imagename'.$i.'']."',".
					"description='".$_POST['imagedescription'.$i.'']."'".
					" WHERE image_id='".$row['image_id']."'";
					
			doQuery($sel);
			
			$i++;
		}
		
		// NEW IMAGE
		if($_POST['newimage'] != ""){
		
			$select = 	"INSERT INTO ecommerce_product_images SET ".
						"product_id='".$_POST['pid']."',".
						"image='".basename($_POST['newimage'])."',".
						"name='".$_POST['newname']."',".
						"description='".$_POST['newdescription']."',".
						"created=NULL,".
						"active='1'";
			doQuery($select);
			$report1 = " and Image Added";
		}
		
		// NEW CATEGORY
		if($_POST['newcategory'] != ""){ 
		
			$select = 	"INSERT INTO ecommerce_product_categories SET ".
						"parent_id='".$_POST['newcategoryparent']."',".
						"name='".$_POST['newcategory']."',".
						"active='1',".
						"created=NULL";
			doQuery($select);
			$report1 = " and Category Added";			
		}
		
		$report = "Product Updated".$report1." Successfully";
		$success = "1";
		
		/* TESTING 
		$mtime = microtime();
		$mtime = explode(" ",$mtime);
		$mtime = $mtime[1] + $mtime[0];
		$endtime = $mtime;
		$totaltime = ($endtime - $starttime);
		echo "It took this long to get to line 484 in ecommerce.php: ".$totaltime." seconds";
		die();
		exit();
		*/
		
		session_write_close();
		header("Location: {$_SERVER["PHP_SELF"]}?REPORT=".$report."&pid=".$_POST['pid']."&SUCCESS=".$success."&VIEW=".$_REQUEST['VIEW']."");
		die();
		exit();

	}
}

/***	ADD CATEGORY					********************************************************/
if (isset($_POST['ADD_CATEGORY'])){
	$error = 0;	
	if($_POST['name'] == ""){ $error = 1; ReportError("Enter a Category Name"); }
	
	if($error == 0){
		
			$_POST = escape_smart_array($_POST);
	
			$next 	= 	nextId('ecommerce_product_categories');
			$select = 	"INSERT INTO ecommerce_product_categories SET ".
						"parent_id='".$_POST['categoryparent']."',".
						"name='".$_POST['name']."',".
						"description='".$_POST['description']."',".
						"image='".basename($_POST['image'])."',".
						"flat_discount='".$_POST['flat_discount']."',".
						"rate_discount='".$_POST['rate_discount']."',".
						"active='1',".
						"created=NULL";
			doQuery($select);
						
	
		
		$report = "Category Added Successfully";
		$success = "1";
		header("Location: {$_SERVER["PHP_SELF"]}?REPORT=".$report."&cid=".$next."&SUCCESS=".$success."&VIEW=".$_REQUEST['VIEW']."");
		exit;
	}
}

/***	UPDATE CATEGORY					********************************************************/
if (isset($_POST['UPDATE_CATEGORY'])){
	$error = 0;	
	if($_POST['name'] == ""){ $error = 1; ReportError("Enter a Category Name"); }
	
	if($error == 0){
		
			$_POST = escape_smart_array($_POST);
	
			$select = 	"UPDATE ecommerce_product_categories SET ".
						"parent_id='".$_POST['categoryparent']."',".						
						"image='".basename($_POST['image'])."',".
						"description='".$_POST['description']."',".
						"name='".$_POST['name']."',".
						"flat_discount='".$_POST['flat_discount']."',".
						"rate_discount='".$_POST['rate_discount']."' ".
						"WHERE category_id='".$_POST['cid']."'";
			doQuery($select);
						
	
		
		$report = "Category Updated Successfully";
		$success = "1";
		//session_write_close();
		header("Location: {$_SERVER["PHP_SELF"]}?REPORT=".$report."&cid=".$_POST['cid']."&SUCCESS=".$success."&VIEW=".$_REQUEST['VIEW']."");
		die();
		exit();
	}
}

/***	UPDATE SALES TAX				********************************************************/
if (isset($_POST['UPDATE_SALES_TAXES'])){

	//
	// LOOP THROUGH SALES TAX
	// 
	$sel = "SELECT * FROM state WHERE active='1'";
	$res = doQuery($sel);
	$num = mysql_num_rows($res);
	$i = 0;
	while($i<$num)
	{
		$row = mysql_fetch_array($res);
		
		$field = "state-".$row['state_id']."";
		$value = $_POST[$field];
		$update = "UPDATE state SET tax='".$value."' WHERE state_id='".$row['state_id']."'";
		doQuery($update);
		
		$i++;
	}
	
	//var_dump($_POST);
	//exit();
	
	header("Location: ".$_SERVER["PHP_SELF"]."?VIEW=".$_REQUEST['VIEW']."&SUB=".$_REQUEST['SUB']."&REPORT=Sales Tax Updated&SUCCESS=1");
	exit();
	

}

/***	UPDATE SETTINGS					********************************************************/
if (isset($_POST['UPDATE_ECOMMERCE_SETTINGS'])){

	//
	// LOOP THROUGH SETTINGS
	// 
	$sel = "SELECT * FROM settings WHERE active='1' AND group_id='4'";
	$res = doQuery($sel);
	$num = mysql_num_rows($res);
	$i = 0;
	while($i<$num)
	{
		$row = mysql_fetch_array($res);
		
		//
		// UPDATE SETTING
		//
		$Settings->updateSetting($row);
		
		$i++;
	}
	
	//var_dump($_POST);
	//exit();
	
	header("Location: ".$_SERVER["PHP_SELF"]."?VIEW=".$_REQUEST['VIEW']."&SUB=".$_REQUEST['SUB']."&REPORT=Settings Updated&SUCCESS=1");
	exit();
	

}

/*** 	DELETE COUPON CODES 			********************************************************/
if (isset($_REQUEST['DELETE_COUPON_CODE']))
{
	doQuery("UPDATE ecommerce_coupon_codes SET active='0' WHERE coupon_id=".$_REQUEST["cuid"]." ".$_SETTINGS['demosqland']."");
	
	$report = "Coupon Code Deleted Successfully";
	$success = "1";
	if($_REQUEST['SUB'] == ""){ $_REQUEST['SUB'] = "COUPONS"; }
	header("Location: ".$_SERVER["PHP_SELF"]."?VIEW=".$_REQUEST["VIEW"]."&SUB=".$_REQUEST['SUB']."&REPORT=".$report."&SUCCESS=".$success."");
	exit();
}

/***	ADD/UPDATE COUPON CODES 		********************************************************/
if (isset($_POST['ADD_COUPON_CODE']) || isset($_POST['UPDATE_COUPON_CODE']))
{
	$error = 0;
	$success = 0;
	$_POST1 = $_POST;
	$_POST = escape_smart_array($_POST);
	
	if($_POST['name'] == ""){ $error=1; ReportError("Please name your coupon code."); }
	if($_POST['code'] == "" AND $_POST['multiple_codes'] == ''){ $error=1; ReportError("Enter the coupon code."); }
	if($_POST['start_date'] == ""){ $error=1; ReportError("Select a start date."); }
	if($_POST['expiration_date'] == ""){ $error=1; ReportError("Select an expiration date."); }

		// ADD COUPON CODE
	if($error == 0){
		if($_POST['cuid'] == ""){
		
			$next = nextId('ecommerce_coupon_codes');
			$insert = 	"INSERT INTO ecommerce_coupon_codes SET ".
						"name='".$_POST['name']."',".
						"code='".$_POST['code']."',".
						"code_range_end='".$_POST['code_range_end']."',".
						"free_promo_product_id='".$_POST['free_promo_product_id']."',".
						"free_promo_flag_product_id='".$_POST['free_promo_flag_product_id']."',".
						"status='".$_POST['status']."',".
						"flat_discount='".$_POST['flat_discount']."',".
						"percent_discount='".$_POST['percent_discount']."',".
						"min_subtotal='".$_POST['min_subtotal']."',".
						"max_flat_discount='".$_POST['max_flat_discount']."',".
						"start_date='".DateTimeIntoTimestamp($_POST['start_date'])."',".
						"expiration_date='".DateTimeIntoTimestamp($_POST['expiration_date'])."',".
						"flag_text_match='".$_POST['flag_text_match']."',".
						"flag_max_discount='".$_POST['flag_max_discount']."',".						
						"code_range_prefix='".$_POST['code_range_prefix']."',".
						"multiple_codes='".$_POST['multiple_codes']."',".
						"max_qty='".$_POST['max_qty']."',".
						"public='".$_POST['public']."',".
						"description='".$_POST['description']."'";
			doQuery($insert);
			
			// UPDATE THE PERMISSIONS
			$delete = "DELETE FROM ecommerce_coupon_permission_relational WHERE coupon_id='".$next."' ";
			doQuery($delete);
			foreach($_POST1['authorized_customers'] AS $permission){
				$insert = "INSERT INTO ecommerce_coupon_permission_relational SET coupon_id='".$next."', permission_id='".$permission."'";
				doQuery($insert);
			}
			
			// UPDATE THE VALID CATEGORIES
			$delete = doQuery("DELETE FROM ecommerce_coupon_category_relational WHERE coupon_id='".$next."'");
			foreach($_POST1['categories'] AS $category){
				$insert = doQuery("INSERT INTO ecommerce_coupon_category_relational SET coupon_id='".$next."', category_id='".$category."'");
			}
			
			// UPDATE THE NOT VALID CATEGORIES
			$delete = doQuery("DELETE FROM ecommerce_coupon_not_valid_category_relational WHERE coupon_id='".$next."'");
			foreach($_POST1['not_valid_categories'] AS $category){
				$insert = doQuery("INSERT INTO ecommerce_coupon_not_valid_category_relational SET coupon_id='".$next."', category_id='".$category."'");
			}			
			
			// VALID PRODUCTS
			$delete = doQuery("DELETE FROM ecommerce_coupon_valid_product_relational WHERE coupon_id='".$next."'");
			foreach($_POST1['valid_products'] AS $valid_product){
				$insert = doQuery("INSERT INTO ecommerce_coupon_valid_product_relational SET coupon_id='".$next."', product_id='".$valid_product."'");
			}
					
			// NOT VALID PRODUCTS
			$delete = doQuery("DELETE FROM ecommerce_coupon_not_valid_product_relational WHERE coupon_id='".$next."'");
			foreach($_POST1['not_valid_products'] AS $not_valid_product){
				$insert = doQuery("INSERT INTO ecommerce_coupon_not_valid_product_relational SET coupon_id='".$next."', product_id='".$not_valid_product."'");
			}
			
			$report = "Coupon code created.";
			$success = 1;		
		} 
		// UPDATE COUPON CODE
		else {
			$next = $_POST['cuid'];
			$update = 	"UPDATE ecommerce_coupon_codes SET ".
						"name='".$_POST['name']."',".
						"code='".$_POST['code']."',".
						"code_range_end='".$_POST['code_range_end']."',".
						"free_promo_product_id='".$_POST['free_promo_product_id']."',".
						"free_promo_flag_product_id='".$_POST['free_promo_flag_product_id']."',".
						"status='".$_POST['status']."',".
						"flat_discount='".$_POST['flat_discount']."',".
						"percent_discount='".$_POST['percent_discount']."',".
						"min_subtotal='".$_POST['min_subtotal']."',".
						"max_flat_discount='".$_POST['max_flat_discount']."',".
						"start_date='".DateTimeIntoTimestamp($_POST['start_date'])."',".
						"expiration_date='".DateTimeIntoTimestamp($_POST['expiration_date'])."',".
						"flag_text_match='".$_POST['flag_text_match']."',".
						"flag_max_discount='".$_POST['flag_max_discount']."',".						
						"code_range_prefix='".$_POST['code_range_prefix']."',".
						"multiple_codes='".$_POST['multiple_codes']."',".
						"max_qty='".$_POST['max_qty']."',".
						"public='".$_POST['public']."',".
						"description='".$_POST['description']."' ".
						"WHERE coupon_id='".$_POST['cuid']."'";
			doQuery($update);
			
			// UPDATE THE PERMISSIONS
			$delete = "DELETE FROM ecommerce_coupon_permission_relational WHERE coupon_id='".$next."' ";
			doQuery($delete);
			foreach($_POST1['authorized_customers'] AS $permission){
				$insert = "INSERT INTO ecommerce_coupon_permission_relational SET coupon_id='".$next."', permission_id='".$permission."'";
				doQuery($insert);
			}			
						
			// UPDATE THE VALID CATEGORIES
			$delete = doQuery("DELETE FROM ecommerce_coupon_category_relational WHERE coupon_id='".$next."'");
			foreach($_POST1['categories'] AS $category){
				$insert = doQuery("INSERT INTO ecommerce_coupon_category_relational SET coupon_id='".$next."', category_id='".$category."'");
			}
			
			// UPDATE THE NOT VALID CATEGORIES
			$delete = doQuery("DELETE FROM ecommerce_coupon_not_valid_category_relational WHERE coupon_id='".$next."'");
			foreach($_POST1['not_valid_categories'] AS $category){
				$insert = doQuery("INSERT INTO ecommerce_coupon_not_valid_category_relational SET coupon_id='".$next."', category_id='".$category."'");
			}
			
			// VALID PRODUCTS
			$delete = doQuery("DELETE FROM ecommerce_coupon_valid_product_relational WHERE coupon_id='".$next."'");
			foreach($_POST1['valid_products'] AS $valid_product){
				$insert = doQuery("INSERT INTO ecommerce_coupon_valid_product_relational SET coupon_id='".$next."', product_id='".$valid_product."'");
			}
					
			// NOT VALID PRODUCTS
			$delete = doQuery("DELETE FROM ecommerce_coupon_not_valid_product_relational WHERE coupon_id='".$next."'");
			foreach($_POST1['not_valid_products'] AS $not_valid_product){
				$insert = doQuery("INSERT INTO ecommerce_coupon_not_valid_product_relational SET coupon_id='".$next."', product_id='".$not_valid_product."'");
			}
			
			$report = "Coupon code updated.";
			$success = 1;
		}
		header("Location: {$_SERVER["PHP_SELF"]}?REPORT=".$report."&cuid=".$next."&SUCCESS=".$success."&VIEW=".$_REQUEST['VIEW']."");
		exit;
	}			
}

/***	DELETE ORDER					***/
if (isset($_REQUEST['DELETE_ORDER'])){
	// REMOVE
	doQuery("UPDATE ecommerce_orders SET active='0' WHERE order_id='".$_REQUEST["oid"]."' ".$_SETTINGS['demosqland']."");
	
	$report = "Order Deleted Successfully";
	$success = "1";
	if($_REQUEST['SUB'] == ""){ $_REQUEST['SUB'] = "ORDERS"; }
	header("Location: ".$_SERVER["PHP_SELF"]."?VIEW=".$_REQUEST["VIEW"]."&SUB=".$_REQUEST['SUB']."&REPORT=".$report."&SUCCESS=".$success."&page=".$_REQUEST['page']."");
	exit();
}
	
/*** 	DELETE ORDERS					****/
if (isset($_REQUEST["DELETE_ORDERS"])){
	
	$itemString = rtrim($_REQUEST['items'], ','); 
	$itemArray = explode(",",$itemString);
	
	foreach($itemArray AS $item_id){
		//echo "".$cartitem."<br>";
		$sel = 	"UPDATE ecommerce_orders SET active='0' WHERE ".
				"order_id='".$item_id."'";
		doQuery($sel);
	}
	
	$report = "Orders Deleted Successfully";
	$success = "1";
	header("Location: ".$_SERVER["PHP_SELF"]."?VIEW=".$_REQUEST["VIEW"]."&SUB=".$_REQUEST['SUB']."&REPORT=".$report."&SUCCESS=".$success."&page=".$_REQUEST['page']."");
	exit();
}

/***	UPDATE ORDER					********************************************************/
if (isset($_POST['UPDATE_ORDER']))
{
	$error = 0;
	
	if($error == 0){
		$update = 	"UPDATE ecommerce_orders SET ".
					"status='".$_POST['status']."' ".
					"WHERE order_id='".$_POST['oid']."'";
		doQuery($update);
	
		$report = "Order updated successfully.";
		$success = "1";
		header("Location: {$_SERVER["PHP_SELF"]}?REPORT=".$report."&oid=".$_POST['oid']."&SUCCESS=".$success."&VIEW=".$_REQUEST['VIEW']."");
		exit;
	
	}	
}

if (isset($_REQUEST['DELETE_REVIEW'])){
	// REMOVE
	doQuery("UPDATE ecommerce_product_comments SET active='0' WHERE comment_id='".$_REQUEST["rid"]."' ".$_SETTINGS['demosqland']."");
	
	$report = "Review Deleted Successfully";
	$success = "1";
	if($_REQUEST['SUB'] == ""){ $_REQUEST['SUB'] = "REVIEWS"; }
	header("Location: ".$_SERVER["PHP_SELF"]."?VIEW=".$_REQUEST["VIEW"]."&SUB=".$_REQUEST['SUB']."&REPORT=".$report."&SUCCESS=".$success."&page=".$_REQUEST['page']."");
	exit();
}

/***	ADD REVIEW					********************************************************/
if (isset($_POST['ADD_REVIEW']))
{	
	$error = 0;
	
	if($error == 0){
		$_POST = escape_smart_array($_POST);
		$update = 	"INSERT INTO ecommerce_product_comments SET ".
					"status='".$_POST['status']."', ".
					"content='".$_POST['content']."', ".
					"user_id='".$_POST['user_id']."', ".
					"bought='".$_POST['bought']."', ".
					"name='".$_POST['name']."', ".
					"email='".$_POST['email']."', ".
					"rating='".$_POST['rating']."', ".
					"created='NULL' ";
		doQuery($update);
	
		$report = "Review updated successfully.";
		$success = "1";
		header("Location: {$_SERVER["PHP_SELF"]}?REPORT=".$report."&rid=".$_POST['rid']."&SUCCESS=".$success."&VIEW=".$_REQUEST['VIEW']."");
		exit;
	
	}
}

/***	UPDATE REVIEW					********************************************************/
if (isset($_POST['UPDATE_REVIEW']))
{	
	$error = 0;
	
	if($error == 0){
		$_POST = escape_smart_array($_POST);
		$update = 	"UPDATE ecommerce_product_comments SET ".
					"status='".$_POST['status']."', ".
					"content='".$_POST['content']."', ".
					"user_id='".$_POST['user_id']."', ".
					"bought='".$_POST['bought']."', ".
					"name='".$_POST['name']."', ".
					"email='".$_POST['email']."', ".
					"rating='".$_POST['rating']."', ".
					"created='".DateTimeIntoTimestamp($_POST['created'])."' ".
					"WHERE comment_id='".$_POST['rid']."'";
		doQuery($update);
	
		//die($update);
		//exit;
	
		$report = "Review updated successfully.";
		$success = "1";
		header("Location: {$_SERVER["PHP_SELF"]}?REPORT=".$report."&rid=".$_POST['rid']."&SUCCESS=".$success."&VIEW=".$_REQUEST['VIEW']."");
		exit;
	
	}
}

/***	CREATE AN ORDER 				***/
// TODO... // PHASE II

/***	CRUD FOR SHIPPING METHODS		********************************************************/
$table						= "ecommerce_shipping_methods";
$name						= "Shipping Method";
$idColumn					= "shipping_method_id";
$id							= $_REQUEST['smid'];
$xid						= "smid";
$emptyValidatedFieldArray	= Array("name");
$fieldArray					= Array("name","description","code","provider","flat_price");
crudTable($table,$name,$idColumn,$id,$xid,$emptyValidatedFieldArray,$fieldArray);

/***	CRUD FOR COUPOON CODES			*******************************************************
$table						= "ecommerce_coupon_codes";
$name						= "Coupon Code";
$idColumn					= "coupon_id";
$id							= $_REQUEST['cuid'];
$xid						= "cuid";
$emptyValidatedFieldArray	= Array("name","code","start_date");
$fieldArray					= Array("name","code","status","flat_discount","percent_discount","min_subtotal","max_flat_discount","start_date","expiration_date");
crudTable($table,$name,$idColumn,$id,$xid,$emptyValidatedFieldArray,$fieldArray);
*/

/***	CRUD FOR AUTOMATED EMAILS		*******************************************************
$table						= "automated_email_contents";
$name						= "Automated Email";
$idColumn					= "email_id";
$id							= $_REQUEST['aeid'];
$xid						= "aeid";
$emptyValidatedFieldArray	= Array("subject","from");
$fieldArray					= Array("subject","from","html");
crudTable($table,$name,$idColumn,$id,$xid,$emptyValidatedFieldArray,$fieldArray);
*/

/***	CRUD FOR ATTRIBUTES				********************************************************/
$table						= "ecommerce_product_attributes";
$name						= "Product Attribute";
$idColumn					= "attribute_id";
$id							= $_REQUEST['aid'];
$xid						= "aid";
$emptyValidatedFieldArray	= Array("name","label");
$fieldArray					= Array("name","label","type","description","category_id");
crudTable($table,$name,$idColumn,$id,$xid,$emptyValidatedFieldArray,$fieldArray);

/***	CRUD FOR ATTRIBUTE OPTIONS		********************************************************/
$table						= "ecommerce_product_attribute_values";
$name						= "Attribute Option";
$idColumn					= "attribute_value_id";
$id							= $_REQUEST['aoid'];
$xid						= "aoid";
$emptyValidatedFieldArray	= Array("name");
$fieldArray					= Array("name","attribute_id","image","price","default");
$parentItemId				= "aid";
crudTable($table,$name,$idColumn,$id,$xid,$emptyValidatedFieldArray,$fieldArray,$parentItemId);

/***	Preview Import							********************************************************/
if (isset($_POST['PREVIEW_IMPORT'])){
	
	// GET TODAY IN 3 VARIABLES
	$m = date("m");
	$d = date("d");
	$y = date("Y");

	// UPLOAD CSV FILE 
	$_FILES['data']['name'] = preg_replace("/ +/", " ", trim($_FILES['data']['name']));
	$_FILES['data']['name'] = str_replace(" ", "", $_FILES['data']['name']);
	$_FILES['data']['name'] = strtolower($_FILES['data']['name']);
	
	// MAKE FILE NAME
	$_FILES['data']['name'] = "$m-$d-$y" . $_FILES['data']['name'];
	$path = "modules/ecommerce/import_files/" . $_FILES['data']['name'];
	
	// DELETE IF ALREADY EXISTS
	unlink($path);
	
	// SAVE CSV FILE TO DATA FOLDER
	move_uploaded_file($_FILES['data']['tmp_name'],$path) or die("Could not upload data  DATA:$path ");
	chmod($path,0777);
	$report = "File Saved Data:$path.";
	
	// GET MONTH
	//$month = $_POST['month'];
	
	// GET YEAR
	//$year = $_POST['year'];

	// GET SECONDS OF NOW
	//$today = strtotime("now");

	// RUN LINES 
	ini_set('auto_detect_line_endings','1');
	$fh = fopen($_SETTINGS['website']."admin/modules/ecommerce/import_files/" . $_FILES['data']['name'],  "r");
	$b=0;
	
	
	
	
	while ($line = fgetcsv($fh, 5000, ',','"')) {
		
		$line = $Ecommerce->oencode($line);
		
		// GET DATA FROM CSV FIELDS 
		$name = $line[0];
		$description = $line[1];
		$price = $line[2];
		$product_number = $line[3];
		$naming_convention = $line[4];
		$image = $line[5];
		
		// PUT IMPORTANT DATA INTO AN INNER ARRAY 
		$innerarray[0] = $name;
		$innerarray[1] = $description;
		$innerarray[2] = $price;
		$innerarray[3] = $product_number;
		$innerarray[4] = $naming_convention;
		$innerarray[5] = $image;
				
		// PUT INNERARRAY INTO A MULTIDIMENSIONAL OUTER ARRAY 
		$outerarray[$b] = $innerarray;
		$b++;
	}//while
}

/***	Finish Import							********************************************************/
if (isset($_POST['FINISH_IMPORT'])){

	//error_reporting(E_ALL);
	//echo "MADE IT<br>";
	// GET MONTH
	//$month = $_POST['month'];	
	// GET YEAR
	//$releaseyear = $_POST['year'];

	if (isset($_POST['path'])){			
		//echo "MADE IT HERE :: PATH :: ".$_POST['path']."<br>";	
		// RUN LINES 
		ini_set('auto_detect_line_endings','1');
		$fh = fopen($_SETTINGS['website']."admin/".$_POST['path'],"r");
		if($fh){			
			//echo "MADE IT HERE TOO<br>";		
			$b=0;
			while ($line = fgetcsv($fh, 5000, ',','"')){				 
				
				$line = $Ecommerce->oencode($line);
				$line = escape_smart_array($line);
				
				// GET DATE FROM CSV FIELDS 
				$name = $line[0];	
				$description = $line[1];
				$price = $line[2];
				$product_number = $line[3];
				$naming_convention = $line[4];
				$image = $line[5];
					
				//
				// CHECK AND INSERT THE REVIEW
				//
				$select1 = "SELECT * FROM ecommerce_products WHERE name='".$name."' LIMIT 1";
				$result1 = doQuery($select1);
				$num1 = mysql_num_rows($result1);
				
				//cho "CHECK : $select1 <br>";
				
				if($num1 == 0){					
					//echo "------------<br>LINE ".$b." :: STATUS: $status :: REL TIME : ".strtotime($release_date)." :: NOW TIME: ".strtotime("now")." <Br>";					
					$review_id = nextId('reviews');
					$select = 	"INSERT INTO `ecommerce_products` SET ".
								"`product_id`='',".
								"`name`='".$name."',".
								"`description`='".$description."',".
								"`price`='".$price."',".
								"`product_number`='".$product_number."',".
								"`naming_convention`='".$naming_convention."',".
								"`image`='".$image."',".
								"`status`='Draft',".
								"`created`=NULL,".
								"`date`=NOW()";
					$result = doQuery($select);	
					//echo "<br> $select <br>";
					//echo(mysql_error()."<br>");						
				}
				$b++;
				//echo "<br>";
			}//while
		} // IF FOPEN
		//exit();
		$report = "Import Successful";
		$success = "1";
		header("Location: ?REPORT=".$report."&SUCCESS=1&SUB=IMPORTPRODUCTS&VIEW=".$_REQUEST['VIEW']."");
		exit();		
	}
}

/********************************************************************************************
*
* BEGIN FORMS
*
********************************************************************************************/

/*** UPDATE / Add PRODUCT FORM		********************************************************/
if ($_REQUEST['SUB'] == 'NEWPRODUCT' OR $_GET['pid'] != ""){
	// ADD/EDIT CHECK
	if (isset($_REQUEST["pid"])) {
		$select = 	"SELECT * FROM ecommerce_products ".
					"WHERE ".
					"product_id='".$_REQUEST["pid"]."'";
		$res = doQuery($select);
		$_POST = mysql_fetch_array($res);		
		$_POST['name'] = form_encode($_POST['name']);		
		$button = "Update Product";
		$doing = "Product";
	} else {
		$button = "Add Product";
		$doing = "New Product";
	}	
	
	// START FORM
	startAdminForm();
	
	// START TABLE
	echo tableHeader("$doing: ".$_POST['name']."",2,'100%');
	
	// PRODUCT NAME
	adminFormField("*Product Name","name",$_POST['name'],"textbox");
	
	// DESCRIPTION
	adminFormField("Description","description",$_POST['description'],"textarea");		
	
	// TAKE INVENTORY
	adminFormField("Take Inventory","take_inventory",$_POST['take_inventory'],"checkbox");		
	
	// TAKE INVENTORY
	adminFormField("Remove If 0","take_off_inventory_empty",$_POST['take_off_inventory_empty'],"checkbox");		
	
	// INVENTORY
	adminFormField("Current Inventory","inventory",$_POST['inventory'],"textbox");		
		
	// STATUS
	adminFormField("Status","status",$_POST['status'],"select",Array("Published","Pending","Draft"));
	
	// HIDDEN
	adminFormField("Hidden For Promo","hidden_promo",$_POST['hidden_promo'],"checkbox");		
	
	// DATE
	adminFormField("Date","date",$_POST['date'],"date");
	
	// PRODUCT IMAGES
	
	echo "	<tr>
			<th>*Follow Naming Convention</th>
			<td><input type='radio' name='naming_convention' ".isChecked($_POST['naming_convention'],'1')." value='1'> ex. \"".strtolower(str_replace(" ","_",$_POST['name'])).".jpg\"</td>
			</tr>
			<tr>
			<th>*Choose Image</th>
			<td><input type='radio' name='naming_convention' ".isChecked($_POST['naming_convention'],'0')." value='0'>";
			
	echo	"	<input type=\"text\" name=\"image\" value=\"".$_POST['image']."\" />";
	echo	"	<button type='button' onClick='SmallFileBrowser(\"../uploads-products/\",\"image\")'>Choose Image...</button>";
	echo	"	Choose Image...";
	echo	"	</button>";
			
	echo "	</td>
			</tr>
	";
	
	$Ecommerce->productImages();
	
	// PRODUCT NUMBER
	adminFormField("Product Number","product_number",$_POST['product_number'],"textbox");
	
	// QB NAME
	// This was removed. Product Number replaces this field.
	//adminFormField("Quickbooks Item Name","qb_name",$_POST['qb_name'],"textbox");
	
	// PRICE
	adminFormField("*Price","price",$_POST['price'],"currency");
	
	// PRICE
	adminFormField("Flat Discount / Sale","flat_discount",$_POST['flat_discount'],"currency");
	
	// PRICE
	adminFormField("Rate Discount","rate_discount",$_POST['rate_discount'],"decimal");
	
	// WEIGHT
	$sela = "SELECT * FROM ecommerce_product_types WHERE type_id='".$_POST['product_type']."'";
	$resa = doQuery($sela);
	$rowa = mysql_fetch_array($resa);
	if($rowa['name'] == 'Item'){
		adminFormField("Weight","weight",$_POST['weight'],"weight");
	}
	// PRODUCT TYPE
	echo "<TR BGCOLOR='#f2f2f2'>";
	echo "	<Th width='200' height='40' style='padding-left:20px;'>Type</Th>";
	echo "	<TD>";
	echo "		<select name='product_type'>";
					$sel1 = "SELECT * FROM ecommerce_product_types WHERE active='1'";
					$res1 = doQuery($sel1);
					$i1 = 0;
					$num1 = mysql_num_rows($res1);
					while($i1<$num1){
						$row1 = mysql_fetch_array($res1);
						$selected = "";
						if($_POST['product_type'] == $row1['type_id']){ $selected = " SELECTED "; }
						$value = "";
						$value = $row1['type_id'];
						echo "		<option ".$selected." ".$value.">".$row1['name']."</option>";					
						$i1++;
					}	
	echo "		</select>";
	echo "	</TD>";
	echo "</TR>";
	// RECURRING		
	if($_POST['product_type'] == '3'){
		$options = Array("Weekly","Bi-Weekly","Monthly","Semi-Annually","Annually","Bi-Annually");
		adminFormField("Recurring","recurring_interval",$_POST['recurring_interval'],"select",$options);
	}
	// ZOOM		
	adminFormField("Enable Zoom","zoom",$_POST['zoom'],"checkbox");
	// USER ACCOUNTS SECTION
	if(checkActiveModule('0000005')){
		if($_REQUEST['pid'] != ""){
			// PERMISSIONS	
			selectPermissions();					
		}
	}
	// CATEGORIES
	$identifier = "categories"; 
	echo "<TR BGCOLOR='#f2f2f2'>";
	echo "	<Th width='200' height='40' style='padding-left:20px;'>Categories</Th>";
	echo "	<TD>";
				hierarchymultiselectTable('ecommerce_product_categories','categories[]','category_id','name','sort_level','ASC',0, 'ecommerce_product_category_relational','product_id',''.$_REQUEST['pid'].'');
	echo "		<script>";
	echo "			//$('#categories').multiSelect();";
	echo "		</script>";
	echo "	</TD>";
	echo "</TR>";
	// CATEGORY ADD NEW	TOGGLER
	echo "<TR BGCOLOR='#f2f2f2'>";
	echo "	<Th width='200' height='40' style='padding-left:20px;'> &nbsp; </Th>";
	echo "	<TD>";
	echo "		<a class='toggleridentifier".$identifier." tog'>New Category</a>";
	echo "		<a class='togglercloseidentifier".$identifier." tog'>Cancel New Category</a>";
	echo "	</TD>";
	echo "</TR>";
	// NEW CATEGORY NAME
	echo "<TR BGCOLOR='#f2f2f2' class='toggleidentifier".$identifier."'>";
	echo "	<Th width='200' height='40' style='padding-left:20px; background-color:#EDF1F2;'>New Category</Th>";
	echo "	<TD style='background-color:#EDF1F2;'>";
	echo "		<input type='text' name='newcategory' value=''>";
	echo "	</TD>";
	echo "</TR>";			
	// NEW CATEGORY PARENT
	echo "<TR BGCOLOR='#f2f2f2' class='toggleidentifier".$identifier."'>";
	echo "	<Th width='200' height='40' style='padding-left:20px; background-color:#EDF1F2;'>New Category Parent</Th>";
	echo "	<TD style='background-color:#EDF1F2;'>";
				$sel1 = "SELECT * FROM ecommerce_product_categories WHERE active='1' AND parent_id=''";
				$res1 = doQuery($sel1);
				$num1 = mysql_num_rows($res1);
				$i1 = 0;
				
				echo "<select name='newcategoryparent'>";
				echo "	<option value=''>No Parent</option>";
						while($i1<$num1){
							$row1 = mysql_fetch_array($res1);
							echo "<option value='".$row1['category_id']."'>".$row1['name']."</option>";					
							$sel2 = "SELECT * FROM ecommerce_product_categories WHERE active='1' AND parent_id='".$row1['category_id']."'";
							$res2 = doQuery($sel2);
							$num2 = mysql_num_rows($res2);
							$i2 = 0;
							while($i2<$num2){
								$row2 = mysql_fetch_array($res2);							
								echo "<option value='".$row2['category_id']."'>&nbsp;&nbsp;".$row2['name']."</option>";								
								$i2++;
							}
							$i1++;
						}
		
	echo "	</TD>";
	echo "	</TR>";
	echo "	<script type='text/javascript'> ";
	echo "		$('.toggleidentifier".$identifier."').hide(); ";
	echo "		$('.togglercloseidentifier".$identifier."').hide(); ";
	echo "		$('.toggleridentifier".$identifier."').click(function(){ ";
	echo "			$('.toggleidentifier".$identifier."').slideToggle('fast',callback('".$identifier."')); ";
	echo "		}); ";			  
	echo "		$('.togglercloseidentifier".$identifier."').click(function(){ ";
	echo "			$('.toggleidentifier".$identifier."').slideToggle('fast',callback1('".$identifier."')); ";
	echo "		}); ";
	echo "	</script> ";
	// ATTRIBUTES
	echo "<tr><th>Attributes</th>";
	echo "	<td>";
	//hierarchymultiselectTable('ecommerce_product_attributes','attributes[]','attribute_id','name','sort_level','ASC',0, 'ecommerce_product_attribute_relational','product_id',''.$_REQUEST['pid'].'');
	multiselectTable('ecommerce_product_attributes','attributes[]','attribute_id','name','sort_level','ASC',0,'ecommerce_product_attribute_relational','product_id',''.$_REQUEST['pid'].'','attribute_id');
	echo "	<td>";
	echo "</tr>";
	
	
	// END ADMIN FORM
	endAdminForm($button,"pid","PRODUCT");
}

/*** IMPORTING REVIEWS							********************************************************/
elseif($_REQUEST['SUB'] == "IMPORTPRODUCTS"){
	$table = "";
	?>
	<form action="" method="post" enctype="multipart/form-data" id="form-demo">
		<?
		echo tableHeader("Import Products:",2,'100%');
		?>
		<tr>
		<th>Browse For Import File
		</th>
		<td>
		<h2></h2>
		<p>Click the button below to browse for a file to upload.</p>
		<!-- <a rel="" title="" target="" href="#" class="" id="demo-browse"> -->
		<input type="file" name="data" />		
		<!-- </a> -->					
		</td>
		</tr>
		</table>
		
		<!-- PREVIEW -->
		<?
		if(isset($_POST['PREVIEW_IMPORT'])){
		echo tableHeaderid("Import Products Preview",6,"100%","list");
		echo "<thead><TR><th width='15' style='width:15px;'>#</th><th>Name</th><th>Description</th><th>Price</th><th>Product Number</th><th>Naming Convention</th><th>Image</th></TR></thead><tbody>";
			
			$b = 0;
			
			// COUNT OUTER ARRAY
			$anum = count($outerarray);	
			
			// SORT MULTIDIMENSIONAL ARRAY BY
			$temp = msort($outerarray, 0);
			
			// RESET TEMP VARIABLE
			reset($temp);
			
			// WEEKER IS A COUNT OF THE WEEK
			$weeker = 1;
			
			// WE 	
			$we = 0;
			
			//$i
			$i = 1;

			
			// LOOP
			while (list($key, $value) = each($temp)) {
				
				
				$table .= "
				<tr>
				<td>".$i."</td>
				<td>".$value[0]."</td>
				<td>".$value[1]."</td>	
				<td>".$value[2]."</td>
				<td>".$value[3]."</td>	
				<td>".$value[4]."</td>
				<td>".$value[5]."</td>	
				";
				
				$table .= "</tr>";
				$i++;
		    }//while

			echo "$table";
		}
		?>		
		</table>		
		<div id="submit">		
			<!-- <img src="images/icons/up_16.png" style='border:0px;' > -->
			
			<?
			if(isset($_POST['PREVIEW_IMPORT'])){
				?>
				<input type="hidden" name="path" value="<?=$path?>" >
				<input type="submit" name="FINISH_IMPORT" value="Finish Import" >
				<?
			} else {
				?>
				<input type="submit" name="PREVIEW_IMPORT" value="Preview Import" >
			<?
			}
			?>
		</div>
		
	</form>
	<form action="index.php" method="GET" style="text-align:right; padding:0 10px 10px 0; background:#EDF1F2;">
		<input type="hidden" name="filename" value="modules/ecommerce/import_files/import_example.csv" >
		<INPUT TYPE=SUBMIT NAME="DOWNLOADFILE" value="Download Example File" >
	</form>
	<?

}

/*** ADD/UPDATE CATEGORY FORM		********************************************************/
elseif($_GET['cid'] != '' || $_REQUEST['SUB'] == 'NEWCATEGORY'){
	
	if (isset($_REQUEST["cid"])){
		$select = 	"SELECT * FROM ecommerce_product_categories ".
					"WHERE ".
					"category_id='".$_REQUEST["cid"]."'";
		$res = doQuery($select);
		$_POST = mysql_fetch_array($res);
		
		$button = "Update Category";
		$doing = "Category";
	} else {
		$button = "Add Category";
		$doing = "New Category";
	}
	
	// START FORM
	startAdminForm();
	
	// START TABLE
	echo tableHeader("$doing: ".$_POST['name']." ".$_POST['category_id']."",2,'100%');
	
	// CATEGORY NAME
	adminFormField("*Category Name","name",$_POST['name'],"textbox");	
	
	adminFormField("Category Flat Discount","flat_discount",$_POST['flat_discount'],"currency");
	//adminFormField("Category Rate Discount","rate_discount",$_POST['rate_discount'],"decimal");
	
	// CATEGORY IMAGE
	echo "
		<tr>
		<th>Image</th>
		<td><input style='float:none;' type='text' name='image' value='".$_POST['image']."' /><button type='button' onClick='SmallFileBrowser(\"../uploads/\",\"image\")'>Choose Image...</button><br><br></td>
		</tr>
		";
	adminFormField("*Description","description",$_POST['description'],"textarea");	
	
	// CATEGORY PARENT
	echo	"<TR BGCOLOR='#f2f2f2'>";
	echo	"	<Th width='200' height='40' style='padding-left:20px;'>Parent Category</Th>";
	echo	"	<TD>";
	$sel1 = "SELECT * FROM ecommerce_product_categories WHERE active='1' AND parent_id=''";
	$res1 = doQuery($sel1);
	$num1 = mysql_num_rows($res1);
	$i1 = 0;
	echo 	"	<select name='categoryparent' style='width:300px;' size='10'>";
	echo	"		<option value=''>No Parent</option>";
					while($i1<$num1){
						$row1 = mysql_fetch_array($res1);
						$selected = "";
						if($_POST['parent_id'] == $row1['category_id']){ $selected = " SELECTED "; }
						echo "<option ".$selected." value='".$row1['category_id']."'>".$row1['name']."</option>";
						$sel2 = "SELECT * FROM ecommerce_product_categories WHERE active='1' AND parent_id='".$row1['category_id']."'";
						$res2 = doQuery($sel2);
						$num2 = mysql_num_rows($res2);
						$i2 = 0;
						while($i2<$num2){
							$row2 = mysql_fetch_array($res2);
							$selected = "";
							if($_POST['parent_id'] == $row2['category_id']){ $selected = " SELECTED "; }
							echo "<option  ".$selected." value='".$row2['category_id']."'>&nbsp;&nbsp;".$row2['name']."</option>";
							$i2++;
						}
						$i1++;
					}
	echo	"	</TD>";
	echo	"</TR>";
	
	// END FORM
	endAdminForm($button,"cid","CATEGORY");
	
	if($_POST['category_id'] != ""){
		// SORTABLE 
		echo "
		<div class='textcontent1'>
			<h1>Products</h1>
			<a class='admin-new-button' href='index.php?VIEW=".$_REQUEST['VIEW']."&SUB=NEWPRODUCT' >New Product</a>
		</div>
		<br />
		<br />
		";
		
		// HEADER
		echo tableHeaderid("Products",6,"100%","list");
		echo "<thead><TR><th width='300px'>Product</th><th>&nbsp;</th></TR></thead><tbody>";
		echo "</tbody></table>";
		
		$ulstyle = " style='float:right; margin-right:60%;' ";
		

			// FIRST LEVEL PRODUCTS
			echo "<ul class=\"resultslist connectedSortableProducts\" id=\"sortableproducts\">";	
				$selectp1 = "SELECT * FROM ecommerce_products ". 
							"LEFT JOIN ecommerce_product_category_relational ON ecommerce_products.product_id=ecommerce_product_category_relational.product_id ".
							"WHERE ".
							"ecommerce_product_category_relational.category_id='".$_POST['category_id']."' ".
							"AND ecommerce_products.active='1' ".
							"ORDER BY ecommerce_product_category_relational.sort_level ASC";
				$resultp1 = doQuery($selectp1);
				$nump1 = mysql_num_rows($resultp1);
				//if(!$nump1){ echo "<li class=\"".$class."\" id=\"".$rowp1['item_id']."\"> <span class=\"noprod\"></span> <span> This category contains 0 products.</span></li>"; }
				$ip1 = 0;
				while($rowp1 = mysql_fetch_array($resultp1)){
					echo "<li class=\"".$class." selector\" id=\"".$rowp1['item_id']."\"> <span class=\"prod1\"></span> <span>{$rowp1["name"]}</span>";
						// FIRST LEVEL PRODUCT FORM
						echo "<FORM ".$ulstyle." class=\"listform\" METHOD=GET ACTION=\"$_SERVER[PHP_SELF]\">";
						echo "<INPUT TYPE=HIDDEN NAME=pid VALUE=\"{$rowp1["product_id"]}\">";
						echo "<INPUT TYPE=HIDDEN NAME=VIEW VALUE=\"{$_GET["VIEW"]}\">";
						echo "<INPUT TYPE=HIDDEN NAME=SUB VALUE=\"{$_GET["SUB"]}\">";
						echo "<INPUT TYPE=SUBMIT NAME=DELETE_PRODUCT VALUE=\"Delete\" onClick=\"return confirm('Are You Sure?');\">";
						echo " <INPUT TYPE=SUBMIT NAME=view VALUE=\"View\">";
						echo "</FORM>";				
					echo "</li>";
					$ip2++;
				}
			echo "</ul>";
			?>
			<script>
			$("#sortableproducts").sortable({
				connectWith: ".connectedSortableProducts",
				dropOnEmpty: true,
				beforeStop: function (event, ui){
					itemContext = ui.item.context;
				},
				receive: function(event, ui) {
					var senderArray = $(ui.sender).sortable('toArray');
					var senderstring = senderArray.toString();										
					if(ui.sender){ alert('sent '+ui.sender+' '+senderstring+''); } else { alert('not sent'); }
					$.ajax({
						type: 'POST',
						url: 'modules/ecommerce/ecommerce.php',
						data: { SORT_PRODUCTS: '1', RELATIONAL_ID: $(itemContext).attr('id'), NEW_CATEGORY: $(itemContext).parent().parent().attr('id') }
					});
					// RESORT THE NEW CATEGORY
					
				},
				stop: function(event, ui) { 
					var result = $('#sortableproducts').sortable('toArray');
					var resultstring = result.toString();	
					$.ajax({
						type: 'POST',
						url: 'modules/ecommerce/ecommerce.php',
						data: { sortarray: resultstring, SORT_PRODUCTS: '1' }
					});
				}
			});					
			</script>
		
			<div class="pagination">&nbsp;</div>			
		<?	
	}	
}

/*** ADD/UPDATE ORDER FORM			********************************************************/
elseif($_REQUEST['oid'] || $_REQUEST['SUB'] == 'NEWORDER'){
	
	// ADD/EDIT CHECK
	if (isset($_REQUEST["oid"])) {
		$select = 	"SELECT * FROM ecommerce_orders ".
					"WHERE ".
					"order_id='".$_REQUEST["oid"]."'";
		$res = doQuery($select);
		$_POST = mysql_fetch_array($res);		
		$_POST['name'] = form_encode($_POST['name']);		
		$button = "Update Order";
		$doing = "Order";
		$editing = "".$_POST['order_id']." / ".lookupDbValue('user_account','name',$_POST['account_id'],'account_id')."";
	} else {
		$button = "Add Order";
		$doing = "New Order";
	}	
	// START FORM
	startAdminForm();
	// START TABLE
	echo tableHeader("$doing: ".$editing."",2,'100%');
	
	// ACCOUNT NAME
	//adminFormField("*Account","account_id",$_POST['account_id'],"select:user_account");
	echo "<tr><th>Account</th><td>".lookupDbValue('user_account','name',$_POST['account_id'],'account_id')."</td></tr>";
	
	// ACCOUNT TYPE
	$permission_id = lookupDbValue('user_account','user_permission',$_POST['account_id'],'account_id');
	echo "<tr><th>Account Type</th><td>".lookupDbValue('user_permission','name',$permission_id,'permission_id')."</td></tr>";
	
	// DATE
	//adminFormField("Date","created", $_POST['created'],"date");
	echo "<tr><th>Date</th><td>".TimestampIntoDate($_POST['created'])." ".TimestampIntoTime($_POST['created'])."</td></tr>";
	
	// STATUS
	adminFormField("Status","status", $_POST['status'],"select",Array('New','Unprocessed','Open','Shipped','Failed'));		
	
	// INVOICE
	echo "	<tr>
			<th>Invoice</th>
			<td>
			<a href='".$_SETTINGS['website']."admin/modules/ecommerce/ecommerce_invoice_pdf.php?order_id=".$_POST['order_id']."' target='_blank'>Print Invoice</a>
			</td>
			</tr>";
		

	// PAYMENT METHOD
	$select = "SELECT * FROM ecommerce_payment_methods WHERE payment_method_id='".$_POST['payment_method_id']."' LIMIT 1";
	$result = doQuery($select);
	$paymentMethod = mysql_fetch_array($result);
	
	//$Ecommerce->maskCreditCard($transaction['cc_number'],4)
	echo "	<tr>
			<th>Payment Method</th>
			<td>".$paymentMethod['name']." ".$Ecommerce->maskCreditCard(lookupDbValue('ecommerce_order_transactions','cc_number',$_POST['order_id'],'order_id'),0,12)."</td>
			</tr>
	";
	
	// SHIPPING METHOD	
	$select = "SELECT * FROM ecommerce_shipping_methods WHERE shipping_method_id='".$_POST['shipping_method_id']."' LIMIT 1";
	$result = doQuery($select);
	$shippingMethod = mysql_fetch_array($result);
	echo "	<tr>
			<th>Shipping Method</th>
			<td>
			".$shippingMethod['name']." <!-- <small>(<a href=\"javascript:alert('Shipping Label Functionality Coming Soon!');\">Fetch Shipping Label</a>)</small> -->";
	
			// TODO...
			// USPS SHIPPING LABEL
			//echo '<pre>'; var_dump($Ecommerce->USPSLabel($_POST['order_id'])); echo '</pre>';
			//$USPSResponse = $Ecommerce->USPSLabel($_POST['order_id']);
			//$USPSLabel = $USPSResponse['DeliveryConfirmationV3.0Response']['DeliveryConfirmationLabel']['VALUE'];

	echo "	</td>
			</tr>";
	
	// IF SHIPPED THEN SHOW TRACKING CODE INPUT, EMAIL SEND BUTTON, AND SENT STATUS
	/* 
	if($_POST['status'] == 'Shipped')
	{		
		echo "		<tr>
							<th>Tracking Details</th>
							<td><input id='tracking_id' class='' /> <button id='send_tracking_email_button' style='display:none;'>Send Email</button> <span id='email_sent_message'></span></td>
							// This Field Under Construction
							<script type='text/javascript'>
								$('#tracking_id').keypress(function(){
										$('#send_tracking_email_button').show();
								});
								$('#send_tracking_email_button').click(function(){
										alert('');
								});
							</script>
							
					</tr>";
	}
	*/
	
	// INVOICE
	/*echo "	<tr>
			<th>Invoice</th>
			<td>
			<a href='".$_SETTINGS['website']."admin/modules/ecommerce/ecommerce_invoice_pdf.php?order_id=".$_POST['order_id']."' target='_blank'>Print Invoice</a>
			</td>
			</tr>";
	*/		
	// REFERRER
	echo "	<tr><th>Referrer</th><td>";
			
			echo "<select name='referrer_id'>";		
				echo "	<option value=''> </option>";
				
				// CHECK IF ACCOUNT PERMISSIONS ARE SET UP FOR REFERRALS AND BUILD SQL STRING
				$sel = "SELECT * FROM user_permission WHERE referrable='1' AND active='1'";
				$res = doQuery($sel);
				$num = mysql_num_rows($res);
				$i = 0;
				$userPermissionSQL = "";
				while($i<$num){
					$ro = mysql_fetch_array($res);
					// IF THE PERMISSION / ACCOUNT TYPE IS refer-ABLE
					//if($ro['referrable'] == '1'){
						if($userPermissionSQL != ""){ $userPermissionSQL .= " AND "; }
						$userPermissionSQL .= " user_permission='".$ro['permission_id']."' ";
					//}
					$i++;
				}
				
				if($userPermissionSQL != ""){ $userPermissionSQL = "AND (".$userPermissionSQL.") "; }
				$csel = "SELECT * FROM user_account WHERE active='1' ".$userPermissionSQL."";
				$cres = doQuery($csel);
				$cnum = mysql_num_rows($cres);
				$c = 0;
				while($c<$cnum){
					$selected = "";
					if($_POST['referrer_id'] == $crow['account_id']){ $selected = " SELECTED "; }
					$crow = mysql_fetch_array($cres);
					echo "<option value='".$crow['account_id']."'>".ucwords($crow['name'])."</option>";
					$c++;
				}
				
			echo "</select>";
			
			
			
			
			
	echo "	</td></tr>";
	
	// THE CART CONTENTES
	echo "<tr><td colspan='2'>";
		$Ecommerce->theShoppingCart($readOnly=true,$forcecartid=$_POST['shopping_cart_id']);						 
	echo "</td></tr>";
	
	
	// BILLING INFORMATION
	$select = "SELECT * FROM user_contact WHERE contact_id='".$_POST['billing_id']."' LIMIT 1";
	$result = doQuery($select);
	$row = mysql_fetch_array($result);
	
	$_POST['billing_fname'] 	= $row['first_name'];
	$_POST['billing_mi'] 		= $row['middle_initial'];
	$_POST['billing_lname'] 	= $row['last_name'];
	$_POST['billing_email']		= $row['email'];
	$_POST['billing_address1'] 	= $row['address1'];
	$_POST['billing_address2'] 	= $row['address2'];
	$_POST['billing_city'] 		= $row['city'];
	$_POST['billing_state'] 	= $row['state'];
	$_POST['billing_zip'] 		= $row['zip'];
	$_POST['billing_phone'] 	= $row['phone'];
	$_POST['billing_country'] 	= $row['country'];	
	$_POST['billing_region'] 	= $row['region'];
	$_POST['billing_company'] 	= $row['company'];	
	
	// SHIPPING INFORMATION
	$select = "SELECT * FROM user_contact WHERE contact_id='".$_POST['shipping_id']."' LIMIT 1";
	$result = doQuery($select);
	$row = mysql_fetch_array($result);
	
	$_POST['shipping_fname'] 	= $row['first_name'];
	$_POST['shipping_mi'] 		= $row['middle_initial'];
	$_POST['shipping_lname'] 	= $row['last_name'];
	$_POST['shipping_email']	= $row['email'];
	$_POST['shipping_address1'] = $row['address1'];
	$_POST['shipping_address2'] = $row['address2'];
	$_POST['shipping_city'] 	= $row['city'];
	$_POST['shipping_state'] 	= $row['state'];
	$_POST['shipping_zip'] 		= $row['zip'];
	$_POST['shipping_phone'] 	= $row['phone'];
	$_POST['shipping_country'] 	= $row['country'];	
	$_POST['shipping_region'] 	= $row['region'];
	$_POST['shipping_company'] 	= $row['company'];	
	
	echo "
		<tr>
			<td colspan='2'>
				<table class='table-in-table'>
					<tr>
						<th style='text-align:left; width:15%;'>Billing Address</th>
						<th style='text-align:left; width:15%;'>Shipping Address</th>
						
					</tr>
					<tr>
						<td>
							<table class='table-in-table'>
								<tr>
									<th>*Name</th>
									<td>
										<input type='text' name='billing_fname' value='".$_POST['billing_fname']."' style='width:100px;'> 
										<input type='text' name='billing_mi' value='".$_POST['billing_mi']."' maxlength='1' style='width:10px;'> 
										<input type='text' name='billing_lname' value='".$_POST['billing_lname']."' style='width:150px;'> 
									</td>
								</tr>
								<tr>
									<th>Company Name</th>
									<td><input type='text' name='billing_compnay' value='".$_POST['billing_company']."'></td>
								</tr>	
								<tr>
									<th>*Email</th>
									<td><input type='text' name='billing_email' value='".$_POST['billing_email']."'></td>
								</tr>
								<tr>
									<th>*Address 1</th>
									<td><input type='text' name='billing_address1' value='".$_POST['billing_address1']."'></td>
								</tr>
								<tr>
									<th>Address 2</th>
									<td><input type='text' name='billing_address2' value='".$_POST['billing_address2']."'></td>
								</tr>
								<tr>
									<th>*City</th>
									<td><input type='text' name='billing_city' value='".$_POST['billing_city']."'></td>
								</tr>
								<tr>
									<th>State</th>
									<td>";
									selectTable("state","billing_state","state_id","state","state","ASC");
	echo 							"</td>
								</tr>
								<tr>
									<th>Zip</th>
									<td><input type='text' name='billing_zip' value='".$_POST['billing_zip']."'></td>
								</tr>
								<tr>
									<th>Region</th>
									<td><input type='text' name='billing_region' value='".$_POST['billing_region']."'></td>
								</tr>
								<tr>
									<th>Country</th>
									<td>";
									selectTable("country","billing_country","country_id","country","country","ASC");
	echo							"</td>
								</tr>
								<tr>
									<th>Phone</th>
									<td><input type='text' name='billing_phone' value='".$_POST['billing_phone']."'></td>
								</tr>								
							</table>
						</td>
						<td>
							<table class='table-in-table'>
								<tr>
									<th>*Name</th>
									<td>
										<input type='text' name='shipping_fname' value='".$_POST['shipping_fname']."' style='width:100px;'> 
										<input type='text' name='shipping_mi' value='".$_POST['shipping_mi']."' maxlength='1' style='width:10px;'> 
										<input type='text' name='shipping_lname' value='".$_POST['shipping_lname']."' style='width:150px;'> 
									</td>
								</tr>
								<tr>
									<th>Company Name</th>
									<td><input type='text' name='shipping_compnay' value='".$_POST['shipping_company']."'></td>
								</tr>	
								<tr>
									<th>*Address 1</th>
									<td><input type='text' name='shipping_address1' value='".$_POST['shipping_address1']."'></td>
								</tr>
								<tr>
									<th>Address 2</th>
									<td><input type='text' name='shipping_address2' value='".$_POST['shipping_address2']."'></td>
								</tr>
								<tr>
									<th>*City</th>
									<td><input type='text' name='shipping_city' value='".$_POST['shipping_city']."'></td>
								</tr>
								<tr>
									<th>State</th>
									<td>";
									selectTable("state","shipping_state","state_id","state","state","ASC");
	echo 							"</td>
								</tr>
								<tr>
									<th>Zip</th>
									<td><input type='text' name='shipping_zip' value='".$_POST['shipping_zip']."'></td>
								</tr>
								<tr>
									<th>Region</th>
									<td><input type='text' name='shipping_region' value='".$_POST['shipping_region']."'></td>
								</tr>
								<tr>
									<th>Country</th>
									<td>";
									selectTable("country","shipping_country","country_id","country","country","ASC");
	echo							"</td>
								</tr>
								<tr>
									<th>Phone</th>
									<td><input type='text' name='shipping_phone' value='".$_POST['shipping_phone']."'></td>
								</tr>		
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	";
	
	
	
	// END ADMIN FORM
	endAdminForm($button,"oid","Order");
	
	// GENERATE SHIPPING LABEL
	//echo '<pre>'; print_r(USPSLabel()); echo '</pre>';
	//$USPSResponse = USPSLabel();
	//$USPSLabel = $USPSResponse['DeliveryConfirmationV3.0Response']['DeliveryConfirmationLabel']['VALUE'];

	// CALCULATING SHIPPING
	//echo USPSParcelRate(3,90210);

	
}

/*** SETTINGS FORM					********************************************************/
elseif($_REQUEST['SUB'] == "SETTINGS"){

	$name 		= "Ecommerce Settings";
	$group_id 	= "4";
	administrativeSettingsForm($name,$group_id);
	
}

/*** PAYMENT METHODS				********************************************************/
elseif($_REQUEST['SUB'] == 'PAYMENTMETHODS'){
	
	//
	// PAYMENT METHODS COMING
	//

}

/*** SHIPPING METHODS				********************************************************/
elseif($_REQUEST['smid'] != "" || $_REQUEST['SUB'] == 'NEWSHIPPING METHOD'){
	if (isset($_REQUEST["smid"])) {
		$select = 	"SELECT * FROM ecommerce_shipping_methods ".
					"WHERE ".
					"shipping_method_id='".$_REQUEST["smid"]."'";
		$res = doQuery($select);
		$_POST = mysql_fetch_array($res);		

		
		$button = "Update Shipping Method";
		$doing = "Shipping Method: ".$_POST['name']."";
	} else {
	
		$button = "Add Shipping Method";
		$doing = "New Shipping Method";
	}	
	
	// START FORM
	startAdminForm();
	// START TABLE
	echo tableHeader($doing,2,'100%');
	
	//NAME
	adminFormField("*Name","name",$_POST['name'],"textbox");
	//DESCRIPTION
	adminFormField("Description","description",$_POST['description'],"textarea");
	//CODE
	adminFormField("Code","code",$_POST['code'],"textbox");
	
	//TYPE
	adminFormField("Provider","provider",$_POST['provider'],"select",Array('USPS','FEDEX','UPS'));
	
	//FLAT PRICE
	adminFormField("Flat Price","flat_price",$_POST['flat_price'],"textbox");
	// END FORM	
	$xid = "smid";
	$identifier = "SHIPPING_METHOD";
	endAdminForm($button,$xid,$identifier);
	
}

/*** PRODUCT ATTRIBUTE FORM			********************************************************/
elseif($_GET['aid'] != "" || $_REQUEST['SUB'] == "NEWPRODUCT ATTRIBUTE"){

	$name				= "Product Attribute";
	$xid 				= "aid";
	$table				= "ecommerce_product_attributes";
	$id_column			= "attribute_id";
	$fieldLabelArray 	= array("*Name",		"Type",																			"Description",	"*Label",	"Link to Category");
	$fieldTypeArray 	= array("Textbox",		"Select",																		"Textarea", 	"Textbox",	"Select");
	$fieldOptionsArray	= array("",				Array('Textbox','Textarea','Select','Checkbox','Checkbox Group','Radio Group'),	"", 			"",			"hierarchy::ecommerce_product_categories");
	$fieldRowArray		= array("name",			"type",																			"description", "label",		"category_id");
	administrativeForm($name,$xid,$table,$id_column,$fieldLabelArray,$fieldTypeArray,$fieldRowArray,$fieldOptionsArray,$back);
	
	//
	// ATTRIBUTE VALUES
	//
	if($_GET['aid'] != ""){
		$name				= "Attribute Options";
		$table				= "ecommerce_product_attribute_values";
		$titleColumnArray	= Array("Id","Name","Image");
		$valueColumnArray	= Array("attribute_value_id","name","image");
		$xid				= "aoid";
		$parentItemId 		= "aid";
		$parentItemId2		= "attribute_id";
		sortableTable($name,$table,$titleColumnArray,$valueColumnArray,$xid,$parentItemId,$parentItemId2);
	}
	
}

/*** PRODUCT ATTRIBUTE OPTION FORM	********************************************************/
elseif($_GET['aoid'] != "" || $_REQUEST['SUB'] == "NEWATTRIBUTE OPTION"){
	// ADD/EDIT CHECK
	if (isset($_REQUEST["aoid"])) {
		$select = 	"SELECT * FROM ecommerce_product_attribute_values ".
					"WHERE ".
					"attribute_value_id='".$_REQUEST["aoid"]."'";
		$res = doQuery($select);
		$_POST = mysql_fetch_array($res);		
		//$_POST['name'] = form_encode($_POST['name']);	

		//GET TYPE OF PARENT ATTRIBUTE
		$select = "SELECT * FROM ecommerce_product_attributes WHERE attribute_id='".$_POST['attribute_id']."'";
		$result = doQuery($select);
		$row = mysql_fetch_array($result);
		$type = $row['type'];
		$attributename = $row['name'];
		
		$button = "Update Attribute Option";
		$doing = "Attribute Option ".$_POST['name']." for <a href='?VIEW=".$_REQUEST['VIEW']."&aid=".$_POST['attribute_id']."'>".$attributename."</a>";
	} else {
	
		//GET TYPE OF PARENT ATTRIBUTE
		$select = "SELECT * FROM ecommerce_product_attributes WHERE attribute_id='".$_REQUEST['parentItemId']."'";
		$result = doQuery($select);
		$row = mysql_fetch_array($result);
		$type = $row['type'];
		$attributename = $row['name'];
	
		$button = "Add Attribute Option";
		$doing = "New Attribute Option for <a href='?VIEW=".$_REQUEST['VIEW']."&aid=".$_REQUEST['parentItemId']."'>".$attributename."</a>";
	}	
	
	// START FORM
	startAdminForm();
	// START TABLE
	echo tableHeader($doing,2,'100%');
	// NAME
	adminFormField("*Name","name",$_POST['name'],"textbox");
	// VALUE
	//adminFormField("*Value","name",$_POST['name'],"textbox");
	// ATTRIBUTE	
	echo "<tr><th>Attribue</th><td>";
	echo "<select name='attribute_id'>";
	$attributes = getSqlSelectArray('ecommerce_product_attributes');
	while($attribute = mysql_fetch_array($attributes)){
		$selected = "";
		//if($_REQUEST['parentItemId'] == $attribute['attribute_id']){ $selected = " SELECTED "; }
		if(isset($_REQUEST['parentItemId'])){ $_POST['attribute_id'] = $_REQUEST['parentItemId']; }
		if($_POST['attribute_id'] == $attribute['attribute_id']){ $selected = " SELECTED "; }
		echo "<option ".$selected." value='".$attribute['attribute_id']."'>".$attribute['name']."</option>";
	}
	echo "</select>";
	echo "</td></tr>";
	
	echo "<tr><th>Price</th><td><input name='price' value='".$_POST['price']."'></td></tr>";
	
	//var_dump($row);
	
	if($_POST['default'] == '1'){ $defchecked = " CHECKED "; }
	echo "<tr><th>Default</th><td><input type='checkbox' value='1' name='default' ".$defchecked."></td></tr>";
	
	// IMAGE
	if($type != "Select"){
		echo "<tr><th>Image</th><td>";
		echo "<img style='float:left; margin-right:10px; margin-top:5px;' src='".$_SETTINGS['website']."uploads/".$_POST['image']."'><br>";
		echo "<input type='text' name='image' value=".$_POST['image']." /><button type='button' onClick=\"SmallFileBrowser('../uploads/','image')\">Choose Image...</button><br><br>	";
		echo "</td></tr>";
	}
	
	// END FORM	
	$xid = "aoid";
	$identifier = "ATTRIBUTEOPTION";
	endAdminForm($button,$xid,$identifier);
}

/*** COUPON FORM		********************************************************/
elseif($_REQUEST['SUB'] == "NEWCOUPON CODE" || $_REQUEST['cuid'] != ""){
	if($_REQUEST['cuid'] != ""){
		$select = 	"SELECT * FROM ecommerce_coupon_codes ".
					"WHERE ".
					"coupon_id='".$_REQUEST['cuid']."' LIMIT 1";
		$res = doQuery($select);
		$_POST = mysql_fetch_array($res);	
	
		$button = "Update Coupon Code";
		$doing = "Coupon Code: ".$_POST['name']."";
	} else {
		$button = "Add Coupon Code";
		$doing = "New Coupon Code:";
	}
	
	// START FORM
	startAdminForm();
	// START TABLE
	echo tableHeader($doing,2,'100%');
	
	// NAME
	adminFormField("*Name","name",$_POST['name'],"textbox");
	
	// CODE
	adminFormField("Code","code",$_POST['code'],"textbox",false,"The coupon code that customers are required to enter in order to receive the discount. Or this can be the starting integer to for a numerical range of codes to be accepted.");
	adminFormField("Code Range End","code_range_end",$_POST['code_range_end'],"textbox",false,"Entering a start range and an end range will allow a numerical range of codes to be accepted.");
	adminFormField("Code Range Prefix","code_range_prefix",$_POST['code_range_prefix'],"textbox",false,"Enter a prefix for promo code ranges. Must be a single word and a dash, for example: sb- or md-.");
	
	echo "<tr id='multiple_codes_row'>";
	echo "<th>";
	echo info();
	echo "Multiple Codes";
	echo "</th><td><textarea name='multiple_codes' id='multiple_codes' style='float:left; margin-right:10px;'>".$_POST['multiple_codes']."</textarea>";
	echo "<div style='float:left;'>";
	$codes = trim($_POST['multiple_codes']); 
	$codesArray = explode("\n", $codes);	
	$codesArray = array_filter($codesArray, 'trim');
 
	$codesArray = array_chunk($codesArray,10);
 
	//loop through the lines
	foreach($codesArray as $codeArray){
		echo "<div style='float:left; margin-right:10px'>";
		foreach($codeArray as $code){
			$nm = 0;
			$st = "SELECT promo_code FROM ecommerce_orders WHERE promo_code='".$code."' LIMIT 1";
			$rt = doQuery($st);
			$nm = mysql_num_rows($rt);
			if($nu > 0){
				echo "<span style='color:red; display:block; margin:0px 3px 3px 3px;'>".$code."</span>";
			} else {
				echo "<span style='color:black; display:block; margin:0px 3px 3px 3px;'>".$code."</span>";
			}		
		}
		echo "</div>";
	}
	
	echo "</div>";
	echo "</td></tr>";
	
	// STATUS
	if($_POST['status'] == "Active"){ $aselected = " SELECTED "; }
	if($_POST['status'] == "Inactive"){ $iselected = " SELECTED "; }
	
	echo "<tr><th>";
	echo info('Select a free product to be included with this promo code.');
	echo "Free Product</th>";
	echo "<td>";
	echo "<select name='free_promo_product_id'>";
	echo "	<option value=''> - Select a Free Product - </option>";
	
	// FREE PRODUCT - GET ALL PRODUCTS
	$selectProd = "SELECT * FROM ecommerce_products WHERE status='Published' AND active='1' ORDER BY name ASC";
	$resultProd = doQuery($selectProd);
	$num = mysql_num_rows($resultProd);
	$j = 0;
	while($j<$num){
		$product = mysql_fetch_array($resultProd);
		echo "<option value='".$product['product_id']."' ".selected($_POST['free_promo_product_id'],$product['product_id']).">".$product['name']."</option>";
		$j++;
	}
	echo "";
	echo "</select>";
	echo "</td></tr>";	
	
	echo "<tr><th>";
	echo info('Select a product that the customer must have in their cart for the free product to work.');
	echo "Flag Product</th>";
	echo "<td>";
	echo "<select name='free_promo_flag_product_id'>";
	echo "	<option value=''> - Select the Flag Product - </option>";
	
	// TRIGGER PRODUCT - GET ALL PRODUCTS
	$selectProd = "SELECT * FROM ecommerce_products WHERE status='Published' AND active='1' ORDER BY name ASC";
	$resultProd = doQuery($selectProd);
	$num = mysql_num_rows($resultProd);
	$j = 0;
	while($j<$num){
		$product = mysql_fetch_array($resultProd);
		echo "<option value='".$product['product_id']."' ".selected($_POST['free_promo_flag_product_id'],$product['product_id']).">".$product['name']."</option>";
		$j++;
	}
	echo "";
	echo "</select>";
	echo "</td></tr>";

	// TEXT MATCH
	echo "
		<tr>
		<th>";
	echo info('Enter a string of text that must be in the name of at least one of the products in the customers cart.');
	echo " Flag Text Match</th>
		<td><input type='text' name='flag_text_match' id='flag_text_match' value='".$_POST['flag_text_match']."'></td>
		</tr>
	";
	
	echo "		<tr>
			<th>";
	echo 	info('Checking flag max discount will limit the discount to the lowest flag product or the flag match products price. If the flag products price is greater than the overall max discount then the discount will be limited to the overall max discount.');
	if($_POST['flag_max_discount'] == '1'){ $flag_selected = " CHECKED "; }
	echo "	Flag Max Discount</th>
			<td><input type='checkbox' name='flag_max_discount' id='flag_max_discount' value='1' ".$flag_selected."></td>	
			</tr>";
			
	echo "		<tr>
				<th>";
	echo				info('Marking the coupon/promo code to public will display the code and description on the "How do I get these" page.');
	echo "				 Public
				</th>
				<td>
					<input type='checkbox' name='public' id='public' value='1' ".isChecked($_POST['public'],"1")." />
				</td>
			</tr>";
	
	echo "		<tr><th>";
	echo 		info('A description of the promotion or purpose of this code.');
	echo "		Description</th>
			<td>
			<textarea type='textbox' name='description'>".$_POST['description']."</textarea>
			</td>
			</tr>";
	
	
	// STATUS
	echo "
		<tr>
		<th>*Status</th>
		<td>
		<select name='status'>
			<option value='Active' ".$aselected.">Active</option>
			<option value='Inactive' ".$iselected.">Inctive</option>
		</select>
		</td>
		</tr>	
	";
		
	
	echo "
		<tr>
		<Th>*Valid Cutsomers</th>
		<td>
		<select name='authorized_customers[]' multiple='multiple' size='3'>
	";
	
	$select = "SELECT * FROM user_permission WHERE active='1'";
	$result = doQuery($select);
	while($ro = mysql_fetch_array($result)){
		$selected = "";
		$select = "SELECT * FROM ecommerce_coupon_permission_relational WHERE coupon_id='".$_REQUEST['cuid']."' AND permission_id='".$ro['permission_id']."' LIMIT 1";
		$num = mysql_num_rows(doQuery($select));
		if($num){ $selected = " SELECTED "; }
		echo "<option ".$selected." value='".$ro['permission_id']."'>".$ro['name']."</option>";
	}
		
	echo "
		</select>
		</td>
		</tr>
	";
	
	// FLAT DISCOUNT
	adminFormField("Flat Discount","flat_discount",$_POST['flat_discount'],"currency",false,"A flat discount will deduct a flat dollar amout from the customer\'s subtotal.");
	
	// PERCENT DISCOUNT
	adminFormField("Rate Discount","percent_discount",$_POST['percent_discount'],"decimal",false,"A rate discount will deduct a percentage from the customer\'s subtotal.");
	
	// CART MINIMUM
	adminFormField("Overall Cart Min","min_subtotal",$_POST['min_subtotal'],"currency",false,"A cart minimum requires the customer\'s subtotal be greater than or equal to the cart minimum.");
	
	// MAX FLAT DISCOUNT
	adminFormField("Overall Max Discount","max_flat_discount",$_POST['max_flat_discount'],"currency",false,"A maximum discount places a ceiling on the amount discounted from the customer\'s subtotal.");
		
	// MAX USES
	if($_REQUEST['cuid'] != "" AND $_POST['max_qty'] != ""){
		//adminFormField("Max Uses","max_qty",$_POST['max_qty'],"textbox");
		$select = "SELECT * FROM ecommerce_orders WHERE promo_code_id='".$_REQUEST['cuid']."' AND (status='New' OR status='Open' OR status='Shipped') AND active='1'";
		$result = doQuery($select); 
		$num = mysql_num_rows($result);
		echo "<tr><th>Times Used</th><td>".$num."</td></tr>";
	}
	
	echo "	<tr id='max_qty_row'>
			<th>Max Uses</th>
			<td>
			<input name='max_qty' id='max_qty' value='".$_POST['max_qty']."'>
			</td>
			</tr>
		";
		
	
		
	// START DATE
	adminFormField("*Start Date","start_date",$_POST['start_date'],"date",false,"The date the coupon code will become valid.");
	
	// EXPIRATION DATE
	adminFormField("*Expiration Date","expiration_date",$_POST['expiration_date'],"date",false,"The date the coupon code will expire.");

	// CATEGORIES
	/*
	echo "<TR BGCOLOR='#f2f2f2'>";
	echo "	<Th width='200' height='40' style='padding-left:20px;'>Valid Categories / Not Valid Categories</Th>";
	echo "	<TD><table><tr><th>Valid Categories</th><th>Not Valid Categories</th></tr><tr><td>";
				hierarchymultiselectTable('ecommerce_product_categories','categories[]','category_id','name','sort_level','ASC',0, 'ecommerce_coupon_category_relational','coupon_id',''.$_REQUEST['cuid'].'');
	echo "		<script>";
	echo "			//$('#categories').multiSelect();";
	echo "		</script>";
	echo "	</td><td>";
				hierarchymultiselectTable('ecommerce_product_categories','not_valid_categories[]','category_id','name','sort_level','ASC',0, 'ecommerce_coupon_not_valid_category_relational','coupon_id',''.$_REQUEST['cuid'].'');
	echo "		<script>";
	echo "			//$('#categories').multiSelect();";
	echo "		</script>";
	echo "	</td></tr></table></TD>";
	
	echo "</TR>";	
	*/
	
	echo "<tr><th>";
	//echo info('  If a free product is selected these selections are ignored.');
	echo "Valid Products / Not Valid Products</th>"	;
	echo "<td><table><tr><th>";
	echo info('Selecting valid products will restrict the promo codes validity to those selected products. If an item in the cart does not match the valid products, then the code will be invalid.');
	echo "Valid Products</th><th>";
	echo info('Selecting not valid products will restrict the codes valididty to all not selected products. If an item in the cart matches a not valid product, then the code will be invalid.');
	echo "Not Valid Products</th></tr><tr><td>";
	
	echo "<select name='valid_products[]' multiple='multiple' size='20'>";
	echo "	<option value=''> - Valid Products - </option>";
	
	// GET SELECTED PRODUCTS
	// $selectedProd = "SELECT * FROM ecommerce_coupon_valid_product_relational WHERE coupon_id='".$_REQUEST['cuid']."'";
	// $resultedProd = doQuery($selectedProd);
	
	// GET ALL PRODUCTS
	$selectProd = "SELECT * FROM ecommerce_products WHERE status='Published' AND active='1' ORDER BY name ASC";
	$resultProd = doQuery($selectProd);
	$num = mysql_num_rows($resultProd);
	$j = 0;
	while($j<$num){
		$product = mysql_fetch_array($resultProd);
		$selectProdMatch = "SELECT * FROM ecommerce_coupon_valid_product_relational WHERE product_id='".$product['product_id']."' AND coupon_id='".$_REQUEST['cuid']."' LIMIT 1";
		$resultProdMatch = doQuery($selectProdMatch);
		$resultNum = mysql_num_rows($resultProdMatch);
		$selected = "";
		if($resultNum > 0){ $selected = " SELECTED=SELECTED "; }
		//$rowProdMatch = mysql_fetch_array($resultProdMatch);
		echo "<option value='".$product['product_id']."' ".$selected.">".$product['name']."</option>";
		$j++;
	}
	echo "";
	echo "</select>";
	
	echo "	</td><td>";
	
	echo "<select name='not_valid_products[]' multiple='multiple' size='20'>";
	echo "	<option value=''> - Exclude Products - </option>";
	$selectProd = "SELECT * FROM ecommerce_products WHERE status='Published' AND active='1' ORDER BY name ASC";
	$resultProd = doQuery($selectProd);
	$num = mysql_num_rows($resultProd);
	$j = 0;
	while($j<$num){	
		$product = mysql_fetch_array($resultProd);
		$selectProdMatch = "SELECT * FROM ecommerce_coupon_not_valid_product_relational WHERE product_id='".$product['product_id']."' AND coupon_id='".$_REQUEST['cuid']."' LIMIT 1";
		$resultProdMatch = doQuery($selectProdMatch);
		$rowProdMatch = mysql_fetch_array($resultProdMatch);
		echo "<option value='".$product['product_id']."' ".selected($product['product_id'],$rowProdMatch['product_id']).">".$product['name']."</option>";
		$j++;
	}
	echo "";
	echo "</select>";
	
	echo "	</td></tr></table></TD>";
	
	echo "</tr>";
	
	echo 	"	<script>
	
				// IF THERE IS A CODE RANGE END THEN HIDE MAX USES, HIDE MULTIPLE CODES SHOW CODE PREFIX
				if($('#code_range_end').val() != ''){
					$('#max_qty_row').hide(); $('#max_qty').val('');
					$('#multiple_codes_row').hide(); $('#multiple_codes').val('');
					$('#code_range_prefix_row').show();
				}
				// ELSE IF THERE ISNT A CODE RANGE END THEN HIDE PREFIX
				// PREFIX ONLY REQUIRED WITH A RANGE
				//else {
				//	$('#code_range_prefix_row').hide(); $('#code_range_prefix').val('');					
				//}
				
				// IF THERE IS A MULTIPLE CODES THEN HIDE MAX USES, HIDE CODE, HIDE CODE RANGE END, HIDE PREFIX
				if($('#multiple_codes').val() != ''){
					$('#max_qty_row').hide(); $('#max_qty').val(''); 							// HIDE MAX USES
					$('#code_row').hide(); $('#code').val('');									// HIDE CODE					
					$('#code_range_end_row').hide(); $('#code_range_end_row').val('');				// HIDE CODE RANGE END
					$('#code_range_prefix_row').hide(); $('#code_range_prefix').val('');		// HIDE PREFIX
				}
				// ELSE IF THERE ISNT A CODE RANGE END THEN HIDE PREFIX
				// PREFIX ONLY REQUIRED WITH A RANGE
				//else {
				//	$('#code_range_prefix_row').hide(); $('#code_range_prefix').val('');					
				//}		

				
				$('#multiple_codes').keydown(function(event) {
					if($(this).val() != ''){
						$('#max_qty_row').fadeOut(); $('#max_qty').val(''); 							// HIDE MAX USES
						$('#code_row').fadeOut(); $('#code').val('');									// HIDE CODE					
						$('#code_range_end_row').fadeOut(); $('#code_range_end_row').val('');				// HIDE CODE RANGE END
						$('#code_range_prefix_row').fadeOut(); $('#code_range_prefix').val('');			// HIDE PREFIX
					} else {
						$('#max_qty_row').fadIn(); 							
						$('#code_row').fadIn(); 										
						$('#code_range_end_row').fadIn(); 				
						$('#code_range_prefix_row').fadIn();
					}				
				});
	
	
				$('#code_range_end').keydown(function(event) {
					//alert(event.keyCode);
					// IF THERE IS A CODE RANGE HIDE MAX USES
					if($(this).val() != ''){
						$('#max_qty_row').fadeOut(); $('#max_qty').val('');
						$('#multiple_codes_row').fadeOut(); $('#multiple_codes').val('');
						$('#code_range_prefix_row').fadeIn();
					} else {
						$('#max_qty_row').fadeIn();
						$('#multiple_codes_row').fadeIn();
						$('#code_range_prefix_row').fadeOut(); $('#code_range_prefix').val('');
					}
				});
				
				$('#code_range_end').blur(function(event) {
					if($(this).val() != ''){
						$('#max_qty_row').fadeOut(); $('#max_qty').val('');
						$('#multiple_codes_row').fadeOut(); $('#multiple_codes').val('');
						$('#code_range_prefix_row').fadeIn();
					} else {
						$('#max_qty_row').fadeIn();
						$('#multiple_codes_row').fadeIn();
						$('#code_range_prefix_row').fadeOut(); $('#code_range_prefix').val('');
					}
				});
	
				</script>";
	
		
	// END FORM	
	$xid = "cuid";
	$identifier = "COUPON_CODE";
	endAdminForm($button,$xid,$identifier);	
	
}

/*** REVIEW FORM		********************************************************/
elseif($_REQUEST['SUB'] == "NEWREVIEW" || $_REQUEST['rid'] != ""){
	if($_REQUEST['rid'] != ""){
		$select = 	"SELECT * FROM ecommerce_product_comments ".
					"WHERE ".
					"comment_id='".$_REQUEST['rid']."' LIMIT 1";
		$res = doQuery($select);
		$_POST = mysql_fetch_array($res);	
	
		$button = "Update Review";
		$doing = "Review: ".$_POST['name']."";
	} else {
		$button = "Add Review";
		$doing = "New Review:";
	}
	
	// START FORM
	startAdminForm();
	// START TABLE
	echo tableHeader($doing,2,'100%');

	// Status
	echo "
		<tr>
		<th>*Status</th>
		<td>
		<select name='status'>
			<option value='Draft' ".selected($_POST['status'],'Draft').">Draft</option>
			<option value='Pending' ".selected($_POST['status'],'Pending').">Pending</option>
			<option value='Published' ".selected($_POST['status'],'Published').">Published</option>
		</select>
		</td>
		</tr>	
	";	
	
	// NAME
	adminFormField("*Submitted Name","name",$_POST['name'],"textbox");
	
	// EMAIL
	adminFormField("*Email","email",$_POST['email'],"textbox");
	
	// ACCOUNT

		echo "<tr><th>Account</th>";
		echo "<td>";
		echo "<select name='user_id'>";
		echo "<option value=''> - No Account Selected - </option>";
		$cselect = "SELECT account_id,name FROM user_account WHERE ACTIVE='1'";
		$cresult = doQuery($cselect);
		while($crow = mysql_fetch_array($cresult)){
			echo "<option value='".$crow['account_id']."' ".selected($_POST['user_id'],$crow['account_id'])." >".$crow['name']."</option>";
		}
		echo "</select>";
		echo "</td></tr>";
	
	// PRODUCT

		echo "<tr><th>Product</th>";
		echo "<td>";
		echo "<select name='product_id'>";
		$cselect = "SELECT product_id,name FROM ecommerce_products WHERE ACTIVE='1'";
		$cresult = doQuery($cselect);
		while($crow = mysql_fetch_array($cresult)){
			echo "<option value='".$crow['product_id']."' ".selected($_POST['product_id'],$crow['product_id'])." >".$crow['name']."</option>";
		}
		echo "</select>";
		echo "</td></tr>";
		
	// Rating

		echo "<tr><th>Rating</th>";
		echo "<td>";
		echo "<span class='ratings'>
				<input name='rating' type='radio' value='1' ".ischecked($_POST['rating'],'1')." class='starform' />
				<input name='rating' type='radio' value='2' ".ischecked($_POST['rating'],'2')." class='starform' />
				<input name='rating' type='radio' value='3' ".ischecked($_POST['rating'],'3')." class='starform' />
				<input name='rating' type='radio' value='4' ".ischecked($_POST['rating'],'4')." class='starform' />
				<input name='rating' type='radio' value='5' ".ischecked($_POST['rating'],'5')." class='starform' />
		</span>
		<script>
			$('.starform').rating();
		</script>";
		echo "</td></tr>";
		
	// Date
	echo "<tr><th>Bought</th><td>";
	echo "<input type='radio' value='1' ".ischecked($_POST['bought'],'1')." name='bought'> Yes &nbsp; &nbsp; &nbsp; <input type='radio' value='0' ".ischecked($_POST['bought'],'0')." name='bought'> No";
	echo "</td></tr>";
	
	// Date
	adminFormField("*Date","created",$_POST['created'],"date");
	
	// Date
	adminFormField("*Content","content",$_POST['content'],"textarea");
	
	
	// END FORM	
	$xid = "rid";
	$identifier = "REVIEW";
	endAdminForm($button,$xid,$identifier);	
	
	
}

/*** SEARCH/VIEW SALES TAX		********************************************************/
elseif($_REQUEST['SUB'] == "SALESTAX"){	
	$name	= "Sales Tax";
	$button = "Update Sales Taxes";
	?>	
	<form name="wesform" id="wesform" method="POST" action="<?=$_SERVER["PHP_SELF"]?>?VIEW=<?=$_GET["VIEW"]?>&ADDNEW=1&<?=SID?>" >	
	<?
	echo tableHeader("Sales Tax",10,'100%');
	
	echo "<Tr><td colspan='10'><p><b>IMPORTANT:</b> Enter values as rates. For example, \"0.0925\". </p></td></tr>";
		
		$select = "SELECT * FROM state WHERE active='1'";
		$result = doQuery($select);
		$num = mysql_num_rows($result);
		$i = 0;
		$j = 0;
		while($i<$num){
			$row = mysql_fetch_array($result);
			if($j == 0){ echo "<tr>";}
			?>
			<Th><?=$row['state']?></Th>
			<TD>
			<INPUT TYPE=TEXT NAME="state-<?=$row['state_id']?>" VALUE="<?=$row['tax']?>" size='5'>		
			</TD>
			<?
			$j++;
			if($j == 5){ echo "</tr>"; $j = 0;}
			$i++;
		}
		?>
	</td></tr></table>	
	<div id="submit">
	<?
	echo "<input type='hidden' name='VIEW' value='".$_REQUEST['VIEW']."'>";
	echo "<input type='hidden' name='SUB' value='".$_REQUEST['SUB']."'>";	
	echo "<INPUT TYPE=SUBMIT NAME=\"".strtoupper(str_replace(" ", "_", $button))."\" VALUE=\"$button\">";
	//echo "<INPUT TYPE=HIDDEN NAME='xid' VALUE='".$_REQUEST["xid"]."'>";
	//echo "<a style=\"margin:0px 10px;\" target=\"_blank\" href=\"".$_SETTINGS['website'].$_POST['clean_url_name']."\">Go To Page</a> &nbsp;&nbsp;";
	//echo "<INPUT TYPE=SUBMIT NAME=DELETE value=\"Delete\" onclick=\"return confirm('Are You Sure?');\">";
	//echo "<INPUT TYPE=SUBMIT NAME=\"PREVIEW_PAGE\" VALUE=\"Preview\">";
	?>
	</div>	
	</form>
	<?
}

/**
 *
 *
 * SEARCH TABLES AND SORTING
 *
 *
 */
 
/*** SEARCH/VIEW CATEGORIES/ORGANIZATION	********************************************************/
elseif($_REQUEST['SUB'] == "CATEGORIES"){
		
	// SORTABLE 
	echo "
	<div class='textcontent1'>
		<h1>Categories</h1>
		<a class='admin-new-button' href='index.php?VIEW=".$_REQUEST['VIEW']."&SUB=NEWCATEGORY' >New Category</a>
	</div>
	<br />
	<br />
	";
	
	// HEADER
	echo tableHeaderid("Categories",6,"100%","list");
	echo "<thead><TR><th width='300px'>Categories</th><th>Action</th></TR></thead><tbody>";
	echo "</tbody></table>";
	
	$ulstyle = " style='float:right; margin-right:60%;' ";
	
	echo "<ul class=\"resultslist connectedSortable\" id=\"sortable\">";	
		
	// GET CATEGORIES FIRST LEVEL
	$select = 	"SELECT category_id,name FROM ecommerce_product_categories ".
				"WHERE ".
				"active='1' AND (parent_id='' OR parent_id=0) ORDER BY sort_level ASC".
				"".$_SETTINGS['demosqland']."";
	$res = doQuery($select);
	$num = mysql_num_rows($res);
	$i=0;
	while ($row = mysql_fetch_array($res)){
		// FIRST LEVEL CATEGORY
		if($i % 2) { $class = "odd"; } else { $class = "even"; }		
		echo "<li class=\"".$class." selector\" id=\"".$row['category_id']."\"> <span class=\"cat1\"></span> <span>{$row["name"]}</span>";
			echo "<FORM ".$ulstyle." class=\"listform\" METHOD=GET ACTION=\"$_SERVER[PHP_SELF]\">";
			echo "<INPUT TYPE=HIDDEN NAME=cid VALUE=\"{$row["category_id"]}\">";
			echo "<INPUT TYPE=HIDDEN NAME=VIEW VALUE=\"{$_GET["VIEW"]}\">";
			echo "<INPUT TYPE=HIDDEN NAME=SUB VALUE=\"{$_GET["SUB"]}\">";
			echo "<INPUT TYPE=SUBMIT NAME=DELETE_CATEGORY VALUE=\"Delete\" onClick=\"return confirm('Are You Sure?');\">";
			echo " <INPUT TYPE=SUBMIT NAME=view VALUE=\"Open\">";
			echo "</FORM>";
			
		
			// SECOND LEVEL CATEGROY			
			echo "<ul class=\"resultslist connectedSortable\" id=\"sortable1".$i."\">";	
				$select1 = "SELECT category_id,name FROM ecommerce_product_categories WHERE active='1' AND parent_id='".$row['category_id']."' ORDER BY sort_level ASC";
				$result1 = doQuery($select1);
				$num1 = mysql_num_rows($result1);
				$i1 = 0;
				while($row1 = mysql_fetch_array($result1)){
					echo "<li class=\"".$class." selector\" id=\"".$row1['category_id']."\"> <span class=\"cat2\"></span> <span>{$row1["name"]}</span>";
						// SECOND LEVEL FORM
						echo "<FORM ".$ulstyle." class=\"listform\" METHOD=GET ACTION=\"$_SERVER[PHP_SELF]\">";
						echo "<INPUT TYPE=HIDDEN NAME=cid VALUE=\"{$row1["category_id"]}\">";
						echo "<INPUT TYPE=HIDDEN NAME=VIEW VALUE=\"{$_GET["VIEW"]}\">";
						echo "<INPUT TYPE=HIDDEN NAME=SUB VALUE=\"{$_GET["SUB"]}\">";
						echo "<INPUT TYPE=SUBMIT NAME=DELETE_CATEGORY VALUE=\"Delete\" onClick=\"return confirm('Are You Sure?');\">";
						echo "<INPUT TYPE=SUBMIT NAME=view VALUE=\"Open\">";
						echo "</FORM>";
						
						
						
						// THIRD LEVEL CATEGORY
						echo "<ul class=\"resultslist connectedSortable\" id=\"sortable2".$i.$i1."\">";
						$select2 = "SELECT category_id,name FROM ecommerce_product_categories WHERE active='1' AND parent_id='".$row1['category_id']."' ORDER BY sort_level ASC";
						$result2 = doQuery($select2);
						$num1 = mysql_num_rows($result2);
						$i2 = 0;
						while($row2 = mysql_fetch_array($result2)){
															
								// CATEGORY
								echo "<li class=\"".$class." selector\" id=\"".$row2['category_id']."\"> <span class=\"cat3\"></span> <span>{$row2["name"]}</span>";
								
								// THIRD LEVEL FORM
								echo "<FORM ".$ulstyle." class=\"listform\" METHOD=GET ACTION=\"$_SERVER[PHP_SELF]\">";
								echo "<INPUT TYPE=HIDDEN NAME=cid VALUE=\"{$row2["category_id"]}\">";
								echo "<INPUT TYPE=HIDDEN NAME=VIEW VALUE=\"{$_GET["VIEW"]}\">";
								echo "<INPUT TYPE=HIDDEN NAME=SUB VALUE=\"{$_GET["SUB"]}\">";
								echo "<INPUT TYPE=SUBMIT NAME=DELETE_CATEGORY VALUE=\"Delete\" onClick=\"return confirm('Are You Sure?');\">";
								echo "<INPUT TYPE=SUBMIT NAME=view VALUE=\"Open\">";
								echo "</FORM>";
								
								
							echo "</li>"; // END THIRD LEVEL CATEGORY
							$i2++;
						}
						echo "</ul>"; // END THIRD LEVEL CATEGORY LIST
						?>
						<script>						
							$("#sortable2<?=$i.$i1 ?>").sortable({
								//connectWith: ".connectedSortable"
							});					
							// AJAX REQUEST SORT THIRD LEVEL
							$( "#sortable2<?=$i.$i1 ?>" ).bind( "sortstop", function(event, ui) {
							  var result = $('#sortable2<?=$i.$i1 ?>').sortable('toArray');
							  var resultstring = result.toString();
							  $.ajax({
								  type: 'POST',
								  url: 'modules/ecommerce/ecommerce.php',
								  data: { sortarray: resultstring, SORT_CATEGORIES: '1', LEVEL: 'cat3' }
								});
							});
						</script>
						<?						
					echo "</li>"; // END SECOND LEVEL CATEGORY					
					$i1++;
				}				
			echo "</ul>"; // END SECOND LEVEL CATEGORY LIST
			?>
			<script>
			$("#sortable1<?=$i ?>").sortable({
				//connectWith: ".connectedSortable"
			});
			$( "#sortable1<?=$i ?>" ).bind( "sortstop", function(event, ui) {
			  var result = $('#sortable1<?=$i ?>').sortable('toArray');
			  var resultstring = result.toString();
			  $.ajax({
				  type: 'POST',
				  url: 'modules/ecommerce/ecommerce.php',
				  data: { sortarray: resultstring, SORT_CATEGORIES: '1', LEVEL: 'cat2' }
				});
			});
			</script>
			<?
		echo "</li>"; // END FIRST LEVEL CATEGORY
		$i++;
	}
	echo "</ul>"; // END FIRST LEVEL CATEGORY LIST
	?>
	<script>
		// CHANGE CAT COlOR
		$("#sortabletop").sortable({
			//connectWith: ".connectedSortable"
		});
		$( "#sortable" ).bind( "sortstart", function(event, ui) {
			$(ui.item).css("background-color","#f3f8ff"); $(ui.item).css("border","2px solid #89a8d8");	$(ui.item).css("cursor","-moz-grabbing");
		});
		// AJAX SORT FIRST LEVEL CATEGORIES
		$( "#sortable" ).bind( "sortstop", function(event, ui) {
		  var result = $('#sortable').sortable('toArray');
		  var resultstring = result.toString();
		  $.ajax({
			  type: 'POST',
			  url: 'modules/ecommerce/ecommerce.php',
			  data: { sortarray: resultstring, SORT_CATEGORIES: '1', LEVEL: 'cat1' }
			});
			// CHANGE CAT COLOR
			$(ui.item).css("background-color","#f5f5f5"); $(ui.item).css("border-top","1px solid #eeeeee"); $(ui.item).css("border-right","1px solid #eeeeee");
			$(ui.item).css("border-bottom","0px solid #eeeeee"); $(ui.item).css("border-left","0px solid #eeeeee");	$(ui.item).css("cursor","-moz-grab");
		});	
		
	</script>
	
	<div class="pagination">&nbsp;</div>
	<?
	
}

/*** SEARCH/VIEW ATTRIBUTES				********************************************************/
elseif($_REQUEST['SUB'] == "ATTRIBUTES"){
	
	$name				= "Product Attributes";
	$table				= "ecommerce_product_attributes";
	$orderByString		= "";
	$searchColumnArray	= Array("attribute_id",		"name",		"description",		"type");
	$titleColumnArray	= Array("Id",				"Type",		"Name",	"Label",	"Description");
	$valueColumnArray	= Array("attribute_id",		"type",		"name",	"label",	"description");
	$xid				= "aid";
	$ajaxURL			= "modules/ecommerce/ecommerce_ajax.php";
	basicSearchListingTable($name,$table,$orderByString,$searchColumnArray,$titleColumnArray,$valueColumnArray,$xid,$ajaxURL);
	
}

/*** SEARCH/VIEW SHIPPING METHODS		********************************************************/
elseif($_REQUEST['SUB'] == "SHIPPINGMETHODS"){
	
	$name				= "Shipping Methods";
	$table				= "ecommerce_shipping_methods";
	$orderByString		= "";
	$searchColumnArray	= Array("shipping_method_id",		"name");
	$titleColumnArray	= Array("Id",						"Name",				"Description");
	$valueColumnArray	= Array("shipping_method_id",		"name",				"description");
	$xid				= "smid";
	basicSearchListingTable($name,$table,$orderByString,$searchColumnArray,$titleColumnArray,$valueColumnArray,$xid);
	
}

/*** SEARCH/VIEW COUPONS				********************************************************/
elseif($_REQUEST['SUB'] == "COUPONS"){
	
	$name				= "Coupon Codes";
	$table				= "ecommerce_coupon_codes";
	$orderByString		= "";
	$searchColumnArray	= Array("coupon_id",		"name",	"code");
	$titleColumnArray	= Array("Id",				"Name",	"Code");
	$valueColumnArray	= Array("coupon_id",		"name",	"code");
	$xid				= "cuid";	
	$ajaxURL			= "modules/ecommerce/ecommerce_ajax.php";
	basicSearchListingTable($name,$table,$orderByString,$searchColumnArray,$titleColumnArray,$valueColumnArray,$xid,$ajaxURL);
	
}

/*** SEARCH/VIEW REVIEWS				********************************************************/
elseif($_REQUEST['SUB'] == "REVIEWS"){
	
	

	echo "<div class='textcontent'><h1>Product Reviews</h1>";
	
		echo "<FORM METHOD=GET>";
		echo "<INPUT TYPE=HIDDEN NAME=\"VIEW\" VALUE=\"".$_GET["VIEW"]."\">";
		echo "<INPUT TYPE=HIDDEN NAME=\"SUB\" VALUE=\"".$_GET["SUB"]."\">";
		echo "<INPUT TYPE=HIDDEN NAME=\"".session_name()."\" VALUE=\"".session_id()."\">";
		echo "<INPUT TYPE=TEXT NAME=\"KEYWORDS\" VALUE=\"{$_GET["KEYWORDS"]}\">";
		echo " <SELECT NAME=\"COLUMN\">";
			echo "<OPTION VALUE=\"name\"".selected($_GET["COLUMN"],"name").">Name</OPTION>";
			echo "<OPTION VALUE=\"status\"".selected($_GET["COLUMN"],"status").">Status</OPTION>";
		echo "</SELECT>";
		echo " <INPUT TYPE=SUBMIT NAME=search VALUE=\"Search\"> <INPUT TYPE=BUTTON NAME=NONE VALUE=\"Reset\" ONCLICK=\"document.location = '{$_SERVER["PHP_SELF"]}?VIEW={$_GET["VIEW"]}&".SID."';\">";
		echo "<input type='checkbox' ";
		if($_REQUEST['hidden'] == '1'){ echo " CHECKED "; }
		echo " name='hidden' value='1'> <small>Show Hidden</small>";
		echo "</FORM>";
		
	echo "</div><br /><br />";

	if ($_GET['KEYWORDS']!="") {
		$q = "AND {$_GET['COLUMN']} like '%{$_GET['KEYWORDS']}%'";
	}
	
	// SHOW HIDDEN
	if ($_GET['hidden']==''){
		$h = "AND hidden='0' "; 
	} else {
		$h = "";
	}
	
	$page = 1;
	$size = 15;	 
	
	$select = 	"SELECT * FROM ecommerce_product_comments WHERE ".
				"active='1' AND 1=1 ".
				"$q ".
				"$h ".
				"".$_SETTINGS['demosqland']." ".
				"ORDER BY comment_id DESC ";
				
	$total_records = mysql_num_rows(doQuery($select)); 
	 
	if (isset($_GET['page'])){
		$page = (int) $_GET['page'];
	}
	
	$pagination = new Pagination();
	$pagination->setLink("index.php?VIEW=".$_REQUEST['VIEW']."&SUB=".$_REQUEST['SUB']."&KEYWORDS=".$_REQUEST['KEYWORDS']."&COLUMN=".$_REQUEST['COLUMN']."&hidden=".$_REQUEST['hidden']."&page=%s");
	$pagination->setPage($page);
	$pagination->setSize($size);
	$pagination->setTotalRecords($total_records);
	 
	// now use this SQL statement to get records from your table
	
	$SQL = 	"SELECT * FROM ecommerce_product_comments WHERE ".
			"active='1' AND 1=1 ".
			"$q ".
			"$h ".
			"".$_SETTINGS['demosqland']." ".
			"ORDER BY comment_id DESC ".
			"".$pagination->getLimitSql()."";	

	echo tableHeaderid("Product Reviews",6,"100%","list");	
	echo "<thead><TR><th>Name</th><th>Email</th><th>Status</th><th>Review</th><th>Action</th></TR></thead><tbody>";	
	$res = doQuery($SQL);
	
	$i=0;	
	while ($row = mysql_fetch_array($res)) {
		if($i % 2) { $class = "odd"; } else { $class = "even"; }
		if($row['hidden'] == '1'){ $class .= " hiddenrow"; }
		echo "<TR class=\"$class\">";
		
		echo "<TD>".$row["name"]."</TD>";
		echo "<TD>".$row['email']."</TD>";
		echo "<TD>".$row['status']."</TD>";
		echo "<TD>".$row['content']."</TD>";
		
		echo "<TD width=\"150\" nowrap ALIGN=\"LEFT\">";
			echo "<FORM METHOD=GET ACTION=\"$_SERVER[PHP_SELF]\">";
			echo "<INPUT TYPE=HIDDEN NAME=rid VALUE=\"{$row["comment_id"]}\">";
			echo "<INPUT TYPE=HIDDEN NAME=VIEW VALUE=\"{$_GET["VIEW"]}\">";
			echo "<INPUT TYPE=SUBMIT NAME=DELETE_REVIEW VALUE=\"Delete\" onClick=\"return confirm('Are You Sure?');\"> <INPUT TYPE=SUBMIT NAME=view VALUE=\"Edit\">";
			echo "</FORM>";
		echo "</TD>";
		
		echo "</TR>";
		$i++;
	}
	echo "</tbody></TABLE>";
	$navigation = $pagination->create_links();
	echo $navigation; // will draw our page navigation
	
	
}

/*** SEARCH/VIEW AUTOMATED EMAILS		********************************************************/
elseif($_REQUEST['SUB'] == "AUTOMATEDEMAILS"){
	
	$name				= "Automated Emails";
	$table				= "automated_email_contents";
	$orderByString		= "";
	$searchColumnArray	= Array("email_id",		"subject");
	$titleColumnArray	= Array("Id",			"Subject",	"From");
	$valueColumnArray	= Array("email_id",		"subject",	"from");
	$xid				= "aeid";
	$ajaxURL			= "modules/ecommerce/ecommerce_ajax.php";
	basicSearchListingTable($name,$table,$orderByString,$searchColumnArray,$titleColumnArray,$valueColumnArray,$xid);
	
}

/*** SEARCH/VIEW PRODUCTS				********************************************************/
elseif($_REQUEST['SUB'] == "PRODUCTS"){
	
	
	$name				= "Products";
	$table				= "ecommerce_products";
	$orderByString		= "";
	$searchColumnArray	= Array("name", "product_id","product_number", "description");	
	$sortColumnArray	= Array("name", "product_id","product_number", "description","created");	
	$titleColumnArray	= Array("Id", "Image", "Product #", "Price","Status", "Category", "Name");	
	$valueColumnArray	= Array("product_id", "ecommerce_product_images", "product_number", "price","status", "ecommerce_product_category_relational", "name");
	
	$xid				= "pid";
	$ajaxURL			= "modules/ecommerce/ecommerce_ajax.php";
	//$Join					= "ecommerce_product_category_relational";
	//$On					= "product_id";
	//$orderByString		= "ORDER BY t2.category_id";
	
	function productSearchListingTable($name,$table,$orderByString,$searchColumnArray,$titleColumnArray,$valueColumnArray,$xid,$ajaxURL="",$Join="",$On="",$readonly=False,$addnew=True,$sortColumnArray)
	{

		global $_GET;
		global $_REQUEST;
		global $_SETTINGS;

		$num = mysql_num_rows(doQuery("SELECT * FROM ".$table." WHERE active='1'"));
		
		/*** Search box ***/
		echo "<div class='textcontent'>";
		echo "	<h1>".$name." (".$num.")</h1>";
		
		if($addnew == True){
			echo "  <a class='admin-new-button' href='index.php?VIEW=".$_REQUEST['VIEW']."&SUB=NEW".strtoupper(rtrim($name,"s"))."' >New ".rtrim($name,"s")."</a>";
		}
		
			echo "<FORM METHOD=GET>";
			echo "<INPUT TYPE=HIDDEN NAME=\"VIEW\" VALUE=\"".$_GET["VIEW"]."\">";
			echo "<INPUT TYPE=HIDDEN NAME=\"SUB\" VALUE=\"".$_GET["SUB"]."\">";
			echo "<small>Search For Keywords: </small> <INPUT TYPE=TEXT NAME=\"KEYWORDS\" VALUE=\"".$_GET["KEYWORDS"]."\"> &nbsp; ";
			echo " <small> in </small> <SELECT NAME=\"COLUMN\">";				
				foreach($searchColumnArray as $column){
					echo "<OPTION VALUE=\"t1.".$column."\" ".selected($_REQUEST['COLUMN'],"t1.".$column."").">".str_replace("_"," ",ucfirst($column))."</OPTION>";			
				}
			echo "</select>";
			echo " <small> order by </small>";
			echo "<SELECT NAME=\"SORTCOLUMN\">";				
				foreach($sortColumnArray as $column){
					echo "<OPTION VALUE=\"t1.".$column."\" ".selected($_REQUEST['SORTCOLUMN'],"t1.".$column."").">".str_replace("_"," ",ucfirst($column))."</OPTION>";			
				}
			echo "</select>";
			echo " <SELECT NAME=\"ASCDESC\">";		
				echo "<OPTION VALUE='ASC' ".selected($_REQUEST['ASCDESC'],"ASC").">Ascending</OPTION>";				
				echo "<OPTION VALUE='DESC' ".selected($_REQUEST['ASCDESC'],"DESC").">Descending</OPTION>";				
			echo "</select>";
			echo "<INPUT TYPE=SUBMIT NAME=search VALUE=\"Search\"> <INPUT TYPE=BUTTON NAME=NONE VALUE=\"Clear\" ONCLICK=\"document.location = '".$_SERVER["PHP_SELF"]."?VIEW=".$_GET["VIEW"]."&SUB=".$_GET['SUB']."';\">";
			echo "<input type='checkbox' ";			
			if($_REQUEST['hidden'] == '1'){ echo " CHECKED "; }
			echo " name='hidden' value='1'> <small>Show Hidden</small>";
			echo "</FORM>";
			
		echo "</div>";
		echo "<br /><br />";
			
		if ($_GET['KEYWORDS']!="") {
			$q = "AND ".$_GET['COLUMN']." like '%".$_GET['KEYWORDS']."%'";
		}

		// SHOW HIDDEN
		if ($_GET['hidden']==''){
			$h = "AND hidden='0' "; 
		} else {
			$h = "";
		}

		if($_GET['SORTCOLUMN'] != ""){
			$orderByString = " ORDER BY ".$_GET['SORTCOLUMN']." ".$_GET['ASCDESC']." ";
		} else {
			$orderByString = " ORDER BY created DESC ";
		}
		
		$page = 1;
		$size = 50;	 

		if($Join != ""){ $Join = " LEFT JOIN ".$Join." t2 ON t1.".$On."=t2.".$On." "; }
		
		$select = 	"SELECT * FROM ".$table." t1 ".$Join." WHERE ".
					"t1.active='1' ".
					"$join".
					"$q ".
					"$h ".
					"".$_SETTINGS['demosqland']." ".
					"".$orderByString." ";
					
		$total_records = mysql_num_rows(doQuery($select)); 
		
		//echo "<Br>SELECT - $select<Br>";
		//echo "<Br>TOTAL RECORDS - $total_records<Br>";
		 
		if (isset($_GET['page'])){
			$page = (int) $_GET['page'];
		}

		$pagination = new Pagination();
		$pagination->setLink("index.php?VIEW=".$_REQUEST['VIEW']."&SUB=".$_REQUEST['SUB']."&KEYWORDS=".$_REQUEST['KEYWORDS']."&ASCDESC=".$_REQUEST['ASCDESC']."&SORTCOLUMN=".$_REQUEST['SORTCOLUMN']."&COLUMN=".$_REQUEST['COLUMN']."&hidden=".$_REQUEST['hidden']."&page=%s");
		$pagination->setPage($page);
		$pagination->setSize($size);
		$pagination->setTotalRecords($total_records);
		 
		// now use this SQL statement to get records from your table

		$SQL = 	$select.$pagination->getLimitSql()."";	

		//echo "<Br> $SQL <Br>";
		
		// 
		// TABLE HEADER COLUMNS
		//
		echo tableHeaderid("".$name."",6,"100%","list");	
		echo "<thead><TR>";
		// CHECKBOX
		echo "<th style='width:25px' width='25'>Delete</th>";
		$i = 0;
		$imagekey = 1000;
		$categorykey = 1000;
		$datekey = 1000;
		foreach($titleColumnArray as $column){
			$width = "";
			if($i == 0){ $width = "width:20px;"; }
			echo "<th style='".$width." ".$textalign."'>".$column."</th>";
			// SET TYPE FLAGS
			if($column == "Image"){ $imagekey = $i; }
			if($column == "Category"){ $categorykey = $i; }
			if(strstr($column,"Date")){ $datekey = $i; }
			$i++;
		}
		echo "<th>Action</th>";
		echo "</TR></thead><tbody>";	
		
		$res = doQuery($SQL);

		//echo "$SQL";
		
		$i=0;	
		while ($row = mysql_fetch_array($res)) {
			if($i % 2) { $class = "odd"; } else { $class = "even"; }
			if($row['hidden'] == '1'){ $class .= " hiddenrow"; }
			//document.location = '".$_SERVER["PHP_SELF"]."?VIEW=".$_GET["VIEW"]."&SUB=".$_GET['SUB']."&".$xid."=".$row[$valueColumnArray[0]]."';
			echo "<TR class=\"$class\" ondblclick=\" document.location = '".$_SERVER["PHP_SELF"]."?VIEW=".$_GET["VIEW"]."&SUB=".$_GET['SUB']."&".$xid."=".$row[$valueColumnArray[0]]."';\">";
			// CHECKBOX
			echo "<td>";
			if($row['locked'] != '1'){
				echo "<input value='".$row[$valueColumnArray[0]]."' type='checkbox' class='deletebox' name='delete_array[]' >";
			}
			echo "</td>";
			// VALUE COLUMNS
			$imagenum = 0;
			$numkey = 0;
			$dkey = 0;
			foreach($valueColumnArray as $column){
				$value = $row[$column];
				$textalign = "";
				
				/*** DESCRIPTION TYPE ***/
				if($column == "description"){					
					$value = truncate($value,100);
				}
				
				/*** IMAGE TYPE ***/
				elseif($imagenum == $imagekey){
					$img = "";
					$value = "";
					
				
					/** FOR ECOMMERCE / TITLE MATCH	**/
					if($value == ""){
						// SECOND LOOK FOR A NAME MATCH
						$size = lookupDbValue('ecommerce_thumbnail_sizes', 'name', $_SETTINGS['product_page_thumbnail_size'], 'size_id');
						$image1 = strtolower(str_replace(" ","_",$row['name']).".jpg");
						$image1Array = explode(".",$image1);
						$image1formated = $image1Array[0]."_w".$size.".".$image1Array[1];
						//$path = $_SETTINGS['website']."uploads-products/wpThumbnails/".$image1formated."";
						if(is_file($_SETTINGS["DOC_ROOT"]."uploads-products/wpThumbnails/".$image1formated."")){
							//$path = $_SETTINGS['website']."themes/".$_SETTINGS['theme']."images/".$_SETTINGS['image_not_available_thumbnail_file']."";
							$value = "<img src='".$_SETTINGS['website']."uploads-products/wpThumbnails/".$image1formated."' style='display:block; margin:0px auto;'/>";
						} elseif(is_file($_SETTINGS["DOC_ROOT"]."uploads-products/".$image1."")) {
						    $value = "<img src='".$_SETTINGS['website']."uploads-products/".$image1."' style='width:60px;'/>";
						}
					}
					
					/** FOR ECOMMERCE RELAIONAL IMAGES */
					if($value == ""){
						// SECOND LOOK FOR IMAGES THAT ARE STORED IN A RELATIONAL TABLE THAT IS THE COLUMN VALUE
						// THE RELATIONAL TABLE MUST BE THE COLUMN VALUE
						$select = "SELECT * FROM `".$column."` WHERE `".$valueColumnArray[0]."`='".$row[$valueColumnArray[0]]."' LIMIT 1";
						// IMPLY NO ERROR
						$result = doQuery($select,0);
						$num = mysql_num_rows($result);					
						if($num){
							$row1 = mysql_fetch_array($result);
							$img = $row1['image'];
							// LOOK IN PRODUCTS
							$value = "<img src='".$_SETTINGS['website']."uploads-products/".$img."' style='max-width:150px; max-height:150px;'>";
							$textalign = "text-align:center; ";
						}
						
						// THIRD CHECK FOR AN IMAGE IN OTHER PLACES IF THERE IS NONE HERE
						// THE IMAGE COLUMN MUST BE THE COLUMN VALUE
						if($img == ""){
							$img = $row[$column];
							if($img != ""){
								$value = "<img src='".$_SETTINGS['website']."uploads/".$row[$column]."' style='max-width:150px; max-height:150px;'>";
								$textalign = "text-align:center; ";
							}
						}	
					} 
				}// END IMAGE FORMAT				
				
				// IF A CATEGORY VALUE
				elseif($numkey == $categorykey){
					/* FOR ECOMMERCE */
					
					//echo "<br>NUMKEY:".$numkey."<br>";
					//echo "<br>CATKEY:".$categorykey."<br>";
										
					$cat = "";
					// LOOK FOR CATGORIES IN A RELATIONAL TABLE
					// THE RELATIONAL TABLE MUST BE THE COLUMN VALUE
					$select = "SELECT * FROM `".$column."` WHERE `".$valueColumnArray[0]."`='".$row[$valueColumnArray[0]]."'";
					$result = doQuery($select,0);
					$num = mysql_num_rows($result);	
					$category = "";
					if($num){
						while($row1 = mysql_fetch_array($result)){							
							$category_id = $row1['category_id'];
							$category .= lookupDbValue('ecommerce_product_categories','name',$category_id,'category_id').",";
						}
					} else {
						$category_id = 0;
						$category = "Uncategorized";
					}
					
					//$value = "";
					$value = "<a id='cat-".$row[$valueColumnArray[0]]."'>".trim($category,",")."</a>";
					$value .= "<div id='catselect-".$row[$valueColumnArray[0]]."'></div>";
					$value .= "<script>";
					$value .= "
					$('#cat-".$row[$valueColumnArray[0]]."').click(function() {
						
						$.ajax({
						  type: 'POST',
						  url: '".$ajaxURL."',
						  data: 'GET_CATEGORY_SELECT=".$row[$valueColumnArray[0]]."',
						  success: function(data) {
							$('#catselect-".$row[$valueColumnArray[0]]."').html(data);
							$('#cat-".$row[$valueColumnArray[0]]."').html('');
						  }
						});
						
						//zoomloader.gif
						
						$('#cat-".$row[$valueColumnArray[0]]."').css('visibility','hidden');
						$('#catselect-".$row[$valueColumnArray[0]]."').html('<img src=\"images/zoomloader.gif\">');
						
						return false;
						
					});";
					$value .= "</script>";
					//$category = $row1['category_id'];						
					//$value = "<select></select>";
					//$value = "<script></script>"							
					//$value= "".hierarchymultiselectTable('ecommerce_product_categories',''.$row[$valueColumnArray[0]].'categories[]','category_id','name','sort_level','ASC',0, 'ecommerce_product_category_relational','product_id',''.$row[$valueColumnArray[0]].'')."";
					
				}
				
				// IF A DATE FIEld
				elseif($datekey == $dkey){
					$value = TimestampIntoDate($value);
				}
				
				// IF A COLUMN FROM ANTHER TABlE
				elseif(strstr($column,"::")){
					//die "<Br>VALUE $value;</br>";
					//exit;
					$valArray  = explode("::",$column);
					$table1 = $valArray[0];
					$column1 = $valArray[1];
					$link1 = $valArray[2];
					$select = "SELECT `".$column1."` FROM `".$table1."` WHERE ".$link1."='".$row[$link1]."' LIMIT 1";
					//echo "$select <br>";
					$result = doQuery($select);
					$val = mysql_fetch_array($result);
					$value = $val[$column1];
				}
				
				// IF A STATUS COLUMN
				elseif($column == "status"){
					$value = $row[$column];
				}
				
				// iF A TYPE COLUMN
				elseif($column == "type"){
					$value = $row[$column];
				}
				
				// IF ANY OTHER TYPE O FIELD SETUP FOR LIVE EDIT
				elseif($dkey != 0){
					if($readonly == false){
						$inputsize="15";
						if($column == 'price'){ $inputsize="5"; }
						if($column == 'name'){ $inputsize="75"; }
						$value = 	"<input value='".$value."' id='".$column.$i."' name='' style='font-size:11px;' size='".$inputsize."'>";
						$value .= 	"<script>";
						$value .= "
										$('input#".$column.$i."').change(function() {
											
											$.ajax({
											  type: 'POST',
											  url: '".$ajaxURL."',
											  data: 'UPDATE_TABLE=".$table."&UPDATE_FIELD=".$column."&UPDATE_FIELD_VALUE=' + $('#".$column.$i."').val() + '&UPDATE_ROW=".$valueColumnArray[0]."&UPDATE_ROW_ID=".$row[$valueColumnArray[0]]."',
											  success: function(data) {
												true;
											  }
											});
											
											
										});";
						
						$value .= 	"</script>";
					} else {
						$value = $row[$column];
					}
				}
				
				//DISPLAY CELL VALUE
				echo "<td style='".$textalign."'>";
				echo $value;
				echo "</td>";
				
				
				
				$imagenum++;
				$numkey++;
				$dkey++;
			}
			
			//echo "<TD>{$row["name"]}</TD>";
			//echo "<TD>/".$row['clean_url_name']."</TD>";
			//echo "<TD>{$row["title"]}</TD>";
			
			//
			
			// ACTION
			echo "<TD width=\"150\" nowrap ALIGN=\"LEFT\">";
				echo "<FORM METHOD=GET ACTION=\"".$_SERVER[PHP_SELF]."\">";				
				echo "<INPUT TYPE=HIDDEN NAME='".$xid."' VALUE=\"".$row[$valueColumnArray[0]]."\">";
				echo "<INPUT TYPE=HIDDEN NAME=VIEW VALUE=\"".$_GET["VIEW"]."\">";				
				echo "<INPUT TYPE=HIDDEN NAME=SUB VALUE=\"".$_GET["SUB"]."\">";
				echo "<INPUT TYPE=HIDDEN NAME=page VALUE=\"".$_GET["page"]."\">";
				if($row['locked'] != '1'){
					echo "<INPUT TYPE=SUBMIT NAME='DELETE_".rtrim(strtoupper($name),"S")."' VALUE=\"Delete\" onClick=\"return confirm('Are You Sure?');\">";
				}
				echo "<INPUT TYPE=SUBMIT NAME=view VALUE=\"Open\">";
				echo "</FORM>";
			echo "</TD>";
			echo "</TR>";
			$i++;
		}
		if($total_records = 0){
			echo "<tr><td colsapan='".count($titleColumnArray)."'>There are 0 records.</td></tr>";
		}
		echo "</tbody></TABLE>";
		echo "<form class='delete-form'>";
		echo "<input type='hidden' name='items' id='items' value=''>";
		echo "<input type='submit' value='Delete' name='DELETE_".strtoupper($name)."'>";
		echo "<INPUT TYPE=HIDDEN NAME=VIEW VALUE=\"".$_GET["VIEW"]."\">";				
		echo "<INPUT TYPE=HIDDEN NAME=SUB VALUE=\"".$_GET["SUB"]."\">";
		echo "<INPUT TYPE=HIDDEN NAME=page VALUE=\"".$_GET["page"]."\">";
		echo "</form>";
		echo "
			<script>
				// EACH INPUT CLICK GET THE CHECKED ITEMS
				$('.deletebox').click(function(){
					// GET ALL CHECKED INPUTS
					var items = '';
					$(\"input[name='delete_array[]']:checked\").each(function () {
						items += $(this).val() + ',';
					  });
					$('#items').val(items);
				});
			

			</script>
		";
		
		$navigation = $pagination->create_links();
		echo $navigation; // will draw our page navigation
		//exit();
	}
	
	productSearchListingTable($name,$table,$orderByString,$searchColumnArray,$titleColumnArray,$valueColumnArray,$xid,$ajaxURL,$Join,$On,False,True,$sortColumnArray);
	
}

/*** SEARCH/VIEW ORDERS					********************************************************/
elseif($_REQUEST['SUB'] == "" || $_REQUEST['SUB'] == 'ORDERS'){

	$name				= "Orders";
	$table				= "ecommerce_orders";
	$orderByString		= " ORDER BY order_id DESC ";
	$searchColumnArray	= Array("order_id",		"account_id",	"account_name",						"email");
	$titleColumnArray	= Array("Id",			"Status",		"Account Name",						"Date",			"Shipping",		"tax",	"Sub-Total",		"Total");
	$valueColumnArray	= Array("order_id",		"status",		"user_account::name::account_id",	"created",		"sh",			"tax",	"subtotal",			"total");
	$xid				= "oid";
	basicSearchListingTable($name,$table,$orderByString,$searchColumnArray,$titleColumnArray,$valueColumnArray,$xid,"","","",true,false);
	
}
?>