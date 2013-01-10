<?

/*** Include Main Configuration File ***/
require_once('../includes/config.php');

/*** Get Admin Authorization ***/
$adminAuth = $_SESSION["session"]->admin->auth;

/*** Get Admin Access Level ***/
$adminLevel = $_SESSION["session"]->admin->accessLevel;	

//var_dump($_SESSION['session']);

/*** Login Code ***/
if( isset($_POST['login']) )
{	
	/*** If username password validate forward to index.php with session id ***/
	if( $_SESSION["session"]->admin->login($_POST['username'], $_POST['password']) )
	{
		header("Location: index.php?");
		exit();
	} else {
		header("Location: ?SUCCSESS=0&REPORT=The username and/or password is incorect&user=".$_POST['username']."");
		exit();
	}
}



if(isset($_POST['retrieve']))
{
	
	
	$select = 	"SELECT * FROM admin WHERE ".
				"email='".$_POST['email']."' ".
				"".$_SESSION['demosqland']." ".
				"LIMIT 1";
				
	$result = doQuery($select);
	if(mysql_num_rows($result)){
		$row = mysql_fetch_array($result);
		$newpass = makePass();
		
		$select = 	"UPDATE admin SET ".
					"password='".md5($newpass)."' ".
					"WHERE admin_id='".$row['admin_id']."' ".
					"".$_SESSION['demosqland']."";
		
		$result = doQuery($select);
		
		$to = $_POST['email'];
		$from = $_SETTINGS['email'];
		$subject = "Password Request from ".$_SETTINGS['siteName']."";
		$message = 	"Your new password is:<br><br><strong>".$newpass."</strong><br><br>Login at:<br>".
					"<a href=\"".$_SETTINGS['website']."admin\">".$_SETTINGS['website']."admin</a><br>";
		
		sendEmail($to,$from,$subject,$message);
	
		header("Location: ?SUCCSESS=0&REPORT=Email has been set containing a new password.&user=".$_POST['username']."&" . SID);
		exit();
		
	} else {
		header("Location: ?SUCCSESS=0&FORGOT_PASSWORD=1&REPORT=That email is not in our system&user=".$_POST['username']."&" . SID);
		exit();	
	}
	
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" >
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
		
		<title><?=$_SETTINGS['adminTitle']?> Login</title>
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
		<?
		if($_SETTINGS['demo'] == "1"){
		?>
			<script>	
			$(function() {
				$("#dialog").dialog({
					bgiframe: true,
					modal: true,
					width: 750,
					closeOnEscape: false,
					resizable: false,
					draggable: false,
					close: function() {
						$("a.nav-administration").effect("pulsate", { times:1000 }, 500);
					},
					buttons: {
						Ok: function() {
							$(this).dialog('close');
							$("li.nav-administration").effect("pulsate", { times:1000 }, 500);
						}
					}
				});
			});
			</script>
		<?
		}
		?>	
		<style type="text/css" media="screen">
		#flashContent { width:100%; height:100%; }
		</style>
	</head>
	<body class="loginbg">
	
		<?
		if($_REQUEST['message'] != ""){ $_REQUEST['REPORT'] = $_REQUEST['message']; $_REQUEST['SUCCESS'] = '0'; }
		report($_REQUEST['REPORT'],$_REQUEST['SUCCESS']);
		?>
		
		<div id="login-logo">
			<center>
			<?
			// LOGO
			if($_SETTINGS['logoImage'] != ""){
				?> <img src="<?=$_SETTINGS['logoImage']?>" /> <?						
			} else {
				?> <img src="images/weslogo.png" />
				<?
			}
			?>
			</center>
		</div>
		<div id="login">
			<form name="loginForm" class="login" method="post" action="">
				
				<?
				// FORGOT PASSWORD
				if($_REQUEST['ACTION'] == 'FORGOT_PASSWORD'){
					?>
					<p style="width:300px; margin-bottom:25px; text-align:center; margin-left:25px; font-size:14px;">
					Enter the email associated with your account below or <a href="?">click here</a> to login.
					</p>

					<p>
					<label for="username">Email*</label>
					<? if($email == ""){ $email = $_REQUEST['email']; } ?>
					<input id="email" name="email" size="30" type="text"  VALUE="<?=$email?>" />
					</p>
				
					<p style="text-align:center;">
					<input class="button" type="submit" name="retrieve" value="Get Password" />
					</p>
					<?
				}
				
				// REGISTRATION
				elseif($_REQUEST['ACTION'] == 'REGISTER'){
					
					
				}
				
				// FACE RECOGNITION LOGIN
				elseif($_REQUEST['ACTION'] == 'FACE_RECOGNITION'){
					?>
					<div id="flashContent">
						<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="350" height="400" id="take_picture" align="middle">
							<param name="movie" value="scripts/face_recognition/take_picture.swf" />
							<param name="quality" value="high" />
							<param name="bgcolor" value="#ffffff" />
							<param name="play" value="true" />
							<param name="loop" value="true" />
							<param name="wmode" value="window" />
							<param name="scale" value="showall" />
							<param name="menu" value="true" />
							<param name="devicefont" value="false" />
							<param name="salign" value="" />
							<param name="allowScriptAccess" value="sameDomain" />
							<!--[if !IE]>-->
							<object type="application/x-shockwave-flash" data="scripts/face_recognition/take_picture.swf" width="350" height="400">
								<param name="movie" value="scripts/face_recognition/take_picture.swf" />
								<param name="quality" value="high" />
								<param name="bgcolor" value="#ffffff" />
								<param name="play" value="true" />
								<param name="loop" value="true" />
								<param name="wmode" value="window" />
								<param name="scale" value="showall" />
								<param name="menu" value="true" />
								<param name="devicefont" value="false" />
								<param name="salign" value="" />
								<param name="allowScriptAccess" value="sameDomain" />
							<!--<![endif]-->
								<a href="http://www.adobe.com/go/getflash">
									<img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" />
								</a>
							<!--[if !IE]>-->
							</object>
							<!--<![endif]-->
						</object>
					</div>
					<?
				}
				
				// STANDARD LOGIN
				else{
					?>
					<p style="width:300px; margin-bottom:25px; margin-left:25px; text-align:center; font-size:14px;">
					Enter your username and password below or <a href="?ACTION=FORGOT_PASSWORD">click here</a> if you forgot your password.
					</p>
					
					<?
					//
					// IF IE 6 SHOW MESSAGE AND UPGRADE OPTIONS
					//
					?>
					
					<!--[if lt IE 6]>
					<p style="width:260px; background:#EFC2C2; padding:20px; margin-bottom:25px; text-align:center; margin-left:25px; font-size:12px;">
					<strong>IMPORTANT:</strong>&nbsp;&nbsp; Ooops! Don't worry there isn't a problem. I need you to upgrade your version of internet explorer. You are currently using Internet Explorer 6, which was released in August 2001. Its time for an upgrade.
					For the best and most secure experience use the Mozilla Firefox browser. It is the world's best browser. Click the button below to quickly install the Firefox Browser or upgrade your Internet Exploerer.
					</p>
					
					
					<p style="width:300px; margin-bottom:25px; text-align:center; margin-left:25px; font-weight:bold; font-size:14px;">

					<a href='http://www.mozilla.com?from=sfx&amp;uid=308186&amp;t=588'>
					<img src='http://images.spreadfirefox.com/firefox/3.6/200x32_best_orange.png' alt='Spread Firefox Affiliate Button' border='0' />
					</a>
					
					<br>
					
					<a href='http://www.microsoft.com/windows/internet-explorer/default.aspx'>
					<img src='images/ie.png' alt='Upgrade Internet Explorer' border='0' />
					</a>

					</p>
					<![endif]-->
					
					<?
					//
					// IF NOT IE 6 SHOW Login Form
					//
					?>
					
					<!--[if !IE 6]><!-->
					
					<p>
					<label for="username">Username*</label>
					<? if($user == ""){ $user = $_REQUEST['user']; } ?>
					<input id="username" name="username" size="30" type="text"  VALUE="<?=$user?>" />
					</p>
					
					<p>
					<label for="password">Password*</label>
					<input id="password" name="password" size="30" type="password" VALUE="<?=$decrypted?>" />
					</p>
									
					<p style="text-align:center;">
					<input type='hidden' name='referer' value='<?=$_SESSION['referer']?>'>
					<input class="button" type="submit" name="login" value="Login" />
					</p>
					
					<!--<![endif]-->
					<?
				}
				?>
				
					
				
			</form>
		</div>
		<style>
		#errorbox {
				margin:0px 0 30px 0;
				position:relative;
				height:auto;
				display:block;
		}
		</style>
		</body>
</html>

<?
/*
<?php
// 
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.     
// 
// @Author Karthik Tharavaad 
//         karthik_tharavaad@yahoo.com
// @Contributor Maurice Svay
//              maurice@svay.Com
 
class Face_Detector {
 
    protected $detection_data;
    protected $canvas;
    protected $face;
    private $reduced_canvas;
 
    public function __construct($detection_file = 'detection.dat') {
        if (is_file($detection_file)) {
            $this->detection_data = unserialize(file_get_contents($detection_file));
        } else {
            throw new Exception("Couldn't load detection data");
        }
        //$this->detection_data = json_decode(file_get_contents('data.js'));
    }
 
    public function face_detect($file) {
        if (!is_file($file)) {
            throw new Exception("Can not load $file");
        }
 
        $this->canvas = imagecreatefromjpeg($file);
        $im_width = imagesx($this->canvas);
        $im_height = imagesy($this->canvas);
 
        //Resample before detection?
        $ratio = 0;
        $diff_width = 320 - $im_width;
        $diff_height = 240 - $im_height;
        if ($diff_width > $diff_height) {
            $ratio = $im_width / 320;
        } else {
            $ratio = $im_height / 240;
        }
 
        if ($ratio != 0) {
            $this->reduced_canvas = imagecreatetruecolor($im_width / $ratio, $im_height / $ratio);
            imagecopyresampled($this->reduced_canvas, $this->canvas, 0, 0, 0, 0, $im_width / $ratio, $im_height / $ratio, $im_width, $im_height);
 
            $stats = $this->get_img_stats($this->reduced_canvas);
            $this->face = $this->do_detect_greedy_big_to_small($stats['ii'], $stats['ii2'], $stats['width'], $stats['height']);
            $this->face['x'] *= $ratio;
            $this->face['y'] *= $ratio;
            $this->face['w'] *= $ratio;
        } else {
            $stats = $this->get_img_stats($this->canvas);
            $this->face = $this->do_detect_greedy_big_to_small($stats['ii'], $stats['ii2'], $stats['width'], $stats['height']);
        }
        return ($this->face['w'] > 0);
    }
 
 
    public function toJpeg() {
        $color = imagecolorallocate($this->canvas, 255, 0, 0); //red
        imagerectangle($this->canvas, $this->face['x'], $this->face['y'], $this->face['x']+$this->face['w'], $this->face['y']+ $this->face['w'], $color);
        header('Content-type: image/jpeg');
        imagejpeg($this->canvas);
    }
 
    public function toJson() {
        return "{'x':" . $this->face['x'] . ", 'y':" . $this->face['y'] . ", 'w':" . $this->face['w'] . "}";
    }
 
    public function getFace() {
        return $this->face;
    }
 
    protected function get_img_stats($canvas){
        $image_width = imagesx($canvas);
        $image_height = imagesy($canvas);     
        $iis =  $this->compute_ii($canvas, $image_width, $image_height);
        return array(
            'width' => $image_width,
            'height' => $image_height,
            'ii' => $iis['ii'],
            'ii2' => $iis['ii2']
        );         
    }
 
    protected function compute_ii($canvas, $image_width, $image_height ){
        $ii_w = $image_width+1;
        $ii_h = $image_height+1;
        $ii = array();
        $ii2 = array();      
 
        for($i=0; $i<$ii_w; $i++ ){
            $ii[$i] = 0;
            $ii2[$i] = 0;
        }                        
 
        for($i=1; $i<$ii_w; $i++ ){  
            $ii[$i*$ii_w] = 0;       
            $ii2[$i*$ii_w] = 0; 
            $rowsum = 0;
            $rowsum2 = 0;
            for($j=1; $j<$ii_h; $j++ ){
                $rgb = ImageColorAt($canvas, $j, $i);
                $red = ($rgb >> 16) & 0xFF;
                $green = ($rgb >> 8) & 0xFF;
                $blue = $rgb & 0xFF;
                $grey = ( 0.2989*$red + 0.587*$green + 0.114*$blue )>>0;  // this is what matlab uses
                $rowsum += $grey;
                $rowsum2 += $grey*$grey;
 
                $ii_above = ($i-1)*$ii_w + $j;
                $ii_this = $i*$ii_w + $j;
 
                $ii[$ii_this] = $ii[$ii_above] + $rowsum;
                $ii2[$ii_this] = $ii2[$ii_above] + $rowsum2;
            }
        }
        return array('ii'=>$ii, 'ii2' => $ii2);
    }
 
    protected function do_detect_greedy_big_to_small( $ii, $ii2, $width, $height ){
        $s_w = $width/20.0;
        $s_h = $height/20.0;
        $start_scale = $s_h < $s_w ? $s_h : $s_w;
        $scale_update = 1 / 1.2;
        for($scale = $start_scale; $scale > 1; $scale *= $scale_update ){
            $w = (20*$scale) >> 0;
            $endx = $width - $w - 1;
            $endy = $height - $w - 1;
            $step = max( $scale, 2 ) >> 0;
            $inv_area = 1 / ($w*$w);
            for($y = 0; $y < $endy ; $y += $step ){
                for($x = 0; $x < $endx ; $x += $step ){
                    $passed = $this->detect_on_sub_image( $x, $y, $scale, $ii, $ii2, $w, $width+1, $inv_area);
                    if( $passed ) {
                        return array('x'=>$x, 'y'=>$y, 'w'=>$w);
                    }
                } // end x
            } // end y
        }  // end scale
        return null;
    }
 
    protected function detect_on_sub_image( $x, $y, $scale, $ii, $ii2, $w, $iiw, $inv_area){
        $mean = ( $ii[($y+$w)*$iiw + $x + $w] + $ii[$y*$iiw+$x] - $ii[($y+$w)*$iiw+$x] - $ii[$y*$iiw+$x+$w]  )*$inv_area;
        $vnorm =  ( $ii2[($y+$w)*$iiw + $x + $w] + $ii2[$y*$iiw+$x] - $ii2[($y+$w)*$iiw+$x] - $ii2[$y*$iiw+$x+$w]  )*$inv_area - ($mean*$mean);    
        $vnorm = $vnorm > 1 ? sqrt($vnorm) : 1;
 
        $passed = true;
        for($i_stage = 0; $i_stage < count($this->detection_data); $i_stage++ ){
            $stage = $this->detection_data[$i_stage];  
            $trees = $stage[0];  
 
            $stage_thresh = $stage[1];
            $stage_sum = 0;
 
            for($i_tree = 0; $i_tree < count($trees); $i_tree++ ){
                $tree = $trees[$i_tree];
                $current_node = $tree[0];    
                $tree_sum = 0;
                while( $current_node != null ){
                    $vals = $current_node[0];
                    $node_thresh = $vals[0];
                    $leftval = $vals[1];
                    $rightval = $vals[2];
                    $leftidx = $vals[3];
                    $rightidx = $vals[4];
                    $rects = $current_node[1];
 
                    $rect_sum = 0;
                    for( $i_rect = 0; $i_rect < count($rects); $i_rect++ ){
                        $s = $scale;
                        $rect = $rects[$i_rect];
                        $rx = ($rect[0]*$s+$x)>>0;
                        $ry = ($rect[1]*$s+$y)>>0;
                        $rw = ($rect[2]*$s)>>0;  
                        $rh = ($rect[3]*$s)>>0;
                        $wt = $rect[4];
 
                        $r_sum = ( $ii[($ry+$rh)*$iiw + $rx + $rw] + $ii[$ry*$iiw+$rx] - $ii[($ry+$rh)*$iiw+$rx] - $ii[$ry*$iiw+$rx+$rw] )*$wt;
                        $rect_sum += $r_sum;
                    } 
 
                    $rect_sum *= $inv_area;
 
                    $current_node = null;
                    if( $rect_sum >= $node_thresh*$vnorm ){
                        if( $rightidx == -1 ) 
                            $tree_sum = $rightval;
                        else
                            $current_node = $tree[$rightidx];
                    } else {
                        if( $leftidx == -1 )
                            $tree_sum = $leftval;
                        else
                            $current_node = $tree[$leftidx];
                    }
                } 
                $stage_sum += $tree_sum;
            } 
            if( $stage_sum < $stage_thresh ){
                return false;
            }
        } 
        return true;
    }
}

 
*/
?>