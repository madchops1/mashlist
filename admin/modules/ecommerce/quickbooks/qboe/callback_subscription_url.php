<?


include '../../../../includes/config.php';

//
// RECIEVE QUICKBOOKS POST
//

$fp = fopen('logs/subscription_url_post_'.time().'.txt', 'w+');
fwrite($fp, print_r($_POST, true));

//$fp = fopen('logs/test_'.time().'.txt', 'w+');
//fwrite($fp, print_r("1", true));

?>