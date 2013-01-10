<?
/******************************************
*
*  MashList Theme INDEX
*
******************************************/
$CMS = new CMS();
$UserAccounts = new UserAccounts();
global $_SETTINGS;


/**
 * BROWSER DETECTION
 */
$msie          = strpos($_SERVER["HTTP_USER_AGENT"], 'MSIE') ? true : false; 
$firefox       = strpos($_SERVER["HTTP_USER_AGENT"], 'Firefox') ? true : false;
$safari        = strpos($_SERVER["HTTP_USER_AGENT"], 'Safari') ? true : false;
$chrome        = strpos($_SERVER["HTTP_USER_AGENT"], 'Chrome') ? true : false;

/**
 * GET USER PLAY TOKEN FROM 8-TRACKS
 */

/*
curl http://8tracks.com/sets/new.xml

<response>
  <notices nil="true"></notices>
  <logged-in type="boolean">false</logged-in>
  <status>200 OK</status>
  <errors nil="true"></errors>
  <play-token>726890873</play-token>
</response>
*/

/*
$curlURL = "http://8tracks.com/sets/new.xml?api_key=70c700077d76945a3287c6bdc5c2b4f2dd76550d";
$c = curl_init();
curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($c, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json'));
curl_setopt($c, CURLOPT_URL, $curlURL . "");
//curl_setopt($c, CURLOPT_POSTFIELDS, json_encode($a_data));

$curlReturn = curl_exec($c);
curl_close($c);

echo "<div><pre>";
var_dump($curlReturn);
echo "</pre></div><br clear='all'/>";

*/

// 3087024 //draper




/*
//for example with the task/list/ the 'result' when var_dumped is just 'bool(true)'.
$s_togglUrl = "http://8tracks.com/mixes.xml?api_key=70c700077d76945a3287c6bdc5c2b4f2dd76550d";
$c = curl_init();
curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($c, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json'));
curl_setopt($c, CURLOPT_URL, $s_togglUrl . "");
//curl_setopt($c, CURLOPT_POSTFIELDS, json_encode($a_data));

$s_returnVal = curl_exec($c);
curl_close($c);

//echo "<pre>";
//var_dump($s_returnVal);
//echo "</pre>";
*/

/**
 * AJAX ACTIONS 
 **/

//die('made it');
	 
 
if($_POST['ajax'] AND $_POST['action']){
	//die('made it');
			
	switch($_POST['action']){
		case "curl":
			
			//die('made it');
			//exit();
			
			$url = $_POST['curlurl'];
			
			//$curlURL = "http://8tracks.com/sets/new.xml?api_key=70c700077d76945a3287c6bdc5c2b4f2dd76550d";
			$curlURL = $url;
			$c = curl_init();
			curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($c, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json'));
			curl_setopt($c, CURLOPT_URL, $curlURL . "&format=json&api_key=70c700077d76945a3287c6bdc5c2b4f2dd76550d");
			
			//curl_setopt($c, CURLOPT_POSTFIELDS, json_encode($a_data));
			
			$curlReturn = curl_exec($c);
			curl_close($c);
			echo $curlReturn;
			
			//echo "<br><div><pre>";
			//var_dump($curlReturn);
			//echo "</pre></div><br clear='all'/>";
			
			die();
			exit();
			break;
		
		case "login":
			if($_POST['username'] != "" AND $_POST['password'] != "")
			{
				$array = $UserAccounts->LoginFormAction();
				
				if($array != "")
				{
					//echo "<pre>";
					//var_dump($array);
					//echo "</pre>";
					echo "fail";
				} 
				//SUCCESS
				else 
				{
					echo "success";
				}			
			}
			die();
			exit();
			break;
			
		case "register":
			if(	$_POST['name'] != "" AND 
				$_POST['email'] != "" AND 
				$_POST['username'] != "" AND
				$_POST['password1'] != "" AND
				$_POST['password2'] != "" AND
				$_POST['terms'] != "")
			{
				$array = $UserAccounts->RegistrationFormAction();		
				
				echo $array[1];
				
				//echo "<pre>";
				//var_dump($array);
				//echo "</pre>";
				
				//if(is_array($array)){
				//	
				//} else {
				//	echo $array;
				//}
				
			}
			
			//echo "success register";
			die();
			exit();
			break;
			
		default:
			echo "";
			
			die();
			exit();
		
	}
	//die('made it zed');
	
}

?>

<!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->

<head>
  	<meta charset="utf-8">

  	<!-- Page title -->
  	<title><?=$CMS->PageTitle(); ?></title>
  	<meta name="author" content="Karl Steltenpohl">
	<meta name="robots" content="index, follow" />
	<meta name="description" content="<?=$CMS->PageDescription(); ?>" />
	<meta name="keywords" content="<?=$CMS->PageKeywords(); ?>" />


	<!-- jQuery -->
	<script src="<?=$_SETTINGS['website'] ?>themes/mashlist/js/libs/jquery-1.6.2.min.js"></script>
	<!-- jQuery cookies.js - super handy, Thanks! -->
	<script src="<?=$_SETTINGS['website'] ?>themes/mashlist/js/jquery.cookies.2.2.0.min.js"></script>
	<!-- jPlayer -->
	<script src="<?=$_SETTINGS['website'] ?>themes/mashlist/js/jquery.jplayer.2.1.0/jquery.jplayer.min.js"></script>
	<link type="text/css" href="<?=$_SETTINGS['website'] ?>themes/mashlist/js/jquery.jplayer.2.1.0/blue.monday/jplayer.blue.monday.css" rel="stylesheet" />
	<!-- <script>window.jQuery || document.write('<script src="js/libs/jquery-1.6.2.min.js"><\/script>')</script> -->
	<script src="<?=$_SETTINGS['website'] ?>themes/mashlist/js/jquery.flip.min.js"></script>
	<link rel="stylesheet" href="<?=$_SETTINGS['website'] ?>themes/mashlist/js/colorbox/example1/colorbox.css" />
	<script src="<?=$_SETTINGS['website'] ?>themes/mashlist/js/colorbox/colorbox/jquery.colorbox.js"></script>
		

	


	<!-- soundManager.useFlashBlock: related CSS -->
	<link rel="stylesheet" type="text/css" href="<?=$_SETTINGS['website'] ?>themes/mashlist/js/soundmanager/demo/flashblock/flashblock.css" />
	
	<!-- required -->
	<link rel="stylesheet" type="text/css" href="<?=$_SETTINGS['website'] ?>themes/mashlist/js/soundmanager/demo/360-player/360player.css" />
	<link rel="stylesheet" type="text/css" href="<?=$_SETTINGS['website'] ?>themes/mashlist/js/soundmanager/demo/360-player/360player-visualization.css" />
	
	<!-- special IE-only canvas fix -->
	<!--[if IE]><script type="text/javascript" src="script/excanvas.js"></script><![endif]-->
	
	<!-- Apache-licensed animation library -->
	<script type="text/javascript" src="<?=$_SETTINGS['website'] ?>themes/mashlist/js/soundmanager/demo/360-player/script/berniecode-animator.js"></script>
	
	<!-- the core stuff -->
	<script type="text/javascript" src="<?=$_SETTINGS['website'] ?>themes/mashlist/js/soundmanager/script/soundmanager2.js"></script>
	<script type="text/javascript" src="<?=$_SETTINGS['website'] ?>themes/mashlist/js/soundmanager/demo/360-player/script/360player.js"></script>
	
	<script type="text/javascript">
	
	soundManager.url = 'themes/mashlist/js/soundmanager/swf/'; // path to directory containing SM2 SWF
	
	soundManager.useFastPolling = true; // increased JS callback frequency, combined with useHighPerformance = true
	soundManager.debugMode = false;
	
	threeSixtyPlayer.config.scaleFont = (navigator.userAgent.match(/msie/i)?false:true);
	threeSixtyPlayer.config.showHMSTime = true;
	
	// enable some spectrum stuffs
	
	threeSixtyPlayer.config.useWaveformData = true;
	threeSixtyPlayer.config.useEQData = true;
	
	// enable this in SM2 as well, as needed
	
	if (threeSixtyPlayer.config.useWaveformData) {
	  soundManager.flash9Options.useWaveformData = true;
	}
	if (threeSixtyPlayer.config.useEQData) {
	  soundManager.flash9Options.useEQData = true;
	}
	if (threeSixtyPlayer.config.usePeakData) {
	  soundManager.flash9Options.usePeakData = true;
	}
	
	if (threeSixtyPlayer.config.useWaveformData || threeSixtyPlayer.flash9Options.useEQData || threeSixtyPlayer.flash9Options.usePeakData) {
	  // even if HTML5 supports MP3, prefer flash so the visualization features can be used.
	  soundManager.preferFlash = true;
	}
	
	// favicon is expensive CPU-wise, but can be enabled.
	threeSixtyPlayer.config.useFavIcon = false;
	//threeSixtyPlayer.config.autoPlay = true;
	
	
	
	</script>
	
		
	
  
  

  
  
  
  
  
  
  <!-- CSS -->
  <link rel="stylesheet" href="<?=$_SETTINGS['website'] ?>themes/mashlist/css/style.css">

  <!-- Favicon -->
  <link rel="shortcut icon" href="<?=$_SETTINGS['website'] ?>themes/mashlist/favicon.png">

  <!-- Scale -->
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  
  <!-- Modernizr -->
  <script src="<?=$_SETTINGS['website'] ?>themes/mashlist/js/libs/modernizr-2.0.6.min.js"></script>
	
	
	
  
  
  
  
  
  <!-- Scripts -->
  <script src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
  <script defer src="<?=$_SETTINGS['website'] ?>themes/mashlist/js/libs/jquery.fancybox-1.3.4.pack.js"></script>
  <script defer src="<?=$_SETTINGS['website'] ?>themes/mashlist/js/libs/jquery.masonry.min.js"></script>
  <script defer src="<?=$_SETTINGS['website'] ?>themes/mashlist/js/swfobject/swfobject.js"></script>
  <!-- 
  	<script defer src="<?=$_SETTINGS['website'] ?>themes/mashlist/js/script.js"></script>
  -->	

	
	<script>
		$(document).ready(function() {
  			// Handler for .ready() called.
			 
			 $.fn.onTypeFinished = function(func) {
			     var T = undefined, S = 0, D = 1000;
			     $(this).bind("keypress", onKeyPress).bind("focusout", onTimeOut);
			     function onKeyPress() {
			        clearTimeout(T);
			        if (S == 0) { S = new Date().getTime(); D = 1000; T = setTimeout(onTimeOut, 1000); return; }
			        var t = new Date().getTime();
			        D = (D + (t - S)) / 2; S = t; T = setTimeout(onTimeOut, D * 2);
			     }
			 
			      function onTimeOut() {
			           func.apply(); S = 0;
			      }
			      return this;
			   };
			 
			 
			 
			 
			 
			var tracksOn = 1;
			//http://8tracks.com/sets/460486803/play.xml?mix_id=2000  
			//http://8tracks.com/sets/new.xml?api_key=70c700077d76945a3287c6bdc5c2b4f2dd76550d			
			// GET USER TOKEN VIA AJAX
			if(tracksOn == 1){
				$.ajax({
				  url: "index.php",
				  context: document.body,
				  type: "POST",
				  data: {'ajax': '1', 'action':'curl', 'curlurl':'http://8tracks.com/sets/new?'},
				  success: function(response1){
				  	
				    //$(this).addClass("done");
				    //alert('draper loaded!');
				    //alert(response1);
				    var userPlayTokenObj = $.parseJSON(response1);
				    
				    
				    if($.cookies.get('userPlayToken')){
				    	token = $.cookies.get('userPlayToken');
					} else {
						$.cookies.set( 'userPlayToken',userPlayTokenObj.play_token ); //A cookie by the name 'sessid' now exists with the value 'dh3tr62fghe'
						token = $.cookies.get('userPlayToken');
					}
				  
					
				    
				    
					  	 
				    //setcookie with = userPlayTokenObj.play_token;
					//alert(userPlayTokenObj.play_token);
				    // call draper playlist (default playlist)
				    
				    // AJAX GET PLAYLIST
				    $.ajax({
				  	  url: "index.php",
					  context: document.body,
					  type: "POST",
					  /*data: {'ajax': '1', 'action':'curl', 'curlurl':'http://8tracks.com/sets/'+userPlayTokenObj.play_token+'/play?mix_id=412488'},*/
					  data: {'ajax': '1', 'action':'curl', 'curlurl':'http://8tracks.com/mixes?q=Music+Don+Draper+Would+Study+To'},
					  success: function(response2){
					  	
					  	 var defaultSetA = $.parseJSON(response2);
					  	 //console.log(defaultSetA);
					  	 var playlistName = defaultSetA.mixes[0].name;
					  	 var playlistImg = defaultSetA.mixes[0].cover_urls.sq250;
					  	 var playlistDesc = defaultSetA.mixes[0].description;
					  	 var playlistBg = defaultSetA.mixes[0].cover_urls.max1024;
					  	 var playlistId = defaultSetA.mixes[0].id;
					  	 
					  	 // SET MIX ID
					  	 $.cookies.set( 'mixId',playlistId ); //A cookie by the name 'sessid' now exists with the value 'dh3tr62fghe'
						 //var playlistDescription = mixes.description;
					  	 //var coverUrls = mixes[1];
					  	 //var coverUrl = mixes.cover_urls.sq250;
					  	 $("#playlist-title").html(playlistName);
					  	 $("#playlist-image").attr("src",playlistImg);
					  	 $("#playlist-description-scroll").html(playlistDesc);
					  	 $("#slideshow").css("background-image","url("+playlistBg+")");
					  	 
					  	 //console.log(playlistName);
					  	 //console.log(playlistImg);
					  	 //console.log(playlistDesc);
					  	 //console.log(playlistBg);
					  	 //console.log(playlistDescription);
					  	 //console.log(coverUrls);
					  	
					  	 // AJAX GET SONG
					  	 $.ajax({
					  	   url: "index.php",
						   context: document.body,
						   type: "POST",
						   data: {'ajax': '1', 'action':'curl', 'curlurl':'http://8tracks.com/sets/'+token+'/play?mix_id=412488'},
						   /*data: {'ajax': '1', 'action':'curl', 'curlurl':'http://8tracks.com/mixes?q=Music+Don+Draper+Would+Study+To'},*/
						   success: function(response3){
						  	
							  	var defaultSet = $.parseJSON(response3);
							  	//console.log(defaultSet);
							  	
							  	var defaultSongURL = defaultSet.set.track.url;
							  	var defaultSongName = defaultSet.set.track.name;
							  	var defaultTrackId = defaultSet.set.track.id;
							  	
							  	// SET TRACK ID
							  	$.cookies.set( 'trackId',defaultTrackId ); //A cookie by the name 'sessid' now exists with the value 'dh3tr62fghe'

							  	
							  	console.log("URL: " + defaultSongName + " " + defaultSongURL);
							  	//alert(defaultSongURL);
							  	$("#song-link").attr("href",defaultSongURL);
							  	$("#song-link").html(defaultSongName);
							  	
							  	//soundManager.play('ui360Sound0',{ volume:100 });
							  	//play
							  	
							  	console.log('play');
							  	
							  	
							  	
							  	// DEFAULT SOUND FX
							  	$("#jquery_jplayer_1").jPlayer({
							        ready: function () {
							          $(this).jPlayer("setMedia", {
							            mp3: "http://labs.sprkk.com/mashlist/uploads-backgrounds/16153__reinsamba__thunderstorm1.mp3"
							          }).jPlayer("play");
									  $(".sm2-360btn").trigger('click');
							  	
							        },
							        swfPath: "themes/mashlist/js/jquery.jplayer.2.1.0",
							        supplied: "mp3",
							        volume: 0.4
							      });
							  	
							  	// ///$("#jquery_jplayer_1").jPlayer("play");
							  	//alert('success');
							    //$(this).addClass("done");
							    //alert('draper loaded!');
							    //alert(response);
							    
							    // call draper playlist (default playlist)
							    $("#loading-overlay").delay(1000).fadeOut(500);
							   
							}
						}); // ajax get song
						
						
						// GET DEFAULT PLAYLISTS
						$.ajax({
					  	  url: "index.php",
						  context: document.body,
						  type: "POST",
						  //http://8tracks.com/mixes.xml?tags=chill%2Bhip+hop&sort=recent
						  /*data: {'ajax': '1', 'action':'curl', 'curlurl':'http://8tracks.com/sets/'+userPlayTokenObj.play_token+'/play?mix_id=412488'},*/
						  data: {'ajax': '1', 'action':'curl', 'curlurl':'http://8tracks.com/mixes?chill%2Bcovers%2Bstudy&sort=popular'},
						  success: function(response6){
						  	 var set6 = $.parseJSON(response6);
						  	 console.log('DEFAULT PLAYLISTS');
						  	 console.log(set6);
						  	 
						  	 for (var i = 0; i < set6.mixes.length; i++){
						  	 	mix = set6.mixes[i];
						  	 	//console.log(mix.name);
						  	 	
						  	 	name = mix.name;
						  	 	description = mix.description;
						  	 	image = mix.cover_urls.sq56;
						  	 	//id = mix.cover_urls.id
						  	 	
						  	 	listItem = "<li><a class='playlist-result' ref='"+name+"' id='' title='"+description+"'><img src='"+image+"'/> "+name+"</a></li>";
						  	 	$("#playlist-search-list").append(listItem);
						  	 }

						  	 //var playlistName = defaultSetA.mixes[0].name;
						  	 //var playlistImg = defaultSetA.mixes[0].cover_urls.sq250;
						  	 //var playlistDesc = defaultSetA.mixes[0].description;
						  	 //var playlistBg = defaultSetA.mixes[0].cover_urls.max1024;
						  	 //var playlistId = defaultSetA.mixes[0].id;
						  	 
						  }
						});
						  	
						
						
					  }
					}); // ajax get playlist
				  }
				}); // ajax get token
			} // tracksOn
			else {
				 $("#loading-overlay").fadeOut(1000);
			}
			
			
			
			/**
			 * SEARCHING
			 * 
			 */
			$("#playlist-keywords").focus(function(){
				$(this).val('');
			});
			$("#playlist-keywords").blur(function(){
				if($(this).val() == ''){
					$(this).val('Search Playlists');
				}
			})
			$("#playlist-keywords").onTypeFinished(function(){
				console.log('dun typin');
				keywords = "";
				keywords = $("#playlist-keywords").val();
				keywords = keywords.replace(" ", "+"); 
				$("#playlist-search-scroll").css('opacity','0.6');
				
				// search for playlists
				$.ajax({
				  	  url: "index.php",
					  context: document.body,
					  type: "POST",
					  data: {'ajax': '1', 'action':'curl', 'curlurl':'http://8tracks.com/mixes?q='+keywords+''},
					  success: function(response7){
					  	var set7 = $.parseJSON(response7);
					  	console.log('SEARCH RESULTS:');
					  	console.log(set7);
					  	$("#playlist-search-list").html('');
					  	
					  	for (var i = 0; i < set7.mixes.length; i++){
					  	 	mix = set7.mixes[i];
					  	 	//console.log(mix.name);
					  	 	
					  	 	name = mix.name;
					  	 	description = mix.description;
					  	 	image = mix.cover_urls.sq56;
					  	 	id = mix.cover_urls.id
					  	 	
					  	 	listItem = "<li><a class='playlist-result' ref='"+name+"' id='' title='"+description+"'><img src='"+image+"'/> "+name+"</a></li>";
					  	 	$("#playlist-search-list").append(listItem);
					  	 }
					  	
					  	
					  	$("#playlist-search-scroll").css('opacity','1.0');
					  	
					  	
					  	
					  }
				});
			});
			
			/**
			 * CLICK A PLAYLIST RESULT / CLICKING NEW PLAYLIST
			 */
			$(".playlist-result").live('click',function(){
				//console.log('change playlist');
				
				newPlaylist = $(this).attr('ref');
				newPlaylist = encodeURIComponent(newPlaylist); 
				//newPlaylist = newPlaylist.replace(" ","+");
				console.log(newPlaylist);
				
				// GET THE PLAYLIST
				$.ajax({
				  	  url: "index.php",
					  context: document.body,
					  type: "POST",
					  data: {'ajax': '1', 'action':'curl', 'curlurl':'http://8tracks.com/mixes?q='+newPlaylist+''},
					  success: function(response8){
					  	
					  	 var set8 = $.parseJSON(response8);
					  	 //console.log(defaultSetA);
					  	 var playlistName = set8.mixes[0].name;
					  	 var playlistImg = set8.mixes[0].cover_urls.sq250;
					  	 var playlistDesc = set8.mixes[0].description;
					  	 var playlistBg = set8.mixes[0].cover_urls.max1024;
					  	 var playlistId = set8.mixes[0].id;
					  	 
					  	 // SET MIX ID
					  	 $.cookies.set( 'mixId',playlistId ); //A cookie by the name 'sessid' now exists with the value 'dh3tr62fghe'
						 //var playlistDescription = mixes.description;
					  	 //var coverUrls = mixes[1];
					  	 //var coverUrl = mixes.cover_urls.sq250;
					  	 $("#playlist-title").html(playlistName);
					  	 $("#playlist-image").attr("src",playlistImg);
					  	 $("#playlist-description-scroll").html(playlistDesc);
					  	 $("#slideshow").css("background-image","url("+playlistBg+")");
					  	 
					  	 $("#current-playlist").delay(200).fadeIn(200);
						 $("#playlist-results").fadeOut(200);
						 
						 // get song
						 // AJAX GET SONG
					  	 $.ajax({
					  	   url: "index.php",
						   context: document.body,
						   type: "POST",
						   data: {'ajax': '1', 'action':'curl', 'curlurl':'http://8tracks.com/sets/'+token+'/play?mix_id='+playlistId+''},
						   /*data: {'ajax': '1', 'action':'curl', 'curlurl':'http://8tracks.com/mixes?q=Music+Don+Draper+Would+Study+To'},*/
						   success: function(response9){
						  	
							  	var set9 = $.parseJSON(response9);
							  	//console.log(defaultSet);
							  	
							  	var SongURL = set9.set.track.url;
							  	var SongName = set9.set.track.name;
							  	var TrackId = set9.set.track.id;
							  	
							  	// SET TRACK ID
							  	$.cookies.set( 'trackId',TrackId ); //A cookie by the name 'sessid' now exists with the value 'dh3tr62fghe'

							  	
							  	console.log("URL: " + SongName + " " + SongURL);
							  	//alert(defaultSongURL);
							  	$("#song-link").attr("href",SongURL);
							  	$("#song-link").html(SongName);
							  	
							  	//soundManager.play('ui360Sound0',{ volume:100 });
							  	//play
							  	
							  	console.log('play');
							  	
							  	
							  	$(".sm2-360btn").delay(300).trigger('click');
							  	
							  	
							  	
							  	
							} // success
						}); // ajax get song
					  	
					  }
				});// get the playlist	 	  
			});
			
			/**
			 * SEARCH MASHLISTS
			 */
			
			// TODO...
			
			/**
			 * CLICK SEARCH
			 */
			$("#search").click(function(e){
				console.log('clicked');
				e.preventDefault();
				$("#current-playlist").fadeOut(200);
				$("#playlist-results").delay(200).fadeIn(200);
				/*
				$("#playlists").flip({
					direction:'rl',
					color:'blue'
				});
				*/
			});
			
			/**
			 * CLICK NOW PLAYING
			 */
			$("#now-playing").click(function(e){
				e.preventDefault();
				$("#current-playlist").delay(200).fadeIn(200);
				$("#playlist-results").fadeOut(200);
			});
			
			
			//$(".welcome-login a").attr('href','#login-modal');
			//$(".welcome-register a").attr('href','#register-modal');
			
			
			
			$(".how-it-works").colorbox({inline:true, width:"50%"});
			$(".my-mashlists").colorbox({inline:true, width:"50%"});
			$(".search-mashlists").colorbox({inline:true, width:"50%"});
			$(".welcome-login a").colorbox({inline:true, width:"50%", href:"#login-modal"});
			$(".welcome-register a").colorbox({inline:true, width:"50%", href:"#register-modal"});
			$("#register-link").colorbox({inline:true, width:"50%", href:"#register-modal"});
			$("#forgot-link").colorbox({inline:true, width:"50%", href:"#forgot-modal"});
			
			/**
			 * LOGIN
			 */
			$("#login-modal input[type='submit']").click(function(e){
				e.preventDefault();
				console.log('clicked login');
				
				var email = $("input#email").val();
				var password = $("input#password").val();
				
				// ajax part of login
				// DONT WORRY IF YOUR THINKING ABOUT TRYING SOMETHING
				// HERES A HINT THIS IS LAYER ONE...
				if(email != "" && password != ""){
					$.ajax({
				  	   url: "index.php",
					   context: document.body,
					   type: "POST",
					   data: {'ajax': '1', 'action':'login', 'LOGIN':'1', 'username':email, 'password':password},
					   success: function(response10){
					   		console.log(response10);
					   		if(response10 == "success"){
					   			console.log('logged in');
					   		}
					   		// error
					   		else {
					   			alert(response10);
					   		}
					   		
					   }
					});
				} 
				// error
				else {
					alert('Enter your username/email and password.');
				}
			});
			
			/**
			 * REGISTER
			 */
			$("input[name='REGISTER']").click(function(e){
				e.preventDefault();
				console.log('clicked register');
				
				var name = $("input[name='name']").val();
				var email = $("input[name='email']").val();
				var username = $("input[name='username']").val();
				var password1 = $("input[name='password1']").val();
				var password2 = $("input[name='password2']").val();
				var emails = $("input[name='emails']").val();
				var terms = $("input[name='terms']").val();
				
				$.ajax({
				  	   url: "index.php",
					   context: document.body,
					   type: "POST",
					   data: {'ajax': '1', 'action':'register', 'REGISTER':'1', 'name':name, 'email':email, 'username':username, 'password1':password1, 'password2':password2, 'emails':emails, 'terms':terms },
					   success: function(response11){
					   		alert(response11);
					   		console.log(response11);
					   		/* 
					   		if(response11.indexOf("success")){
					   			console.log('registered');
					   			$.colorbox.close();
					   		}
					   		// error
					   		else {
					   			alert(response11);
					   		}
					   		*/
					   }
					});
			});
			
			/**
			 * SAVE MASHLIST
			 */
			
			
			
			
		});	
	</script>
  
	
	
	
	
	
	
	

	
	
	
	
	
	
	
	
</head>

<body><!-- Use <body class="black"> for the black skin. -->

	
	<div id="loading-overlay"><img src="<?=$_SETTINGS['website'] ?>themes/mashlist/img/loader.gif" /><br><br>MASHLIST Loading...</div>
  	<span id="corner-banner" style="cursor:pointer;" onclick="javascript: window.location = 'http://labs.sprkk.com';">
    	<em>Sprkk</em>
    </span>
    
  <!-- Slideshow -->
  <div id="slideshow">
    
    <div id="welcome-box-wrapper">
    	
    	<? $UserAccounts->WelcomeBox(); ?>
    	<ul style="float:right;">
    		<li><a class="how-it-works" href="#how-modal">How it Works</a></li>
    		<li><a class="my-mashlists" href="#my-modal">My MashLists</a></li>
    		<li><a class="search-mashlists" href="#search-modal">Search MashLists</a></li>
    	</ul>
		
	</div>
    
    <!-- Header -->
    <header>

      <!-- Left navigation -->
      <nav>
        <ul>
        	<!--
        	<li><a href="">Recent</a></li>
          	<li><a href="">Hot</a></li>
          	<li><a href="">Popular</a></li>
          	<li><a href="">About</a></li>
         -->
         
        </ul>
      </nav>

      <!-- Heading -->
      <span id="pretag" style="color:#fff; width:200px; display:inline-block; text-align:right;">
      	8tracks + Relxaing Backgrounds = 
      </span>
      <h1><a>MASHLIST</a></h1>
      <span id="posttag" style="color:#fff; width:200px; display:inline-block; text-align:left;">
      	For Study, Coding, and Work.
      </span>

      <!-- Right navigation -->
      <nav>
        <ul>
        	<!--
          	<li><a href="">About</a></li>
          	<li><a href="">Hot</a></li>
          	<li><a href="">Popular</a></li>
          	<li><a href="">Random</a></li>
          -->
          
        </ul>
      </nav>

		

    </header>

	<div>
		
		<div id="search-wrapper">
			
			<div id="playlists">
				<div id="current-playlist">
					<h1>
						Current Playlist
						<a href="" id="search" style="float:right; font-size:10px">
							Search <img width="16px" src="admin/images/icons/speaker.png">
						</a>
					</h1>
					<div id="playlist-description">
						<img style="width:100px; min-height:100px; margin-top:10px;" id="playlist-image" src="<?=$_SETTINGS['website'] ?>themes/mashlist/img/loader.gif" />
						<h3 id="playlist-title">Playlist Loading...</h3>
						<div id="playlist-description-scroll">Welcome to MASHLIST, a musical experiment created for study, programming, and work.</div>
					</div>
				</div>
				<div id="playlist-results" style="display:none;">
					<h1>
						Search Playlists
						<a href="" id="now-playing" style="float:right; font-size:10px">
							Now Playing <img width="16px" src="admin/images/icons/speaker.png">
						</a>
					</h1>
					<input class="searchinput" id="playlist-keywords" value="Search Playlists" />
					<div id="playlist-search-scroll">
						<ul id="playlist-search-list">
						</ul>
					</div>
				</div>
			</div>
			
			<div id="player-wrapper">
				<div class="sm2-inline-list">
					<div class="ui360 ui360-vis"><a id="song-link" href="http://freshly-ground.com/data/audio/sm2/Adrian%20Glynn%20-%20Blue%20Belle%20Lament.mp3">Crash 1</a></div>
				</div>
				<div id="playlist-songs">
					<ul id="playlist-songs-list">
					</ul>
				</div>
			</div>
			
			
			
			
			<div id="sfx">
				<div>
					<h1>Current Backgrounds</h1>
					<div id="current-background-scroll">
						<ul id="current-background-scroll-list">
							<li class="wrapper">
								
								<div id="jquery_jplayer_1" class="jp-jplayer"></div>
								  <div id="jp_container_1" class="jp-audio">
								    <div class="jp-type-single">
								      <div class="jp-gui jp-interface">
								      	<div class="jp-title">
									        <ul>
									          <li>Rain</li>
									        </ul>
									    </div>
								        <ul class="jp-controls">
								          <li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>
								          <li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>
								          <li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>
								          <li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>
								          <li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>
								          <li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>
								        </ul>
								        
								        <div class="jp-volume-bar">
								          <div class="jp-volume-bar-value"></div>
								        </div>
								        <div class="jp-time-holder">
								          <div class="jp-current-time"></div>
								          <div class="jp-duration"></div>
								          <ul class="jp-toggles">
								            <li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat">repeat</a></li>
								            <li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="repeat off">repeat off</a></li>
								          </ul>
								        </div>
								      </div>
								      <div class="jp-no-solution">
								        <span>Update Required</span>
								        To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
								      </div>
								    </div>
								  </div>
								
							</li>
							
							
							
						</ul>
					</div>
				</div>			
				
				<div id="backgrounds-results" style="display:none;">
					<h1>Search Backgrounds:</h1>
					<input class="searchinput" value="Search Backgrounds" />
					<ul>
						<li>
							
							
							
							
							
							
							
							
							
							
							
							
							
							
							
							
						</li>
					</ul>
				</div>
			</div>
			
			<br clear="all"/>
		</div>
		
		
	</div>
	
	<?
	/*
    <!-- Slides -->
    <div class="slides">
      <ul>
        <li
          data-type="photo"
          data-title="Lorem ipsum dolor"
          data-description="Lorem ipsum dolor sit amet, consectetur adipiscing"
          data-image-url="themes/placeholders/1024x768/1.jpg">
          <a href="#"><img src="<?=$_SETTINGS['website'] ?>themes/mashlist/placeholders/150x110/1.jpg" alt=""></a>
        </li>
        <!--
        <li
          data-type="video"
          data-title="Time Lapse Sunset (HD 720p)"
          data-video-url="zUXogj7wESg">
          <a href="#"><img src="../img.youtube.com/vi/zUXogj7wESg/1.jpg" alt=""></a>
        </li>
        -->
        <li
          data-type="photo"
          data-title="Morbi faucibus nisl"
          data-description="Lorem ipsum dolor sit amet"
          data-image-url="themes/mashlist/placeholders/1024x768/2.jpg">
          <a href="#"><img src="<?=$_SETTINGS['website'] ?>themes/mashlist/placeholders/150x110/2.jpg" alt=""></a>
        </li>
        <li
          data-type="photo"
          data-title="Vestibulum sed"
          data-description="Lorem ipsum dolor sit amet, consectetur adipiscing elit"
          data-image-url="themes/mashlist/placeholders/1024x768/3.jpg">
          <a href="#"><img src="<?=$_SETTINGS['website'] ?>themes/mashlist/placeholders/150x110/3.jpg" alt=""></a>
        </li>
        <li
          data-type="photo"
          data-title="Praesent urna felis"
          data-description="Lorem ipsum dolor sit amet"
          data-image-url="themes/mashlist/placeholders/1024x768/4.jpg">
          <a href="#"><img src="<?=$_SETTINGS['website'] ?>themes/mashlist/placeholders/150x110/4.jpg" alt=""></a>
        </li>
        <li
          data-type="photo"
          data-title="Pellentesque neque"
          data-description="Lorem ipsum dolor sit amet, consectetur adipiscing"
          data-image-url="themes/mashlist/placeholders/1024x768/5.jpg">
          <a href="#"><img src="<?=$_SETTINGS['website'] ?>themes/mashlist/placeholders/150x110/5.jpg" alt=""></a>
        </li>
        <li
          data-type="photo"
          data-title="Fusce at eros tortor"
          data-description="Lorem ipsum dolor sit amet"
          data-image-url="mashlist/placeholders/1024x768/6.jpg">
          <a href="#"><img src="<?=$_SETTINGS['website'] ?>themes/mashlist/placeholders/150x110/6.jpg" alt=""></a>
        </li>
        <li
          data-type="photo"
          data-title="Quisque varius"
          data-description="Lorem ipsum dolor sit amet"
          data-image-url="mashlist/placeholders/1024x768/7.jpg">
          <a href="#"><img src="<?=$_SETTINGS['website'] ?>themes/mashlist/placeholders/150x110/7.jpg" alt=""></a>
        </li>
        <li
          data-type="photo"
          data-title="Pellentesque habitant morbi"
          data-description="Lorem ipsum dolor sit amet"
          data-image-url="themes/mashlist/placeholders/1024x768/8.jpg">
          <a href="#"><img src="<?=$_SETTINGS['website'] ?>themes/mashlist/placeholders/150x110/8.jpg" alt=""></a>
        </li>
        <li
          data-type="photo"
          data-title="Praesent porttitor"
          data-description="Lorem ipsum dolor sit amet"
          data-image-url="themes/mashlist/placeholders/1024x768/9.jpg">
          <a href="#"><img src="<?=$_SETTINGS['website'] ?>themes/mashlist/placeholders/150x110/9.jpg" alt=""></a>
        </li>
        <li
          data-type="photo"
          data-title="Curabitur eu est turpis"
          data-description="Lorem ipsum dolor sit amet"
          data-image-url="themes/mashlist/placeholders/1024x768/10.jpg">
          <a href="#"><img src="<?=$_SETTINGS['website'] ?>themes/mashlist/placeholders/150x110/10.jpg" alt=""></a>
        </li>
        <li
          data-type="photo"
          data-title="Suspendisse rutrum lacus"
          data-description="Lorem ipsum dolor sit amet"
          data-image-url="themes/mashlist/placeholders/1024x768/11.jpg">
          <a href="#"><img src="<?=$_SETTINGS['website'] ?>themes/mashlist/placeholders/150x110/11.jpg" alt=""></a>
        </li>
        <li
          data-type="photo"
          data-title="Etiam auctor"
          data-description="Lorem ipsum dolor sit amet"
          data-image-url="themes/mashlist/placeholders/1024x768/12.jpg">
          <a href="#"><img src="<?=$_SETTINGS['website'] ?>themes/mashlist/placeholders/150x110/12.jpg" alt=""></a>
        </li>
      </ul>

      <!-- Scroll areas -->
      <span class="scroll_left"></span>
      <span class="scroll_right"></span>

    </div>
	
    <!-- Darken background image -->
    <div class="slideshow_overlay"></div>

    <!-- Slide info -->
    <div class="slide_info">
      <h2></h2>
      <p></p>
    </div>

    <!-- Slide info on hover -->
    <span class="slide_info_hover"></span>
	*/
	?>
  </div>
	
  <div style="display:none;">	
	<!-- LOGIN MODAL -->
	<div class="modal" id="login-modal" style="">
		<form>
			<h2>Login</h2>
			<p>
				<label>*Email/Username</label>
				<input id='email' type='text' value='' />
			</p>
			<p>
				<label>*Password</label>
				<input id='password' type='password' value='' />
			</p>
			<p>
				<input id='login-button' type='submit' value='Login' />
			</p>
			<p>
				<a id="forgot-link" href="#forgot-modal">I Forgot My Password</a><br>
				<a id="register-link" href="#register-modal">I Don't Have An Account</a>
			</p>
			
		</form>
	</div>
	
	<!-- HOW MODAL -->
	<div class="modal" id="how-modal" style="">
		<h2>How It Works...</h2>
		<p>sadf asf fasdfasdfsd fasdfassadf asf fasdfasdfsd fasdfassadf asf fasdfasdfsd fasdfas
			sadf asf fasdfasdfsd fasdfassadf asf fasdfasdfsd fasdfassadf asf fasdfasdfsd fasdfas
			sadf asf fasdfasdfsd fasdfassadf asf fasdfasdfsd fasdfasdf asdf af </p>
			
		<p>sadf asf fasdfasdfsd fasdfassadf asf fasdfasdfsd fasdfassadf asf fasdfasdfsd fasdfas
			sadf asf fasdfasdfsd fasdfassadf asf fasdfasdfsd fasdfassadf asf fasdfasdfsd fasdfas
			sadf asf fasdfasdfsd fasdfassadf asf fasdfasdfsd fasdfasdf asdf af </p>
	</div>
	
	<!-- MY MODAL -->
	<div class="modal" id="my-modal" style="">
		<h2>My Mashlists</h2>
		<p>sadf asf fasdfasdfsd fasdfassadf asf fasdfasdfsd fasdfassadf asf fasdfasdfsd fasdfas
			sadf asf fasdfasdfsd fasdfassadf asf fasdfasdfsd fasdfassadf asf fasdfasdfsd fasdfas
			sadf asf fasdfasdfsd fasdfassadf asf fasdfasdfsd fasdfasdf asdf af </p>
			
		<p>sadf asf fasdfasdfsd fasdfassadf asf fasdfasdfsd fasdfassadf asf fasdfasdfsd fasdfas
			sadf asf fasdfasdfsd fasdfassadf asf fasdfasdfsd fasdfassadf asf fasdfasdfsd fasdfas
			sadf asf fasdfasdfsd fasdfassadf asf fasdfasdfsd fasdfasdf asdf af </p>
	</div>
	
	<!-- TOP MODAL -->
	<div class="modal" id="search-modal" style="">
		<h2>Search Mashlists</h2>
		<p>sadf asf fasdfasdfsd fasdfassadf asf fasdfasdfsd fasdfassadf asf fasdfasdfsd fasdfas
			sadf asf fasdfasdfsd fasdfassadf asf fasdfasdfsd fasdfassadf asf fasdfasdfsd fasdfas
			sadf asf fasdfasdfsd fasdfassadf asf fasdfasdfsd fasdfasdf asdf af </p>
			
		<p>sadf asf fasdfasdfsd fasdfassadf asf fasdfasdfsd fasdfassadf asf fasdfasdfsd fasdfas
			sadf asf fasdfasdfsd fasdfassadf asf fasdfasdfsd fasdfassadf asf fasdfasdfsd fasdfas
			sadf asf fasdfasdfsd fasdfassadf asf fasdfasdfsd fasdfasdf asdf af </p>
	</div>
	
	<!-- REGISTER MODAL -->
	<div class="modal" id="register-modal" style="">
		<?
			$UserAccounts->RegistrationForm(0,1,"");
		?>
		<script type='text/javascript'>
			/**
			 * 	SCRIPTS FOR FORM ALTERATIONS
			 */
			$("input[name='city']").parent('p').hide();
			$("input[name='email']").parent('p').css("clear","both");
			$("select[name='billing_state']").parent('p').hide();
			$("input[name='phone']").parent('p').hide();
			$("select[name='heard']").parent('p').hide();
			
			
			
		</script>
	</div>

	<!-- ACCOUNT MODAL
	<div id="account-modal" style="">Account</div>
	-->
  </div> <!-- end modal display none; -->
  
  <!-- Content -->
  <div id="content">

    
    <div class="clearfix"></div>
  </div>

	<!-- Customization
	<div id="settings">
	  	<a href="#" class="white selected"><span></span></a>
	  	<a href="#" class="black"><span></span></a>
	</div>
	-->
  
  
	<!-- Google Analytics: Change UA-XXXXX-X to be your site's ID
	<script>
		window._gaq = [['_setAccount','UA-2529322-16'],['_trackPageview'],['_trackPageLoadTime']];
		Modernizr.load({
		load: ('https:' == location.protocol ? '//ssl' : '//www') + '.google-analytics.com/ga.js'
		});
	</script>
	-->
  
  <!-- Prompt IE 6 users to install Chrome Frame -->
  <!--[if lt IE 7 ]>
    <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
    <script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
  <![endif]-->

	<script type="text/javascript">

		var _gaq = _gaq || [];
		 _gaq.push(['_setAccount', 'UA-29991034-1']);
		 _gaq.push(['_trackPageview']);
		
		(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();

	</script>
</body>

</html>
