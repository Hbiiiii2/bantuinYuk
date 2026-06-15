import api from '@/lib/api'
import type { ApiResponse, PaginatedResponse } from '@/types/api.types'
import type { Task } from '@/features/tasks/task.types'
import type { 
  HelperProfile, 
  HelperDashboardData, 
  HelperStats,
  RatingSummary,
  UpdateProfileRequest,
  CreateProgressRequest,
  TaskListParams
} from '../types/helper.types'

export const helperService = {
  async getDashboard(): Promise<HelperDashboardData> {
    const [statsRes, currentRes, availableRes] = await Promise.all([
      api.get<ApiResponse<HelperStats>>('/helpers/stats'),
      api.get<ApiResponse<Task[]>>('/helpers/tasks', { params: { statuses: 'in_progress,accepted', per_page: 1 } }),
      api.get<ApiResponse<PaginatedResponse<Task>>>('/tasks', { params: { status: 'open', per_page: 5 } })
    ])
    
    const currentTask = currentRes.data.data?.[0] || null
    const nearbyTasks = availableRes.data.data.data || []
    
    return {
      stats: statsRes.data.data,
      current_task: currentTask,
      nearby_tasks: nearbyTasks
    }
  },

  async getAvailableTasks(params?: TaskListParams): Promise<PaginatedResponse<Task>> {
    const response = await api.get<ApiResponse<PaginatedResponse<Task>>>('/tasks', { 
      params: { ...params, status: 'open' } 
    })
    return response.data.data
  },

  async getTaskById(id: number): Promise<Task> {
    const response = await api.get<ApiResponse<Task>>(`/tasks/${id}`)
    return response.data.data
  },

  async getMyTasks(params?: TaskListParams): Promise<PaginatedResponse<Task>> {
    const response = await api.get<ApiResponse<PaginatedResponse<Task>>>('/helpers/tasks', { params })
    return response.data.data
  },

  async getCurrentTask(): Promise<Task | null> {
    const response = await api.get<ApiResponse<Task[]>>('/helpers/tasks', { 
      params: { statuses: 'in_progress,accepted', per_page: 1 } 
    })
    return response.data.data?.[0] || null
  },

  async acceptTask(taskId: number): Promise<Task> {
    const response = await api.post<ApiResponse<Task>>(`/helpers/tasks/${taskId}/accept`)
    return response.data.data
  },

  async startTask(taskId: number): Promise<Task> {
    const response = await api.post<ApiResponse<Task>>(`/helpers/tasks/${taskId}/start`)
    return response.data.data
  },

  async submitTask(taskId: number): Promise<Task> {
    const response = await api.post<ApiResponse<Task>>(`/helpers/tasks/${taskId}/submit`)
    return response.data.data
  },

  async createProgress(taskId: number, data: CreateProgressRequest): Promise<void> {
    await api.post(`/tasks/${taskId}/progress`, data)
  },

  async getProgress(taskId: number): Promise<unknown[]> {
    const response = await api.get<ApiResponse<unknown[]>>(`/tasks/${taskId}/progress`)
    return response.data.data
  },

  async getProfile(): Promise<HelperProfile> {
    const response = await api.get<ApiResponse<HelperProfile>>('/helpers/profile')
    return response.data.data
  },

  async updateProfile(data: UpdateProfileRequest): Promise<HelperProfile> {
    const response = await api.put<ApiResponse<HelperProfile>>('/helpers/profile', data)
    return response.data.data
  },

  async updateLocation(data: { latitude: number; longitude: number }): Promise<void> {
    await api.put('/helpers/location', data)
  },

  async getRatingSummary(): Promise<RatingSummary> {
    const helper = await this.getProfile()
    const response = await api.get<ApiResponse<RatingSummary>>(`/helpers/${helper.id}/rating-summary`)
    return response.data.data
  },

  async getCategories(): Promise<{ id: number; name: string }[]> {
    try {
      const response = await api.get<ApiResponse<{ id: number; name: string }[]>>('/admin/categories')
      return response.data.data
    } catch {
      return [
        { id: 1, name: 'Bangunan' },
        { id: 2, name: 'Pembersihan' },
        { id: 3, name: 'Pindahan' },
        { id: 4, name: 'Perbaikan' },
        { id: 5, name: 'Lainnya' }
      ]
    }
  }
}
