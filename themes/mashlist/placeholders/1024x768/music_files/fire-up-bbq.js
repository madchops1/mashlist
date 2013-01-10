//alert('url has been declared');
$(function(){

	  $(window).bind( 'hashchange', function(e) {
		  
		  // IE DOESN'T LIKE CONSOLE
		  //if (!window.console) { console = []; }
		  
		  //console.log('url is: ' + url)
		  var url;
		  url = $.param.fragment();
		  console.log('url is: ' + url);
		  
	    if(url=='')  {
	    	
	    	$('#landing-section').show();
	    	//$('#sidebar-top-default').hide(); 	// HIDE THE START SEARCH BUTTON
	    	$('#sidebar-top-filter').show();		// SHOW THE FILTERS
	    	$('#filter-results-section').hide();	// HIDE RESULTS		
	    	
	    }
	    else if((url=='my_library')){

            var self = $(this);
            $('#sidebar-tab-library').addClass('active');
            $('#sidebar-tab-search').removeClass('active');
            //$('#sidebar-top-default').show();   	// SHOW THE START SEARCH BUTTON
            $('#sidebar-top-filter').hide();	    // HIDE THE FILTERS
            
            //$(".heading-search").removeClass("active");
            $(".sidebar-search").fadeOut(function() {
                $(".sidebar-my-library").fadeIn(function() {
                    var myLibraryScrollBar = $(".top-level.active").siblings().children(".sub-section-wrap");

                    myLibraryScrollBar.jScrollPane({
                        verticalDragMinHeight: 26,
                        verticalDragMaxHeight: 26
                    });
                });
            });

            console.log('default playlist: ' + default_playlist);
	    	var default_playlist = $('p:contains("No Songs Loaded")');
            console.log('default playlist length: ' + default_playlist.length)
	    	if(default_playlist.length==1)  {
	    	$('#landing-section').show();
	    	$('#playlist-section').hide();  //issues/56
	    	}
	    	else {
	    		UI.showContentSectionId('playlist-section');
	    	}

	    }
	    else if(url=='search')  {
	    	//$('#jquery_jplayer_1').jPlayer('stop');
	    	//console.log($.cookie('pageloaded'));
            
	    	// IF CLICK SEARCH TAB RESET THE PLAYER
	    	//console.log('reset player');
	    	//var songArray = eval('(' + data  + ')');
	    	//searchUI.setPlaylist();
	    	//audioPlaylist.playlistChange(0);

	    	//$('#sidebar-top-default').hide(); 	// HIDE THE START SEARCH BUTTON
        $('#sidebar-top-filter').show();	// SHOW THE FILTERS
            
	    	var self = $(this);
            $('#sidebar-tab-search').addClass('active');   
            $('#sidebar-tab-library').removeClass('active');         
            searchClick();
            //$.cookie('pageloaded',1,{expires:10});
	    }
	    else if(url=='search/detail')  {
	      //$('#song-details-section').show();
	      $('#filter-results-section').hide();
	    }
	    else if(url=='my_library/detail')  {
		      //$('#song-details-section').show();
		      $('#playlist-section').hide();
		    }
	    

	  });
	  
	  // Since the event is only triggered when the hash changes, we need to trigger
	  // the event now, to handle the hash the page may have loaded with.
	  $(window).trigger( 'hashchange' );

});
