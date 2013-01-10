<?
/***	AJAX SORT ITEMS			********************************************************/
if (isset($_POST['SORT_ITEMS'])){
	// FOR AJAX
	@require_once '../../../includes/config.php';
	
	//
	// GET SORT ARRAY
	//
	$sortarray = $_POST['sortarray'];
	$sortarray = explode(",",$sortarray);
	
		
		$i = 1;
		foreach($sortarray AS $item){
			$select = "UPDATE scroll_navigation_items SET sort_level='".$i."' WHERE item_id='".$item."'";
			echo $select."<br>";
			$result = doQuery($select);
			$i++;
		}
	
	echo "true";
	die();
	exit();
}

$ScrollNavigation = new ScrollNavigation();
report($_REQUEST['REPORT'],$_REQUEST['SUCCESS']);

/*** DELTE ITEM	********************************************************/
if (isset($_REQUEST["DELETE_ITEM"])){
	// REMOVE PRODUCT
	doQuery("UPDATE scroll_navigation_items SET active='0' WHERE item_id=".$_REQUEST["iid"]." ".$_SETTINGS['demosqland']."");
	
	$report = "Item deleted successfully.";
	$success = "1";
	header("Location: ".$_SERVER["PHP_SELF"]."?VIEW=".$_REQUEST["VIEW"]."&SUB=".$_REQUEST['SUB']."&REPORT=".$report."&SUCCESS=".$success."&page=".$_REQUEST['page']."");
	exit();
}

/*** INSERT/UPDATE ITEM	********************************************************/
if ($_POST['ADD_ITEM'] != "" || $_POST['UPDATE_ITEM'] != ""){
	$error = 0;
	if($_POST['name'] == ""){ $error = 1; ReportError("Enter an item name."); }
	
	// IF VALIDATED
	if($error == 0)	{
		$_POST = escape_smart_array($_POST); // ESCAPE SMART ARRAY
		$date = DateTimeIntoTimestamp($_POST['date']); // FORMAT DATE
		
		if($_POST['iid'] == ""){
			// INSERT ITEM
			$item_id = nextId('scroll_navigation_items');
			$select = 	"INSERT INTO scroll_navigation_items SET ".
						"name='".$_POST['name']."',".
						"image='".basename($_POST['image'])."',".
						"hover_image='".basename($_POST['hover_image'])."',".
						"content='".$_POST['content']."',".
						"link='".$_POST['link']."',".
						"status='".$_POST['status']."',".
						"type='".$_POST['type']."',".
						"category_id='".$_POST['category_id']."',".
						"product_id='".$_POST['product_id']."',".
						"date='".$date."',".
						"active='1'";
			$report = "Item created successfully.";
		} else {
			// INSERT ITEM
			$item_id = $_POST['iid'];
			$select = 	"UPDATE scroll_navigation_items SET ".
						"name='".$_POST['name']."',".
						"image='".basename($_POST['image'])."',".
						"hover_image='".basename($_POST['hover_image'])."',".
						"content='".$_POST['content']."',".
						"link='".$_POST['link']."',".
						"status='".$_POST['status']."',".
						"type='".$_POST['type']."',".
						"category_id='".$_POST['category_id']."',".
						"product_id='".$_POST['product_id']."',".
						"date='".$date."' ".
						"WHERE item_id='".$_POST['iid']."'";
			$report = "Item updated successfully.";
		}				
					
		doQuery($select);		
		header("Location: ?REPORT=".$report."&SUCCESS=1&iid=".$item_id."&VIEW=".$_REQUEST['VIEW']."");
		exit();			
	}		
}

/*** UPDATE / Add ITEM FORM		********************************************************/
if ($_REQUEST['SUB'] == 'NEWBEADROW ITEM' OR $_REQUEST['iid'] != ""){
	if($_REQUEST['iid'] != ""){
		$select = 	"SELECT * FROM scroll_navigation_items ".
					"WHERE ".
					"item_id='".$_REQUEST["iid"]."'";
		$res = doQuery($select);
		$_POST = mysql_fetch_array($res);			
		$button = "Update Item";
		$doing = "Item: ".$_POST['name']."";
	} else {	
		$button = "Add Item";
		$doing = "New Item";
	}	
	
	// START FORM
	startAdminForm();
	// START TABLE
	echo tableHeader($doing,2,'100%');
	
	// NAME
	adminFormField("*Name","name",$_POST['name'],"textbox");
	
	// IMAGE
	adminFormField("*Image","image",$_POST['image'],"image","../uploads-beadrow/");
	
	// HOVER BG IMAGE
	adminFormField("*Hover Bg Image","hover_image",$_POST['hover_image'],"image","../uploads-beadrow/");
	
	// CATEGORY
	echo "	<tr>
			<th>Type</th>
			<td>";
	
	echo 	"<select name='type'>";
	echo 	"	<option ".isSelected($_POST['type'],'She Beads')." value='She Beads'>She Beads</option>";
	echo 	"	<option ".isSelected($_POST['type'],'He Beads')." value='He Beads'>He Beads</option>";	
	echo 	"	<option ".isSelected($_POST['type'],'Intention Beads')." value='Intention Beads'>Intention Beads</option>";
	echo 	"</select>";
	
	
	echo "	</td>
			</tr>
	";
	
	// CONNECTED CATEGORY NAME
	// adminFormField("Connected Category","category_id",$_POST['category_id'],"textbox");
		
	// CATEGORY
	$identifier = "categories"; 
	echo "<TR BGCOLOR='#f2f2f2'>";
	echo "	<Th width='200' height='40' style='padding-left:20px;'>Link to Category</Th>";
	echo "	<TD>";
				hierarchyselectTable('ecommerce_product_categories','category_id','category_id','name','sort_level','ASC',0,1);
	echo "	</TD>";
	echo "</TR>";

	
	echo "<tr>";
	echo "<th>Link to Product</th>";
	echo "<td>";
	echo "<select name='product_id'>";

	echo "<option value=''> -- SELECT -- </option>";
	$selectProducts = "SELECT * FROM ecommerce_products WHERE active='1' AND status='Published' ORDER BY name";
	$resultProducts = doQuery($selectProducts);
	while($item = mysql_fetch_array($resultProducts)){
		echo "<option value='".$item['product_id']."' ".isSelected($item['product_id'],$_POST['product_id']).">".$item['name']."</option>";
	}
	
	echo "</select>";
	echo "</td>";
	echo "</tr>";	
		
	// CONTENT
	echo "<tr><th>*Content</th><td>";
	displayWysiwyg("content",$_POST['content'],"400","300");	
	echo "</td></tr>";
	
	// LINK
	adminFormField("Link","link",$_POST['link'],"textbox");
	
	// STATUS
	adminFormField("Status","status",$_POST['status'],"select",Array("Published","Pending","Draft"));
	
	// DATE
	adminFormField("Date","date",$_POST['date'],"date");
	
	// END FORM	
	$xid = "iid";
	$identifier = "ITEM";
	endAdminForm($button,$xid,$identifier);
	
}

/*** SORT ITEMS				********************************************************/
elseif($_REQUEST['SUB'] == "SORT_ITEMS"){

//$name 				= "Items";
//$table				= "scroll_navigation_items";
//$titleColumnArray	= Array("Id","Image","Name","Description","Links To");
//$valueColumnArray	= Array("item_id",);
//$xid				= "iid";
//sortableTable($name,$table,$titleColumnArray,$valueColumnArray,$xid);


	global $_REQUEST;
	global $_SESSION;
	global $_SETTINGS;
	
	//
	// SORTABLE 
	//	
	echo "<div class='textcontent1'>";
	echo "	<h1>Items</h1>";
	echo "  <a class='admin-new-button' href='index.php?VIEW=".$_REQUEST['VIEW']."&SUB=NEW_ITEM' >New Item</a>";
	echo "</div>";
	echo "<br />";
	echo "<br />";
	
	// HEADER
	//echo tableHeaderid('Items',6,"100%","list");
	//echo "<thead><TR>";	
	//echo "	<th style='width:10%;' class='head-sortable-cell'>ID</th>
	//		<th style='width:20%;' class='head-sortable-cell'>Image</th>
	//		<th style='width:20%;' class='head-sortable-cell'>Name</th>";
	//echo "	<th width='width:20%;'>Action</th>";
	//echo "</TR></thead><tbody>";
	//echo "</tbody>";
	//echo "</table>";
	
	// List
	$select = 	"SELECT * FROM scroll_navigation_items ".
				"WHERE ".
				"active='1' ".
				"ORDER BY sort_level ASC".
				"".$_SETTINGS['demosqland']."";	
	echo sortableList();	
	$res = doQuery($select);
	$num = mysql_num_rows($res);
	$i=0;
	while ($row = mysql_fetch_array($res)){
		$default = "";
		if($i % 2){ $class = "odd"; } else { $class = "even"; }
		echo "<li class=\"".$class." selector\" id=\"".$row['item_id']."\">";		
			echo "<span class='sortable-cell' style='width:20px;'>".$row['item_id']."</span>";
			echo "<span class='sortable-cell' style='width:30px;'><img src='../uploads-beadrow/".$row['image']."' width='25' ></span>";
			echo "<span class='sortable-cell' style='width:200px;'>".$row['name']."</span>";
			
			// TOP LEVEL FORM
			//echo "<span class='sortable-cell' style='width:20%; display:table-cell;'>";
			echo "<FORM style='' class='listform' METHOD='GET' ACTION='".$_SERVER[PHP_SELF]."'>";
			echo "<INPUT TYPE='HIDDEN' NAME='iid' VALUE='".$row['item_id']."'>";
			echo "<INPUT TYPE='HIDDEN' NAME='VIEW' VALUE='".$_GET["VIEW"]."'>";
			echo "<INPUT TYPE='HIDDEN' NAME='SUB' VALUE='".$_GET["SUB"]."'>";
			echo "<INPUT TYPE='SUBMIT' NAME='DELETE_ITEM' VALUE='Delete' onClick=\"return confirm('Are You Sure?');\">";
			echo "<INPUT TYPE='SUBMIT' NAME='view' VALUE='Open'>";
			echo "</FORM>";
			//echo "</span>";
		echo "</li>";	
		$i++;
	}
	echo "</ul>";
	
	echo "	<script>		
				$('#sortable').bind('sortstart', function(event, ui) {
					$(ui.item).css('background-color','#f3f8ff');
					$(ui.item).css('border','2px solid #89a8d8');
					$(ui.item).css('cursor','-moz-grabbing');
				});
		
				$('#sortable').bind('sortstop',function(event, ui) {
					
					var result = $('#sortable').sortable('toArray');
					var resultstring = result.toString();
					
					alert(resultstring);
					
					$.post('modules/scrollnavigation/scrollnavigation.php',{ sortarray: ''+resultstring+'', SORT_ITEMS: '1' },function(data){
						//alert('posted');
					});
					
					$(ui.item).css('background-color','#f5f5f5');
					$(ui.item).css('border-top','1px solid #eeeeee');
					$(ui.item).css('border-right','1px solid #eeeeee');
					$(ui.item).css('border-bottom','0px solid #eeeeee');
					$(ui.item).css('border-left','0px solid #eeeeee');
					$(ui.item).css('cursor','-moz-grab');
					
				});
	
			</script> ";
			
	/*
	echo "	$( '#sortable' ).bind( 'sortstop', function(event, ui) { ";
	echo "	  var result = $('#sortable').sortable('toArray'); ";
	echo "	  var resultstring = result.toString(); ";
		  
	echo "	  $.ajax({ ";
	echo "		  type: 'POST', ";
	echo "		  url: 'modules/scrollnavigation/scrollnavigation.php', ";
	echo "		  data: { sortarray: resultstring, SORT_ITEMS: '1' } ";
	echo "		}); ";
	echo "		//$(ui.item).css('background-color','transparent'); ";
	cheo "		$(ui.item).css('background-color','#f5f5f5'); ";
	echo "		$(ui.item).css('border-top','1px solid #eeeeee'); ";
	echo "		$(ui.item).css('border-right','1px solid #eeeeee'); ";
	echo "		$(ui.item).css('border-bottom','0px solid #eeeeee'); ";
	echo "		$(ui.item).css('border-left','0px solid #eeeeee'); ";
	echo "		$(ui.item).css('cursor','-moz-grab'); ";
	echo "	});	";
	*/
	
	echo "<div class='pagination'> &nbsp;";
	echo "</div>";

}

/*** TABLE FOR ITEMS ***/
else {
	
	$name				= "Beadrow Items";
	$table				= "scroll_navigation_items";
	$orderByString		= " ORDER BY sort_level "; // ORDER BY sort_level
	$searchColumnArray	= Array("name", "item_id","name");	
	$titleColumnArray	= Array("Id","Image","Name", "Type","Status");	
	$valueColumnArray	= Array("item_id","image::uploads-beadrow","name","type","status");
	
	$xid				= "iid";
	$ajaxURL			= "modules/ecommerce/ecommerce_ajax.php";
	basicSearchListingTable($name,$table,$orderByString,$searchColumnArray,$titleColumnArray,$valueColumnArray,$xid,$ajaxURL);
}
?>