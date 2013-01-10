<?php
/*******************************************************************
*
* Karl Steltenpohl Development 
* Web Business Framework
* Version 1.0
* Copyright 2009 Karl Steltenpohl Development All Rights Reserved
*
*******************************************************************/
session_start();
$RandomStr = md5(microtime());// md5 to generate the random string
$ResultStr = substr($RandomStr,0,5);//trim 5 digit 
$NewImage =imagecreatefromjpeg("img.jpg");//image create by existing image and as back ground 
$LineColor = imagecolorallocate($NewImage,255,255,255);//line color 
$TextColor = imagecolorallocate($NewImage, 0, 0, 0);//text color-white
imageline($NewImage,1,1,40,40,$LineColor);//create line 1 on image 
imageline($NewImage,1,100,60,0,$LineColor);//create line 2 on image 

//$LineColor = imagecolorallocate($NewImage,233,239,239);
//$TextColor = imagecolorallocate($NewImage, 255, 255, 255);
//imageline($NewImage,1,1,40,40,$LineColor);
//imageline($NewImage,1,100,60,0,$LineColor);

imagestring($NewImage, 5, 20, 10, $ResultStr, $TextColor);// Draw a random string horizontally 
$_SESSION['key'] = $ResultStr;// carry the data through session
header("Content-type: image/jpeg");// out out the image 
imagejpeg($NewImage);//Output image to browser 
?>
