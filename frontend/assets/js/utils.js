const getAuthHeader = () => {
    const token = localStorage.getItem('jwt_token');
    if (token) {
        return { 'Authorization': `Bearer ${token}` };
    }
    return {};
};

const API_BASE = 'http://localhost/your-project-name/rest';