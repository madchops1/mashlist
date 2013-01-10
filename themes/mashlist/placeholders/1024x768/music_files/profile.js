profileApi = {
    
    get: function(options) {
        var options = options || {};
        options.type = 'get';
        options.url = baseUrl + '/music_profile/get';
        if (!options.data) {
            options.data = {'format':'json'};
        }
        $.ajax(options);
    },

    updateProfile: function(postVars, onSuccess, onError) {
        $.ajax({
            type: 'post',
            url: baseUrl + '/music_profile/update',
            data: postVars,
            success: onSuccess,
            error: onError
        });
    }
    
};


