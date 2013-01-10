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


//
// Declare UserAccounts Class and Report Function
//
$ImageSlideshow3 = new ImageSlideshow3();
report($_REQUEST['REPORT'],$_REQUEST['SUCCESS']);
	
/***	DELETE SLIDE 				********************************************************/
if (isset($_REQUEST["DELETE_SLIDE"])){
	$select = "DELETE FROM image_slideshow_3_slides WHERE slide_id='".$_REQUEST['sid']."'";
	doQuery($select);
	header("Location: ".$_SERVER["PHP_SELF"]."?REPORT=Slide deleted&SUCCESS=1&VIEW=".$_REQUEST["VIEW"]."");
	exit;
}
	
/***	NEW SLIDE 				********************************************************/
if(isset($_POST['NEW_SLIDE']))
{
	$error = 0;
	$_POST = escape_smart_array($_POST);
	if($error == 0){
		// FORMAT IMAGE SQL
		if($_POST['image1'] != ""){	$image1 = "image1='".basename($_POST['image1'])."',";}		
		
		//$date = DateTimeIntoTimestamp($_POST['date'],'');
		$publishDate = DateTimeAmPmIntoSeconds($_POST['publish_date']);
		$expirationDate = DateTimeAmPmIntoSeconds($_POST['expiration_date']);
		
		//echo "<br>Publish Date: ".$publishDate."<br>";
		//echo "Expiration Date: ".$expirationDate."<Br>";
		//exit();
		//die();
		
		
		// INSERT RECORD
		$slide_id = nextId('image_slideshow_3_slides');
		$select =	"INSERT INTO image_slideshow_3_slides SET ".
					"name='".$_POST['name']."',".
					"date='".date("Y-m-d")."',".
					"publish_date='".$publishDate."',".
					"expiration_date='".$expirationDate."',".
					"dont_expire='".$_POST['dont_expire']."',".
					"status='".$_POST['status']."',".
					"".$image1."".
					"type='".$_POST['type']."',".
					"text1='".$_POST['text1']."',".
					"title1='".$_POST['title1']."',".
					"link1='".$_POST['link1']."',".
					"active='1' ".
					"".$_SETTINGS['demosql']."";	
		
		doQuery($select);
		header("Location: ".$_SERVER["PHP_SELF"]."?REPORT=Slide created&SUCCESS=1&sid=".$slide_id."&VIEW=".$_GET["VIEW"]."");
		exit;
	}
}

/***	Update A SLIDE 				********************************************************/
if(isset($_POST["UPDATE_SLIDE"]))
{
	$error = 0;		
	$_POST = escape_smart_array($_POST);
	
	if($error == 0){		
		// FORMAT IMAGE SQL
		if($_POST['image1'] != ""){	$image1 = "image1='".basename($_POST['image1'])."',";}		
		if($_POST['remove1'] != ""){ $image1 = "image1='',";}		
	
		$publishDate = DateTimeAmPmIntoSeconds($_POST['publish_date']);
		$expirationDate = DateTimeAmPmIntoSeconds($_POST['expiration_date']);
		
		//echo "<br>Publish Date: ".$publishDate."<br>";
		//echo "Expiration Date: ".$expirationDate."<Br>";
		//exit();
		//die();
		
		// UPDATE RECORD
		$select =	"UPDATE image_slideshow_3_slides SET ".
					"name='".$_POST['name']."',".
					"publish_date='".$publishDate."',".
					"expiration_date='".$expirationDate."',".
					"dont_expire='".$_POST['dont_expire']."',".
					"status='".$_POST['status']."',".
					"".$image1."".
					"type='".$_POST['type']."',".
					"text1='".$_POST['text1']."',".
					"title1='".$_POST['title1']."',".
					"link1='".$_POST['link1']."' ".
					"".$_SETTINGS['demosql']."".
					" WHERE slide_id='".$_POST['sid']."'";	

		doQuery($select);
		header("Location: ".$_SERVER["PHP_SELF"]."?REPORT=Slide updated&SUCCESS=1&sid=".$_POST['sid']."&VIEW=".$_GET["VIEW"]."");
		exit;
	}
}

/*** NEW / UPDATE Slide Form				********************************************************/
if($_REQUEST['SUB'] == "NEWSLIDE" OR $_REQUEST['sid'] != ""){
	// ADD/EDIT CHECK
	if (isset($_REQUEST["sid"])) {
		$select = 	"SELECT * FROM image_slideshow_3_slides ".
					"WHERE ".
					"slide_id='".$_REQUEST["sid"]."'";
		$res = doQuery($select);
		$_POST = mysql_fetch_array($res);		
		//$_POST['name'] = form_encode($_POST['name']);	

		//$_POST['date'] = TimestampIntoDate($_POST['date']);
		$_POST['publish_date'] = SecondsIntoDateTimeAmPm($_POST['publish_date']);
		$_POST['expiration_date'] = SecondsIntoDateTimeAmPm($_POST['expiration_date']);
		
		$button = "Update Slide";
		$doing = "Slide ".$_POST['name']."";
	} else {	
	
		$button = "New Slide";
		$doing = "New Slide";
	}	
	
	if($_POST['date'] == ""){ $_POST['date'] = date("m/d/Y"); }
	
	$debugvisible = 'visibility:hidden;';

	// START FORM
	startAdminForm();
		// START TABLE
		echo tableHeader($doing,2,'100%');			
		?>
			
			<script type="text/javascript">
				$(function() {
					$(".datepicker").datetimepicker();
				});
			</script>
			
			<tr>
				<th>Name</th>
				<td><input type='textbox' name='name' id='name' value='<?=$_POST['name']?>'></td>
			</tr>
			
			<tr>			
				<th>Status</th>
				<td>
					<select name='status'>
						<option value='Published' <? if($_POST['status'] == 'Published'){ ?> SELECTED <? } ?> >Published</option>								
						<option value='Pending' <? if($_POST['status'] == 'Pending'){ ?> SELECTED <? } ?> >Pending</option>
						<option value='Draft' <? if($_POST['status'] == 'Draft'){ ?> SELECTED <? } ?> >Draft</option>
						<option value='Draft' <? if($_POST['status'] == 'Expired'){ ?> SELECTED <? } ?> >Expired</option>
					</select>
					
					<? //var_dump($_POST); ?>
					
				</td>
			</tr>
			
			<tr>			
				<th>Publish Date</th>
				<td>
					<?
					echo "	Area:		".date_default_timezone_get()."
							Timezone:	".date("T")."
							Date:		".date("m/d/Y h:i:s a")."";
							
					?>
					<Br>
					<input class='datepicker' type='textbox' name='publish_date' id='publish_date' value='<?=$_POST['publish_date']?>'></td>						
			</tr>
			
			<tr>			
				<th>Expiration Date</th>
				<td>
					<input class='datepicker' type='textbox' name='expiration_date' id='expiration_date' value='<?=$_POST['expiration_date']?>'> 
					<input type='checkbox' value='1' name='dont_expire' /> Don't Expire
				</td>						
			</tr>
			
			<tr>			
				<th>Website</th>
				<td>
					<select name='type'>
						<option value='She Beads' <? if($_POST['type'] == "She Beads"){ ?> SELECTED <? } ?>>She Beads</option>
						<option value='He Beads' <? if($_POST['type'] == "He Beads"){ ?> SELECTED <? } ?>>He Beads</option>
						<option value='Intention Beads' <? if($_POST['type'] == "Intention Beads"){ ?> SELECTED <? } ?>>Intention Beads</option>
					</select>
				</td>						
			</tr>	
			
			<tr>
				<th>Title</th>
				<td><input type="text" name="title1" value="<?=$_POST['title1']?>" style="float:none;" /></td>
			</tr>
			
			<tr>
				<th>Image</th>
				<td>
				<input style="float:none;" type="text" name="image1" value="<?=$_POST['image1']?>" /><button type="button" onClick="SmallFileBrowser('../uploads/','image1')">Choose Image...</button>
				<br>
				<img src='<?=$_SETTINGS['website']?>uploads/<?=$_POST['image1']?>' style='width:20%;'>
				</td>
			</tr>
			
			<tr>
				<th>Link</th>
				<td><input type="text" name="link1" value="<?=$_POST['link1']?>" style="float:none;" /></td>
			</tr>
			
			<tr>
				<th>Text</th>
				<td>
				<?
				echo displayWysiwyg("text1",$_POST['text1'],'600','50');
				?>
				</td>
			</tr>
			
		
		<?
		// END FORM	
		$xid = "sid";
		$identifier = "SLIDE";
		endAdminForm($button,$xid,$identifier);
		


	
}
/*** VIEWING SLIDES		 **********/
elseif($_REQUEST['SUB'] == "")
{
	
	$name				= "Slides";
	$table				= "image_slideshow_3_slides";
	$orderByString		= "";
	$searchColumnArray	= Array("slide_id",		"name");
	$titleColumnArray	= Array("Id",			"Name",	"Status",	"Type",	"Date");
	$valueColumnArray	= Array("slide_id",		"name",	"status",	"type",	"date");
	$xid				= "sid";
	basicSearchListingTable($name,$table,$orderByString,$searchColumnArray,$titleColumnArray,$valueColumnArray,$xid);

}
?>		

