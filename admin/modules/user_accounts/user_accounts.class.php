<?
/*************************************************************************************************************************************
*
*	88      a8P   ad88888ba   88888888ba,    
*	88    ,88'   d8"     "8b  88      `"8b   
*	88  ,88"     Y8,          88        `8b  
*	88,d88'      `Y8aaaaa,    88         88  
*	8888"88,       `"""""8b,  88         88  
*	88P   Y8b            `8b  88         8P  
*	88     "88,  Y8a     a8P  88      .a8P   
*	88       Y8b  "Y88888P"   88888888Y"'    
*
* 	This file is part of KSD's Wes software.
*   Copyright 2010 Karl Steltenpohl Development LLC. All Rights Reserved.
*	
*	Licensed under a Commercial Wes License (a "Wes License");
*	either the Wes Forever License (the "Forever License"),
*	or the Wes Annual Licencse (the "Annual License");
*	you may not use this file exept in compliance
*	with at least one Wes License.
*
*	You may obtain a copy of the Wes Licenses at	
*	http://www.wescms.com/license
*
*************************************************************************************************************************************/


class UserAccounts {

	var $auth;
	var $registration;
	var $registration_page_clean_url;
	var $forgot_password_page_clean_url;
	var $login_page_clean_url;
	var $account_page_clean_url;
	
	function UserAccounts()
	{
		/*************************
		* Class Constructor
		*************************/
		$this->auth = 0;
		$this->registration = lookupDbValue('settings', 'value', 'Registration', 'name');
		$this->registration_page_clean_url = lookupDbValue('settings', 'value', 'Registration Page Clean URL', 'name'); 
		$this->forgot_password_page_clean_url = lookupDbValue('settings', 'value', 'Forgot Password Page Clean URL', 'name'); 
		$this->login_page_clean_url = lookupDbValue('settings', 'value', 'Login Page Clean URL', 'name'); 
		$this->account_page_clean_url = lookupDbValue('settings', 'value', 'Account Page Clean URL', 'name'); 
	}
	
	function Logout()
	{
		global $_REQUEST;
		global $_SETTINGS;
		if($_REQUEST['FORM1'] == "logout")
		{
			//session_unset();
			//session_destroy();
			$_SESSION['UserAccount']['parentid'] 		= "";				
			$_SESSION['UserAccount']['userid'] 			= "";
			$_SESSION['UserAccount']['username'] 		= "";
			$_SESSION['UserAccount']['email'] 			= "";			
			$_SESSION['UserAccount']['userpermission'] 	= "";
			$_SESSION['UserAccount']['permissionlevel']	= "";
			
			header("location: ".$_SETTINGS['website'].$this->login_page_clean_url."/login-form/You Are Logged Out/0/0");
			exit();
		}
	}
	
	function LoginFormAction()
	{
		global $_REQUEST;
		global $_POST;
		global $_SETTINGS;	
		
		if($_POST['LOGIN'] != ""){	
			$error = 0;
			$number = 0;
			
			$username = $_POST['username'];
			$email = $_POST['username'];
			
			// CHECK USERNAME
			if($_POST['username'] == ""){ $error=1; $report = "Please enter your username_"; }
			
			// CHECK PASSWORD
			if($_POST['password'] == ""){ $error=1; $report = "Please enter your password_"; }
			$password = md5($_POST['password']);
			
			// CHECK USER EXISTS
			$select = 	"SELECT * FROM `user_account` ".
						"WHERE ".
						"(`username` = '$username' OR `email` = '$email') AND ".
						"(`password` = '$password') AND ". 
						"(`active` = '1') ".
						"".$_SETTINGS['demosqland']." LIMIT 1";
			$Query = doQuery($select);
			$number = mysql_num_rows($Query);
			$user = mysql_fetch_array($Query);	
			if($number == 0){ $error=1; $report = "Email address and/or password does not match_"; }
			
			// CHECK IF EMAIL VAILDTATION PROCESS COMPLETE
			if($_SETTINGS['registration_email_link_validation'] == '1'){
				if($_SESSION['registration_email_link_validation'] == '1'){
					if($user['email_verified'] != "1"){
						$error = 1;
						$report = "Your email address has not been verified_ Please follw the activation link in the email that was sent to you when you registered_ To have a new verification email and link sent to you enter your email below_";
					}
				}
			}
			
			
			if($error == 0){
				
			
				// IF THE ACCOUNT IS PENDING THEN SET IT AS THE DEFAULT ACCOUNT
				if($user['status'] == 'Pending'){
					$user['user_permission'] = $_SETTINGS['new_account_permission'];
				}
										
				// CHECK IF ACCOUNT SUB LOGIN
				if($user['parent'] != ""){					
					$_SESSION['UserAccount']['parentid'] = $user['parent'];					
				}

				// UPDATE LAST LOGIN
				doQuery("UPDATE user_account SET last_login = NOW() WHERE account_id='".$user['account_id']."'");
				
				$sel1 = "SELECT * FROM user_permission WHERE active='1' AND permission_id='".$user['user_permission']."'";
				$permission = mysql_fetch_array(doQuery($sel1));

				$_SESSION['UserAccount']['userid'] 			= $user['account_id'];
				$_SESSION['UserAccount']['username'] 		= $user['username'];
				$_SESSION['UserAccount']['email'] 			= $user['email'];
				$_SESSION['UserAccount']['userpermission'] 	= $user['user_permission'];
				$_SESSION['UserAccount']['permissionlevel']	= $permission['permission_level'];
				
				

				// SUCCESSFUL REDIRECT TO ACCOUNT PAGE
				$referer = $_POST['referer'];
				
				if($_POST['ajax'] == '1'){
					return "";
					die();
					exit();
				}
					
				if($referer == ""){
					header("Location: ".$_SETTINGS['website'].$this->account_page_clean_url."/0/You are logged in_/1/0");
					exit();
				} else {
					header("Location: ".$referer."");
					exit();
				}
			
				
			
				
			}
			else
			{
				// FAILED NO MATCH
				$success = '0';
				$report = $report;	
				$array[0] = $success;
				$array[1] = $report;
				return $array;	
			}
		}
	}
	
	function LoginForm($heading=0,$requiresFlag=true)
	{

		global $_SETTINGS;
		global $_REQUEST;
		global $_POST;
		global $_COOKIE;
		global $_SESSION;
			
		//if($_REQUEST['FORM1'] == "login-form"){
		$flag = $_SETTINGS['login_page_clean_url'];
		
		//echo "made it<br>";
		
		if($flag == $_REQUEST['page'] OR $requiresFlag == false){
		
			//echo "made it here<br>";
		
			if($_SESSION['UserAccount']['userid'] == ""){
				if($heading == 1){ ?><span class="accounttitle">Login</span><? } ?>
				<form action="" method="post" class="moduleform login-form" >
					<p>
					Enter your email address and password in the fields below. If you have a username you may enter that instead of your email address.
					</p>
					
					<p>
						<label>*Email</label>
						<input name="username" value="<?=$_REQUEST['username']?>" />
					</p>					
					<p>
						<label>*Password</label>
						<input type="password" name="password" value="" />
					</p>					
					
					<input type="hidden" name="referer" value="<?=$_SESSION['referer']?>" />
					
					<p>
						<label>&nbsp;</label>
						<input type="submit" class="submit button login-form-submit" name="LOGIN" value="Login" />
					</p>
					
					<p>
						<label>&nbsp;</label>
						<small><a href="<?=$_SETTINGS['website'].$this->forgot_password_page_clean_url?>">Forgot Password</a></small>
					</p>
					
					<?
					//
					// CHECK IF REGISTRATION IS TURNED ON
					//
					if($this->registration == "1"){ ?>
						<p>
							<label>&nbsp;</label>
							<small><a href="<?=$_SETTINGS['website'].$this->registration_page_clean_url?>">Create an account.</a></small>
						</p>
						
						<?
						if($_SETTINGS['registration_email_link_validation'] == '1'){
							?>
							<p>
								<label>&nbsp;</label>
								<small><a href="<?=$_SETTINGS['website'].$_SETTINGS['verification_email_page_clean_url']?>">Send me another verification email.</a></small>
							</p>
							<? 
						}
					} ?>					
				</form>
				<?
			} else {
			
				?>
				<p>Welcome <strong><?=$this->getUserName() ?></strong></p>
				<p>
				You are already logged in. <A href='<?=$_SETTINGS['website']?><?=$_SETTINGS['account_page_clean_url']?>'>Click here</a> to go to your account page.
				</p>
				<?			
			}
		}
	}
	
	function WelcomeBox()
	{
		global $_SESSION;
		global $_REQUEST;
		global $_SETTINGS;
		
		if($_SESSION['UserAccount']['userid'] != "")
		{
			//
			// LOGGED IN
			//
			echo "<ul class=\"welcomebox\">";
				//
				// WELCOME
				//
				echo "<li class=\"welcome-username\"><a href=\"". $_SETTINGS['website'] ."". $this->account_page_clean_url ."\">Welcome ".$this->getUserName()."</a></li>";			
				//
				// ACCOUNT
				//
				echo "<li class=\"welcome-account\"><a href=\"". $_SETTINGS['website'] ."". $this->account_page_clean_url ."\">My Account</a></li>";					
				//
				// LOGOUT
				// USES FORM1 variable to trigger logout
				//				
				echo "<li class=\"welcome-logout\"><a href=\"". $_SETTINGS['website'] . $this->login_page_clean_url . "/logout\">Logout</a></li>";				
			echo "</ul>";
		}
		else
		{
			//
			// LOGGED OUT
			//
			echo "<ul class=\"welcomebox\" />";
					
				echo "<li class=\"welcome-login\"><a href=\"". $_SETTINGS['website'] . $this->login_page_clean_url ."\">Login</a></li>";				
				
				//
				// CHECK IF REGISTRATION IS TURNED ON
				//
				if($this->registration == "1"){
					echo "<li class=\"welcome-register\"><a href=\"". $_SETTINGS['website'] . $this->registration_page_clean_url ."\">Create An Account</a></li>";
				}
				
			echo "</ul>";
		}
	}
		
	function ForgotPasswordFormAction()
	{
		global $_POST;
		global $_SETTINGS;
		global $_REQUEST;
		
		if($_REQUEST['FORGOTPASSWORD'])
		{
			if($_POST['email'] != ""){
				$email = $_POST['email'];				
				$select = "SELECT * FROM user_account WHERE active='1' AND email='".$email."' ".$_SETTINGS['demosql']." LIMIT 1";
				$result = doQuery($select);
				$row = mysql_fetch_array($result);
				
				// NEW PASSWORD
				$newpassword = makePass();
				$newpasswordhash = md5($newpassword);
				$select1 = "UPDATE `user_account` SET `password`='".$newpasswordhash."' WHERE `account_id`='".$row['account_id']."'";
				$result1 = doQuery($select1);
				
				// SEND EMAIL TO USER
				$to = $row['email'];
				$from = $_SETTINGS['email'];
				$subject = "Your New Password";
				$message = "
				<br><br>
				Your password has been reset.
				<br>
				Your new password is <strong>".$newpassword."</strong>
				<br><br>
				- Thank You
				";
				sendEmail($to,$from,$subject,$message);				
				
				
				
				header("Location: ". $_SETTINGS['website'].$this->login_page_clean_url."/0/An email with your new password has been sent to " .str_replace('.','_',$to). "/1/0");
				//header("Location: ". $_SETTINGS['website'] ."page/");
				exit();
			} else {
				$report = "Please enter an email.";
				$success = '0';
				$array[0] = $success;
				$array[1] = $report;
				return $array;
			}
		}
	}
		
	function ForgotPasswordForm($heading=0)
	{
		global $_REQUEST;		
		global $_SETTINGS;
		
		//if($_REQUEST['FORM1']=="forgot-password"){			
		$flag = $_SETTINGS['forgot_password_page_clean_url'];
		if($flag == $_REQUEST['page']){	
			if($heading == 1){ ?><span class="accounttitle">Forgot Password</span><? } ?>
			<form action="" method="post" class="moduleform forgot-password">
				<p>
					<label>*Email</label>
					<input name="email" value="<?=$_REQUEST['email']?>" />
				</p>
				
				<p>
					<label>&nbsp;</label>
					<input type="submit" class="submit button forgot-password-submit" name="FORGOTPASSWORD" value="Request Password" />
				</p>
				
				<p>
					<label>&nbsp;</label>
					<a href="<?=$_SETTINGS['website'].$this->login_page_clean_url?>">Login</a>
				</p>
				
				<?
				if($this->registration == "1"){
				?>
					<p>
						<label>&nbsp;</label>
						<a href="<?=$_SETTINGS['website'].$this->registration_page_clean_url?>">Create An Account</a>
					</p>
				<?
				} 
				?>
				
				<br clear="all" />
			</form>
			<?
		}
	}
		
	function UnsubscribeEmailFormAction()
	{
		global $_POST;
		global $_SETTINGS;
		global $_REQUEST;
		
		if($_REQUEST['UNSUBSCRIBEEMAIL'])
		{
			if($_POST['email'] != ""){
				$email = $_POST['email'];				
				$select = "SELECT * FROM user_account WHERE email='".$email."' and active='1' ".$_SETTINGS['demosql']." LIMIT 1";
				$result = doQuery($select);
				$row = mysql_fetch_array($result);
				
				// UPDATe				
				$select1 = "UPDATE `user_account` SET `send_emails`='0' WHERE `account_id`='".$row['account_id']."'";
				$result1 = doQuery($select1);		
				
				$success = 1;
				$report = "Your Email Has Been Removed From Our Mailing Lists";
				header("Location: ". $_SETTINGS['website'].$_SETTINGS['unsubscribe_email_page_clean_url']."/unsubscribe-email/".$report."/".$success."/0");
				exit();
			} else {
				$report = "Please enter an email.";
				$success = '0';
				$array[0] = $success;
				$array[1] = $report;
				return $array;
			}
		}
	}
	
	function UnsubscribeEmailForm($heading=0)
	{
		global $_REQUEST;		
		global $_SETTINGS;
		
		//if($_REQUEST['FORM1']=="unsubscribe-email"){			
		$flag = $_SETTINGS['unsubscribe_email_page_clean_url'];
		if($flag == $_REQUEST['page']){	
			if($heading == 1){ ?><span class="accounttitle">Unsubscribe From <?=$_SETTINGS['site_name'] ?>Emails</span><? } ?>
			<form action="" method="post" class="moduleform forgot-password">
				<p>
					<label>*Email</label>
					<input name="email" value="<?=$_REQUEST['email']?>" />
				</p>
				<p>
					<label>&nbsp;</label>
					<input style="submit button unsubscribe-email-submit" type="submit" name="UNSUBSCRIBEEMAIL" value="Unsubscribe" />
				</p>
				<br clear="all" />
			</form>
			<?
		}
	}
	
	function SendVerificationEmail($emailverificationstring,$email)
	{
		global $_POST;
		global $_SETTINGS;
		$error = 0;
		if(VerifyEmail($email) != 1){ $error = 1; }
		
		if($error == 0){
						
				$email_id = 3; // SET THE VERIFICATION EMAIL
				
				$ammended_message_html 	= 	"<p><a href=\"".$_SETTINGS['website'].$_SETTINGS['verification_email_page_clean_url']."/".$emailverificationstring."\">".
											"Click this link to verify your new account and login<br>".
											"".$_SETTINGS['website'].$_SETTINGS['verification_email_page_clean_url']."/".$emailverificationstring."".
											"</a></p>";
						
				$message_html = lookupDbValue('automated_email_contents','html',$email_id,'email_id');
				$message_html = $message_html.$ammended_message_html;
				$template = lookupDbValue('automated_email_contents','template',$email_id,'email_id');
				$subject = lookupDbValue('automated_email_contents','subject',$email_id,'email_id');
				$from = lookupDbValue('automated_email_contents','from',$email_id,'email_id');
				
				$email_html = file_get_contents("".$_SETTINGS['website']."themes/".$_SETTINGS['theme']."".$template."");
				
				// TESTING
				//echo "<br><br>TEMPLATE: ".$_SETTING['ecommerce_email_template'];
				//die($email_html);
				//exit();
				
				$email_html = str_replace("|date|","".date("m/d/Y")."",$email_html);
				$email_html = str_replace("|message_html|","".$message_html."",$email_html);
				
				@sendEmail($email,$from,$subject,$email_html);		
						
			/*			
			// SEND EMAIL TO USER
			$mail = new Rmail();			
			$mail->setFrom(''.$_SETTINGS['sitename'].' <'.$_SETTINGS['automated_reply_email'].'>');
			$mail->setSubject(''.$_SETTINGS['sitename'].' New Account Verification Email');
			$mail->setPriority('high');
			//$mail->setText('Sample text');				
			
			
			$message = file_get_contents($_SERVER['DOCUMENT_ROOT'].$_SETTINGS['website_path'].'themes/'.$_SETTINGS['theme'].'emailtemplate-registration.html');
			
			
			$registration_body 	= 	"<p><a href=\"".$_SETTINGS['website'].$_SETTINGS['verification_email_page_clean_url']."/".$emailverificationstring."\">".
									"Click this link to verify your new account and login<br>".
									"".$_SETTINGS['website'].$_SETTINGS['verification_email_page_clean_url']."/".$emailverificationstring."".
									"</a></p>";
									
			$body = $registration_body."<p>".$_SETTINGS['automated_registration_email_message']."</p>";
			
			// MESSAGE REPLACES
			$message = str_replace("|WEBSITE|",$_SETTINGS['website'],$message);
			$message = str_replace("|COMPLETE_IMAGE_PATH|",$_SETTINGS['website']."themes/".$_SETTINGS['theme']."email_sources/",$message);
			$message = str_replace("|BODY|",$body,$message);
			$message = str_replace("|SITENAME|",$_SETTINGS['sitename'],$message);
			$message = str_replace("|HEADING|","Registration",$message);
			$message = str_replace("|UNSUBSCRIBE_PAGE_CLEAN_URL|",$_SETTINGS['unsubscribe_email_page_clean_url'],$message);
			$message = str_replace("|EMAIL_TO|",$email,$message);
			$message = str_replace("|UNSUBSCRIBE_PAGE_CLEAN_URL|",$_SETTINGS['unsubscribe_email_page_clean_url'],$message);
			$message = str_replace("|UNSUBSCRIBE_PAGE_CLEAN_URL|",$_SETTINGS['unsubscribe_email_page_clean_url'],$message);				
			$message = str_replace("|SITELINK|",$_SETTINGS['website'],$message);
			$mail->setHTML($message);
			//$mail->setReceipt('test@test.com');
			//$mail->addEmbeddedImage(new fileEmbeddedImage('background.gif'));
			//$mail->addAttachment(new fileAttachment('example.zip'));
			$address = ''.$email.'';
			$mail->send(array($address));
			*/
			
			return "Email Sent";
		} else {
			return "Email Error";
		}
	}
	
	function VerifyEmailAction()
	{
		global $_SETTINGS;
		global $_REQUEST;
		
		// IF VERIFICATION PAGE
		if($_REQUEST['page'] == $_SETTINGS['verification_email_page_clean_url']){
			$verificationstring = $_REQUEST['FORM1'];
			$sel = "SELECT * FROM user_account WHERE active='1' AND email_verified='0' AND email_verification_string='".$verificationstring."'";
			$res = doQuery($sel);
			$num = mysql_num_rows($res);
			$row = mysql_fetch_array($res);
			
			// IF FORM1 IS NOT EMPTY
			if($_REQUEST['FORM1'] == $row['email_verification_string']){
				
				
				// IF THE FORM1 VALUE DOES MATCH AN ENCRYPTED EMAIL IN THE SYSTEM THAT IS UNVERIFIED
				if($row['account_id'] != ""){
					
					// UPDATE NEW ACCOUNT
					$sel = "UPDATE user_account SET email_verified='1' WHERE active='1' AND account_id='".$row['account_id']."'";
					$res = doQuery($sel);
					
					$sel1 = "SELECT * FROM user_permission WHERE active='1' AND permission_id='".$user['user_permission']."'";
					$permission = mysql_fetch_array(doQuery($sel1));
					
					// LOGIN THE USER
					$_SESSION['UserAccount']['userid'] 			= $row['account_id'];
					$_SESSION['UserAccount']['userpermission'] 	= $row['user_permission'];
					$_SESSION['UserAccount']['permissionlevel']	= $permission['permission_level'];
					$_SESSION['UserAccount']['username'] 		= $row['username'];
					$_SESSION['UserAccount']['email'] 			= $row['email'];

						
					// REDIRECT
					$success = '1';
					$report = 'Verification Successful, Welcome '.$row['username'].'';
					header("Location: ".$_SETTINGS['website'].$_SETTINGS['login_page_success_clean_url']."/0/".$report."/".$success."/0");
					exit;
				} else {
					// REDIRECT
					$success = '0';
					$report = 'This verification link is invalid';
					header("Location: ".$_SETTINGS['website'].$_SETTINGS['verification_email_page_clean_url']."/0/".$report."/".$success."/0");
					exit;
				}
			}
		}
	}
	
	function SendVerificationEmailFormAction()
	{
		global $_POST;
		global $_SETTINGS;
		global $_REQUEST;
		
		if($_REQUEST['SENDVERIFICATIONEMAIL'])
		{
			if($_POST['email'] != ""){
				$email = $_POST['email'];				
				$select = "SELECT * FROM user_account WHERE email='".$email."' AND active='1' AND email_verified='0' ".$_SETTINGS['demosql']." LIMIT 1";
				$result = doQuery($select);
				$row = mysql_fetch_array($result);
				
				// STRING
				$emailverificationstring = md5($email);
				
				$select1 = "UPDATE `user_account` SET `email_verification_string`='".$emailverificationstring."' WHERE `account_id`='".$row['account_id']."'";
				$result1 = doQuery($select1);
				
				// SEND EMAIL
				$this->SendVerificationEmail($emailverificationstring,$email);
				
				$success = 1;
				$report = "A new verification link has been sent";
				header("Location: ". $_SETTINGS['website'].$this->login_page_clean_url."/login-form/".$report."/".$success."/0");
				//header("Location: ". $_SETTINGS['website']."page/");
				exit();
			} else {
				$report = "Please enter an email.";
				$success = '0';
				$array[0] = $success;
				$array[1] = $report;
				return $array;
			}
		}
	}
	
	function SendVerificationEmailForm($heading=0)
	{
		global $_REQUEST;
		global $_SETTINGS;
		
		//if($_REQUEST['FORM1']=="verification-email"){
		$flag = $_SETTINGS['verification_email_page_clean_url'];
		if($flag == $_REQUEST['page']){
			if($heading == 1){ ?><span class="accounttitle">Verification Email</span><? } ?>
			<form action="" method="post" class="moduleform forgot-password">
				<p>
					<label>*Email</label>
					<input name="email" value="<?=$_REQUEST['email']?>" />
				</p>
				
				<p>
					<label>&nbsp;</label>
					<input type="submit" class="submit button send-email-submit" name="SENDVERIFICATIONEMAIL" value="Send Email" />
				</p>
				
				<p>
					<label>&nbsp;</label>
					<a href="<?=$_SETTINGS['website'].$this->login_page_clean_url?>">Login</a>
				</p>
				<? if($this->registration == "1"){ ?>
					<p>
						<label>&nbsp;</label>
						<a href="<?=$_SETTINGS['website'].$this->registration_page_clean_url?>">Create An Account</a>
					</p>
				<? } ?>
				
				<br clear="all" />
			</form>
			<?
		}
	}
		
	function sendSuccessfulRegistrationEmail($to = "",$account_type)
	{
		global $_SETTINGS;
		global $_SESSION;
		
		// GET AUTOMATED EMAIL ID FROM USER PERMISSION / ACCOUNT TYPE
		$email_id = 2; // SET LOWEST LEVEL DEFAULT REGISTRATION EMAIL ID
		$email_id = lookupDbValue('user_permission','registration_email_id',$account_type,'permission_id');
		
		$message_html = lookupDbValue('automated_email_contents','html',$email_id,'email_id');
		$subject = lookupDbValue('automated_email_contents','subject',$email_id,'email_id');
		$from = lookupDbValue('automated_email_contents','from',$email_id,'email_id');
		
		//$email_template = $_SETTINGS['ecommerce_email_template'];
		$email_html = file_get_contents("".$_SETTINGS['website']."themes/".$_SETTINGS['theme']."".$_SETTINGS['customer_email_template']."");
		
		// TESTING
		//echo "<br><br>TEMPLATE: ".$_SETTING['ecommerce_email_template'];
		//die($email_html);
		//exit();
		
		$email_html = str_replace("|date|","".date("m/d/Y")."",$email_html);
		$email_html = str_replace("|message_html|","".$message_html."",$email_html);
		
		@sendEmail($to,$from,$subject,$email_html);
		return true;
	}

	/**
	 *
	 * INHOUSE SYSTEM EMAIL TO OWNER WHEN A CUSTOMER REGISTERS
	 *
	 */	 
	function sendInhouseRegistrationEmail($to = "",$account_type,$row)
	{
		global $_SETTINGS;
		global $_SESSION;
		
		// GET AUTOMATED EMAIL ID FROM USER PERMISSION / ACCOUNT TYPE
		//$email_id = 2; // SET LOWEST LEVEL DEFAULT REGISTRATION EMAIL ID
		//$email_id = lookupDbValue('user_permission','registration_email_id',$account_type,'permission_id');
		
		$message_html = "
						<Br>
						New Registration and/or Application from ".$_SETTINGS['site_name']."
						<Br><Br>
						Date: ".date("m/d/Y")."<Br>
						Name: ".$row['name']."<br>
						Account Type: ".lookupDbValue('user_permission','name',$account_type,'permission_id')."<Br>
						<br><Br>
						<a href='".$_SETTINGS['website']."admin/index.php?xid=".$row['account_id']."&VIEW=useraccounts&view=View'>Click here to login, and review this customer.</a>
						<br>
						";
						
		$subject = "New Registration and/or Application from ".$_SETTINGS['site_name']."";
		$from = 'no-reply@'.$_SETTINGS['website_domain'].'';
		
		@sendEmail($to,$from,$subject,$message_html);
		return true;
	}	
	
		
	function NewsletterSignupFormAction(){
		global $_POST;
		global $_SETTINGS;
		global $_REQUEST;
		
		if($_POST['NEWSLETTER_SIGNUP'])	{
			$error = 0;
			
			// VALIDATEION
			if($_POST['newsletter_email'] =="" ){$error = 1; $report = "Please enter your email_"; }
			
			if($error == 0){
				$_POST = escape_smart_array($_POST);
				// INSERT 
				$insert = 	"INSERT INTO email_newsletter SET ".
							"name='".$_POST['newsletter_name']."',".
							"email='".$_POST['newsletter_email']."',".
							"opt_in='1',".
							"active='1',".
							"dob='".DateTimeIntoTimestamp($_POST['newsletter_dob'])."'";
				doQuery($insert);
			
				$success = 1;
				$report = "Thanks for signing up to receive special offers, news, and more_";
				if($_SETTINGS['newsletter_confirmation_page_clean_url'] != ""){ 
					$_POST['page'] = $_SETTINGS['newsletter_confirmation_page_clean_url'];
				}
				header("Location: ". $_SETTINGS['website'].$_POST['page']."/0/".$report."/".$success."/0");
				exit();
				
			} else {
				$success = '0';
				$array[0] = $success;
				$array[1] = $report;
				return $array;			
			}			
		}
	}
		
	function RegistrationFormAction()
	{
		global $_REQUEST;
		global $_POST;
		global $_SETTINGS;
		global $_SESSION;
		
		if($_POST['REGISTER'])
		{
			$error = 0;
			$account_type = $_SETTINGS['new_account_permission'];
			if($_POST['account_type'] != ""){ $account_type = $_POST['account_type']; }
			$registrationSetting = lookupDbValue('user_permission','registration_setting',$account_type,'permission_id');
			if($registrationSetting == "Registration Closed"){ $error = 1; $report = "Registration Is Closed_"; }
			
			if($_SESSION['UserAccount']['userid'] != ""){
				if($_SESSION['UserAccount']['userid'] == $_POST['account_type']){ $error = 1; $report = "You Currently Have This Type Of Account_"; }
			}

			if($_POST["username"]==""){ $error = 1; $report = "Please Enter A Username_"; }
			if($_POST["email"]==""){ $error = 1; $report = "Please Enter An Email_"; }
			if(VerifyEmail($_POST['email']) != 1){ $error = 1; $report = "The Email Address Is Not Valid_"; }
				
			if($this->CheckEmail() == false){ $error = 1; $report = "The Email Address Is Already In Use_"; }
			if($this->CheckUsername() == false){ $error = 1; $report = "The Username Is Already In Use_"; }
			
			if($_SESSION['UserAccount']['userid'] == ""){
				if($_POST["password1"]==""){ $error = 1; $report = "Please Enter A Password_"; }		
				if($_POST["password1"]!=$_POST["password2"]){ $error = 1; $report = "Your Passwords Do Not Match_"; }
			}
			
			if($_POST["terms"] != "1"){ $error = 1; $report = "Please Agree To The Website Terms_"; }
			
			if($error == 0){
				$password = md5($_POST["password1"]);
				$_POST = escape_smart_array($_POST);
				$nextid = nextId("user_account");
				$emailverificationstring = md5($_POST['email']);
				
				// CHECK IF EMAIL VERIFICATION IS REQUIRED
				if($_SETTINGS['registration_email_link_validation'] == '1'){ $email_verified = '0'; } else { $email_verified = '1'; }
				
				// GET THE ACCOUNT TYPE INFO
				$accountName = lookupDbValue('user_permission', 'name',$account_type,'permission_id');
				if($registrationSetting == "By Application Only"){
					$status = 'Pending';
					$report2 = " Thank you for applying for a ".$accountName." Account we will respond to your application asap_";
				} else {
					$status = 'Active';
				}
				
				// NORMAL ACCOUNT REGISTRATION
				// IF REGISTERING A NEW ACCOUNT
				//
				// [email_vefified] is an email verification method prior to the customers first login
				// [status] is a setting for user permissions/acount types and their access
				//			
				if($_SESSION['UserAccount']['userid'] == ''){
					//INSERT A NEW ACCOUNT
					$account_id = nextId('user_account');
					$select = 	"INSERT INTO user_account SET ".
								"name='".$_POST['name']."',".
								"username='{$_POST["username"]}',".
								"email='{$_POST["email"]}',".
								"password='{$password}',".
								"active=1,".
								"status='".$status."',".
								"user_permission='".$account_type."',".
								"email_verified='".$email_verified."',".
								"email_verification_string='".$emailverificationstring."',".
								"created=NULL".
								"".$_SETTINGS['demosql']."";	
					$result = doQuery($select);
					
					if($_SETTINGS['registration_email_link_validation'] == '1'){
						// SEND VERIFICATION EMAIL
						$this->SendVerificationEmail($emailverificationstring,$_POST['email']);
						$report = 'Registration successfull you will recieve an email with a link you must click to verify your account_'.$report2;
					} else {
						// SEND WELCOME EMAIL UPON REGISTRATION
						$to	= $_POST['email'];
						@$this->sendSuccessfulRegistrationEmail($to,$account_type);
						
						//$to = $_SETTINGS['registration_receives_email'];
						// SEND IN-HOUSE EMAIL TO BUSINESS OWNER
						//@$this->sendInhouseRegistrationEmail($to,$account_type,$_POST);
						
						// FAST LOGIN
						$_SESSION['UserAccount']['userid'] 			= $account_id;
						$_SESSION['UserAccount']['userpermission'] 	= $account_type;
						$_SESSION['UserAccount']['permissionlevel']	= lookupDbValue('user_permission', 'permission_level', $_SETTINGS['new_account_permission'], 'permission_id');
						$_SESSION['UserAccount']['username'] 		= $_POST['username'];
						$_SESSION['UserAccount']['email'] 			= $_POST['email'];
						$report = 'Registration successfull, you have been logged in_'.$report2.'';
					}
					$success = '1';				
					
					if($_POST['ajax'] == '1'){
						return "success - ".$account_type.", ".$accountName."";
						
						die("");
						exit();
					} else {
						header("Location: ".$_SETTINGS['website'].$_SETTINGS['login_page_clean_url']."/login-form/".$report."/".$success."/0");
						exit();
					}			
				} else {
					// UPGRADE ACCOUNT
					$account_id = $_SESSION['UserAccount']['userid'];
					$select = 	"UPDATE user_account SET ".
								"user_permission='".$account_type."',".
								"status='Pending',".
								"new='1'";
					doQuery($select);
					
					
					
				}
				
				// SEND SUCCESSFULL UPGRADE APPLICATION EMAIL TO CUSTOMER
				@$this->sendSuccessfulRegistrationEmail($to,$account_type);
				// SEND IN-HOUSE EMAIL TO BUSINESS OWNER
				@$this->sendInhouseRegistrationEmail($to,$account_type,$_POST);
				
				
				
				// ELSE IF UPGRADING A CURRENT ACCOUNT
				//elseif($_POST['account_type'] == '' AND $_SESSION['UserAccount']['userid'] != '') {
				//UPDATE CURRENT ACCOUNT
				//	$upgrade = 	"UPDATE user_account SET ".
				//				.
				//}
				
				// IF ECOMMERCE INSTALLED
				//if(checkActiveModule('0000012') AND $_POST['account_type'] != ""){
				// ADD TO SHOPPING CART					
				// REDIRECT TO SHOPPING CART					
				//}
				
			} else {			
				$success = '0';
				$array[0] = $success;
				$array[1] = $report;
				return $array;			
			}
		}
	}
	
	function RegistrationForm($heading=0,$implicit=0,$account_type="")
	{
		global $_REQUEST;
		global $_SETTINGS;
		global $_SESSION;
				
		
		// GET THE LOWEST ACCOUNT PERMISSION LEVEL
		if($account_type == ""){ $account_type = $_SETTINGS['new_account_permission']; }
		
		$flag = $_SETTINGS['registration_page_clean_url'];
		if($flag == $_REQUEST['page']){		
			$display = 1;
		}
		
		// CHECK IF IMPLICIT
		if($implicit == 1){
			$display = 1;
		}
		
		if($display == 1){
			
			// IF THE USER IS LOGGED IN AND THEIR ACCOUNT IS THE SAME THEN REDIRECT TO THE ACCOUNT PAGE
			if($_SESSION['UserAccount']['userid'] != "" AND $_SESSION['UserAccount']['permissionlevel'] == $account_type){
				$report = "You have this account type and you are logged in.";
				$success = "1";
				header("Location: ".$_SETTINGS['website'].$_SETTINGS['account_page_clean_url']."/0/".$report."/".$success."/0");
				exit;
			}
			
			$doing = "Register";
			
			// IF THE USER IS TRYING TO UPGRADE
			if($_SESSION['UserAccount']['userid'] != "" AND $_SESSION['UserAccount']['permissionlevel'] < $account_type){
				$doing = "Upgrade Account";
			}
			
			// iF THE USER IS TRYING TO DOWNGRADE
			if($_SESSION['UserAccount']['userid'] != "" AND $_SESSION['UserAccount']['permissionlevel'] > $account_type){
				$doing = "Downgrade Account";
			}

			// CHECK ACCOUNT TYPE TYPE
			$registrationSetting = lookupDbValue('user_permission','registration_setting',$account_type,'permission_id');
			$accountName = lookupDbValue('user_permission','name',$account_type,'permission_id');
			$display = 1;
			?>
			
			<form action="" method="post" class="moduleform registration-form">
				
				<h2><?=$doing?></h2>
			
				<?
				if($registrationSetting == 'By Application Only' AND $doing != "Downgrade Account"){ 
					?>
					<p><?=$accountName?> accounts are approved by application only. Please fill out the form below. We will review and respond to your application promptly.</p>
					<?		
				}
				
				if($doing == "Downgrade Account"){
					?>
					<p>
					Click the button below to downgrade to a <?=$accountName?> account.
					</p>
					<?			
					$display = 0;
				}
				
				if($registrationSetting == 'Registration Closed'){ 
					?>
					<p>Our apoligies, <?=$accountName?> account application and registration is currently closed. Thanks for understanding.</p>
					<?			
					$display = 0;
				}
				
				if($display == 1){
					?>
					<p>
						<label>Name/Company</label>
						<input name="name" size="30" value="<?=$_REQUEST['name']?>" />
					</p>
					<p>
						<label>*Email</label>
						<input name="email" size="40" value="<?=$_REQUEST['email']?>" />
					</p>
					
					<?
					if($doing == "Register" ){
						?>
						<p>
							<label>*Username</label>
							<input name="username" size="14" value="<?=$_REQUEST['username']?>" />
						</p>
						<p>
							<label>*Password</label>
							<input type="password" size="14" name="password1" value="" />
						</p>
						<p>
							<label>*Re-Type Password</label>
							<input type="password" size="14" name="password2" value="" />
						</p>
						<?
					}
					?>
					<p>
						<label>*City</label>
						<input type="text" size="14" name='city' value="<?=$_POST['phone']?>" >
					</p>
					<p>
						<label>*State</label>
						<?
						selectTable("state","billing_state","state_id","state","state","ASC");
						?>
					</p>
					<p>
						<label>*Phone</label>
						<input type="text" size="14" name='phone' value="<?=$_POST['phone']?>" >
					</p>
					<p>
						<label>How did you hear about us?</label>
						<select name='heard'>
							<option value=""></option>
							<option value="Word of Mouth">Word of Mouth</option>
							<option value="Search Engine">Search Engine</option>
							<option value="Received a Gift">Received a Gift</option>
							<option value="Repeat Customer">Repeat Customer</option>
							<option value="A Party">A Party</option>
							<option value="The Store">The Store</option>
							<option value="A booth">A booth</option>
							<option value="Email">Email</option>
						</select>
					</p>
					<!--
					<p>
						<label>Birthday</label>
						<input type='text' size='14' value=""> <small>ex. 05/10/1985</small>
					</p>
					-->
					<p>
						<label>&nbsp;</label>
						<input class="checkbox" type="checkbox" name="emails" value="1" <? if($_POST['emails'] == 1){ ?> CHECKED <? } ?> /> <small>Do you want to receive information via email from <?=$_SETTINGS['siteName'] ?>?</small>
					</p>
					<p>
						<label>&nbsp;</label>
						<input class="checkbox" type="checkbox" name="terms" value="1" <? if($_POST['terms'] == 1){ ?> CHECKED <? } ?>  /> <small>Do you agree to the <a target='_blank' href='<?=$_SETTINGS['website'].$_SETTINGS['terms_of_use_clean_url']?>'><?=$_SETTINGS['siteName'] ?> terms of use</a>?</small>
					</p>								
				<?
				}
				
				if($doing == "Downgrade Acount"){
					?>
					<p>
						<label>&nbsp;</label>
						<input type='checkbox' name='sure' value='1'> I am sure that I want to downgrade my account.
					</p>
					<?
				}
				?>
				
				<p>
					<label>&nbsp;</label>
					<input type='hidden' name='account_type' value='<?=$account_type?>' >
					<input type='hidden' name='url-referrer' value='<?=$HTTP_REFERER ?>' > 
					<input class="submit button register-submit" type="submit" name="REGISTER" value="<?=$doing?>" />
				</p>
		
				<p class='reg-questions'>						
					<a href="<?=$_SETTINGS['website'].$this->forgot_password_page_clean_url?>">Do you already have a customer account and you can't remember your password? Click here.</a>						
				</p>
				
				<br clear="all" />
			</form>
			<?
		}
	}
	
	function DeleteContactAction()
	{
		global $_SETTINGS;
		global $_REQUEST;
		
		// IF VERIFICATION PAGE
		if($_REQUEST['page'] == $_SETTINGS['account_page_clean_url']){			
			// IF FORM1 IS NOT EMPTY
			if($_REQUEST['FORM1'] != ""){				
				$form1array = explode("_",$_REQUEST['FORM1']);			
				// IF 
				if($form1array[0] == 'delete-contact'){				
					if($form1array[1] != ""){						
						$select = "UPDATE user_contact SET active='0' WHERE contact_id='".$form1array[1]."' AND account_id='".$_SESSION['UserAccount']['userid']."'";
						doQuery($select);
						
						$select = "DELETE FROM user_contact_relational WHERE contact_id='".$form1array[1]."'";
						doQuery($select);
						
						// REDIRECT
						$success = '1';
						$report = 'Contact Delete Successfully';
						header("Location: ".$_SETTINGS['website'].$_SETTINGS['account_page_clean_url']."/account-form/".$report."/".$success."/0");
						exit;
					}				
				}				
			}
		}
	}
	
	function AccountFormAction()
	{
		global $_REQUEST;
		global $_POST;
		global $_SETTINGS;
		
		// IF POST UPDATEACCOUNT
		if($_POST['UPDATEACCOUNT']){
		
			$error = 0;			
			//if($_POST["username"]==""){ $error = 1; $report = "Please Enter A Username"; }
			if($_POST["email"]==""){ $error = 1; $report = "Please Enter An Email"; }	
			if(VerifyEmail($_POST['email']) != 1){ $error = 1; $report = "The Email Address Is Not Valid"; }
			if($_POST['email'] != $_POST['emailhidden']){
				if($this->CheckEmail() == false){ $error = 1; $report = "The Email Address Is Already In Use"; }
			}
			
			if($_POST['username'] != ""){
				if($_POST['username'] != $_POST['usernamehidden']){
					if($this->CheckUsername() == false){ $error = 1; $report = "The Username Is Already In Use"; }
				}
			}
			if($_POST["password1"]!=$_POST["password2"]){ $error = 1; $report = "Your Passwords Do Not Match"; }
			
			// IF ERROR 0
			if($error == 0){
			
				if($_POST['password1'] != ""){
					$pass = md5($_POST['password1']);
					$password = ",password='".$pass."'";
				}
				
				$_POST = escape_smart_array($_POST);
				
				// UPDATE ACCOUNT
				$select1 = 	"UPDATE user_account SET ".
							"name='".$_POST['name']."',".
							"email='".$_POST['email']."',".
							"username='".$_POST['username']."'".
							"$password".
							"".$_SETTINGS['demosql']."".
							"WHERE account_id='".$_SESSION['UserAccount']['userid']."'";
				$result1 = doQuery($select1);
				
				// UPDATDING CONTACT // MUST HAPPEN BEFORE INSERT OF NEW CONTACT
				$select2= "SELECT * FROM user_contact WHERE active='1' AND account_id='".$_SESSION['UserAccount']['userid']."'";
				$result2 = doQuery($select2);
				$i = 0;
				$num = mysql_num_rows($result2);
				while($i<$num){
					$row  = mysql_fetch_array($result2);
					
					$sel1 = "UPDATE user_contact SET ".
							"first_name='".$_POST['cfirst_name'.$row['contact_id']]."',".
							"last_name='".$_POST['clast_name'.$row['contact_id']]."',".
							"email='".$_POST['cemail'.$row['contact_id']]."',".
							"address1='".$_POST['caddress1'.$row['contact_id']]."',".
							"address2='".$_POST['caddress2'.$row['contact_id']]."',".
							"city='".$_POST['ccity'.$row['contact_id']]."',".
							"state='".$_POST['cstate'.$row['contact_id']]."',".
							"zip='".$_POST['czip'.$row['contact_id']]."',".
							"phone='".$_POST['cphone'.$row['contact_id']]."',".
							"country='".$_POST['ccountry'.$row['contact_id']]."',".
							"active='1' ".
							"WHERE contact_id='".$row['contact_id']."'";
					$res1 = doQuery($sel1);
					
					
					// LOOP THROUGH CONTACT TYPES FOR RELATIONAL ENTRY
					$sel3 = "SELECT * FROM user_contact_type WHERE active='1'";
					$res3 = doQuery($sel3);
					$i3 = 0;					
					$num3 = mysql_num_rows($res3);
					while($i3<$num3){
						
						$row3 = mysql_fetch_array($res3);
						// IF CONTACT TYPE SELECTED
						if($_POST['contact_type_'.$row3['type_id'].'_'.$row['contact_id']] == '1'){
					
							// INSERT  NECESSARY RELAIONAL ENTRY
							$select4 = 	"INSERT INTO user_contact_relational SET ".
										"type_id='".$row3['type_id']."',contact_id='".$row['contact_id']."'";
							$result4 =	doQuery($select4);						
							//echo($select4."<br>");
							//echo 'contact_type_'.$row3['type_id']."_".$row['contact_id']."<br>";
							//echo 'POST: '.$_POST['contact_type_'.$row3['type_id']."_".$row['contact_id']]."<br>";
						} else {
						
							// DELETE NECESSARRY RELATIONAL ENTRY
							$select4 = 	"DELETE FROM user_contact_relational WHERE type_id='".$row3['type_id']."' AND contact_id='".$row['contact_id']."'";
							$result4 =	doQuery($select4);
							//echo($select4."<br>");
							//echo 'contact_type_'.$row3['type_id']."_".$row['contact_id']."<br>";
							//echo 'POST: '.$_POST['contact_type_'.$row3['type_id']."_".$row['contact_id']]."<br>";
						}
						
						$i3++;
					}		
						
							
							
					$i++;
				}
				//exit();
				
				// IF ADDING CONTACT
				if($_POST['cfirst_name'] != "" || $_POST['clast_name'] != ""){
				
					// INSERT CONTACT
					$next2 = nextId('user_contact');
					$sel2 = "INSERT INTO user_contact SET ".
							"first_name='".$_POST['cfirst_name']."',".
							"last_name='".$_POST['clast_name']."',".
							"email='".$_POST['cemail']."',".
							"address1='".$_POST['caddress1']."',".
							"address2='".$_POST['caddress2']."',".
							"city='".$_POST['ccity']."',".
							"state='".$_POST['cstate']."',".
							"zip='".$_POST['czip']."',".
							"phone='".$_POST['cphone']."',".
							"country='".$_POST['ccountry']."',".
							"active='1',".
							"account_id='".$_SESSION['UserAccount']['userid']."'";
					$res2 = doQuery($sel2);
					
					// LOOP THROUGH CONTACT TYPES FOR RELATIONAL ENTRY
					$sel1 = "SELECT * FROM user_contact_type WHERE active='1'";
					$res1 = doQuery($sel1);
					$i1 = 0;					
					$num1 = mysql_num_rows($res1);
					while($i1<$num1){
						
						$row1 = mysql_fetch_array($res1);
						// IF CONTACT TYPE SELECTED
						$contact_type = "";
						$contact_type = "contact_type_".$row1['type_id'];
						if($_POST[$contact_type] == '1'){
					
							// INSERT  NECESSARY RELAIONAL ENTRY
							$select4 = 	"INSERT INTO user_contact_relational SET ".
										"type_id='".$row1['type_id']."',contact_id='".$next2."'";
							$result4 =	doQuery($select4);							
							
						} else {
						
							// DELETE NECESSARRY RELATIONAL ENTRY
							$select4 = 	"DELETE FROM user_contact_relational WHERE type_id='".$row1['type_id']."' AND contact_id='".$next2."'";
							$result4 =	doQuery($select4);
							
						}
						
						$i1++;
					}					
				}
				
				
				
				//redirect
				$success = '1';
				$report = 'Account Updated Successfully';
				header("Location: ".$_SETTINGS['website'].$_SETTINGS['account_page_clean_url']."/account-form/".$report."/".$success."/0");
				exit();
			}
			else
			{
				$success = '0';
				$array[0] = $success;
				$array[1] = $report;
				return $array;
			}
		}
	}
	
	function AccountForm($heading=0)
	{
		global $_REQUEST;
		global $_SESSION;
		global $_SETTINGS;
		
		//if($_REQUEST['FORM1']=="account-form"){
		$flag = $_SETTINGS['account_page_clean_url'];
		if($flag == $_REQUEST['page']){
			$select = "SELECT * FROM user_account WHERE account_id='".$_SESSION['UserAccount']['userid']."'";
			$result = doQuery($select);
			$row = mysql_fetch_array($result);
			$_POST = $row;
			if($heading == 1){ ?><span class="accounttitle">My Account</span><? } ?>
			<form action="" method="post" class="moduleform account-form">
				<h2>Details</h2>				
					<p>
						<label>*Name/Company</label>
						<input name="name" size="40" value="<?=$_POST['name']?>" />
					</p>
					<p>
						<label>*Email</label>
						<input name="email" size="40" value="<?=$_POST['email']?>" />
						<input type="hidden" name="emailhidden" value="<?=$row['email']?>" />
					</p>
					<p>
						<label>*Username</label>
						<input name="username" size="20" value="<?=$_POST['username']?>" />
						<input type="hidden" name="usernamehidden" value="<?=$row['username']?>" />
					</p>
					<?
					if($_POST['user_permission'] != "" OR $_POST['user_permission'] != "0"){
					?>
					<p>
						<label>Account Type</label>
						<?
						$sel3 = "SELECT * FROM user_permission WHERE permission_id='".$_SESSION['UserAccount']['userpermission']."' LIMIT 1";
						$res3 = doQuery($sel3);
						$row3 = mysql_fetch_array($res3);
						echo "".$row3['name']." <span class='contact-permission-description'>".$row3['description']."</span>";
						?>
					</p>
					<p>
						<label>Account Status</label>
						<?
						$status = lookupDbValue('user_account','status',$_SESSION['UserAccount']['userid'],'account_id');
						echo $status;
						?>
					</p>
					<?
					}
					?>
					
					<?
					if($status != "Pending" AND $_POST['discount_rate'] != '0.00'){
						?>
						<p>
							<label>Discount</label>
							<?
							$discountArray = explode('.',$_POST['discount_rate']);
							$rate = $discountArray[1]."%";
							echo $rate;
							?>
						</p>
						<?
					}
					?>
					
					<h2>Change your Password</h2>
					<?
					//
					// CHANGE PASSWORD
					//
					?>
					<a name="passwordanchor" id="passwordanchor">
					<p class="passtoggle">
						<a class="passtoggler">Click here to Change your Password</a>
					</p>
					<p class="pass1">
						<label>*Password</label>
						<input type="password" size="20" name="password1" value="" />
					</p>
					<p class="pass2">
						<label>*Re-Type Password</label>
						<input type="password" size="20" name="password2" value="" />
					</p>
					<script type="text/javascript">
						//hide the all of the element
						$(".pass1").hide();
						$(".pass2").hide();
						
						//toggle the componenet
						$(".passtoggler").click(function()
						{
							//$(".pass1").slideToggle('fast',callback);
							$(".pass1").slideToggle('fast');
							$(".pass2").slideToggle('fast');
							$(".passtoggle").hide();
						});					
					</script>
				
				
					
				
					<?
					//
					// EXISTING CONTACTS
					//
					?>				
					<h2>Contact Information</h2>
					
					
					<?
					//
					// NEW CONTACT INFO FORM
					//
					?>
					<a name="contactanchor" id="contactanchor">
					<p class="contacttoggle">
						<a class="contacttoggler"><img src="<?=$_SETTINGS['website']."admin/images/icons/plus_16.png"?>" alt="edit" border="0"> New Contact Information</a>
					</p>
					
					<h2 class="contacthide">New Contact</h2>
					
					<p class="contacttogglereopen">
						<a class="contacttogglerreopen"><img src="<?=$_SETTINGS['website']."admin/images/icons/block_16.png"?>" alt="edit" border="0"> Cancel</a>
					</p>
					<p class="contacthide">
						<label>*Contact Type</label>					
						<span style="display:block; float:left; width:300px; margin-bottom:10px;">
							You may select more than one contact type.<Br><br>
							<?
							$select1 = "SELECT * FROM user_contact_type WHERE active='1'";
							$result1 = doQuery($select1);
							$num1 = mysql_num_rows($result1);
							$i1 = 0;
							$iz = 1;
							while($i1<$num1){
								$row1 = mysql_fetch_array($result1);
								?>
								<input type="checkbox" name="contact_type_<?=$row1['type_id']?>" value="1"> <?=$row1['name']?> 
								<?
								$i1++;						
								if($iz == 3){ $iz = 1; /*echo"<br><Br>";*/ } else { $iz++; echo "&nbsp;&nbsp;&nbsp;"; }
							}
							?>
						</span><Br clear='all'>
					</p>
					
					<p class="contacthide">
						<label>*First Name</label>
						<input type="text" size="30" name="cfirst_name" value="<?=$_POST['cfirst_name']?>" />
					</p>
					<p class="contacthide">
						<label>Last Name</label>
						<input type="text" size="30" name="clast_name" value="<?=$_POST['clast_name']?>" />
					</p>
					<p class="contacthide">
						<label>*Email</label>
						<input type="text" size="40" name="cemail" value="<?=$_POST['cemail']?>" />
					</p>				
					<p class="contacthide">
						<label>Phone</label>
						<INPUT TYPE=TEXT size="15" NAME="cphone" VALUE="<?=$_POST['cphone']?>">
					</p>
					<p class="contacthide">
						<label>Address 1</label>
						<input type="text" size="40" name="caddress1" value="<?=$_POST['caddress1']?>" />
					</p>
					<p class="contacthide">
						<label>Address 2</label>
						<input type="text" size="40" name="caddress2" value="<?=$_POST['caddress2']?>" />
					</p>				
					<p class="contacthide">
						<label>City</label>
						<input type="text" size="30" name="ccity" value="<?=$_POST['ccity']?>" />
					</p>
					<p class="contacthide">
						<label>State</label>
						<? selectTable("state","cstate","state_id","state","state","ASC"); ?>
					</p>		
					<p class="contacthide">
						<label>Zip</label>
						<input type="text" size="15" name="czip" value="<?=$_POST['czip']?>" />
					</p>
					<?
					if($_SETTINGS['international'] == '1'){
					?>
					<p class="contacthide">
						<label>Country</label>
						<? selectTable("country","ccountry","country_id","country","country","ASC"); ?>
					</p>
					<? } ?>
					<script type="text/javascript">
						//hide the all of the element
						$(".contacthide").hide();
						$(".contacttogglereopen").hide();
						
						//toggle the componenet
						$(".contacttoggler").click(function()
						{
							//$(".pass1").slideToggle('fast',callback);
							$(".contacthide").slideToggle('fast');
							$(".contacttogglereopen").slideToggle('fast');
							$(".contacttoggle").hide();
							
						});				

						$(".contacttogglerreopen").click(function()
						{
							//$(".pass1").slideToggle('fast',callback);
							$(".contacthide").slideToggle('fast');
							$(".contacttoggle").slideToggle('fast');
							$(".contacttogglereopen").hide();
						});
					
					</script>
					
					
					<?
					// SELECT CONTACTS
					$sel1 = "SELECT * FROM user_contact WHERE ".
							"active='1' AND ".
							"account_id='".$row['account_id']."'";
					$res1 = doQuery($sel1);
					$num1 = mysql_num_rows($res1);
					$i1 = 0;
					
					if($num1 != 0){
						?>
						<p>
							<label class='contact-head-label'>Type</label>
							<span class='contact-head-span'>Name</span>
							<span class='contact-head-span'>Address</span>
							<span class='contact-head-span'>Edit/Delete</span>							
							<br clear='all'>
						</p>
					<?
					}
					
					while($i1<$num1){
						$row1 = mysql_fetch_array($res1);
						$name = $this->FormatFirstLast($row1);
						?>						
						<p class="contact<?=$i1?>toggle">
							
							<?
							$type = "";
							$type = $this->FormatContactType($row1['contact_id']);
							?>
							
							<label><?=$type?> Contact</label>
							<span class='contacta'><?=$this->FormatFirstLast($row1) ?> </span>
							<span class='contacta'><?=$row1['address1'] ?> </span>
							<small>
							&nbsp;&nbsp;
							<a class="contact<?=$i1?>toggler"><img src="<?=$_SETTINGS['website']."admin/images/icons/pencil_16.png"?>" alt="edit" border="0"> edit</a> 
							&nbsp;&nbsp;
							<a href="<?=$_SETTINGS['website'].$_SETTINGS['account_page_clean_url']."/delete-contact_".$row1['contact_id'].""?>"><img src="<?=$_SETTINGS['website']."admin/images/icons/delete_16.png"?>" alt="delete" border="0"> delete</a>
							</small>
							<br clear='all'>
						</p>
						
						<p class="contact<?=$i1?>togglerreopen">
							<label><?=$type?> Contact</label>
							<span class='contacta'><?=$this->FormatFirstLast($row1) ?></span>
							<span class='contacta'><?=$row1['address1'] ?></span>
							<span class='contacta'>
								<small>
									&nbsp;&nbsp;
									<a class="contact<?=$i1?>toggleropen"><img src="<?=$_SETTINGS['website']."admin/images/icons/block_16.png"?>" alt="edit" border="0"> cancel</a>
									&nbsp;&nbsp;
									<a href="<?=$_SETTINGS['website'].$_SETTINGS['account_page_clean_url']."/delete-contact_".$row1['contact_id'].""?>"><img src="<?=$_SETTINGS['website']."admin/images/icons/delete_16.png"?>" alt="delete" border="0"> delete</a>
								</small>
							</span>							
							<br clear='all'>
						</p>
						
						<?
						// CONTACT TYPE
						?>
						<p class="contact<?=$i1?>hide">
							<label>*Contact Type</label>
							<span style="display:block; float:left; width:300px; margin-bottom:10px;">
							You may select more than one contact type.<Br><br>
							<?
							$select11 = "SELECT * FROM user_contact_type WHERE active='1'";
							$result11 = doQuery($select11);
							$num11 = mysql_num_rows($result11);
							$i11 = 0;
							$iz = 1;
							while($i11<$num11){
								// DETERMINE IF ACTIVE
								$active = 0;
								$row11 = mysql_fetch_array($result11);
								$sel22 = "SELECT * FROM user_contact_relational WHERE type_id='".$row11['type_id']."' AND contact_id='".$row1['contact_id']."'";
								$res22 = doQuery($sel22);
								$row22 = mysql_fetch_array($res22);
								if($row22['contact_id'] != ""){ $active = 1; }
								?>
								<input type="checkbox" class="checkbox-type" name="contact_type_<?=$row11['type_id']?>_<?=$row1['contact_id']?>" <? if($active == 1){ ?> CHECKED <? } ?> value="1"> <?=$row11['name']?> 
								<?
								$i11++;						
								if($iz == 3){ $iz = 1; echo"<br><Br>"; } else { $iz++; echo "&nbsp;&nbsp;&nbsp;"; }
							}
							?>
							</span>
							<br clear='all'>
						</p>					
						
						<p class="contact<?=$i1?>hide">
							<label>*First Name & Mi</label>
							<input type="text" size="30" name="cfirst_name<?=$row1['contact_id']?>" value="<?=$row1['first_name']?>" /> <input name='cmiddle_initial<?=$row1['contact_id']?>' value='<?=$row1['middle_initial']?>' style='width:15px;' >
						</p>
						
						<p class="contact<?=$i1?>hide">
							<label>Last Name</label>
							<input type="text" size="30" name="clast_name<?=$row1['contact_id']?>" value="<?=$row1['last_name']?>" />
						</p>
						
						<p class="contact<?=$i1?>hide">
							<label>*Email</label>
							<input type="text" size="40" name="cemail<?=$row1['contact_id']?>" value="<?=$row1['email']?>" />
						</p>
						
						<p class="contact<?=$i1?>hide">
							<label>Phone</label>
							<INPUT TYPE=TEXT size="15" NAME="cphone<?=$row1['contact_id']?>" VALUE="<?=$row1['phone']?>">
						</p>
						
						<p class="contact<?=$i1?>hide">
							<label>Address 1</label>
							<input type="text" size="40" name="caddress1<?=$row1['contact_id']?>" value="<?=$row1['address1']?>" />
						</p>
						
						<p class="contact<?=$i1?>hide">
							<label>Address 2</label>
							<input type="text" size="40" name="caddress2<?=$row1['contact_id']?>" value="<?=$row1['address2']?>" />
						</p>
						
						<p class="contact<?=$i1?>hide">
							<label>City</label>
							<input type="text" size="30" name="ccity<?=$row1['contact_id']?>" value="<?=$row1['city']?>" />
						</p>
						
						<p class="contact<?=$i1?>hide">
							<label>State</label>
							
							<?
							//echo $row1['state'];
							selectTable("state","cstate".$row1['contact_id']."","state_id","state","state","ASC","",$row1['state']);
							?>
							
						</p>	
						
						<p class="contact<?=$i1?>hide">
							<label>Zip</label>
							<input type="text" size="15" name="czip<?=$row1['contact_id']?>" value="<?=$row1['zip']?>" />
						</p>
						
						<?
						if($_SETTINGS['international'] == '1'){
							?>
							<p class="contact<?=$i1?>hide">
								<label>Country</label>
								
								<?
								selectTable("country","ccountry".$row1['contact_id']."","country_id","country","country","ASC","",$row1['country']);
								?>
								
							</p>
						<?
						}
						?>
						
						<p class="contact<?=$i1?>hide">
							<label>&nbsp;</label>
							&nbsp;
						</p>	
						<script type="text/javascript">
							//hide the all of the element
							$(".contact<?=$i1?>hide").hide();
							$(".contact<?=$i1?>togglerreopen").hide();
							
							//toggle the componenet
							$(".contact<?=$i1?>toggler").click(function()
							{
								//$(".pass1").slideToggle('fast',callback);
								$(".contact<?=$i1?>hide").slideToggle('fast');
								$(".contact<?=$i1?>togglerreopen").slideToggle('fast');
								$(".contact<?=$i1?>toggle").hide();
							});	
							
							//toggle the componenet
							$(".contact<?=$i1?>toggleropen").click(function()
							{
								$(".contact<?=$i1?>hide").hide();
								$(".contact<?=$i1?>togglerreopen").hide();
								$(".contact<?=$i1?>toggle").slideToggle('fast');
							});	
							
						</script>						
						<?
						$i1++;
						?>
						
						<?
					}
					
					//
					// IF NO CONTACTS
					//
					if($num1 == 0){
						echo "<p>Your account has no additional contact information.</p>";
					}
					?>
				
				<!-- SUBMIT -->
				<p>
					<label>&nbsp;</label>
					<input style="submit button account-submit" type="submit" name="UPDATEACCOUNT" value="Update Account" />
				</p>
				<br clear="all" />
			</form>
			
			<?
			//
			// ECOMMERCE FORM SECTION
			//
			if(checkActiveModule('0000012')){
				$Ecommerce = new Ecommerce();
				
				echo "<h1>Order History</h1>";
			
				$select = "SELECT * FROM ecommerce_orders WHERE account_id='".$_SESSION['UserAccount']['userid']."' AND active='1' ORDER BY order_id DESC";
				$result = doQuery($select);
				$num = mysql_num_rows($result);
				$i=0;
				while($i<$num){
					$order = mysql_fetch_array($result);
					
					if($order['status'] != "Shipped"){ $order['status'] = "Processing <small>(Check back for shipping info.)</small>"; }
					if($order['status'] == 'Shipped'){ 
						$order['status'] == "Shipped via ".lookupDbValue('ecommerce_shipping_methods','name',$order['shipping_method_id'],'shipping_method_id')." # <a href='javascript:alert(\"Tracking capability coming soon\");'>".$order['shipping_tracking_number']."<a/>"; 
					}
					
					
					
					echo "<table class='order-history'>";
					echo "	<tr>";
					echo "		<th>Order #</th>";
					echo "		<th>Date</th>";
					echo "		<th>Status</th>";				
					echo "		<th>See Invoice</th>";
					echo "		<th style='text-align:right;'>Total</th>";
					echo "	</tr>";
					
					
					echo "	<tr class='order-row'>";
					echo "		<td>".$order['order_id']."</td>";
					echo "		<td>".TimestampIntoDate($order['created'])."</td>";
					echo "		<td>".$order['status']."</td>";
					echo "		<td><a href='".$_SETTINGS['website']."admin/modules/ecommerce/ecommerce_invoice_pdf.php?order_id=".$order['order_id']."' target='_blank'>See Invoice</a></td>";
					echo "		<td style='text-align:right;'>".$order['total']."</td>";
					echo "	</tr>";
					
					// LOOP PRODUCTS UNDER ORDER 
				
					$select1 = "SELECT * FROM ecommerce_product_cart_relational WHERE shopping_cart_id='".$order['shopping_cart_id']."'";
					$result1 = doQuery($select1);
					
					while($row1 = mysql_fetch_array($result1)){
						// GET THE PRODUCT INFO
						$select2 = "SELECT * FROM ecommerce_products WHERE product_id='".$row1['product_id']."' LIMIT 1";
						$result2 = doQuery($select2);
						$row2 = mysql_fetch_array($result2);
						echo "
								<tr>
									<td>&nbsp;</td>
									<td colspan='2'>
										<small>".$row2['name']."<small>
									</td>
									<td colspan='1'>
										<!--
										<input name='star-".$row2['product_id']."' value='1' type='radio' class='star'/>
										<input name='star-".$row2['product_id']."' value='2' type='radio' class='star'/>
										<input name='star-".$row2['product_id']."' value='3' type='radio' class='star'/>
										<input name='star-".$row2['product_id']."' value='4' type='radio' class='star'/>
										<input name='star-".$row2['product_id']."' value='5' type='radio' class='star'/>
										-->
									</td>
									<td>
										<!--
										<a id='comment-open-".$row2['product_id']."'>Write a Comment.</a>
										<textarea name='comment-".$row2['product_id']."' id='comment-".$row2['product_id']."' style=''></textarea>
										<script>
											$('#comment-".$row2['product_id']."').hide();
										</script>
										-->										
									</td>
								</tr>
						";
					}	
					echo "</table>";
					$i++;
				}
				
			
			
				
		
				echo "<h2>Recomended Items</h2>";
				$Ecommerce->SBDisplayRelatedProducts("","",3);
			}
		}
	}
	
	function SecurePage($settings_array)
	{
		/************************************************************
		*
		*   SECURE PAGE FUNCTION SETTINGS
		*	----------------------------------------------
		*	
		*	DESCRIPTION
		*	----------------------------------------------
		*	The secure page function can be used on static pages, or dynamic pages
		*	It will require a front-end user logs in before being able to view certain pages
		*
		*	SETTINGS
		*	----------------------------------------------
		*	Note: the id_array element can be a multi-dimensional array of page ids that require login
		*	Note: the id_array can be left empty and the page will require login
		*	$settings_array["id_array"][0] = "1";
		*	$settings_array["id_array"][1] = "15";
		*	$settings_array["id_array"][2] = "27";
		*
		*	$settings_array["login_page"] = "login.php?1=1" or  "?page_id=5"
		*
		*	IMPLIMENTATION
		*	----------------------------------------------
		*	$UserAccounts->SecurePage($settings_array);
		*	
		*	CSS STYLES
		*	----------------------------------------------
		*	no styles for this function
		*
		************************************************************/
		
		global $_REQUEST;
		global $_SESSION;
				
		if($_SESSION['UserAccount']['userid'] == "")
		{
			/*** IF ID ARRAY IS NOT BEING USED ***/
			if($settings_array['id_array'][0] == ""){
				header("Location: ".$settings_array['login_page']."&LOGINFORM=1&SUCCESS=0&REPORT=Please Login To Access That Page");
				exit();
			}
			/*** IF ID ARRAY IS BEING USED ***/
			else
			{
				$i=0;
				$num = count($settings_array['id_array']);
				while($i<$num)
				{
					echo $i_array[$i];
					if($settings_array['id_array'][$i] == $_REQUEST['page_id'])
					{
						header("Location: ".$settings_array['login_page']."&LOGINFORM=1&SUCCESS=0&REPORT=Please Login To Access That Page");
						exit();
					}
					$i++;
				}
			}
		}
	}
	
	function CheckEmail($implicit_email="")
	{
		global $_POST;
		global $_REQUEST;
		if($_POST['email'] == ""){
			$_POST['email'] = $implicit_email;
		}
		$select = "SELECT * FROM user_account WHERE email='".$_POST['email']."' AND active='1' ".$_SETTINGS['demosqland']."";
		$result = doQuery($select);
		if(mysql_num_rows($result))
		{
			return false;
		} else {
			return true;
		}
	}
	
	function CheckUsername()
	{
		global $_POST;
		global $_REQUEST;
		$select = "SELECT * FROM user_account WHERE username='".$_POST['username']."' AND active='1' ".$_SETTINGS['demosqland']."";
		$result = doQuery($select);
		if(mysql_num_rows($result))
		{
			return false;
		} else {
			return true;
		}
	}
	
	function CheckAccount()
	{
		global $_POST;
		global $_REQUEST;
		$select = "SELECT * FROM user_account WHERE name='".$_POST['name']."' AND email='".$_POST['email']."' AND active='1' ".$_SETTINGS['demosqland']."";
		$result = doQuery($select);
		if(mysql_num_rows($result))
		{
			return false;
		} else {
			return true;
		}
	}
	
	function FormatFirstLast($row)
	{
		global $_SESSION;
		global $_POST;
		global $_SETTINGS;
		
		$name = "";
		
		if($row['first_name'] != ""){
			$name = $row['first_name'];
		}
		
		if($row['last_name'] != ""){
			if($name != ""){ $name .= " "; }	
			$name .= $row['last_name'];
		}		
		return $name;	
	}
	
	function FormatContactType($contact_id)
	{
		//
		// SELECT USER CONTACT TYPES OF THIS CONTACT FOR THE LABEL
		//
		$select2 = 	"SELECT DISTINCT(type_id) FROM user_contact_relational WHERE contact_id='".$contact_id."'";
		$result2 = doQuery($select2);						
		$jum2 = mysql_num_rows($result2);
		$j2 = 0;
		$type = "";
		while($j2<$jum2){
			$jow2 = mysql_fetch_array($result2);
			
			$select3 = "SELECT * FROM user_contact_type WHERE type_id='".$jow2['type_id']."'";
			$result3 = doQuery($select3);
			$jow3 = mysql_fetch_array($result3);
			
			if($type != ""){
				if($j2 == $jum2-1){ $type .= ", and "; } else {
					$type .= ", ";
				}						
			}			
			
			$type .= $jow3['name'];
			$j2++;
		}			
		return $type;
	}
	
	/*** FORMAT USER NAME ***/
	function getUserName()
	{
		global $_SESSION;
		global $_POST;
		global $_SETTINGS;
		
		//var_dump($_SESSION['UserAccount']);
		//exit();
		
		
		/*** USE THE ACCOUNTS CONTACT ENTRY NAME 	
		$sel1 = "SELECT * FROM user_contact WHERE account_id='".$_SESSION['UserAccount']['userid']."' AND record_type='Primary'";
		$res1 = doQuery($sel1);
		$nu = mysql_num_rows($res1);
		$ro = mysql_fetch_array($res1);
		$name = $ro['first_name']." ".$ro['last_name'];
		***/
		
		/*** USE THE ACCOUNTS CONTACT ENTRY NAME 	
		if($name == "" || $name == " ")
		$sel1 = "SELECT * FROM user_contact WHERE account_id='".$_SESSION['UserAccount']['userid']."' AND record_type='Primary'";
		$res1 = doQuery($sel1);
		$nu = mysql_num_rows($res1);
		$ro = mysql_fetch_array($res1);
		$name = $ro['first_name']." ".$ro['last_name'];
		***/
		
		/*** USE THE ACCOUNT NAME ***/
		if($name == "" || $name == " "){
			$sel1 = "SELECT * FROM user_account WHERE account_id='".$_SESSION['UserAccount']['userid']."'";
			$res1 = doQuery($sel1);
			$nu = mysql_num_rows($res1);
			$ro = mysql_fetch_array($res1);
			$name = $ro['name'];
		}
		
		/*** USE THE ACCOUNT USERNAME NAME ***/
		if($name == "" || $name == " "){			
			$name = $ro['username'];
		}
		
		return $name;
		
	}
}
?>