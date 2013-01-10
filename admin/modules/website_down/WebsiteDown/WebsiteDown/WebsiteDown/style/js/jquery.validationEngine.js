var jq = jQuery.noConflict( );

/*
 * Inline Form Validation Engine 1.6.3, jQuery plugin
 * 
 * Copyright(c) 2009, Cedric Dugas
 * http://www.position-relative.net
 *	
 * Form validation engine allowing custom regex rules to be added.
 * Thanks to Francois Duquette
 * Licenced under the MIT Licence
 */
 
(function(jq) {
	
	jq.fn.validationEngine = function(settings) {

	if(jq.validationEngineLanguage){				// IS THERE A LANGUAGE LOCALISATION ?
		allRules = jq.validationEngineLanguage.allRules;
	}else{
		jq.validationEngine.debug("Validation engine rules are not loaded check your external file");
	}
 	settings = jQuery.extend({
		allrules:allRules,
		validationEventTriggers:"focusout",					
		inlineValidation: true,	
		returnIsValid:false,
		liveEvent:false,
		unbindEngine:true,
		ajaxSubmit: false,
		scroll:true,
		promptPosition: "topRight",	// OPENNING BOX POSITION, IMPLEMENTED: topLeft, topRight, bottomLeft, centerRight, bottomRight
		success : false,
		beforeSuccess :  function() {},
		failure : function() {}
	}, settings);	
	jq.validationEngine.settings = settings;
	jq.validationEngine.ajaxValidArray = new Array();	// ARRAY FOR AJAX: VALIDATION MEMORY 
	
	if(settings.inlineValidation == true){ 		// Validating Inline ?
		if(!settings.returnIsValid){					// NEEDED FOR THE SETTING returnIsValid
			allowReturnIsvalid = false;
			if(settings.liveEvent){						// LIVE event, vast performance improvement over BIND
				jq(this).find("[class*=validate][type!=checkbox]").live(settings.validationEventTriggers, function(caller){ _inlinEvent(this);})
				jq(this).find("[class*=validate][type=checkbox]").live("click", function(caller){ _inlinEvent(this); })
			}else{
				jq(this).find("[class*=validate]").not("[type=checkbox]").bind(settings.validationEventTriggers, function(caller){ _inlinEvent(this); })
				jq(this).find("[class*=validate][type=checkbox]").bind("click", function(caller){ _inlinEvent(this); })
			}
			firstvalid = false;
		}
			function _inlinEvent(caller){
				jq.validationEngine.settings = settings;
				if(jq.validationEngine.intercept == false || !jq.validationEngine.intercept){		// STOP INLINE VALIDATION THIS TIME ONLY
					jq.validationEngine.onSubmitValid=false;
					jq.validationEngine.loadValidation(caller); 
				}else{
					jq.validationEngine.intercept = false;
				}
			}
	}
	if (settings.returnIsValid){		// Do validation and return true or false, it bypass everything;
		if (jq.validationEngine.submitValidation(this,settings)){
			return false;
		}else{
			return true;
		}
	}
	jq(this).bind("submit", function(caller){   // ON FORM SUBMIT, CONTROL AJAX FUNCTION IF SPECIFIED ON DOCUMENT READY
		jq.validationEngine.onSubmitValid = true;
		jq.validationEngine.settings = settings;
		if(jq.validationEngine.submitValidation(this,settings) == false){
			if(jq.validationEngine.submitForm(this,settings) == true) {return false;}
		}else{
			settings.failure && settings.failure(); 
			return false;
		}		
	})
};	
jq.validationEngine = {
	defaultSetting : function(caller) {		// NOT GENERALLY USED, NEEDED FOR THE API, DO NOT TOUCH
		if(jq.validationEngineLanguage){				
			allRules = jq.validationEngineLanguage.allRules;
		}else{
			jq.validationEngine.debug("Validation engine rules are not loaded check your external file");
		}	
		settings = {
			allrules:allRules,
			validationEventTriggers:"blur",					
			inlineValidation: true,	
			returnIsValid:false,
			scroll:true,
			unbindEngine:true,
			ajaxSubmit: false,
			promptPosition: "topRight",	// OPENNING BOX POSITION, IMPLEMENTED: topLeft, topRight, bottomLeft, centerRight, bottomRight
			success : false,
			failure : function() {}
		}	
		jq.validationEngine.settings = settings;
	},
	loadValidation : function(caller) {		// GET VALIDATIONS TO BE EXECUTED
		if(!jq.validationEngine.settings){
			jq.validationEngine.defaultSetting()
		}
		rulesParsing = jq(caller).attr('class');
		rulesRegExp = /\[(.*)\]/;
		getRules = rulesRegExp.exec(rulesParsing);
		str = getRules[1];
		pattern = /\[|,|\]/;
		result= str.split(pattern);	
		var validateCalll = jq.validationEngine.validateCall(caller,result)
		return validateCalll;
	},
	validateCall : function(caller,rules) {	// EXECUTE VALIDATION REQUIRED BY THE USER FOR THIS FIELD
		var promptText =""	
		
		if(!jq(caller).attr("id")) { jq.validationEngine.debug("This field have no ID attribut( name & class displayed): "+jq(caller).attr("name")+" "+jq(caller).attr("class")) }

		caller = caller;
		ajaxValidate = false;
		var callerName = jq(caller).attr("name");
		jq.validationEngine.isError = false;
		jq.validationEngine.showTriangle = true;
		callerType = jq(caller).attr("type");

		for (i=0; i<rules.length;i++){
			switch (rules[i]){
			case "optional": 
				if(!jq(caller).val()){
					jq.validationEngine.closePrompt(caller);
					return jq.validationEngine.isError;
				}
			break;
			case "required": 
				_required(caller,rules);
			break;
			case "custom": 
				 _customRegex(caller,rules,i);
			break;
			case "exemptString": 
				 _exemptString(caller,rules,i);
			break;
			case "ajax": 
				if(!jq.validationEngine.onSubmitValid){
					_ajax(caller,rules,i);	
				};
			break;
			case "length": 
				 _length(caller,rules,i);
			break;
			case "maxCheckbox": 
				_maxCheckbox(caller,rules,i);
			 	groupname = jq(caller).attr("name");
			 	caller = jq("input[name='"+groupname+"']");
			break;
			case "minCheckbox": 
				_minCheckbox(caller,rules,i);
				groupname = jq(caller).attr("name");
			 	caller = jq("input[name='"+groupname+"']");
			break;
			case "confirm": 
				 _confirm(caller,rules,i);
			break;
			default :;
			};
		};
		radioHack();
		if (jq.validationEngine.isError == true){
			linkTofield = jq.validationEngine.linkTofield(caller);
			
			(jq("div."+linkTofield).size() ==0) ? jq.validationEngine.buildPrompt(caller,promptText,"error")	: jq.validationEngine.updatePromptText(caller,promptText);
		}else{ jq.validationEngine.closePrompt(caller);}			
		/* UNFORTUNATE RADIO AND CHECKBOX GROUP HACKS */
		/* As my validation is looping input with id's we need a hack for my validation to understand to group these inputs */
		function radioHack(){
	      if(jq("input[name='"+callerName+"']").size()> 1 && (callerType == "radio" || callerType == "checkbox")) {        // Hack for radio/checkbox group button, the validation go the first radio/checkbox of the group
	          caller = jq("input[name='"+callerName+"'][type!=hidden]:first");     
	          jq.validationEngine.showTriangle = false;
	      }      
	    }
		/* VALIDATION FUNCTIONS */
		function _required(caller,rules){   // VALIDATE BLANK FIELD
			callerType = jq(caller).attr("type");
			if (callerType == "text" || callerType == "password" || callerType == "textarea"){
								
				if(!jq(caller).val()){
					jq.validationEngine.isError = true;
					promptText += jq.validationEngine.settings.allrules[rules[i]].alertText+"<br />";
				}	
			}	
			if (callerType == "radio" || callerType == "checkbox" ){
				callerName = jq(caller).attr("name");
		
				if(jq("input[name='"+callerName+"']:checked").size() == 0) {
					jq.validationEngine.isError = true;
					if(jq("input[name='"+callerName+"']").size() ==1) {
						promptText += jq.validationEngine.settings.allrules[rules[i]].alertTextCheckboxe+"<br />"; 
					}else{
						 promptText += jq.validationEngine.settings.allrules[rules[i]].alertTextCheckboxMultiple+"<br />";
					}	
				}
			}	
			if (callerType == "select-one") { // added by paul@kinetek.net for select boxes, Thank you		
				if(!jq(caller).val()) {
					jq.validationEngine.isError = true;
					promptText += jq.validationEngine.settings.allrules[rules[i]].alertText+"<br />";
				}
			}
			if (callerType == "select-multiple") { // added by paul@kinetek.net for select boxes, Thank you	
				if(!jq(caller).find("option:selected").val()) {
					jq.validationEngine.isError = true;
					promptText += jq.validationEngine.settings.allrules[rules[i]].alertText+"<br />";
				}
			}
		}
		function _customRegex(caller,rules,position){		 // VALIDATE REGEX RULES
			customRule = rules[position+1];
			pattern = eval(jq.validationEngine.settings.allrules[customRule].regex);
			
			if(!pattern.test(jq(caller).attr('value'))){
				jq.validationEngine.isError = true;
				promptText += jq.validationEngine.settings.allrules[customRule].alertText+"<br />";
			}
		}
		function _exemptString(caller,rules,position){		 // VALIDATE REGEX RULES
			customString = rules[position+1];
			if(customString == jq(caller).attr('value')){
				jq.validationEngine.isError = true;
				promptText += jq.validationEngine.settings.allrules['required'].alertText+"<br />";
			}
		}
		function _ajax(caller,rules,position){				 // VALIDATE AJAX RULES
			
			customAjaxRule = rules[position+1];
			postfile = jq.validationEngine.settings.allrules[customAjaxRule].file;
			fieldValue = jq(caller).val();
			ajaxCaller = caller;
			fieldId = jq(caller).attr("id");
			ajaxValidate = true;
			ajaxisError = jq.validationEngine.isError;
			
			if(!jq.validationEngine.settings.allrules[customAjaxRule].extraData){
				extraData = jq.validationEngine.settings.allrules[customAjaxRule].extraData;
			}else{
				extraData = "";
			}
			/* AJAX VALIDATION HAS ITS OWN UPDATE AND BUILD UNLIKE OTHER RULES */	
			if(!ajaxisError){
				jq.ajax({
				   	type: "POST",
				   	url: postfile,
				   	async: true,
				   	data: "validateValue="+fieldValue+"&validateId="+fieldId+"&validateError="+customAjaxRule+extraData,
				   	beforeSend: function(){		// BUILD A LOADING PROMPT IF LOAD TEXT EXIST		   			
				   		if(jq.validationEngine.settings.allrules[customAjaxRule].alertTextLoad){
				   		
				   			if(!jq("div."+fieldId+"formError")[0]){				   				
	 			 				return jq.validationEngine.buildPrompt(ajaxCaller,jq.validationEngine.settings.allrules[customAjaxRule].alertTextLoad,"load");
	 			 			}else{
	 			 				jq.validationEngine.updatePromptText(ajaxCaller,jq.validationEngine.settings.allrules[customAjaxRule].alertTextLoad,"load");
	 			 			}
			   			}
			  	 	},
			  	 	error: function(data,transport){ jq.validationEngine.debug("error in the ajax: "+data.status+" "+transport) },
					success: function(data){					// GET SUCCESS DATA RETURN JSON
						data = eval( "("+data+")");				// GET JSON DATA FROM PHP AND PARSE IT
						ajaxisError = data.jsonValidateReturn[2];
						customAjaxRule = data.jsonValidateReturn[1];
						ajaxCaller = jq("#"+data.jsonValidateReturn[0])[0];
						fieldId = ajaxCaller;
						ajaxErrorLength = jq.validationEngine.ajaxValidArray.length;
						existInarray = false;
						
			 			 if(ajaxisError == "false"){			// DATA FALSE UPDATE PROMPT WITH ERROR;
			 			 	
			 			 	_checkInArray(false)				// Check if ajax validation alreay used on this field
			 			 	
			 			 	if(!existInarray){		 			// Add ajax error to stop submit		 		
				 			 	jq.validationEngine.ajaxValidArray[ajaxErrorLength] =  new Array(2);
				 			 	jq.validationEngine.ajaxValidArray[ajaxErrorLength][0] = fieldId;
				 			 	jq.validationEngine.ajaxValidArray[ajaxErrorLength][1] = false;
				 			 	existInarray = false;
			 			 	}
				
			 			 	jq.validationEngine.ajaxValid = false;
							promptText += jq.validationEngine.settings.allrules[customAjaxRule].alertText+"<br />";
							jq.validationEngine.updatePromptText(ajaxCaller,promptText,"",true);				
						 }else{	 
						 	_checkInArray(true);
						 	jq.validationEngine.ajaxValid = true; 						   
	 			 			if(jq.validationEngine.settings.allrules[customAjaxRule].alertTextOk){	// NO OK TEXT MEAN CLOSE PROMPT	 			
	 			 				 				jq.validationEngine.updatePromptText(ajaxCaller,jq.validationEngine.settings.allrules[customAjaxRule].alertTextOk,"pass",true);
 			 				}else{
				 			 	ajaxValidate = false;		 	
				 			 	jq.validationEngine.closePrompt(ajaxCaller);
 			 				}		
			 			 }
			 			function  _checkInArray(validate){
			 				for(x=0;x<ajaxErrorLength;x++){
			 			 		if(jq.validationEngine.ajaxValidArray[x][0] == fieldId){
			 			 			jq.validationEngine.ajaxValidArray[x][1] = validate;
			 			 			existInarray = true;
			 			 		
			 			 		}
			 			 	}
			 			}
			 		}				
				});
			}
		}
		function _confirm(caller,rules,position){		 // VALIDATE FIELD MATCH
			confirmField = rules[position+1];
			
			if(jq(caller).attr('value') != jq("#"+confirmField).attr('value')){
				jq.validationEngine.isError = true;
				promptText += jq.validationEngine.settings.allrules["confirm"].alertText+"<br />";
			}
		}
		function _length(caller,rules,position){    	  // VALIDATE LENGTH
		
			startLength = eval(rules[position+1]);
			endLength = eval(rules[position+2]);
			feildLength = jq(caller).attr('value').length;

			if(feildLength<startLength || feildLength>endLength){
				jq.validationEngine.isError = true;
				promptText += jq.validationEngine.settings.allrules["length"].alertText+startLength+jq.validationEngine.settings.allrules["length"].alertText2+endLength+jq.validationEngine.settings.allrules["length"].alertText3+"<br />"
			}
		}
		function _maxCheckbox(caller,rules,position){  	  // VALIDATE CHECKBOX NUMBER
		
			nbCheck = eval(rules[position+1]);
			groupname = jq(caller).attr("name");
			groupSize = jq("input[name='"+groupname+"']:checked").size();
			if(groupSize > nbCheck){	
				jq.validationEngine.showTriangle = false;
				jq.validationEngine.isError = true;
				promptText += jq.validationEngine.settings.allrules["maxCheckbox"].alertText+"<br />";
			}
		}
		function _minCheckbox(caller,rules,position){  	  // VALIDATE CHECKBOX NUMBER
		
			nbCheck = eval(rules[position+1]);
			groupname = jq(caller).attr("name");
			groupSize = jq("input[name='"+groupname+"']:checked").size();
			if(groupSize < nbCheck){	
			
				jq.validationEngine.isError = true;
				jq.validationEngine.showTriangle = false;
				promptText += jq.validationEngine.settings.allrules["minCheckbox"].alertText+" "+nbCheck+" "+jq.validationEngine.settings.allrules["minCheckbox"].alertText2+"<br />";
			}
		}
		return(jq.validationEngine.isError) ? jq.validationEngine.isError : false;
	},
	submitForm : function(caller){
		if(jq.validationEngine.settings.ajaxSubmit){		
			if(jq.validationEngine.settings.ajaxSubmitExtraData){
				extraData = jq.validationEngine.settings.ajaxSubmitExtraData;
			}else{
				extraData = "";
			}
			jq.ajax({
			   	type: "POST",
			   	url: jq.validationEngine.settings.ajaxSubmitFile,
			   	async: true,
			   	data: jq(caller).serialize()+"&"+extraData,
			   	error: function(data,transport){ jq.validationEngine.debug("error in the ajax: "+data.status+" "+transport) },
			   	success: function(data){
			   		if(data == "true"){			// EVERYTING IS FINE, SHOW SUCCESS MESSAGE
			   			jq(caller).css("opacity",1)
			   			jq(caller).animate({opacity: 0, height: 0}, function(){
			   				jq(caller).css("display","none");
			   				jq(caller).before("<div class='ajaxSubmit'>"+jq.validationEngine.settings.ajaxSubmitMessage+"</div>");
			   				jq.validationEngine.closePrompt(".formError",true); 	
			   				jq(".ajaxSubmit").show("slow");
			   				if (jq.validationEngine.settings.success){	// AJAX SUCCESS, STOP THE LOCATION UPDATE
								jq.validationEngine.settings.success && jq.validationEngine.settings.success(); 
								return false;
							}
			   			})
		   			}else{						// HOUSTON WE GOT A PROBLEM (SOMETING IS NOT VALIDATING)
			   			data = eval( "("+data+")");	
			   			if(!data.jsonValidateReturn){
			   				 jq.validationEngine.debug("you are not going into the success fonction and jsonValidateReturn return nothing");
			   			}
			   			errorNumber = data.jsonValidateReturn.length	
			   			for(index=0; index<errorNumber; index++){	
			   				fieldId = data.jsonValidateReturn[index][0];
			   				promptError = data.jsonValidateReturn[index][1];
			   				type = data.jsonValidateReturn[index][2];
			   				jq.validationEngine.buildPrompt(fieldId,promptError,type);
		   				}
	   				}
   				}
			})	
			return true;
		}
		// LOOK FOR BEFORE SUCCESS METHOD		
			if(!jq.validationEngine.settings.beforeSuccess()){
				if (jq.validationEngine.settings.success){	// AJAX SUCCESS, STOP THE LOCATION UPDATE
					if(jq.validationEngine.settings.unbindEngine){ jq(caller).unbind("submit") }
					jq.validationEngine.settings.success && jq.validationEngine.settings.success(); 
					return true;
				}
			}else{
				return true;
			} 
		return false;
	},
	buildPrompt : function(caller,promptText,type,ajaxed) {			// ERROR PROMPT CREATION AND DISPLAY WHEN AN ERROR OCCUR
		if(!jq.validationEngine.settings){
			jq.validationEngine.defaultSetting()
		}
		deleteItself = "." + jq(caller).attr("id") + "formError"
	
		if(jq(deleteItself)[0]){
			jq(deleteItself).stop();
			jq(deleteItself).remove();
		}
		var divFormError = document.createElement('div');
		var formErrorContent = document.createElement('div');
		linkTofield = jq.validationEngine.linkTofield(caller)
		jq(divFormError).addClass("formError")
		
		if(type == "pass"){ jq(divFormError).addClass("greenPopup") }
		if(type == "load"){ jq(divFormError).addClass("blackPopup") }
		if(ajaxed){ jq(divFormError).addClass("ajaxed") }
		
		jq(divFormError).addClass(linkTofield);
		jq(formErrorContent).addClass("formErrorContent");
		
		jq("body").append(divFormError);
		jq(divFormError).append(formErrorContent);
			
		if(jq.validationEngine.showTriangle != false){		// NO TRIANGLE ON MAX CHECKBOX AND RADIO
			var arrow = document.createElement('div');
			jq(arrow).addClass("formErrorArrow");
			jq(divFormError).append(arrow);
			if(jq.validationEngine.settings.promptPosition == "bottomLeft" || jq.validationEngine.settings.promptPosition == "bottomRight"){
			jq(arrow).addClass("formErrorArrowBottom")
			jq(arrow).html('<div class="line1"><!-- --></div><div class="line2"><!-- --></div><div class="line3"><!-- --></div><div class="line4"><!-- --></div><div class="line5"><!-- --></div><div class="line6"><!-- --></div><div class="line7"><!-- --></div><div class="line8"><!-- --></div><div class="line9"><!-- --></div><div class="line10"><!-- --></div>');
		}
			if(jq.validationEngine.settings.promptPosition == "topLeft" || jq.validationEngine.settings.promptPosition == "topRight"){
				jq(divFormError).append(arrow);
				jq(arrow).html('<div class="line10"><!-- --></div><div class="line9"><!-- --></div><div class="line8"><!-- --></div><div class="line7"><!-- --></div><div class="line6"><!-- --></div><div class="line5"><!-- --></div><div class="line4"><!-- --></div><div class="line3"><!-- --></div><div class="line2"><!-- --></div><div class="line1"><!-- --></div>');
			}
		}
		jq(formErrorContent).html(promptText)
	
		callerTopPosition = jq(caller).offset().top;
		callerleftPosition = jq(caller).offset().left;
		callerWidth =  jq(caller).width();
		inputHeight = jq(divFormError).height();
	
		/* POSITIONNING */
		if(jq.validationEngine.settings.promptPosition == "topRight"){callerleftPosition +=  callerWidth -30; callerTopPosition += -inputHeight -10; }
		if(jq.validationEngine.settings.promptPosition == "topLeft"){ callerTopPosition += -inputHeight -10; }
		
		if(jq.validationEngine.settings.promptPosition == "centerRight"){ callerleftPosition +=  callerWidth +13; }
		
		if(jq.validationEngine.settings.promptPosition == "bottomLeft"){
			callerHeight =  jq(caller).height();
			callerleftPosition = callerleftPosition;
			callerTopPosition = callerTopPosition + callerHeight + 15;
		}
		if(jq.validationEngine.settings.promptPosition == "bottomRight"){
			callerHeight =  jq(caller).height();
			callerleftPosition +=  callerWidth -30;
			callerTopPosition +=  callerHeight + 15;
		}
		jq(divFormError).css({
			top:callerTopPosition,
			left:callerleftPosition,
			opacity:0
		})
		return jq(divFormError).animate({"opacity":0.87},function(){return true;});	
	},
	updatePromptText : function(caller,promptText,type,ajaxed) {	// UPDATE TEXT ERROR IF AN ERROR IS ALREADY DISPLAYED
		
		linkTofield = jq.validationEngine.linkTofield(caller);
		var updateThisPrompt =  "."+linkTofield;
		
		if(type == "pass") { jq(updateThisPrompt).addClass("greenPopup") }else{ jq(updateThisPrompt).removeClass("greenPopup")};
		if(type == "load") { jq(updateThisPrompt).addClass("blackPopup") }else{ jq(updateThisPrompt).removeClass("blackPopup")};
		if(ajaxed) { jq(updateThisPrompt).addClass("ajaxed") }else{ jq(updateThisPrompt).removeClass("ajaxed")};
	
		jq(updateThisPrompt).find(".formErrorContent").html(promptText);
		callerTopPosition  = jq(caller).offset().top;
		inputHeight = jq(updateThisPrompt).height();
		
		if(jq.validationEngine.settings.promptPosition == "bottomLeft" || jq.validationEngine.settings.promptPosition == "bottomRight"){
			callerHeight =  jq(caller).height();
			callerTopPosition =  callerTopPosition + callerHeight + 15;
		}
		if(jq.validationEngine.settings.promptPosition == "centerRight"){  callerleftPosition +=  callerWidth +13;}
		if(jq.validationEngine.settings.promptPosition == "topLeft" || jq.validationEngine.settings.promptPosition == "topRight"){
			callerTopPosition = callerTopPosition  -inputHeight -10;
		}
		jq(updateThisPrompt).animate({ top:callerTopPosition });
	},
	linkTofield : function(caller){
		linkTofield = jq(caller).attr("id") + "formError";
		linkTofield = linkTofield.replace(/\[/g,""); 
		linkTofield = linkTofield.replace(/\]/g,"");
		return linkTofield;
	},
	closePrompt : function(caller,outside) {						// CLOSE PROMPT WHEN ERROR CORRECTED
		if(!jq.validationEngine.settings){
			jq.validationEngine.defaultSetting()
		}
		if(outside){
			jq(caller).fadeTo("fast",0,function(){
				jq(caller).remove();
			});
			return false;
		}
		if(typeof(ajaxValidate)=='undefined'){ajaxValidate = false}
		if(!ajaxValidate){
			linkTofield = jq.validationEngine.linkTofield(caller);
			closingPrompt = "."+linkTofield;
			jq(closingPrompt).fadeTo("fast",0,function(){
				jq(closingPrompt).remove();
			});
		}
	},
	debug : function(error) {
		if(!jq("#debugMode")[0]){
			jq("body").append("<div id='debugMode'><div class='debugError'><strong>This is a debug mode, you got a problem with your form, it will try to help you, refresh when you think you nailed down the problem</strong></div></div>");
		}
		jq(".debugError").append("<div class='debugerror'>"+error+"</div>");
	},			
	submitValidation : function(caller) {					// FORM SUBMIT VALIDATION LOOPING INLINE VALIDATION
		var stopForm = false;
		jq.validationEngine.ajaxValid = true;
		jq(caller).find(".formError").remove();
		var toValidateSize = jq(caller).find("[class*=validate]").size();
		
		jq(caller).find("[class*=validate]").each(function(){
			linkTofield = jq.validationEngine.linkTofield(this);
			
			if(!jq("."+linkTofield).hasClass("ajaxed")){	// DO NOT UPDATE ALREADY AJAXED FIELDS (only happen if no normal errors, don't worry)
				var validationPass = jq.validationEngine.loadValidation(this);
				return(validationPass) ? stopForm = true : "";					
			};
		});
		ajaxErrorLength = jq.validationEngine.ajaxValidArray.length;		// LOOK IF SOME AJAX IS NOT VALIDATE
		for(x=0;x<ajaxErrorLength;x++){
	 		if(jq.validationEngine.ajaxValidArray[x][1] == false){
	 			jq.validationEngine.ajaxValid = false;
 			}
 		}
		if(stopForm || !jq.validationEngine.ajaxValid){		// GET IF THERE IS AN ERROR OR NOT FROM THIS VALIDATION FUNCTIONS
			if(jq.validationEngine.settings.scroll){
				destination = jq(".formError:not('.greenPopup'):first").offset().top;
				jq(".formError:not('.greenPopup')").each(function(){
					testDestination = jq(this).offset().top;
					if(destination>testDestination){
						destination = jq(this).offset().top;
					}
				})
				jq("html:not(:animated),body:not(:animated)").animate({ scrollTop: destination}, 1100);
			}
			return true;
		}else{
			return false;
		}
	}
}
})(jQuery);



(function(jq) {
	jq.fn.validationEngineLanguage = function() {};
	jq.validationEngineLanguage = {
		newLang: function() {
			jq.validationEngineLanguage.allRules = 	{"required":{    			// Add your regex rules here, you can take telephone as an example
						"regex":"none",
						"alertText":"* This field is required",
						"alertTextCheckboxMultiple":"* Please select an option",
						"alertTextCheckboxe":"* This checkbox is required"},
					"length":{
						"regex":"none",
						"alertText":"*Between ",
						"alertText2":" - ",
						"alertText3": " characters"},
					"maxCheckbox":{
						"regex":"none",
						"alertText":"* Checks allowed Exceeded"},	
					"minCheckbox":{
						"regex":"none",
						"alertText":"* Please select ",
						"alertText2":" options"},	
					"confirm":{
						"regex":"none",
						"alertText":"* Your field is not matching"},		
					"telephone":{
						"regex":"/^[0-9\-\(\)\ ]+$/",
						"alertText":"* Invalid phone number"},	
					"email":{
						"regex":"/^[a-zA-Z0-9_\.\-]+\@([a-zA-Z0-9\-]+\.)+[a-zA-Z0-9]{2,4}$/",
						"alertText":"* Invalid email address"},	
					"date":{
                         "regex":"/^[0-9]{4}\-\[0-9]{1,2}\-\[0-9]{1,2}$/",
                         "alertText":"* Invalid date, must be in YYYY-MM-DD format"},
					"onlyNumber":{
						"regex":"/^[0-9\ ]+$/",
						"alertText":"* Numbers only"},	
					"noSpecialCaracters":{
						"regex":"/^[0-9a-zA-Z]+$/",
						"alertText":"* No special caracters allowed"},	
					"ajaxUser":{
						"file":"validateUser.php",
						"extraData":"name=eric",
						"alertTextOk":"* This user is available",	
						"alertTextLoad":"* Loading, please wait",
						"alertText":"* This user is already taken"},	
					"ajaxName":{
						"file":"validateUser.php",
						"alertText":"* This name is already taken",
						"alertTextOk":"* This name is available",	
						"alertTextLoad":"* Loading, please wait"},		
					"onlyLetter":{
						"regex":"/^[a-zA-Z\ \']+$/",
						"alertText":"* Letters only"}
					}	
		}
	}
})(jQuery);

jq(document).ready(function() {	
	jq.validationEngineLanguage.newLang()
});