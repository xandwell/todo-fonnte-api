import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';

// Add a request interceptor
axios.interceptors.request.use(
    config => {
        const token = localStorage.getItem('token');
        if (token) {
            config.headers['Authorization'] = `Bearer ${token}`;
        }
        return config;
    },
    error => {
        return Promise.reject(error);
    }
);

// Add a response interceptor
axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response.status === 401) {
            // Token expired or invalid, redirect to login
            localStorage.removeItem('token');
            window.location.href = "/login";
        }
        return Promise.reject(error);
    }
);