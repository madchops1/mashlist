<?php

session_start();

error_reporting(E_ERROR);





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





include'../../../administration/includes/framework_functions.php';



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

/**

 * Its a demo, you would move or process the file like:

 *

 * move_uploaded_file($_FILES['Filedata']['tmp_name'], '../uploads/' . $_FILES['Filedata']['name']);

 * $return['src'] = '/uploads/' . $_FILES['Filedata']['name'];

 *

 * or

 *

 * $return['link'] = YourImageLibrary::createThumbnail($_FILES['Filedata']['tmp_name']);

 *

 */

 

$dir = "../../../uploads_dogs/";

// Make sure its uploaded

if (is_uploaded_file($_FILES['Filedata']['tmp_name'])) {		

	if (is_writable($dir)==FALSE){

		$error = 'Directory not writable.';

	}			

	if (!is_dir($dir)){

		mkdir($dir, 0777);

	}

		

	/*** FILE NAMES ***/
	$uniquename = uniqueFileName($dir,$_FILES['Filedata']['name']);

	$originalname = $uniquename;
	//$fullname = 'full'.$uniquename;
	$thumbname = 'thumb_'.$uniquename;
	$bigname = 'big_'.$uniquename;

	//if(file_exists($dir.$uniquename)){
	// Thumbnail
	//sslib_ScaleCopy($_FILES['Filedata']['tmp_name'], $dir.$thumbname, 0, 150);
	// Large Version
	//sslib_ScaleCopy($_FILES['Filedata']['tmp_name'], $dir.$bigname, 0, 300);

	/*** SAVE ORIGINAL ***/
	move_uploaded_file($_FILES['Filedata']['tmp_name'],$dir.$uniquename);	

	

	/*** SAVE THUMBNAIL	***/
	$image1 = new SimpleImage();
	$image1->load($dir.$uniquename);
	//$image1->resizeToHeight(150);
	$image1->resize(150,150);
	$image1->save($dir.$thumbname);
	

	/*** SAVE BIG ***/
	$image2 = new SimpleImage();
	$image2->load($dir.$uniquename);
	$image2->resizeToHeight(300);
	$image2->save($dir.$bigname);	

	$return['src'] = $dir.$uniquename;

	list($width, $height, $type, $attr) = getimagesize($dir.$thumbname);

	$imagewidth = $width;	

	//
	// CHECK IF THE IMAGE IS WIDER THAN
	//
	if($imagewidth > 400){	
		$image3 = new SimpleImage();
		$image3->load($dir.$bigname);
		$image3->resizeToWidth(400);
		$image3->save($dir.$bigname);
	}
	
	
	

	//}

}



if ($error) {



	$return = array(

		'status' => '0',

		'error' => $error

	);



} else {



	



	$return = array(

		'status' => '1',

		//'name' => $_FILES['Filedata']['name']

		'name' => $uniquename,

		'width' => $imagewidth

	);



	$_SESSION['imagename'] = $uniquename;

	

	// Our processing, we get a hash value from the file

	$return['hash'] = md5_file($_FILES['Filedata']['tmp_name']);



	// ... and if available, we get image data

	$info = @getimagesize($_FILES['Filedata']['tmp_name']);



	if ($info) {

		$return['width'] = $info[0];

		$return['height'] = $info[1];

		$return['mime'] = $info['mime'];

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
