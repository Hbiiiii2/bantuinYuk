import axios, { AxiosError, InternalAxiosRequestConfig, AxiosResponse } from 'axios'
import type { ApiError } from '@/types'

const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://bantuinYuk.test/api/v1'

const api = axios.create({
  baseURL: API_BASE_URL,
  timeout: 30000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

api.interceptors.request.use(
  (config: InternalAxiosRequestConfig) => {
    const token = getStoredToken()
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

api.interceptors.response.use(
  (response: AxiosResponse) => {
    return response
  },
  (error: AxiosError<ApiError>) => {
    if (error.response?.status === 401) {
      clearAuth()
      window.location.href = '/login'
    }
    
    const apiError: ApiError = {
      success: false,
      message: error.response?.data?.message || 'An error occurred',
      errors: error.response?.data?.errors
    }
    
    return Promise.reject(apiError)
  }
)

function getStoredToken(): string | null {
  try {
    const authData = localStorage.getItem('bantuin-auth')
    if (authData) {
      const parsed = JSON.parse(authData)
      return parsed?.state?.token || null
    }
  } catch {
    return null
  }
  return null
}

function clearAuth(): void {
  localStorage.removeItem('bantuin-auth')
}

export default api
