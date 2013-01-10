var songApi = (function($) {
     
    var pub = {}
    
    pub.getSong = function(songNid, options, onSuccess, onError) {
        $.ajax({
            url: baseUrl + '/music_song/single/' + songNid,
            data: options,
            success: onSuccess,
            error: onError
        });
    };

    pub.getSongsByArtist = function(artistNid, options, onSuccess, onError) {
        $.ajax({
            url: baseUrl + '/music_song/artist/' + artistNid,
            data: options,
            success: onSuccess,
            error: onError
        });
    };

    pub.getSongsByAlbum = function(albumNid, options, onSuccess, onError) {
        $.ajax({
            url: baseUrl + '/music_song/album/' + albumNid,
            data: options,
            success: onSuccess,
            error: onError
        });
    };

    pub.getSongsByPlaylist = function(playlistNid, options, onSuccess, onError) {
        $.ajax({
            url: baseUrl + '/music_song/playlist/' + playlistNid,
            data: options,
            success: onSuccess,
            error: onError
        });
    };
   
    pub.getSongsByTids = function(csvTids, options, onSuccess, onError) {
        $.ajax({
            url: baseUrl + '/music_song/search/' + csvTids,
            data: options,
            success: onSuccess,
            error: onError
        });
    };

    pub.getSongDetail = function(songNid, playlistItemId, isSearch) {
        
        var url = baseUrl + '/music_song/detail/' + songNid + '?';
        
        if (playlistItemId && playlistItemId != 0) {
            url += 'playlistItemId=' + playlistItemId + '&amp;';
        }
        
        if (isSearch == 1) {
            url += 'isSearch=1';
        }
        
        $.ajax({
            url: url,
            success: function(response) {
              
              $("#song-details-section").html(response);              
              UI.showContentSectionId('song-details-section');
              
            }
        });
    };

    pub.getTopSongs = function(onSuccess, onError) {
        $.ajax({
            url: baseUrl + '/music_song/topsongs',
            success: onSuccess,
            error: onError
        });
    };
    
    return pub;

})(jQuery);

