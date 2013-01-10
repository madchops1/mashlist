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



$AutomatedEmails = new AutomatedEmails();	
report($_REQUEST['REPORT'],$_REQUEST['SUCCESS']);



/***	DELETE EMAIL					********************************************************/
if (isset($_REQUEST["DELETE_EMAIL"])){
	// REMOVE PRODUCT
	doQuery("UPDATE automated_email_contents SET active='0' WHERE email_id=".$_REQUEST["eid"]." ".$_SETTINGS['demosqland']."");
	
	$report = "Email deleted.";
	$success = "1";
	if($_REQUEST['SUB'] == ""){ $_REQUEST['SUB'] = "EMAILS"; }
	header("Location: ".$_SERVER["PHP_SELF"]."?VIEW=".$_REQUEST["VIEW"]."&SUB=".$_REQUEST['SUB']."&REPORT=".$report."&SUCCESS=".$success."&page=".$_REQUEST['page']."");
	exit();
}

/***	DELETE MULTIPLE					********************************************************/
if (isset($_REQUEST["DELETE_EMAILS"])){
	
	$string = rtrim($_REQUEST['items'], ','); 
	$Array = explode(",",$string);
	
	foreach($Array AS $id){
		//echo "".$cartitem."<br>";
		$sel = 	"UPDATE automated_email_contents SET active='0' WHERE ".
				"email_id='".$id."'";
		doQuery($sel);
	}
	
	$report = "Emails Deleted Successfully";
	$success = "1";
	header("Location: ".$_SERVER["PHP_SELF"]."?VIEW=".$_REQUEST["VIEW"]."&SUB=".$_REQUEST['SUB']."&REPORT=".$report."&SUCCESS=".$success."&page=".$_REQUEST['page']."");
	exit();
}



//
// ACTIONS
//
if($_POST['SAVE_EMAIL']){
	$error = 0;
	// VALIDATION
	if($_POST['from'] == ""){ $error=1; $report = "Enter a from email address."; }
	if($_POST['subject'] == ""){ $error=1; $report = "Enter a subject."; }
	
	if($error == 0){
		
		$_UNESCAPED = $_POST;
		$_POST = escape_smart_array($_POST);
	
		// UPDATE
		if($_POST['eid'] != ""){			
			$select = 	"UPDATE `automated_email_contents` SET ".
						"`subject`='".$_POST['subject']."',".
						"`html`='".$_POST['html']."',".
						"`template`='".$_POST['template']."',".
						"`from`='".$_POST['from']."' ".
						"WHERE email_id='".$_POST['eid']."'";			
		}
		
		// NEW
		if($_POST['eid'] == ""){			
			$_POST['eid'] = nextId('automated_email_contents');
			$select = 	"INSERT INTO `automated_email_contents` SET ".
						"`subject`='".$_POST['subject']."',".
						"`html`='".$_POST['html']."',".
						"`template`='".$_POST['template']."',".
						"`from`='".$_POST['from']."',".
						"`active`='1'";			
		}
		
		doQuery($select);
		$report = "Email saved.";
		$success = "1";
		header("Location: {$_SERVER["PHP_SELF"]}?REPORT=".$report."&eid=".$_POST['eid']."&SUCCESS=".$success."&VIEW=".$_REQUEST['VIEW']."");
		exit;
	}
}

//
// FORMS
//

if($_REQUEST['SUB'] == 'NEWEMAIL' OR $_REQUEST['eid'] != ""){

	// ADD/EDIT CHECK
	if (isset($_REQUEST["eid"])) {
		$select = 	"SELECT * FROM automated_email_contents ".
					"WHERE ".
					"email_id='".$_REQUEST["eid"]."'";
		$res = doQuery($select);
		$_POST = mysql_fetch_array($res);		
		$_POST['name'] = form_encode($_POST['name']);		
		$button = "Save Email";
		$doing = "Email";
	} else {
		$button = "Save Email";
		$doing = "New Email";
	}	
	
	// START FORM
	startAdminForm();
	// START TABLE
	echo tableHeader("$doing: ".$_POST['subject']."",2,'100%');
	
	// SUBJECT
	adminFormField("*Subject","subject",$_POST['subject'],"textbox");
	
	// FROM
	adminFormField("*From","from",$_POST['from'],"textbox");
	
	// FROM
	adminFormField("*Template","template",$_POST['template'],"textbox");
		
	// HTML
	echo "
		<tr>
		<th>Content</th>
		<td>";
		echo displayWysiwyg("html",$_POST['html'],"400","400");
	echo "
		</td>
		</tr>";
	
	// END ADMIN FORM
	endAdminForm($button,"eid","EMAIL",$_POST);
	
}

//
// LISTS
//
elseif($_REQUEST['SUB'] == "EMAILS" || $_REQUEST['SUB'] == ""){

	$name				= "Automated Emails";
	$table				= "automated_email_contents";
	$orderByString		= "";
	$searchColumnArray	= Array("email_id","subject","from");	
	$titleColumnArray	= Array("Id", "Subject", "Tempalte", "From");	
	$valueColumnArray	= Array("email_id", "subject", "template", "from");
	
	$xid				= "eid";
	$ajaxURL			= "modules/ecommerce/ecommerce_ajax.php";
	//$Join				= "ecommerce_product_category_relational";
	//$On					= "product_id";
	//$orderByString		= "ORDER BY t2.category_id";
	basicSearchListingTable($name,$table,$orderByString,$searchColumnArray,$titleColumnArray,$valueColumnArray,$xid,$ajaxURL,$Join,$On);

}
?>