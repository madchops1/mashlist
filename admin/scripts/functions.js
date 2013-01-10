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

/**
 *
 * Grow Textarea
 *
 */					
function textareaexpand(that){
	$(that).css("height","150px");
}

/**
 *
 * Shrink Textarea
 *
 */ 
function textareaclose(that){
	$(that).css("height","50px");
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

/**
 *
 * simple event handler, called from onChange and onSelect
 * event handlers, as per the Jcrop invocation above
 *
 */
function showPreview(coords)
{
	if (parseInt(coords.w) > 0)
	{
		var rx = 100 / coords.w;
		var ry = 100 / coords.h;

		jQuery('#preview').css({
			width: Math.round(rx * 588) + 'px',
			height: Math.round(ry * 323) + 'px',
			marginLeft: '-' + Math.round(rx * coords.x) + 'px',
			marginTop: '-' + Math.round(ry * coords.y) + 'px'
		});
	}
}

/**
 *
 * JQUERY ON DOCUMENT READY
 *
 */
$(document).ready(function() {
	
	var height1 = $('.col1').height() - 5;	  
	$('.col2').height(height1);
	
	$(window).mousemove(function(event) {
	  //var msg = 'Handler for .mousemove() called at ' + event.pageX + ', ' + event.pageY;
	 // $('#log').append('<div> + msg + '</div>');
	 
		var height1 = $('.col1').height() - 5;	  
		$('.col2').height(height1);
	 
	 
	});
	
});

// Invoke Jcrop within jQuery(window).load(...), or Jcrop may not initialize properly
jQuery(window).load(function(){

	jQuery('#slideshow-cropbox1').Jcrop({
		onChange: showPreview,
		onSelect: showPreview,
		aspectRatio: 1
	});
	
	jQuery('#slideshow-cropbox2').Jcrop({
		onChange: showPreview,
		onSelect: showPreview,
		aspectRatio: 1
	});
	
	jQuery('#slideshow-cropbox3').Jcrop({
		onChange: showPreview,
		onSelect: showPreview,
		aspectRatio: 1
	});

});


