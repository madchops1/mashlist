<?

@require_once '../../../includes/config.php';

global $_SETTINGS;
global $_SESSION;

/***	CATEGORY AJAX				********************************************************/
if(isset($_POST['GET_CATEGORY_SELECT'])){
	// FOR AJAX
	//die('MADE IT');
	//exit();
	// GET THE SELECT MENU
	//echo "<span id='".$_POST['GET_CATEGORY_SELECT']."cat'>";
	//echo "<button id='".$_POST['GET_CATEGORY_SELECT']."catsave'>Save</button><Br>";
	hierarchymultiselectTable('ecommerce_product_categories',''.$_POST['GET_CATEGORY_SELECT'].'categories','category_id','name','sort_level','ASC',0, 'ecommerce_product_category_relational','product_id',''.$_POST['GET_CATEGORY_SELECT'].'')."";
	//echo "</span>";
	
	$ajaxURL			= "modules/ecommerce/ecommerce_ajax.php";
	
	echo "<script>	
		 //$('#".$_POST['GET_CATEGORY_SELECT']."categories').focus();

	
		$('#".$_POST['GET_CATEGORY_SELECT']."categories').blur(function() {		
			// GET SELECTED CATEGORIES			
			
			//var categories = $(\"select[name='".$_POST['GET_CATEGORY_SELECT']."categories'] option:selected\").map(function(){return $(this).val();}).get();
			var categories = '';
			
			$(\"select[name='".$_POST['GET_CATEGORY_SELECT']."categories'] option:selected\").each(function () {
                categories += $(this).val() + ',';
              });

			
			// POST SAVE
			$.ajax({
			  type: 'POST',
			  url: '".$ajaxURL."',
			  data: 'SAVE_CATEGORY_UPDATE=1&product_id=".$_POST['GET_CATEGORY_SELECT']."&categories=' + categories + '',
			  success: function(data) {
				$('#cat-".$_POST['GET_CATEGORY_SELECT']."').html(data);
				$('#catselect-".$_POST['GET_CATEGORY_SELECT']."').html('');
				$('#cat-".$_POST['GET_CATEGORY_SELECT']."').css('visibility','visible');
				//$('body).css('display','none');
			  }
			});			
			
			$('#catselect-".$_POST['GET_CATEGORY_SELECT']."').html('<img src=\"images/zoomloader.gif\">');
			
			return false;
		});		
	</script>";
	
	//echo "hello";
	//echo "<script> alert('hello'); </script>";
	
	exit;
}

if(isset($_POST['SAVE_CATEGORY_UPDATE'])){
	// AJAX
	//die('MADE IT');
	//exit;
	// DELETE RELATIONS
	doQuery("DELETE FROM ecommerce_product_category_relational WHERE product_id='".$_POST['product_id']."'");
		
	$category = "";	
	$catstring = rtrim($_POST['categories'], ','); 
	$catarray = explode(",",$catstring);
	foreach($catarray AS $cat){
		$insert = "INSERT INTO ecommerce_product_category_relational SET product_id='".$_POST['product_id']."',category_id='".$cat."'";
		doQuery($insert);
		$category .= lookupDbValue('ecommerce_product_categories','name',$cat,'category_id').",";
		
	}
	
	//$category = lookupDbValue('ecommerce_product_categories','name',$category,'category_id');
	//if($category == ""){$category = "Uncategorized"; }
	echo "".trim($category,",")."";
	exit;
}

/*** 	UPDATE FIELD AJAX			********************************************************/
if(isset($_POST['UPDATE_FIELD'])){
	$update = "UPDATE ".$_POST['UPDATE_TABLE']." SET ".$_POST['UPDATE_FIELD']."='".$_POST['UPDATE_FIELD_VALUE']."' WHERE ".$_POST['UPDATE_ROW']."='".$_POST['UPDATE_ROW_ID']."'";
	doQuery($update);
	echo $update;
	exit;
}


?>