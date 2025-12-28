var UserService = {
    register: function(data) {
        if (data.password.length < 6) {
            return $.Deferred().reject({ 
                responseJSON: { error: "Password must be at least 6 characters" } 
            });
        }

        return $.ajax({
            url: 'rest/register',
            type: 'POST',
            data: JSON.stringify(data),
            contentType: "application/json"
        });
    }
};