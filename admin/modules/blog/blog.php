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
$Blog = new Blog();	
report($_REQUEST['REPORT'],$_REQUEST['SUCCESS']);



/*** POST CRUD  ***/
/**************************
* Remove POST
**************************/
if (isset($_POST["DELETE_POST"]) || isset($_GET["DELETE_POST"])) {/*{{{*/
	doQuery("DELETE FROM blog WHERE blog_id=".$_REQUEST["pid"]." ".$_SETTINGS['demosqland']."");
	doQuery("DELETE FROM blog_category_relational WHERE blog_id=".$_REQUEST["pid"]." ".$_SETTINGS['demosqland']."");
	header("Location: {$_SERVER["PHP_SELF"]}?VIEW={$_REQUEST["VIEW"]}&REPORT=Post Deleted&SUCCESS=1&".SID);
	exit();
}/*}}}*/

/**************************
* Add Post
**************************/
if (isset($_POST["ADD_POST"]))
{
	$error = 0;
	/*** Validation ***/
	if($_POST['title'] == ""){ ReportError("Please Fill In Post Title"); $error = 1; }
	if($_POST['date'] == ""){ ReportError("Please Choose A Post Date"); $error = 1; }
	if($_POST['TextArea'] == ""){ ReportError("Please Enter Some Content"); $error = 1; }
	
	if($error == 0)
	{
		$_POST = escape_smart_array($_POST);
		
		// COMMENTS		
		if($_POST['comments'] == "1"){
			$comments = "comments='1',";
		} else {
			$comments = "comments='',";
		}
		
		/*** insert record ***/
		$created = $_POST['date'];
		$created_array = explode("/",$created);
		$created_time = mktime(0,0,0,$created_array[0],$created_array[1],$created_array[2]);
		$created_timestamp = date("Y-m-d",$created_time)." 01:01:01";

		$sql = "INSERT INTO blog SET ".
					"blog_id='',".
					"admin_id='{$_SESSION['session']->admin->userid}',".
					"user_id='',".
					"title='{$_POST['title']}',".
					"details='',".
					"content='".$_POST['TextArea']."',".
					"status='".$_POST['status']."',".
					"$comments".
					"date='".$created_timestamp."',".
					"created=NOW() ".
					"".$_SETTINGS['demosql']."";
		$next = nextId('blog'); // GET NEXT
		$sqlresult = doQuery($sql);
		
		$n = 0;
		$numer2 = count($_REQUEST['categories']);
		while($n<$numer2)
		{	
			if($_REQUEST['categories'][$n] != ""){
				doQuery("INSERT INTO blog_category_relational SET blog_id='".$next."',category_id='".$_REQUEST['categories'][$n]."'".$_SETTINGS['demosql']."");
			}
			$n++;
		} //while
		
		$report = "Post Created Successfully";
		header("Location: {$_SERVER["PHP_SELF"]}?REPORT={$report}&SUCCESS=1&VIEW={$_GET["VIEW"]}&pid={$next}&".SID);
		exit;
	}
}

/**************************
* Update Post 
**************************/
if (isset($_POST["UPDATE_POST"]))
{
	$error = 0;
	/*** Validation ***/
	if($_POST['title'] == ""){ ReportError("Please Fill In Post Title"); $error = 1; }
	if($_POST['date'] == ""){ ReportError("Please Choose A Post Date"); $error = 1; }
	if($_POST['TextArea'] == ""){ ReportError("Please Enter Some Content"); $error = 1; }

	if($error == 0)
	{
		$_POST = escape_smart_array($_POST);

		// COMMENTS		
		if($_POST['comments'] == "1"){
			$comments = "comments='1',";
		} else {
			$comments = "comments='',";
		}
		
		/*** insert record ***/
		$created = $_POST['date'];
		$created_array = explode("/",$created);
		$created_time = mktime(0,0,0,$created_array[0],$created_array[1],$created_array[2]);
		$created_timestamp = date("Y-m-d",$created_time)." 01:01:01";
		$sql = 			"UPDATE blog SET ".
					"admin_id='{$_SESSION['session']->admin->userid}',".
					"user_id='',".
					"title='{$_POST['title']}',".
					"details='',".
					"content='".$_POST['TextArea']."',".
					"status='".$_POST['status']."',".
					"$comments".
					"date='".$created_timestamp."' ".
					"WHERE blog_id='".$_POST['pid']."'".
					"".$_SETTINGS['demosql']."";
		$sqlresult = doQuery($sql);
		
		// DELETE RELATIONS
		doQuery("DELETE FROM blog_category_relational WHERE blog_id='".$_POST["pid"]."' ".$_SETTINGS['demosqland']."");
		
		// ADD RELATIONS
		$n = 0;
		$numer2 = count($_REQUEST['categories']);
		while($n<$numer2)
		{	
			if($_REQUEST['categories'][$n] != ""){
				doQuery("INSERT INTO blog_category_relational SET blog_id='".$_POST['pid']."',category_id='".$_REQUEST['categories'][$n]."'".$_SETTINGS['demosql']."");
			}
			$n++;
		}//while
		
		$report = "Post Saved Successfully";
		header("Location: {$_SERVER["PHP_SELF"]}?REPORT={$report}&SUCCESS=1&VIEW={$_GET["VIEW"]}&pid={$_POST["pid"]}&".SID);
		die();
		exit;
	}
}

/*** CATEGORY CRUD ***/
/**************************
* Remove CATEGORY
**************************/
if (isset($_POST["DELETE_CATEGORY"]) || isset($_GET["DELETE_CATEGORY"]))
{
	doQuery("DELETE FROM blog_category WHERE blog_category_id=".$_REQUEST["cid"]." ".$_SETTINGS['demosqland']."");
	header("Location: {$_SERVER["PHP_SELF"]}?VIEW={$_REQUEST["VIEW"]}&CATEGORIES=1&REPORT=Category Deleted&SUCCESS=1&".SID);
	exit();
}

/**************************
* Add CATEGORY
**************************/
if (isset($_POST["ADD_CATEGORY"]))
{
	$error = 0;
	/*** Validation ***/
	if($_POST['title'] == ""){ ReportError("Please Fill In Category Name"); $error = 1; }
	
	if($error == 0)
	{
		$next = nextId('blog_category');
		$_POST = escape_smart_array($_POST);
		/*** insert record ***/
		$sql = "INSERT INTO blog_category SET ".
					"blog_category_id='',".
					"title='".$_POST['title']."',".
					"parent_id='".$_POST['parent']."',".
					"description='".$_POST['description']."',".
					"created=NOW()".
					"".$_SETTINGS['demosql']."";
		$result = doQuery($sql);
		$report = "Category Created Successfully";
		header("Location: {$_SERVER["PHP_SELF"]}?REPORT={$report}&SUCCESS=1&VIEW={$_GET["VIEW"]}&cid={$next}&".SID);
		exit;
	}
}

/**************************
* Update Category 
**************************/
if (isset($_POST["UPDATE_CATEGORY"]))
{
	$error = 0;
	/*** Validation ***/
	if($_POST['title'] == ""){ ReportError("Please Fill In Category Name"); $error = 1; }

	
	if($error == 0)
	{
		$_POST = escape_smart_array($_POST);
		/*** update record ***/
		$sql = "UPDATE blog_category SET ".
					"title='".$_POST['title']."',".
					"parent_id='".$_POST['parent']."',".
					"description='".$_POST['description']."'".
					"".$_SETTINGS['demosql']."".
					" WHERE blog_category_id='".$_POST['cid']."'".
					" ".$_SETTINGS['demosqland']."";
		$result = doQuery($sql);
		$report = "Category Updated Successfully";
		header("Location: {$_SERVER["PHP_SELF"]}?REPORT={$report}&SUCCESS=1&VIEW={$_GET["VIEW"]}&cid={$_POST["cid"]}&".SID);
		exit;
	}
}

/*** COMMENT CRUD ***/
/**************************
* Update COMMENT 
**************************/
if (isset($_POST["UPDATE_COMMENT"]))
{
	$error = 0;
	/*** Validation ***/
	if($_POST['name'] == ""){ ReportError("Please Fill In Name"); $error = 1; }
	if($_POST['email'] == ""){ ReportError("Please Fill In Email"); $error = 1; }
	if($_POST['content'] == ""){ ReportError("Please Fill In Comment"); $error = 1; }
	if(VerifyEmail($_POST['email']) != 1){ ReportError("Please Enter Valid Email"); $error=1; }
	
	if($error == 0)
	{
		$_POST = escape_smart_array($_POST);
		/*** update record ***/
		if($_POST['approved'] == "1"){ $approved = "1"; } else { $approved = "0"; }
		$sql = "UPDATE blog_comment SET ".
					"name='".$_POST['name']."',".
					"email='".$_POST['email']."',".
					"content='".$_POST['content']."',".
					"approved='".$approved."'".
					"".$_SETTINGS['demosql']."".
					" WHERE comment_id='".$_POST['coid']."' ".$_SETTINGS['demosqland']."";
		$result = doQuery($sql);
		$report = "Commented Updated Successfully";
		//die('made it');
		//die("Location:".$_SETTINGS['website']."admin/index.php?REPORT={$report}&SUCCESS=1&VIEW={$_GET["VIEW"]}&coid={$_POST["coid"]}");
		//exit;
		header("Location: ".$_SETTINGS['website']."admin/index.php?REPORT={$report}&SUCCESS=1&VIEW={$_GET["VIEW"]}&coid={$_POST["coid"]}");
		//ob_flush();
		exit;
	}
}

// QUICK APPROVE
if (isset($_POST["APPROVE_COMMENT"]) || isset($_GET["APPROVE_COMMENT"])) {/*{{{*/
	doQuery("UPDATE blog_comment SET approved='1' WHERE comment_id=".$_REQUEST["coid"]." ".$_SETTINGS['demosqland']."");
	//header("Location: {$_SERVER["PHP_SELF"]}?VIEW={$_REQUEST["VIEW"]}&COMMENTS=1&REPORT=Comment Approved&SUCCESS=1&".SID);
	header("Location: ".$_SETTINGS['website']."admin/index.php?VIEW={$_REQUEST["VIEW"]}&COMMENTS=1&REPORT=Comment Approved&SUCCESS=1");
	exit();
}

// QUICK UNNAPROVE
if (isset($_POST["UNAPPROVE_COMMENT"]) || isset($_GET["UNAPPROVE_COMMENT"])) {/*{{{*/
	doQuery("UPDATE blog_comment SET approved='0' WHERE comment_id=".$_REQUEST["coid"]." ".$_SETTINGS['demosqland']."");
	header("Location: ".$_SETTINGS['website']."admin/index.php?VIEW={$_REQUEST["VIEW"]}&COMMENTS=1&REPORT=Comment Unapproved&SUCCESS=1");
	exit();
}


/**************************
* Remove COMMENT
**************************/
if (isset($_POST["DELETE_COMMENT"]) || isset($_GET["DELETE_COMMENT"])) {/*{{{*/
	doQuery("DELETE FROM blog_comment WHERE comment_id=".$_REQUEST["coid"]." ".$_SETTINGS['demosqland']."");
	header("Location: {$_SERVER["PHP_SELF"]}?VIEW={$_REQUEST["VIEW"]}&COMMENTS=1&REPORT=Comment Deleted&SUCCESS=1&".SID);
	exit();
}/*}}}*/

/*** SUBSCRIBER CRUD ***/
/**************************
* Update SUBSCRIBER 
**************************/
if (isset($_POST["UPDATE_SUBSCRIBER"]))
{
	$error = 0;
	/*** Validation ***/
	if($_POST['name'] == ""){ ReportError("Please Fill In Name"); $error = 1; }
	if($_POST['email'] == ""){ ReportError("Please Fill In Email"); $error = 1; }
	if(VerifyEmail($_POST['email']) != 1){ ReportError("Please Enter Valid Email"); $error=1; }
	
	if($error == 0)
	{
		$_POST = escape_smart_array($_POST);
		/*** update record ***/
		$sql = "UPDATE blog_alert SET ".
					"name='".$_POST['name']."',".
					"email='".$_POST['email']."'".
					"".$_SETTINGS['demosql']."".
					" WHERE blog_alert_id='".$_POST['sid']."' ".$_SETTINGS['demosqland']."";
		$result = doQuery($sql);
		$report = "Subscriber Updated Successfully";
		header("Location: {$_SERVER["PHP_SELF"]}?REPORT={$report}&SUCCESS=1&VIEW={$_GET["VIEW"]}&sid={$_POST["sid"]}&".SID);
		exit;
	}
}

/**************************
* Remove SUBSCRIBER / ALERT
**************************/
if (isset($_POST["DELETE_SUBSCRIBER"]) || isset($_GET["DELETE_SUBSCRIBER"])) {/*{{{*/
	doQuery("DELETE FROM blog_alert WHERE blog_alert_id=".$_REQUEST["sid"]." ".$_SETTINGS['demosqland']."");
	header("Location: {$_SERVER["PHP_SELF"]}?VIEW={$_REQUEST["VIEW"]}&SUBSCRIBERS=1&REPORT=Subscriber Deleted&SUCCESS=1&".SID);
	exit();
}/*}}}*/

/**********************************************
*
* FORMS 
*
**********************************************/

/*********************************
* UPDATE / Add POST FORM
**********************************/
if ($_REQUEST['SUB'] == "ADDNEWPOST" || $_REQUEST['pid'] != "")
{
	// get post to modify
	if (isset($_REQUEST["pid"]))
	{
		$res = doQuery("SELECT * FROM blog WHERE blog_id=".$_REQUEST["pid"]." ".$_SETTINGS['demosql']."");
		$_POST = mysql_fetch_array($res);
		$button = "Update Post";
	}
	else
	{
		$button = "Add Post";
	}
	?>
	<FORM name="user" METHOD=POST ACTION="<?=$_SERVER["PHP_SELF"]?>?VIEW=<?=$_GET["VIEW"]?>&ADDNEWPOST=1&<?=SID?>">
	
	<?
	echo tableHeader("Post Information",2,'100%');
	?>
	
	
		
		<TR BGCOLOR="#f2f2f2">
		<Th>* Title</Th>
		<TD>
		<INPUT TYPE=TEXT NAME="title" VALUE="<?=$_POST['title']?>">
		<?
		if (isset($_REQUEST["pid"])) {
		?>
				<INPUT TYPE=HIDDEN NAME="pid" style="margin:0px; padding:0px; height:0px; line-height:0px;" VALUE="<?=$_REQUEST["pid"]?>" />
		<?
			}
		?>
		</TD>
		</TR>
		
		
		<script type="text/javascript">
		$(function() {
			$("#datepicker").datepicker();
		});
		</script>
		
		
		<?
		if($_REQUEST['pid']){
		//$_POST['date'] = $_POST['date'];
		$date_array1 = explode(" ",$_POST['date']);
		$date_array2 = explode("-",$date_array1[0]);
		$_POST['date'] = "".$date_array2[1]."/".$date_array2[2]."/".$date_array2[0]."";
		}
		?>
		<TR>
			<Th>* Date</Th>
			<TD><INPUT TYPE=TEXT NAME="date" id="datepicker" VALUE="<?=$_POST['date']?>"></TD>
		</TR>
		
		
		<tr>
			<th>Allow Comments</th>
			<td><input type="checkbox" name="comments" value="1" <? if($_POST['comments']=="1"){ ?>CHECKED<? } ?> /></td>
		</tr>
				
		<?
		// STATUS
		adminFormField("Status","status",$_POST['status'],"select",Array("Published","Pending","Draft"));
		?>
		
		<tr>
			<th>Post to Facebook</th>
			<td>
			<input type="checkbox" name="sent_facebook" value="1"  /> 
			<? if($_POST['facebook']=="1"){ ?>
			This post has been posted to Facebook.
			<? } else { ?>
			This post has <u>not</u> been posted to Facebook.
			<? } ?>
			</td>
		</tr>
		
		<tr>
			<th>Post to Twitter</th>
			<td>
			<input type="checkbox" name="sent_twitter" value="1"  /> 
			<? if($_POST['twitter']=="1"){ ?>
			This post has been posted to Twitter.
			<? } else { ?>
			This post has <u>not</u> been posted to Twitter.
			<? } ?>
			</td>
		</tr>
		
		<tr>
			<th>Email to Subscribers</th>
			<td>
			<input type="checkbox" name="sent_email" value="1" /> 
			<? if($_POST['email']=="1"){ ?>
			This post has been emailed to subscribers.
			<? } else { ?>
			This post has <u>not</u> been emailed to subscribers.
			<? } ?>
			</td>			
		</tr>
		
		<?
		/*
		<tr BGCOLOR="#F2F2F2" class="toggleropenidentifier">
		<th>&nbsp;</th>
		<td><a class="toggleridentifier tog">Open Advanced Options</a></td>
		</tr>
		<tr BGCOLOR="#F2F2F2" class="toggleidentifier">
		<th>&nbsp;</th>
		<td><a class="togglercloseidentifier tog">Close Advanced Options</a></td>
		</tr>
		*/
		?>
		
		
		<?
		//echo togglertableHeader("Categories",'100%','identifier1');	
		//echo toggletableHeader(2,'100%','identifier1');
		?>
		
		<tr BGCOLOR="#f2f2f2" class="toggleidentifier">
		<th>Categories</th>
		<td>
					<select id="categories" class="multiselect" multiple="multiple" name="categories[]" size="5" style="height:200px;">
					<!-- <option value=""> </option> -->
					<?
					
					
					/*** LIST SELECTED ***/
					$selecter = "SELECT * FROM blog_category_relational WHERE blog_id='".$_REQUEST['pid']."' ".$_SETTINGS['demosqland']."";
					$resulter = mysql_query($selecter) or die("err ( $select )");
					$numer = mysql_num_rows($resulter);
					$j = 0;
					while($j<$numer)
					{
						$rower = mysql_fetch_array($resulter);
						echo "<option selected=\"selected\" value=\"".$rower['category_id']."\">".$Blog->getcat($rower["category_id"])."</option>";
						// BUILD WHERE STATEMENT FOR NEXT SQL QUERY
						$other_sql .= "blog_category_id!='".$rower['category_id']."'";
						if($numer != 0 and $j != ($numer-1))
						{
							$other_sql .= " AND ";
						}
						$j++;
					}//while
					
					/*** LIST NOT SELECTED ***/
					if($_REQUEST['pid'] == "")
					{
						$selecter1 = "SELECT * FROM blog_category";
					}
					else
					{
						if($other_sql != "")
						{
							$selecter1 = "SELECT * FROM blog_category WHERE ($other_sql)";
						}
						else
						{
							$selecter1 = "SELECT * FROM blog_category";
						}					
					}
					
					//die($selecter1);
					//exit();
					$resulter1 = mysql_query($selecter1);
					$numer1 = mysql_num_rows($resulter1);
					$k = 0;
					while($k<$numer1)
					{
						$rower1 = mysql_fetch_array($resulter1);
						echo "<option value=\"".$rower1['blog_category_id']."\">".$rower1["title"]."</option>";
						$k++;
					}//while
					?>
					<!-- <option> </option><option> </option><option> </option> -->
				  </select>
		</td>
		</tr>
	
		<?
		/*
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
		*/
		?>
		
		<?
		//echo togglertableHeader("Content",'100%','identifier2');	
		//echo toggletableHeader(1,'100%','identifier2');
		?>
		<!-- <tr BGCOLOR="#f2f2f2">
			<td colspan="2">Content</td>
		</tr> -->
		
		<script type="text/javascript">
		$(function() {
			$("#tabs").tabs();
		});
		</script>
			
		<tr BGCOLOR="#f2f2f2">
		<td colspan="2">
		<div class="demo" style="width:780px; margin:13px;">
			<div id="tabs">
				<ul>
						<li><a href="#tabs-1">Blog Content</a></li>
				</ul>
				
				<div id="tabs-1">
					<table>
						<tr BGCOLOR="#f2f2f2">
							<td colspan="2">
								<?
								$editor = new wysiwygPro();

								// configure the editor:

								// give the editor a name (equivalent to the name attribute on a regular textarea):
								$editor->name = 'TextArea';
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
								
								$editor->documentDir 	= $_SERVER["DOCUMENT_ROOT"].$_SETTINGS['website_path']."uploads/";
								$editor->documentURL 	= $_SETTINGS['website']."uploads";
								
								$editor->mediaDir 		= $_SERVER["DOCUMENT_ROOT"].$_SETTINGS['website_path']."uploads/";
								$editor->mediaURL 		= $_SETTINGS['website']."uploads";
								
								
								// set the content to be edited:
								if($_REQUEST['pid'] != ""){
									$sel = "SELECT * FROM blog WHERE `blog_id`='".$_REQUEST['pid']."' ".$_SETTINGS['demosqland']."";
									$res = doQuery($sel);
									$ro = mysql_fetch_array($res);
									$editor->value = $ro["content"];
								} else {
									$editor->value = $_POST["TextArea"];
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
		</td>
		</tr>
		</table>
		
	<div id="submit">
	<?
	if (isset($_REQUEST["pid"])) {
		echo "<INPUT TYPE=SUBMIT NAME=DELETE_POST value=\"Delete\" onclick=\"return confirm('Are You Sure?');\">";
	}
	echo "<INPUT TYPE=SUBMIT NAME=\"".strtoupper(str_replace(" ", "_", $button))."\" VALUE=\"$button\">";
	?>
	</div>
	</FORM>
	
	<?
}
/*** ADD / UPDATE CATEGORY ***/
elseif($_REQUEST['SUB'] == "ADDNEWCATEGORY" || $_REQUEST['cid'] != "")
{
	// get cat to modify
	if (isset($_REQUEST["cid"]))
	{
		$res = doQuery("SELECT * FROM blog_category WHERE blog_category_id=".$_REQUEST["cid"]." ".$_SETTINGS['demosqland']."");
		$_POST = mysql_fetch_array($res);
		$button = "Update Category";
	}
	else
	{
		$button = "Add Category";
	}

	echo tableHeader("Category Information",2,'100%');
	?>
	<FORM name="user" METHOD=POST ACTION="<?=$_SERVER["PHP_SELF"]?>?VIEW=<?=$_GET["VIEW"]?>&ADDNEWCATEGORY=1&<?=SID?>">
	
	
		<TR BGCOLOR="#F2F2F2">
		<TD BGCOLOR="#F2F2F2" width="200" height="20" style="padding-left:20px;">
		&nbsp;
		</TD>
		<TD>
		&nbsp;
		</TD>
		</TR>
		
		
		<TR BGCOLOR="#f2f2f2">
		<TD width="200" height="40" style="padding-left:20px;">* Name</TD>
		<TD>
		<INPUT TYPE=TEXT NAME="title" VALUE="<?=$_POST['title']?>">
		<?
		if (isset($_REQUEST["cid"])) {
		?>
				<INPUT TYPE=HIDDEN NAME="cid" VALUE="<?=$_REQUEST["cid"]?>">
		<?
		}
		?>
		</TD>
		</TR>
		
		
		<tr BGCOLOR="#f2f2f2"><td height="40" style="padding-left:20px;">Parent Category</td><td>
		<select name="parent">
		<option value=""></option>
		<?
		/*** Cant be its own parent ***/
		if($_REQUEST['cid']){
			$result = doQuery("SELECT * FROM blog_category WHERE blog_category_id != '".$_REQUEST['cid']."'");
		} else {
			$result = doQuery("SELECT * FROM blog_category");
		}
		
		while($row = mysql_fetch_array($result))
		{
			?><option value="<?=$row['blog_category_id']?>" <? if($_POST['parent_id'] == $row['blog_category_id']){ ?>SELECTED <? } ?>><?=$row['title']?></option><?
		}// END while
		?>
		</select>
		</td></tr>
		
		<tr BGCOLOR="#f2f2f2">
		<td height="40" style="padding-left:20px;">Description</td>
		<td>
		<textarea name="description"><?=$_POST['description']?></textarea><br /><br />
		</td>
		</tr>
		
	<?	
	echo "</TABLE>";
	?>
	
	<div id="submit">
	<?
	if (isset($_REQUEST["cid"])) {
		echo "<INPUT TYPE=SUBMIT NAME=DELETE_CATEGORY value=\"Delete\" onclick=\"return confirm('Are You Sure?');\">";
	}
	echo "<INPUT TYPE=SUBMIT NAME=\"".strtoupper(str_replace(" ", "_", $button))."\" VALUE=\"$button\">";
	?>
	</div>
	</FORM>
	<?
}
elseif($_REQUEST['NEWCOMMENT'] || $_REQUEST['coid'] != "")
{
	// get comment to modify
	if (isset($_REQUEST["coid"]))
	{
		$res = doQuery("SELECT * FROM blog_comment WHERE comment_id=".$_REQUEST["coid"]." ".$_SETTINGS['demosqland']."");
		$_POST = mysql_fetch_array($res);
		$button = "Update Comment";
	}
	else
	{
		$button = "Add Comment";
	}
	?>
	<FORM name="user" METHOD=POST ACTION="index.php?VIEW=<?=$_GET["VIEW"]?>&ADDNEWCOMMENT=1">
	<?
	echo tableHeader("Comment Information",2,'100%');
	?>
	
		<TR BGCOLOR="#f2f2f2">
		<TD width="200" height="40" style="padding-left:20px;">* Name</TD>
		<TD>
		<INPUT TYPE=TEXT NAME="name" VALUE="<?=$_POST['name']?>">
		<?
		if (isset($_REQUEST["coid"])) {
		?>
				<INPUT TYPE=HIDDEN NAME="coid" VALUE="<?=$_REQUEST["coid"]?>">
		<?
			}
		?>
		</TD>
		</TR>
		
		<TR BGCOLOR="#f2f2f2">
		<TD width="200" height="40" style="padding-left:20px;">* Email</TD>
		<TD><INPUT TYPE=TEXT NAME="email" VALUE="<?=$_POST['email']?>"></TD>
		</TR>
		
		<TR BGCOLOR="#f2f2f2">
		<TD width="200" height="40" style="padding-left:20px;">* Comment</TD>
		<TD><textarea name="content"><?=$_POST['content']?></textarea></TD>
		</TR>
		
		<TR BGCOLOR="#f2f2f2">
		<TD width="200" height="40" style="padding-left:20px;">Approved</TD>
		<TD><input type="checkbox" name="approved" value="1" <? if($_POST['approved'] == "1"){ ?>CHECKED<?} ?> /></TD>
		</TR>

		
	<?	
	echo "</TABLE>";
	?>
	
	<div id="submit">
	<?
	if (isset($_REQUEST["coid"])) {
		echo "<INPUT TYPE=SUBMIT NAME=DELETE_COMMENT value=\"Delete\" onclick=\"return confirm('Are You Sure?');\">";
	}
	echo "<INPUT TYPE=SUBMIT NAME=\"".strtoupper(str_replace(" ", "_", $button))."\" VALUE=\"$button\">";
	?>
	</div>
	</FORM>
	<?
}
elseif($_REQUEST['SUB'] == "NEWSUBSCRIBER" || $_REQUEST['sid'] != "")
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
	
	<div id="submit">
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
elseif($_REQUEST['SUB'] == "CATEGORIES")
{
	/**************************
	* Search categories
	***************************/
	?>
	<div class="textcontent">
		<h1>Blog Categories</h1>
		<?
		
		//echo $_SESSION["session"]->admin->tableHeader("Current User Accounts",1,"100%");
		echo "<FORM METHOD=GET>";
		echo "<INPUT TYPE=HIDDEN NAME=\"VIEW\" VALUE=\"".$_GET["VIEW"]."\">";
		echo "<INPUT TYPE=HIDDEN NAME=\"SUB\" VALUE=\"".$_GET["SUB"]."\">";
		echo "<INPUT TYPE=HIDDEN NAME=\"CATEGORIES\" VALUE=\"1\">";
		echo "<INPUT TYPE=HIDDEN NAME=\"".session_name()."\" VALUE=\"".session_id()."\">";
		echo "<INPUT TYPE=TEXT NAME=\"KEYWORDS\" VALUE=\"{$_GET["KEYWORDS"]}\">";
		echo " <SELECT NAME=\"COLUMN\">";
			echo "<OPTION VALUE=\"title\"".selected($_GET["COLUMN"],"title").">Title</OPTION>";
		echo "</SELECT>";
		echo " <INPUT TYPE=SUBMIT NAME=search VALUE=\"Search\"> <INPUT TYPE=BUTTON NAME=NONE VALUE=\"Reset\" ONCLICK=\"document.location = '{$_SERVER["PHP_SELF"]}?VIEW={$_GET["VIEW"]}&CATEGORIES=1&".SID."';\">";
		echo "</FORM>";
		//echo "<TR><TD COLSPAN=4 ALIGN=\"CENTER\" BGCOLOR=\"#EEEEEE\"><br></TD></TR>";
		//echo "</TABLE>";/*}}}*/

		//echo '<Br><CENTER><a href="' . $_SERVER['PHP_SELF'] . '?VIEW=' . $_GET['VIEW'] . '&ADDNEW=1&' . SID . '" CLASS="new_link">&raquo; Add New User &laquo;</a></CENTER>';
		?>
	</div>
	<br /><br />
	<?
	/**************************
	* List all cats
	***************************/
	
	if ($_GET['KEYWORDS']!="") {
		$q = "AND {$_GET['COLUMN']} like '%{$_GET['KEYWORDS']}%'";
	}
	
	$page = 1;
	// how many records per page
	$size = 20;	 
	$total_records = mysql_num_rows(doQuery("SELECT * FROM blog_category WHERE 1=1 $q ".$_SETTINGS['demosqland']." ORDER BY title,created DESC")); 
	 
	// we get the current page from $_GET
	if (isset($_GET['page'])){
		$page = (int) $_GET['page'];
	}
	
	// create the pagination class
	$pagination = new Pagination();
	$pagination->setLink("index.php?VIEW=blog&CATEGORIES=1&page=%s");
	$pagination->setPage($page);
	$pagination->setSize($size);
	$pagination->setTotalRecords($total_records);
	 
	// now use this SQL statement to get records from your table
	$SQL = "SELECT * FROM blog_category WHERE 1=1 $q ".$_SETTINGS['demosqland']." ORDER BY title,created DESC " . $pagination->getLimitSql();	
	
	echo tableHeaderid("Blog Categories",6,"100%","list");
	
	echo "<thead><TR><th>Id</th><th>Title</th><th>Action</th></TR></thead><tbody>";
	
	$res = doQuery($SQL);
	
	$i=0;	
	while ($row = mysql_fetch_array($res)) {
		if($i % 2) { $class = "odd"; } else { $class = ""; }
		echo "<TR class=\"$class\">";
		echo "<TD>{$row["blog_category_id"]}</TD>";
		echo "<TD>{$row["title"]}</TD>";
		echo "<TD nowrap ALIGN=\"LEFT\">";
			echo "<FORM METHOD=GET ACTION=\"$_SERVER[PHP_SELF]\">";
			echo "<INPUT TYPE=HIDDEN NAME=cid VALUE=\"{$row["blog_category_id"]}\">";
			echo "<INPUT TYPE=HIDDEN NAME=VIEW VALUE=\"{$_GET["VIEW"]}\">";
			echo "<INPUT TYPE=HIDDEN NAME=\"".session_name()."\" VALUE=\"".session_id()."\">";
			echo "<INPUT TYPE=SUBMIT NAME=DELETE_CATEGORY VALUE=\"Delete\" onClick=\"return confirm('Are You Sure?');\"> <INPUT TYPE=SUBMIT NAME=view VALUE=\"View\">";
			echo "</FORM>";
		echo "</TD>";
		echo "</TR>";
		$i++;
	}
	
	echo "</tbody></TABLE>";/*}}}*/
	$navigation = $pagination->create_links();
	echo $navigation; // will draw our page navigation
}
elseif($_REQUEST['SUB'] == "COMMENTS")
{
	/**************************
	* Search comments
	***************************/
	?>
	<div class="textcontent">
		<h1>Blog Comments</h1>
		<?
		echo "<FORM METHOD=GET>";
		echo "<INPUT TYPE=HIDDEN NAME=\"VIEW\" VALUE=\"".$_GET["VIEW"]."\">";
		echo "<INPUT TYPE=HIDDEN NAME=\"SUB\" VALUE=\"".$_GET["SUB"]."\">";
		echo "<INPUT TYPE=HIDDEN NAME=\"COMMENTS\" VALUE=\"1\">";
		echo "<INPUT TYPE=HIDDEN NAME=\"".session_name()."\" VALUE=\"".session_id()."\">";
		echo "<INPUT TYPE=TEXT NAME=\"KEYWORDS\" VALUE=\"{$_GET["KEYWORDS"]}\">";
		echo " <SELECT NAME=\"COLUMN\">";
			echo "<OPTION VALUE=\"name\"".selected($_GET["COLUMN"],"name").">Name</OPTION>";
			echo "<OPTION VALUE=\"email\"".selected($_GET["COLUMN"],"email").">Email</OPTION>";
		echo "</SELECT>";
		echo " <INPUT TYPE=SUBMIT NAME=search VALUE=\"Search\"> <INPUT TYPE=BUTTON NAME=NONE VALUE=\"Reset\" ONCLICK=\"document.location = '{$_SERVER["PHP_SELF"]}?VIEW={$_GET["VIEW"]}&COMMENTS=1&".SID."';\">";
		echo "</FORM>";
		?>
	</div>
	<br /><br />
	<?
	/**************************
	* List all comments
	***************************/
	
	if ($_GET['KEYWORDS']!="") {
		$q = "AND {$_GET['COLUMN']} like '%{$_GET['KEYWORDS']}%'";
	}
	
	$page = 1;
	// how many records per page
	$size = 15;	 
	$total_records = mysql_num_rows(doQuery("SELECT * FROM blog_comment WHERE 1=1 $q ".$_SETTINGS['demosqland']." ORDER BY created DESC")); 
	 
	// we get the current page from $_GET
	if (isset($_GET['page'])){
		$page = (int) $_GET['page'];
	}
	
	// create the pagination class
	$pagination = new Pagination();
	$pagination->setLink("index.php?VIEW=blog&COMMENTS=1&page=%s");
	$pagination->setPage($page);
	$pagination->setSize($size);
	$pagination->setTotalRecords($total_records);
	 
	// now use this SQL statement to get records from your table
	$SQL = "SELECT * FROM blog_comment WHERE 1=1 $q ".$_SETTINGS['demosqland']." ORDER BY created DESC " . $pagination->getLimitSql();	
	
	echo tableHeaderid("Blog Comments",6,"100%","list");
	
	echo "<thead><TR><th>Name</th><th>Email</th><th>Approved</th><th>Blog Post</th><th>Action</th></TR></thead><tbody>";
	
	$res = doQuery($SQL);
	
	$i=0;	
	while ($row = mysql_fetch_array($res)) {
		if($i % 2) { $class = "odd"; } else { $class = ""; }
		echo "<TR class=\"$class\">";
		echo "<TD>{$row["name"]}</TD>";
		echo "<TD>{$row["email"]}</TD>";
		if($row['approved'] == "1"){
			$approved = "Yes <small><a href=\"?VIEW=".$_REQUEST['VIEW']."&COMMENTS=1&UNAPPROVE_COMMENT=1&coid=".$row['comment_id']."\">Unapprove</a></small>";
		} else {
			$approved = "No <small><a href=\"?VIEW=".$_REQUEST['VIEW']."&COMMENTS=1&APPROVE_COMMENT=1&coid=".$row['comment_id']."\">Approve</a></small>";
		}
		echo "<TD>".$approved."</TD>";
		echo "<TD>".$Blog->getblog($row['blog_id'])."</TD>";
		echo "<TD nowrap ALIGN=\"LEFT\">";
			echo "<FORM METHOD=GET ACTION=\"$_SERVER[PHP_SELF]\">";
			echo "<INPUT TYPE=HIDDEN NAME=coid VALUE=\"{$row["comment_id"]}\">";
			echo "<INPUT TYPE=HIDDEN NAME=VIEW VALUE=\"{$_GET["VIEW"]}\">";
			echo "<INPUT TYPE=HIDDEN NAME=\"".session_name()."\" VALUE=\"".session_id()."\">";
			echo "<INPUT TYPE=SUBMIT NAME=DELETE_COMMENT VALUE=\"Delete\" onClick=\"return confirm('Are You Sure?');\"> <INPUT TYPE=SUBMIT NAME=view VALUE=\"View\">";
			echo "</FORM>";
		echo "</TD>";
		echo "</TR>";
		$i++;
	}
	
	echo "</tbody></TABLE>";/*}}}*/
	$navigation = $pagination->create_links();
	echo $navigation; // will draw our page navigation
}
elseif($_REQUEST['SUB'] == "SUBSCRIBERS")
{
	/**************************
	* Search subscribers
	***************************/
	?>
	<div class="textcontent">
		<h1>Blog Subscribers</h1>
		<?
		echo "<FORM METHOD=GET>";
		echo "<INPUT TYPE=HIDDEN NAME=\"VIEW\" VALUE=\"".$_GET["VIEW"]."\">";
		echo "<INPUT TYPE=HIDDEN NAME=\"SUB\" VALUE=\"".$_GET["SUB"]."\">";
		echo "<INPUT TYPE=HIDDEN NAME=\"SUBSCRIBERS\" VALUE=\"1\">";
		echo "<INPUT TYPE=HIDDEN NAME=\"".session_name()."\" VALUE=\"".session_id()."\">";
		echo "<INPUT TYPE=TEXT NAME=\"KEYWORDS\" VALUE=\"{$_GET["KEYWORDS"]}\">";
		echo " <SELECT NAME=\"COLUMN\">";
			echo "<OPTION VALUE=\"name\"".selected($_GET["COLUMN"],"name").">Name</OPTION>";
			echo "<OPTION VALUE=\"email\"".selected($_GET["COLUMN"],"email").">Email</OPTION>";
		echo "</SELECT>";
		echo " <INPUT TYPE=SUBMIT NAME=search VALUE=\"Search\"> <INPUT TYPE=BUTTON NAME=NONE VALUE=\"Reset\" ONCLICK=\"document.location = '{$_SERVER["PHP_SELF"]}?VIEW={$_GET["VIEW"]}&SUBSCRIBERS=1&".SID."';\">";
		echo "</FORM>";
		?>
	</div>
	<div class="minitoolbar">
		<a href="modules/blog/blog_alert_excel.php">Export To Excel</a>
	</div>
	<br clear="all" />
	<?
	/**************************
	* List all subscribers
	***************************/
	
	if ($_GET['KEYWORDS']!="") {
		$q = "AND {$_GET['COLUMN']} like '%{$_GET['KEYWORDS']}%'";
	}
	
	$page = 1;
	// how many records per page
	$size = 20;	 
	$total_records = mysql_num_rows(doQuery("SELECT * FROM blog_alert WHERE 1=1 $q ".$_SETTINGS['demosqland']." ORDER BY created DESC")); 
	 
	// we get the current page from $_GET
	if (isset($_GET['page'])){
		$page = (int) $_GET['page'];
	}
	
	// create the pagination class
	$pagination = new Pagination();
	$pagination->setLink("index.php?VIEW=blog&COMMENTS=1&page=%s");
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
			echo "<FORM METHOD=GET ACTION=\"$_SERVER[PHP_SELF]\">";
			echo "<INPUT TYPE=HIDDEN NAME=sid VALUE=\"{$row["blog_alert_id"]}\">";
			echo "<INPUT TYPE=HIDDEN NAME=VIEW VALUE=\"{$_GET["VIEW"]}\">";
			echo "<INPUT TYPE=HIDDEN NAME=\"".session_name()."\" VALUE=\"".session_id()."\">";
			echo "<INPUT TYPE=SUBMIT NAME=DELETE_SUBSCRIBER VALUE=\"Delete\" onClick=\"return confirm('Are You Sure?');\"> <INPUT TYPE=SUBMIT NAME=view VALUE=\"View\">";
			echo "</FORM>";
		echo "</TD>";
		echo "</TR>";
		$i++;
	}
	
	echo "</tbody></TABLE>";/*}}}*/
	$navigation = $pagination->create_links();
	echo $navigation; // will draw our page navigation
}
else
{
	/**************************
	* Search posts
	***************************/
	?>
	<div class="textcontent">
		<h1>Blog Posts</h1>
		<?
		
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
		//echo "</TABLE>";/*}}}*/

		//echo '<Br><CENTER><a href="' . $_SERVER['PHP_SELF'] . '?VIEW=' . $_GET['VIEW'] . '&ADDNEW=1&' . SID . '" CLASS="new_link">&raquo; Add New User &laquo;</a></CENTER>';
		?>
	</div>
	<br /><br />
	<?
	/**************************
	* List all posts
	***************************/
	
	if ($_GET['KEYWORDS']!="") {
		$q = "AND {$_GET['COLUMN']} like '%{$_GET['KEYWORDS']}%'";
	}
	
	$page = 1;
	// how many records per page
	$size = 20;	 
	$total_records = mysql_num_rows(doQuery("SELECT * FROM blog WHERE 1=1 $q ".$_SETTINGS['demosqland']." ORDER BY created DESC")); 
	 
	// we get the current page from $_GET
	if (isset($_GET['page'])){
		$page = (int) $_GET['page'];
	}
	
	// create the pagination class
	$pagination = new Pagination();
	$pagination->setLink("index.php?VIEW=blog&page=%s");
	$pagination->setPage($page);
	$pagination->setSize($size);
	$pagination->setTotalRecords($total_records);
	 
	// now use this SQL statement to get records from your table
	$SQL = "SELECT * FROM blog WHERE 1=1 $q ".$_SETTINGS['demosqland']." ORDER BY created DESC " . $pagination->getLimitSql();	
	
	echo tableHeaderid("Blog Posts",6,"100%","list");
	
	echo "<thead><TR><th>Id</th><th>Date</th><th>Title</th><th>Status</th><th>Action</th></TR></thead><tbody>";
	
	$res = doQuery($SQL);
	
	$i=0;	
	while ($row = mysql_fetch_array($res)) {
		if($i % 2) { $class = "odd"; } else { $class = ""; }
		echo "<TR class=\"$class\">";
		echo "<TD>{$row["blog_id"]}</TD>";
		echo "<TD>".FormatTimeStamp($row["created"])."</TD>";
		echo "<TD>{$row["title"]}</TD>";
		echo "<TD>".$row['status']."</TD>";
		echo "<TD nowrap ALIGN=\"LEFT\">";
			echo "<FORM METHOD=GET ACTION=\"$_SERVER[PHP_SELF]\">";
			echo "<INPUT TYPE=HIDDEN NAME=pid VALUE=\"{$row["blog_id"]}\">";
			echo "<INPUT TYPE=HIDDEN NAME=VIEW VALUE=\"{$_GET["VIEW"]}\">";
			echo "<INPUT TYPE=HIDDEN NAME=\"".session_name()."\" VALUE=\"".session_id()."\">";
			echo "<INPUT TYPE=SUBMIT NAME=DELETE_POST VALUE=\"Delete\" onClick=\"return confirm('Are You Sure?');\"> <INPUT TYPE=SUBMIT NAME=view VALUE=\"View\">";
			echo "</FORM>";
		echo "</TD>";
		echo "</TR>";
		$i++;
	}
	
	echo "</tbody></TABLE>";/*}}}*/
	$navigation = $pagination->create_links();
	echo $navigation; // will draw our page navigation

}
?>