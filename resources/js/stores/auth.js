import { defineStore } from 'pinia'
import apiClient from '../api/axios'

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: JSON.parse(localStorage.getItem('user')),
        token: localStorage.getItem('token'),
    }),
    getters: {
        isAuthenticated: (state) => !!state.token,
        authUser: (state) => state.user,
    },
    actions: {
        setAuth(data) {
            this.user = data.user
            this.token = data.access_token
            localStorage.setItem('user', JSON.stringify(data.user))
            localStorage.setItem('token', data.access_token)
        },
        clearAuth() {
            this.user = null
            this.token = null
            localStorage.removeItem('user')
            localStorage.removeItem('token')
        },
        async login(credentials) {
            try {
                const response = await apiClient.post('/login', credentials)
                this.setAuth(response.data)
                return response.data
            } catch (error) {
                console.error('Login failed:', error)
                throw error
            }
        },
        async logout() {
            try {
                await apiClient.post('/logout')
            } catch (error) {
                console.error('Logout failed:', error)
                // Even if the server-side logout fails, we still want to clear client-side data
                // to reflect a logged-out state and prevent a stale token from being used.
                throw error // Re-throw the error to be handled by the caller if needed
            } finally {
                this.clearAuth()
            }
        },
    },
})

