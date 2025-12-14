const RestClient = {
    request: (method, url, data = null) => {
        const fullUrl = `${API_BASE}${url}`;
        const authHeader = getAuthHeader(); 
        const headers = {
            'Content-Type': 'application/json',
            ...authHeader,
        };

        const config = {
            method: method,
            headers: headers,
        };

        if (data) {
            config.body = JSON.stringify(data);
        }

        return fetch(fullUrl, config)
            .then(async response => {
                const responseData = await response.json().catch(() => ({}));
                
                if (!response.ok) {
                    throw { 
                        status: response.status, 
                        message: responseData.error || responseData.message || 'An unknown error occurred' 
                    };
                }
                
                return responseData;
            })
    },

    get: (url) => {
        return RestClient.request('GET', url);
    },

    post: (url, data) => {
        return RestClient.request('POST', url, data);
    },

    put: (url, data) => {
        return RestClient.request('PUT', url, data);
    },

    delete: (url) => {
        return RestClient.request('DELETE', url);
    }
};