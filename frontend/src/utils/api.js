export const apiFetch = async (endpoint, options = {}) => {
    const token = localStorage.getItem('token');
    
    const defaultHeaders = {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
    };

    const response = await fetch(`http://127.0.0.1:8000/api${endpoint}`, {
        ...options,
        headers: { ...defaultHeaders, ...options.headers }
    });

    if (response.status === 401) {
        // Token inválido o expirado
        window.location.href = '/';
    }

    return response;
};