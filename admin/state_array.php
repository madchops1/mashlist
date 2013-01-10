<?
require_once('../includes/config.php');  

$select = "SELECT state,state_abbr FROM state";
$result = doQuery($select);
$output = "array(";
while($row = mysql_fetch_array($result)){
	$output .= "'".$row['state']."' => '".$row['state_abbr']."',";
	
}

$output = rtrim($output,",");
$output .= ")";
echo $output;




?>