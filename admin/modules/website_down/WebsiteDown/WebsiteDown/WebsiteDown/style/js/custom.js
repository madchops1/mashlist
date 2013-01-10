/************************************************************************/// CAROUSEL /************************************************************************/
jQuery.easing['BounceEaseOut'] = function(p, t, b, c, d) {
	if ((t/=d) < (1/2.75)) {
		return c*(7.5625*t*t) + b;
	} else if (t < (2/2.75)) {
		return c*(7.5625*(t-=(1.5/2.75))*t + .75) + b;
	} else if (t < (2.5/2.75)) {
		return c*(7.5625*(t-=(2.25/2.75))*t + .9375) + b;
	} else {
		return c*(7.5625*(t-=(2.625/2.75))*t + .984375) + b;
	}
};

function mycarousel_initCallback(carousel) {
    jQuery('a.control, a.control2').bind('click', function() {
        carousel.scroll(jQuery.jcarousel.intval(jQuery(this).text()));
        return false;
    });
};


jQuery(document).ready(function() {
    jQuery('#page').jcarousel({
    	vertical: true,
        easing: 'BounceEaseOut',
        animation: 1000,
        scroll: 1,
        visible: 1,
        initCallback: mycarousel_initCallback,
        buttonNextHTML: null,
        buttonPrevHTML: null

    });
});

var j = jQuery.noConflict( );


/************************************************************************/// SLIDING LABELS /************************************************************************/
	/*
	Sliding labels is open source code by Tim Wright of CSSKarma.com
	Use as you see fit, I'd like it if you kept this in the code, but 
	basically share it and don't be a jerk.
	
	Support:
	http://www.csskarma.com/blog/sliding-labels-v2
	
	Version: 1.1
*/

j(function(){
j('form#info .slider label').each(function(){
	var restingPosition = '5px';
	
	// style the label with JS for progressive enhancement
	j(this).css({
		 	'position' : 'absolute',
	 		'top' : '9px',
			'left' : restingPosition,
			'display' : 'inline',
    		'z-index' : '99'
	});
	
	
	var inputval = j(this).next().val();
	
	// grab the label width, then add 5 pixels to it
	var labelwidth = j(this).width();
	var labelmove = labelwidth + 5 +'px';
	
	//onload, check if a field is filled out, if so, move the label out of the way
	if(inputval !== ''){
		j(this).stop().animate({ 'left':'-'+labelmove }, 1);
	}    	
	
	// if the input is empty on focus move the label to the left
	// if it's empty on blur, move it back
	j('input, textarea').focus(function(){
		var label = j(this).prev('label');
		var width = j(label).width();
		var adjust = width + 5 + 'px';
		var value = j(this).val();
		
		if(value == ''){
			label.stop().animate({ 'left':'-'+adjust }, 'fast');
		} else {
			label.css({ 'left':'-'+adjust });
		}
	}).blur(function(){
		var label = j(this).prev('label');
		var value = j(this).val();
		
		if(value == ''){
			label.stop().animate({ 'left':restingPosition }, 'fast');
		}	
		
	});	
}); // End "each" statement
}); // End loaded jQuery




/************************************************************************/// VALIDATION /************************************************************************/

jq(document).ready(function() {
			// SUCCESS AJAX CALL, replace "success: false," by:     success : function() { callSuccessFunction() },
			jq("#info").validationEngine()
			//jq.validationEngine.loadValidation("#date")
			//alert(jq("#info").validationEngine({returnIsValid:true}))
			//jq.validationEngine.buildPrompt("#date","This is an example","error")	 		 // Exterior prompt build example								 // input prompt close example
			//jq.validationEngine.closePrompt(".formError",true) 							// CLOSE ALL OPEN PROMPTS
		});
		

		
/************************************************************************/// SUBSCRIBE /************************************************************************/


var w = jQuery.noConflict( );
w(document).ready(function() {
				
				w('.disappear').focus(function() {
		
					if(w(this).val() == "Enter your email to get updates and information")
						w(this).val('');
		
				}).blur(function() {
		
					if(w(this).val() == "")
						w(this).val('Enter your email to get updates and information');
		
				});
				
			});
/************************************************************************/// AJAX SUBMIT /************************************************************************/


jq(document).ready(function() {
			// SUCCESS AJAX CALL, replace "success: false," by:     success : function() { callSuccessFunction() }, 
			jq("#info").validationEngine({
				ajaxSubmit: true,
					ajaxSubmitFile: "contact.php",
					ajaxSubmitMessage: "Thank you for your message!",
				success :  false,
				failure : function() {}
			})	
		});

jq(document).ready(function() {
			// SUCCESS AJAX CALL, replace "success: false," by:     success : function() { callSuccessFunction() }, 
			jq("#form-email").validationEngine({
				ajaxSubmit: true,
					ajaxSubmitFile: "subscribe.php",
					ajaxSubmitMessage: "Thanks for your support and interest!",
				success :  false,
				failure : function() {}
			})	
		});
