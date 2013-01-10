UI = {
    activeContentSectionId : '',
    activeFilterResultsId : '',
    activeSidebarSectionId : '',
    activeSidebarLibraryId : '',
    activeSidebarSearchId : '',
    activeFilterSectionId : '',
    
    prevContentSectionId : '',
    prevFilterResultsId : '',
    prevFilterSectionId : '',
    
    currentPlaylistId : 0,
    currentPlaylistIdx : 0,
    currentPlaylistCount : 0,
    currentPlaylistPage : 0,
    currentPlaylistMode : '',
    currentSongId : 0, //use : audioPlaylist.playlist[audioPlaylist.current]

    currentPlaylistItemDetailNID : 0,
    currentSongDetailNID : 0,
    songDetailPlaylistIdx : 0,
    
    currentAltSongDetailNID : 0, // for saving alt versions in a playlist -kjs 02/28/2012
    
    isLoadingPlaylist : false,
    isSingleSong : false,
    
    currentSearchId : 0,
    currentSearchPage : 0,
    currentSearchTotal : 0,
    isSongPlaying : false,
    isFeaturedLoaded : false,
    //isPlaylistModalLoaded : false,
    
    isSearchBoxLoaded : false,
    isPlayerButtons : false,
    canAdminEdit : false,
    canViewArtists : false,
    
    /**
     * User term ids from songs in playlists.
     */
    userTermIds: [],

    /**
     * Store new playlist id to add songs to once created.  Should be reset to false after use.
     */
    newListId: false,
    
    /**
     * Main content container's
     */
    getContentSectionIds : function() {
        return ["landing-section","featured-section","filter-results-section","playlist-section","song-details-section","custom-playlist-section"];
    },
    
    /**
     * Two sidebar tabs
     */
    getSidebarSectionIds : function() {
        return ["sidebar-content-library","sidebar-content-search"];
    },
    
    /**
     * Five search category, filter tree containers, checkboxes
     */
    getFilterSectionIds : function() {
        return ["filter-set-vid-vocalinstr","filter-set-vid-3","filter-set-vid-1","filter-set-vid-5","filter-set-vid-14","filter-set-vid-vocaltype","filter-set-vid-soundslike", "filter-set-vid-advanced"];
    },
    
    /**
     * Sub-containers in 'filter-results-section' container
     */
    getFilterResultsIds : function() {
        return ["filter-default-section","filter-section","results-section"];
    },
    
    /**
     * Five search category links
     */
    getSidebarSearchIds : function() {
        return ["filter-link-vocalinstr","filter-link-3","filter-link-1","filter-link-5","filter-link-14","filter-link-vocaltype","filter-link-soundslike","filter-link-advanced"];
    },
    
    /**
     * Four collapsable library sections
     */
    getSidebarLibraryIds : function() {
        return ["library-section-my-playlists","library-section-featured-playlists","library-section-recommended-playlists","library-section-my-searches"];
    },
    
    /**
     * Find main content container that is currently being shown
     * Only used if 'activeContentSectionId' is empty
     */
    findActiveContentSectionId : function() {
        var divs = this.getContentSectionIds();
        var activeId = '';
        for (var i = 0; i < divs.length; i++) {
            var divId = divs[i];
            
            var el = document.getElementById(divId);
            if (el.style.display != 'none') {
                activeId = divs[i];
                break;
            }
        }
        return activeId;
    },
    
    
    
    
    
    /**
     * Set the main content container that is currently being shown
     * Does not 'show' content, call showContentSectionId() to show content
     */
    setActiveContentSectionId : function(sectionId) {
        var divs = this.getContentSectionIds();
        var idx = getIndexOf(sectionId, divs);
        if (idx < 0) {
            return false;
        }
        
        //store previous section
        this.prevContentSectionId = this.activeContentSectionId;
        
        this.activeContentSectionId = sectionId;
        return true;
    },
    
    /**
     * Get the main content container that is currently being shown
     * Find it if we have to
     */
    getActiveContentSectionId : function() {
        if (this.activeContentSectionId == '') {
            this.activeContentSectionId = this.findActiveContentSectionId();
        }
        return this.activeContentSectionId;
    },
    
    /**
     * Show a main content container, hide others first
     * Set current container id for content
     */
    showContentSectionId : function(sectionId) {
        var divs = this.getContentSectionIds();
        // setActiveContentSectionId handles storing the "last shown" section
        if (!this.setActiveContentSectionId(sectionId)) {
            return false;
        }
        for (var i = 0; i < divs.length; i++) {
            var div = divs[i];
            if (sectionId != div) {
                $('#' + div).hide();
            }
        }
        
        $('#' + sectionId).show();
        return true;
    },
    
    /**
     * Set active sub-container in 'filter-results-section' container
     * Does not 'show' content, call showFilterResultsContentId to show content
     */
    setActiveFilterResultsId : function(sectionId) {
        var divs = this.getFilterResultsIds();
        
        var idx = getIndexOf(sectionId, divs);
        if (idx < 0) {
            return false;
        }
        
        this.prevFilterResultsId = this.activeFilterResultsId;
        this.activeFilterResultsId = sectionId;
        return true;
    },
    
    /**
     * Set active search category link id eg 'Genre', 'Vocals', etc
     * Does not 'show' content, call showSidebarSearchId to show content
     */
    setActiveFilterSectionId : function(sectionId) {
        var divs = this.getFilterSectionIds();
        
        var idx = getIndexOf(sectionId, divs);
        if (idx < 0) {
            return false;
        }
        
        this.prevFilterSectionId = this.activeFilterSectionId;
        this.activeFilterSectionId = sectionId;
        return true;
    },
    
    /**
     * Get active sub-container in 'filter-results-section' container
     */
    getActiveFilterResultsId : function() {
        return this.activeFilterResultsId;
    },
    
    /**
     * Show sub-container in 'filter-results-section' container,
     * Show parent container first
     */
    showFilterResultsContentId : function(sectionId) {
        
        var divs = this.getFilterResultsIds();
        var preSectionId = this.activeFilterResultsId;
        
        if (!this.setActiveFilterResultsId(sectionId)) {
            return false;
        }
        
        //show parent container
        this.showContentSectionId('filter-results-section');
        
        for (var i = 0; i < divs.length; i++) {
            var div = divs[i];
            if (div != sectionId) {
                $('#' + div).hide();
            }
        }
        

        $('#' + sectionId).show();
        
        return true;
    },
    
    /**
     * Set current search link id
     */
    setActiveSidebarSearchId : function(elId) {
        var links = this.getSidebarSearchIds();
        
        var idx = getIndexOf(elId, links);
        if (idx < 0) {
            return false;
        }
        
        this.activeSidebarSearchId = elId;
        return true;
    },
    
    /**
     * Show search tab, de-activate library tab first
     * Activate search link, de-activate other search links first
     * Show filter checkboxes in main content, hide other main content first
     * Does not check checkboxes
     * Input can be a dom element or dom element id
     */
    showSidebarSearchId : function(el) {
        var links = this.getSidebarSearchIds();
        var elId = '';
        var relId = '';
        
        var elType = typeof el;
        
        //this method handles a dom element or a dom element id
        if (elType == 'string') {
            elId = el;
            el = $('#' + elId);
        } else {
            el = $(el);
        }
        
        elId = el.attr("id");
        relId = el.attr("rel");
        
        var elIdType = typeof elId;
        if (elIdType != "string") {
            return false;
        }
        
        // elId needs to be a string here
        if (!this.setActiveSidebarSearchId(elId)) {
            return false;
        }
        //safe to continue
        
        // set search link as active, remove active from others first
        $(".search-menu-link").removeClass("active");
        $(el).addClass("active");
        
        /*
        $(el).jScrollPane({
            verticalDragMinHeight: 26,
            verticalDragMaxHeight: 26
        }); //*/
        
        //show sidebar section
        this.showSidebarSection("search");
        
        //show content section
        this.showFilterResultsContentId('filter-section');
        
        //show filters section
        $("#filter-section .filter-inner-wrap").hide();
        this.showFilterSectionId(relId);
        
        return true;
    },
    
    /**
     * Toggle sidebar tab and content between library and search
     * Does not activate any sidebar content
     */
    showSidebarSection : function(section) {
        
        if (section != "library") {
            section = "search";
        }
        
        if (section == "search") {
            $("#sidebar-tab-library").removeClass("active");
            $("#sidebar-tab-search").addClass("active");
            $("#sidebar-content-library").hide();
            $("#sidebar-content-search").show();
        } else {
            $("#sidebar-tab-search").removeClass("active");
            $("#sidebar-tab-library").addClass("active");
            $("#sidebar-content-search").hide();
            $("#sidebar-content-library").show();
        }
        
        return true;
    },
    
    /**
     * Show filter tree in main content, hide other main content first
     */
    showFilterSectionId : function(sectionId) {
        
        var divs = this.getFilterSectionIds();
        var preSectionId = this.activeFilterSectionId;
        
        if (!this.setActiveFilterSectionId(sectionId)) {
            return false;
        }
        
        //show parent container
        this.showContentSectionId('filter-results-section');
        
        for (var i = 0; i < divs.length; i++) {
            var div = divs[i];
            if (div != sectionId) {
                $('#' + div).hide();
            }
        }
        
        //if (preSectionId != sectionId) {
            $('#' + sectionId).show();
        //}
        return true;
        
    },
    
    toggleSearchBox : function(action) {
        
        if (!this.isSearchBoxLoaded) {
        
            var sidebarTopScrollBar = $(".sidebar-top-content.alt");

            sidebarTopScrollBar.jScrollPane({
                verticalDragMinHeight: 26,
                verticalDragMaxHeight: 26
            });
            
            this.isSearchBoxLoaded = true;
        
        }
        
        if (action == "show") {
            //$("#sidebar-top-default").hide();
            $("#sidebar-top-filter").show();
        } else {
            $("#sidebar-top-filter").hide();
            //$("#sidebar-top-default").show();
        }
        
        return true;
    },
    
    toggleAltSidebar : function(action) {
        
        if (action == "show") {
            $(".sidebar").hide();
            $(".sidebar-top").hide();
            $(".sidebar-alt").show();
        } else {
            $(".sidebar-alt").hide();
            $(".sidebar-top").show();
            $(".sidebar").show();
        }
        
        return true;
    },

    setPlayerFields: function(playlistItem) {
    	
    	// TESTING
    	console.log('setting player fields');
    	//console.log(playlistItem);
    	
        $('.player-title').text(playlistItem.songTitle);
        
        var artistTitle = playlistItem.artistTitle;
        var artistLink = '<a href="#" class="player-sub-title-link artist" target="_blank">'+ artistTitle +'</a>';
        var artistAnchor = $('.player-sub-title-link.artist');
        var artistContainer = $('#player-artist-container');
        var albumContainer = $('#player-album-container');
        
        var albumAnchor = $('.player-sub-title-link.album');
        var albumTitle = playlistItem.albumTitle;
        if (albumTitle == null || albumTitle.length == 0) {
            albumTitle = '';
        }
        var albumLink = '<a href="#" class="player-sub-title-link album" target="_blank">'+ albumTitle +'</a>';
        
        if (UI.canViewArtists) {
            artistContainer.html(artistLink);
            artistAnchor = $('.player-sub-title-link.artist');
            if (playlistItem.artistNid) {
                artistAnchor.attr('href', baseUrl + '/node/' + playlistItem.artistNid);
            } else {
                artistAnchor.attr('href', 'javascript:;');
            }
        } else {
            artistContainer.html(artistTitle);
        }
        
        if (UI.canViewArtists && albumTitle.length > 0) {
            albumContainer.html(albumLink);
            albumAnchor = $('.player-sub-title-link.album');
            if (playlistItem.albumNid) {
                albumAnchor.attr('href', baseUrl + '/node/' + playlistItem.albumNid);
            } else {
                albumAnchor.attr('href', 'javascript:;');
            }
        } else {
            albumContainer.html(albumTitle);
        }

        var artistImage = $('.player-thumbnail img');
        var placeholderImageUrl = baseUrl + '/sites/all/themes/musictheme/images/placeholder-image.jpg';
        artistImage.src = placeholderImageUrl;
        
        //console.log(playlistItem);
        
        if (playlistItem.artistFilepath) {
            artistImage.attr('src', baseUrl + '/' + playlistItem.artistFilepath);
            
            // SET test.jpg for all artists profile pics in player
            // ON LOCAL, STAGING, DEV ONLY !!
            var currentUrl = window.location.href;
            var isLocal = currentUrl.indexOf('local');
            //console.log('isLocal: ' + isLocal);
            //console.log('URL: ' + window.location.href);
            //console.log('baseUrl' + baseUrl);
            if(isLocal > 0){
              //artistImage.attr('src', baseUrl + '/default/files/profile_pics/testprofile.jpg');
              artistImage.attr('src', placeholderImageUrl);
            }
            
        } else {
            artistImage.attr('src', placeholderImageUrl);
        }

        var $likeButton = $('.player-button.like');
        if (playlistItem.songLiked) {
            $likeButton.addClass('active');
            $likeButton.attr('title', 'You like this song.');
        } else {
            $likeButton.removeClass('active');
            $likeButton.attr('title', 'Like This Song!');
        }
    },
    
    opacityAll : function(action) {
        
        var sections = UI.getContentSectionIds();
        
        if (action == 'show') {
            
            for (var i = 0; i < sections.length; i++) {
                var section = sections[i];
                $('#' + section).css({opacity : 1.0});
                $("#loader-img-section").hide();
            }
            
        } else {
            
            for (var i = 0; i < sections.length; i++) {
                var section = sections[i];
                $('#' + section).css({opacity : 0.3});
                $("#loader-img-section").show();
            }
        }
        
        return true;

    },

    /**
     * Shows or hides the count ribbon and sets its value
     * @param Number number
     *   The value to show in the ribbon; if falsey, hides the ribbon 
     */
    setRibbonCount: function(number) {
        var $ribbonOuter = $('.ribbon-wrap');
        var $ribbonInner = $('.ribbon-value', $ribbonOuter); 
        if (number) {
          $ribbonInner.text(number); 
          $ribbonOuter.show();
        } else {
          $ribbonOuter.hide();
        }
    },
    
    togglePlayerButtons : function(action) {
        
        if (action == 'show') {
            $("#player-buttons").show();
            this.isPlayerButtons = true;
        } else {
            $("player-buttons").hide();
            this.isPlayerButtons = false;
        }
        
        return true;
    },
    
    offsetStrToInt : function(offset) {
        //get int value of both sides
        var offsetInt = 0;
        var offsetData = offset.split(':');
        var minutes = 0;
        var seconds = 0;
        
        if (offsetData.length == 2) {
            minutes = parseInt(offsetData[0]);
            seconds = parseInt(offsetData[1]);
        }
        
        offsetInt = (minutes * 60) + seconds;
        
        return offsetInt;
    },
    
    updateNoteCell : function(idx, note) {
        $('#custom-note-' + idx).text(note);
    },
    
    redirectToLogin : function() {
       window.location = baseUrl + "/user/login";   
    },
    
    checkSession : function() {
        
        $.ajax({
            url: baseUrl + "/music_song/is_logged_in",
            dataType: 'json',
            success: function(response) {
                if (response.logged_in != 1) {
                    UI.redirectToLogin();
                }
            }
        });
    },
    
    retrieveSongFromArray : function(songNid, songs) {
        var song = {};
        
        for (var i = 0; i < songs.length; i++) {
            song = songs[i];
            if (song.songNid == songNid) {
                break;
            }
        }
        
        return song;
    },
    
    getReorderedPlaylist : function(songNids, playlist) {
        
        var newPlaylist = [];
        
        for (var i = 0; i < songNids.length; i++) {
            var songNid = songNids[i];
            var song = UI.retrieveSongFromArray(songNid, playlist);
            if (song.songNid > 0) {
                newPlaylist.push(song);
            }
        }
        
        return newPlaylist;
    },
    
    getTableSongNids : function() {
        //get song nids from data elements in each table row
        var songNids = [];
        
        var rows = [];
        switch(UI.activeContentSectionId) {
            case 'playlist-section':
                rows = $('#playlist-tbody tr');
                break;
            case 'custom-playlist-section':
                rows = $('#playlist-custom-tbody tr');
                break;
            default:
                //silence
                break;
        }
        
        for (var i = 0; i < rows.length; i++) {
            var row = rows[i];
            var songNid = $(row).data('nid');
            if (songNid > 0) {
                songNids.push(songNid);
            }
        }
        
        return songNids;
    },
    
    reorderPlaylist : function() {    
        audioPlaylist.playlist = this.getReorderedPlaylist(this.getTableSongNids(), audioPlaylist.playlist);
    },
    
    initPlaylistDragDrop : function() {
        
        switch(UI.activeContentSectionId) {
            case 'playlist-section':
                $('#playlist-tbody').sortable({
                    stop : function(e, ui) {
                        UI.reorderPlaylist();
                        //send to server
                        var playlistId = UI.currentPlaylistId;
                        var songIds = UI.getTableSongNids().join(',');
                        playlistApi.setSongOrder(songIds, playlistId);
                    },
					
					containment: "parent"
                });
                break;
            case 'custom-playlist-section':
                $('#playlist-custom-tbody').sortable({
                    stop : function(e, ui) {
                        UI.reorderPlaylist();
                        //send to server
                        var playlistId = UI.currentPlaylistId;
                        var songIds = UI.getTableSongNids().join(',');
                        playlistApi.setSongOrder(songIds, playlistId);
                    },
					
					containment: "parent"
                });
                break;
            default:
                //silence
                break;
        }
		
		$(".ui-sortable").children().mousedown(function () {
			var self = $(this);
			self.closest(".page-wrap").css("position", "static");
			self.addClass("sortable-dragging");
		});
		
		$(".ui-sortable").children().mouseup(function () {
			var self = $(this);
			self.closest(".page-wrap").css("position", "relative");
			self.removeClass("sortable-dragging");
		});
		
		$(".ui-sortable").find("a").mousedown(function (e) {
			e.stopPropagation();
		});
    }
    
};
