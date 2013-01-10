<?
//
// WES INFO
//

include 'includes/config.php';

$select = "SELECT * FROM settings ORDER BY group_id ASC";
$result = doQuery($select);
$num = mysql_num_rows($result);
$i = 0;
echo "<table style='border-collapse:collaps; border:1px solid #000;'><tr><td>Id</td><td>Setting Name</td><td>Value</td><td>Group</td><td>Active</td></tr>";
while($i<$num){
	$row = mysql_fetch_array($result);
	echo "<tr><td>".$row['setting_id']."</td><td>".$row['name']."</td><td>".$row['value']."</td><td>".$row['group_id']."</td><td>".$row['active']."</td></tr>";
	$i++;
}
echo "</table>";
?>
