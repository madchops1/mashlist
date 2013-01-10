playlistApi = {
  
  getMyPlaylistsAdd : function() {

    // ?excludeId=" + UI.currentPlaylistId
    $.ajax({
      url: baseUrl + "/music_playlist/my_add",
      cache: false,
      success: function(response){
        
        $('#add-to-playlist-list').html(response);
        
        // Ensure playlists in the "add-to-playlist" modal get custom checkbox handlers
        $("#add-to-playlist-modal .checkbox-custom").click(function(e) {
          e.preventDefault();
            var self = $(this);
            if (self.hasClass("checked")) {
                self.removeClass("checked");
                self.children().attr("checked", false);
            } else {
                self.addClass("checked");
                self.children().attr("checked", true);
            }
        });
        
        // ALLOW PLAYLIST MODAL TO BE INTERACTED WITH
        // -kjs plan.io #172
        $('.checkbox-custom.alt.checkall').css('visibility','visible');
        $('.modal-add-to-playlist-list-wrap').show();
        $('.modal-add-to-playlist-loading').hide();
        
      }
    });
  },

  loadPlaylistSidebar : function(id) {

    $.ajax({
        url: baseUrl + "/music_playlist/get/" + id + "?format=sidebar",
        success: function(response) {
            $("#alt-sidebar-container").html(response);
            
            $('#alt-sidebar-container .sidebar-alt-button').tipsy({
                tip: '.tooltip-alt-wrap',
                text: '.tooltip-alt-content',
                top: 0,
                center: -5,
                textCallback: function($el) {
                    return $el.text();
                }
            });
 
            UI.toggleAltSidebar('show');
        }
    });

  },

  loadSharePlaylistUrl : function(id) {
        $.ajax({
            url: baseUrl + "/music_playlist/get/"+ id,
            data: {},
            dataType: 'json',
            success: function(response) {
                $("#share-playlist-url").val(baseUrl + '/playlists/' + response.nid);
            }
        });
        
  },

  /**
   * Copies a playlist
   * @param int playlistId
   *  The ID of the playlist to be copied
   * @return bool
   */
  copyPlaylist : function(playlistId, options) {

    $.ajax({
        url: baseUrl + "/music_playlist/copy/" + playlistId,
        type: 'post',
        dataType: 'json',
        success: options.success,
        error: options.error
    });

  },

  /**
   * Creates a playlist
   * @param string name
   *  The name of the new playlist
   * @return bool
   */
  createPlaylist : function(name, add) {

    $.ajax({
        url: baseUrl + "/music_playlist/create",
        type: 'post',
        data: {
          'name': name
        },
        dataType: 'json',
        success: function(response) {
            playlistUI.postSaveNewMyPlaylist(response);
	    if (add) {
		playlistItemUI.submitAddSongsToPlaylists();
	    }
		
		$(".options-link").tipsy({
			tip: '.tooltip-alt-wrap',
            text: '.tooltip-alt-content',
            top: 0,
            center: -7,
            textCallback: function($el) {
                return "Playlist Options";
            }
		});
		
        }
    });

  },

  /**
   * Renames a playlist
   * @param string name
   *  The new name of the playlist
   * @param int playlistId
   *  The node NID of the playlist to be modified
   * @return bool
   */
  renamePlaylist : function(name, playlistId) {
    
      var formData = {
          'id': playlistId,
          'name': name
      };

    $.ajax({
        url: baseUrl + "/music_playlist/update/" + playlistId,
        type: 'post',
        data: formData,
        dataType: 'json',
        success: function(response) {
            // silence
        }
    });

  },

  /**
   * Deletes a playlist
   * @param int playlistId
   *  The ID associated with the playlist being modified.
   * @return bool
   */
  deletePlaylist : function(playlistId) {

    $.ajax({
        url: baseUrl + "/music_playlist/delete/" + playlistId,
        dataType: 'json',
        success: function(response) {
            $("#my-playlists-div .sub-section-wrap").jScrollPane({
                                verticalDragMinHeight: 26,
                                verticalDragMaxHeight: 26
                        });
        }
    });

  },

  /**
   * Adds any number of songs to any number of playlists.
   * @param int[] songs
   * @param int[] playlists
   * @return bool
   */
  addSongsToPlaylists : function(songs, playlists) {

    $.ajax({
      type: 'post',
      url : baseUrl + "/music_playlist/addsongs/toplaylists",
      data: {
        'songs' : songs,
        'playlists' : playlists
      },
      dataType: 'json',
      success: function(response){
        //silence
      }
    });

  },

  /**
   * Adds any number of songs to any number of playlists.
   * @param int[] songs
   * @param int[] playlists
   * @param string listname
   * @return bool
   */
  addSongsToNewPlaylist : function(songs, listname) {

    var formData = {
        songs : songs,
        newlist : listname
    };

    $.ajax({
        type: 'post',
        url : baseUrl + "/music_playlist/addsongs/toplaylists",
        data: formData,
        dataType: 'json',
        success: function(response){
            //silence
        }
    });


  },

  submitShareForm : function() {
      var formData = {
          url: $('#share-playlist-url').val(),
          details: $('#share-playlist-details').is(':checked'),
          download: $('#share-playlist-download').is(':checked')
      };

      var recipient = $('#share-playlist-email');
      var msg = $('#share-playlist-msg');

      // Because this field has some validation that changes the css, then when the modal is reopened
      // we readd the placeholder (see global.js).  I think that broke how the placeholder 
      // functionality works...however, the quickest fix is to just check for the placeholder text 
      // value.
      if (recipient.val() == 'Enter Recipient Email') {
          formData.recipient = '';
      }
      else {
          formData.recipient = recipient.val();
      }

      // validate email
      function isValidEmailAddress(emailAddress) {
          //var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
          //return emailAddress.match(re);
          var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
          return pattern.test(emailAddress);
      };

      // if( isValidEmailAddress( formData.recipient ) ) {
      
      if (msg.hasClass('placeholder-text')) {
          formData.msg = '';
      }
      else {
          formData.msg = msg.val();
      }

      $.ajax({
              type: "post",
              url: $("#share-playlist-form").attr('action'),
              data: formData,
	      dataType: 'json',
              success: function(response) { 
		  if (response.success == 1) {
                      // Ideally, this should be part of the UI object,
                      // but we don't want to process it before $.ajax finishes
                      var myForms = $("#share-playlist-form");
                      // If we found any forms, reset them.
                      myForms.each(function(i, elt) {
                              elt.reset();
                          });
                      //$('#share-playlist-form').hide();
                      //$('.modal-content').height('80');
                      //$('.modal-content').text('Message sent successfully!');
                      //$('#share-modal').delay('300').fadeOut();
                      $('.modal-form-list').append('<div id="modal-form-success">Your email was sent successfully!</div>');
                      $('#share-modal').delay('1200').fadeOut();
                  }
		  else {
		      switch (response.error) {
		      case 'recipient':
		        $('#share-playlist-email').css('color', 'red');
		        break;
		      }
		  }
              }
          });      

      // } else {
      //     $('#share-playlist-email').css('color', 'red');
      // }

    },

    getPlaylistAsync: function(playlistNid, options, onSuccess, onError) {
        $.ajax({
            url: baseUrl + "/music_playlist/get/" + playlistNid,
            data: options,
            success: onSuccess,
            error: onError
        });
    },
    
    loadTerms : function(nid) {
        
        var url = baseUrl + "/music_playlist/get_terms/" + nid;
        
        $.ajax({
            url : url,
            dataType : 'json',
            success : function(response) {
                searchState.setAllFilters(response);
                searchUI.load(searchState.filters);
            }            
        });
    },
    
    loadRecommendedPlaylists : function() {
        
        var termIds = searchState.filters.join(",");
        var url = baseUrl + "/music_playlist/recommended/" + termIds;
        
        $.ajax({
            url : url,
            success : function(response) {
                $("#my-library-recommended-playlists ul.sub-section").html(response);
            }            
        });
    },
    
    setSongOrder : function(songNids, playlistId) {
        
        //songNids is a comma-separated string
        
        var data = {
            songNids : songNids,
            playlistId : playlistId
        };
        
        var postUrl = baseUrl + "/music_playlist/order_songs";
        
        $.ajax({
            type: "post",
            url: postUrl,
            data: data,
            dataType: 'json',
            success: function(response) { 
                //silence
            }
        });
    }
};

// D-TOOL PLAYLIST SECTION
playlistUI = {
    currentElId : false,
    untitledCount : 0,

    /**
     * New 'My Playlist'
     *
     * Adds a playlist to the top of the list.
     * It first instantiates the top element, then creates new elements
     * and inserts before the top element.
     * It's using html,css specific to the inline editing.
     */
    newMyPlaylist : function () {
        //get parent element
        var ul = document.getElementById('my-playlist-ul');

        //top li element
        var topLi = ul.firstChild;

        //create new element
        var newLi = document.createElement('li');

        //prepare id for new elements
        this.untitledCount++;
        var newLiId = 'my-playlist-top-' + this.untitledCount;
        var newSpanId = 'my-playlist-tmp-span-' + this.untitledCount;

        //set element ids
        newLi.setAttribute('id', newLiId);

        newLi.innerHTML = '<a id="' + newSpanId + '" class="sub-section-title editable">Untitled Playlist</a';
        ul.insertBefore(newLi, topLi);

        $(newLi).children('a').trigger('dblclick');

        return $(newLi).children('a');

    },

    inlineSaveNewMyPlaylist : function(ev, el) {

        /* CUSTOM */
        var playlistName = el.value;
        if (playlistName == '') {
            playlistName = 'Untitled Playlist';
        }

        //check for 'tmp' in current playlist element (span id)
        if (this.currentElId.indexOf('tmp') >= 0) {
            //create()

            playlistApi.createPlaylist(playlistName);

        } else {
            //update()
            var lastDash = this.currentElId.lastIndexOf('-') + 1;
            var playlistId = this.currentElId.substr(lastDash);
            playlistApi.renamePlaylist(playlistName, playlistId);
        }
    },

    postSaveNewMyPlaylist : function(responseObj) {

        var newId = 0;

        //bail if there was an error
        if (responseObj.success != "1") {
            return false;
        }

        //node id
        newId = responseObj.nid;
	UI.newListId = newId;

        //note: no longer a span
        var newSpanId = 'my-playlist-' + newId;

        //get div container
        var container = document.getElementById('my-library-my-playlists');

        //get li container, as the parent of the current span
        var li = document.getElementById(this.currentElId).parentNode;

        var newDivId = 'options-list' + newId; //rel for new link also
        var newLinkId = 'gear-' + newId;
        var newLiId = 'my-playlist-' + newId;

        //create new div element for div container
        var newDiv = document.createElement('div');
        newDiv.setAttribute('id', newDivId);
        newDiv.innerHTML = this.getGearMenuHtml(newId);

        //create new link for li container
        var newLink = document.createElement('a');
        newLink.setAttribute('id', newLinkId);

        //add new elements to dom
        container.appendChild(newDiv);
        li.appendChild(newLink);

        //do jquery after it's in the dom
        $('#' + newDivId).addClass('options-list-wrap');
        $('#' + newLinkId).attr('rel', newDivId);
        $('#' + newLinkId).attr('href', '#');
        $('#' + newLinkId).addClass('options-link');

        //grab span by id and set new id
        $('#' + this.currentElId).attr('id', newSpanId);
        $('#' + newSpanId).addClass('a-playlist');
        
        var el = $('a#' + newLiId + ' .inlineEdit-placeholder');
        if (el.length == 1) {
            el.text('Untitled Playlist');
            el.removeClass('inlineEdit-placeholder');
            el.removeClass('default');
        }

        //grab li by id and set new id
        li.setAttribute('id', newLiId);

    },

    getGearMenuHtml : function(nid) {

        var html = '<a class="options-link-close" href="#">Options</a>'+
            '<ul class="options-list">'+
            '<li><a class="options-list-link option-open" href="#">Open Playlist</a></li>';
            
            if (UI.canAdminEdit) {
                html += '<li><a class="options-list-link option-admin" href="#" target="_blank">Admin Edit</a></li>';
            }
            
            html += '<li><a class="options-list-link option-share modal-anchor" href="#share-modal">Share Playlist</a></li>'+
            '<li><a class="options-list-link option-delete" href="#">Delete</a></li>'+
            '<li><a class="options-list-link option-duplicate" href="#">Duplicate</a></li>'+
            '</ul>';
        
            //'<li><a class="options-list-link option-add" href="#">Add to My Library</a></li>'+
        return html;
    },

    deletePlaylist : function(nid) {

        var gearElId = 'options-list' + nid;
        var gearEl = document.getElementById(gearElId);
        gearEl.parentNode.removeChild(gearEl);

        var elId = 'my-playlist-' + nid;
        var element = document.getElementById(elId);
        element.parentNode.removeChild(element);

        playlistApi.deletePlaylist(nid);

    },

    getPlaylistHtml : function(nid, name) {
        return '<li id="my-playlist-'+ nid +'">'+
        '<span id="'+nid+'" class="sub-section-title editable">'+ name +'</span>'+
        '<a class="options-link" href="#" rel="options-list'+ nid +'">Options</a></li>';

    },

    searchFilter : function(term) {
        var searchTerm = replaceSpaces(term.toLowerCase(), '');

        $('.my-playlist-li').each(function(idx) {
            var txt = replaceSpaces($(this).text(), '');
            txt = txt.toLowerCase();
            if (txt.indexOf(searchTerm) < 0) {
                $(this).css('display', 'none');
            }
        });
    },

    clearSearchFilter : function() {
        $('.my-playlist-li').css('display', '');
    },

    broadSearch : function() {
        var el = $('#txt-broad-search');
        var txt = el.val();

        if (txt == '' || txt == 'Start Typing...') {
            this.clearSearchFilter();
        } else {
            this.searchFilter(txt);
        }
        return false; //blocks form submission
    },

    sortByDate : function() {
        var filter = 'span.playlist-date';
        var selector = $('.top-level.active').parent();
        // the sorter looks for the parent container to sort DOM elements on.
        $('.sub-section li', selector).sortElements(function(a,b){
           return ($(filter, a).text() > $(filter, b).text()) ? -1 : 1;
        });
    },

    sortByTitle : function() {
        var filter = '.sub-section-title';
        var selector = $('.top-level.active').parent();
        // the sorter looks for the parent container to sort DOM elements on.
        $('.sub-section li', selector).sortElements(function(a,b){
           return ($(filter, a).text() > $(filter, b).text()) ? 1 : -1;
        });
    },

    addSongsToPlaylists : function() {
      var songs = $('#library-section .checkbox-cell :checked');
      var songsToAdd = [];
      
      if (UI.isSingleSong) {
          
        songsToAdd.push(UI.currentSongId);
        
      } else {
          
        $.each(songs, function(key, value) {
            songsToAdd.push($(value).val());
        });
        
      }
      var playlists = $('#playlist-add-modal input[type=checkbox]:checked').serializeArray();
      playlistApi.addSongsToPlaylists(songsToAdd, playlists);
      return false; // stop form processing
    },

    addSongsToNewPlaylist : function() {
      var songs = $('#library-section .checkbox-cell :checked');
      var songsToAdd = [];
      $.each(songs, function(key, value) {
        songsToAdd.push($(value).val());
      });

      var newlist = $('#playlist-add-modal input[name=newlist]').val();
      playlistApi.addSongsToNewPlaylist(songsToAdd, newlist);
      return false; // stop form processing
    },
    
    fillShareForm : function(link) {
    	$('#modal-form-success').hide();
        var nid 			= $(link).parent().parent().parent().attr("id").substr(12);
        //var playlistName 	= $(link).parent().parent().parent().attr("playlistname");
        //console.log('playlistName' + playlistName);
        //set form action
        $('#share-playlist-form').get(0).action = baseUrl + '/music_playlist/share/' + nid;
        //$('#share-playlist-url').val(playlistName);
        playlistApi.loadSharePlaylistUrl(nid); // NOT NEEDED BECAUSE OF THE LINE ABOVE
    },
    
    // THIS ISN'T DOING ANYTHING
    fillShareFormAlt : function(link) {
        var nid = $(link).closest('.sidebar-alt-playlist').data('nid');
        $('#share-playlist-form').get(0).action = baseUrl + '/music_playlist/share/' + nid;
        playlistApi.loadSharePlaylistUrl(nid);
    },
    
    submitShareForm : function() {
        playlistApi.submitShareForm();
        //$('#share-modal').fadeOut(); // moved up into submitShareForm function
        return false;
    },
    
    setPlaylist : function(nodes, append) {
        
        if (nodes.length == 0) {
            return false;
        }
        
        if (!UI.isPlayerButtons) {
            UI.togglePlayerButtons('show');
        }

        var playlist = [];
        
        for (var i = 0; i < nodes.length; i++) {
            var node = nodes[i];
            var song = {};
            song.name = node.songTitle;
            song.mp3 = baseUrl + '/' + node.songFilepath;
            song.songNid = node.songNid;
            song.songTitle = node.songTitle;
            song.artistNid = node.artistNid;
            song.artistTitle = node.artistTitle;
            song.artistFilepath = node.artistFilepath;
            song.albumNid = node.albumNid;
            song.albumTitle = node.albumTitle;
            song.songLiked = node.songLiked;
            song.startTime = node.startTime;
            song.duration = node.songDuration;
            playlist.push(song);
        }

        //FTW
        
        if (append == 1) {
        	
            audioPlaylist.playlist.push.apply(audioPlaylist.playlist, playlist);
            
        } else {
            audioPlaylist.playlist = playlist;
            if (audioPlaylist.playlist.length > 0) {
                UI.setPlayerFields(audioPlaylist.playlist[0]);
                audioPlaylist.current = 0;
                audioPlaylist.playlistChange(0);
                audioPlaylist.playlistStop();
            }
        }
        
    },
    
    removeFromPlaylist : function() {
        
        var currentSection = UI.activeContentSectionId;
        var songIds = [];
        var checkedSongs = [];
        
        if (currentSection == 'custom-playlist-section') {
            checkedSongs = $('#custom-playlist-section .checked');
        } else {
            checkedSongs = $('#playlist-section .checked');
        }
        
        if (checkedSongs.length > 0) {
            for (var i = 0; i < checkedSongs.length; i++) {
                var checkboxEl = checkedSongs[i];
                var checkbox = $(checkboxEl);
                var songId = checkbox.parent().parent().attr("data-nid");
                if (songId > 0) {
                    songIds.push(songId);
                    checkbox.parent().parent().remove();
                }
            }
            
            //remove from the other playlist listing
            if (currentSection == 'custom-playlist-section') {
                var checkedSongs2 = $('#playlist-section .checked');
                
            }
            
            playlistItemApi.removeSongs(songIds, UI.currentPlaylistId);
        }
    }
};
