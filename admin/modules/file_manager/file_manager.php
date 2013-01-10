<?
// SET ALL TO 0777

//@chmodDirectory("../uploads/",0);
//@chmodDirectory("../uploads-beadrow/",0);
//@chmodDirectory("../uploads-products/",0);
//@chmodDirectory("../uploads-covers/",0);
//@chmodDirectory("../uploads-user/",0);

//@chmod("modules/file_manager/createthumbnailsflag.txt",0777);
//@chmod("modules/file_manager/createuploaddirectoryflag.txt",0777);

/** AJAX SET THUMBNAIL FLAG 	BEFORE DECLARE CLASS
 *
 *
 *
 **/
if(isset($_POST['SETTHUMBNAILFLAG']))
{
	global $_SESSION;
	
	if($_POST['do'] == '1'){
		$fp = fopen("createthumbnailsflag.txt", w);
		fwrite($fp, '1');
		fclose($fp);
		echo "true";
	
	} else {
		$fp = fopen("createthumbnailsflag.txt", w);
		fwrite($fp, '0');
		fclose($fp);
		echo "false";
	}	
	exit;
}

/** AJAX SET UPLOAD DIRECTORY FLAG 	BEFORE DECLARE CLASS	
 *
 *
 **/
if(isset($_POST['SETUPLOADDIRECTORYFLAG']))
{
	global $_SESSION;
	if($_POST['do'] != ""){
		$fp = fopen("createuploaddirectoryflag.txt", w);
		fwrite($fp, $_POST['do']);
		fclose($fp);
		echo $_POST['do'];
	} else {
		$fp = fopen("createuploaddirectoryflag.txt", w);
		fwrite($fp, 'uploads/');
		fclose($fp);
		echo "uploads/";
	}	
	exit;
}

// DISPLAY THE WHOLE FILE MANAGER
if(isset($_POST['DISPLAYCUSTOMERFILEMANAGER']))
{
	@require_once '../../../includes/config.php';
	$FileManager = new FileManager();
	$content = $FileManager->uploadsManager("","");
	echo $content;	
	die();
	exit();
}

// DISPLAY A FILE MANAGER DIR
if(isset($_POST['DISPLAYCUSTOMERFILEMANAGERDIR']))
{
	@require_once '../../../includes/config.php';
	$FileManager = new FileManager();
	$content = $FileManager->uploadsManager($_POST['DISPLAYCUSTOMERFILEMANAGERDIR'],"");
	echo $content;
	die();
	exit();
}

// DELETE A FILE
if(isset($_POST['DELETEFILE']))
{
	@require_once '../../../includes/config.php';
	@unlink("../../..".$_POST['DELETEFILE']."");
	die();
	exit();
}

// Declare File Manager Class and Report Function
$FileManager = new FileManager();
report($_REQUEST['REPORT'],$_REQUEST['SUCCESS']);
	
/** Remove/DELETE
 *
 *
 **/
if (isset($_POST["DELETE"]) || isset($_GET["DELETE"])){
	
	//
	// DELETE FILES
	//	
	$filepath = $_REQUEST['filename'];
	$file = basename($filepath);
	$filenamearray = explode(".",$file);
	$ext = $filenamearray[1];

	if($_REQUEST['THUMBNAIL'] == '1'){
		// DELETE JUST A THUMBNAIL
		@unlink($filepath);
	} else {
		// DELETE ORIGINAL
		@unlink($filepath);
		
		// CHECK IF PHOTO AND DELETE IF
		// TYPE PHOTOS
		if(
			strpos(strtolower($file), '.gif',1)||
			strpos(strtolower($file), '.jpg',1)||
			strpos(strtolower($file), '.jpeg',1)||
			strpos(strtolower($file), '.png',1)
		){			
			
			// DELETE THUMBNAILS
			$file1 = $filenamearray[0]."_w94.".$filenamearray[1];
			$file2 = $filenamearray[0]."_w150.".$filenamearray[1];
			$file3 = $filenamearray[0]."_w300.".$filenamearray[1];
			$file4 = $filenamearray[0]."_w600.".$filenamearray[1];
			$file5 = $filenamearray[0]."_w1024.".$filenamearray[1];
			
			$dir = $filename;
			// remove the basename
			$dir = str_replace($file,"",$dir);
			$thumbdir = $dir."wpThumbnails/";
			
			@unlink($thumbdir.$file1);
			@unlink($thumbdir.$file2);
			@unlink($thumbdir.$file3);
			@unlink($thumbdir.$file4);			
			@unlink($thumbdir.$file5);			
		}	
	}
	
	header("Location: ".$_SERVER["PHP_SELF"]."?VIEW=".$_REQUEST["VIEW"]."&PHOTOS=".$_REQUEST['PHOTOS']."&THUMBNAILS=".$_REQUEST['THUMBNAILS']."&VIDEOS=".$_REQUEST['VIDEOS']."&DOCUMENTS=".$_REQUEST['DOCUMENTS']."&REPORT=File Deleted Successfully&SUCCESS=1");
	exit();
}

/***************************************************************************************************************************
* BEGIN FORMS
****************************************************************************************************************************/

/** VIEWING FILE
 *
 *
 **/
if((isset($_REQUEST["filename"])))
{
	if(isset($_REQUEST["filename"]))
	{
		// FILENAME
		$filename = $_REQUEST['filename'];
		
		// FILE EXTENSION
		$uniquenamearray = explode(".",basename($filename));
		$file_ext = $uniquenamearray[1];
		
		$file = basename($filename);
		
		// SIZE
		$size = "";
		
		// TYPE PHOTOS
		if
		(
			strpos(strtolower($file), '.gif',1)||
			strpos(strtolower($file), '.jpg',1)||
			strpos(strtolower($file), '.jpeg',1)||
			strpos(strtolower($file), '.png',1)
		)
		{			
			$dim = $FileManager->getformatImageDimensions($filename);
			$type = "Photo";
			$section = "PHOTOS";
		}
		
		// TYPE VIDEOS
		if(
			strpos(strtolower($file), '.avi',1)||
			strpos(strtolower($file), '.mpeg',1)||
			strpos(strtolower($file), '.mov',1)||
			strpos(strtolower($file), '.swf',1)||
			strpos(strtolower($file), '.flv',1)||
			strpos(strtolower($file), '.wmv',1)
		){			
			$type = "Video";
			$section = "VIDEOS";
		}
		
		// TYPE AUDIO
		if(
			strpos(strtolower($file), '.mp3',1) || 
			strpos(strtolower($file), '.wma',1)
		){			
			$type = "Audio";
			$section = "AUDIO";
		}
				
		// TYPE DOCUMENTS
		if(
			strpos(strtolower($file), '.doc',1)||
			strpos(strtolower($file), '.docx',1)||
			strpos(strtolower($file), '.pdf',1)||
			strpos(strtolower($file), '.psd',1)||
			strpos(strtolower($file), '.ai',1)||
			strpos(strtolower($file), '.php',1)||
			strpos(strtolower($file), '.js',1)||
			strpos(strtolower($file), '.htm',1)||
			strpos(strtolower($file), '.html',1)||
			strpos(strtolower($file), '.xls',1)||
			strpos(strtolower($file), '.csv',1)||
			strpos(strtolower($file), '.txt',1)
		){			
			$type = "Document";
			$section = "DOCUMENTS";
		}

		$_POST = mysql_fetch_array($res);
		//$button = "Download";
	} else {
		//$button = "Download";
	}
	?>
	<FORM name="file" METHOD=GET ACTION="">
		<?
		echo tableHeader("".basename($filename)."",2,'100%');
		?>
			<TR BGCOLOR="#f2f2f2">
			<Th width="200" height="40" style="padding-left:20px;">Type:</Th>
			<TD>
			<?=$type ?>
			<?
			if($_REQUEST['THUMBNAILS'] == '1'){
				?> (Thumnail)<?
			}
			?>
			</TD>
			</TR>
			
			<TR BGCOLOR="#f2f2f2">
			<Th width="200" height="40" style="padding-left:20px;">Filename:</Th>
			<TD>
			<?=basename($filename) ?>
			</TD>
			</TR>
			
			<TR BGCOLOR="#f2f2f2">
			<Th width="200" height="40" style="padding-left:20px;">Format:</Th>
			<TD>
			<?=".".$file_ext ?>
			</TD>
			</TR>

			<TR BGCOLOR="#f2f2f2">
			<Th width="200" height="40" style="padding-left:20px;">URL:</Th>
			<TD>
			<?=$_SETTINGS['website'].str_replace("../","",$filename) ?>
			</TD>
			</TR>
			
			<?
			if($type == "Photo"){
				?>
				<TR BGCOLOR="#f2f2f2">
				<Th width="200" height="40" style="padding-left:20px;">Dimensions:</Th>
				<TD>
				<?=$dim ?>
				</TD>
				</TR>
				
				<?
				if($_REQUEST['THUMBNAILS'] != '1'){
					//
					// PHOTO THUMBNAILS
					//
					
					// the filepath
					$dir = $filename;
					
					// remove the basename
					$dir = str_replace($file,"",$dir);
					$thumbdir = $dir."wpThumbnails/";
					
					// filearray
					$file1 = $uniquenamearray[0]."_w94.".$uniquenamearray[1];
					$file2 = $uniquenamearray[0]."_w150.".$uniquenamearray[1];
					$file3 = $uniquenamearray[0]."_w300.".$uniquenamearray[1];
					$file4 = $uniquenamearray[0]."_w600.".$uniquenamearray[1];
					$file5 = $uniquenamearray[0]."_w1024.".$uniquenamearray[1];
					
					?>
					<TR BGCOLOR="#f2f2f2">
					<Th width="200" height="40" style="padding-left:20px;">Thumbnails:<BR><small>(previews smaller than acutal size)</small></Th>
					<TD>
					
					
						<table>
							<tr>
								<th align="center" style="border:0px; text-align:center;">
								94px
								</th>
								<th align="center" style="border:0px; text-align:center;">
								150px
								</th>
								<th align="center" style="border:0px; text-align:center;">
								300px
								</th>
								<th align="center" style="border:0px; text-align:center;">
								600px
								</th>
								<th align="center" style="border:0px; text-align:center;">
								1024px
								</th>
							</tr>
							<tr>
								<td align="center" style="border:0px;">
								<?
								if(file_exists($thumbdir.$file1)) {
									echo "<img src='".$thumbdir.$file1."' style='width:10px;' >"; 
								} else { 
									echo "<img src='images/icons/delete_16.png' >";
								}
								?>
								</td>
								<td align="center" style="border:0px;">
								<?
								if(file_exists($thumbdir.$file2)) {
									echo "<img src='".$thumbdir.$file2."' style='width:20px;' >"; 
								} else { 
									echo "<img src='images/icons/delete_16.png' >";
								}
								?>
								</td>
								<td align="center" style="border:0px;">
								<?
								if(file_exists($thumbdir.$file3)) {
									echo "<img src='".$thumbdir.$file3."' style='width:40px;' >"; 
								} else { 
									echo "<img src='images/icons/delete_16.png' >";
								}
								?>
								</td>
								<td align="center" style="border:0px;">
								<?
								if(file_exists($thumbdir.$file4)) {
									echo "<img src='".$thumbdir.$file4."' style='width:80px;' >"; 
								} else { 
									echo "<img src='images/icons/delete_16.png' >";
								}
								?>
								</td>
								<td align="center" style="border:0px;">
								<?
								if(file_exists($thumbdir.$file5)) {
									echo "<img src='".$thumbdir.$file5."' style='width:40px;' >"; 
								} else { 
									echo "<img src='images/icons/delete_16.png' >";
								}
								?>
								</td>
							</tr>
						</table>
					
					
					</TD>
					</TR>			
				<?
				}
				?>
				
				<TR BGCOLOR="#f2f2f2">
				<TD colspan="2" align='center' style='text-align:center;'>
				<?
				$dimarray = explode(" ",$dim);
				if((int)$dimarray[0] > 943){
					$widthstyle = "width:943px;";
				} else {
					$widthstyle = "";
				}
				//echo $dimarray[0];
				?>
				<img src="<?=$_SETTINGS['website'].str_replace("../","",$filename) ?>" style=" margin-bottom:20px; margin-top:20px; <?=$widthstyle?>" />
				</TD>
				</TR>
				<?
			}
			
			if($type == "Video"){
				?>		
				<TR BGCOLOR="#f2f2f2">
				<TD colspan="2">
					<a  
						 href="<?=$_SETTINGS['website'].str_replace("../","",$filename)?>"  
						 style="display:block;width:520px;height:330px; margin:20px auto;"  
						 id="player"> 
					</a> 			
					<!-- this will install flowplayer inside previous A- tag. -->
					<script>
						flowplayer("player", "scripts/flowplayer/flowplayer-3.2.1.swf");
					</script>	
				</TD>
				</TR>	
							
				<?
			}
			?>
		</table>		
		<?
		//
		// Submit FORM
		//
		?>
		<div id="submit">
		<a href="?VIEW=<?=$_REQUEST['VIEW']?>&<?=$section ?>=1">Back</a> &nbsp;&nbsp;&nbsp; 
		<?
		echo "<input type=HIDDEN NAME=\"filename\" VALUE=\"".$filename."\" >";
		echo "<INPUT TYPE=HIDDEN NAME=\"VIEW\" VALUE=\"".$_GET['VIEW']."\">";
		echo "<INPUT TYPE=HIDDEN NAME=\"PHOTOS\" VALUE=\"".$_GET["PHOTOS"]."\">";		
		echo "<INPUT TYPE=HIDDEN NAME=\"VIDEOS\" VALUE=\"".$_GET["VIDEOS"]."\">";
		echo "<INPUT TYPE=HIDDEN NAME=\"DOCUMENTS\" VALUE=\"".$_GET["DOCUMENTS"]."\">";
		echo "<INPUT TYPE=HIDDEN NAME=\"THUMBNAILS\" VALUE=\"".$_GET["THUMBNAILS"]."\">";
		
		if (isset($_REQUEST['filename'])){
			echo "<INPUT TYPE=SUBMIT NAME=DOWNLOADFILE value=\"Download\" >";
			echo "<INPUT TYPE=SUBMIT NAME=DELETE value=\"Delete\" onclick=\"return confirm('Are You Sure?');\">";			
		}
		?>
		</div>
	</FORM>
	<?
}			
/*** VIEWING PHOTOS		********************************************************/		
elseif ($_GET["PHOTOS"] == '1')
{
?>
<div class="textcontent">
	<h1>Photos <? if($_REQUEST['THUMBNAILS'] == '1'){ ?>(Thumbnails)<? } ?></h1>
	
	<?
	// VIEW THUMBNAILS
	if($_REQUEST['THUMBNAILS'] == '1'){
	?>
	<a href='?VIEW=<?=$_REQUEST['VIEW']?>&PHOTOS=1&THUMBNAILS=0' style='float:right;'>View Main Directory</a>
	<?
	} else {
	?>		
	<a href='?VIEW=<?=$_REQUEST['VIEW']?>&PHOTOS=1&THUMBNAILS=1' style='float:right;'>View Thumbnails Directory</a>
	<?
	}
	?>
	
	
	<?
	echo "<FORM METHOD=GET>";
	echo "Folder: ";
	?>
	<select name="DIRECTORY" id="DIRECTORY" onchange="uploaddirectorychange(this);">
		<?
		loopUploadsFolder("../");
		?>						
	</select>
	<?
	// FORM
	echo "<INPUT TYPE=HIDDEN NAME=\"VIEW\" VALUE=\"".$_GET["VIEW"]."\">";
	echo "<INPUT TYPE=HIDDEN NAME=\"PHOTOS\" VALUE=\"".$_GET["PHOTOS"]."\">";		
	echo "<INPUT TYPE=HIDDEN NAME=\"VIDEOS\" VALUE=\"".$_GET["VIDEOS"]."\">";
	echo "<INPUT TYPE=HIDDEN NAME=\"DOCUMENTS\" VALUE=\"".$_GET["DOCUMENTS"]."\">";
	echo "<INPUT TYPE=HIDDEN NAME=\"AUDIO\" VALUE=\"".$_GET["AUDIO"]."\">";
	echo "<INPUT TYPE=HIDDEN NAME=\"THUMBNAILS\" VALUE=\"".$_GET["THUMBNAILS"]."\">";
	echo "<INPUT TYPE=SUBMIT NAME=go VALUE=\"View\">";
	echo "</FORM>";
	?>	
</div>
<?

echo tableHeaderid("Photos",6,"100%","listjs");

if($_REQUEST['THUMBNAILS'] == '1'){
	echo "<thead><TR><th>Image</th><th width=\"250\">Filename</th><th width=\"100\">Size</th><th width=\"230\">Action</th></TR></thead><tbody>";
} else {
	echo "<thead><TR><th>Image</th><th width=\"250\">Filename</th><th width=\"110\">Original Dim.</th><th>94px</th><th>150px</th><th>300px</th><th>600px</th><th>1024px</th><th width=\"100\">Original Size</th><th width=\"230\">Action</th></TR></thead><tbody>";
}	
if($_GET['DIRECTORY'] == ""){
	$dir = "../uploads/"; //You could add a $_GET to change the directory
	$thumbdir = "../uploads/wpThumbnails/"; //You could add a $_GET to change the directory
} else {
	$dir = "../".$_GET['DIRECTORY']."";
	$thumbdir = "../".$_GET['DIRECTORY']."wpThumbnails/"; 
}

// THUMBNAILS
if($_REQUEST['THUMBNAILS'] == '1'){
	$dir = $thumbdir;
}

$files = scandir($dir);
$i = 0;
foreach($files as $key => $file){
	if ($file != "." && $file != "..") {
		//
		// SHOW ONLY FILETYPES:
		//
		if (
			strpos(strtolower($file), '.gif',1)||
			strpos(strtolower($file), '.jpg',1)||
			strpos(strtolower($file), '.jpeg',1)||
			strpos(strtolower($file), '.png',1)
			){       
			if($i % 2) { $class = "odd"; } else { $class = "even"; }
			echo "<TR class=\"$class\">";
		    
			// Image & FILE NAME
			echo "<td><img src='".$_SETTINGS['website']."".str_replace("../","",$dir)."".$file."' style=\"width:50px\"></td>";
			echo "<td>".truncateString($file, 60, true)."</td>";
			
			if($_REQUEST['THUMBNAILS'] == '1'){
				//
				echo "<td align='left'>".$file."</td>";				
			} else {
				// ORIGINAL DIM.
				echo "<td>".$FileManager->getformatImageDimensions($dir.$file)."</td>";
				
				$filearray = explode(".",$file);
				
				if(file_exists($thumbdir.$filearray[0]."_w94.".$filearray[1])) { $file1 = "<img src='images/icons/tick_16.png' >"; } else { $file1 = "<img src='images/icons/delete_16.png' >"; }
				echo "<td align='center'>".$file1."</td>";
				if(file_exists($thumbdir.$filearray[0]."_w150.".$filearray[1])) { $file2 = "<img src='images/icons/tick_16.png' style='border:0px;' >"; } else { $file2 = "<img src='images/icons/delete_16.png' >"; }
				echo "<td align='center'>".$file2."</td>";
				if(file_exists($thumbdir.$filearray[0]."_w300.".$filearray[1])) { $file3 = "<img src='images/icons/tick_16.png' style='border:0px;' >"; } else { $file3 = "<img src='images/icons/delete_16.png' >"; }
				echo "<td align='center'>".$file3."</td>";
				if(file_exists($thumbdir.$filearray[0]."_w600.".$filearray[1])) { $file4 = "<img src='images/icons/tick_16.png' style='border:0px;' >"; } else { $file4 = "<img src='images/icons/delete_16.png' >"; }
				echo "<td align='center'>".$file4."</td>";
				if(file_exists($thumbdir.$filearray[0]."_w1024.".$filearray[1])) { $file5 = "<img src='images/icons/tick_16.png' style='border:0px;' >"; } else { $file5 = "<img src='images/icons/delete_16.png' >"; }
				echo "<td align='center'>".$file5."</td>";
				
				// ORIGINAL SIZE
				echo "<td>".$FileManager->formatFilesize(filesize($dir.$file))."</td>";
			}
			
			// ACTION
			echo "<TD nowrap ALIGN=\"LEFT\">";
				echo "<FORM METHOD=GET ACTION=\"$_SERVER[PHP_SELF]\">";
				echo "	<INPUT TYPE=HIDDEN NAME=filename VALUE=\"".$dir.$file."\">";
				
				echo "	<INPUT TYPE=HIDDEN NAME=\"VIEW\" VALUE=\"".$_GET['VIEW']."\">";
				echo "	<INPUT TYPE=HIDDEN NAME=\"PHOTOS\" VALUE=\"".$_GET["PHOTOS"]."\">";		
				echo "	<INPUT TYPE=HIDDEN NAME=\"VIDEOS\" VALUE=\"".$_GET["VIDEOS"]."\">";
				echo "	<INPUT TYPE=HIDDEN NAME=\"AUDIO\" VALUE=\"".$_GET["AUDIO"]."\">";
				echo "	<INPUT TYPE=HIDDEN NAME=\"DOCUMENTS\" VALUE=\"".$_GET["DOCUMENTS"]."\">";
				echo "  <INPUT TYPE=HIDDEN NAME=\"THUMBNAILS\" VALUE=\"".$_GET["THUMBNAILS"]."\">";
				
				echo "	<INPUT TYPE=SUBMIT NAME=DELETE VALUE=\"Delete\" onClick=\"return confirm('Are You Sure?');\">";
				echo "	<INPUT TYPE=SUBMIT NAME=DOWNLOADFILE VALUE=\"Download\">";
				echo "	<INPUT TYPE=SUBMIT NAME=view VALUE=\"View\">";
				echo "</FORM>";
			echo "</TD>";
			
			echo "</tr>";
		}
	}
} 
echo "	</tbody></TABLE>
		<script type='text/javascript'>
			//$('table.results').lazyKarl();
		</script>";

/*** VIEWING VIDEOS		********************************************************/		
}
elseif($_GET['VIDEOS'] == '1')
{
?>
<div class="textcontent">
		<h1>Videos</h1>
		<?
		echo "<FORM METHOD=GET>";
		echo "Folder: ";
		?>
		<select name="DIRECTORY" id="DIRECTORY" onchange="uploaddirectorychange(this);">
			<?
			loopUploadsFolder("../");
			?>								
		</select>
		
		<?
		echo "<INPUT TYPE=HIDDEN NAME=\"VIEW\" VALUE=\"".$_GET["VIEW"]."\">";
		echo "<INPUT TYPE=HIDDEN NAME=\"PHOTOS\" VALUE=\"".$_GET["PHOTOS"]."\">";		
		echo "<INPUT TYPE=HIDDEN NAME=\"VIDEOS\" VALUE=\"".$_GET["VIDEOS"]."\">";
		echo "<INPUT TYPE=HIDDEN NAME=\"AUDIO\" VALUE=\"".$_GET["AUDIO"]."\">";
		echo "<INPUT TYPE=HIDDEN NAME=\"DOCUMENTS\" VALUE=\"".$_GET["DOCUMENTS"]."\">";
		echo "<INPUT TYPE=HIDDEN NAME=\"THUMBNAILS\" VALUE=\"".$_GET["THUMBNAILS"]."\">";
		echo "<INPUT TYPE=SUBMIT NAME=go VALUE=\"View\">";
		echo "</FORM>";
		?>
</div>
<?
echo tableHeaderid("Videos",6,"100%","listjs");
echo "<thead><TR><th width=\"200\">Filename</th><th>Size</th><th width=\"300\">Action</th></TR></thead><tbody>";

	
if($_GET['DIRECTORY'] == ""){
	$dir = "../uploads/"; //You could add a $_GET to change the directory
	$thumbdir = "../uploads/wpThumbnails/"; //You could add a $_GET to change the directory
} else {
	$dir = "../".$_GET['DIRECTORY']."";
	$thumbdir = "../".$_GET['DIRECTORY']."wpThumbnails/"; 
}

$files = scandir($dir);
$i = 0;
foreach($files as $key => $file){
	if ($file != "." && $file != "..") {
		//
		// SHOW ONLY FILETYPES:
		//
		if (
			strpos(strtolower($file), '.avi',1)||
			strpos(strtolower($file), '.mpeg',1)||
			strpos(strtolower($file), '.mov',1)||
			strpos(strtolower($file), '.swf',1)||
			strpos(strtolower($file), '.flv',1)||
			strpos(strtolower($file), '.wmv',1)
			){       
			if($i % 2) { $class = "odd"; } else { $class = "even"; }
			echo "<TR class=\"$class\">";
		    //echo "<tr>";
			echo "<td><img src=\"images/icons/file_video_16.png\" style=\"margin-bottom:-3px;\"> ".$file."</td>";
			echo "<td>".$FileManager->formatFilesize(filesize($dir.$file))."</td>";
			
			echo "<TD nowrap ALIGN=\"LEFT\">";
				echo "<FORM METHOD=GET ACTION=\"$_SERVER[PHP_SELF]\">";
				echo "	<INPUT TYPE=HIDDEN NAME=filename VALUE=\"".$dir.$file."\">";
				
				echo "	<INPUT TYPE=HIDDEN NAME=\"VIEW\" VALUE=\"".$_GET['VIEW']."\">";
				echo "	<INPUT TYPE=HIDDEN NAME=\"PHOTOS\" VALUE=\"".$_GET["PHOTOS"]."\">";		
				echo "	<INPUT TYPE=HIDDEN NAME=\"VIDEOS\" VALUE=\"".$_GET["VIDEOS"]."\">";				
				echo "  <INPUT TYPE=HIDDEN NAME=\"AUDIO\" VALUE=\"".$_GET["AUDIO"]."\">";
				echo "	<INPUT TYPE=HIDDEN NAME=\"DOCUMENTS\" VALUE=\"".$_GET["DOCUMENTS"]."\">";
				echo "  <INPUT TYPE=HIDDEN NAME=\"THUMBNAILS\" VALUE=\"".$_GET["THUMBNAILS"]."\">";
				
				echo "	<INPUT TYPE=SUBMIT NAME=DELETE VALUE=\"Delete\" onClick=\"return confirm('Are You Sure?');\">";
				echo "	<INPUT TYPE=SUBMIT NAME=DOWNLOADFILE VALUE=\"Download\">";
				echo "	<INPUT TYPE=SUBMIT NAME=view VALUE=\"View\">";
				echo "</FORM>";
			echo "</TD>";
			
			echo "</tr>";
		}
	}
} 
echo "</tbody></TABLE>";

/*** VIEWING AUDIO		********************************************************/		
}
elseif($_GET['AUDIO'] == '1')
{
?>
	<div class="textcontent">
		<h1>Audio</h1>
		<?
		echo "<FORM METHOD=GET>";
		echo "Folder: ";
		?>
		<select name="DIRECTORY" id="DIRECTORY" onchange="uploaddirectorychange(this);">
			<?
			loopUploadsFolder("../");
			?>									
		</select>
		
		<?
		echo "<INPUT TYPE=HIDDEN NAME=\"VIEW\" VALUE=\"".$_GET["VIEW"]."\">";
		echo "<INPUT TYPE=HIDDEN NAME=\"PHOTOS\" VALUE=\"".$_GET["PHOTOS"]."\">";		
		echo "<INPUT TYPE=HIDDEN NAME=\"VIDEOS\" VALUE=\"".$_GET["VIDEOS"]."\">";
		echo "<INPUT TYPE=HIDDEN NAME=\"AUDIO\" VALUE=\"".$_GET["AUDIO"]."\">";
		echo "<INPUT TYPE=HIDDEN NAME=\"DOCUMENTS\" VALUE=\"".$_GET["DOCUMENTS"]."\">";
		echo "<INPUT TYPE=HIDDEN NAME=\"THUMBNAILS\" VALUE=\"".$_GET["THUMBNAILS"]."\">";
		echo "<INPUT TYPE=SUBMIT NAME=go VALUE=\"View\">";
		echo "</FORM>";
		?>
	</div>
	<?
	echo tableHeaderid("Videos",6,"100%","listjs");
	echo "<thead><TR><th width=\"200\">Filename</th><th>Size</th><th width=\"300\">Action</th></TR></thead><tbody>";

		
	if($_GET['DIRECTORY'] == ""){
		$dir = "../uploads/"; //You could add a $_GET to change the directory
		$thumbdir = "../uploads/wpThumbnails/"; //You could add a $_GET to change the directory
	} else {
		$dir = "../".$_GET['DIRECTORY']."";
		$thumbdir = "../".$_GET['DIRECTORY']."wpThumbnails/"; 
	}

	$files = scandir($dir);
	$i = 0;
	foreach($files as $key => $file){
		if ($file != "." && $file != "..") {
			//
			// SHOW ONLY FILETYPES:
			//
			if (
				strpos(strtolower($file), '.mp3',1) || 
				strpos(strtolower($file), '.wma',1)
				){       
				if($i % 2) { $class = "odd"; } else { $class = "even"; }
				echo "<TR class=\"$class\">";
				//echo "<tr>";
				echo "<td><img src=\"images/icons/file_video_16.png\" style=\"margin-bottom:-3px;\"> ".$file."</td>";
				echo "<td>".$FileManager->formatFilesize(filesize($dir.$file))."</td>";
				
				echo "<TD nowrap ALIGN=\"LEFT\">";
					echo "<FORM METHOD=GET ACTION=\"$_SERVER[PHP_SELF]\">";
					echo "	<INPUT TYPE=HIDDEN NAME=filename VALUE=\"".$dir.$file."\">";
					
					echo "	<INPUT TYPE=HIDDEN NAME=\"VIEW\" VALUE=\"".$_GET['VIEW']."\">";
					echo "	<INPUT TYPE=HIDDEN NAME=\"PHOTOS\" VALUE=\"".$_GET["PHOTOS"]."\">";		
					echo "	<INPUT TYPE=HIDDEN NAME=\"VIDEOS\" VALUE=\"".$_GET["VIDEOS"]."\">";				
					echo "  <INPUT TYPE=HIDDEN NAME=\"AUDIO\" VALUE=\"".$_GET["AUDIO"]."\">";
					echo "	<INPUT TYPE=HIDDEN NAME=\"DOCUMENTS\" VALUE=\"".$_GET["DOCUMENTS"]."\">";
					echo "  <INPUT TYPE=HIDDEN NAME=\"THUMBNAILS\" VALUE=\"".$_GET["THUMBNAILS"]."\">";
					
					echo "	<INPUT TYPE=SUBMIT NAME=DELETE VALUE=\"Delete\" onClick=\"return confirm('Are You Sure?');\">";
					echo "	<INPUT TYPE=SUBMIT NAME=DOWNLOADFILE VALUE=\"Download\">";
					echo "	<INPUT TYPE=SUBMIT NAME=view VALUE=\"View\">";
					echo "</FORM>";
				echo "</TD>";
				
				echo "</tr>";
			}
		}
	} 
	echo "</tbody></TABLE>";
}
/** VIEWING DOCUMENTS
 *
 *
 **/
elseif($_GET['DOCUMENTS'] == '1')
{
?>
	<div class="textcontent">
			<h1>Documents</h1>
			<?
			echo "<FORM METHOD=GET>";
			echo "Folder: ";
			?>
			<select name="DIRECTORY" id="DIRECTORY" onchange="uploaddirectorychange(this);">
				<?
				loopUploadsFolder("../");
				?>					
			</select>
			
			<?
			echo "<INPUT TYPE=HIDDEN NAME=\"VIEW\" VALUE=\"".$_GET["VIEW"]."\">";
			echo "<INPUT TYPE=HIDDEN NAME=\"PHOTOS\" VALUE=\"".$_GET["PHOTOS"]."\">";		
			echo "<INPUT TYPE=HIDDEN NAME=\"VIDEOS\" VALUE=\"".$_GET["VIDEOS"]."\">";			
			echo "<INPUT TYPE=HIDDEN NAME=\"AUDIO\" VALUE=\"".$_GET["AUDIO"]."\">";
			echo "<INPUT TYPE=HIDDEN NAME=\"DOCUMENTS\" VALUE=\"".$_GET["DOCUMENTS"]."\">";
			echo "<INPUT TYPE=HIDDEN NAME=\"THUMBNAILS\" VALUE=\"".$_GET["THUMBNAILS"]."\">";
			echo "<INPUT TYPE=SUBMIT NAME=go VALUE=\"View\">";
			echo "</FORM>";
			?>
	</div>
	<?
	echo tableHeaderid("Documents",6,"100%","listjs");
	echo "<thead><TR><th width=\"200\">Filename</th><th>Size</th><th width=\"300\">Action</th></TR></thead><tbody>";

		
	if($_GET['DIRECTORY'] == ""){
	$dir = "../uploads/"; //You could add a $_GET to change the directory
	$thumbdir = "../uploads/wpThumbnails/"; //You could add a $_GET to change the directory
	} else {
		$dir = "../".$_GET['DIRECTORY']."";
		$thumbdir = "../".$_GET['DIRECTORY']."wpThumbnails/"; 
	}
	
	$files = scandir($dir);
	$i = 0;
	foreach($files as $key => $file){
		if ($file != "." && $file != "..") {
			//
			// SHOW ONLY FILETYPES:
			//
			if (
				strpos(strtolower($file), '.doc',1)||
				strpos(strtolower($file), '.docx',1)||
				strpos(strtolower($file), '.pdf',1)||
				strpos(strtolower($file), '.psd',1)||
				strpos(strtolower($file), '.ai',1)||
				strpos(strtolower($file), '.php',1)||
				strpos(strtolower($file), '.js',1)||
				strpos(strtolower($file), '.htm',1)||
				strpos(strtolower($file), '.html',1)||				
				strpos(strtolower($file), '.xls',1)||
				strpos(strtolower($file), '.csv',1)||
				strpos(strtolower($file), '.txt',1)
				) {       
				if($i % 2) { $class = "odd"; } else { $class = "even"; }
				echo "<TR class=\"$class\">";
				//echo "<tr>";
				echo "<td><img src=\"images/icons/file_document_16.png\" style=\"margin-bottom:-3px;\"> ".$file."</td>";
				echo "<td>".$FileManager->formatFilesize(filesize($dir.$file))."</td>";
				
				echo "<TD nowrap ALIGN=\"LEFT\">";
					echo "<FORM METHOD=GET ACTION=\"$_SERVER[PHP_SELF]\">";
					echo "	<INPUT TYPE=HIDDEN NAME=filename VALUE=\"".$dir.$file."\">";
					
					echo "	<INPUT TYPE=HIDDEN NAME=\"VIEW\" VALUE=\"".$_GET['VIEW']."\">";
					echo "	<INPUT TYPE=HIDDEN NAME=\"PHOTOS\" VALUE=\"".$_GET["PHOTOS"]."\">";		
					echo "	<INPUT TYPE=HIDDEN NAME=\"VIDEOS\" VALUE=\"".$_GET["VIDEOS"]."\">";					
					echo "  <INPUT TYPE=HIDDEN NAME=\"AUDIO\" VALUE=\"".$_GET["AUDIO"]."\">";
					echo "	<INPUT TYPE=HIDDEN NAME=\"DOCUMENTS\" VALUE=\"".$_GET["DOCUMENTS"]."\">";
					echo "  <INPUT TYPE=HIDDEN NAME=\"THUMBNAILS\" VALUE=\"".$_GET["THUMBNAILS"]."\">";
					
					echo "	<INPUT TYPE=SUBMIT NAME=DELETE VALUE=\"Delete\" onClick=\"return confirm('Are You Sure?');\">";
					echo "	<INPUT TYPE=SUBMIT NAME=DOWNLOADFILE VALUE=\"Download\">";
					echo "	<INPUT TYPE=SUBMIT NAME=view VALUE=\"View\">";
					echo "</FORM>";
				echo "</TD>";
				
				echo "</tr>";
			}
		}
	} 
	echo "</tbody></TABLE>";

}
/** NEW FILEMANAGER
 *
 *
 **/
elseif($_REQUEST['SUB'] == 'FileManager')
{
	echo "	<div class='textcontent'>
			<h1>File Manager</h1>
			</div>
			<div id='pane-files'>";
	echo 		$FileManager->uploadsManager('','');
	echo "	</div>";
}
/** UPLOAD FILES
 *
 *
 **/
else
{
	// INITIALLY SET THE THUMBNAIL FLAG TO FALSE
	$fp = fopen("modules/file_manager/createthumbnailsflag.txt", w);
	fwrite($fp, '0');
	fclose($fp);
	
	// INITIALL SET THE UPLOAD DIRECTORY TO uploads/
	$fp = fopen("modules/file_manager/createuploaddirectoryflag.txt", w);
	fwrite($fp, 'uploads/');
	fclose($fp);
	?>
	<form action="scripts/fancyupload-3.0-1/showcase/filemanager-script.php" method="post" enctype="multipart/form-data" id="form-demo">
		<?
		echo tableHeader("Upload Files",1,'100%');
		?>
		<tr>
		<td>
		<fieldset id="demo-fallback">
			<legend>File Upload</legend>
			<label for="demo-photoupload">
			Upload a File:
			<input type="file" name="Filedata" />
			</label>
		</fieldset>
		<div id="demo-status" class="hide">
				<div style="float:left;">
					<table id="" width="600">				
					<tr>
					
					<td style="border:0px;" valign="top">
					<h2>Choose Location</h2>
					<p>Select a directory to upload the files to.</p>
					
					<script>
						/*** KSD POST BYPASS ***/	
						
			
						function uploaddirectorychange(box){
														
							 //var checked = (box.checked) ? "1" : "0"; 
							//alert('AHHH!');
							// GET VALUE	
							var col = (box.options[box.selectedIndex].value);
							//alert(col);
								/*** SET AJAX SESSION ***/							
								
								var req = new Request({
									method: 'post',
									url: 'modules/file_manager/file_manager.php',
									data: { 'do' : col,'SETUPLOADDIRECTORYFLAG' : '1' },
								}).send();
								
						}
						/*** END ***/
					</script>	
					
					<select name="uploads_directory" id="uploads_directory" onchange="uploaddirectorychange(this);">
						<?
						loopUploadsFolder("../");
						?>		
					</select>
					</td>
					
					<td style="border:0px;" valign="top">
						<h2>Browse For Files</h2>
						<p>Click the button below to browse for files to upload.</p>
						<a rel="" title="" target="" href="#" class="" id="demo-browse">
						<button>Browse</button>				
						</a>					
					</td>
					
					<td style="border:0px;" valign="top">
						<h2>Create Thumbnails?</h2>
						<p>Check the box if you would like to create thumbnails for the photos being uploaded.</p>
						<script>
						/*** KSD POST BYPASS ***/	
						//on dom ready...
						//window.addEvent('domready', function() {
			
						function createthumbnailschange(box){
							//this.checked
							
							 var checked = (box.checked) ? "1" : "0"; 
							 //if(checked == "1"){
								//alert('checked');
								/*** SET AJAX SESSION ***/							
								
								var req = new Request({
									method: 'post',
									url: 'modules/file_manager/file_manager.php',
									data: { 'do' : checked,'SETTHUMBNAILFLAG' : '1' },
								}).send();
							 //}
						}
						/*** END ***/
						</script>							
						
						<input type="checkbox" onclick="createthumbnailschange(this);" name="createthumbnails" id="createthumbnails" style="float:none;" value="1" /> Create Thumbnails
								
					</td>				
					
					
					
					
					</tr>
					</table>
					<BR>
						
						
					
				</div>
				<div style="float:left; margin:35px 0 0 20px;">			
					<div>
						<strong class="overall-title"></strong><br />
						<img src="scripts/fancyupload-3.0-1/assets/progress-bar/bar.gif" class="progress overall-progress" />
					</div>
					<div>
						<strong class="current-title"></strong><br />
						<img src="scripts/fancyupload-3.0-1/assets/progress-bar/bar.gif" class="progress current-progress" />
					</div>
					<div class="current-text"></div>
				</div>
				<br clear="all" />
		</div>
		</td>
		</tr>
		<tr>
			<td>
				<ul id="demo-list"></ul>
			</td>
		</tr>
		</table>
		
		<div id="submit">
		
			<a rel="" title="" target="" href="#" class=" " id="demo-upload">
			<!-- <img src="images/icons/up_16.png" style='border:0px;' > -->
			<button>Start Upload</button>
			</a>	
		
			<a rel="" title="" target="" href="#" class="" id="demo-clear">
			<!-- <img src="images/icons/delete_16.png" style='border:0px;' > -->
			<button>Clear List</button>
			</a>	
		
		</div>
	</form>
<?
}
?>
