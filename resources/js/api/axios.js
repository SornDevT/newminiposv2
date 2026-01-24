import axios from "axios";
import { useAuthStore } from "../stores/auth";

const apiClient = axios.create({
    baseURL: '/api',
    withCredentials: true, // this might be necessary for Laravel Sanctum
});

apiClient.interceptors.request.use(config => {
    const authStore = useAuthStore();
    const token = authStore.token;
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
}, error => {
    return Promise.reject(error);
});

apiClient.interceptors.response.use(
    response => response,
    async error => {
        const authStore = useAuthStore();
        if (error.response && error.response.status === 401 && authStore.token) {
            console.warn('401 Unauthorized: Clearing auth state and redirecting to login.');
            authStore.clearAuth(); // Directly clear auth state, don't rely on authStore.logout() which makes another API call
            if (window.location.pathname !== '/login') { // Prevent redirect loop if already on login page
                window.location.href = '/login';
            }
        }
        return Promise.reject(error);
    }
);

export default apiClient;
