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
global $_SETTINGS;

// DECLARE CLASSES
// EVENTS
if(checkActiveModule('0000019')){
	if($_REQUEST['VIEW'] == 'events'){
		$Events = new Events();
		
		?>
			<link rel='stylesheet' type='text/css' href='modules/events/scripts/fullcalendar-1.4.7/fullcalendar.css' />
			<script type='text/javascript' src='modules/events/scripts/fullcalendar-1.4.7/fullcalendar.min.js'></script>
		<?
		
	}
}

// IMAGESLIDESHOWTHREE
if(checkActiveModule('0000022')){
	if($_REQUEST['VIEW'] == 'image_slideshow_3'){
		$ImageSlideshow3 = new ImageSlideshow3();
	}
}
?>

<script>
//
// BEGIN JAVASCRIPT FUNCTIONS
//

/**
 *
 * Grow Textarea
 *
 */					
function textareaexpand(that){
	true;
	//$(that).css("height","150px");
}

/**
 *
 * Shrink Textarea
 *
 */ 
function textareaclose(that){
	true;
	//$(that).css("height","50px");
}

/**
 * EX.
 * CONVERT DATE FROM 2010-5-26 to 5/26/2010
 *
 */
function convertDateFromYMD(date){	
	var Date 		= date;
	var DateArray 	= Date.split('-');
	var ConvertedDate = "" + DateArray[1] + "/" + DateArray[2] +  "/" + DateArray[0] + "";
	//alert(ConvertedDate);
	return ConvertedDate;
}

/**
 * EX.
 * CONVERT DATE FROM 5/26/2010 to 2010-5-26
 *
 */
function convertDateToYMD(date){
	var Date 		= date;
	var DateArray 	= Date.split('/');
	var ConvertedDate = "" + DateArray[2] + "-" + DateArray[0] +  "-" + DateArray[1] + "";
	//alert(ConvertedDate);
	return ConvertedDate;
}
</script>

<?
//
// JAVASCRIPT DOC READY FOR TOOLBAR
//
?>
<script>
/**
 *
 * JQUERY ON DOCUMENT READY FOR TOOLBAR
 *
 */
$(document).ready(function(){		
	//
	// FOR TOOLBAR
	//
	var height1 = $('.col1').height() - 5;	  
	$('.col2').height(height1);	
	$(window).mousemove(function(event) {
		var height1 = $('.col1').height() - 5;	  
		$('.col2').height(height1);	 
	});
});
</script>

<?
//
// JAVASCRIPT DOC READY FOR EVENTS
//

	//
	// EVENTS - SETUP CALENDAR
	//
	
	if(checkActiveModule('0000019')){
		if($_REQUEST['VIEW'] == 'events'){
		?>
		<script>
		$(document).ready(function(){		
			// FOR CALENDAR
			var date = new Date();
			var d = date.getDate();
			var m = date.getMonth();
			var y = date.getFullYear();

			$('#eventscalendar').fullCalendar({
				editable: true,
				header: {
						left: 'prev,next today',
						center: 'title',
						right: 'month,basicWeek,basicDay'
				},
				theme: true,
				events: [
					<?=$Events->GetEventsJsForCalendar()?>
				]
			});
		});
		</script>
		<?
		}
	} // END IF EVENTS
	

// JAVASCRIPT DOCREADY / WINDOW ONLOAD FOR IMAGE SLIDESHOW
if(checkActiveModule('0000022')){
	if($_REQUEST['VIEW'] == 'image_slideshow_3'){
		include 'modules/image_slideshow_3/scripts/image_slideshow_3_docready.php';
	}
}

// CUSTOMER FILE MANAGER MODULE SCRIPTS
if(checkActiveModule('0000025')){
	if($_REQUEST['VIEW'] == 'customer_filemanager' || $_REQUEST['VIEW'] == 'useraccounts'){
		include 'modules/customer_filemanager/scripts/customer_filemanager_docready.php';
	}
}
?>