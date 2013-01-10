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

//
// SET DEFAULT CREATED
//
$select = "UPDATE user_account SET created=NOW() WHERE created='0000-00-00 00:00:00'";
doQuery($select);

/***	Sort PERMISSIONS	BEFORE DECLARE CLASS	*********************************************/
if(isset($_POST['SORT_PERMISSION'])){
	// FOR AJAX
	@require_once '../../../includes/config.php';
	
	//
	// GET SORT ARRAY
	//
	$sortarray = $_POST['sortarray'];
	$sortarray = explode(",",$sortarray);
	//
	// GET TOP MOST PERMISSION LEVEL
	//
	$select = "SELECT permission_level FROM user_permission WHERE active='1' ORDER BY permission_level DESC";
	$result = doQuery($select);
	$num = mysql_num_rows($result);
	echo "NUM: $num <Br>";
	$top_level = $num;
	foreach($sortarray AS $permission){	
		$select = "UPDATE user_permission SET permission_level='".$top_level."' WHERE permission_id='".$permission."'";
		echo "SELECT:".$select."<br>";		
		$result = doQuery($select);		
		$top_level = $top_level - 1;
	}
	//
	// MUST EXIT
 	//
	echo "true";
	exit;
}

/***	ACCOUNT NOTES	BEFORE DECLARE CLASS	*********************************************/
if(isset($_REQUEST['ACCOUNT_NOTES'])){
	@require_once '../../../includes/config.php';
	
	// ACCOUNT NOTES SUBMIT ACTION
	if(isset($_POST['UPDATE_NOTES'])){
		$select = "UPDATE user_account SET notes='".$_REQUEST['notes']."' WHERE account_id='".$_REQUEST['xid']."'";
		doQuery($select);
		$_REQUEST['REPORT'] = "Notes Updated Successfully";
		$_REQUEST['SUCCESS'] = 1;
	}
	
	$select = "SELECT notes FROM user_account WHERE account_id='".$_REQUEST['xid']."'";
	$result = doQuery($select);
	$_POST = mysql_fetch_array($result);
	
	echo "<html><head>";
	echo "<title>Customer Notes</title>";
	//echo "<link href='../../../admin/scripts/adminStyles.css' rel='stylesheet' type='text/css' />";
	echo "<script type='text/javascript' src='../../../admin/scripts/jquery/jquery-1.4.2.min.js' type='text/javascript' language='javascript'></script>";
	echo "</head>";
	echo "<body style='margin:0px; padding:0px; background-color:#EDF1F2;'>";
	echo "<style>";
	echo "body{ font-family:arial; } ";
	echo "#successbox{ font-size:12px; font-weight:bold; color:#fff; background-color:#90d977; display:block; width:300px; margin:10px auto; padding:10px; text-align:center; } ";
	echo "#errorbox{  }	";
	
	echo "</style>";
	echo "<div style='background-color:#EDF1F2; padding:20px 0px;'>&nbsp;&nbsp;&nbsp;&nbsp;Account: ".lookupDbValue('user_account', 'name', $_REQUEST['xid'], 'account_id')." </div>";
	echo "<FORM name='user' METHOD=POST ACTION=''>";
	
	echo "<div style='padding:20px 0px; width:100%; text-align:center; background-color:#DDDDDD;'>";
	echo report($_REQUEST['REPORT'],$_REQUEST['SUCCESS']);
	echo "<br><textarea name='notes' style='width:300px; height:500px;'>".$_POST['notes']."</textarea>";
	echo "</div>";

	// Submit FORM		
	echo "<div id='submit' style='background-color:#EDF1F2; padding:20px 0px; text-align:center;'>";		
	echo "<INPUT TYPE='HIDDEN' NAME='xid' VALUE='".$_REQUEST['xid']."'>";	
	echo "<INPUT TYPE='HIDDEN' NAME='ACCOUNT_NOTES' VALUE='1'>";	
	echo "<INPUT TYPE='SUBMIT' NAME='UPDATE_NOTES' VALUE='Update Notes'>";	
	echo "</div>";
	
	echo "</form>";
	
	echo "</body></html>";
	exit;
}

//
// Declare UserAccounts Class and Report Function
//
$UserAccounts = new UserAccounts();
report($_REQUEST['REPORT'],$_REQUEST['SUCCESS']);
	
/***	REDIRECT TO BILLING 				********************************************************/
if(isset($_POST['opid']))
{
	header("Location: ?VIEW=billing&opid=".$_POST['opid']."");
	exit();
}

/***	REDIRECT TO CAR INSURANCE PAYMENT	********************************************************/
if(isset($_POST['CARINSURANCEPAYMENT']))
{
	header("Location: ?VIEW=car_insurance&SUB=PAYMENT&aid=".$_POST['account_id']."");
	exit();
}
	
/***	Remove An Account				********************************************************/
if (isset($_POST["DELETE"]) || isset($_GET["DELETE"]))
{
	// REMOVE ACCOUNT
	doQuery("UPDATE user_account SET active='0' WHERE account_id=".$_REQUEST["xid"]." ".$_SETTINGS['demosqland']."");
	// REMOVE CONTACTS
	doQuery("UPDATE user_contact SET active='0' WHERE account_id=".$_REQUEST["xid"]." ".$_SETTINGS['demosqland']."");
	
	$report = "Customer Deleted Successfully";
	$success = "1";
	header("Location: ".$_SERVER["PHP_SELF"]."?VIEW=".$_REQUEST["VIEW"]."&REPORT=".$report."&SUCCESS=".$success."/0");
	exit();
}

/***	Add A Location	 				********************************************************/
if ((isset($_POST["location_name"])) and ($_POST['location_name'] != ""))
{
	$error = 0;
	if($error == 0){	
		$_POST['location_id'] = nextId('locations');
		$sql = 	"INSERT INTO locations SET ".
		 		"name='".$_POST['location_name']."'";
		doQuery($sql);
	}    
}

/***	Add An Account					********************************************************/
if (isset($_POST["ADD_CUSTOMER"]))
{
	$error = 0;	
	if($_POST['name'] == ""){ $error = 1; ReportError("Enter A Customer Customer Name"); }	
	if($UserAccounts->CheckAccount() == false){ $error = 1; ReportError("That customer already exists");  }	
	
	// IF CMS VALIDATE EMAIL, USERNAME, PASSWORD
	if(checkActiveModule('0000000')){
		if($_POST['email'] == ""){ $error = 1; ReportError("Enter An Email Address"); }	
		if(VerifyEmail($_POST['email']) != 1){ $error = 1; ReportError("The Email Address Is Not Valid"); }
		if($UserAccounts->CheckEmail() == false){ $error = 1; ReportError("The Email Address Is Already In Use"); }
		if($_POST['username'] == ""){ $error = 1; ReportError("Enter A Username"); }
		if($UserAccounts->CheckUsername() == false){ $error = 1; ReportError("The Username Is Already In Use"); }
		if($_POST["password1"]!=$_POST["password2"]){ $error = 1; ReportError("Your Passwords Do Not Match"); }
	}
	
	if($error == 0)	{
		$_POST["password"] = md5($_POST["password1"]);
		$_POST = escape_smart_array($_POST);			
		
		// DOB
		$dob = $_POST['dob'];
		$dob_array = explode("/",$dob);
		$dob_time = mktime(0,0,0,$dob_array[0],$dob_array[1],$dob_array[2]);
		$dob_date = date("Y-m-d",$dob_time)."";
		
		// INSERT RECORD
		$next = nextId('user_account');
		$select =	"INSERT INTO user_account SET ".
					"name='".$_POST["name"]."',".
					"username='".$_POST["username"]."',".
					"password='".$_POST["password"]."',".
					"email='".$_POST["email"]."',".
					"email_verified='1',".
					"discount_rate='".$_POST['discount_rate']."',".
					"location_id='".$_POST['location_id']."',".
					"user_permission='".$_POST['user_permission']."',".
					"active=1,".
					"dob=".$dob_date.",".
					"assigned_to='".$_POST['assigned_to']."',".
					"created=NULL,".
					"created_by='".$_SESSION['session']->admin->userid."'".
					"".$_SETTINGS['demosql']."";			
		doQuery($select);
		
		

		$report = "Customer Created Successfully";
		header("Location: ?REPORT=".$report."&SUCCESS=1&xid=".$next."&VIEW=".$_REQUEST['VIEW']."");
		exit();
	}
}

/***	Update An Account 				********************************************************/
if (isset($_POST["UPDATE_CUSTOMER"]))
{
	$error = 0;
	if($_POST['name'] == ""){ $error = 1; ReportError("Enter a customer account name"); }	
	
	// IF CMS VALIDATE USERNAME AND PASSWORD 
	if(checkActiveModule('0000000')){
		if($_POST['email'] == ""){ $error = 1; ReportError("Enter An Email Address"); }	
		if(VerifyEmail($_POST['email']) != 1){ $error = 1; ReportError("The Email Address Is Not Valid"); }
		if($_POST['email'] != $_POST['emailhidden']){
			if($UserAccounts->CheckEmail() == false){ $error = 1; ReportError("The Email Address Is Already In Use"); }
		}
		if($_POST['username'] != $_POST['usernamehidden']){
			if($UserAccounts->CheckUsername() == false){ $error = 1; ReportError("The Username Is Already In Use"); }
		}	
		if ($_POST["password1"]!=$_POST["password2"]) { $error = 1; ReportError("Your Passwords Do Not Match"); }
		if ($_POST["password1"]!=""){ $password = ",password='".md5($_POST["password1"])."'"; }
	}
	if($error == 0){		
		// IF CMS DO EMAIL VERIFICATION STUFF
		if(checkActiveModule('0000000')){
			$emailverificationstring = md5($_POST['email']);
			if($_POST['email_verified'] == '1'){
				$emailverified = ",email_verified='1'";
			}		
			if($_POST['send_email_verification'] == '1'){
				$emailverified = ",email_verified='0'";
				$report1 = ". ".$UserAccounts->SendVerificationEmail($emailverificationstring,$_POST['email']);
			}
		}		
		
		// DOB
		$dob = $_POST['dob'];
		$dob_array = explode("/",$dob);
		$dob_time = mktime(0,0,0,$dob_array[0],$dob_array[1],$dob_array[2]);
		$dob_date = date("Y-m-d",$dob_time)."";

		
		// ACCOUNT 
		$select =	"UPDATE user_account SET ".		
					"name='".$_POST['name']."',".
					"username='".$_POST['username']."',".
					"user_permission='".$_POST['user_permission']."',".
					"assigned_to='".$_POST['assigned_to']."',".
					"location_id='".$_POST['location_id']."',".
					"email='".$_POST['email']."',".
					"discount_rate='".$_POST['discount_rate']."',".
					"dob='".$dob_date."',".
					"email_verification_string='".$emailverificationstring."'".	
					"".$_SETTINGS['demosql']."".					
					"$password".
					"$emailverified".					
					" WHERE account_id='".$_POST["xid"]."'";						
		doQuery($select);		
		$report = "Customer Updated Successfully".$report1;
		$success = "1";
		header("Location: {$_SERVER["PHP_SELF"]}?REPORT=".$report."&xid=".$_POST['xid']."&SUCCESS=".$success."&VIEW={$_GET["VIEW"]}&".SID);
		exit;
	}
}

/***	Remove  CONTACT					********************************************************/
if (isset($_POST["DELETECONTACT"]) || isset($_GET["DELETECONTACT"]))
{
	doQuery("UPDATE user_contact SET active='0' WHERE contact_id=".$_REQUEST["cid"]." ".$_SETTINGS['demosqland']."");
	doQuery("DELETE FROM user_contact_relational WHERE contact_id='".$_REQUEST["cid"]."'");
	$report = "User Contact Deleted Successfully";
	$success = "1";
	header("Location: ".$_SERVER["PHP_SELF"]."?VIEW=".$_REQUEST["VIEW"]."&xid=".$_REQUEST['xid']."&REPORT=".$report."&SUCCESS=".$success."/0");
	exit();
}

/***	ADD A CONTACT	 				********************************************************/
if (isset($_POST["ADD_CONTACT"]))
{
	
	$error = 0;
	// Validation
	if($_POST['email']){
		if(VerifyEmail($_POST['email']) != 1){ $error = 1; ReportError("The Email Address Is Not Valid"); }
	}
	
	if($error == 0){
		// insert record
		$next = nextId('user_contact');
		$select =	"INSERT INTO user_contact SET ".
					"first_name='".$_POST['first_name']."',".
					"last_name='".$_POST['last_name']."',".
					"email='".$_POST['email']."',".
					"address1='".$_POST['address1']."',".
					"address2='".$_POST['address2']."',".
					"city='".$_POST['city']."',".
					"state='".$_POST['state']."',".
					"zip='".$_POST['zip']."',".
					"phone='".$_POST['phone']."',".
					"country='".$_POST['country']."',".
					"account_id='".$_POST['xid']."',".
					"active=1".
					"".$_SETTINGS['demosql']."";	
		doQuery($select);
		
		// RELATIONAL
		// LOOP THROUGH CONTACT TYPES FOR RELATIONAL ENTRY
		$sel3 = "SELECT * FROM user_contact_type WHERE active='1'";
		$res3 = doQuery($sel3);
		$i3 = 0;					
		$num3 = mysql_num_rows($res3);
		while($i3<$num3){
			
			$row3 = mysql_fetch_array($res3);
			// IF CONTACT TYPE SELECTED
			if($_POST['contact_type_'.$row3['type_id']] == '1'){
		
				// INSERT  NECESSARY RELAIONAL ENTRY
				$select4 = 	"INSERT INTO user_contact_relational SET ".
							"type_id='".$row3['type_id']."',contact_id='".$next."'";
				$result4 =	doQuery($select4);						
			} else {
			
				// DELETE NECESSARRY RELATIONAL ENTRY
				$select4 = 	"DELETE FROM user_contact_relational WHERE type_id='".$row3['type_id']."' AND contact_id='".$next."'";
				$result4 =	doQuery($select4);
			}
			
			$i3++;
		}	
		
		
		
		$report = "Contact Information Added Successfully";
		$success = "1";
		header("Location: ".$_SERVER["PHP_SELF"]."?REPORT=".$report."&cid=".$next."&SUCCESS=".$success."&VIEW=".$_GET["VIEW"]."");
		exit;
	}
}

/***	Update A Contact				********************************************************/
if (isset($_POST["UPDATE_CONTACT"]))
{

	$error = 0;
	// Validation
	if($_POST['email']){
		if(VerifyEmail($_POST['email']) != 1){ $error = 1; ReportError("The Email Address Is Not Valid"); }
	}
		
	if($error == 0){
		// update record
		$select =	"UPDATE user_contact SET ".
					"first_name='".$_POST['first_name']."',".
					"last_name='".$_POST['last_name']."',".
					"email='".$_POST['email']."',".
					"address1='".$_POST['address1']."',".
					"address2='".$_POST['address2']."',".
					"city='".$_POST['city']."',".
					"state='".$_POST['state']."',".
					"zip='".$_POST['zip']."',".
					"phone='".$_POST['phone']."',".
					"country='".$_POST['country']."',".
					"active=1".
					"".$_SETTINGS['demosql']."".
					" WHERE contact_id='".$_POST["cid"]."'";	
					
					
		// RELATIONAL			
		// LOOP THROUGH CONTACT TYPES FOR RELATIONAL ENTRY
		$sel3 = "SELECT * FROM user_contact_type WHERE active='1'";
		$res3 = doQuery($sel3);
		$i3 = 0;					
		$num3 = mysql_num_rows($res3);
		while($i3<$num3){
			
			$row3 = mysql_fetch_array($res3);
			// IF CONTACT TYPE SELECTED
			if($_POST['contact_type_'.$row3['type_id']] == '1'){
		
				// INSERT  NECESSARY RELAIONAL ENTRY
				$select4 = 	"INSERT INTO user_contact_relational SET ".
							"type_id='".$row3['type_id']."',contact_id='".$_POST["cid"]."'";
				$result4 =	doQuery($select4);						
			} else {
			
				// DELETE NECESSARRY RELATIONAL ENTRY
				$select4 = 	"DELETE FROM user_contact_relational WHERE type_id='".$row3['type_id']."' AND contact_id='".$_POST["cid"]."'";
				$result4 =	doQuery($select4);
			}
			
			$i3++;
		}	
					
		doQuery($select);
		$report = "Contact Information Updated Successfully";
		$success = "1";
		header("Location: ".$_SERVER["PHP_SELF"]."?REPORT=".$report."&cid=".$_POST['cid']."&SUCCESS=".$success."&VIEW=".$_GET["VIEW"]."");
		exit;
	}
}
	
/*** 	Remove A Credit Card		 	********************************************************/
if (isset($_POST["DELETECREDIT"]) || isset($_GET["DELETECREDIT"]))
{
	doQuery("UPDATE credit_card SET active='0' WHERE credit_card_id='".$_REQUEST["pid"]."'");
	header("Location: ".$_SERVER["PHP_SELF"]."?VIEW=".$_REQUEST["VIEW"]."&xid=".$_REQUEST['xid']."&REPORT=Payment Info Deleted Successfully&SUCCESS=1&".SID);
	exit();
}

/*** 	Add PAYMENT INFO				********************************************************/
if (isset($_POST["ADD_PAYMENT_INFORMATION"]))
{
	$error = 0;
	global $_SESSION;
	// Validation
	if($_POST['number'] == ""){ $error = 1; ReportError("Enter A Credit Card"); }
	if($_POST['name'] == ""){ $error = 1; ReportError("Enter Name As It Appears On Card"); }
	if($_POST['exp_m'] == ""){ $error = 1; ReportError("Experation Month"); }
	if($_POST['exp_y'] == ""){ $error = 1; ReportError("Experation Year"); }
	if($_POST['cvv'] == ""){ $error = 1; ReportError("CVV Code"); }
	
	if($error == 0)
	{
		$_POST = escape_smart_array($_POST);
		// insert client record
		$next = nextId('credit_card');
		$select =	"INSERT INTO credit_card SET ".
					"account_id='".$_POST['xid']."',".
					"number='".$_POST['number']."',".
					"name='".$_POST['name']."',".
					"exp_m='".$_POST['exp_m']."',".
					"exp_y='".$_POST['exp_y']."',".
					"cvv='".$_POST['cvv']."',".
					"active='1',".
					"created=NULL".
					"".$_SETTINGS['demosql']."";			
		doQuery($select);
		$report = "Credit Card Information Saved Successfully";
		header("Location: {$_SERVER["PHP_SELF"]}?REPORT={$report}&SUCCESS=1&xid=".$_REQUEST['xid']."&VIEW={$_GET["VIEW"]}&".SID);
		exit();
	}
}

/*** 	Update Payment INFO 			********************************************************/
if (isset($_POST["UPDATE_PAYMENT_INFORMATION"]))
{

	$error = 0;
	// Validation 
	if($_POST['number'] == ""){ $error = 1; ReportError("Enter A Credit Card"); }
	if($_POST['name'] == ""){ $error = 1; ReportError("Enter Name As It Appears On Card"); }
	if($_POST['exp_m'] == ""){ $error = 1; ReportError("Experation Month"); }
	if($_POST['exp_y'] == ""){ $error = 1; ReportError("Experation Year"); }
	if($_POST['cvv'] == ""){ $error = 1; ReportError("CVV Code"); }
	
	if($error == 0)
	{
		// update record 
		$select =	"UPDATE credit_card SET ".
					"number='".$_POST['number']."',".
					"name='".$_POST['name']."',".
					"exp_m='".$_POST['exp_m']."',".
					"exp_y='".$_POST['exp_y']."',".
					"cvv='".$_POST['cvv']."',".
					"active=1".
					" WHERE credit_card_id='".$_POST["pid"]."'";		
		doQuery($select);
		
		header("Location: {$_SERVER["PHP_SELF"]}?REPORT=User Updated Successfully&cid=".$_POST['cid']."&pid=".$_POST['pid']."&xid=".$_POST['xid']."&SUCCESS=1&VIEW={$_GET["VIEW"]}&".SID);
		exit();
	}
}

/*** 	DELETE A PERMISSION			 	********************************************************/
if (isset($_POST["DELETE_PERMISSION"]) || isset($_GET["DELETE_PERMISSION"]))
{

		doQuery("UPDATE user_permission SET active='0' WHERE permission_id='".$_REQUEST["peid"]."'");
		header("Location: ".$_SERVER["PHP_SELF"]."?VIEW=".$_REQUEST["VIEW"]."&PERMISSIONS=1&REPORT=Permission Deleted Successfully&SUCCESS=1&".SID);
		exit();

}

/*** 	Add A PERMISSION 				********************************************************/
if (isset($_POST["ADD_PERMISSION"]))
{
	$error = 0;
	global $_SESSION;
	// Validation
	//if($_POST['permission_level'] == ""){ $error = 1; ReportError("Enter A Permission Level"); }
	if($_POST['name'] == ""){ $error = 1; ReportError("Enter Name"); }
	
	if($error == 0)
	{
		$_POST = escape_smart_array($_POST);
		//
		// GET THE NEXT PERMISSION LEVEL
		//
		$select =	"SELECT * FROM user_permission ORDER BY permission_level DESC LIMIT 1";
		$result = 	doQuery($select);
		$row	= 	mysql_fetch_array($result); 
		$nextPermission = $row['permission_level'] + 1;
		//
		// insert record
		//
		$next = nextId('user_permission');
		$select =	"INSERT INTO user_permission SET ".
					"permission_level='".$nextPermission."',".
					"name='".$_POST['name']."',".
					"referrable='".$_POST['referrable']."',".
					"discount='".$_POST['discount']."',".
					"discount_max='".$_POST['discount_max']."',".
					"discount_maximum_dollar_amount='".$_POST['discount_maximum_dollar_amount']."',".
					"discount_type='".$_POST['discount_type']."',".
					"registration_setting='".$_POST['registration_setting']."',".
					"registration_email_id='".$_POST['registration_email_id']."',".
					"charge_method='".$_POST['charge_method']."',".
					"quickbooks_checkout_method='".$_POST['quickbooks_checkout_method']."',".
					"quickbooks_invoice_terms='".$_POST['quickbooks_invoice_terms']."',".
					"quickbooks_invoice_due_days='".$_POST['quickbooks_invoice_due_days']."',".
					"active='1',".
					"created=NULL".
					"".$_SETTINGS['demosql']."";			
		doQuery($select);
		$report = "Permission Added Successfully";
		header("Location: {$_SERVER["PHP_SELF"]}?REPORT={$report}&SUCCESS=1&PERMISSIONS=1&peid=".$next."&VIEW={$_GET["VIEW"]}&".SID);
		exit();
	}
}

/*** 	Update A PERMISSION				********************************************************/
if (isset($_POST["UPDATE_PERMISSION"]))
{

	$error = 0;
	// Validation
	//if($_POST['permission_level'] == ""){ $error = 1; ReportError("Enter A Permission Level"); }
	if($_POST['name'] == ""){ $error = 1; ReportError("Enter Name"); }
	
	if($error == 0)
	{
		// update record 
		$select =	"UPDATE user_permission SET ".
					//"permission_level='".$_POST['permission_level']."',".
					"name='".$_POST['name']."',".
					"referrable='".$_POST['referrable']."',".
					"discount='".$_POST['discount']."',".
					"discount_max='".$_POST['discount_max']."',".
					"discount_maximum_dollar_amount='".$_POST['discount_maximum_dollar_amount']."',".
					"discount_type='".$_POST['discount_type']."',".
					"registration_setting='".$_POST['registration_setting']."',".
					"registration_email_id='".$_POST['registration_email_id']."',".
					"charge_method='".$_POST['charge_method']."',".
					"quickbooks_checkout_method='".$_POST['quickbooks_checkout_method']."',".
					"quickbooks_invoice_terms='".$_POST['quickbooks_invoice_terms']."',".
					"quickbooks_invoice_due_days='".$_POST['quickbooks_invoice_due_days']."',".
					"active='1'".
					" WHERE permission_id='".$_POST["peid"]."'";		
		doQuery($select);
		
		header("Location: {$_SERVER["PHP_SELF"]}?REPORT=Permission Updated Successfully&peid=".$_POST['peid']."&PERMISSIONS=1&SUCCESS=1&VIEW={$_GET["VIEW"]}");
		exit();
	}
}

/*************************************************************************************************************************************
*
* BEGIN FORMS
*
*************************************************************************************************************************************/

/*** UPDATE / Add An Account FORM		********************************************************/
if (isset($_GET["ADDNEW"]) || (isset($_GET["xid"]) AND ($_REQUEST['NEWCONTACT'] == "") AND ($_GET['ADDPAYMENTINFO'] == "" AND $_GET['pid'] == ""))){
	if (isset($_REQUEST["xid"])) {
		$select = 	"SELECT * FROM user_account ".
					"WHERE ".
					"account_id='".$_REQUEST["xid"]."'";
		$res = doQuery($select);
		
		$_POST = mysql_fetch_array($res);
		
		//var_dump($_POST);
		
		$button = "Update Customer";
		$doing = "Customer ".$_POST['account_id']."";
	} else {
		$button = "Add Customer";
		$doing = "New Customer";
	}
	?>
	<FORM name="user" METHOD=POST ACTION="">
		<?
		echo tableHeader("$doing: ".$_POST['name']."",2,'100%');
		?>
			
			<?
			if($_SETTINS['registration_email_link_validation'] == '1'){
				if($_POST['email_verified'] == '0'){
				?>
				<TR BGCOLOR="#f2f2f2">
				<Th width="200" style="padding-left:20px;">New Customer:</Th>
				<TD>
				<span style="color:red;">This user registered but never verified their email address.</span>
				<Br><Br>
				<input type="checkbox" value="1" name="email_verified"> Verify Manually
				<br><br>
				<input type="checkbox" value="1" name="send_email_verification"> Send Another Verification Email
				</TD>
				</TR>
				<?
				}
			}
			?>
			
			<TR BGCOLOR="#f2f2f2">
			<Th width="200" style="padding-left:20px;">Customer Name:</Th>
			<TD>
			<INPUT TYPE=TEXT NAME="name" VALUE="<?=$_POST['name']?>" />
			</TD>
			</TR>
			
			
			
			
			
			<?
			//if($_REQUEST['pid']){
			//$_POST['date'] = $_POST['date'];
			$date_array1 = explode(" ",$_POST['dob']);
			$date_array2 = explode("-",$date_array1[0]);
			$_POST['dob'] = "".$date_array2[1]."/".$date_array2[2]."/".$date_array2[0]."";
			//}
			?>
			<TR>
				<Th>DOB</Th>
				<TD><INPUT TYPE=TEXT NAME="dob" id="datepicker" VALUE="<?=$_POST['dob']?>"></TD>
			</TR>
			
			<script type="text/javascript">
			$(function() {
				$("#datepicker").datepicker();
			});
			</script>
			
			<?			
			// IF CMS MODULE EXISTS
			if(checkActiveModule('0000000')){
				?>
				<TR BGCOLOR="#f2f2f2">
				<Th width="200" style="padding-left:20px;">*Login Username:</Th>
				<TD>
				<INPUT TYPE=TEXT NAME="username" VALUE="<?=$_POST['username']?>">
				<INPUT TYPE=HIDDEN NAME="usernamehidden" VALUE="<?=$_POST['username']?>">
				</TD>
				</TR>
				
				<?
				/**
				 *
				 * EMAIL ADDRESS
				 *
				 **/
				?>
				<TR BGCOLOR="#f2f2f2">
				<Th width="200" style="padding-left:20px;">*Email:</Th>
				<TD>
				<INPUT TYPE=TEXT NAME="email" VALUE="<?=$_POST['email']?>">
				<INPUT TYPE=HIDDEN NAME="emailhidden" VALUE="<?=$_POST['email']?>">
				</TD>
				</TR>
				<?
			} // end if cms module exists
			
			
			
			
			
			
			/**
			 *
			 * Account Origin
			 *
			 **/
			if($_REQUEST['xid'] != ""){
				?>
				<TR BGCOLOR="#f2f2f2">
				<Th width="200" style="padding-left:20px;">Origin:</Th>
				<TD>
				<? if($_POST['created_by'] != '0'){ ?>
					<?=$_POST['name'] ?> was registered by <?=lookupDbValue('admin', 'name', $_POST['created_by'], 'admin_id') ?> on <?=TimestampIntoDate($_POST['created']) ?> at  <?=TimestampIntoTime($_POST['created']) ?>.
				<? } else { ?>
					<?=$_POST['name'] ?> regisered themselves on <?=$_POST['created'] ?>.
				<? } ?>
				</TD>
				</TR>
				<?
			}
			?>
			
			<TR BGCOLOR="#f2f2f2">
			<Th width="200" style="padding-left:20px;">Assigned To:</Th>
			<TD>
			<select name="assigned_to">
				<option value="0">Unassigned</option>
				<?
				$sel1 = "SELECT * FROM admin WHERE active='1'";
				$res1 = doQuery($sel1);
				$num1 = mysql_num_rows($res1);
				$i1 = 0;
				while($i1<$num1){
					$row1 = mysql_fetch_array($res1);
					?>
					<option value="<?=$row1['admin_id']?>" <? if($_POST['assigned_to'] == $row1['admin_id']){ ?> SELECTED <? } ?>><?=$row1['name']?></option>
					<?
					$i1++;
				}
				?>
			</select>
			</TD>
			</TR>
			
			<?
			//
			// LOCATION
			//
			$identifier = 'location1';
			?>
			<TR BGCOLOR="#f2f2f2" class="toggleropenidentifier<?=$identifier ?>">
			<Th width="200" style="padding-left:20px;">Assigned Location:</Th>
			<TD>
			<select name="location_id">
				<option value="0">Unassigned</option>
				<?
				$sel1 = "SELECT * FROM locations WHERE active='1'";
				$res1 = doQuery($sel1);
				$num1 = mysql_num_rows($res1);
				$i1 = 0;
				while($i1<$num1){
					$row1 = mysql_fetch_array($res1);
					?>
					<option value="<?=$row1['location_id']?>" <? if($_POST['location_id'] == $row1['location_id']){ ?> SELECTED <? } ?>><?=$row1['name']?></option>
					<?
					$i1++;
				}
				?>
			</select>
			<!-- NEW Location -->
			&nbsp;
			&nbsp;
			<a class="toggleridentifier<?=$identifier ?> tog">New Location</a>
			</TD>
			</TR>
			
			<?
			//
			// LOCATION NAME
			//
			?>			
			<TR BGCOLOR="#f2f2f2" class="toggleidentifier<?=$identifier ?>">
			<Th width="200" style="padding-left:20px;">Location Name:</Th>
			<TD>
			<INPUT TYPE=TEXT NAME="location_name" size="30" VALUE="<?=$_POST['location_name']?>" />
			&nbsp;
			&nbsp;
			<a class="togglercloseidentifier<?=$identifier ?> tog">Cancel New Location</a>
			</TD>
			</TR>
			
			<?
			//
			// LOCATION JAVASCRIPT
			//
			?>		
			<script type="text/javascript">
			  $(".toggleidentifier<?=$identifier ?>").hide();
			  $(".toggleridentifier<?=$identifier ?>").click(function()
			  {
				$(".toggleidentifier<?=$identifier ?>").slideToggle('fast',callback('<?=$identifier?>'));
			  });
			  
			  $(".togglercloseidentifier<?=$identifier ?>").click(function()
			  {
				$(".toggleidentifier<?=$identifier ?>").slideToggle('fast',callback1('<?=$identifier?>'));
			  });
			</script>

			<?
			//
			// IF CMS MODULE EXISTS
			//
			if(checkActiveModule('0000000')){
				?>
				<TR BGCOLOR="#f2f2f2">
				<Th width="200" style="padding-left:20px;"><?=info('Set the permission level this account has access to.');?>  Permission</Th>
				<TD>
				<?
				$sel1 = "SELECT * FROM user_permission WHERE active='1' ORDER BY permission_level ASC";
				$res1 = doQuery($sel1);
				$num1 = mysql_num_rows($res1);
				$i1 = 0;
				?>
				<select name="user_permission">
					<option value="0" <? if($_POST['user_permission'] == "0"){ ?> SELECTED <? } ?>>None</option>
					<?
					while($i1<$num1){
						$row1 = mysql_fetch_array($res1);
						?>
						<option <? if($_POST['user_permission'] == $row1['permission_id']){ ?> SELECTED <? } ?> value="<?=$row1['permission_id']?>">
						<?=$row1['name']?>
						</option>
						<?
						$i1++;
					}
				?>
				</select>	

				<?
				// IF ECOM
				if(checkActiveModule('0000012')){
					// GET DISCOUNT TYPE
					//$discountType = lookupDbValue('user_permission','discount_type',$_POST['user_permission'],'permission_id');
					//echo "<br>PERMISSION ID ".$row1['permission_id']."<br>DISCOUNT TYPE $discountType <br>";
					if($discountType == "Referral Based"){
						$ratedisabled = "";
					}
					elseif($discountType == "Rate"){
						$ratedisabled = " DISABLED ";
					}
					?>
					<tr>
					<th>Discount Rate</th>
					<Td><input name='discount_rate' <?=$ratedisabled?> value='<?=$_POST['discount_rate']?>' /></td>
					</tr>
					<?
				}
				?>
				
				</TD>
				</TR>
			
			
				<TR BGCOLOR="#f2f2f2">
				<Th width="200" style="padding-left:20px;">*Password:</Th>
				<TD><INPUT TYPE=password NAME="password1" VALUE=""></TD>
				</TR>
				
				<TR BGCOLOR="#f2f2f2">
				<Th width="200" style="padding-left:20px;">*Confirm Password:</Th>
				<TD><INPUT TYPE=password NAME="password2" VALUE=""></TD>
				</TR>
	
			<?
			} // END CMS MODULE
			?>
	
	
	
	
			<?
			//
			// IF EDITING THEN CONTACT CONDITION
			//
			if($_REQUEST['xid'] != ""){				
				// ADD NEW CONTACT BUTTON
				?>
				<TR BGCOLOR="#f2f2f2">
				<Th width="200" style="padding-left:20px;">New Contact</Th>
				<TD>
				<a href="?VIEW=<?=$_GET['VIEW']?>&NEWCONTACT=1&xid=<?=$_POST['account_id']?>"><img src="<?=$_SETTINGS['website']."admin/images/icons/plus_16.png"?>" alt="edit" border="0"> Add A New Contact To This Account</a>
				</TD>
				</TR>				
				<?
				//
				// GET USER CONTACTS
				//			
				$sel2 = "SELECT * FROM user_contact WHERE account_id='".$_POST['account_id']."' AND active='1'";
				$res2 = doQuery($sel2);
				$num2 = mysql_num_rows($res2);
				$i2 = 0;
				while($i2<$num2){
					$row2 = mysql_fetch_array($res2);
					
					$type = "";
					$type = $UserAccounts->FormatContactType($row2['contact_id']);
										
					?>
					<TR BGCOLOR="#f2f2f2">
					<Th width="200" style="padding-left:20px;"><?=$type?> Contact</Th>
					<TD>
					<?=$UserAccounts->FormatFirstLast($row2) ?> <i>(<?=$row2['email']?>)</i>
					<small>
					&nbsp;&nbsp;
					<a href="?VIEW=<?=$_GET['VIEW']?>&cid=<?=$row2['contact_id']?>"><img src="<?=$_SETTINGS['website']."admin/images/icons/pencil_16.png"?>" alt="edit" border="0"> Edit Contact</a>
					&nbsp;&nbsp;
					<a href="?VIEW=<?=$_GET['VIEW']?>&DELETECONTACT=1&cid=<?=$row2['contact_id']?>&xid=<?=$row2['account_id']?>"><img src="<?=$_SETTINGS['website']."admin/images/icons/delete_16.png"?>" alt="edit" border="0"> Delete</a>
					</small>
					</TD>
					</TR>
					<?
					$i2++;
				}
			}
		
			//
			// ACCOUNT NOTES
			//
			if($_REQUEST['xid'] != ""){		
				?>
				<tr>
				<Th width="200" style="padding-left:20px;">Customer Notes</Th>
				<TD>
				<INPUT type="button" value="Open This Customer's Notes" onClick="window.open('modules/user_accounts/user_accounts.php?ACCOUNT_NOTES=1&xid=<?=$_REQUEST['xid']?>','Account Notes','resizable=no,scrollbars=yes,toolbar=no,location=no,width=400,height=700,left=100,top=100,screenX=0,screenY=100')"> 
				</TD>
				</tr>
				<?
			
				// IF CAR INSURANCE
				if(checkActiveModule('0000010')){
					$CarInsurance = new CarInsurance();
					echo "<TR><TD colspan='2'>";				
					$CarInsurance->displayAccountsForms($_REQUEST['xid']);
					echo "</TD></TR>";
				}
				
				// IF ECOMMERCE
				if(checkActiveModule('0000012')){
					$Ecommerce = new Ecommerce();
					echo "<tr><td colspan='2'>";					
					$Ecommerce->displayAccountForms($_REQUEST['xid']);
					echo "</td></tr>";
				}				
			}
			?>
		
		</table>
		
		<?		
		// Submit FORM
		?>
		<div id="submit">
		<a href="?VIEW=<?=$_GET['VIEW']?>">Back</a> &nbsp;&nbsp;&nbsp; 
		
		<?		
		echo "<INPUT TYPE=SUBMIT NAME=\"".strtoupper(str_replace(" ", "_", $button))."\" VALUE=\"$button\">";
		if (isset($_REQUEST['xid'])){
			echo "<INPUT TYPE=HIDDEN NAME='xid' VALUE='".$_REQUEST["xid"]."' >";
			echo "<INPUT TYPE=SUBMIT NAME=DELETE value=\"Delete\" onclick=\"return confirm('Are You Sure?');\">";
		}
		?>
		<script>
		  function callback($ident){
			$(".toggleidentifier"+$ident+"").css({'display' : ''});
			$(".toggleridentifier"+$ident+"").css({'display' : 'none'});
			$(".toggleropenidentifier"+$ident+"").css({'display' : 'none'});
			return true;
		  }
		  
		  function callback1($ident){
			$(".toggleidentifier"+$ident+"").css({'display' : 'none'});
			$(".toggleridentifier"+$ident+"").css({'display' : 'inline'});
			$(".toggleropenidentifier"+$ident+"").css({'display' : ''});
			return true;
		  }
		</script>
		</div>
	</FORM>
	<?
}

/*** ADD / EDIT A Contact CID			********************************************************/
elseif(isset($_REQUEST['NEWCONTACT']) || isset($_REQUEST['cid']))
{
	//
	// GET THE CONTACT INFO
	//
	if(isset($_GET['cid'])){
		$sel1 = "SELECT * FROM user_contact WHERE contact_id='".$_GET['cid']."'";
		$res1 = doQuery($sel1);
		$_POST = mysql_fetch_array($res1);
		
		$sel2 = "SELECT * FROM user_account WHERE account_id='".$_POST['account_id']."'";
		$res2 = doQuery($sel2);
		$rowz = mysql_fetch_array($res2);
		
		$button = "Update Contact";
		$doing = "Contact Information For ";
	} else {
		
		$sel2 = "SELECT * FROM user_account WHERE account_id='".$_REQUEST['xid']."'";
		$res2 = doQuery($sel2);
		$rowz = mysql_fetch_array($res2);
		
		$button = "Add Contact";
		$doing = "New Contact Information For ";
	}
	?>
	<FORM name="user" METHOD=POST ACTION="">
		<?
		echo tableHeader($doing.$rowz['name']."",2,'100%');
		?>
					
			<TR BGCOLOR="#f2f2f2">
			<Th width="200" style="padding-left:20px;">*Contact Type:</Th>
			<TD>
				<?
				$select11 = "SELECT * FROM user_contact_type WHERE active='1'";
				$result11 = doQuery($select11);
				$num11 = mysql_num_rows($result11);
				$i11 = 0;
				$iz = 1;
				while($i11<$num11){
					// DETERMINE IF ACTIVE
					$active = 0;
					$row11 = mysql_fetch_array($result11);
					$sel22 = "SELECT * FROM user_contact_relational WHERE type_id='".$row11['type_id']."' AND contact_id='".$_POST['contact_id']."'";
					$res22 = doQuery($sel22);
					$row22 = mysql_fetch_array($res22);
					if($row22['contact_id'] != ""){ $active = 1; }
					?>
					<input type="checkbox" style="float:none;" name="contact_type_<?=$row11['type_id']?>" <? if($active == 1){ ?> CHECKED <? } ?> value="1"> <?=$row11['name']?> 
					<?
					$i11++;						
					if($iz == 3){ $iz = 1; echo"<br><Br>"; } else { $iz++; echo "&nbsp;&nbsp;&nbsp;"; }
				}
				?>			
			</TD>
			</TR>
					
			<TR BGCOLOR="#f2f2f2">
			<Th width="200" style="padding-left:20px;">*First Name:</Th>
			<TD>
			<INPUT TYPE=TEXT NAME="first_name" size="30" VALUE="<?=$_POST['first_name']?>">
			</TD>
			</TR>
			
			<TR BGCOLOR="#f2f2f2">
			<Th width="200" style="padding-left:20px;">Last Name:</Th>
			<TD>
			<INPUT TYPE=TEXT NAME="last_name" size="30" VALUE="<?=$_POST['last_name']?>">
			</TD>
			</TR>
			
			<TR BGCOLOR="#f2f2f2">
			<Th width="200" style="padding-left:20px;">Email:</Th>
			<TD>
			<INPUT TYPE=TEXT NAME="email" size="40" VALUE="<?=$_POST['email']?>">
			</TD>
			</TR>
			
			<TR BGCOLOR="#f2f2f2">
			<Th width="200" style="padding-left:20px;">Phone:</Th>
			<TD>
			<INPUT TYPE=TEXT NAME="phone" size="15" VALUE="<?=$_POST['phone']?>">
			</TD>
			</TR>
			
			<TR BGCOLOR="#f2f2f2">
			<Th width="200" style="padding-left:20px;">Address 1</Th>
			<TD>
			<INPUT TYPE=TEXT NAME="address1" size="40" VALUE="<?=$_POST['address1']?>">
			</TD>
			</TR>
			
			<TR BGCOLOR="#f2f2f2">
			<Th width="200" style="padding-left:20px;">Address 2:</Th>
			<TD>
			<INPUT TYPE=TEXT NAME="address2" size="40" VALUE="<?=$_POST['address2']?>">
			</TD>
			</TR>
			
			<TR BGCOLOR="#f2f2f2">
			<Th width="200" style="padding-left:20px;">City:</Th>
			<TD>
			<INPUT TYPE=TEXT NAME="city" size="30" VALUE="<?=$_POST['city']?>">
			</TD>
			</TR>
			
			<TR BGCOLOR="#f2f2f2">
			<Th width="200" style="padding-left:20px;">State:</Th>
			<TD>
			<select name="state">
				<?
				$sel2 = "SELECT * FROM state";
				$res2 = doQuery($sel2);
				$num2 = mysql_num_rows($res2);
				$i2 = 0;
				while($i2<$num2)
				{
					$row2 = mysql_fetch_array($res2);
					?>
					<option <? if($_POST['state'] == $row2['state']){ ?> SELECTED <? } ?> value="<?=$row2['state']?>"><?=$row2['state']?></option>
					<?
					$i2++;
				}
				?>
			</select>		
			</TD>
			</TR>
			
			<TR BGCOLOR="#f2f2f2">
			<Th width="200" size="15" style="padding-left:20px;">Zip:</Th>
			<TD>
			<INPUT TYPE=TEXT NAME="zip" VALUE="<?=$_POST['zip']?>">
			</TD>
			</TR>
			
			<?
			if($_SETTINGS['international'] == '1'){
				?>
				<TR BGCOLOR="#f2f2f2">
				<Th width="200" style="padding-left:20px;">Country:</Th>
				<TD>
				<select name="country">
					<?
					$sel2 = "SELECT * FROM country";
					$res2 = doQuery($sel2);
					$num2 = mysql_num_rows($res2);
					$i2 = 0;
					while($i2<$num2)
					{
						$row2 = mysql_fetch_array($res2);
						?>
						<option <? if($_POST['country'] == $row2['country']){ ?> SELECTED <? } ?> value="<?=$row2['country']?>"><?=$row2['country']?></option>
						<?
						$i2++;
					}
					?>
				</select>	
				</TD>
				</TR>
				<?
			}
			?>
			
			
		</table>
		
		<div id="submit">
		<input type="hidden" name="xid" value="<?=$rowz['account_id']?>" />
		<a href="?VIEW=<?=$_GET['VIEW']?>&xid=<?=$rowz['account_id']?>">Back</a> &nbsp;&nbsp;&nbsp;
		<?
		echo "<INPUT TYPE=SUBMIT NAME=\"".strtoupper(str_replace(" ", "_", $button))."\" VALUE=\"$button\">";
		if (isset($_REQUEST['cid']))
		{
			echo "<INPUT TYPE=HIDDEN NAME=cid value=\"".$_REQUEST['cid']."\" />";
			echo "<INPUT TYPE=SUBMIT NAME=DELETECONTACT value=\"Delete\" onclick=\"return confirm('Are You Sure?');\">";
		}		
		?>
		</div>
	</FORM>
	<?
}
/*** ADD/EDIT PAYMENT INFO				********************************************************/
elseif(isset($_REQUEST['ADDPAYMENTINFO']) OR isset($_REQUEST['pid'])){
	//
	// GET THE PAYMENT INFO
	//
	if(isset($_GET['pid']))
	{
		$sel1 = "SELECT * FROM credit_card WHERE credit_card_id='".$_REQUEST['pid']."' AND active='1'";
		$res1 = doQuery($sel1);
		$_POST = mysql_fetch_array($res1);
		$button = "Update Payment Information";
	} else {
		$button = "Add Payment Information";
	}
	?>
	<FORM name="user" METHOD=POST ACTION="">
		<?
		echo tableHeader("Payment Information",2,'100%');	
		?>				
			<TR BGCOLOR="#f2f2f2">
			<TD width="200" style="padding-left:20px;">*Name On Card:</TD>
			<TD>
			<INPUT TYPE=TEXT NAME="name" VALUE="<?=$_POST['name']?>">
			</TD>
			</TR>	
			
			<TR BGCOLOR="#f2f2f2">
			<TD width="200" style="padding-left:20px;">*Credit Card #:</TD>
			<TD>
			<INPUT TYPE=TEXT NAME="number" size="20" VALUE="<?=$_POST['number']?>">
			</TD>
			</TR>	
			
			<TR BGCOLOR="#f2f2f2">
			<TD width="200" style="padding-left:20px;">*Experation Date:</TD>
			<TD>
			<select name="exp_m">
				<option <? if($_POST['exp_m'] == "01"){ ?> SELECTED <? } ?> value="01">01</option>
				<option <? if($_POST['exp_m'] == "02"){ ?> SELECTED <? } ?> value="02">02</option>
				<option <? if($_POST['exp_m'] == "03"){ ?> SELECTED <? } ?> value="03">03</option>
				<option <? if($_POST['exp_m'] == "04"){ ?> SELECTED <? } ?> value="04">04</option>
				<option <? if($_POST['exp_m'] == "05"){ ?> SELECTED <? } ?> value="05">05</option>
				<option <? if($_POST['exp_m'] == "06"){ ?> SELECTED <? } ?> value="06">06</option>
				<option <? if($_POST['exp_m'] == "07"){ ?> SELECTED <? } ?> value="07">07</option>
				<option <? if($_POST['exp_m'] == "08"){ ?> SELECTED <? } ?> value="08">08</option>
				<option <? if($_POST['exp_m'] == "09"){ ?> SELECTED <? } ?> value="09">09</option>
				<option <? if($_POST['exp_m'] == "10"){ ?> SELECTED <? } ?> value="10">10</option>
				<option <? if($_POST['exp_m'] == "11"){ ?> SELECTED <? } ?> value="11">11</option>
				<option <? if($_POST['exp_m'] == "12"){ ?> SELECTED <? } ?> value="12">12</option>
			</select>
			
			<?
			$y = date("Y");
			?>
			<select name="exp_y">
				<?
				for ($j=$y; $j<=$y+4; $j++) {
				?>
				<option <? if($_POST['exp_y'] == $j){ ?> SELECTED <? } ?> value="<?=$j?>"><?=$j?></option>
				<?
				}
				?>
			</select>
			</TD>
			</TR>	
			
			<TR BGCOLOR="#f2f2f2">
			<TD width="200" style="padding-left:20px;">*CVV Code:</TD>
			<TD>
			<INPUT TYPE=TEXT NAME="cvv" size="4" VALUE="<?=$_POST['cvv']?>">
			</TD>
			</TR>
			
		</table>
		<div id="submit">
		<input type="hidden" name="xid" value="<?=$_REQUEST['xid']?>" />
		<input type="hidden" name="pid" value="<?=$_REQUEST['pid']?>" />
		<input type="hidden" name="ADDPAYMENTINFO" value="1" />
		<a href="?VIEW=<?=$_GET['VIEW']?>&xid=<?=$_REQUEST['xid']?>">Back</a> &nbsp;&nbsp;&nbsp;
		<?
		if (isset($_REQUEST['pid']))
		{
			echo "<INPUT TYPE=SUBMIT NAME=DELETECREDIT value=\"Delete\" onclick=\"return confirm('Are You Sure?');\">";
		}
		echo "<INPUT TYPE=SUBMIT NAME=\"".strtoupper(str_replace(" ", "_", $button))."\" VALUE=\"$button\">";
		?>
		</div>
	</FORM>
	<?
}
/*** ADD/EDIT PERMISSION INFO				********************************************************/
elseif($_REQUEST['ADDNEWPERMISSION'] == '1' OR isset($_REQUEST['peid'])) {
	//
	// GET THE PERMISSION INFO
	//
	if(isset($_GET['peid']))
	{
		$sel1 = "SELECT * FROM user_permission WHERE permission_id='".$_REQUEST['peid']."' AND active='1'";
		$res1 = doQuery($sel1);
		$_POST = mysql_fetch_array($res1);
		$button = "Update Permission";
	} else {
		$button = "Add Permission";
	}
	?>
	<FORM name="user" METHOD=POST ACTION="">
		<?
		echo tableHeader("Permission Information",2,'100%');	
		?>				
			<TR BGCOLOR="#f2f2f2">
			<Th >*Name:</Th>
			<TD>
			<INPUT TYPE=TEXT NAME="name" VALUE="<?=$_POST['name']?>">
			</TD>
			</TR>	
			
			<?
			// IF ECOMMERCE MODULE INSTALLED
			//
			if(checkActiveModule('0000012')){
			?>
				<TR BGCOLOR="#f2f2f2">
				<Th >*Referrable:</Th>
				<TD>
				<?
				if($_POST['referrable'] == '1'){ $checked = " CHECKED "; }
				?>
				<INPUT TYPE='checkbox' NAME="referrable" <?=$checked?> VALUE="1">
				</TD>
				</TR>	
				
				
				<TR BGCOLOR="#f2f2f2">
				<Th>*Discount Type:</Th>
				<TD>
				<select name='discount_type'>
					<option <? if($_POST['discount_type'] == "None"){ ?>SELECTED<? } ?> value='None'>None</option>
					<option <? if($_POST['discount_type'] == "Rate"){ ?>SELECTED<? } ?> value='Rate'>Rate</option>
					<option <? if($_POST['discount_type'] == "Referral Based"){ ?>SELECTED<? } ?> value='Referral Based'>Referral Based</option>
				</select>
				</TD>
				</TR>
				
				<?
				if($_POST['discount_type'] == 'Rate'){
					?>
					<TR BGCOLOR="#f2f2f2">
					<Th >Discount:</Th>
					<TD>
					<small>Rate</small> <INPUT TYPE='textbox' NAME="discount" VALUE="<?=$_POST['discount']?>"> <small>Ex. "0.5" would be equal to 50%</small>
					</TD>
					</TR>	
					<?
				}
				?>
				
				<?
				if($_POST['discount_type'] == 'Referral Based'){
					?>
					<TR BGCOLOR="#f2f2f2">
					<Th >*Discount:</Th>
					<TD>
					Discount starts at <INPUT TYPE='textbox' style='width:50px;' NAME="discount" VALUE="<?=$_POST['discount']?>"> up to a maximum of <input type='textbox' style='width:50px' name='discount_max' value='<?=$_POST['discount_max']?>'>
					</TD>
					</TR>	
					
					<TR BGCOLOR="#f2f2f2">
					<Th >*Discount Maximum Dollar Amount:</Th>
					<TD>
					<INPUT TYPE='textbox' NAME="discount_maximum_dollar_amount" VALUE="<?=$_POST['discount_maximum_dollar_amount']?>">
					</TD>
					</TR>	
					<?
				}
				
				
				if($_POST['charge_method'] == 'Invoice and Charge'){ $inchselected = " SELECTED "; }
				if($_POST['charge_method'] == 'Invoice Only'){ $inselected = " SELECTED "; }
				?>
				<tr>
				<th>*Process Order With</th>
				<td>
					<select name='charge_method'>
						<option value='Invoice and Charge' <?=$inchselected?>>Invoice and Charge</option>
						<option value='Invoice Only' <?=$inselected?>>Invoice Only</option>
					</select>
				</td>
				</tr>
				
				<?
				
				if($_POST['quickbooks_checkout_method'] == 'Sales Receipt'){ $qb1selected = " SELECTED "; }				
				if($_POST['quickbooks_checkout_method'] == 'Invoice'){ $qb2selected = " SELECTED "; }
				if($_POST['quickbooks_checkout_method'] == 'None'){ $qb3selected = " SELECTED "; }
				?>
				
				<?
				if($_SETTINGS['quickbooks_active'] == 1){
					?>
					<tr>
					<th>Send to Quickbooks As</th>
					<td>
						<select name='quickbooks_checkout_method'>
							<option value='Sales Receipt' <?=$qb1selected?>>Sales Receipt</option>
							<option value='Invoice' <?=$qb2selected?>>Invoice</option>
							<option value='None' <?=$qb3selected?>>None</option>
						</select>
					</td>
					</tr>
					<?
					
					if($_POST['quickbooks_checkout_method'] == 'Invoice'){
						?>
						<tr>
						<th>Invoice Terms</th>
						<td>
							<input type='text' name='quickbooks_invoice_terms' value='<?=$_POST['quickbooks_invoice_terms']?>'>
						</td>
						</tr>
						
						<tr>
						<th>Invoice Due In</th>
						<td>
							<input type='text' name='quickbooks_invoice_due_days' value='<?=$_POST['quickbooks_invoice_due_days']?>'> Days
						</td>
						</tr>
						<?
					}					
				}
			} // IF CHECK ACTIVE MODULE
			?>
			
			<TR BGCOLOR="#f2f2f2">
			<Th >*Registration Setting:</Th>
			<TD>
			<select name='registration_setting'>
				<option <? if($_POST['registration_setting'] == "Registration Open"){ ?>SELECTED<? } ?> value='Registration Open'>Registration Open</option>
				<option <? if($_POST['registration_setting'] == "By Application Only"){ ?>SELECTED<? } ?> value='By Application Only'>By Application Only</option>
				<option <? if($_POST['registration_setting'] == "Registration Closed"){ ?>SELECTED<? } ?> value='Registration Closed'>Registration Closed</option>
			</select>
			</TD>
			</TR>

			<TR BGCOLOR="#f2f2f2">
			<Th >*Successful Registration Email:</Th>
			<TD>
			<select name='registration_email_id'>
				<?
				$select = "SELECT * FROM automated_email_contents WHERE active='1'";
				$result = doQuery($select);
				while($ro = mysql_fetch_array($result)){
					?>
					<option <? if($_POST['registration_email_id'] == $ro['email_id']){ ?> SELECTED <? } ?> value='<?=$ro['email_id']?>'><?=$ro['subject']?></option>
					<?
				}
				?>
			</select>
			</TD>
			</TR>			
			
			
			
		</table>
		
		<div id="submit">
		<input type="hidden" name="peid" value="<?=$_REQUEST['peid']?>" />
		<?
		// SUBMIT BUTTON
		echo "<INPUT TYPE=SUBMIT NAME=\"".strtoupper(str_replace(" ", "_", $button))."\" VALUE=\"$button\">";
		// DELETE BUTTON
		if (isset($_REQUEST['peid'])){
			echo "<INPUT TYPE=SUBMIT NAME=DELETE_PERMISSION value=\"Delete\" onclick=\"return confirm('Are You Sure?');\">";
		}
		?>
		</div>
	</FORM>
	<?
}
/*** VIEW USER PERMISSION HIERARCHY				********************************************************/
elseif($_REQUEST['PERMISSIONS'] == '1'){
	//
	// SORTABLE BOX
	//
	?>
	<div class="textcontent1">
		<h1>User Permissions</h1>
	</div>
	<br />
	<br />
	<?
	
	// HEADER
	echo tableHeaderid("Customers",6,"100%","list");
	echo "<thead><TR><th width='600'>Permission</th><th>Action</th></TR></thead><tbody>";
	echo "</tbody></table>";
	
	// List
	$select = 	"SELECT * FROM user_permission ".
				"WHERE ".
				"active='1' ".
				"".$_SETTINGS['demosqland']." ".
				"ORDER BY permission_level DESC";
		
	echo sortableList();	
	$res = doQuery($select);
	$num = mysql_num_rows($res);
	$i=0;
	$j=$num;
	$size = 15;
	if($num < $size){
		$remainder = $size - $num;
	}
	while ($row = mysql_fetch_array($res)){
		$default = "";
		if($i % 2) { $class = "odd"; } else { $class = "even"; }
		echo "<li class=\"".$class." selector\" id=\"".$row['permission_id']."\">";
			
			
			echo "<span style=\"display:inline-block; width:475px;\">Level {$j}: {$row["name"]} {$default}</span>";			
			
			echo "<FORM class=\"listform\" METHOD=GET ACTION=\"$_SERVER[PHP_SELF]\">";
			echo "<INPUT TYPE=HIDDEN NAME=peid VALUE=\"{$row["permission_id"]}\">";
			echo "<INPUT TYPE=HIDDEN NAME=VIEW VALUE=\"{$_GET["VIEW"]}\">";
			echo "<INPUT TYPE=HIDDEN NAME=\"".session_name()."\" VALUE=\"".session_id()."\">";
			echo "<INPUT TYPE=SUBMIT NAME=DELETE_PERMISSION VALUE=\"Delete\" onClick=\"return confirm('Are You Sure?');\">";
			echo " <INPUT TYPE=SUBMIT NAME=view VALUE=\"View\">";
			echo "</FORM>";
			
			
		echo "</li>";
		$i++;
		$j--;
	}
	echo "</ul>";
	
	
	if($remainder > 0){
		echo"<ul class=\"resultslist\">";
		$r = 0;
		while($r<$remainder){
			echo"<li style=\"height:30px;\">&nbsp;</li>";
			$r++;
		}
		echo"</ul>";
	}
	
	
	?>
	<script type="text/javascript">
	$( "#sortable" ).bind( "sortstop", function(event, ui) {
	  var result = $('#sortable').sortable('toArray');
	  var resultstring = result.toString();
	  $.ajax({
		  type: 'POST',
		  url: 'modules/user_accounts/user_accounts.php',
		  data: { sortarray: resultstring, SORT_PERMISSION: '1' }
		});
		
	   $(ui.item).css("background-color","#f5f5f5");
	   $(ui.item).css("border-top","1px solid #eeeeee");
	   $(ui.item).css("border-right","1px solid #eeeeee");
	   $(ui.item).css("border-bottom","0px solid #eeeeee");
	   $(ui.item).css("border-left","0px solid #eeeeee");
	   $(ui.item).css("cursor","-moz-grab");
		
	});
	
	$( "#sortable" ).bind( "sortstart", function(event, ui) {
	  $(ui.item).css("background-color","#f3f8ff");
	  $(ui.item).css("border","2px solid #89a8d8");
	  $(ui.item).css("cursor","-moz-grabbing");
	});
	
	</script>
	<div class="pagination">&nbsp;</div>
	<?
}
/*** SEARCH/VIEW ACCOUNTS				********************************************************/
else
{
	//
	// Search box
	//
	?>
	<div class="textcontent">
		<?
		$sel = "SELECT * FROM user_account WHERE active='1'";
		$res = doQuery($sel);
		$num = mysql_num_rows($res);
		?>
		<h1>Customers (<?=$num?>)</h1>
		<?			
		echo "<FORM METHOD=GET>";
		echo "<INPUT TYPE=HIDDEN NAME=\"VIEW\" VALUE=\"".$_GET["VIEW"]."\">";
		echo "<INPUT TYPE=HIDDEN NAME=\"SUB\" VALUE=\"".$_GET["SUB"]."\">";
		echo "<INPUT TYPE=HIDDEN NAME=\"".session_name()."\" VALUE=\"".session_id()."\">";
		echo "<INPUT TYPE=TEXT NAME=\"KEYWORDS\" VALUE=\"{$_GET["KEYWORDS"]}\">";
		echo " <SELECT NAME=\"COLUMN\">";
			echo "<OPTION VALUE=\"user_account.account_id\"".selected($_GET["COLUMN"],"user_account.account_id").">Customer Number</OPTION>";
			echo "<OPTION VALUE=\"user_account.name\"".selected($_GET["COLUMN"],"user_account.name").">Customer Name</OPTION>";			
			
			// CAR INSURANCE
			if(checkActiveModule('0000010')){
				echo "<OPTION VALUE=\"car_insurance_operators.dl\"".selected($_GET["COLUMN"],"car_insurance_operators.dl").">Drivers License</OPTION>";
				echo "<OPTION VALUE=\"car_insurance_operators.phone\"".selected($_GET["COLUMN"],"car_insurance_operators.phone").">Phone</OPTION>";
				echo "<OPTION VALUE=\"car_insurance_applications.application_id\"".selected($_GET["COLUMN"],"car_insurance_applications.application_id").">Application Id</OPTION>";
				echo "<OPTION VALUE=\"car_insurance_premium_finance_contract.contract_id\"".selected($_GET["COLUMN"],"car_insurance_premium_finance_contract.contract_id").">Contract Id</OPTION>";
				echo "<OPTION VALUE=\"car_insurance_operators.request_id\"".selected($_GET["COLUMN"],"car_insurance_operators.request_id").">Request Id</OPTION>";				
			}
			
			echo "<OPTION VALUE=\"user_account.username\"".selected($_GET["COLUMN"],"user_account.username").">Username</OPTION>";
			echo "<OPTION VALUE=\"user_account.email\"".selected($_GET["COLUMN"],"user_account.email").">Email</OPTION>";
		echo "</SELECT>";
		echo " <INPUT TYPE=SUBMIT NAME=search VALUE=\"Search\"> <INPUT TYPE=BUTTON NAME=NONE VALUE=\"Reset\" ONCLICK=\"document.location = '{$_SERVER["PHP_SELF"]}?VIEW={$_GET["VIEW"]}&".SID."';\">";
		echo "</FORM>";
		?>
	</div>
	<br />
	<br />
	<?
	//
	// List USER ACCOUNTS
	//
	if ($_GET['KEYWORDS']!=""){
		$q = "AND {$_GET['COLUMN']} like '%{$_GET['KEYWORDS']}%'";
	}
	$page = 1;
	$size = 15;	 
	
	$select = 	"SELECT ";
	$select .= 	"user_account.account_id AS account_id ";
	$select .=  ",user_account.user_permission AS user_permission";
	$select .= 	",user_account.name AS name ";
	$select .= 	",user_account.username AS username ";
	$select .= 	",user_account.email AS email ";
	$select .= 	",user_account.created AS created ";				
	$select .= 	"FROM user_account ";
	
	// CAR INSURANCE TABLE JOINS
	if(checkActiveModule('0000010') AND $q != ""){
		$select .= 	"LEFT JOIN car_insurance_applications ON car_insurance_applications.account_id=user_account.account_id ";
		$select .= 	"LEFT JOIN car_insurance_premium_finance_contract ON car_insurance_premium_finance_contract.account_id=user_account.account_id ";
		$select .= 	"LEFT JOIN car_insurance_general_request_form ON car_insurance_general_request_form.account_id=user_account.account_id ";
		$select .= 	"LEFT JOIN car_insurance_operators ON car_insurance_operators.account_id=user_account.account_id ";
	}		
	
	$select	.=	"WHERE ";
	$select .= 	"user_account.parent='0' AND ";				
	$select .= 	"user_account.active='1' ";	
	$select .= 	"$q ";
	$select .= 	"".$_SETTINGS['demosqland']."";
	$select .= 	"GROUP BY user_account.account_id ORDER BY user_account.created DESC ";
	$total_records = mysql_num_rows(doQuery($select)); 
	
	if (isset($_GET['page'])){
		$page = (int) $_GET['page'];
	}
	 
	$pagination = new Pagination();
	$pagination->setLink("index.php?VIEW=".$_REQUEST['VIEW']."&page=%s");
	$pagination->setPage($page);
	$pagination->setSize($size);
	$pagination->setTotalRecords($total_records);
	 
	$select2 = 	$select." ".$pagination->getLimitSql()."";
		
	//echo $select2;
	
	echo tableHeaderid("Customers",7,"100%","list");
	echo "<thead><TR>";
	echo "<th>Customer Number</th>";
	echo "<th style='width:250px;'>Customer Name</th>";
	echo "<th>Email</th>";
	echo "<th>Type</th>";
	echo "<th>Created</th>";
	echo "<th>Action</th>";
	echo "</TR></thead><tbody>";
	
	$res = doQuery($select2);
	$rnum = mysql_num_rows($res);
	//$remainder = $size - $rnum;
	
	
	$i=0;
	while ($row = mysql_fetch_array($res)){
		if($i % 2) { $class = "odd"; } else { $class = "even"; }
		echo "<TR class=\"$class\">";
		echo "<TD>".$row["account_id"]."</TD>";
		echo "<TD>".$row["name"]."</TD>";	
		echo "<TD>".$row["email"]."</TD>";		
		echo "<TD>".lookupDbValue('user_permission','name',$row["user_permission"],'permission_id')."</TD>";		
		echo "<TD>".FormatTimeStamp($row["created"])."</TD>";
		echo "<TD nowrap ALIGN=\"LEFT\">";
			echo "<FORM METHOD=GET ACTION=\"$_SERVER[PHP_SELF]\">";
			echo "<INPUT TYPE=HIDDEN NAME=xid VALUE=\"".$row["account_id"]."\">";
			echo "<INPUT TYPE=HIDDEN NAME=VIEW VALUE=\"".$_GET["VIEW"]."\">";			
			
			// IF CAR INSURANCE MODULE THEN ADD PAYMENT BUTTON
			if(checkActiveModule('0000010')){
				echo "<INPUT TYPE=SUBMIT NAME=CARINSURANCEPAYMENT VALUE=\"Make A Payment\"> ";			
			}
			
			// DELETE BUTTON
			echo "<INPUT TYPE=SUBMIT NAME=DELETE VALUE=\"Delete\" onClick=\"return confirm('Are You Sure?');\"> ";	
			
			// VIEW ACCOUNT BUTTON
			if(checkActiveModule('0000010')){
				echo "<INPUT TYPE=SUBMIT NAME=view VALUE=\"View Account / Insurance Cards\"> ";
			} else {
				echo "<INPUT TYPE=SUBMIT NAME=view VALUE=\"View\"> ";
			}
			
			
			echo "</FORM>";
		echo "</TD>";
		echo "</TR>";
		$i++;
	}
	/*
	if($remainder > 0){
		$r = 0;
		while($r<$remainder){
			echo"<tr><td colspan=\"5\">&nbsp;</td></tr>";
			$r++;
		}
	}
	*/
	echo "</tbody></TABLE>";
	$navigation = $pagination->create_links();
	echo $navigation;
}
?>
