<?php
include_once '../../../../../../includes/config.php';

/*  RECEIVE POST   */
$Name=$_POST['Name'];
$Email=$_POST['Email'];


/* VALIDATE HOW YOU NEED TO VALIDATE */

$EmailFrom = "$Email";
$EmailTo = "yourmail@domain.com";
$Subject = "Contact Form Submission";

$Name = Trim(stripslashes($_POST['Name'])); 
$Email = Trim(stripslashes($_POST['Email'])); 
$Message = Trim(stripslashes($_POST['Message'])); 

// prepare email body text
$Body = "";
$Body .= "Name: ";
$Body .= $Name;
$Body .= "\n";
$Body .= "Email: ";
$Body .= $Email;
$Body .= "\n";
$Body .= "Message: ";
$Body .= $Message;
$Body .= "\n";

// send email 
$success = mail($EmailTo, $Subject, $Body, "From: $EmailFrom");


/* RETURN ERROR */

$arrayError[0][0] = "#Name";			// FIELDID 
$arrayError[0][1] = "Your email do not match.. whatever it need to match"; 	// TEXT ERROR	
$arrayError[0][2] = "error";			// BOX COLOR

$arrayError[1][0] = "#Email";		// FIELD
$arrayError[1][1] = "Your email do not match.. whatever it need to match"; 	// TEXT ERROR	
$arrayError[1][2] = "error";			// BOX COLOR



$isValidate = true;  // RETURN TRUE FROM VALIDATING, NO ERROR DETECTED
/* RETTURN ARRAY FROM YOUR VALIDATION  */


/* THIS NEED TO BE IN YOUR FILE NO MATTERS WHAT */
if($isValidate == true){
	echo "true";
}else{
	echo '{"jsonValidateReturn":'.json_encode($arrayError).'}';		// RETURN ARRAY WITH ERROR
}
?>