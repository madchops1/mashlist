<?
/*************************************************************************************************************************************
*
*   Copyright (c) 2011 Karl Steltenpohl Development LLC. All Rights Reserved.
*	
*	This file is part of Karl Steltenpohl Development LLC's WES (Website Enterprise Software).
*	Authored By Karl Steltenpohl
*	Commercial License
*	http://www.wescms.com/license
*
*	http://www.wescms.com
*	http://www.webksd.com/wes
* 	http://www.karlsteltenpohl.com/wes
*
*************************************************************************************************************************************/

$Mashlist = new Mashlist();	
report($_REQUEST['REPORT'],$_REQUEST['SUCCESS']);


/**
 * NEW/EDIT BACKGROUND
 */

if($_REQUEST['SUB'] == "ADDBACKGROUND" || $_REQUEST['bid'] != "")
{
	echo "BGFORM";
}

/**
 * SEARCH BACKGROUNDS
 */
elseif($_REQUEST['SUB'] == "BACKGROUNDS"){
	echo "BACKGROUNDS TABLE";
}
/**
 * SEARCH MASHLISTS
 */
else {
	
	/**************************
	* Search Mashlists
	***************************/
	?>
	<div class="textcontent">
		<h1>Mashlists</h1>
		<?
		echo "<FORM METHOD=GET>";
		echo "<INPUT TYPE=HIDDEN NAME=\"VIEW\" VALUE=\"".$_GET["VIEW"]."\">";
		echo "<INPUT TYPE=HIDDEN NAME=\"SUB\" VALUE=\"".$_GET["SUB"]."\">";
		echo "<INPUT TYPE=HIDDEN NAME=\"".session_name()."\" VALUE=\"".session_id()."\">";
		echo "<INPUT TYPE=TEXT NAME=\"KEYWORDS\" VALUE=\"{$_GET["KEYWORDS"]}\">";
		echo " <SELECT NAME=\"COLUMN\">";
			echo "<OPTION VALUE=\"name\"".selected($_GET["COLUMN"],"name").">Name</OPTION>";
		echo "</SELECT>";
		echo " <INPUT TYPE=SUBMIT NAME=search VALUE=\"Search\"> <INPUT TYPE=BUTTON NAME=NONE VALUE=\"Reset\" ONCLICK=\"document.location = '{$_SERVER["PHP_SELF"]}?VIEW={$_GET["VIEW"]}';\">";
		echo "</FORM>";
		?>
	</div>
	<br /><br />
	<?
	/**************************
	* List all posts
	***************************/
	
	if ($_GET['KEYWORDS']!="") {
		$q = "AND {$_GET['COLUMN']} like '%{$_GET['KEYWORDS']}%'";
	}
	
	$page = 1;
	// how many records per page
	$size = 20;	 
	$total_records = mysql_num_rows(doQuery("SELECT * FROM mashlists WHERE 1=1 $q ORDER BY created DESC")); 
	 
	// we get the current page from $_GET
	if (isset($_GET['page'])){
		$page = (int) $_GET['page'];
	}
	
	// create the pagination class
	$pagination = new Pagination();
	$pagination->setLink("index.php?VIEW=mashlists&page=%s");
	$pagination->setPage($page);
	$pagination->setSize($size);
	$pagination->setTotalRecords($total_records);
	 
	// now use this SQL statement to get records from your table
	$SQL = "SELECT * FROM mashlists WHERE 1=1 $q ORDER BY created DESC " . $pagination->getLimitSql();	
	
	echo tableHeaderid("Mashlists",6,"100%","list");
	
	echo "<thead>
			<TR>
				<th>Id</th>
				<th>Date</th>
				<th>Name</th>
				<th>User</th>
				<th>Status</th>
				<th>View</th>
			</TR>
		</thead>
		<tbody>";
	
	$res = doQuery($SQL);
	
	$i=0;	
	while ($row = mysql_fetch_array($res)) {
		if($i % 2) { $class = "odd"; } else { $class = ""; }
		echo "<TR class=\"$class\">";
		
		// ID
		echo "<TD>{$row["mashlist_id"]}</TD>";
		
		// DATE 
		echo "<TD>".FormatTimeStamp($row["created"])."</TD>";
		
		// NAME
		echo "<TD>".$row["name"]."</TD>";
		
		// USER
		echo "<TD>".$row["user_id"]."</TD>";
		
		// STATUS
		echo "<TD>".$row['status']."</TD>";
		
		// VIEW
		echo "<TD nowrap ALIGN=\"LEFT\">";
			echo "<FORM METHOD=GET ACTION=\"".$_SERVER[PHP_SELF]."\">";
			echo "<INPUT TYPE=HIDDEN NAME=mid VALUE=\"".$row["mashlist_id"]."\">";
			echo "<INPUT TYPE=HIDDEN NAME=VIEW VALUE=\"".$_GET["VIEW"]."\">";
			echo "<INPUT TYPE=SUBMIT NAME=DELETE_POST VALUE=\"Delete\" onClick=\"return confirm('Are You Sure?');\"> <INPUT TYPE=SUBMIT NAME=view VALUE=\"View\">";
			echo "</FORM>";
		echo "</TD>";
		echo "</TR>";
		$i++;
	}
	
	echo "</tbody></TABLE>";/*}}}*/
	$navigation = $pagination->create_links();
	echo $navigation; // will draw our page navigation
	
}


?>
