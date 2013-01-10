<?
global $_SETTINGS;
?>
	<script>
	//$(document).ready(function() {
		function SmallFileBrowser($uploadDir,$fieldId){			
			window.open('<?=$_SETTINGS['website']?>admin/modules/file_manager/small_file_manager.php?f='+$uploadDir+'&fieldname='+$fieldId+'','Quick File Chooser','width=500,height=600,scrollbars=yes');
		}
	//});
	</script>
<?

?>	