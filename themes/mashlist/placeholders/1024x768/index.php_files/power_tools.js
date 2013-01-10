/**
 * ON DONE TYPING
 */
$.fn.onTypeFinished = function(func){
	var T = undefined, S = 0, D = 1000;
	$(this).bind("keypress", onKeyPress).bind("focusout", onTimeOut);
	function onKeyPress() {
	    clearTimeout(T);
	    if (S == 0) { S = new Date().getTime(); D = 1000; T = setTimeout(onTimeOut, 1000); return; }
	    var t = new Date().getTime();
	    D = (D + (t - S)) / 2; S = t; T = setTimeout(onTimeOut, D * 2);
	}
 
	function onTimeOut() {
       func.apply(); S = 0;
	}
    
	return this;

};

function hideOverlay(){
	$("#power-tools-overlay").fadeOut(200);
	$("#loading").fadeOut(200);
}

function showOverlay(){
	$("#power-tools-overlay").fadeIn(200);
	$("#loading").fadeIn(200);
}



/**
 * DOCREADY
 */
$(document).ready(function(){
	
	var lastFocus;
	
	hideOverlay();
	
	$(".powerinput").focus(function(){
		lastFocus = $(this).attr('id');
	});
	
	$(".close-link").click(function(e){
		e.preventDefault();
		$(this).closest('table').fadeOut(200);
		hideOverlay();
	});
	
	$(".group-link").click(function(e){
		e.preventDefault();
		$('#power-tools-overlay').fadeIn(200);
		
	});
	
	$("input.powerinput").focus(function(){
		$("#excel-input").val($(this).val());
		//$("#excel-input").attr('table',$(this).attr('table'));
		//$("#excel-input").attr('column',$(this).attr('column'));
		//$("#excel-input").attr('xid',$(this).attr('xid'));
		//$("#excel-input").attr('idcol',$(this).attr('idcol'));
		//$("#excel-input").attr('ammendsql',$(this).attr('ammendsql'));
		
	});
	
	/*
	$("#excel-input").onTypeFinished(function(){
		//$("")
		updateField(); 
	});
	
	$("input.powerinput").change(function(){
		$("#excel-input").val($(this).val());
	});
	
	$("input.powerinput").keyup(function(){
		$("#excel-input").val($(this).val());
	});
	*/
	
	/**
	 * SAVE TEXT
	 */
	$("input.powerinput").onTypeFinished(function(){
		//console.log('finished typin');
		//console.log("VAL: "+$(this).val());
		//var val 		= lastFocus;
		
		updateField();
		
		//console.log(this);
		$("#"+lastFocus).animate({ backgroundColor: "red" }, 'fast').delay(1000).animate({ backgroundColor: "transparent"}, 'fast');

	});
	
	/**
	 * SAVE SELECT
	 */
	$("select.powerinput").change(function(){
		
		updateField();
		
		/*
		//console.log('finished typin');
		//console.log("VAL: "+$(this).val());
		//var val 		= lastFocus;
		var table 		= $("#"+lastFocus).attr('table');
		var column 		= $("#"+lastFocus).attr('column');
		var xid 		= $("#"+lastFocus).attr('xid');
		var idcol 		= $("#"+lastFocus).attr('idcol');
		var ammendsql 	= $("#"+lastFocus).attr('ammendsql');
		var value 		= $("#"+lastFocus).val()
		
		console.log(lastFocus);
		console.log("VAL:"+$("#"+lastFocus).val());
		
		
		
		//var
		
		$.ajax({
		  	   url: "index.php",
			   type: "POST",
			   data: {'ajax': '1', 'action':'update', 'table':table, 'column':column, 'idcol':idcol, 'xid':xid, 'value':value, 'ammendsql':ammendsql },
			   success: function(response){
				   console.log(response);
			   }
		});
		
		//console.log(this);
		//$(this).css('background-color','red');
		*/
	});
	
	/**
	 * UPDATE A FIELD WITH AJAX
	 */
	function updateField(){
		var table 		= $("#"+lastFocus).attr('table');
		var column 		= $("#"+lastFocus).attr('column');
		var xid 		= $("#"+lastFocus).attr('xid');
		var idcol 		= $("#"+lastFocus).attr('idcol');
		var ammendsql 	= $("#"+lastFocus).attr('ammendsql');
		var value 		= $("#"+lastFocus).val();
		
		console.log(lastFocus);
		console.log("VAL:"+$("#"+lastFocus).val());
		
		//var
		
		$.ajax({
		  	   url: "index.php",
			   type: "POST",
			   data: {'ajax': '1', 'action':'update', 'table':table, 'column':column, 'idcol':idcol, 'xid':xid, 'value':value, 'ammendsql':ammendsql },
			   success: function(response){
				   console.log(response);
			   }
		});
	}
	
});


