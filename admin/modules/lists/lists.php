<?
/*************************************************************************************************************************************  
*
* 	This file is part of KSD's Wes software.
*   Copyright 2010 Karl Steltenpohl Development LLC. All Rights Reserved.
*	
*	Licensed under a Commercial Wes License (a "Wes License");
*	either the Wes Forever License (the "Forever License"),
*	or the Wes Annual Licencse (the "Annual License");
*	you may not use this file exept in compliance
*	with at least one Wes License.
*
*	You may obtain a copy of the Wes Licenses at	
*	http://www.wescms.com/license
*
*************************************************************************************************************************************/

$Lists = new Lists();	
report($_REQUEST['REPORT'],$_REQUEST['SUCCESS']);

// NEW LIST
if ($_REQUEST['SUB'] == 'NEWLIST'){


}

// LISTS DOWNLOAD
elseif($_REQUEST['xid'] == 'ebl'){

}

// LISTS
elseif(($_REQUEST['SUB'] == 'LISTS' || $_REQUEST['SUB'] == '') AND $_REQUEST['xid'] == ''){
	echo '<div class="textcontent">';
	$sel = "SELECT * FROM lists WHERE active='1'";
	$res = doQuery($sel);
	$num = mysql_num_rows($res);		
	echo '	<h1>Lists ('.$num.')</h1>';					
	echo "	<FORM METHOD=GET>";
	echo "		<INPUT TYPE=HIDDEN NAME=\"VIEW\" VALUE=\"".$_GET["VIEW"]."\">";
	echo "		<INPUT TYPE=HIDDEN NAME=\"SUB\" VALUE=\"".$_GET["SUB"]."\">";
	echo "		<INPUT TYPE=TEXT NAME=\"KEYWORDS\" VALUE=\"{$_GET["KEYWORDS"]}\">";
	echo " 		<SELECT NAME=\"COLUMN\">";
	echo "			<OPTION VALUE=\"lists.name\"".selected($_GET["COLUMN"],"lists.name").">List Name</OPTION>";			
	echo "			<OPTION VALUE=\"lists.list_id\"".selected($_GET["COLUMN"],"lists.list_id").">List Id</OPTION>";
	echo "		</SELECT>";
	echo " 		<INPUT TYPE=SUBMIT NAME=search VALUE=\"Search\"> <INPUT TYPE=BUTTON NAME=NONE VALUE=\"Reset\" ONCLICK=\"document.location = '{$_SERVER["PHP_SELF"]}?VIEW={$_GET["VIEW"]}';\">";
	echo "	</FORM>";		
	echo "</div><br /><br />";
	
	if ($_GET['KEYWORDS']!=""){
		$q = "AND {$_GET['COLUMN']} like '%{$_GET['KEYWORDS']}%'";
	}
	$page = 1;
	$size = 15;	 
	
	$select = 	"SELECT * ";	
	$select .= 	"FROM lists ";
	$select	.=	"WHERE ";
	$select .= 	"lists.active='1' ";	
	$select .= 	"$q ";
	$select .= 	"".$_SETTINGS['demosqland']."";

	$total_records = mysql_num_rows(doQuery($select)); 	
	if (isset($_GET['page'])){ $page = (int) $_GET['page'];	}	 
	$pagination = new Pagination();
	$pagination->setLink("index.php?VIEW=".$_REQUEST['VIEW']."&page=%s");
	$pagination->setPage($page);
	$pagination->setSize($size);
	$pagination->setTotalRecords($total_records);	 
	$select2 = 	$select." ".$pagination->getLimitSql()."";		
	
	echo tableHeaderid("Lists",7,"100%","list");
	echo "<thead><TR>";
	echo "<th>List Id</th>";
	echo "<th>List Name</th>";
	echo "<th>Description</th>";
	echo "<th>Created</th>";
	echo "<th>Action</th>";
	echo "</TR></thead><tbody>";
	
	$res = doQuery($select2);
	$rnum = mysql_num_rows($res);
	
	$i=0;
	while ($row = mysql_fetch_array($res)){
		if($i % 2) { $class = "odd"; } else { $class = "even"; }
		echo "<TR class=\"$class\">";
		echo "	<TD>".$row["list_id"]."</TD>";
		echo "	<TD>".$row["name"]."</TD>";	
		echo "	<TD>".$row["description"]."</TD>";		
		echo "	<TD>".FormatTimeStamp($row["created"])."</TD>";
		echo "	<TD nowrap ALIGN=\"LEFT\">";
		echo "		<FORM METHOD=GET ACTION=\"$_SERVER[PHP_SELF]\">";
		echo "			<INPUT TYPE=HIDDEN NAME=xid VALUE=\"".$row["account_id"]."\">";
		echo "			<INPUT TYPE=HIDDEN NAME=VIEW VALUE=\"".$_GET["VIEW"]."\">";			
		echo "			<INPUT TYPE=SUBMIT NAME=DELETE VALUE=\"Delete\" onClick=\"return confirm('Are You Sure?');\"> ";	
		echo "			<INPUT TYPE=SUBMIT NAME=view VALUE=\"View\"> ";		
		echo "		</FORM>";
		echo "	</TD>";
		echo "</TR>";
		$i++;
	}
	
	// Static Email Blast List
	echo "<tr>";
	echo "		<td>EBL</td>";
	echo "		<td>Email Blast List</td>";
	echo "		<td>An excel file of all (opt-in) emails in the database.</td>";
	echo "		<td> </td>";
	echo "		<td>";
	//echo "			<form method='get'>";
	//echo "				<input type='hidden' name='xid' value='ebl'>";
	//echo "				<input type='hidden' name='VIEW' value='".$_GET['VIEW']."'>";
	echo "				<input type='submit' name='' id='' value='Download List' onClick=\"document.location='modules/lists/ebl.php';\"> ";
	//echo "			</form>";
	echo "		</td>";
	echo "</tr>";
	
	echo "</tbody></TABLE>";
	$navigation = $pagination->create_links();
	echo $navigation;
}
?>