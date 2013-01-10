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
global $_SETTINGS;
global $_REQUEST;




/* EVENTS 
   Is used for the events section only
*/
if($_REQUEST['page'] == $_SETTINGS['events_page_clean_url']){
?>
	<link rel='stylesheet' type='text/css' href='<?=$_SETTINGS['website'] ?>admin/modules/events/scripts/fullcalendar-1.4.7/fullcalendar.css' />
	<script type='text/javascript' src='<?=$_SETTINGS['website'] ?>admin/modules/events/scripts/fullcalendar-1.4.7/fullcalendar.min.js'></script>
<?
}




// ONLY SHOW ON EVENTS PAGE
if($_REQUEST['page'] == $_SETTINGS['events_page_clean_url']){
	$Events = new Events();
	?>

	<script>
		$(document).ready(function() {
			
			var date = new Date();
			var d = date.getDate();
			var m = date.getMonth();
			var y = date.getFullYear();

						
			$('#calendar').fullCalendar({
				editable: true,
				header: {
						left: 'prev,next today',
						center: 'title',
						right: 'month,basicWeek,basicDay'
				},
				theme: true,
				disableDragging: true,
				events: [
					<?=$Events->GetEventsJsForCalendar()?>
				]
			});
			
			$('#smallcalendar').fullCalendar({
				editable: true,
				header: {
						left: 'prev,next',
						center: 'title'
				},
				theme: true,
				events: [
					<?=$Events->GetEventsJsForCalendar($title=false)?>
				]
			});
			
		});
	</script>

	<?
	/**
	 *
	 * GOOGLE MAPS
	 *
	 */ 
	?>
	<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />

	<? /*
	TODO...
	SSL ISSUE
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
	*/ ?>

	<style type="text/css">
	  html { height: 100% }
	  body { height: 100%; margin: 0px; padding: 0px }
	  .eventmaps {

		clear:both;
		float:left;
		height:115px;
		margin-bottom:0px;
		margin-right:0px;
		width:175px;
		
	}
	</style>


	<script type="text/javascript">
	<?
	/**
	 *
	 * EVENTS GOOGLE MAP LOCATION
	 *
	 */
	?>

	<? /*
	// INITIALIZE FUNCTION
	function initialize() {

		

		
		geocoder = new google.maps.Geocoder();
			
		//
		// SETUP MAPS FOR EACH EVENT
		//
		<?
		$select = "SELECT * FROM events WHERE active='1' ORDER BY date DESC";
		$result = doQuery($select);
		$num = mysql_num_rows($result);
		$i = 0;
		while($i<$num){
		?>
			var geocoder<?=$i?>;
			var map<?=$i?>;
			var latlng<?=$i?> = new google.maps.LatLng(-34.397, 150.644);
			var myOptions<?=$i?> = {
				zoom: 8,
				center: latlng<?=$i?>,
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				disableDefaultUI: true,
			}
			map<?=$i?> = new google.maps.Map(document.getElementById("eventmap<?=$i?>"), myOptions<?=$i?>);
		<?
			$i++;
		}
		?>
		
	}
	*/ ?>

	/*
	function codeAddress() {
		var address = document.getElementById("address").value;
		geocoder.geocode( { 'address': address}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			map.setCenter(results[0].geometry.location);
			var marker = new google.maps.Marker({
			map: map,
			position: results[0].geometry.location
			});
		} else {
			alert("Geocode was not successful for the following reason: " + status);
		}
		});
	} 
	*/
	  
	//<input id="address" type="textbox" value="Sydney, NSW">
	//<input type="button" value="Geocode" onclick="codeAddress()">

	  
	</script>
<?
}
?>