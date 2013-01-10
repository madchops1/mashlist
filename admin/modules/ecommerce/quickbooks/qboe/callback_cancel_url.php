<?


include '../../../../includes/config.php';

//
// RECIEVE QUICKBOOKS POST
//

$fp = fopen('logs/cancel_url_post_'.time().'.txt', 'w+');
fwrite($fp, print_r($_POST, true));

?>