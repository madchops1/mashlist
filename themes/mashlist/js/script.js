jQuery.fx.interval = 50;

var duration = 5000;
var interval;

$(document).ready(function () {

	/* Slideshow */
	$('#slideshow .slides ul').each(function () {
		$(this).children('li').append('<span class="progress" />');
		intervalFunction(true);
	});

	$('#slideshow .slides li a').click(function (e) {
		setCurrentSlide($(this).parent());
		intervalFunction(false);
		return false;
	});


	/* Gallery scroll */
	slides = $('#slideshow .slides');
	slides_ul = $('#slideshow .slides ul');
	last_child = $('#slideshow .slides ul').children('li:last-child');
	slides_width = last_child.offset().left + last_child.outerWidth() + 30 - $('body').width() + 80;
	//slides_width = 20 + last_child.outerWidth() + 30 - $('body').width() + 80;
	slides.hover(function (e) {
		slides_ul.animate({'left': -1 * slides_width * (e.pageX/$('body').width()) + 40}, 500, function () {
			slides.bind('mousemove', function (e) {
				slides_ul.stop().css({'left': -1 * slides_width * (e.pageX/$('body').width()) + 40});
			});
		});
	}, function () {
		slides.unbind('mousemove');
	});


	/* Slide caption on hover */
	$('#slideshow .slides li a').hover(function () {
		info = $('.slide_info_hover');
		info.html($(this).parent().attr('data-title'));
		$(this).bind('mousemove', function (e) {
			position = $(this).offset().left + $(this).outerWidth()/2 - info.outerWidth()/2;
			info.stop().animate({'left': position + 'px', 'opacity': 1});
		});
		info.stop().animate({'opacity': 1});
	}, function () {
		$('.slide_info_hover').stop().animate({'opacity': 0});
	});


	/* Navigation */
	function scrollToContent (file) {
		if (file == '') {
			$('#slideshow').animate({'top': '0%'}, 1500);
			$('#content').fadeOut(function () {
				$(this).empty();
			});
			intervalFunction(false);
			return;
		}
		ytplayer = document.getElementById('myytplayer');
		if (ytplayer) ytplayer.stopVideo();
		$('#slideshow').animate({'top': '-100%'}, 1500);
		$.ajax({
			url: 'pages/' + file + '.html',
			type: 'GET',
			success: function (data) {
				$('#content').fadeOut(function () {
					$('header nav .selected').removeClass('selected');
					$('header nav a[href="#!' + file + '"]').addClass('selected');
					$(this).html(data).fadeIn();
					$('#content header').html($('#slideshow header').html());
					clearTimeout(interval);
					setTimeout(function () {
						refreshScripts();
					}, 100);
				});
			},
			error: function (xhr) {
				alert('Error: ' + xhr.status);
			}
		});
	}


	/* Internal links */
	$('a').live('click', function () {
		if (unescape($(this).attr('href')).match(/^#!/)) scrollToContent(unescape($(this).attr('href')).replace('#!', ''));
	});


	/* Handle URL address links */
	scrollToContent(location.hash.replace('#!', ''));


	function refreshScripts () {

		/* Contact form */
		$('#contact_form').submit(function () {
			$.ajax({
				type: 'POST',
				url: 'contact.php',
				data: {
					name: $('#contact_form input#name').val(),
					email: $("#contact_form input#email").val(),
					text: $("#contact_form textarea").val()
				},
				success: function(data) {
					if ( data == 'sent' ) {
						$('#contact_form .status').html('E-mail has been sent.');
					} else if ( data == 'invalid' ) {
						$('#contact_form .status').html('Your name, email or message is invalid.');
					} else {
						$('#contact_form .status').html('E-mail could not be sent.');					
					}
				},
				error: function () {
					$('#contact_form .status').html('E-mail could not be sent.');
				}
			});
			return false;
		});

		/* Masonry */
		$('.portfolio, .masonry').not('.no-masonry').masonry();

		/* Contact map */
		$('.google_map').each(function (index) {
        	marker_location = new google.maps.LatLng($(this).attr('data-latitude'), $(this).attr('data-longitude'));
	        var map = new google.maps.Map($(this)[index], {
	          zoom: 17,
	          center: marker_location,
	          mapTypeId: google.maps.MapTypeId.ROADMAP
	        });
	        var marker = new google.maps.Marker({
	          position: marker_location,
	          map: map
	        });
		});

		/* Accordion */
		$('.accordion li').each(function () {
			toggle = $(this).children('.toggle');
			toggle.text($(this).hasClass('opened') ? '-' : '+');
		});
		$('.accordion .toggle').click(function () {
			$(this).parent('li').children('p').slideToggle();
			$(this).text(($(this).text() == '-') ? '+' : '-');
			return false;
		});

		/* Tabs */
		$('.tabs .selectors a').click(function () {
			/* Select menu item */
			$('.tabs .selectors li').removeClass('selected');
			$(this).parent('li').addClass('selected');
			/* Display tab */
			$('.tabs .tab').removeClass('selected');
			$('.tabs .tab[data-tab="' + $(this).attr('data-tab') + '"]').addClass('selected');
			return false;
		});

		/* Category filter */
		$('.filter a').click(function () {
			/* Select menu item */
			$('.filter a').removeClass('selected');
			$(this).addClass('selected');
			/* Fade in category */
			$('.filterable li, .filterable li img').fadeTo('slow', 0.4);
			$('.filterable li.' + $(this).attr('data-category') + ', .filterable li.' + $(this).attr('data-category') +' img').stop().fadeTo('slow', 1);
			return false;
		});

		/* Fancybox */
		$('a.fancybox_photo').fancybox({
			'transitionIn'		: 'elastic',
			'padding'			: 0,
			'overlayColor'		: '#000'
		});
		$('a.fancybox_video').fancybox({
			'transitionIn'		: 'elastic',
			'padding'			: 0,
			'overlayColor'		: '#000',
			'type'				: 'iframe'
		});

	    /* Recent tweets */
	    $('.tweets').each(function () {
		    var user = $(this).attr('data-source');
		    var el = $(this); 
		    $.getJSON('http://twitter.com/statuses/user_timeline.json?screen_name=' + user + '&count=5&callback=?', function(data) {
		        el.children('blockquote').text(data[0].text);
		        el.children('.author').html('<a href="http://twitter.com/' + data[0].user.screen_name + '">@' + data[0].user.screen_name + '</a>');
		        el.children('.time').text(data[0].created_at).prettyDate();
				$('.portfolio, .masonry').not('.no-masonry').masonry();
		    });
	    });

	}


	/* Settings */
	$('#settings a').click(function () {
		$('#settings a').removeClass('selected');
		$(this).addClass('selected');
		$('body').removeClass('black');
		return false;
	});
	$('#settings a.black').click(function () {
		$('body').addClass('black');
	})
	
});



function setCurrentSlide (current) {

	/* Set current */
	$('.current').removeClass('current');
	current.addClass('current');

	/* Reset progress bars */
	current.prevAll().children('.progress').stop().css('width', '100%');
	current.nextAll().children('.progress').stop().css('width', '0%');
	current.children('.progress').stop().css('width', '0%');

	/* Image fading */
	old_image = $('.slideshow_image');
	if (current.attr('data-type') == 'photo') {	

		/* Resize and append image */
		height = $('#slideshow').height();
		width = $('#slideshow').width();
		$("#slideshow").append('<img src="' + current.attr('data-image-url') + '" class="slideshow_image" style="opacity:0;">');

		/* Update captions */
		$('.slide_info h2').fadeOut(function (){
			$(this).html(current.attr('data-title')).fadeIn(2000);
		});
		$('.slide_info p').fadeOut(function (){
			$(this).html(current.attr('data-description')).fadeIn(2000);
		});

	} else if (current.attr('data-type') == 'video') {

		$("#slideshow").append('<div class="slideshow_image" style="opacity:0;"><div id="slideshow_video"></div></div>');
	    swfobject.embedSWF('http://www.youtube.com/v/' + current.attr('data-video-url') + '?enablejsapi=1&playerapiid=ytplayer&version=3',
        	'slideshow_video', $('#slideshow').width(), $('#slideshow').height(), '8', null, null, { allowScriptAccess: 'always', wmode: 'transparent' }, { id: 'myytplayer' });
		
		/* Update captions */
		$('.slide_info h2').fadeOut();
		$('.slide_info p').fadeOut();

	}

	$('.slideshow_image').not(old_image).animate({'opacity': 1}, 1000, function () {
		old_image.remove();
	});

	/* Progress bar */
	current.children('.progress').animate({'width': '100%'}, duration, 'linear');

}



function intervalFunction (now) {
	clearTimeout(interval);

	if (now) {
		current = $('.current').next();
		setCurrentSlide((current.length == 0) ? $('#slideshow .slides ul').children(':first-child') : current);
	}

	interval = setTimeout(function () { intervalFunction(true); }, duration);
}



function onYouTubePlayerReady(playerId) {
	ytplayer = document.getElementById('myytplayer');
	ytplayer.playVideo();
	length = ytplayer.getDuration()*1000;
	clearTimeout(interval);
	interval = setTimeout(function () { intervalFunction(true); }, length);
	$('.slides .current .progress').stop().css('width', '0%').animate({'width': '100%'}, length, 'linear');
}