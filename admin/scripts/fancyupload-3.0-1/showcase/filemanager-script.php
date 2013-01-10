<?php
/*************************************************************************************************************************************
*   
*
* Karl Steltenpohl Development LLC
* Web Business Framework
* Version 1.0
* Copyright 2010 Karl Steltenpohl Development All Rights Reserved
*
* Commercially Licensed 
* View License At: http://www.karlsdevelopment.com/web-business-framework/license
*
*************************************************************************************************************************************/
 
session_start();
include_once'../../../../includes/config.php';
//include'../../../../admin/includes/framework_functions.php';
//error_reporting(E_ALL);
global $_SETTINGS;

// INI SET
ini_set("upload_max_filesize", "10M");
ini_set("post_max_size", "20M");
ini_set("memory_limit","20M");

/*** SimpleImageClass***/
class SimpleImage {
   var $image;
   var $image_type;
 
   function load($filename) {
      $image_info = getimagesize($filename);
      $this->image_type = $image_info[2];
      if( $this->image_type == IMAGETYPE_JPEG ) {
         $this->image = imagecreatefromjpeg($filename);
      } elseif( $this->image_type == IMAGETYPE_GIF ) {
         $this->image = imagecreatefromgif($filename);
      } elseif( $this->image_type == IMAGETYPE_PNG ) {
         $this->image = imagecreatefrompng($filename);
      }
   }
   function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null) {
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image,$filename,$compression);
      } elseif( $image_type == IMAGETYPE_GIF ) {
         imagegif($this->image,$filename);         
      } elseif( $image_type == IMAGETYPE_PNG ) {
         imagepng($this->image,$filename);
      }   
      if( $permissions != null) {
         chmod($filename,$permissions);
      }
   }
   function output($image_type=IMAGETYPE_JPEG) {
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image);
      } elseif( $image_type == IMAGETYPE_GIF ) {
         imagegif($this->image);         
      } elseif( $image_type == IMAGETYPE_PNG ) {
         imagepng($this->image);
      }   
   }
   function getWidth() {
      return imagesx($this->image);
   }
   function getHeight() {
      return imagesy($this->image);
   }
   function resizeToHeight($height) {
      $ratio = $height / $this->getHeight();
      $width = $this->getWidth() * $ratio;
      $this->resize($width,$height);
   }
   function resizeToWidth($width) {
      $ratio = $width / $this->getWidth();
      $height = $this->getheight() * $ratio;
      $this->resize($width,$height);
   }
   function scale($scale) {
      $width = $this->getWidth() * $scale/100;
      $height = $this->getheight() * $scale/100; 
      $this->resize($width,$height);
   }
   function resize($width,$height) {
      $new_image = imagecreatetruecolor($width, $height);
      imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
      $this->image = $new_image;   
   }      
}

// BEGIN FANCY UPLOAD
$result = array();

$result['time'] = date('r');
$result['addr'] = substr_replace(gethostbyaddr($_SERVER['REMOTE_ADDR']), '******', 0, 6);
$result['agent'] = $_SERVER['HTTP_USER_AGENT'];

if (count($_GET)) {
	$result['get'] = $_GET;
}
if (count($_POST)) {
	$result['post'] = $_POST;
}
if (count($_FILES)) {
	$result['files'] = $_FILES;
}

// we kill an old file to keep the size small
if (file_exists('script.log') && filesize('script.log') > 102400) {
	unlink('script.log');
}

$log = @fopen('script.log', 'a');
if ($log) {
	fputs($log, print_r($result, true) . "\n---\n");
	fclose($log);
}


// Validation

$error = false;

if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
	$error = 'Invalid Upload';
}

/**
 * You would add more validation, checking image type or user rights.
 *

if (!$error && $_FILES['Filedata']['size'] > 2 * 1024 * 1024)
{
	$error = 'Please upload only files smaller than 2Mb!';
}

if (!$error && !($size = @getimagesize($_FILES['Filedata']['tmp_name']) ) )
{
	$error = 'Please upload only images, no other files are supported.';
}

if (!$error && !in_array($size[2], array(1, 2, 3, 7, 8) ) )
{
	$error = 'Please upload only images of type JPEG, GIF or PNG.';
}

if (!$error && ($size[0] < 25) || ($size[1] < 25))
{
	$error = 'Please upload an image bigger than 25px.';
}
*/


// Processing
$dir 		= "../../../../uploads/";
$thumbdir 	= "../../../../uploads/wpThumbnails/";

// GET THE UPLOAD DIRECTORY
$uploaddirectoryflag = file_get_contents('../../../modules/file_manager/createuploaddirectoryflag.txt');
if($uploaddirectoryflag != ""){
	$dir 		= "../../../../".$uploaddirectoryflag."";
	$thumbdir 	= "../../../../".$uploaddirectoryflag."wpThumbnails/";
}

// CHECK DIRECTORY EXIST
if(!file_exists(rtrim($dir,'/'))){
	mkdir(rtrim($dir,'/') , 0777);
}

if(!file_exists(rtrim($thumbdir,'/'))){
	mkdir(rtrim($thumbdir,'/') , 0777);
}

// GET A UNIQUE FILENAME
$uniquename = uniqueFileName($dir,$_FILES['Filedata']['name']);
$uniquename = str_replace("JPG","jpg",$uniquename);

// GET EXTENSION
$filearray = explode(".",$uniquename);

if($filearray[1] == 'JPG'){
	$filearray[1] = 'jpg';
}

// CREATE THUMBNAIL NAMES
$uniquenamethumb1 = strtolower($filearray[0]."_w94.".$filearray[1]);
$uniquenamethumb2 = strtolower($filearray[0]."_w150.".$filearray[1]);
$uniquenamethumb3 = strtolower($filearray[0]."_w300.".$filearray[1]);
$uniquenamethumb4 = strtolower($filearray[0]."_w600.".$filearray[1]);
$uniquenamethumb5 = strtolower($filearray[0]."_w1024.".$filearray[1]);

if ($error) {
	$return = array(
		'status' => '0',
		'error' => $error
	);
} else {
	$return = array(
		'status' => '1',
		'name' => $uniquename
	);
	// Our processing, we get a hash value from the file
	$return['hash'] = @md5_file($_FILES['Filedata']['tmp_name']);
	// ... and if available, we get image data
	$info = @getimagesize($_FILES['Filedata']['tmp_name']);

	if ($info) {
		$return['width'] = $info[0];
		$return['height'] = $info[1];
		$return['mime'] = $info['mime'];
	}
	
	//
	// COPY FILES
	//
	
	// COPY ORIGINAL
	move_uploaded_file($_FILES['Filedata']['tmp_name'],$dir.strtolower($uniquename));
	//chmod($dir.$uniquename,0777);
	
	// GET THUMBNAIL FLAG
	$thumbnailflag = file_get_contents('../../../modules/file_manager/createthumbnailsflag.txt');

	// GET EXTENSION
	$uniquenamearray = explode(".",$uniquename);
	$file_ext = $uniquenamearray[1];
	
	//
	// IF IMAGE
	//
	if($file_ext == "jpg" || $file_ext== "JPG" || $file_ext == "jpeg" || $file_ext == "png" || $file_ext == "gif" || $file_ext == "tif"){
		//
		// COPY THUMBNAILS
		//
		if($thumbnailflag == '1'){		 
			$image = new SimpleImage();
			$image->load($dir.$uniquename);
			//if($image->getWidth() >= 94 AND $image->getHeight() >= 94){
			if($image->getWidth() >= 94){
				$image->resizeToWidth(94);
				$image->save($thumbdir.$uniquenamethumb1);
			}

			$image1 = new SimpleImage();
			$image1->load($dir.$uniquename);
			//if($image1->getWidth() >= 150 AND $image1->getHeight() >= 150){
			if($image1->getWidth() >= 150){
				$image1->resizeToWidth(150);
				$image1->save($thumbdir.$uniquenamethumb2);
			}

			$image2 = new SimpleImage();
			$image2->load($dir.$uniquename);
			//if($image2->getWidth() >= 300 AND $image2->getHeight() >= 300){
			if($image2->getWidth() >= 300){
				$image2->resizeToWidth(300);
				$image2->save($thumbdir.$uniquenamethumb3);
			}

			$image3 = new SimpleImage();
			$image3->load($dir.$uniquename);
			//if($image3->getWidth() >= 600 AND $image3->getHeight() >= 600){
			if($image3->getWidth() >= 600){
				$image3->resizeToWidth(600);
				$image3->save($thumbdir.$uniquenamethumb4);
			}

			$image4 = new SimpleImage();
			$image4->load($dir.$uniquename);
			//if($image4->getWidth() >= 1024 AND $image4->getHeight() >= 1024){
			if($image4->getWidth() >= 1024){
				$image4->resizeToWidth(1024);
				$image4->save($thumbdir.$uniquenamethumb5);
			}
		}
	}
	
	// RETURN ORIGINAL
	$return['src'] = $dir.$uniquename;
}




if (!function_exists('json_encode')){
	function json_encode($a=false){
		if (is_null($a)) return 'null';
		if ($a === false) return 'false';
		if ($a === true) return 'true';
		if (is_scalar($a))
		{
		if (is_float($a))
		{
		// Always use "." for floats.
		return floatval(str_replace(",", ".", strval($a)));
		}

		if (is_string($a))
		{
		static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
		return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
		}
		else
		return $a;
		}
		$isList = true;
		for ($i = 0, reset($a); $i < count($a); $i++, next($a))
		{
		if (key($a) !== $i)
		{
		$isList = false;
		break;
		}
		}
		$result = array();
		if ($isList)
		{
		foreach ($a as $v) $result[] = json_encode($v);
		return '[' . join(',', $result) . ']';
		}
		else
		{
		foreach ($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v);
		return '{' . join(',', $result) . '}';
		}
	}
}











// Output


/**
 * Again, a demo case. We can switch here, for different showcases
 * between different formats. You can also return plain data, like an URL
 * or whatever you want.
 *
 * The Content-type headers are uncommented, since Flash doesn't care for them
 * anyway. This way also the IFrame-based uploader sees the content.
 */

if (isset($_REQUEST['response']) && $_REQUEST['response'] == 'xml') {
	// header('Content-type: text/xml');

	// Really dirty, use DOM and CDATA section!
	echo '<response>';
	foreach ($return as $key => $value) {
		echo "<$key><![CDATA[$value]]></$key>";
	}
	echo '</response>';
} else {
	// header('Content-type: application/json');

	echo json_encode($return);
}

?>