const UserService = {

    getCurrentUserProfile: () => {
        const user = AuthService.getCurrentUser();
        if (!user || !user.id) {
            return Promise.reject({ message: "User not logged in or ID missing." });
        }
        return RestClient.get(`/users/${user.id}`);
    },

    updateUserProfile: (id, data) => {
        return RestClient.put(`/users/${id}`, data);
    }
};