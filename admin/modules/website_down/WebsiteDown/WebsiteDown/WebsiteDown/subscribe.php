<?php
include_once '../../../../../../includes/config.php';


/*  RECEIVE POST   */
$Name=$_POST['Newsletter'];


/* VALIDATE HOW YOU NEED TO VALIDATE */

$EmailFrom = "$Newsletter";
$EmailTo = "youremail@domain.com";
$Subject = "Newsletter Submission";

$Newsletter = Trim(stripslashes($_POST['Newsletter'])); 

// prepare email body text
$Body = "";
$Body .= "This person has subscribed to the newsletter: ";
$Body .= $Newsletter;
$Body .= "\n";

// send email 
$success = mail($EmailTo, $Subject, $Body, "From: $EmailFrom");


/* RETURN ERROR */

$arrayError[0][0] = "#Newsletter";			// FIELDID 
$arrayError[0][1] = "Your email do not match.. whatever it need to match"; 	// TEXT ERROR	
$arrayError[0][2] = "error";			// BOX COLOR


$isValidate = true;  // RETURN TRUE FROM VALIDATING, NO ERROR DETECTED
/* RETTURN ARRAY FROM YOUR VALIDATION  */


/* THIS NEED TO BE IN YOUR FILE NO MATTERS WHAT */
if($isValidate == true){
	echo "true";
}else{
	echo '{"jsonValidateReturn":'.json_encode($arrayError).'}';		// RETURN ARRAY WITH ERROR
}
?>