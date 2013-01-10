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

/**
 *
 * Ajax Sort SINGLE LEVEL Categories 
 *
 */
if (isset($_POST['SORT_CATEGORIES'])){
	// FOR AJAX
	@require_once '../../../includes/config.php';
	
	//
	// GET SORT ARRAY
	//
	$sortarray = $_POST['sortarray'];
	$sortarray = explode(",",$sortarray);
	
	if($_POST['LEVEL'] == '1'){
		// GET BOTTOM MOST SORT LEVEL OF CATEGORY LEVEL 1
		$select = "SELECT sort_level FROM portfolio_categories WHERE active='1' ORDER BY sort_level DESC";
		echo $select."<br>";
		$result = doQuery($select);
		// THE NUMBER OF TOP LEVEL CATEGORIES
		$num = mysql_num_rows($result);
		$i = 1;
		foreach($sortarray AS $category){
			$select = "UPDATE portfolio_categories SET sort_level='".$i."' WHERE category_id='".$category."'";
			echo $select."<br>";
			$result = doQuery($select);
			$i++;
		}
	}
	echo "true";
	exit;
}


/**
 *
 * Ajax Sort Item
 *
 */
if (isset($_POST['SORT_PRODUCTS'])){
	// FOR AJAX
	@require_once '../../../includes/config.php';
	
	//
	// GET SORT ARRAY
	//
	$sortarray = $_POST['sortarray'];
	$sortarray = explode(",",$sortarray);
	
	
		// GET BOTTOM MOST SORT LEVEL
		$select = 	"SELECT sort_level FROM portfolio_items ".
				"LEFT JOIN portfolio_category_item_relational ON portfolio_items.item_id=portfolio_category_item_relational.item_id ".
				"LEFT JOIN portfolio_categories ON portfolio_category_item_relational.category_id=portfolio_categories.category_id ".
				"WHERE portfolio_items.active='1' AND portfolio_categories.category_id='".$_POST['CATEGORY']."' ORDER BY portfolio_items.sort_level DESC";
		echo $select."<br>";
		$result = doQuery($select);
		// THE NUMBER OF PRODUCTS
		$num = mysql_num_rows($result);
		$i = 1;
		foreach($sortarray AS $product){
			$select = "UPDATE portfolio_items SET sort_level='".$i."' WHERE item_id='".$product."'";
			echo $select."<br>";
			$result = doQuery($select);
			$i++;
		}
	
	echo "true";
	exit;
}



/**
 *
 * DECLARE CLASS AND REPORT
 *
 */
$Portfolio = new Portfolio();
$Settings = new Settings();
report($_REQUEST['REPORT'],$_REQUEST['SUCCESS']);
	
/**
 *
 * REMOVE Item
 *
 */
if (isset($_REQUEST["DELETE_ITEM"])){
	doQuery("UPDATE portfolio_items SET active='0' WHERE item_id=".$_REQUEST["xid"]."");
	$REPORT = "Item Deleted Successfully";
	$SUCCESS = "1";
	header("Location: ".$_SERVER["PHP_SELF"]."?VIEW=".$_REQUEST["VIEW"]."&SUB=".$_REQUEST['SUB']."&REPORT=".$REPORT."&SUCCESS=".$SUCCESS."");
	exit();
}

/**
 *
 * REMOVE Category
 *
 */
if (isset($_REQUEST["DELETE_CATEGORY"])){
	doQuery("UPDATE portfolio_categories SET active='0' WHERE category_id=".$_REQUEST["cid"]."");
	$REPORT = "Category Deleted Successfully";
	$SUCCESS = "1";
	header("Location: ".$_SERVER["PHP_SELF"]."?VIEW=".$_REQUEST["VIEW"]."&SUB=".$_REQUEST['SUB']."&REPORT=".$REPORT."&SUCCESS=".$SUCCESS."");
	exit();
}

/**
 *
 * ADD & UPDATE Item
 *
 */
 if (isset($_POST["ADD_ITEM"]) OR isset($_POST['UPDATE_ITEM'])){

	// UNFORMATED DATA
	$_UNFORMATED = $_POST;
	// FORMATED DATA
	$_POST = escape_smart_array($_POST);
	
	$error = 0;
	if($_POST['title'] == ""){ $error = 1; ReportError("Please enter a title."); }
	if($_UNFORMATED['categories'] == "" AND $_POST['category_name'] == ""){ $error = 1; ReportError("Category is required."); }
	
	$date = $_POST['date'];
	$date_array = explode("/",$date);
	$date = $date_array['2']."-".$date_array[0]."-".$date_array[1];
	
	if($error == 0){ 
		//
		// create new category
		//
		if($_POST['category_name'] != ""){		
			$_POST['category_id'] = nextId('categories');
			$sel1 = "INSERT INTO portfolio_categories SET ".
					"`name`='".$_POST['category_name']."'";
			doQuery($sel1);
		} 
	
		//
		// update Portfolio Item
		//	
		if(isset($_POST['UPDATE_ITEM'])){			
			$select =	"UPDATE portfolio_items SET ".						
					"`title`='".$_POST['title']."',".
					"`description`='".$_POST['description']."',".
					"`thumbnail_image`='".basename($_POST['thumbnail_image'])."',".
					"`active`='1',".
					"`status`='".$_POST['status']."',".
					"`date`='".$date."'".
					"".$_SETTINGS['demosql']."".
					" WHERE item_id='".$_POST["xid"]."'";		
			doQuery($select);
			
			// IF NEW CATEGORY SET THAT AS THE RELATION
			if($_POST['category_name'] != "")
			{
				// UPDATE RELATIONAL CATEGORIES
				doQuery("DELETE FROM portfolio_category_item_relational WHERE item_id='".$_POST['xid']."'");					 
				$select = 	"INSERT INTO portfolio_category_item_relational SET ".
						"item_id='".$_POST['xid']."',".
						"category_id='".$_POST['category_id']."' ";				
				doQuery($select);	
			}
			// UPDATE RELATIONAL CATEGORIES
			else
			{
				doQuery("DELETE FROM portfolio_category_item_relational WHERE item_id='".$_POST['xid']."'");
				$test=$_UNFORMATED['categories'];
				if($test){
					foreach ($test as $t){		 
						$select = 	"INSERT INTO portfolio_category_item_relational SET ".
								"item_id='".$_POST['xid']."',".
								"category_id='".$t."' ";				
						doQuery($select);	
					}
				}			
			}
			
			// DELETE CURRENT ADDITIONAL IMAGES
			doQuery("DELETE FROM portfolio_item_images WHERE item_id='".$_POST['xid']."'");
			
			// ADDITIONAL IMAGES
			if($_POST['newadditionalimage'] != ""){
				$insert = 	"INSERT INTO portfolio_item_images SET ".
						"image='".basename($_POST['newadditionalimage'])."',".
						"item_id='".$_POST['xid']."'";
				//echo "INSERT<br><br>".$insert."<br><br>";		
				doQuery($insert);
			}
			
			$i = 1;
			foreach($_POST as $key=>$value)
			{
				//echo "$key=$value <Br><Br>";
				if(strstr($key,"existingadditionalimage")){
					//$tmp = 'additionalimage-'.$i.'';
					$image = $value;
					if($image != ""){
						$insert = 	"INSERT INTO portfolio_item_images SET ".
								"image='".basename($image)."',".
								"item_id='".$_POST['xid']."'";
						//echo "".$insert."<Br><Br><Br>";
						doQuery($insert);
						
					}
					$i++;
				}
				
			}
			//die();
			//exit;
			$report = "Portfolio item updated successfully.";
			$success = "1";			
			header("Location: {$_SERVER["PHP_SELF"]}?REPORT=".$report."&xid=".$_POST['xid']."&SUCCESS=".$success."&VIEW=".$_REQUEST['VIEW']."");
			exit();
		} else {			
			//
			// INSERT Portfolio Item
			//
			$next = nextId('portfolio_items');
			$select =	"INSERT INTO portfolio_items SET ".						
						"`title`='".$_POST['title']."',".
						"`description`='".$_POST['description']."',".						
						"`image`='".basename($_POST['image'])."',".
						"`thumbnail_image`='".basename($_POST['thumbnail_image'])."',".
						"`active`='1',".
						"`status`='".$_POST['status']."',".
						"`created`=NULL,".
						"`date`='".$date."'";			
			doQuery($select);
			
			// IF NEW CATEGORY SET THAT AS THE RELATION
			if($_POST['category_name'] != ""){
				// INSERT RELATIONAL CATEGORIES			 
				$select = 	"INSERT INTO portfolio_category_item_relational SET ".
							"item_id='".$next."',".
							"category_id='".$_POST['category_id']."' ";				
				doQuery($select);	
			} else {
				// INSERT RELATIONAL CATEGORIES
				$test=$_UNFORMATED['categories'];
				if($test){
				 foreach ($test as $t){		 
					$select = 	"INSERT INTO portfolio_category_item_relational SET ".
								"item_id='".$next."',".
								"category_id='".$t."' ";				
					doQuery($select);	
					}
				}			
			}
			
			// ADDITIONAL IMAGES
			if($_POST['newadditionalimage'] != ""){
				$insert = 	"INSERT INTO portfolio_item_images SET ".
						"image='".basename($_POST['newadditionalimage'])."',".
						"item_id='".$next."'";
				//echo "INSERT<br><br>".$insert."<br><br>";		
				doQuery($insert);
			}
			// DELETE CURRENT ADDITIONAL IMAGES
			
			$report = "Portfolio item created successfully.";
			$success = "1";			
			header("Location: {$_SERVER["PHP_SELF"]}?REPORT=".$report."&xid=".$next."&SUCCESS=".$success."&VIEW=".$_REQUEST['VIEW']."");
			exit();
		}
	}
}

/**
 *
 * ADD & UPDATE Category
 *
 */
if (isset($_POST['ADD_CATEGORY']) OR isset($_POST['UPDATE_CATEGORY'])){
	$error = 0;
	if($_POST['name'] == ""){ $error = 1; ReportError("Enter a name."); }
	
	if($error == 0){	
		if(isset($_POST['ADD_CATEGORY'])){
			// create new category
			$_POST = escape_smart_array($_POST);
			$next = nextId('portfolio_categories');
			$sel1 = 	"INSERT INTO portfolio_categories SET ".
					"`name`='".$_POST['name']."',".
					"`description`='".$_POST['description']."'";
			$res1 = doQuery($sel1);
			$report = "Category created.";
			header("Location: ?REPORT=".$report."&SUCCESS=1&cid=".$next."&VIEW=".$_REQUEST['VIEW']."");
			exit();
		} else {
			// update category
			$_POST = escape_smart_array($_POST);
			$sel1 = "UPDATE portfolio_categories SET ".
					"`name`='".$_POST['name']."', ".
					"`description`='".$_POST['description']."' ".
					"WHERE category_id='".$_POST['cid']."'";
			$res1 = doQuery($sel1);
			$report = "Category updated.";
			header("Location: ?REPORT=".$report."&SUCCESS=1&cid=".$_POST['cid']."&VIEW=".$_REQUEST['VIEW']."&SUB=".$_REQUEST['SUB']."");
			exit();
		}
	}
}


/**
 *
 * UPDATE SETTINGS
 *
 */
if($_POST['UPDATE_SETTINGS'] != "")
{
	// LOOP THROUGH SETTINGS
	$sel = "SELECT * FROM settings WHERE active='1' AND group_id='8'";
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

	$report = "Settings updated.";
	$success = "1";
	header("Location: {$_SERVER["PHP_SELF"]}?VIEW=".$_REQUEST["VIEW"]."&SUB=".$_REQUEST['SUB']."&REPORT=".$report."&SUCCESS=".$success."");
	exit();
}


/**
 *
 * ADD & UPDATE Item Form
 *
 */
if ($_GET["SUB"] == "NEWITEM" || (isset($_GET["xid"]))){
	global $_SETTINGS;
	if (isset($_REQUEST["xid"])) {
		$select = 	"SELECT * FROM portfolio_items ".
					"WHERE ".
					"item_id='".$_REQUEST["xid"]."'";
		$res = doQuery($select);
		
		$_POST = mysql_fetch_array($res);
		$button = "Update Item";
	} else {
		$button = "Add Item";
	}
	?>
	<FORM name="wesform" id="wesform" METHOD="POST" ACTION="" enctype="multipart/form-data" autocomplete="off">
		<?
		echo tableHeader("Portfolio Item: ".$_POST['title']."",2,'100%');
		?>
			<TR BGCOLOR="#f2f2f2">
			<Th width="200" height="40" style="padding-left:20px;">*Title:</Th>
			<TD>
			<INPUT TYPE=TEXT NAME="title" size="60" VALUE="<?=$_POST['title']?>" />
			</TD>
			</TR>
			
			<TR BGCOLOR="#f2f2f2">
			<Th width="200" height="40" style="padding-left:20px;">Status:</Th>
			<TD>
			<select name="status">
			<option value="Draft" <? if($_POST['status'] == "Draft"){ ?> SELECTED <? } ?> >Draft</option>
			<option value="Pending" <? if($_POST['status'] == "Pending"){ ?> SELECTED <? } ?> >Pending</option>
			<option value="Published" <? if($_POST['status'] == "Published"){ ?> SELECTED <? } ?> >Published</option>
			</select>
			</TD>
			</TR>
			
			
			<TR BGCOLOR="#f2f2f2">
			<Th width="200" height="40" style="padding-left:20px;">Date:</Th>
			<TD>
			<?
			if($_REQUEST['xid'] != ''){
				$date = FormatTimeStamp($_POST['date']." 00:00:00");
			} else {
				$date = date("m/d/Y");
			}
			?>
			<INPUT TYPE=TEXT NAME="date" class="datepicker" VALUE="<?=$date ?>" />
			</TD>
			</TR>
			
			<script type="text/javascript">
			$(function() {
				$(".datepicker").datepicker();
			});
			</script>
			
			<TR BGCOLOR="#f2f2f2">
			<Th width="200" height="40" style="padding-left:20px;">Description</Th>
			<TD>
			<textarea name="description" style="height:100px;"><?=$_POST['description']?></textarea>
			</TD>
			</TR>
			
			<!-- CATEGORY -->			
			<?
			$identifier = "2";
			?>
			<TR BGCOLOR="#f2f2f2" class="toggleropenidentifier<?=$identifier ?>">
			<Th width="200" height="40" style="padding-left:20px;">*Category:</Th>
			<TD>
			<?
			//hierarchyselectTable('portfolio_categories','categories[]','category_id','name','sort_level','ASC',0);
			?>
			
			<?
			//hierarchymultiselectTable('portfolio_categories','categories[]','category_id','name','sort_level','ASC',0, 'portfolio_category_item_relational','item_id',''.$_REQUEST['xid'].'');
			multiselectTable('portfolio_categories','categories[]','category_id','name','sort_level','ASC',0, 'portfolio_category_item_relational','item_id',''.$_REQUEST['xid'].'');
			?>
			
			&nbsp;
			&nbsp;
			<a class="toggleridentifier<?=$identifier ?> tog">New Category</a>
			</TD>
			</TR>
			
			<TR BGCOLOR="#f2f2f2" class="toggleidentifier<?=$identifier ?>">
			<Th width="200" height="40" style="padding-left:20px;">*Category Name:</Th>
			<TD>
			<INPUT TYPE=TEXT NAME="category_name" size="30" VALUE="<?=$_POST['category_name']?>" />
			&nbsp;
			&nbsp;
			<a class="togglercloseidentifier<?=$identifier ?> tog">Cancel New Category</a>
			</TD>
			</TR>
			
		
			
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
			
			// CHECK THUMBNAIL DIRECTOR FOR IMAGES
			if(findinString($_POST['image_thumbnail'],"_w")){
				$t2 = "wpThumbnails/";
			}
			?>
			
			<TR BGCOLOR="#f2f2f2">
			<Th width="200" height="40" style="padding-left:20px;">Image:</Th>
			<TD>
				
				<table class='table-in-table'>
					<tr>
						<th style='text-align:left;'>Thumbnail</th>
						<th style='text-align:left;'>Main Images*</th>
					</tr>
					<tr>
						<td style='text-align:left;'>
							<?
							//echo $_SERVER['DOCUMENT_ROOT'];
							//if(!is_file($_SERVER['DOCUMENT_ROOT']."/uploads/".$t2.$_POST['thumbnail_image']."")){
							//	$_POST['thumbnail_image'] = 'nophoto.png';
							//}
							
							?>
							<p>A thumbnail image is not required. If there is no thumbnail image then the first main image will be used for the thumnail image.</p>
							<image src="<?=$_SETTINGS['website']?>uploads/<?=$t1.$_POST['thumbnail_image']?>" style="width:75px;" />
							<br><Br>
							<input style="float:none;" type="text" name="thumbnail_image" value="<?=$_POST['thumbnail_image']?>"  autocomplete="off" /><button type="button" onClick="SmallFileBrowser('../uploads/','thumbnail_image')">Choose Image...</button>
							
							
						</td>
						<td style='text-align:left;'>
							
							<?
							// NEW ADDITIONAL IMAGE
							echo "<div style='border-bottom:1px solid #f2f2f2; padding:20px;'>";
							echo "New Image";
							echo "<br><br>";
							echo "<input style='float:none;' type='text' name='newadditionalimage' value=''  autocomplete='off' /><button type='button' onClick='SmallFileBrowser(\"../uploads/\",\"newadditionalimage\")'>Choose Image...</button>";
							echo "</div>";
							
							
							// LIST ADDITIONAL IMAGES
							$additionalSql = "SELECT * FROM portfolio_item_images WHERE item_id='".$_POST['item_id']."'";
							$additionalRes = doQuery($additionalSql);
							$a=1;
							while($additional = mysql_fetch_array($additionalRes)){
								// CHECK THUMBNAIL DIRECTOR FOR IMAGES
								$imageFolder = '';
								if(findinString($additional['image'],"_w")){
									$imageFolder .= "wpThumbnails/";
								}
								if(!is_file($_SERVER['DOCUMENT_ROOT']."/uploads/".$imageFolder.$additional['image']."")){
									$_POST['image'] = 'nophoto.png';
								}
								echo "<div style='border-bottom:1px solid #f2f2f2; padding:20px;'>";
								echo "<img src='".$_SETTINGS['website']."uploads/".$imageFolder.$additional['image']."' style='width:75px;'>";
								echo "<br><br>";
								echo "<input style='float:none;' type='text' name='existingadditionalimage".$a."' value='".$additional['image']."'  autocomplete='off' /><button type='button' onClick='SmallFileBrowser(\"../uploads/\",\"existingadditionalimage".$a."\")'>Choose Image...</button>";
								echo "</div>";
								$a++;
							}
							?>
						</td>
					</tr>
				</table>
			
			
			
			</TD>
			</TR>
			
			<!--
			<TR BGCOLOR="#f2f2f2">
			<Th width="200" height="40" style="padding-left:20px;">Thumbnail Size <?=info('Select the thumbnail size of the main image.');?></Th>
			<TD>			
			<select name='thumbnail_size'>
				<option <? if($_POST['thumbnail_size'] == '94px'){ ?> SELECTED <? } ?> value='94px'>94px</option>
				<option <? if($_POST['thumbnail_size'] == '150px'){ ?> SELECTED <? } ?> value='150px'>150px</option>
				<option <? if($_POST['thumbnail_size'] == '300px'){ ?> SELECTED <? } ?> value='300px'>300px</option>
			</select>
			</TD>
			</TR>	
			-->
			
		</table>		
		<?
		//
		// Submit FORM
		//
		if (isset($_REQUEST["xid"])){
		?>
			<INPUT TYPE=HIDDEN NAME="xid" VALUE="<?=$_REQUEST["xid"]?>">
		<?
		}
		?>
		<div id="submit">
		<a href="?VIEW=<?=$_GET['VIEW']?>">Back</a> &nbsp;&nbsp;&nbsp; 
		<?
		echo "<INPUT TYPE=SUBMIT NAME=\"".strtoupper(str_replace(" ", "_", $button))."\" VALUE=\"$button\">";
		if (isset($_REQUEST['xid'])){
			echo "<INPUT TYPE=SUBMIT NAME=DELETE_ITEM value=\"Delete\" onclick=\"return confirm('Are You Sure?');\">";
		}		
		?>
		</div>
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
	</FORM>
	<?
} 
/**
 *
 * ADD/UPDATE Category Form
 *
 */
elseif($_GET["SUB"] == "NEWCATEGORY" || (isset($_GET["cid"]))){
	if (isset($_REQUEST["cid"])) {
		$select = 	"SELECT * FROM portfolio_categories ".
					"WHERE ".
					"category_id='".$_REQUEST["cid"]."'";
		$res = doQuery($select);
		
		$_POST = mysql_fetch_array($res);
		$button = "Update Category";
		$header = "Category: ";
		$c = "AND `category_id`!='".$_REQUEST['cid']."'";
	} else {
		$button = "Add Category";
		$header = "New Category";
	}
	?>
	<FORM name="user" METHOD=POST ACTION="">
		<?
		echo tableHeader($header.$_POST['name']."",2,'100%');
		?>
			<TR BGCOLOR="#f2f2f2" class="toggleidentifier<?=$identifier ?>">
				<TD width="200" height="40" style="padding-left:20px;">*Category Name:</TD>
				<TD>
				<INPUT TYPE=TEXT NAME="name" size="30" VALUE="<?=$_POST['name']?>" />
				</TD>
			</TR>
			<tr>
				<td>Description</td>
				<td>
					
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<?
					$editor = new wysiwygPro();

					// configure the editor:

					// give the editor a name (equivalent to the name attribute on a regular textarea):
					$editor->name = 'description';
					$CMS = new CMS();
					$currenttheme = $CMS->activeTheme();									
					//$editor->addStylesheet($_SETTINGS['website']."themes/".$currenttheme."scripts/style.css");
					$editor->addStylesheet($_SETTINGS['website']."themes/".$currenttheme."scripts/adminStylesEditor.css");									
					$editor->editImages = 1;
					$editor->upload = 1;
					$editor->deleteFiles = 1;
					$editor->maxImageSize = '10000 MB';
					$editor->maxImageWidth = 100000;
					$editor->maxImageHeight = 100000;
					$editor->maxDocSize = '10000 MB';									
					$editor->disableFeatures(array('previewtab'));
					$editor->escapeCharacters = true;
					
					$editor->imageDir 		= $_SERVER["DOCUMENT_ROOT"].$_SETTINGS['website_path']."uploads/";
					$editor->imageURL 		= $_SETTINGS['website']."uploads";																	
					
					$editor->documentDir 		= $_SERVER["DOCUMENT_ROOT"].$_SETTINGS['website_path']."uploads/";
					$editor->documentURL 		= $_SETTINGS['website']."uploads";
					
					$editor->mediaDir 		= $_SERVER["DOCUMENT_ROOT"].$_SETTINGS['website_path']."uploads/";
					$editor->mediaURL 		= $_SETTINGS['website']."uploads";
					
					// set the content to be edited:
					if($_REQUEST['cid'] != ""){
						$editor->value = $_POST["description"];
					} else {
						$editor->value = $_POST["TextArea"];
					}
					// display the editor, the two paramaters set the width and height:
					$editor->display('900', '400');
					?>
				</td>
			</tr>
		</table>		
		<?
		//
		// Submit FORM
		//
		if (isset($_REQUEST["cid"])){
		?>
			<INPUT TYPE=HIDDEN NAME="cid" VALUE="<?=$_REQUEST["cid"]?>">
		<?
		}
		?>
		<div id="submit">
		<a href="?VIEW=<?=$_GET['VIEW']?>&VIEWCATEGORIES=1">Back</a> &nbsp;&nbsp;&nbsp; 
		<?
		echo "<INPUT TYPE=SUBMIT NAME=\"".strtoupper(str_replace(" ", "_", $button))."\" VALUE=\"$button\">";
		if (isset($_REQUEST['cid'])){
			echo "<INPUT TYPE=SUBMIT NAME=DELETE_CATEGORY value=\"Delete\" onclick=\"return confirm('Are You Sure?');\">";
		}		
		?>
		</div>
	</form>	
<?
}
/**
 *
 * PORTFOLIO SETTINGS
 *
 */
elseif($_REQUEST['SUB'] == 'SETTINGS')
{
	$button = "Update Settings";
	$doing 	= "Portfolio Settings";
	?>
	<FORM method="post" enctype="multipart/form-data" ACTION="" name="settingsform" id="settingsform">
	
		<?
		echo tableHeader("$doing ".$_POST['name']."",2,'100%');

			$sela = "SELECT * FROM settings WHERE active=1 AND group_id='8' ORDER BY type ASC";

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
/**
 *
 * PORTFOLIO CATEGORIES
 *
 */
elseif($_REQUEST['SUB'] == 'CATEGORIES')
{
	// SORTABLE
	?>
	<div class="textcontent1">
		<h1>Portfolio Categories and Organization</h1>
	</div>
	<br />
	<br />	
	<?
	// HEADER
	echo tableHeaderid("",6,"100%","list");
	echo "<thead><TR><th width='600'>Categories &amp; Portfolio Items</th><th>Action</th></TR></thead><tbody>";
	echo "</tbody></table>";	
	// SELECT CATEGORIES
	$select = 	"SELECT * FROM portfolio_categories ".
				"WHERE ".
				"active='1' ORDER BY sort_level ASC".
				"".$_SETTINGS['demosqland']."";
	$res = doQuery($select);
	$num = mysql_num_rows($res);
	$i=0;
	// CREATE SORTABLE LIST
	echo sortableList();		
	while ($row = mysql_fetch_array($res)){
		$default = "";
		if($i % 2) { $class = "odd"; } else { $class = "even"; }
		echo "<li class=\"".$class." selector\" id=\"".$row['category_id']."\"> <span class=\"cat1\"></span> <span>{$row["name"]} {$default}</span>";
			// LEVEL 1 - top level // CATEGORIES FORM
			echo "<FORM class=\"listform\" METHOD=GET ACTION=\"$_SERVER[PHP_SELF]\">";
			echo "<INPUT TYPE=HIDDEN NAME=cid VALUE=\"{$row["category_id"]}\">";
			echo "<INPUT TYPE=HIDDEN NAME=VIEW VALUE=\"{$_GET["VIEW"]}\">";
			echo "<INPUT TYPE=HIDDEN NAME=SUB VALUE=\"{$_GET["SUB"]}\">";
			echo "<INPUT TYPE=SUBMIT NAME=DELETE_CATEGORY VALUE=\"Delete\" onClick=\"return confirm('Are You Sure?');\">";
			echo " <INPUT TYPE=SUBMIT NAME=view VALUE=\"Edit\">";
			echo "</FORM>";
			// LEVEL 1 ITEMS
			// Sortable Script
			echo "<script>$('#sortableproducts1".$i."').sortable();</script>";
			// CREATE SORTABLE LIST
			echo sortableList("products1".$i."");
			// GET ITEMS
			$selectp1 = "SELECT * FROM portfolio_items ". 
						"LEFT JOIN portfolio_category_item_relational ON portfolio_items.item_id=portfolio_category_item_relational.item_id ".
						"WHERE ".
						"portfolio_category_item_relational.category_id='".$row['category_id']."' ".
						"AND portfolio_items.active='1' ".
						"ORDER BY sort_level ASC";
			$resultp1 = doQuery($selectp1);
			$nump1 = mysql_num_rows($resultp1);
			$ip1 = 0;
			while($rowp1 = mysql_fetch_array($resultp1)){
				echo "<li class=\"".$class." selector\" id=\"".$rowp1['item_id']."\"> <span class=\"prod1\"></span> <span>".$rowp1["title"]." ".$default."</span>";
					// LEVEL 1 - Item form
					echo "<FORM class=\"listform\" METHOD=GET ACTION=\"$_SERVER[PHP_SELF]\">";
					echo "<INPUT TYPE=HIDDEN NAME=xid VALUE=\"".$rowp1["item_id"]."\">";
					echo "<INPUT TYPE=HIDDEN NAME=VIEW VALUE=\"".$_GET["VIEW"]."\">";
					echo "<INPUT TYPE=HIDDEN NAME=SUB VALUE=\"".$_GET["SUB"]."\">";
					echo "<INPUT TYPE=SUBMIT NAME=DELETE_ITEM VALUE=\"Delete\" onClick=\"return confirm('Are You Sure?');\">";
					echo " <INPUT TYPE=SUBMIT NAME=view VALUE=\"Edit\">";
					echo "</FORM>";				
				echo "</li>";
				$ip2++;
			}
			echo "</ul>";
			?>
			<script>
			// AJAX REQUEST SORT TOP LEVEL ITEMS
			$( "#sortableproducts1<?=$i ?>" ).bind( "sortstop", function(event, ui) {
			  var result = $('#sortableproducts1<?=$i ?>').sortable('toArray');
			  var resultstring = result.toString();
			  
			  $.ajax({
				  type: 'POST',
				  url: 'modules/portfolio/portfolio.php',
				  data: { sortarray: resultstring, SORT_PRODUCTS: '1', CATEGORY: '<?=$row['category_id']?>' }
				});
			});
			</script>
			<?
		echo "</li>"; // LEVEL 1 END LI
		$i++;
	}
	echo "</ul>"; // LEVEL 1 OUTER UL
	?>
	<script>
		// SORT TOP LEVEL CSS - on sort start
		$( "#sortable" ).bind( "sortstart", function(event, ui) {
			$(ui.item).css("background-color","#f3f8ff");
			$(ui.item).css("border","2px solid #89a8d8");
			$(ui.item).css("cursor","-moz-grabbing");
		});
		
		// AJAX REQUEST SORT TOP LEVEL - on sort stop
		$( "#sortable" ).bind( "sortstop", function(event, ui) {
		  var result = $('#sortable').sortable('toArray');
		  var resultstring = result.toString();
		  
		  $.ajax({
			  type: 'POST',
			  url: 'modules/portfolio/portfolio.php',
			  data: { sortarray: resultstring, SORT_CATEGORIES: '1', LEVEL: '1' }
			});
			// RESET CSS
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
 * PORTFOLIO ITEMS
 *
 */
else
{
	// Search box
	echo '<div class="textcontent">';
		
		$sel = "SELECT * FROM portfolio_items WHERE active='1'";
		$res = doQuery($sel);
		$num = mysql_num_rows($res);
		?>
		<h1>Portfolio Items (<?=$num ?>)</h1>
		<?			
		echo "<FORM METHOD=GET>";
		echo "<INPUT TYPE=HIDDEN NAME=\"VIEW\" VALUE=\"".$_GET["VIEW"]."\">";
		echo "<INPUT TYPE=HIDDEN NAME=\"SUB\" VALUE=\"".$_GET["SUB"]."\">";
		echo "<INPUT TYPE=TEXT NAME=\"KEYWORDS\" VALUE=\"{$_GET["KEYWORDS"]}\">";
		echo " <SELECT NAME=\"COLUMN\">";
			echo "<OPTION VALUE=\"name\"".selected($_GET["COLUMN"],"name").">Account Name</OPTION>";
			echo "<OPTION VALUE=\"username\"".selected($_GET["COLUMN"],"username").">Username</OPTION>";
			echo "<OPTION VALUE=\"email\"".selected($_GET["COLUMN"],"email").">Email</OPTION>";
		echo "</SELECT>";
		echo " <INPUT TYPE=SUBMIT NAME=search VALUE=\"Search\"> <INPUT TYPE=BUTTON NAME=NONE VALUE=\"Reset\" ONCLICK=\"document.location = '{$_SERVER["PHP_SELF"]}?VIEW={$_GET["VIEW"]}&".SID."';\">";
		echo "</FORM>";
		?>
	</div>
	<br />
	<br />
	<?

	// List
	if ($_GET['KEYWORDS']!=""){
		$q = "AND {$_GET['COLUMN']} like '%{$_GET['KEYWORDS']}%'";
	}
	
	$page = 1;
	$size = 15;	 
	
	$select = 	"SELECT * FROM portfolio_items ".
				"WHERE ".
				"active='1' ".
				"$q ".
				"".$_SETTINGS['demosqland']." ".
				"ORDER BY created DESC";
		
	$total_records = mysql_num_rows(doQuery($select)); 
	
	if (isset($_GET['page'])){
		$page = (int) $_GET['page'];
	}
	 
	$pagination = new Pagination();
	$pagination->setLink("index.php?VIEW=".$_REQUEST['VIEW']."&page=%s");
	$pagination->setPage($page);
	$pagination->setSize($size);
	$pagination->setTotalRecords($total_records);
	$select = $select." ".$pagination->getLimitSql()."";
	
	echo tableHeaderid("Portfolio Items",6,"100%","list");
	echo "<thead><TR><th style='width:20px; text-align:center;'>Id</th><th style='text-align:center;'>Image</th><th width='200'>Title</th><th>Categories</th><th>Status</th><th width='90'>Date</th><th width='160'>Action</th></TR></thead><tbody>";
	
	$res = doQuery($select);
	
	$i=0;
	while ($row = mysql_fetch_array($res)){
		if($i % 2) { $class = "odd"; } else { $class = "even"; }
		
		// BUILD CATEGORIES STRING
		$catsel = 	"SELECT * FROM portfolio_categories ".
				"LEFT JOIN portfolio_category_item_relational ON portfolio_categories.category_id=portfolio_category_item_relational.category_id ".
				"LEFT JOIN portfolio_items ON portfolio_category_item_relational.item_id=portfolio_items.item_id ".
				"WHERE portfolio_items.item_id='".$row['item_id']."' AND portfolio_categories.active='1'";
		$catres = doQuery($catsel);
		$catnum = mysql_num_rows($catres);
		$cati = 0;
		$categories = "";
		while($cati<$catnum){
			$catrow = mysql_fetch_array($catres);
			$categories .= "".$catrow['name'].", ";
			$cati++;
		}
		// remove whitespace
		$categories = trim($categories);
		
		echo "<TR class=\"$class\">";
		
		// GET THE THUMBNAIL IMAGE
		$thumbnailImage = $row['thumbnail_image'];
		
		// CHECK IF THE THUMBNAIL EXISTS IN THE UPLOADS DIR
		if(is_file($_SERVER['DOCMENT_ROOT']."/uploads/".$row['thumbnail_image'])){
			$thumbSrc = $_SETTINGS['website']."uploads/".$row['thumbnail_image'];
		}
		// CHECK IF THE THUMBNAIL EXISTS IN THE UPLOADS/THUMBNAILS DIR
		elseif(is_file($_SERVER['DOCUMENT_ROOT']."/uploads/wpThumbnails/".$row['thumbnail_image'].""))
		{
			$thumbSrc = $_SETTINGS['website']."/uploads/wpThumbnails/".$row['thumbnail_image'];
		}
		// ELSE NO THUMBNAIL FOUND GET THE FIRST ADDITIONAL IMAGE OF THE PORTFOLIO
		else
		{
			$selectThumb = "SELECT * FROM portfolio_item_images WHERE item_id='".$row['item_id']."' ORDER BY image_id ASC LIMIT 1";
			$resultThumb = doQuery($selectThumb);
			$rowThumb = mysql_fetch_array($resultThumb);
			if(is_file($_SERVER['DOCUMENT_ROOT']."/uploads/".$rowThumb['image'])){
				$thumbSrc = $_SETTINGS['website']."/uploads/".$rowThumb['image'];
			}
		}
		
		//echo "<br>ROW:<Br>";
		//print_r($row);
		//echo "<br><br>";
		
		//echo "<br>THUMBNAIL IMAGE: ".$_SERVER['DOCUMENT_ROOT']."/uploads/".$row['thumbnail_image']."<br>";
		echo "<TD style='text-align:center;'>".$row['item_id']."</TD>";
		
		echo "<TD style='text-align:center;'><img src='".$thumbSrc."' style='width:94px;' ></TD>";
		
		echo "<TD>".$row["title"]."</TD>";
		
		echo "<TD>".trim($categories,",")."</TD>";
		
		echo "<TD>".$row['status']."</TD>";
		
		echo "<TD>".FormatTimestamp($row["date"]." 00:00:00")."</TD>";
		
		echo "<TD nowrap ALIGN=\"LEFT\">";
			echo "<FORM METHOD=GET ACTION=\"$_SERVER[PHP_SELF]\">";
			echo "<INPUT TYPE=HIDDEN NAME=xid VALUE=\"{$row["item_id"]}\">";
			echo "<INPUT TYPE=HIDDEN NAME=VIEW VALUE=\"{$_GET["VIEW"]}\">";
			if($row['accesslevel'] != "0"){
				echo "<INPUT TYPE=SUBMIT NAME=DELETE_ITEM VALUE=\"Delete\" onClick=\"return confirm('Are You Sure?');\">";
			}
			echo " <INPUT TYPE=SUBMIT NAME=view VALUE=\"Edit\">";
			echo "</FORM>";
		echo "</TD>";
		echo "</TR>";
		$i++;
	}
	echo "</tbody></TABLE>";
	$navigation = $pagination->create_links();
	echo $navigation;
}
?>
