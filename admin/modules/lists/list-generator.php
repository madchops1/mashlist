<?
require_once('../../../includes/config.php');  
set_time_limit(10);
// Link to php_write_excel_class
// http://www.bettina-attack.de/jonny/view.php/projects/php_writeexcel/demo/
require_once "scripts/php_write_excel_class/class.writeexcel_workbook.inc.php";
require_once "scripts/php_write_excel_class/class.writeexcel_worksheet.inc.php";
///////////////////////////////////////////////////////////////////////////////////

$fname = tempnam("tmp", "".$_SETTINGS['site_name']."_Email_Blast_List_".date('m-d-Y').".xls");
$workbook =& new writeexcel_workbook($fname);
$worksheet =& $workbook->addworksheet("".$_SETTINGS['site_name']."_Email_Blast_List_".date('m-d-Y')."");
$heading =& $workbook->addformat(array('align' => 'center', 'bold' => 1));
$worksheet1->set_column(0, 2, 25);
$worksheet1->write(0, 0, "Name", $heading);
$worksheet1->write(0, 1, "Email", $heading);

// Select Emails From User Account table
$sql = 	"SELECT ".
                "a.email,a.name,a.company_name ".
                "FROM ".
                "user_account a".
                "WHERE ".
                "a.send_emails='1'";
$result1 = doQuery($sql);

// Select Emails From User Contacts table
$sql = 	"SELECT ".
                "a.email,a.first_name,a.last_name ".
                "FROM ".
                "user_contact a".
                "WHERE ".
                "a.send_emails='1'";
$result2 = doQuery($sql);

// Select Emails From Email Newsletter table
$sql =	"SELECT ".
                "a.email,a.name ".
                "FROM ".
                "email_newsletter a".
                "WHERE ".
                "a.opt_in='1'";
$result3 = doQuery($sql);
?>