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
* 	wes Version 1.0 Copyright 2010 Karl Steltenpohl Development LLC. All Rights Reserved.
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
class Blog {
	
	//var $theme;
	//var $website_path;
	
	function Blog()
	{
		global $_SETTINGS;
		//$this->theme = $this->activeTheme();
		//$this->website_path = $_SETTINGS['website_path'];
	}
	
	/** Main Blog Page
	 *
	 *
	 *
	 */
	function blogposts()
	{	
		global $_GET;
		global $_POST;
		global $_REQUEST;
		global $_SETTINGS;
		global $_SESSION;
		
		echo '<div class="blog">';
		$page = 1;
		
			// List all or single post
			if ($_GET['FORM1']!='')
			{
				$FORM1 = explode(',',$_GET['FORM1']);
				
				if($FORM1[0] != ""){
					@$cid = $FORM1[0];
					@$categorySql1 = "LEFT JOIN blog_category_relational c ON c.blog_id=b.blog_id LEFT JOIN blog_category d ON d.blog_category_id=c.category_id ";
					@$categorySql2 = " AND d.blog_category_id='".$cid."' ";
				}
				
				if($FORM1[1] != ""){
					@$entrySql = " AND b.blog_id='".$FORM1[1]."'";
					@$bid = $FORM1[1];
				}
				
				if($FORM1[2] != ""){
					@$page = $FORM1[2];
				}
			}
			
			$status = " b.status='Published' AND b.active='1' ";
			$dater = " AND b.date<=NOW()";
			
			// how many records per page
			$size = 4;	 
			// now use this SQL statement to get records from your table
			$SQL = 	"SELECT *,b.blog_id as bblog_id FROM blog b ".
					$categorySql1.
					"WHERE $status $categorySql2 $entrySql $dater ".$_SETTINGS['demosqland']." ORDER BY b.date DESC ";	
			$total_records = mysql_num_rows(doQuery($SQL)); 
			// we get the current page from $_GET
			if ($page){ $page = (int) $page; }
			// create the pagination class
			$pagination = new Pagination();
			$pagination->setLink("".$_SETTINGS['website'].$_REQUEST['page']."/".$cid.",".$bid.",%s");
			$pagination->setPage($page);
			$pagination->setSize($size);
			$pagination->setTotalRecords($total_records);
			// now use this SQL statement to get records from your table
			$SQL .= $pagination->getLimitSql();	
			
			//echo"<Br>$SQL<br>";
			
			$result = doQuery($SQL);
			
			$i=0;
			while ($row = mysql_fetch_array($result)) 
			{
				echo '<div class="blogpost" style="clear:both;">';
						$this->blogtitle($row,$i);
						$this->bloghead($row);
						$this->blogcontent($row);
						$this->blogcomments($row);
				echo '</div>';
				$i++;
			}			
			if($i==0){			
				echo '<p>There are no posts.</p>';					
			}			
			
			$navigation = $pagination->create_links();
			echo $navigation; // will draw our page navigation
			
		echo '</div>';
	}
		
	/*** Latest ***/
	function latestposts()
	{
		global $_SETTINGS;
		global $_REQUEST;
		global $_SESSION;
		global $_GET;
		global $_POST;
		
		// if user, hide un-approved
		if ($_SESSION['session']->admin->auth!=1)
		{
			$approved = " approved='1' ";
		} else {
			$approved = " 1=1 ";
		}
		
		$dater = " AND created<=NOW()";
		
		$select = 	"SELECT * FROM blog WHERE $approved $dater ".$_SETTINGS['demosqland']." ORDER BY created DESC LIMIT 3";
		
		$result = doQuery($select);
		$num = mysql_num_rows($result);
		///echo "NUM:$num, SELECT:$select";
		
		echo '<div class="latest-blog">';		
			
			$sel1 = "SELECT * FROM settings WHERE name='Blog URL'";
			$result1 = doquery($sel1);
			$row1 = mysql_fetch_array($result1);
			$blogurl = $row1['value'];
			
			$i=0;
			while($i<$num){
				$row = mysql_fetch_array($result);
				
				$sel = "SELECT * FROM blog_category_relational WHERE blog_id='".$row['blog_id']."' LIMIT 1";
				$res = doQuery($sel);
				$ro = mysql_fetch_array($res);
				
				echo '<div class="latest-post">';
				echo '<a href="'.$_SETTINGS['website'].$_REQUEST['page'].'/'.$ro['category_id'].','.$row['blog_id'].',">';
				echo '<h3>'.$row['title'].'</h3>';
				echo '</a>';
				echo '<p>';
				
				//echo truncate(strip_tags($row['content']), 200);
				echo smarty_modifier_html_substr($row['content'], 300);
				
				echo '</p>';
				echo '</div>';
				
				$i++;
			}
			
		echo '	<p class="spacer"></p>';
		echo '</div>';
	}		
		
	function blogalertformAction()
	{
		global $_SETTINGS;
		if(isset($_POST['ALERTSIGNUP']))
		{
			/*** VALIDATION ***/
			$error = 0;
			if($_POST['name'] == ""){ $error=1; }
			if($_POST['email'] == ""){ $error=1; }
			if(VerifyEmail($_POST['email']) != 1){ $error=1; }
			
			if($error == 0){
				$_POST = escape_smart_array($_POST);
				$select = 	"INSERT INTO blog_alert SET ".
							"blog_alert_id='',".
							"name='".$_POST['name']."',".
							"email='".$_POST['email']."',".
							"created=NOW()".
							"".$_SETTINGS['demosql']."";
				$result = 	doQuery($select);
				$report = "Registration Successfull.";
				header("Location: ?BlogALERTSIGNUP=1&page_id=".$_REQUEST['page_id']."&CID=".$_REQUEST['CID']."&BID=".$_REQUEST['BID']."&report=".$report."&success=1");
				exit();
			} else {
				$report = "Please enter all *required information.";
				header("Location: ?BlogALERTSIGNUP=1&page_id=".$_REQUEST['page_id']."&CID=".$_REQUEST['CID']."&BID=".$_REQUEST['BID']."&name=".$_POST['name']."&email=".$_POST['email']."&report=".$report."&success=0");
				exit();
			}
		}
		if(isset($_POST['ALERTREMOVE']))
		{
			/*** VALIDATION ***/
			$error = 0;
			if($_POST['email'] == ""){ $error=1; }
			if(VerifyEmail($_POST['email']) != 1){ $error=1; }
			
			if($error == 0){
				$_POST = escape_smart_array($_POST);
				$select = 	"DELETE FROM blog_alert WHERE ".
							"email='".$_POST['email']."' ".$_SETTINGS['demosqland']."";
				$result = 	doQuery($select);
				$report = "Email Removed Successsfully.";
				header("Location: ?BlogALERTREMOVE=1&page_id=".$_REQUEST['page_id']."&CID=".$_REQUEST['CID']."&BID=".$_REQUEST['BID']."&report=".$report."&success=1");
				exit();
			} else {
				$report = "Please enter all *required information.";
				header("Location: ?BlogALERTREMOVE=1&page_id=".$_REQUEST['page_id']."&CID=".$_REQUEST['CID']."&BID=".$_REQUEST['BID']."&name=".$_POST['name']."&email=".$_POST['email']."&report=".$report."&success=0");
				exit();
			}
		}
	}
	function blogcategories($heading=false)
	{
		global $_SETTINGS;
		global $_REQUEST;
		
		echo '<div class="blogcategories">';
		if($heading == false){
			echo '	<h2><span>Categories</span></h2>';
		} else {
			echo '	<h2><span>'.$heading.'</span></h2>';
		}	
			// COUNT ALL BLOGS
			$res1 = doQuery("SELECT * FROM blog where status='Published' AND created<=NOW() ".$_SETTINGS['demosqland']."");
			$num = mysql_num_rows($res1);
			
			echo '<ul>'; // START LIST			
			echo '	<li>';
			echo '		<a class="" href="'.$_SETTINGS['website'].$_REQUEST['page'].'" class="all-entries">';
			echo '			View all Posts <span>('.$num.')</span>';
			echo '		</a>';
			echo '	</li>';
			
			// LOOP TOP LEVEL CATS
			// THERE ARE ONLU TOP LEVEL CATS
			$res = doQuery("SELECT * FROM blog_category WHERE parent_id=0 ".$_SETTINGS['demosqland']." ORDER BY sort ASC");
			while ($row = mysql_fetch_Array($res))
			{
				$res2 = doquery("SELECT * from blog_category_relational c LEFT JOIN blog b ON b.blog_id=c.blog_id where c.category_id='".$row["blog_category_id"]."' AND b.status='Published' AND b.date<=NOW() ".$_SETTINGS['demosqland']."");
				$num = sprintf("%d",mysql_num_rows($res2));
				
				echo '<li>';
				echo '	<a class="" href="'.$_SETTINGS['website'].$_REQUEST['page'].'/'.$row["blog_category_id"].',," class="navlink">';
				echo '		'.$row["title"].' <span>('.$num.')</span>';
				echo '	</a>';				
				echo '</li>';
			}
		echo '</div>';
	}
	function blogtitle($blog,$i)
	{
		global $_SETTINGS;
		global $_REQUEST;
		
		echo '<h3 class="blog-title">';
		echo '<a href="'.$_SETTINGS['website'].$_REQUEST['page'].'/,'.$blog['bblog_id'].',">'.$blog['title'].'</a>';
		echo '</h3>';		
	}
	
	function bloghead($blog, $separator=" | ")
	{
		global $_SETTINGS;		
		echo '<div class="blog-head">';			
			$author 		= lookupDbValue('admin', 'name', $blog['admin_id'], 'admin_id');
			if($author != ""){ 
			echo "Written By: $author";
			echo "<br>";
			}
			
			$categories 	= trim($this->list_categories($blog['bblog_id']),",");
			$date 			= FormatTimeStamp($blog['created']);

			$sql 			= "SELECT * FROM blog_comment WHERE approved='1' AND blog_id='".$blog['bblog_id']."' ".$_SETTINGS['demosqland']."";
			$res 			= doQuery($sql);
			$comment_num	= mysql_num_rows($res);
			
			echo "$date";
			echo "<br>";
			echo "$categories"; 		
			if($this->allowComments($blog['bblog_id'])){
				echo "<br>";
				echo "<a href='".$_SETTINGS['website'].$_SETTINGS['blog_page_clean_url']."/".$_REQUEST['cid'].",".$blog['bblog_id'].",#comments-anchor'>".$comment_num."</a> Comments";
			}			
		echo '</div>';		
	}
	
	function blogcontent($blog)
	{	
		global $_GET;
		global $_SETTINGS;
		$truncate = 1;
		
		echo '<div class="blog-content">';	
			
			if ($_GET['FORM1']!='')
			{
				$FORM1 = explode(',',$_GET['FORM1']);				
				if($FORM1[1] != ""){
					//@$entrySql = " AND b.blog_id='".$FORM1[1]."'";
					$truncate = 0;
				}
			}
		
			//if($truncate == 1){
			//echo 	"".$this->smarty_modifier_html_substr($blog['content'], 1000)."";
			//echo 	"<a class=\"blog-more\" href=\"".$_SETTINGS['website'].$_REQUEST['page']."/,".$blog['bblog_id'].",\"><span>Read More &raquo;</span></a><br style='clear:both;'>";
			//} else {
				echo $blog['content'];
			//}		
		echo '</div>';		
	}
	
	
	
	function blogcomments($blog)
	{
		global $_GET;
		global $_SETTINGS; 
		
		if ($_GET['FORM1']!='')
		{
			$FORM1 = explode(',',$_GET['FORM1']);			
			if($FORM1[1] != ""){
				if($this->allowComments($FORM1[1])){
					$this->commentform($blog);
					$this->comments($blog);
				}				
			}
		} else {
			if($this->allowComments($blog['blog_id'])){
				$this->joinConversation($blog);
			}
		}
	}
	
	function commentformAction()
	{
		global $_SETTINGS;
		global $_SERVER;
		global $_SESSION;
		global $_POST;
		global $_REQUEST;
		
		if(isset($_POST['POSTBlogCOMMENT']))
		{
			
			
			
			$error = 0;

			// Captcha Validation
			global $recaptchaprivatekey;
			$privatekey = $recaptchaprivatekey;
			$resp = recaptcha_check_answer ($privatekey,
										$_SERVER["REMOTE_ADDR"],
										$_POST["recaptcha_challenge_field"],
										$_POST["recaptcha_response_field"]);
			if (!$resp->is_valid) {
				$error = 1; $_REQUEST['SUCCESS'] = 0; $_REQUEST['REPORT'] = "Verification code is incorrect_";
			} else {
				$error = 0;
			}
			
			
			if($_POST['name'] == ""){ 		$error=1; $_REQUEST['SUCCESS'] = 0; $_REQUEST['REPORT'] = "Please enter your name_"; }
			if($_POST['email'] == ""){ 		$error=1; $_REQUEST['SUCCESS'] = 0; $_REQUEST['REPORT'] = "Please enter your email, it will never be displayed_"; }
			if($_POST['content'] == ""){ 		$error=1; $_REQUEST['SUCCESS'] = 0; $_REQUEST['REPORT'] = "Please enter a comment_"; }
			if(VerifyEmail($_POST['email']) != 1){ 	$error=1; $_REQUEST['SUCCESS'] = 0; $_REQUEST['REPORT'] = "Please enter a valid email_"; }
			
			if($error == 0){
				$_POST = escape_smart_array($_POST);
				$select = 	"INSERT INTO blog_comment SET ".
							"comment_id='',".
							"name='".$_POST['name']."',".
							"email='".$_POST['email']."',".
							"blog_id='".$_REQUEST['bid']."',".
							"content='".$_POST['content']."',".
							"approved='0',".
							"created=NOW()".
							"".$_SETTINGS['demosql']."";
				$result = 	doQuery($select);
				$report = "Comment submitted successfully_ Your comment will be published once it passes approval_";
				
				// EMAIL NOTIFICATION
				$to = $_SETTINGS['email'];
				$from = "".$_SETTINGS['siteName']." <".$_SETTINGS['automated_reply_email'].">";
				$subj = "Blog Comment | ".$_SETTINGS['siteName'].".";
				$mess = "Someone has commented on your blog article at ".$_SETTINGS['siteName'].".".
					"<br>View and approve the comment for viewing in the website's administration. Click on the link below.".
					"<br><a href=\"".$_SETTINGS['website']."admin\">".$_SETTINGS['website']."admin</a><br>";
				sendEmail($to,$from,$subj,$mess);

				// Send To Author
				$authorid = lookupDbValue('blog', 'admin_id', $_REQUEST['bid'], 'blog_id');
				$authoremail = lookupDbValue('admin', 'email', $authorid, 'admin_id');
				if($authoremail != $superemail)
				{
					$to = $authoremail;
					sendEmail($to,$from,$subj,$mess);
				}

				header("Location: ".$_SETTINGS['website'].$_REQUEST['page']."/".$_REQUEST['cid'].",".$_REQUEST['bid']."/".$report."/1/0#successbox");
				exit();
			}
		}
	}
	
	function commentform($blog)
	{
		
		global $_SETTINGS;
		global $_SESSION;
		global $_POST;
		
		// List all or single post
		if ($_GET['FORM1']!='')
		{
			$FORM1 = explode(',',$_GET['FORM1']);
			
			if($FORM1[0] != ""){
				@$cid = $FORM1[0];
				@$categorySql1 = "LEFT JOIN blog_category_relational c ON c.blog_id=b.blog_id LEFT JOIN blog_category d ON d.blog_category_id=c.category_id ";
				@$categorySql2 = " AND d.blog_category_id='".$cid."' ";
			}
			
			if($FORM1[1] != ""){
				@$entrySql = " AND b.blog_id='".$FORM1[1]."'";
				@$bid = $FORM1[1];
			}
			
			if($FORM1[2] != ""){
				@$page = $FORM1[2];
			}
		}
		
		echo '	<div class="commentform-box">';
		
			report($_REQUEST['REPORT'],$_REQUEST['SUCCESS']);
			
			echo '	<a name="comments-anchor" id="comments-anchor"></a>';
			
			// IF LOGIN IS REQUIRED
			if($_SETTINGS['blog_comment_require_login'] == '1' and $_SESSION['UserAccount']['userid'] == ''){
				
				$UserAccount = new UserAccounts;
				$UserAccount->LoginForm();				
				
			} else {
			
				echo '	<h3 class="blog-commentform-title">Leave a Comment</h3>
					<form action="#errorbox" class="moduleform" method="post">';
				
				report($_REQUEST['report'],$_REQUEST['success']);
				
				echo '	<p>
						<label>*Email <small>(Required but never displayed)</small></label>
						<input type="text" name="email" value="'.$_REQUEST['email'].'" />
						</p>';
				
				echo '	<p>
						<label>*Name</label>
						<input type="text" name="name" value="'.$_REQUEST['name'].'" />
						</p>';
						
				
				
				echo '	<p>
						<label>*Comment</label>
						<textarea class="blog-comment-textarea" name="content">'.$_REQUEST['content'].'</textarea>
						</p>';
						
				// CAPTCHA
				echo "<br><div class='blog-captcha'>";
				global $recaptchapublickey;
				echo recaptcha_get_html($recaptchapublickey);
				echo "</div><br>";
				
				echo '	<p class="submit-button">
						<label>&nbsp;</label>
						<input type="hidden" value="'.$cid.'" name="cid">
 						<input type="hidden" value="'.$bid.'" name="bid">
						<input type="submit" value="Submit Comment" name="POSTBlogCOMMENT" class="submit button Contact Form-form-submit">
						</p>';
				echo '</form>';
			
			}
			
		echo "</div>";
	}
	
	function comments($blog)
	{
		global $_SETTINGS;
		$select = "SELECT * FROM blog_comment WHERE approved='1' AND blog_id='".$blog['bblog_id']."' ".$_SETTINGS['demosqland']." ORDER BY created DESC";
		$result = doQuery($select);		
		echo '<h3 class="blog-comments-title">Comments</h3>';		
		$i=0;
		while($rowa = mysql_fetch_array($result))
		{	
			if($i&1){ $odd = "commentodd"; } else { $odd = ""; } 
			echo '		<div class="blog-comment '.$odd.' moduleform" style="min-height:0px;">
						<div class="blog-comment-head">
							<b>Comment By: '.$rowa['name'].'</b><br>
							Posted On: '.FormatTimeStamp($rowa['created']).'
						</div>
						<div class="blog-comment-content">
							'.nl2br($rowa['content']).'
						</div>
					</div>';			
			$i++;
		}// End while		
		if($i==0){ echo 'There are no comments.'; }
	}
	
	function joinConversation($blog)
	{
		global $_SETTINGS;
		$select = "SELECT * FROM blog_comment WHERE approved='1' AND blog_id='".$blog['bblog_id']."' ".$_SETTINGS['demosqland']." ORDER BY created DESC";
		$result = doQuery($select);
		$num 	= mysql_num_rows($result);
		if($num == 0)
		{
			$linktext = "Be the first to comment!";
		} else {
			$linktext = "Join the conversation!";
		}
		echo "	<div class='moduleform' style='min-height:0px;'>
				<h4>Join the Conversation</h4>
				<p>There are ".$num." comments. <a href='".$_SETTINGS['website'].$_SETTINGS['blog_page_clean_url']."/".$_REQUEST['cid'].",".$blog['blog_id'].",#comments-anchor''>".$linktext."</a></p>
			</div>";	
	}
	/** List all categories a post belongs to
	 *
	 *
	 *
	 */
	function list_categories($pid)
	{
		global $_SETTINGS;
		if($_REQUEST['CID'] != ""){
			$select = "SELECT * FROM blog_category WHERE blog_category_id='".$_REQUEST['CID']."' ".$_SETTINGS['demosqland']." LIMIT 1";
			$result = mysql_query($select);
			$row = mysql_fetch_array($result);
			$linktext = $this->getcat($row['blog_category_id']);
		} else {
			$select = "SELECT * FROM blog_category_relational WHERE blog_id='".$pid."' ".$_SETTINGS['demosqland']." LIMIT 1";
			$result = mysql_query($select);
			$row = mysql_fetch_array($result);
			$linktext = $this->getcat($row['category_id']);
			//$string = "<a href=\"?page_id=".$_REQUEST['page_id']."&CID=".$row['category_id']."\">".$this->getcat($row['category_id'])."</a>";
		}
		
		if($linktext == ""){
			//$string = "Uncategorized";
		} else {
			$string = "<a href=\"?page_id=".$_REQUEST['page_id']."&CID=".$row['blog_category_id']."\">".$linktext."</a>";
			//$string = "// <a href=\"".$_SETTINGS['website'].$_REQUEST['page']."/".$row['blog_category_id']."\">".$linktext."</a>";
		}
		
		return $string;
	}
	
	/** Return Category Title
	 * return name
	 *
	 *
	 */
	function getcat($id)
	{
		global $_SETTINGS;
		$selecter = "SELECT * FROM blog_category WHERE blog_category_id='".$id."' ".$_SETTINGS['demosqland']."";
		$resulter = mysql_query($selecter) or die("err ( $selecter )");
		$rower = mysql_fetch_array($resulter);
		return $rower["title"];
	}// end function
	
	
	/** Breadcrumbs
	 *
	 *
	 *
	 */
	function breadcrumbs()
	{
		global $_POST;
		global $_SETTINGS;
		global $_REQUEST;
		
		// List all or single post
		if ($_GET['FORM1']!='')
		{
			$FORM1 = explode(',',$_GET['FORM1']);
			
			if($FORM1[0] != ""){
				//@$categorySql = " AND c.category_id='".$FORM1[0]."' AND b.blog_id=c.blog_id ";
				@$cid = $FORM1[0];
			}
			
			if($FORM1[1] != ""){
				//@$entrySql = " AND b.blog_id='".$FORM1[1]."'";
				@$bid = $FORM1[1];
			}
			
			if($FORM1[2] != ""){
				@$page = $FORM1[2];
			}
		
		
				
			echo "<p class='breadcrumbs blogbreadcrumbs'>";			
			echo "<a href='".$_SETTINGS['website']."".$_SETTINGS['blog_page_clean_url']."'>".ucwords($_REQUEST['page'])." Home</a>";
			if($cid != ''){
				echo " / <a href=''>".$this->getcat($cid)."</a>";
			}
			if($bid != ''){
				echo " / <a href=''>".$this->getblog($bid)."</a>";
			}			
			echo "</p>";
		}
		
		
	}
	
	
	/** Return Blog Title
	 *
	 *
	 */
	function getblog($id)
	{
		global $_SETTINGS;
		$selecter = "SELECT * FROM blog WHERE blog_id='".$id."' ".$_SETTINGS['demosqland']."";
		$resulter = mysql_query($selecter) or die("err ( $selecter )");
		$rower = mysql_fetch_array($resulter);
		return $rower["title"];
	}// end function
	
	/** Truncate String 
	 * Add Elipses...
	 *
	 */
	function truncate($string, $length, $stopanywhere=false)
	{
		//truncates a string to a certain char length, stopping on a word if not specified otherwise.
		if (strlen($string) > $length) {
			//limit hit!
			$string = substr($string,0,($length -3));
			if ($stopanywhere) {
				//stop anywhere
				$string .= '...';
			} else{
				//stop on a word.
				$string = substr($string,0,strrpos($string,' ')).'...';
			}
		}
		return $string;
	}
	
	/** RETURN TRUE IF Blog ALLOWS COMMENTS ***/
	function allowComments($bid)
	{
		global $_SETTINGS;
		$select = "SELECT comments FROM blog WHERE blog_id='".$bid."' ".$_SETTINGS['demosqland']."";
		$result = doQuery($select);
		$row = mysql_fetch_array($result);
		$comments = $row['comments'];
		if($comments == "1"){ return true; } else { return false; }	
	}
		
	function RSSlink(){
		global $_SETTINGS;
		echo '<a href="'.$_SETTINGS['website'].'admin/modules/blog/blog_rss.php" target="_blank" class="navlink rsslink">RSS Feed</a>';
	}
	
	function displayRSS()
	{
		header("Content-Type: application/rss+xml");	
		echo "<?xml version=\"1.0\"?>";
		echo "<rss version=\"2.0\">";
		echo "<channel>";
		echo $this->getChannel();
		echo $this->getItems();
		echo "</channel>";
		echo "</rss>";		
		exit();
	}
	
	function getChannel()
	{
		global $_SETTINGS;
			$channel = 	"<title>".$_SETTINGS['siteName']."</title>".
						"<link>".$_SETTINGS['website']."</link>".
						"<description>".$_SETTINGS['siteName']."</description>".
						"<language>en-us</language>".
						"<pubDate>".date("Y-m-d")."</pubDate>".
						"<lastBuildDate>".date("Y-m-d")."</lastBuildDate>".
						"<docs>http://blogs.law.harvard.edu/tech/rss</docs>".
						"<generator>WES&trade; Website Enterprise Software, Karl Steltenpohl Development LLC Blog</generator>";			
		return $channel;
	}

	function getItems()
	{
		global $_SETTINGS;
		global $_REQUEST;
		$sel = "SELECT * FROM blog WHERE status='Published' AND active='1' AND date<=NOW() ".$_SETTINGS['demosqland']." ORDER BY date DESC LIMIT 5";
		$result = doQuery($sel);
		$items = '';
		
		$sel1 = "SELECT * FROM settings WHERE name='Blog URL' ".$_SETTINGS['demosqland']."";
		$result1 = doquery($sel1);
		$row1 = mysql_fetch_array($result1);
		$blogurl = $row1['value'];
		
		while($row = mysql_fetch_array($result))
		{
			$items .= 	"<item>".
						"<title>".$row["title"]."</title>".
						"<link>".$_SETTINGS['website'].$blogurl."&amp;BID=".$row['blog_id']."</link>".
						"<description>".htmlentities(strip_tags($this->truncate($row['content'],300)))."</description>".
						"<pubDate>".$row['created']."</pubDate>".
						"<guid>".$_SETTINGS['website'].$blogurl."&amp;BID=".$row['blog_id']."</guid>".
						"</item>";	
		}
		return $items;
	}

	/** Cut a string preserving any tag nesting and matching
	* Smarty plugin
	*
	-------------------------------------------------------------
	* File: modifier.html_substr.php
	* Type: modifier
	* Name: html_substr
	* Version: 1.0
	* Date: June 19th, 2003
	* Purpose: .
	* Install: Drop into the plugin directory.
	* Author: Original Javascript Code: Benjamin Lupu <lupufr@aol.com>
	* Translation to PHP & Smarty: Edward Dale <scompt@scompt.com>
	*
	-------------------------------------------------------------
	*/
	function smarty_modifier_html_substr($string, $length)
	{
		if( !empty( $string ) && $length>0 ) {
		$isText = true;
		$ret = "";
		$i = 0;

		$currentChar = "";
		$lastSpacePosition = -1;
		$lastChar = "";

		$tagsArray = array();
		$currentTag = "";
		$tagLevel = 0;

		$noTagLength = strlen( strip_tags( $string ) );

		// Parser loop
		for( $j=0; $j<strlen( $string ); $j++ ) {

		$currentChar = substr( $string, $j, 1 );
		$ret .= $currentChar;

		// Lesser than event
		if( $currentChar == "<") $isText = false;

		// Character handler
		if( $isText ) {

		// Memorize last space position
		if( $currentChar == " " ) { $lastSpacePosition = $j; }
		else { $lastChar = $currentChar; }

		$i++;
		} else {
		$currentTag .= $currentChar;
		}

		// Greater than event
		if( $currentChar == ">" ) {
		$isText = true;

		// Opening tag handler
		if( ( strpos( $currentTag, "<" ) !== FALSE ) &&
		( strpos( $currentTag, "/>" ) === FALSE ) &&
		( strpos( $currentTag, "</") === FALSE ) ) {

		// Tag has attribute(s)
		if( strpos( $currentTag, " " ) !== FALSE ) {
		$currentTag = substr( $currentTag, 1, strpos( $currentTag, " " ) - 1 );
		} else {
		// Tag doesn't have attribute(s)
		$currentTag = substr( $currentTag, 1, -1 );
		}

		array_push( $tagsArray, $currentTag );

		} else if( strpos( $currentTag, "</" ) !== FALSE ) {

		array_pop( $tagsArray );
		}

		$currentTag = "";
		}

		if( $i >= $length) {
		break;
		}
		}

		// Cut HTML string at last space position
		if( $length < $noTagLength ) {
		if( $lastSpacePosition != -1 ) {
		$ret = substr( $string, 0, $lastSpacePosition );
		} else {
		$ret = substr( $string, $j );
		}
		}

		// Close broken XHTML elements
		while( sizeof( $tagsArray ) != 0 ) {
		$aTag = array_pop( $tagsArray );
		$ret .= "</" . $aTag . ">\n";
		}

		} else {
		$ret = "";
		}

		return( $ret );
	}	
}
?>
