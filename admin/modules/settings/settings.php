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

$Settings = new Settings();
report($_REQUEST['REPORT'],$_REQUEST['SUCCESS']);

//error_reporting(E_ALL);

// CHMOD BACKUP FOLDER
@chmodDirectory("../website_backup_files/",0);

/***	CRUD FOR ATTRIBUTES				********************************************************/
$table						= "settings";
$name						= "Setting";
$idColumn					= "setting_id";
$id							= $_REQUEST['sid'];
$xid						= "sid";
$emptyValidatedFieldArray	= Array("name","value","type","group_id");
$fieldArray					= Array("name","value","description","type","group_id","user_friendly_name");
crudTable($table,$name,$idColumn,$id,$xid,$emptyValidatedFieldArray,$fieldArray);




/***	UPDATE SETTINGS			********************************************************/
if(isset($_POST['UPDATE_SETTINGS']))
{
	/*** LOOP THROUGH SETTINGS ***/
	$sel = "SELECT * FROM settings WHERE active='1' ".$_SETTINGS['demosqland']."";
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
		
	header("Location: {$_SERVER["PHP_SELF"]}?VIEW={$_REQUEST["VIEW"]}&REPORT=Settings Updated&SUCCESS=1&".SID);
	exit();
}

/*** UPDATE CONTENT FROM LIVE 	********************************************************/
elseif($_POST['UPDATE_FROM_DATABASE'] != "")
{

//$_SETTINGS['dbHost'] 		= 'localhost';									// Database Host
//$_SETTINGS['dbName']		= 'wes-v15';									// Database Name
//$_SETTINGS['dbUser'] 		= 'root';										// Database User
//$_SETTINGS['dbPass'] 		= 'Karlkarl1';									// Database Password

	header("Location: {$_SERVER["PHP_SELF"]}?VIEW={$_REQUEST["VIEW"]}&REPORT=Website content has been updated from ".$_POST['database'].".&SUCCESS=1");
	exit();
	
}

/*** BACKUP WEBSITE ***/
elseif($_POST['BACKUP_WEBSITE'] != "")
{

	header("Location: {$_SERVER["PHP_SELF"]}?VIEW={$_REQUEST["VIEW"]}&SUB=BACKUP&REPORT=!! Backups in WES coming soon! For now please email your developr/administrator for a backup. No backup saved. !!&SUCCESS=0");
	exit();

	// BACKUP THESE AREAS
	// directory should have trailing slash 
	$configBackup = array('../includes/','../themes/','../uploads/','../uploads-beadrow/','../uploads-products/','../uploads-user/','../.htaccess');

	// which directories to skip while backup 
	//$configSkip   = array('backup/');  

	// Put backups in which directory 
	$configBackupDir = '../website_backup_files/';

	//  Databses you wish to backup , can be many ( tables array if contains table names only those tables will be backed up ) 
	$configBackupDB[] = array('server'=>$_POST['host'],'username'=>$_POST['user'],'password'=>$_POST['password'],'database'=>$_POST['database'],'tables'=>array());

	// Put in a email ID if you want the backup emailed 
	$configEmail = $_POST['email'];

	$backupName = "backup-".date('d-m-y H-i-s').'.zip';

	$createZip = new createZip;

	if (isset($configBackup) && is_array($configBackup) && count($configBackup)>0)
	{

		// Lets backup any files or folders if any

		foreach ($configBackup as $dir)
		{
			$basename = basename($dir);

			// dir basename
			if (is_file($dir))
			{
				$fileContents = file_get_contents($dir);
				$createZip->addFile($fileContents,$basename);
			}
			else
			{

				$createZip->addDirectory($basename."/");

				$files = directoryToArray($dir,true);

				$files = array_reverse($files);

				foreach ($files as $file)
				{

					$zipPath = explode($dir,$file);
					$zipPath = $zipPath[1];

					// skip any if required

					$skip =  false;
					foreach ($configSkip as $skipObject)
					{
						if (strpos($file,$skipObject) === 0)
						{
							$skip = true;
							break;
						}
					}

					if ($skip) {
						continue;
					}


					if (is_dir($file))
					{
						$createZip->addDirectory($basename."/".$zipPath);
					}
					else
					{
						echo $file."<Br>";
						$fileContents = file_get_contents($file);
						$createZip->addFile($fileContents,$basename."/".$zipPath);
					}
				}
			}

		}

	}

	if (isset($configBackupDB) && is_array($configBackupDB) && count($configBackupDB)>0)
	{
		
		 foreach ($configBackupDB as $db)
		 {
			 $backup = new MySQL_Backup(); 
			 $backup->server   = $db['server'];
			 $backup->username = $db['username'];
			 $backup->password = $db['password'];
			 $backup->database = $db['database'];
			 $backup->tables   = $db['tables'];
			 
			 $backup->backup_dir = $configBackupDir;
			 
			 $sqldump = $backup->Execute(MSB_STRING,"",false);

			 $createZip->addFile($sqldump,$db['database'].'-sqldump.sql');
			 
		 }

	}

	$fileName = $configBackupDir.$backupName;
	$fd = fopen ($fileName, "wb");
	$out = fwrite ($fd, $createZip -> getZippedfile());
	fclose ($fd);

	// Dump done now lets email the user 

	//if (isset($configEmail) && !empty($configEmail)) 
	//{
	//	@mailAttachment($fileName,$configEmail,$_SETTINGS['automated_reply_email'],$_SETTINS['site_name']." Website Backup",$_SETTINGS['automated_reply_email'],'Backup - '.$backupName,"Backup file is attached");
	//}


	
	//header("Location: {$_SERVER["PHP_SELF"]}?VIEW={$_REQUEST["VIEW"]}&SUB=BACKUP&REPORT=Website has been backed up successfully.&SUCCESS=1");
	//exit();

















	/*
	// Create the mysql backup file
	// edit this section
	//$dbhost = "yourhost"; // usually localhost
	//$dbuser = "yourusername";
	//$dbpass = "yourpassword";
	///$dbname = "yourdb";
	$sendto = "Webmaster <webmaster@yourdomain.com>";
	$sendfrom = "Automated Backup <backup@yourdomain.com>";
	$sendsubject = "Daily Mysql Backup";
	$bodyofemail = "Here is the daily backup.";
	// don't need to edit below this section
	
	// BACKUP DATABASE
	$backupfile = "../website_backup_files" . date("Y-m-d") . '.sql';
	system("mysqldump -h ".$_POST['host']." -u ".$_POST['username']." -p ".$_POST['password']." ".$_POST['database']." > ".$backupfile."");

	
	
	sendEmail($_POST['email'],$_SETTINGS['automated_reply_email'],''.$_SETTINGS['site_name'].' Website Backup','Here is the website backup',$backupfile)
		
		

	// Delete the file from your server
	unlink($backupfile);
	*/

}

/*** FIX PATHS ****/
elseif($_POST['FIX_ABSOLUTE_PATHS'] != ""){
	
	$select = "SELECT c.page_id,c.content,c.id FROM content c LEFT JOIN pages p ON c.page_id=p.id AND p.active='1' WHERE c.content != ''";
	$result = doQuery($select);
	$num = mysql_num_rows($result);
	while($i<$num){
		$row = mysql_fetch_array($result);
		
		// GET THE
		$content = $row['content'];
		$html = $content;
		
		$html = str_replace("\n", ' ', $html);
		
		
		/**
		 *
		 * LINKS / href
		 *
		 */
		
		preg_match_all('/<a[\s]+[^>]*href\s*=\s*([\"\']+)([^>]+?)(\1|>)/i', $html, $m);
		/* this regexp is a combination of numerous 
		versions I saw online; should be good. */

		foreach($m[2] as $url) {
		
			$url1 = trim($url);

			/* get rid of PHPSESSID, #linkname, &amp; and javascript: */
			$url1 = preg_replace(
			array('/([\?&]PHPSESSID=\w+)$/i','/(#[^\/]*)$/i', '/&amp;/','/^(javascript:.*)/i'),
			array('','','&',''),
			$url1);

			$url2 = relative2absolute("".$_SETTINGS['website']."", $url);    

			$content = str_replace($url1,$url2,$content);
			
		
		}
		
		
		/**
		 *
		 * IMAGES
		 *
		 */
		preg_match_all('/<img[\s]+[^>]*src\s*=\s*([\"\']+)([^>]+?)(\1|>)/i', $html, $j);
		foreach($j[2] as $url) {
		
			$url1 = trim($url);

			/* get rid of PHPSESSID, #linkname, &amp; and javascript: */
			$url1 = preg_replace(
			array('/([\?&]PHPSESSID=\w+)$/i','/(#[^\/]*)$/i', '/&amp;/','/^(javascript:.*)/i'),
			array('','','&',''),
			$url1);
			
			$url2  = relative2absolute("".$_SETTINGS['website']."", $url);    

			$content = str_replace($url1,$url2,$content);

		}
		
		doQuery("UPDATE content SET content='".escape_smart($content)."' WHERE id='".$row['id']."'");
		
		//echo "<br><br><Br> ------------------- <br><br><br>";
		
		$i++;
	}

	header("Location: {$_SERVER["PHP_SELF"]}?VIEW={$_REQUEST["VIEW"]}&SUB=FIX&REPORT=Absolute paths fixed.&SUCCESS=1");
	exit();

}

/**
 * SETTINGS FORM
 * The main WES admin settings form
 */
elseif($_REQUEST['SUB'] == "" || $_REQUEST['SUB'] == "SETTINGS")
{
	$button = "Update Settings";
	echo tableHeader("Website Settings",2,'100%');
	echo "<FORM method='post' enctype='multipart/form-data' ACTION='' name='wesform' id='wesform'>";		
			
			// create a new WysiwygPro Instance
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

			$editor->value = $ro["content"];
			$editor->displayFileBrowserJS('OpenFileBrowser');
			
			echo "<tr><td colspan='2' style='margin:0px; padding:0px;'>";
			
			echo "
			<script type='text/javascript'>
			$(function() {
				$('#tabs').tabs({ cookie: { expires: 1 } });
			});
			</script>
			";
			
			echo "
			<div class='demo'>
				<div id='tabs'>
					<ul style='border-bottom:1px solid #f1f1f1;'>						
						<li><a href='#tabs-0'>General</a></li>";		
						
						$sel = "SELECT name,module_name FROM settings_groups WHERE active='1'";
						$res = doQuery($sel);
						$num = mysql_num_rows($res);
						$j = 0;
						$tab = 1;
						while($j<$num){	
							$row=mysql_fetch_array($res);						
							$sel1 = "SELECT status FROM wes_modules WHERE `unique_identifier`='".$row['module_name']."' LIMIT 1";
							$res1 = doQuery($sel1);
							$row1 = mysql_fetch_array($res1);
							if($row1['status'] == 'Installed' || $row['module_name'] == ''){								
								echo "<li><a href='#tabs-".$tab."'>".$row['name']."</a></li>";								
							}
							$j++;
							$tab++;
						}					
						
					echo "</ul>"; // END LIST
					echo "
					<div id='tabs-0'>						
						<Table width='100%'>";
						
						$sela = "SELECT * FROM settings WHERE active=1 ".$_SETTINGS['demosqland']." AND group_id='' ORDER BY type ASC";
						$resa = doQuery($sela);
						$numa = mysql_num_rows($resa);
						$ja = 0;					
						while($ja<$numa){
							$rowa = mysql_fetch_array($resa);							
							$Settings->displaySettingField($rowa,$j,$ja);							
							$ja++;
						}
						
					echo "
						</table>						
					</div>";
					
					// SETTINGS GROUPS
					$sel = "SELECT * FROM settings_groups WHERE active='1'";
					$res = doQuery($sel);
					$num = mysql_num_rows($res);
					$j = 0;
					$tab = 1;
					
					while($j<$num){	
						$row = mysql_fetch_array($res);
						$sel1 = "SELECT status FROM wes_modules WHERE `unique_identifier`='".$row['module_name']."' LIMIT 1";
						$res1 = doQuery($sel1);
						$row1 = mysql_fetch_array($res1);
						if($row1['status'] == 'Installed' || $row['module_name'] == ""){
							
						echo "	
							<div id='tabs-".$tab."'>						
								<Table width='100%'>";	
								
								$sela = "SELECT * FROM settings WHERE active=1 ".$_SETTINGS['demosqland']." AND group_id='".$row['group_id']."' ORDER BY id,user_friendly_name,type ASC";
								$resa = doQuery($sela);
								$numa = mysql_num_rows($resa);
								$ja = 0;						
								while($ja<$numa){
									$rowa = mysql_fetch_array($resa);							
									$Settings->displaySettingField($rowa,$j,$ja);							
									$ja++;
								}
						echo "							
								</table>						
							</div>";					
							
						}
						$j++;
						$tab++;
					}
				echo "				
				</div>
			</div>
		</td></tr>
	</table>		
	<div id='submit'>";			
	if (isset($_GET["xid"])){
		echo "<a target=\"_blank\" href=\"".$_SETTINGS['website']."index.php?page_id=".$_REQUEST['xid']."\">Go To Page</a> &nbsp;&nbsp;";
		echo "<INPUT TYPE=SUBMIT NAME=DELETE value=\"Delete\" onclick=\"return confirm('Are You Sure?');\">";
		echo "<INPUT TYPE=SUBMIT NAME=\"PREVIEW_PAGE\" VALUE=\"Preview\">";
	}	
	echo "<INPUT TYPE=SUBMIT NAME=\"".strtoupper(str_replace(" ", "_", $button))."\" VALUE=\"$button\">";
	
	
	echo "
	</div>
	</FORM>";	
/*** NEW SETTING ***/	
}

/*** NEW SETTING FORM			********************************************************/
elseif($_REQUEST['SUB'] == "NEWSETTING")
{

	$button = "Add Setting";
	$doing = "New Setting";	
	// START FORM
	startAdminForm();
	// START TABLE
	echo tableHeader($doing,2,'100%');
	// SETTING NAME
	adminFormField("*Name","name",$_POST['name'],"textbox");	
	// SETTING VALUE
	adminFormField("*Value","value",$_POST['value'],"textbox");	
	// SETTING DESCRIPTION
	adminFormField("Description","description",$_POST['description'],"textbox");	
	// USER FRIENDLY NAME
	adminFormField("Label","user_friendly_name",$_POST['user_friendly_name'],"textbox");
	// SETTING TYPE
	echo "
			<tr>
			<th>*Type</th>
			<td>
				<select name='type'>
					<option value='Textbox'>Textbox</option>
					<option value='Textarea'>Text Area</option>
					<option value='Boolean'>Boolean</option>
					<option value='page'>Web Page</option>
					<option value='table_row_id'>SQL Table</option>
					<option value='Image'>Image</option>
				</select>
			</td>
			</tr>";
	
	// SETTING GROUP
	echo	"<TR BGCOLOR='#f2f2f2'>";
	echo	"	<Th width='200' height='40' style='padding-left:20px;'>Group</Th>";
	echo	"	<TD>";
	$sel1 = "SELECT * FROM settings_groups WHERE active='1'";
	$res1 = doQuery($sel1);
	$num1 = mysql_num_rows($res1);
	$i1 = 0;
	echo 	"	<select name='group_id'>";
	echo	"		<option value='0'>General</option>";
					while($i1<$num1){
						$row1 = mysql_fetch_array($res1);
						echo "<option  value='".$row1['group_id']."'>".$row1['name']."</option>";					
						$i1++;
					}
	echo	"	</TD>";
	echo	"</TR>";
	// END FORM
	endAdminForm($button,"sid","SETTING");
}	

/*** UPDATE FROM DATABASE***/
elseif($_REQUEST['SUB'] == "UPDATE")
{
	
	if(!strstr($_SERVER['SERVER_NAME'],"dev.")){
		
		$doing = "This is a production (live) website.";	
		// START FORM
		//startAdminForm();
		// START TABLE
		echo tableHeader($doing,2,'100%');
		
		echo "
			
			<tr>
				<td colspan='2'>
					<p>
					This is a production (live) website, you cannot update the database content.
					</p>
				</td>
			</tr>";
		
		echo "</table>";
	}
	// PRODUCTION
	else
	{
		
		
		$button = "Update Database";
		$doing = "Updating Dev Database from Production Website";	
		// START FORM
		startAdminForm();
		// START TABLE
		echo tableHeader($doing,2,'100%');
		
		echo "
			
			<tr>
				<td colspan='2'>
					<p>
					Enter the credentials corresponding to the database you wish to fetch more recent data from.
					</p>
				</td>
			</tr>
			
			<tr>
			<th>*Host</th>
			<td><input name='host' id='host' value='".$_POST['host']."'></td>
			</tr>
			
			<tr>
			<th>*Database</th>
			<td><input name='database' id='database' value='".$_POST['database']."'></td>
			</tr>
			
			<tr>
			<th>*Username</th>
			<td><input name='username' id='username' value='".$_POST['username']."'></td>
			</tr>
			
			<tr>
			<th>*Password</th>
			<td><input name='password' id='password' value='".$_POST['password']."'></td>
			</tr>
		
		";
		
		// END FORM
		echo "
			</table>
			<div id='submit'>
			
			<input type='submit' name='UPDATE_FROM_DATABASE' value='".$button."'>
			</div>
		";
	}
}

/*** BACKUP SITE FORM 			********************************************************/
elseif($_REQUEST['SUB'] == 'BACKUP')
{
	$button = "Backup Website";
	$doing = "Website Backup";	
	// START FORM
	startAdminForm();
	// START TABLE
	echo tableHeader($doing,2,'100%');
	
	// DEVELOPERS EMAIL
	if($_POST['email'] == ''){ $_POST['email'] = $_SETTINGS['developer_email']; }
	
	// EMAIL NAME
	echo "
		
		<tr>
			<td colspan='2'>
			
				<p>
				Backing up and Restoring your website is easy! The \"Host\", \"Database\", and \"User\" fields should be auto-completed.
				</p>
				
				<p>
				You will need to enter your database's password. This is an added layer of security.
				</p>
				
				<p>
				Enter the email address where you want to send the backup.
				</p>
			
			</td>
		</tr>
		
		<tr>
		<th>*Host</th>
		<td><input name='host' id='host' value='".$_SETTINGS['dbHost']."'></td>
		</tr>

		<tr>
		<th>*Database</th>
		<td><input name='database' id='database' value='".$_SETTINGS['dbName']."'></td>
		</tr>
		
		<tr>
		<th>*User</th>
		<td><input name='user' id='user' value='".$_SETTINGS['dbUser']."'></td>
		</tr>
		
		<tr>
		<th>*Password</th>
		<td><input name='password' id='password' value='".$_POST['password']."'></td>
		</tr>
		
		<tr>
		<th>*Email</th>
		<td><input name='email' id='email' value='".$_POST['email']."'> <small>A backup will be emailed to this address.</small></td>
		</tr>
	
	";	

	// END FORM
	echo "
		</table>
		<div id='submit'>
		
		<input type='submit' name='BACKUP_WEBSITE' value='".$button."'
		</div>
		</form>
	";
}

/*** FIX ABSOLUTE PATHS ***/
elseif($_REQUEST['SUB'] == 'FIX')
{
	if($_POST['PREVIEW_ABSOLUTE_PATHS'] == ""){
		$button = "Preview Absolute Paths";
	} else {
		$button = "Fix Absolute Paths";
	}
	
	$doing = "Fixing Absolute Paths";	
	// START FORM
	startAdminForm();
	// START TABLE
	echo tableHeader($doing,2,'100%');
	

	// END FORM
	echo "
		
		<tr>
		<Td colspan='2'>
		<p><b>WARNING:</b> Only Fix Absolute Paths If You Know What You Are Doing!</p>
		<p>Your website uses absolute paths instead of relative paths. \"http://www.yourdomain.com/images/picture.jpg\" instead of \"/images/picture.jpg\".</p>
		<p>The links and images you add through the CMS may be broken if you have just moved a site from development to production or if you have just updated the developemnt database with live content.</p>
		<p>In either of these cases you can fix the absolute paths by clicking the button below.</p>
		</td>
		</tr>
		
		</table>
		
		<div id='submit'>
		
		<input type='submit' name='".strtoupper(str_replace(" ","_",$button))."' value='".$button."'>
		</div>
	";
	
	if($_POST['PREVIEW_ABSOLUTE_PATHS'] != ""){
		$select = "SELECT c.page_id,c.content FROM content c LEFT JOIN pages p ON c.page_id=p.id AND p.active='1' WHERE c.content != ''";
		$result = doQuery($select);
		$num = mysql_num_rows($result);
		while($i<$num){
			$row = mysql_fetch_array($result);
			
			// GET THE
			$content = $row['content'];
			$html = $content;
			//$links=array();
			
			if($row['page_id'] == '0'){
				$pagename = "Layout";
			} else {
				$pagename = lookupDbValue('pages','name',$row['page_id'],'id');
			}
			
			echo "<b> ".$pagename.": </b><Br>";
			
			/*
			Extract the BASE tag (if present) for
			relative-to-absolute URL conversions later
			*/

			//if(preg_match('/<base[\s]+href=\s*[\"\']?([^\'\" >]+)[\'\" >]/i',$html, $matches)){
			//$base_url=$matches[1];
			//} else {
			//$base_url=$page_url;
			//}

			$html = str_replace("\n", ' ', $html);
			
			
			/**
			 *
			 * LINKS / href
			 *
			 */
			
			preg_match_all('/<a[\s]+[^>]*href\s*=\s*([\"\']+)([^>]+?)(\1|>)/i', $html, $m);
			/* this regexp is a combination of numerous 
			versions I saw online; should be good. */

			foreach($m[2] as $url) {
			
				$url=trim($url);

				/* get rid of PHPSESSID, #linkname, &amp; and javascript: */
				$url=preg_replace(
				array('/([\?&]PHPSESSID=\w+)$/i','/(#[^\/]*)$/i', '/&amp;/','/^(javascript:.*)/i'),
				array('','','&',''),
				$url);

				echo $url."<br>";
				
				/* turn relative URLs into absolute URLs. 
				relative2absolute() is defined further down 
				below on this page. */
				$url = relative2absolute("".$_SETTINGS['website']."", $url);    

				echo $url."<br>";
				// check if in the same (sub-)$domain
				//if(preg_match("/^http[s]?:\/\/[^\/]*".str_replace('.', '\.', $domain)."/i", $url)) {
					//save the URL
				//	if(!in_array($url, $links)) $links[]=$url;
				//} 
			}
			
			
			/**
			 *
			 * IMAGES
			 *
			 */
			preg_match_all('/<img[\s]+[^>]*src\s*=\s*([\"\']+)([^>]+?)(\1|>)/i', $html, $j);
			foreach($j[2] as $url) {
			
				$url=trim($url);

				/* get rid of PHPSESSID, #linkname, &amp; and javascript: */
				$url=preg_replace(
				array('/([\?&]PHPSESSID=\w+)$/i','/(#[^\/]*)$/i', '/&amp;/','/^(javascript:.*)/i'),
				array('','','&',''),
				$url);

				echo $url."<br>";
				
				/* turn relative URLs into absolute URLs. 
				relative2absolute() is defined further down 
				below on this page. */
				$url = relative2absolute("".$_SETTINGS['website']."", $url);    

				echo $url."<br>";
				// check if in the same (sub-)$domain
				//if(preg_match("/^http[s]?:\/\/[^\/]*".str_replace('.', '\.', $domain)."/i", $url)) {
					//save the URL
				//	if(!in_array($url, $links)) $links[]=$url;
				//} 
			}
			
			
			
			echo "<br><br><Br> ------------------- <br><br><br>";
			
			$i++;
		}
		//die();
		//exit();
	
	}
	
	
}