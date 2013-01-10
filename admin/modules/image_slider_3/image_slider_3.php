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
* Karl Steltenpohl Development LLC
* Web Business Framework
* Version 1.0
* Copyright 2010 Karl Steltenpohl Development All Rights Reserved
*
* Commercially Licensed 
* View License At: http://www.karlsdevelopment.com/web-business-framework/license
*
*************************************************************************************************************************************/


//
// Declare UserAccounts Class and Report Function
//
$ImageSlider3 = new ImageSlider3();
report($_REQUEST['REPORT'],$_REQUEST['SUCCESS']);
	
/***	Update An Account 				********************************************************/
if (isset($_POST["UPDATE"])){
	$error = 0;
		
	if($error == 0)
	{
		
		$_POST = escape_smart_array($_POST);
		
		if($_POST['image1'] != ""){
			$image1 = "image1='".basename($_POST['image1'])."',";
		}
		
		if($_POST['image2'] != ""){
			$image2 = "image2='".basename($_POST['image2'])."',";
		}
		
		if($_POST['image3'] != ""){
			$image3 = "image3='".basename($_POST['image3'])."',";
		}
		
		if($_POST['remove1'] != ""){
			$image1 = "image1='',";
		}
		
		if($_POST['remove2'] != ""){
			$image2 = "image2='',";
		}
		
		if($_POST['remove3'] != ""){
			$image3 = "image3='',";
		}
		
		// update  record
		$select =	"UPDATE image_slider_3 SET ".
					"".$image1."".
					"".$image2."".
					"".$image3."".
					"text1='".$_POST['text1']."',".
					"text2='".$_POST['text2']."',".
					"text3='".$_POST['text3']."',".
					"title1='".$_POST['title1']."',".
					"title2='".$_POST['title2']."',".
					"title3='".$_POST['title3']."',".
					"link1='".$_POST['link1']."',".
					"link2='".$_POST['link2']."',".
					"link3='".$_POST['link3']."',".
					"image1_w='".$_POST['image1_w']."',".
					"image1_h='".$_POST['image1_h']."',".
					"image1_x='".$_POST['image1_x']."',".
					"image1_y='".$_POST['image1_y']."',".
					"image1_x2='".$_POST['image1_x2']."',".
					"image1_y2='".$_POST['image1_y2']."',".
					"image2_w='".$_POST['image2_w']."',".
					"image2_h='".$_POST['image2_h']."',".
					"image2_x='".$_POST['image2_x']."',".
					"image2_y='".$_POST['image2_y']."',".
					"image2_x2='".$_POST['image2_x2']."',".
					"image2_y2='".$_POST['image2_y2']."',".
					"image3_w='".$_POST['image3_w']."',".
					"image3_h='".$_POST['image3_h']."',".
					"image3_x='".$_POST['image3_x']."',".
					"image3_y='".$_POST['image3_y']."',".
					"image3_x2='".$_POST['image3_x2']."',".
					"image3_y2='".$_POST['image3_y2']."'".
					"".$_SETTINGS['demosql']."".
					" WHERE id='1'";	

		doQuery($select);
		header("Location: {$_SERVER["PHP_SELF"]}?REPORT=Image Slideshow Updated Successfully&SUCCESS=1&VIEW={$_GET["VIEW"]}");
		exit;
	}
}


/*** SEARCH/VIEW ACCOUNTS				********************************************************/

//
// GET IMAGE SLIDER DATA
//
$select = "SELECT * FROM image_slider_3 WHERE id='1'";
$result = doQuery($select);
$_POST = mysql_fetch_array($result);
?>

	<form action="" method="post" enctype="multipart/form-data" name="wesform" id="wesform">
		<?
		echo tableHeader("Image Slideshow",3,'100%');
		?>
		<tr>
			<th align='center' style='text-align:center;'>
			Slide 1
			</th>
			<th align='center' style='text-align:center;'>
			Slide 2
			</th>
			<th align='center' style='text-align:center;'>
			Slide 3
			</th>
		</tr>		
		<tr>
			<td align='center'>
				<br>
				<div style="width:<? echo ($_SETTINGS['image_width'] / 3); ?>px;height:<? echo ($_SETTINGS['image_height'] / 3); ?>px;overflow:hidden; border:1px solid #000;">
					<a id="link1" href="<?=$_SETTINGS['website']."uploads/".$_POST['image1']?>">
						<img src="<?=$_SETTINGS['website']."uploads/".$_POST['image1']?>" id="preview1" />
					</a>
				</div>
							
				
				<?
				if($_SETTINGS['debug'] == 1){
					$debugvisible = 'visibility:visible;';
				} else {
					$debugvisible = 'visibility:hidden;';
				}
				?>				
							
							
				<input style='<?=$debugvisible?>' type='text' name='image1_w' id='image1_w' value='<?=$_POST['image1_w']?>' size="4" />
				<input style='<?=$debugvisible?>' type='text' name='image1_h' id='image1_h' value='<?=$_POST['image1_h']?>' size="4" />
				<input style='<?=$debugvisible?>' type='text' name='image1_x' id='image1_x' value='<?=$_POST['image1_x']?>' size="4" />
				<input style='<?=$debugvisible?>' type='text' name='image1_y' id='image1_y' value='<?=$_POST['image1_y']?>' size="4" />
				<input style='<?=$debugvisible?>' type='text' name='image1_x2' id='image1_x2' value='<?=$_POST['image1_x2']?>' size="4" />
				<input style='<?=$debugvisible?>' type='text' name='image1_y2' id='image1_y2' value='<?=$_POST['image1_y2']?>' size="4" />
				
				<?
				if($_POST['image1'] != ''){
				?>
				Click on the image to crop &amp; resize.
				<Br><input type='checkbox' name='remove1' value='1' /> Clear Image<Br><Br>
				<?
				}
				?>
			</td>
			<td align='center'>		
				<br>
				<div style="width:<? echo ($_SETTINGS['image_width'] / 3); ?>px;height:<? echo ($_SETTINGS['image_height'] / 3); ?>px;overflow:hidden; border:1px solid #000;">
					<a id="link2" href="<?=$_SETTINGS['website']."uploads/".$_POST['image2']?>">
						<img src="<?=$_SETTINGS['website']."uploads/".$_POST['image2']?>" id="preview2" />
					</a>
				</div>
				
				<input style='<?=$debugvisible?>' type='text' name='image2_w' id='image2_w' value='<?=$_POST['image2_w']?>' size="4" />
				<input style='<?=$debugvisible?>' type='text' name='image2_h' id='image2_h' value='<?=$_POST['image2_h']?>' size="4" />
				<input style='<?=$debugvisible?>' type='text' name='image2_x' id='image2_x' value='<?=$_POST['image2_x']?>' size="4" />
				<input style='<?=$debugvisible?>' type='text' name='image2_y' id='image2_y' value='<?=$_POST['image2_y']?>' size="4" />
				<input style='<?=$debugvisible?>' type='text' name='image2_x2' id='image2_x2' value='<?=$_POST['image2_x2']?>' size="4" />
				<input style='<?=$debugvisible?>' type='text' name='image2_y2' id='image2_y2' value='<?=$_POST['image2_y2']?>' size="4" />
				
				<?
				if($_POST['image2'] != ''){
				?>
				Click on the image to crop &amp; resize.
				<Br><input type='checkbox' name='remove2' value='1' /> Clear Image<Br><Br>
				<?
				}
				?>
			</td>
			<td align='center'>
				<br>
				<div style="width:<? echo ($_SETTINGS['image_width'] / 3); ?>px;height:<? echo ($_SETTINGS['image_height'] / 3); ?>px;overflow:hidden; border:1px solid #000;">
					<a id="link3" href="<?=$_SETTINGS['website']."uploads/".$_POST['image3']?>">
						<img src="<?=$_SETTINGS['website']."uploads/".$_POST['image3']?>" id="preview3" />
					</a>
				</div>
				
				<input style='<?=$debugvisible?>' type='text' name='image3_w' id='image3_w' value='<?=$_POST['image3_w']?>' size="4" />
				<input style='<?=$debugvisible?>' type='text' name='image3_h' id='image3_h' value='<?=$_POST['image3_h']?>' size="4" />
				<input style='<?=$debugvisible?>' type='text' name='image3_x' id='image3_x' value='<?=$_POST['image3_x']?>' size="4" />
				<input style='<?=$debugvisible?>' type='text' name='image3_y' id='image3_y' value='<?=$_POST['image3_y']?>' size="4" />
				<input style='<?=$debugvisible?>' type='text' name='image3_x2' id='image3_x2' value='<?=$_POST['image3_x2']?>' size="4" />
				<input style='<?=$debugvisible?>' type='text' name='image3_y2' id='image3_y2' value='<?=$_POST['image3_y2']?>' size="4" />
				
				<?
				if($_POST['image3'] != ''){
				?>
				Click on the image to crop &amp; resize.
				<Br><input type='checkbox' name='remove3' value='1' /> Clear Image<Br><Br>
				<?
				}
				?>
			</td>
		</tr>
		<tr>
			<td>
				<input type="text" name="title1" value="<?=$_POST['title1']?>" style="float:none;" /> Title
			</td>
			<td>
				<input type="text" name="title2" value="<?=$_POST['title2']?>" style="float:none;" /> Title
			</td>
			<td>
				<input type="text" name="title3" value="<?=$_POST['title3']?>" style="float:none;" /> Title
			</td>
		</tr>
		<tr>
			<td>	
				<input style="float:none;" type="text" name="image1" value="<?=$_POST['image1']?>" /><button type="button" onClick="SmallFileBrowser('../uploads/','image1')">Choose Image...</button><br><br>			
			</td>
			<td>
				<input style="float:none;" type="text" name="image2" value="<?=$_POST['image2']?>" /><button type="button" onClick="SmallFileBrowser('../uploads/','image2')">Choose Image...</button><br><br>			
			</td>
			<td>
				<input style="float:none;" type="text" name="image3" value="<?=$_POST['image3']?>" /><button type="button" onClick="SmallFileBrowser('../uploads/','image3')">Choose Image...</button><br><br>			
			</td>
		</tr>
		<tr>
			<td>
				<input type="text" name="link1" value="<?=$_POST['link1']?>" style="float:none;" /> Link
			</td>
			<td>
				<input type="text" name="link2" value="<?=$_POST['link2']?>" style="float:none;" /> Link
			</td>
			<td>
				<input type="text" name="link3" value="<?=$_POST['link3']?>" style="float:none;" /> Link
			</td>
		</tr>
		<tr>
			<td>
				<textarea name="text1" style="height:70px;"><?=$_POST['text1']?></textarea><br>
			</td>
			<td>
				<textarea name="text2" style="height:70px;"><?=$_POST['text2']?></textarea><br>
			</td>
			<td>
				<textarea name="text3" style="height:70px;"><?=$_POST['text3']?></textarea><br>
			</td>
		</tr>
		</tbody>
	</table>
	

	
	<div id="submit">
	<input type="hidden" name="VALUE" value="<?=$_REQUEST['VALUE']?>" />
	<?
	$button = "Update";
	echo "<INPUT TYPE=SUBMIT NAME=\"".strtoupper(str_replace(" ", "_", $button))."\" VALUE=\"$button\">";
	?>
	</div>
	
	</form>
	
	
	<?

?>
