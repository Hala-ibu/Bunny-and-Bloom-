const App = {
    protectedRoutes: {
        '#admin': ['admin'],
        '#Profile': ['user', 'admin']
    },

    init: () => {
        const app = $.spapp({
            defaultView: "#Home",
            templateDir: "./pages/"
        });
        
        app.route({
            view: '*', 
            onBeforeLoad: (target) => {
                const viewId = '#' + target.view;
                const requiredRoles = App.protectedRoutes[viewId];
                
                if (requiredRoles) {
                    const isLoggedIn = AuthService.isLoggedIn();
                    
                    if (!isLoggedIn) {
                        toastr.warning('Please log in to access this page.');
                        window.location.hash = '#login'; 
                        return false; 
                    }
                    
                    const userRole = AuthService.getUserRole();
                    if (!requiredRoles.includes(userRole)) {
                        toastr.error('Access forbidden. You do not have permission.');
                        window.location.hash = '#Home';
                        return false; 
                    }
                }
                return true; 
            },
            onReady: (view) => {
                App.updateNav();
            }
        });

        app.route({
            view: 'Profile', 
            load: ProfileController.init 
        });
        
        app.route({
            view: 'login', 
            load: AuthController.init 
        });
        
        app.route({
            view: 'register', 
            load: AuthController.init 
        });


        app.run();
        
        App.updateNav();
    },

    updateNav: () => {
        const isLoggedIn = AuthService.isLoggedIn();
        const userRole = AuthService.getUserRole();
        
        $('#nav-admin, #nav-profile, #nav-login, #nav-register, #nav-logout').addClass('d-none');

        if (isLoggedIn) {
            $('#nav-logout').removeClass('d-none');
            $('#nav-profile').removeClass('d-none'); 

            if (userRole === 'admin') {
                $('#nav-admin').removeClass('d-none');
            }
        } else {
            $('#nav-login').removeClass('d-none');
            $('#nav-register').removeClass('d-none');
            
            $('#nav-login a').attr('href', '#login'); 
            $('#nav-register a').attr('href', '#register'); 
        }
    }
};

$(document).ready(function() {
    App.init();
    
    $('a.nav-link[href^="#"]').on('click', function(event) {
        $('#navbarResponsive').collapse('hide');
    });
});