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

class ImageSlideshow3 {

	var $auth;
	
	/**
	 *
	 * Class Constructor
	 *
	 */
	function ImageSlideshow3()
	{
		
		$this->auth = 0;		
	}
	

	
	/**
	 *
	 * Class Display Image Slider
	 *
	 */
	function DisplayImageSlideshow3($type='She Beads')
	{
		global $_SETTINGS;
		
		// PUBLISH PENDING SLIDES
		//publishPendingItems('image_slideshow_3_slides');
		
		// EXPIRE PUBLISHED SLIDES
		//expirePublishedItems('image_slideshow_3_slides');
		
		echo "	<div id='slider-wrapper'>";
        echo "	  	<div id='slider' class='nivoSlider'>";
						
						// DO IMAGES
						
						$select = 	"SELECT * FROM image_slideshow_3_slides WHERE ".
									"active='1' AND ".
									"status='Published' AND ".
									"type='".$type."' ORDER BY slide_id DESC";
						
						/*
						$select = 	"SELECT * FROM image_slideshow_3_slides WHERE ".
									"active='1' AND ".
									"status='Published' AND ".
									"type='".$type."' ORDER BY published_date DESC, slide_id DESC";
						*/
						
						
						$result = doQuery($select);
						$num = mysql_num_rows($result);
						$i = 0;
						while($i<$num){
							$row = mysql_fetch_array($result);
							
							if($row['link1'] == ""){
								$row['link1'] = "".$_SETTINGS['website']."items";
							}
							
							echo "<a href='".$row['link1']."'><img src='".$_SETTINGS['website']."uploads/".$row['image1']."' alt='' title='#htmlcaption".$i."' /></a>";
							$i++;
						}
						
		echo"		</div>";      

						// DO CAPTIONS
						$select = 	"SELECT * FROM image_slideshow_3_slides WHERE ".
									"active='1' AND ".
									"status='Published' AND ".
									"type='".$type."' ORDER BY slide_id DESC";
						$result = doQuery($select);
						$num = mysql_num_rows($result);
						$i = 0;
						while($i<$num){
							$row = mysql_fetch_array($result);
							if($row['text1'] != ""){
								echo "<div id='htmlcaption".$i."' class='nivo-html-caption'>";
								echo $row['text1'];
								echo "</div>";
							}
							$i++;
						}
        echo "	</div>";
	}
}
?>