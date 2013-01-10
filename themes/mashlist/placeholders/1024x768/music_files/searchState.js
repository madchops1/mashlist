/* 
 * So, you see all the add/remove functions below? 
 * Do you suppose we could just make those one function and not 12??? 
 * The function could take two additional params, $op & $type 
 * 
 */
searchState = {
    filters : [],
    vocalTypes : [],    
    soundLikes : [],
    vocalInstr : [],
    
    // mutate this object directly <- meaning what?!??!!??!  -stk
    // nice comment, mutate, that makes sense -kjs
    // THE 
    advFiltersObj : {},
    
    isEmptyStr : function(str) {  //right, jquery doesn't provide this -stk
        if (str == '' || str == 'none') {
            return true;
        }
        return false;
    },
    addFilter : function(filter) {
        if (this.isEmptyStr(filter)) {
            return this.filters;
        } else {
            this.filters.push(filter);
        }
        return this.filters;
    },
    removeFilter : function(filter) {
        var idx = getIndexOf(filter, this.filters);
        if (idx >= 0) {
            this.filters.splice(idx, 1);
        }
        return this.filters;
    },
    
    addVocalType : function(filter) {
        if (this.isEmptyStr(filter)) {
            return this.vocalTypes;
        } else {
            this.vocalTypes.push(filter);
        }
        return this.vocalTypes;
    },
    removeVocalType : function(filter) {
    	console.log('removed vocal type');
        var idx = getIndexOf(filter, this.vocalTypes);
        if (idx >= 0) {
            this.vocalTypes.splice(idx, 1);
        }
        return this.vocalTypes;
    },
    
    addSoundLikes : function(filter) {
    	console.log('added sounds like' + filter + '');
        if (this.isEmptyStr(filter)) {
            return this.soundLikes;
        } else {
            this.soundLikes.push(filter);
        }
        console.log(filter);
        return this.vocalTypes;
    },
    removeSoundLikes : function(filter) {
    	console.log('removed sounds like ' + filter + '');
    	
    	// REMOVED THIS CUZ IN THE NEW SOUNDSLIKE SEARCH THERE CAN ONLY BE ONE SOUNDSLIKE AT A TIME
    	// REVERTED TO THIS ON 12/20/2012 -kjs
    	var idx = getIndexOf(filter, this.soundLikes);
        if (idx >= 0) {
            this.soundLikes.splice(idx, 1);
        }
        
    	// SO WE CAN SIMPLY CLEAR THE SOUNDS LIKE ARRAY AS IS DONE BELOW -kjs plan.io #239
    	this.soundLikes = [];
        
    	return this.soundLikes;
    },
    
    addVocalInstr : function(filter) {
        if (this.isEmptyStr(filter)) {
            return this.vocalInstr;
        } else {
            this.vocalInstr.push(filter);
        }
        return this.vocalInstr;
    },
    removeVocalInstr : function(filter) {
        var idx = getIndexOf(filter, this.vocalInstr);
        if (idx >= 0) {
            this.vocalInstr.splice(idx, 1);
        }
        return this.vocalTypes;
    },
    
    getFilters : function() {
        return this.filters;
    },
    clearFilters : function() {
    	console.log('clearing all filters');
    	//alert('clearing!');
        this.filters = [];
        this.vocalTypes = [];
        this.vocalInstr = [];
        this.soundLikes = [];
        this.advFiltersObj = {};
        return this.filters;
    },
    getFiltersObj : function() {
        search = {
            "filters" : this.filters
        };
        return search;
    },
    getFiltersStr : function() {
        return JSON.stringify(this.getFiltersObj());
    },
    getAdvFiltersStr : function() {
        return JSON.stringify(this.advFiltersObj);
    },
    getAllFiltersObj : function() {
        search = {
            "filters" : this.filters,
            "vocalTypes" : this.vocalTypes,
            "soundLikes" : this.soundLikes,
            "vocalInstr" : this.vocalInstr,
            "advfilters" : this.advFiltersObj
        };
        return search;
    },
    getAllFiltersStr : function() {
        return JSON.stringify(this.getAllFiltersObj());
    },
    setAllFilters : function(obj) {
        this.filters = obj.filters;
        this.vocalTypes = obj.vocalTypes;
        this.vocalInstr = obj.vocalInstr;
        this.advFiltersObj = obj.advfilters;
    }
};

/**
 * Constructor for advFilterObj
 * 
 * searchState.advFilterObj = new advFilterObj(options);
 * advFilterObj = new advFilterObj({})
 */
function advFilterObj(options) {
    this.artist = options.artist;
    this.songtitle = options.songtitle;
    this.album = options.album;
    this.keyword = options.keyword;
    this.explicit = options.explicit;
    this.secondarygenre = options.secondarygenre;
    this.instrument = options.instrument;
    this.tempo = options.tempo;
    this.bpm = options.bpm;
    this.language = options.language;
    this.artistcity = options.artistcity;
    this.artiststate = options.artiststate;
    this.artistcountry = options.artistcountry;
}

searchApi = {
    loadSearch : function(id) { // todo rb
        
        searchUI.toggleSearchBoxSpinner('show');
        
        $.ajax({
            url: baseUrl + "/music_search/get/" + id,
            dataType: 'json',
            success: function(response) {
                searchState.filters = response.filters;
                searchUI.load(searchState.getFilters());
                
                //load advanced filters
                
                searchState.advFiltersObj = {};
                var advType = typeof response.advfilters;
                if (advType != 'undefined') {
                    searchState.advFiltersObj = response.advfilters; 
                    searchUI.loadAdvFilters(searchState.advFiltersObj);
                }
                
                searchState.vocalInstr = [];
                var viType = typeof response.vocalInstr;
                if (viType != 'undefined') {
                    searchState.vocalInstr = response.vocalInstr; 
                    searchUI.loadVocalInstr(searchState.vocalInstr);
                }
                
                searchState.vocalTypes = [];
                var vtType = typeof response.vocalTypes;
                if (vtType != 'undefined') {
                    searchState.vocalTypes = response.vocalTypes; 
                    searchUI.loadVocalTypes(searchState.vocalTypes);
                }
                
                searchState.soundLikes = [];
                var slType = typeof response.soundLikes;
                if (slType != 'undefined') {
                    searchState.soundLikes = response.soundLikes; 
                    searchUI.loadSoundLikes(searchState.soundLikes);
                }
                
                searchUI.toggleSearchBoxSpinner('hide');
            }
        });
        
    },
    
    loadSearchResults : function(page) {
        
        UI.checkSession();
        
        var timeout = setTimeout("UI.opacityAll('show')", 60 * 1000);
        UI.currentSearchPage = page;
        
        UI.opacityAll('');
        var url = baseUrl + "/music_song/search/" + page + "?filters=" + searchState.getAllFiltersStr();
        
        $.ajax({
            url: url,
            success: function(response) {
                
                if (page == 1) {
                    $("#results-section").html(response);
                } else {
                    $("#results-tbody").append(response);
                }
                
                UI.currentSearchPage = page;                
                UI.showFilterResultsContentId('results-section');
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
    
    loadSearchAndResults : function(id, page) { // todo rb
        
        $.ajax({
            url: baseUrl + "/music_search/get/" + id,
            dataType: 'json',
            success: function(response) {
                
                //do same as loadSearch
                searchState.filters = response.filters;
                searchUI.load(searchState.filters);
                
                searchState.advFiltersObj = {};
                var advType = typeof response.advfilters;
                if (response.hasOwnProperty('advfilters')) {
                    searchState.advFiltersObj = response.advfilters;
                    searchUI.loadAdvFilters(searchState.advFiltersObj);
                }
                
                searchState.vocalInstr = [];
                
                var viType = typeof response.vocalInstr;
                
                if (response.hasOwnProperty('vocalInstr')) {
                    searchState.vocalInstr = response.vocalInstr; 
                    searchUI.loadVocalInstr(searchState.vocalInstr);
                }
                
                searchState.vocalTypes = [];
                
                var vtType = typeof response.vocalTypes; 
                
                if (response.hasOwnProperty('vocalTypes')) {
                    searchState.vocalTypes = response.vocalTypes; 
                    searchUI.loadVocalTypes(searchState.vocalTypes);
                }
                
                
                searchState.soundLikes = []; 
                
                var slType = typeof response.soundLikes;
                
                if (response.hasOwnProperty('soundLikes')) {
                    searchState.soundLikes = response.vocalTypes; 
                    searchUI.loadSoundLikes(searchState.soundLikes);
                }
                
                //load results
                searchApi.loadSearchResults(page);
            }
        });
        
    },
    
    saveSearch : function(searchName) {
        
        var filters = searchState.getAllFiltersStr();
        
        //DEV NOTE : dont need to handle other filters, its all one json string
        
        var searchData = {
            name : searchName,
            data : filters
        };
        
        $.ajax({
            url: baseUrl + "/music_search/save",
            type: 'post',
            data: searchData,
            dataType: 'json',
            success: function(response){
                searchUI.newMySearch(searchName, response.id);
				
				if($("#saved-searches-div .sub-section-wrap").is(":visible")) {
					$("#saved-searches-div .sub-section-wrap").jScrollPane({
						verticalDragMinHeight: 26,
						verticalDragMaxHeight: 26
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
				}
            }
        });
        
    },
    
    updateSearch : function(id) {
        
        var filters = searchState.getAllFiltersStr();
        
        //DEV NOTE : dont need to handle other filters, its all one json string
        
        var searchData = {
            data : filters
        };
        
        $.ajax({
            url: baseUrl + "/music_search/update/" + id,
            type: 'post',
            data: searchData,
            dataType: 'json',
            success: function(response){
                //nothing for now
            }
        });
        
    },
    removeSearch : function(id) {
        
        $.ajax({
            url: baseUrl + "/music_search/remove/" + id,
            dataType: 'json',
            success: function(response){
                $("#saved-searches-div .sub-section-wrap").jScrollPane({
					verticalDragMinHeight: 26,
					verticalDragMaxHeight: 26
				});
            }
        });
        
    },
    getSavedSearches : function(options, callback) {
        $.ajax({
            url: baseUrl + "/music_search/my",
            data: options,
            success: function(response) {
                if($.isFunction(callback)) {
                    callback(response);
                }
            }
        });
    },
    loadModalSearches : function() {
        
        $.ajax({
            url: baseUrl + "/music_search/my",
            data: {},
            success: function(response) {
                $('#modal-save-search-ul').html(response);
                customCheckbox.init();
				$(".modal-save-search-list-wrap").jScrollPane({
					verticalDragMinHeight: 26,
					verticalDragMaxHeight: 26
				});
            }
        });
    }
};

// D-TOOL SEARCH SECTION
searchUI = {
    untitledCount : 0,
    
    /**
     * Checks checkboxes for filters
     */
    load : function(filterIds) {
        
        var type = typeof filterIds;
        if (type != "object") {
            return false;
        }
        
        UI.toggleSearchBox('show');
        
        searchState.filters = [];
        for (var i = 0; i < filterIds.length; i++) {
            var filterId = filterIds[i];            
            searchUI.checkFilter($('#link-filter-' + filterId));
        }
        return true;
    },
    
    /**
     * Checks checkboxes for filters
     */
    loadVocalInstr : function(filterIds) {
        
        var type = typeof filterIds;
        if (type != "object") {
            return false;
        }
        
        UI.toggleSearchBox('show');
        
        searchState.vocalInstr = [];
        for (var i = 0; i < filterIds.length; i++) {
            var filterId = filterIds[i];            
            searchUI.checkFilter($('#link-filter-vocalinstr-' + filterId));
        }
        return true;
    },
    
    /**
     * Checks checkboxes for filters
     * wtf is this specific to vocal types?  why can't this just be a gerneric function?  it returns a damn boolean! -stk
     * furthermore, why even declare searchState.vocalTypes = []  it does nothing. -stk
     */
    loadVocalTypes : function(filterIds) {
        
        var type = typeof filterIds;
        if (type != "object") {
            return false;
        }
        
        UI.toggleSearchBox('show');
        
        searchState.vocalTypes = [];
        for (var i = 0; i < filterIds.length; i++) {
            var filterId = filterIds[i];            
            searchUI.checkFilter($('#link-filter-vocaltype-' + filterId));
        }
        return true;
    },
    /**
     * Checks checkboxes for filters
     * wtf is this specific to vocal types?  why can't this just be a gerneric function?  it returns a damn boolean!-stk
     * furthermore, why even declare searchState.soundLikes = []  it does nothing.  -stk
     */
    loadSoundLikes : function(filterIds) {
        
        var type = typeof filterIds;
        if (type != "object") {
            return false;
        }
        
        UI.toggleSearchBox('show');
        
        searchState.soundLikes = [];
        for (var i = 0; i < filterIds.length; i++) {
            var filterId = filterIds[i];            
            searchUI.checkFilter($('#link-filter-' + filterId));
        }
        return true;
    },

    
    /**
     * this method should only check the advanced checkboxes
     */
    loadAdvFilters : function(advFiltersObj) {
        
        var type = typeof advFiltersObj;
        if (type != "object") {
            return false;
        }
        
        UI.toggleSearchBox('show');
        
        var filters = {
            'artist': '',
            'songtitle': '',
            'album': '',
            'keyword': '',
            'explicitlyrics': '',
            'secondarygenre': '',
            'instrument': '',
            'tempo': '',
            'bpm': '',
            'language': ''
        };
        
        var filters = new advFilterObj({});

        var types = {};
        var values = {};
        
        for (var filter in filters) {
            types[filter] = typeof advFiltersObj[filter];
            values[filter] = '';
            $('#adv-search-' + filter).val('');
            
            if (types[filter] != 'undefined') {
                values[filter] = advFiltersObj[filter];
            }
            
            if (values[filter] != '') {
                $('#adv-search-' + filter).val(values[filter]);
                searchUI.checkFilter($('#link-filter-' + filter));
            }
            
        }
        
    },
    
    /**
     * CLEAR ALL FILTERS
     */
    clearAll : function() {
        searchState.clearFilters();
        $(".filter-added").remove();
        $(".filter-list .checkbox-custom").removeClass("checked");
        $(".filter-list li").removeClass("checked-filter selected-filter");
        $(".filter-list .checkbox").attr("checked", false);
        $(".filter-link").removeClass("checked-filter-link selected-filter-link");
        UI.setRibbonCount(false);
    },
    /**
     * Save or update a search
     */
    saveSearch : function(formObj) {
        
        if ($('#searchName').val() != '') {
            var searchName = $('#searchName').val();
            searchApi.saveSearch(searchName);
            $('#save-search-modal').fadeOut();
        } else {
            //iterate through elements
            var searchId = 0;
            var elements = formObj.elements;
            for (var i = 0; i < elements.length; i++) {
                //check type == checkbox
                var type = elements[i].type;
                var name = elements[i].name;
                //see if it's checked
                if (type == 'checkbox' && elements[i].checked) {
                    var elId = elements[i].getAttribute('id');
                    var lastDash = elId.lastIndexOf('-') + 1;
                    searchId = elId.substr(lastDash);
                    break;
                }
            }
            searchApi.updateSearch(searchId);
            $('#save-search-modal').fadeOut();
        }
        
        return false;
    },
    
    /**
     * New 'My Search'
     *
     * Adds a search to the top of the list.
     * It first instantiates the top element, then creates new elements
     * and inserts before the top element.
     */
    newMySearch : function (searchName, searchId) {

        //get parent element
        var ul = document.getElementById('my-searches-ul');
        var liContainer = document.getElementById('my-library-saved-searches');
        
        //top li element
        var topLi = ul.firstChild;

        //create new element
        var newLi = document.createElement('li');

        //prepare id for new elements
        var newLiId = 'my-saved-searches-' + searchId;

        //set element ids
        newLi.setAttribute('id', newLiId);
        newLi.innerHTML = '<a class="sub-section-title" href="#">'+searchName+'</a>'+
            '<a class="options-link" href="#" rel="options-list'+searchId+'">_</a>';
            
        var gearDiv = document.createElement('div');
        var gearDivId = 'options-list' + searchId;
        gearDiv.setAttribute('id', gearDivId);
        gearDiv.setAttribute('class', 'options-list-wrap');
        gearDiv.style.display = 'none';
        
        var gearMenuInner = '<a class="options-link-close" href="#">Options</a>'+
                        '<ul class="options-list">'+
                            '<li><a class="options-list-link option-open" href="#">Open Search</a></li>'+
                            '<li><a class="options-list-link option-delete" href="#">Delete</a></li>'+
                        '</ul>';
        
        gearDiv.innerHTML = gearMenuInner;
        ul.insertBefore(newLi, topLi);
        liContainer.appendChild(gearDiv);
        
    },
    
    activateMyLibrary : function () {
        
        UI.showSidebarSection('library');
        
        $(".saved-searches").siblings(".list-section-wrap").slideDown(function () {
            var dropDownScrollBar = $(".saved-searches").parent().children().children(".sub-section-wrap");
            dropDownScrollBar.fadeIn();
            dropDownScrollBar.jScrollPane({
                verticalDragMinHeight: 26,
                verticalDragMaxHeight: 26
            });
        });
        
    },
    
    activateSearchType : function(self) {
        
        // dev note : self needs to be a search category link eg 'Genre'
        
        if (self == null) {
            self = $("#filter-link-vocalinstr");
        }
        
        UI.showSidebarSearchId(self);
    },
    
    activateSidebarTab : function(tab) {
        UI.showSidebarSection(tab);
    },

    /**
     * All search results are funneled through this method
     * 
     * When page > 1, append songs to current playlist
     */
    setPlaylist : function(nodes, append) {
        
        if (nodes.length == 0) {
            return false;
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
        //console.log('actually setting playlist');
        if (append == 1) {
        	console.log('searchState appending playlist');
            audioPlaylist.playlist.push.apply(audioPlaylist.playlist, playlist);
            
            // AFTER THE SONGS ARE PUSHED INTO THE PLAYLIST RESET THE INDEXES ON PLAY BUTTONS
            // BY KARL - TASK/BUG #6 in Plan.io
            var songIndex = 0;
            $('a.main-icon.play').each(function(){
            	$(this).attr('data-listindex',songIndex);
            	songIndex++;
            });
        } else {
        	// TESTING
        	console.log('setting the playlist');
        	//console.log(playlist);
        	
            audioPlaylist.playlist = playlist;
            
            console.log(UI.isPlayerButtons);
            console.log(audioPlaylist.playlist.length);
            
            //if (!UI.isPlayerButtons && audioPlaylist.playlist.length > 0) {
            if (audioPlaylist.playlist.length > 0) {
                UI.togglePlayerButtons('show');
                UI.setPlayerFields(audioPlaylist.playlist[0]);
                audioPlaylist.current = 0;
                audioPlaylist.playlistChange(0);
                audioPlaylist.playlistStop(); // ADDED BY KARL TO STOP AUTOPLAY
            }
        }
    },
    
    toggleSearchBoxSpinner : function(action) {
        
        if (action != 'show') {
            action = 'hide';
        }
        
        if (action == 'show') {
            //add opacity
            $('.sidebar-top-content').css({ opacity: 0.3 });
            
            //show spinner element
            
            
        } else {
            //remove opacity
            $('.sidebar-top-content').css({ opacity: 1.0 });
            
            //hide spinner element
            
        }
        
    },

    checkFilter: function($a) {
        $checkbox = $a.find('input');
        if (!$checkbox.prop('checked')) {
            $checkbox.trigger('click');
        }
    },

    uncheckFilter: function($a) {
        $checkbox = $a.find('input');
        if ($checkbox.prop('checked')) {
            $checkbox.trigger('click');
        }
    }

};
