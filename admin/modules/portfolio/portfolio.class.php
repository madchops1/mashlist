<?
class Portfolio {
	
	/** CONSTRUCTOR
	 *
	 *
	 *
	 */
	function Portfolio()
	{
	
		return true;
	}
	
	/** Breadcrumbs
	 *
	 *
	 *
	 */
	function DisplayBreadcrumbs()
	{
		
		return true;
	}
	
	/** Display Portfolio
	 *
	 *
	 *
	 */
	function DisplayPortfolio($force=false)
	{
		global $_SETTINGS;
		global $_REQUEST;
		
		// FLAG / FORCE DISPLAY OF PORTFOLIO
		$flag = $_SETTINGS['portfolio_page_clean_url'];
		if($flag == $_REQUEST['page'] OR $force == true){
			
			$category1 = "";
			$page = "";
			// IF ON PORTFOLIO PAGE AND FORM1 HAS VALUE
			if($_REQUEST['FORM1'] != "")
			{
				// GET PAGE AND CAT
				if(strstr($_REQUEST['FORM1'],"|")){					
					$FORM1ARRAY = explode("|",$_REQUEST['FORM1']);
					$categoryName = $FORM1ARRAY[0];
					$categorySql = "AND (c.name='".$categoryName."') ";
					$page = $FORM1ARRAY[1];
				}
				// GET CAT IF NO PAGE
				else
				{
					$categoryName = $_REQUEST['FORM1'];
					$categorySql = "AND (c.name='".$categoryName."') ";
				}
				
				// GET CATEGORY DESCRIPTION
				$catSelect = 	"SELECT * FROM portfolio_categories c WHERE 1=1 ".$categorySql." LIMIT 1";
				$catResult = 	doQuery($catSelect);
				$catRow =	mysql_fetch_array($catResult);
				$catDescription = $catRow['description'];
			}
			
			// DISPLAY CURRENT CATEGORY
			if($categoryName)
			{
				echo 	"<h1 class='portfolio-header'>".$categoryName."</h1>";
			} 
			// OR PAGE NAME
			else 
			{
				echo 	"<h1 class='portfolio-header'>".lookupDbValue('pages', 'name', $_REQUEST['page'], 'clean_url_name')."</h1>";
			}
			
			// BREADCRUMBS
			$this->DisplayBreadcrumbs();
			
			// ITEM SELECT
			$select = 	"SELECT ".
						"a.item_id,".
						"a.thumbnail_image,".
						"a.thumbnail_size,".
						"a.description AS image_description, ".
						"a.title AS image_title, ".
						"c.description AS category_description, ".
						"c.name AS category_title ".
						"FROM portfolio_items a ".
						"LEFT JOIN portfolio_category_item_relational b on a.item_id=b.item_id ".
						"LEFT JOIN portfolio_categories c on c.category_id=b.category_id ".
						"WHERE 1=1 ".
						"AND a.active='1' ".
						"AND (a.status='Published' OR (a.status='Pending' AND DATE(a.date) <= '".date("Y-m-d")."')) ".
						$categorySql.
						"ORDER BY c.sort_level ASC,a.sort_level ASC ";
					
			$size = $_SETTINGS['portfolio_items_per_row'];
			$total_records = mysql_num_rows(doQuery($select)); 			// GET TOTAL RECORDS
			if ($page){ $page = (int) $page; }							// CHECK FOR AND GET PAGE
			$pagination = new Pagination();								// NEW PAGINATION INSTANCE
			$pagination->setLink("".$_SETTINGS['website'].$_REQUEST['page']."/".$categoryName."|%s");
			$pagination->setPage($page);
			$pagination->setSize($size);
			$pagination->setTotalRecords($total_records);
			$select .= $pagination->getLimitSql();			
			$result = doQuery($select);			
			//echo "<Br><br>".$select."<Br><br>";			
			$i=0;
			$count = mysql_num_rows($result);
			
			// CATEGORY DESCRIPTION
			if($catDescription != ""){
				echo "<p>".$catDescription."</p>";
			}
			
			while ($row = mysql_fetch_array($result)) 
			{
				
				//echo "<br><pre>";
				//var_dump($row);
				//echo "</pre><br>";
				
				if($i==0){
					echo 	"<ul class='showcase'>";
				}
				
				// IF FORCE WIDTH
				if($_SETTINGS['portfolio_thumbnail_size'] != ""){
					$force_size = "max-height:".$_SETTINGS['portfolio_thumbnail_size']."; max-width:".$_SETTINGS['portfolio_thumbnail_size']."";
				}
								
				$additionalselect = "SELECT * FROM portfolio_item_images WHERE item_id='".$row['item_id']."'";
				$additionalresult = doQuery($additionalselect);
				$additionalarray = mysql_fetch_array($additionalresult);
				
				//echo "<br>";
				//var_dump($additionalarray);
				//echo "<br>";
								
				// GET THUMBNAIL IMAGE
				$smallImage = "";
				if($row['thumbnail_image'] != ''){
					$smallImage = $row['thumbnail_image'];
				}
				
				//var_dump($additionalarray);
				// SET FIRST IMAGE
				if(is_file($_SERVER['DOCUMENT_ROOT']."/uploads/".$additionalarray['image']))
				{
					$firstImage = $_SETTINGS['website']."uploads/".$additionalarray['image'];
					if($smallImage == ""){ $smallImage = $additionalarray['image']; }
				}
				
				echo	"	<li>";
				echo	"		<a class='gallery portfolioitem' rel='".str_replace(" ","",$row['image_title'])."' href='".$firstImage."' title=\"".$row['image_title']." ".htmlentities($row['description'])."\" >";
				echo	"			<span class='portfolio-image-wrapper'>";
				echo 	"				<img style='".$force_size."' src='".$_SETTINGS['website']."uploads/".$smallImage."' alt='".$row['image_title']."' title=\"".$row['image_title']." ".htmlentities($row['description'])."\" />";
				echo 	"			</span>";
				
				if($_SETTINGS['show_portfolio_details'] == '1')
				{
					echo 	"		<div class='portfolio-description'>";
					echo	"			<h3 class='portfolio-title'>".$row['image_title']."</h3>";
					echo	"			<p class='portfolio-descrtiption-p'>".truncate($row['image_description'],100)."</p>";
					echo 	"		</div>";
				}
				
				echo	"		</a>";
				
				// ADDITIONAL IMAGES IN SLIDESHOW
				$z = 1;
				while($additional = mysql_fetch_array($additionalresult))
				{
					//echo "<Br>NOT TRUE YET<Br>";
					if(is_file($_SERVER['DOCUMENT_ROOT']."/uploads/".$additional['image'])){
						//echo "<Br>TRUE<Br>";
						//echo "FILE 1 : ".$_SERVER['DOCUMENT_ROOT']."/uploads/".$additional['image']."<br>";
						//echo "FILE 2 : ".$_SETTINGS['website']."uploads/".$additional['image']."<br><br>";						
						$additionalImage = $_SETTINGS['website']."uploads/".$additional['image'];
						echo "	<a href='".$additionalImage."' title=\"".$row['image_title']." ".htmlentities($row['image_description'])."\" class='gallery portfolioitem' rel='".str_replace(" ","",$row['image_title'])."'></a>";
					} else {
						//echo "<Br>FALSE<Br>";
					}
					$z++;
				}
				
				
				echo	"	</li>";
				if($i==($count-1)){
					echo "</ul>";
				}
				$i++;
			}
			if($count == 0){
				echo "<ul class='showcase'><li><p>There are no images in the portifolio</p></li></ul>";
			} else {
				$navigation = $pagination->create_links();
				echo $navigation; // will draw our page navigation
			}
		}		
	}
	
	/** Display Portfolio Categories
	 *
	 *
	 *
	 */
	function DisplayCategories($heading = 0){
		
		global $_REQUEST;
		global $_SETTINGS;
		
		if($heading == 1){
			echo "<h2>Categories</h2>";
		}
		
		
		echo "<ul class='category-level1'>";
		
		$sel = "SELECT * FROM portfolio_categories WHERE active='1' ORDER BY sort_level ASC";
		$res = doQuery($sel);
		$num = mysql_num_rows($res);
		$i = 0;
		
		while($i<$num){
			$class = '';
			if($_REQUEST['FORM1'] != ""){
				if(strstr($_REQUEST['FORM1'],"|")){					
					$FORM1ARRAY = explode("|",$_REQUEST['FORM1']);
					$categoryName = $FORM1ARRAY[0];
					if($categoryName == $row['name']){
						$class = 'active';
					}
				}
			}
			
			$row = mysql_fetch_array($res);
			
			echo "<li><a href='".$_SETTINGS['website']."".$_REQUEST['page']."/".$row['name']."|1' style='".$class."'>".$row['name']."</a>";
			
			$i++;
		}
		
		echo "</ul>";
		
		
	}
	
	/** Get Array of Categories
	 *
	 *
	 *
	 */
	function getCategoriesArray(){
		$sel = "SELECT * FROM portfolio_categories WHERE active='1' ORDER BY sort_level ASC";
		$res = doQuery($sel);
		$num = mysql_num_rows($res);
		$i = 0;
		$array = array();
		while($i<$num){			
			$row = mysql_fetch_array($res);
			$array[$i] = $row['category_id'];			
			$i++;
		}		
		return $array;	
	}
	
	/** Get Category Name
	 *
	 *
	 *
	 */
	function getCategoryName($catid){
		$sel = "SELECT name FROM portfolio_categories WHERE category_id='".$catid."'";
		$res = doQuery($sel);
		$row = mysql_fetch_array($res);
		$name = $row['name'];
		return $name;
	}
	
}
?>