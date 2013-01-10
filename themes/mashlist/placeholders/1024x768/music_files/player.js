playerUI = {
  // used to instantiate the player
  cssSelector : '',
  cssId : '',
  options : {},
  instance : '',

  // used when "song details" is clicked
  songDetailRow : '',

  // the current song
  currSong : null,

  playlist : {},

  /**
   * Returns the current position in M:S on the playHead.
   */
  getPlayHead : function() {
    return $('.jp-current-time', this.cssSelector.interface).text();
  },

  /**
   * Returns the duration in M:S of the current song.
   */
  getDuration : function() {
    return $('.jp-duration', this.cssSelector.interface).text();
  },

  /**
   * Sets the song title
   */
  setSongTitle : function(title) {
    $('.player-info .player-title').html(title);
  },

  /**
   * Sets the artist title and link
   */
  setArtistTitle : function(title, url) {
    $('.player-sub-title-link.artist').html(title);
    $('.player-sub-title-link.artist').attr('href', url);
  },

  /**
   * Sets the album title and link
   */
  setAlbumTitle : function(title, url) {
    $('.player-sub-title-link.album').html(title);
    $('.player-sub-title-link.album').attr('href', url);
  },

  /**
   * Assigns a download URL to the download button
   */
  setDownloadButton : function(url) {
    $('.player-button.download').css('visibility','visible');
    $('.player-button.download').attr('href', url);
    $('.player-button.download').attr('target','_blank');
  },

  /**
   * Sets up the "share" link
   */
  setShareButton : function() {
    // what is this supposed to do??
  },

  /**
   * Triggers the "add to playlist" radio button next to the song in results
   */
  setCheckedButton : function(songRow) {
    // wipe the old function
    $('.player-button.add').unbind('click');
    $('.player-button.add').click(function(){
      $('a.checkbox-custom', songRow).toggleClass('checked');
    });
  },

  setPlaceholderImage : function(src) {
    //alert(window.location.href);
    $('.player-thumbnail img').attr('src', src);
  },


  // -------- Player functions ---------- //

  /**
   * Stops the player
   */
  stop : function() {
    $(this.cssSelector.jPlayer).jPlayer('stop');
  },

  /**
   * Plays a song
   * @param string songURL
   *  The real URL of the song to be played
   * @param int index
   *  Number of seconds to offset the play head.
   */
  play : function(songURL, index) {
    if (null !== songURL || 'undefined' !== songURL) {
      this.loadSong(songURL);
    }
    if (null !== index) {
      $(this.cssSelector.jPlayer).jPlayer('play', index);
    }
    else {
      $(this.cssSelector.jPlayer).jPlayer('play');
    }
  },

  /**
   * Loads a song into the player
   */
  loadSong : function(url) {
    $(this.cssSelector.jPlayer).jPlayer('setMedia', {mp3: url});
    $(this.cssSelector.jPlayer).jPlayer('load');
  },

  /**
   * Removes the currently loaded song from memory
   */
  clear : function() {
    $(this.cssSelector.jPlayer).jPlayer('clearMedia');
  },

  /**
   * Plays the next song in the list
   */
  playNext : function() {
    var next = $(this.currSong).next();

    if ('undefined' == next
      || null == next
      || next.length == 0) {
      return this.setupSong($(this.currSong).siblings(':first'));
    }

    this.setupSong(next);
  },

  /**
   * Plays the next song in the list
   */
  playPrevious : function() {
    var prev = $(this.currSong).prev();
    if ('undefined' == prev
      || null == prev
      || prev.length == 0) {
      return this.setupSong($(this.currSong).siblings(':last'));
    }
    this.setupSong(prev);
  },

  /**
   * Sets the playlist title and count
   */
  setPlaylistTitleAndCount : function(title, count) {
    $('#playlist-section .playlist-title').html(title);
    $('#playlist-section .section-main-heading-number').html(count);
  },

  /**
   * Takes information from the result list about a song,
   * changes the player information,
   * updates interface buttons and attempts to play the song.
   * @param element uniqueRow
   *  A uniquely identifiable table row in the playlist or resultlist.
   */
  setupSong : function(uniqueRow) {
    this.stop();
    this.currSong = uniqueRow;
    //console.log('setup song');
    var data = {
      'songURL' : $(".main-icon.play",uniqueRow).data('url'),
      'songTitle' : $('.song-title-cell',uniqueRow).text(),
      'songArtist' : {
        'title' : $('.artist-name-cell .main-cell-value', uniqueRow).text(),
        'url' : $('.artist-title-cell a', uniqueRow).attr('href')
      },
      'album' : {
        'title' : $('.album-name-cell .main-cell-value', uniqueRow).text(),
        'url' : '#'
      },
      'playHead' : $(".main-icon.play", uniqueRow).data('start-at')
    };

    // update the player UI first
    this.setSongTitle(data.songTitle);
    this.setArtistTitle(data.songArtist.title, data.songArtist.url);
    this.setAlbumTitle(data.album.title, data.album.url);
    this.setDownloadButton(data.songURL);
    this.setCheckedButton(uniqueRow);

    if ('undefined' !== data.playHead
      || null !== data.playHead
      || false !== data.playHead) {
      this.play(data.songURL, data.playHead);
    }
    else {
      this.play(data.songURL);
    }
  },

  /**
   * Sets up the player and buttons
   */
  init : function() {
    var self = this;
    this.instance = 1;
    this.options = {
      ready: function() {
        // When the player loads, reset the volume to the user's preferred setting
        // This must occur on the jPlayer's ready() function or it will fall back to 1.0
        $(self.cssSelector.jPlayer).jPlayer('volume', $.cookie('jp_volume'));
      },
      ended: function() {
        self.playNext();
      },
      play: function() {
        // finds all instances of jPlayer and pauses them
        $(self.cssSelector.jPlayer).jPlayer("pauseOthers");
      },
      swfPath: "assets/scripts",
      supplied: "mp3"
    };

    this.cssId = {
      jPlayer: "jquery_jplayer_",
      interface: "jp_interface_"
    };
    this.cssSelector = {};

    $.each(this.cssId, function(entity, id) {
      self.cssSelector[entity] = "#" + id + self.instance;
    });

    // instantiate the jPlayer
    $(this.cssSelector.jPlayer).jPlayer(this.options);

    // Handles previous() function.
    $(this.cssSelector.interface + " .jp-previous").click(function() {
      self.playPrevious();
      $(this).blur();
      return false;
    });

    // Handles next() function.
    $(this.cssSelector.interface + " .jp-next").click(function() {
      self.playNext();
      $(this).blur();
      return false;
    });

    // hide the pause button
    $(".jp-pause", this.cssSelector.interface).hide();

    // make the player head draggable
    
    // dev-note : disabled on 8/29 because of error "not a function"
    
    $(".jp-play-bar", this.cssSelector.interface).draggable({
      axis: "x",
      containment: "parent"
    });

    // make the volume bar draggable
    /*
    $(".jp-volume-bar-value", this.cssSelector.interface).draggable({
      axis: "x",
      containment: "parent"
    });
    */

    // Copy the volume setting into the player object when changed
    $(this.cssSelector.jPlayer).bind($.jPlayer.event.volumechange, function(event) {
      self.currentVolume = event.jPlayer.status.volume;
      $.cookie('jp_volume', event.jPlayer.status.volume, {expires: new Date('+180 days'), path: '/'});
    });

   
   
    // I DON'T THINK THIS CLICK EVENT IS CALLED SO I AM COMMENTING IT OUT
    // This is in global.js
    // -kjs
    
    /*
    // ----------------------------- Should be moved ----------------------------- //
    // The live() function requires jQuery 1.5 or newer
    // and ensures DOM elements added to page will have
    // the same event listeners assigned them as
    // one set with .click(function(){}); now
    $(".main-icon.play").live('click',function(){
      // gets the song NID
      var nid = $(this).parent().parent().data('nid');
      // uniquely identifies this song row in results
      var uniqueRow = $(this).parents('tr[data-nid=' + nid + ']');
      console.log(uniqueRow);
      self.setupSong(uniqueRow);
    });
	*/
    
    
    // When a "detail button" is clicked,
    // copy this row instance into a placeholder.
    // If the user clicks on "listen" in
    // the song detail view,
    // populate player elements with details from the row.
    $(".main-icon.detail").live('click',function(){
      // gets the song NID
      var nid = $(this).parent().parent().data('nid');
      // uniquely identifies this song row in results
      var uniqueRow = $(this).parents('tr[data-nid=' + nid + ']');
      self.songDetailRow = uniqueRow;
      return false;
    });

    // If the "song-details" section changes, this may need to be updated
    $("a.song-details-listen").click(function(){
      self.setupSong(self.songDetailRow);
      return false;
    });

  }
};

