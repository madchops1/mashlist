<?

/** WES ECOMMERCE CLASS
 *
 * 
 *
 */
class Ecommerce
{

	var $auth;
	var $currency;
	var $quickbooks_source_options;
	
	/** CLASS CONSTRUCTOR
	 *
	 *
	 *
	 */
	function Ecommerce()
	{
	
		global $_SETTINGS;
	
		/*************************
		* Class Constructor
		*************************/
		$this->auth = 0;
		$this->currency = lookupDbValue('ecommerce_currencies', 'symbol', $_SETTINGS['currency'], 'currency_id');
		$this->quickbooks_source_options = array(
			'certificate' => ''.$_SETTINGS['DOC_ROOT'].'includes/quickbooks_online.pem', 
			'connection_ticket' => ''.$_SETTINGS['qboe_connection_ticket'].'', 
			'application_login' => ''.$_SETTINGS['qboe_application_login'].'', 
			'application_id' => ''.$_SETTINGS['qboe_application_id'].''
			);
			
		//die($_SETTINGS['currency']);
		//exit()
		
	}
	
	function oencode($array)
	{
		foreach ($array as $key => $value)
		{
			$array[$key] = str_replace("&Otilde;","'",htmlentities($array[$key])); // COMMA
		}
		return $array;		
	}
		
	/** SHOPPING CART BOX DISPLAY
	 *
	 * 
	 * Shopping Cart information and Product drag and drop box
	 *
	 */
	function ShoppingCartBox()
	{
		global $_SETTINGS;

		if($this->checkCartEmpty() == false){ $links = true; }
		
		echo "<div class=\"shoppingcartbox droppable\">"; 		
		echo "	<ul>";		
		echo "		<li class='image'><a></a></li>";
		echo "		<li class='add'>";
		
		if($links == true){ echo "<a href='".$_SETTINGS['website']."".$_SETTINGS['shopping_cart_page_clean_url']."'>"; }
		echo "		My Cart";
		if($links == true){ echo "</a>"; }
		
		echo "		</li>";	
		$totals = $this->calculateTotals();
		
		echo "		<li>";
		if($links == true){ echo "<a href='".$_SETTINGS['website']."".$_SETTINGS['shopping_cart_page_clean_url']."'>"; }
		echo 		$this->currency.money_format('%i',$totals[0]);
		if($links == true){ echo "</a>"; }
		echo "		</li>";
		
		// TESTING
		$sessionrandomphrase = md5($_SETTINGS['session_random_phrase']);
		//echo "		<li>CART ID: ".$_SESSION['shoppingcart-'.$sessionrandomphrase.'']."</li>";
		//echo "		<li>PHRASE: ".$sessionrandomphrase."</li>";
		//echo "		<li>PHRASE: ".$sessionrandomphrase."</li>";
		
		echo "	</ul>";		
		echo "</div>";	
		
		echo "<script>";		
		// IF CART IS DROPPABLE
		if($_SETTINGS['ecommerce_droppable'] == '1'){
			// MENU FOLLOW
			echo 	"	var name = '.shoppingcartbox'; ". 
					"	var menuYloc = null; ".
					"	menuYloc = parseInt($(name).css('top').substring(0,$(name).css('top').indexOf('px'))); ".  
					"	$(window).scroll(function () { ".  
					"		var offset = menuYloc+$(document).scrollTop()+'px'; ".  
					"		$(name).animate({top:offset},{duration:100,queue:false}); ".  
					"	});  ";
			
			// DROP BOX
			echo 	"	function fnremove(){ ".
					"		$('.shoppingcartbox').removeClass('dropped'); ".
					"	} ".
					"	$(function() { ".
					"		$('.draggable').draggable({ ".
					"			'revert':true,".
					"			'zIndex': 3800,".
					"			'opacity': 0.95,".
					"			'helper':'clone',".
					"			'containment': 'document'".
					"		}); ".
					"		$('.droppable').droppable({ ".
					"			hoverClass: 'carthover', ".
					"			accept: '.product', ".
					"			activeClass: 'carthighlight', ".
					"			tolerance: 'pointer', ".
					"			drop: function(event, ui) { ".
					"				ui.helper.fadeOut(); ".
					"				$('.shoppingcartbox').addClass('dropped'); ".
					"				setTimeout('fnremove()', 600); ".
					"			} ".
					"		}); ".
					"}	); ";
		}		
		echo "</script> ";	
	}

	/** MASK CC
	 *
	 * 
	 *
	 */
	function maskCreditCard($str,$start = 0,$length = null)
	{
        $mask = preg_replace ( "/\S/", "*", $str );
        if ( is_null ( $length )) {
            $mask = substr ( $mask, $start );
            $str = substr_replace ( $str, $mask, $start );
        } else {
            $mask = substr ( $mask, $start, $length );
            $str = substr_replace ( $str, $mask, $start, $length );
        }
        return $str;
    }
	
	/** GET THE TOPMOST IMAGE FOR THE PRODUCT ACCORDING TO SORTORDER
	 *
	 *
	 *
	 */
	function getTopMostProductImage($product_id)
	{
		$selimage = "SELECT * FROM ecommerce_product_images WHERE ".
					"active='1' AND ".
					"product_id='".$product_id."' ".
					"ORDER BY sort_level ASC LIMIT 1";
		$resimage = doQuery($selimage);
		$rowimage = mysql_fetch_array($resimage);
		return $rowimage['image'];
	}
	
	/** GET THE MAIN IMAGE FOR THE PRODUCT ACCORDING TO THE SETTINGS
	 *
	 *
	 * Uses $theimage which is the normal image file in the uploads-products/ folder
	 *
	 */
	function getMainImage($theimage)
	{
		global $_POST;
		global $_REQUEST;
		global $_SETTINGS;
		
		// GET MAIN IMAGE
		$mainimagearray	= explode(".",urldecode($theimage));				
		$mainimagesize  = lookupDbValue('ecommerce_thumbnail_sizes', 'name', $_SETTINGS['detail_page_main_image_size'], 'size_id');
		
		$mainimage  = $mainimagearray[0]."_w".$mainimagesize.".".$mainimagearray[1];
		$mainimage1	= $mainimagearray[0]."_w150.".$mainimagearray[1];
		$mainimage2	= $mainimagearray[0]."_w300.".$mainimagearray[1];
		$mainimage3	= $mainimagearray[0]."_w600.".$mainimagearray[1];
		$mainimage4 = $mainimagearray[0]."_w1024.".$mainimagearray[1];
		
		// CHECK IF THE FILE FORMATED TO THE SELECTED DISPLAY SIZE EXISTS
		$filecheck = $_SERVER['DOCUMENT_ROOT'].$_SETTINGS['website_path']."uploads-products/wpThumbnails/".$mainimage."";
		
		// SET THE THUMBNAIL FOLDER FOR ALL RESIZED / REFORMATED IMAGES
		$thumbnailext = "wpThumbnails/";
		
		if(!file_exists($filecheck)){
			//
			// IF FILE DOESNT EXIST GO TO THE NEXT SIZE DOWN
			//
			if($mainimagesize == '1024'){
				$mainimage = $mainimage3;
			}
			
			if($mainimagesize == '600'){
				$mainimage = $mainimage2;
			}
			
			if($mainimagesize == '300'){
				$mainimage = $mainimage1;
			}
			
			//
			// IF NO SIZES EXIST THEN USE THE ORIGINAL
			//
			$filecheck = $_SERVER['DOCUMENT_ROOT'].$_SETTINGS['website_path']."uploads-products/wpThumbnails/".$mainimage."";
			if(!file_exists($filecheck)){
				// GET ORIGINAL
				$mainimage = $theimage;
				$thumbnailext = "";
			}
		}		
		return $thumbnailext.$mainimage;
	}
	
	/** GET THE LARGEST IMAGE FOR THE PRODUCT
	 *
	 *
	 * Uses $theimage which is the normal image file in the uploads-products/ folder
	 *
	 */
	function getLargeImage($theimage)
	{
		global $_POST;
		global $_REQUEST;
		global $_SETTINGS;
		
		// GET MAIN IMAGE
		$mainimagearray	= explode(".",urldecode($theimage));				
		$mainimagesize  = lookupDbValue('ecommerce_thumbnail_sizes', 'name', $_SETTINGS['detail_page_main_image_size'], 'size_id');
		
		$mainimage  = $mainimagearray[0]."_w".$mainimagesize.".".$mainimagearray[1];
		
		$mainimage1	= $mainimagearray[0]."_w150.".$mainimagearray[1];
		$mainimage2	= $mainimagearray[0]."_w300.".$mainimagearray[1];
		$mainimage3	= $mainimagearray[0]."_w600.".$mainimagearray[1];
		$mainimage4 = $mainimagearray[0]."_w1024.".$mainimagearray[1];
		
		// CHECK IF FILE EXISTS
		$filecheck	 	= $_SERVER['DOCUMENT_ROOT'].$_SETTINGS['website_path']."uploads-products/wpThumbnails/".$mainimage."";
		$filecheck4 	= $_SERVER['DOCUMENT_ROOT'].$_SETTINGS['website_path']."uploads-products/wpThumbnails/".$mainimage4."";
		$filecheck3 	= $_SERVER['DOCUMENT_ROOT'].$_SETTINGS['website_path']."uploads-products/wpThumbnails/".$mainimage3."";
		$filecheck2 	= $_SERVER['DOCUMENT_ROOT'].$_SETTINGS['website_path']."uploads-products/wpThumbnails/".$mainimage2."";
		
		// CHECK IF LARGEST EXISTS
		if(file_exists($filecheck4)){
			$largeimage = $mainimage4;
		}
		
		// IF NO 1024 THEN CHECK FOR 600
		if($largeimage == "" AND file_exists($filecheck3)){
			$largeimage = $mainimage3;
		}
		
		// IF NO 600 THEN CHECK FOR 300
		//if($largeimage == "" AND file_exists($filecheck2)){
		//	$largeimage = $mainimage2;
		//}

		// IF NO IMAGE YET USE THE ORIGINAL
		if($largeimage == ""){
			$largeimage = $theimage;
			$thumbnailext = "";
		} else {
			$thumbnailext = "wpThumbnails/";
		}
		
		return $thumbnailext.$largeimage;
	}
	
	/** GET THE SMALLEST IMAGE FOR THE PRODUCT // 94px
	 *
	 *
	 * Uses $theimage which is the normal image file in the uploads-products/ folder
	 *
	 */
	function getSmallImage($theimage)
	{
	
		global $_POST;
		global $_REQUEST;
		global $_SETTINGS;
		
		// GET SMALL IMAGE
		$smallimagearray	= explode(".",urldecode($theimage));				
		$smallimagesize  = "94";
		
		$image  = $smallimagearray[0]."_w".$smallimagesize.".".$smallimagearray[1];
	
		// CHECK IF FILE EXISTS
		$filecheck	 	= $_SERVER['DOCUMENT_ROOT'].$_SETTINGS['website_path']."uploads-products/wpThumbnails/".$image."";
		
		
		// CHECK IF LARGEST EXISTS
		if(!file_exists($filecheck)){
			// IF FILE DOES NOT EXIST THEN LOOK FOR 150px VERSION
			$image150 = $smallimagearray[0]."_w150.".$smallimagearray[1];
			if(file_exists($_SERVER['DOCUMENT_ROOT'].$_SETTINGS['website_path']."uploads-products/wpThumbnails/".$image150."")){
				$smallimage = $image150;
			}
			
			// IF STILL NO SMALL IMAGE THEN CHECK FOR 300px VERSION
			$image300 = $smallimagearray[0]."_w300.".$smallimagearray[1];
			if($smallimage == "" AND file_exists($_SERVER['DOCUMENT_ROOT'].$_SETTINGS['website_path']."uploads-products/wpThumbnails/".$image300."")){
				$smallimage = $image300;
			}
			
			// IF STILL NO SMALL IMAGE THEN CHECK FOR 600px VERSION
			$image600 = $smallimagearray[0]."_w600.".$smallimagearray[1];
			if($smallimage == "" AND file_exists($_SERVER['DOCUMENT_ROOT'].$_SETTINGS['website_path']."uploads-products/wpThumbnails/".$image600."")){
				$smallimage = $image600;
			}
			
			// IF STILL NO SMALL IMAGE THEN CHECK FOR 1024px VERSION
			$image1024 = $smallimagearray[0]."_w1024.".$smallimagearray[1];
			if($smallimage == "" AND file_exists($_SERVER['DOCUMENT_ROOT'].$_SETTINGS['website_path']."uploads-products/wpThumbnails/".$image1024."")){
				$smallimage = $image1024;
			}

		} else {
			// IF SMALL FILE DID EXIST THEN SET THE 94 PX
			$smallimage = $image;
		}
		
		
		$thumbnailext = "wpThumbnails/";
		return $thumbnailext.$smallimage;	
	}
	
	/** DISPLAY PRODUCTS
	 *
	 *
	 * Display the products 
	 *
	 */
	function SBSearchAndDisplayProducts($implicit=0)
	{
		global $_POST;
		global $_REQUEST;
		global $_SETTINGS;		
		global $_SESSION;
		$display = 0;
		
		// CHECK IS SET TO DISPLAY
		$flag = $_SETTINGS['shopping_cart_page_clean_url'];
		if($flag == $_REQUEST['page']){		
			$display = 1;
		}		
		// CHECK IF IMPLICIT DISPLAY
		if($implicit == 1){
			$display = 1;
		}
		// IF DISPLAY TRUE
		if($display == 1){
		
			echo "<div class='filter-products-topbar'>";
					// KEYWORD BOX
					$this->searchKeywords();
					// PRICE RANGE
					$this->filterPrice();
					// SORT PRODUCTS
					$this->sortProducts();
			echo "</div>";
			
			echo 	"<div class='search-leftcolumn'>";			
				//
				// PRODUCT FILTER
				//
				$this->SBfilterProductForm();			
			echo 	"</div>";
			
			echo 	"<div class='search-rightcolumn'>";
				// BEGIN PRODUCTS BOX
				echo "<div class='products' id='products'>";
				$this->SBajaxProducts();
				echo "</div>";
				//$select = "SELECT * FROM ecommerce_products WHERE active='1' AND status='Published'";
				//$result = doQuery($select);
				//$totalProductNum = mysql_num_rows($result);
			echo "</div>"; // end right column
		}//display	
	}
	
	/** SET SEARCHING DEFAULTS
	 *
	 *
	 *
	 *
	 */	 
	function setDefaults()
	{
		global $_SESSION;
		global $_SETTINGS;
		// FILTER DEFALUT LIMIT
		if($_SESSION['product_filter_limit'] == ""){ $_SESSION['product_filter_limit'] = $_SETTINGS['products_per_page']; }	
		// FILTER DEFAULT LIMIT START
		if($_SESSION['product_filter_limit_start'] == ""){ $_SESSION['product_filter_limit_start'] = 0; }
		// DEFAULT FILTER SORT ASC / DESC
		if($_SESSION['product_filter_sort'] == ""){	$_SESSION['product_filter_sort'] = "ASC"; }		
		// DEFAULT FILTER ORDER BY
		if($_SESSION['product_filter_order'] == ""){ $_SESSION['product_filter_order'] = "ep.sort_level"; }		
		// DEFAULT PRICE RAGE 
		if($_SESSION['product_filter_price_low'] == ""){
			$_SESSION['product_filter_price_low'] = "0";
		}
		// GET THE LARGEST PRICE IN THE DATABASE
		$select = "SELECT price FROM ecommerce_products ORDER BY price DESC LIMIT 1";
		$result = doQuery($select);
		$row = mysql_fetch_array($result);
		$hi = $row['price'];
		if($_SESSION['product_filter_price_high'] == ""){
			$_SESSION['product_filter_price_high'] = $hi;
		}
	}
	
	/** RE-SET SEARCH DEFAULTS
	 *
	 *
	 *
	 *
	 */
	function resetDefaults()
	{
		global $_SESSION;
		global $_SETTINGS;
		// FILTER DEFALUT LIMIT
		$_SESSION['product_filter_limit'] = $_SETTINGS['products_per_page'];
		// FILTER DEFAULT LIMIT START
		$_SESSION['product_filter_limit_start'] = 0;
		// DEFAULT FILTER SORT ASC / DESC
		$_SESSION['product_filter_sort'] = "ASC";	
		// DEFAULT FILTER ORDER BY
		$_SESSION['product_filter_order'] = "ep.sort_level";
		//$_SESSION['product_filter_order'] = "epcr.sort_level";
		// DEFAULT PRICE RAGE 		
		$_SESSION['product_filter_price_low'] = "0";		
		// GET THE LARGEST PRICE IN THE DATABASE
		$select = "SELECT price FROM ecommerce_products ORDER BY price DESC LIMIT 1";
		$result = doQuery($select);
		$row = mysql_fetch_array($result);
		$hi = $row['price'];
		$_SESSION['product_filter_price_high'] = $hi;
		// KEYWORDS
		$_SESSION['product_filter_keywords'] = "";
	}
	
	/** FILTER PRODUCTS FOR AJAX FUNCTION
	 *
	 *
	 *
	 */
	function FilterProductsAjax()
	{
		global $_POST;
		global $_SETTINGS;
		global $_SESSION;
		global $_REQUEST;
		
		$this->setDefaults();
		
		//die("made it aaa");
		//exit;		
			
		// FILTERING CATEGORY
		// BEFOR IF POST FILTER PRODUCTS // THIS IS TECHNICALLY A GET
		// IF FORM 1 EXPLODE IT TO CHECK FOR CATEGORIES AND SUCH
		if (isset($_REQUEST['FORM1'])){			
			$form1array = explode(":",$_REQUEST['FORM1'],2);
			$form1array = explode("-",$_REQUEST['FORM1'],2);
			$category = $form1array[0];
			$category_id = $form1array[1];
			
			
			//$pageNumber = $form1array[1];			
			// SET TITLE AS CATEGORY
			//echo "<br>TOP: $category<br>";
			$title = $category;
			
			// CHECK IF A CATEGORY
			$sel = "SELECT * FROM ecommerce_product_categories WHERE category_id='".$category_id."' LIMIT 1";
			$res = doQuery($sel);
			
			// IT IS A CATEGORY SO GET THE ID TO GET THE CATEGORY FROM THE RELATIONAL CAT ARCH.
			//$ro = mysql_fetch_array($res);
			$ccategory_id = $category_id;	
			
			// SESSION product_filter_set_category holds the category_id. This condition happens if the category changes only.
			//echo "$ccategory_id <br>";
			if($_SESSION['product_filter_set_category'] != $ccategory_id){ 
				
				$this->resetDefaults();
			
				$_SESSION['product_filter_set_category'] = $ccategory_id;
				//echo "<br>TOP: $ccategory_id<br>";
				$_SESSION['product_filter_category_id'] = $ccategory_id;
				$_SESSION['product_filter_limit'] = $_SETTINGS['products_per_page'];
				$_SESSION['product_filter_limit_start'] = 0;
				//echo "This Happened<br>";
			}
			
			if($_REQUEST['FORM1'] == "All"){
				$this->resetDefaults();
			}
			
		} else {
			$_SESSION['product_filter_category_id'] = "";
		}
		
		// IF FILTERING THE PRODUCTS
		if($_POST['filter_products'] == '1'){
			
			// TESTING
			//echo "<br>MADE IT TO THE PRODUCT FILTER";
			//echo "<br>FORM1 ".$_REQUEST['FORM1']."";
			
			// IF LOADING MORE PRODUCTS
			if($_POST['filter_limit'] == "more" || $_POST['filter_limit'] == "next"){
				$_SESSION['product_filter_limit'] = $_SETTINGS['products_per_page'];
				//echo "START: ".$_SESSION['product_filter_limit_start']."<Br>";
				$_SESSION['product_filter_limit_start'] = $_SESSION['product_filter_limit_start'] + $_SETTINGS['products_per_page'];
				//echo "START: ".$_SESSION['product_filter_limit_start']."<Br>";
			}
			
			if($_POST['filter_limit'] == "previous"){
				$_SESSION['product_filter_limit'] = $_SETTINGS['products_per_page'];
				$_SESSION['product_filter_limit_start'] = $_SESSION['product_filter_limit_start'] - $_SETTINGS['products_per_page'];
				if($_SESSION['product_filter_limit_start'] < 0){ $_SESSION['product_filter_limit_start'] = 0; }
			}
			
			if($_POST['filter_limit'] == "first"){
				$_SESSION['product_filter_limit'] = $_SETTINGS['products_per_page'];
				$_SESSION['product_filter_limit_start'] = 0;
			}
			
			if($_POST['filter_limit'] == "last"){
				//TODO...
				$_SESSION['product_filter_limit'] = $_SETTINGS['products_per_page'];
				//$_SESSION['product_filter_limit_start'] = $_SESSION['product_filter_limit_start'] - 12;
				//if($_SESSION['product_filter_limit_start'] < 0){ $_SESSION['product_filter_limit_start'] = 0; }
			}
							
			// SORTING / ORDERING
			if(isset($_POST['filter_order'])){
				$_SESSION['product_filter_order'] = $_POST['filter_order'];
			}
			
			// ASC/DESC
			if(isset($_POST['filter_sort'])){
				$_SESSION['product_filter_sort'] = $_POST['filter_sort'];
			}
			
			// FILTER BY KEYWORDS
			if(isset($_POST['filter_keywords'])){
				$_SESSION['product_filter_keywords'] = $_POST['filter_keywords'];
			}
			
			if(isset($_POST['clear_keywords'])){
				$_SESSION['product_filter_keywords'] = "";
			}
			
			// FILTERING PRICE
			if(isset($_POST['filter_price_low'])){
				$_SESSION['product_filter_price_low'] = $_POST['filter_price_low'];
				$_SESSION['product_filter_price_high'] = $_POST['filter_price_high'];
			}
			
			// HE SHE
			//if($_POST['filter_heshe'] != ""){
			//	$_SESSION['filter_heshe'] = $_POST['filter_heshe'];
			//}
			
			$this->SBajaxProducts();
			exit;
		}
	}
		
	/** CATEGORYFILTER FOR SBAJAX
	 *
	 *
	 *
	 */	
	function SBCategoryFilter($category_id,$row)
	{
		global $_SESSION;
		
		// DISPLAY THE PRODUCT BY DEFAULT
		$display = 1;
		// UNLESS WE ARE VIEWING A CATEGORY
		if($category_id != ""){ $display = 0; }
		
		//
		// CATEGORIES // HE SHE
		// LOOP THROUGH CATEGORY RELATIONS TO THE PRODUCT
		//
		$select1 = "SELECT * FROM ecommerce_product_category_relational WHERE product_id='".$row['product_id']."'";
		$result1 = doQuery($select1);
		$num1 = mysql_num_rows($result1);
		$i1 = 0;
		$sqlcatid = "";
		while($i1 < $num1){
			$row1 = mysql_fetch_array($result1);				
			// SELECT THE CATEGORY FOR THE PRODUCT
			$select2 = "SELECT * FROM ecommerce_product_categories WHERE category_id='".$row1['category_id']."' LIMIT 1";
			$result2 = doQuery($select2);
			$row2 = mysql_fetch_array($result2);
			
			
			// THIS IS THE CATEGORY OF THE PRODUCT
			$categoryName = $row2['name'];
			
			
			// CHECK IF IT IS SHE BEADS
			if($row2['category_id'] == '24'){ $sheshe = true; $hehe = false; }
			if($row2['category_id'] == '25'){ $hehe = true; $sheshe = false; }
			// CHECK IF THIS CATEGORY MATCHES THE SEARCHING CATEGORY
			// TESTING
			//echo "<br>CATEGORY ID: ".$row2['category_id']."";
			
			if($row2['category_id'] == $category_id){ $sqlcatid = $category_id; }
			
			//echo "<br>SQL CATID: ".$sqlcatid."";
			
			// CHECK IF THE CATEGORY HAS A PARENT
			if($row2['parent_id'] != ""){
				// SELECT THE PARENT CATEGORY
				$select3 = "SELECT * FROM ecommerce_product_categories WHERE category_id='".$row2['parent_id']."' LIMIT 1";
				$result3 = doQuery($select3);
				$row3 = mysql_fetch_array($result3);
				// CHECK IF IT IS SHE BEADS
				if($row3['category_id'] == '24'){ $sheshe = true; $hehe = false; }
				if($row3['category_id'] == '25'){ $hehe = true; $sheshe = false; }
				//if($row3['category_id'] == $category_id){ $sqlcatid = $category_id; }
				
				// CHECK IF THE PARENT CATEGORY HAS A PARENT
				if($row3['parent_id'] != ""){
					// SELECT THE PARENT CATEGORY
					$select4 = "SELECT * FROM ecommerce_product_categories WHERE category_id='".$row3['parent_id']."' LIMIT 1";
					$result4 = doQuery($select4);
					$row4 = mysql_fetch_array($result4);
					// CHECK IF IT IS SHE BEADS
					if($row4['category_id'] == '24'){ $sheshe = true; $hehe = false; }
					if($row4['category_id'] == '25'){ $hehe = true; $sheshe = false; }
					//if($row4['category_id'] == $category_id){ $sqlcatid = $category_id; }
				}					
			}
			$i1++;
		}
		
		// HE SHE FILTER CHECK
		if($hehe == true){ $heshe = "he"; }
		if($sheshe == true){ $heshe = "she"; }
		if($_SESSION['filter_heshe'] != $heshe){ $display = 0; }
		// IF THE CATEGORY MATCHES THIS ITEMS CATEGORY AND THE USER IS FILTERING FOR A CATEGORY THEN DISPLAY
		if($sqlcatid != "" AND $category_id != ""){ $display = 1; }
		$array = array($display,$categoryName);
		return $array;
	}	
	
	/** SET HE/SHE/INTENTION PRODUCTS
	 *
	 *
	 *
	 */
	function SBFilter()
	{
		global $_SESSION;
		if($_SESSION['filter_heshe'] == "she")		{ 	$category = '24'; }
		if($_SESSION['filter_heshe'] == "he")		{ 	$category = '25'; }
		if($_SESSION['filter_heshe'] == "intention"){ 	$category = '75'; }
		
		$select = "SELECT name,category_id,parent_id,active FROM ecommerce_product_categories WHERE parent_id='".$category."' AND active='1'";
		$result = doQuery($select);
		$categorysql = "";
		while($row = mysql_fetch_array($result)){
			$categorysql .= "epcr.category_id='".$row['category_id']."' OR ";
			$categoryname .= "".$row['name'].",";
			
			$sel = "SELECT name,category_id FROM ecommerce_product_categories WHERE parent_id='".$row['category_id']."'";
			$res = doQuery($sel);
			while($ro = mysql_fetch_array($res)){
				$categorysql .= "epcr.category_id='".$ro['category_id']."' OR ";
				$categoryname .= "".$ro['name'].",";
			}			
		}
		
		$categorysql = substr($categorysql, 0, -3); 
		
		$_SESSION['product_sql'] = "";
		$select = 	"SELECT ep.product_id FROM ecommerce_products ep LEFT JOIN ecommerce_product_category_relational epcr ON ep.product_id=epcr.product_id ".
					"WHERE ep.active='1' AND (".$categorysql.")";
		$result = doQuery($select);
		while($row = mysql_fetch_array($result)){
			$filter_product_sql .= "ep.product_id='".$row['product_id']."' OR ";
		}
		
		// REMOVE THE LAST OR
		$filter_product_sql = substr($filter_product_sql, 0, -3); 
		
		//echo "<br>$select<br>$categoryname";
		
		if($filter_product_sql != ""){}
		$_SESSION['filter_product_sql'] = "(".$filter_product_sql.") AND ";
	}
	
	/**
	 *
	 *
	 *
	 */
	 function formatThumbnail($name)
	 {
		global $_SETTINGS;
		
		//var_dump($name);
		
		// GET THUMBNAIL SIZE
		$size = lookupDbValue('ecommerce_thumbnail_sizes', 'name', $_SETTINGS['product_page_thumbnail_size'], 'size_id');
		//$image1 = strtolower(str_replace(" ","_",$name).".jpg");
		$image1Array = explode(".",$name);
		$image1formated = $image1Array[0]."_w".$size.".".$image1Array[1];		
		$path = $_SETTINGS['website']."uploads-products/wpThumbnails/".$image1formated."";
		
		//if(!file_exists($_SETTINGS["DOC_ROOT"]."uploads-products/wpThumbnails/".$image1formated."")){
		//	$path = $_SETTINGS['website']."themes/".$_SETTINGS['theme']."images/".$_SETTINGS['image_not_available_thumbnail_file']."";
		//}
		return $path;
	 }
	 
	 /**
	 *
	 *
	 *
	 */
	 function formatMain($name)
	 {
		global $_SETTINGS;
		// GET THUMBNAIL SIZE
		$size = lookupDbValue('ecommerce_thumbnail_sizes', 'name', $_SETTINGS['detail_page_main_image_size'], 'size_id');
		//$image1 = strtolower(str_replace(" ","_",$name).".jpg");
		$image1Array = explode(".",$name);
		$image1formated = $image1Array[0]."_w".$size.".".$image1Array[1];
		$path = $_SETTINGS['website']."uploads-products/wpThumbnails/".$image1formated."";
		if(!file_exists($_SETTINGS["DOC_ROOT"]."uploads-products/wpThumbnails/".$image1formated."")){
			$path = $_SETTINGS['website']."themes/".$_SETTINGS['theme']."images/".$_SETTINGS['image_not_available_thumbnail_file']."";
		}
		return $path;
	 }
	 
	/**
	 *
	 *
	 *
	 */
	 function formatLargest($name)
	 {
		global $_SETTINGS;
		
		
		
		$path = $_SETTINGS['website']."uploads-products/".$name."";
		if(!file_exists($_SETTINGS["DOC_ROOT"]."uploads-products/".$name."")){
			$path = $_SETTINGS['website']."themes/".$_SETTINGS['theme']."images/".$_SETTINGS['image_not_available_file']."";
			return $path;
			//return false;
		}
		return $path;
	 }
	
	/** SB FUNCTION TO GET ACTUAL PRODUCTS
	 *
	 *
	 *
	 */
	function SBajaxProducts()
	{
		global $_SESSION;
		global $_SETTINGS;
		global $_REQUEST;
		global $_POST;
		global $_GET;
		
		$this->SBFilter();		
		//echo "<div class='products' id='products'>";
		
		// DEFAULT SESSION VALUES
		$this->setDefaults();
		
		//echo "<br>".$_SESSION['product_filter_category_id']."<Br>";
		
		// GET THE CATEGORY ID IF THERE IS ONE
		$category_id = $_SESSION['product_filter_category_id'];
				
		//echo"<br>PPP: ".$_SETTINGS['products_per_page']."<br>";	
				
		// TESTING
		//echo "<br>CATEGORY ID: ".$category_id."";
		//echo "<br>CATEGORY: ".$_REQUEST['FORM1']."";
		// TESTING
		//echo "<br>SESSION KEYWORDS: ".$_SESSION['product_filter_keywords']."";
		//echo "<br>SESSION PRICE LO: ".$_SESSION['product_filter_price_low']."";
		//echo "<br>SESSION PRICE HI: ".$_SESSION['product_filter_price_high']."";
		//echo "<br>SESSION ORDER: ".$_SESSION['product_filter_order']."";
		//echo "<br>SESSION SORT: ".$_SESSION['product_filter_sort']."";
		
		// KEYWORDS SQL
		$keywords = "ep.name LIKE '%".$_SESSION['product_filter_keywords']."%' AND ";

		// PRICE SQL
		$price = "(ep.price >= '".$_SESSION['product_filter_price_low']."' AND ep.price <= '".$_SESSION['product_filter_price_high']."') AND ";
		
		//echo "<Br>ID: ".$_SESSION['product_filter_category_id']."<br>";
		
		// CATEGORY
		
		if($_SESSION['product_filter_category_id'] != ""){	
			$catjoin = " LEFT JOIN ecommerce_product_category_relational epcr ON ep.product_id=epcr.product_id ";
			$catvar = ",epcr.sort_level";	
			$catand = "epcr.category_id='".$_SESSION['product_filter_category_id']."' AND ";
			if($_SESSION['product_filter_order'] == "ep.sort_level" ){
				$_SESSION['product_filter_order'] = "epcr.sort_level";			
			}
		} else {
			if($_SESSION['product_filter_order'] == "epcr.sort_level" ){
				$_SESSION['product_filter_order'] = "ep.sort_level";			
			}
		}
		
		// " LEFT JOIN ecommerce_product_categories epc ON epcr.category_id=epc.category_id"
		
		// SELECT SQL FOR COUNT
		$select = 	"SELECT ep.product_id ".
					"FROM ecommerce_products ep ".
					"".$catjoin."".
					"WHERE ".	
					"".$catand."".
					"".$keywords."".
					"".$price."".
					"".$_SESSION['filter_product_sql']."".
					"ep.active='1' AND ".
					"ep.status='Published' ".	
					"ORDER BY ".$_SESSION['product_filter_order']." ".$_SESSION['product_filter_sort']." ";	
		$result = doQuery($select);
		$total_records = mysql_num_rows($result);
		$p = $total_records; // TOTAL COUNT
						
		// start, show
		//$_SESSION['product_filter_limit'] = "12";
		
		// SELECT PAGINATED
		$select = 	"SELECT ep.product_id,ep.naming_convention,ep.image,ep.name,ep.price,ep.description".$catvar." ".
					"FROM ecommerce_products ep ".
					"".$catjoin."".
					"WHERE ".	
					"".$catand."".	
					"".$keywords."".
					"".$price."".
					"".$_SESSION['filter_product_sql']."".
					"ep.active='1' AND ".
					"ep.hidden_promo='0' AND ".
					"ep.status='Published' ".	
					"ORDER BY ".$_SESSION['product_filter_order']." ".$_SESSION['product_filter_sort']." LIMIT ".$_SESSION['product_filter_limit_start'].",".$_SESSION['product_filter_limit']."";	
		$result = doQuery($select);
		$num	= mysql_num_rows($result);
		
		// TESTING
		$searchQuery = $select;
		//echo "<Br><br>$select<br><br>";
		//echo "<br>CATEGORY ID: ".$_SESSION['product_filter_category_id']."<Br>";
				
		// SET THE NUMBER OF SELECTED RESULTES
		$_SESSION['product-filter-viewing'] = $num;
		
		$i 		= 0;			
		// IF NO RESULTS
		if($num < 1){
			echo "<p class='no-products'>";
			if($_SESSION['product_filter_keywords'] != ""){
				echo "<i style='font-size:25px;'>\"".$_SESSION['product_filter_keywords']."\"</i><br>";
			}
			echo "No products matched your search criteria, please broaden your search.";
			echo "</p>";
			echo "<p class='no-products-small'>Below are a few other products you may be interested in.</p>";
			$this->SBDisplayRelatedProducts("","",$limit="4");
		}	
		
		$j = 1;
		$k = 0;
		// LOOP
		while($i<$num){
			$row = mysql_fetch_array($result);
			$product_id = $row['product_id'];
			
			//$array = $this->SBCategoryFilter($category_id,$row);
			//$display = $array[0];
			//$categoryName = $array[1];
			
			$display = 1;
			$categoryName = "";
			
			//
			// SET DRAGGABLE PRODUCT
			//
			if($row['draggable'] == '1'){ $draggable = "draggable"; } else { $draggable = ""; }
			$width = $thumbsize;	
			
			if($display == 1){
				if($j == $_SETTINGS['products_per_row']){ $class = "list-product-last"; } else { $class = "list-product"; }			
				echo "<div class='".$draggable." ".$class." product'>";
					// GET PRODUCT IMAGES1
					$selimages = "SELECT * FROM ecommerce_product_images WHERE product_id='".$product_id."' AND active='1' ORDER BY sort_level ASC LIMIT 1";
					$resimages = doQuery($selimages);
					$numimages = mysql_num_rows($resimages);
					$rowimages = mysql_fetch_array($resimages);		
					
					
					// SETUP IMAGE NAMING CONVENTION
					$path = "";
					if($row['naming_convention'] == '1'){
						$imagenameFormated = strtolower(str_replace(" ","_",$row['name']).".jpg");
					} else {
						$imagenameFormated = $row['image'];
					}
					$path = $this->formatThumbnail($imagenameFormated);
					
										
					echo "<a class='productlist-details' href='".$_SETTINGS['website']."".$_SETTINGS['product_detail_page_clean_url']."/".$row['name']."'>";
					echo "	<div class='productimage' style='background-image:url(".$path.");'></div>";				
					echo "	<div class='producttext'>";
					echo "		<span class='productname'>".$row['name']."</span>";
					echo "		<span class='categoryname'>".$categoryName."</span>";
					echo "		<span class='productprice'>".$this->currency."".$row['price']."</span>";
					
					$description = $row['description'];
					if(strstr($description,"Intention:" ) AND strstr($description,"Affirmation:") AND $_SESSION['filter_heshe'] == "intention"){
						$description = str_replace("Intention:","",$description);
						$descriptionArray = explode("Affirmation:",$description);
						@$intentionString = $descriptionArray[0];
						@$affirmationString = $descriptionArray[1];
						echo 	"		<span class='productsummary'>";
						echo	"		<strong>Intention:</strong><br>";
						echo 	"		<i>".$intentionString."</i>";
						echo	"		<br><br><strong>Affirmation:</strong><br>";
						echo	"		<i>".$affirmationString."</i>";
						echo 	"		</span>";
					} else {
						echo 	"		<span class='productsummary'>".truncate($description,100)."</span>";
					}
					//echo "		<span class='productminiimage'><img style='width:75px;' src='".$path."'></span>";
					echo "	</div>";
					
					echo "	<form method='post' class='addtocart-listform'>";
					echo "		<input type='hidden' class='addtocart-hidden' name='PRODUCTID' value='".$product_id."'>";
					echo "		<input type='hidden' class='addtocart-hidden' name='QTY' value='1'>";
		
					echo "		<input type='submit' class='details-button' name='VIEWDETAILS' value='View Details'>";
					//echo "		<input type='submit' class='addtocart-listbutton' name='ADDTOCART' value='Buy'>";
					echo "	</form>";
					echo "<div style='height:1px; line-height:1px; clear:both;'>&nbsp;</div>";
					echo "</a>";
				
				echo "</div>";
				//echo "<br>".$j."";
				if($j == $_SETTINGS['products_per_row']){ echo "<br clear='all'>"; $j = 0;}
				$j++;
				$k++;
			}
			$i++;
		}
		
		//if($_SESSION['product_filter_limit'] < $_SESSION['product_filter_total']){
		//	$_SESSION['product_filter_viewing'] = $_SESSION['limit'];
		//} else {
		//	$_SESSION['product_filter_viewing'] = $_SESSION['product_filter_limit'];
		//}		
		
		//$_SESSION['product_filter_viewing'] = $k;
		
		// TOTAL RECORDS
		$_SESSION['product_filter_total'] = $p;
		
		// TESTING
		//echo "<br>NUM VIEWING: ".$_SESSION['product_filter_viewing']."";
		//echo "<br>NUM TOTAL: ".$_SESSION['product_filter_total']."";

		if($_SESSION['product_filter_total'] > 0){
			echo "<div class='pagination'>";
			
			if($_SESSION['product_filter_limit_start'] != 0 AND $_SESSION['product_filter'] != $_SESSION['products_perpage']){
				echo 	"<a id='first-results' class='pagbutton first-button' style=''>Go to the first page</a>";
			}
			
			// SHOW PREVIOUS BUTTON IF NOT ON THE FIRST PAGE
			if($_SESSION['product_filter_limit_start'] != 0){
				echo 	"<a id='previous-results' class='pagbutton previous-button' style=''>Previous</a>";
			}

			$end = $_SESSION['product_filter_limit_start'] + $_SETTINGS['products_per_page'];
			
			if($_SESSION['product_filter_total'] > $end){
				echo 	"<a id='more-results' class='pagbutton more-results' style=''>Click here to view more products.</a>";
				//echo 	"<a id='last-results' class='pagbutton last-button' style=''>Go to the last page</a>";		
			}
			
			//echo 	"<a id='next-results' class='pagbutton next-button' style=''>Next</a>";
			//
			// AJAX FOR MORE RESULTS HANDLING
			//
			if($_REQUEST['FORM1'] != ""){ $url = $_REQUEST['FORM1']; }
			if($_REQUEST['FORM1'] == ""){ $url = $_REQUEST['page']; }
			
			//echo "<br>".$_REQUEST['FORM1']."<br>";
			
			echo "
					<script type='text/javascript'>		
						function SBMoreResults(doing){
							$.ajax({
							  type: 'POST',
							  url: 'items',
							  data: 'FORM1=".$_REQUEST['FORM1']."&filter_products=1&filter_limit=' + doing + '',
							  success: function(data) {
								$('#products').html(data);
								//alert('Products Filtered BY KEYWORD.');
							  }
							});
						}

						$('#more-results').click(function() {
							SBMoreResults('more');			
							return false;
						});
						
						$('#previous-results').click(function() {
							SBMoreResults('previous');			
							return false;
						});
						
						$('#first-results').click(function() {
							SBMoreResults('first');			
							return false;
						});
						
						$('#last-results').click(function() {
							SBMoreResults('last');			
							return false;
						});
						
					</script>
			";
			echo "</div>";
			echo "<p class='remaining'><small>Currently viewing ".$_SESSION['product_filter_limit_start']."-".$end." / ".$_SESSION['product_filter_total']." items.</small></p>";
		} // END PAGINATION
		
		//echo "<small>".$searchQuery."</small>";
		
		//echo "</div>"; // END PRODUCTS BOX
	}
	
	/** LIST A CATEGORIES FOR FILTER
	 *
	 *
	 * uses category_id to display a list of the categories children
	 *
	 */		
	function listCategoryFilter($category_id)
	{
		// SELECT THE CAT
		$select = "SELECT * FROM ecommerce_product_categories WHERE category_id='".$category_id."' AND active='1'";
		$result = doQuery($select);
		$row = mysql_fetch_array($result);
		// TITLE THE PARENT CAT
		echo "<h2>".$row['name']."</h2>";
		// SELECT THE CAT CHILDREN		
		$select = "SELECT * FROM ecommerce_product_categories WHERE parent_id='".$category_id."' AND active='1'";
		$result = doQuery($select);
		$num 	= mysql_num_rows($result);
		$i 		= 0;		
		echo "<ul class='filter-results-list'>";
		while($i<$num){
			$row = mysql_fetch_array($result);
			echo "<li><a class='filter-results-".$row['category_id']."' id='filter-results-".$row['category_id']."'>".$row['name']."</a></li>";
			$i++;
		}			
		echo "</ul>";
	}
	
	/** SEARCH FILTER FORM
	 *
	 *
	 * 	
	 */
	function SBfilterProductForm()
	{
		
		global $_SESSION;
		global $_POST;
		global $_REQUEST;
		global $_SETTINGS;
			
		echo "<form action='' method='get'>";		
		echo "	<div>";		
		
		
		/*
		$sbdomainclass = "";
		$hbdomainclass = "";
		if($_SESSION['filter_heshe'] == "she"){ $sbdomainclass = "filter-shebeadsactive"; }
		if($_SESSION['filter_heshe'] == "he"){ $hbdomainclass = "filter-hebeadsactive"; }
		if($_SESSION['filter_heshe'] == "intention"){ $ibdomainclass = "filter-intentionbeadsactive"; }
		
		echo "
		<div>			
			<h2>Filter</h2>
			<a href='".$_SETTINGS['website']."items/shebeads' id='filter-shebeads' class='filter-shebeads ".$sbdomainclass."'>She Beads</a>
			<a href='".$_SETTINGS['website']."items/hebeads' id='filter-hebeads' class='filter-hebeads ".$hbdomainclass."'>He Beads</a>
			<a href='".$_SETTINGS['website']."items/intentionbeads' id='filter-intentionbeads' class='filter-intentionbeads ".$ibdomainclass."'>Intention Beads</a>
		</div>				
		";
		
		//echo "<br>".$_SESSION['filter_heshe']."<br>";
		*/
		
		echo "<br><a href='".$_SETTINGS['website']."".$_SETTINGS['products_page_clean_url']."/All'>View All</a>";
		
		
		// SEASONAL COLLECTIONS
		if($_SESSION['filter_heshe'] == "she"){ $category_id = '123'; }
		if($_SESSION['filter_heshe'] == "he"){ $category_id = ''; }
		if($_SESSION['filter_heshe'] == "intentrion"){ $category_id = ''; }
		
		if($category_id != ''){
			//$category_id = '1';
			// SELECT THE CAT
			$select = "SELECT * FROM ecommerce_product_categories WHERE category_id='".$category_id."' AND active='1'";
			$result = doQuery($select);
			$row = mysql_fetch_array($result);
			// TITLE THE PARENT CAT
			echo "<h2>".$row['name']."</h2>";
			// SELECT THE CAT CHILDREN		
			$select = "SELECT * FROM ecommerce_product_categories WHERE parent_id='".$category_id."' AND active='1' ORDER BY sort_level ASC";
			$result = doQuery($select);
			$num 	= mysql_num_rows($result);
			$i 		= 0;	
			echo "<ul class='filter-results-list'>";
			//echo "<li><a href='".$_SETTINGS['website']."".$_SETTINGS['products_page_clean_url']."/All'>View All</a></li>";
			while($i<$num){
				$row = mysql_fetch_array($result);
				$class = "";
				if($_REQUEST['FORM1'] == $row['name']){ $class = "active"; }
				echo "<li class='".$class." text-li'><a href='".$_SETTINGS['website']."".$_SETTINGS['products_page_clean_url']."/".$row['name']."-".$row['category_id']."' class='".$class." filter-results-".$row['category_id']."' id='filter-results-".$row['category_id']."'>".$row['name']."</a></li>";
				$i++;
			}			
			echo "</ul>";
		}
		
		// BOUTIQUE
		if($_SESSION['filter_heshe'] == "she"){ $category_id = '124'; }
		if($_SESSION['filter_heshe'] == "he"){ $category_id = ''; }
		if($_SESSION['filter_heshe'] == "intentrion"){ $category_id = ''; }
		
		if($category_id != ''){
			//$category_id = '1';
			// SELECT THE CAT
			$select = "SELECT * FROM ecommerce_product_categories WHERE category_id='".$category_id."' AND active='1'";
			$result = doQuery($select);
			$row = mysql_fetch_array($result);
			// TITLE THE PARENT CAT
			echo "<h2>".$row['name']."</h2>";
			// SELECT THE CAT CHILDREN		
			$select = "SELECT * FROM ecommerce_product_categories WHERE parent_id='".$category_id."' AND active='1' ORDER BY sort_level ASC";
			$result = doQuery($select);
			$num 	= mysql_num_rows($result);
			$i 		= 0;	
			echo "<ul class='filter-results-list'>";
			//echo "<li><a href='".$_SETTINGS['website']."".$_SETTINGS['products_page_clean_url']."/All'>View All</a></li>";
			while($i<$num){
				$row = mysql_fetch_array($result);
				$class = "";
				if($_REQUEST['FORM1'] == $row['name']){ $class = "active"; }
				echo "<li class='".$class." text-li'><a href='".$_SETTINGS['website']."".$_SETTINGS['products_page_clean_url']."/".$row['name']."-".$row['category_id']."' class='".$class." filter-results-".$row['category_id']."' id='filter-results-".$row['category_id']."'>".$row['name']."</a></li>";
				$i++;
			}			
			echo "</ul>";
		}
		
		
		
		// ITEMS
		if($_SESSION['filter_heshe'] == "she"){ $acategory_id = '19'; }
		if($_SESSION['filter_heshe'] == "he"){ $acategory_id = '26'; }		
		if($_SESSION['filter_heshe'] == "intention"){ $acategory_id = '89'; }
		// SELECT THE CAT
		$select = "SELECT * FROM ecommerce_product_categories WHERE category_id='".$acategory_id."' AND active='1'";
		$result = doQuery($select);
		$row = mysql_fetch_array($result);
		// TITLE THE PARENT CAT
		echo "<h2>".$row['name']."</h2>";
		// SELECT THE CAT CHILDREN		
		$select = "SELECT * FROM ecommerce_product_categories WHERE parent_id='".$acategory_id."' AND active='1' ORDER BY sort_level ASC";
		$result = doQuery($select);
		$num 	= mysql_num_rows($result);
		$i 		= 0;		
		echo "<ul class='filter-results-list'>";		
		//echo "<li></li>";
		while($i<$num){
			$row = mysql_fetch_array($result);
			$class = "";
			if($_REQUEST['FORM1'] == $row['name']){ $class = "active"; }
			echo "<li class='".$class." text-li'><a href='".$_SETTINGS['website']."".$_SETTINGS['products_page_clean_url']."/".$row['name']."-".$row['category_id']."' class='".$class." filter-results-".$row['category_id']."' id='filter-results-".$row['category_id']."'>".$row['name']."</a></li>";
			$i++;
		}			
		echo "</ul>";
		
		// COLORS
		if($_SESSION['filter_heshe'] == "she"){ $bcategory_id = '39'; }
		if($_SESSION['filter_heshe'] == "he"){ $bcategory_id = '59'; }
		if($_SESSION['filter_heshe'] == "intention"){ $bcategory_id = '88'; }
		// SELECT THE CAT
		$select = "SELECT * FROM ecommerce_product_categories WHERE category_id='".$bcategory_id."' AND active='1'";
		$result = doQuery($select);
		$row = mysql_fetch_array($result);
		// TITLE THE PARENT CAT
		echo "<h2>".$row['name']."</h2>";
		// SELECT THE CAT CHILDREN		
		$select = "SELECT * FROM ecommerce_product_categories WHERE parent_id='".$bcategory_id."' AND active='1' ORDER BY sort_level ASC";
		$result = doQuery($select);
		$num 	= mysql_num_rows($result);
		$i 		= 0;		
		echo "<ul class='filter-results-list'>";
		//echo "<li><a href='".$_SETTINGS['website']."".$_SETTINGS['products_page_clean_url']."/All'>View All</a></li>";
		while($i<$num){
			$row = mysql_fetch_array($result);
			$class = "";
			if($_REQUEST['FORM1'] == $row['name']){ $class = "active"; }
			echo "<li class='".$class." color-li'><a href='".$_SETTINGS['website']."".$_SETTINGS['products_page_clean_url']."/".$row['name']."-".$row['category_id']."' class=' a-".$row['name']." ".$class." color-a filter-results-".$row['category_id']."' id='filter-results-".$row['category_id']."'>".$row['name']."</a></li>";
			$i++;
		}			
		echo "</ul>";
		
		
		// COLOR COLLECTIONS
		if($_SESSION['filter_heshe'] == "she"){ $category_id = '1'; }
		if($_SESSION['filter_heshe'] == "he"){ $category_id = '51'; }
		if($_SESSION['filter_heshe'] == "intention"){ $category_id = '146'; }
		
		if($category_id != ''){
			//$category_id = '1';
			// SELECT THE CAT
			$select = "SELECT * FROM ecommerce_product_categories WHERE category_id='".$category_id."' AND active='1'";
			$result = doQuery($select);
			$row = mysql_fetch_array($result);
			// TITLE THE PARENT CAT
			echo "<h2>".$row['name']."</h2>";
			// SELECT THE CAT CHILDREN		
			$select = "SELECT * FROM ecommerce_product_categories WHERE parent_id='".$category_id."' AND active='1' ORDER BY sort_level ASC";
			$result = doQuery($select);
			$num 	= mysql_num_rows($result);
			$i 		= 0;	
			echo "<ul class='filter-results-list'>";
			//echo "<li><a href='".$_SETTINGS['website']."".$_SETTINGS['products_page_clean_url']."/All'>View All</a></li>";
			while($i<$num){
				$row = mysql_fetch_array($result);
				$class = "";
				if($_REQUEST['FORM1'] == $row['name']){ $class = "active"; }
				echo "<li class='".$class." text-li'><a href='".$_SETTINGS['website']."".$_SETTINGS['products_page_clean_url']."/".$row['name']."-".$row['category_id']."' class='".$class." filter-results-".$row['category_id']."' id='filter-results-".$row['category_id']."'>".$row['name']."</a></li>";
				$i++;
			}			
			echo "</ul>";
		}
		
		
		
		
		
		
		
		echo "	</div>";						
		echo "</form>";		
	}
	
	/** PRICE FILTER
	 *
	 *
	 *
	 */
	function filterPrice()
	{
		global $_SESSION;
		global $_REQUEST;
		
		$this->SetDefaults();
		
		if($_REQUEST['FORM1'] != ""){ $url = $_REQUEST['FORM1']; }
		if($_REQUEST['FORM1'] == ""){ $url = $_REQUEST['page']; }
		
		echo "
		<script>
		function FilterProductsByPrice(low,high){
						$.ajax({
						  type: 'POST',
						  url: '".$url."',
						  data: 'filter_products=1&filter_price_low=' + low + '&filter_price_high=' + high + '',
						  success: function(data) {
							$('#products').html(data);
							//alert('Products Filtered BY PRICE.');
						  }
						});
					}
		</script>
		";
		echo "<div class='filter-price'>";
		echo "	<label>Price Range</label> &nbsp;";				
		echo "	<style type='text/css'>";
		echo "		#demo-frame > div.demo { padding: 10px !important; };";
		echo "	</style>";
		echo "	<script type='text/javascript'>";
		echo "	$(function() {";
		echo "		$('#slider-range').slider({";
		echo "			range: true,";
		echo "			min: 0,";
		// GET THE LARGEST PRICE IN THE DATABASE
		$select = "SELECT price FROM ecommerce_products ORDER BY price DESC LIMIT 1";
		$result = doQuery($select);
		$row = mysql_fetch_array($result);
		$hi = $row['price'];
		echo "			max: ".$hi.",";
		echo "			values: [".$_SESSION['product_filter_price_low'].", ".$_SESSION['product_filter_price_high']."],";
		echo "			change: function(event, ui) { FilterProductsByPrice(ui.values[0],ui.values[1]); },";
		echo "			slide: function(event, ui) {";
		echo "				$('#amount').val('$' + ui.values[0] + ' - $' + ui.values[1]);";
		echo "			}";
		echo "		});";
		echo "		$('#amount').val('$' + $('#slider-range').slider('values', 0) + ' - $' + $('#slider-range').slider('values', 1));";
		echo "	});";
		echo "	</script>";
		echo "	<input type='text' id='amount' class='dark' style='margin-bottom:10px;' />";
		echo "	<div class='slider-range-wrapper'><div id='slider-range'></div></div>";			
		echo "</div>	";
	
		echo "
		
		";
	
	}
	
	/** SORT FILTER
	 *
	 *
	 *
	 */
	function sortProducts()
	{
		global $_SESSION;
		

		if($_SESSION['product_filter_sort'] == "ASC"){
			$ascActive = " SELECTED ";
		}
		if($_SESSION['product_filter_sort'] == "DESC"){
			$descActive = " SELECTED ";
		}
		if($_SESSION['product_filter_order'] == "ep.price"){
			$priceActive = " SELECTED ";
		}
		if($_SESSION['product_filter_order'] == "ep.name"){
			$nameActive = " SELECTED ";
		}
		
		//
		// SORTING FORM
		//
		echo	"<div class='filter-sort'>";			
		echo	"		<p>";
		echo	"			<label>Sort By</label> &nbsp; ";
		echo	"			<select name='order' id='order' class='dark'>";
		echo 	"				<option value=''>Featured</option>";
		echo	"				<option ".$priceActive." value='ep.price'>Price</option>";
		echo	"				<option ".$nameActive." value='ep.name'>Name</option>";
		echo	"			</select> ";
		echo	"			<select name='sort' id='sort' class='dark'>";
		echo	"				<option ".$ascActive." value='ASC'>Ascending</option>";
		echo 	"				<option ".$descActive." value='DESC'>Descending</option>";
		echo 	"			</select>";		
		echo	"		</p>";
		echo	"</div>";	
		
		if($_REQUEST['FORM1'] != ""){ $url = $_REQUEST['FORM1']; }
		if($_REQUEST['FORM1'] == ""){ $url = $_REQUEST['page']; }
		echo 	"
				<script>
				
				function OrderProducts(order){
					$.ajax({
					  type: 'POST',
					  url: '".$url."',
					  data: 'filter_products=1&filter_order=' + order + '',
					  success: function(data) {
						$('#products').html(data);
						//alert('Products Filtered BY KEYWORD.');
					  }
					});
				}
				
				$('#order').change(function() {
					order = $('#order').val();
					OrderProducts(order);
					//alert('SEARCH');
					return false;
				});
				
				function SortProducts(sort){
					$.ajax({
					  type: 'POST',
					  url: '".$url."',
					  data: 'filter_products=1&filter_sort=' + sort + '',
					  success: function(data) {
						$('#products').html(data);
						//alert('Products Filtered BY KEYWORD.');
					  }
					});
				}
				
				$('#sort').change(function() {
					sort = $('#sort').val();
					SortProducts(sort);					
					//alert('FILTER SORT BY');
					return false;
				});
				
				</script>
				";
		
	}
	
	/** KEYWORD BOX
	 *
	 *
	 *
	 */
	function searchKeywords()
	{
		global $_REQUEST;
		global $_SESSION;
		echo "
		<div class='search-keywords'>
				<!-- JAVASCRIPT BELOW HANDLES THESE FILTERS -->
				<input type='textbox' class='searchbox2' id='searchbox' name='keywords' value='".$_SESSION['product_filter_keywords']."' > 
				<input type='submit' class='searchbutton2' id='searchbutton' name='search' value='Search' > 
				<input type='submit' class='searchbutton2' id='clearbutton' name='clear_keywords' value='Clear'> 			
		</div>
		";
		if($_REQUEST['FORM1'] != ""){ $url = $_REQUEST['FORM1']; }
		if($_REQUEST['FORM1'] == ""){ $url = $_REQUEST['page']; }
		echo 	"
				<script>
				
					function FilterProductsByKeyword(keywords){
						$.ajax({
						  type: 'POST',
						  url: '".$url."',
						  data: 'filter_products=1&filter_keywords=' + keywords + '',
						  success: function(data) {
							$('#products').html(data);
							//alert('Products Filtered BY KEYWORD.');
						  }
						});
					}
					
					$('#searchbutton').click(function() {
						keywords = $('#searchbox').val();
						FilterProductsByKeyword(keywords);
						//alert('SEARCH');
						return false;
					});
					
					$('#clearbutton').click(function() {
						$('#searchbox').attr('value','');
						$('#topsearchbox').attr('value','');
						keywords = '';
						FilterProductsByKeyword(keywords);
						return false;
					});								
				</script>
				";
		
		
		
	}

	/** DISPLAY RELATED PRODUCTS
	 *
	 *
	 *
	 */
	function SBDisplayRelatedProducts($product_id="",$category_id="",$limit="3")
	{
		
		global $_SETTINGS;
		global $_SESSION;
		
		// GET RELATED PRODUCTS TO THE PRODUCT ID
		if($category_id == "" AND $product_id != ""){
			// GET THE PRODUCTS CATEGORY
			$select = "SELECT * FROM ecommerce_product_category_relational WHERE product_id=".$product_id."";
			$result = doQuery($select);
			$row = mysql_fetch_array($result);
			$category_id = $row['category_id'];
		}
		
		if($category_id != ""){		
			// GET THE PRODUCTS 
			$select = "SELECT * FROM ecommerce_products ep LEFT JOIN ecommerce_product_category_relational epcr ON ep.product_id=epcr.product_id WHERE ".$_SESSION['filter_product_sql']." epcr.category_id='".$category_id."' ORDER BY RAND() LIMIT ".$limit."";
		}
		
		// IF NO PRODUCT ID GET SOME ANYWAYS
		if($category_id == ""){
			$select = "SELECT * FROM ecommerce_products ep WHERE ".$_SESSION['filter_product_sql']." active='1' AND ep.status='Published' ORDER BY RAND() LIMIT ".$limit."";
		}
		
		//echo "<Br>$select<br>";
		
		$result = doQuery($select);
		$i = 0;
		$num = mysql_num_rows($result);
		echo "<div class='related-products'>";
		while($i<$num){
			$row = mysql_fetch_array($result);
			if(($i+1) == $limit){
				$class='list-product-last';
			} else {
				$class='list-product';
			}
			echo "<div class='".$class." product'>";
				
				// FORMAT THE IMAGE THUMBNAIL ACCORDING TO NAME
				$path = "";
				if($row['naming_convention'] == '1'){
					$imagenameFormated = strtolower(str_replace(" ","_",$row['name']).".jpg");	
				} else {
					$imagenameFormated = $rowproduct['image'];
				}				
				$path = $this->formatThumbnail($imagenameFormated);
				
				echo "<a class='productlist-details' href='".$_SETTINGS['website']."".$_SETTINGS['product_detail_page_clean_url']."/".$row['name']."'>";
				echo "	<div class='productimage'><img style='".$height." ".$margin."' src='".$path."'></div>";				
				echo "	<div class='producttext'>";
				echo "		<span class='productname'>".$row['name']."</span>";
				echo "		<span class='categoryname'>".$categoryName."</span>";
				echo "		<span class='productprice'>".$this->currency."".$row['price']."</span>";
				echo "		<span class='productsummary'>".truncate($row['description'],100)."</span>	";
				echo "	</div>";
				
				echo "	<form method='post' class='addtocart-listform'>";
				echo "		<input type='hidden' class='addtocart-hidden' name='PRODUCTID' value='".$product_id."'>";
				echo "		<input type='hidden' class='addtocart-hidden' name='QTY' value='1'>";
					
				echo "		<input type='submit' class='details-button' name='VIEWDETAILS' value='View Details'>";
				echo "	</form>";
				echo "</a>";
			
			echo "</div>";
			$i++;
		}	
		echo "<br clear='all'></div>";
	}
	
	
	/** DISPLAY PRODUCT DETAILS
	 *
	 *
	 * Display the product details
	 *
	 */
	function DisplayProductDetails($implicit=0)
	{
		global $_POST;
		global $_REQUEST;
		global $_SETTINGS;
		
		$display = 0;
		
		$flag = $_SETTINGS['product_detail_page_clean_url'];
		if($flag == $_REQUEST['page']){		
			$display = 1;
		}
		
		// CHECK IF IMPLICIT
		if($implicit == 1){
			$display = 1;
		}
		
		if($display == 1){
		
			$productArray = explode(":",$_REQUEST['FORM1']);
			$product = $productArray[0];
			if($productArray[1] == 'edit'){
				$cartitem_id = $productArray[2];
				$edit = 1;
			}
			
			// GET PRODUCT
			$selproduct = "SELECT * FROM ecommerce_products WHERE name='".$product."' AND active='1' AND status='Published' LIMIT 1";
			$resproduct = doQuery($selproduct);
			$rowproduct = mysql_fetch_array($resproduct);
			
			// CHECK IF A FREE PROMO PRODUCT
			if($rowproduct['hidden_promo'] == '1'){
				// GET THE PROMO CODE
				$selcode = "SELECT * FROM ecommerce_coupon_codes WHERE free_promo_product_id='".$rowproduct['product_id']."' AND coupon_id='".$_SESSION['promo_code_id']."' LIMIT 1";
				$rescode = doQuery($selcode);
				$numcode = mysql_num_rows($rescode);
				if($numcode > 0){
					true;
				} else {
					$report = "You do not have permission to see this page_";
					$success = "1";
					header("Location: ".$_SETTINGS['website']."".$_SETTINGS['products_page_clean_url']."");
					exit();					
				}
				
				// CHECK IF THE ITEM IS ALREADY IN THE CART
				if($edit != 1){
					$cartid = $this->getCartId();
					// GET THE ITEMS IN THE CART
					$prodselect = 	"SELECT a.product_id,c.name FROM ecommerce_product_cart_relational a ".
									"LEFT JOIN ecommerce_products c ON a.product_id=c.product_id ".
									"WHERE a.shopping_cart_id='".$cartid."'";
					$prodresult = doQuery($prodselect);
					$prodnum 	= mysql_num_rows($prodresult);
					$p = 0;
					while($p<$prodnum){
						$prodrow = mysql_fetch_array($prodresult);
						if($prodrow['product_id'] == $rowproduct['product_id']){
							$report = "This item is already in your cart_ Click edit in your cart to update this item's options_";
							$success = "1";
							header("Location: ".$_SETTINGS['website']."".$_SETTINGS['shopping_cart_page_clean_url']."/0/".$report."/".$success."/0");
							exit();
						}
						$p++;
					}
				}
			}
			
			$catdescription = "";
			$ammendcatdescription = "";
			
			// GET PRODUCT CATEGORY
			$selcat = "SELECT * FROM ecommerce_product_category_relational WHERE product_id='".$rowproduct['product_id']."'";
			$rescat = doQuery($selcat);
			while($rowcat = mysql_fetch_array($rescat)){			
				$ammendcatdescription = lookupDbValue('ecommerce_product_categories','description',$rowcat['category_id'],'category_id');
				if($ammendcatdescription != ""){ 
					$catdescription .= "<li>".$ammendcatdescription."</li>";
				}
				$catname = lookupDbValue('ecommerce_product_categories','name',$rowcat['category_id'],'category_id');
				$catid = $rowcat['category_id'];
			}
			
			//$selcat = "SELECT * FROM ecommerce_product_categories WHERE category_id='".$rowcat['category_id']."' LIMIT 1";
			//$rescat = doQuery($selcat);
			//$rowcat = mysql_fetch_array($rescat);
			
			//$catdescription = $rowcat['description'];
			
			// ZOOM SETTING
			$zoom = '0';
			if($_SETTINGS['product_zoom'] == '1' || $rowproduct['zoom'] == '1'){
				$zoom = '1';
			}
			
			$description	= $rowproduct['description'];
			//if($description != ""){ $description .= "<br>"; }
			//$description 	.= $catdescription;
			
			$name			= $rowproduct['name'];
			
			//
			// FORMAT PRICE
			//
			$discount = '0.00';
			if($rowproduct['flat_discount'] != '0.00' || $rowproduct['rate_discount'] != '0.00'){
				
				// IF FLAT DISCOUNT
				if($rowproduct['flat_discount'] != '0.00'){
					$discount = $rowproduct['flat_discount'];
					
				}
				// IF RATE DISCOUNT
				elseif($rowproduct['rate_discount'] != '0.00'){
					$discount = $rowproduct['price'] * $rowproduct['rate_discount'];
				}
				
				
			}
			
			$list_price	= $this->currency.money_format('%i',$rowproduct['price']);
			$new_price = $rowproduct['price'] - $discount;
			$new_price = $this->currency.money_format('%i',$new_price);	

			// PAGE TITLE / PRDUCT NAME
			echo 	"<h1>".$_REQUEST['FORM1']."</h1> ";
						
			echo 	"<p class='breadcrumbs'> ";
			echo 	"<a href='".$_SETTINGS['website'].$_SETTINGS['products_page_clean_url']."' >Items</a> >> ";
			echo 	"<a href='".$_SETTINGS['website'].$_SETTINGS['products_page_clean_url']."/".$catname."-".$catid."'>".$catname."</a> >> ";
			echo	"".$name."".
					"</p>";
			
			// BEGIN PRODUCT BOX
			echo 	"<div class='productbox'>";	
			
			if($list_price != $new_price){
				echo "<div class='salebubble'>This item is on sale. Save ".$this->currency.money_format('%i',$discount)."!</div>";
			}
			
			//if($rowproduct['description']){
				echo "<div class='salebubble' style='display:none;'></div>";
			//}
			
			// MAIN IMAGE
			echo	"	<div class='productimagegallery'>";
			
			
			if($rowproduct['droppable'] == "1"){ $droppable = "droppable"; }
			echo 	"		<div class='mainimage ".$droppable." product'>";	
			
			// GET IMAGE NAME
			if($rowproduct['naming_convention'] == '1'){
				$imagenameFormated = strtolower(str_replace(" ","_",$name).".jpg");	
			} else {
				$imagenameFormated = $rowproduct['image'];
			}
			
			$thumbnailpath = $this->formatThumbnail($imagenameFormated);
			
			// If HI RES THEN GET THE LARGEST SIZE IMAGE
			if($_SETTINGS['hi_res_images'] == '1'){ 
				$mainImage = $this->formatLargest($imagenameFormated);
			}
			// IF NORMAL THEN GET THE MAIN IMAGE SIZE FROM SETTING
			else {
				$mainImage = $this->formatMain($imagenameFormated);
			}
			$size = lookupDbValue('ecommerce_thumbnail_sizes', 'name', $_SETTINGS['detail_page_main_image_size'], 'size_id');
			if($zoom == '1'){
				echo	"		<a id='main-image-zoom-a' href='".$this->formatLargest($imagenameFormated)."' class='jqzoom productlightbox' style='' title=''>";
				echo	"			<img style='width:".$size."px;' id='main-detail-image' class='main-detail-image' src='".$mainImage."'>";
				echo	"		</a>";					
			} else {					
				echo	"		<img style='width:".$size."px;' id='main-detail-image' class='main-detail-image' src='".$mainImage."'>";				
			}				
			echo			"</div>"; // END MAIN IMAGE
			
			// LOOP GALLERY IMAGES
			$selimage1 = 	"SELECT * FROM ecommerce_product_images WHERE ".
							"(active='1' AND product_id='".$rowproduct['product_id']."') ".							
							"ORDER BY sort_level ASC";
			$resimage1 =	doQuery($selimage1);
			$numimage1 = 	mysql_num_rows($resimage1);
			$iimage1 =		0;		
			if($numimage1 > 0){
				echo	"	<div class='gallerythumbnails'>";
						
						
						
					while($iimage1<$numimage1){
						$rowimage1 = mysql_fetch_array($resimage1);
						if($masterimage != $rowimage1['image']){							
							echo 	"<div class='gallerythumbnail draggable product'>".
									"	<a id='image".$iimage1."' class='image".$iimage1."' href='#' rel='".$rowimage1['description']."' title='".$rowimage1['description']."'>".
									"		<img src='".$_SETTINGS['website']."uploads-products/".$this->getSmallImage($rowimage1['image'])."' rel='".$rowimage1['description']."' title='".$rowimage1['description']."'>".
									"	</a>";
							echo 	"
										<script type='text/javascript'>
										
										$('#image".$iimage1."').click(function() {
											
											// SET THE MAIN IMAGE AS THIS IMAGE
											$('#main-detail-image').attr('src','".$_SETTINGS['website']."uploads-products/".$this->getMainImage($rowimage1['image'])."');
											// SET THE MAIN IMAGE ZOOMER AS THE ORIGINAL 
											$('#main-image-zoom-a').attr('href','".$_SETTINGS['website']."uploads-products/".$rowimage1['image']."');
											// SET THE IMAGE DESCRIPTION
											
											";
											
											if($rowimage1['description'] != ""){
												echo "	$('.salebubble').css('display','block');
													$('.salebubble').html('".$rowimage1['description']."');";
											} else {
												echo " 	$('.salebubble').css('display','none');
													$('.salebubble').html('');";
											}
											
							echo    "			return false;
										});
										
										</script>						
							";									
							echo 	"</div>"; // end gallery ITEM
						} // end if the first item
						$iimage1++;
					}	

					echo "<div class='gallerythumbnail draggable product'><a id='image00' class='image00' href='#'><img src='".$_SETTINGS['website']."uploads-products/".$this->getSmallImage($imagenameFormated)."' style-'width:94px;'></a></div>";
					echo "<script>";
					echo "$('#image00').click(function() {";
					echo "$('#main-detail-image').attr('src','".$_SETTINGS['website']."uploads-products/".$this->getMainImage($imagenameFormated)."');";
					echo "$('#main-image-zoom-a').attr('href','".$_SETTINGS['website']."uploads-products/".$imagenameFormated."');";
					if($rowproduct['description'] != ""){
						echo "	$('.salebubble').css('display','block');
							$('.salebubble').html('".$rowproduct['description']."');";
					} else {
						echo " 	$('.salebubble').css('display','none');
							$('.salebubble').html('');";
					}
					echo "});";
					echo "</script>";
					
				echo	"		<br clear='all'>".
						"	</div>"; // end GALLERY
			} // end if numimage1
			// END THE IMAGE GALLERY
			
			// SCRIPT FOR ZOOM
			if($zoom == '1'){
				echo 	"	<script>".
						"		var options = { ".
						"		position: 'left', ".
						"		zoomWidth: 300, ".
						"		zoomHeight: 250 ".
						"		}; ".
						"		$(function() { ".
						"			$('.jqzoom').jqzoom(options); ".
						"		}); ".
						"	</script>";
			}
			echo	"	</div>";// END IMAGE GALLERY
			
			// START FORM
			echo 	"	<form class='product-form' method='post'>";
			echo 	"	<div class='productinfobox'>".
					"		<div class='productdescription'>";
			
			// TOP ADD TO CART BUTTON
			$displaybutton = 0;
			if($rowproduct['take_inventory'] == '1'){
				if($rowproduct['inventory'] < 0){ $rowproduct['inventory'] = 0; }
				if($rowproduct['inventory'] > 0){
					$displaybutton = 1;
				}
			} else {
				$displaybutton = 1;
			}
			
			if($displaybutton == 1){
				if($edit == 1){
					echo 	"	<p class='p-add-to-cart'><input type='hidden' name='cartitem_id' value='".$cartitem_id."'><input class='add-to-cart-button button' type='submit' name='UPDATEITEM' value='Update Item'></p>";
				} else {	
					echo 	"	<p class='p-add-to-cart'><input class='add-to-cart-button button' type='submit' name='ADDTOCART' value='Add To Cart'></p>";
				}	
			}
			
			
			// PRODUCT DESCRIPTION
			if($description != ""){
				if(strstr($description,"Intention:" ) AND strstr($description,"Affirmation:") AND $_SESSION['filter_heshe'] == "intention"){
					$description = str_replace("Intention:","",$description);
					$descriptionArray = explode("Affirmation:",$description);
					@$intentionString = $descriptionArray[0];
					@$affirmationString = $descriptionArray[1];
					echo	"		<span class='productdescription-heading'>Intention:</span>";
					echo 	"		".$intentionString."";
					echo	"		<span class='productdescription-heading'>Affirmation:</span>";
					echo	"		".$affirmationString."";
				} else {
					echo	"			<span class='productdescription-heading'>Details:</span>";
					echo	"			".$description."";
				}
			} else {
				echo	"			<span class='productdescription-heading'>Details:</span>";
			}
			
			// CAT DESCRIPTION
			if($_SESSION['filter_heshe']!= 'intention'){
				if($catdescription != "")
				{
					echo "<ul class='prod-details'>";
					if(strstr($catdescription,"--")){
						echo "".str_replace("--","</li><li>",$catdescription)."";
					}
					else
					{
						echo $catdescription;
					}
					echo "</ul>";
				}
			}
			
			// PRODUCT PRICE	
			if($list_price != $new_price){
				echo "<span class='productdescription-heading'>Price:</span> List Price: <del>".$list_price."</del><Br>";
				echo "Your Price: <strong>".$new_price."</strong>";
			} else {
				echo "<span class='productdescription-heading'>Price:</span> ".$list_price."";
			}
			
					
			// PRODUCT QUANTITY	
			$_POST['quantity'] == $_POST['QTY'];
			if($edit == 1){
				// GET THE QUANTITY FROM THE CART
				$sel = "SELECT * FROM ecommerce_product_cart_relational WHERE item_id='".$cartitem_id."' LIMIT 1";
				$res = doQuery($sel);
				$item = mysql_fetch_array($res);
				$_POST['quantity'] = $item['qty'];
				
			}
			if($_POST['quantity'] == ""){ $_POST['quantity'] = "1"; }
			echo 	"			<span class='productdescription-heading'>Quantity:</span>";
			
			$display = 1;			
			// IF INVENTORY THEN DISPLAY IT
			if($rowproduct['take_inventory'] == '1'){
				if($rowproduct['inventory'] < 0){ $rowproduct['inventory'] = 0; }
				if($rowproduct['inventory'] > 0){
					echo "<input type='text' class='product-qty' style='width:30px;' name='QTY' value='".$_POST['quantity']."'> ";
					echo "<small> Available Inventory: ".$rowproduct['inventory']."</small>";			
				} elseif($rowporduct['inventory'] == 0) {
					echo "<small> Available Inventory: ".$rowproduct['inventory']." left. </small>";
					$display = 0;
				}
			}
			// NOT TAKING INVENTORY
			else {
				if($rowproduct['hidden_promo'] == '1'){
					echo 	"1 <input type='hidden' class='' style='' name='QTY' value='".$_POST['quantity']."'>";
				} else {
					echo 	"<input type='text' class='product-qty' style='width:30px;' name='QTY' value='".$_POST['quantity']."'>";
				}
			}
			
			if($display == 1){
				// ATTRIBUTES		
				$sele = "SELECT * FROM ecommerce_product_attributes WHERE active='1' ORDER BY sort_level ASC";
				$rese = doQuery($sele);
				$nume = mysql_num_rows($rese);
				$ie	  = 0;
				while($ie<$nume)
				{
					$numa = 0;
					
					// CHECK IF ATTRIBUTE MATCHES PRODUCT
					$row = mysql_fetch_array($rese);
					$selcheck = "SELECT * FROM ecommerce_product_attribute_relational WHERE product_id='".$rowproduct['product_id']."' AND attribute_id='".$row['attribute_id']."' LIMIT 1";
					$rescheck = doQuery($selcheck);
					//echo "<br>$selcheck<br>";
					$numa = mysql_num_rows($rescheck);
					
					// CHECK IF ATTRIBUTE MACHES CATEGORY
					$selcheck = "SELECT * FROM ecommerce_product_category_relational WHERE product_id='".$rowproduct['product_id']."' AND category_id != '0'";
					$rowcheck = doQuery($selcheck);
					while($rowc = mysql_fetch_array($rowcheck)){
						// CHECK IF ATTRIBUTE MATCHES
						// echo "<br>".$rowc['category_id']." --- ".$row['category_id']."<br>";
						
						if($rowc['category_id'] == $row['category_id']){
							$numa = 1;
						}
						
					}
					
					if($numa == 1){
						//$rowa = mysql_fetch_array($rescheck);
						
						//echo "<Br>ATTRIBUTE: ".$row['name']."<br>";
						//echo "TEXT VALUE: ".$_POST['value']."<Br>";
						//echo "CARTITEM ID: ".$cartitem_id."<br>";
						//echo "ATTRIBUTE ID:".$row['attribute_id']."<br>";
						
						if($edit == 1){
							// GET THE ATTRIBUTE VALUE FROM THE CART RELATIONAL ITEM
							$sel = "SELECT * FROM ecommerce_product_attribute_cart_relational WHERE relational_item_id='".$cartitem_id."' AND attribute_id='".$row['attribute_id']."'";
							$res = doQuery($sel);
							$item = mysql_fetch_array($res);
							$_POST['value'] = $item['value'];						
						}
						
						//echo "ATTRIBUTE VALUE: ".$_POST['value']."";
						
						// ATTRIBUTE NAME
						echo	"		<span class='productdescription-heading'>".$row['label'].":</span>";
						
						// DESCRIPTION
						if($row['description'] != ""){
							echo	"	<p class='product-description'>".$row['description']."</p>";
						}
						
						// TEXTBOX ATTRIBUTE
						if($row['type'] == 'Textbox'){
							echo	"	<input name='attribute[]' type='text' value='".$_POST['value']."'>";	
						}
						
						// TEXTAREA ATTRIBUTE
						if($row['type'] == 'Textarea'){
							echo	"	<textarea name='attribute[]'>".$_POST['value']."</textarea>";
						}
						
						if($edit == 1){
							// GET THE ATTRIBUTE VALUE FROM THE CART RELATIONAL ITEM
							//$sel = "SELECT * FROM ecommerce_product_attribute_cart_relational WHERE item_id='".$cartitem_id."' AND attribute_id='".$row['attribute_id']."'";
							//$res = doQuery($sel);
							//$item = mysql_fetch_array($res);
							$_POST['value'] = $item['attribute_value_id'];		
					
						} else {
							// CHECK DEFAULT GET DEFAULT ATTRIBUTE VALUE
							$sel1 = "SELECT * FROM ecommerce_product_attribute_values WHERE `default`='1' AND attribute_id='".$row['attribute_id']."' LIMIT 1";
							$res1 = doQuery($sel1);
							$row1 = mysql_fetch_array($res1);
							$_POST['value'] = $row1['attribute_value_id'];
						}
						
						//echo "<Br>ATTRIBUTE ID VALUE: ".$_POST['value']."<Br>";
						
						// SELECT ATTRIBUTE
						if($row['type'] == 'Select'){
							echo	"	<select name='attribute[]'>";
								//$table = "ecommerce_product_attribute_values";
								//$options = getSqlSelectArray($table);
								$sel = "SELECT * FROM ecommerce_product_attribute_values WHERE attribute_id='".$row['attribute_id']."' AND active='1'";
								$options = doQuery($sel);
								while($row1 = mysql_fetch_array($options)){
									$selected = "";
									if($row1['attribute_value_id'] == $_POST['value']){ $selected = " SELECTED "; }
									
									echo	"	<option value='".$row1['attribute_value_id']."' ".$selected.">".$row1['name']."</option>";
								}
							echo 	"	</select>";
						}
						
						// CHECKBOX GROUP
						if($row['type'] == 'Checkbox Group'){
							echo 	"	<ul class='attribute-list attribute-ul-".$row['attribute_id']."'>"; // BEGIN ATTRIBUTE LOOPED LIST
							//$table = "ecommerce_product_attribute_values";
							//$options = getSqlSelectArray($table);
							$sel = "SELECT * FROM ecommerce_product_attribute_values WHERE attribute_id='".$row['attribute_id']."' AND active='1'";
							$options = doQuery($sel);
							while($row1 = mysql_fetch_array($options)){
							
								if($edit == 1){
									// GET THE  RELATIONAL ATTRIBUTE ITEMS FOR THIS ATTRIBUTE IN THE CART FOR THIS CART ITEM				
									$sel = "SELECT * FROM ecommerce_product_attribute_cart_relational WHERE relational_item_id='".$cartitem_id."' AND attribute_id='".$row['attribute_id']."' AND attribute_value_id='".$row1['attribute_value_id']."'";
									$res = doQuery($sel);
									$item = mysql_fetch_array($res);
									$_POST['value'] = $item['attribute_value_id'];		
								}
								
								//echo "<Br>ATTRIBUTE ID VALUE: ".$_POST['value']."<Br>";
							
								$selected = "";
								if($row1['attribute_value_id'] == $_POST['value']){ $selected = " CHECKED "; }
								echo	"		<li class='attribute-li-".$row1['attribute_value_id']."' style='background-image:url(".$_SETTINGS['website']."uploads/".$row1['image']."); background-repeat:no-repeat;'>";
								echo 	"			<input type='checkbox' ".$selected." name='attribute[]' value='".$row1['attribute_value_id']."'> ";
								echo 	"			<label>".$row1['name']."</label>";
								echo	"		</li>";
							}

							
							
							echo	"	</ul><br clear='all'"; // END ATTRIBUTE LOOPED LIST
						}
						
						
						// RADIO GROUP
						if($row['type'] == 'Radio Group'){
							echo 	"	<ul class='attribute-list attribute-ul-".$row['attribute_id']."'>"; // BEGIN ATTRIBUTE LOOPED LIST
							//$table = "ecommerce_product_attribute_values";
							//$options = getSqlSelectArray($table);
							$sel = "SELECT * FROM ecommerce_product_attribute_values WHERE attribute_id='".$row['attribute_id']."' AND active='1'";
							$options = doQuery($sel);
							while($row1 = mysql_fetch_array($options)){
							
								if($edit == 1){
									// GET THE  RELATIONAL ATTRIBUTE ITEMS FOR THIS ATTRIBUTE IN THE CART FOR THIS CART ITEM				
									$sel = "SELECT * FROM ecommerce_product_attribute_cart_relational WHERE relational_item_id='".$cartitem_id."' AND attribute_id='".$row['attribute_id']."' AND attribute_value_id='".$row1['attribute_value_id']."'";
									$res = doQuery($sel);
									$item = mysql_fetch_array($res);
									$_POST['value'] = $item['attribute_value_id'];		
								}
								
								//echo "<Br>ATTRIBUTE ID VALUE: ".$_POST['value']."<Br>";
							
								$selected = "";
								if($row1['attribute_value_id'] == $_POST['value']){ $selected = " CHECKED "; }
								echo	"		<li class='attribute-li-".$row1['attribute_value_id']."' style='background-image:url(".$_SETTINGS['website']."uploads/".$row1['image']."); background-repeat:no-repeat;'>";
								echo 	"			<input type='radio' ".$selected." name='attribute[]' value='".$row1['attribute_value_id']."'> ";
								echo 	"			<label>".$row1['name']."</label>";
								echo	"		</li>";
							}				
							
							echo	"		<li class='attribute-li-".$row1['attribute_value_id']."' style=''>";
							echo 	"			<input type='radio' ".$selected." name='attribute[]' value=''> ";
							echo 	"			<label>None</label>";
							echo	"		</li>";
							
							echo	"	</ul><br clear='all'><br>"; // END ATTRIBUTE LOOPED LIST
						}
						
					}
					$ie++;				
				}			
				
				// PRODUCT NOTE 
				if($edit == 1){
					// GET THE NOTE FROM THE CART
					$sel = "SELECT * FROM ecommerce_product_cart_relational WHERE item_id='".$cartitem_id."' LIMIT 1";
					$res = doQuery($sel);
					$item = mysql_fetch_array($res);
					$_POST['NOTE'] = $item['note'];				
				}
				echo 	"			<span class='productdescription-heading'>Note:</span>".
						"			<textarea class='product-note' name='NOTE' >".$_POST['NOTE']."</textarea>";
				
				echo	"			<input type='hidden' class='addtocart-hidden' name='PRODUCTID' value='".$rowproduct['product_id']."'";
				//echo	"			<input type='hidden' class='addtocart-hidden' name='QTY' value='1'";
				if($edit == 1){
					echo 	"			<p class='p-add-to-cart'><input type='hidden' name='cartitem_id' value='".$cartitem_id."'><input class='add-to-cart-button button' type='submit' name='UPDATEITEM' value='Update Item'></p>";
				} else {	
					//if($rowproduct['take_inventory'] == '1' ){
					//	if($rowproduct['inventory'] > 0){
					echo 	"		<p class='p-add-to-cart'><input class='add-to-cart-button button' type='submit' name='ADDTOCART' value='Add To Cart'></p>";
					//	}
					//}
				}
			}
			
			echo 	"		</div>"; // END PRODUCT DESCRIPTION
			echo 	"	</div>"; // END PRODUCT INFO BOX		
			//echo 	"	<br clear='all'>";
			echo 	"	</form>"; // END FORM
			
			// IF THERE ARE REVIEW FOR THIS PRODUCT
			// SHOW THE VIEW TAB
			$seltestimonial = "SELECT comment_id FROM ecommerce_product_comments WHERE product_id='".$rowproduct['product_id']."' AND active='1' AND status='Published'";
			$restestimonial = doQuery($seltestimonial);
			$numtestimonial = mysql_num_rows($restestimonial);
			
			echo "<div id='tabs'>
					<ul>";
			
			if($numtestimonial){		
				echo "	<li><a href='#tabs-reviews'>Product Reviews</a></li>";
				
			}			
			
			echo "		<li><a href='#tabs-write'>Write a Review</a></li>";
			echo "		<li><a href='#tabs-related'>You'll also like...</a></li>";
			echo "		<li><a href='#tabs-packaging'>Packaging</a></li>";
			
			if($_SESSION['filter_heshe'] == 'intention')
			{
				echo "	<li><a href='#tabs-manifestation'>Manifestation Card</a></li>";
			}
			
			echo "	</ul>";
				
			
			if($numtestimonial){		
				echo "	<div id='tabs-reviews'>";
						$this->DisplayComments($rowproduct['product_id']);							
				echo "	</div>";
			}			
			echo "	<div id='tabs-write'>";
						$this->DisplayCommentForm($rowproduct['product_id']);							
			echo "	</div>
					<div id='tabs-related'>";						
						$this->SBDisplayRelatedProducts();							
			echo "	</div>";
			
			echo "<div id='tabs-packaging' style='text-align:center;'>";
			echo "	<img src='".$_SETTINGS['website']."uploads/packaging.jpg' style='margin:25px 0;'>";
			echo "</div>";
			
			if($_SESSION['filter_heshe'] == 'intention')
			{
				echo "	<div id='tabs-manifestation' style='text-align:center;'><a class='productlightbox' href='".$_SETTINGS['website']."uploads/manifestation.jpg'><img src='".$_SETTINGS['website']."uploads/manifestation.jpg' style='margin:25px 0; width:550px;'></a></div>";	
			}
			
			echo "</div>";

			echo "
			<script>
				$( '#tabs' ).tabs();
			</script>			
			";
			
			echo "<br clear='all'>";
			
			echo	"</div>"; // END PRODUCT BOX
		} // display
	}
	
	/** DISPLAY COMMENTS
	 *
	 *
	 *
	 */
	function DisplayComments($product_id)
	{
		
		// COMMENTS // PRODUCT TESTIMONIALS
		echo "			<div class='product-comment-box'><h2>What People are Saying about this item...</h2>";
		
		// CHECK IF THE USER HAS PURCHASED THIS PRODUCT IN THE PAST AND SHOW THEM THE LEAVE TESTIMONIAL FORM
		
		// GET TESTIMONIALS
		$seltestimonial = "SELECT * FROM ecommerce_product_comments WHERE product_id='".$product_id."' AND active='1' AND status='Published'";
		$restestimonial = doQuery($seltestimonial);
		$numtestimonial = mysql_num_rows($restestimonial);
		$itestimonial = 0;
		while($itestimonial < $numtestimonial){
			$rowtestimonial = mysql_fetch_array($restestimonial);
			// DISPLAY COMMENTS HERE 
			echo "				<div class='product-comment'>";
			echo "					<h3>";
			echo "					".$rowtestimonial['name']."'s Review";
			echo "					<span class='ratings ratings-right'>
										<input name='rating' type='radio' value='1' disabled ".ischecked($rowtestimonial['rating'],'1')." class='starview' />
										<input name='rating' type='radio' value='2' disabled ".ischecked($rowtestimonial['rating'],'2')." class='starview' />
										<input name='rating' type='radio' value='3' disabled ".ischecked($rowtestimonial['rating'],'3')." class='starview' />
										<input name='rating' type='radio' value='4' disabled ".ischecked($rowtestimonial['rating'],'4')." class='starview' />
										<input name='rating' type='radio' value='5' disabled ".ischecked($rowtestimonial['rating'],'5')." class='starview' />
									</span>
									<script>
										$('.starview').rating();
									</script>";
			echo "					<span class='ratings-date'>".TimestampIntoDate($rowtestimonial['created'])."</span>";
			echo "					</h3>";
			echo "					<p class='product-comments'>\"".$rowtestimonial['content']."\"</p>";
			
			echo "				</div>"; // END COMMENT BOX	
				
			$itestimonial++;
		}
		echo "</div>";
		//
		// EXAMPLE COMMENTS
		//
		/*echo "				<div class='product-comment'>";
		echo "					<h3>Our product reveiw </h3>";
		echo "					<p class='product-comments'>\"I have purchased and love this product. This is a customer comment example.\"</p>";
		echo "					<p class='product-comments-date'>9/21/2010</p>";
		echo "				</div>";			
		echo "				<div class='product-comment'>";
		echo "					<h3>John Smith Says...</h3>";
		echo "					<p class='product-comments'>\"I have purchased and love this product. This is a customer comment example.\"</p>";
		echo "					<p class='product-comments-date'>9/26/2010</p>";
		echo "				</div>";			
		echo "			</div>"; // END COMMENT BOX*/
	}
	
	/** COMMENT FORM ACTION
	 * 
	 *
	 *
	 */
	function CommentFormAction()
	{
		global $_SETTINGS;
		global $_REQUEST;
		global $_SESSION;
		
		if($_POST['SUBMITCOMMENT'] != ""){
			$error = 0;
			
			// CAPTCHA VALIDATION
			
			//$key=substr($_SESSION['key'],0,5);
			//$number = $_REQUEST['number'];
			//if($_POST['captcha'] != $key){ $ }
			/*
			if(chk_crypt($_POST['captcha'])){
				$error = 0;
			} else {
				$error = 1; $_REQUEST['SUCCESS'] = 0; $_REQUEST['REPORT'] = "Verification code is incorrect_";
			} 
			*/
			//echo "Correct";
			//else echo "Bad :(";
			
			
			global $recaptchaprivatekey;
			$privatekey = $recaptchaprivatekey;
			$resp = recaptcha_check_answer ($privatekey,
										$_SERVER["REMOTE_ADDR"],
										$_POST["recaptcha_challenge_field"],
										$_POST["recaptcha_response_field"]);

			//var_dump($resp);
			//die();
			//exit();
										
			if (!$resp->is_valid) {
				$error = 1; $_REQUEST['SUCCESS'] = 0; $_REQUEST['REPORT'] = "Verification code is incorrect_";
			} else {
				$error = 0;
			}

			
			
			
			// VALIDATION
			if($_POST['name'] == ''){ $error = 1; $_REQUEST['SUCCESS'] = 0; $_REQUEST['REPORT'] = "Please enter your name_"; }
				
			if($error == 0){	
			
				// INSERT
				$_POST = escape_smart_array($_POST);
				$sql = 	"INSERT INTO ecommerce_product_comments SET ".
						"content='".$_POST['content']."',".
						"created=NULL,".
						"product_id='".$_POST['product_id']."',".
						"name='".$_POST['name']."',".
						"status='Pending',".
						"email='".$_POST['email']."',".
						"user_id='".$_SESSION['UserAccount']['userid']."',".
						"rating='".$_POST['rating']."',".
						"ip='".getUserIp()."',".
						"bought='".$_POST['bought']."'";
				doQuery($sql);
						
				$to = $_SETTINGS['product_review_email'];
				$from = $_SETTINGS['automated_reply_email'];
				$subject = "New product review for ".lookupDbValue('ecommerce_products','name',$_POST['product_id'],'product_id')."";
				$message = "<br><a href='".$_SETTINGS['website']."admin/index.php'>New product review for ".lookupDbValue('ecommerce_products','name',$_POST['product_id'],'product_id')."</a>.";
				sendEmail($to,$from,$subject,$message,$attachment="");		
						
				$success = 1;
				$report = "Thanks for the review_";
				header("Location: ".$_SETTINGS['website']."".$_SETTINGS['product_detail_page_clean_url']."/".$_REQUEST['FORM1']."/".$report."/".$success."/0#tabs-write");
				exit();
			}
		}
	}
	
	
	/** DISPLAY COMMENT FORM
	 * 
	 *
	 *
	 */
	function DisplayCommentForm($product_id="")
	{
		global $_SETTINGS;
		global $_SESSION;
		global $_POST;
		//global $_REQUEST;
		
		echo "<div class='product-comment-form'>";
		
			report($_REQUEST['REPORT'],$_REQUEST['SUCCESS']);
		
			// IF LOGIN IS REQUIRED
			if($_SETTINGS['product_review_require_login'] == '1' and $_SESSION['UserAccount']['userid'] == ''){
				
				$UserAccount = new UserAccounts;
				$UserAccount->LoginForm();				
				
			} else {
				
				$productName = lookupDbValue('ecommerce_products','name',$product_id,'product_id');
				
				echo "
						<form action='' method='post'>
							<p><label>Product Name</label> <span class='commentproductname'>".$productName."</span></p>
							<p>
								<label>Rating</label>
								<span class='ratings'>
									<input name='rating' type='radio' value='1' ".ischecked($_POST['rating'],'1')." class='starform' />
									<input name='rating' type='radio' value='2' ".ischecked($_POST['rating'],'2')." class='starform' />
									<input name='rating' type='radio' value='3' ".ischecked($_POST['rating'],'3')." class='starform' />
									<input name='rating' type='radio' value='4' ".ischecked($_POST['rating'],'4')." class='starform' />
									<input name='rating' type='radio' value='5' ".ischecked($_POST['rating'],'5')." class='starform' />
								</span>
								<script>
									$('.starform').rating();
								</script>
							</p>
							
							<p>
								<label>Do you own this?</label>
								<input type='radio' value='1' ".ischecked($_POST['bought'],'1')." name='bought'> Yes &nbsp; &nbsp; &nbsp; <input type='radio' value='0' ".ischecked($_POST['bought'],'0')." name='bought'> No
							</p>
							<p>
								<label>Name*</label>
								<input type='text' name='name' value='".$_POST['name']."'>
							</p>
							<p>
								<label>Email*</label>
								<input type='text' name='email' value='".$_POST['email']."'>
							</p>
							<p>
								<label> &nbsp;</label>
								<textarea name='content'>".$_POST['content']."</textarea>
							</p>";
							
						/*
							<br>
							<div class='captcha'>";						
							dsp_crypt(0,1);
							echo "</div>
							<br>
						*/

				echo "<br><div class='captcha'>";
				global $recaptchapublickey;
				echo recaptcha_get_html($recaptchapublickey);
				echo "</div><br>";
				
							
							
				echo "			<p>
								<label> &nbsp;</label>
								<input type='hidden' name='product_id' value='".$product_id."'>
								<input type='submit' class='button' name='SUBMITCOMMENT' value='Submit Review'>
							</p>
						</form>		
				";			
			}
		echo "</div>";
	}
	
	/** DISPLAY CATEGORIES
	 *
	 *
	 * Display the product categories
	 *
	 */
	function DisplayCategories()
	{
	
		//
		// LEVEL 1
		//
		$select = "";				
	
	}
	
	/** SET SHOPPING CART
	 *
	 *
	 * Utility function to set the shopping cart
	 *
	 */
	function setShoppingCart()
	{
		global $_REQUEST;
		global $_POST;
		global $_SETTINGS;
		global $_SESSION;
		
		$sessionrandomphrase = md5($_SETTINGS['session_random_phrase']);
		
		//echo "PHRASE MATCH ".$sessionrandomphrase."";
		
		if($_SESSION["shoppingcart-".$sessionrandomphrase.""] != ''){
			$sel = "SELECT * FROM ecommerce_shopping_carts WHERE shopping_cart_id='".$_SESSION['shoppingcart-'.$sessionrandomphrase.'']."' AND locked='0'";
			//$sel = "SELECT * FROM ecommerce_shopping_carts WHERE shopping_cart_id='".$_SESSION['shoppingcart-'.$sessionrandomphrase.'']."'";
			$res = doQuery($sel);
			$row = mysql_fetch_array($res);
			if($row['shopping_cart_id'] != ""){
				//die("cart should not change");
				//exit;
				return true;
			}
			//return true;
		}
		
		// CHECK IF USER IS LOGGED IN GET THE IP
		if($_SESSION['UserAccount']['userid'] == ''){
			// CHECK IF THERE IS A SHOPPING CART FOR THIS IP ADDRESS
			$user_ip = getUserIP();				
			
			// GET THE CART FROM THE IP
			$sel1 = "SELECT * FROM ecommerce_shopping_carts WHERE ip='".$user_ip."' AND locked='0' ORDER BY created DESC LIMIT 1";
			$res1 = doQuery($sel1);
			$num1 = mysql_num_rows($res1);	
			
		} else {
			// IF USER LOGGED IN GET THE ACCOUNT
			$user_id = $_SESSION['UserAccount']['userid'];
			$sel = "SELECT * FROM user_account WHERE account_id='".$user_id."' AND active='1'";
			$res = doQuery($sel);
			$rowaccount = mysql_fetch_array($res);		
			
			// GET THE CART FROM THE USER ID
			$sel1 = "SELECT * FROM ecommerce_shopping_carts WHERE account_id='".$user_id."' AND locked='0' ORDER BY created DESC LIMIT 1";
			$res1 = doQuery($sel1);
			$num1 = mysql_num_rows($res1);
			
		}
		
		if($num1){
			// IF THERE IS A CART
			$row1 = mysql_fetch_array($res1);
			$cartid = $row1['shopping_cart_id'];
		} else {
			// IF NO CART THEN CREATE THE CART
			$cartid = nextId('ecommerce_shopping_carts');
			$sel2 = "INSERT INTO ecommerce_shopping_carts SET ".
					"account_id='".$user_id."',".
					"ip='".$user_ip."',".
					"active='1',".
					"created=NULL";
			doQuery($sel2);
		}		
		
		// SET CART
		$_SESSION['shoppingcart-'.$sessionrandomphrase.''] = $cartid;		
		return true;
	}
	
	/** DISPLAY SHIPPING ESTIMATOR
	 *
	 *
	 *
	 */
	function DisplayShippingEstimator()
	{
		echo "<span class='checkoutsubtitle'>Shipping Estimator</span>";
		echo "<form class='shipping-estimator-form' action='' method='post'>";

		echo "<p><label>Zip Code</label> <input type='text' name='zip_code' class='zipcode-field'></p>";

		echo "</form>";
	}
	 
	/** FORM ACTION
	 *
	 *
	 * Add to cart form action
	 * SHOPPING CARTS ARE DATABASE DRIVEN AND ITEMIZED 
	 * 
	 * PRODUCT ID IS $_POST['PRODUCTID']
	 * QTY IS $_POST['QTY']
	 *
	 */
	function AddToCartFormAction()
	{
		global $_REQUEST;
		global $_POST;
		global $_SETTINGS;
		global $_SESSION;
		
		// GO TO PRODUCT DETAILS / VIEW DETAILS
		if($_POST['VIEWDETAILS'] != ""){
		
			// GET PRODUCT NAME
			$name = lookupDbValue('ecommerce_products', 'name', $_POST['PRODUCTID'], 'product_id');
		
			// GO TO PRODUCT DETAIL PAGE
			header("Location: ".$_SETTINGS['website']."".$_SETTINGS['product_detail_page_clean_url']."/".$name."");
			exit();
			return true;
		}
		
		// GET SESSION RANDOM PHRASE
		$sessionrandomphrase = md5($_SETTINGS['session_random_phrase']);	
		$cartid		= $_SESSION['shoppingcart-'.$sessionrandomphrase.''];
		
		// PRODUCT ID AND QTY
		$productid 	= $_POST['PRODUCTID'];
		$qty		= $_POST['QTY'];
		$note 		= $_POST['NOTE'];		
		
		// GET PRODUCT
		$sel = "SELECT * FROM ecommerce_products WHERE product_id='".$productid."'";
		$res = doQuery($sel);
		$ro = mysql_fetch_array($res);
		
		// CHECK INVENTORY
		if($ro['take_inventory'] == '1'){
			$currentInventory = $ro['inventory'];
			if($qty > $currentInventory){
				$qty = $currentInventory;
				$productName = lookupDbValue('ecommerce_products','name',$row['product_id'],'product_id');
				$report1 = "Your quantity for ".$productName." is more than we have in our inventory_ Your cart has been updated_";				
			}
		}		
		
		// ADD TO CART
		if($_POST['ADDTOCART'] != ""){
			// ADD ITEM TO CART
			// NEXT CART ROW
			$nextCartRow = nextId("ecommerce_product_cart_relational");
			$selinsert = 	"INSERT INTO ecommerce_product_cart_relational SET ".
							"shopping_cart_id='".$cartid."',".
							"product_id='".$productid."',".
							"qty='".$qty."',".							
							"price='".$ro['price']."',".
							"flat_discount='".$ro['flat_discount']."',".
							"rate_discount='".$ro['rate_discount']."',".
							"note='".$note."'";
			doQuery($selinsert);
			
			// GET POSTED ATTRIBUTES
			// FORMAT: attribute[]
			foreach($_POST['attribute'] as $item){
				// TO INSERT A CART >> PRODUCT >> ATTRIBUTE RELATIONAL ITEM A PRODUCT IN THE CART EXISTS AS A RELATIONAL ITEM
				// NEED THE RELATIONAL ITEM AND THE ATTRIBUTE ID AND VALUE
								
				// GET THE ATTRIBUTE ID THROUGH THE ATTRIBUTE VALUE ID
				$select = "SELECT * FROM ecommerce_product_attribute_values WHERE attribute_value_id='".$item."'";
				$result = doQuery($select);
				$row = mysql_fetch_array($result);				
				$attribute_id = $row['attribute_id'];
				
				// ADD ATTRIBUTES TO ITEM IN CART
				//if(is_int($item)){ $column = "attribute_value_id"; } else { $column = "value"; }
				$selinsert = 	"INSERT INTO ecommerce_product_attribute_cart_relational SET ".
								"relational_item_id='".$nextCartRow."', ".
								"attribute_id='".$attribute_id."', ".
								"attribute_value_id='".$item."', ".
								"price='".$row['price']."',".
								"value='".$item."'";
				doQuery($selinsert);		
			}
			
			// GO TO SHOPPING CART PAGE
			$success = 1;
			$report = "Successfully added to cart_ ".$report1."";
			header("Location: ".$_SETTINGS['website']."".$_SETTINGS['shopping_cart_page_clean_url']."/0/".$report."/".$success."/0");
			exit();
		}
		
		// UPDATE ITEM
		if($_POST['UPDATEITEM']){
			
			// GET THE CART ITEM ID
			
			// ADD ITEM TO CART
			// NEXT CART ROW
			//$nextCartRow = nextId("ecommerce_product_cart_relational");
			$cartRow = $_POST['cartitem_id'];
			$selinsert = 	"UPDATE ecommerce_product_cart_relational SET ".
							"shopping_cart_id='".$cartid."',".
							"product_id='".$productid."',".
							"qty='".$qty."',".
							"price='".$ro['price']."',".
							"flat_discount='".$ro['flat_discount']."',".
							"rate_discount='".$ro['rate_discount']."',".
							"note='".$note."' WHERE item_id='".$cartRow."'";
			doQuery($selinsert);
			
			// GET POSTED ATTRIBUTES
			// FORMAT: attribute[]
			
			// SINCE EDITING THE PRODUCT DELETE THE OLD ATTRIBUTES FIRST
			doQuery("DELETE FROM ecommerce_product_attribute_cart_relational WHERE relational_item_id='".$cartRow."'");
			
			foreach($_POST['attribute'] as $item){
				// TO INSERT A CART >> PRODUCT >> ATTRIBUTE RELATIONAL ITEM A PRODUCT IN THE CART EXISTS AS A RELATIONAL ITEM
				// NEED THE RELATIONAL ITEM AND THE ATTRIBUTE ID AND VALUE
								
				// GET THE ATTRIBUTE ID THROUGH THE ATTRIBUTE VALUE ID
				$select = "SELECT * FROM ecommerce_product_attribute_values WHERE attribute_value_id='".$item."'";
				$result = doQuery($select);
				$row = mysql_fetch_array($result);				
				$attribute_id = $row['attribute_id'];
				
				// ADD ATTRIBUTES TO ITEM IN CART
				//if(is_int($item)){ $column = "attribute_value_id"; } else { $column = "value"; }
				$selinsert = 	"INSERT INTO ecommerce_product_attribute_cart_relational SET ".
								"relational_item_id='".$cartRow."', ".
								"attribute_id='".$attribute_id."', ".
								"attribute_value_id='".$item."',".
								"price='".$row['price']."',".
								"value='".$item."'";
				doQuery($selinsert);		
			}
			
			// GO TO SHOPPING CART PAGE
			$success = 1;
			$report = "Item updated_ ".$report1."";
			header("Location: ".$_SETTINGS['website']."".$_SETTINGS['shopping_cart_page_clean_url']."/0/".$report."/".$success."/0");
			exit();
		}
		
	}

	/** ATTRIBUTES
	 *
	 *
	 *
	 */
	function CartItemAttributes($cartItemId)
	{
		// GET THE RELATIONAL ATTRIBUTES FOR THE CART ITEM ORDER BY ATTRIBUTE ID TO GROUP THE ATTRIBUETES
		$select = "SELECT * FROM ecommerce_product_attribute_cart_relational WHERE relational_item_id='".$cartItemId."' ORDER BY attribute_id";
		$result = doQuery($select);
		$num = mysql_num_rows($result);
		$i = 0;
		echo "<ul class='product-attributes'>";
		while($i<$num){
			$row = mysql_fetch_array($result);
			
			// GET THE ATTRIBUTE
			$select1 = "SELECT * FROM ecommerce_product_attributes WHERE attribute_id='".$row['attribute_id']."' LIMIT 1";
			$result1 = doQuery($select1);
			$attribute = mysql_fetch_array($result1);
			echo "<li><label class='product-attribute-label'>".$attribute['label']."</label> ";
			
			// GET THE ATTRIBUTE VALUE
			$select1 = "SELECT * FROM ecommerce_product_attribute_values WHERE attribute_value_id='".$row['attribute_value_id']."' LIMIT 1";
			$result1 = doQuery($select1);
			$attributeValue = mysql_fetch_array($result1);
			echo "".$attributeValue['name']."</li>";
			
			$i++;
		}
		echo "</ul>";
		$select = "SELECT * FROM ecommerce_product_cart_relational WHERE item_id='".$cartItemId."' LIMIT 1";
		$result = doQuery($select);
		$row = mysql_fetch_array($result);
		if($row['note'] != ""){
			echo "<p class='product-note'><label>Note</label> \"<i>".$row['note']."</i>\"</p>";
		}
	}
	
	
	/** FORM DISPLAY
	 *
	 *
	 * Checkout page form. Displays the checkout page / cart contents
	 *
	 */
	function DisplayShoppingCart($heading=0,$implicit=0)
	{
		global $_SETTINGS;
		global $_REQUEST;
		global $_POST;
		global $_COOKIE;
		global $_SESSION;
			
		$sessionrandomphrase = md5($_SETTINGS['session_random_phrase']);
		$display = 0;
		
		$flag = $_SETTINGS['shopping_cart_page_clean_url'];
		if($flag == $_REQUEST['page']){		
			$display = 1;
		}
		
		// CHECK IF IMPLICIT
		if($implicit == 1){
			$display = 1;
		}
		
		if($display == 1){
			// HEADING
			if($heading == 1){ echo "<span class='checkouttitle'>Shopping Cart</span>"; } 
			
			// DEBUGING
			if($_SETTINGS['debug'] == 1 ){			
				echo "<br>SETTING CURRENCY ID ".$_SETTINGS['currency'];
				echo "<br>CURRENCY ".$this->currency;
				echo "<br>";
			}
			
			// FORM/TABLE START
			echo	"<form method='POST'>";
			
			$this->theShoppingCart(false);		
			
			/*
			echo 	"<div class='checkout-box'><h2>Shipping Estimator</h2>";
			echo 	"	<p><label>Zip Code</label> <input type='text' name='shipping-zip' class='zipcode-field'></p>";
			echo 	"	<p><input type='submit' name='ESTIMATESHIPPING' value='Estimate Shipping'></p>";
			echo 	"</div>";
			*/
			
			
			if($this->checkCartEmpty() == false){
			
				$cartid = $this->getCartId();
				
				$code = lookupDbValue('ecommerce_shopping_carts','coupon_code',$cartid,'shopping_cart_id');
				
				/*
				if($REQUEST['FORM1'] != ""){
					if(strstr($_REQUEST['FORM1'],"coupon")){
						$couponArray = explode("|",$_REQUEST['FORM1']);
						$code = $couponArray[1];
						
					}
				}*/
				//$code = lookupDbValue('ecommerce_coupon_codes','code',$code,'coupon_id');
				
				echo 	"<div class='checkout-box'><h2>Promo Code</h2><small class='how-to-get-promo'><a href='".$_SETTINGS['website'].$_SETTINGS['how_do_i_get_promos_url']."'>How to I get these?</a></small>";
				echo 	"	<p><label>Promo Code</label> <input class='promo-field' type='text' name='promo' value='".$code."'></p>";
				echo 	"	<p><input type='submit' name='APPLYPROMO' value='Apply Promo Code'></p>";		
				echo 	"</div>";
				
				
				
				// NOT LOGGED IN
				if($_SESSION['UserAccount']['userid'] == ''){
					
					echo 	"<div class='checkout-box-last guest-checkout'>";
					echo	"	<h2>Checkout</h2><p>Don't have an account? Click continue to quickly checkout.</p>";
					echo 	"	<p><input class='checkout-button express-checkout-button' type='submit' name='EXPRESSCHECKOUT' value='Checkout'></p>";
					echo 	"</div>";
					
					echo	"<div class='checkout-box-last returning-customer'>";
					echo 	"	<h2>Returning Customer</h2><p>Enter your email and password to log in and checkout.</p>";
					echo	"	<p><label>*Email</label><input type='text' name='username' value='".$_POST['username']."'>";
					echo 	"	<p><label>*Password</label><input type='password' name='password' value='' onkeydown='if (event.keyCode == 13) document.getElementById(\"LOGIN\").click()'></p>";
					echo	"	<input type='hidden' name='referer' value='".$_SETTINGS['checkout_page_clean_url']."'>";
					echo	"	<p><input class='checkout-button login-checkout-button' type='submit' name='LOGIN' id='LOGIN' value='Login and Checkout'></p>";
					echo 	"</div>";
				
				} else {
					echo 	"<div class='checkout-box-last'><h2>Checkout</h2><p><input class='checkout-button' type='submit' name='CHECKOUT' value='Checkout'></p></div>";
				}
			}
			
			//echo 	"</div>";
			
			echo 	"</form>";
		}
	}
	
	/** CHECK IF CART EMPTY
	 *
	 *
	 * RETURN FALS IF THERE ARE ITEMS IN CART
	 *
	 */
	function checkCartEmpty()
	{
		global $_SETTINGS;
		global $_REQUEST;
		global $_POST;
		global $_COOKIE;
		global $_SESSION;
			
		$sessionrandomphrase = md5($_SETTINGS['session_random_phrase']);
		$cartid		= $_SESSION['shoppingcart-'.$sessionrandomphrase.''];
		
		// GET CART ITEMS
		$selinsert = 	"SELECT * FROM ecommerce_product_cart_relational WHERE ".
						"shopping_cart_id='".$cartid."'";
		$selresult = 	doQuery($selinsert);
					
		$selnum = mysql_num_rows($selresult);
		
		if($selnum > 0){
			return false;
		} else {
			return true;
		}
	}
	
	/** JUST THE CART
	 *
	 *
	 *
	 */
	function theShoppingCart($readOnly=false,$forcecartid="")
	{
	
		global $_SETTINGS;
		global $_REQUEST;
		global $_POST;
		global $_COOKIE;
		global $_SESSION;
			
		$sessionrandomphrase = md5($_SETTINGS['session_random_phrase']);
		
		echo 	"<table class='shoppingcart-table'>";			
		// HEADING
		echo		"<tr>";
		
		if($readOnly == false){ echo	"<th style='font-size:10px; width:10px;' class='price-header'>Remove</th>"; }
		
		echo			"<th colspan='2'>Cart Contents</th>";
		echo			"<th class='qty-header'>Quantity</th>";
		echo			"<th class='price-header' style='text-align:right;'>Price</th>";
		echo			"<th class='price-header' style='text-align:right;'>Total</th>";
		echo		"</tr>";
				
		$cartid		= $_SESSION['shoppingcart-'.$sessionrandomphrase.''];
		
		if($forcecartid != ""){
			$cartid = $forcecartid;
		}		
		
		// GET CART ITEMS
		$selinsert = 	"SELECT * FROM ecommerce_product_cart_relational WHERE ".
						"shopping_cart_id='".$cartid."'";
		$selresult = 	doQuery($selinsert);
					
		$seli = 0;
		$selnum = mysql_num_rows($selresult);
		while($seli<$selnum){
			$selrow = mysql_fetch_array($selresult);
			
			$select = "SELECT * FROM ecommerce_products WHERE product_id='".$selrow['product_id']."'";
			$result = doQuery($select);
			$i = 0;
			$num = mysql_num_rows($result);
			while($i<$num){
				if($seli % 2){ $class = "screven"; } else { $class = "scrodd"; }
				//if($i & 1) { $class = "odd"; } else { $class = "even"; }
				//echo "$class <br>";
				//echo "I: $i <br>";
				$row = mysql_fetch_array($result);
				echo		"<tr class='shopping-cart-row ".$class."'>";
				if($readOnly == false){	echo	"<td style='text-align:center;'><input type='checkbox' name='remove[]' value='".$selrow['item_id']."'></td>"; }
				
				// IF PRODUCT FOLLOWS NAMING CONVENTION
				if($row['naming_convention'] == '1'){
					$imagenameFormated = strtolower(str_replace(" ","_",$row['name']).".jpg");	
				} else {
					$imagenameFormated = $row['image'];
				}
				
				$path = $this->formatThumbnail($imagenameFormated);
				
				echo			"<td class='shopping-cart-small-image-cell'><img class='shopping-cart-small-image' width='94' style='width:94px;' src='".$path."'></td>";
				echo			"<td>";
				echo			"".$row['name']."";				
				//echo "<Br>";
				//echo			"<label>Product Id</label> ".$row['product_number']."<br>";
				if($readOnly == false){
					echo " <a class='item-edit' href='".$_SETTINGS['website']."".$_SETTINGS['product_detail_page_clean_url']."/".$row['name'].":edit:".$selrow['item_id']."'>Edit</a>";
				}
				// GET CART ITEM ATTRIBUTES
				$this->CartItemAttributes($selrow['item_id']);
				
				//echo "<br>";
				
				
				//echo 			"// Attributes Here";
				//echo			"<label>Product Id</label> ".$row['product_number']."<br>";
				
				echo			"</td>";
				if($readOnly == false){
					if($row['hidden_promo'] == '1'){
						echo 			"<td style='text-align:right;'>".$selrow['qty']."<input type='hidden' id='QTY-".$selrow['item_id']."' name='QTY-".$selrow['item_id']."' value='".$selrow['qty']."'></td>";
					} else {
						echo			"<td style='text-align:right;'><input type='text' style='width:40px;' id='QTY-".$selrow['item_id']."' name='QTY-".$selrow['item_id']."' value='".$selrow['qty']."'></td>";
					}				
				} else {
					echo 			"<td style='text-align:right;'>".$selrow['qty']."</td>";
				}
				
			
				// FORMAT PRICE
				$discount = '0.00';
				if($selrow['flat_discount'] != '0.00' || $selrow['rate_discount'] != '0.00'){
					
					// IF FLAT DISCOUNT
					if($selrow['flat_discount'] != '0.00'){
						$discount = $selrow['flat_discount'];
						
					}
					// IF RATE DISCOUNT
					elseif($selrow['rate_discount'] != '0.00'){
						$discount = $selrow['price'] * $selrow['rate_discount'];
					}
					
					
				}
				
				$list_price = money_format('%i',$selrow['price']);
				$new_price = $selrow['price'] - $discount;
				$new_price = money_format('%i',$new_price);
				
				if($list_price != $new_price){
					echo			"<td style='text-align:right;'><del>".$this->currency.$list_price."</del> ".$this->currency.$new_price."</td>";
				} else {
					echo			"<td style='text-align:right;'>".$this->currency.$new_price."</td>";
				}
				
				$qty_price = $new_price * $selrow['qty'];			
				echo 			"<td style='text-align:right;'>".$this->currency.money_format('%i',$qty_price)."</td>";				
				echo		"</tr>";
				$i++;
				
				
			}
			$seli++;
		}
		if($selnum == 0){
			echo		"<tr>";
			echo 			"<td colspan='6' style='text-align:left;'>Your Shopping Cart is Empty</td>";
			echo		"</tr>";
		}
		
		//$totalsArray = $this->calculateTotals($forcecartid);
		$totalsArray = $this->calculateTotals($cartid);
		
		// SUBOTAL ROW
		echo		"<tr class='subtotal-row' style='text-align:right;'>";
		if($readOnly == false){ $colspan = "4"; } else { $colspan = "3"; }
		echo			"<td colspan='".$colspan."'>";
		if($selnum != 0 AND $readOnly == false){
			echo		"<input class='update-cart-button' type='submit' name='UPDATESHOPPINGCART' value='Update Cart'>";
		}
		echo			"</td>";
		echo			"<td class='total'>Subtotal</td>";
		
		
		
		echo			"<td class='total subtotal'>";

		// SUBTOTAL
		if($totalsArray[4] > 0){
			echo 			"".$this->currency.money_format('%i',$totalsArray[0])."";
		} else {
			echo 			$this->currency.money_format('%i',$totalsArray[0]);
		}
		
		
		
		// ACCOUNT DISCOUNT
		if($totalsArray[5] > 0){
			echo 			"<br><i style='color:red; border-bottom:1px solid #fff;'>- ".$this->currency.money_format('%i',$totalsArray[5])."</i><br>";
		}
				
		// PROMO CODE DISCOUNT
		if($totalsArray[6] > 0){		
			echo 			"<br><i style='color:red; border-bottom:1px solid #fff;'>- ".$this->currency.money_format('%i',$totalsArray[6])."</i><br>";
		}				

		if($totalsArray[7] > 0){
			echo 			"<br><i style='color:red; border-bottom:1px solid #fff;'>- ".$this->currency.money_format('%i',$totalsArray[7])."</i><br>";
		}
		
		if($totalsArray[4] > 0){
			$discountSub = $totalsArray[0] - $totalsArray[5] - $totalsArray[6];
			echo 			"".$this->currency.money_format('%i',$discountSub)."";
		}
		
		// PROMO CODE
		if($_SESSION['promo_code_id'] != ""){
			echo			"<div class='promoflag'>".lookupDbValue('ecommerce_coupon_codes','name',lookupDbValue('ecommerce_shopping_carts','coupon_code_id',$cartid,'shopping_cart_id'),'coupon_id')."</strong></div>";
		}
		
		//echo 			" ".var_dump($totalsArray)." </td>";
		echo		"</tr>";
		

		
		// S&H ROW
		echo		"<tr class='total-row' style='text-align:right;'>";
		echo			"<td colspan='".$colspan."'>";
		if($selnum != 0 AND $readOnly == false){
			echo			"<input class='empty-cart-button' type='submit' name='EMPTYSHOPPINGCART' value='Empty Cart'>";
		}
		echo			"</td>";
		echo			"<td class='total'>S&H</td>";
		echo			"<td class='total sh'>".$this->currency.money_format('%i',$totalsArray[3])."</td>";
		echo		"</tr>";
		
		// TAX ROW
		echo		"<tr class='tax-row' style='text-align:right;'>";
		echo			"<td  colspan='".$colspan."'>&nbsp;</td>";
		echo			"<td class='total'>Tax</td>";
		echo			"<td class='total tax'>".$this->currency.money_format('%i',$totalsArray[1])."</td>";
		echo		"</tr>";
		
		// TOTAL ROW
		echo		"<tr class='total-row' style='text-align:right;'>";
		echo			"<td colspan='".$colspan."'>&nbsp;</td>";
		echo			"<td class='total'>Total</td>";
		echo			"<td class='total grandtotal'>".$this->currency.money_format('%i',$totalsArray[2])."</td>";
		echo		"</tr>";
		
		// TABLE FORM ENDS
		echo	"</table>";	
	
	
	}
	
	 /** FORM ACTION
	 *
	 *
	 * Begin Checkout Process Form Action
	 *
	 */
	function ShoppingCartFormAction()
	{
		global $_SETTINGS;
		global $_REQUEST;
		global $_POST;
		global $_COOKIE;
		global $_SESSION;
		
		// GET CART ID
		$cartid = $this->getCartId();
		
		//
		// EXPRESS CHECKOUT
		//
		if($_POST['EXPRESSCHECKOUT'] != ""){
			header("Location: ".$_SETTINGS['website']."".$_SETTINGS['checkout_page_clean_url']."");
			exit();
		}
				
		//
		// NORMAL CHECKOUT
		//
		if($_POST['CHECKOUT'] != ""){		
			//echo "madeit";
			//die;
			//exit;
			header("Location: ".$_SETTINGS['website']."".$_SETTINGS['checkout_page_clean_url']."");
			exit();
		}
		
		//
		// THE LOGIN VIA CHECKOUT IS HANDLED IN THE USER ACCOUNT LOGIN FUNCTION
		//
		//...
		
		//
		// UPDATING THE SHOPPING CART
		//
		if($_POST['UPDATESHOPPINGCART'] != ""){		
			//
			// REMOVE PRODUCTS
			//
			// CHECK IF REMOVED PRODUCTS
			if($_POST['remove'] != ""){
				// LOOP REMOVE SELECTED PRODUCTS
				foreach($_POST['remove'] AS $cartitem){
					//echo "".$cartitem."<br>";
					$sel = 	"DELETE FROM ecommerce_product_cart_relational WHERE ".
							"item_id='".$cartitem."'";
					doQuery($sel);
				}
			}
			
			//
			// UPDATE QUANTITIES
			//
			// GET ALL PRODUCTS IN CART
			$select = "SELECT * FROM ecommerce_product_cart_relational WHERE shopping_cart_id='".$cartid."'";
			$result = doQuery($select);
			$num 	= mysql_num_rows($result);
			$i = 0;
			while($i<$num){
				$row = mysql_fetch_array($result);
				$newQTY = $_POST['QTY-'.$row['item_id'].''];
								
				// GET PRODUCT
				$sel = "SELECT * FROM ecommerce_products WHERE product_id='".$row['product_id']."'";
				$res = doQuery($sel);
				$ro = mysql_fetch_array($res);
				
				// CHECK INVENTORY
				if($ro['take_inventory'] == '1'){
					$currentInventory = $ro['inventory'];					
					if($newQTY > $currentInventory){						
						$newQTY = $currentInventory;
						$productName = lookupDbValue('ecommerce_products','name',$row['product_id'],'product_id');
						$report1 .= "Your quantity for ".$productName." is more than we have in our inventory_ Your cart has been updated_ ";
					}
				}	
				
				$update = "UPDATE ecommerce_product_cart_relational SET qty='".$newQTY."' WHERE item_id='".$row['item_id']."'";
				doQuery($update);
				$i++;
			}

			//
			// UPDATE ATTRIBUTES
			//
			
			// TO DO ...
			
			// REDIRECT
			$report = "Shopping Cart Updated_ ".$report1."";
			$success = "1";			
			header("Location: ".$_SETTINGS['website']."".$_REQUEST['page']."/0/".$report."/".$success."/0");
			exit();			
		}
		
		//
		// EMPTY SHOPPING CART BY DELETING ALL SHOPPING CARTS
		//
		if($_POST['EMPTYSHOPPINGCART'] != ""){
		
			// CHECK IF USER IS LOGGED IN GET THE IP
			if($_SESSION['UserAccount']['userid'] == ''){
				// CHECK IF THERE IS A SHOPPING CART FOR THIS IP ADDRESS
				$user_ip = getUserIP();				
			} else {
				// IF USER LOGGED IN GET THE ACCOUNT
				$user_id = $_SESSION['UserAccount']['userid'];
				$sel = "SELECT * FROM user_account WHERE account_id='".$user_id."' AND active='1'";
				$res = doQuery($sel);
				$rowaccount = mysql_fetch_array($res);				
			}
					
			// DELETE ITEMS		
			$sessionrandomphrase = md5($_SETTINGS['session_random_phrase']);
			$cart_id = $_SESSION['shoppingcart-'.$sessionrandomphrase.''];
			$sel1 = "DELETE FROM ecommerce_product_cart_relational WHERE shopping_cart_id='".$cart_id."' AND locked='0'";
			doQuery($sel1);		
			
			$update = "UPDATE ecommerce_shopping_carts SET coupon_code_id='' WHERE shopping_cart_id='".$cartid."'";
			doQuery($update);			
					
			// DELETE CARTS SO A NEW CART WILL BE STARTED
			//$sel1 = "DELETE FROM ecommerce_shopping_carts WHERE ip='".$user_ip."' OR account_id='".$user_id."'";			
			//doQuery($sel1);
			
			// REDIRECT
			$report = "Your Shopping Cart is Empty";
			$success = "1";			
			header("Location: ".$_SETTINGS['website']."".$_REQUEST['page']."/0/".$report."/".$success."/0");
			exit();		    	
		}		

		//
		// APPLY PROMO
		//
		if($_POST['APPLYPROMO'] != ""){
			if($_POST['promo'] != ""){
				$error = 0;
				$freeProduct = 0;
				// PROMO CODE VALIDATION
				// CHECK IF THE CODE IS VALID
				$today = date("Y-m-d");
				$totalArray = $this->calculateTotals();
							
				$codeFound = 0;
				
				// MULTI CODE MATCH
				if($codeFound == 0){				
					// GET THE CODE
					$codeselect = 	"SELECT * FROM ecommerce_coupon_codes WHERE "; 	// INITIAL SELECT
					$codeselect .= 	"multiple_codes LIKE '%".$_POST['promo']."%' AND ";									
					$codeselect .= 	"(`start_date` <= '".$today."' AND ";			// START DATE
					$codeselect .= 	"`expiration_date` >= '".$today."') AND ";		// EXPIRATION DATE
					$codeselect .=  "`status`='Active' AND ";
					$codeselect .= 	"`active`='1' LIMIT 1";
					$coderesult = doQuery($codeselect);
					$codenum = mysql_num_rows($coderesult);
					if($codenum > 0){
						$codeFound = 1;
					}
				}
				
				// IF NO MULTI CODE FOUND CHECK FOR OTHER CODES
				if($codeFound == 0){
					// GET THE CODE
					$codeselect = 	"SELECT * FROM ecommerce_coupon_codes WHERE "; 	// INITIAL SELECT
					// RANGE CODE
					if(strstr($_POST['promo'],"-")){
						$promoArray = explode("-",$_POST['promo']);
						$promocode = $promoArray[1];
						$codeSql = "code_range_prefix='".$promoArray[0]."-' AND (code <= '".$promocode."' AND code_range_end >= '".$promocode."')";
					}
					// NORMAL CODE
					else {
						$promocode = $_POST['promo'];
						$codeSql = "code='".$_POST['promo']."'";
					}
					
					$codeselect .= 	"".$codeSql." AND ";									
					$codeselect .= 	"(`start_date` <= '".$today."' AND ";			// START DATE
					$codeselect .= 	"`expiration_date` >= '".$today."') AND ";		// EXPIRATION DATE
					$codeselect .=  "`status`='Active' AND ";
					$codeselect .= 	"`active`='1' LIMIT 1";
					$coderesult = doQuery($codeselect);
				}
				
				//echo "<br>select: $codeselect <br>";				
				
				$coderow = mysql_fetch_array($coderesult);
				
				
				
				// CHECK IF THE CODE EXISTS, IS NOT EXPIRED, ETC
				if($coderow['coupon_id'] == ''){
					$error = 1;
					$_REQUEST['SUCCESS'] = 0;
					$_REQUEST['REPORT'] = 'Coupon code '.$_POST['promo'].' is not valid_';
				}
				
			
				
				if($error == 0){
				
					// CHECK IF RANGE AND HAS BEEN USED
					if($coderow['code_range_end'] != ""){
						$selorder = "SELECT promo_code FROM ecommerce_orders WHERE promo_code='".$_POST['promo']."' LIMIT 1";
						$resorder = doQuery($selorder);
						$numorder = mysql_num_rows($resorder);
						if($numorder > 0){
							$error = 1;
							$_REQUEST['SUCCESS'] = 0;
							$_REQUEST['REPORT'] = 'Coupon code '.$_POST['promo'].' has already been used_';
						}
					}
				
					// CHECK MIN SUBTOTAL
					if($coderow['min_subtotal'] > $totalArray[0] AND $coderow['min_subtotal'] > 0){
						$error = 1;
						$_REQUEST['SUCCESS'] = 0;
						$_REQUEST['REPORT'] = "Coupon code requires a cart subtotal minimum of $".$coderow['min_subtotal']."_";				
					}
					
					// CHECK IF THE CODE IS FOR THIS CUSTOMER'S ACCOUNT TYPE
					$permissionId = $_SESSION['UserAccount']['userpermission'];
					if($_SESSION['UserAccount']['userpermission'] == ""){
						$permissionId = $_SETTINGS['new_account_permission'];
					} else {
						$permissionId = $_SESSION['UserAccount']['userpermission'];
					}
					$perselect = "SELECT * FROM ecommerce_coupon_permission_relational WHERE coupon_id='".$coderesult['coupon_id']."' AND permission_id='".$permissionId."' LIMIT 1";
					$perresult = doQuery($perselect);
					if(mysql_num_rows($perresult) > 0){
						$error = 1;
						$_REQUEST['SUCCESS'] = 0;
						$_REQUEST['REPORT'] = 'Coupon code '.$_POST['promo'].' is not intended for your account type_';
					}				
					
					// CHECK IF THE CODE IS OVERUSED
					// Get the numbrer of times the code has been used
					$numselect = "SELECT * FROM ecommerce_orders WHERE promo_code_id='".$coderow['coupon_id']."' AND (status='New' || status='Open' || status='Shipped') AND active='1'";
					$numresult = doQuery($numselect);
					$timesUsed = mysql_num_rows($numresult); // times used
					if($coderow['max_qty'] > 0 AND $timesUsed >= $coderow['max_qty']){
						$error = 1;
						$_REQUEST['SUCCESS'] = 0;
						$_REQUEST['REPORT'] = 'Coupon code has been used the maximum times alloted_ '.$timesUsed.' / '.$coderow['max_qty'].'_';
					}
									
					// GET THE ITEMS IN THE CART
					$prodselect = 	"SELECT a.product_id,c.name,c.price FROM ecommerce_product_cart_relational a ".
									"LEFT JOIN ecommerce_products c ON a.product_id=c.product_id ".
									"WHERE a.shopping_cart_id='".$cartid."'";
					$prodresult = doQuery($prodselect);
					$prodnum 	= mysql_num_rows($prodresult);
					$i = 0;
							
					//if($coderow['free_promo_product_id'] != '0'){
						$promoflag = 1;	
						if($coderow['flag_text_match'] != ""){ $promoflag = 0; }
						if($coderow['free_promo_flag_product_id'] > 0){ $promoflag = 0; }
					//} else {
					//	$promoflag = 1;
					//}
					
					while($i<$prodnum){
					
						$prodrow = mysql_fetch_array($prodresult);
						//var_dump($prodrow);
						//echo "<br><br>--------------------------------------";
						//echo "<br><br>";
											
						// CHECK IF THIS COUPON CODE HAS SPECIFIED VALID PRODUCTS
						$validProduct = "SELECT * FROM ecommerce_coupon_valid_product_relational WHERE coupon_id='".$coderow['coupon_id']."'";
						$validResult = doQuery($validProduct);
						$validNum = mysql_num_rows($validResult);
						if($validNum > 0){
							// CHECK IF THERE ARE ONLY VALID PRODUCTS IN THE CART
							$validProduct = "SELECT * FROM ecommerce_coupon_valid_product_relational WHERE coupon_id='".$coderow['coupon_id']."' AND product_id='".$prodrow['product_id']."' LIMIT 1";
							$validResult = doQuery($validProduct);
							$validRow = mysql_fetch_array($validResult);
							if($validRow['product_id'] == $prodrow['product_id']){
								true;
							} else {
								$prodname = lookupDbValue('ecommerce_products','name',$prodrow['product_id'],'product_id');
								$error = 1;
								$_REQUEST['SUCCESS'] = 0;
								$_REQUEST['REPORT'] = 'This coupon code is not valid with '.$prodname.'_';
							}
						}
						
						// CHECK IF THIS COUPON CODE HAS SPECIFIED NOT VALID PRODUCTS
						$notvalidproduct = "SELECT * FROM ecommerce_coupon_not_valid_product_relational WHERE coupon_id='".$coderow['coupon_id']."'";
						$notvalidresult = doQuery($notvalidproduct);
						$notnum = mysql_num_rows($notvalidresult);
						if($notnum > 0){
							// CHECK IF THERE ARE ONLY VALID PRODUCTS IN THE CART
							$validProduct = "SELECT * FROM ecommerce_coupon_not_valid_product_relational WHERE coupon_id='".$coderow['coupon_id']."' AND product_id='".$prodrow['product_id']."' LIMIT 1";
							$validResult = doQuery($validProduct);
							$validRow = mysql_fetch_array($validResult);
							if($validRow['product_id'] == $prodrow['product_id']){
								$prodname = lookupDbValue('ecommerce_products','name',$prodrow['product_id'],'product_id');
								$error = 1;
								$_REQUEST['SUCCESS'] = 0;
								$_REQUEST['REPORT'] = 'This coupon code is not valid with '.$prodname.'_';
							} else {
								true;							
							}
						}	
						$i++;
										
					
						// IF FREE PROMO PRODUCT ID
						if($coderow['free_promo_product_id'] != '0'){							
							// IF FLAG TEXT
							if($coderow['flag_text_match'] != ""){
								//echo "FLAG TEXT MATCH<Br>";
								//echo "".strtolower($prodrow['name'])." = ".strtolower($coderow['flag_text_match'])."";
								//$promoflag = 0;						
								if(strstr(strtolower($prodrow['name']),strtolower($coderow['flag_text_match']))){
									$promoflag = 1;
								}			
							}
							
							// IF FLAG PRODUCT
							if($coderow['free_promo_flag_product_id'] > 0){
								//echo "FLAG PRODUCT MATCH<Br>";
								//$promoflag = 0;
								if($prodrow['product_id'] == $coderow['free_promo_flag_product_id']){
									$promoflag = 1;
								}				
							}
						}	
					}
				}
				
				if($promoflag == 0){
					$error = 1;
					$_REQUEST['SUCCESS'] = 0;
					$_REQUEST['REPORT'] = 'Your cart does not meet this code\'s requirements, or this code is invalid_';
				}
				
				
				// IF ERROR CHECK FOR A BDAY CODE
				if($error == 1)
				{
					// USER MUST BE LOGGED INTO TO DO A BDAY CODE
					if(isset($_SESSION['UserAccount']['userid']))
					{
						// IF CODE DOES NOT EXIST CHECK IF ITS A BIRTHDAY
						if(strstr($_POST['promo'],"/"))
						{
							// MUST HAVA A BDAY AND MATCH
							$theirBday = lookupDbValue('user_account','dob',''.$_SESSION['UserAccount']['userid'].'','account_id');
							$theirBdayArray = explode("-",$theirBday);
							$bdayPromoArray = explode("/",$_POST['promo']);
							$nowInSeconds = strtotime("now");
							$bdayInSeconds = strtotime("".date("Y")."-".$bdayPromoArray[0]."-".$bdayPromoArray[1]."");
							if($theirBdayArray[0] == $bdayPromoArray[2] AND $theirBdayArray[1] == $bdayPromoArray[0] AND $theirBdayArray[2] = $bdayPromoArray[1])
							{
								// IF IT IS A DATE AND IS LESS THAN TODAY
								if($bdayInSeconds < $nowInSeconds)
								{
									// IF THE CUSTOMER HASN't USED A BDAY CODE THIS YEAR
									$thisYear = lookupDbValue('user_account','used_birthday_promos',''.$_SESSION['UserAccount']['userid'].'','account_id');
									if(!strstr($thisYear,date("Y")))
									{
										$error = 0;
										//$_REQUEST['SUCCESS'] = 1;
										//$_REQUEST['REPORT'] = 'Birthday Coupon code '.$_POST['promo'].' applied_';
									}
								}
							}
						}
					}
				}
				
				
				if($error == 0){
					// IF CODE IS VALID JUST STORE IT IN THE DATABASE / THE TOTALS FUNCTION WILL CALCULATE THE REST
					// FORGET SESSIONS AND PROMO CODES
					// STORE THE CODE IN THE DB FOR THE CART
					// $_SESSION['coupon_code_id'] = $coderow['coupon_id'];	
					$update = "UPDATE ecommerce_shopping_carts SET coupon_code_id='".$coderow['coupon_id']."',coupon_code='".$_POST['promo']."' WHERE shopping_cart_id='".$cartid."'";
					doQuery($update);
					$_SESSION['promo_code_id'] 	= $coderow['coupon_id'];
					$_SESSION['promo_code']		= $_POST['promo'];
					
					// CHECK FOR A FREE PRODUCT
					if($coderow['free_promo_product_id'] != '0' AND $coderow['free_promo_product_id'] != false){
						$freeProductName = lookupDbValue('ecommerce_products','name',$coderow['free_promo_product_id'],'product_id');
						$report = "Your cart has qualified for this free product_ Please select your options and add this item to your cart_";
						$success = "1";
						header("Location: ".$_SETTINGS['website']."".$_SETTINGS['product_detail_page_clean_url']."/".$freeProductName."/".$report."/".$success."/0");
						exit();
					}
					
					// SUCCESS REDIRECT
					$report = "Promo code accepted_";
					$success = "1";			
					header("Location: ".$_SETTINGS['website']."".$_REQUEST['page']."/0/".$report."/".$success."/0");
					exit();		
				}
			}
			// IF POST PROM IS EMPTY
			else {
				$update = "UPDATE ecommerce_shopping_carts SET coupon_code_id='',coupon_code='".$_POST['promo']."' WHERE shopping_cart_id='".$cartid."'";
				doQuery($update);
				$_SESSION['promo_code_id'] 	= "";
				$_SESSION['promo_code']		= "";
				//$report = "Promo code accepted_ The id is ".$coderow['coupon_id']."_";
				//$success = "1";			
				header("Location: ".$_SETTINGS['website']."".$_REQUEST['page']."");
				exit();	
			}			
		}
		
		//
		// SHIPPING EST.
		// TODO...
		/*
		if($_POST['ESTIMATESHIPPING']){
			$error = 0;
			// ZIP CODE VALIDATION
			if($_POST['shipping-zip'] == ""){ $error = 1; $report = "Please Enter your Shipping Destination's Zip Code"; }
			
			if($error == 0){
				// REDIRECT
				$report = "Your Shopping Cart is Empty";
				$success = "1";			
				header("Location: ".$_SETTINGS['website']."".$_REQUEST['page']."/0/".$report."/".$success."/0");
				exit();		
			}
		}
		*/
	}
	
	/** FORM ACTION
	 *
	 *
	 * Checkout Information / Billing Information Form 
	 *
	 */
	function CheckoutInformationFormAction()
	{
		global $_SETTINGS;
		global $_REQUEST;
		global $_POST;
		global $_COOKIE;
		global $_SESSION;
		
		if($_POST['REVIEW'] != ""){
			$error = 0;
			
			// ACCOUNT VALIDATION
			if($_POST['email'] != ""){
				if(!VerifyEmail($_POST['email'])){ $error=1;$report="Enter a Valid Email Address Under Create an Account"; }
				if($_POST['password1'] == ''){ $error=1;$report="Enter a Password"; }
				if($_POST['password2'] == ''){ $error=1;$report="Re-Type your Password"; }
				if($_POST['password1'] != $_POST['password2']){ $error=1;$report="Your Passwords do not Match"; }
			}		
			
			// BILLING VALIDATION 
			if($_POST['billing_fname'] == ""){ $error=1;$report="Enter your First Name Under Billing Information"; }
			if($_POST['billing_email'] == ""){ $error=1;$report="Enter your Email Under Billing Information"; }
			if(!VerifyEmail($_POST['billing_email'])){ $error=1;$report="Enter a Valid Email Address"; }
			if($_POST['billing_lname'] == ""){ $error=1;$report="Enter your Last Name Under Billing Information"; }
			if($_POST['billing_address1'] == ""){ $error=1;$report="Enter your Address Under Billing Information"; }
			if($_POST['billing_city'] == ""){ $error=1;$report="Enter your City Under Billing Information"; }
			if($_POST['billing_zip'] == ""){ $error=1;$report="Enter your Zip Code Under Billing Information"; }
			if($_POST['billing_phone'] == ""){ $error=1;$report="Enter your Phone Number Under Billing Information"; }
			
			// SHIPPING VALIDATION
			if($_POST['shipping_fname'] == ""){ $error=1;$report="Enter your First Name Under Shipping Information"; }
			if($_POST['shipping_lname'] == ""){ $error=1;$report="Enter your Last Name Under Shipping Information"; }
			if($_POST['shipping_address1'] == ""){ $error=1;$report="Enter your Address Under Shipping Information"; }
			if($_POST['shipping_city'] == ""){ $error=1;$report="Enter your City Under Shipping Information"; }
			if($_POST['shipping_zip'] == ""){ $error=1;$report="Enter your Zip Code Under Shipping Information"; }
			if($_POST['shipping_phone'] == ""){ $error=1;$report="Enter your Phone Number Under Shipping Information"; }
			
			// PAYMENT VALIDATION
			if($_POST['payment_method_id'] == "1"){
				if($_POST['cctype'] == "")	{ 	$error=1;$report="Select a credit card type"; }
				if($_POST['ccname'] == "")	{ 	$error=1;$report="Enter your name as it appears on the card"; }
				if($_POST['expm'] == "")	{ 	$error=1;$report="Select the expiration month"; }
				if($_POST['expy'] == "")	{ 	$error=1;$report="Select the expiration year"; }
			}
			
			// STORE PAYMENT METHOD INFO IN A SESSION
			$_SESSION['payment_method_id'] = $_POST['payment_method_id'];
			
			// HOLD CC DATA TEMPORARILY
			$_SESSION['cctype'] 	= $_POST['cctype'];
			$_SESSION['ccname'] 	= $_POST['ccname'];
			$_SESSION['ccnumber'] 	= $_POST['ccnumber'];
			$_SESSION['expm'] 		= $_POST['expm'];
			$_SESSION['expy'] 		= $_POST['expy'];
			
			// REMEMBER IS SAME SHIPPING AS BILLING CHECKED
			if($_POST['SAMESHIPPINGBILLING'] == '1'){
				$_SESSION['shipping_same'] = '1';
			} else {
				$_SESSION['shipping_same'] = '0';
			}
			
			// STORE DEFAULT SHIPPING METHOD INFO IN A SESSION
			$_SESSION['shipping_method_id'] = $_POST['shipping_method_id'];
			
			// STORE referAL ID IN A SESSION
			$_SESSION['referrer_id'] = $_POST['referrer_id'];
			
			// STORE ORDER NOTE
			$_SESSION['order_note'] = $_POST['order_note'];
			
			// STORE GIFT NOTE
			$_SESSION['gift_message'] = $_POST['gift_message'];
			
			// ESCAPE SMART ARRAY
			$_POST = escape_smart_array($_POST);
			$billing_id = $_POST['billing_contact_id'];
			$shipping_id = $_POST['shipping_contact_id'];
			$account_id = $_SESSION['UserAccount']['userid'];
			
			if($error == 0){
				$account_id = $this->createAccount($account_id); // CREATES AND LOGS IN A GUEST USER IF NECESSARY // WONT CREATE AN ACCOUNT IF THE USER IS LOGGED IN
				$billing_id = $this->updateBilling($billing_id,$account_id); // CREATES OR UPDATES ACCOUNTS BILLING 
				$shipping_id = $this->updateShipping($shipping_id,$account_id); // CREATES OR UPDATES ACCOUNTS SHIPPING
				// CHECK IF ACCOUNT WAS CREATED OR IF THE CREATE ACCOUNT ERRORED DUE TO A FAULTY OR IN USE EMAIL EMAIL
				if(!is_numeric($account_id)){ $error=1;$report=$account_id; }			
			}
			
			
			// IF NO VALIDATION ERRORS
			if($error == 0){				
			
				// STORE BILLING AND SHIPPING ID IN A SESSION
				$_SESSION['billing_id'] = $billing_id;
				$_SESSION['shipping_id'] = $shipping_id;
					
				// REDIRECT TO REVIEW / CONFIRMATION PAGE
				header("Location: ".$_SETTINGS['website']."".$_SETTINGS['confirmation_page_clean_url']."");
				exit();				
			} else {			
				// NO VALIDATION / NO REDIRECT
				$success = 0;
				$array = Array($success,$report);
				return $array;			
			}
		}
	}
	
	/** FORM
	 *
	 *
	 * Checkout Information / Billing Information Form 
	 *
	 */
	function CheckoutInformationForm($heading=0,$implicit=0)
	{
		global $_SETTINGS;
		global $_REQUEST;
		global $_POST;
		global $_COOKIE;
		global $_SESSION;
		
		$display = 0;
		
		$flag = $_SETTINGS['checkout_page_clean_url'];
		if($flag == $_REQUEST['page']){		
			$display = 1;
		}
		
		// CHECK IF IMPLICIT
		if($implicit == 1){
			$display = 1;
		}
		
		if($display == 1){
			echo "<form action='' method='post' class='moduleform'>";
			
			// IF LOGGED IN 
			if($_SESSION['UserAccount']['userid'] != ""){
				// CHECK FOR A BILLING CONTACT
				// IF THERE IS A BILLING IN SESSION THEN GET IT, ELSE LOOK SPECIFICALLY FOR THE FIRST BILLING ID ON THE ACCOUNT
				//if($_SESSION['billing_id'] != ""){
				//	$select = "SELECT * FROM user_contact WHERE contact_id='".$_SESSION['billing_id']."' LIMIT 1";
				//} else {
				$select = "SELECT * FROM user_contact WHERE account_id='".$_SESSION['UserAccount']['userid']."' AND active='1'";
				//}
				$result = doQuery($select);
				$num = mysql_num_rows($result);
				$i = 0;
				while($i<$num){
					$row = mysql_fetch_array($result);
					
					// CHECK THE TYPE
					$select1 = "SELECT * FROM user_contact_relational WHERE type_id='2' AND contact_id='".$row['contact_id']."' LIMIT 1";					
					$result1 = doQuery($select1);
					if(mysql_num_rows($result1)){
						//$row1 = mysql_fetch_array($result1);
						$_POST['billing_contact_id'] 	= $row['contact_id'];
						$_POST['billing_fname']			= $row['first_name'];
						$_POST['billing_lname']			= $row['last_name'];
						$_POST['billing_mi']			= $row['middle_initial'];
						$_POST['billing_company']		= $row['company'];
						$_POST['billing_address1']		= $row['address1'];
						$_POST['billing_address2']		= $row['address2'];
						$_POST['billing_city']			= $row['city'];
						$_POST['billing_state']			= $row['state'];
						$_POST['billing_zip']			= $row['zip'];
						$_POST['billing_region']		= $row['region'];
						$_POST['billing_country']		= $row['country'];	
						$_POST['billing_email']			= $row['email'];
						$_POST['billing_phone']			= $row['phone'];
					}
					$i++;
				}
				
				// CHECK FOR A SHIPPING CONTACT
				// IF THERE IS A SHIPPING IN SESSION THEN GET IT, ELSE LOOK SPECIFICALLY FOR THE FIRST SHIPPING ID ON THE ACCOUNT
				//if($_SESSION['shipping_id'] != ""){
				//	$select = "SELECT * FROM user_contact WHERE contact_id='".$_SESSION['shipping_id']."' LIMIT 1";
				//} else {
					$select = "SELECT * FROM user_contact WHERE account_id='".$_SESSION['UserAccount']['userid']."' AND active='1'";
				//}
				$result = doQuery($select);
				$num = mysql_num_rows($result);
				$i = 0;
				while($i<$num){
					$row = mysql_fetch_array($result);
					// CHECK THE TYPE
					$select1 = "SELECT * FROM user_contact_relational WHERE type_id='3' AND contact_id='".$row['contact_id']."' LIMIT 1";
					$result1 = doQuery($select1);
					if(mysql_num_rows($result1)){
						$row1 = mysql_fetch_array($result1);
						$_POST['shipping_contact_id'] 	= $row['contact_id'];
						$_POST['shipping_fname']		= $row['first_name'];
						$_POST['shipping_lname']		= $row['last_name'];
						$_POST['shipping_mi']			= $row['middle_initial'];
						$_POST['shipping_company']		= $row['company'];
						$_POST['shipping_address1']		= $row['address1'];
						$_POST['shipping_address2']		= $row['address2'];
						$_POST['shipping_city']			= $row['city'];
						$_POST['shipping_state']		= $row['state'];
						$_POST['shipping_zip']			= $row['zip'];
						$_POST['shipping_region']		= $row['region'];
						$_POST['shipping_country']		= $row['country'];		
						$_POST['shipping_phone']		= $row['phone'];
					}
					$i++;
				}				
			} 
			
			//
			// BILLING
			//
			echo "<div class='checkout-billing'>";
			echo "<h2>Billing Information</h2>";
			// NAME
			echo "<p><label>*Name</label>";
			echo "<small class='small-fname'>First</small> <input type='text' class='input1' name='billing_fname' id='billing_fname' value='".$_POST['billing_fname']."'> ";
			echo "<small class='small-mi'>Mi</small> <input type='text' class='mi' name='billing_mi' id='billing_mi' value='".$_POST['billing_mi']."'> ";
			echo "<small class='small-lname'>Last</small> <input type='text' class='input1' name='billing_lname' id='billing_lname' value='".$_POST['billing_lname']."'>";
			echo "</p>";
			//echo "<p><label>&nbsp;</label><span>First Name</span> <span>Middle Initial</span> <span>Last Name</span></p>";
			// COMPANY NAME
			echo "<p><label>Company Name</label><input class='input2' type='text' name='billing_company' id='billing_company' value='".$_POST['billing_company']."'></p>";
			// BILLING EMAIL
			echo "<p><label>*Email</label><input class='input2' type='text' name='billing_email' id='billing_email' value='".$_POST['billing_email']."'></p>";
			// ADDRESS
			echo "<p><label>*Address 1</label><input class='input2' type='text' name='billing_address1' id='billing_address1' value='".$_POST['billing_address1']."'></p>";
			echo "<p><label>Address 2</label><input class='input2' type='text' name='billing_address2' id='billing_address2' value='".$_POST['billing_address2']."'></p>";
			// CITY, STATE, ZIP, REGION
			echo "<p><label>*City</label><input class='input2' type='text' name='billing_city' id='billing_city' value='".$_POST['billing_city']."'></p>";
			echo "<p><label>*State</label>";
			selectTable("state","billing_state","state_id","state","state","ASC");
			echo "</p>";
			echo "<p><label>*Zip</label><input class='input0' type='text' name='billing_zip' id='billing_zip' value='".$_POST['billing_zip']."'></p>";
			echo "<p><label>Region</label><input class='input2' type='text' name='billing_region' id='billing_region' value='".$_POST['billing_region']."'></p>";
			// COUNTRY
			echo "<p><label>*Country</label>";
			// SET DEFAULT COUNTRY
			if($_POST['billing_country']==""){ $_POST['billing_country'] = "226"; }
			selectTable("country","billing_country","country_id","country","country","ASC","","226");
			echo "</p>";
			// PHONE
			echo "<p><label>*Phone</label><input class='input1' type='text' name='billing_phone' id='billing_phone' value='".$_POST['billing_phone']."'></p>";
			echo "<input type='hidden' name='billing_contact_id' id='billing_contact_id' value='".$_POST['billing_contact_id']."'>";
			echo "</div>";
			
			//
			// SHIPPING
			//
			echo "<div class='checkout-shipping'>";
			echo "<h2>Shipping Information</h2>";
			echo "<p class='same-shipping-p'><input type='checkbox' name='SAMESHIPPINGBILLING' id='SAMESHIPPINGBILLING' ".isChecked($_SESSION['shipping_same'],'1')." value='1' > <span class='same-shipping'>Check if your Shipping Information is the same as your Billing Information</span></p>";
			// NAME
			echo "<p><label>*Name</label>";
			echo "<small class='small-fname'>First</small> <input type='text' class='input1' name='shipping_fname' id='shipping_fname' value='".$_POST['shipping_fname']."'> ";
			echo "<small class='small-mi'>Mi</small> <input type='text' class='mi' name='shipping_mi' id='shipping_mi' value='".$_POST['shipping_mi']."'> ";
			echo "<small class='small-lname'>Last</small> <input type='text' class='input1' name='shipping_lname' id='shipping_lname' value='".$_POST['shipping_lname']."'>";
			echo "</p>";
			//echo "<p><label>&nbsp;</label><span>First Name</span> <span>Middle Initial</span> <span>Last Name</span></p>";
			// COMPANY NAME
			echo "<p><label>Company Name</label><input class='input2' type='text' name='shipping_company' id='shipping_company' value='".$_POST['shipping_company']."'></p>";
			// ADDRESS
			echo "<p><label>*Address 1</label><input class='input2' type='text' name='shipping_address1' id='shipping_address1' value='".$_POST['shipping_address1']."'></p>";
			echo "<p><label>Address 2</label><input class='input2' type='text' name='shipping_address2' id='shipping_address2' value='".$_POST['shipping_address2']."'></p>";
			// CITY, STATE, ZIP, REGION
			echo "<p><label>*City</label><input class='input2' type='text' name='shipping_city' id='shipping_city' value='".$_POST['shipping_city']."'></p>";
			echo "<p><label>*State</label>";
			selectTable("state","shipping_state","state_id","state","state","ASC");
			echo "</p>";
			echo "<p><label>*Zip</label><input class='input0' type='text' name='shipping_zip' id='shipping_zip' value='".$_POST['shipping_zip']."'></p>";
			echo "<p><label>Region</label><input class='input2' type='text' name='shipping_region' id='shipping_region' value='".$_POST['shipping_region']."'></p>";
			// COUNTRY
			echo "<p><label>*Country</label>";
			// SET DEFAULT COUNTRY
			if($_POST['shipping_country']==""){ $_POST['shipping_country'] = "226"; }
			selectTable("country","shipping_country","country_id","country","country","ASC","","226");
			echo "</p>";
			// PHONE
			echo "<p><label>*Phone</label><input class='input1' type='text' name='shipping_phone' id='shipping_phone' value='".$_POST['shipping_phone']."'></p>";
			echo "<input type='hidden' name='shipping_contact_id' id='shipping_contact_id' value='".$_POST['shipping_contact_id']."'>";
			// SHIPPING METHOD
			echo "<p><label>*Shipping Method</label>";
			echo "<select name='shipping_method_id' id='shipping_method_id'>";
			$sel = "SELECT * FROM `ecommerce_shipping_methods` WHERE `active`='1'";
			$res = doQuery($sel);
			$nu = mysql_num_rows($res);
			$i = 0;
			while($i<$nu){
				$ro = mysql_fetch_array($res);
				echo "<option ".isSelected($_SESSION['shipping_method_id'],$ro['shipping_method_id'])." value='".$ro['shipping_method_id']."'>".$this->currency.money_format('%i',$ro['flat_price'])." - ".$ro['name']."</option>";
				$i++;
			}
			echo "</select>";
			echo "</p>";
			echo "</div>";
			
			//
			// SCRIPT FOR IF SHIPPING THE SAME
			//
			
			echo "
				<script type='text/javascript'>
					$('input#SAMESHIPPINGBILLING').click(function(){				
						if($('input#SAMESHIPPINGBILLING').is(':checked')){
							fillShippingFields();
						} else {
							emptyShippingFields();
						}			
					});
					
					function fillShippingFields(){
					
						var billing_fname	= $('#billing_fname').val();
						var billing_lname	= $('#billing_lname').val();
						var billing_mi		= $('#billing_mi').val();
						var billing_company	= $('#billing_company').val();
						var billing_address1= $('#billing_address1').val();
						var billing_address2= $('#billing_address2').val();
						var billing_city	= $('#billing_city').val();
						var billing_state	= $('#billing_state').val();
						var billing_zip		= $('#billing_zip').val();
						var billing_country	= $('#billing_country').val();
						var billing_region	= $('#billing_region').val();
						var billing_phone	= $('#billing_phone').val();
						
						$('#shipping_fname').val(billing_fname);
						$('#shipping_lname').val(billing_lname);
						$('#shipping_mi').val(billing_mi);
						$('#shipping_company').val(billing_company);
						$('#shipping_address1').val(billing_address1);
						$('#shipping_address2').val(billing_address2);
						$('#shipping_city').val(billing_city);
						$('#shipping_state').val(billing_state);
						$('#shipping_zip').val(billing_zip);
						$('#shipping_country').val(billing_country);
						$('#shipping_region').val(billing_region);
						$('#shipping_phone').val(billing_phone);
						
					}
					
					function emptyShippingFields(){
					
						$('#shipping_fname').val('');
						$('#shipping_lname').val('');
						$('#shipping_mi').val('');
						$('#shipping_company').val('');
						$('#shipping_address1').val('');
						$('#shipping_address2').val('');
						$('#shipping_city').val('');
						$('#shipping_state').val('');
						$('#shipping_zip').val('');
						$('#shipping_country').val('');
						$('#shipping_region').val('');
						$('#shipping_phone').val('');
					
					}
					
				</script>
			";
			
			echo "<div class='checkout-payment'>";
			echo "	<h2>Payment Information</h2>";
			
			$charge_method = lookupDbValue('user_permission','charge_method',$_SESSION['UserAccount']['userpermission'],'permission_id');
			if($charge_method != "Invoice Only"){
			
				echo "	<p><label>*Payment Type</label>";
				echo "	<select name='payment_method_id'>";
				
				$sel = "SELECT * FROM ecommerce_payment_methods WHERE active='1' AND web='1'";
				$res = doQuery($sel);
				while($ro = mysql_fetch_array($res)){
					echo "<option value='".$ro['payment_method_id']."' ".isSelected($_SESSION['payment_method_id'],$ro['payment_method_id'])." >".$ro['name']."</option>";
				}
				//echo "		<option value='Credit Card' ".$ccChecked." >Credit Card</option>";
				//echo "		<option value='paypal'>Paypal</option>";
				
				echo "	</select>";
				echo "	</p>";
			
				//
				// CC DETAILS
				//			
				
				echo "	<p><label>*Card Type</label>";
				echo "		<select name='cctype'>";
				
				$sel = "SELECT * FROM ecommerce_cc_types WHERE active='1'";
				$res = doQuery($sel);
				$i = 0;
				$num = mysql_num_rows($res);
				while($i<$num){
					$row = mysql_fetch_array($res);
					echo "			<option value='".$row['cc_type_id']."'>".$row['name']."</option>";
					$i++;
				}
				//echo "			<option value='01'>Visa</option>";
				echo "		</select>";
				echo "	</p>";
				
				//if($_SESSION['ccnumber'] != ""){ $_POST['ccnumber'] == $_SESSION['ccnumber']; }
				//if($_SESSION['ccname']){}
				
				echo "	<p><label>*Card Number</label><input type='text' class='input2' name='ccnumber' id='ccnumber' value='".$_POST['ccnumber']."'</p>";
				echo "	<p><label>*Name on Card</label><input type='text' class='input2' name='ccname' id='ccname' value='".$_POST['ccname']."'</p>";
				
				echo "	<p><label class='exp-date'>*Expiration Date</label><select name='expm'>";
				echo "		<option value='01'>01</option>";
				echo "		<option value='02'>02</option>";
				echo "		<option value='03'>03</option>";
				echo "		<option value='04'>04</option>";
				echo "		<option value='05'>05</option>";
				echo "		<option value='06'>06</option>";
				echo "		<option value='07'>07</option>";
				echo "		<option value='08'>08</option>";
				echo "		<option value='09'>09</option>";
				echo "		<option value='10'>10</option>";
				echo "		<option value='11'>11</option>";
				echo "		<option value='12'>12</option>";
				echo "	</select> ";
				
				echo "	<select name='expy'>";
				echo "		<option value='".date("Y")."'>".date("Y")."</option>";
				echo "		<option value='".(date("Y") +1)."'>".(date("Y") +1)."</option>";
				echo "		<option value='".(date("Y") +2)."'>".(date("Y") +2)."</option>";
				echo "		<option value='".(date("Y") +3)."'>".(date("Y") +3)."</option>";
				echo "		<option value='".(date("Y") +4)."'>".(date("Y") +4)."</option>";
				echo "		<option value='".(date("Y") +5)."'>".(date("Y") +5)."</option>";
				echo "	</select></p>";
			} else {
				// IF NO CHARGE AT THE TIME OF CHECKOUT
				echo "<p>Because you have a ".lookupDbValue('user_permission','name',$_SESSION['UserAccount']['userpermission'],'permission_id')." account you will be invoiced when you complete this order on our website. However you will not be charged at this time.</p>";
			}

			
			// REFERRALS
			// CHECK IF ACCOUNT PERMISSIONS ARE SET UP FOR REFERRALS AND BUILD SQL STRING
			$sel = "SELECT * FROM user_permission WHERE referrable='1' AND active='1'";
			$res = doQuery($sel);
			$num = mysql_num_rows($res);
			$i = 0;
			$userPermissionSQL = "";
			while($i<$num){
				$ro = mysql_fetch_array($res);
				// IF THE PERMISSION / ACCOUNT TYPE IS referABLE
				//if($ro['referrable'] == '1'){
					if($userPermissionSQL != ""){ $userPermissionSQL .= " AND "; }
					$userPermissionSQL .= " user_permission='".$ro['permission_id']."' ";
				//}
				$i++;
			}
			
			// IF THERE IS A PERMISSION SQL AMMENDMENT THEN THAT MEANS THERE IS AT 
			// LEAST ONE ACCOUNT USER PERMISSION LEVEL SET TO BE referABLE
			if($userPermissionSQL != ""){
				echo "<h2>Referral</h2>";
				echo "<p><label>Referrer</label>";
			
				echo "<select name='referrer_id'>";		
				echo "	<option value=''> </option>";
				
				if($userPermissionSQL != ""){ $userPermissionSQL = "AND (".$userPermissionSQL.") "; }
				$csel = "SELECT * FROM user_account WHERE active='1' ".$userPermissionSQL." AND status='Active' AND name!='Test'";
				$cres = doQuery($csel);
				$cnum = mysql_num_rows($cres);
				$c = 0;
				while($c<$cnum){
					$selected = "";
					$crow = mysql_fetch_array($cres);
					if($_SESSION['referral_id'] == $crow['account_id']){ $selected = " SELECTED "; }
					echo "<option ".$selected." value='".$crow['account_id']."'>".ucwords($crow['name'])."</option>";
					$c++;
				}
				
				echo "</select>";
				echo "</p>";
			}
			
			
			echo "</div>"; // END PAYMENT BOX
			
			echo "<div class='gift-message-box'>";
			echo "<h2>Gift Message</h2>";
			echo "<p><label>Message to recipient</label><textarea class='gift_message 'name='gift_message'>".$_SESSION['gift_message']."</textarea></p>";
			echo "</div>";
			
			
			echo "<div class='order-note-box'>";
			echo "<h2>Special Instructions</h2>";
			echo "<p><label>Note</label><textarea class='order_note' name='order_note'>".$_SESSION['order_note']."</textarea></p>";
			echo "</div>";
			
			
			
			echo "<div class='create-account-box'>";
			if($_SESSION['UserAccount']['userid'] == ""){
				// CREATE ACCOUNT FORM				
				echo "	<h2>Create an Account (Optional)</h2>";
				echo "	<p><label>*Email</label><input type='textbox' class='input2' name='email' value='".$_POST['email']."'></p>";
				echo "	<p><label>*Password</label><input type='password' class='input2' name='password1' value='".$_POST['password1']."'></p>";
				echo "	<p><label>*Re-Type Password</label><input type='password' class='input2' name='password2' value='".$_POST['password2']."'></p>";
				//if($_POST['dob'] == ""){ $_POST['dob'] = 'mm/dd/yy'; }
				echo "	<p><label>Birthday</label><input type='text' name='dob' class='input1' value='".$_POST['dob']."'> <small class='dob-ex'>Ex. 5/10/1985 , month/day/year </small></p>";
				echo "	<p><input type='checkbox' name='newsletter' value='1' CHECKED > ".$_SETTINGS['site_name']." may send me email with special promotions and offers.";
				// CONTINUE
				echo "	<p><label> &nbsp; </label><input type='submit' name='REVIEW' value='Continue' class='button'></p>";
				echo "	<p><label> &nbsp; </label>Click Continue to Review Your Order.</p>";				
			}
			echo "</div>";
			
			// CONTINUE CHECKING OUT...
			echo "<div class='continue-checkout-box-1'>";
			echo "	<p><input class='continue-button button' type='submit' name='REVIEW' value='Continue'></p>";
			echo "	<p><span class='button-info'>Click Continue to Review Your Order.</span></p>";
			echo "</div>";
			
			
			echo "<br clear='all' />";			
			echo "</form>";
		}
	}
	
	/**
	 *
	 * CREATE ACCOUNT
	 *
	 */
	function createAccount($account_id)
	{
		global $_POST;
		global $_REQUEST;
		global $_SESSION;
		global $_SETTINGS;
	
		// IF NO ACCOUNT CREATE A NEW ACCOUNT THROUGH CHECKOUT // EVEN IF ITS A GUEST
		if($account_id==''){
			$accounterror = 0;
			// CREATE A PASSWORD IF NONE EXISTS
			if($_POST['password1'] == ""){ $password = makePass(); }
			
			// SET THE NEW ACCOUNTS USER PERMISSION / ACCOUNT TYPE FROM SETTINGS
			$permission_id = $_SETTINGS['new_account_permission'];
			$permission_level = lookupDbValue('user_permission', 'permission_level', $permission_id, 'permission_id');
			
			// CHECK IF THE PERMISSION FROM SETTINGS IS VALID
			$select = 	"SELECT * FROM user_permission WHERE permission_id='".$permission."'";
			$result =	doQuery($select);
			if(!mysql_num_rows($result)){			
				// IF NOT AN ACTUAL EXISTING USER PERMISSION THEN GET THE LOWEST LEVEL PERMISSION
				$select = 	"SELECT * FROM user_permission ".
							"WHERE ".
							"active='1' ".
							"".$_SETTINGS['demosqland']." ".
							"ORDER BY permission_level ASC LIMIT 1";
				$result = 	doQuery($select);
				$row 	= 	mysql_fetch_array($result);
				$permission_id = $row['permission_id'];
				$permission_level = $row['permission_level'];				
			}
			
			// SQL STRING FOR OPT IN TO EMAILS
			if($_POST['newsletter'] == '1'){ $sendemails = '1'; } else { $sendemails = '0'; }			
			// FORMAT DOB
			$dob = FormatBirthdayForDatabase($_POST['dob']);			
			// EMAIL FOR THE ACCOUNT
			if($_POST['email'] == ""){ $account_email = $_POST['billing_email']; } else { $account_email = $_POST['email']; }
			// CHECK THE EMAIL VALID
			if(!VerifyEmail($account_email)){
				$accounterror = 1;
				$account_id = "".$account_email." is not a Valid Email Address";
			}
			// CHECK THE EMAIL USED
			$UserAccounts = new UserAccounts();
			if($UserAccounts->CheckEmail($account_email) == false){
				$accounterror = 1;
				$account_id = "There is already an account for ".str_replace(".","_",$account_email)."_ Please log in_";
				$report = $account_id;
				$success = 0;
				header("Location: ".$_SETTINGS['website']."".$_SETTINGS['login_page_clean_url']."/0/".$report."/".$success."/0");
				exit();
			}
			
			//
			// INSERT THE ACCOUNT
			//
			if($accounterror == 0){
				$account_id = nextId("user_account");
				$insert = 	"INSERT INTO user_account SET ".
							"name='".$_POST['billing_fname']." ".$_POST['billing_lname']."',".
							"company_name='".$_POST['billing_company']."',".
							"password='".md5($password)."',".
							"email='".$account_email."',".
							"user_permission='".$permission_id."',".
							"email_verified='1',".
							"send_emails='".$sendemails."',".
							"created=NULL,".
							"dob='".$dob."'";
				doQuery($insert);
			
				//die(mysql_error());
				//exit();
				
				//
				// FAST LOGIN
				//
				$_SESSION['UserAccount']['userid'] 			= $account_id;
				$_SESSION['UserAccount']['userpermission'] 	= $permission_id;
				$_SESSION['UserAccount']['permissionlevel']	= $permission_level;
				$_SESSION['UserAccount']['username'] 		= $billing_fname." ".$billing_lname;
				$_SESSION['UserAccount']['email'] 			= $_POST['email'];
			}
		}		
		return $account_id;		
	}
	
	/**
	 *
	 * CREATE ACCOUNT
	 *
	 */
	function updateBilling($billing_id,$account_id)
	{
		global $_POST;
		//
		// INSERT/UPDATE CUSTOMER BILLING CONTACT
		//
		if($billing_id != ""){
			// UPDATE THE BILLING CONTACT
			$update = 	"UPDATE user_contact SET ".
						"first_name='".$_POST['billing_fname']."',".
						"middle_initial='".$_POST['billing_mi']."',".
						"last_name='".$_POST['billing_lname']."',".
						"company='".$_POST['billing_company']."',".
						"region='".$_POST['billing_region']."',".
						"email='".$_POST['billing_email']."',".
						"address1='".$_POST['billing_address1']."',".
						"address2='".$_POST['billing_address2']."',".
						"city='".$_POST['billing_city']."',".
						"state='".$_POST['billing_state']."',".
						"zip='".$_POST['billing_zip']."',".
						"phone='".$_POST['billing_phone']."',".
						"country='".$_POST['billing_country']."' ".
						"WHERE contact_id='".$billing_id."' AND account_id='".$account_id."'";
			doQuery($update);
		} else {
			// INSERT A NEW BILLING CONTACT
			$billing_id = nextId("user_contact");
			$insert = 	"INSERT INTO user_contact SET ".
						"account_id='".$account_id."',".
						"first_name='".$_POST['billing_fname']."',".
						"middle_initial='".$_POST['billing_mi']."',".
						"last_name='".$_POST['billing_lname']."',".
						"company='".$_POST['billing_company']."',".
						"region='".$_POST['billing_region']."',".
						"email='".$_POST['billing_email']."',".
						"address1='".$_POST['billing_address1']."',".
						"address2='".$_POST['billing_address2']."',".
						"city='".$_POST['billing_city']."',".
						"state='".$_POST['billing_state']."',".
						"zip='".$_POST['billing_zip']."',".
						"phone='".$_POST['billing_phone']."',".
						"country='".$_POST['billing_country']."'";
			doQuery($insert);			
			// TYPE RELATIONS FOR NEW BILLING CONTACT
			doQuery("INSERT INTO user_contact_relational SET type_id='2', contact_id='".$billing_id."'");
		}
		return $billing_id;
	}
	
	/**
	 * UPDATE SHIPPING
	 *
	 *
	 */
	function updateShipping($shipping_id,$account_id)
	{
		global $_POST;
		//
		// INSERT / UPDATE NEW SHIPPING CONTACT
		//
		if($shipping_id != ""){
			// UPDATE THE SHIPPING CONTACT
			$update = 	"UPDATE user_contact SET ".
						"first_name='".$_POST['shipping_fname']."',".
						"middle_initial='".$_POST['shipping_mi']."',".
						"last_name='".$_POST['shipping_lname']."',".
						"company='".$_POST['shipping_company']."',".
						"region='".$_POST['shipping_region']."',".
						"email='".$_POST['shipping_email']."',".
						"address1='".$_POST['shipping_address1']."',".
						"address2='".$_POST['shipping_address2']."',".
						"city='".$_POST['shipping_city']."',".
						"state='".$_POST['shipping_state']."',".
						"zip='".$_POST['shipping_zip']."',".
						"phone='".$_POST['shipping_phone']."',".
						"country='".$_POST['shipping_country']."' ".
						"WHERE contact_id='".$shipping_id."' AND account_id='".$account_id."'";
			doQuery($update);
		} else {
			// INSERT A NEW SHIPPING CONTACT
			$shipping_id = nextId("user_contact");
			$insert = 	"INSERT INTO user_contact SET ".
						"account_id='".$account_id."',".
						"first_name='".$_POST['shipping_fname']."',".
						"middle_initial='".$_POST['shipping_mi']."',".
						"last_name='".$_POST['shipping_lname']."',".
						"company='".$_POST['shipping_company']."',".
						"region='".$_POST['shipping_region']."',".
						"email='".$_POST['email']."',".
						"address1='".$_POST['shipping_address1']."',".
						"address2='".$_POST['shipping_address2']."',".
						"city='".$_POST['shipping_city']."',".
						"state='".$_POST['shipping_state']."',".
						"zip='".$_POST['shipping_zip']."',".
						"phone='".$_POST['shipping_phone']."',".
						"country='".$_POST['shipping_country']."'";
			doQuery($insert);		
			// TYPE RELATIONS FOR NEW SHIPPING CONTACT
			doQuery("INSERT INTO user_contact_relational SET type_id='3', contact_id='".$shipping_id."'");
		}
		return $shipping_id;
	}
	
	/**
	 *
	 * FORM DISPLAY
	 * Checkout Information / Billing Information Form 
	 *
	 */
	function CheckoutConfirmationForm($heading=0,$implicit=0)
	{
	
		global $_SETTINGS;
		global $_REQUEST;
		global $_POST;
		global $_COOKIE;
		global $_SESSION;
		
		$display = 0;
		
		$flag = $_SETTINGS['confirmation_page_clean_url'];
		if($flag == $_REQUEST['page']){		
			$display = 1;
		}
		
		// CHECK IF IMPLICIT
		if($implicit == 1){
			$display = 1;
		}
		
		if($display == 1){
		
			echo "<form action='' class='moduleform' method='POST'>"; // BEGIN FORM
			
			//
			// REVIEW BILLING
			//
			$select1 = "SELECT * FROM user_contact WHERE contact_id='".$_SESSION['billing_id']."' LIMIT 1";
			$result1 = doQuery($select1);
			if(mysql_num_rows($result1)){
				$row1 = mysql_fetch_array($result1);
				$_POST['billing_contact_id'] 	= $row1['contact_id'];
				$_POST['billing_fname']			= $row1['first_name'];
				$_POST['billing_lname']			= $row1['last_name'];
				$_POST['billing_mi']			= $row1['middle_initial'];
				$_POST['billing_company']		= $row1['company'];
				$_POST['billing_address1']		= $row1['address1'];
				$_POST['billing_address2']		= $row1['address2'];
				$_POST['billing_city']			= $row1['city'];
				$_POST['billing_state']			= $row1['state'];
				$_POST['billing_zip']			= $row1['zip'];
				$_POST['billing_region']		= $row1['region'];
				$_POST['billing_country']		= $row1['country'];		
				$_POST['billing_phone']			= $row1['phone'];		
				$_POST['billing_email']			= $row1['email'];					
			}
			
			echo "
					<div class='review-billing'>
						<h2>Billing Information</h2> <span class='change-info'><a href='".$_SETTINGS['website']."".$_SETTINGS['checkout_page_clean_url']."'>Edit</a></span>					
						<p><label>Name</label> 			".$_POST['billing_fname']." ".$_POST['billing_mi']." ".$_POST['billing_lname']."</p>
						<p><label>Company Name</label>	".$_POST['billing_company']."</p>
						<p><label>Email</label>			".$_POST['billing_email']."</p>
						<p><label>Address 1</label>		".$_POST['billing_address1']."</p>
						<p><label>Address 2</label>		".$_POST['billing_address2']."</p>
						<p><label>City</label>			".$_POST['billing_city']."</p>
						<p><label>State</label>			".lookupDbValue('state', 'state', $_POST['billing_state'], 'state_id')."</p>
						<p><label>Zip</label>			".$_POST['billing_zip']."</p>
						<p><label>Region</label>		".$_POST['billing_region']."</p>
						<p><label>Country</label>		".lookupDbValue('country', 'country', $_POST['billing_country'], 'country_id')."</p>
						<p><label>Zip</label>			".$_POST['billing_zip']."</p>
						<p><label>Phone</label>			".$_POST['billing_phone']."</p>
						<h2>Gift Message</h2>
						<p><label>Message to recipient</label><small>".$_SESSION['gift_message']."</small></p>
					</div>
			"; // END BILLING DIV
			
			//
			// REVIEW SHIPPING
			//
			$select1 = "SELECT * FROM user_contact WHERE contact_id='".$_SESSION['shipping_id']."' LIMIT 1";
			$result1 = doQuery($select1);
			if(mysql_num_rows($result1)){
				$row1 = mysql_fetch_array($result1);
				$_POST['shipping_contact_id'] 		= $row1['contact_id'];
				$_POST['shipping_fname']			= $row1['first_name'];
				$_POST['shipping_lname']			= $row1['last_name'];
				$_POST['shipping_mi']				= $row1['middle_initial'];
				$_POST['shipping_company']			= $row1['company'];
				$_POST['shipping_address1']			= $row1['address1'];
				$_POST['shipping_address2']			= $row1['address2'];
				$_POST['shipping_city']				= $row1['city'];
				$_POST['shipping_state']			= $row1['state'];
				$_POST['shipping_zip']				= $row1['zip'];
				$_POST['shipping_region']			= $row1['region'];
				$_POST['shipping_country']			= $row1['country'];		
				$_POST['shipping_phone']			= $row1['phone'];		
				$_POST['shipping_email']			= $row1['email'];					
			}
			
			echo "
					<div class='review-shipping'>
						<h2>Shipping Information</h2> <span class='change-info'><a href='".$_SETTINGS['website']."".$_SETTINGS['checkout_page_clean_url']."'>Edit</a></span>
						<p><label>Name</label> 			".$_POST['shipping_fname']." ".$_POST['shipping_mi']." ".$_POST['shipping_lname']."</p>
						<p><label>Company Name</label>	".$_POST['shipping_company']."</p>
						<p><label>Address 1</label>		".$_POST['shipping_address1']."</p>
						<p><label>Address 2</label>		".$_POST['shipping_address2']."</p>
						<p><label>City</label>			".$_POST['shipping_city']."</p>
						<p><label>State</label>			".lookupDbValue('state', 'state', $_POST['shipping_state'], 'state_id')."</p>
						<p><label>Zip</label>			".$_POST['shipping_zip']."</p>
						<p><label>Region</label>		".$_POST['shipping_region']."</p>
						<p><label>Country</label>		".lookupDbValue('country', 'country', $_POST['shipping_country'], 'country_id')."</p>
						<p><label>Zip</label>			".$_POST['shipping_zip']."</p>
						<p><label>Phone</label>			".$_POST['shipping_phone']."</p>
						<p><label>Shipping Method</label> ".lookupDbValue('ecommerce_shipping_methods', 'name', $_SESSION['shipping_method_id'], 'shipping_method_id')."</p>";
						echo "<h2>Special Instructions</h2>";
						echo "<p><label>Note</label> <small>".$_SESSION['order_note']."</small></p>";
			echo "		</div>"; // END SHIPPING DIV
					
			//
			// REVIEW PAYMENT
			//		
			echo "
					<div class='review-payment'>
						<h2>Payment Information</h2> <span class='change-info'><a href='".$_SETTINGS['website']."".$_SETTINGS['checkout_page_clean_url']."'>Edit</a></span>
						";
					
			$charge_method = lookupDbValue('user_permission','charge_method',$_SESSION['UserAccount']['userpermission'],'permission_id');
			if($charge_method != 'Invoice Only'){		
				echo "
							<p><label>Payment Method</label>	".lookupDbValue('ecommerce_payment_methods','name',$_SESSION['payment_method_id'],'payment_method_id')."</p>
							<p><label>Card Type</label>			".lookupDbValue('ecommerce_cc_types','name',$_SESSION['cctype'],'cc_type_id')."</p>
							<p><label>Card Number</label>		".$_SESSION['ccnumber']."</p>
							<p><label>Name On Card</label>		".$_SESSION['ccname']."</p>
							<p><label class='review-exp-date'>Expiration Date</label>	".$_SESSION['expm']." / ".$_SESSION['expy']."</p>";
			} else {
				// IF NO CHARGE AT THE TIME OF CHECKOUT
				echo "<p>Because you have a ".lookupDbValue('user_permission','name',$_SESSION['UserAccount']['userpermission'],'permission_id')." account you will be invoiced when you complete this order on our website. However you will not be charged at this time.</p>";
			}
			
			echo "<h2>Referral</h2>";
			$referrer = lookupDbValue('user_account','name',$_SESSION['referrer_id'],'account_id');
			if($referrer == ""){ $referrer = "N/A"; }
			echo "<p><label>Referrer</label> ".ucwords($referrer)."</p>";
			
			
			
			//$totalsArray = $this->calculateTotals($shipping = 0);
			$totalsArray = $this->calculateTotals();
			echo "<h2>Total</h2>";							
			// SUBOTAL ROW
			echo "<table class='review-totals'>";
			echo		"<tr class='' style='text-align:right;'>";		
			echo			"<td class=''>Subtotal</td>";
			echo			"<td class=' subtotal'>".$this->currency.money_format('%i',$totalsArray[0])."</td>";
			echo		"</tr>";
			
			if($totalsArray[4] != "" || $totalsArray[4] != 0 || $totalsArray[4] != 0.00){
				// Discount ROW
				echo "<table class='review-totals'>";
				echo		"<tr class='' style='text-align:right;'>";
				echo			"<td class=''>Discount</td>";
				echo			"<td class=' discount'>- ".$this->currency.money_format('%i',$totalsArray[4])."</td>";
				echo		"</tr>";
			}
			
			// S&H ROW
			echo		"<tr class='' style='text-align:right;'>";						
			echo			"<td class=''>S&H</td>";
			echo			"<td class=''>".$this->currency.money_format('%i',$totalsArray[3])."</td>";
			echo		"</tr>";
			
			// TAX ROW
			echo		"<tr class='' style='text-align:right;'>";
			echo			"<td class=''>Tax</td>";
			echo			"<td class=''>".$this->currency.money_format('%i',$totalsArray[1])."</td>";
			echo		"</tr>";
			
			// TOTAL ROW
			echo		"<tr class='' style='text-align:right;'>";
			echo			"<td class=''>Total</td>";
			echo			"<td class=''>".$this->currency.money_format('%i',$totalsArray[2])."</td>";
			echo		"</tr>";
			echo "</table>";
			
		
			
			echo "		<p style='text-align:center;'>";
			echo "		<input class='place-order-button' type='submit' name='CONFIRMORDER' value='Place My Order'>";
			//echo "		<img class='loading' src='".$_SETTINGS['website']."admin/modules/ecommerce/images/ajax-loader.gif' >";
			echo "		</p>";
			
			echo "</div>";

			//
			// CART CONTENTS
			//
			echo "<br clear='all'>";
			echo "<h2>Cart Contents</h2>";
			$this->theShoppingCart(true);	
			
			// CONFIRM AND PROCESS ORDER
			// CONTINUE CHECKING OUT...
			echo "	<div class='continue-checkout-box-2'>";
			echo "		<p>";
			echo "		<input class='place-order-button' type='submit' name='CONFIRMORDER' value='Place My Order'>";
			//echo "		<img class='loading' src='".$_SETTINGS['website']."admin/modules/ecommerce/images/ajax-loader.gif' >";
			echo "		</p>";
			echo "		<p><span class='button-info'>Click Continue to Place Your Order.</span></p>";
			echo "	</div>";
			echo "	<br clear='all'>";
			echo "</form>";

			
			echo "
				<a id='process-trigger' href='#process-order'></a>
				<div style='display:none;'>
					<div id='process-order' class='process-order'>
						<center>
							<h2>Processing Order</h2>
							<p>Please be patient while your order is processed.</p>
							<p style='text-align:center;'><img class='loading1' src='".$_SETTINGS['website']."admin/modules/ecommerce/images/ajax-loader.gif' ></p>
						</center>
					</div>
				</div>
			";
			
			
			echo "
				<script>

				$('img.loading').hide();
				
				
				$('#process-trigger').fancybox({
					'modal'			: true,
					overlayOpacity	: 0.8,
					'titleShow'		: false,
					'onClosed'		: function() {
						$('#process-order').hide();
					}
				});
				
				
				$('input.place-order-button').click(function() {					
					$('#process-trigger').trigger('click');				
				});
				</script>
			";
			
		} // IF DISPLAY		
	}
	
	/**
	 *
	 * FORM ACTION -- PROCESS THE ORDER !! IMPORTANT
	 * Checkout Information / Billing Information Form 
	 *
	 */
	function CheckoutConfirmationFormAction()
	{
		global $_SETTINGS;
		global $_REQUEST;
		global $_POST;
		global $_COOKIE;
		global $_SESSION;
		
		if($_POST['CONFIRMORDER'] != ""){
		
		
		
			//die("BILLING:".$_SESSION['billing_id']." - ".$_SESSION['shipping_id']);
			//exit();
			
			/*
			// CHECK INVENTORY
			$select = "SELECT * FROM ecommerce_product_cart_relational WHERE shopping_cart_id='".$this->getCartId()."'";
			$result = doQuery($select);
			$num = mysql_num_rows($result);
			$i = 0;
			while($i<$num){
				$row = mysql_fetch_array($result)
				// GET PRODUCT
				$sel = "SELECT * FROM ecommerce_products WHERE product_id='".$row['product_id']."'";
				$res = doQuery($sel);
				$ro = mysql_fetch_array($res);
				if($ro['take_inventory'] == '1'){
					$currentInventory = $ro['inventory'];
					if($row['qty'] > $currentInventory){
						// UPDATE RELATIONAL CART ITEM
						doQuery("UPDATE ecommerce_product_cart_relational SET qty='".$currentInventory."' WHERE item_id='".$row['item_id']."'");
						$productName = lookupDbValue('ecommerce_products','name',$row['product_id'],'product_id');
						$report = "Your quantity for ".$productName." is more than we have in our inventory_ Your cart has been updated_";
						$success = 0;
						header("LOCATION: ".$_SETTINGS['website']."".$_SETTINGS['shopping_cart_page_clean_url']."/0/".$report."/".$success."/0");
						exit();
					}
				}
				$i++;
			}
			*/
			
			$error = 0;			
			$totalsArray = $this->calculateTotals();			
			// INSERT ORDER
			$order_id = nextId('ecommerce_orders');
			$insert = 	"INSERT INTO ecommerce_orders SET ".
						"account_id='".$_SESSION['UserAccount']['userid']."',".
						"billing_id='".$_SESSION['billing_id']."',".
						"shipping_id='".$_SESSION['shipping_id']."',".
						"referrer_id='".$_SESSION['referrer_id']."',".
						"shopping_cart_id='".$this->getCartId()."',".
						"promo_code_id='".$_SESSION['promo_code_id']."',".
						"promo_code='".$_SESSION['promo_code']."',".
						"shipping_method_id='".$_SESSION['shipping_method_id']."',".
						"payment_method_id='".$_SESSION['payment_method_id']."',".
						"status='Unprocessed',".
						"subtotal='".$totalsArray[0]."',".
						"total='".$totalsArray[2]."',".
						"sh='".$totalsArray[3]."',".
						"tax='".$totalsArray[1]."',".
						"discount='".$totalsArray[4]."',".
						"promocode_discount='".$totalsArray[6]."',".
						"account_discount='".$totalsArray[5]."',".
						"category_discount='".$totalsArray[7]."',".
						"tax_id='',".
						"tax_price='',".
						"note='".escape_smart($_SESSION['order_note'])."',".
						"gift_message='".escape_smart($_SESSION['gift_message'])."',".
						"active='1',".
						"created=NULL";						
			doQuery($insert);
			$orderCheck = mysql_error();
			
			// DO A CHECK TO SEE IF THE ORDER WAS INSERTED INTO THE DB
			// IF THERE WAS A MYSQL INSERT ERROR SEND AN EMAIL TO THE DEVELOPER
			// AND DIE AND EXIT 
			if($orderCheck != ""){
				$error = 1;
				$report = "Oops... Our website experienced a hiccup. We have not charged you for this order. Please try again.";
				// SEND DEVELOPER ERROR EMAIL
				@sendDeveloperEmail("ERROR: ".$orderCheck."<Br><br>SQL: ".$insert."<br><br>FILE: ecommerce.class.php");
			}
			
			// IF THERE WAS NO ERROR // CONTINUE
			if($error == 0){				
				
				$user_charge_method = lookupDbValue('user_permission','charge_method',$_SESSION['UserAccount']['userpermission'],'permission_id');
				
				//die($user_charge_method);
				//exit();
				
				/**
				 *
				 * NOTE: Invoices are generated dynamically so there is no need to create an invoice programatically. Got It!
				 *
				 */
				// CHARGE THE CUSTOMER IF THEIR SETTING IS NOT INVOICE ONLY
				if($user_charge_method != 'Invoice Only'){
					
					//die($_SESSION['payment_method_id']);
					//exit();
					
					// IF PAYMENT METHOD IS CREDIT CARD // ID 1
					if($_SESSION['payment_method_id'] == '1'){						
						// PROCESS CREDIT CARD HERE
						$processCardArray = $this->processCard($totalsArray[2],$order_id,$_SESSION['ccnumber'],$_SESSION['expm'],$_SESSION['expy'],$_SESSION['cccode'],"");
						$processCardStatus = $processCardArray[0];
						$processCardError = $processCardArray[1];
						$processCardResponse = $processCardArray[2];		
						
						// GET THE PROCESSOR ID
						$processor_id = $_SETTINGS['ecommerce_cc_processor'];			
						
		
						
						// SUCCESSFULL STATUS
						if($processCardStatus == "1"){					
							// INSERT CHARGED TRANSACTION
							$sel1 = "INSERT INTO ecommerce_order_transactions SET ".
									"order_id='".$order_id."',".
									"processor_id='".$processor_id."',".
									"status_code='".$processCardStatus."',".
									"error='".$processCardError."',".
									"response='".$processCardResponse."',".									
									"cc_type='".$_SESSION['cctype']."',".
									"cc_number='".$_SESSION['ccnumber']."',".
									"cc_name='".$_SESSION['ccname']."',".
									"cc_expm='".$_SESSION['expm']."',".
									"cc_expy='".$_SESSION['expy']."',".
									"amount='".$totalsArray[2]."',".
									"status='Charged',".
									"updated=NOW(),".
									"created=NOW()";
							$res1 = doQuery($sel1);
					
							// UPDATE ORDER STATUS TO OPEN
							$select = "UPDATE ecommerce_orders SET status='New' WHERE order_id='".$order_id."'";
							$result = doQuery($select);
						}
						
						// UNSUCCESSFUL STATUS
						if($processCardStatus != "1"){							
							$sel1 = "INSERT INTO ecommerce_order_transactions SET ".
									"order_id='".$order_id."',".
									"processor_id='".$processor_id."',".
									"status_code='".$processCardStatus."',".
									"error='".$processCardError."',".
									"response='".$processCardResponse."',".								
									"cc_type='".$_SESSION['cctype']."',".
									"cc_number='".$_SESSION['ccnumber']."',".
									"cc_name='".$_SESSION['ccname']."',".
									"cc_expm='".$_SESSION['expm']."',".
									"cc_expy='".$_SESSION['expy']."',".
									"amount='".$totalsArray[2]."',".
									"status='Failed',".
									"updated=NOW(),".
									"created=NOW()";
							$res1 = doQuery($sel1);
							
							$report = "There was an error processing your card. ".$processCardError."";
							$array = array(0,$report);
							
							// REMOVE ORDER
							$select = "UPDATE ecommerce_orders SET active='0',status='Unprocessed' WHERE order_id='".$order_id."'";
							$result = doQuery($select);
							
							return $array;
							
							
						}	
					}// IF PAYMENT METHOD CC
				}
				
				
				if($_SETTINGS['quickbooks_active'] == 1){
					/************************/
					/***    QUICKBOOKS    ***/
					/************************/
						
						//echo "<br>ORDER ID: ".$order_id."";
						//echo "<br>CART  ID: ".$this->getCartId()."";
						
						// CHECK / ADD CUSTOMER
						$cust_list_id = $this->QuickbooksAddCustomer($_SESSION['UserAccount']['userid']);						
						
						// CHECK WHAT THIS ACCOUNT TYPE'S SETTING IS FOR QUICKBOOKS
						$user_setting = lookupDbValue('user_permission','quickbooks_checkout_method',$_SESSION['UserAccount']['userpermission'],'permission_id');
						if($user_setting == 'Sales Receipt'){
							// ADD SALES RECEIPT
							$order_txn_id = $this->QuickbooksAddSalesReceipt($_SESSION['UserAccount']['userid'],$order_id);
						} elseif($user_setting == 'Invoice'){
							// ADD INVOICE
							$order_txn_id = $this->QuickbooksAddInvoice($_SESSION['UserAccount']['userid'],$order_id);
						}
						
						//echo "<Br>CUST LIST ID: $cust_list_id <Br>";
						//echo "<Br>SALES RECEIPT TXN ID: $order_txn_id <Br>";
						//echo "<br>INVOICE TXN ID: $order_txn_id <br>";
					
						//die();
						//exit();
					
					/************************/
					/***  END QUICKBOOKS  ***/
					/************************/
				}
				
				
				// EMAIL CUSTOMER AFTER SUCCESSFUL ORDER
				$to	= lookupDbValue('user_contact','email',$_SESSION['billing_id'],'contact_id');
				$name = lookupDbValue('user_contact','first_name',$_SESSION['billing_id'],'contact_id')." ".lookupDbValue('user_contact','last_name',$_SESSION['billing_id'],'contact_id');
				@$this->sendSuccessfulOrderEmail($to,$name);
				
				
				// EMAIL BUSINESS OWNER AFTER SUCCESSFUL ORDER
				$to	= $_SETTINGS['email_receives_orders'];
				//$name = lookupDbValue('user_contact','first_name',$_SESSION['billing_id'],'contact_id')." ".lookupDbValue('user_contact','last_name',$_SESSION['billing_id'],'contact_id');
				@$this->sendInHouseSuccessfulOrderEmail($to,$order_id);
				
			
				/************************/
				/***   LOCKOUT CART   ***/
				/************************/
				
					// LOCK CART SO IF CANNOT BE DELETED
					$update = 	"UPDATE ecommerce_shopping_carts SET locked='1',".
								"locked_subtotal='".$totalsArray[0]."',".
								"locked_total='".$totalsArray[2]."',".
								"locked_sh='".$totalsArray[3]."',".
								"locked_tax='".$totalsArray[1]."',".
								"locked_discount='".$totalsArray[4]."',".
								"locked_account_discount='".$totalsArray[5]."',".
								"locked_promo_discount='".$totalsArray[6]."' ".
								"WHERE shopping_cart_id='".$this->getCartId()."'";
					doQuery($update);						
					
					// LOCK CART ITEMS SO IF CANNOT BE DELETED
					$update = 	"UPDATE ecommerce_product_cart_relational SET ".
								"locked='1' ".
								"WHERE shopping_cart_id='".$this->getCartId()."'";
					doQuery($update);							
					
					// LOCK CART ITEM ATTRIBUTE OPTIONS SO IF CANNOT BE DELETED
					$select = "SELECT * FROM ecommerce_product_cart_relational WHERE shopping_cart_id='".$this->getCartId()."'";
					$result = doQuery($select);
					while($row = mysql_fetch_array($result)){
						$update = "UPDATE ecommerce_product_attribute_cart_relational SET locked='1' WHERE relational_item_id='".$row['item_id']."'";
						doQuery($update);
					}
				
				/************************/
				/*** END LOCKOUT CART ***/
				/************************/						
				
				// INVENTORY UPKEEP
				$select = "SELECT * FROM ecommerce_product_cart_relational WHERE shopping_cart_id='".$this->getCartId()."'";
				$result = doQuery($select);
				while($row = mysql_fetch_array($result)){
					// GET PRODUCT
					$sel = "SELECT * FROM ecommerce_products WHERE product_id='".$row['product_id']."'";
					$res = doQuery($sel);
					$ro = mysql_fetch_array($res);
					if($ro['take_inventory'] == '1'){
						$currentInventory = $ro['inventroy'];
						$newInventory = $currentInventory - $row['qty'];
						// UPDATE NEW INVENTORY
						doQuery("UPDATE ecommerce_products SET inventory='".$newInventory."' WHERE product_id='".$row['product_id']."'");
					}
				}
				
				// CLEAN UP CART
				$sessionrandomphrase = md5($_SETTINGS['session_random_phrase']);
				$_SESSION["shoppingcart-".$sessionrandomphrase.""] == '';
				
				// REDIRECT TO THANKYOU
				$success = 1;
				$report = "Thank You, Your order has been placed";
				header("Location: ".$_SETTINGS['website']."".$_SETTINGS['thank_you_page_clean_url']."/0/".$report."/".$success."/0");
				exit();
				
			}
			/*** IF ERROR = 1 ***/
			else
			{
				$array = array(0,$report);
				return $array;
			}
		}
	}
	
	/**
	 *
	 * DOES THIS FUNCTION EVEN NEED TO EXIST??
	 *
	 */
	function ThankYou($heading=0,$implicit=0)
	{
		global $_SETTINGS;
		global $_SESSION;
		$display = 0;
		
		//echo "$implicit";
		
		$flag = $_SETTINGS['thank_you_page_clean_url'];
		if($flag == $_REQUEST['page']){		
			$display = 1;
		}
		
		// CHECK IF IMPLICIT
		if($implicit == 1){
			$display = 1;
		}
		
		// HOW ARE YOU GOING TO DO THIS?
		// DOES THERE EVEN NEED TO BE A THANKYOU HERE OR CAN THE CMS HANDLE IT
		if($display == 1){
			//echo "
			//Thank You.
			//";
		
			
			//
			// RUN QUICKBOOKS AFTER THE FACT IN THE BACKGROUND
			//
			
			//echo "HELLO";
			
			/*
			echo "
				<script>
				$.ajax({
				  type: 'POST',
				  url: '".$_SETTINGS['thank_you_page_clean_url']."',
				  data: 'post_order_quickbooks=1',
				  success: function(data) {
					//$('#products').html(data);
					//alert('Products Filtered BY KEYWORD.');
				  }
				});			
				</script>		
			";
			*/
		
		} // IF DISPLAY 
	}

	/**
	 *
	 * CART SUBTOTAL
	 *
	 */
	function calculateTotals($forcecartid="")
	{
	
		global $_SETTINGS;
		global $_REQUEST;
		global $_POST;
		global $_COOKIE;
		global $_SESSION;
		
		// GET SESSION RANDOM PHRASE
		$sessionrandomphrase = md5($_SETTINGS['session_random_phrase']);
		$cartid	= $_SESSION['shoppingcart-'.$sessionrandomphrase.''];
		
		// FORCE A CART ID
		$orderDate = "";
		$orderTime = "";
		//$now = time();
		if($forcecartid != ""){
			$cartid = $forcecartid;			
			// GET THE ORDER
			$select = "SELECT * FROM ecommerce_orders WHERE shopping_cart_id='".$cartid."' LIMIT 1";
			$result = doQuery($select);
			$row = mysql_fetch_array($result);
			$orderDate = $row['created'];
			$orderTime = strtotime($orderDate);
			//echo $cartid;
			//var_dump($row);
			//die();
			//exit();
			if($row){
				// ALSO GET THE SHIPPING ID FROM THE ORDER
				$_SESSION['shipping_id'] = $row['shipping_id'];
				// ALSO GET THE SHIPPING METHOD ID FROM THE ORDER
				$_SESSION['shipping_method_id'] = $row['shipping_method_id'];
			}
		}
		
		// GET THE SHOPPING CART
		$select = "SELECT * FROM ecommerce_shopping_carts WHERE active='1' AND shopping_cart_id='".$cartid."' LIMIT 1";
		$result = doQuery($select);
		$shopping_cart = mysql_fetch_array($result);
		if($shopping_cart['locked'] == '1'){
			// THIS IS A LOCKED CART RETURN THE locked totals
			// ARRAY sub-total, tax-total, grand-total
			$priceArray = Array($shopping_cart['locked_subtotal'],$shopping_cart['locked_tax'],$shopping_cart['locked_total'],$shopping_cart['locked_sh'],$shopping_cart['locked_discount'],$shopping_cart['locked_account_discount'],$shopping_cart['locked_promo_discount']);		
			return $priceArray;	
		}
		
		// CALCULATE SUBTOTAL
		$select = "SELECT * FROM ecommerce_product_cart_relational WHERE shopping_cart_id='".$cartid."'";
		$result = doQuery($select);
		$num = mysql_num_rows($result);
		$i = 0;
		$subtotal = 0.00;
		$taxtotal = 0.00;
		$flatCatDiscount = 0.00;
		while($i<$num){
			$row = mysql_fetch_array($result);
			//$subselect 	= "SELECT * FROM ecommerce_products WHERE product_id='".$row['product_id']."'";
			//$subresult 	= doQuery($subselect);
			//$subrow 	= mysql_fetch_array($subresult);
			
			// CHECK FOR PRODUCT DISCOUNTS
			// FORMAT PRICE
			$discount = '0.00';
			if($row['flat_discount'] != '0.00' || $row['rate_discount'] != '0.00'){				
				// IF FLAT DISCOUNT
				if($row['flat_discount'] != '0.00'){
					$discount = $row['flat_discount'];					
				}
				// IF RATE DISCOUNT
				elseif($row['rate_discount'] != '0.00'){
					$discount = $row['price'] * $row['rate_discount'];
				}		
			}
			
			// CHECK FOR CATEGORY DISCOUNTS
			$selCat = "SELECT * FROM ecommerce_product_category_relational WHERE product_id='".$row['product_id']."'";
			$resCat = doQuery($selCat);
			$numCat = mysql_num_rows($resCat);
			$iCat = 0;
			while($rowCat = mysql_fetch_array($resCat))
			{
				// GET THE CATEGORY
				$selCat1 = "SELECT * FROM ecommerce_product_categories WHERE category_id='".$rowCat['category_id']."' AND active='1' LIMIT 1";
				$resCat1 = doQuery($selCat1);
				
				while($rowCat1 = mysql_fetch_array($resCat1)){
					// CHECK IF THE CATEGORY HAS A FLAT DISCOUNT
					if($rowCat1['flat_discount'] != 0)
					{ 
						$flatCatDiscount = $rowCat1['flat_discount'];
					}
					//echo $flatCatDiscount."<br>";
				}
				
				
				
				$iCat++;
			}
			
			$list_price = money_format('%i',$row['price']);
			$new_price = $row['price'] - $discount;
			$new_price = money_format('%i',$new_price);
			
			$subtotal = $subtotal + ($new_price * $row['qty']);				
			$i++;
		}
		
		// PRODUCT DISCOUNTS AFFECT THE SUBTOTAL
		//echo "<br>".$flatCatDiscount."<br>";
		
		
		/******
		 *
		 * ACCOUNT TYPE DISCOUNTS
		 *
		 */
		// ACCOUNT TYPE DISCOUNTS
		$discount = 0.00;
		// GET THE USER PERMISSION
		$select = "SELECT user_permission FROM user_account WHERE account_id='".$shopping_cart['account_id']."' LIMIT 1";
		$result = doQuery($select);
		if(mysql_num_rows($result)){
			$row = mysql_fetch_array($result);
			$userPermission = $row['user_permission'];
		} else {
			$userPermission = $_SETTINGS['new_account_permission'];
		}
		
		// CALCULATE THE ACCOUNT TYPE DISCOUNT
		$select = "SELECT * FROM user_permission WHERE permission_id='".$userPermission."' LIMIT 1";
		$result = doQuery($select);
		$row = mysql_fetch_array($result);
		if($discount_type='Rate'){
			$discount = $subtotal * $row['discount'];
		}
		
		// CHECK ACCOUNT TYPE MAX DISCOUNT
		if($discount > $row['discount_maximum_dollar_amount'] AND $row['discount_maximim_dollar_amount'] > 0){
			$discount = $row['discount_maximum_dollar_amount'];
		}
		
		/******
		 *
		 * INDIVIDUAL PER ACCOUNT DISCOUNTS
		 *
		 */
		// PER ACCOUNT DISCOUNT (INDIVIDUAL DiSCOUNT)
		// OVERRIDES ACCOUNT TYPE DISCOUNT
		$customerRate = lookupDbValue('user_account','discount_rate',$_SESSION['UserAccount']['userid'],'account_id');
		if($customerRate != "" AND $customerRate != "0" AND $customerRate != "0.00"){
			$discount = 0.00;
			$discount = $subtotal * $customerRate;
			//$discount = $customerRate;
		}
		
		$accountdiscount = $discount;
		
		/******
		 *
		 * COUPON CODE DISCOUNT
		 * GETS ADDED TO EXISTING DISCOUNTS
		 *
		 */
		// CALCULATE THE PROMO CODE DISCOUNT
		if($shopping_cart['coupon_code_id'] != ""){
			$select = "SELECT * FROM ecommerce_coupon_codes WHERE coupon_id='".$shopping_cart['coupon_code_id']."' LIMIT 1";
			$result = doQuery($select);
			if(mysql_num_rows($result)){
				$rowcode = mysql_fetch_array($result);
				
				// IF THERE IS A FREE PRODUCT WITH THIS PROMO 
				if($rowcode['free_promo_product_id'] != '0'){				
					// CHECK FOR A FREE PRODUCT FLAG MAX DISCOUNT
					if($rowcode['flag_max_discount'] == '1'){
						// GET THE ITEMS IN THE CART
						$prodselect = 	"SELECT a.product_id,c.name,c.price FROM ecommerce_product_cart_relational a ".
										"LEFT JOIN ecommerce_products c ON a.product_id=c.product_id ".
										"WHERE a.shopping_cart_id='".$cartid."'";
						$prodresult = doQuery($prodselect);
						$prodnum 	= mysql_num_rows($prodresult);
						$i = 0;
						$discountpriceArray = array();
						$promoflag = 0;						
						while($i<$prodnum){
						
							$prodrow = mysql_fetch_array($prodresult);
							
							
							// IF FLAG TEXT
							if($rowcode['flag_text_match']){
								if(strstr(strtolower($prodrow['name']),strtolower($rowcode['flag_text_match']))){
									$promoflag = 1;
									if($prodrow['price'] != '0.00'){
										array_push($discountpriceArray, $prodrow['price']);
									}
								}					
							}
							
							// IF FLAG PRODUCT
							if($coderow['free_promo_flag_product_id'] > 0){								
								if($prodrow['product_id'] == $rowcode['free_promo_flag_product_id']){
									$promoflag = 1;
									if($prodrow['price'] != '0.00'){
										array_push($discountpriceArray, $prodrow['price']);
									}
								}				
							}
							$i++;
						}
						if($promoflag == 1){
							sort($discountpriceArray,SORT_NUMERIC);							
							$promocodediscount = $discountpriceArray[0];							
							$discount = $discount + $promocodediscount;
						}
					}					
				} else {
					if($rowcode['flat_discount'] > 0){				
						$promocodediscount = $rowcode['flat_discount'];
						$discount = $discount + $promocodediscount;
					} elseif($rowcode['percent_discount'] > 0){
						$promocodediscount = ($subtotal-$discount) * $rowcode['percent_discount'];
						$discount = $discount + $promocodediscount;
					}	
				}
				
				
				
				
				if($discount > $rowcode['max_flat_discount'] AND $rowcode['max_flat_discount'] > 0){
					$discount = $discount + $rowcode['max_flat_discount'];
					//$discount = $rowcode['max_flat_discount'];
				}			

											
			}
		}
		
		// CALCULATE SHIPPING
		// CHEC IF THE SHIPPING METHOD IS A FLAT PRICE
		$shipping = 0.00;
		if($_SESSION['shipping_method_id'] == ""){ $_SESSION['shipping_method_id'] = $_SETTINGS['default_shipping_method']; }
		$shipping = lookupDbValue('ecommerce_shipping_methods','flat_price',$_SESSION['shipping_method_id'],'shipping_method_id');
		
		//if($shipping == 0){
		// IF SHIPPING 0 CALCULATE SHIPPING BASE ON THE METHOD
		// TODO...
		//}
		
		// CALCULATE SALES TAX
		// GET THE SHIPPING STATE
		$taxtotal = 0;
		$state_tax = 0;
		$shipping_state = lookupDbValue('user_contact','state',$_SESSION['shipping_id'],'contact_id');
		// CHECK IF THE STATE ID HAS A TAX
		$state_tax = lookupDbValue('state','tax',$shipping_state,'state_id');
		if($state_tax != 0 ){
			$taxtotal = $subtotal*$state_tax;
		}
		
		//$total = ($subtotal + $taxtotal + $shipping) - $discount;
		$discount = $accountdiscount + $promocodediscount + $flatCatDiscount;
		
		/*********
		 *
		 * !!IMPORTANT!!
		 * START
		 * The followin code is very important, 
		 * websites running prior to 1301512494 cacluated the order totals differentley at the time the order was placed than the way 
		 * orders are calclulated now. So leave this alone.
		 *
		 *********/
		
		// IF UNORDERED OR ORDERED AFTER THE TOTALS CHANGE WAS MADE
		if($orderTime == "" OR $orderTime>1301512494){
			$total = (($subtotal - $accountdiscount - $promocodediscount - $flatCatDiscount) + $taxtotal + $shipping);
		}
		// ELSE IF ORDER IS OLDER
		elseif($orderTime<1301512494) {
			$total = ($subtotal + $taxtotal + $shipping - $accountdiscount - $promocodediscount);			
		}
		
		/*********
		 *
		 * END
		 * !!IMPORTANT!!
		 *
		 *********/
		
		// ARRAY sub-total, tax-total, grand-total
		$priceArray = Array($subtotal,$taxtotal,$total,$shipping,$discount,$accountdiscount,$promocodediscount,$flatCatDiscount);		
		return $priceArray;	
	}
	
	/**
	 *
	 * GET CART ID
	 *
	 */
	function getCartId()
	{
		global $_SETTINGS;
		global $_SESSION;
		$sessionrandomphrase = md5($_SETTINGS['session_random_phrase']);
		$cartid = $_SESSION['shoppingcart-'.$sessionrandomphrase.''];
		return $cartid;
	}
	
	/**
	 *
	 * PROCESS CC CARD
	 * RETURN 1 SUCCESS
	 * RETRUN 0 FAIL
	 *
	 */
	function processCard($amount=0,$orderId,$ccNumber,$expM,$expY,$ccCode,$description="")
	{
		global $_SETTINGS;
		
		// GET METHOD
		$method = $_SETTINGS['ecommerce_cc_processor'];
		
		//
		// AUTHORIZE.net == PROCESSOR ID 1
		//
		if($method == "1"){
			// IF TESTING / NOT LIVE
			if($_SETTINGS['anet_live'] == 0){		
				// By default, this sample code is designed to post to our test server for
				// developer accounts: https://test.authorize.net/gateway/transact.dll
				// for real accounts (even in test mode), please make sure that you are
				// posting to: https://secure.authorize.net/gateway/transact.dll		
				$post_url = "https://test.authorize.net/gateway/transact.dll";	
				$loginid = $_SETTINGS['anet_test_login_id'];
				$transkey = $_SETTINGS['anet_test_transaction_key'];				
			} elseif($_SETTINGS['anet_live'] == 1){
				$post_url = "https://secure.authorize.net/gateway/transact.dll";	
				$loginid = $_SETTINGS['anet_production_login_id'];
				$transkey = $_SETTINGS['anet_production_transaction_key'];		
			}
		
			$expY = substr($expY,2);
		
			$post_values = array(			
				// the API Login ID and Transaction Key must be replaced with valid values
				"x_login"			=> $loginid,
				"x_tran_key"		=> $transkey,

				"x_version"			=> "3.1",
				"x_delim_data"		=> "TRUE",
				"x_delim_char"		=> "|",
				"x_relay_response"	=> "FALSE",

				"x_type"			=> "AUTH_CAPTURE",
				"x_method"			=> "CC",
				"x_card_num"		=> $ccNumber,
				"x_exp_date"		=> $expM.$expY,
				"x_card_code"		=> $ccCode,
				
				"x_amount"			=> $amount,
				"x_description"		=> $description

				//"x_first_name"		=> "".$_POST['firt_name']."",
				//"x_last_name"		=> "".$_POST['last_name']."",
				//"x_address"			=> "".$_POST['address1']."",
				//"x_state"			=> "".$_POST['state']."",
				//"x_zip"				=> "".$_POST['zip'].""
				// Additional fields can be added here as outlined in the AIM integration
				// guide at: http://developer.authorize.net
			);
		
			// This section takes the input fields and converts them to the proper format
			// for an http post.  For example: "x_login=username&x_tran_key=a1B2c3D4"
			$post_string = "";
			foreach( $post_values as $key => $value )
				{ $post_string .= "$key=" . urlencode( $value ) . "&"; }
			$post_string = rtrim( $post_string, "& " );

			// This sample code uses the CURL library for php to establish a connection,
			// submit the post, and record the response.
			// If you receive an error, you may want to ensure that you have the curl
			// library enabled in your php configuration
			$request = curl_init($post_url); // initiate curl object
				curl_setopt($request, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
				curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
				curl_setopt($request, CURLOPT_POSTFIELDS, $post_string); // use HTTP POST to send form data
				curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment this line if you get no gateway response.
				$post_response = curl_exec($request); // execute curl post and store results in $post_response
				// additional options may be required depending upon your server configuration
				// you can find documentation on curl options at http://www.php.net/curl_setopt
			curl_close ($request); // close curl object

			// This line takes the response and breaks it into an array using the specified delimiting character
			$response_array = explode($post_values["x_delim_char"],$post_response);		
			$response_string = "";
	
			// The results are output to the screen in the form of an html numbered list.
			//echo "<OL>\n";
			//foreach ($response_array as $value)
			//{
				//echo "<LI>" . $value . "&nbsp;</LI>\n";
			//	$i++;
			//}
			
			//echo "</OL>\n";
			//die();
			//exit();
			// individual elements of the array could be accessed to read certain response
			// fields.  For example, response_array[0] would return the Response Code,
			// response_array[2] would return the Response Reason Code.
			// for a list of response fields, please review the AIM Implementation Guide
	
			$response_string = "".date("Y-m-d H:i:s")."|".$price."|".$response_array[4]."";
			$response = $response_array;
			//echo "<br><br>";
			//var_export($post_values);
			
			//die();
			//exit();
			
			// RETURN TRANSACTION
			
			$array = array($response_array[0],$response_array[3],$response_array);
			return $array;

		}
		
		//
		// PAYPAL WEBSITE PAYMENTS PRO
		// TODO...
		if($method=="2"){
			$array = array(0,"","");
			return $array;
		}		
	}	
	
	/**
	 *
	 * SEND SUCCESSFUL ORDER EMAIL
	 *
	 */	
	function sendSuccessfulOrderEmail($to="",$name)
	{
		global $_SETTINGS;
		global $_SESSION;
		
		$message_html = lookupDbValue('automated_email_contents','html','1','email_id');
		$subject = lookupDbValue('automated_email_contents','subject','1','email_id');
		$from = lookupDbValue('automated_email_contents','from','1','email_id');
		
		//$email_template = $_SETTINGS['ecommerce_email_template'];
		$email_html = file_get_contents("".$_SETTINGS['website']."themes/".$_SETTINGS['theme']."".$_SETTINGS['ecommerce_email_template']."?cartid=".$this->getCartId()."");
		
		// TESTING
		//echo "<br><br>TEMPLATE: ".$_SETTING['ecommerce_email_template'];
		//die($email_html);
		//exit();
		
		$email_html = str_replace("|date|","".date("m/d/Y")."",$email_html);
		$email_html = str_replace("|message_html|","".$message_html."",$email_html);
		$email_html = str_replace("|name|",$name,$email_html);
		
		
		@sendEmail($to,$from,$subject,$email_html);
		return true;
	}
	
	/**
	 *
	 * SEND IN HOUSE EMAIL
	 *
	 */
	function sendInHouseSuccessfulOrderEmail($to,$order_id)
	{
		global $_SETTINGS;
		global $_SESSION;
		
		$select = "SELECT * FROM ecommerce_orders WHERE order_id='".$order_id."' LIMIT 1";
		$result = doQuery($select);
		$order = mysql_fetch_array($result);
		
		$message_html = "
						<Br>
						New Order from ".$_SETTINGS['site_name']."
						<Br><Br>
						Date: ".date("m/d/Y")."<Br>
						Customer: ".$order['account_id']."<Br>
						Total: ".$order['total']."
						<br><Br>
						<a href='".$_SETTINGS['website']."admin/index.php?oid=".$order['order_id']."&VIEW=ecommerce&view=View'>Click here to login, and review this customer.</a>
						<br>
						";
						
		$subject = "New Order from ".$_SETTINGS['site_name']."";
		$from = 'no-reply@'.$_SETTINGS['website_domain'].'';
		
		@sendEmail($to,$from,$subject,$message_html);
		return true;
		
		
	}
	
	/**
	 *
	 * POST ORDER QUICKBOOKS AJAx
	 * NOT USED NOT A FUNCTION !!
	 *
	 */	
	function PostOrderQuickbooksAjax()
	{
		global $_SETTINGS;
		global $_SESSION;
		global $_POST;
		//if(isset($_POST['post_order_quickbooks'])){
		//	$this->QuickbooksAddCustomer($_SESSION['UserAccount']['userid']);
		//}
	}
	
	/**
	 *
	 * RETURN ADD INVOICE XML
	 *
	 */		
	function QuickbooksAddInvoice($xid,$order_id)
	{
	
		global $_SESSION;
		global $_SETTINGS;
		//error_reporting(E_ALL | E_STRICT);
		
		/**
		 * Require the QuickBooks base classes
		 */
		require_once '/home/ksdwebksd/dev/2010/shebeads/admin/modules/ecommerce/quickbooks/qboe/v1-5-3/QuickBooks.php';

		// get customer
		$select = "SELECT * FROM user_account WHERE account_id='".$xid."' LIMIT 1";
		$result = doQuery($select);
		$customer = mysql_fetch_array($result);
		$custlistid = $customer['qb_list_id'];
		
		// get order
		$select = "SELECT * FROM ecommerce_orders WHERE order_id='".$order_id."' LIMIT 1";
		$result = doQuery($select);
		$order = mysql_fetch_array($result);
		// THIS IS THE ???
		$txnid = $order['qb_txn_id'];
		
		//GET BILLING INFO
		$select = "SELECT * FROM user_contact WHERE contact_id='".$order['billing_id']."' LIMIT 1";
		$result = doQuery($select);
		$billing = mysql_fetch_array($result);

		//GET SHIPPING INFO
		$select = "SELECT * FROM user_contact WHERE contact_id='".$order['shipping_id']."' LIMIT 1";
		$result = doQuery($select);
		$shipping = mysql_fetch_array($result);
	
		print($_SESSION['UserAccount']);
	
		// GET PERMISSION INFO
		$select = "SELECT * FROM user_permission WHERE permission_id='".$_SESSION['UserAccount']['userpermission']."' LIMIT 1";
		$result = doQuery($select);
		$permission = mysql_fetch_array($result);
	
		//MAKE DUE DAE		
		$due_date = date('Y-m-d',mktime() + (86400 * $permission['quickbooks_invoice_due_days'])); 	
		
		// Create the API instance
		$source_type = QUICKBOOKS_API_SOURCE_ONLINE_EDITION;
		$API = new QuickBooks_API(null, 'api', $source_type, null, array(), $this->quickbooks_source_options, array());
		$API->enableRealtime(true);
		// Let's get some general information about this connection to QBOE: 
		//print('Our connection ticket is: ' . $API->connectionTicket() . "\n <br>");
		//print('Our session ticket is: ' . $API->sessionTicket() . "\n <br>");
		//print('Our application id is: ' . $API->applicationID() . "\n <br>");
		//print('Our application login is: ' . $API->applicationLogin() . "\n <br>");
		//print('Last error number: ' . $API->errorNumber() . "\n <br>");
		//print('Last error message: ' . $API->errorMessage() . "\n <br>");
		
		// BUILD ITEM XML
		// GET CART ITEM
		$select = "SELECT * FROM ecommerce_product_cart_relational WHERE shopping_cart_id='".$order['shopping_cart_id']."'";
		$result = doQuery($select);
		$num = mysql_num_rows($result);
		$i = 0;
		while($i<$num){
			$row = mysql_fetch_array($result);
			// GET THE PRODUCT
			$select = "SELECT * FROM ecommerce_products WHERE product_id='".$row['product_id']."' LIMIT 1";
			$res = doQuery($select);
			$product = mysql_fetch_array($res);				
			
			
			// GET THE ATTRIBUTES AND AMMEND THEM TO THE DESCRIPTION
			$attributeclassxml = "";
			$attributes = "";			
			// THIS FUNCTION NEEDS THE PRODUCT CART RELATIONAL ITEM
			$attributeArray 	= $this->QuickbooksAttributeClass($row['item_id']);
			$attributes			= $attributeArray[0];
			$attributeclassxml	= $attributeArray[1];
			
			// CHECK IF ITEM IN QUICKBOOKS INSERT IT IF NOT
			$qbFullName = $this->QuickbooksAddItem($product);			
			$itemxml .= '<InvoiceLineAdd>
							<ItemRef>
								<FullName >'.$qbFullName.'</FullName>
							</ItemRef>
							<Desc >'.$product['name'].' - '.$attributes.'</Desc>
							<Quantity >'.$row['qty'].'</Quantity>'.$attributeclassxml.'
							<Amount >'.$product['price'].'</Amount>
						</InvoiceLineAdd>';
			$i++;
		}
		
		// DISCOUNTS
		$discountxml = '';
		
		/*
		$promocode_discount = $order['promocode_discount'];
		if($promocode_discount > 0){		
			$discountxml .=	'
							<DiscountLineAdd>
								<Amount >'.$promocode_discount.'</Amount>
							</DiscountLineAdd>
							';		
		}
		
		$account_discount = $order['account_discount'];
		if($account_discount > 0){		
			$discountxml .=	'
							<DiscountLineAdd>
								<Amount >'.$account_discount.'</Amount>
							</DiscountLineAdd>
							';		
		}
		*/
		
		$taxxml = 	'<SalesTaxLineAdd>
						<Amount >'.$order['tax'].'</Amount>
					</SalesTaxLineAdd>';
		
		$shippingxml = 	'<ShippingLineAdd>
							<Amount >'.$order['sh'].'</Amount>
						</ShippingLineAdd>';
			
		$xml = '<InvoiceAddRq> 
					<InvoiceAdd>
						<CustomerRef>
							<ListID >'.$custlistid.'</ListID>
							<FullName >'.$customer['name'].'</FullName>
						</CustomerRef>
						<ClassRef>
							<FullName >Web</FullName>
						</ClassRef>
						<TxnDate >'.date("Y-m-d").'</TxnDate>
						<BillAddress>
							<Addr1 >'.$billing['address1'].'</Addr1>
							<Addr2 >'.$billing['address2'].'</Addr2>
							<City >'.$billing['city'].'</City>
							<State >'.lookupDbValue('state','state',$billing["state"],'state_id').'</State>
							<PostalCode >'.$billing['zip'].'</PostalCode>
							<Country >'.lookupDbValue('country','country',$billing["country"],'country_id').'</Country>
						</BillAddress>
						<ShipAddress>
							<Addr1 >'.$shipping['address1'].'</Addr1>
							<Addr2 >'.$shipping['address2'].'</Addr2>
							<City >'.$shipping['city'].'</City>
							<State >'.lookupDbValue('state','state',$shipping["state"],'state_id').'</State>
							<PostalCode >'.$shipping['zip'].'</PostalCode>
							<Country >'.lookupDbValue('country','country',$shipping["country"],'country_id').'</Country>
						</ShipAddress>';
			
			if($permission['quickbooks_invoice_terms'] != ""){
				$xml .=		'<TermsRef>
								<FullName >'.$permission['quickbooks_invoice_terms'].'</FullName>
							</TermsRef>';
			}
			
			$xml .=		'<DueDate >'.$due_date.'</DueDate>
						<ShipDate >'.date("Y-m-d").'</ShipDate>
						<IsToBePrinted >0</IsToBePrinted>
						'.$itemxml.''.$discountxml.'
						'.$taxxml.'
						'.$shippingxml.'						
					</InvoiceAdd>
				</InvoiceAddRq>';
						
			echo "<br><br>ADD INVOICE XML: <br> $xml <Br><Br>";
		
		$return = $API->qbxml($xml);

		/*
		// NOT USED BECAUSE OF REAL TIME QBOE
		// This function gets called when QuickBooks Online Edition sends a response back
		function _invadd_raw_qbxml_callback($method, $action, $ID, &$err, $qbxml, $Iterator, $qbres)
		{
			global $_SESSION;
			print('<Br>We got back this qbXML from QuickBooks Online Edition: <Br><br>' . $qbxml . '<Br><br>');\			
			$xml = simplexml_load_string($qbxml);
			print("<br><br>".$xml."");			
			$listid = $xml->QBXMLMsgsRs->CustomerAddRs->CustomerRet->ListID;
			// INSERT LIST ID IF CUSTOMER 
			$insert = "UPDATE user_account SET qb_list_id='".$listid."' WHERE account_id='".$_SESSION['UserAccount']['userid']."'";
			$result = doQuery($insert);
			// SET LIST ID
			$_SESSION['listid'] = $listid;		
		}					
		*/
		
		// GET THE LIST ID
		if ($API->usingRealtime()){
			echo "<br>- RESPONSE FROM QUICKBOOKS FOR ADD INVOICE<br>";
			
			print_r($return);
			echo "<br><br>";				
			$formatxml = strstr($return['qbxml'],"<");			
			echo "<br><br>".$formatxml."<br><br>";			
			$xml = simplexml_load_string($formatxml);				
			$txnid = $xml->QBXMLMsgsRs->InvoiceAddRs->InvoiceRet->TxnID;	
			$refnum = $xml->QBXMLMsgsRs->InvoiceAddRs->InvoiceRet->RefNumber;
			echo "<br><Br>- INVOICE TXN ID FROM QUICKBOOKS: ".$txnid." <Br><Br>";			
		}				
		// UPDATE CUST LIST ID
		doQuery("UPDATE ecommerce_orders SET qb_txn_id='".$txnid."',qb_ref_number='".$refnum."',qb_response='".var_dump($return)."' WHERE order_id='".$order_id."'");
		return $refnum;			
	}
	
	/**
	 *
	 * CHECKS AND ADS ITEM TO QUICKBOOKS
	 * RETURNS A FULL PRODUCT NAME
	 *
	 */
	function QuickbooksAddItem($product)
	{
		global $_SESSION;
		global $_SETTINGS;
		//error_reporting(E_ALL | E_STRICT);
		
		/**
		 * Require the QuickBooks base classes
		 */
		require_once '/home/ksdwebksd/dev/2010/shebeads/admin/modules/ecommerce/quickbooks/qboe/v1-5-3/QuickBooks.php';
		
		// Create the API instance
		$source_type = QUICKBOOKS_API_SOURCE_ONLINE_EDITION;
		$API = new QuickBooks_API(null, 'api', $source_type, null, array(), $this->quickbooks_source_options, array());
		$API->enableRealtime(true);
		// Let's get some general information about this connection to QBOE: 
		//print('Our connection ticket is: ' . $API->connectionTicket() . "\n <br>");
		//print('Our session ticket is: ' . $API->sessionTicket() . "\n <br>");
		//print('Our application id is: ' . $API->applicationID() . "\n <br>");
		//print('Our application login is: ' . $API->applicationLogin() . "\n <br>");
		//print('Last error number: ' . $API->errorNumber() . "\n <br>");
		//print('Last error message: ' . $API->errorMessage() . "\n <br>");
		
		// CHECK QUICKBOOKS FOR LIST ID
		//if( $product['qb_name'] == ""){		
			// CHECK FULL NAME
			
			if($product['qb_name'] == ""){ $product['qb_name'] = $product['product_number']; }
			if($product['qb_name'] == ""){ $product['qb_name'] = $product['name']; }	
			
			$xml = '<ItemQueryRq> 
						<FullName >'.$product['qb_name'].'</FullName>
					</ItemQueryRq>';
			
			echo "<Br><br>ITEM QUERY XML:<br> $xml <br><br>";
			//exit();
			
			
			//$return = $API->qbxml($xml, '_itemraw_qbxml_callback');
			$return = $API->qbxml($xml);
			
			// NOT USED IN REAL TIME MODE
			// This function gets called when QuickBooks Online Edition sends a response back
			/*
			function _itemraw_qbxml_callback($method, $action, $ID, &$err, $qbxml, $Iterator, $qbres)
			{
				
				//global $_SESSION;
				//print('<Br>We got back this qbXML from QuickBooks Online Edition: <br><br>' . $qbxml);			
				//$xml = simplexml_load_string($qbxml);			
				//$listid = $xml->QBXMLMsgsRs->CustomerQueryRs->CustomerRet->ListID;		
				//$_SESSION['listid'] = $listid;			
				//print( "<Br><br>LIST ID: ".$listid."<br>" );			
				//ECHO "<br><Br>ARRAY:<br><Br>";			
				//print_r($xml);			
				//return $listid;			
				
			}
			*/
			
			// GET THE FULL NAME
			if ($API->usingRealtime()){
				echo "<br>- RESPONSE FROM QUICKBOOKS FOR CHECK ITEM<br>";
				print_r($return);
				echo "<br>";				
				$xml = simplexml_load_string($return['qbxml']);					
				$product['qb_name'] = $xml->QBXMLMsgsRs->ItemQueryRs->ItemServiceRet->FullName;
				echo "<Br>- Item FULL NAME FROM QUICKBOOKS: ".$product['qb_name']." <Br>";				
			}
		//} // END CHECK
		
		//exit;
		
		// ADD ITEM TO QUICKBOOKS
		if($product['qb_name'] == ""){
		
									
			$xml = '<ItemServiceAddRq> 
						<ItemServiceAdd>
							<Name >'.$product['name'].'</Name>
							<SalesOrPurchase>
								<Desc >'.$product['name'].'</Desc>
								<Price >'.$product['price'].'</Price>
								<AccountRef>
									<FullName >Sales</FullName>
								</AccountRef>
							</SalesOrPurchase>
						</ItemServiceAdd>
					</ItemServiceAddRq>';
			
			//echo "$xml";
		
			$return = $API->qbxml($xml);

			// NOT USED BECAUSE OF REAL TIME QBOE
			// This function gets called when QuickBooks Online Edition sends a response back
			/*function _itemadd_raw_qbxml_callback($method, $action, $ID, &$err, $qbxml, $Iterator, $qbres)
			{
				global $_SESSION;
				print('<Br>We got back this qbXML from QuickBooks Online Edition: <Br><br>' . $qbxml . '<Br><br>');\			
				$xml = simplexml_load_string($qbxml);
				print("<br><br>".$xml."");			
				$listid = $xml->QBXMLMsgsRs->CustomerAddRs->CustomerRet->ListID;
				// INSERT LIST ID IF CUSTOMER 
				$insert = "UPDATE user_account SET qb_list_id='".$listid."' WHERE account_id='".$_SESSION['UserAccount']['userid']."'";
				$result = doQuery($insert);
				// SET LIST ID
				$_SESSION['listid'] = $listid;		
			}					*/
			
			// GET THE LIST ID
			if ($API->usingRealtime()){
				echo "<br>- RESPONSE FROM QUICKBOOKS FOR ADD ITEM<br>";
				print_r($return);
				echo "<br>";				
				$xml = simplexml_load_string($return['qbxml']);					
				$product['qb_name']= $xml->QBXMLMsgsRs->ItemServiceAddRs->ItemServiceRet->FullName;
				echo "<Br>- ITEM FULL NAME  FROM QUICKBOOKS:".$product['qb_name']."<Br>";
			}			
		}		
		// UPDATE PRODUCE
		doQuery("UPDATE ecommerce_products SET qb_name='".$product['qb_name']."' WHERE product_id='".$product['product_id']."'");
		return $product['qb_name'];
	}
	
	/**
	 *
	 * CHECKS AND ADS A CLASS INTO QUICKBOOKS
	 * RETURNS THE ATTRUIBE INFORMATION AND ATTRIBUTE XML
	 *
	 */
	function QuickbooksAttributeClass($item_id){
		global $_SETTINGS;
		// GET THE COLLECTION / PATTERN TO INSERT AS THE ATTRIBUTE TO REFERENCE INTO A LINE ITEM CLASS
		// TODO...
		
		// GET THE ATTRIBUTE INFO
		$attribselect = "SELECT * FROM ecommerce_product_attribute_cart_relational WHERE relational_item_id='".$item_id."'";
		$attribresult = doQuery($attribselect);
		while($attribrow = mysql_fetch_array($attribresult)){
			// THIS IS FOR THE DESCRIPTION
			$attribute = lookupDbValue('ecommerce_product_attributes','name',$attribrow['attribute_id'],'attribute_id');
			$attribute_value = lookupDbValue('ecommerce_product_attribute_values','name',$attribrow['attribute_value_id'],'attribute_value_id');
			$attributes .= " [".$attribute.":".$attribute_value."] ";
			
			// IF THIS IS THE ATTRIBUTE TO REFERENCE INTO A LINE ITEM CLASS THEN DO IT
			if($attribute == $_SETTINGS['quickbooks_attribute_reference_item_class']){
				// CHECK THAT A CLASS EXISTS IN QB FOR THIS VALUE					
				$attributeclassxml =   '
										<ClassRef>
										<FullName >'.$attribute_value.'</FullName>
										</ClassRef>
										';
			}				
		}
		$returnarray = Array($attributes,$attributeclassxml);
		return $returnarray;
	}
	
	/**
	 *
	 * ADD SALES RECEIPT TO QUICKBOOKS
	 * RETURNS A TxnID
	 *
	 */	
	function QuickbooksAddSalesReceipt($xid,$order_id)
	{
	
		global $_SESSION;
		global $_SETTINGS;
		//error_reporting(E_ALL | E_STRICT);
		
		/**
		 * Require the QuickBooks base classes
		 */
		require_once '/home/ksdwebksd/dev/2010/shebeads/admin/modules/ecommerce/quickbooks/qboe/v1-5-3/QuickBooks.php';

		// get customer
		$select = "SELECT * FROM user_account WHERE account_id='".$xid."' LIMIT 1";
		$result = doQuery($select);
		$customer = mysql_fetch_array($result);
		$custlistid = $customer['qb_list_id'];
		
		// get order
		$select = "SELECT * FROM ecommerce_orders WHERE order_id='".$order_id."' LIMIT 1";
		$result = doQuery($select);
		$order = mysql_fetch_array($result);
		$txnid = $order['qb_txn_id'];
		
		//GET BILLING INFO
		$select = "SELECT * FROM user_contact WHERE contact_id='".$order['billing_id']."' LIMIT 1";
		$result = doQuery($select);
		$billing = mysql_fetch_array($result);

		//GET SHIPPING INFO
		$select = "SELECT * FROM user_contact WHERE contact_id='".$order['shipping_id']."' LIMIT 1";
		$result = doQuery($select);
		$shipping = mysql_fetch_array($result);
	
		// Create the API instance
		$source_type = QUICKBOOKS_API_SOURCE_ONLINE_EDITION;
		$API = new QuickBooks_API(null, 'api', $source_type, null, array(), $this->quickbooks_source_options, array());
		$API->enableRealtime(true);
		// Let's get some general information about this connection to QBOE: 
		print('Our connection ticket is: ' . $API->connectionTicket() . "\n <br>");
		print('Our session ticket is: ' . $API->sessionTicket() . "\n <br>");
		print('Our application id is: ' . $API->applicationID() . "\n <br>");
		print('Our application login is: ' . $API->applicationLogin() . "\n <br>");
		print('Last error number: ' . $API->errorNumber() . "\n <br>");
		print('Last error message: ' . $API->errorMessage() . "\n <br>");
		
		// BUILD ITEM XML
		//$itemxml = '<SalesReceiptLineGroupAdd >';
		// GET CART ITEM
		$select = "SELECT * FROM ecommerce_product_cart_relational WHERE shopping_cart_id='".$order['shopping_cart_id']."'";
		$result = doQuery($select);
		$num = mysql_num_rows($result);
		$i = 0;
		while($i<$num){
			$row = mysql_fetch_array($result);
			// GET THE PRODUCT
			$select = "SELECT * FROM ecommerce_products WHERE product_id='".$row['product_id']."' LIMIT 1";
			$res = doQuery($select);
			$product = mysql_fetch_array($res);
			
			
			// GET THE ATTRIBUTES AND AMMEND THEM TO THE DESCRIPTION
			$attributeclassxml = "";
			$attributes = "";			
			// THIS FUNCTION NEEDS THE PRODUCT CART RELATIONAL ITEM
			$attributeArray 	= $this->QuickbooksAttributeClass($row['item_id']);
			$attributes			= $attributeArray[0];
			$attributeclassxml	= $attributeArray[1];
			
			
			// CHECK IF ITEM IN QUICKBOOKS INSERT IT IF NOT
			$qbFullName = $this->QuickbooksAddItem($product);
			$itemxml .= '<SalesReceiptLineAdd >
							<ItemRef>
								<ListID ></ListID>
								<FullName >'.$qbFullName.'</FullName>
							</ItemRef>
							<Desc >'.$product['name'].' - '.$attributes.'</Desc>
							<Quantity >'.$row['qty'].'</Quantity>'.$attributeclassxml.'
							<Amount >'.$product['price'].'</Amount>
						</SalesReceiptLineAdd>';
			$i++;
		}
		//$itemxml .= '</SalesReceiptLineGroupAdd >';
		
		// DISCOUNTS
		$discountxml = '';
		/*
		$promocode_discount = $order['promocode_discount'];
		if($promocode_discount > 0){		
			$discountxml .=	'
							<DiscountLineAdd>
								<Amount >'.$promocode_discount.'</Amount>
							</DiscountLineAdd>
							';		
		}
		
		$account_discount = $order['account_discount'];
		if($account_discount > 0){		
			$discountxml .=	'
							<DiscountLineAdd>
								<Amount >'.$account_discount.'</Amount>
							</DiscountLineAdd>
							';		
		}
		*/
		
		$taxxml = 	'<SalesTaxLineAdd>
						<Amount >'.$order['tax'].'</Amount>
					</SalesTaxLineAdd>';
		
		$shippingxml = 	'<ShippingLineAdd>
							<Amount >'.$order['sh'].'</Amount>
						</ShippingLineAdd>';
						
		$xml = '<SalesReceiptAddRq> 
					<SalesReceiptAdd >
						<CustomerRef>
							<ListID >'.$custlistid.'</ListID>
							<FullName >'.$customer['name'].'</FullName>
						</CustomerRef>
						<ClassRef>
							<FullName >Web</FullName>
						</ClassRef>
						<TxnDate >'.date("Y-m-d").'</TxnDate>
						<BillAddress>
							<Addr1 >'.$billing['address1'].'</Addr1>
							<Addr2 >'.$billing['address2'].'</Addr2>
							<City >'.$billing['city'].'</City>
							<State >'.lookupDbValue('state','state',$billing["state"],'state_id').'</State>
							<PostalCode >'.$billing['zip'].'</PostalCode>
							<Country >'.lookupDbValue('country','country',$billing["country"],'country_id').'</Country>
						</BillAddress>
						<ShipAddress>
							<Addr1 >'.$shipping['address1'].'</Addr1>
							<Addr2 >'.$shipping['address2'].'</Addr2>
							<City >'.$shipping['city'].'</City>
							<State >'.lookupDbValue('state','state',$shipping["state"],'state_id').'</State>
							<PostalCode >'.$shipping['zip'].'</PostalCode>
							<Country >'.lookupDbValue('country','country',$shipping["country"],'country_id').'</Country>
						</ShipAddress>
						<IsToBePrinted >0</IsToBePrinted>					
						'.$itemxml.''.$discountxml.'
						'.$taxxml.'
						'.$shippingxml.'
					</SalesReceiptAdd>
				</SalesReceiptAddRq>';
			
			echo "<br>XML: <br><br> $xml <Br><Br>";
		$requestXML = $xml;
		$return = $API->qbxml($xml);

		// NOT USED BECAUSE OF REAL TIME QBOE
		// This function gets called when QuickBooks Online Edition sends a response back
		/*function _sradd_raw_qbxml_callback($method, $action, $ID, &$err, $qbxml, $Iterator, $qbres)
		{
			global $_SESSION;
			print('<Br>We got back this qbXML from QuickBooks Online Edition: <Br><br>' . $qbxml . '<Br><br>');\			
			$xml = simplexml_load_string($qbxml);
			print("<br><br>".$xml."");			
			$listid = $xml->QBXMLMsgsRs->CustomerAddRs->CustomerRet->ListID;
			// INSERT LIST ID IF CUSTOMER 
			$insert = "UPDATE user_account SET qb_list_id='".$listid."' WHERE account_id='".$_SESSION['UserAccount']['userid']."'";
			$result = doQuery($insert);
			// SET LIST ID
			$_SESSION['listid'] = $listid;
		}*/					
		
		// GET THE LIST ID
		if ($API->usingRealtime()){
		
			echo "<br>- RESPONSE FROM QUICKBOOKS FOR ADD SALES RECEIPT<br>";
			print_r($return);
			echo "<br><br>";	
			
			

			//echo "<br><br>".$return['qbxml']."<br><br>";
			
			$formatxml = strstr($return['qbxml'],"<");
			
			echo "<br><br>".$formatxml."<br><br>";
			
			
			$xml = simplexml_load_string($formatxml);				
			$txnid = $xml->QBXMLMsgsRs->SalesReceiptAddRs->SalesReceiptRet->TxnID;
			$refnum = $xml->QBXMLMsgsRs->SalesReceiptAddRs->SalesReceiptRet->RefNumber;			
			echo "<br><Br>- SALES RECEIPT TXN ID FROM QUICKBOOKS: ".$txnid." <Br><Br>";
			
		}		
		
		// UPDATE CUST LIST ID
		doQuery("UPDATE ecommerce_orders SET qb_txn_id='".$txnid."',qb_ref_number='".$refnum."',qb_request='".$requestXML."',qb_response='".var_dump($return)."' WHERE order_id='".$order_id."'");
		return $txnid;	
		
		
	}
	
	/**
	 *
	 * CHECK AND ADS CUSTOMER TO QUICKBOOKS
	 * RETURNS A ListID
	 *
	 */
	function QuickbooksAddCustomer($xid)
	{
	
		global $_SESSION;
		global $_SETTINGS;
		//error_reporting(E_ALL | E_STRICT);
		
		/**
		 * Require the QuickBooks base classes
		 */
		require_once '/home/ksdwebksd/dev/2010/shebeads/admin/modules/ecommerce/quickbooks/qboe/v1-5-3/QuickBooks.php';

		// get customer
		$select = "SELECT * FROM user_account WHERE account_id='".$_SESSION['UserAccount']['userid']."' LIMIT 1";
		$result = doQuery($select);
		$customer = mysql_fetch_array($result);
		//$listid = $customer['qb_list_id'];
		
		// Create the API instance
		$source_type = QUICKBOOKS_API_SOURCE_ONLINE_EDITION;
		$API = new QuickBooks_API(null, 'api', $source_type, null, array(), $this->quickbooks_source_options, array());
		$API->enableRealtime(true);
		// Let's get some general information about this connection to QBOE: 
		//print('Our connection ticket is: ' . $API->connectionTicket() . "\n <br>");
		//print('Our session ticket is: ' . $API->sessionTicket() . "\n <br>");
		//print('Our application id is: ' . $API->applicationID() . "\n <br>");
		//print('Our application login is: ' . $API->applicationLogin() . "\n <br>");
		//print('Last error number: ' . $API->errorNumber() . "\n <br>");
		//print('Last error message: ' . $API->errorMessage() . "\n <br>");
		
		// CHECK QUICKBOOKS FOR LIST ID
		if( $listid == "" || $listid == 0){		
			// CHECK CUSTOMER
			
			$xml = '<CustomerQueryRq>
						<FullName>'.$customer['name'].'</FullName>
					</CustomerQueryRq>';
			
			echo "$xml";
			
			$return = $API->qbxml($xml);
			
			// NOT USED IN REAL TIME MODE
			// This function gets called when QuickBooks Online Edition sends a response back
			/*function _raw_qbxml_callback($method, $action, $ID, &$err, $qbxml, $Iterator, $qbres)
			{
				
				//global $_SESSION;
				//print('<Br>We got back this qbXML from QuickBooks Online Edition: <br><br>' . $qbxml);			
				//$xml = simplexml_load_string($qbxml);			
				//$listid = $xml->QBXMLMsgsRs->CustomerQueryRs->CustomerRet->ListID;		
				//$_SESSION['listid'] = $listid;			
				//print( "<Br><br>LIST ID: ".$listid."<br>" );			
				//ECHO "<br><Br>ARRAY:<br><Br>";			
				//print_r($xml);			
				//return $listid;			
				
			}*/
			
			// GET THE LIST ID
			if ($API->usingRealtime()){
				echo "<br>- RESPONSE FROM QUICKBOOKS FOR CHECK CUSTOMER<br>";
				print_r($return);
				echo "<br>";				
				$xml = simplexml_load_string($return['qbxml']);					
				$listid = $xml->QBXMLMsgsRs->CustomerQueryRs->CustomerRet->ListID;
				echo "<Br>- CUSTOMER LIST ID FROM QUICKBOOKS: $listid<Br>";				
			}
		} // END CHECK
		
		//exit;
		
		// ADD CUSTOMER TO QUICKBOOKS
		if($listid == "" || $listid == 0){
		
			//GET LAST ORDER
			$select = "SELECT * FROM ecommerce_orders WHERE account_id='".$_SESSION['UserAccount']['userid']."' ORDER BY order_id DESC LIMIT 1";
			$result = doQuery($select);
			$order = mysql_fetch_array($result);
			
			//GET BILLING INFO
			$select = "SELECT * FROM user_contact WHERE contact_id='".$order['billing_id']."' LIMIT 1";
			$result = doQuery($select);
			$billing = mysql_fetch_array($result);

			//GET SHIPPING INFO
			//$select = "SELECT * FROM user_contact WHERE contact_id='".$order['shipping_id']."' LIMIT 1";
			//$result = doQuery($select);
			//$billing = mysql_fetch_array($result);
			
						
			$xml = '<CustomerAddRq>
						<CustomerAdd>
							<Name >'.$customer['name'].'</Name>
							<FirstName >'.$billing["first_name"].'</FirstName>
							<LastName >'.$billing["last_name"].'</LastName>
							<BillAddress>
								<Addr1 >'.$billing["address1"].'</Addr1>
								<Addr2 >'.$billing["address2"].'</Addr2>
								<City >'.$billing["city"].'</City>
								<State >'.lookupDbValue('state','state',$billing["state"],'state_id').'</State>
								<PostalCode >'.$billing["zip"].'</PostalCode>
								<Country >'.lookupDbValue('country','country',$billing["country"],'country_id').'</Country>
							</BillAddress>
							<Phone >'.$billing["phone"].'</Phone>
							<Email >'.$billing["email"].'</Email>
							<DeliveryMethod >Print</DeliveryMethod>
						</CustomerAdd>
					</CustomerAddRq>';
			
			echo "$xml";
		
			$return = $API->qbxml($xml);

			// NOT USED BECAUSE OF REAL TIME QBOE
			// This function gets called when QuickBooks Online Edition sends a response back
			/*function _custadd_raw_qbxml_callback($method, $action, $ID, &$err, $qbxml, $Iterator, $qbres)
			{
				global $_SESSION;
				print('<Br>We got back this qbXML from QuickBooks Online Edition: <Br><br>' . $qbxml . '<Br><br>');\			
				$xml = simplexml_load_string($qbxml);
				print("<br><br>".$xml."");			
				$listid = $xml->QBXMLMsgsRs->CustomerAddRs->CustomerRet->ListID;
				// INSERT LIST ID IF CUSTOMER 
				$insert = "UPDATE user_account SET qb_list_id='".$listid."' WHERE account_id='".$_SESSION['UserAccount']['userid']."'";
				$result = doQuery($insert);
				// SET LIST ID
				$_SESSION['listid'] = $listid;		
			}					*/
			
			// GET THE LIST ID
			if ($API->usingRealtime()){
				echo "<br>- RESPONSE FROM QUICKBOOKS FOR ADD CUSTOMER<br>";
				print_r($return);
				echo "<br>";				
				$xml = simplexml_load_string($return['qbxml']);					
				$listid = $xml->QBXMLMsgsRs->CustomerAddRs->CustomerRet->ListID;
				echo "<Br>- CUSTOMER LIST ID FROM QUICKBOOKS: $listid<Br>";
			}			
		}		
		// UPDATE CUST LIST ID
		doQuery("UPDATE user_account SET qb_list_id='".$listid."' WHERE account_id='".$_SESSION['UserAccount']['userid']."'");
		return $listid;
	}
	
	/**
	 *
	 * AMMENDMANT TO CUSTOMER PAGE
	 *
	 */	
	function displayAccountForms($xid)
	{
	
		global $_SETTINGS;
		global $_SESSION;
		global $_REQUEST;
		
		echo "<table class='table-in-table'>";
		echo "<tr>";
		echo "<td valign='top'>";
		
		echo "<table class='table-in-table'>";
		echo 	"<tr><th colspan='2'><h2>Orders (".$num.")</h2> ".
				"<a href='?VIEW=".$_REQUEST['VIEW']."&SUB=PAYMENT&xid=".$_REQUEST['xid']."' style='float:right;'> ".
				"<img src='".$_SETTINGS['website']."admin/images/icons/wallet_16.png' alt='edit' border='0'> Create Order </a>".
				"</th></tr>";
		echo "</table>";	
		
		echo "<table class='table-in-table'>";	
		if($num > 0){				
			echo "<tr><th style='width:100px'>Order Id</th><th>Date</th><th>Status</th><th>Subtotal</th><th>Shipping</th><th>Tax</th><th>Total</th><th style='width:350px;'>Action</th></tr>";			
			$select = "SELECT * FROM ecommerce_orders WHERE active='1'";
			$result = doQuery($select);
			$i = 0;
			while($i<$num){
				$row = mysql_fetch_array($result);						
				echo "<tr>";
					echo "
						<td>".$row['order_id']."</td>
						<td>".$row['date']."</td>
						<td>".$row['status']."</td>			
						<td>".$row['subtotal']."</td>		
						<td>".$row['shipping']."</td>	
						<td>".$row['tax']."</td>	
						<td>".$row['total']."</td>							
					";				
					
					echo "<td></td>";
					
				echo "</tr>";			
				$i++;
			}	
		} else {
			echo "<tr><td style='text-align:center;'>This customer has 0 orders.</td></tr>";
		}			
		echo "</td>";
		echo "</tr>";	
		echo "</table>";	
	}
	
	/**
	 *
	 * How to get promos page
	 *
	 */	
	function howToGetPromos()
	{
		global $_SETTINGS;
		echo "<div class='wesclass-how-to-get-these-promos'>";
		$today = date("Y-m-d");
		
		// SELECT PUBLIC PROMOS
		
		$select = 	"SELECT * FROM ecommerce_coupon_codes ".
				"WHERE ".
				"(`start_date` <= '".$today."' AND ".
				"`expiration_date` >= '".$today."') AND ".
				"status='Active' AND ".
				"active='1' AND ".
				"public='1' ".
				"ORDER BY coupon_id DESC";
		$result = 	doQuery($select);
		$num = 		mysql_num_rows($result);
		$i = 0;
		while($row = mysql_fetch_array($result)){
			echo "	<div class='wesclass-coupon-code-how'>
					<h3>".$row['name']."</h3> <h4><label>Code:</label> \"".$row['code']."\"</h4>
					<form class='wesclass-how-form' method='POST' action='".$_SETTINGS['website'].$_SETTINGS['shopping_cart_page_clean_url']."'>
						<input type='hidden' name='promo' value='".$row['code']."' />
						<input type='submit' name='APPLYPROMO' value='Apply Code' />
					</form>
					<div class='wesclass-cc-description'>
						".$row['description']."
					</div>
					<div class='wesclass-cc-details'>
						<ul>
							<li>Start Date: ".FormatTimeStamp($row['start_date'])."</li>
							<li>Expiration Date: ".FormatTimeStamp($row['expiration_date'])."</li>
						</ul>
					</div>
				</div>";
			$i++;
		}
		
		
		// BIRTHDAY PROMO
		if($_SETTINGS['birthday_promo_on'] == '1')
		{
			echo "	<div class='wesclass-coupon-code-how'>
					<h3>For Your Birthday</h3>
					<div>
						".$_SETTINGS['birthday_promo_description']."
					</div>
				</div>";
		}
		
		//echo $num;
		//echo "<br>";
		//echo $_SETTINGS['birthday_promo_on'];
		//echo "<br>";
		
		if($num == '0' AND $_SETTINGS['birthday_promo_on'] == '0')
		{
			echo "<p>Sorry there are no current promotions.</p>";
		}
		
		echo "</div>";
	}
	
	/**
	 *
	 * USPS GENERATE LABEL CLASS
	 *
	 */	
	function USPSLabel($order_id)
	{
		global $_SETTINGS;

		// This script was written by Mark Sanborn at http://www.marksanborn.net
		// If this script benefits you are your business please consider a donation
		// You can donate at http://www.marksanborn.net/donate.

		// ========== CHANGE THESE VALUES TO MATCH YOUR OWN ===========
		if($_SETTINGS['usps_live'] == '1'){
			$userName 		= $_SETTINGS['usps_production_user_id']; // Your USPS Username
			$password		= $_SETTINGS['usps_production_password'];
		} else {
			$userName 		= $_SETTINGS['usps_test_user_id']; // Your USPS Username
			$password		= $_SETTINGS['usps_test_password'];
		}
		
		// GET ORDER
		$select = "SELECT * FROM ecommerce_orders WHERE order_id='".$order_id."' LIMIT 1";
		$result = doQuery($select);
		$order = mysql_fetch_array($result);
		
		// GET SHIPPING CONTACT
		$select = "SELECT * FROM user_contact WHERE contact_id='".$order['shipping_id']."'";
		$result = doQuery($select);
		$shipping = mysql_fetch_array($result);
		
		// WEIGHT
		$weightOunces	= $order['weight'];
		if($weightOunces == ""){ $weightOunces = 5; }

		// SERVICE TYPE CODE
		$serviceType 	= lookupDbValue('ecommerce_shipping_methods','code',$order['shipping_method_id'],'shipping_method_id');
		
		// =============== DON'T CHANGE BELOW THIS LINE ===============

		//$url = "https://Secure.ShippingAPIs.com/ShippingAPI.dll";
		//http://production.shippingapis.com/ShippingAPI.dll
		//http://testing.shippingapis.com/ShippingAPITest.dll
		//https://secure.shippingapis.com/ShippingAPITest.dll
		
		//"http://testing.shippingapis.com/ShippingAPITest.dll?API=[API_Name]&XML=[XML_String_containing_User_ID]
		
		//http://testing.shippingapis.com/ShippingAPITest.dll
		//https://secure.shippingapis.com/ShippingAPITest.dll.  
		
		if($_SETTINGS['usps_live'] == '1'){
			$url = "https://Secure.ShippingAPIs.com/ShippingAPI.dll";
		} else {
			$url = "https://secure.shippingapis.com/ShippingAPITest.dll";
		}
		
		

		$data = "".$url."?API=DeliveryConfirmationV3&XML=<DeliveryConfirmationV3.0Request USERID=\"".$userName."\" >
			<Option>1</Option>
			<ImageParameters />
			<FromName>".$_SETTINGS['return_address_name']."</FromName>
			<FromFirm />
			<FromAddress1 />
			<FromAddress2>".$_SETTINGS['return_address1']." ".$_SETTINGS['return_address2']."</FromAddress2>
			<FromCity>".$_SETTINGS['return_address_city']."</FromCity>
			<FromState>".lookupDbValue('state','state_abbr',$_SETTINGS['return_address_state'],'state_id')."</FromState>
			<FromZip5>".$_SETTINGS['return_address_zip']."</FromZip5>
			<FromZip4 />
			<ToName>".$shipping['first_name']." ".$shipping['last_name']."</ToName>
			<ToFirm />
			<ToAddress1 />
			<ToAddress2>".$shipping['address1']." ".$shipping['address2']."</ToAddress2>
			<ToCity>".$shipping['city']."</ToCity>
			<ToState>".lookupDbValue('state','state_abbr',$shipping['state'],'state_id')."</ToState>
			<ToZip5>".$shipping['zip']."</ToZip5>
			<ToZip4 />
			<WeightInOunces>".$weightOunces."</WeightInOunces>
			<ServiceType>".$serviceType."</ServiceType>
			<POZipCode />
			<ImageType>PDF</ImageType>
			<LabelDate />
			</DeliveryConfirmationV3.0Request>";

			
			
		//echo "<br>REQUEST:<br> ".$data." <Br><br>";
		
		
		echo "<br>URL: ".$url."<br>";
		
		
		$ch = curl_init();

		// set the target url
		curl_setopt($ch, CURLOPT_URL,$data);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

		// parameters to post
		//curl_setopt($ch, CURLOPT_POST, 1);
		// send the POST values to USPS
		//curl_setopt($ch, CURLOPT_POSTFIELDS,$data);

		$result=curl_exec($ch);
		
		var_dump($result);
		die();
		exit();
		
		
		$data = strstr($result, '<?');
		//echo '<Br>DATA:<br>'. $data. '<br>'; // Uncomment to show XML in comments
		//die('madeit');
		//exit();
		$xmlParser = new uspsxmlParser();
		$fromUSPS = $xmlParser->xmlparser($data);
		$fromUSPS = $xmlParser->getData();

		curl_close($ch);
		return $fromUSPS;
	}

	/**
	 *
	 * UPSP RACE CALCULATOR
	 *
	 */
	function USPSParcelRate($weight,$dest_zip)
	{

		global $_SETTINGS;
		
		// This script was written by Mark Sanborn at http://www.marksanborn.net
		// If this script benefits you are your business please consider a donation
		// You can donate at http://www.marksanborn.net/donate.  

		// ========== CHANGE THESE VALUES TO MATCH YOUR OWN ===========

		$userName = 'username'; // Your USPS Username
		$orig_zip = '12345'; // Zipcode you are shipping FROM

		// =============== DON'T CHANGE BELOW THIS LINE ===============

		$url = "http://Production.ShippingAPIs.com/ShippingAPI.dll";
		$ch = curl_init();

		// set the target url
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

		// parameters to post
		curl_setopt($ch, CURLOPT_POST, 1);

		$data = "API=RateV3&XML=<RateV3Request USERID=\"$userName\"><Package ID=\"1ST\"><Service>PRIORITY</Service><ZipOrigination>$orig_zip</ZipOrigination><ZipDestination>$dest_zip</ZipDestination><Pounds>$weight</Pounds><Ounces>0</Ounces><Size>REGULAR</Size><Machinable>TRUE</Machinable></Package></RateV3Request>";

		// send the POST values to USPS
		curl_setopt($ch, CURLOPT_POSTFIELDS,$data);

		$result=curl_exec ($ch);
		$data = strstr($result, '<?');
		// echo '<!-- '. $data. ' -->'; // Uncomment to show XML in comments
		$xml_parser = xml_parser_create();
		xml_parse_into_struct($xml_parser, $data, $vals, $index);
		xml_parser_free($xml_parser);
		$params = array();
		$level = array();
		foreach ($vals as $xml_elem) {
			if ($xml_elem['type'] == 'open') {
				if (array_key_exists('attributes',$xml_elem)) {
					list($level[$xml_elem['level']],$extra) = array_values($xml_elem['attributes']);
				} else {
				$level[$xml_elem['level']] = $xml_elem['tag'];
				}
			}
			if ($xml_elem['type'] == 'complete') {
			$start_level = 1;
			$php_stmt = '$params';
			while($start_level < $xml_elem['level']) {
				$php_stmt .= '[$level['.$start_level.']]';
				$start_level++;
			}
			$php_stmt .= '[$xml_elem[\'tag\']] = $xml_elem[\'value\'];';
			eval($php_stmt);
			}
		}
		curl_close($ch);
		// echo '<pre>'; print_r($params); echo'</pre>'; // Uncomment to see xml tags
		return $params['RATEV3RESPONSE']['1ST']['1']['RATE'];
	}
	
	/**
	 *
	 * PRODUCT IMAGES
	 *
	 */
	function productImages()
	{
		global $_GET;
		global $_REQUEST;
		global $_SETTINGS;
	
		/**
		 *
		 * IMAGES
		 *
		 */
		if($_REQUEST['pid'] != ""){
			// GET IMAGES FROM RELATIONAL
			$sel1 = "SELECT * FROM ecommerce_product_images WHERE product_id='".$_REQUEST['pid']."' AND active='1' ORDER BY sort_level ASC ";
			$res1 = doQuery($sel1);
			$i1 = 0;
			$num1 = mysql_num_rows($res1);
			
			echo "<TR BGCOLOR='#f2f2f2'>";
			echo "<Th width='200' height='40' style='padding-left:20px;'>Additional Product Image(s) </Th>";
			echo "<TD>";
			
			echo "	<ul class='innersort' id='sortable'>";
				
				while($i1<$num1){
					$row1 = mysql_fetch_array($res1);
					echo "
						<li id='".$row1['image_id']."'>
							<a style='float:right;' id='image".$i1."' href=''><img src='images/icons/delete_16.png' border='0'> Remove</a>
							<script>
								$('#image".$i1 ."' ).bind( 'click', function(event, ui) {							  
								  event.preventDefault();
								  $.ajax({
									  type: 'POST',
									  url: 'modules/ecommerce/ecommerce.php',
									  data: { DELETE_IMAGE: '1', pid: '".$_REQUEST['pid']."', iid: '".$row1['image_id']."' }
									});							
									$('#".$row1['image_id'] ."' ).fadeOut('slow');	
								});
							</script>
							<div style='float:left; margin-right:10px; width:100px; min-height:100px;'>
								<img src='".$_SETTINGS['website'] ."uploads-products/".$row1['image']."' width='100px'> &nbsp;
							</div>
							<input style='float:none;' type='text' name='image".$i1."' value='".$row1['image']."' /><button type='button' onClick='SmallFileBrowser('../uploads-products/','image".$i1."')'>Choose Image...</button>
							<br><br>
							<input type='text' name='imagename".$i1."' value='".$row1['name'] ."' > Name
							<Br><Br>
							<textarea name='imagedescription".$i1."' >".$row1['description']."</textarea>&nbsp; Description
							<br clear='all'>
						</li>";	
					$i1++;
				}
				
				if($i1 == 0){
					echo "<li>There are no additional images for this product.</li>";
				}
				
				echo "</ul>";
				echo "	<script>					
							$('#sortable').sortable();							
							// AJAX REQUEST SORT TOP LEVEL					
							$( '#sortable' ).bind( 'sortstart', function(event, ui) {
								$(ui.item).css('background-color','#f3f8ff');
								$(ui.item).css('border','2px solid #89a8d8');
								$(ui.item).css('cursor','-moz-grabbing');						
							});							
							$( '#sortable' ).bind( 'sortstop', function(event, ui) {
							  var result = $('#sortable').sortable('toArray');
							  var resultstring = result.toString();							  
							  $.ajax({
								  type: 'POST',
								  url: 'modules/ecommerce/ecommerce.php',
								  data: { sortarray: resultstring, SORT_IMAGES: '1', pid: ".$_REQUEST['pid']." }
								});							   
								$(ui.item).css('background-color','#EDF1F2');
								$(ui.item).css('border','1px solid #eeeeee');
								$(ui.item).css('cursor','-moz-grab');					   
							});					
						</script>
			</TD>
			</TR>";
			
			/**
			 *
			 * ADD NEW IMAGE
			 *
			 */
			$identifier = "imagenew"; 

			echo "
			<TR BGCOLOR='#f2f2f2'>
			<Th width='200' height='40' style='padding-left:20px;'> &nbsp; </Th>
			<TD>
			<a class='toggleridentifier".$identifier." tog'>Add Image</a>
			<a class='togglercloseidentifier".$identifier." tog'>Cancel New Image</a>
			</TD>
			</TR>";
			
			
			/**
			 *
			 * NEW IMAGE NAME
			 *
			 */
			echo "
			<TR BGCOLOR='#f2f2f2' class='toggleidentifier".$identifier."'>
			<Th width='200' height='40' style='padding-left:20px; background-color:#EDF1F2;'>New Image Name</Th>
			<TD style=' background-color:#EDF1F2;'>
			<input name='newname' value='".$_POST['newname']."' />
			</TD>
			</TR>";
			
			
			/**
			 *
			 * NEW IMAGE DESCRIPTION
			 *
			 */
			echo "
			<TR BGCOLOR='#f2f2f2' class='toggleidentifier".$identifier."'>
			<Th width='200' height='40' style='padding-left:20px; background-color:#EDF1F2;'>New Image Description</Th>
			<TD style='background-color:#EDF1F2;'>
			<textarea name='newdescription' >".$_POST['newdescription']."</textarea>
			</TD>
			</TR>";
			
			
			/**
			 *
			 * NEW IMAGE FILE BROWSER
			 *
			 */
			echo "
			<TR BGCOLOR='#f2f2f2' class='toggleidentifier".$identifier."'>
			<Th width='200' height='40' style='padding-left:20px; background-color:#EDF1F2;'>New Image</Th>
			<TD style=' background-color:#EDF1F2;'>";
			
			echo "	<input style=\"float:none;\" type=\"text\" name=\"newimage\" value=\"\" /><button type=\"button\" onClick=\"SmallFileBrowser('../uploads-products/','newimage')\">Choose Image...</button>";
				
			echo "	</TD>
					</TR>
					<script type='text/javascript'>
					  $('.toggleidentifier".$identifier."').hide();
					  $('.togglercloseidentifier".$identifier."').hide();
					  $('.toggleridentifier".$identifier."').click(function()
					  {
						$('.toggleidentifier".$identifier."').slideToggle('fast',callback('".$identifier."'));
					  });
					  
					  $('.togglercloseidentifier".$identifier."').click(function()
					  {
						$('.toggleidentifier".$identifier."').slideToggle('fast',callback1('".$identifier."'));
					  });
					</script>";
					
		} else {
		
			/**
			 *
			 * IF NOT EDITING -> ADD IMAGE FORM
			 *
			 */
			echo "
			<TR BGCOLOR='#f2f2f2'>
			<Th width='200' height='40' style='padding-left:20px;'>Product Image</Th>
			<TD>";
				
				/*
				<!--
				<input type="text" name="image" value="<?=basename($_POST['image'])?>" />
				<button type="button" onClick="OpenFileBrowser('image', function(url) {document.productform.image.value=url;}, function() {return document.productform.image.value;} )">
				Choose Image...
				</button>
				-->
				*/
				
				echo "<input style='float:none;' type='text' name='image' value='".basename($_POST['image'])."' />";
				echo "<button type=\"button\" onClick=\"SmallFileBrowser('../uploads-products/','image')\">Choose Image...</button>";	
				echo "<br><small>Note: Additional images can be added later. </small>
						</TD>
						</TR>";
			
			
			/**
			 *
			 * IMAGE NAME
			 *
			 */
			
			echo "	<TR BGCOLOR='#f2f2f2'>
					<Th width='200' height='40' style='padding-left:20px;'>Image Name </Th>
					<TD>
						<input name='imagename' value='".$_POST['imagename']."' />
					</TD>
					</TR>";
			
			
			/**
			 *
			 * IMAGE DESCRIPTION
			 *
			 */
			
			echo "	<TR BGCOLOR='#f2f2f2'>
					<Th width='200' height='40' style='padding-left:20px;'>Image Description </Th>
					<TD>
						<textarea name='imagedescription'>".$_POST['imagedescription']."</textarea>
					</TD>
					</TR>";
		}		
	}	
}

/** ALTERNATE USPS CLASS FOR TRACKING AND SUCH
 *
 * 
 *
 */
class USPS
{
		
	//http://production.shippingapis.com/ShippingAPI.dll
	//http://testing.shippingapis.com/ShippingAPITest.dll
	//https://secure.shippingapis.com/ShippingAPITest.dll
	
	var $user_id;
	var $post_url;

	
	function USPS(){
		global $_SETTINGS;
		if($_SETTINGS['usps_live'] == '1'){
			// LIVE SETTINGS
			$this->user_id = $_SETTINGS['usps_production_user_id'];
			$this->post_url = "http://testing.shippingapis.com/ShippingAPITest.dll";
		} else {
			// TEST SETTINGS
			$this->user_id = $_SETTINGS['usps_test_user_id'];
			$this->post_url = "http://production.shippingapis.com/ShippingAPI.dll";
		}	
	}
	
	
	//function __construct( $uid )
	//{
	//	$this->user_id = $uid;
	//}

	/**************************************************************************************
		GET RATE

		ex.

		$ordersArray = array(
			"org_zip" => $orgZip,
			"dest_zip" => $destZip,
			"pkgs" => $pkg,
			"service" => array("EXPRESS","PARCEL","PRIORITY")
		);

		$rv = $usps->GetRate($ordersArray);

	**************************************************************************************/

	function GetRate( $array )
	{
		$pkgArray = $array["pkgs"];
		$destZip = $array["dest_zip"];
		$orgZip = $array["org_zip"];
		$service = $array["service"];

		$returnArray = array();

		if(is_array($service)) $numServices = count($service);
		else $numServices = 1;

		for($i=0; $i<$numServices; $i ++)
		{
			if(is_array($service)) $curService = $service[$i];
				else $curService = $service;

			// create xml
			$xml = self::CreateMailXML($pkgArray, $orgZip, $destZip, $curService);

			// send to usps and get xml back
			$post_response = self::SendToUSPS($xml);

			// convert to xml
			$xml_reponse = self::ConvertXMLToArray($post_response);
			$xml_reponse = self::CreateRateArray($xml_reponse);

			$rate = 0;

			foreach( $xml_reponse["RATEV3RESPONSE"] as $key => $value )
			{

				 foreach($xml_reponse["RATEV3RESPONSE"][$key] as $rk => $rv)
				 {
					 if(is_array($xml_reponse["RATEV3RESPONSE"][$key][$rk]))
					 {
						 $rate += $xml_reponse["RATEV3RESPONSE"][$key][$rk]["RATE"];
					 }
				 }
			 }

			array_push($returnArray, array("service" => $curService, "price" => $rate ));
		}

		return $returnArray;
	 }

	/**************************************************************************************
		Track Package

		ex.

		$usps->TrackPackage("12345678908776543221"); // Tracking number

	**************************************************************************************/

	function TrackPackage($trackingnum)
	{
		$tracking_num = $trackingnum;
		 $returnArray = array();
		$detailsArray = array();

		// create xml
		$xml = self::CreateTrackingXML($tracking_num);

		// send to usps
		$post_response = self::SendToUSPS($xml);

		// convert xml
		$xml_response = self::ConvertXMLToArray($post_response);

		foreach($xml_response as $index)
		{
			if($index["tag"] == "TRACKSUMMARY")
			{
				$returnArray["summery"] = $index["value"];
			 }

			if($index["tag"] == "TRACKDETAIL")
			{
				 array_push($detailsArray, $index["value"]);
			}
		 }

		$returnArray["details"] = $detailsArray;

		 var_dump($returnArray);

	}

	private function CreateTrackingXML($tracking_num)
	 {
		$xml = 'API=TrackV2&XML=<TrackRequest USERID="'.$this->user_id.'">
		<TrackID ID="'.$tracking_num.'"></TrackID>
		</TrackRequest>';

		return $xml;
	}

	private function CreateMailXML($pkgArray, $orgZip, $destZip, $service)
	{
		$xml = 'API=RateV3&XML=<RateV3Request USERID="'.$this->user_id.'">';

		$x = 1; // Package number

		foreach($pkgArray as $pkg)
		{
			$weight = $pkg["weight"]*$pkg["amount"];

			if($weight > 70)
			{
				 while($weight > 70)
				 {
					$xml .= '<Package ID="'.$x++.'p">
					<Service>'.$service.'</Service>
					<ZipOrigination>'.$orgZip.'</ZipOrigination>
					<ZipDestination>'.$destZip.'</ZipDestination>
					<Pounds>70</Pounds>
						<Ounces>0</Ounces>
					<Size>regular</Size>
					<Width>15</Width>
					<Length>15</Length>
					<Height>20</Height>
					<Machinable>true</Machinable>
					</Package>';

					$weight -= 70;
				}
				$xml .= '<Package ID="'.$x++.'p">
				<Service>'.$service.'</Service>
				<ZipOrigination>'.$orgZip.'</ZipOrigination>
				<ZipDestination>'.$destZip.'</ZipDestination>
				<Pounds>'.$weight.'</Pounds>
				<Ounces>0</Ounces>
				<Size>regular</Size>
				<Width>15</Width>
				<Length>15</Length>
				<Height>20</Height>
				<Machinable>true</Machinable>
				</Package>';
			} else
			{
				$xml .= '<Package ID="'.$x++.'p">
				<Service>'.$service.'</Service>
				<ZipOrigination>'.$orgZip.'</ZipOrigination>
				<ZipDestination>'.$destZip.'</ZipDestination>
				<Pounds>'.$weight.'</Pounds>
				<Ounces>0</Ounces>
				<Size>regular</Size>
				<Width>15</Width>
				<Length>15</Length>
				<Height>20</Height>
				<Machinable>true</Machinable>
				</Package>';
			}
		}

		$xml .= "</RateV3Request>";

		return $xml;
	}

	private function CreateRateArray($vals)
	{
		foreach ($vals as $xml_elem)
		{
			if ($xml_elem['type'] == 'open')
			{
				if (array_key_exists('attributes',$xml_elem))
				 {
					list($level[$xml_elem['level']],$extra) = array_values($xml_elem['attributes']);
				} else {
					$level[$xml_elem['level']] = $xml_elem['tag'];
				}
			}  

			 if ($xml_elem['type'] == 'complete')
			 {
				$start_level = 1;
				$php_stmt = '$params';  

				while($start_level < $xml_elem['level'])
				{
					$php_stmt .= '[$level['.$start_level.']]';
					$start_level++;
				}  

				$php_stmt .= '[$xml_elem[\'tag\']] = $xml_elem[\'value\'];'; 

				eval($php_stmt);
			}
		}

		return $params;
	}

	private function SendToUSPS($xml)
	{
		$request = curl_init( $this->post_url );

		curl_setopt( $request, CURLOPT_HEADER, 0 );
		curl_setopt( $request, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $request, CURLOPT_POSTFIELDS, $xml );
		curl_setopt( $request, CURLOPT_SSL_VERIFYPEER, FALSE ); 

		$post_response = curl_exec( $request ); 

		curl_close ($request);

		return $post_response;
	}

	private function ConvertXMLToArray($xml)
	{
		$xml =  strstr($xml, "<?");

		$xml_parser = xml_parser_create();
		xml_parse_into_struct($xml_parser, $xml, $vals, $index);
		xml_parser_free($xml_parser);  

		$params = array();
		$level = array();  

		return $vals;
	}
}

/** CLASS FOR USPS XML PARSING
 *
 *
 *
 */
class uspsxmlParser
{

	var $params = array(); //Stores the object representation of XML data
	var $root = NULL;
	var $global_index = -1;
	var $fold = false;

	/* Constructor for the class
	* Takes in XML data as input( do not include the <xml> tag
	*/
	function xmlparser($input, $xmlParams=array(XML_OPTION_CASE_FOLDING => 0)) {
		$xmlp = xml_parser_create();
			foreach($xmlParams as $opt => $optVal) {
				switch( $opt ) {
				case XML_OPTION_CASE_FOLDING:
					$this->fold = $optVal;
				break;
				default:
				break;
				}
				xml_parser_set_option($xmlp, $opt, $optVal);
		}

		if(xml_parse_into_struct($xmlp, $input, $vals, $index)) {
			$this->root = $this->_foldCase($vals[0]['tag']);
			$this->params = $this->xml2ary($vals);
		}
		xml_parser_free($xmlp);
	}

	function _foldCase($arg) {
		return( $this->fold ? strtoupper($arg) : $arg);
	}

	/*
	 * Credits for the structure of this function
	 * http://mysrc.blogspot.com/2007/02/php-xml-to-array-and-backwards.html
	 *
	 * Adapted by Ropu - 05/23/2007
	 *
	*/

	function xml2ary($vals) {

		$mnary=array();
		$ary=&$mnary;
		foreach ($vals as $r) {
			$t=$r['tag'];
			if ($r['type']=='open') {
				if (isset($ary[$t]) && !empty($ary[$t])) {
					if (isset($ary[$t][0])){
						$ary[$t][]=array();
					} else {
						$ary[$t]=array($ary[$t], array());
					}
					$cv=&$ary[$t][count($ary[$t])-1];
				} else {
					$cv=&$ary[$t];
				}
				$cv=array();
				if (isset($r['attributes'])) {
					foreach ($r['attributes'] as $k=>$v) {
					$cv[$k]=$v;
					}
				}

				$cv['_p']=&$ary;
				$ary=&$cv;

				} else if ($r['type']=='complete') {
					if (isset($ary[$t]) && !empty($ary[$t])) { // same as open
						if (isset($ary[$t][0])) {
							$ary[$t][]=array();
						} else {
							$ary[$t]=array($ary[$t], array());
						}
					$cv=&$ary[$t][count($ary[$t])-1];
				} else {
					$cv=&$ary[$t];
				}
				if (isset($r['attributes'])) {
					foreach ($r['attributes'] as $k=>$v) {
						$cv[$k]=$v;
					}
				}
				$cv['VALUE'] = (isset($r['value']) ? $r['value'] : '');

				} elseif ($r['type']=='close') {
					$ary=&$ary['_p'];
				}
		}

		$this->_del_p($mnary);
		return $mnary;
	}

	// _Internal: Remove recursion in result array
	function _del_p(&$ary) {
		foreach ($ary as $k=>$v) {
		if ($k==='_p') {
			  unset($ary[$k]);
			}
			else if(is_array($ary[$k])) {
			  $this->_del_p($ary[$k]);
			}
		}
	}

	/* Returns the root of the XML data */
	function GetRoot() {
	  return $this->root;
	}

	/* Returns the array representing the XML data */
	function GetData() {
	  return $this->params;
	}
}

?>