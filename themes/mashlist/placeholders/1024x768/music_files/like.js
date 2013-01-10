var likeApi = {

    /**
     * Add 1 'like' to a song. Maxes out at 1 like per user per song.
     * @param Number nid
     *   The NID of the song node. If no song node has that NID, no likes will be registered.
     * @param Object options
     *   An object packed with settings as accepted by jQuery.ajax().
     * @return String
     *   A JSON representation of the value returned by votingapi_set_votes().
     */
    like: function(nid, options, isLike) {
        if (isLike) {
            options.url = baseUrl + '/music_like/like/' + nid;
        } else {
            options.url = baseUrl + '/music_like/unlike/' + nid;
        }
        options.type = 'post';
        $.ajax(options);
    }

};

