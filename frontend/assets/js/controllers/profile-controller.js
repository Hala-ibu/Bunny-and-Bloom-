const ProfileController = {

    init: () => {
        document.getElementById('profile-form')?.addEventListener('submit', ProfileController.handleUpdateProfile);
        
        ProfileController.loadProfileData();

        document.getElementById('editProfileBtn')?.addEventListener('click', ProfileController.toggleEditMode);
    },

    loadProfileData: () => {
        UserService.getCurrentUserProfile()
            .then(user => {
                document.getElementById('username').value = user.username || '';
                document.getElementById('email').value = user.email || '';
                document.getElementById('role').value = user.role || '';
                
                document.getElementById('profile-name-display').innerText = user.username || 'User Profile';
            })
            .catch(error => {
                toastr.error(`Failed to load profile: ${error.message}`);
                window.location.hash = '#Home';
            });
    },

    handleUpdateProfile: (event) => {
        event.preventDefault();

        const username = document.getElementById('username').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('new-password').value; 

        if (!username || !email) {
            toastr.warning('Username and Email are required.');
            return;
        }
        
        const updateData = {
            username: username,
            email: email
        };

        if (password.length > 0) {
            updateData.password = password;
        }

        const user = AuthService.getCurrentUser();
        if (!user || !user.id) {
            toastr.error('Authentication error. Please log in again.');
            return;
        }

        UserService.updateUserProfile(user.id, updateData)
            .then(updatedUser => {
                toastr.success('Profile updated successfully!');
                AuthService.updateLocalUser(updatedUser);
                
                ProfileController.toggleEditMode(); 
                document.getElementById('new-password').value = '';
            })
            .catch(error => {
                const message = error.message || 'Profile update failed. Check your input.';
                toastr.error(message);
            });
    },
    
    toggleEditMode: () => {
        const isEditingActive = document.getElementById('editProfileBtn').innerText === 'Save Changes';
        
        const fields = document.querySelectorAll('#profile-form input:not(#role)'); 

        fields.forEach(field => {
            field.readOnly = isEditingActive;
        });

        document.getElementById('password-group').classList.toggle('d-none', !isEditingActive);

        document.getElementById('editProfileBtn').innerText = isEditingActive ? 'Edit Profile' : 'Save Changes';
        document.getElementById('editProfileBtn').type = isEditingActive ? 'button' : 'submit'; 


        if(isEditingActive) {
            ProfileController.loadProfileData();
        }
    }
};