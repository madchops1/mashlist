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
class Events
{
	
	/**
	 *
	 * CONSTRUCTOR
	 *
	 */
	function Events()
	{
		global $_SETTINGS;
	}
	
	/**
	 *
	 * LIST EVENTS
	 *
	 * URL format
	 * $_SETTINGS['website']/$_REQUEST['page']/category:pagenum
	 * $_SETTINGS['website']/$_REQUEST['page']/eventname
	 * 
	 */
	function DisplayEvents()
	{	
		global $_GET;
		global $_POST;
		global $_REQUEST;
		global $_SETTINGS;
		global $_SESSION;	
		
		//
		// PAGE FLAG
		//
		$flag = $_SETTINGS['events_page_clean_url'];
		if($flag == $_REQUEST['page']){
			
			//
			// DEBUG SECTION
			//
			if($_SETTINGS['debug'] == 1){
				echo "<br>PAGE: ".$_REQUEST['page']."<br>";
				echo "<br>FORM1: ".$_REQUEST['FORM1']."<br>";
			}
			
			//
			// IF FORM1 CALENDAR
			//
			if($_REQUEST['FORM1'] == 'calendar'){
				$this->DisplayCalendarOfEvents();
			}
			//
			// ELSE DISPLAY EVENTS BY DEFAULT
			//
			else {		
				echo "<div class='events'>";
								
				// CHECK FOR A CATEGORY
				if($_REQUEST['form1'] != ''){
				
					$form1Array = explode(":",$_REQUEST['FORM1']);
					$categoryName = $form1Array[0];
					$pageNum = $form1Array[1];
					$eventName = $form1Array[2];
					
					$cagegorySQL = "AND ec.name='".$categoryName."' ";
					$nameSQL = "AND ev.name='".$eventName."' ";
					
				}
				
				// PAGINATION
				$page = 1; // start page			
				$size = 4; // records per page
				$select = 	"SELECT * FROM events ".
							"WHERE events.active='1' ".
							"AND events.published='1' ".
							"".$_SETTINGS['demosqland']." ".
							"ORDER BY events.date DESC ";	
				$total_records = mysql_num_rows(doQuery($select)); // total records
				if($pageNum){ $page = (int) $pageNum; } // current page
				$pagination = new Pagination();
				$pagination->setLink("".$_SETTINGS['website']."/".$_REQUEST['page']."/".$categoryName.":%s");
				$pagination->setPage($page);
				$pagination->setSize($size);
				$pagination->setTotalRecords($total_records);
				$select2 = 	$select.$pagination->getLimitSql();				
				$result = doQuery($select);
				
				$i=0;
				while ($row = mysql_fetch_array($result)){
					
					echo "<div class='event_cont'>";
					
					echo "<div class='eventmaps' id='eventmap".$i."'></div>";
					echo "<div class='event_cont_img_box'><img src='".$_SETTINGS['website']."uploads/".$row['image']."' class='event_cont_img' alt='' /></div>";
					
					echo "<strong>".$row['title']."</strong>";
					echo "<label>".TimestampIntoDate($row['date'])."</label>";
					echo "<label>".$row['location']."</label>";
					
					echo "".truncateString($row['content'], 300, $stopanywhere=false)."";
					
					echo "<ul class='event_list'>";
					echo "<li><b>Location:</b> ".$row['location']."</li>";
					echo "<li><b>Address:</b> ".$row['address']."</li>";
					echo "<li><b>Openings:</b></li>";
					echo "<li><b>Price:</b> $".$row['price']."</li>";					
					echo "</ul>";
					
					// CHECKOUT FORM
					if($row['price'] != 0){
						echo	"<form class='product-form' method='post'>".
								"<input class='hidden' type='hidden' name='pid' value='".$product_id."' >".
								"<input type='hidden' class='qtyinput' name='qty' value='1' size='2' >";
						echo	"<input type='submit' name='ADDTOCART' class='event-submit' value='Attend This Event'>";
						echo	"<input type='button' class='event-directions' value='Get Directions'>".
								"</form>";
					} else {
						echo	"<form>";
						
						echo	"<input type='submit' name='ATTENDEVENT' class='event-submit' value='Attend This Event'>";
						echo	"<input type='button' class='event-directions' value='Get Directions'>".
								"</form>";
					}
					
					echo "</div>";
					
					$i++;
				}

				if($i==0){
					echo "There are currently no events listed";			
				}

				?>
				
				<?
				
				$navigation = $pagination->create_links();
				echo $navigation; // will draw our page navigation
			
				echo "</div>";	
				echo "<div id='map_canvas'></div>";
			}
		}
	}
		
	/**
	 *
	 * Display the main calendar
	 *
	 */	
	function DisplayCalendarOfEvents()
	{
		global $_SETTINGS;
		echo "<div id='calendar'></div>";	
	}


	function DisplaySmallCalendarOfEvents($w='300px',$h='300px')
	{
		global $_SETTINGS;
		echo "<div style='width:".$w."; height:".$h.";' id='smallcalendar'></div>";
	}
	
	
	/**
	 *
	 * Get the Javascript fot the events
	 *
	 */	
	function GetEventsJsForCalendar($frontend=true)
	{
		global $_SETTINS;

		// SELECT EVENTS
		$select = 	"SELECT * FROM events ".
					"WHERE events.active='1' ".
					"".$_SETTINGS['demosqland']." ".
					"ORDER BY events.date DESC ";
		$result = doQuery($select);			
		$num = mysql_num_rows($result);
		$i = 0;
		while($i<$num){
			$row = mysql_fetch_array($result);
			if($i != 0){ $js .= ","; }
			$js .= 	"{";
			
				// TITLE CHECK
				$js .= "title: '".$row['title']."',";
				$js .= "description: '".$row['description']."',";
				
				
				
				// GET FORMAT AND EXPLODE DATE
				$start_date = TimestampIntoDate($row['date']);
				$end_date = TimestampIntoDate($row['end_date']);
				$startArray = explode("/",$start_date);
				$endArray = explode("/",$end_date);		
				$sy = $startArray[2];
				$sm = $startArray[0];
				$sd = $startArray[1];			
				$ey = $endArray[2];
				$em = $endArray[0];
				$ed = $endArray[1];
				
				$js .= "start: new Date(".$sy.", ".$sm."-1, ".$sd."),";
				
				if($_POST['end_date'] == "0000-00-00 00:00:00" || $_POST['end_date'] == ""){
				
				} else {
					$js .= "end: new Date(".$ey.", ".$em."-1, ".$ed."),";
				}
				
				
				if($frontend == true){			
					$js .= "url: 'http://".$_SETTINGS['website'].$_SETTINGS['events_page_clean_url']."/'";
				} else {
					$js .= "url: 'http://".$_SETTINGS['website'].$_SETTINGS['events_page_clean_url']."/'";
				}
				
				
			$js .= "}";
			$i++;
		}
		
		$js .=	"";
		return $js;
		
		/*
		{
			title: 'All Day Event',
			start: new Date(y, m, 1)
		},
		{
			title: 'Long Event',
			start: new Date(y, m, d-5),
			end: new Date(y, m, d-2)
		},
		{
			id: 999,
			title: 'Repeating Event',
			start: new Date(y, m, d-3, 16, 0),
			allDay: false
		},
		{
			id: 999,
			title: 'Repeating Event',
			start: new Date(y, m, d+4, 16, 0),
			allDay: false
		},
		{
			title: 'Meeting',
			start: new Date(y, m, d, 10, 30),
			allDay: false
		},
		{
			title: 'Lunch',
			start: new Date(y, m, d, 12, 0),
			end: new Date(y, m, d, 14, 0),
			allDay: false
		},
		{
			title: 'Birthday Party',
			start: new Date(y, m, d+1, 19, 0),
			end: new Date(y, m, d+1, 22, 30),
			allDay: false
		},
		{
			title: 'Click for Google',
			start: new Date(y, m, 28),
			end: new Date(y, m, 29),
			url: 'http://google.com/'
		}
		*/
		
		
	}
	
	/**
	 *
	 * Display the map of events calendar
	 *
	 */		
	function DisplayMapOfEvents($width='100%',$height='100%')
	{
		if($_SETTINGS['default_eventsmap_width'] != "") { $width = $_SETTINGS['default_eventsmap_width']; }
		if($_SETTINGS['default_eventsmap_height'] != "") { $height = $_SETTINGS['default_eventsmap_height']; }
		echo '<div id="map_of_events" style="width:'.$width.'; height:'.$height.';"></div>';
	}
	
	/**
	 *
	 * Latest
	 *
	 */
	function latestEvents($limit="3")
	{
		global $_SETTINGS;
		global $_REQUEST;
		global $_SESSION;
		global $_GET;
		global $_POST;
				
		$select = 	"SELECT * FROM events ".
					"WHERE events.active='1' ".
					"AND events.published='1' ".
					"".$_SETTINGS['demosqland']." ".
					"ORDER BY events.date DESC ".$limit."";	
		
		$result = doQuery($select);
		$num = mysql_num_rows($result);
		
		//echo "<div class='latest-blog'>";		
					
		$i=0;
		while($i<$num){
			$row = mysql_fetch_array($result);
			
			$sel = "SELECT * FROM event_categories WHERE category_id='".$row['category_id']."' LIMIT 1";
			$res = doQuery($sel);
			$ro = mysql_fetch_array($res);

			/*
			echo "<div class='latest-post'>";
			echo "	<a href='".$eventsURL."/".$_REQUEST['page']."'>";
			echo "		<h3>".$row['title']."</h3>";
			echo "	</a>";
			
			echo "	<p>";

			echo truncate(strip_tags($row['content']), 200);

			echo "	</p>";
			echo "</div>";
			*/
			
			?>
			<span class="polaroid">
			<img src="images/gallery3.jpg" class="pic" width="105" height="85" alt="" />
			</span>
			<span class="eventer">
			<label><?=$row['title']?></label>
			<strong>London, UK</strong>
			<small><?=truncate(strip_tags($row['content']), 200) ?> <a href="#" title="read more">Read More ></a></small>
			</span>		
			<?

			$i++;
		}
			
		//echo "	<a href='".$_SETTINGS['website']."/".$_REQUEST['events_page_clean_url']."/event-alert-signup' class='brown-arrow'><span>Blog Sign-Up</span></a>";
		//echo "</div>";
		
	}		
	
	/**
	 *
	 * EVENT ALERT FORM
	 *
	 */
	function eventAlertFormAction()
	{
		global $_SETTINGS;
		global $_POST;
		global $_REQUST;
		global $_SESSION;
		
		if(isset($_POST['EVENT_ALERT_SIGNUP'])){
			// VALIDATION
			$error = 0;
			if($_POST['name'] == ""){ $error=1; }
			if($_POST['email'] == ""){ $error=1; }
			if(VerifyEmail($_POST['email']) != 1){ $error=1; }
			
			// SUCCESSFUL VALIDATION
			if($error == 0){
				$_POST = escape_smart_array($_POST);
				$select = 	"INSERT INTO blog_alert SET ".
							"blog_alert_id='',".
							"user_id='".$_SESSION['userid']."',".
							"name='".$_POST['name']."',".
							"email='".$_POST['email']."',".
							"created=NOW()".
							"".$_SETTINGS['demosql']."";
				$result = 	doQuery($select);
				
				$report = "Registration Successfull.";
				$success = "1";
				header("Location: ".$_SETTINGS['website']."/".$_REQUEST['page']."/".$categoryName.":".$pageNum.":".$eventName."/".$report."/".$success."");
				exit();
			} else {
				$report = "Please enter all *required information.";
				$success = "0";
				header("Location: ".$_SETTINGS['website']."/".$_REQUEST['page']."/".$categoryName.":".$pageNum.":".$eventName."/".$report."/".$success."");
				exit();
			}
		}
	}
	
	/**
	 *
	 * EVENT ALERT OPTOUT FORM
	 *
	 */
	function eventAlertOptOutFormAction()
	{		
		if(isset($_POST['EVENT_ALERT_OPTOUT'])){
			// VALIDATION
			$error = 0;
			if($_POST['email'] == ""){ $error=1; }
			if(VerifyEmail($_POST['email']) != 1){ $error=1; }
			
			// SUCCESSFUL VALIDATION
			if($error == 0){
				$_POST = escape_smart_array($_POST);
				$select = 	"DELETE FROM blog_alert WHERE ".
							"email='".$_POST['email']."' ".$_SETTINGS['demosqland']."";
				$result = 	doQuery($select);
				$report = "Email Removed Successsfully.";
				header("Location: ?BLOGALERTREMOVE=1&page_id=".$_REQUEST['page_id']."&CID=".$_REQUEST['CID']."&BID=".$_REQUEST['BID']."&report=".$report."&success=1");
				exit();
			} else {
				$report = "Please enter all *required information.";
				$success = "0";
				header("Location: ?BLOGALERTREMOVE=1&page_id=".$_REQUEST['page_id']."&CID=".$_REQUEST['CID']."&BID=".$_REQUEST['BID']."&name=".$_POST['name']."&email=".$_POST['email']."&report=".$report."&success=0");
				exit();
			}
		}
	}
	
	/**
	 *
	 * EVENT NAVIGATION
	 *
	 */
	function eventNav()
	{
		global $_SESSION;
		global $_SETTINGS;
		global $_REQUEST;
		
		echo "<div class='events-navigation'>";
		
		// DISPLAY MAIN NAVIGATION
		echo	"<ul class='event-nav'>".
					"<li><a class='' href='".$_SETTINGS['website'].$_REQUEST['page']."'>All Events</a></li>".
					"<li><a class='' href='".$_SETTINGS['website'].$_REQUEST['page']."/calendar'>Calendar</a></li>".
					"<li><a class='' href='".$_SETTINGS['website'].$_REQUEST['page']."/eventalertsignup'>Event Alerts</a></li>".
					"<li><a class='' href='".$_SETTINGS["website"].$_REQUEST['page']."admin/modules/events/event_rss.php' target='_blank' class='navlink rsslink'>RSS</a></li>".
				"</ul>";
		
		// DISPLAY SEARCH FORM
		echo 	"<form class='events-search'>".
					"<input type='text' id='' name='keyword' value='' >".
					"<input type='submit' id='' name='submit' value='Search' >".
				"</form>";
		
		echo "</div>";
	}
		
	/**
	 *
	 * DISPLAY EVENT ALERT FORM
	 *
	 */	
	function eventAlertForm()
	{
		global $_REQUEST;
		global $_SETTINGS;
		
		echo 	"<div class='event-alert-form wes-theme-form-wrapper'>".
				"	<a name='alert-form wes-theme-anchor' id='alert-form'></a>".
				"	<form action='' method='post' class='wes-theme-form event-form'>".
				"		<p>Event alerts notify you via email when there are new events and important updates!</p>".
				"		<p>".
				"			<label>*Email</label>".
				"			<input type='text' name='email' value='".$_REQUEST['email']."' />".
				"		</p>".
				"		<p>".
				"			<label>*Name</label>".
				"			<input type='text' name='name' value='".$_REQUEST['name']."' />".
				"		</p>".
				"		<p>".
				"			<label>&nbsp;</label>".
				"			<input type='submit' name='EVENT_ALERT_SIGNUP' value='Subscribe to Event Alerts' />".
				"		</p>".
				"	</form>".
				"</div>";
	}

	/**
	 *
	 * DISPLAY EVENT ALERT FORM
	 *
	 */	
	function eventAlertOptoutForm()
	{
		
		echo 	"<div class='event-alert-form wes-theme-form-wrapper'>".
				"	<a name='alert-form wes-theme-anchor' id='alert-optout-form'></a>".
				"	<form action='' method='post' class='wes-theme-form event-form'>".
				"		<p>To stop recieving event alerts enter your email below</p>".
				"		<p>".
				"			<label>*Email</label>".
				"			<input type='text' name='email' value='".$_REQUEST['email']."' />".
				"		</p>".
				"		<p>".
				"			<label>&nbsp;</label>".
				"			<input type='submit' name='EVENT_ALERT_SIGNUP' value='Opt Out of Event Alerts' />".
				"		</p>".
				"	</form>".
				"</div>";
	}	
	
	
	
	/**
	 *
	 * DISPLAY EVENT CATEGORIES
	 *
	 */	
	function displayEventCategories()
	{
		global $_SETTINGS;
		global $_REQUEST;
		
		// IF FORM1
		if($_REQUEST['FORM1'] != ''){
			$form1Array = explode(":",$_REQUEST['FORM1']);
			$categoryName = $form1Array[0];
		}
		
		echo "<div class='blogcategories'>";
		echo "	<h2>Categories</h2>";

		$select = 	"SELECT * FROM event_categories WHERE ".
					"active='1' ".
					"".$_SETTINGS['demosqland']." ".
					"ORDER BY sort_level ASC";
		$result = doQuery($select);
		$num = mysql_num_rows($result);
		$i = 0;

		// SINGLE LEVEL CATEGORIES (no parents or sub cats)
		echo "<ul>";
		
		// ALL CATEGORY LINK
		echo 	"<li>".
				"<a class='".$active."' href='".$_SETTINGS['website']."/".$_REQUEST['page']."'>All Events<span>(".$allEventNum.")</span></a>".
				"</li>";
		
		// LOOP CATEGORIES
		while($i<$num){
			$row = mysql_fetch_array($result);
				echo 	"<li>".
						"<a class='".$active."' href='".$_SETTINGS['website']."/".$_REQUEST['page']."'>".$row['name']."<span>(".$thisEventNum.")</span></a>".
						"</li>";
			$i++;
		}

		echo "</ul>";
		echo "</div>";		
	}
	
	/**
	 *
	 * Return Event Content
	 * If $i is not 0 then there are multiple events being listed most likely so content is trunctated.
	 *
	 */
	function eventContent($event,$i)
	{
		global $_REQUEST;
		global $_SETTINGS;
		
		echo "<div class='event-content'>";
		
		if($i != 0){
			echo 	"".$this->truncate($event['content'], 250, false)."".
					"<a class='event-more' href='".$_SETTINGS['website']."/".$_REQUEST['page']."/".$event['name']."' >".
					"<span>More</span>".
					"</a>";
		} else {
			echo $event['content'];
		}
		
		echo "</div>";
	}
	
	/**
	 *
	 * Return Category Title
	 *
	 */
	function getCategoryTitle($id)
	{
		global $_SETTINGS;
		$select = 	"SELECT * FROM event_category WHERE ".
					"category_id='".$id."' ".
					"".$_SETTINGS['demosqland']."";
		$result = doQuery($select);
		$row = mysql_fetch_array($result);
		return $row["title"];
	}

	/**
	 *
	 * Return Event Details: Location, Date, Time
	 *
	 */	
	function getDetails($id)
	{
	
		$date = lookupDbValue('events', 'date', $id, 'event_id');
		$endDate = lookupDbValue('events', 'end_date', $id, 'event_id');
		$location = lookupDbValue('events', 'location', $id, 'event_id');
		
		$dateString = TimestampIntoDate($date);
		$timeString = TimestampIntoTime($date);
		
		$endDateString = TimestampIntoDate($endDate);
		$endTimeString = TimestampIntoTime($endDate);
		
		$dateString = "<ul class='event-date'>";
		$dateString .= "<li><label>Location:</label> ".$loctaion."</li>";
		$dateString .= "<li><label>Date:</label> ".$dateString."</li>";
		$dateString .= "<li><label>Time:</label> ".$timeString."</li>";
		
		if($enddate != '0000-00-00 00:00:00'){
			$dateString .= "<li><label>End Date:</label> ".$endDateString."</li>";
		}
		$dateString .= "<li><label>End Time:</label> ".$endTimeString."</li>";
		
		$dateString .= "</ul>";
		
		return $dateString;
		
	}
	
	/**
	 *
	 * Return Event Title
	 *
	 */
	function getTitle($id)
	{
		global $_SETTINGS;
		$select = 	"SELECT * FROM events WHERE ".
					"event_id='".$id."' ".
					"".$_SETTINGS['demosqland']."";
		$result = doQuery($select);
		$row = mysql_fetch_array($result);
		return $row["title"];
	}
	
	/**
	 *
	 * Truncate String
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
		
	/**
	 *
	 * RSS FEED
	 *
	 */	
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
	
	/**
	 *
	 * RSS function
	 *
	 */	
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
						"<generator>KSD's Wes&trade; Blog</generator>";			
		return $channel;
	}

	/**
	 *
	 * RSS function
	 *
	 */	
	function getItems()
	{
		global $_SETTINGS;
		global $_REQUEST;
		$sel = "SELECT * FROM events WHERE status='Published' ".$_SETTINGS['demosqland']." ORDER BY `date` DESC LIMIT 5";
		$result = doQuery($sel);
		$items = '';
		
		while($row = mysql_fetch_array($result)){
			$items .= 	"<item>".
						"<title>".$row["title"]."</title>".
						"<link>".$_SETTINGS['website']."".$_SETTINGS['blog_url']."&amp;BID=".$row['blog_id']."</link>".
						"<description>".htmlentities(strip_tags($this->truncate($row['content'],300)))."</description>".
						"<pubDate>".$row['created']."</pubDate>".
						"<guid>".$_SETTINGS['website']."".$_SETTINGS['blog_url']."&amp;BID=".$row['blog_id']."</guid>".
						"</item>";	
		}
		return $items;
	}
	
	/**
	 *
	 * ATTENDING COUNT
	 *
	 */
	function AttendingCount($eid){
		
		$select = "SELECT * FROM event_rsvps WHERE event_id='".$eid."' AND active='1'";
		$result = doQuery($select);
		$num = mysql_num_rows($result);
		return $num;
	
	}
}
?>
