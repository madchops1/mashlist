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
include'';
$Events = new Events();	
report($_REQUEST['REPORT'],$_REQUEST['SUCCESS']);


/**
 *
 * Remove Event
 *
 */
if (isset($_REQUEST["DELETE_EVENT"]))
{
	doQuery("UPDATE events SET active='0' WHERE event_id=".$_REQUEST["eid"]." ".$_SETTINGS['demosqland']."");
	$report 	= "Event Deleted";
	$success 	= "1";
	header("Location: ".$_SERVER["PHP_SELF"]."?VIEW=".$_REQUEST["VIEW"]."&REPORT=".$report."&SUCCESS=".$success."");
	exit();
}

/**
 *
 * Add EVENT
 *
 */
if (isset($_POST["ADD_EVENT"]))
{
	$error = 0;
	
	// Validation
	if($_POST['title'] == ""){ ReportError("Please Fill In a Title"); $error = 1; }
	if($_POST['start_date'] == ""){ ReportError("Please Choose an Event Date"); $error = 1; }
	
	if($error == 0)	{
		$_POST = escape_smart_array($_POST);
		
		$date = DateTimeIntoTimestamp($_POST['start_date'],$_POST['start_time']);
		$end_date = DateTimeIntoTimestamp($_POST['end_date'],$_POST['end_time']);
		
		if($_POST['all_day'] != '1'){ $_POST['all_day'] = '0'; }
		
		//
		// IF ECOMMERCE INSERT EVENT AS A PRODUCT
		//
		/*
		if(checkActiveModule('0000012')){
			//
			// ECOMMERCE STUFF
			//
			if($_POST['price'] != '0' || $_POST['price'] != ''){			
				// INSERT PRODUCT
				$product_id = nextId('ecommerce_products');
				$select =	"INSERT INTO ecommerce_products SET ".
							"name='".$_POST['title']."',".
							"price='".$_POST['price']."',".
							"status='Published',".
							"product_type='Item',".
							"shipping='0',".
							"active=1,".
							"created=NULL".
							"".$_SETTINGS['demosql']."";			
				doQuery($select);
				
				// CREATE PRODUCT - EVENT RELATIONAL
				$select = 	"INSERT INTO event_product_relational SET event_id='".$event_id."',product_id='".$product_id."',active='1'";
				doQuery($select);
			}		
		}
		*/
		
		//
		// INSERT EVENT
		//
		$next = nextId('events');
		$sql = "INSERT INTO events SET ".
					"event_id='',".
					"admin_id='".$_SESSION['session']->admin->userid."',".
					"product_id='".$product_id."',".
					"category_id='".$_POST['category_id']."',".
					"title='".$_POST['title']."',".
					"image='".basename($_POST['image'])."',".
					"content='".$_POST['content']."',".
					"location='".$_POST['location']."',".	
					"address='".$_POST['address']."',".		
					"status='".$_POST['status']."',".
					"all_day='".$_POST['all_day']."',".
					"price='".$_POST['price']."',".
					"date='".$date."',".
					"end_date='".$end_date."',".
					"created=NULL".
					"".$_SETTINGS['demosql']."";
		$sqlresult = doQuery($sql);
		
		$report = "Event Created Successfully";
		$success = "1";
		header("Location: ".$_SERVER["PHP_SELF"]."?REPORT=".$report."&SUCCESS=".$success."&VIEW=".$_REQUEST["VIEW"]."&SUB=".$_REQUEST['SUB']."&eid=".$next."");
		exit;
	}
}

/**
 *
 * Update Event 
 *
 */
if (isset($_POST["UPDATE_EVENT"]))
{
	$error = 0;
	// VALIDATION
	if($_POST['title'] == ""){ ReportError("Please Fill In a Title"); $error = 1; }
	if($_POST['start_date'] == ""){ ReportError("Please Choose an Event Date"); $error = 1; }
	
	if($error == 0){
		$_POST = escape_smart_array($_POST);
		
		$date = DateTimeIntoTimestamp($_POST['start_date'],$_POST['start_time']);
		$end_date = DateTimeIntoTimestamp($_POST['end_date'],$_POST['end_time']);

		if($_POST['all_day'] != '1'){ $_POST['all_day'] = '0'; }
		
		// UPDATE
		$sql = 	"UPDATE events SET ".
				"category_id='".$_POST['category_id']."',".
				"title='".$_POST['title']."',".
				"image='".basename($_POST['image'])."',".
				"content='".$_POST['content']."',".
				"location='".$_POST['location']."',".	
				"address='".$_POST['address']."',".		
				"status='".$_POST['status']."',".
				"all_day='".$_POST['all_day']."',".
				"date='".$date."',".
				"end_date='".$end_date."' ".
				"WHERE event_id='".$_POST['eid']."' ".
				"".$_SETTINGS['demosql']."";
				
		//die("<br>$sql<br>");		
		//exit;
		$sqlresult = doQuery($sql);
		
		
		
		$report = "Event Updated Successfully";
		$success = "1";
		header("Location: ".$_SERVER["PHP_SELF"]."?REPORT=".$report."&SUCCESS=".$success."&VIEW=".$_GET["VIEW"]."&SUB=".$_REQUEST['SUB']."&eid=".$_POST["eid"]."");
		exit;
	}
}

/**
 *
 * Remove CATEGORY
 *
 */
if (isset($_POST["DELETE_CATEGORY"]))
{
	doQuery("DELETE FROM event_category WHERE category_id=".$_REQUEST["cid"]." ".$_SETTINGS['demosqland']."");
	$report = "Category Deleted Successfully";
	$success = "1";
	header("Location: ".$_SERVER["PHP_SELF"]."?VIEW=".$_REQUEST["VIEW"]."&SUB=".$_REQUEST['SUB']."&REPORT=".$report."&SUCCESS=".$success."");
	exit();
}

/**
 *
 * Add CATEGORY
 *
 */
if (isset($_POST["ADD_CATEGORY"]))
{
	$error = 0;
	//Validation
	if($_POST['name'] == ""){ ReportError("Please Fill In Category Name"); $error = 1; }
	
	if($error == 0){
		$next = nextId('event_category');
		$_POST = escape_smart_array($_POST);
		// nsert record
		$sql = 	"INSERT INTO event_category SET ".
				"category_id='',".
				"name='".$_POST['name']."',".
				"description='".$_POST['description']."',".
				"active='1',".
				"created=NOW()".
				"".$_SETTINGS['demosql']."";
		$result = doQuery($sql);
		
		$report = "Category Added Successfully";
		$success = "1";
		header("Location: ".$_SERVER["PHP_SELF"]."?REPORT=".$report."&SUCCESS=".$success."&VIEW=".$_GET["VIEW"]."&SUB=".$_REQUEST['SUB']."&cid=".$next."");
		exit;
	}
}

/**
 *
 * Update CATEGORY
 *
 */
if (isset($_POST["UPDATE_CATEGORY"]))
{
	$error = 0;
	// Validation
	if($_POST['name'] == ""){ ReportError("Please Fill In Category Name"); $error = 1; }

	if($error == 0){
		$_POST = escape_smart_array($_POST);
		// update record 
		$sql = "UPDATE event_category SET ".
					"name='".$_POST['name']."',".
					"description='".$_POST['description']."'".
					"".$_SETTINGS['demosql']."".
					" WHERE category_id='".$_POST['cid']."'".
					" ".$_SETTINGS['demosqland']."";
		$result = doQuery($sql);
		
		$report = "Category Updated Successfully";
		$success = "1";
		header("Location: ".$_SERVER["PHP_SELF"]."?REPORT=".$report."&SUCCESS=".$success."&VIEW=".$_GET["VIEW"]."&SUB=".$_REQUEST['SUB']."&cid=".$_POST['cid']."");
		exit;
	}
}

/**
 *
 * OPTOUT ALERT SUBSCRIBER
 *
 */
if (isset($_POST["OPTOUT_SUBSCRIBER"]))
{
	doQuery("UPDATE event_alert SET active='1' WHERE event_alert_id=".$_REQUEST["sid"]." ".$_SETTINGS['demosqland']."");
	$report = "Event Alert Subscriber Deleted";
	$success = "1";
	header("Location: ".$_SERVER["PHP_SELF"]."?REPORT=".$report."&SUCCESS=".$success."&VIEW=".$_GET["VIEW"]."&SUB=".$_REQUEST['SUB']."");
	exit();
}

/**
 *
 * OPTIN ALERT SUBSCRIBER
 *
 */
if (isset($_POST["OPTIN_SUBSCRIBER"]))
{
	doQuery("UPDATE event_alert SET active='1' WHERE event_alert_id=".$_REQUEST["sid"]." ".$_SETTINGS['demosqland']."");
	$report = "Event Alert Subscriber Deleted";
	$success = "1";
	header("Location: ".$_SERVER["PHP_SELF"]."?REPORT=".$report."&SUCCESS=".$success."&VIEW=".$_GET["VIEW"]."&SUB=".$_REQUEST['SUB']."");
	exit();
}

/**
 *
 * Add/Update Event
 *
 */
if ($_REQUEST['SUB'] == 'NEWEVENT' || $_REQUEST["eid"] != '')
{
	// get post to modify
	if (isset($_REQUEST["eid"])){
		$res = doQuery("SELECT * FROM events WHERE event_id=".$_REQUEST["eid"]." ".$_SETTINGS['demosql']."");
		$_POST = mysql_fetch_array($res);
		$button = "Update Event";
	} else {
		$button = "Add Event";
	}

	echo tableHeader("Event: ".$_POST['title']."",2,'100%');

	//
	// create a new WysiwygPro Instance
	//
	$editor = new wysiwygPro();
	
	$editor->editImages = 1;
	$editor->upload = 1;
	$editor->deleteFiles = 1;
	$editor->maxImageSize = '10000 MB';
	$editor->maxImageWidth = 100000;
	$editor->maxImageHeight = 100000;
	$editor->maxDocSize = '10000 MB';									

	$editor->imageDir = $_SERVER["DOCUMENT_ROOT"].$_SETTINGS['website_path']."uploads/";
	$editor->imageURL = $_SETTINGS['website']."uploads";																	

	//$editor->value = $ro["content"];
	$editor->displayFileBrowserJS('OpenFileBrowser');
	?>
	
	<FORM id="eventsform" name="eventsform" METHOD=POST ACTION="">
	
		<TR BGCOLOR="#f2f2f2">
		<TH style="padding-left:20px;">* Title<? info('The title of the event.'); ?></TH>
		<TD>
		<INPUT TYPE=TEXT style='width:300px;' NAME="title" VALUE="<?=$_POST['title']?>">
		</TD>
		</TR>
		
		<TR BGCOLOR="#f2f2f2">
		<TH style="padding-left:20px;">Image<? info('An image to go along with the event.'); ?></TH>
		<TD>
		<input type="text" name="image" value="<?=$_POST['image']?>" />
		<button type="button" onClick="OpenFileBrowser('image', function(url) {document.wesform.image.value=url;}, function() {return document.wesform.image.value;} )">
		Choose Image...
		</button>
		</TD>
		</TR>
		
		<TR BGCOLOR="#f2f2f2">
		<TH style="padding-left:20px;">Category <?=info('Select a category/group for your event.');?> </TH>
		<TD>
		<?
		selectTable('event_category','category_id','category_id','name','sort_level',$direction="ASC",$other=' -- SELECT CATEGORY -- ');		
		?>
		</TD>
		</TR>
				
		<?
		/**
		 *
		 * DATE
		 *
		 */
		if($_REQUEST['eid']){	
		
			$dateTimestamp = $_POST['date'];
			$endDateTimestamp = $_POST['end_date'];
			
			$_POST['start_time'] = TimestampIntoTime($dateTimestamp);
			$_POST['end_time'] = TimestampIntoTime($endDateTimestamp);					
			
			$_POST['start_date'] = TimestampIntoDate($dateTimestamp);
			$_POST['end_date'] = TimestampIntoDate($endDateTimestamp);			

		}
		?>
		<script type="text/javascript">
		$(function() {
			$("#datepicker").datepicker();
		});
		</script>	
		<TR BGCOLOR="#f2f2f2">
		<Th style="padding-left:20px;">*Date<? info('Select the date of the event.'); ?></Th>
		<TD>
		<INPUT TYPE=TEXT NAME="start_date" id="datepicker" VALUE="<?=$_POST['start_date']?>">
		<input type='checkbox' id='all_day' name='all_day' value='1' <? if($_POST['all_day'] == '1'){ ?> CHECKED <? } ?> > All Day
		</TD>
		</TR>
		
		<?
		/**
		 *
		 * END DATE
		 *
		 */
		?>
		<script type="text/javascript">
		$(function() {
			$("#datepicker2").datepicker();
		});
		</script>
		<TR BGCOLOR="#f2f2f2" id='datepickerrow2'>
		<Th style="padding-left:20px;">End Date<? info('If the event is longer than one day; select the end date.'); ?></TD>
		<TD>
		<INPUT TYPE=TEXT NAME="end_date" id="datepicker2" VALUE="<?=$_POST['end_date']?>">
		
		</TD>
		</TR>
		
		<?
		/**
		 *
		 * START Time
		 *
		 */
		?>		
		<script type="text/javascript">
		$(function() {
			$("#timepicker").timePicker({
			  show24Hours: false,
			  separator: ':',
			  step: 15});
		});
		</script>
		<TR BGCOLOR="#f2f2f2" id='timepickerrow'>
		<Th style="padding-left:20px;">Start Time<? info('Select the time that the event starts.'); ?></TD>
		<TD>
		<INPUT TYPE=TEXT NAME="start_time" id="timepicker" VALUE="<?=$_POST['start_time']?>">		
		</TD>
		</TR>
		
		<?
		/**
		 *
		 * END Time
		 *
		 */
		?>		
		<script type="text/javascript">
		$(function() {
			$("#timepicker2").timePicker({
			  show24Hours: false,
			  separator: ':',
			  step: 15});
		});
		</script>
		<TR BGCOLOR="#f2f2f2" id='timepickerrow2'>
		<Th style="padding-left:20px;">End Time<? info('Select the time that the event ends.'); ?></TD>
		<TD>
		<INPUT TYPE=TEXT NAME="end_time" id="timepicker2" VALUE="<?=$_POST['end_time']?>">		
		</TD>
		</TR>
		
		<script>
		// INITIAL PAGE LOAD CHECK
		if($('input[name=all_day]').attr('checked') == true){
			// hide 
			$('#datepickerrow2').hide();
			$('#timepickerrow').hide();
			$('#timepickerrow2').hide();
		} else {
			// show
			$('#datepickerrow2').show();
			$('#timepickerrow').show();
			$('#timepickerrow2').show();
		}
		
		<?
		if($_POST['all_day'] == '' || $_POST['all_day'] == '0'){
			?>
			// show
			$('#datepickerrow2').show();
			$('#timepickerrow').show();
			$('#timepickerrow2').show();
			<?
		}
		?>
				
		// CHECK ON CHANGE FUNCTION
		$("#all_day").change(function(){
			if($('input[name=all_day]').attr('checked') == true){
				// hide 
				$('#datepickerrow2').hide();
				$('#timepickerrow').hide();
				$('#timepickerrow2').hide();
			} else {
				// show
				$('#datepickerrow2').show();
				$('#timepickerrow').show();
				$('#timepickerrow2').show();
			}
		});		
		</script>
		
		<TR BGCOLOR="#f2f2f2">
		<Th style="padding-left:20px;">Location <? info('The location of the venue.'); ?></TD>
		<TD>
		<textarea name='location'><?=$_POST['location']?></textarea>		
		</TD>
		</TR>
		
		<TR BGCOLOR="#f2f2f2">
		<Th style="padding-left:20px;">Address <? info('The specific address of the venue. This field works with Google&trade; Maps to display a map to the event for website visitors.'); ?></TD>
		<TD>
		<INPUT TYPE=TEXT NAME="address" style='width:400px;' VALUE="<?=$_POST['address']?>">		
		</TD>
		</TR>
		
		<TR BGCOLOR="#f2f2f2">
		<Th style="padding-left:20px;">Quantity <? info('How many people can attend this event. Leave blank if there is no limit.'); ?></TD>
		<TD>
		<INPUT TYPE=TEXT NAME="quantity" VALUE="<?=$_POST['quantity']?>">		
		</TD>
		</TR>
		
		<TR BGCOLOR="#f2f2f2">
		<Th style="padding-left:20px;">Price <? info('The price for attending.'); ?></TD>
		<TD>
		$<INPUT TYPE=TEXT NAME="price" VALUE="<?=$_POST['price']?>">		
		</TD>
		</TR>
		
		<?
		/**
		 *
		 * STATUS
		 *
		 */
		?>
		<TR BGCOLOR="#f2f2f2">
		<Th width="200" height="40" style="padding-left:20px;">Status</Th>
		<TD>
		<select name="status">
			<option value="Draft" <? if($_POST['status'] == "Draft"){ ?> SELECTED <? } ?> >Draft</option>
			<option value="Pending" <? if($_POST['status'] == "Pending"){ ?> SELECTED <? } ?> >Pending</option>
			<option value="Published" <? if($_POST['status'] == "Published"){ ?> SELECTED <? } ?> >Published</option>
		</select>
		</TD>
		</TR>
		
		<?
		/**
		 *
		 * CONTENT
		 *
		 */
		?>
		<script type="text/javascript">
		$(function() {
			$("#tabs").tabs();
		});
		</script>
			
		<tr BGCOLOR="#f2f2f2">
		<td colspan="2">
		<div class="demo">
			<div id="tabs">
				
				<ul>
					<li><a href="#tabs-1">Event Content</a></li>
				</ul>
				
				<div id="tabs-1">
					<table>
						<tr BGCOLOR="#f2f2f2">
							<td colspan="2">
								<?
								displayWysiwyg('content',$_POST['content']);
								?>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		</td>
		</tr>
		</table>
		
		<div id="submit">
			<?
			echo "<INPUT TYPE='HIDDEN' NAME='VIEW' style='margin:0px; padding:0px; height:0px; line-height:0px;' VALUE='".$_REQUEST['VIEW']."' />";
			echo "<INPUT TYPE='HIDDEN' NAME='SUB' style='margin:0px; padding:0px; height:0px; line-height:0px;' VALUE='".$_REQUEST['SUB']."' />";
			echo "<INPUT TYPE=SUBMIT NAME=\"".strtoupper(str_replace(" ", "_", $button))."\" VALUE=\"$button\">";
			
			if (isset($_REQUEST["eid"])){
				echo "<INPUT TYPE='SUBMIT' NAME='DELETE_EVENT' value=\"Delete\" onclick=\"return confirm('Are You Sure?');\">";
				echo "<INPUT TYPE='HIDDEN' NAME='eid' style='margin:0px; padding:0px; height:0px; line-height:0px;' VALUE='".$_REQUEST['eid']."' />";
			}
			?>
		</div>
	
	</FORM>
	
	<?
}
/**
 *
 * Add/Update Category
 *
 */
elseif($_REQUEST['SUB'] == 'NEWCATEGORY' || $_REQUEST['cid'] != '')
{
	// get cat to modify
	if (isset($_REQUEST["cid"]))	{
		$res = doQuery("SELECT * FROM event_category WHERE category_id=".$_REQUEST["cid"]." ".$_SETTINGS['demosqland']."");
		$_POST = mysql_fetch_array($res);
		$button = "Update Category";
	} else {
		$button = "Add Category";
	}

	echo tableHeader("Category Information",2,'100%');
	?>
	<FORM name="user" METHOD=POST ACTION="<?=$_SERVER["PHP_SELF"]?>?VIEW=<?=$_GET["VIEW"]?>&ADDNEWCATEGORY=1&<?=SID?>">
			
		<TR BGCOLOR="#f2f2f2">
		<TD width="200" height="40" style="padding-left:20px;">* Name</TD>
		<TD>
		<INPUT TYPE=TEXT NAME="name" VALUE="<?=$_POST['name']?>">
		</TD>
		</TR>
		
		<tr BGCOLOR="#f2f2f2">
		<td height="40" style="padding-left:20px;">Description</td>
		<td>
		<textarea name="description"><?=$_POST['description']?></textarea><br /><br />
		</td>
		</tr>
		
	</table>
	
	
	<div id="submit">
	<?
	if (isset($_REQUEST["cid"])) {
		echo "<INPUT TYPE=SUBMIT NAME=DELETE_CATEGORY value=\"Delete\" onclick=\"return confirm('Are You Sure?');\">";
		echo "<INPUT TYPE=HIDDEN NAME='cid' VALUE='".$_REQUEST["cid"]."'>";
	}
	echo "<INPUT TYPE='HIDDEN' NAME='VIEW' style='margin:0px; padding:0px; height:0px; line-height:0px;' VALUE='".$_REQUEST['VIEW']."' />";
	echo "<INPUT TYPE='HIDDEN' NAME='SUB' style='margin:0px; padding:0px; height:0px; line-height:0px;' VALUE='".$_REQUEST['SUB']."' />";
	echo "<INPUT TYPE=SUBMIT NAME=\"".strtoupper(str_replace(" ", "_", $button))."\" VALUE=\"$button\">";
	?>
	</div>
	
	</FORM>
	<?
}
/**
 *
 * Add New Subscriber
 *
 */
elseif($_REQUEST['SUB'] == 'NEWSUBSCRIBER' || $_REQUEST['sid'] != '')
{
	// get subscriber to modify
	if (isset($_REQUEST["sid"]))
	{
		$res = doQuery("SELECT * FROM blog_alert WHERE blog_alert_id=".$_REQUEST["sid"]." ".$_SETTINGS['demosqland']."");
		$_POST = mysql_fetch_array($res);
		$button = "Update Subscriber";
	}
	else
	{
		$button = "Add Subscriber";
	}

	echo tableHeader("Subscriber/Alert Information",2,'100%');
	?>
	<FORM name="user" METHOD=POST ACTION="<?=$_SERVER["PHP_SELF"]?>?VIEW=<?=$_GET["VIEW"]?>&ADDNEWSUBSCRIBER=1&<?=SID?>">
			
		<TR BGCOLOR="#f2f2f2">
		<TD width="200" height="40" style="padding-left:20px;">* Name</TD>
		<TD>
		<INPUT TYPE=TEXT NAME="name" VALUE="<?=$_POST['name']?>">
		<?
		if (isset($_REQUEST["sid"])) {
		?>
				<INPUT TYPE=HIDDEN NAME="sid" VALUE="<?=$_REQUEST["sid"]?>">
		<?
			}
		?>
		</TD>
		</TR>
		
		<TR BGCOLOR="#f2f2f2">
		<TD width="200" height="40" style="padding-left:20px;">* Email</TD>
		<TD><INPUT TYPE=TEXT NAME="email" VALUE="<?=$_POST['email']?>"></TD>
		</TR>
					
	<?	
	echo "</TABLE>";
	?>
	<div
	id="submit">
	<?
	if (isset($_REQUEST["sid"])) {
		echo "<INPUT TYPE=SUBMIT NAME=DELETE_SUBSCRIBER value=\"Delete\" onclick=\"return confirm('Are You Sure?');\">";
	}
	echo "<INPUT TYPE=SUBMIT NAME=\"".strtoupper(str_replace(" ", "_", $button))."\" VALUE=\"$button\">";
	?>
	</div>
	</FORM>
	<?
}
/**
 *
 * Search categories
 *
 */
elseif($_REQUEST['SUB'] == 'CATEGORIES')
{
	//
	// SORTABLE 
	//	
	echo "<div class='textcontent1'>";
	echo "	<h1>Categories</h1>";
	echo "</div>";
	echo "<br />";
	echo "<br />";
	
	// HEADER
	echo tableHeaderid("Categories",6,"100%","list");
	echo "<thead><TR><th width='600'>Categories &amp; Products</th><th>Action</th></TR></thead><tbody>";
	echo "</tbody></table>";
	
	// List
	$select = 	"SELECT * FROM event_category ".
				"WHERE ".
				"active='1' ORDER BY sort_level ASC".
				"".$_SETTINGS['demosqland']."";
	
	echo sortableList();
	
	$res = doQuery($select);
	$num = mysql_num_rows($res);
	$i=0;
	while ($row = mysql_fetch_array($res)){
		$default = "";
		if($i % 2) { $class = "odd"; } else { $class = "even"; }
		echo "<li class=\"".$class." selector\" id=\"".$row['category_id']."\"> <span class=\"cat1\"></span> <span>{$row["name"]} {$default}</span>";
			// TOP LEVEL FORM
			echo "<FORM class=\"listform\" METHOD=GET ACTION=\"$_SERVER[PHP_SELF]\">";
			echo "<INPUT TYPE=HIDDEN NAME=cid VALUE=\"{$row["category_id"]}\">";
			echo "<INPUT TYPE=HIDDEN NAME=VIEW VALUE=\"{$_GET["VIEW"]}\">";
			echo "<INPUT TYPE=HIDDEN NAME=SUB VALUE=\"{$_GET["SUB"]}\">";
			echo "<INPUT TYPE=SUBMIT NAME=DELETECATEGORY VALUE=\"Delete\" onClick=\"return confirm('Are You Sure?');\">";
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
			  url: 'modules/events/events.php',
			  data: { sortarray: resultstring, SORT_CATEGORIES: '1' }
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
}
/**
 *
 * Search subscribers
 *
 *
 */
elseif($_REQUEST['SUB'] == 'SUBSCRIBERS')
{
	echo "<div class='textcontent'>";
		echo "<h1>Event Alert Subscribers</h1>";

		echo "<FORM METHOD=GET>";
		echo "<INPUT TYPE=HIDDEN NAME='VIEW' VALUE='".$_GET["VIEW"]."'>";
		echo "<INPUT TYPE=HIDDEN NAME='SUB' VALUE='".$_GET["SUB"]."'>";
		echo "<INPUT TYPE=TEXT NAME='KEYWORDS' VALUE='".$_GET["KEYWORDS"]."'>";
		echo "<SELECT NAME='COLUMN'>";
		echo "<OPTION VALUE='name'".selected($_GET["COLUMN"],"name").">Name</OPTION>";
		echo "<OPTION VALUE='email'".selected($_GET["COLUMN"],"email").">Email</OPTION>";
		echo "</SELECT>";
		echo "<INPUT TYPE='SUBMIT' NAME='search' VALUE='Search'> <INPUT TYPE=BUTTON NAME=NONE VALUE='Reset' ONCLICK=\"document.location = '".$_SERVER["PHP_SELF"]."?VIEW=".$_REQUEST["VIEW"]."&SUB=".$_REQUEST['SUB']."';\">";
		echo "</FORM>";
		
	echo "</div>";
	echo "<div class='minitoolbar'>";
	echo "	<a href='modules/events/event_alert_excel.php'>Export To Excel</a>";
	echo "</div>";
	echo "<br clear='all' />";

	/**
	 * List all subscribers
	 */
	
	if ($_GET['KEYWORDS']!="") {
		$q = "AND {$_GET['COLUMN']} like '%{$_GET['KEYWORDS']}%'";
	}
	
	$page = 1;
	$size = 20;	// how many records per page
	
	$total_records = mysql_num_rows(doQuery("SELECT * FROM event_alert WHERE active='1' $q ".$_SETTINGS['demosqland']."")); 
	 
	if (isset($_GET['page'])){ $page = (int) $_GET['page']; } // we get the current page from $_GET
	
	// create the pagination class
	$pagination = new Pagination();
	$pagination->setLink("index.php?VIEW=".$_REQUEST['VIEW']."&SUB=".$_REQUEST['SUB']."&page=%s");
	$pagination->setPage($page);
	$pagination->setSize($size);
	$pagination->setTotalRecords($total_records);
	 
	// now use this SQL statement to get records from your table
	$SQL = "SELECT * FROM blog_alert WHERE 1=1 $q ".$_SETTINGS['demosqland']." ORDER BY created DESC " . $pagination->getLimitSql();	
	
	echo tableHeaderid("Blog Subscribers/Alerts",6,"100%","list");
	
	echo "<thead><TR><th>Name</th><th>Email</th><th>Action</th></TR></thead><tbody>";
	
	$res = doQuery($SQL);
	
	$i=0;	
	while ($row = mysql_fetch_array($res)) {
		if($i % 2) { $class = "odd"; } else { $class = ""; }
		echo "<TR class=\"$class\">";
		echo "<TD>{$row["name"]}</TD>";
		echo "<TD>{$row["email"]}</TD>";
		echo "<TD nowrap ALIGN=\"LEFT\">";
			echo "<FORM METHOD='GET' ACTION='".$_SERVER[PHP_SELF]."'>";
			echo "<INPUT TYPE='HIDDEN' NAME='aid' VALUE='".$row["alert_id"]."'>";
			echo "<INPUT TYPE='HIDDEN' NAME='VIEW' VALUE='".$_GET["VIEW"]."'>";
			echo "<INPUT TYPE='HIDDEN' NAME='SUB' VALUE='".$_GET["SUB"]."'>";
			echo "<INPUT TYPE='SUBMIT' NAME='DELETE_ALERT_SUBSCRIBER' VALUE='Delete' onClick=\"return confirm('Are You Sure?');\">";
			echo "<INPUT TYPE=SUBMIT NAME=view VALUE='View'>";
			echo "</FORM>";
		echo "</TD>";
		echo "</TR>";
		$i++;
	}
	
	echo "</tbody></TABLE>";
	$navigation = $pagination->create_links();
	echo $navigation; // will draw our page navigation
}
/**
 *
 * Search Events
 *
 */
elseif($_REQUEST['SUB'] == 'EVENTS') 
{
	echo "<div class='textcontent'>";
		echo "<h1>Events</h1>";

		echo "<FORM METHOD=GET>";
		echo "<INPUT TYPE=HIDDEN NAME='VIEW' VALUE='".$_GET["VIEW"]."'>";
		echo "<INPUT TYPE=HIDDEN NAME='SUB' VALUE='".$_GET["SUB"]."'>";
		echo "<INPUT TYPE=TEXT NAME='KEYWORDS' VALUE='".$_GET["KEYWORDS"]."'>";
		echo "<SELECT NAME='COLUMN'>";
		echo "<OPTION VALUE='name'".selected($_GET["COLUMN"],"title").">Title</OPTION>";
		echo "<OPTION VALUE='location'".selected($_GET["COLUMN"],"location").">Location</OPTION>";
		echo "</SELECT>";
		echo "<INPUT TYPE='SUBMIT' NAME='search' VALUE='Search'> <INPUT TYPE=BUTTON NAME=NONE VALUE='Reset' ONCLICK=\"document.location = '".$_SERVER["PHP_SELF"]."?VIEW=".$_REQUEST["VIEW"]."&SUB=".$_REQUEST['SUB']."';\">";
		echo "</FORM>";
		
	echo "</div>";
	echo "<br /><br />";

	/**
	 *
	 * List all posts
	 *
	 */
	if ($_GET['KEYWORDS']!="") {
		$q = "AND ".$_GET['COLUMN']." like '%".$_GET['KEYWORDS']."%'";
	}
	
	$page = 1; // start page
	$size = 20;	// how many records per page
	
	$select = 	"SELECT * FROM events WHERE ".
				"active='1' ".
				"$q ".
				"".$_SETTINGS['demosqland']."";
	$total_records = mysql_num_rows(doQuery($select)); 
	 
	if (isset($_GET['page'])){ $page = (int) $_GET['page']; }// get current page
	$pagination = new Pagination();
	$pagination->setLink("index.php?VIEW=".$_REQUEST['VIEW']."&SUB=".$_REQUEST['SUB']."&page=%s");
	$pagination->setPage($page);
	$pagination->setSize($size);
	$pagination->setTotalRecords($total_records);
	 
	// now use this SQL statement to get records from your table
	$select = 	"SELECT * FROM events WHERE ".
				"active='1' ".
				"$q ".
				"".$_SETTINGS['demosqland']." ".
				" ORDER BY date DESC " . $pagination->getLimitSql();	
	
	echo tableHeaderid("Events",6,"100%","list");
	echo "<thead><TR><th>Title</th><th>Date</th><th>Location</th><th>Attending</th><th>Action</th></TR></thead><tbody>";
	
	$res = doQuery($select);
	
	$i=0;	
	while ($row = mysql_fetch_array($res)) {
		if($i % 2) { $class = "odd"; } else { $class = ""; }
		echo "<TR class='".$class."'>";
		//echo "<TD>".$row["event_id"]."</TD>";		
		echo "<TD>".$row["title"]."</TD>";
		echo "<TD>".FormatTimeStamp($row["date"])."</TD>";
		echo "<TD>".$row["location"]."</TD>";
		echo "<TD>".$Events->AttendingCount($row["event_id"])."</TD>";
		echo "<TD nowrap ALIGN=\"LEFT\">";
			echo "<FORM METHOD='GET' ACTION='".$_SERVER[PHP_SELF]."'>";
			echo "<INPUT TYPE='HIDDEN' NAME='eid' VALUE='".$row["event_id"]."'>";
			echo "<INPUT TYPE='HIDDEN' NAME='VIEW' VALUE='".$_GET["VIEW"]."'>";
			echo "<INPUT TYPE='HIDDEN' NAME='SUB' VALUE='".$_GET["SUB"]."'>";
			echo "<INPUT TYPE='SUBMIT' NAME='DELETE_EVENT' VALUE='Delete' onClick=\"return confirm('Are You Sure?');\">";
			echo "<INPUT TYPE='SUBMIT' NAME='view' VALUE='View'>";
			echo "</FORM>";
		echo "</TD>";
		echo "</TR>";
		$i++;
	}	
	echo "</tbody>";
	echo "</TABLE>";
	
	$navigation = $pagination->create_links();
	echo $navigation; // will draw our page navigation
}
elseif($_REQUEST['SUB'] == 'CALENDAR' OR $_REQUEST['SUB'] == '') 
{

	echo "<style>table.fc-header { margin:10px 0 0 10px; width:90%; }</style>";
	echo "<div id='eventscalendar'></div>";


}
?>