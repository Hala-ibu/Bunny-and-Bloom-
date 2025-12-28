var AuthService = {
    login: function(loginData) {
        return $.ajax({
            url: 'rest/login',
            type: 'POST',
            data: JSON.stringify(loginData),
            contentType: "application/json"
        });
    },

    logout: function() {
        localStorage.removeItem('jwt_token');
    }
};