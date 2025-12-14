const AuthService = {
    login: (email, password) => {
        return RestClient.post('/auth/login', {
            email: email,
            password: password
        })
        .then(response => {
            localStorage.setItem('jwt_token', response.token);
            localStorage.setItem('user_data', JSON.stringify(response.user));
            return response.user;
        });
    },

    register: (username, email, password) => {
        return RestClient.post('/register', {
            username: username,
            email: email,
            password: password
        });
    },


logout: () => {
        localStorage.removeItem('jwt_token');
        localStorage.removeItem('user_data');
        window.location.hash = '#login'; 
    },
    isLoggedIn: () => {
        return !!localStorage.getItem('jwt_token');
    },

    getUserRole: () => {
        const user = AuthService.getCurrentUser();
        return user ? user.role : 'guest';
    }
};