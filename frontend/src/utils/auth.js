// Usaremos una técnica simple para persistir el token
export const auth = {
    setToken: (token) => localStorage.setItem('token', token),
    getToken: () => localStorage.getItem('token'),
    setUser: (user) => localStorage.setItem('user', JSON.stringify(user)),
    getUser: () => JSON.parse(localStorage.getItem('user')),
    logout: () => {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
    }
};