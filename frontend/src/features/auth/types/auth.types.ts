import type { User } from '@/types'

export interface AuthState {
  user: User | null
  token: string | null
  isAuthenticated: boolean
  loading: boolean
  initialized: boolean
}

export interface LoginCredentials {
  email: string
  password: string
  remember_me?: boolean
}

export interface RegisterData {
  name: string
  email: string
  phone: string
  password: string
  password_confirmation: string
}

export interface AuthResponse {
  user: User
  token: {
    access_token: string
    type: string
    expires_in: number
  }
}

export interface RegisterResponse {
  user_id: number
  name: string
  email: string
}
