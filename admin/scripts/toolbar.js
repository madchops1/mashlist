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

$(document).ready(function(){ 

	// INITIATE TABLE SORTER AND SORTABLE 
	$("#list").tablesorter();	
	$("#sortable").sortable();					
	
	// SET ROWS PER PAGE
	var options = {
	  currPage : 1, 
	  rowsPerPage : 25
	}
	
	// INITIATE TABLE SORTER AND TABLE PAGINATION
	$('#listjs').tablePagination(options);
	$("#listjs").tablesorter();
		
	// MAKE INFO NOTES IN TOOLBAR HIDDEN
	$(".col2 p").css({'display' : 'none'});
	
	
	
	function toolbarOver(){
		
		$(".col2").css({'height' : ''+$(".col1").height() - 11 +''});
	
		/*
		$(".col2").css({'background-color' : '#eeeeee'});
		$(".col2").css({'z-index' : '100'});						
		$(".col2").css({'border-right' : '1px solid #fff'});		
				
		$(".col2").css({'-moz-border-radius' : '0px 0px 0px 12px'});
		$(".col2").css({'-webkit-border-radius' : '0px 0px 0px 12px'});
		$(".col2").css({'border-radius' : '0px 0px 0px 12px'});		
		$(".col2").css({'-moz-box-shadow' : '0 15px 30px -15px #444444'});
		$(".col2").css({'-webkit-box-shadow' : '0 15px 30px -15px #444444'});
		$(".col2").css({'box-shadow' : '0 15px 30px -15px #444444'});
		*/
		
		$(".col2").addClass('toolbarOver');
		$(".col2").removeClass('toolbarOut');
		$(".col2").stop(true, true);
		$(".col2").animate({'width' : '186px'},0);	
		$(".col2 p").fadeIn(200);
		$(".col2").animate({'opacity' : '1.0'},200);
			
	}
	
	function toolbarOut(){		
		
		$(".col2 p").css({'display' : 'none'});
		$(".col2").animate({'width' : '40px'},0);
		
		/*				
		$(".col2").css({'-moz-box-shadow' : 'none'});
		$(".col2").css({'-webkit-box-shadow' : 'none'});
		$(".col2").css({'box-shadow' : 'none'});		
		$(".col2").css({'-moz-border-radius' : '0px 0px 0px 12px'});
		$(".col2").css({'-webkit-border-radius' : '0px 0px 0px 12px'});
		$(".col2").css({'border-radius' : '0px 0px 0px 12px'});		
		$(".col2").css({'border' : '0px'});
		$(".col2").css({'z-index' : '0'});
		$(".col2").css({'background-color' : '#eee'});
		*/
		
		$(".col2").addClass('toolbarOut');
		$(".col2").removeClass('toolbarOver');
		$(".col2 p").fadeOut(50);
		$(".col2").animate({'opacity' : '0.92'},50);
	}
	
	// CONFIGURE TOOLBAR HOVER
	var config = {    
		 over: toolbarOver, // function = onMouseOver callback (REQUIRED)    
		 timeout: 200, // number = milliseconds delay before onMouseOut    
		 out: toolbarOut // function = onMouseOut callback (REQUIRED)    
	};

	//$("#demo3 li").hoverIntent( config )
	
	
	$(".col2").hoverIntent( config );
	
}); 