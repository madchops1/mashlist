<?
/*************************************************************************************************************************************
*
* 	This file is part of KSD's Wes software.
*   	Copyright (c) 2010-2011 Karl Steltenpohl Development LLC. All Rights Reserved.			
*	
*	Licensed under a Commercial Wes License (a "Wes License");
*	you may not use this file exept in compliance
*	with a Wes License.
*
*	You may obtain a copy of the Wes Licenses at	
*	http://www.wescms.com/license
*
*************************************************************************************************************************************/

/** Include config.php file
 *
 *
 *
 */	 
require_once('../includes/config.php');  
 
/*** Get URL Location Vars | LEGACY NOT USED ANYMORE ***/			
$section 	= preg_replace('/[^a-zA-Z0-9]/', '', $_GET['VIEW']);

/** Log In Check
 *
 *
 *
 */
 //var_dump($_SESSION['session']);
 //die();
 //exit;
 
if ( $_SESSION["session"]->admin->auth == 0 ){
	header('LOCATION: login.php?SUCCESS=0&REPORT=Please login');
	exit();
}

/** Logout
 *
 *
 *
 */
if( isset($_GET["LOGOUT"]) ){
	session_unset();
	session_destroy();
	header("location: ".$_SETTINGS["website"]."admin/login.php");
	exit();
}

/** File Manager Force Downloads
 *
 *
 *
 */
$FileManager = new FileManager();
$FileManager->downloadFile();	




?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title><?=$_SETTINGS['adminTitle']?></title>
		<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
		<link href="scripts/adminStyles.css" rel="stylesheet" type="text/css" />
		<link href="scripts/adminStylesIcons.css" rel="stylesheet" type="text/css" />
		<link href="scripts/jquery/multiselect/jquery.multiSelect.css" rel="stylesheet" type="text/css" />
		<style>	embed{ position:relative; top:7px; } </style>
		<?
		/**
		 *
		 * jQuery Styles & Scripts
		 *
		 */
		//if($_REQUEST['VIEW'] != "filemanager" OR ($_REQUEST['VIEW'] == 'filemanager' AND ($_REQUEST['filename'] != "" OR $_REQUEST['PHOTOS'] != "" OR $_REQUEST['DOCUMENTS'] != "" OR $_REQUEST['VIDEOS'] != "")))
		if($_REQUEST['VIEW'] != "filemanager" OR ($_REQUEST['VIEW'] == 'filemanager' AND $_REQUEST['SUB'] != '') OR ($_REQUEST['filename'] != "" OR $_REQUEST['PHOTOS'] != "" OR $_REQUEST['DOCUMENTS'] != "" OR $_REQUEST['VIDEOS'] != ""))
		{
			?>
						
			<? // FIREBUG LITE FOR NON FIREBUG BROWSERS ?>
			<!-- <script type='text/javascript' src='http://getfirebug.com/releases/lite/1.2/firebug-lite-compressed.js'></script> -->
			
			<? // JQUERY ITSELF ?>
			<script type="text/javascript" src="scripts/jquery/jquery-1.5.1.min.js" type="text/javascript" language="javascript"></script>
			
			<? // THE JQUERY UI ?>
			<script type="text/javascript" src="scripts/jquery/jquery-ui-1.8.14.custom.min.js"></script>
			<link type="text/css" href="scripts/jquery/ui-smoothness/jquery-ui-1.7.2.custom.css" rel="stylesheet" />	
			
			<? // TIMEPICKER ADDON TO JQUERY UI ?>
			<script type="text/javascript" src="scripts/jquery/jquery-ui-timepicker-addon.js"></script>
			
			<? // JQUERY OTHER ?>
			<script type="text/javascript" src="scripts/jquery/jquery.cookie.js"></script>
			
			<? // THE TABLE SORTING & PAGINATION ?>
			<script type="text/javascript" src="scripts/jquery/jquery.tablesorter.min.js"></script> 
			<script type="text/javascript" src="scripts/jquery/pagination/jquery.pagination.js" language="javascript"></script>		
			<script type="text/javascript" src="scripts/jquery/jquery.tablePagination.0.2.js" language="javascript"></script>			
			<!-- <link type="text/css" href="scripts/jquery/pagination/pagination.css" rel="stylesheet" /> -->
			
			<? // LOCALISATION PLUGIN -- USED IN CONJUCNTION WITH OTHER PLUGINS ?>
			<script type="text/javascript" src="scripts/jquery/plugins/localisation/jquery.localisation-min.js"></script>
			
			<? // SCROLL TO PLUGIN ?>
			<script type="text/javascript" src="scripts/jquery/plugins/scrollTo/jquery.scrollTo-min.js"></script>
			
			<? // MULTI SELECT PLUGIN ?>
			<script type="text/javascript" src="scripts/jquery/ui.multiselect.js"></script>
			<script type="text/javascript" src="scripts/jquery/multiselect/jquery.multiSelect.js"></script>
			<link type="text/css" href="scripts/jquery/ui.multiselect.css" rel="stylesheet" />	
			
			<? // EASY SLIDER PLUGIN ?>
			<script type="text/javascript" src="scripts/jquery/easySlider1.7.js"></script>
			
			<? // SIMPLE TIP PLUGIN ?>
			<script type="text/javascript" src="scripts/jquery/simpletip-1.3.1.js"></script>
			
			<? // FLOT ?>
			<!--[if IE]><script language="javascript" type="text/javascript" src="scripts/flot/excanvas.min.js"></script><![endif]-->
			<script type="text/javascript" src="scripts/flot/jquery.flot.js" language="javascript"></script>
			<script type="text/javascript" src="scripts/flot/jquery.flot.selection.js" language="javascript"></script>		
			
			<? //  ?>
			<script type="text/javascript" src="scripts/jquery/multiselect/jquery.bgiframe.min.js"></script>		
			
			<? // HOVER INTENT PLUGIN FOR THE WES LEFT TOOLBAR ?>
			<script type="text/javascript" src="scripts/jquery/jquery.hoverIntent.js"></script>
			
			<? // TIMEPICKER ?>
			<script type="text/javascript" src="scripts/jquery/jquery.timepicker.js"></script>
			<link type="text/css" href="scripts/jquery/timePicker.css" rel="stylesheet" />				
			
			<? // JCROP FOR THE IMAGE SLIDER AND OTHER IMAGE CROPPING ?>			
			<script src="scripts/jquery/Jcrop/js/jquery.Jcrop.js"></script>
			<link rel="stylesheet" href="scripts/jquery/Jcrop/css/jquery.Jcrop.css" type="text/css" />
			
			<? // FANCYBOX FROM PORTFOLIO ?>	
			<script type="text/javascript" src="modules/portfolio/scripts/jquery.fancybox-1.3.1/fancybox/jquery.mousewheel-3.0.2.pack.js"></script>
			<script type="text/javascript" src="modules/portfolio/scripts/jquery.fancybox-1.3.1/fancybox/jquery.fancybox-1.3.1.js"></script>
			<link rel="stylesheet" type="text/css" href="modules/portfolio/scripts/jquery.fancybox-1.3.1/fancybox/jquery.fancybox-1.3.1.css" media="screen" />
	
			<? // ECOMMERCE STAR RATING ?>
			<link rel="stylesheet" type="text/css" href="scripts/jquery/star-rating/jquery.rating.css" media="screen" />
			<script type="text/javascript" src="scripts/jquery/star-rating/jquery.rating.js"></script>	
	
			<? // LAZYKARL ?>
			<script type="text/javascript" src="scripts/jquery/lazykarl/lazykarl.js"></script>
	
			<? // SMALL FILE MANAGER PLUGIN
			include 'modules/file_manager/scripts/file_manager_docready.php';
			?>
				
			<?			
			/**
			 *
			 * END JQUERY PLUGINS
			 *
			 */			
			?>
			
			<? // WES JS FUNCTIONS ?>
			<!-- <script type="text/javascript" src="scripts/jsfunctions.js"></script> -->
			<?
			include 'scripts/jsfunctions.php';
			?>		
			
			<? // Google Analytics API
			if($_REQUEST['VIEW'] == 'analytics')
			{
				?>
				<script type="text/javascript" src="http://www.google.com/jsapi"></script>
				<script type="text/javascript" src="modules/analytics/scripts/accountFeed.js"></script>
				<?
			}
			?>
			
			<? // Flowplayer ?>
			<script type="text/javascript" src="scripts/flowplayer/example/flowplayer-3.2.0.min.js"></script>		
			
			<? // WES Dashboard Toolbar Script ?>
			<script type="text/javascript" src="scripts/toolbar.js"></script>				
			
			<?
		} else {
			
			/**
			 *
			 * Mootools For File Manager Environment
			 *
			 */
			?>
			<link type="text/css" href="modules/file_manager/scripts/styles.css" rel="stylesheet" />
			<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/mootools/1.2.2/mootools.js"></script>
			<script type="text/javascript" src="scripts/fancyupload-3.0-1/showcase/photoqueue/../../source/Swiff.Uploader.js"></script>
			<script type="text/javascript" src="scripts/fancyupload-3.0-1/showcase/photoqueue/../../source/Fx.ProgressBar.js"></script>
			<script type="text/javascript" src="http://github.com/mootools/mootools-more/raw/master/Source/Core/Lang.js"></script>
			<script type="text/javascript" src="scripts/fancyupload-3.0-1/showcase/photoqueue/../../source/FancyUpload2.js"></script>
			<script type="text/javascript" src="modules/file_manager/scripts/javascript.js"></script>			
			<?
		}
		?>	
	</head>
	
	<body style="background-color:#ddd;">
	
		<?
		// CONDITIONAL BACKGROUND IMAGE
		if($_SETTINGS['background'] != ""){
			// CUSTOM ADMIN USER BACKGROUND
			?>
			<style>
				#pagebackground{
					height:100%;
					left:0;
					position:fixed;
					top:0;
					width:100%;
				}
			</style>			
			<div id="pagebackground">
				<img width="100%" height="100%" src="<?=$_SETTINGS['website']."uploads/".$_SETTINGS['background']?>" >
			</div>
			<?
		}
		?>
		
		<div id="contentwrap">
			<div id="content">
				<div id="header">
					<div id="header-logo">
						<h1>
							<a href="index.php">
								<?
								// LOGO
								if($_SETTINGS['logoImage'] != ""){
									// SETTINGS LOGO
									?> <img src="<?=$_SETTINGS['logoImage']?>" /> <?						
								} else {
									// WES LOGO
									?>
									<img src="images/weslogo.png" />
									<?
								}						
								?>
							</a>
						</h1>
					</div>
					<div style="margin-right:20px;">
						<p id="welcomebar">
							<?
							// ADMIN WELCOME BAR
							if ($_SESSION["session"]->admin->auth==1){
								// WELECOME								
								echo "Welcome, ".$_SESSION["session"]->admin->name;
								
								// HELP
								//echo "&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp; <a href='".$_SERVER["PHP_SELF"]."?'>Help</a> ";
								
								// LOGOUT
								echo "&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp; <a href='".$_SERVER["PHP_SELF"]."?LOGOUT=1&'>Logout</a>	";														
							
								// DEMO MODE
								if($_SETTINGS['demo'] == '1'){								
									echo "&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp; You are in Demo Mode";		
								}
								
								// IF CMS VIEW WEBSITE
								if(checkActiveModule('0000000')){
									// VIEW SITE
									echo "&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp; <a href='../' target='_blank'>View Web Site</a>";
								}
							}
							?>
						</p>				
						
						<?
						if($_SETTINGS['debug'] == '1'){
							?>
							<p id="debugbar">
								[IP : <?=$_SERVER['REMOTE_ADDR'] ?>] 
								[ADMIN ID : <?=$_SESSION["session"]->admin->userid ?>] 
								<? if($_SESSION["session"]->admin->accesslevel == '0'){$superadmin = 'Yes'; } else { $superadmin = 'No'; } ?>
								[SUPER ADMIN : <?=$superadmin?>]
							</p>					
							<?
						}
						?>
						
					</div>
					<br clear="all" />
						
						<?
						$z = 0;
						$zz = 1;
						$z1 = 1;
						$z2 = 25;
						$z3 = 50;
						$rowloop = 5;
						$loop = count($_ADMIN)/$rowloop;
						
						//echo "rowloop: $rowloop <Br>";
						//echo "loop: $loop <Br><Br><br><br>";
						
						?>
						
				</div>
				
				<div class='navtop'>
					<div id="tabsB" style="clear:both;">
					  <ul>				
						<li><a href="<?=$_SERVER["PHP_SELF"]?>" title="Home" class="Home <? if($_GET['VIEW']==""){ ?> active <? }?>"><span>Home</span></a></li>
						<?		
						//
						// TOP NAVIGATION TABS
						//
						foreach ($_ADMIN as $adminer)
						{
							// IF ACTIVE AND ACCESSIBLE
							if($adminer["4"] == 1 and $_SESSION["session"]->admin->CheckAccessLevelNavigation($adminer))
							{
								?>
								<li>
								<?
								if($adminer["7"] != ""){
									$href = "href=\"".$adminer['7']."\" target=\"_blank\"";
								}else{
									$href = "href=\"".$_SERVER["PHP_SELF"]."?VIEW=".$adminer["2"]."&\" target=\"\"";
								}
								?>
								<a class="<?=str_replace(" ","",$adminer["0"])?> <? if($_GET['VIEW']==$adminer["2"]){ ?> active <? }?>" id="" rel="<?=$adminer["8"]?>" <?=$href?> title="<?=$adminer["0"]?>">
								<span><?=$adminer["0"]?></span>
								</a>
								</li>
								<?
								//if($z == 4){ echo "<br clear='all'>"; } 
								
								/*
								if($zz == $rowloop){
									?>
									</span><span style="display:block; margin:0 0 0 0px;">
									<?
									//$zz = 0;
									$z1++;
								}								
								*/
								
								$zz++;
								$z++;
								
							}
						}					
						?>
						</span>
					  </ul>
					</div>	
				</div>			
				
			<div style='width:100%; margin:0px auto;'>	
				
				<div class="col2 toolbarOut" style="border-right:1px solid #ccc;">
					<?							
					if( $section != '' ){
						//
						// INCLUDE MODULE LEFT NAVIGATION IF ACCESS LEVEL PERMITS
						//
						?><ul><?
						foreach ($_ADMIN as $adminer)
						{
							if ($_GET['VIEW']==$adminer["2"]) {
								$_SESSION["session"]->admin->CheckAccessLevel($adminer);
								include($adminer["5"]);
								$inc = 1;
							}
						}		
						?></ul><?
					} else {
						//
						// ELSE INCLUDE HOME PAGE LEFT NAVIGATION
						//
						?><ul><?
						foreach ($_ADMIN as $adminer)
						{
							if($adminer["4"] == 1 and $_SESSION["session"]->admin->CheckAccessLevelNavigation($adminer) == true)
							{
								?>
								<li>
								<?
								if($adminer["7"] != ""){
									$href = "href=\"".$adminer['7']."\" target=\"_blank\"";
								}else{
									$href = "href=\"".$_SERVER["PHP_SELF"]."?VIEW=".$adminer["2"]."&\" target=\"\"";
								}
								?>
								<a class="<?=str_replace(" ","",$adminer["0"])?> <? if($_GET['VIEW']==$adminer["2"]){ ?> active <? }?>" rel="<?=$adminer["8"]?>" <?=$href?> title="<?=$adminer["0"]?>">
							
								<?=$adminer["0"]?>
								</a>
								</li>
								<?
							}
						}	
						?></ul><?	
					}
					?>					
				</div>
								
				<!-- <div class="colmask leftmenu">	-->					
					
					<div class="col1">
						<?
							
							/* TESTING
							$mtime = microtime();
							$mtime = explode(" ",$mtime);
							$mtime = $mtime[1] + $mtime[0];
							$endtime = $mtime;
							$totaltime = ($endtime - $starttime);
							echo "This page was created in ".$totaltime." seconds";
							die();
							exit();
							*/
							
						/*** CURRENT MODULE IF ACCESS LEVEL PERMITS ***/
						if( $section != '' )
						{
							foreach ($_ADMIN as $adminer)
							{
								if($_GET['VIEW'] == $adminer["2"])
								{
									$_SESSION["session"]->admin->CheckAccessLevel($adminer);
									echo "<div class='maincontent'>";
									include($adminer["3"]);
									echo "</div>";
									$inc = 1;
								}
							}
						}
						/*** ELSE HOME PAGE BUTTONS ***/
						else
						{
						?>
							
							<?
							if($_SETTINGS['wes_graphic'] == '1'){
							?>
							<style>
							.col1{ background-image:url(images/wesbg.png); }
							</style>
							<?
							} else {
							?>
							<style>
							.col1{ background-image:; }
							</style>
							<?
							}
							?>
							
							<div class="maincontent">	
								
								
								
								<div class="textcontent" style='border-bottom:1px solid #ccc;'><h1>Main Menu</h1></div>
								<? report($_REQUEST['REPORT'],$_REQUEST['SUCCESS']); ?>
								<ul id='mainul'>
								
									<?
									/*** HOME PAGE BUTTONS ***/
									$i=1;
									foreach ($_ADMIN as $adminer)
									{
										if($adminer["4"] == 1 and $_SESSION["session"]->admin->CheckAccessLevelNavigation($adminer) == true)
										{
										?>
											<li>
											
											<?
											if($adminer["7"] != ""){
												$href = "href=\"".$adminer['7']."\" target=\"_blank\"";
											}else{
												$href = "href=\"".$_SERVER["PHP_SELF"]."?VIEW=".$adminer["2"]."&\" target=\"\"";
											}
											?>
											<a class="<?=str_replace(" ","",$adminer["0"])?> <? if($_GET['VIEW']==$adminer["2"]){ ?> active <? }?>" <?=$href?> title="<?=$adminer["0"]?>" rel="<?=$adminer["8"]?>">
											<img src="<?=$adminer["6"]?>" border="0" />
											<span><?=$adminer["0"]?></span>
											</a>
											</li>
											<?													
											$i++;
										}
									}					
									?>
									<br clear='all'>
								</ul>
							
								
							</div>
						<?
						}
						?>
						<?
						ob_end_flush();
						ob_flush();
						?>
						<!-- <br clear="all" /> -->
					</div>			
			</div>
			
					<!-- </div> -->
				<!-- </div> -->
				<div id="footer">
					<table border=0 cellpadding=0 cellspacing=0 style="margin:0px auto;">
					<tr>
						<td class="copyimg">
							<a href="http://www.karlsdevelopment.com" style="border:0px;"><img src="images/weslogo.png" align="left" width="50" border="0"></a>
						</td>
						<td class="copytext">
							<small>v<?=$_SETTINGS['version']?> &copy; 2009 - <?=date("Y")?></small>
							<Br>
						</td>
					</tr>
					</table>
				</div>
			</div>
		</div>	
	</body>
</html>
<?php
if($_SETTINGS["show_page_time"] == '1')
{
   $mtime = microtime();
   $mtime = explode(" ",$mtime);
   $mtime = $mtime[1] + $mtime[0];
   $endtime = $mtime;
   $totaltime = ($endtime - $starttime);
   //echo "This page was created in ".$totaltime." seconds";
}
?>