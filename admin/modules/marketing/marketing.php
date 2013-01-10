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

//ini_set('display_errors', 1); 
//error_reporting(E_ERROR);
//error_reporting(E_ALL);

/***	AJAX OPT OUT			********************************************************/
if (isset($_POST['OPT_OUT'])){
	// FOR AJAX
	@require_once '../../../includes/config.php';
	
	//
	$account_id = $_POST['account_id'];
	$update = "UPDATE user_account SET birthday_promo_opt_in='0' WHERE account_id='".$account_id."'";
	doQuery($update);
	
	echo "true";
	die();
	exit();
}

/***	AJAX OPT IN			********************************************************/
if (isset($_POST['OPT_IN'])){
	// FOR AJAX
	@require_once '../../../includes/config.php';
	
	//
	$account_id = $_POST['account_id'];
	$update = "UPDATE user_account SET birthday_promo_opt_in='1' WHERE account_id='".$account_id."'";
	doQuery($update);
	
	echo "true";
	die();
	exit();
}

// CALL CLASSES
$Marketing = new Marketing();
$Settings = new Settings();

// UPDATE SETTINGS
if($_POST['UPDATE_SETTINGS'] != "")
{

	//
	// LOOP THROUGH SETTINGS
	// 
	$sel = "SELECT * FROM settings WHERE active='1' AND (group_id='3' OR group_id='7' OR group_id='11')";
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

// SEND BDAY EMAIL
if($_POST['SENDBIRTHDAYPROMO'])
{
	global $_SETTINGS;
	global $_SESSION;
	
	$currentYear 	= date("Y");
	$currentMonth 	= date("m");
	$nextMonth 		= date("m",strtotime("+1 months"));
	$currentDay 	= date("d");
	$tomorrow 		= date("d",strtotime("+1 days"));
	
	$message_html = lookupDbValue('automated_email_contents','html','Birthday Promotion','name');
	$subject = lookupDbValue('automated_email_contents','subject','Birthday Promotion','name');
	$from = lookupDbValue('automated_email_contents','from','Birthday Promotion','name');
	$template = lookupDbValue('automated_email_contents','template','Birthday Promotion','name');
	
	//$email_template = $_SETTINGS['ecommerce_email_template'];
	$original_email_html = file_get_contents("".$_SETTINGS['website']."themes/".$_SETTINGS['theme']."".$template."");
	
	// TESTING
	//echo "<br><br>TEMPLATE: ".$_SETTING['ecommerce_email_template'];
	//die($email_html);
	//exit();
	
	// LOOP VALID CUSTOMERS
	$custSelect = "	SELECT * FROM user_account WHERE dob != '0000-00-00' AND birthday_promo_opt_in='1' AND active='1'";
	$custResult = doQuery($custSelect);
	$custNum = mysql_num_rows($custResult);
	$i=0;
	
	while($custRow=mysql_fetch_array($custResult) AND $i < 1)
	{
		// IF CUSTOMER DIDN'T ALREADY USE A BIRTHDAY PROMO FOR THIS YEAR
		if(!strstr($custRow['used_birthday_promos'],$currentYear))
		{
			$dobArray = explode("-",$custRow['dob']); 
			$dobArray = explode("-",$custRow['dob']); 
			$now = strtotime("now"); 
			$birthday = strtotime("".date("Y")."-".$dobArray[1]."-".$dobArray[2]."");
			
			if($birthday <= $now)
			{
					
				// SEND BIRTHDAY EMAIL
				$to = $custRow['email'];
				//$to = 'karl@webksd.com';
				$from = $from;
				$subject = $subject;
				
				// PUT MESSAGE HTML INTO EMAIL HTML
				$email_html = str_replace("|message_html|","".$message_html."",$original_email_html);
				
				$email_html		=	str_replace("|title|",$subject,$email_html); 				// |title| REPLACE EMAIL TITLE WITH SUBJECT
				$email_html 	= 	str_replace("|name|",$name,$email_html);					// |name| REPLACE NAME WITH CUSTOMER NAME
				$email_html 	= 	str_replace("|NAME|",$name,$email_html);					// |NAME| REPLACE NAME WITH CUSTOMER NAME
				
				$formattedBirthday = TimestampIntoDate($custRow['dob']);
				
				$email_html 	= 	str_replace("|BIRTHDAY|",$formattedBirthday,$email_html);	// |NAME| REPLACE NAME WITH CUSTOMER NAME
				$email_html 	= 	str_replace("|date|","".date("m/d/Y")."",$email_html);	// |date| REPLACE DATE WITH CURRENT DATE
				
				@sendEmail($to,$from,$subject,$email_html);
				
				// MARK CUSTOMER ACCOUNT AS HAVING RECEIVED THIS YEARS BIRTHDAY PROMO
				$newUsedBirthdayPromos = $custRow['used_birthday_promos'] . " ".$currentYear."";
				
				/*
				$updateCustomer = 	"UPDATE user_account SET ".
									"used_birthday_promos='".$newUsedBirthdayPromos."' ".
									"WHERE account_id='".$custRow['account_id']."'";
				doQuery($updateCustomer);
				*/
				
			}						
		}
		$i++;
	}
	$report = "Birthday promotional emails sent.";
	$success = "1";
	header("Location: {$_SERVER["PHP_SELF"]}?REPORT=".$report."&SUB=BIRTHDAYPROMO&SUCCESS=".$success."&VIEW=".$_REQUEST['VIEW']."");
	exit;
}

// REPORT
report($_REQUEST['REPORT'],$_REQUEST['SUCCESS']);

if($_REQUEST['SUB'] == 'PROMOS' OR $_REQUEST['SUB'] == '')
{

	echo '<div class="textcontent">';
	$sel = "SELECT * FROM promotions WHERE active='1'";
	$res = doQuery($sel);
	$num = mysql_num_rows($res);		
	echo '	<h1>Promotions ('.$num.')</h1>';					
	echo "	<FORM METHOD=GET>";
	echo "		<INPUT TYPE=HIDDEN NAME=\"VIEW\" VALUE=\"".$_GET["VIEW"]."\">";
	echo "		<INPUT TYPE=HIDDEN NAME=\"SUB\" VALUE=\"".$_GET["SUB"]."\">";
	echo "		<INPUT TYPE=TEXT NAME=\"KEYWORDS\" VALUE=\"{$_GET["KEYWORDS"]}\">";
	echo " 		<SELECT NAME=\"COLUMN\">";
	echo "			<OPTION VALUE=\"lists.name\"".selected($_GET["COLUMN"],"lists.name").">List Name</OPTION>";			
	echo "			<OPTION VALUE=\"lists.list_id\"".selected($_GET["COLUMN"],"lists.list_id").">List Id</OPTION>";
	echo "		</SELECT>";
	echo " 		<INPUT TYPE=SUBMIT NAME=search VALUE=\"Search\"> <INPUT TYPE=BUTTON NAME=NONE VALUE=\"Reset\" ONCLICK=\"document.location = '{$_SERVER["PHP_SELF"]}?VIEW={$_GET["VIEW"]}';\">";
	echo "	</FORM>";		
	echo "</div><br /><br />";
	
	if ($_GET['KEYWORDS']!=""){
		$q = "AND {$_GET['COLUMN']} like '%{$_GET['KEYWORDS']}%'";
	}
	$page = 1;
	$size = 15;	 
	
	$select = 	"SELECT * ";	
	$select .= 	"FROM promotions ";
	$select	.=	"WHERE ";
	$select .= 	"promotions.active='1' ";	
	$select .= 	"$q ";
	$select .= 	"".$_SETTINGS['demosqland']."";

	$total_records = mysql_num_rows(doQuery($select)); 	
	if (isset($_GET['page'])){ $page = (int) $_GET['page'];	}	 
	$pagination = new Pagination();
	$pagination->setLink("index.php?VIEW=".$_REQUEST['VIEW']."&page=%s");
	$pagination->setPage($page);
	$pagination->setSize($size);
	$pagination->setTotalRecords($total_records);	 
	$select2 = 	$select." ".$pagination->getLimitSql()."";		
	
	echo tableHeaderid("Promos",7,"100%","list");
	echo "<thead><TR>";
	echo "<th>Promo Id</th>";
	echo "<th>Name</th>";
	echo "<th>Description</th>";
	echo "<th>Created</th>";
	echo "<th>Action</th>";
	echo "</TR></thead><tbody>";
	
	$res = doQuery($select2);
	$rnum = mysql_num_rows($res);
	
	$i=0;
	while ($row = mysql_fetch_array($res)){
		if($i % 2) { $class = "odd"; } else { $class = "even"; }
		echo "<TR class=\"$class\">";
		echo "	<TD>".$row["list_id"]."</TD>";
		echo "	<TD>".$row["name"]."</TD>";	
		echo "	<TD>".$row["description"]."</TD>";		
		echo "	<TD>".FormatTimeStamp($row["created"])."</TD>";
		echo "	<TD nowrap ALIGN=\"LEFT\">";
		echo "		<FORM METHOD=GET ACTION=\"".$_SERVER[PHP_SELF]."\">";
		echo "			<INPUT TYPE=HIDDEN NAME=xid VALUE=\"".$row["promotion_id"]."\">";
		echo "			<INPUT TYPE=HIDDEN NAME=VIEW VALUE=\"".$_GET["VIEW"]."\">";			
		echo "			<INPUT TYPE=SUBMIT NAME=DELETE VALUE=\"Delete\" onClick=\"return confirm('Are You Sure?');\"> ";	
		echo "			<INPUT TYPE=SUBMIT NAME=view VALUE=\"View\"> ";		
		echo "		</FORM>";
		echo "	</TD>";
		echo "</TR>";
		$i++;
	}
	
	// Static BIRTHDAY PROMO
	echo "<tr>";
	echo "		<td>BD</td>";
	echo "		<td>Birthday Promotion</td>";
	echo "		<td>Customer birthday promotion.</td>";
	echo "		<td> </td>";
	echo "		<td>";
	
	echo "		<FORM METHOD=GET ACTION=\"".$_SERVER[PHP_SELF]."\">";
	echo "			<INPUT TYPE=HIDDEN NAME=xid VALUE=\"\">";
	echo "			<INPUT TYPE=HIDDEN NAME=VIEW VALUE=\"".$_GET["VIEW"]."\">";
	echo "			<INPUT TYPE=HIDDEN NAME=SUB VALUE=\"BIRTHDAYPROMO\">";
	//echo "			<INPUT TYPE=SUBMIT NAME=DELETE VALUE=\"Delete\" onClick=\"return confirm('Are You Sure?');\"> ";	
	echo "			<INPUT TYPE=SUBMIT NAME=view VALUE=\"Open\"> ";		
	echo "		</FORM>";
	
	//echo "			<form method='get'>";
	//echo "				<input type='hidden' name='xid' value='ebl'>";
	//echo "				<input type='hidden' name='VIEW' value='".$_GET['VIEW']."'>";
	//echo "				<input type='submit' name='' id='' value='Download List' onClick=\"document.location='modules/lists/ebl.php';\"> ";
	//echo "			</form>";
	
	echo "		</td>";
	echo "</tr>";
	
	echo "</tbody></TABLE>";
	$navigation = $pagination->create_links();
	echo $navigation;


}

elseif($_REQUEST['SUB'] == 'BIRTHDAYPROMO')
{

	echo '	<div class="textcontent">
				<h1 style="width:350px;">Current Birthday Promo Recipients</h1>';

				/*
				//echo $_SESSION["session"]->admin->tableHeader("Current User Accounts",1,"100%");
				echo "<FORM METHOD=GET>";
				echo "<INPUT TYPE=HIDDEN NAME=\"VIEW\" VALUE=\"".$_GET["VIEW"]."\">";
				echo "<INPUT TYPE=HIDDEN NAME=\"SUB\" VALUE=\"".$_GET["SUB"]."\">";
				echo "<INPUT TYPE=HIDDEN NAME=\"".session_name()."\" VALUE=\"".session_id()."\">";
				echo "<INPUT TYPE=TEXT NAME=\"KEYWORDS\" VALUE=\"{$_GET["KEYWORDS"]}\">";
				echo " <SELECT NAME=\"COLUMN\">";
					echo "<OPTION VALUE=\"name\"".selected($_GET["COLUMN"],"title").">Title</OPTION>";
				echo "</SELECT>";
				echo " <INPUT TYPE=SUBMIT NAME=search VALUE=\"Search\"> <INPUT TYPE=BUTTON NAME=NONE VALUE=\"Reset\" ONCLICK=\"document.location = '{$_SERVER["PHP_SELF"]}?VIEW={$_GET["VIEW"]}&".SID."';\">";
				echo "</FORM>";
				//echo "<TR><TD COLSPAN=4 ALIGN=\"CENTER\" BGCOLOR=\"#EEEEEE\"><br></TD></TR>";
				//echo "</TABLE>";

				//echo '<Br><CENTER><a href="' . $_SERVER['PHP_SELF'] . '?VIEW=' . $_GET['VIEW'] . '&ADDNEW=1&' . SID . '" CLASS="new_link">&raquo; Add New User &laquo;</a></CENTER>';
				*/

	echo '	</div>
			<br /><br />';

	echo tableHeaderid("Birthday Promo",4,'100%');

	echo '	<FORM name="user" METHOD=POST ACTION="">
				<thead>
					<tr>
						<th class="header" style="text-align:left; padding-left:20px;">Opt-Out</th>
						<th class="header" style="text-align:left; padding-left:20px;">Name</th>
						<th class="header" style="text-align:left; padding-left:20px;">Email</th>
						<th class="header" style="text-align:left; padding-left:20px;">Birthday</th>
					</tr>
				</thead>
				<tbody>';
					
					
					$currentYear = date("Y");
					/*
					$currentMonth = date("m");
					$nextMonth =  date("m",strtotime("+1 months"));
					echo "Next Month: $nextMonth<Br><br>";
					$currentDay = date("d");
					$tomorrow = date("d",strtotime("+1 days"));
					echo "Tomorrow: $tomorrow<Br><br>";
					*/
					$custSelect = "	SELECT * FROM user_account WHERE dob != '0000-00-00' AND active='1' ORDER BY MONTH(dob) ASC, DAYOFMONTH(dob) ASC";
					$custResult = doQuery($custSelect);
					$custNum = mysql_num_rows($custResult);
					
					$i=0;
					
					while($custRow=mysql_fetch_array($custResult))
					{
						// IF CUSTOMER DIDN"T ALREADY USED BIRTHDAY PROMO FOR THIS YEAR
						if(!strstr($custRow['used_birthday_promos'],$currentYear)){
							$dobArray = explode("-",$custRow['dob']); 
							$now = strtotime("now"); 
							$birthday = strtotime("".date("Y")."-".$dobArray[1]."-".$dobArray[2]."");
							
							if($birthday <= $now)
							{
								echo "	<tr>
										<td>
											<input type='checkbox' id='check".$i."' value='".$custRow['account_id']."' ".($custRow['birthday_promo_opt_in'] == '1' ? ' CHECKED ' : ' ')." />
											<span id='span".$i."'>".($custRow['birthday_promo_opt_in'] == '1' ? ' IN ' : ' OUT ')."</span>
											<script type='text/javascript'>
												$('#check".$i."').click(function(){
													if($(this).is(':checked')){
														//AJAX OPT OUT
														$.post('modules/marketing/marketing.php',{OPT_IN:1,account_id:'".$custRow['account_id']."'},function(){
															$('#span".$i."').html(' IN ');
														});
													} else {
														//AJAX OPT OUT
														$.post('modules/marketing/marketing.php',{OPT_OUT:1,account_id:'".$custRow['account_id']."'},function(){
															$('#span".$i."').html(' OUT ');
														});
													}
												});
											</script>
										</td>
										<td>
											".$custRow['name']." 
										</td>
										<td>
											".$custRow['email']."
										</td>
										<td>
											".$dobArray[1]."/".$dobArray[2]."/".$dobArray[0]." <!--  | ".$birthday." < ".$now." -->
										</td>
									</tr>";
								//echo $custRow['dob']." | ".$dobArray[0]." ".$dobArray[1]." ".$dobArray[2]."<br>";
							}
							//array_push($custArray,$custRow);
						}
						$i++;
					}
				
				
	echo '		</tbody>
			</table>
			<div id="submit">';
			
				//if (isset($_REQUEST["sid"])) {
				//	echo "<INPUT TYPE=SUBMIT NAME=DELETE_SUBSCRIBER value=\"Delete\" onclick=\"return confirm('Are You Sure?');\">";
				//}
				echo "<INPUT TYPE=SUBMIT NAME=\"SENDBIRTHDAYPROMO\" VALUE=\"Send Birthday Promotion Email\">";
			
	echo '	</div>
		</form>';

}

// SETTINGS
elseif($_REQUEST['SUB'] == 'SETTINGS')
{

	$button = "Update Settings";
	$doing = "Marketing Settings";

	echo '	<FORM method="post" enctype="multipart/form-data" ACTION="'.$_SERVER["PHP_SELF"].'?VIEW='.$_GET["VIEW"].'&SUB='.$_GET['SUB'].'" name="settingsform" id="settingsform">';
	echo 	tableHeader("$doing ".$_POST['name']."",2,'100%');
	
			$sela = "SELECT * FROM settings WHERE active=1 AND (group_id='3' OR group_id='7' OR group_id='11') ORDER BY type ASC";

			$resa = doQuery($sela);
			$numa = mysql_num_rows($resa);
			$ja = 0;
			
			//echo $sela;
			
			while($ja<$numa){
				$rowa = mysql_fetch_array($resa);
				
				$Settings->displaySettingField($rowa,0,$ja);
				
				$ja++;
			}
			
	echo '</table>';
	
	// Submit FORM
	echo '	<div id="submit">
				<a href="?VIEW='.$_GET['VIEW'].'">Back</a> &nbsp;&nbsp;&nbsp;
				<INPUT TYPE=SUBMIT NAME=\"'.strtoupper(str_replace(" ", "_", $button)).'\" VALUE=\"'.$button.'\">
			</div>
		</form>';
}
?>