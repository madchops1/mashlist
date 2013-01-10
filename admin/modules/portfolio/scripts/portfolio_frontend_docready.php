<?
global $_SETTINGS;
global $_REQUEST;
if($_REQUEST['page'] == $_SETTINGS['portfolio_page_clean_url']){
	?>
	
		<script type="text/javascript" src="<?=$_SETTINGS['website'] ?>admin/modules/portfolio/scripts/jquery.fancybox-1.3.1/fancybox/jquery.mousewheel-3.0.2.pack.js"></script>
		<script type="text/javascript" src="<?=$_SETTINGS['website'] ?>admin/modules/portfolio/scripts/jquery.fancybox-1.3.1/fancybox/jquery.fancybox-1.3.1.js"></script>
		<link rel="stylesheet" type="text/css" href="<?=$_SETTINGS['website'] ?>admin/modules/portfolio/scripts/jquery.fancybox-1.3.1/fancybox/jquery.fancybox-1.3.1.css" media="screen" />
	
		<script>
		$(document).ready(function() {
				
				/** Examples - images
				 *  
				 *
				 *
					$("a.portfolioitem").fancybox({
						'titleShow'		: true,
						'transitionIn'	: 'fade',
						'transitionOut'	: 'fade',
						'titlePosition'	: 'over'
					});		
				*/
				
				$("a.portfolioitem").fancybox({
					'transitionIn'	: 'fade',
					'transitionOut'	: 'fade',
					'titleShow'		: false
				});		
				
		});		
		</script>
	<?
}
?>