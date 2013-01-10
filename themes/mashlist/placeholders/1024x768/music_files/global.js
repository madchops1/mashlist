/* ---------------------------------------------------------------------
Global JavaScript & jQuery

Target Browsers: All
Authors: Zach Walders, Jesse Hanson, Ryan Bailey, Will Jaspers
------------------------------------------------------------------------ */

var NERD = NERD || {};
//var sidebarTabs;

$(document).ready(function() {
	
	/**
	 * CONSOLE ISSUES IN IE
	 */
	//if (!window.console) { console = []; }
	var alertFallback = true;
	if (typeof console === "undefined" || typeof console.log === "undefined") {
		console = {};
		if (alertFallback) {
			console.log = function(msg) {
				//alert(msg);
			};
		} else {
			console.log = function() {};
		}
	}
	
    // Initialize!
    NERD.ExternalLinks.init();
    NERD.AutoReplace.init();
    sidebarTabs.init();
    sidebarDropdown.init();
    sidebarOptions.init();
    sidebarSearchMenu.init();
    customScroll.init();
    sortActive.init();
    rowAlternate.init();
    customCheckbox.init();
    filterMenu.init();
    modalWindows.init();
    customToggles.init();
    headerLogin.init();
    //backButtons.init();
    //myProfile.init();
    tooltips.init();
    playerButtons.init();
    audioPlayer.init();
    $('.editable').inlineEdit();
    featuredItems.init();
    songDetail.init();
    advancedSearch.init();
    //soundsLike.init(); // THIS MIGHT NOT BE NEEDED FOR OLD WAY
    modalForms.init();
    

	
	
	/* 
	 * FOR THE NEW SOUNDS LIKE / SIMILAR TO SELECT BOX FUNCTIONALITY 
	 */
	$('#filter-link-soundslike').click(function(e){
		$('#textbox-soundslike').focus();
	});
	
	$('#textbox-soundslike').change(function() {
	 // alert('Handler for .change() called.');
	  if($('#link-filter-soundslike').hasClass('checked')){
		  //$('#link-filter-soundslike').trigger('click');
		  $('#link-filter-soundslike').trigger('click');
		  $('#link-filter-soundslike').trigger('click');
	  } else {
		  $('#link-filter-soundslike').trigger('click');
	  }
	});
	
	/* 
	 * ENTER KEY HANDLING 
	 */
	$("body input, body select").keypress(function(event) {
	  if ( event.which == 13 ) {
		  event.preventDefault();
	      console.log('clicked enter');
		  $('a.show-songs').trigger('click');
	   }
	});
	
	/*
	 * FORCING ADMIN SONG EDIT LINKS TO target=_blank
	 */
	$('body').ajaxStop(function(){
		$('.song-details-list a').attr('target','_blank');
	});
	//attr('target','_blank');

	
	
	
	/**
	 * TAKE THE TOUR LINK
	 */
	$('#takethetour, #takethetour a').click(function(e){
		e.preventDefault();
		$("#md2_overlay").fadeIn(200);
		$("#takethetour-window").fadeIn(200);
		
	});
	
	$("#md2_overlay").click(function(e){
		e.preventDefault();
		$("#md2_overlay").fadeOut(200);
		$("#takethetour-window").fadeOut(200);
	});
	
	// VIDEO 1
	$(".tourvid").click(function() {
		$.fancybox({
			'padding'		: 0,
			'autoScale'		: false,
			'transitionIn'	: 'none',
			'transitionOut'	: 'none',
			'title'			: this.title,
			'width'			: 680,
			'height'		: 495,
			'href'			: this.href.replace(new RegExp("watch\\?v=", "i"), 'v/'),
			'type'			: 'swf',
			'swf'			: {
							    'wmode'				: 'transparent',
								'allowfullscreen'	: 'true'
							  }
		});

		return false;
	});
	
	
	
	
});




/* ---------------------------------------------------------------------
ExternalLinks
Author: Nerdery Boilerplate

Launches links with a rel="external" in a new window
------------------------------------------------------------------------ */

NERD.ExternalLinks = {
    init: function() {
        $('a[rel=external]').attr('target', '_blank');
    }
};

/* ---------------------------------------------------------------------
AutoReplace
Author: Nerdery Boilerplate

Mimics HTML5 placeholder behavior

Additionally, adds and removes 'placeholder-text' class, used as a styling
hook for when placeholder text is visible or not visible

Additionally, prevents forms from being
submitted if the default text remains in input field - which we may
or may not want to leave in place, depending on usage in site
------------------------------------------------------------------------ */
NERD.AutoReplace = {
    $fields: undefined,

    init: function() {
        var $fields = $('[placeholder]');

        
        
        if ($fields.length !== 0) {
            var self = this;
            self.$fields = $fields.addClass('placeholder-text');
            self.bind();
        }
    },

    bind: function() {
        var self = this;

        self.$fields.each(
            function() {
                var me = $(this);
                var defaultText = me.attr('placeholder');
                me.attr('placeholder', '').val(defaultText);

                me.focus(
                    function() {
                        if (me.val() === defaultText) {
                            me.val('').removeClass('placeholder-text');
                        }
                    }
                );

                me.blur(
                    function() {
                        if (me.val() === '') {
                            me.val(defaultText).addClass('placeholder-text');
                        }
                    }
                );

                me.parents('form').submit(
                    function() {
                        if (me.is('.required') && (me.val() === defaultText || me.val() === "")) {
                            return false;
                        }
                    }
                );
            }
        );
    }
};

/* ---------------------------------------------------------------------
 *
 * General helper methods
 *
 * --------------------------------------------------------------------- */

//only for arrays . use native indexOf() for strings
function getIndexOf(needle, haystack) {
    for (var i = 0; i < haystack.length; i++) {
        if (haystack[i] == needle) {
            return i;
        }
    }
    return -1;
}

//only for arrays . use native lastIndexOf() for strings
function getLastIndexOf(needle, haystack) {
    for (var i = haystack.length; i >= 0; i--) {
        if (haystack[i] == needle) {
            return i;
        }
    }
    return -1;
}

function replaceSpaces(str, replace) {
    return str.replace(/ /g, replace);
}

// implement JSON.stringify serialization
JSON = [];
JSON.stringify = JSON.stringify || function (obj) {
    var t = typeof (obj);
    if (t != "object" || obj === null) {
        // simple data type
        if (t == "string") obj = '"'+obj+'"';
        return String(obj);
    } else {
        // recurse array or object
        var n, v, json = [], arr = (obj && obj.constructor == Array);
        for (n in obj) {
            v = obj[n]; t = typeof(v);
            if (t == "string") v = '"'+v+'"';
            else if (t == "object" && v !== null) v = JSON.stringify(v);
            json.push((arr ? "" : '"' + n + '":') + String(v));
        }
        return (arr ? "[" : "{") + String(json) + (arr ? "]" : "}");
    }
};

modalForms = {
  
    init : function() {
        
        // handle all textarea placeholders
    
        $('form').delegate('textarea', 'focus', function(event){
            if ($(this).attr('placeholder') == $(this).val()) {
                $(this).val('');
            }
        }).delegate('textarea','blur',function(event){
            if ($(this).val() == '') {
                $(this).val($(this).attr('placeholder'));
            }
        });

        $('.modal-details-textarea-wrap').click(function(e) {
            $('#songdetails-modal-notes').focus();
        });
        
    }
    
};

/* ---------------------------------------------------------------------

	Sidebar Tabs

	Controls sidebar tabbed navigation

------------------------------------------------------------------------ */

sidebarTabs = {
	init : function() {
		//var url = $.param.fragment();
		//console.log('the url really matters here: ' + url);
		$(".section-main-task-bar .remove-from-playlist").live('click', function(e) {
		    var self = $(this);
		    playlistUI.removeFromPlaylist();
		    e.preventDefault();
		});
		
		$(".heading-my-library").click(function(e) {
			var self = $(this);
			self.addClass("active");
			
			$(".heading-search").removeClass("active");
			$(".sidebar-search").fadeOut(function() {
			$(".sidebar-my-library").fadeIn(function() {
			var myLibraryScrollBar = $(".top-level.active").siblings().children(".sub-section-wrap");
			
			        myLibraryScrollBar.jScrollPane({
			            verticalDragMinHeight: 26,
			            verticalDragMaxHeight: 26
			        });
			    });
			});
			
			UI.showContentSectionId('playlist-section');
			//e.preventDefault();  //this prevents hashchange tracking, which I need to maintain state -stk
		});

		searchClick = function() {
			$(".heading-my-library").removeClass("active");

			$(".sidebar-my-library").fadeOut(function() {
				$(".sidebar-search").show();
			});

			$("#playlist-section").fadeOut(function() {

				UI.showContentSectionId('filter-results-section');
				
				var sidebarTopScrollBar = $(".sidebar-top-content.alt");
				
			    sidebarTopScrollBar.jScrollPane({
			        verticalDragMinHeight: 26,
			        verticalDragMaxHeight: 26
			    });
			});

			UI.toggleSearchBox('show');
		}

		$(".heading-search").click(function(e) {
			var self = $(this);
			self.addClass("active");
			searchClick();
			//e.preventDefault();  //this prevents hashchange tracking, which I need to maintain state -stk
		});

		$(".start-search").click(function(e) {
			$(".heading-search").addClass("active");
			searchClick();
			//e.preventDefault();
		});

		$(".task-bar-link.new-search").click(function(e) {
			searchClick();
			searchUI.clearAll();
			
			$(".search-menu-link").removeClass("active");
			$(".filter-inner-wrap").hide();
			$(".filter-list-child").hide();
			$(".heading-search").addClass("active");
			$("#filter-default-section").show();
			$("#selected-filters-heading").fadeOut(function() {
				$("#find-music-heading").show();
			});
			$("#results-section").hide();
			$("#filter-section").hide();
		    searchClick();
		    e.preventDefault();
		});
	
		$(".task-bar-link.new-playlist").click(function(e) {
			if (!$("#library-section-my-playlists").hasClass("active")) {
				$("#library-section-my-playlists").trigger("click");
			}
			if($("#my-playlists-div .sub-section-wrap").is(":visible")) {
				$("#my-playlists-div .sub-section-wrap").jScrollPane({
					verticalDragMinHeight: 26,
					verticalDragMaxHeight: 26
				});
			}
			$('.playlist-placeholder').hide();
			playlistUI.newMyPlaylist();
			e.preventDefault();
		});
	
		$(".playlist-placeholder").click(function(e) {
			var self = $(this);
	
			if (!$("#library-section-my-playlists").hasClass("active")) {
				$("#library-section-my-playlists").trigger("click");
			}
	
			if($("#my-playlists-div .sub-section-wrap").is(":visible")) {
				$("#my-playlists-div .sub-section-wrap").jScrollPane({
					verticalDragMinHeight: 26,
					verticalDragMaxHeight: 26
				});
			}
	    
			self.hide();
			playlistUI.newMyPlaylist();
		});
	
	    $(".filter-clear-all").click(function(e) {
			searchClick();
			searchUI.clearAll();
			
			$(".search-menu-link").removeClass("active");
			$(".filter-inner-wrap").hide();
			$(".filter-list-child").hide();
			$(".heading-search").addClass("active");
			
			$("#filter-default-section").fadeIn();
			$("#selected-filters-heading").fadeOut(function() {
			$("#find-music-heading").show();
			});
			$("#results-section").hide();
			$("#filter-section").hide();
			
		    searchClick();
		    e.preventDefault();
	    });
	
		$('.filter-save-search').click(function(e) {
		searchApi.loadModalSearches();
		$('#save-search-modal').fadeIn();
		    e.preventDefault();
		});
	
		$('#sidebar-content-library').delegate('.a-playlist', 'click', function(e) {
			var self = $(this);
			var elId = self.attr("id");
			var nid = elId.substr(elId.lastIndexOf('-') + 1);
			
			if (self.hasClass('favorite')) {
			    playlistItemApi.loadFavoriteSongs(1);
			} else {
			    playlistItemApi.loadPlaylistSongs(nid, 1, 'playlist');
			    console.log('playlist is loaded');
			}
			
			/*
			if (self.hasClass('recommended')) {
			    UI.toggleSearchBox('show');
			    searchUI.clearAll();
			    playlistApi.loadTerms(nid);
			}
			*/
			//e.preventDefault();  To allow hashstate, done by stk
		});
	
		$('#sidebar-content-library').delegate('.sub-section-title input', 'blur', function(e) {
			$el = $(e.target);
			playlistUI.inlineSaveNewMyPlaylist(e, {'value': $el.val()})
		});
	
		$('#sidebar-content-library').delegate('.sub-section-title input', 'keypress', function(e) {
			if (e.keyCode == 13) {
				$el = $(e.target);
				playlistUI.inlineSaveNewMyPlaylist(e, {'value': $el.val()})
		    }
		});
	
		$('.next-25-wrapper .show-next').live('click', function(e) {
			console.log('show more');
			if (UI.activeContentSectionId == 'filter-results-section') {
			    e.preventDefault();                
			    searchApi.loadSearchResults(UI.currentSearchPage + 1);
			} else if (UI.currentPlaylistId == 0) {
			    var page = UI.currentPlaylistPage + 1;
			    playlistItemApi.loadFavoriteSongs(page);
			} else {
			    var page = UI.currentPlaylistPage + 1;
			    playlistItemApi.loadPlaylistSongs(UI.currentPlaylistId, page, UI.currentPlaylistMode);
			}
		
			/*
			 * Re-index the songs when finished ajaxing so that the player will play the new additions.
			 * -kjs plan.io #282
			 * 
			 */
			$('.show-next').ajaxStop(function() {
				var songIndex = 0;
				// REINDEX SEARCH RESULTS
				$('#results-tbody tr').each(function(){
					$(this).attr('data-index',songIndex); // the row
					$(this).children("td").children("a['data-listindex']").attr('data-listindex',songIndex); // the play link
					$(this).children("td").children("span").children("a['data-listindex']").attr('data-listindex',songIndex); // the title button
					songIndex++;
				});
				
				var songIndex = 0;
				// REINDEX PLAYLISTS
				$('#playlist-tbody tr').each(function(){
					$(this).attr('data-index',songIndex);
					$(this).children("td").children("a['data-listindex']").attr('data-listindex',songIndex); // the play link
					$(this).children("td").children("span").children("a['data-listindex']").attr('data-listindex',songIndex); // the title button
					songIndex++;
				});
				
				console.log('songs re-indexed');
			});
		    
		    e.preventDefault();
		});
	
		$('#my-library-saved-searches').delegate('.sub-section-title', 'click', function(e) {
			var $this = $(e.target);
			var id = $this.parent().attr('id');
			var nid = id.substr(id.lastIndexOf('-') + 1);
			searchUI.clearAll();
			UI.toggleSearchBox('show');
			searchApi.loadSearchAndResults(nid, 1);
			e.preventDefault();
		});
	
		$('#my-library-saved-searches').delegate('.option-delete', 'click', function(e) {
			var gearLink = $(e.target);
			var nid = gearLink.closest('.options-list-wrap').attr('id').substr(12);
			var li = $("#my-saved-searches-" + nid);
			gearLink.parent().parent().parent().hide();
			gearLink.parent().parent().parent().remove();
			li.remove();
			searchApi.removeSearch(nid);
			e.preventDefault();
		});
	
		$('#my-library-saved-searches').delegate('.option-open', 'click', function(e) {
			var gearLink = $(e.target);
			var div = gearLink.parent().parent().parent();
			var nid = div.attr("id").substr(12);
			UI.currentSearchId = nid;
			UI.currentPlaylistId = 0;
			gearLink.parent().parent().parent().hide();
			
			searchUI.clearAll();
			searchApi.loadSearch(nid);
			
			$(".heading-search").trigger("click");
			searchUI.activateSearchType(null); //show filters and activate link
			    
			e.preventDefault();
		});
	
		// Click 'Open Playlist' 
		$('#sidebar-content-library > ul > li').not('#my-library-saved-searches').delegate('.option-open', 'click', function(e) {
			var elId = $(this).closest("div").attr("id");
			console.log(elId);
			var nid = elId.substr(elId.lastIndexOf('list') + 4);
			console.log('clicked open playlist!');
			console.log('nid: '+nid+'');
			playlistItemApi.loadPlaylistSongs(nid, 1, 'playlist');
			$('li.my-playlist-li a').removeClass('active');
			$('#span-my-playlist-' + nid).addClass('active');
		});
	
	
		// Click 'Share Playlist'
		$('#sidebar-content-library > ul > li').not('#my-library-saved-searches').delegate('.option-share', 'click', function(e) {
		var link = $(e.target);
		//console.log("Link: " + link);
		    playlistUI.fillShareForm(link);
		    e.preventDefault();
		});
	
		// Click 'Delete Playlist'
		$('#sidebar-content-library > ul > li').not('#my-library-saved-searches').delegate('.option-delete', 'click', function(e) {
		var nid = $(e.target).closest('div').attr('id').substr(12);
		    $(e.target).parent().parent().parent().hide();
		    playlistUI.deletePlaylist(nid);
		    e.preventDefault();
		});
		
		/* TESTING IF ITS THE TRIM FUNCTION CAUSING IE ISSUE 
		 * -kjs 1/26/2012
		 */
		if(typeof String.prototype.trim !== 'function') {
		  String.prototype.trim = function() {
		    return this.replace(/^\s+|\s+$/g, ''); 
		  }
		}

		
		// Click 'Duplicate Playlist'
		$('#sidebar-content-library > ul > li').not('#my-library-saved-searches').delegate('.option-duplicate, .option-add', 'click', function(e) {
			$el = $(e.target);
		
			$el.parent().parent().parent().hide();
			
			var nid = $el.closest('div').attr('id').substr(12);
			var title = $('#my-playlist-' + nid).children('.a-playlist').text();
			var $a = $(playlistUI.newMyPlaylist()).text('Copy of ' + $.trim(title));
			var $myPlaylistsHeader = $('#library-section-my-playlists');
			if (!$myPlaylistsHeader.hasClass('active')) {
			$myPlaylistsHeader.trigger('click');
			}
			playlistApi.copyPlaylist(nid, {
			    'success': function(data) {
			    	playlistUI.currentElId = $a.attr('id');
			        playlistUI.postSaveNewMyPlaylist(data);
			    }
			});
			$("#my-playlists-div .sub-section-wrap").jScrollPane({
				verticalDragMinHeight: 26,
				verticalDragMaxHeight: 26
			});
		    e.preventDefault();
	    });
    }
};

/* ---------------------------------------------------------------------

	Sidebar Dropdown

	Hides and shows items in the sidebar drop down menu

------------------------------------------------------------------------ */

sidebarDropdown = {
    init: function() {
        
        $(".top-level.my-playlists").siblings(".list-section-wrap").show();
        $(".top-level.my-playlists").siblings().children(".sub-section-wrap").show();

        $(".top-level").live('click',function(e) {
            var self = $(this);

            if (self.hasClass("active")) {
                self.removeClass("active");
                self.siblings(".list-section-wrap").slideUp();
            } else {
                $(".top-level").removeClass("active");
                self.addClass("active");
                $(".list-section-wrap").slideUp();
                self.siblings(".list-section-wrap").slideDown(function () {
                    var dropDownScrollBar = self.parent().children().children(".sub-section-wrap");
                    dropDownScrollBar.fadeIn();
                    dropDownScrollBar.jScrollPane({
                        verticalDragMinHeight: 26,
                        verticalDragMaxHeight: 26
                    });
                });
            }

            e.preventDefault();
        });

        
        //add active class to library links
        $(".sub-section-title").live('click',function(e) {
            
            var self = $(this);

            if (!self.hasClass("active")) {
                $(".sub-section-title").removeClass("active");
                self.addClass("active");
            }
            
            $(".checkbox-custom").removeClass("checked");
            $(".checkbox").attr("checked", false);
            //alert('clicked a playlist')
            //e.preventDefault();
        });
        
    }
};

/* ---------------------------------------------------------------------

	Sidebar Options List

	Hides and shows items in the sidebar options pop up menu

------------------------------------------------------------------------ */

sidebarOptions = {
    init: function() {

        $("#sidebar-content-library").delegate('.options-link', 'click',function(e) {            
            $(".options-list-wrap").fadeOut();
            var self = $(this);
            var selfOffset = self.position();
            var scrollBarWrap = self.closest(".sub-section-wrap");
            var parentOffset = scrollBarWrap.position();
            var scrollBarPane = scrollBarWrap.children(".jspContainer").children(".jspPane");
            var scrolledOffset = scrollBarPane.position();
            var optionsMenu = $("#" + self.attr("rel"));

            if (scrolledOffset.top < 0) {
                optionsMenu.css("top", selfOffset.top + parentOffset.top + scrolledOffset.top);
                optionsMenu.css("left", selfOffset.left + parentOffset.left);
            } else {
                optionsMenu.css("top", selfOffset.top + parentOffset.top);
                optionsMenu.css("left", selfOffset.left +parentOffset.left);
            }

            optionsMenu.fadeIn();
            e.preventDefault();
        });

        $(".options-link-close").live('click',function(e) {
            var self = $(this);
            self.parent().fadeOut();
            e.preventDefault();
        });

        $(document).mousedown(function() {
            $(".options-list-wrap").fadeOut();
            //e.preventDefault();
        });

        $(".options-list-wrap" ).mousedown(function(e) {
            e.stopPropagation();
        });

        $(".sub-section-wrap").scroll(function (e) {
            $(".options-list-wrap").hide();
            e.preventDefault();
        });

        $(".options-list-link").click(function (e) {
            var self = $(e.target);
            //assuming each item has same parents
            // a -> li -> ul -> div
            self.parent().parent().parent().hide();
            //e.preventDefault(); //breaks normal link behavior
        });
    }
};

/* ---------------------------------------------------------------------

	Sidebar Search Menu

	Controls the search/filter functions in the sidebar

------------------------------------------------------------------------ */

sidebarSearchMenu = {
    init: function() {
    
        $(".search-menu-link").click(function(e) {
            var self = $(this);
            searchUI.activateSearchType(self);
            
            $("#" + self.attr("rel") + " .filter-list-wrap").jScrollPane({
                verticalDragMinHeight: 26,
                verticalDragMaxHeight: 26
            });
            
            e.preventDefault();
        });

        $(".show-songs").click(function(e) {
            
            //UI.currentPlaylistId = 0;
            
        	var url;
      	  	url = $.param.fragment();
      	  	console.log('url is: ' + url);
        	
            $('.ui-autocomplete').hide();
            
            //assumes search filters are already loaded
            searchApi.loadSearchResults(1);
            
            //handle content transition
            UI.showContentSectionId('results-section');
            
            //playlistApi.loadRecommendedPlaylists();
            
            // CLICKED SHOW SONGS SO LOAD PLAYLIST
            //var elId = $(this).closest("div").attr("id");
            //var nid = elId.substr(elId.lastIndexOf('list') + 4);
            //playlistItemApi.loadPlaylistSongs(nid, 1, 'playlist');
            
            
            //e.preventDefault();
        });

        

    }
};

/* ---------------------------------------------------------------------

	Search And Sort Active States

	Add an active class to search and sort buttons when clicked

------------------------------------------------------------------------ */

sortActive = {
    init: function() {
        $(".sort-link").click(function(e) {
            
            var self = $(this);

            if (self.hasClass("active")) {
                $(".sort-link").removeClass("active");
            } else {
                $(".sort-link").removeClass("active");
                self.addClass("active");
            }

            e.preventDefault();
        });
    }
};

/* ---------------------------------------------------------------------

	Alternating Rows

	Adds a background for every even table row

------------------------------------------------------------------------ */

rowAlternate = {
    init: function() {
        $(".main-table tbody tr:odd").addClass("row-highlight");
    }
};

/* ---------------------------------------------------------------------

	Custom Checkboxes

	Allows styling of checkboxes

	Also includes a "check all/ check none" toggle

------------------------------------------------------------------------ */

customCheckbox = {
    init: function() {
        
        // intentionally having this load on document ready <- someone else wrote this...
        // not sure why this happens here, 
        // maybe to make sure the playlists are there before the custom checkboxes are added
        // - kjs
        playlistApi.getMyPlaylistsAdd();
        
        /**
        * Ensure playlistItems and search results get custom checkbox handlers
        */
        $(".main-table .checkbox-custom").live('click',function(e) {
            var self = $(this);
            if (self.hasClass("checked")) {
                self.removeClass("checked");
                self.children().attr("checked", false);
                if (self.hasClass('checkall')) {
                    //console.log('un-check everything');
                    self.parents("table").find(".checkbox-custom").removeClass("checked");
                    self.parents("table").find(".checkbox").removeAttr("checked");
                }
            } else {
                self.addClass("checked");
                self.children().attr("checked", true);
                if (self.hasClass('checkall')) {
                    //console.log('check everything');
                    self.parents("table").find(".checkbox-custom").addClass("checked");
                    self.parents("table").find(".checkbox").attr("checked","checked");
                }
            }

            e.preventDefault();
        });
        

        $("#add-to-playlist-modal .checkall").toggle(function() {
            $(".modal-add-to-playlist-list").find(".checkbox-custom").addClass("checked");
            $(".modal-add-to-playlist-list").find(".checkbox").attr("checked",true);
			var self = $(this);
			self.addClass("checked");
            self.children().attr("checked", true);
        }, function() {
            $(".modal-add-to-playlist-list").find(".checkbox-custom").removeClass("checked");
            $(".modal-add-to-playlist-list").find(".checkbox").attr("checked", false);
			var self = $(this);
			self.removeClass("checked");
            self.children().attr("checked", false);
        });

        $("#save-search-modal .checkbox-custom").click(function(e) {
            var self = $(this);
            if (self.hasClass("checked")) {
                self.removeClass("checked");
                self.children().attr("checked", false);
            } else {
                $("#save-search-modal .checkbox-custom").removeClass("checked");
                $("#save-search-modal .checkbox-custom").children().attr("checked", false);
                self.addClass("checked");
                self.children().attr("checked", true);
            }
            e.preventDefault();
        });
    
    }
};

/* ---------------------------------------------------------------------

	Filter Menu

	Controls animation for filter sections

	Also controls checkbox active states/ selected active states for
	filter items

------------------------------------------------------------------------ */

filterMenu = {
    init: function() {
        
        $(".filter-link").click(function(e) {
            e.preventDefault();
        });

        $(".parent-filter .filter-link").live('click', function(e) {
            var self = $(this);
            var myParent = self.parent(".filter-list li");
            var rel = self.attr("rel");
            //console.log("REL: " + rel + "");
            var nextLevel = $("#" + self.attr("rel"));

            if (myParent.hasClass("selected-filter")) {
                myParent.removeClass("selected-filter");
                self.removeClass("selected-filter-link");
                
                // IF CLICKING A SELECTED LEVEL THREE PARENT THEN CLOSE LEVEL FOUR
                if(self.hasClass('level-3')){
                	
                	$('li.level-4').hide();
                	
                	console.log('hiding level 4 if click selected level 3');
                }
                
                nextLevel.fadeOut();
            } else {
                self.closest(".filter-list").children("li").removeClass("selected-filter");
                self.closest(".filter-list").children().children(".filter-link").removeClass("selected-filter-link");
                myParent.addClass("selected-filter");
                self.addClass("selected-filter-link");
                
                /*
                 * HIDING FIELDS
                 */
                
                // IF CLICKING A FIRST LEVEL LINK THEN HIDE THE SECOND LEVEL
                // AND UNSET THE SELECTED CLASSES IN LEVEL 2 AND 3
                if(self.hasClass('level-1')){ 
                	$('div.level-1').hide(); 
                	$('div.level-2').hide(); 
                	$('a.level-2').parent('li').removeClass('selected-filter');
                	$('a.level-2').removeClass('selected-filter-link');
                	$('a.level-3').parent('li').removeClass('selected-filter');
                	$('a.level-3').removeClass('selected-filter-link');
                }
                
                // IF CLICKING A SECOND LEVEL THEN HIDE THE THIRD LEVEL
                // AND UNSET THE SELECTEC CLASSES IN LEVEL 3
                if(self.hasClass('level-2')){ 
                	$('div.level-2').hide(); 
                	$('a.level-3').parent('li').removeClass('selected-filter');
                	$('a.level-3').removeClass('selected-filter-link');
                }
                
                // IF CLICKING A LEVEL THREE PARENT THEN HIDE ALL LEVEL FOURS
                if(self.hasClass('level-3')){
                	$('li.level-4').hide();
                	console.log(self);
                	console.log('hide all level 4 before opening a specific level 4');
                }
                
                /*
                 * SHOWING FIELDS
                 */
                
                //self.closest(".filter-inner-wrap").children(".filter-list-child").hide();
                // OPEN THE LEVEL DESIRED
                // LEVEL 4 ACCORIDIANS
                // LEVEL 3 IS THE ANCHORS CLASS THAT OPENS THE LEVEL ACCORDIANS 
                if(self.hasClass('level-3')){
                	var thisFilter = self.attr('filter');
                	$('li[parent='+thisFilter+']').fadeIn('fast');
                	var newHeight = $(self).closest('ul').height();
                	$(self).closest('.jspContainer').height(newHeight);
                	console.log('open level 4');
                	console.log('resize ul new height' + newHeight);
                	//console.log('clicked level 3 - filter '+thisFilter+'');
                } else {
                	// IF CLICKING A LEVEL 2 PARENT THEN FADE OUT LEVEL 4 WHEN OPENED
                	if(self.hasClass('level-2')){
                		console.log('opening level 3');
                		console.log('hiding level 4 when opening level 3');
                		$('li.level-4').hide();
                	}
                	
                	// STANDARD LEVEL FADE IN
                	nextLevel.fadeIn();
                	nextLevel.jScrollPane({
                		verticalDragMinHeight: 26,
                		verticalDragMaxHeight: 26
                	});
                }
            }
            
            e.preventDefault();
        });

        
        
        /*
         * Ensure drill-down items get custom checkbox handlers
         * D-TOOL SEARCH CHECKBOX DELEGATION
         */
        $('#filter-section').delegate('.filter-list .checkbox-custom', 'click', function(e) {
        //$('.filter-list .checkbox-custom').click(function(e) {
            
        	var self = $(this);
            var parentOfLabel = self.parent(".filter-list li");
            var siblingLink = self.siblings(".filter-link");
            var clonedLink = $("#search-" + self.attr("rel"));

            var siblingLinkId = siblingLink.attr("id");
            var searchFilterId = 'search-' + siblingLinkId; // search-link-filter-1320
            var lastDash = siblingLinkId.lastIndexOf('-') + 1;
            var filterId = siblingLinkId.substr(lastDash);
            
            // ADDED BY KARL FOR 4-LEVEL FILTER
            //var clickedLinkLevel = siblingLink.attr
            
            var isAdvanced = false;
            if (self.parent().parent().attr('id') == 'adv-filter-list') {
                isAdvanced = true;
            }
            
            // IF A CHILD THEN CHECK THE PARENTS
            if (self.parent().hasClass("parent-filter")) {
                var filterSetId = siblingLink.attr("rel");
                var filterSet = $('#' + filterSetId);
                if (filterSet.css('display') != 'block') {
                    siblingLink.trigger("click");
                }
            }
            
            // TESTING
            /*
            console.log('self:');
            console.log(self);
            
            console.log('parentOfLabel:');
            console.log(parentOfLabel);
            
            console.log('siblingLink:');
            console.log(siblingLink);
            
            console.log('clonedLink:');
            console.log(clonedLink);
            
            console.log('siblingLinkId:');
            console.log(siblingLinkId);
            
            console.log('searchFilterId:');
            console.log(searchFilterId);
            
            console.log('event:');
            console.log(e);
            */
            
            // UNCHECKING BOX
            if (self.hasClass("checked")) {
                
            	// THIS CODE IS 1000x BETTER
            	// IF UNCHECKING A PARENT THEN UNCHECK ALL THE CHILDREN OF THAT PARENT
            	if(UI.activeFilterSectionId == 'filter-set-vid-3' || UI.activeFilterSectionId == 'filter-set-vid-1' || UI.activeFilterSectionId == 'filter-set-vid-5' || UI.activeFilterSectionId == 'filter-set-vid-14'){
                    var thisParentsFilter = self.attr('filter');
                    $('a[parent='+thisParentsFilter+'].checked').trigger('click');
            	}
            	
                
                self.removeClass("checked");
                parentOfLabel.removeClass("checked-filter");
                siblingLink.removeClass("checked-filter-link");
                clonedLink.remove();
                self.children().attr("checked", false);

                var sidebarTopScrollBar = $(".sidebar-top-content.alt");

                sidebarTopScrollBar.jScrollPane({
                    verticalDragMinHeight: 26,
                    verticalDragMaxHeight: 26
                });
                
                if (isAdvanced) {
                    var id = self.attr('id');
                    var name = id.substr(id.lastIndexOf('-')+1);
                    searchState.advFiltersObj[name] = '';
                } else {
                    
                    if (self.hasClass('vocalinstr')) {
                        searchState.removeVocalInstr(filterId);
                    } else if (self.hasClass('vocaltype')) {
                        searchState.removeVocalType(filterId);
                    } 
                    // SOUNDS LIKE
                    else if (self.hasClass('soundslike')) {
                    	// OLD WAY WITH CHECKBOXES
                        searchState.removeSoundLikes(filterId);
                    	// NEW WAY WITH AUTOCOMPLETE - kjs plan.io #25
                    	//searchState.removeSoundLikes($('#sounds-like-hidden-id').val());
                    }
                    else {
                        searchState.removeFilter(filterId);
                    }
                }
            }
            // CHECKING BOX
            else
            {
                
                var siblingLinkId = siblingLink.attr("id");
                var searchFilterId = 'search-' + siblingLinkId; // search-link-filter-1320

                self.addClass("checked");
                parentOfLabel.addClass("checked-filter");
                siblingLink.addClass("checked-filter-link");
                self.children().change();
                self.children().attr("checked", true);

                var sidebarTopScrollBar = $(".sidebar-top-content.alt");

                sidebarTopScrollBar.jScrollPane({
                    verticalDragMinHeight: 26,
                    verticalDragMaxHeight: 26
                });
                
                // IF A PARENT THEN
                // THE REASON FOR THE CONDITION IS THE ADD TO TAG BOX
                if (parentOfLabel.hasClass("parent-filter")) {
                	// ADD TO TAG BOX
                	siblingLink.clone().removeAttr("rel").removeClass("filter-link checked-filter-link selected-filter-link").addClass("filter-added parent-added").appendTo("#sidebar-top-filter .sidebar-top-content .jspContainer .jspPane").attr("id", searchFilterId).wrapInner("<span class='filter-added-inner'>");
                    // add class for advanced filters
                    if (UI.activeFilterSectionId == 'filter-set-vid-advanced') {
                        $('#' + searchFilterId).addClass("advanced");
                    }
                    self.attr("rel", siblingLinkId);
                }
                // IF A CHILD THEN 
                else {
                	// ADD TO TAG BOX
                    siblingLink.clone().removeAttr("rel").removeClass("filter-link checked-filter-link selected-filter-link").addClass("filter-added").appendTo("#sidebar-top-filter .sidebar-top-content .jspContainer .jspPane").attr("id", searchFilterId).wrapInner("<span class='filter-added-inner'>");
                    //add class for advanced filters
                    if (UI.activeFilterSectionId == 'filter-set-vid-advanced') {
                        $('#' + searchFilterId).addClass("advanced");
                    }
                    self.attr("rel", siblingLinkId);
                }
                
                // NEED TO CHECK ANCESTORS IF NOT ALREADY CHECKED
                // -kjs
                if(UI.activeFilterSectionId == 'filter-set-vid-3' || UI.activeFilterSectionId == 'filter-set-vid-1' || UI.activeFilterSectionId == 'filter-set-vid-5' || UI.activeFilterSectionId == 'filter-set-vid-14'){
                	var thisChildsParent = self.attr('parent');
                	var doesthishavecheckedclass = $('a[filter='+thisChildsParent+']').hasClass('checked');
                	
                	// THIS WORKS FOR ALL INCLUDING LEVEL 4
                	if(doesthishavecheckedclass == false){
                		$('a[filter='+thisChildsParent+']').trigger('click');
                	}
                	
                	// THIS WORKS FOR ALL BUT LEVEL 4 FOR SOME REASON 
                	//$('a[filter='+thisChildsParent+']:not(.checked)').trigger('click');
                }
                
                //this just doesnt makes sense to have here -stk
                $("#find-music-heading").fadeOut(function() {
                    $("#selected-filters-heading").show();
                });
                
                if (isAdvanced) {
                    //figure out which input
                   
                    var id = self.attr('id');
                    var name = id.substr(id.lastIndexOf('-')+1); 
                    //alert('NAME: '+name);
                    
                    // -kjs plan.io #7
                    // FOR THE NEW LT/GT BPM THERE FUNCTIONALITY
                    // THERE NEEDS TO BE 2 FILTERS SET ON CHECK
                    if(name == 'bpm'){
                      // THIS SETS BPMGT AND BPMLT FILTERS UPON CHECKING THE BPM BOX
                      searchState.advFiltersObj['bpmgt'] = $('#adv-search-bpmgt').val();
                      searchState.advFiltersObj['bpmlt'] = $('#adv-search-bpmlt').val();
                    } else {
                      // THIS SETS ADVANCED FILTERS UPON CHECKING THEIR RESPECTIVE BOXES
                      searchState.advFiltersObj[name] = $('#adv-search-' + name).val();
                    }
                } else {
                    
                    if (self.hasClass('vocalinstr')) {
                        searchState.addVocalInstr(filterId);
                    } else if (self.hasClass('vocaltype')) {
                        searchState.addVocalType(filterId);
                    }
                    // SOUNDS LIKE
                    else if (self.hasClass('soundslike')) {
                    	// OLD WAY WITH CHECKBOXES
                    	// REVERTED TO THIS 12/20/2012
                    	//var soundsLikeFilter = $('#sounds-like-hidden-id').val();
                    	//console.log(soundsLikeFilter);
                    	//var soundsLikeBand = self.siblings('filter-link').html();
                    	//console.log("BAND: " + soundsLikeBand);
                    	filterId = $('#textbox-soundslike').val();
                    	searchState.addSoundLikes(filterId); // uses id
                    	
                    	// NEW WAY TEXTBOX -kjs plan.io #25
                    	//var soundsLikeFilter = $('#sounds-like-hidden-id').val();
                    	//console.log("Sounds Like:" + soundsLikeFilter);
                    	//searchState.addSoundLikes(soundsLikeFilter);
                    } else {
                        searchState.addFilter(filterId);  //essentially a .push, unless id is empty, see searchState.js
                    }
                }
            } // END CHECKING BOX

            e.preventDefault();

            $(".filter-added").click(function(e) {
                
                var self = $(this);
                var isAdvanced = false;
                var selfID = self.attr("id");
                var originalLink = "#" + selfID.replace("search-", "");
                if (self.hasClass('advanced')) {
                    isAdvanced = true;
                }
                
                // TESTING
                //console.log("ORIGINAL LINK: "+originalLink);
                
                $(originalLink).removeClass("checked-filter-link selected-filter-link");
                $(originalLink).parent().removeClass('checked').removeAttr('rel');
                $(originalLink).parent().parent().removeClass("checked-filter");
                $(originalLink).parent().siblings().removeClass("checked checked-filter-link");
                $(originalLink).parent().siblings().children().attr("checked", false);
                self.remove();

                // TESTING
                //console.log(selfID);
                
                var lastDash = selfID.lastIndexOf('-') + 1;
                var filterId = selfID.substr(lastDash);
                if (selfID.indexOf('vocalinstr') >= 0) {
                    searchState.removeVocalInstr(filterId);
                } else if (selfID.indexOf('vocaltype') >= 0) {
                    searchState.removeVocalType(filterId);
                } 
                // REMOVED OLD WAY LOOKED FOR "soundlikes" NEW WAY LOOKS FOR "soundslike" -kjs plan.io #239
                //else if (selfID.indexOf('soundlikes') >= 0) {
                else if(selfID.indexOf('soundslike') >= 0){
                    searchState.removeSoundLikes(filterId);
                }
                else if (isAdvanced) {
                    searchState.advFiltersObj[filterId] = "";
                } else {
                    searchState.removeFilter(filterId);
                }

                sidebarTopScrollBar.jScrollPane({
                    verticalDragMinHeight: 26,
                    verticalDragMaxHeight: 26
                });


                UI.showSidebarSection('search');

                $("#playlist-section").fadeOut(function() {
                    
                    UI.showContentSectionId('filter-results-section');
                    
                    var sidebarTopScrollBar = $(".sidebar-top-content.alt");

                    sidebarTopScrollBar.jScrollPane({
                        verticalDragMinHeight: 26,
                        verticalDragMaxHeight: 26
                    });
                    
                });


                UI.toggleSearchBox('show');

                e.preventDefault();
            });
        });
    }
};

/* ---------------------------------------------------------------------

	Custom Scroll Bar

	Call custom scroll bar

------------------------------------------------------------------------ */

customScroll = {
  init : function() {
    var libraryScrollBar = $(".top-level.active").siblings().children(".sub-section-wrap");
    var filterScrollBar = $(".filter-list-wrap");
    var sidebarTopScrollBar = $(".sidebar-top-content.alt");

    if (libraryScrollBar.is(":visible")) {
      libraryScrollBar.jScrollPane({
        verticalDragMinHeight: 26,
        verticalDragMaxHeight: 26
      });
    }

    if (filterScrollBar.is(":visible")) {
      filterScrollBar.jScrollPane({
        verticalDragMinHeight: 26,
        verticalDragMaxHeight: 26
      });
    }

    if (sidebarTopScrollBar.is(":visible")) {
      sidebarTopScrollBar.jScrollPane({
        verticalDragMinHeight: 26,
        verticalDragMaxHeight: 26
      });
    }
  }
};

/* ---------------------------------------------------------------------

        HTML5 Audio Player

        Setting up jQuery jPlayer

------------------------------------------------------------------------ */
  
  var Playlist = function(instance, playlist, options) {
      var self = this;

      this.instance = instance; // String: To associate specific HTML with this playlist
      this.playlist = playlist; // Array of Objects: The playlist
      this.options = options; 	// Object: The jPlayer constructor options for this playlist

      this.current = 0;

      this.cssId = {
        jPlayer: "jquery_jplayer_",
        interface: "jp_interface_",
        playlist: "jp_playlist_"
      };
      this.cssSelector = {};

      $.each(this.cssId, function(entity, id) {
        self.cssSelector[entity] = "#" + id + self.instance;
      });

      if(!this.options.cssSelectorAncestor) {
        this.options.cssSelectorAncestor = this.cssSelector.interface;
      }

      //console.log('yup');
      //var url;
	    //url = $.param.fragment();
	    //console.log('url is: ' + url);
      //console.log('url is: ' + url);
      //console.log(options);
      
      // 
      $(this.cssSelector.jPlayer).jPlayer(this.options);

      $(this.cssSelector.interface + " .jp-previous").click(function() {
        self.playlistPrev();
        $(this).blur();
        return false;
      });

      $(this.cssSelector.interface + " .jp-next").click(function() {
        self.playlistNext();
        $(this).blur();
        return false;
      });
  }; // Playlist
  
  // ADDING METHODS TO Playlist
  Playlist.prototype = {
    displayPlaylist: function() {
      var self = this;
      for (i=0; i < this.playlist.length; i++) {
        $(this.cssSelector.playlist + " ul").append(listItem);
        $(this.cssSelector.playlist + "_item_" + i).data("index", i).click(function() {
          var index = $(this).data("index");
          if(self.current !== index) {
            console.log('made it 1!');
        	self.playlistChange(index);
          } else {
        	console.log('made it 2!');
            $(self.cssSelector.jPlayer).jPlayer("play");
          }
          $(this).blur();
          return false;
        });
      }
    },
    playlistInit: function(autoplay) {
      if(autoplay) {
        this.playlistChange(this.current);
        console.log('setupSong autoplay');
      } else {
        this.playlistConfig(this.current);
        console.log('setupSong not autoplay');
      }
    },
    playlistConfig: function(index) {
      $(this.cssSelector.playlist + "_item_" + this.current).removeClass("jp-playlist-current").parent().removeClass("jp-playlist-current");
      $(this.cssSelector.playlist + "_item_" + index).addClass("jp-playlist-current").parent().addClass("jp-playlist-current");
      this.current = index;
      //alert('made it 3!');
      // THIS IS WHERE THE PLAYER AUTO PLAYS OUR SONGS
      //console.log(this.playlist);
      
      $(this.cssSelector.jPlayer).jPlayer("setMedia", this.playlist[this.current]);
      
      //$(this.cssSelector.jPlayer).jPlayer("stop");
    },
    playlistChange: function(index) {
      this.playlistConfig(index);
      var song = audioPlaylist.playlist[index];
      UI.setPlayerFields(audioPlaylist.playlist[index]);
      if (song.songNid > 0) {
        UI.currentSongId = song.songNid;
      }
      var offset = Number(song.startTime);
      $(this.cssSelector.jPlayer).jPlayer("play", (offset > 0 ? offset : 0) );
    },
    playlistNext: function() {
      var index = (this.current + 1 < this.playlist.length) ? this.current + 1 : 0;
      this.playlistChange(index);
    },
    playlistPrev: function() {
      var index = (this.current - 1 >= 0) ? this.current - 1 : this.playlist.length - 1;
      this.playlistChange(index);
    },
    playlistStop: function() {
      console.log("stop!");
      //var test 	= $('body').length;
      //var test1 = $(this.cssSelector.jPlayer).length;
      $(this.cssSelector.jPlayer).jPlayer("stop");
    },
    playlistPlay: function(){
    	console.log("play!");
    	$(this.cssSelector.jPlayer).jPlayer("play");
    }/*,
    setPlaceholderImage : function(src) {
        $('.player-thumbnail img').attr('src', src);
    }*/
  };
  

// DEV-NOTE : to replace array of songs, do:
// audioPlaylist.playlist = [song1, song2, ..] 
//, where songX is an object with name and mp3 eg songX = { name: 'bla', mp3: 'http://../' };
audioPlaylist = {}; //replace this with playlist when audioPlayer.init() is called

audioPlayer = {
    init: function() {

  //DEV NOTE: careful with object vars eg mp3/oga
  
  //cannot instantiate jplayer with an empty playlist
  //var songArray = [];
/*
  
  var songArray2 = [
    {
      name:"Hidden",
      mp3:"http://www.jplayer.org/audio/mp3/Miaow-02-Hidden.mp3",
      oga:"http://www.jplayer.org/audio/ogg/Miaow-02-Hidden.ogg"
    }
  ]; //*/
  
  var suppliedStr = "mp3";
  var solutionStr = "html";
  
  var userAgent = "" + navigator.userAgent.toLowerCase();
  if (userAgent.indexOf("mozilla") >= 0) {
      solutionStr = "html, flash";
  }
  
  //handle path
  var swfPathStr = baseUrl + "/sites/all/themes/musictheme/js";
  /*
  var swfPathStr = "assets/scripts";
  var location = "" + window.location;
  if (location.indexOf("_static") < 0) {
      location = "_static/assets/scripts";
      swfPathStr = "_static/assets/scripts";
  }//*/
  
  var configObj = {
    ready: function() {
      audioPlaylist.playlistInit(false); // Parameter is a boolean for autoplay.
    },
    ended: function() {
      audioPlaylist.playlistNext();
    },
    play: function() {
      $(this).jPlayer("pauseOthers");
    },
    swfPath: swfPathStr,
    supplied: suppliedStr,
    solution: solutionStr
  };
  
  //set up jPlayer
  var welcomeSong = {
      name: "Welcome to Music Dealers",
      mp3: baseUrl + "/sites/default/files/welcome.mp3",
      songNid : 0
  };
  var firstPlaylist = [welcomeSong];
  
  audioPlaylist = new Playlist("1", firstPlaylist, configObj);

  $(".jp-pause").hide();

  $(".jp-play-bar").draggable({
    axis: "x",
    containment: "parent"
  });

  var $volumeBar = $('.jp-volume-bar');
  var $volumeScrubber = $('.jp-volume-bar-value');
  var $volumeBarAndScrubber = $volumeBar.add($volumeScrubber);
  
  function startVolumeDrag(e) {
      $volumeBarAndScrubber.unbind('mousedown', startVolumeDrag);
      $volumeBarAndScrubber.bind('mousemove', volumeDrag);
      $('body').bind('mouseup', endVolumeDrag);
      volumeDrag(e);
  }
  
  function volumeDrag(e) {
      e.type = 'click';
      $volumeBar.trigger(e);
  }
  
  function endVolumeDrag(e) {
      $('body').unbind('mouseup', endVolumeDrag);
      $volumeBarAndScrubber.unbind('mousemove', volumeDrag);
      $volumeBarAndScrubber.bind('mousedown', startVolumeDrag);
  }
  
  $volumeBarAndScrubber.bind('mousedown', function(e) {
      e.preventDefault();
      startVolumeDrag(e);
  });

  $('#playlist-section, #custom-playlist-section, #results-section').delegate('.main-icon.play', 'click', function(e) {
	  /**
	   * CLICKING A SONG TITLE PLAY LINK
	   */
	  //$('#playlist-section tr td a').removeClass('active');
      //$('#filtered-results-section tr td a').removeClass('active');
	  $('.main-icon.play').removeClass('active'); // REMOVE BLUE PLAY BUTTONS
	  $(this).addClass('active'); // ADD BLUE PLAY BUTTONS
	  var index = $(e.target).data('listindex');
	  console.log(index);
      audioPlaylist.playlistChange(index);
      e.preventDefault();
  }).delegate('.song-title-cell span a','click',function(e){
	  /**
	   * CLICKING A SONG'S CIRCLE PLAY ICON BUTTON
	   */
	  $('.main-icon.play').removeClass('active'); // REMOVE BLUE PLAY BUTTONS
	  $(this).parent('span.main-cell-value').parent('td.song-title-cell').siblings('td.play-cell').children('.main-icon.play').addClass('active');
	  //.siblings('td.play-cell').children('.main-icon.play').addClass('active'); // ADD BLUE PLAY BUTTONS
	  var index = $(e.target).data('listindex');
	  console.log(index);
      audioPlaylist.playlistChange(index);
      e.preventDefault();
  });
  
  //can use click() since the content does not change
  $('#player-similar-songs').click(function(e) {
      
      UI.toggleSearchBox('show');
      searchUI.clearAll();
      
      var idx = audioPlaylist.current;
      if (idx < 0) {
          idx = 0;
      }
      var currentSong = audioPlaylist.playlist[idx];
      var currentSongId = currentSong.songNid;
      
      playlistItemApi.loadSimilarSongs(currentSongId);
      e.preventDefault();
  });
  
  /*
  
  if($.jPlayer.platform.tablet) {
    // Do something for all tablet devices
    if($.jPlayer.platform.ipad) {
       // Do something on ipad devices
    }
    if($.jPlayer.platform.android) {
       // Do something on android tablet devices
    }
}
  
  //*/

  }//end init
}; //*/

/* ---------------------------------------------------------------------

	jQuery Modals
	Calls for modal windows

------------------------------------------------------------------------ */

modalWindows = {
    init: function() {
      $('.modal-details-textarea').ata();
      
      /**
       * CLICKING SONG DETAIL FROM SEARH RESULTS
       * Ensure "detail" buttons work in playlistItem lists and search results
       */
      $(".main-icon.detail").live('click', function(e) {

        var self = $(this);

        UI.songDetailPlaylistIdx = self.parent().parent().data("index");
        UI.currentSongDetailNID = self.parent().parent().data("nid");
        UI.currentPlaylistItemDetailNID = self.parent().parent().data("playlistitem-nid");

        UI.currentAltSongDetailNID = 0; // reset the altsongid every time a detail is clicked -kjs
        
        var isSearch = 0;
        
        //search area or favorites playlist
        if (UI.activeContentSectionId == 'filter-results-section' || UI.currentPlaylistId == 0) {
          isSearch = 1;
        }
        
        if (UI.currentPlaylistItemDetailNID && UI.currentPlaylistItemDetailNID != 0) {
          songApi.getSongDetail(UI.currentSongDetailNID, UI.currentPlaylistItemDetailNID, isSearch);
        } else {
          songApi.getSongDetail(UI.currentSongDetailNID, 0, isSearch);
        }
        
         //e.preventDefault();  register hashchange
      });
    
      /**
       * CLICKING ADD ALT VERSION TO PLAYLIST
       * -kjs 02/28/2012
       */
      $('.alt-song-details-add').live('click',function(e){
    	  UI.currentAltSongDetailNID = $(this).attr('nid');
    	  e.preventDefault();
      });
  
      $(".modal-anchor").live('click',function(e){
        e.preventDefault();
        var self = $(this);
        var popHref = self.attr("href");
        var modalScrollBar = $(".modal-add-to-playlist-list-wrap");
        var modalScrollBarAlt = $(".modal-details-textarea-wrap");
        var modalScrollBarAlt2 = $(".modal-save-search-list-wrap");
  
        // FADE IN THE DESIRED MODAL
        $(popHref).fadeIn(function() {
        
          //fix bug with value going blank
          $('#share-playlist-email').attr("placeholder", "Enter Recipient Email");
          $("#share-modal .modal-form-textarea").val("Enter Personal Message");
          $("#share-modal .modal-form-textarea").addClass("placeholder-text"); 	
          
          //alert('here!');
          // FIX THE BUG CAUSED BY THE NEW PLAYLIST NAME FIELD
          // BEING PRE POPULATED BY CLEARING THE FIELD EVERY TIME
          // THIS MODAL LOADS... -kjs plan.io #172
          var newVal = '';
          $('#new-playlist-title').val(newVal);
          
        });
  
        $(".options-list-wrap").fadeOut();
        
        // SETUP SCROLL BARS??
        modalScrollBar.jScrollPane({
          verticalDragMinHeight: 26,
          verticalDragMaxHeight: 26
        });
      
        modalScrollBarAlt.jScrollPane({
          verticalDragMinHeight: 26,
          verticalDragMaxHeight: 26
        });
      
        modalScrollBarAlt2.jScrollPane({
          verticalDragMinHeight: 26,
          verticalDragMaxHeight: 26
        });
  
        /* //doing this below with delegate
        if (popHref == '#share-modal') {
            playlistUI.fillShareForm(self);
        } else //*/
  
        // IF THIS MODAL IS THE ADD TO PLAYLIST MODAl
        if (popHref == '#add-to-playlist-modal') {
          
          // ?? NOT SURE WHAT THIS IS DOING
          if (self.attr("id") == 'add-single-song') {
              UI.isSingleSong = true;
          } else {
              UI.isSingleSong = false;
          }
          
          // PREVENT MODAL FROM BEING INTERACTED WITH UNTIL getMyPlaylistsAdd() is successfull
          // - kjs plan.io #172
          // checkbox-custom alt checkall
          $('.checkbox-custom.alt.checkall').css('visibility','hidden');
          $('.modal-add-to-playlist-list-wrap').hide();
          $('.modal-add-to-playlist-loading').show();
          
          //$('#add-to-playlist-list').html('<Br><br><center>Loading Playlists...</center>');
          
          // UPDATE THE AVAILABLE PLAYLISTS IN THE MODAL SINCE A USER MAY HAVE ADDED A NEW PLAYLIST 
          playlistApi.getMyPlaylistsAdd();
            
        } else if (popHref == '#edit-song-detail-modal') {
          
          // Prepopulate the edit-song-details modal.
          //get playlist index from UI, was already set from details link
          playlistItemUI.addSongNotes();
          
        }
      });
  
      $(".modal-wrap").click(function(){
        $(this).fadeOut();
      });
  
      $(".modal-box").click(function(e){
        e.stopPropagation();
      });
  
      $(".modal-close").click(function(e){
        e.preventDefault();
        // Find the form(s) this event was triggered within
        var myForms = $(this).parents('form');
        // If we found any forms, reset them.
        myForms.each(function(i, elt) {
          elt.reset();
        });
        $(this).closest(".modal-wrap").fadeOut();
        $('.error').hide();
      });
  
      $(".modal-overlay").css("opacity", "0.8");
  
      $(".modal-edit-profile-add-contact").click(function (e) {
        e.preventDefault();
        var self = $(this);
        var newContact =  '<li id="profile-contact-fields" class="alt">' + 
                          ' <a href="#" class="modal-edit-profile-remove">Remove Contact</a>' + 
                          ' <input type="text" class="modal-form-element modal-form-input-text alt3" />' +
                          ' <span class="modal-form-element modal-form-label alt">Please Specify</span>' +
                          ' <input type="text" class="modal-form-element modal-form-input-text alt4" />' +
                          ' <a href="#" class="modal-edit-profile-ok">OK</a>' +
                          ' <div class="clear">' +
                          ' </div>' +
                          ' <ul class="modal-edit-profile-contact-list">' +
                          '   <li id="edit-profile-add-twitter">' +
                          '     <a href="#" class="modal-edit-profile-contact-list-link">Twitter</a>' +
                          '   </li>' +
                          '   <li id="edit-profile-add-linkedin">' +
                          '     <a href="#" class="modal-edit-profile-contact-list-link">LinkedIn</a>' +
                          '   </li>' +
                          '   <li id="edit-profile-add-facebook">' +
                          '     <a href="#" class="modal-edit-profile-contact-list-link">Facebook</a>' +
                          '   </li>' +
                          '   <li>' + 
                          '     <a href="#" class="modal-edit-profile-contact-list-link">Tumblr</a>' +
                          '   </li>' + 
                          ' </ul>' +
                          '</li>';
      						
        self.parents(".modal-form-list.alt").children(".alt").remove();
        self.parents(".modal-form-list.alt").append(newContact);
      
        $(".modal-form-element.modal-form-input-text.alt4").click(function() {
          var selfInput = $(this);
          var myPosition = selfInput.position();
        
          if(selfInput.siblings(".modal-edit-profile-contact-list").is(":visible")) {
            $(".modal-edit-profile-contact-list").hide();
          } else {
          	$(".modal-edit-profile-contact-list").hide();
          	selfInput.siblings(".modal-edit-profile-contact-list").show();
          	selfInput.siblings(".modal-edit-profile-contact-list").css("top", myPosition.top + selfInput.outerHeight());
          }
        });
      
        $(".modal-edit-profile-contact-list-link").click(function(e) {
          e.preventDefault();
          var selfLink = $(this);
          selfLink.parents(".modal-form-list.alt li").children(".modal-form-element.modal-form-input-text.alt4").attr("value", selfLink.text());
          selfLink.parents(".modal-edit-profile-contact-list").hide();
        });
        
        $(".modal-edit-profile-contact-list-link").mousedown(function(e) {
        	e.stopPropagation();
        });
        
        $(document).mousedown(function() {
          $(".modal-edit-profile-contact-list").hide();
        });
        
        $(".modal-edit-profile-remove").click(function(e) {
          e.preventDefault();
          $(this).parent().remove();		
        });
        
      });
    }
};

/* ---------------------------------------------------------------------

	jQuery Custom Toggles

	Controls custom "toggle" checkboxes

------------------------------------------------------------------------ */

customToggles = {
  init: function() {
    $(".modal-form-toggle").click(function(e) {
      var self = $(this);

      if(self.hasClass("toggled")) {
        self.removeClass("toggled");
        self.children().attr("checked", false);
      } else {
        self.addClass("toggled");
        self.children().attr("checked", true);
      }

      e.preventDefault();
    });
  }
};

/* ---------------------------------------------------------------------

	jQuery Slider/ Carousels

	Calls for Nivo Slider and jCarousel Lite

------------------------------------------------------------------------ */

featuredItems = {
    init: function() {
            
        $(".section-main-heading-link.featured-items").live('click', function(e){
            if (UI.isFeaturedLoaded) {
                UI.showContentSectionId('featured-section');
            } else {
                playlistItemApi.loadFeaturedItems();
            }
            e.preventDefault();
        });
        
    },
    
    init2 : function() {
        
        $("#featured-artist-slider").nivoSlider({
            effect:"sliceDown", // Specify sets like: 'fold,fade,sliceDown'
            slices:15, // For slice animations
            animSpeed:500, // Slide transition speed
            startSlide:0, // Set starting Slide (0 index)
            directionNav:true, // Next & Prev navigation
            directionNavHide:true, // Only show on hover
            controlNav:true, // 1,2,3... navigation
            pauseOnHover:true, // Stop animation while hovering
            prevText: "Previous", // Prev directionNav text
            nextText: "Next", // Next directionNav text
            manualAdvance:true, // Force manual transitions
            captionOpacity:1 // Universal caption opacity
        });

        $(".nivo-caption").hover(function(e) {
            e.stopPropagation();
        });

        $("#featured-artist-slider").hover(function(){
            $(".featured-artist-play-song").show();
            }, function(){
            $(".featured-artist-play-song").hide();
        });

        $("#featured-playlist-slider").nivoSlider({
            effect:"sliceDown", // Specify sets like: 'fold,fade,sliceDown'
            slices:15, // For slice animations
            animSpeed:500, // Slide transition speed
            startSlide:0, // Set starting Slide (0 index)
            directionNav:true, // Next & Prev navigation
            directionNavHide:true, // Only show on hover
            controlNav:true, // 1,2,3... navigation
            pauseOnHover:true, // Stop animation while hovering
            prevText: "Previous", // Prev directionNav text
            nextText: "Next", // Next directionNav text
            manualAdvance:true, // Force manual transitions
            captionOpacity:1 // Universal caption opacity
        });

        $("#featured-playlist-slider").hover(function(){
            $(".featured-playlist-open").show();
            }, function(){
            $(".featured-playlist-open").hide();
        });

        $(".top-songs-carousel").jCarouselLite({
            btnPrev: ".top-songs-left",
            btnNext: ".top-songs-right",
            circular: false
        });

        $(".top-songs-nav").click(function(e) {
            e.preventDefault();
        });
    
  }
};

/* ---------------------------------------------------------------------

 Header Login

 Page Transitions for Alternate Sidebar/ Custom Header Image
 

------------------------------------------------------------------------ */

headerLogin = {
    init: function() {
     
     
        $(".header-login .button-submit").click(function(e) {
            $(".header-login").hide();
            $(".sidebar").hide();
            $(".sidebar-top").hide();
            $("#filter-results-section").hide();
            $("#featured-section").hide();
            $("#song-details-section").hide();
            $("#playlist-section").hide();
            $(".header-user").fadeIn();
            $(".sidebar-alt").fadeIn();
            $(".custom-header-image-wrap").fadeIn();
            $("#custom-playlist-section").fadeIn();
            e.preventDefault();
        });
        
        //*/
    }
};

/* ---------------------------------------------------------------------

 Back Buttons

 Adds and Remove Back Buttons/ Controls Functions of Back Buttons
 
 Nov 11th: as far as I can tell, this code doesnt work.  I am adding an alert so I can see if this is ever called.
------------------------------------------------------------------------ */

backButtons = {
    init: function() {
        
        $(".section-main-heading-link.back-to-search").live('click', function(e) {
            alert('.section-main-heading-link.back-to-search was clicked, see steve about the this error message');
            UI.showFilterResultsContentId('filter-section');
            UI.showSidebarSearchId('filter-link-vocalinstr');
            e.preventDefault();
        });

        //$('#playlist-section').delegate('.back-to-results', 'click', function(e) {
            
        $('.back-to-results').live('click', function(e) {
            alert('.back-to-results was clicked, see steve about the this error message');
            UI.showFilterResultsContentId('results-section');
            e.preventDefault();
        });
        
        $(".back-to-playlist").live('click', function(e) {
            alert('.back-to-playlist was clicked, see steve about the this error message');
            e.preventDefault();
            
            if (UI.activeContentSectionId == 'custom-playlist-section') {
                UI.showContentSectionId('playlist-section');
                UI.toggleAltSidebar('');
            } else if (UI.prevContentSectionId == 'custom-playlist-section') {
                UI.showContentSectionId('custom-playlist-section');
                UI.toggleAltSidebar('show');
            } else if (UI.prevContentSectionId == 'filter-results-section') {
                UI.showFilterResultsContentId('results-section');
                UI.toggleAltSidebar('');
            } else {
                UI.showContentSectionId('playlist-section');
                UI.toggleAltSidebar('');
            }
            
            
        });
    
  }
};

/*----------------------------------------------------------------------------

Alternate Sidebar

----------------------------------------------------------------------------*/

altSidebar = {
    
    init : function() {
        
        // todo : init functionality related to alternate sidebar 
        
        
    }
    
}

/*----------------------------------------------------------------------------

 My Profile Modal

----------------------------------------------------------------------------*/
myProfile = {

    init: function() {
        myProfile.$form = $('#edit-profile-modal form');
        myProfile.errorTag = '<p class="modal-edit-profile-sub-text alt error" style="clear:both;"></p>';
        myProfile.bind();
        delete myProfile.init;
    },

    bind: function() {
        
        // todo: double-check this
        // need val() ?
        
        var fields = {
            'myname':      $('#edit-profile-myname'),
            'companyname': $('#edit-profile-companyname'),
            'position':    $('#edit-profile-position'),
            'website':     $('#edit-profile-website'),
            'phone':       $('#edit-profile-phone'),
            'name':        $('#edit-profile-name'),
            'mail':        $('#edit-profile-mail'),
            'pass':        $('#edit-profile-pass'),
            'pass2':       $('#edit-profile-pass2'),
            'chat':        $('#edit-profile-chat'),
            'twitter':     $('#edit-profile-twitter'),
            'linkedin':    $('#edit-profile-linkedin'),
            'facebook':    $('#edit-profile-facebook'),
            'tumblr':      $('#edit-profile-tumblr')
        };

        var defaults = {
            'myname':      'My Name',
            'companyname': 'Company Name',
            'position':    'Position',
            'website':     'Website',
            'phone':       'Phone Number',
            'chat':        'Chat'
        };

        function getDefault(id) {
            return '<span class="default inlineEdit-placeholder" data-default="true">' +  defaults[id] + '</span>';
        }
        
        // Modal Open Link
        function updateProfile() {
            profileApi.get({
                'data': {
                    'format': 'json'
                },
                'success': function(data) {
                    var profile = $.parseJSON(data);
                    
                    // Set profile input fields to contain correct text
                    fields['myname'].html(profile.profile_myname || getDefault('myname'));
                    fields['companyname'].html(profile.profile_companyname || getDefault('companyname'));
                    fields['position'].html(profile.profile_position || getDefault('position'));
                    fields['website'].html(profile.profile_website || getDefault('website'));
                    fields['phone'].html(profile.profile_phone || getDefault('phone'));
                    fields['chat'].html(profile.profile_chat || getDefault('chat'));
                    fields['name'].html(profile.name).val(profile.name);
                    fields['mail'].text(profile.mail).val(profile.mail);

                    // Show or hide contact fields depending on if they have values
                    if (profile.profile_twitter) {
                        fields['twitter'].text(profile.profile_twitter);
                        $('#edit-profile-twitter-wrap').show();
                    } else {
                        $('#edit-profile-twitter-wrap').hide();
                    }
                    if (profile.profile_linkedin) {
                        fields['linkedin'].text(profile.profile_linkedin);
                        $('#edit-profile-linkedin-wrap').show();
                    } else {
                        $('#edit-profile-linkedin-wrap').hide();
                    }
                    if (profile.profile_facebook) {
                        fields['facebook'].text(profile.profile_facebook);
                        $('#edit-profile-facebook-wrap').show();
                    } else {
                        $('#edit-profile-facebook-wrap').hide();
                    }
                    if (profile.profile_tumblr) {
                        fields['tumblr'].text(profile.profile_tumblr);
                        $('#edit-profile-tumblr-wrap').show();
                    } else {
                        $('#edit-profile-tumblr-wrap').hide();
                    }

                }
            });
            
        };
        
        updateProfile();

        // Add Contact
        myProfile.$form.delegate('#profile-contact-fields .modal-edit-profile-ok', 'click', function(e) {
            var $value = $('.modal-form-input-text.alt3');
            var value = $value.val();

            var $network = $('.modal-form-input-text.alt4');
            var network = $network.attr('value');

            switch (network) {
                case 'Twitter':
                    fields.twitter.text(value);
                    if (value == '') {
                       $('#edit-profile-twitter-wrap').hide();
                    } else {
                       $('#edit-profile-twitter-wrap').show();
                    }
                    break;
                case 'LinkedIn':
                    fields.linkedin.text(value);
                    if (value == '') {
                        $('#edit-profile-linkedin-wrap').hide();
                    } else {
                        $('#edit-profile-linkedin-wrap').show();
                    }
                    break;
                case 'Facebook':
                    fields.facebook.text(value);
                    if (value == '') {
                        $('#edit-profile-facebook-wrap').hide();
                    } else {
                        $('#edit-profile-facebook-wrap').show();
                    }
                    break;
                case 'Tumblr':
                    fields.tumblr.text(value);
                    if (value == '') {
                        $('#edit-profile-tumblr-wrap').hide();
                    } else {
                        $('#edit-profile-tumblr-wrap').show();
                    }
                    break;
                default:
                    break;
            }
            $('.modal-edit-profile-remove').trigger('click');

        });

        // Submit Form
        myProfile.$form.bind('submit', function(e) {
            $('.modal-edit-profile-sub-text.error').remove();
            var input = {};
            var val;
            $.each(fields, function(index, $element) {
                var nodeName = $element.get(0).nodeName.toLowerCase();

                // Get correct attribute based on <tag>
                if (nodeName == 'input') {
                    val = $element.val();
                } else {
                    val = $element.text();
                }

                // If value is default, submit empty string
                if (val == defaults[index]) {
                    input[index] = '';
                } else {
                    input[index] = val;
                }

            });
            
            profileApi.updateProfile(input, function(data) {
                var response = $.parseJSON(data);
                if (!response.success) {
                    $.each(fields, function(index, $element) {
                        if(response[index]) {
                            fields[index].parent().after($(myProfile.errorTag).html(response[index]));
                        }
                    });
                } else {
                    $('#edit-profile-modal').fadeOut();
                    $('.header-user-link.modal-anchor').text(fields['name'].val());
                }
            });
            e.preventDefault();
        });

        myProfile.$form.find('.cancel').bind('click', function() {
            updateProfile();
        });

    }

};

/*----------------------------------------------------------------------------

 Tooltips

----------------------------------------------------------------------------*/
tooltips = {
    init: function() {
        /*$('#filter-section li').tipsy({
            tip:'.tooltip-description',
            text:'.tooltip-description-inner',
            right: 30,
            middle: 10,
            textCallback: function($el) {
                return '<h3 class="tooltip-description-heading">' + $el.find('.filter-link').text() + '</h3>' +
                       '<div class="tooltip-description-body">' + $el.find('.filter-description').text() + '</div>';
            }
        }); */

        $('.player-button').tipsy({
            tip: '.tooltip-alt-wrap',
            text: '.tooltip-alt-content',
            top: 0,
            center: -5,
            textCallback: function($el) {
                return $el.text();
            }
        });
        
		
		$(".sort-link").tipsy({
			tip: '.tooltip-alt-wrap',
            text: '.tooltip-alt-content',
            top: 0,
            center: -6,
            textCallback: function($el) {
                return $el.text();
            }
		});
		
		$(".options-link").tipsy({
			tip: '.tooltip-alt-wrap',
            text: '.tooltip-alt-content',
            top: 0,
            center: -7,
            textCallback: function($el) {
                return "Playlist Options";
            }
		});
        
        $('.catalog-indicator').tipsy({
            tip: '.tooltip-alt-wrap',
            text: '.tooltip-alt-content',
            top: 0,
            center: -5,
            textCallback: function($el) {
                return $el.attr('tooltip');
            }
        });
    }
};


/*----------------------------------------------------------------------------

 Player Buttons

----------------------------------------------------------------------------*/
playerButtons = {
    init: function() {
        playerButtons.bind();
    },

    bind: function() {
        $('.player-button.add').click(function(e) {
            
        });
        
        $('.player-button.like').click(function(e) {
            var $el = $(e.target);
            
            var songObject = audioPlaylist.playlist[audioPlaylist.current];
            
            if (!songObject.songLiked) {
                likeApi.like(songObject.songNid, {
                    'success': function(data) {}
                    },
                    true
                );
                songObject.songLiked = 1;
                $el.addClass('active');
                $el.attr('title', 'You like this song.');
                
            } else {
                likeApi.like(songObject.songNid, {
                    'success': function(data) {}
                    },
                    false
                );
                songObject.songLiked = 0;
                $el.removeClass('active');
                $el.attr('title', 'Like this song.');
            }
            
            e.preventDefault();
        });
        
        //add button is with modal anchor stuff
    }
    
};

/*----------------------------------------------------------------------------

 Song Detail

----------------------------------------------------------------------------*/
songDetail = {
    init: function() {
        var self = this;
        songDetail.bind(self);
        delete self.init;
    },

    bind: function(self) {
        
        //handle listen button in song details
        $('#song-details-section').delegate('.song-details-listen', 'click', function(e) {
            
        	var nid = $(e.target).closest('.song-details-section-wrap').data('nid');
            var playlistIdx = $(e.target).closest('.song-details-section-wrap').data('index');
            var currentSong = audioPlaylist.playlist[audioPlaylist.current];
            
            //load song into player. grab song if we dont have it
            if (!currentSong || (currentSong && currentSong.songNid != nid)) {
                songApi.getSong(nid, {'format':'json'}, function(data) {
                	//alert('made it!');
                    var songArray = eval('(' + data  + ')');
                    playlistUI.setPlaylist(songArray);
                    audioPlaylist.playlistChange(0);
                });
            }
        });
    }
};

/*----------------------------------------------------------------------------

 Re-ordering Playlist Items

----------------------------------------------------------------------------*/
playlistItemReorder = {
  // Holds the row currently being dragged
  curDraggingRow : '',
  
  setup : function(ownerElt) {
    $(ownerElt).sortable({
      containment : 'parent',
      cursor : 'hand',
      delay : 500,
      distance : 15,
      handle : '.draggable-table-row-handle',

      items : 'tr',
      revert : true,
      zIndex : 100,

      create: function(event, ui) {
        // when UI component is initialized
      },
      start: function(event, ui) {
        // nothing special to do
      },
      stop: function(event, ui) {
        // nothing special to do
      },
      update: function(event, ui) {
        // here is where we report back the item weight in relation to its siblings;
        //console.log(event.target);
      }
    });
  }
};



/*----------------------------------------------------------------------------

Sounds Like
-kjs plan.io #25

----------------------------------------------------------------------------*/
/*
soundsLike = {
	init: function(){
		var self = this;
		self.bind();
		delete self.init;
	},
	bind: function(){
		$('.sounds-like-list-wrap input[type=text]').bind('keyup', function(e) {
			var $el = $(e.target);
			soundsLike.handle($el);
		});
		
		var soundsLikeCache = {}, soundsLikeLastXhr;
        $("#textbox-soundslike").autocomplete({
            minLength: 1,
            source: function( request, response ) {
                var term = request.term;
                if ( term in soundsLikeCache ) {
                    response( soundsLikeCache[ term ] );
                    return;
                }
                
                var url = baseUrl + "/music_song/autocomplete/"+ term +"/soundslike";
                
                soundsLikeLastXhr = $.getJSON( url, request, function( data, status, xhr ) {
                	soundsLikeCache[ term ] = data;
                    if ( xhr === soundsLikeLastXhr ) {
                        response( data );
                    }
                });
            }
        });
	},
	handle: function($el){
		var val = $el.val();
        var id = $el.attr('id');
        var name = id.substr(id.lastIndexOf('-') + 1);
        
        searchState.soundLikes = [];
        searchState.addSoundLikes(val); // add the current val
        
        $checkbox = $('#filter-soundslike');
        
        //console.log('CHECKBOX NAME: ' + name);
        //console.log('VAL: ' + val);
        //console.log('CHECKED: ' +  $checkbox.prop('checked'));
        //console.log('ID:' + id);
        if (val != '' && !$checkbox.prop('checked')) {
            $checkbox.trigger('click');
            //console.log('Trigger Checkbox 1');
        } else if (val == '' && $checkbox.prop('checked')) {
            $checkbox.trigger('click');
            //console.log('Trigger Checkbox 2');
        }
	}
}
*/

/*----------------------------------------------------------------------------

 Advanced Search
 
----------------------------------------------------------------------------*/
advancedSearch = {
    init: function() {
        var self = this;
        self.bind();
        delete self.init;
    },
    
    bind: function() {

        $('.adv-search-list-wrap input[type=text]').bind('keyup', function(e) {
            var $el = $(e.target);
            advancedSearch.handle($el);
        });
        
        $('#adv-search-language').bind('change', function(e) {
            var $el = $(e.target);
            advancedSearch.handle($el);
        });
        
        $('#adv-search-explicit').bind('change', function(e) {
            var $el = $(e.target);
            advancedSearch.handle($el);
        });
        
        $('#adv-search-secondgenre').bind('change', function(e) {
            var $el = $(e.target);
            advancedSearch.handle($el);
        });
        
        $('#adv-search-timesig').bind('change', function(e) {
            var $el = $(e.target);
            advancedSearch.handle($el);
        });
        
        $('#adv-search-rhythmicfeel').bind('change', function(e) {
            var $el = $(e.target);
            advancedSearch.handle($el);
        });
        
        $('#adv-search-catalog').bind('change', function(e) {
            var $el = $(e.target);
            advancedSearch.handle($el);
        });
        
        $('#adv-search-era').bind('change', function(e) {
            var $el = $(e.target);
            advancedSearch.handle($el);
        });
        
        $('#adv-search-key').bind('change', function(e) {
            var $el = $(e.target);
            advancedSearch.handle($el);
        });
        
        $('#adv-search-tempo').bind('change', function(e) {
            var $el = $(e.target);
            advancedSearch.handle($el);
        });
        
        // ARTIST ADVANCED AUTOCOMPLETE
        var artistCache = {}, artistLastXhr;
        $("#adv-search-artist").autocomplete({
            minLength: 2,
            source: function( request, response ) {
                var term = request.term;
                if ( term in artistCache ) {
                    response( artistCache[ term ] );
                    return;
                }
                
                var url = baseUrl + "/music_song/autocomplete/"+ term +"/artist";
                
                artistLastXhr = $.getJSON( url, request, function( data, status, xhr ) {
                    artistCache[ term ] = data;
                    if ( xhr === artistLastXhr ) {
                        response( data );
                    }
                });
            }
        });
        
        // SONG / NODE ID ADVANCED AUTOCOMPLETE
        // NO AUTOCOMPLETE
        /*
        var songIdCache = {}, songIdLastXhr;
        $("#adv-search-nodeid").autocomplete({
        	minLength: 2,
            source: function( request, response ) {
                var term = request.term;
                if ( term in songIdCache ) {
                    response( songIdCache[ term ] );
                    return;
                }
                
                // songid is important as type of search
                var url = baseUrl + "/music_song/autocomplete/"+ term +"/songid";
                
                songIdLastXhr = $.getJSON( url, request, function( data, status, xhr ) {
                    songIdCache[ term ] = data;
                    if ( xhr === songIdLastXhr ) {
                        response( data );
                    }
                });
            }
        });
        */
        
        // ALBUM ADVANCED AUTOCOMPLETE
        var albumCache = {}, albumLastXhr;
        $("#adv-search-album").autocomplete({
            minLength: 2,
            source: function( request, response ) {
                var term = request.term;
                if ( term in albumCache ) {
                    response( albumCache[ term ] );
                    return;
                }
                
                var url = baseUrl + "/music_song/autocomplete/"+ term +"/album";
                
                albumLastXhr = $.getJSON( url, request, function( data, status, xhr ) {
                    albumCache[ term ] = data;
                    if ( xhr === albumLastXhr ) {
                        response( data );
                    }
                });
            }
        });
        
        var songCache = {}, songLastXhr;
        $("#adv-search-songtitle").autocomplete({
            minLength: 2,
            source: function( request, response ) {
                var term = request.term;
                if ( term in songCache ) {
                    response( songCache[ term ] );
                    return;
                }
                
                var url = baseUrl + "/music_song/autocomplete/"+ term +"/song";
                
                songLastXhr = $.getJSON( url, request, function( data, status, xhr ) {
                    songCache[ term ] = data;
                    if ( xhr === songLastXhr ) {
                        response( data );
                    }
                });
            }
        });
        
    },
    
    handle : function($el) {
        
        var val = $el.val();
        var id = $el.attr('id');
        var name = id.substr(id.lastIndexOf('-') + 1);
        
        searchState.advFiltersObj[name] = val;
        
        $checkbox = $('#filter-' + name);
        
        //console.log('CHECKBOX NAME: ' + name);
        //console.log('VAL: ' + val);
        //console.log('CHECKED: ' +  $checkbox.prop('checked'));
        
        if (val != '' && !$checkbox.prop('checked')) {
        	//console.log('Trigger Checkbox 1');
            $checkbox.trigger('click');
        } else if (val == '' && $checkbox.prop('checked')) {
        	//console.log('Trigger Checkbox 2');
            $checkbox.trigger('click');
        }
        
    }
};




