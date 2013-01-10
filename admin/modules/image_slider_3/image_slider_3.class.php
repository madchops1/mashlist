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

class ImageSlider3 {

	var $auth;
	
	/**
	 *
	 * Class Constructor
	 *
	 */
	function ImageSlider3(){
		
		$this->auth = 0;		
	}
	
	/**
	 *
	 * Class Display Image Slider
	 *
	 */
	function DisplayImageSlider3(){
	
		global $_SETTINGS;
		$select = "SELECT * FROM image_slider_3 WHERE id='1'";
		$result = doQuery($select);
		$row = mysql_fetch_array($result);
	
		$flag = $_SETTINGS['image_slideshow_c_clean_url'];
		if($flag == $_REQUEST['page']){
	
			?>
			<div id='image-slideshow'>
				<ul id="slides">
					
					<?
					if($row['image1'] != ''){	
					?>
					<li style="position: absolute; top: 0px; left: 0px; display: list-item; z-index: 4;">
						<div style="width:<? echo ($_SETTINGS['image_width']); ?>px;height:<? echo ($_SETTINGS['image_height']); ?>px; overflow:hidden;">
							<a href="<?=$row['link1']?>" >
							<img id='slide1' src="<?=$_SETTINGS['website'] ?>uploads/<?=$row['image1']?>" alt="" />					
							</a>
						</div>
						
						<script>
						<?
						// INITIAL SETTINGS IMAGE 1
						$img_path = lookupDbValue('image_slider_3', 'image1', '1', 'id');
						list($width, $height) = getimagesize(''.$_SETTINGS['website'].'uploads/'.$row['image1'].'');
						$w = lookupDbValue('image_slider_3', 'image1_w', '1', 'id');
						$h = lookupDbValue('image_slider_3', 'image1_h', '1', 'id');
						$x = lookupDbValue('image_slider_3', 'image1_x', '1', 'id');
						$y = lookupDbValue('image_slider_3', 'image1_y', '1', 'id');
						if($w != ''){
						?>
							var width = <? echo ($width); ?>;
							var height = <? echo ($height); ?>;
						
							var rx = <? echo ($_SETTINGS['image_width']); ?> / <?=$w?>;
							var ry = <? echo ($_SETTINGS['image_height']); ?> / <?=$h?>;
						
							jQuery('#slide1').css({
								width: Math.round(rx * width) + 'px',
								height: Math.round(ry * height) + 'px',
								marginLeft: '-' + Math.round(rx * <?=$x?>) + 'px',
								marginTop: '-' + Math.round(ry * <?=$y?>) + 'px'
							});
						<?
						}
						?>
						</script>
						<span class="slide_caption"><?=$row['text1']?></span>
					</li>
					<?
					}
					?>
					
					<?
					if($row['image2'] != ''){
					?>
					<li style="position: absolute; top: 0px; left: 0px; display: none; z-index: 3;">
						<div style="width:<? echo ($_SETTINGS['image_width']); ?>px;height:<? echo ($_SETTINGS['image_height']); ?>px; overflow:hidden;">
							<a href="<?=$row['link2']?>">
							<img id='slide2' src="<?=$_SETTINGS['website'] ?>uploads/<?=$row['image2']?>" alt="" />
							</a>
						</div>
						<script>
						<?
						// INITIAL SETTINGS IMAGE 2
						$img_path = lookupDbValue('image_slider_3', 'image2', '1', 'id');
						list($width, $height) = getimagesize(''.$_SETTINGS['website'].'uploads/'.$row['image2'].'');
						$w = lookupDbValue('image_slider_3', 'image2_w', '1', 'id');
						$h = lookupDbValue('image_slider_3', 'image2_h', '1', 'id');
						$x = lookupDbValue('image_slider_3', 'image2_x', '1', 'id');
						$y = lookupDbValue('image_slider_3', 'image2_y', '1', 'id');
						if($w != ''){
						?>
							var width = <? echo ($width); ?>;
							var height = <? echo ($height); ?>;
						
							var rx = <? echo ($_SETTINGS['image_width']); ?> / <?=$w?>;
							var ry = <? echo ($_SETTINGS['image_height']); ?> / <?=$h?>;
						
							jQuery('#slide2').css({
								width: Math.round(rx * width) + 'px',
								height: Math.round(ry * height) + 'px',
								marginLeft: '-' + Math.round(rx * <?=$x?>) + 'px',
								marginTop: '-' + Math.round(ry * <?=$y?>) + 'px'
							});
						<?
						}
						?>
						</script>					
						<span class="slide_caption"><?=$row['text2']?></span>					
					</li>
					<?
					}
					?>
					
					<?
					if($row['image3'] != ''){
					?>
					<li style="position: absolute; top: 0px; left: 0px; display: none; z-index: 2;">
						<div style="width:<? echo ($_SETTINGS['image_width']); ?>px;height:<? echo ($_SETTINGS['image_height']); ?>px; overflow:hidden;">
							<a href="<?=$row['link3']?>">
							<img id='slide3' src="<?=$_SETTINGS['website'] ?>uploads/<?=$row['image3']?>" alt="" />
							</a>
						</div>
						<script>
						<?
						// INITIAL SETTINGS IMAGE 3
						$img_path = lookupDbValue('image_slider_3', 'image3', '1', 'id');
						list($width, $height) = getimagesize(''.$_SETTINGS['website'].'uploads/'.$row['image3'].'');
						$w = lookupDbValue('image_slider_3', 'image3_w', '1', 'id');
						$h = lookupDbValue('image_slider_3', 'image3_h', '1', 'id');
						$x = lookupDbValue('image_slider_3', 'image3_x', '1', 'id');
						$y = lookupDbValue('image_slider_3', 'image3_y', '1', 'id');
						if($w != ''){
						?>
							var width = <? echo ($width); ?>;
							var height = <? echo ($height); ?>;
						
							var rx = <? echo ($_SETTINGS['image_width']); ?> / <?=$w?>;
							var ry = <? echo ($_SETTINGS['image_height']); ?> / <?=$h?>;
						
							jQuery('#slide3').css({
								width: Math.round(rx * width) + 'px',
								height: Math.round(ry * height) + 'px',
								marginLeft: '-' + Math.round(rx * <?=$x?>) + 'px',
								marginTop: '-' + Math.round(ry * <?=$y?>) + 'px'
							});
						<?
						}
						?>
						</script>	
						<span class="slide_caption"><?=$row['text3']?></span>						
					</li>
					<?
					}
					?>
					
				</ul>	
				
				
				<div id="slider_copy" class="clearfix">				
					<div style="display: block;" id="project_caption"></div>				
					<div id="slide_navigation" class="clearfix"></div>
				</div>
			</div>
			<?			
		}		
	}
}
?>