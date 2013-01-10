/*
 * @file playList.js is essentially the api for the playlist.  no includes, it's all here with POSTS to drupal pages. 
 * 
 * This file is incredibly important.  It handles a ton of the HTTP POSTS for playlist related pages, which is essentially the entire app.
 * 
 * It provides the playlist api, which essentlially is http post to drupal pages, which return jsons
 */
playlistItemApi = {
    addSong : function(songId, playlistId) {
        
        var itemData = {
            song_id : songId,
            playlist_id : playlistId
        };

        $.ajax({
            url: baseUrl + "/music_playlistitem/save",
            type: 'post',
            data: itemData,
            dataType: 'json',
            success: function(response){
                //silence
            }
        });
        
    },
    
    addSongsToPlaylists : function(songIds, playlistIds) {
        
        var songs = songIds.join(',');
        var playlists = playlistIds.join(',');
        
        var formData = {
            songIds : songs,
            playlistIds : playlists
        };
        
        $.ajax({
            url: baseUrl + "/music_playlistitem/save_multiple",
            type: 'post',
            data: formData,
            dataType: 'json',
            success: function(response){
                //silence
            }
        });
        
    },
    
    loadSimilarSongs : function(songId) {
        
        searchState.clearFilters();
        
        $.ajax({
            url: baseUrl + "/music_song/terms/" + songId,
            dataType: 'json',
            success: function(response) {
                searchState.setAllFilters(response);
                searchUI.loadAdvFilters(searchState.advFiltersObj);
                searchUI.load(searchState.filters);
                //searchUI.loadSoundLikes(searchState.soundLikes);  //@todo: incorporate the bands for similar songs
                searchUI.loadVocalTypes(searchState.vocalTypes);
                searchUI.loadVocalInstr(searchState.vocalInstr);
                searchApi.loadSearchResults(1);
            }
        });
        
    },

    removeSong : function(songId, playlistId) {

        $.ajax({
            url: baseUrl + "/music_playlistitem/remove/" + songId + "/" + playlistId,
            dataType: 'json',
            success: function(response){
                //silence
            }
        });

    },
    
    removeSongs : function(songIds, playlistId) {
        
        var songIdsStr = '';
        if (songIds.length == 0) {
            return false;
        }
        songIdsStr = songIds.join(',');
        
        $.ajax({
            url: baseUrl + "/music_playlistitem/remove_multiple/" + songIdsStr + "/" + playlistId,
            dataType: 'json',
            success: function(response){
                //silence
            }
        });

    },

    loadPlaylistSongs : function(playlistId, page, mode) {
        
        UI.checkSession();
        
        //get html and set html to div
        if (mode != 'custom') {
            mode = 'playlist';
        }
        
        var timeout = setTimeout("UI.opacityAll('show')", 60 * 1000);
        
        UI.isLoadingPlaylist = true;
        
        UI.opacityAll('');
        
        $.ajax({
            url: baseUrl + "/music_song/playlist/" + playlistId + "/" + page + "?mode=" + mode,
            success: function(response) {
        	console.log('losing my mind');
                if (page == 1) {
                    if (mode == 'custom') {
                    	console.log('custom playlist is loaded');
                        $('#custom-playlist-section').html(response);
                        UI.showContentSectionId('custom-playlist-section');
                    } else {
                    	console.log('standard playlist is loaded');
                        $('#playlist-section').html(response);
                        console.log('and now what????')
                        UI.showContentSectionId('playlist-section');
                        console.log('section id has been shown')
                    }
                } else { 
                    if (mode == 'custom') {
                        $('#custom-playlist-tbody').append(response);
                    } else {
                        $('#playlist-tbody').append(response); 
                    }
                }
                
                UI.isLoadingPlaylist = false;
                UI.currentPlaylistId = playlistId;
                UI.currentPlaylistPage = page;
                UI.currentPlaylistMode = mode;
                UI.opacityAll('show');
                
                UI.initPlaylistDragDrop();
                
                clearTimeout(timeout);
                console.log('time cleared');
                $('.catalog-indicator').tipsy({
                    tip: '.tooltip-alt-wrap',
                    text: '.tooltip-alt-content',
                    top: 0,
                    center: -5,
                    textCallback: function($el) {
                        return $el.attr('tooltip');
                    }
                });
                
            },
            error : function() {
                UI.opacityAll('show');
                clearTimeout(timeout);
            }
        });

    },
    
    loadFavoriteSongs : function(page) {
        
        UI.checkSession();
        
        var timeout = setTimeout("UI.opacityAll('show')", 60 * 1000);
        
        UI.isLoadingPlaylist = true;
        
        UI.opacityAll('');
        
        
        $.ajax({
            url: baseUrl + "/music_song/favorites/" + page,
            success: function(response) {
                
                if (page == 1) {                    
                    $("#playlist-section").html(response);
                    UI.showContentSectionId('playlist-section');
                } else { 
                    $('#playlist-tbody').append(response);
                }
                
                UI.isLoadingPlaylist = false;
                UI.currentPlaylistId = 0;
                UI.currentPlaylistPage = page;
                UI.currentPlaylistMode = 'playlist';
                
                UI.opacityAll('show');                
                clearTimeout(timeout);
                
                $('.catalog-indicator').tipsy({
                    tip: '.tooltip-alt-wrap',
                    text: '.tooltip-alt-content',
                    top: 0,
                    center: -5,
                    textCallback: function($el) {
                        return $el.attr('tooltip');
                    }
                });
            },
            error : function() {
                UI.opacityAll('show');
                clearTimeout(timeout);
            }
        });

    },
    
    loadPlaylistSongsCustom : function(playlistId, page, format) {
        
        if (!format) {
          format = 'html';
        }

        $.ajax({
            url: baseUrl + "/music_playlistitem/getplaylist/" + playlistId,
            data: {'page': page, 'format': format},
            success: function(response) {
                
                if (page == 1) {
                    $('#custom-playlist-section').html(response);
                }
                
                UI.showContentSectionId('custom-playlist-section');
                UI.isLoadingPlaylist = false;
                UI.currentPlaylistId = playlistId;
                UI.currentPlaylistPage = page;
            }
        });

    },

    addSongNotes : function(playlistItemNid, notes, offsetStr) {

        var offsetInt = UI.offsetStrToInt(offsetStr);
        
        if (notes == $("#songdetails-modal-notes").attr("placeholder")) {
            notes = '';
        }
        
        var formData = {
            playlistItemNid : playlistItemNid,
            notes : notes,
            startTime : offsetStr
        };

        $.ajax({
            url : baseUrl + "/music_playlistitem/save_notes",
            type: 'post',
            data: formData,
            success : function(response) {
                
                var idx = UI.songDetailPlaylistIdx;
                audioPlaylist.playlist[idx].startTime = offsetInt;
                UI.updateNoteCell(idx, notes);
            }
        });

  },

  loadSongNotes : function(playlistItemSongId) {
    var songNotes = false;
    $.ajax({
      url : baseUrl + "/music_playlistitem/loadSongNotes/" + playlistItemSongId,
      dataType: 'json',
      success : function(songNotes) {

        var playHead = $('.jp-current-time').text();
        var modal = $('#edit-song-detail-modal');

        //$("[name=song-notes]", modal).val(songNotes.notes ? songNotes.notes : 'Enter some text...');
        $("#songdetails-modal-notes").val(songNotes.notes);
        
        $(".song-title", modal).html(songNotes.song.title);
        $(".artist-title", modal).html(songNotes.song.artist.title);
        // Adjust the playHead to either the value
        // in the current song notes, or if it is at 0, try to use the current player playHead.
        $("[name=play-head]", modal).val(songNotes.startTime.seconds > 0 ? songNotes.startTime.ready_str : playHead);
        $(".modal-details-total-time", modal).html( "/"+songNotes.song.duration.ready_str);
        
        //$("[name=edit-song-detail-playlistitem-nid]", modal).val(songNotes.playlistItemId > 0 ? songNotes.playlistItemId : playlistItemId);
        
        $("#songdetails-modal-notes").focus();
        
      }
    });
  },
  
  loadFeaturedItems : function() {
      
      UI.opacityAll('');
      
      $.ajax({
          url : baseUrl + "/music_playlistitem/featured",
          success : function(response) {
              $("#featured-section").html(response);
              UI.isFeaturedLoaded = true;
              UI.showContentSectionId('featured-section');
              featuredItems.init2();
              UI.opacityAll('show');
          }
      });
  }

};

playlistItemUI = {

  /**
   * Pre-populate "edit-song" modal data with information from the "song-detail" display
   */
  addSongNotes : function() {
    var playlistItemId = UI.currentPlaylistItemDetailNID;
    // this will take care of pre-population tasks
    playlistItemApi.loadSongNotes(playlistItemId);
  },

  submitSongNotes : function() {
    var modal = $('#edit-song-detail-modal');
    var notes = $('textarea[name=song-notes]', modal).val();
    var startTime = $('[name=play-head]', modal).val();
    // attempt to save playlistItem song notes
    var startTimeInt = UI.offsetStrToInt(startTime);
    
    if (notes == 'Enter some notes...') {
        notes = '';
    }
    
    //if offset is greater than song length
    
    var song = audioPlaylist.playlist[UI.songDetailPlaylistIdx];
    var duration = song.duration;
    var durationType = typeof duration;
    
    if (durationType == "string" && duration.indexOf(':') >= 0) {
        duration = UI.offsetStrToInt(duration);
    }
    
    if (startTimeInt >= duration) {
        $('#song-detail-offset-error').show();
    } else {
        playlistItemApi.addSongNotes(UI.currentPlaylistItemDetailNID, notes, startTime);
        $(modal).closest(".modal-wrap").fadeOut();
        $('.error').hide();
    }
    
    return false;
  },
  
  submitAddSongsToPlaylists : function() {
      
      //get form data
      
      var playlistIds = playlistItemUI.getSelectedPlaylistsIds();
      var songIds = playlistItemUI.getSelectedSongsIds();

      $('#add-to-playlist-modal').hide();
      //call api method
      playlistItemApi.addSongsToPlaylists(songIds, playlistIds);
      
      return false;
  },

  getSelectedPlaylistsIds: function() {
	var ids = [];

	//send playlistIds as comma-separated
	var playlists = $('#add-to-playlist-list .checked');
      
	for (var x = 0; x < playlists.length; x++) {
	    var playlistItem = playlists[x];
	    var playlist = $(playlistItem);
	    var checkbox = playlist.children(".checkbox");
	    var playlistId = checkbox.val();
	    ids.push(playlistId);
	}

	if (UI.newListId) {
	    ids.push(UI.newListId);
	    UI.newListId = false;
	}

	return ids;
    },

  getSelectedSongsIds: function() {
      var ids = [];
      var songs = [];
      
      
      
      if (UI.currentSongDetailNID != 0 && !UI.isSingleSong && UI.currentAltSongDetailNID == 0) {
    	  
    	  console.log('adding song detail');
    	  ids.push(UI.currentSongDetailNID);
      
      } else if(UI.currentAltSongDetailNID != 0 && !UI.isSingleSong){
    	
    	  console.log('adding alt versions song detail');
    	  ids.push(UI.currentAltSongDetailNID);
    	  
      }
      

      if (UI.isSingleSong) {

    	  console.log('adding single song');
          ids.push(UI.currentSongId);

      } else {

        switch (UI.activeContentSectionId) {
        	case 'filter-results-section':
        		console.log('adding from filter results section');
        		songs = $('#results-tbody .checked');
        		break;
        	case 'custom-playlist-section':
        		console.log('adding from custom playlist section');
        		songs = $('#custom-playlist-section .checked');
        		break;
        	default:
        		//playlist-section
        		console.log('adding from playlist section');
          		songs = $('#playlist-tbody .checked');
          		break;
          
        }
	    
	    for (var x = 0; x < songs.length; x++) {
	        var songItem = songs[x];
	        var song = $(songItem);
	        var checkbox = song.children(".checkbox");
	        var songId = checkbox.val();
	        ids.push(songId);
	    }
      
	}

	return ids;
    },

  handleAddSongsModal : function() {
	  
	  var newPlaylistName = $('#new-playlist-title').val();
	  
	  //if txt input is not empty
	  if (newPlaylistName != '') {

		  var newEl = playlistUI.newMyPlaylist();
		  newEl.text(newPlaylistName); 
		  playlistApi.createPlaylist(newPlaylistName, true);
          $('.playlist-placeholder').hide();
          
	  } else {
	      playlistItemUI.submitAddSongsToPlaylists();
	  }
	  
	  $("#add-to-playlist-modal").hide();
	  
	 return false; 
  }
};
