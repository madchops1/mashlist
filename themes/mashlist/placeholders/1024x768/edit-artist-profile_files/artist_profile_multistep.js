/**
 * Multi Step Artist Profile Form Js
 * This script takes the standard content type fapi and changes it magically
 * into a multi-step form... amazing no modules for multistep whodathunkit?
 * 
 * By: Karl J. Steltenpohl
 * Date: 2/13/2012
 * 
 */

$(document).ready(function(){
	
	//alert('loaded artist_profile_multistep.js');
	
	
	/**
	 * DETERMINE CREATE || UPDATE
	 */
	var pathArray = window.location.pathname.split( '/' );
	//console.log(pathArray);
	var updating = 0;
	if(pathArray[2] == 'create-artist-profile'){
		updating = 0;
	} else {
		updating = 1;
	}
	console.log("UPDATING: "+updating+"");
	
	/**
	 * HASH CHANGE
	 */
	// HASH CHANGE
	$(window).bind( 'hashchange', function(e) {
		  
	  // IE DOESN'T LIKE CONSOLE
	  //if (!window.console) { console = []; }
	  //console.log('url is: ' + url)
	  var url;
	  url = $.param.fragment();
	  console.log('URL: ' + url);
	
	});
	
	/**
	 * 
	 * HTML SETUP
	 * 
	 */
	// HIDE THE "other" FIELDSET
	$('fieldset.group-other').hide();
	
	
	var $i = 0;
	var stepArray = [];
	$('.dashboard-form-wrapper form div:first fieldset').each(function(){
		
		thisParent = $(this).parents('fieldset');
		
		//console.log(thisParent.tagName);
		
		if (!thisParent.length && !$(this).hasClass("group-other")){
			
			
			$(this).attr('step',$i);
			$(this).addClass('multistep-group');	
			legend = $(this).children('legend a').html();
			legend = legend.replace(/(<([^>]+)>)/ig,""); 	// STRIP HTML FROM THIS STRING
			machineLegend = legend.replace("/","");			// STRIP SLASHES
			machineLegend = machineLegend.replace(/ /g,''); // STRIP ALL SPACES
	
			machineLegend = machineLegend.toLowerCase();
			//machineLegend = machineLegend.toLowerCase();
			$(this).attr('machname',machineLegend);
			//alert(legend);
			stepArray.push(legend);
			$i++;
		}
	});
	
	$('fieldset.collapsed legend a').trigger('click');	// OPEN ALL COLLAPSED DIVS FOR ADMINS AND SUCH
	$('.multistep-group').hide();				// HIDE ALL THE STEPS
	$('.multistep-group:first').show();			// SHOW FIRST STEP

	var listHtml = '';
	var zIndexHigh = stepArray.length;
	console.log('HIGH: ' + zIndexHigh );
	for (var i = 0, l = stepArray.length; i < l; ++i) {
	    //div.innerHTML += input[i] + "<br />";
		if(i == 0){ activeClass = 'active'; } else { activeClass = ''; } // MAKE FIRST STEP ACTIVE
		listHtml = listHtml + '<li><a href="#'+i+'" class="'+activeClass+' multistep-nav-step" step="'+i+'" style="z-index:' + zIndexHigh + '">' + stepArray[i] + '</a></li>';
		--zIndexHigh;
	}
	++i;
	
	listHtml = listHtml + '<li><a id="multistep-complete" step="'+i+'">Complete</a></li>'; // ADD THE COMPLETE NAV ITEM
	$('.dashboard-multistep-form-nav-list').prepend(listHtml); 		// PREPEND THE LIST TO THE PAGE
	
	var nextButton = '<input id="multistep-next" class="form-submit" type="submit" value="Next >" />'; 		// NEXT BUTTON
	var previousButton = '<input id="multistep-previous" class="form-submit" type="submit" value="< Previous" />';		// PREVIOUS BUTTON
	// PUT NAV BUTTONS BEFORE SAVE BUTTON
	$('#edit-submit').before(nextButton + ' ' + previousButton);
	
	$('#multistep-previous').hide(); 	// INITIALLY HIDE THE PREVIOUS BUTTON TO START
	$('#multistep-next').show(); 		// INITIALLY SHOW THE NEXT BUTTON TO START
	
	
	/**
	 * 
	 * MASK FIELDS
	 * 
	 */
	
	$("#group_band_members_values tr input[id*='age-value']").mask("99/99/9999"); // INITIALLY ADDS MASK TO ALL BDAY FIELDS IN GENERAL
	//$("input[id*='phone']").mask("(999) 999-9999");
	//$("input[id*='cell']").mask("(999) 999-9999");
	
	// DOM TREE CHANGE TEST
	// This is for handling the dynamic ahah fields 
	$("body").bind("DOMSubtreeModified", function() {
	    //console.log("tree changed");
	    
	    // REMASK AHAH FIELDS
	    $("#group_band_members_values tr input[id*='age-value']").mask("99/99/9999"); // ADDS MASK TO ALL BDAY FIELDS IN AHAH
	    //$("input[id*='phone']").mask("(999) 999-9999");
	    //$("input[id*='cell']").mask("(999) 999-9999");
	    
	    // PROBLY A BETTER WAY
	    $('#edit-group-band-members-group-band-members-add-more').val('Add Another Member');
	    $('#edit-group-additional-band-contacts-group-additional-band-contacts-add-more').val('Add Another Band Contact');
	    
		
	});

	/**
	 *  
	 * DISABLE OLD FIELDS 
	 *  
	 */
	
	$("#edit-field-artist-relation-to-artist-0-value").attr('disabled','disabled');
	$("#edit-field-artist-band-members-0-value").attr('disabled','disabled');
	$("#edit-field-artist-payee-state-0-value").attr('disabled','disabled');
	$("#edit-field-artist-payee-country-0-value").attr('disabled','disabled');
	$("#edit-field-artist-state-0-value").attr('disabled','disabled');
	$("#edit-field-artist-country-0-value").attr('disabled','disabled');
	
	/**
	 * 
	 * INITIAL JS FIELD SHOW/HIDE
	 * 
	 */
	 if(updating == 0){
		// HIDE SAVE BUTTON TILL LAST STEP IF CREATING
		$("#edit-submit").hide();	
	 }
	/**
	 * 
	 * SETUP SELECT DROPDOWNS
	 * This won't work till we update jquery ui and jquery
	 * 
	 */
	
	//$('#edit-field-custom-music-time-value').selectmenu(); // SETUP ui.select.js
	
	/**
	 * 
	 * MOVE SPECIAL FIELDS AROUND
	 * 
	 */
	
	// ARTIST/BAND (title)
	if($('#edit-title-wrapper').length && $('.multistep-group[machname="artistband"]').length){
		$("#edit-title-wrapper").prependTo('.multistep-group[machname="artistband"]');
	}
	
	// CHECKOUT FIELD (admin field)
	if($('#edit-checkout-wrapper').length && $('.multistep-group[machname="revisioninformation"]').length){
		$("#edit-checkout-wrapper").prependTo('.multistep-group[machname="revisioninformation"]');
	}
	
	// ADD BEFORE MUSICDEALERS URL
	//http://musicdealers.com/artist/ 
	$("#edit-field-artist-page-url-0-value").before("http://musicdealers.com/artist/");
	
	
	
	/**
	 * 
	 * HIDE CERTAIN FIELDS
	 * 
	 */
	
	// IF THE CUSTOM MUSIC AND CHECK GENRE FIELDS ARE THERE THEN HIDE EM'
	if($('.child-of-field_custom_music_time').length && $('.child-of-field_artist_main_genre_check').length){
		
		$('.child-of-field_custom_music_time').hide();
		$('.child-of-field_artist_main_genre_check').hide();
		
		// IF THE CUSTOM MUSIC CHECKBOX IS CHECKED THEN SHOW EM'
		if($('#edit-field-artist-availability-value-10:checked').length){
			
			$('.child-of-field_custom_music_time').show();
			$('.child-of-field_artist_main_genre_check').show();
		}
	}
	
	// PUT TAX FORMS H2 TITLE INTO FORM
	$('#edit-field-artist-upload-w9-0-ahah-wrapper').before('<h2 style="clear:both;">Tax Forms</h2>');
	
	
	/**
	 * 
	 * OLD DISABLED FIELDS AND THEIR NEW COUNTERPART FIELDS
	 * If the new field is empty and the old field isn't then show the old field
	 * so the data can be updated
	 * 
	 */
	
	// BAND MEMBERS
	if($('#edit-group-band-members-0-field-artist-mem-real-first-name-value').val() == "" && $('#edit-field-artist-band-members-0-value').val() != ""){
		$(".child-of-field_artist_band_members").show();
	} else {
		$(".child-of-field_artist_band_members").hide();
	}
	
	
	
	/**
	 * 
	 * ARTIST STATE HANDLING TEXTBOX OR SELECT
	 * If the country is not U.S. Then replace the select html with a textbox
	 *
	 */
	var stateCurrentVal = $("#edit-field-artist-state-select-value").val();
	var stateTextField = "<label for='edit-field-artist-state-select-value'>Province: <span title='This field is required.' class='form-required'>*</span></label><input type='text' id='#edit-field-artist-state-select-value' name='field_artist_state_select[value]' value='"+stateCurrentVal+"' />";
	var stateSelectField = $(".child-of-field_artist_state_select").html(); 	// GET THE SELECT HTML AND SAVE IT JUST IN CASE
	
	// IF THE DEFAULT VALUE IS NOT United States
	if($("#edit-field-artist-country-selec-value").val() != "US"){
		$("#edit-field-artist-state-select-value").parent("div").html(stateTextField);
	}
	
	// ONCHANGE FOR ARTIST COUNTRY
	$("#edit-field-artist-country-selec-value").change(function(e){
		
		console.log('country changed');
		console.log($("#edit-field-artist-country-selec-value").val());
		
		if($("#edit-field-artist-country-selec-value").val() == "US"){
			// US
			$(".child-of-field_artist_state_select").html(stateSelectField);
		} else {
			// INTERNATIONAL
			$(".child-of-field_artist_state_select").html(stateTextField);
			$(".child-of-field_artist_state_select").children('input').val(""); // CLEAR THE TEXT FIELD ON CHANGE
		}
		
	});
	
	/**
	 * 
	 * PAYEE STATE HANDLING TEXTBOX OR SELECT 
	 *
	 */
	var paystateCurrentVal 	= $("#edit-field-artist-payee-state-select-value").val();
	var paystateTextField 	= "<label for='edit-field-artist-payee-state-select-value'>Province: <span title='This field is required.' class='form-required'>*</span></label><input type='text' id='#edit-field-artist-payee-state-select-value' name='field_artist_payee_state_select[value]' value='"+paystateCurrentVal+"' />";
	var paystateSelectField = $(".child-of-field_artist_payee_state_select").html(); 	// GET THE SELECT HTML AND SAVE IT JUST IN CASE
	
	// IF THE DEFAULT VALUE IS NOT United States
	if($("#edit-field-artist-payee-country-selec-value").val() != "US"){
		$("#edit-field-artist-payee-state-select-value").parent("div").html(paystateTextField);
		$(".child-of-field_artist_upload_w8").show();
		$(".child-of-field_artist_upload_w9").hide();
	}
	
	// ONCHANGE FOR PAYEE COUNTRY
	$("#edit-field-artist-payee-country-selec-value").change(function(e){
		
		console.log('payee country changed');
		console.log($("#edit-field-artist-payee-country-selec-value").val());
		
		if($("#edit-field-artist-payee-country-selec-value").val() == "US"){
			// US
			$(".child-of-field_artist_payee_state_select").html(paystateSelectField);
			$(".child-of-field_artist_upload_w8").hide();
			$(".child-of-field_artist_upload_w9").show();
		} else {
			// INTERNTIONAL
			$(".child-of-field_artist_payee_state_select").html(paystateTextField);
			$(".child-of-field_artist_payee_state_select").children('input').val(""); // CLEAR THE TEXT FIELD ON CHANGE
			$(".child-of-field_artist_upload_w8").show();
			$(".child-of-field_artist_upload_w9").hide();
		}
		
	});
	
	/**
	 * 
	 * SAG FIELD
	 * 
	 */
	
	
	
	
	
	/**
	 * 
	 * RENAMING STUFF
	 * 
	 */
	
	
	
	/**
	 * 
	 * POST VALIDATION UI
	 * This makes sure the user sees their mistakes after submitting a form
	 * ... that doesn't make it through validation.
	 * 
	 */

	if($('.multistep-group .form-item .error').length){
		numErrors = $('.error').length;
		console.log('there are '+numErrors+' errors');
		//	errorStep = $('.error').parent('fieldset').attr('step');
	
		$('.multistep-group .form-item .error').each(function(){
			
			console.log($(this).attr('name'));
			//$(this).parent('fieldset').attr('step');
			parentContainerStep = $(this).parents('fieldset.multistep-group').attr('step');
			//console.log('parent step: '+parentContainerStep);
			if($(this).parents('fieldset').length){
				console.log('this error has a parent fieldset and its step is '+parentContainerStep+'');
			}
			//$(this).parents('.multistep-group').css('border','2px solid blue');
			$('.multistep-nav-step[step="'+parentContainerStep+'"]').removeClass('active').addClass('errorstep');
		});
		
	}
	
	/**
	 * 
	 * EVENTS
	 * 
	 */
	
	$('#edit-field-artist-availability-value-10').click(function(e){
		$('.child-of-field_custom_music_time').toggle();
		$('.child-of-field_artist_main_genre_check').toggle();
	});
	
	function activeNavStep(thisStep){
		$('.multistep-nav-step').removeClass('active');						// REMOVE ACTIVE CLASS FROM NAVS
		$('.multistep-nav-step[step="'+thisStep+'"]').addClass('active');	// ADD ACTIVE CLASS TO THIS
		$('.multistep-group').hide();										// HIDE GROUPS
		$('.multistep-group[step='+thisStep+']').show();					// SHOW THIS GROUP
		
		// HIDE THE PREVIOUS BUTTON
		if(thisStep == '0'){
			$('#multistep-previous').hide();
		} else {
			$('#multistep-previous').show();
		}
		
		// HIDE THE NEXT BUTTON
		topCount = stepArray.length;
		--topCount;
		if(thisStep == topCount){
			$('#multistep-next').hide();
			if(updating == 0){
				$("#edit-submit").show();
			}
		} else {
			$('#multistep-next').show();
		}
	}

	// CLICKING A STEP
	$('.multistep-nav-step').click(function(e){
		e.preventDefault();
		thisStep = $(this).attr('step');
		activeNavStep(thisStep);
		
	});
	
	// CLICKING PREVIOUS
	$('#multistep-previous').click(function(e){
		e.preventDefault();
		thisStep = $('.multistep-nav-step.active').attr('step');
		--thisStep;
		activeNavStep(thisStep);
		
		new_position = $('#content-content').offset();
	    window.scrollTo(new_position.left,new_position.top);
	});
	
	// CLICKING NEXT
	$('#multistep-next').click(function(e){
		e.preventDefault();
		thisStep = $('.multistep-nav-step.active').attr('step');
		++thisStep
		activeNavStep(thisStep);
		
		new_position = $('#content-content').offset();
	    window.scrollTo(new_position.left,new_position.top);
	});
	
	// CLICKING SAVE JS VALIDATION
	
	/**
	 * 
	 * VALIDATION
	 * 
	 */
	
	// HERE WE COULD DO SOMETHING ELSE COOL AND TURN THE FORM FIELD FOR ARTIST PROFILE PIC RED
	// ... IF IT ERRORS
	// ajaxStop won't work with ahah
	//$('#edit-field-artist-profilepic-0-upload').ajaxStop(function(){
	//	console.log('ajax stop (artist profile form)');
	//});
	
	// ARTIST PAGE URL JS VALIDATION
	$('#edit-field-artist-page-url-0-value').keyup(function(e){
		valuein = $(this).val();
		
		alphanum = new RegExp ('^([a-zA-Z0-9_]){3,20}$', 'gi');
		hasalpha = new RegExp ('^(?=.*[a-zA-Z])', 'gi');
		
		result = alphanum.test(valuein) && hasalpha.test(valuein);
		
		// HERE WE COULD DO SOMETHING COOL AND HAVE THE URL APPEAR IN THE DESCRIPTION IN REAL TIME
		// TODO...
		//$('#edit-field-artist-page-url-0-value-wrapper .description').
		
		// SHOW OR HIDE MESSAGE
		if (result) {
			$('div#artist_page_url_validator_pass').show();
			$('div#artist_page_url_validator_fail').hide();
			$(this).removeClass('error');
		} else {
			$('div#artist_page_url_validator_pass').hide();
			$('div#artist_page_url_validator_fail').show();
			$(this).addClass('error');
		}
	});

	
	// ONLY 3 BOXES CAN BE CHECKED FOR GENRE
	$(".child-of-field_artist_main_genre_check input").click(function(e){
		
		var maxChecks = 3;
		var checkedCount = $(".child-of-field_artist_main_genre_check input").length;
		//console.log(checkedCount+':::');
		if(checkedCount > maxChecks){
			handleBoxes();
		}
		
		
		
	});
	
	function handleBoxes(){
		var i = 0;
		$("#checkyourmaingenre-wrapper input").each(function(){
			if($(this+':checked')){
				++i;
				if(i >= 4){
					//alert('you can only have 3 boxes checked');
					--i;
					//$(this).trigger('click');
					$(this).attr('checked',false);
				} else {
					//alert(i+' boxes checked')
				}
			}
		});
	}
	
	
});