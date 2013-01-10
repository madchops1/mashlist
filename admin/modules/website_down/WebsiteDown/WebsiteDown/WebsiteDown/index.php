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
error_reporting(E_ALL);
include_once '../../../../../../includes/config.php';

if($_SESSION['session']->admin->accesslevel != ""){
	header("Location: ".$_SETTINGS['website']."");
	exit;
}

?>
<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<!--**********************************************************************************************************************************
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
	* 	wes Version <?=$_SETTINGS['version']?> Copyright 2009-<?=date("Y")?> Karl Steltenpohl Development LLC. All Rights Reserved.
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
	***********************************************************************************************************************************-->
	<meta charset="UTF-8">
	<title><?=$_SETTINGS['site_name']?></title>
	<link rel="stylesheet" type="text/css" href="style/css/reset.css" />
	
	<?
	/**
	 *
	 * WEBSITE DOWN STYLE COLORS
	 *
	 */
	?>
	<link rel="stylesheet" type="text/css" href="style/css/<?=strtolower(lookupDbValue('website_down_style_colors', 'name', $_SETTINGS['website_down_page_color'], 'color_id'))?>.css" />
	
	<!--[if IE 8]>
	<link rel="stylesheet" type="text/css" href="style/css/ie8.css" />
	<![endif]-->
	
	<!--[if lte IE 7]>
	<link rel="stylesheet" type="text/css" href="style/css/ie.css" />
	<![endif]-->
	
	<?
	/**
	 *
	 * JS
	 *
	 */
	?>
	<script type="text/javascript" src="style/js/jquery-1.4.min.js"></script>
	<script type="text/javascript" src="style/js/jquery.validationEngine.js"></script>
	<script type="text/javascript" src="style/js/jquery.jcarousel.pack.js"></script>
	<script type="text/javascript" src="style/js/jquery.countdown.min.js"></script>
	<script type="text/javascript" src="style/js/cufon.yui.js"></script>
	<script type="text/javascript" src="style/js/kievit.js"></script>
	<script type="text/javascript" src="style/js/twitter.min.js"></script>
	<script type="text/javascript" src="style/js/custom.js"></script>
	<script type="text/javascript">
			Cufon.replace('h2, p');
	
		 
			//
			// COUNTDOWN
			//
			j(function () {

				//var newYear = new Date(); 
				//newYear = new Date(2010, 06 - 1, 12);
				newYear = new Date('<?=$_SETTINGS['web_down_time']?>'); 
				j('.defaultCountdown').countdown({until: newYear}); 
	  
			});  
			
			//
			// TWITTER
			//
			<? if($_SETTINGS['twitter_id'] != ''){ ?>
				getTwitters('twitter', {
					id: '<?=$_SETTINGS['twitter_id']?>', 
					count: 1, 
					enableLinks: true, 
					ignoreReplies: false,
					template: '<span class="twitterPrefix"><span class="twitterStatus">%text%</span> <em class="twitterTime"><a href="http://twitter.com/%user_screen_name%/statuses/%id%">- %time%</a></em>',
					newwindow: true
				});
			<? } ?>
			
	</script>
	
	
	
</head>
<body>
<div id="wrapper">
	<div id="logo"> 
		<a href="#">
			<img src="../../../../../images/weslogo.png" />
			<!-- <img src="style/images/logo.png" alt="Company Name" /> -->
		</a>
	</div><!--end-logo--> 
  	<div id="page-container">
  		<ul id="page" class="jcarousel-skin-tango">
    		<li> 
    			<span class="content">
      				<h2><?=$_SETTINGS['site_name']?></h2>
      				
      				<p><?=$_SETTINGS['website_down_message']?></p>
					
					<p>Estimated time till we're back:</p>
      				
      				<span class="defaultCountdown"></span><!--end-countdown--> 
      				
					<?
					/*
      				<form action="" method="post" id="form-email">
                        <input class="disappear" type="text" name="Newsletter" id="Newsletter" value="Enter your email to subscribe"/>
                        <input type="submit" name="submit" class="submit" value="Submit" />

               		 </form><!--end-newsletter-form--> 
					*/
					?>
      			</span> 
      			<? /*<a class="control" href="#" onclick="jq.validationEngine.closePrompt('.formError',true)">2</a><!--end-first-arrow--> */ ?>
   			</li>
    
			<?
			/*
    		<li> 
    			<span class="content">
    				<span class="contact">
     					 <form action="" method="post" id="info">
        						<span id="name-wrap" class="slider">
        							<label for="name">Name</label>
        							<input type="text" id="Name" name="Name" class="validate[required] text-input">
       						  	</span>
        						<!--/#name-wrap-->
        						<span id="email-wrap"  class="slider">
        							<label for="email">E&ndash;mail</label>
        							<input class="validate[custom[email]] text-input" type="text" id="Email" name="Email">
        						</span>
       							<!--/#email-wrap-->
        						<span id="comment-wrap"  class="slider">
       								<label for="comment">Comment</label>
        							<textarea class="validate[length[6,300]] text-input" cols="53" rows="10" name="Message" id="Message"></textarea>
       							</span>
        						<!--/#comment-wrap-->
        						<input type="submit" id="btn" name="btn" value="">
      					 </form>
      				</span><!--end-contact-form--> 
      				<span class="social">
      					<a href="<?=$_SETTINGS['twitter_link']?>" title="Follow me on Twitter" alt="Twitter">
						<img src="style/images/bird.png" alt="Twitter" />
						</a><!--twitter-bird-icon--> 
      					<span class="message" id="twitter"></span><!--end-twitter-message--> 
     					<ul class="links">
							<li><a href="#"><img src="style/images/icon-fb.png" alt="Facebook" title="Facebook" /></a></li>
							<li><a href="#"><img src="style/images/icon-fm.png" alt="Last.fm" title="Last.fm" /></a></li>
							<li><a href="#"><img src="style/images/icon-tu.png" alt="Tumblr" title="Tumblr" /></a></li>
							<li><a href="#"><img src="style/images/icon-su.png" alt="StumbleUpon" title="StumbleUpon" /></a></li>
							<li><a href="#"><img src="style/images/icon-ff.png" alt="FriendFeed" title="FriendFeed" /></a></li>
							<li><a href="#"><img src="style/images/icon-fl.png" alt="Flickr" title="Flickr" /></a></li>
							<li><a href="#"><img src="style/images/icon-del.png" alt="Delicious" title="Delicious" /></a></li>
						</ul><!--end-social-links--> 
      				</span> 
      			</span>
      			<a class="control2" href="#" onclick="jq.validationEngine.closePrompt('.formError',true)">1</a><!--end-second-arrow--> 
      		</li>
			*/
			?>
			
			
 		 </ul>
  		<div class="shadow"></div>
	</div><!--end-page-container-->
</div><!--end-wrapper-->
</body>
</html>
