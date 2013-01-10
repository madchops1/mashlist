featuredArtistApi = {

    getFive : function(onSuccess, onError) {
        $.ajax({
            url : baseUrl + '/music_featured_artist/five',
            success : onSuccess,
            error : onError
        });
    }

};

