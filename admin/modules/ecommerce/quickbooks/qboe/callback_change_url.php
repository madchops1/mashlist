<?


include '../../../../includes/config.php';

//
// RECIEVE QUICKBOOKS POST
//

$fp = fopen('logs/change_url_post_'.time().'.txt', 'w+');
fwrite($fp, print_r($_POST, true));

?>