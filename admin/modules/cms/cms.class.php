<?
 /*************************************************************************************************************************************
*
*   Copyright (c) 2011 Karl Steltenpohl Development LLC. All Rights Reserved.
*	
*	This file is part of Karl Steltenpohl Development LLC's WES (Website Enterprise Software).
*	Authored By Karl Steltenpohl
*	Commercial License
*	http://www.wescms.com/license
*
*	http://www.wescms.com
*	http://www.webksd.com/wes
* 	http://www.karlsteltenpohl.com/wes
*
*************************************************************************************************************************************/

/*** CMS Class ***/
class CMS {
	
	var $theme;
	var $website_path;
	
	// CONSTRUCTOR
	function CMS()
	{
		global $_SETTINGS;
		$this->theme = $this->activeTheme();
		$this->website_path = $_SETTINGS['website_path'];
	}
	
	// GET ACTIVE THEME
	function activeTheme()
	{
		global $_SETTINGS;
		
		$select = 	"SELECT * FROM `settings` WHERE ".
					"`name`='Theme' ".
					"".$_SETTINGS['demosqland']."";
					
		$result = mysql_query($select) or die("err");
		$row = mysql_fetch_array($result);
		return $row['value'];
	} 
	
	// GET ACTIVE HOMEPAGE
	function activeHomepage()
	{
		global $_SETTINGS;
		
		$select = 	"SELECT * FROM `settings` WHERE ".
					"`name`='Homepage' ".
					"".$_SETTINGS['demosqland']."";
				
		$result = mysql_query($select) or die("err");
		$row = mysql_fetch_array($result);
		
		$select = 	"SELECT * FROM `pages` WHERE ".
					"id='".$row['value']."' ".
					"".$_SETTINGS['demosqland']."";
		$result = mysql_query($select) or die("err1");
		$row = mysql_fetch_array($result);
		
		//die("ROW: ".$row['clean_url_name']."");
		//exit();
		
		return $row['clean_url_name'];
	}
	
	// GET THE TEMPLATE
	function get_template()
	{
		global $_SETTINGS;
		global $_REQUEST;
		
		$page = $_REQUEST['page'];
		
		if($page == ""){
			if(isset($_REQUEST['xid'])){
				$page = $_REQUEST['xid'];
				$admin = 1;
			}
		}
		
		if($page == "")		{
			$page = $this->activeHomepage(); 
		}	
		
		//if($_SETTINGS['debug'] == 1){
		//	echo "P: ".$file." <Br>";
		//}
		
		//die("Page: ".$page);
		//exit();
		
		if($admin == 1){
			$select = 	"SELECT * FROM `pages` WHERE ".
						"`id`='".$page."' ".
						"".$_SETTINGS['demosqland']."";
		} else {
			$select = 	"SELECT * FROM `pages` WHERE ".
						"`clean_url_name`='".$page."' AND active='1'".
						"".$_SETTINGS['demosqland']."";
		}	
		$result = mysql_query($select) or die("err");
		$row = mysql_fetch_array($result);
		return $row['template_path'];
	}
	
	// GET ARRAY OF THEMES TEMPLATES
	function templates()
	{
		$websitepath = $this->website_path;
		$theme = $this->theme;
		$directory = $_SERVER['DOCUMENT_ROOT'].$websitepath.'themes/'.$theme.'';
		if(file_exists($directory))
		{
			if ($handle = opendir($_SERVER['DOCUMENT_ROOT'].$websitepath.'themes/'.$theme.'')) { 
				$i = 0;
				while (false !== ($file = readdir($handle)))
				{ 
					if ($file != "." && $file != "..")
					{
						if(!is_dir($_SERVER['DOCUMENT_ROOT'].$websitepath.'themes/'.$theme.''.$file.''))
						{
							$myFile = $_SERVER['DOCUMENT_ROOT'].$websitepath.'themes/'.$theme.''.$file;
							$fh = fopen($myFile, 'r');
							$theData = fread($fh, 5000);
							fclose($fh);
							
							$pos = strrpos($theData, "{{{{{KSD Template");
							if ($pos === false) { // note: three equal signs
								//die("Not a template.");
							} else {
								$array1 = explode("}}}}}",$theData);
								$array2 = explode("{{{{{",$array1[0]);
								$data = $array2[1];
								$array3 = explode(",",$data);
								$filearray[$i]['file'] = $file; 
								$filearray[$i]['name'] = $array3[1]; 
								$i++;
							}
						}
					}
				}// END while	
				closedir($handle);
			} 
		} else {
			echo("$directory <br />NOT EXISTS");
		}
		return $filearray;
	}
		
	// Get and include TEMPLATE for page build
	function Template()
	{		
		global $_REQUEST;		
		$theme = $this->theme;
		/*** Get Template By Page Id ***/
		$file = $this->get_template();
		
		if($_SETTINGS['debug'] == 1){
			echo "TEMPLATE: ".$file." <Br>";
		}
		
		$websitepath = $this->website_path;
		//die("FILE: $file");
		//exit();
		include''.$_SERVER['DOCUMENT_ROOT'].$websitepath.'themes/'.$theme.''.$file.'';
	}
		
	// Get array of template's content areas
	function content_areas($template)
	{
		$websitepath = $this->website_path;
		$theme = $this->theme;
		$directory = $_SERVER['DOCUMENT_ROOT'].$websitepath.'themes/'.$theme.''.$template;

		if(file_exists($directory))
		{
			$myFile = $directory;
			$fh = fopen($myFile, 'r');
			$theData = fread($fh, filesize($myFile));
			fclose($fh);

			//$theDataArray = explode('$CMS->Content(',$theData);		
			$theDataArray = explode('$CMS->Content(',$theData);			
			
			$anum = count($theDataArray);
			//echo "BEFORE LOOP COUNT: $anum<br>";
			$a = 0;
			$g = 0;
			$i = 0;
			$f = 0;
			
			while($a<$anum)
			{
				$theContentArray = explode(");",$theDataArray[$a]);
				$bnum = count($theContentArray);
				//echo "Loop 1: $a <br />";
				//echo "VARIABLE BEING EXPLODED:<br>{{".substr($theDataArray[$a],0,200)."}} <br />";
				//echo "COUNT ARRAY:".$bnum." <br /><br>";
				
				if($bnum >= 2)
				{
					$theFunctionArray = explode(",",$theContentArray[0]);
					$cnum = count($theFunctionArray);
					//echo "Loop2: $g<br />";
					//echo "NEW VARIABLE BEING EXPLODED:<br>";
					//echo "{{".substr($theContentArray[0],0,200)."}} <br />";
					//echo "COUNT ARRAY:".$cnum." <br /><br>";
					
					if(strpos($theContentArray[0], ",'")){						
						$contentarray[$a]['name'] = $theFunctionArray[1];
						$contentarray[$a]['order'] = $theFunctionArray[0];
						//echo "VAR: ".$contentarray[$i]['name']."<br>";
						//echo "NAME: ".$contentarray[$a]['name']."<br>";
						//echo "Order: ".$contentarray[$a]['order']."<br><br>";
						$f++;
					}
					$g++;
				}
				$a++;
			}//END while
		}
		//exit();
		return $contentarray;
	}

	/**
	 *
	 * Get and include CONTENT for page build
	 *
	 */
	function Content($order,$name)
	{
		global $_REQUEST;
		global $_SETTINGS;
		
		$page = $_REQUEST['page'];
		
		if($page == "")
		{
			$page = $this->activeHomepage();
		}
		
		$select = "SELECT * FROM pages WHERE clean_url_name='".$page."' ".$_SETTINGS['demosqland']." and active='1'";
		$result = doQuery($select);
		$row = mysql_fetch_array($result);
		$page_id = $row['id'];
		
		if($_SETTINGS['debug'] == 1){
			echo "CONTENT AREA PAGE ID: $page_id<Br>";
		}
		
		
		if($_REQUEST['PREVIEW']){
		
			$select = 	"SELECT * FROM `content` WHERE ".
						"`page_id`='".$page_id."' AND ".
						"`order`='".$order."' AND ".
						"`preview`='1' ".
						"".$_SETTINGS['demosqland']."";
						
		} else {

			$select = 	"SELECT * FROM `content` WHERE ".
						"`page_id`='".$page_id."' AND ".
						"`order`='".$order."' AND ".
						"`preview`='0' ".
						"".$_SETTINGS['demosqland']."";

		}
		$result = mysql_query($select)or die("err");
		$row = mysql_fetch_array($result);
		$content = $row['content']; 
		
		if($_SETTINGS['debug'] == 1){
			echo "CONTENT AREA ID: ".$row['id']."<Br>";
		}
		
		echo $content;
	}
	
	/**
	 *
	 * Get array of OTHER CONTENT areas
	 *
	 */
	function layout_content_areas()
	{
		$websitepath = $this->website_path;
		$theme = $this->theme;
		$directory = $_SERVER['DOCUMENT_ROOT'].$websitepath.'themes/'.$theme.'index.php';
		if(file_exists($directory))
		{
			/*** OPEM THE FILE ***/
			$myFile = $directory;
			$fh = fopen($myFile, 'r');
			$theData = fread($fh, filesize($myFile));
			fclose($fh);
			
			/*** GET ARRAY OF FILE ***/
			$theDataArray = explode('$CMS->LayoutContent(',$theData);
			$anum = count($theDataArray);
			$a = 0;
			$i = 0;
			while($a<$anum)
			{
				//if($a > 0){
					$theContentArray = explode(");",$theDataArray[$a]);
					$bnum = count($theContentArray);
					//echo "a-$a : bnum-$bnum <br />";
					if($bnum >= 2)
					{
						$theFunctionArray = explode(",",$theContentArray[0]);
						$cnum = count($theFunctionArray);
						//echo "b-$a : cnum-$cnum : ".$theContentArray[0]."<br />";
						
						if($cnum == 2)
						{
							$contentarray[$i]['name'] = str_replace("'","",$theFunctionArray[1]);
							$contentarray[$i]['order'] = str_replace("'","",$theFunctionArray[0]);
							
							/*** INSERT CHECK ***/
							$sel1 = "INSERT INTO `content` SET ".
									"`order`='".$contentarray[$i]['order']."', ".
									"`layout`='1'".
									"".$_SETTINGS['demosql']."";
							$res1 = doQuery($sel1);		
							$i++;
						}
						
						//echo "<strong>".$theContentArray[0]."</strong><br>";
					}
					//echo "<br />theDataArray-$a: ".$theDataArray[$a]."<br />";
				//}
				$a++;
			}//END while
		}
		return $contentarray;
	}
	
	/**
	 *
	 * GET Other Content Area Content
	 *
	 */
	function LayoutContent($order,$name)
	{
		global $_REQUEST;
		global $_SETTINGS;
		
		if($_REQUEST['PREVIEW']){
		
			$select = 	"SELECT * FROM `content` WHERE ".
						"`layout`='1' AND ".
						"`order`='".$order."' AND ".
						"`preview`='1' ".
						"".$_SETTINGS['demosqland']."";
						
		} else {
		
			$select = 	"SELECT * FROM `content` WHERE ".
						"`layout`='1' AND ".
						"`order`='".$order."' AND ".
						"`preview`='0' ".
						"".$_SETTINGS['demosqland']."";
			
		}		
		$result = mysql_query($select)or die("err");
		$row = mysql_fetch_array($result);
		$content = $row['content']; 
		echo $content;
	}
	
	/**
	 *
	 * Return TRUE if homepage
	 *
	 */
	function thisActiveHomepage()
	{
	
		global $_REQUEST;
		$page = $_REQUEST['page_id'];
				
		if($page == ""){
			$page = $this->activeHomepage();
		}
		if($page == $this->activeHomepage())
		{
			return true;
		} else {
			return false;
		}
	}
		
	/**
	 *
	 * Build Page
	 *
	 */
	function buildPage()
	{
		global $_REQUEST;
		global $_SESSION;
		global $_SETTINGS;
		global $_POST;
		$websitepath = $this->website_path;
		
		//error_reporting(E_ALL);
		
		//
		// Get Active Theme
		//
		$theme = $this->theme;
		
		// DEBUG
		if($_SETTINGS['debug'] == 1){
			echo "<br>THEME: $theme <Br>";
			
		}
		
		// SET REFERER
		global $_SERVER;		
		//echo "<Br>REFERER".$HTTP_REFERER."<br>";
		//echo "REFERER".$_SERVER['HTTP_REFERER']."<br>";
		//echo "REFERER".$_SERVER['REFERER']."<br>";	
		if($_SERVER['HTTP_REFERER'] != ""){
			$_SESSION['referer'] = $_SERVER['HTTP_REFERER'];
		}
		//echo $_SESSION['referer'];
		
		/**
		 *
		 * PAGE STATUS
		 *
		 */
		 
		//CHECK IF HOME PAGE, AND SET HOME PAGE CLEAN URL
		if($_REQUEST['page'] == ''){
			// GET HOMEPAGE CLEANURL
			$homepagecleanurl = lookupDbValue('pages', 'clean_url_name', $_SETTINGS['homePage'],'id');
			$_REQUEST['page'] = $homepagecleanurl;
		}
				
		// GET THE PAGE
		$select = "SELECT * FROM pages WHERE clean_url_name='".$_REQUEST['page']."' AND active='1'";
		$result = doQuery($select);		
		$num = mysql_num_rows($result);
		
		// TESTING
		if($_SETTINGS['debug'] == 1){	echo "<Br>PAGE: ".$_REQUEST['page']."<Br>";	}
		
		// IF THERE IS A PAGE
		if($num > 0){
			$row = mysql_fetch_array($result);
			// IF PAGE NOT PUBLISHED REDIRECT TO 404
			if($row['status'] != 'Published'){
				header("Location: ".$_SETTINGS['website'].$_SETTINGS['404_page_clean_url']."");
				exit;
			}
			
			// TESTING
			if($_SETTINGS['debug'] == 1){echo "<br>PAGE ID: ".$row['id']." <Br>";}
			
			// SECURE
			if($row['secure'] == '1'){
			  if($_SERVER['HTTPS']!="on"){
				 $redirect= "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
				 header("Location:$redirect");
			  }
			}
			
		}
		// ELSE IF PAGE DOESN'T EXIST REDIRECT TO 404
		else {
			header("Location: ".$_SETTINGS['website'].$_SETTINGS['404_page_clean_url']."");
			exit;			
		}
		
		//
		// ALTERNATE URL
		//
		if($_REQUEST['page'])
		{
			$select = "SELECT template_path,alternate_url FROM pages WHERE clean_url_name='".$_REQUEST['page']."' AND active='1'";
			$result = doQuery($select);
			$row = mysql_fetch_array($result);
			if($row['template_path'] == '0'){
				if($row['alternate_url'] != ""){
					//die($row['alternate_url']);
					//exit();
					header("Location: ".$row['alternate_url']."");
					exit;
				}
			}
		}
		
		// CHECK WEBSITE DOWN
		if($_SETTINGS['website_down'] == "1" AND $_SESSION["session"]->admin->userid == "")
		{
			header("Location: ".$_SETTINGS['website']."admin/modules/website_down/WebsiteDown/WebsiteDown/WebsiteDown/index.php");
			exit;
		}
		// IF USER ACCOUNTS MODULE PRESENT
		if(checkActiveModule('0000005'))
		{

			$UserAccounts = new UserAccounts();

			//
			// FRONT END USER ACCOUNT PERMISSION ACCESS
			//
			if($this->checkUserPermission() == false){
				$homepagecleanurl = lookupDbValue('pages', 'clean_url_name', $_SETTINGS['homePage'],'id');
				header("Location: ".$_SETTINGS['website'].$_SETTINGS['permission_page_clean_url']."/0/You do not permission to view that page/0/0");
				exit;
			}			
			
			//
			// LOGIN, FORGOT PASSWORD, ACCOUNT VERIFICATION, REGISTRATION, LOGOUT, ETC
			//
			$loginformarray = $UserAccounts->LoginFormAction();					
			if($loginformarray){ $array = $loginformarray; }		
			
			// FORGOT PASSWORD
			$forgotpasswordformarray = $UserAccounts->ForgotPasswordFormAction();					
			if($forgotpasswordformarray){ $array = $forgotpasswordformarray; }

			// REGISTRATION
			$registrationformarray = $UserAccounts->RegistrationFormAction();					
			if($registrationformarray){ $array = $registrationformarray; }
			
			// VERIFICATION FORM
			$verificationemailformarray = $UserAccounts->SendVerificationEmailFormAction();					
			if($verificationemailformarray){ $array = $verificationemailformarray; }
			
			// VERIFICATION ACTION
			$verificationactionarray = $UserAccounts->VerifyEmailAction();
			if($verificationactionarray){ $array= $verificationactionarray; }
			
			// ACCOUNTFORM ACTION
			$accountformarray = $UserAccounts->AccountFormAction();
			if($accountformarray){ $array= $accountformarray; }
			
			// DELETE CONTACT ACTION
			$deletecontactarray = $UserAccounts->DeleteContactAction();
			if($deletecontactarray){ $array= $deletecontactarray; }
			 
			// UNSUBSCRIBE ACTION
			$unsubscribeemailformarray = $UserAccounts->UnsubscribeEmailFormAction();
			if($unsubscribeemailformarray){ $array= $unsubscribeemailformarray; }
			
			// NEWSLETTER SIGNUP
			$newslettersignupformarray = $UserAccounts->NewsletterSignupFormAction();
			if($newslettersignupformarray){ $array= $newslettersignupformarray; }
			
			// LOGOUT
			$UserAccounts->Logout();			
		}
		// IF CONTACT FORM MODULE PRESENT
		if(checkActiveModule('0000006'))
		{			
			$Contact = new Contact();
			
			// CONTACT FORM ACTION
			$contactformarray = $Contact->ContactFormAction();
			if($contactformarray){ $array = $contactformarray; }
		}
		// IF ECOMMERCE MODULE PRESENT
		if(checkActiveModule('0000012'))
		{
			$Ecommerce = new Ecommerce();
			
			// SET SHOPPING CART
			$Ecommerce->setShoppingCart();		
			
			// FILTER PRODUCTS AJAX
			// SEARCH FORM
			// IMPORTANT THAT SEARCH PAGE IS SET SO THE FILTERS/SEARCHING
			// SESSIONS ARE UNAFFECTED BY OTHER WEB STUFF
			if($_REQUEST['page'] == $_SETTINGS['products_page_clean_url']){
				$Ecommerce->FilterProductsAjax();
			}
			
			// QUICKBOOKS AJAX
			$Ecommerce->PostOrderQuickbooksAjax();
			
			// ADD TO CART FORM ACTION
			$addtocartformarray = $Ecommerce->AddToCartFormAction();
			if($addtocartformarray){ $array = $addtocartformarray; }
			
			// SHOPPING CART FORM ACTION
			$shoppingcartformarray = $Ecommerce->ShoppingCartFormAction();
			if($shoppingcartformarray){ $array = $shoppingcartformarray; }
			
			// CHECKOUT INFORMATION FORM ACTION
			// STORES CUSTOMER ACCOUNT DETAILS, SHIPPING, BILLING
			$checkoutinformationformarray = $Ecommerce->CheckoutInformationFormAction();
			if($checkoutinformationformarray){ $array = $checkoutinformationformarray; }
			
			// CONFIRMATION / PLACE ORDER FORM ACTION 
			// CREATES ORDER
			$confirmationformarray = $Ecommerce->CheckoutConfirmationFormAction();
			if($confirmationformarray){ $array = $confirmationformarray; }
			
			$commentformarray = $Ecommerce->CommentFormAction();
			if($commentformarray){ $array = $commentformarray; }
			//echo "<Br> CART ID: ".$Ecommerce->getCartId()."<Br>";
			
		}
		// IF EVENTS MODULE PRESENT
		if(checkActiveModule('0000019'))
		{
			$Events = new Events();
			
			// EVENT ALERTS FORM ACTION
			$eventalertformarray = $Events->eventAlertFormAction();
			if($eventalertformarray){ $array = $eventalertformarray; }
			
			// EVENT ALERTS OPTOUT FORM ACTION
			$eventalertoptoutformarray = $Events->eventAlertOptOutFormAction();
			if($eventalertoptoutformarray){ $array = $eventalertoptoutformarray; }
			
			// EVENT ALERTS OPTOUT FORM ACTION
			$addeventtocartformarray = $Events->eventAlertOptOutFormAction();
			if($addeventtocartformarray){ $array = $addeventtocartformarray; }
		}
		// IF BLOG MODULE
		if(checkActiveModule('0000016'))
		{
			$Blog = new Blog();
			
			// COMMENTS FORM ACTION
			$blogcommentformarray = $Blog->commentformAction();
			if($blogcommentformarray){ $array = $blogcommentformarray; }
		}
		
		// IF PROPERTIES MODULE PRESENT
		if(checkActiveModule('0000020'))
		{
			$Properties = new Properties();						
		}
		// CUSTOMER FILE MANAGER
		if(checkActiveModule('0000025'))
		{
			$CustomerFileManager = new CustomerFileManager();
		}
		

		// UNIVERAL ARRAY FOR REPORT / SUCCESS
		if($array)
		{
			$_REQUEST['SUCCESS'] = $array[0];
			$_REQUEST['REPORT'] = $array[1];
		}
		
		// BUILD PAGE
		$file = "index.php";
		if($_SETTINGS['debug'] == 1){
			echo '<br>Before Main Include / Page Build: '.$_SERVER['DOCUMENT_ROOT'].$websitepath.'themes/'.$theme.''.$file.'<br>';
		}		
		(include(''.$_SERVER['DOCUMENT_ROOT'].$websitepath.'themes/'.$theme.''.$file.'')) or die('<Br>Error Building Page...<br>');	
		if($_SETTINGS['debug'] == 1){
			echo "<br>Page Built Successfully...<br>";
		}
	}
	
	/**
	 *
	 * FUNCTIONALITY
	 * Display all page functionality
	 *
	 */
	function Functionality()
	{
		global $_REQUEST;
		global $_SESSION;
		global $_SETTINGS;
		
		/**
		 *
		 * User Accounts
		 *
		 */
		if(checkActiveModule('0000005'))
		{		
			$UserAccounts = new UserAccounts();			
			$UserAccounts->LoginForm();
			$UserAccounts->ForgotPasswordForm();
			$UserAccounts->RegistrationForm(0,0);
			$UserAccounts->SendVerificationEmailForm();
			$UserAccounts->AccountForm();
			$UserAccounts->UnsubscribeEmailForm();			
		}
		
		/**
		 *
		 * Contact
		 *
		 */
		if(checkActiveModule('0000006'))
		{			
			$Contact = new Contact();			
			$Contact->ContactForm();		
		}
		
		/**
		 *
		 * Slideshow
		 *
		 */
		if(checkActiveModule('0000015'))
		{		
			$ImageSlider3 = new ImageSlider3();				
			$ImageSlider3->DisplayImageSlider3();
		}		
		
		/**
		 *
		 * Portfolio
		 *
		 */
		if(checkActiveModule('0000017'))
		{		
			$Portfolio = new Portfolio();				
			$Portfolio->DisplayPortfolio();
		}	
		
		/**
		 *
		 * Events
		 *
		
		if(checkActiveModule('0000019'))
		{		
			$Events = new Events();				
			$Events->DisplayEvents();			
		}	
		 */
		
		/**
		 *
		 * Ecommerce
		 *
		 */
		if(checkActiveModule('0000012'))
		{
			$Ecommerce = new Ecommerce();
			//$Ecommerce->SearchAndDisplayProducts();
			$Ecommerce->DisplayProductDetails();
			$Ecommerce->DisplayShoppingCart();
			$Ecommerce->CheckoutInformationForm();
		}
		
		/**
		 *
		 * Properties
		 *
		 */
		if(checkActiveModule('0000020'))
		{
			$Properties = new Properties();
			$Properties->DisplayProperties();
		}
	}
	
	/**
	 *
	 * CHECK USER PERMISSION
	 *
	 */
	function checkUserPermission()
	{
		global $_REQUEST;
		global $_SESSION;
		global $_SETTINGS;
		$websitepath = $this->website_path;
		//
		// THE USERS PERMISSION
		//
		$user_permission = $_SESSION['UserAccount']['permissionlevel'];
		// CHECK IF THE ACCOUNT IS APPROVED 
		$userStatus = 'Pending';
		$userStatus = lookupDbValue('user_account','status',$_SESSION['userid'],'account_id');
		
		// THE CLEAN URL OF THE REQUESTED PAGE
		$clean_url_name = $_REQUEST['page'];
		
		//
		// GET THE PAGE PERMISSION AND TYPE
		//
		if($clean_url_name != ""){
			$select = "SELECT user_permission,permission_type FROM pages WHERE clean_url_name='".$clean_url_name."' AND active='1' LIMIT 1";
			$result = doQuery($select);
			$row = mysql_fetch_array($result);
			$page_permission_id = $row['user_permission'];
			$select = "SELECT permission_level FROM user_permission WHERE permission_id='".$page_permission_id."' AND active='1'";
			$result = doQuery($select);
			$row1 = mysql_fetch_array($result);
			$page_permission = $row1['permission_level'];
			$page_permission_type = $row['permission_type'];
		} else {
			// IF NO CLEAN URL NAME
			//$select = "SELECT user_permission,permission_type FROM pages WHERE id='".$_SETTINGS['homePage']."' AND active='1' LIMIT 1";
			$page_permission = '1';
			$page_permission_type = 'Hierarchical';
		}
		
		//
		// GET LOWEST USER PERMISSION
		//
		$select = "SELECT permission_level FROM user_permission WHERE active='1' ORDER BY permission_level ASC LIMIT 1";
		$result = doQuery($select);
		$row = mysql_fetch_array($result);
		$lowest_user_permission = $row['permission_level'];
		
		//echo "Clean URL NAME ".$clean_url_name;
		//echo "USER PERMISSION ".$user_permission;
		//echo "<br>PAGE PERMISSION ".$page_permission;
		//echo "<br>TYPE ".$page_permission_type;
		//echo "<br>Lowest Permission ".$lowest_user_permission;
		
		// IF PAGE PERMISSION IS NOT SET THEN RETURN TRUE
		if($page_permission == ''){
			return true;
		}
		
		// IF PAGE PERMISSION IS HIGHER THAN THE LOWEST LEVEL AND THE
		// CUSTOMER ACCOUNT TYPE IS NOT APPROVED THEN RETURN FALSE
		if($page_permission != $lowest_user_permission){
			if($userStatus != 'Active'){
				return false;
			}
		}		
		
		// IF PSGE PERMISSION IS HIERSCHICATL
		// AND THE USER HAS AN GREATER THAN OR
		// EQUAL TO ACCESS THEN RETURN TRUE
		if($page_permission_type == "Hierarchical"){
			if($user_permission >= $page_permission){
				return true;
			}
		}
		
		if($page_permission_type == "Sole Access"){
			if($user_permission == $page_permission){
				return true;
			}
		}
		
		
		
		
		return false;
		//die('false');
		//exit;
			
	}
	
	/**
	 *
	 * GET PAGE TITLE
	 *
	 */
	function PageTitle()
	{
		global $_SETTINGS;
		global $_REQUEST;
		
		$page = $_REQUEST['page'];
		if($page == ""){ $page = $this->activeHomepage(); }
		
		$select = 	"SELECT * FROM pages WHERE ".
					"clean_url_name='".$page."' ".
					"".$_SETTINGS['demosqland']."";
					
		$result = mysql_query($select) or die("err");
		$row = mysql_fetch_array($result);
		
		$prefix = $_SETTINGS['titlePrefix'];
		$suffix = $_SETTINGS['titleSuffix'];
		$default = $_SETTINGS['titleDefault'];
		if($default != "")
		{
			return $default;
		}
		else
		{
			return "$prefix".$row['title']."$suffix";			
		}
	}
	
	/**
	 *
	 * GET PAGE NAME
	 *
	 */
	function PageName()
	{
		global $_SETTINGS;
		global $_REQUEST;
		
		$page = $_REQUEST['page'];
		if($page == ""){ $page = $this->activeHomepage(); }
		
		$select = 	"SELECT * FROM pages WHERE ".
					"clean_url_name='".$page."' ".
					"".$_SETTINGS['demosqland']."";
		
		$result = mysql_query($select) or die("err");
		$row = mysql_fetch_array($result);
		return $row['name'];			
	}
	
	/**
	 *
	 * GET PAGE NAME
	 *
	 */
	function PageSubTitle()
	{
		global $_SETTINGS;
		global $_REQUEST;
		
		$page = $_REQUEST['page'];
		if($page == ""){ $page = $this->activeHomepage(); }
		
		$select = 	"SELECT * FROM pages WHERE ".
					"clean_url_name='".$page."' ".
					"".$_SETTINGS['demosqland']."";
		
		$result = mysql_query($select) or die("err");
		$row = mysql_fetch_array($result);
		return $row['subtitle'];			
	}
	
	function PageImage()
	{
		global $_SETTINGS;
		global $_REQUEST;
		
		$page = $_REQUEST['page'];
		if($page == ""){ $page = $this->activeHomepage(); }
		
		$select = 	"SELECT * FROM pages WHERE ".
					"clean_url_name='".$page."' ".
					"".$_SETTINGS['demosqland']."";
		
		$result = mysql_query($select) or die("err");
		$row = mysql_fetch_array($result);
		return $row['image'];	
	}
	
	/**
	 *
	 * HEAD TAG SEO Optimization 
	 *
	 */
	function headOptimization()
	{
		
		echo "<meta name='description' content='".$this->PageDescription()."' />";
		echo "<meta name='keywords' content='".$this->PageKeywords()."' />";
		echo "<title>".$this->PageTitle()."</title>";
	
	}
	
	/**
	 *
	 * GET PAGE DESCRIPTION
	 *
	 */
	function PageDescription()
	{
		global $_SETTINGS;
		global $_REQUEST;
		
		$page = $_REQUEST['page'];
		if($page == ""){ $page = $this->activeHomepage(); }
		
		$select = 	"SELECT * FROM pages WHERE ".
					"clean_url_name='".$page."' ".
					"".$_SETTINGS['demosqland']."";
		
		$result = mysql_query($select) or die("err");
		$row = mysql_fetch_array($result);
		return $row['description'];			
	}
	
	/*** Get Page Keywords ***/
	function PageKeywords()
	{
		global $_SETTINGS;
		global $_REQUEST;
		
		$page = $_REQUEST['page'];
		if($page == ""){ $page = $this->activeHomepage(); }
		
		$select = 	"SELECT * FROM pages WHERE ".
					"clean_url_name='".$page."' ".
					"".$_SETTINGS['demosqland']."";
		
		$result = mysql_query($select) or die("err");
		$row = mysql_fetch_array($result);
		return $row['keywords'];			
	}
		
	/*** Main Navigation ***/
	function MainNavigation($dropdown=true)
	{
		global $_SETTINGS;
		global $_REQUEST;
		global $_POST;
		global $_SESSION;
		
		/*** SELECT 1st LEVEL ***/
		$sel1 = "SELECT * FROM pages WHERE ".
				"main_nav='1' AND ".
				"active='1' AND ".
				"status='Published' ".
				"".$_SETTINGS['demosqland']." ".
				"ORDER BY sort ASC";
		
		$res1 = doQuery($sel1);
		$num1 = mysql_num_rows($res1);
		$i1 = 0;
		while($i1<$num1){
			$row1 = mysql_fetch_array($res1);
			?>
			<li><a class="<? if($_REQUEST['page'] == $row1['clean_url_name']){ ?> active <? } ?> nav-<?=$row1['clean_url_name']?> nav-<?=$i1?>" href="<?=$_SETTINGS['website']?><?=$row1['clean_url_name']?><?=$row1['ammend_url']?>"><span><?=$row1['name']?></span></a>
			<?
			if($dropdown != false){
				/*** SELECT 2nd LEVEL ***/
				$sel2 =	"SELECT * FROM pages WHERE ".
						"parent='".$row1['id']."' AND ".
						"active='1' AND ".
						"status='Published' ".
						"".$_SETTINGS['demosqland']."".
						"ORDER BY sort ASC";
				
				$res2 = doQuery($sel2);
				$num2 = mysql_num_rows($res2);
				$i2 = 0;
				if($num2){
					?>
					<ul>
					<?
					while($i2<$num2){
						$row2 = mysql_fetch_array($res2);
						?>
						<li><a class="<? if($_REQUEST['page'] == $row2['clean_url_name']){ ?> active <? } ?> nav-<?=$row2['clean_url_name']?> nav-<?=$i1."-".$i2?>" href="<?=$_SETTINGS['website']?><?=$row2['clean_url_name']?><?=$row2['ammend_url']?>"><span><?=$row2['title']?></span></a>
						<?
						/*** SELECT 3rd LEVEL ***/
						$sel3 =	"SELECT * FROM pages WHERE ".
								"parent='".$row2['id']."' AND ".
								"active='1' AND ".
								"status='Published' ".
								"".$_SETTINGS['demosqland']."".
								"ORDER BY sort ASC";
						
						$res3 = doQuery($sel3);
						$num3 = mysql_num_rows($res3);
						$i3 = 0;
						if($num3){
							?>
							<ul>
							<?
							while($i3<$num3){
								$row3 = mysql_fetch_array($res3);
								?>
								<li><a class="<? if($_REQUEST['page'] == $row3['clean_url_name']){ ?> active <? } ?> nav-<?=$row3['clean_url_name']?> nav-<?=$i1."-".$i2."-".$i3?>" href="<?=$_SETTINGS['website']?><?=$row3['clean_url_name']?><?=$row3['ammend_url']?>"><span><?=$row3['title']?></span></a>
								</li>
								<?
								$i3++;
							}
							?>
							</ul>
							<?
						}
						?>
						</li>
						<?
						$i2++;
					}
					?>
					</ul>
					<?
				}
			}// if dropdown
			?>
			</li>
			<?
			$i1++;
		}
	}
		
	
	// SUB NAVIGATION
	function SubNavigation($page="",$class="")
	{
		global $_SETTINGS;
		global $_REQUEST;
		
		if($page == ""){ $page = $_REQUEST['page']; }			// IF $page EMPTY GET $_REQUEST['page']
		$parent_id=lookupDbValue('pages','parent',$page,'clean_url_name');		// GET PARENT ID
		if($parent_id != "" AND $parent_id != '0')
		{
			echo "<ul class='".$class."'>";
			$select = "SELECT * FROM pages WHERE parent='".$parent_id."' AND status='Published' AND active='1' ORDER BY sort ASC";
			$result = doQuery($select);
			//echo "<li>".$select."</li>";
			while($row = mysql_fetch_array($result))
			{
				$active = "";
				if($_REQUEST['page'] == $row['clean_url_name'])
				{
					$active = "subnav-active";
				}
				echo "<li class='subnav ".$active."'><a href='".$_SETTINGS['website'].$row['clean_url_name']."'>".$row['name']."</a></li>";
			}
			echo "</ul>";
		}
	}
	
	// GOOGLE, ETC. TRACKING CODE
	function TrackingCode()
	{
		global $_SETTINGS;
		$code = lookupDbValue('settings', 'value', 'Tracking Code', 'name');
		echo"$code";
	}
	
	
	// FACEBOOK LIKE BUTTON
	function FacebookLikeButton()
	{
		global $_SETTINGS;
		global $_REQUEST;
		echo "<iframe src='http://www.facebook.com/plugins/like.php?href=".$_SETTINGS['website']."".$_REQUEST['page']."&amp;layout=standard&amp;show_faces=false&amp;width=340&amp;action=like&amp;font=arial&amp;colorscheme=dark&amp;height=35' scrolling='no' frameborder='0' style='border:none; overflow:hidden; width:340px; height:35px;' allowTransparency='true'></iframe>";
	
	}
	
	
}
?>