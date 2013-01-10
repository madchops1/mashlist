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
$AdminUsers = new AdminUsers();
report($_REQUEST['REPORT'],$_REQUEST['SUCCESS']);
	
/*** Remove An Account ***/
if (isset($_POST["DELETE"]) || isset($_GET["DELETE"]))
{
	doQuery("UPDATE admin SET active='0' WHERE accesslevel!='0' AND admin_id=".$_REQUEST["xid"]);
	header("Location: {$_SERVER["PHP_SELF"]}?VIEW={$_REQUEST["VIEW"]}&REPORT=Admin User Deleted&SUCCESS=1");
	exit();
}

/*** Add An Account ***/
if (isset($_POST["ADD_USER"]))
{	
	if (VerifyPost($_POST))
	{		
		if ($_POST["password1"]==$_POST["password2"])
		{
			$_POST["password"] = md5($_POST["password1"]);
			$_POST = escape_smart_array($_POST);
			
			// USER LEVEL	
			$accesslevel = "accesslevel='".$_POST['accesslevel']."',";			
			
			// SPECIAL PRIVILIGES
			$SPEC_PRIVS = "";
			$add = 0;
			$update = 0;
			foreach ($_ADMIN as $section){
				// And its CHECKED
				if ($_POST[strtoupper($section[2])."_ACCESS"]==1) {
					$SPEC_PRIVS .= $AdminUsers->ConcatSpecialPrivs($_POST["xid"],strtoupper($section[2])."_ACCESS",$update);
				} else {
					$SPEC_PRIVS .= $AdminUsers->RemoveSpecialPriv($_POST["xid"],strtoupper($section[2])."_ACCESS",$update);
				}
			}
			
			// insert client record
			$next = nextId('admin');
			$select =	"INSERT INTO admin SET ".
						"name='{$_POST["name"]}',".
						"username='{$_POST["username"]}',".
						"email='{$_POST["email"]}',".
						"phone='{$_POST["phone"]}',".
						"password='{$_POST["password"]}',".
						"active='1',".
						"special_privs='$SPEC_PRIVS',".
						"$accesslevel ".
						"created=NULL".
						"".$_SETTINGS['demosql']."";
			
			doQuery($select);
			
			// SEND EMAIL TO NEW ADMINISTRATOR
			$message = 	"<br>Hi ".$_POST['name'].", welcome to the ".$_SETTINGS['site_name']." administrative dashboard. You can login to the dashboard at ".
						"<a href='".$_SETTINGS['website']."admin/login.php'>".$_SETTINGS['website']."admin/login.php</a>.<br><br>".
						"These are your login credentials:<br><strong>".
						"USERNAME: ".$_POST['username']."<br>".
						"PASSWORD: ".$_POST['password1']."<br><br>";
			sendEmail($_POST['email'],$_SETTINGS['email'],'Your '.$_SETTINGS['site_name'].' administrator account has been created',$message);
			
			$report = "Admin User Created Successfully";
			header("Location: {$_SERVER["PHP_SELF"]}?REPORT={$report}&SUCCESS=1&xid=".$next."&VIEW={$_GET["VIEW"]}");exit;
		} else {
			ReportError("Your Passwords Do Not Match");
		}
	} else {
		ReportError("Please Fill In All Required Fields");
	}
}

/*** Update An Account ***/
if (isset($_POST["UPDATE_USER"]))
{
	$res = doQuery("SELECT * FROM admin WHERE admin_id=".$_POST["xid"]);
	$row = mysql_fetch_Array($res);
	$error = 0;
	$access = 0;
	
	// PASSWORD VALIDATION
	if ($_POST["password1"]!=""){
		if($_POST["password1"]!=$_POST["password2"]){
			$error = 1; $report = "Passwords do not match";
		} else {
			$password = ",password='".md5($_POST["password1"])."'";
		}
	}
	
	// USER LEVEL
	$accesslevel = "accesslevel='".$_POST['accesslevel']."' ";
		
	// USER PERMISSIONS / SPECIAL PRIVILEGES
	$SPEC_PRIVS = "";
	$add = 0;
	$update = 0;
	foreach ($_ADMIN as $section){
		// And its CHECKED
		if ($_POST[strtoupper($section[2])."_ACCESS"]==1) {
			$SPEC_PRIVS .= $AdminUsers->ConcatSpecialPrivs($_POST["xid"],strtoupper($section[2])."_ACCESS",$update);
		} else {
			$SPEC_PRIVS .= $AdminUsers->RemoveSpecialPriv($_POST["xid"],strtoupper($section[2])."_ACCESS",$update);
		}
	}
	
	// IF THE USER IS A SUPER ADMIN HE CAN UPDATE THE ACCOUNT
	if($_SESSION['session']->admin->accesslevel == "0"){ $access = 1; }	
	// IF THE ACCOUNT BEING UPDATED WAS A SUPER ADMIN ACCOUNT THE USER CANNOT UPDATE THE ACCOUNT
	//$res = doQuery("SELECT accesslevel FROM admin WHERE admin_id='".$_POST['xid']."'");
	//$ro = mysql_fetch_array($res);	
	if($ro['accesslevel'] == "0"){ $access = 0; $report = "You do not have permission to update this account";}	
	// UNLESS IT IS THE USERS ACCOUNT HE CAN UPDATE THE ACCOUNT	
	if($_SESSION['session']->admin->userid == $_POST['xid']){ $access = 1; }	

	if($error==0 AND $access==1 ){			
		// USER PERMISSIONS
		$specprivs = ",special_privs='$SPEC_PRIVS' ";	
		
		// update client record
		$select =	"update admin SET ".
					"name='{$_POST["name"]}',".
					"username='{$_POST["username"]}',".
					"email='{$_POST["email"]}',".
					"phone='{$_POST["phone"]}',".
					"$accesslevel ".
					"$specprivs ".
					"".$_SETTINGS['demosql']."".
					"$password ".
					"WHERE admin_id='".$_POST["xid"]."'";
		doQuery($select);
		
		// SEND PASSWORD CHANGE EMAIL
		if($password != ""){
			$message = 	"<br>Hi ".$_POST['name'].", your password has been changed through ".$_SETTINGS['site_name']." administrative dashboard. You can login to the dashboard at ".
						"<a href='".$_SETTINGS['website']."admin/login.php'>".$_SETTINGS['website']."admin/login.php</a>.<br><br>".
						"These are your new login credentials:<br><strong>".
						"USERNAME: ".$_POST['username']."<br>".
						"PASSWORD: ".$_POST['password1']."<br><br>";
			sendEmail($_POST['email'],$_SETTINGS['email'],'Your '.$_SETTINGS['site_name'].' administrator account password has been changed.',$message);
		}
		
		$report = "User updated";
		header("Location: {$_SERVER["PHP_SELF"]}?REPORT=".$report."&xid=".$_POST['xid']."&SUCCESS=1&VIEW=".$_GET['VIEW']."");
		exit;
	} else {
		ReportError($report);
	}
}

/*** UPDATE / Add An Account FORM ***/
if (isset($_GET["ADDNEW"]) || isset($_GET["xid"])){
	// get client to modify
	if (isset($_REQUEST["xid"])) {

		$select = 	"SELECT * FROM admin ".
					"WHERE ".
					"admin_id='".$_REQUEST["xid"]."' ".
					"".$_SESSION['demosqland']."";
					//" AND ".$_SETTINGS['demosql']."";
	
		//$res = doQuery("SELECT * FROM admin WHERE admin_id=".$_REQUEST["xid"]);
		
		$res = doQuery($select);
		
		$_POST = mysql_fetch_array($res);
		$button = "Update User";
	} else {
		$button = "Add User";
		$_POST["accesslevel"] = "1";
	}
	?>
	
	<FORM name="user" METHOD=POST ACTION="<?=$_SERVER["PHP_SELF"]?>?VIEW=<?=$_GET["VIEW"]?>&ADDNEW=1">
	<?
	echo tableHeader("User Information",2,'100%');
	?>
		
			<TR>
			<th height="40" style="padding-left:20px;">*Name:</th>
			<TD><INPUT TYPE=TEXT NAME=name VALUE="<?=$_POST["name"]?>"></TD>
			</TR>			
			
			<TR>
			<th width="200" height="40" style="padding-left:20px;">*Username:</th>
			<TD><INPUT TYPE=TEXT NAME="username" VALUE="<?=$_POST['username']?>"></TD>
			</TR>
			
			<TR>
			<th width="200" height="40" style="padding-left:20px;">*Email:</th>
			<TD><INPUT TYPE=TEXT NAME="email" VALUE="<?=$_POST['email']?>"></TD>
			</TR>
						
			<?
			$passwordEditable = 0;					
			// IF USER IS A SUPER ADMIN
			if($_SESSION["session"]->admin->accesslevel == '0' ){$passwordEditable = 1;}			
			// IF ADMIN ACCOUNT BEING VIEWED IS A SUPER ADIMIN
			if($_POST["accesslevel"] == '0' ){$passwordEditable = 0;}			
			// IF VIEWING THE USERS OWN ACCOUNT
			if($_SESSION["session"]->admin->userid == $_POST['admin_id']){$passwordEditable = 1;}	
			// IF ADDING A NEW ACCOUNT
			if($_POST['admin_id'] == ""){ $passwordEditable = 1;}
			if($passwordEditable == 1){
				?>
				<TR><th width="200" height="40" style="padding-left:20px;">*Password:</th><TD><INPUT TYPE=password NAME="password1" VALUE=""></TD></TR>
				<TR><th width="200" height="40" style="padding-left:20px;">*Confirm Password:</th><TD><INPUT TYPE=password NAME="password2" VALUE=""></TD></TR>
				<?
			}
			
			//
			// IF USER IS SUPER ADMIN, ADD THE USER LEVEL INPUT TO THE FORM
			//
			?>
			<tr>
			<th width="200" height="40" style="padding-left:20px;">User Level</th>
			<td>
				<?
				// IF THE VIEWER IS AN ADMINISTRATOR THEN ALLOW THEM THE ABILITY TO SET ADMINISTRATOR SETTINGS
				if($_SESSION["session"]->admin->accesslevel == '0'){
				?>
				<input type='radio' name='accesslevel' <? if($_POST['accesslevel'] == "0"){ ?>CHECKED<? }?> value='0'> Administrator <br><br>
				<?
				}
				?>
				<input type='radio' name='accesslevel' <? if($_POST['accesslevel'] == "1"){ ?>CHECKED<? }?> value='1'> User	
			</td>
			</tr>
			
			<?
			// IF VIEWER IS AN ADMINISTRATOR AND THE ACCOUNT IS A USER
			// OR IF THIS IS A NEW ACCOUNT		
			if(($_SESSION["session"]->admin->accesslevel == '0' AND $_POST['accesslevel'] == '1') OR $_POST['admin_id'] == ""){
				?>						
				<tr class="toggleropenidentifier">
				<th width="200" height="40" style="padding-left:20px;"><? info('Permissions allow you to control which sections of the administration an admin user can access.'); ?>User Permissions</th>
				<td>
					<a class="toggleridentifier tog left">Click to edit permissions.</a>			
				</td>
				</tr>
				
				<tr class="toggleidentifier">
				<th width="200" height="40" style="padding-left:20px;"><? info('Permissions allow you to control which sections of the administration an admin user can access.'); ?>User Permissions</th>
				<td>
					<a class="togglercloseidentifier tog left">Click to close permissions.</a>
				</td>
				</tr>
				<?	
				// FOREACH MODULE
				foreach ($_ADMIN as $section) {
					// IF MODULE ACTIVE
					if ($section[4] == 1) {
						?>
						<TR class="toggleidentifier">
						<th width="200" height="40" style="padding-left:20px;"><?=$section[0]?> Section</th>
						<TD width="">
						<?
						if($_REQUEST['ADDNEW'] == '1'){
							if(isset($_POST['ADD_ACCOUNT'])){
								$namer = strtoupper($section[2])."_ACCESS";
								//echo("page post POSTNAMER: ".$_POST[$namer]."<br>");
								if($_POST[$namer] == "1"){
									$checked = "CHECKED";
								} else {
									$checked = "";
								}
							} else {
								$checked = "CHECKED";
							}
							?>
							<INPUT TYPE=checkbox name="<?=strtoupper($section[2])?>_ACCESS" value="1" <?=$checked?>>
							<?
						} else {
							?>
							<INPUT TYPE=checkbox name="<?=strtoupper($section[2])?>_ACCESS" value="1"<?=in_array(strtoupper($section[2])."_ACCESS", explode(",",$_POST["special_privs"])) ? "CHECKED" : ""?>>
							<?
						}
						?>
						</TD>
					</TR>
					<?
					}
				}
			}// if access level
		?>
	</table>
	
	<script type="text/javascript">
	  //hide the all of the element with class msg_body
	  $(".toggleidentifier").hide();
	  //toggle the componenet with class msg_body
	  $(".toggleridentifier").click(function()
	  {
		$(".toggleidentifier").slideToggle('fast',callback);
	  });
	  
	  $(".togglercloseidentifier").click(function()
	  {
		$(".toggleidentifier").slideToggle('fast',callback1);
	  });
	  
	  function callback(){
		$(".toggleidentifier").css({'display' : ''});
		$(".toggleridentifier").css({'display' : 'none'});
		$(".toggleropenidentifier").css({'display' : 'none'});
		//$(".toggleridentifier").parent.parent.css({'display' : 'none'});
		return true;
	  }
	  
	  function callback1(){
		//$(".toggleidentifier").css({'display' : ''});
		$(".toggleridentifier").css({'display' : 'inline'});
		$(".toggleropenidentifier").css({'display' : ''});
		return true;
	  }
	</script>


	<?
	echo "<div id='submit'>";
	if (isset($_REQUEST["xid"])){
		echo "<INPUT TYPE=HIDDEN NAME='xid' VALUE='".$_REQUEST['xid']."'>";
	}
	echo "<INPUT TYPE=SUBMIT NAME=\"".strtoupper(str_replace(" ", "_", $button))."\" VALUE=\"$button\">";
	if (isset($_REQUEST["xid"])){
		$showDelete = 0;
		// SHOW DELETE IF USER IS ADMIN
		if($_SESSION['session']->admin->accesslevel == "0"){ $showDelete = 1;}
		// IF ACCOUNT VIEWED IS ADMIN DONT ALLOW DELETE
		if($_POST["accesslevel"] == '0' ){$showDelete = 0;}			
		// IF ACCOUNT VIEWED IS USER ACCOUNT
		if($_SESSION['session']->admin->userid == $_POST['xid']){ $showDelete = 1;}
		if($showDelete == 1){
			echo "<INPUT TYPE=SUBMIT NAME=DELETE value=\"Delete\" onclick=\"return confirm('Are You Sure?');\">";
		}
	}
	?>
	</div>
	</FORM>
	
	<?
}
/*** ELSE LIST USERS ***/
else
{
	/*** Search box ***/
	?>
	<div class="textcontent">
		<?
		$sel = "SELECT * FROM admin WHERE active='1'";
		$res = doQuery($sel);
		$num = mysql_num_rows($res);
		?>
		<h1>Users (<?=$num?>)</h1>
		<?
		
		//echo $_SESSION["session"]->admin->tableHeader("Current User Accounts",1,"100%");
		echo "<FORM METHOD=GET>";
		echo "<INPUT TYPE=HIDDEN NAME=\"VIEW\" VALUE=\"".$_GET["VIEW"]."\">";
		echo "<INPUT TYPE=HIDDEN NAME=\"SUB\" VALUE=\"".$_GET["SUB"]."\">";
		//echo "<INPUT TYPE=HIDDEN NAME=\"".session_name()."\" VALUE=\"".session_id()."\">";
		echo "<INPUT TYPE=TEXT NAME=\"KEYWORDS\" VALUE=\"{$_GET["KEYWORDS"]}\">";
		echo " <SELECT NAME=\"COLUMN\">";
			echo "<OPTION VALUE=\"name\"".selected($_GET["COLUMN"],"name").">Name</OPTION>";
			echo "<OPTION VALUE=\"email\"".selected($_GET["COLUMN"],"email").">Email</OPTION>";
		echo "</SELECT>";
		echo " <INPUT TYPE=SUBMIT NAME=search VALUE=\"Search\"> <INPUT TYPE=BUTTON NAME=NONE VALUE=\"Reset\" ONCLICK=\"document.location = '{$_SERVER["PHP_SELF"]}?VIEW={$_GET["VIEW"]}';\">";
		echo "</FORM>";
		//echo "<TR><TD COLSPAN=4 ALIGN=\"CENTER\" BGCOLOR=\"#EEEEEE\"><br></TD></TR>";
		//echo "</TABLE>";
		?>
	</div>
	<br /><br />
	<?	
	if ($_GET['KEYWORDS']!="") {
		$q = "AND {$_GET['COLUMN']} like '%{$_GET['KEYWORDS']}%'";
	}
	
	$page = 1;
	$size = 15;	 
	
	$select = 	"SELECT * FROM admin ".
				"WHERE ".
				"(active='1') AND (1=1 ".
				"$q ".
				"".$_SETTINGS['demosqland'].") ".
				"ORDER BY name,created DESC";
	
	$total_records = mysql_num_rows(doQuery($select)); 

	if (isset($_GET['page'])){
		$page = (int) $_GET['page'];
	}
	 
	// create the pagination class
	$pagination = new Pagination();
	$pagination->setLink("index.php?VIEW=users&page=%s");
	$pagination->setPage($page);
	$pagination->setSize($size);
	$pagination->setTotalRecords($total_records);
	 
	$select = 	"SELECT * FROM admin ".
				"WHERE ".
				"(active='1') AND (1=1 ".
				"$q ".
				"".$_SETTINGS['demosqland'].") ".
				"ORDER BY name,created DESC ".
				"".$pagination->getLimitSql()."";

	
	echo tableHeaderid("Customers",6,"100%","list");
	echo "<thead><TR><th>Id</th><th>Name</th><th>Email</th><th>Created</th><th>Type</th><th>Action</th></TR></thead><tbody>";
	
	$res = doQuery($select);
	$rnum = mysql_num_rows($res);
	$remainder = $size - $rnum;
	
	$i=0;
	while ($row = mysql_fetch_array($res)) {
		if($i % 2) { $class = "odd"; } else { $class = "even"; }
		echo "<TR class=\"$class\">";
		
		if($row['accesslevel'] == '0'){ $type = "Administrator"; }
		if($row['accesslevel'] == '1'){ $type = "Standard User"; }
		
		echo "<TD>{$row["admin_id"]}</TD>";
		echo "<TD>{$row["name"]}</TD>";
		echo "<TD>{$row["email"]}</TD>";
		echo "<TD>".FormatTimeStamp($row["created"])."</TD>";
		echo "<TD>".$type."</TD>";
		echo "<TD nowrap ALIGN=\"LEFT\">";
			echo "<FORM METHOD=GET ACTION=\"$_SERVER[PHP_SELF]\">";
			echo "<INPUT TYPE=HIDDEN NAME=xid VALUE=\"{$row["admin_id"]}\">";
			echo "<INPUT TYPE=HIDDEN NAME=VIEW VALUE=\"{$_GET["VIEW"]}\">";
			//echo "<INPUT TYPE=HIDDEN NAME=\"".session_name()."\" VALUE=\"".session_id()."\">";
			if($row['accesslevel'] != "0")
			{
				echo "<INPUT TYPE=SUBMIT NAME=DELETE VALUE=\"Delete\" onClick=\"return confirm('Are You Sure?');\">";
			}
			echo " <INPUT TYPE=SUBMIT NAME=view VALUE=\"Edit\">";
			echo "</FORM>";
		echo "</TD>";
		echo "</TR>";
		$i++;
	}
	if($remainder > 0){
		$r = 0;
		while($r<$remainder){
			echo"<tr><td colspan=\"6\">&nbsp;</td></tr>";
			$r++;
		}
	}
	echo "</tbody></TABLE>";
	$navigation = $pagination->create_links();
	echo $navigation; // will draw our page navigation
}
?>
