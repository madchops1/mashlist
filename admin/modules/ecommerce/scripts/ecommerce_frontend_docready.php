<?
global $_SETTINGS;
global $_REQUEST;
?>



<?
if($_REQUEST['page'] == $_SETTINGS['product_detail_page_clean_url']){
?>	
	<link rel="stylesheet" type="text/css" href="<?=$_SETTINGS['website'] ?>admin/modules/portfolio/scripts/jquery.fancybox-1.3.1/fancybox/jquery.fancybox-1.3.1.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="<?=$_SETTINGS['website'] ?>admin/scripts/jquery/star-rating/jquery.rating.css" media="screen" />
	
	<script type="text/javascript" src="<?=$_SETTINGS['website'] ?>admin/scripts/jquery/jqzoom.pack.1.0.1.js"></script>	
	<script type="text/javascript" src="<?=$_SETTINGS['website'] ?>admin/scripts/jquery/star-rating/jquery.rating.js"></script>	
	<script type="text/javascript" src="<?=$_SETTINGS['website'] ?>admin/modules/portfolio/scripts/jquery.fancybox-1.3.1/fancybox/jquery.mousewheel-3.0.2.pack.js"></script>
	<script type="text/javascript" src="<?=$_SETTINGS['website'] ?>admin/modules/portfolio/scripts/jquery.fancybox-1.3.1/fancybox/jquery.fancybox-1.3.1.js"></script>
<?
}
?>

<script>

jQuery(document).ready(function(){
	
	<?
	if($_REQUEST['page'] == $_SETTINGS['product_detail_page_clean_url']){
	?>
		/*
		 *   Examples - images
		 * 	 Used on product detail pages
		 */
		$("a.productlightbox").fancybox({
			'titleShow'		: true,
			'transitionIn'	: 'elastic',
			'transitionOut'	: 'elastic',
			'titlePosition'	: 'over'
		});		
	<?
	}
	?>
	
	function SBFilterProducts(){
		$.ajax({
		  type: 'POST',
		  url: '<?=$_REQUEST['page'] ?>',
		  data: 'filter_products=1&filter_limit=more',
		  success: function(data) {
			$('#products').html(data);
			alert('Products Filtered.');
		  }
		});
	}
	
	
	
	function SBMoreResults(){
		$.ajax({
			type: 'POST',
			url: '<?=$_REQUEST['page'] ?>',
			data: 'filter_products=1&filter_limit=more',
			success: function(data){
				$('#products').html(data);
				alert('More Products Shown.');
		    }
		});
	}
	
	/*
	function SBFilterHESHEProducts($heshe){
		$.ajax({
			type: 'POST',
			url: '<?=$_REQUEST['page'] ?>',
			data: 'filter_products=1&filter_heshe=' + $heshe + '',
			success: function(data){
				$('#products').html(data);
				alert('FILTERED ' + $heshe + '.');
		    }
		});
	}
	*/
	
});

</script>