<?
require_once('../../../includes/config.php');
//error_reporting(E_ALL);

set_time_limit(10);
// Link to php_write_excel_class
// http://www.bettina-attack.de/jonny/view.php/projects/php_writeexcel/demo/
require_once "../../scripts/php_write_excel_class/class.writeexcel_workbook.inc.php";
require_once "../../scripts/php_write_excel_class/class.writeexcel_worksheet.inc.php";
///////////////////////////////////////////////////////////////////////////////////

function domain_exists($email,$record = 'MX')
{
	list($user,$domain) = split('@',$email);
	return checkdnsrr($domain,$record);
}

function arrayUnique($array, $preserveKeys = false)  
{  
    // Unique Array for return  
    $arrayRewrite = array();  
    // Array with the md5 hashes  
    $arrayHashes = array();  
    foreach($array as $key => $item) {  
        // Serialize the current element and create a md5 hash  
        $hash = md5(serialize($item));  
        // If the md5 didn't come up yet, add the element to  
        // to arrayRewrite, otherwise drop it  
        if (!isset($arrayHashes[$hash])) {  
            // Save the current element hash  
            $arrayHashes[$hash] = $hash;  
            // Add element to the unique Array  
            if ($preserveKeys) {  
                $arrayRewrite[$key] = $item;  
            } else {  
                $arrayRewrite[] = $item;  
            }  
        }  
    }  
    return $arrayRewrite;  
}  


$fname = tempnam("tmp", "Email_Blast_List_".date('m-d-Y').".xls");
$workbook =& new writeexcel_workbook($fname);
$worksheet1 =& $workbook->addworksheet("Email_Blast_List_".date('m-d-Y')."");
$heading =& $workbook->addformat(array('align' => 'center', 'bold' => 1));
$heading1 =& $workbook->addformat(array('align' => 'left', 'bold' => 0));
$worksheet1->set_column(0, 2, 25);
$worksheet1->write(0, 0, "Name", $heading);
$worksheet1->write(0, 1, "Email", $heading);

$contactArray = array();

// Select Emails From User Account table
$sql = 	"SELECT ".
        "a.email,a.name,a.company_name ".
        "FROM ".
        "user_account a ".
        "WHERE ".
        "a.send_emails='1'";
$result1 = doQuery($sql);
$i=0;
$num = mysql_num_rows($result1);
while($i<$num){
    $row = mysql_fetch_array($result1);
    if(VerifyEmail($row['email'])){
        array_push($contactArray,$row);
    }
    $i++;
}

// Select Emails From User Contacts table
$sql = 	"SELECT ".
		"a.email,a.first_name,a.last_name ".
		"FROM ".
		"user_contact a ".
		"WHERE ".
		"a.send_emails='1'";
$result2 = doQuery($sql);
$i=0;
$num = mysql_num_rows($result2);
while($i<$num){
    $row = mysql_fetch_array($result2);
    if(VerifyEmail($row['email'])){
        array_push($contactArray,$row);
    }
    $i++;
}

// Select Emails From Email Newsletter table
$sql =	"SELECT ".
		"a.email,a.name ".
		"FROM ".
		"email_newsletter a ".
		"WHERE ".
		"a.opt_in='1'";
$result3 = doQuery($sql);
$i=0;
$num = mysql_num_rows($result3);
while($i<$num){
    $row = mysql_fetch_array($result3);
    if(VerifyEmail($row['email'])){
        array_push($contactArray,$row);
    }
    $i++;
}

$i = 1;
sort($contactArray);
$contactArray = arrayUnique($contactArray); 
//$contactArray = array_unique($contactArray);

foreach($contactArray AS $row)
{
	$error = 0;
	
    $name = $row['name'];
    if($name=="")
	{
        $name = $row['first_name']." ".$row['last_name'];
    }
	
	// MX CHECK
	if(!domain_exists($row['email']))
	{
		$error = 1;
	}
	
	// REMOVE CERTAIN COMAINS
	list($user,$domain) = split('@',$row['email']);
	if($domain == "21cn.com" || $domain == "163.com" || $domain == "qq.com" || $domain == "sohu.com")
	{
		$error = 1;
	}
	
	if($error == 0)
	{
		$worksheet1->write($i, 0, ucwords($name), $heading1);
		$worksheet1->write($i, 1, $row['email'], $heading1);
		$i++;
    }
	
}


//die();
//exit();

$workbook->close();



header("Content-Type: application/x-msexcel; name=\"example-demo.xls\"");
header("Content-Disposition: inline; filename=\"example-demo.xls\"");
$fh=fopen($fname, "rb");
fpassthru($fh);
unlink($fname);
?>