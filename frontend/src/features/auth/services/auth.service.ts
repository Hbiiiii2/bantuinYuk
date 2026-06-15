import api from '@/lib/api'
import type { ApiResponse, User, LoginRequest, RegisterRequest } from '@/types'

interface LoginResponse {
  user: User
  token: {
    access_token: string
    type: string
    expires_in: number
  }
}

interface RegisterResponse {
  user_id: number
  name: string
  email: string
}

export const authService = {
  async login(data: LoginRequest): Promise<LoginResponse> {
    const response = await api.post<ApiResponse<LoginResponse>>('/auth/login', data)
    return response.data.data
  },

  async register(data: RegisterRequest): Promise<RegisterResponse> {
    const response = await api.post<ApiResponse<RegisterResponse>>('/auth/register', data)
    return response.data.data
  },

  async logout(): Promise<void> {
    try {
      await api.post('/auth/logout')
    } catch {
      // Logout should succeed even if API fails
    }
  },

  async getCurrentUser(): Promise<User> {
    const response = await api.get<ApiResponse<User>>('/auth/me')
    return response.data.data
  }
}
