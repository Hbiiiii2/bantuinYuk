import api from '@/lib/api'
import type { ApiResponse, PaginatedResponse } from '@/types/api.types'
import type {
  DashboardSummary,
  Analytics,
  User,
  UserProfile,
  HelperList,
  HelperProfile,
  Task,
  Dispute,
  Transaction,
  Wallet
} from '../types/admin.types'

export const adminService = {
  // Dashboard & Analytics
  async getDashboardSummary(): Promise<DashboardSummary> {
    const response = await api.get<ApiResponse<DashboardSummary>>('/admin/dashboard')
    return response.data.data
  },

  async getAnalytics(): Promise<Analytics> {
    const response = await api.get<ApiResponse<Analytics>>('/admin/analytics')
    return response.data.data
  },

  // User Management
  async getUsers(params?: {
    page?: number
    per_page?: number
    search?: string
    role?: string
    sort_by?: string
  }): Promise<PaginatedResponse<User>> {
    const response = await api.get<ApiResponse<PaginatedResponse<User>>>('/admin/users', { params })
    return response.data.data
  },

  async getUserDetail(id: number): Promise<UserProfile> {
    const response = await api.get<ApiResponse<UserProfile>>(`/admin/users/${id}`)
    return response.data.data
  },

  async updateUserStatus(id: number, active: number): Promise<User> {
    const response = await api.put<ApiResponse<User>>(`/admin/users/${id}/status`, { active })
    return response.data.data
  },

  // Helper Management
  async getHelpers(params?: {
    page?: number
    per_page?: number
    search?: string
    verification_status?: string
  }): Promise<PaginatedResponse<HelperList>> {
    const response = await api.get<ApiResponse<PaginatedResponse<HelperList>>>('/admin/helpers', { params })
    return response.data.data
  },

  async getHelperDetail(id: number): Promise<HelperProfile> {
    const response = await api.get<ApiResponse<HelperProfile>>(`/admin/helpers/${id}`)
    return response.data.data
  },

  async verifyHelper(id: number): Promise<HelperProfile> {
    const response = await api.post<ApiResponse<HelperProfile>>(`/admin/helpers/${id}/verify`)
    return response.data.data
  },

  async rejectHelper(id: number, reason: string): Promise<HelperProfile> {
    const response = await api.post<ApiResponse<HelperProfile>>(`/admin/helpers/${id}/reject`, { reason })
    return response.data.data
  },

  // Task Management
  async getTasks(params?: {
    page?: number
    per_page?: number
    search?: string
    status?: string
    category_id?: number
  }): Promise<PaginatedResponse<Task>> {
    const response = await api.get<ApiResponse<PaginatedResponse<Task>>>('/admin/tasks', { params })
    return response.data.data
  },

  async getTaskDetail(id: number): Promise<Task> {
    const response = await api.get<ApiResponse<Task>>(`/admin/tasks/${id}`)
    return response.data.data
  },

  // Dispute Management
  async getDisputes(params?: {
    page?: number
    per_page?: number
    search?: string
    status?: string
  }): Promise<PaginatedResponse<Dispute>> {
    const response = await api.get<ApiResponse<PaginatedResponse<Dispute>>>('/admin/disputes', { params })
    return response.data.data
  },

  // Note: GET dispute details uses the verified general endpoint /disputes/{id}
  async getDisputeDetail(id: number): Promise<Dispute> {
    const response = await api.get<ApiResponse<Dispute>>(`/disputes/${id}`)
    return response.data.data
  },

  async reviewDispute(id: number): Promise<Dispute> {
    const response = await api.post<ApiResponse<Dispute>>(`/admin/disputes/${id}/review`)
    return response.data.data
  },

  async resolveDispute(id: number, resolution: string): Promise<Dispute> {
    const response = await api.post<ApiResponse<Dispute>>(`/admin/disputes/${id}/resolve`, { resolution })
    return response.data.data
  },

  async rejectDispute(id: number, resolution: string): Promise<Dispute> {
    const response = await api.post<ApiResponse<Dispute>>(`/admin/disputes/${id}/reject`, { resolution })
    return response.data.data
  },

  // Transaction Management
  async getTransactions(params?: {
    page?: number
    per_page?: number
    search?: string
    type?: string
    status?: string
  }): Promise<PaginatedResponse<Transaction>> {
    const response = await api.get<ApiResponse<PaginatedResponse<Transaction>>>('/admin/transactions', { params })
    return response.data.data
  },

  async getTransactionDetail(id: number): Promise<Transaction> {
    const response = await api.get<ApiResponse<Transaction>>(`/admin/transactions/${id}`)
    return response.data.data
  },

  // Wallet Management
  async getWallets(params?: {
    page?: number
    per_page?: number
    search?: string
  }): Promise<PaginatedResponse<Wallet>> {
    const response = await api.get<ApiResponse<PaginatedResponse<Wallet>>>('/admin/wallets', { params })
    return response.data.data
  }
}
