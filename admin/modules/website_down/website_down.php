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


/**
 *
 * AJAX SORT FIELDS
 *
 */
if (isset($_POST['SORT_FIELDS']))
{
	// FOR AJAX
	@require_once '../../../includes/config.php';
	
	//
	// GET SORT ARRAY
	//
	$sortarray = $_POST['sortarray'];
	$sortarray = explode(",",$sortarray);
	
	// GET BOTTOM MOST SORT LEVEL
	$select = "SELECT sort_level FROM form_field_relational WHERE form_id='1' ORDER BY sort_level DESC";
	$result = doQuery($select);
	// THE NUMBER OF FIELDS
	$num = mysql_num_rows($result);
	$i = 1;
	foreach($sortarray AS $field){
		$select = "UPDATE form_field_relational SET sort_level='".$i."' WHERE field_id='".$field."'";
		echo $select."<br>";
		$result = doQuery($select);
		$i++;
	}
	
	echo "true";
	exit;
}

/**
 *
 * CALL CLASSES
 *
 */
$Contact = new Contact();
$Settings = new Settings();

// FORM ID
$form_id = '1';

/**
 *
 * REPORT
 *
 */
report($_REQUEST['REPORT'],$_REQUEST['SUCCESS']);

/**
 *
 * ADD NEW FIELD
 *
 */
if($_POST['ADD_FIELD'] != "")
{
	$error = 0;
	if($_POST['label'] == "" ){ $error = 1; ReportError("Enter a label for the field."); }
	
	if($error == 0){
	
		$_POST = escape_smart_array($_POST);			
	
		// INSERT FIELD
		$nextId = nextId('form_fields');
		$select = 	"INSERT INTO form_fields SET ".
					"label='".$_POST['label']."',".
					"type='".$_POST['type']."',".
					"active='1'";		
		doQuery($select);
		
		// INSERT RELATIONAL
		$select =	"INSERT INTO form_field_relational SET ".
					"form_id='".$form_id."',".
					"field_id='".$nextId."',".
					"required='".$_POST['required']."'";
		doQuery($select);
		
		$report = "Field Created";
		$success = "1";
		header("Location: {$_SERVER["PHP_SELF"]}?VIEW=".$_REQUEST["VIEW"]."&SUB=".$_REQUEST['SUB']."&feid=".$nextId."&REPORT=".$report."&SUCCESS=".$success."");
		exit();
	}
}

/**
 *
 * UPDATE FIELD
 *
 */
if($_POST['UPDATE_FIELD'] != "")
{
	$error = 0;
	if($_POST['label'] == "" ){ $error = 1; ReportError("Enter a label for the field."); }
		
	if($error == 0){
	
		$_POST = escape_smart_array($_POST);
		
		// UPDATE FIELD
		$select = 	"UPDATE form_fields SET ".
					"label='".$_POST['label']."',".
					"type='".$_POST['type']."' ".
					"WHERE field_id='".$_POST['feid']."'";
		doQuery($select);
		
		// UPDATE RELATIONAL 
		$select =	"UPDATE form_field_relational SET ".
					"required='".$_POST['required']."' ".
					"WHERE form_id='".$form_id."' AND field_id='".$_POST['feid']."'";
		doQuery($select);
		
		$report = "Field Updated";
		$success = "1";
		header("Location: {$_SERVER["PHP_SELF"]}?VIEW=".$_REQUEST["VIEW"]."&SUB=".$_REQUEST['SUB']."&feid=".$_POST['feid']."&REPORT=".$report."&SUCCESS=".$success."");
		exit();
	}
}

/**
 *
 * DELETE FIELD
 *
 */
if($_POST['DELETE_FIELD'] != "")
{
	// DELETE FIELD
	doQuery("DELETE * FROM form_fields WHERE field_id='".$_REQUEST['feid']."'");
	
	// DELDTE RELATIONALS
	doQuery("DELETE * FROM form_field_relational WHERE field_id='".$_REQUEST['feid']."'");
	
	$report = "Field Deleted";
	$success = "1";
	header("Location: {$_SERVER["PHP_SELF"]}?VIEW=".$_REQUEST["VIEW"]."&SUB=".$_REQUEST['SUB']."&REPORT=".$report."&SUCCESS=".$success."");
	exit();
}

/**
 *
 * UPDATE SETTINGS
 *
 */
if($_POST['UPDATE_SETTINGS'] != "")
{
	// LOOP THROUGH SETTINGS
	$sel = "SELECT * FROM settings WHERE active='1' AND group_id='6'";
	$res = doQuery($sel);
	$num = mysql_num_rows($res);
	$i = 0;
	while($i<$num)
	{
		$row = mysql_fetch_array($res);		
		// UPDATE SETTING
		$Settings->updateSetting($row);
		$i++;
	}

	$report = "Settings Updated";
	$success = "1";
	header("Location: {$_SERVER["PHP_SELF"]}?VIEW=".$_REQUEST["VIEW"]."&SUB=".$_REQUEST['SUB']."&REPORT=".$report."&SUCCESS=".$success."");
	exit();
}

/**
 *
 * ADD NEW FIELD
 *
 */
if ($_REQUEST['SUB'] == 'ADDNEWFIELD' OR $_REQUEST['feid'] != "")
{
	if (isset($_REQUEST["feid"]))
	{
		$select = 	"SELECT * FROM form_fields ".
					"LEFT JOIN form_field_relational ON form_field_relational.field_id=form_fields.field_id ".
					"WHERE ".
					"form_fields.field_id='".$_REQUEST["feid"]."'";
		$res = doQuery($select);
		
		$_POST = mysql_fetch_array($res);
		$_POST['label'] = form_encode($_POST['label']);		
		
		$button = "Update Field";
		$doing = "Field";
	} else {
		$button = "Add Field";
		$doing = "New Field";
	}
	?>
	<form name="fieldform" id="fieldform" METHOD="POST" ACTION="" enctype="multipart/form-data">
	
		<?
		echo tableHeader("$doing: ".$_POST['label']."",2,'100%');
		?>
		
			<TR BGCOLOR="#f2f2f2">
			<Th width="200" height="40" style="padding-left:20px;">*Label</Th>
			<TD>
			<INPUT TYPE=TEXT NAME="label" VALUE="<?=$_POST['label']?>" />
			</TD>
			</TR>
			
			<TR BGCOLOR="#f2f2f2">
			<Th width="200" height="40" style="padding-left:20px;">Required</Th>
			<TD>
			<INPUT TYPE=checkbox NAME="required" <? if($_POST['required'] == '1'){ ?> CHECKED <? }?> VALUE="1" />
			</TD>
			</TR>
			
			<TR BGCOLOR="#f2f2f2">
			<Th width="200" height="40" style="padding-left:20px;">Type</Th>
			<TD>
			<select name="type">
				
				<option value="text" <? if($_POST['type'] == 'text'){ ?> SELECTED <? } ?>>Textbox</option>
				<option value="textarea" <? if($_POST['type'] == 'textarea'){ ?> SELECTED <? } ?>>Text Area</option>
				<option value="captcha" <? if($_POST['type'] == 'captcha'){ ?> SELECTED <? } ?>>Captcha Validation</option>
							
			</select>
			</TD>
			</TR>
			
		</table>
		
		<?		
		//
		// SUBMIT BOX
		//
		?>
		<div id="submit">
			<a href="?VIEW=<?=$_GET['VIEW']?>">Back</a> &nbsp;&nbsp;&nbsp;
			<?		
			// SUBMIT FORM
			echo "<INPUT TYPE=SUBMIT NAME=\"".strtoupper(str_replace(" ", "_", $button))."\" VALUE=\"$button\">";
			echo "<INPUT TYPE=HIDDEN NAME=\"VIEW\" VALUE=\"".$_REQUEST["VIEW"]."\">";
			echo "<INPUT TYPE=HIDDEN NAME=\"SUB\" VALUE=\"".$_REQUEST["SUB"]."\">";
			
			if(isset($_REQUEST['feid'])){
				echo "<INPUT TYPE=HIDDEN NAME=\"feid\" VALUE=\"".$_REQUEST["feid"]."\">";				
				echo "<INPUT TYPE=SUBMIT NAME=DELETEFIELD value=\"Delete\" onclick=\"return confirm('Are You Sure?');\">";
			}
			?>		
		</div>
		
	</form>
	
<?
} elseif($_REQUEST['SUB'] == 'CONTACTFORM') {	
//
// SORTABLE CONTACT FIELDS
//
	?>
	<div class="textcontent1">
		<h1>Contact Form</h1>
	</div>
	<br />
	<br />
	
	<?
	// HEADER
	echo tableHeaderid("Contact Form",6,"100%","list");
	echo "<thead><TR><th width='600'>Label</th><th>Action</th></TR></thead><tbody>";
	echo "</tbody></table>";
		
	// GET FIELDS THROUGH RELATIONSHIPS
	$select = 	"SELECT * FROM form_field_relational WHERE form_id='1' ORDER BY sort_level ASC";
	
	echo sortableList();	
	$res = doQuery($select);
	$num = mysql_num_rows($res);
	$i=0;
	while ($i<$num){
		$rower = mysql_fetch_array($res);
		
		$sel1 = "SELECT * FROM form_fields WHERE field_id='".$rower['field_id']."'";
		$res1 = doQuery($sel1);
		$row1 = mysql_fetch_array($res1);
		
		if($i % 2) { $class = "odd"; } else { $class = "even"; }		
		if($rower['required'] == '1'){ $row1['label'] = "*".$row1['label']; }		
		echo "<li class=\"".$class." selector\" id=\"".$row1['field_id']."\"> <span>".$row1['label']."</span>";
			 
		// TOP LEVEL FORM
		echo "<FORM class=\"listform\" METHOD=GET ACTION=\"$_SERVER[PHP_SELF]\">";
		echo "<INPUT TYPE=HIDDEN NAME=feid VALUE=\"{$row1["field_id"]}\">";
		echo "<INPUT TYPE=HIDDEN NAME=VIEW VALUE=\"{$_GET["VIEW"]}\">";
		echo "<INPUT TYPE=HIDDEN NAME=SUB VALUE=\"{$_GET["SUB"]}\">";
		
		if($rower['wes_required'] != '1'){
			echo "<INPUT TYPE=SUBMIT NAME=DELETE_FIELD VALUE=\"Delete\" onClick=\"return confirm('Are You Sure?');\">";
		}
		
		echo " <INPUT TYPE=SUBMIT NAME=view VALUE=\"View\">";
		echo "</FORM>";
			
		echo "</li>";
		
		$i++;
	}	
	echo "</ul>";	
	?>
	<script>
		// AJAX REQUEST SORT TOP LEVEL
		
		$( "#sortable" ).bind( "sortstart", function(event, ui) {
			//$(ui.item).css("background-color","#ffffff");
			$(ui.item).css("background-color","#f3f8ff");
			$(ui.item).css("border","2px solid #89a8d8");
			$(ui.item).css("cursor","-moz-grabbing");
		});
		
		$( "#sortable" ).bind( "sortstop", function(event, ui) {
		  var result = $('#sortable').sortable('toArray');
		  var resultstring = result.toString();
		  
		  $.ajax({
			  type: 'POST',
			  url: 'modules/contact/contact.php',
			  data: { sortarray: resultstring, SORT_FIELDS: '1' }
			});
			//$(ui.item).css("background-color","transparent");
			$(ui.item).css("background-color","#f5f5f5");
			$(ui.item).css("border-top","1px solid #eeeeee");
			$(ui.item).css("border-right","1px solid #eeeeee");
			$(ui.item).css("border-bottom","0px solid #eeeeee");
			$(ui.item).css("border-left","0px solid #eeeeee");
			$(ui.item).css("cursor","-moz-grab");
		});	
	</script>
	<div class="pagination">&nbsp;</div>
	<?
} else {
//
// CONTACT SETTINGS
//

		$button = "Update Settings";
		$doing = "Contact Settings";

	?>
	<FORM method="post" enctype="multipart/form-data" ACTION="<?=$_SERVER["PHP_SELF"]?>?VIEW=<?=$_GET["VIEW"]?>" name="settingsform" id="settingsform">
	
		<?
		echo tableHeader("$doing ".$_POST['name']."",2,'100%');
		?>
		
			<?
			$sela = "SELECT * FROM settings WHERE active=1 AND group_id='6' ORDER BY type ASC";

			$resa = doQuery($sela);
			$numa = mysql_num_rows($resa);
			$ja = 0;
			
			//echo $sela;
			
			while($ja<$numa){
				$rowa = mysql_fetch_array($resa);
				
				$Settings->displaySettingField($rowa,0,$ja);
				
				$ja++;
			}

			?>		
						
		</table>
		<?		
		//
		// Submit FORM
		//
		?>
		<div id="submit">
			<a href="?VIEW=<?=$_GET['VIEW']?>">Back</a> &nbsp;&nbsp;&nbsp;
			<?		
			echo "<INPUT TYPE=SUBMIT NAME=\"".strtoupper(str_replace(" ", "_", $button))."\" VALUE=\"$button\">";
			?>		
		</div>
	</form>

<?
}
?>