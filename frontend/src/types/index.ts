import type { User } from './api.types'

export * from './api.types'

export interface LoginRequest {
  email: string
  password: string
}

export interface RegisterRequest {
  name: string
  email: string
  phone: string
  password: string
}

export interface LoginResponse {
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

export interface CreateTaskRequest {
  title: string
  description: string
  price: number
  category_id: number
  deadline_start: string
  deadline_end: string
  location?: string
}

export interface CreateReviewRequest {
  rating: number
  review?: string
}

export interface WithdrawRequest {
  amount: number
  description?: string
}

export interface UpdateHelperProfileRequest {
  bio?: string
  skills?: string
}

export interface UpdateLocationRequest {
  latitude: number
  longitude: number
}

export interface CreateDisputeRequest {
  task_id: number
  reason: string
  description: string
}

export interface PaginatedList<T> {
  data: T[]
  total: number
  page: number
  per_page: number
}

export type { User }
