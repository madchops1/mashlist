<?
/*************************************************************************************************************************************
*
*   Copyright (c) 2011 Karl Steltenpohl Development LLC. All Rights Reserved.
*	
*	This file is part of Karl Steltenpohl Development LLC's WES (Website Enterprise Software).
*	Authored By Karl Steltenpohl
*	Commercial License
*	http://www.wescms.com/license
*
*	http://www.wescms.com
*	http://www.webksd.com/wes
* 	http://www.karlsteltenpohl.com/wes
*
*************************************************************************************************************************************/

$CMS = new CMS();	
report($_REQUEST['REPORT'],$_REQUEST['SUCCESS']);

/*** Remove Page ***/
if (isset($_POST["DELETE"]) || isset($_GET["DELETE"])){

	$select = 	"UPDATE pages SET ".
				"`active`='0' ".
				"WHERE ".
				"id='".$_REQUEST["xid"]."' ".
				"".$_SETTINGS['demosqland']."";
	
	doQuery($select);
	header("Location: {$_SERVER["PHP_SELF"]}?VIEW={$_REQUEST["VIEW"]}&REPORT=Page Deleted&SUCCESS=1&".SID);
	exit();
}

/*** Add Page ***/
if (isset($_POST["ADD_PAGE"])){
	$error = 0;
	$_POST = escape_smart_array($_POST);
	/*** Validation ***/
	if($_POST['name'] == ""){ ReportError("Please Fill In Page Name"); $error = 1; }
	if($_POST['template'] == ""){ ReportError("Please Choose Page Template"); $error = 1; }
	
	if($error == 0)
	{
		/*** content area records ***/
		$content_array = $CMS->content_areas($_POST['template']);
		$nnum = count($content_array);
		$n = 0;
		$nextpageid = nextId('pages');
		while($n<$nnum)
		{
			
			$select = 	"INSERT INTO content SET ".
						"id='',".
						"`order`='$n',".
						"page_id='$nextpageid',".
						"content='',".
						"preview='0'".
						"".$_SETTINGS['demosql']."";
			
			doQuery($select);
			$n++;
		}			
		
		/*** homepage ***/
		if($_POST['homepage'] == "1")
		{			
			$select = 	"UPDATE settings SET ".
						"value='".$nextpageid."'".
						"".$_SETTINGS['demosql']." ".
						"WHERE name='Homepage' ";
			
			doQuery($select);
		}		
		
		if($_POST['clean_url_name'] == ""){
			$clean_url_name = strtolower(str_replace(" ","-",$_POST['name']));
		} else {
			$clean_url_name = strtolower(str_replace(" ","-",$_POST['clean_url_name']));
		}
		
		// CHECK IF TITLE IS EMPTY
		if($_POST['title'] == ""){	$_POST['title'] = $_POST['name']; }
		// CONVERT DATE
		$date = DateTimeIntoTimestamp($_POST['date'],'');
		// CHECK MAIN NAV
		if($_POST['parent'] == "mainnav"){ $_POST['parent'] = "0"; $main_nav="1"; } else { $main_nav="0"; }	
		// TEMPLATE // ALTERNATE URL
		if($_POST['template'] == "0"){	$alternate_url = $_POST['alternate_url']; }
		
		$select = 	"INSERT INTO pages SET ".
					"parent='".$_POST['parent']."',".
					"main_nav='".$main_nav."',".
					"ammend_url='".$_POST['ammend_url']."',".
					"name='".$_POST["name"]."',".
					"subtitle='".$_POST["subtitle"]."',".
					"locked='".$_POST["locked"]."',".
					"clean_url_name='".$clean_url_name."',".
					"user_permission='".$_POST["user_permission"]."',".
					"permission_type='".$_POST["permission_type"]."',".
					"title='".$_POST["title"]."',".
					"sort='".$_POST["sort"]."',".
					"status='".$_POST["status"]."',".
					"alternate_url='".$alternate_url."',".
					"description='".$_POST["description"]."',".
					"image='".basename($_POST['image'])."',".
					"keywords='".$_POST["keywords"]."',".
					"date='".$_POST['date']."',".
					"secure='".$_POST['secure']."',".
					"template_path='".$_POST["template"]."'".
					"".$_SETTINGS['demosql'].""; 
		
		doQuery($select);
		
		$report = "Page created successfully.";
		header("Location: {$_SERVER["PHP_SELF"]}?REPORT={$report}&SUCCESS=1&VIEW={$_GET["VIEW"]}&xid={$nextpageid}&".SID);
		exit;
	}
}

/*** Update Page ***/
if (isset($_POST["UPDATE_PAGE"]) OR (isset($_POST['edit']) AND $_POST['PREVIEW_PAGE'] == "")){
	
	$error = 0;
	$_POST = escape_smart_array($_POST);
	/*** Validation ***/
	if($_POST['name'] == ""){ ReportError("Please Fill In Page Name"); $error = 1; }
	if($_POST['template'] == ""){ ReportError("Please Choose Page Template"); $error = 1;  }
	
	if($error == 0)
	{
		/*** content area records ***/
		$content_array = $CMS->content_areas($_POST['template']);
		$nnum = count($content_array); // NUMBER OF CONTENT AREAS IN THE CHOSEN TEMPLATE
		
		/*** IF CHANGING TEMPLATE THE SYSTEM NEEDS TO DETERMINE IF THERE NEEDS TO BE NEW CONTENT AREAS ADDED ***/
		
		$select = 	"SELECT * FROM content WHERE ".
					"page_id='".$_POST['xid']."' AND ".
					"preview='0' ".
					"".$_SETTINGS['demosqland']."";
					
		$resu = doQuery($select);
		$numu = mysql_num_rows($resu); // NUMBER OF CONTENT AREAS CURRENTLY IN DATABASE FOR THIS PAGE
		$numv = $numu; // To be used in loop below
	
		$nextpageid = nextId('pages');
	
		/*** INSERT NEW CONTENT AREAS ***/
		$numa = $nnum - $numu; // IF NEGATIVE THEN NO NEW CONTEN AREAS ARE NEEDED, IF POSITIVE THEN ADD THAT MANY CONTENT AREAS
		if($numa>0)
		{
			$nn = 0;
			while($nn<$numa)
			{
				
				$select = 	"INSERT INTO content SET ".
							"id='',".
							"page_id='".$_POST['xid']."',".
							"content='',".
							"`order`='".$numv."',".
							"`preview`='0'".
							"".$_SETTINGS['demosql']."";
				
				doQuery($select);
				$numv++;
				$nn++;
			}// END while
		}
		
		/*** UPDATE CONTENT AREAS ***/
		$n = 0;
		while($n<$nnum){
		
			$content = $_POST['TextArea'.$n.''];
			$content = str_replace("<p><span>&nbsp;</span></p>","",$content);
		
			$select = 	"UPDATE content SET ".
						"content='".$content."'".
						"".$_SETTINGS['demosql']." ".
						"WHERE ".
						"`order`='$n' AND ".
						"page_id='".$_POST['xid']."' AND ".
						"preview='0' ".
						"".$_SETTINGS['demosqland']."";
						
			doQuery($select);
			$n++;
		}		
		
		/*** homepage ***/
		if($_POST['homepage'] == "1"){
		
			$select = 	"UPDATE settings SET ".
						"value='".$_POST['xid']."'".
						"".$_SETTINGS['demosql']." ".
						"WHERE ".
						"name='Homepage' ".
						"".$_SETTINGS['demosqland']."";
						
			doQuery($select);
		}	
		
		//
		// CHECK MAIN NAV
		//
		if($_POST['parent'] == "mainnav"){ $_POST['parent'] = "0"; $main_nav="1"; } else { $main_nav="0"; }
		
		if($_POST['template'] == "0"){
			$alternate_url = $_POST['alternate_url'];
		}
		
		// CONVERT DATE
		$_POST['date'] = DateTimeIntoTimestamp($_POST['date'],'');
		
		//
		// INSERT RECORD
		//
		$select = 	"UPDATE pages SET ".
					"parent='".$_POST['parent']."',".
					"main_nav='".$main_nav."',".
					"ammend_url='".$_POST['ammend_url']."',".
					"name='".$_POST["name"]."',".
					"subtitle='".$_POST["subtitle"]."',".
					"locked='".$_POST["locked"]."',".
					"title='".$_POST["title"]."',".
					"sort='".$_POST["sort"]."',".
					"description='".$_POST["description"]."',".
					"image='".basename($_POST['image'])."',".
					"keywords='".$_POST["keywords"]."',".
					"clean_url_name='".strtolower(str_replace(" ","-",$_POST['clean_url_name']))."',".
					"user_permission='".$_POST["user_permission"]."',".
					"permission_type='".$_POST["permission_type"]."',".
					"status='".$_POST["status"]."',".
					"alternate_url='".$alternate_url."',".
					"hidden='".$_POST['hidden']."',".
					"date='".$_POST['date']."',".
					"secure='".$_POST['secure']."',".
					"template_path='".$_POST["template"]."'".
					"".$_SETTINGS['demosql']." ".
					"WHERE ".
					"id='".$_POST["xid"]."' ".
					"".$_SETTINGS['demosqland']."";
		doQuery($select);

		//
		// REMOVE ANY ADDITIONAL PURPOSE FOR THIS PAGE
		//
		/*
		$select = 	"UPDATE settings SET value='' WHERE value='".$_POST['clean_url_name']."'";
		doQuery($select);
		
			
		if($_POST['settings_function'] != ''){	
		
			//
			// SET THE ADDITIONAL PURPOSE IF THERE IS ONE
			//
			$select = 	"UPDATE settings SET value='".$_POST['clean_url_name']."' WHERE name='".$_POST['settings_function']."'";
			doQuery($select);
		}
		*/
		$report = "Page updated successfully.";
		header("Location: {$_SERVER["PHP_SELF"]}?REPORT={$report}&SUCCESS=1&VIEW={$_GET["VIEW"]}&xid={$_POST["xid"]}&".SID);
		exit;
	}
}

/*** PREVIEW Page ***/
if (isset($_POST["PREVIEW_PAGE"])){
	
	$error = 0;
	$_POST = escape_smart_array($_POST);
	/*** Validation ***/
	if($_POST['name'] == ""){ ReportError("Please Fill In Page Name"); $error = 1; }
	if($_POST['template'] == ""){ ReportError("Please Choose Page Template"); $error = 1;  }
	
	if($error == 0)
	{
		/*** content area records ***/
		$content_array = $CMS->content_areas($_POST['template']);
		$nnum = count($content_array); // NUMBER OF CONTENT AREAS IN THE CHOSEN TEMPLATE
		
		/*** IF CHANGING TEMPLATE THE SYSTEM NEEDS TO DETERMINE IF THERE NEEDS TO BE NEW CONTENT AREAS ADDED ***/
		
		$select = 	"SELECT * FROM content WHERE ".
					"page_id='".$_POST['xid']."' AND ".
					"preview='1' ".
					"".$_SETTINGS['demosqland']."";
		
		$resu = doQuery($select);
		$numu = mysql_num_rows($resu); // NUMBER OF CONTENT AREAS CURRENTLY IN DATABASE FOR THIS PAGE
		$numv = $numu; // To be used in loop below

		$nextpageid = nextId('pages');
		if($_POST['xid'] == ""){
			$status = "new";
			$_POST['xid'] = $nextpageid;
		} else {
			$status = "update";
		}
		
		/*** INSERT NEW CONTENT AREAS ***/
		$numa = $nnum - $numu; // IF NEGATIVE THEN NO NEW CONTEN AREAS ARE NEEDED, IF POSITIVE THEN ADD THAT MANY CONTENT AREAS
		if($numa>0)
		{
			$nn = 0;
			while($nn<$numa)
			{
			
				$select = 	"INSERT INTO content SET ".
							"id='', ".
							"page_id='".$_POST['xid']."', ".
							"content='', ".
							"`order`='".$numv."', ".
							"`preview`='1'".
							"".$_SETTINGS['demosql']."";
							
				doQuery($select);
				$numv++;
				$nn++;
			}// END while
		}
		
		/*** UPDATE CONTENT AREAS ***/
		$n = 0;
		while($n<$nnum)
		{
			
			$select = 	"UPDATE content SET ".
						"content='".$_POST['TextArea'.$n.'']."'".
						"".$_SETTINGS['demosql']." ".
						"WHERE ".
						"`order`='$n' AND ".
						"page_id='".$_POST['xid']."' AND ".
						"`preview`='1' ".
						"".$_SETTINGS['demosqland']."";
			
			doQuery($select);
			$n++;
		}		

		/*** insert record ***/
		if($status == "new"){
		
			$select = 	"INSERT INTO pages SET ".
						"name='".$_POST["name"]."',".
						"clean_url_name='".strtolower(str_replace(" ","-",$_POST['name']))."',".
						"title='".$_POST["title"]."',".
						"sort='".$_POST["sort"]."',".
						"description='".$_POST["description"]."',".
						"keywords='".$_POST["keywords"]."',".
						"template_path='".$_POST["template"]."'".
						"".$_SETTINGS['demosql']."";
			
			doQuery($select);
		}

		header("Location: {$_SERVER["PHP_SELF"]}?PREVIEW=1&VIEW={$_GET["VIEW"]}&xid={$_POST["xid"]}&".SID);
		exit();
	}
}

if($_REQUEST['PREVIEW']){
	$sel = "SELECT * FROM pages WHERE id='".$_REQUEST['xid']."'";
	$res = doQuery($sel);
	$row = mysql_fetch_array($res);
	?>	
	<script>
	window.open("<?=$_SETTINGS['website']?><?=strtolower(str_replace(" ","-",$row['clean_url_name']))?>/1/1/1/1");
	</script>
	<?
}

/*** Update Layout ***/
if (isset($_POST["UPDATE_LAYOUT"])){
	$error = 0;
	$_POST = escape_smart_array($_POST);
	
	if($error == 0)
	{
		/*** CHECK IF THERE IS A CONTENT AREA FOR THE LAYOUT AT THAT ORDER***/
		
		$sel = 	"SELECT * FROM content WHERE ".
				"layout='1' AND ".
				"`order`='".$_POST['order']."' ".
				"".$_SETTINGS['demosqland']."";
				
		$res = doQuery($sel);
		$nu = mysql_num_rows($res);
		
		if($nu==0)
		{
			
			$sel = 	"INSERT INTO `content` SET ".
					"`layout`='1',".
					"content='".$_POST['TextArea0']."',".
					"`order`='".$_POST['order']."',".
					"page_id='',".
					"preview='0'".
					"".$_SETTINGS['demosql']."";
			
			$res = doQuery($sel);
		}
		
		/*** UPDATE ***/
		
		$sel = 	"UPDATE content SET ".
				"content='".$_POST['TextArea0']."',".
				"page_id='',".
				"preview='0'".
				"".$_SETTINGS['demosql']." ".
				"WHERE ".
				"`layout`='1' AND ".
				"`order`='".$_POST['order']."'";
				
		$res = doQuery($sel);
		
		$report = "".$_POST['content_name']." updated successfully.";
		header("Location: {$_SERVER["PHP_SELF"]}?REPORT={$report}&SUCCESS=1&VIEW={$_GET["VIEW"]}&LAYOUT=1&order=".$_POST['order']."&content_name={$_POST["content_name"]}&".SID);
		exit;
	}
}

/*** PREVIEW Layout Page ***/
if (isset($_POST["PREVIEW_LAYOUT"])){
	
	$error = 0;
	$_POST = escape_smart_array($_POST);
	if($error == 0)
	{
		/*** CHECK IF THERE IS A CONTENT AREA FOR THE LAYOUT AT THAT ORDER***/
		
		$sel = 	"SELECT * FROM content WHERE ".
				"layout='1' AND ".				
				"preview='1' AND ".
				"`order`='".$_POST['order']."' ".
				"".$_SETTINGS['demosqland']."";
		
		$res = doQuery($sel);
		if(mysql_num_rows($res) < 1)
		{
		
			$sel = 	"INSERT INTO content SET ".
					"layout='1',".
					"`order`='".$_POST['order']."',".
					"preview='1'".
					"".$_SETTINGS['demosql']."";
					
			$res = doQuery($sel);
		}
		
		/*** UPDATE ***/
		
		$sel = 	"UPDATE content SET ".
				"content='".$_POST['TextArea0']."',".
				"preview='1'".
				"".$_SETTINGS['demosql']." ".
				"WHERE ".
				"layout='1' AND ".
				"preview='1' AND ".
				"`order`='".$_POST['order']."' ".
				"".$_SETTINGS['demosqland']."";
				
		$res = doQuery($sel);
		
		//$report = "".$_POST['content_name']." updated successfully.";
		header("Location: {$_SERVER["PHP_SELF"]}?PREVIEW_LAYOUT=1&VIEW={$_GET["VIEW"]}&LAYOUT=1&order=".$_POST['order']."&content_name={$_POST["content_name"]}&".SID);
		exit;
	}
}

if($_REQUEST['PREVIEW_LAYOUT']){
	$pagepreview = $CMS->activeHomepage();
	?>	
	<script>
	//window.open("<?=$_SETTINGS['website']?>index.php?PREVIEW=1");
	window.open("<?=$_SETTINGS['website']?><?=strtolower(str_replace(" ","-",$pagepreview))?>/1/1/1/1");
	</script>
	<?
}

/*** UPDATE / Add PAGE FORM ***/
if (isset($_GET["ADDNEW"]) || isset($_GET["xid"]))
{
	// get page to modify
	if (isset($_REQUEST["xid"]))
	{
		$select = 	"SELECT * FROM pages WHERE ".
					"id=".$_REQUEST["xid"]." ".
					"".$_SETTINGS['demosqland']."";
					
		$res = doQuery($select);
		$_POST = mysql_fetch_array($res);
		$button = "Update Page";
	}
	else
	{
		$button = "Add Page";
	}
	
	echo tableHeader("Page Information",2,'100%');
	?>
	<form name="wesform" id="wesform" method="POST" action="<?=$_SERVER["PHP_SELF"]?>?VIEW=<?=$_GET["VIEW"]?>&ADDNEW=1&<?=SID?>" >
		
		<TR>
		<Th>*Name <?=info('A simple name for your page.');?></Th>
		<TD>
		<INPUT TYPE=TEXT NAME="name" VALUE="<?=$_POST['name']?>">		
		</TD>
		</TR>
		
		<TR>
		<Th>*Subtitle <?=info('A page can have a sub title if your theme incorporates the feature.');?></Th>
		<TD>
		<INPUT TYPE=TEXT NAME="subtitle" VALUE="<?=$_POST['subtitle']?>">		
		</TD>
		</TR>
		
		<TR>
		<Th>*Status <?=info('Select the status of this page. Choose draft if this page is a work in progress. Choose pending to set a publish date in the future. Choose published to make this page live.');?></Th>
		<TD>
			<select name="status">
				<option value="Draft" <? if($_POST['status'] == "Draft"){ ?> SELECTED <? } ?> >Draft</option>
				<option value="Pending" <? if($_POST['status'] == "Pending"){ ?> SELECTED <? } ?> >Pending</option>
				<option value="Published" <? if($_POST['status'] == "Published"){ ?> SELECTED <? } ?> >Published</option>
			</select>
		</TD>
		</TR>
		
		<script type="text/javascript">
				$(function() {
					$(".datepicker").datepicker();
				});
		</script>
		<TR>
		<Th>*Date</Th>
		<TD>
			<?
			if($_POST['date'] == ""){ $_POST['date'] = date("m/d/Y"); } else { $_POST['date'] = TimestampIntoDate($_POST['date']); }
			?>
			<input type='text' name='date' class='datepicker' value='<?=$_POST['date']?>' >
		</TD>
		</TR>
		
		
		<?
		if (isset($_REQUEST["xid"])) {
		?>
			<TR>
			<?
			//$link = str_replace(" ","_",$_POST['name']);
			?>
			<Th>Link <?=info('This is the URL of this page. Use it to link to this page from other places.');?></Th>
			<TD>
			<strong style="float:left;">
			<?=$_SETTINGS['website']?><?=$_POST['clean_url_name']?><?=$_POST['ammend_url']?>
			</strong>			
			</TD>
			</TR>
		<?
			}
		?>	
		
		<TR>
			<Th>
				*Template  <?=info('Your website design has various templates for different page layouts, and purposes. Choose the template that suits your needs for this page. Select alternate URL if you want to redirect visitors to this page somewhere else.');?>
			</Th>
			<TD>
				<?
				$template_array = $CMS->templates();
				$num = count($template_array);
				?>
				
				<? if(isset($_REQUEST['xid'])){ ?><input type="hidden" name="edit" value="1" /><? } ?>
				<script>
				function templatechange(thisone){
					thisone.form.submit();
				}
				</script>
				<select NAME="template" <? if(isset($_REQUEST['xid'])){ ?>onchange="templatechange(this);"<? } ?>>
					<?
					$i=0;
					while($i<$num)
					{
						?>
						<option value="<?=$template_array[$i]['file']?>" <? if($_POST['template_path']==$template_array[$i]['file']){ ?>SELECTED<? } ?>><?=$template_array[$i]['name']?></option>
						<?
						$i++;
					}//END while
					?>
					<option value="0" <? if($_POST['template_path'] == '0'){ ?> SELECTED <? } ?>>No Template - Alternate URL</option>
					</select>
			</TD>
		</TR>		
		
		
		
		<?
		/**
		 *
		 * ALTERNATE URL
		 *
		 */
		if($_POST['template_path'] == '0'){ 
			?>
			<TR>
				<Th>Alternate URL  <?=info('Enter an alternate URL if you would like users to be redirected somewhere else when they arrive on this page.');?></Th>
				<TD>
				<INPUT TYPE=TEXT NAME="alternate_url" size="75" VALUE="<?=$_POST['alternate_url']?>" />				
				</TD>
			</TR>
			<?
		}
		?>
		
		<?
		/**
		 *
		 * NAVIAGION / PARENT
		 *
		 */
		?>		
		<TR>
			<Th>
				Navigation / Parent  <?=info('Select main navigaion if you want this page to appear in the main nav. Select a page to add this page as a drop-down item in the main nav. The selected page must be in the main nav. Choose none if you don\'t want this page to be in the main navigation.');?>
			</Th>
			<TD>
				<select NAME="parent">
				<option value="none" <? if($_POST['parent'] == 'none' OR ($_POST['parent'] == '0' AND $_POST['main_nav'] == '0')){ ?> SELECTED <? } ?>>None</option>
				<option value="mainnav" <? if($_POST['parent'] == 'mainnav' OR ($_POST['parent'] == '0' AND $_POST['main_nav'] == '1') ){ ?> SELECTED <? } ?> >Main Navigation</option>
				<?
				/*** SELECT 1st LEVEL ***/
				
				if($_REQUEST['xid'] != ''){
					$qq = "(`id`!='".$_REQUEST['xid']."') AND ";
				}
				
				$sel1 =	"SELECT * FROM pages WHERE ".
						"1=1 AND ".$qq."".
						"active='1' ".
						"".$_SETTINGS['demosqland']." ".
						"ORDER BY sort ASC";
				
				$res1 = doQuery($sel1);
				$num1 = mysql_num_rows($res1);
				$i1 = 0;
				while($i1<$num1)
				{
					$row1 = mysql_fetch_array($res1);
					?>
					<option value="<?=$row1['id']?>" <? if($_POST['parent']==$row1['id']){ ?> SELECTED <? } ?>><?=$row1['name']?></option>
					<?
					//
					// SELECT 2nd LEVEL
					//
					$sel2 = "SELECT * FROM pages WHERE ".
							"parent='".$row1['id']."' AND ".$qq."".
							"active='1' ".
							"".$_SETTINGS['demosqland']." ".
							"ORDER BY sort ASC";
					
					$res2 = doQuery($sel2);
					$num2 = mysql_num_rows($res2);
					$i2 = 0;
					while($i2<$num2)
					{
						$row2 = mysql_fetch_array($res2);
						?>
						<option style="margin-left:10px;" value="<?=$row2['id']?>" <? if($_POST['parent']==$row2['id']){ ?> SELECTED <? } ?>><?=$row2['name']?></option>
						<?
						//
						// SELECT 2nd LEVEL
						//
						$sel3 = "SELECT * FROM pages WHERE ".
								"parent='".$row2['id']."' AND ".$qq."".
								"active='1' ".
								"".$_SETTINGS['demosqland']." ".
								"ORDER BY sort ASC";
						
						$res3 = doQuery($sel3);
						$num3 = mysql_num_rows($res3);
						$i3 = 0;
						while($i3<$num3)
						{
							$row3 = mysql_fetch_array($res3);
							?>
							<option style="margin-left:20px;" value="<?=$row3['id']?>" <? if($_POST['parent']==$row3['id']){ ?> SELECTED <? } ?>><?=$row3['name']?></option>
							<?						
							$i3++;
						}					
						$i2++;
					}
					$i1++;
				}//END while
				?>
				</select>	
			</TD>
		</TR>
		
		<Tr>
		<th>Page Image</th>
		<td>
			<input style="float:none;" type="text" name="image" value="<?=$_POST['image']?>" /><button type="button" onClick="SmallFileBrowser('../uploads/','image')">Choose Image...</button><br><br>			
		</td>
		</tr>
		
		<?
		//
		// ADVANCED OPTIONS
		//
		?>
		
		<tr class="toggleropenidentifier">
		<Th>&nbsp;</th>
		<td><a class="toggleridentifier tog">Open Advanced Options</a></td>
		</tr>
		
		<tr class="toggleidentifier">
		<Th>&nbsp;</th>
		<td><a class="togglercloseidentifier tog">Close Advanced Options</a></td>
		</tr>
		
		<TR class="toggleidentifier">
		<Th>Clean URL Name  <?=info('This is the clean URL of the page. For example: www.yoursitehere.com/about. If this is what you wanted your about page URL to be you would enter a value of "about" in this field. It is very beneficial for SEO strategies since Google recognizes the connection between the page URL and the page\'s content. The value must be a unique name, meaning no other page can share the same clean URL name, and it cannot contain spaces or certain symbols.');?></Th>
		<TD>
		<INPUT TYPE=TEXT NAME="clean_url_name" VALUE="<?=$_POST['clean_url_name']?>" size=40'>
		</TD>
		</TR>
		
		<TR class="toggleidentifier">
		<Th>Sort Order  <?=info('Sets the order of your main navigation, and sub-menus. Can be a decimal for quick changes.');?></TH>
		<TD>
		<INPUT TYPE=TEXT NAME="sort" VALUE="<?=$_POST['sort']?>" size=40'>
		</TD>
		</TR>

		<TR class="toggleidentifier">
		<Th>Secure Page  <?=info('Redirects page to https:// mode.');?></TH>
		<TD>
		<?
		if($_POST['secure'] == '1'){ $securechecked = " CHECKED "; }
		?>
		<INPUT TYPE=checkbox NAME="secure" VALUE="1" <?=$securechecked ?>>
		</TD>
		</TR>
		
		
		<?
		if(checkActiveModule('0000005')){
			if($_REQUEST['xid'] != ""){
				if($_REQUEST['xid'] != $_SETTINGS['homePage']){
					?>
					<TR class="toggleidentifier">
					<Th>Permission <?=info('Set the permission level users must have to access this page.')?></TH>
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
					</TD>
					</TR>
					
					
					<TR class="toggleidentifier">
					<Th>Authentication Method <?=info('If the authentication method is set to Hierarchical then the only accounts of that permission level and higher will be authorized. If the method is set to Sole Access then only accounts with the selected permission will be authorized.')?></TH>
					<TD>
					<input type="radio" name="permission_type" <? if($_POST['permission_type'] == 'Hierarchical'){ ?> CHECKED <? } ?> value="Hierarchical" /> Hierarchical<br><br>
					<input type="radio" name="permission_type" <? if($_POST['permission_type'] == 'Sole Access'){ ?> CHECKED <? } ?> value="Sole Access" /> Sole Access					
					</TD>
					</TR>
					<?
				}
			}
		}
		?>
		
		<TR class="toggleidentifier">
		<Th>Title <?=info('The meta tag title value.')?></TH>
		<TD><INPUT TYPE=TEXT NAME="title" VALUE="<?=$_POST['title']?>" size=40'></TD>
		</TR>
		
		<TR class="toggleidentifier"><Th>Keywords <?=info('Improve search engine results by optimizing your pages with keywords.')?></TH>
		<TD><INPUT TYPE=TEXT NAME="keywords" VALUE="<?=$_POST['keywords']?>">
		</TD></TR>
		
		<tr class="toggleidentifier">
			<th valign="top">Description <?=info('Improve search engine results by optimizing your pages with descriptions.')?></th>
			<td>
				<textarea cols=40 rows=3 NAME="description"><?=$_POST['description']?></textarea>
			</td>
		</tr>
		
		<TR class="toggleidentifier">
		<Th>Ammend To Navigation URL <?=info('This is a legacy field. So its probably not important and should be left blank. Some pages require a value to be ammended to the URL to trigger a certain feature of that page. If this field holds a value in it, its safe to leave it there if you don\'t know what its for.')?></TH>
		<TD><INPUT TYPE=TEXT NAME="ammend_url" VALUE="<?=$_POST['ammend_url']?>" size=50'>
		</TD>
		</TR>
		
		<tr class="toggleidentifier">
		<?		
		$select = 	"SELECT value FROM `settings` WHERE ".
					"`name`='Homepage' ".
					"".$_SETTINGS['demosqland']."";
					
		$result = mysql_query($select) or die("ERR");
		$row = mysql_fetch_array($result);
		?>
		<Th>Homepage <?=info('Check this box if you want this page to be your website homepage.')?></TH>
		<td><input type="checkbox" name="homepage" value="1" <? if($row['value']==$_REQUEST['xid']){ ?>CHECKED<? } ?> />
		</td>
		</tr>
		
		<tr class="toggleidentifier">
		<Th>Hidden Page <?=info('Make this a hidden page.')?></TH>
		<td><input type="checkbox" name="hidden" value="1" <? if($_POST['hidden']=='1'){ ?>CHECKED<? } ?> />
		</td>
		</tr>
		
		<tr class="toggleidentifier">
		<Th>Locked <?=info('Pages can be locked so that they cannot be accidentally deleted. Do not unlock a page unless you are sure you want to unlock the page.')?></TH>
		<td><input type="checkbox" name="locked" value="1" <? if($_POST['locked']=='1'){ ?>CHECKED<? } ?> />
		</td>
		</tr>
		
		<?
		if(isset($_REQUEST["xid"]))
		{	
			
			/*
			//
			// PAGE FUNCTIONALITY
			//
			?>
			<tr class="toggleidentifier">
			<Th>Additional Page Purpose</TH><TD>			
			<ul style="list-style:none; margin:0px; padding:0px;">				
			<?			
			//
			// CHECK FOR PAGE FUNCTIONALITY
			//
			$selectk = "SELECT * FROM settings WHERE `type`='page' AND active='1'";
			$resultk = doQuery($selectk);
			$ik = 0;
			$ij = 0;
			$numk = mysql_num_rows($resultk);
			while($ik<$numk){
				$rowk = mysql_fetch_array($resultk);
				
				?>
				<li style='float:left; width:200px;'><input type="radio" name="settings_function" <? if($rowk['value'] == $_POST['clean_url_name']){ $ij = 1; ?> CHECKED <? } ?> value="<?=$rowk['name']?>"><?=$rowk['user_friendly_name'] ?></li>
				<?
				
				$ik++;
			}		
			?>
			<li><input type="radio" name="settings_function" <? if($ij == 0){ ?> CHECKED <? } ?> value="">No Additional Purpose</li>
			</ul>			
			</td></tr>
			*/
			
			//
			// WYSIWYG
			//
			$template = $CMS->get_template();
			$content_array = $CMS->content_areas($template);
			$jnum = count($content_array);
			if($jnum != 0){
				?>
				<tr><td colspan="2">
				
				<h2>Editable Areas:</h2>
				
				<?
				/**
				 * 
				 * Content Areas
				 *
				 */
				// GET CURRENT TEMPLATE
				
				//var_dump($content_array);
				//exit();				
				?>
				
				<script type="text/javascript">
				$(function() {
					$("#tabs").tabs();
				});
				</script>
				<div class="demo">
					<div id="tabs">
						<ul>
							<?
							$j = 0;
							foreach($content_array as $contenter)
							{	
								?>
								<li><a href="#tabs-<?=$j?>"><?=trim($contenter['name'],"'")?></a></li>
								<?
								$j++;
							}
							?>							
						</ul>
					<?
					$j = 0;
					foreach($content_array as $contenter)
					{
						//echo togglertableHeader(trim($content_array[$j]['name'],"'"),'100%',$j);	
						//echo toggletableHeader(1,'100%',$j);
						?>
						<div id="tabs-<?=$j?>">
							<table>
								<tr>
									<td colspan="2">
										<?
										$editor = new wysiwygPro();

										// configure the editor:

										// give the editor a name (equivalent to the name attribute on a regular textarea):
										$editor->name = 'TextArea'.$j.'';
										
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
										
										$editor->documentDir 	= $_SERVER["DOCUMENT_ROOT"].$_SETTINGS['website_path']."uploads/";
										$editor->documentURL 	= $_SETTINGS['website']."uploads";
										
										$editor->mediaDir 		= $_SERVER["DOCUMENT_ROOT"].$_SETTINGS['website_path']."uploads/";
										$editor->mediaURL 		= $_SETTINGS['website']."uploads";
										
										if($_REQUEST['PREVIEW']){
											// set the content to be edited:
											
											$sel = 	"SELECT * FROM content WHERE ".
													"`order`='$j' AND ".
													"page_id='".$_REQUEST['xid']."' AND ".
													"preview='1' AND ".
													"layout='0' ".
													"".$_SETTINGS['demosqland']."";
													
											$res = doQuery($sel);
											$ro = mysql_fetch_array($res);
											$editor->value = $ro["content"];
										}
										else
										{
											// set the content to be edited:
											
											$sel = 	"SELECT * FROM content WHERE ".
													"`order`='$j' AND ".
													"page_id='".$_REQUEST['xid']."' AND ".
													"preview='0' AND ".
													"layout='0' ".
													"".$_SETTINGS['demosqland']."";
											
											$res = doQuery($sel);
											$ro = mysql_fetch_array($res);
											$editor->value = $ro["content"];
										}
										
										// display the editor, the two paramaters set the width and height:
										
										if($_SETTINGS['debug'] == '1'){
											echo '<br>$image_dir:'.$_SERVER["DOCUMENT_ROOT"].$_SETTINGS['website_path'].'uploads/'.'<br>';
											echo '<br>$image_url:'.$_SETTINGS['website'].'uploads'.'<br>';
										}
										
										$editor->display('900', '400');
										?>
									</td>
								</tr>
							</table>
						</div>
						<?
						$j++;
					}//END while
					?>
					</div>
				</div>
			</td></tr>
			<? 
			} 
		}//END isset
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
	
	</td></tr></table>
	
	<div id="submit">
	<?
	echo "<INPUT TYPE=SUBMIT NAME=\"".strtoupper(str_replace(" ", "_", $button))."\" VALUE=\"$button\">";
	if (isset($_REQUEST["xid"])) {
		echo "<INPUT TYPE=HIDDEN NAME='xid' VALUE='".$_REQUEST["xid"]."'>";
		echo "<a style=\"margin:0px 10px;\" target=\"_blank\" href=\"".$_SETTINGS['website'].$_POST['clean_url_name']."\">Go To Page</a> &nbsp;&nbsp;";
		echo "<INPUT TYPE=SUBMIT NAME=DELETE value=\"Delete\" onclick=\"return confirm('Are You Sure?');\">";
		echo "<INPUT TYPE=SUBMIT NAME=\"PREVIEW_PAGE\" VALUE=\"Preview\">";
	}
	?>
	</div>
	
	</form>
	<?
}
/*** EDIT LAYOUT CONTENT AREA FORM ***/
elseif($_REQUEST['content_name'] != "")
{
	if (isset($_REQUEST["content_name"]))
	{
		$button = "Update Layout";
	}
	echo tableHeader('Edit Content',2,'100%');
	?>
	<FORM name="user" METHOD=POST ACTION="<?=$_SERVER["PHP_SELF"]?>?VIEW=<?=$_GET["VIEW"]?>&ADDNEW=1&<?=SID?>">
		<tr><td colspan="2">	
		
		<?
		if(isset($_REQUEST["content_name"]))
		{

			?>
			<script type="text/javascript">
			$(function() {
				$("#tabs").tabs();
			});
			</script>
			<div class="demo">
				<div id="tabs">
					<ul>
						<? $j = 0; ?>
						<li><a href="#tabs-<?=$j?>"><?=$_REQUEST['content_name']?></a></li>
					</ul>
				<? $j = 0; ?>
					<div id="tabs-<?=$j?>">
						<table>
							<tr>
								<td colspan="2">
									<?
									$editor = new wysiwygPro();

									// configure the editor:

									// give the editor a name (equivalent to the name attribute on a regular textarea):
									$editor->name = 'TextArea'.$j.'';
									
									
									$currenttheme = $CMS->activeTheme();									
									$editor->addStylesheet($_SETTINGS['website']."themes/".$currenttheme."scripts/style.css");
									$editor->addStylesheet("scripts/adminStylesEditor.css");									
									$editor->editImages = 1;
									$editor->upload = 1;
									$editor->deleteFiles = 1;
									$editor->maxImageSize = '10000 MB';
									$editor->maxImageWidth = 100000;
									$editor->maxImageHeight = 100000;
									$editor->maxDocSize = '10000 MB';									
									$editor->disableFeatures(array('previewtab'));
									$editor->escapeCharacters = true;
									
									$editor->imageDir = $_SERVER["DOCUMENT_ROOT"].$_SETTINGS['website_path']."uploads/";
									$editor->imageURL = $_SETTINGS['website']."uploads";																	
									$editor->documentDir = $_SERVER["DOCUMENT_ROOT"].$_SETTINGS['website_path']."uploads/";
									$editor->documentURL = $_SETTINGS['website']."uploads";
									$editor->mediaDir = $_SERVER["DOCUMENT_ROOT"].$_SETTINGS['website_path']."uploads/";
									$editor->mediaURL = $_SETTINGS['website']."uploads";
									
									if($_REQUEST['PREVIEW']){
										// set the content to be edited:
										
										$sel = 	"SELECT * FROM content WHERE ".
												"`order`='".$_REQUEST['order']."' AND ".
												"layout='1' AND ".
												"preview='1' ".
												"".$_SETTINGS['demosqland']."";
												
										$res = doQuery($sel);
										$ro = mysql_fetch_array($res);
										$editor->value = $ro["content"];
									}
									else
									{
										// set the content to be edited:
										
										$sel = 	"SELECT * FROM content WHERE ".
												"`order`='".$_REQUEST['order']."' AND ".
												"layout='1' AND ".
												"preview='0' ".
												"".$_SETTINGS['demosqland']."";
										
										$res = doQuery($sel);
										$ro = mysql_fetch_array($res);
										$editor->value = $ro["content"];
									}
									
									// display the editor, the two paramaters set the width and height:
									$editor->display('900', '400');
									?>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
			<?
		}//END isset
	?>
	</td></tr></table>
		<div id="submit">
		<?
		echo "<INPUT TYPE=SUBMIT NAME=\"PREVIEW_LAYOUT\" VALUE=\"Preview\">";
		echo "<input type=\"hidden\" name=\"order\" value=\"".$_REQUEST['order']."\">";
		echo "<input type=\"hidden\" name=\"content_name\" value=\"".$_REQUEST['content_name']."\">";
		echo "<INPUT TYPE=SUBMIT NAME=\"".strtoupper(str_replace(" ", "_", $button))."\" VALUE=\"$button\">";
		?>
		</div>
	</FORM>
	<?
}
/*** LIST LAYOUT CONTENT AREAS ***/
elseif($_REQUEST['LAYOUT']=="1" AND $_REQUEST['content_name']=='')
{
	/*** NO Search box ***/
	?>
	<div class="textcontent1">
		<h1>Other Content</h1>
	</div>
	<br /><br />
	<?
	$page = 1;
	$size = 15;	 
	$template_array = $CMS->layout_content_areas();
	$total_records = count($template_array); 
	if (isset($_GET['page'])){
		$page = (int) $_GET['page'];
	}
	// create the pagination class
	$pagination = new Pagination();
	$pagination->setLink("index.php?VIEW=".$_REQUEST['VIEW']."&LAYOUT=1&page=%s");
	$pagination->setPage($page);
	$pagination->setSize($size);
	$pagination->setTotalRecords($total_records);

	echo tableHeaderid("Other Content",6,"100%","list");	
	echo "<thead><TR><th>Name</th><th>Action</th></TR></thead><tbody>";
	
	$i=0;	
	foreach ($template_array as $row) {
		if($i % 2) { $class = "odd"; } else { $class = "even"; }
		echo "<TR class=\"$class\">";
		echo "<TD>{$row['name']}</TD>";
		echo "<TD nowrap ALIGN=\"LEFT\">";
			echo "<FORM METHOD=GET ACTION=\"$_SERVER[PHP_SELF]\">";
			
			echo "<INPUT TYPE=HIDDEN NAME=\"content_name\" VALUE=\"".$row['name']."\">";
			echo "<INPUT TYPE=HIDDEN NAME=\"order\" VALUE=\"".$row['order']."\">";
			
			echo "<INPUT TYPE=HIDDEN NAME=VIEW VALUE=\"{$_GET["VIEW"]}\">";
			echo "<INPUT TYPE=HIDDEN NAME=\"".session_name()."\" VALUE=\"".session_id()."\">";
			echo "<INPUT TYPE=SUBMIT NAME=view VALUE=\"Edit\">";
			echo "</FORM>";
		echo "</TD>";
		echo "</TR>";
		$i++;
	}
	
	echo "</tbody></TABLE>";
	$navigation = $pagination->create_links();
	echo $navigation; // will draw our page navigation
}
/*** LIST WEBSITE PAGES ***/
else
{
	/*** Search box ***/
	?>
	<div class="textcontent">
		<h1>Website Pages</h1>
		<?
		echo "<FORM METHOD=GET>";
		echo "<INPUT TYPE=HIDDEN NAME=\"VIEW\" VALUE=\"".$_GET["VIEW"]."\">";
		echo "<INPUT TYPE=HIDDEN NAME=\"SUB\" VALUE=\"".$_GET["SUB"]."\">";
		echo "<INPUT TYPE=HIDDEN NAME=\"".session_name()."\" VALUE=\"".session_id()."\">";
		echo "<INPUT TYPE=TEXT NAME=\"KEYWORDS\" VALUE=\"{$_GET["KEYWORDS"]}\">";
		echo " <SELECT NAME=\"COLUMN\">";
			echo "<OPTION VALUE=\"name\"".selected($_GET["COLUMN"],"name").">Name</OPTION>";
			echo "<OPTION VALUE=\"clean_url_name\"".selected($_GET["COLUMN"],"clean_url_name").">Page</OPTION>";
			echo "<OPTION VALUE=\"title\"".selected($_GET["COLUMN"],"title").">Title</OPTION>";
		echo "</SELECT>";
		echo " <INPUT TYPE=SUBMIT NAME=search VALUE=\"Search\"> <INPUT TYPE=BUTTON NAME=NONE VALUE=\"Reset\" ONCLICK=\"document.location = '{$_SERVER["PHP_SELF"]}?VIEW={$_GET["VIEW"]}&".SID."';\">";
		echo "<input type='checkbox' ";
		if($_REQUEST['hidden'] == '1'){ echo " CHECKED "; }
		echo " name='hidden' value='1'> <small>Show Hidden</small>";
		echo "</FORM>";
		?>
	</div>
	<br /><br />
	<?	
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
	$size = 50;	 
	
	$select = 	"SELECT * FROM pages WHERE ".
				"active='1' AND 1=1 ".
				"$q ".
				"$h ".
				"".$_SETTINGS['demosqland']." ".
				"ORDER BY main_nav DESC,name ASC ";
				
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
	
	$SQL = 	"SELECT * FROM pages WHERE ".
			"active='1' AND 1=1 ".
			"$q ".
			"$h ".
			"".$_SETTINGS['demosqland']." ".
			"ORDER BY main_nav DESC,name ASC ".
			"".$pagination->getLimitSql()."";	

	echo tableHeaderid("Website Pages",6,"100%","list");	
	echo "<thead><TR><th>Name</th><th>URL</th><th>Title</th><th>Parent</th><th>Action</th></TR></thead><tbody>";	
	$res = doQuery($SQL);
	
	$i=0;	
	while ($row = mysql_fetch_array($res)) {
		if($i % 2) { $class = "odd"; } else { $class = "even"; }
		if($row['hidden'] == '1'){ $class .= " hiddenrow"; }
		echo "<TR class=\"$class\">";
		
		$secure = "";
		if($row['secure'] == '1'){ $secure = " - SECURE "; }
		
		echo "<TD>".$row["name"]."".$secure."</TD>";
		echo "<TD>/".$row['clean_url_name']."</TD>";
		echo "<TD>{$row["title"]}</TD>";
		
		//
		// FORMAT PARENT
		//
		if($row['parent'] == '0'){
			if($row['main_nav'] == '1'){ $parent = "Main Navigation"; } else { $parent = "None"; }
		} else {
			$parent = lookupDbValue('pages', 'name', $row['parent'], 'id');
		}
		echo "<TD>".$parent."</TD>";
		echo "<TD width=\"150\" nowrap ALIGN=\"LEFT\">";
			echo "<FORM METHOD=GET ACTION=\"$_SERVER[PHP_SELF]\">";
			echo "<INPUT TYPE=HIDDEN NAME=xid VALUE=\"{$row["id"]}\">";
			echo "<INPUT TYPE=HIDDEN NAME=VIEW VALUE=\"{$_GET["VIEW"]}\">";
			echo "<INPUT TYPE=HIDDEN NAME=\"".session_name()."\" VALUE=\"".session_id()."\">";
			echo "<INPUT TYPE=SUBMIT NAME=DELETE VALUE=\"Delete\" onClick=\"return confirm('Are You Sure?');\"> <INPUT TYPE=SUBMIT NAME=view VALUE=\"Edit\">";
			echo "</FORM>";
		echo "</TD>";
		echo "</TR>";
		$i++;
	}
	echo "</tbody></TABLE>";
	$navigation = $pagination->create_links();
	echo $navigation; // will draw our page navigation
}
?>
