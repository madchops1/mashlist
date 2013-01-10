<?
/*
 * Slideshow Functions FOR FRONT END
 */
global $_SETTINGS;
global $_REQUEST;

// ONLY SHOW ON SLIDESHOW PAGE TO OPTIMIZE LOADING TIMES
if($_REQUEST['page'] == $_SETTINGS['slideshow_page_clean_url']){
	$ImageSlideshow3 = new ImageSlideshow3();

	// SETTINGS
	if($_SETTINGS['slideshow_3_duration'] == ''){
		$_SETTINGS['slideshow_3_duration'] = 8000;
	}
	?>
	<script type="text/javascript" src="<?=$_SETTINGS['website'] ?>admin/modules/image_slideshow_3/scripts/nivo-slider/jquery.nivo.slider.pack.js"></script>
	<script type="text/javascript">
	$(window).load(function() {

		//$('$slider').fadeIn("1000");

		$('#slider').nivoSlider({
			effect:'fade', //Specify sets like: 'fold,fade,sliceDown'
			slices:1,
			animSpeed:400, //Slide transition speed
			pauseTime:4000
		});
	});
	</script>
<?
}
?>