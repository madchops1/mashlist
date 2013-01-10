<?
//ob_start();
//die("hello world");
//exit;
error_reporting(E_ALL);
session_start();

// STEP SETUP
if($_SESSION['setup-step'] == '' || $_POST['setup-step'] == "" || $_REQUEST['setup-step'] == ""){ $_SESSION['setup-step'] = '1'; }

// SETUP FILESTRING AND CONFIG FLAG VAR
$filestring = "";
$config = 0;

// GET THE CONFIG FILE CONTENTS TO SEE IF ITS BEEN WRITTEN
@$filestring = file_get_contents('../includes/config.php');

// CONNECT TO DB
@mysql_connect($_SESSION['dbhost'], $_SESSION['dbusername'], $_SESSION['dbpassword']);
@mysql_select_db($_SESSION['dbname']);

$mysqlError = mysql_error();


// CHECK THAT THE DB IS WORKING
//if($mysqlError == "" AND $filestring != ""){	
if($filestring != ""){
		include('../includes/config.php') or die('could not include config!');
		$config = 1;
		echo "<pre>";
		var_dump($_SESSION['session']);
		echo "</pre>";
		echo "<br>";
		echo "AUTH: ".$_SESSION["session"]->admin->auth;
		echo "<br>";
		die();
		exit();
		
		if($_SESSION["session"]->admin->auth == 0){
				header('Location: login.php?SUCCESS=0&REPORT=Please login&RETURN=setup.php?setup-step=2');
				die();
				exit();
		}			
}

//error_reporting(E_ALL);

function setupnav(){
	global $_SESSION;	
	echo "<center>WES&trade; Setup Step ".$_SESSION['setup-step']."</center>";	
	?>
	<p style="width:550px; margin-bottom:0px; margin-left:25px; text-align:left; font-size:10px;">
		<style>	a { color:#000; } </style>
		<span <? if($_SESSION['setup-step'] == '1'){ ?> style='font-weight:bold; ' <? } ?> ><a href='?setup-step=1'>1. Database		</a></span> >> 
		<span <? if($_SESSION['setup-step'] == '2'){ ?> style='font-weight:bold; ' <? } ?> ><a href='?setup-step=2'>2. User		</a></span> >> 
		<span <? if($_SESSION['setup-step'] == '3'){ ?> style='font-weight:bold; ' <? } ?> ><a href='?setup-step=3'>3. Website Settings	</a></span> >> 
		<span <? if($_SESSION['setup-step'] == '4'){ ?> style='font-weight:bold; ' <? } ?> ><a href='?setup-step=4'>4. Modules		</a></span> >> 	
		<span <? if($_SESSION['setup-step'] == '5'){ ?> style='font-weight:bold; ' <? } ?> ><a href='?setup-step=5'>5. Replace Path 	</a></span> >>
		<span <? if($_SESSION['setup-step'] == '6'){ ?> style='font-weight:bold; ' <? } ?> ><a href='?setup-step=6'>6. Update WES 	</a></span> >> 
		<span <? if($_SESSION['setup-step'] == '7'){ ?> style='font-weight:bold; ' <? } ?> >Finished					</span>
	</p>	
	<?
}

if($_POST['next'] != ''){
	//
	// STEP 1 - SETUP CONFIGURATION FILE
	//
	if($_POST['setup-step'] == '1'){
		
		// GET CONFIG FILE
		$filestring = file_get_contents('../includes/config-example.php');		
		// HOST
		$filestring = str_replace("|dbHost|",$_POST['dbhost'],$filestring);
		// NAME
		$filestring = str_replace("|dbName|",$_POST['dbname'],$filestring);
		// USERNAME
		$filestring = str_replace("|dbUser|",$_POST['dbusername'],$filestring);
		// PASSWORD
		$filestring = str_replace("|dbPass|",$_POST['dbpassword'],$filestring);
		// LOCATION
		$filestring = str_replace("|website_path|",$_POST['website_path'],$filestring);
		
		// WRITE CONFIG FILE
		chmod("../includes", 0777); 
		unlink("../includes/config.php");
		fopen("../includes/config.php","w");
		file_put_contents('../includes/config.php',$filestring);
		chmod("../includes", 0755); 
		
		// CHMOD OTHER IMPORTANT DIRECTORIES
		@chmod("../uploads", 0777);
		@chmod("../uploads/wpThumbnails", 0777);
		@chmod("../uploads-user", 0777);
		@chmod("../uploads-user/wpThumbnails", 0777);
		
		
		// SET DB INFO
		$_SESSION['dbhost'] = $_POST['dbhost'];
		$_SESSION['dbname'] = $_POST['dbname'];
		$_SESSION['dbusername'] = $_POST['dbusername'];
		$_SESSION['dbpassword'] = $_POST['dbpassword'];		
		$_SESSION['setup-step'] = '2';
	}
	
	//
	// STEP 2 - SETUP ADMIN USER
	//
	if($_POST['setup-step'] == '2'){
			if($error != 1){			
			// RESET ADMIN TABLE
			mysql_query("TRUNCATE TABLE `admin`");
			// SETUP ADMIN USER
			$select = 	"INSERT INTO admin SET ".
						"name='Super Admin',".
						"username='".$_POST['admin_username']."',".
						"password='".md5($_POST['admin_password1'])."',".
						"active='1',".
						"accesslevel='0',".
						"created=NULL";
			mysql_query($select);		
			$_SESSION['setup-step'] = '3';
		} else {
			$_SESSION['setup-step'] = '2';
		}	
	}
	
	//
	// STEP 3 - CONFIGURE SETTINGS
	//
	if($_POST['setup-step'] == '3'){
		// SITE NAME
		$select = "UPDATE settings SET value='".$_POST['website_name']."' WHERE name='Site Name'";
		mysql_query($select);
		
		// THEME
		$select = "UPDATE settings SET value='".$_POST['theme']."' WHERE name='Theme'";
		mysql_query($select);
		
		$_SESSION['setup-step'] = '4';
		//echo "SETTINGS CONFIGURED<br>";		
	}
	
	//
	// STEP 4 - CONFIGURE MODULES
	//
	if($_POST['setup-step'] == '4'){
		// UNINSTALL MODULES
		mysql_query("UPDATE wes_modules SET active='0',status='Uninstalled'");		
		// GET AND LOOP MODULES
		$select = "SELECT * FROM wes_modules ORDER BY module_id DESC";
		$result = mysql_query($select);
		$num = mysql_num_rows($result);
		$i = 0;
		while($i<$num){
			$row = mysql_fetch_array($result);
			$check = "";
			$check = $row['unique_identifier'];
			//echo "POST: ".$_POST[$check]."<br>";
			if($_POST[$check] == '1'){	
				// INSTALL MODULE
				$sel = "UPDATE wes_modules SET active='1',status='Installed' WHERE unique_identifier='".$row['unique_identifier']."'";
				$res = mysql_query($sel);
			}			
			$i++;
		}
		// GO TO NEXT STEP
		$_SESSION['setup-step'] = '5';
	}
	
	//
	// STEP 5 - REPLACE PATHS IN CONTENT FOR IMAGES,ETC
	//
	if($_POST['setup-step'] == '5'){
		// GET THE CONTENT LOOP It
		$select = "SELECT * FROM content";
		$result = doQuery($select);
		$num = mysql_num_rows($result);
		$i = 0;
		while($i<$num){
			$row = mysql_fetch_array($result);
			
			// REPLACE THE STRING			
			$newString = escape_smart(str_replace(''.$_POST['find'].'',''.$_POST['replace'].'',$row['content']));
			
			
			
			// UPDATE THE STRING			
			$update = "UPDATE content SET content='".$newString."' WHERE id='".$row['id']."'";
			doQuery($update);
					
			$i++;
		}
		// GO TO NEXT STEP
		$_SESSION['setup-step'] = '6';
	}
	
	//
	// STEP 6 - UPDATE WES
	//
	if($_POST['setup-step'] == '6'){
		
	}
}

if($_POST['skip'] != ""){
	//echo "SKIPPED STEP<Br>";
	$_SESSION['setup-step'] = $_SESSION['setup-step'] + 1;
}

if($_GET['setup-step'] == '1'){ $_SESSION['setup-step'] = '1'; }
if($_GET['setup-step'] == '2'){ $_SESSION['setup-step'] = '2'; }
if($_GET['setup-step'] == '3'){ $_SESSION['setup-step'] = '3'; }
if($_GET['setup-step'] == '4'){ $_SESSION['setup-step'] = '4'; }
if($_GET['setup-step'] == '5'){ $_SESSION['setup-step'] = '5'; }
if($_GET['setup-step'] == '6'){ $_SESSION['setup-step'] = '6'; }
if($_GET['setup-step'] == '7'){ $_SESSION['setup-step'] = '7'; }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" >
	<head>
				
		<title>WES&trade; Setup</title>
		<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
		<meta name="description" content="Admin Login" />	
		<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
		<link href="scripts/adminStyles.css" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" type="text/css" media="screen" href="scripts/jquery/ui-smoothness/jquery-ui-1.7.2.custom.css" />
		
		<!--[if lt IE 7]>
		<link href="scripts/adminStylesIE.css" rel="stylesheet" type="text/css" />
		<![endif]-->
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.1/jquery.min.js"></script>
		<script type="text/javascript" src="scripts/jquery/jquery-ui-1.7.2.custom.min.js"></script>
		
		<style>
		hr{ 
		border-color:#CCCCCC #FFFFFF #FFFFFF #CCCCCC;
		border-style:solid;
		border-width:1px;
		}
		</style>		
		
	</head>
	<body class="loginbg">		
		<div id="login-logo">
			<center><img src="images/weslogo.png" /></center>
		</div>

		<div id="login" style='width:600px;'>
			<center>
			<?
			//if($_REQUEST['message'] != ""){ $_REQUEST['REPORT'] = $_REQUEST['message']; $_REQUEST['SUCCESS'] = '0'; }
			//report($_REQUEST['REPORT'],$_REQUEST['SUCCESS']);
			?>
			</center>
			<form name="loginForm" class="login" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo SID; ?>" enctype="multipart/form-data">
				
					<?
					setupnav();
					?>
					<hr>
					<?
					/**
					 *
					 * STEP 1 | DATABASE
					 *
					 **/
					if($_SESSION['setup-step'] == '1'){			
						if($_POST['dbhost'] == ""){ $_POST['dbhost'] = "localhost"; }
						if($config == 1){ $_POST['dbhost']=$_SETTINGS['dbHost']; }
						?>
						<p>
						<label for="username">DB Host</label>					
						<input id="dbhost" name="dbhost" size="30" type="text"  VALUE="<?=$_POST['dbhost']?>" />
						</p>
						
						<?
						if($_POST['dbname'] == ""){ $_POST['dbname'] = "wes-"; }
						if($config == 1){ $_POST['dbname']=$_SETTINGS['dbName']; }
						?>
						<p>
						<label for="username">DB Name</label>					
						<input id="dbname" name="dbname" size="30" type="text"  VALUE="<?=$_POST['dbname']?>" />
						</p>
						
						<?
						if($_POST['dbusername'] == ""){ $_POST['dbusername'] = "root"; }
						if($config == 1){ $_POST['dbusername']=$_SETTINGS['dbUser']; }
						?>
						<p>
						<label for="username">DB Username</label>					
						<input id="dbusername" name="dbusername" size="30" type="text"  VALUE="<?=$_POST['dbusername']?>" />
						</p>
						
						<?
						if($_POST['dbpassword'] == ""){ $_POST['dbpassword'] = "Karlkarl1"; }
						if($config == 1){ $_POST['dbpassword']=$_SETTINGS['dbPass']; }
						?>
						<p>
						<label for="username">DB Password</label>					
						<input id="dbpassword" name="dbpassword" size="30" type="text"  VALUE="<?=$_POST['dbpassword']?>" />
						</p>					
						
						<?
						if($_POST['website_path'] == ""){ $_POST['website_path'] = "/2010/foldername/"; }
						if($config == 1){ $_POST['website_path']=$_SETTINGS['website_path']; }
						?>
						<p>
						<label for="username">Location</label>					
						<input id="website_path" name="website_path" size="30" type="text"  VALUE="<?=$_POST['website_path']?>" />
						</p>
						
					<?
					}
					/**
					 *
					 * STEP 2 | Admin Username and Password
					 *
					 **/
					elseif($_SESSION['setup-step'] == '2'){
						if($config == 1){ $_POST['admin_username'] = lookupDbValue('admin', 'username', '0', 'accesslevel'); }					
						?>
						<p>
						<label for="username">Username</label>					
						<input id="admin_username" name="admin_username" size="30" type="text"  VALUE="<?=$_POST['admin_username']?>" />
						</p>					
						
						<?
						if($config == 1){ $_POST['admin_password1'] = "password"; }		
						?>
						<p>
						<label for="username">Password</label>					
						<input id="admin_password1" name="admin_password1" type='text' size="30" type="text"  VALUE="<?=$_POST['admin_password1']?>" />
						</p>					
					
						<?
					} elseif($_SESSION['setup-step'] == '3'){					
						?>

							<?
							if($config == 1){ $_POST['website_name'] = $_SETTINGS['site_name']; }		
							?>
							<p>
							<label for="username">Website Name</label>					
							<input id="website_name" name="website_name" size="30" type="text"  VALUE="<?=$_POST['website_name']?>" />
							</p>	
							
							<?
							if($config == 1){ $_POST['theme'] = $_SETTINGS['theme']; }		
							?>
							<p>
							<label for="username">Theme</label>					
							<input id="theme" name="theme" size="30" type="text"  VALUE="<?=$_POST['theme']?>" />
							</p>	
							
						<?
					} elseif($_SESSION['setup-step'] == '4'){	
						$select = "SELECT * FROM wes_modules ORDER BY module_id DESC";
						$result = mysql_query($select);
						$num = mysql_num_rows($result);
						$i = 0;
						while($i<$num){
							$row = mysql_fetch_array($result);
							?>
							<p style='width:150px; float:left; display:block;'>
							<label for="username"><?=$row['name']?></label>		
							<input type='checkbox' <? if($row['active'] == '1' AND $row['status'] == 'Installed'){ ?> CHECKED <? } ?> name='<?=$row['unique_identifier']?>' value='1'>
							</p>											
							<?
							$i++;
						}
					} elseif($_SESSION['setup-step'] == '5'){
						?>
						 <p>
						 <label>Find</label><input type='text' name='find' value='<?=$_POST['find']?>'> <small><i>Ex. "2010/osplacejazz/"</i></small>
						 </p>
						 
						 <p>
						 <label>Replace</label><input type='text' name='replace' value='<?=$_POST['replace']?>'> <small><i>usually nothing</i></small>
						 </p>
						<?
					} elseif($_SESSION['setup-step'] == '6'){
						?>
						<p>To update wes enter the user and password for the the necessary area</p>
						 
						<?
						if($config == 1){ $_POST['host_user'] = $_SETTINGS['host_user']; }		
						?>
						 <p>
						 <label>User</label><input type='text' name='host_user' value='<?=$_POST['host_user']?>'> 
						 </p>
						 
						<?
						if($config == 1){ $_POST['host_password'] = $_SETTINGS['host_password']; }		
						?>
						 <p>
						 <label>Password</label><input type='text' name='host_password' value='<?=$_POST['host_password']?>'> 
						 </p>
						 
						 
						 <p>
								<button onclick='javascript:void(0);' >Begin Update</button>
						 </p>
						<?
					} elseif($_SESSION['setup-step'] == '7'){
						?>

						<p style="width:550px; margin-bottom:25px; margin-left:25px; text-align:center; ">
						Setup Complete. <a href='login.php'>Click here to login to WES&trade;.</a>
						</p>
						
						<?
					}
					?>
					
					
					<?
					if($_SESSION['setup-step'] != '7'){
					?>
						<hr style='clear:both;'>
						<p style="text-align:center;">
						<input type="hidden" name="setup-step" value="<?=$_SESSION['setup-step']?>" />
						<? if($_SESSION['setup-step'] == '6'){ ?>
								<input class="button" type="submit" name="skip" value="Next" /> <!-- this is named skip so that it won't trigger any action -->
						<? } else { ?>
								<input class="button" type="submit" name="next" value="Save" />
								<input class="button" type="submit" name="skip" value="Skip" />
						<? } ?>
						<input class="button" type="button" name="cancel" value="Cancel" />
						</p>
					<?
					}
					?>
			</form>
			
			<? if(mysql_error()){ echo "DB ERROR: ".mysql_error(); } ?>
		</div>
		<style>
		#errorbox {
				margin:10px 0;
				position:relative;
				height:auto;
				display:block;
		}
		</style>
		</body>
</html>