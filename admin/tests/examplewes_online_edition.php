<?php

var_dump($_SERVER)
die();
exit;

include'/home/ksdwebksd/dev/2010/shebeads/includes/config.php';



//error_reporting(E_ALL | E_STRICT);

/**
 * Require the QuickBooks base classes
 */
require_once '/home/ksdwebksd/dev/2010/shebeads/admin/modules/ecommerce/quickbooks/qboe/v1-5-3/QuickBooks.php';

// Tell the framework what username to use to keep track of your requests
//	(You can just make these up at the moment, they don't do anything...)
$username = 'api';
$password = 'password';

// Tell the QuickBooks_API class you'll be connecting to QuickBooks Online Edition
$source_type = QUICKBOOKS_API_SOURCE_ONLINE_EDITION;
$api_driver_dsn = null;
$source_dsn = null;
$api_options = array();
$source_options = array(
	'certificate' => '/home/ksdwebksd/dev/2010/shebeads/admin/modules/ecommerce/quickbooks/qboe/company.pem', 
	'connection_ticket' => 'TGT-32-qmH8MgVw455zgbHs0I7MNw', 
	'application_login' => 'shebeadsdev2.dev.webksd.com', 
	'application_id' => '184369516'
	);

// Driver options
$driver_options = array();

// If you want to log requests/responses to a database, initialize the database
if ($api_driver_dsn and !QuickBooks_Utilities::initialized($api_driver_dsn))
{
	QuickBooks_Utilities::initialize($api_driver_dsn);
	QuickBooks_Utilities::createUser($api_driver_dsn, $username, $password);
}

// Create the API instance
$API = new QuickBooks_API($api_driver_dsn, $username, $source_type, $source_dsn, $api_options, $source_options, $driver_options);
$API->enableRealtime(true);

// Let's get some general information about this connection to QBOE: 
print('Our connection ticket is: ' . $API->connectionTicket() . "\n <br>");
print('Our session ticket is: ' . $API->sessionTicket() . "\n <br>");
print('Our application id is: ' . $API->applicationID() . "\n <br>");
print('Our application login is: ' . $API->applicationLogin() . "\n <br>");
print("\n");

print('Last error number: ' . $API->errorNumber() . "\n <br>");
print('Last error message: ' . $API->errorMessage() . "\n <br>");
print("\n");

$return = $API->qbxml('
		<CustomerQueryRq>
			<FullName>Kay Belt</FullName>
		</CustomerQueryRq>', '_raw_qbxml_callback');

// This function gets called when QuickBooks Online Edition sends a response back
function _raw_qbxml_callback($method, $action, $ID, &$err, $qbxml, $Iterator, $qbres)
{
	print('<Br>We got back this qbXML from QuickBooks Online Edition: <br><br>' . $qbxml);
	
	$xml = simplexml_load_string($qbxml);
	
	$listid = $xml->QBXMLMsgsRs->CustomerQueryRs->CustomerRet->ListID;
	
	print( "<Br><br>LIST ID: ".$listid."<br>" );
	
	ECHO "<br><Br>ARRAY:<br><Br>";
	
	print_r($xml);
	

	
}
echo "<Br><Br>";
print_r($return);

// For QuickBooks Online Edition, you can use real-time connections so that you 
//	get return values instead of having to write callback functions. Note that 
//	if you do this, you make your code less portable to other editions of 
//	QuickBooks that do not support real-time connections (i.e. QuickBooks 
//	desktop editions via the Web Connector)
if ($API->usingRealtime())
{
	print('Our real-time response from QuickBooks Online Edition was: ');
	print_r($return);
}


$name = 'Keith Palmer (' . mt_rand() . ')';

$Customer = new QuickBooks_Object_Customer();
$Customer->setName($name);
$Customer->setShipAddress('134 Stonemill Road', '', '', '', '', 'Storrs', 'CT', '', '06268');

// Just a demo showing how to generate the raw qbXML request
print('<Br>Here is the qbXML request we\'re about to send to QuickBooks Online Edition: ' . "\n<br><br>");
print($Customer->asQBXML('CustomerAdd'));

// Send the request to QuickBooks
$API->addCustomer($Customer, '_add_customer_callback', 15);

// This is our callback function, this will get called when the customer is added successfully
function _add_customer_callback($method, $action, $ID, &$err, $qbxml, $Customer, $qbres)
{
	print('<br>Customer #' . $ID . ' looks like this within QuickBooks Online Edition: ' . "\n<br><Br>");
	print_r($Customer);
}


exit();

?>