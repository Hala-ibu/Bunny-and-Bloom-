
const AuthController = {
    
    init: () => { 
        document.getElementById('login-form')?.addEventListener('submit', AuthController.handleLogin);
        document.getElementById('myForm')?.addEventListener('submit', AuthController.handleRegister);
        document.getElementById('logoutBtn')?.addEventListener('click', AuthService.logout);
    },
    

    handleLogin: (event) => {
        event.preventDefault();

        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        if (!email || !password) {
            toastr.error('Email and password are required.');
            return;
        }

        AuthService.login(email, password)
            .then(user => {
                toastr.success(`Welcome, ${user.username}!`);
                
                if (user.role === 'admin') {
                    window.location.hash = '#admin';
                } else {
                    window.location.hash = '#Home';
                }
                
                App.updateNav(); 
            })
            .catch(error => {
                const message = error.message || 'Login failed. Check your credentials.';
                toastr.error(message);
            });
    },

    handleRegister: (event) => {
        event.preventDefault();

        const username = document.getElementById('Username').value;
        const email = document.getElementById('Email').value;
        const password = document.getElementById('Password').value;

        if (!username || !email || !password) {
            toastr.error('Username, email, and password are required.');
            return;
        }
        
        if (!document.getElementById('check')?.checked) {
            toastr.error('You must agree to the terms and conditions.');
            return;
        }

        AuthService.register(username, email, password)
            .then(response => {
                toastr.success('Registration successful! Please log in.');
                document.getElementById('myForm').reset();

                window.location.hash = '#login'; 
            })
            .catch(error => {
                const message = error.message || 'Registration failed. Please try a different email or username.';
                toastr.error(message);
            });
    }
};