import api from '@/lib/api'
import type { ApiResponse, PaginatedResponse } from '@/types/api.types'
import type { Task, TaskProgress, TaskAttachment } from '@/features/tasks/task.types'
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
    const [statsRes, currentRes, availableRes, profileRes, walletRes] = await Promise.all([
      api.get<ApiResponse<any>>('/helpers/stats'),
      api.get<ApiResponse<PaginatedResponse<Task>>>('/helpers/my-tasks', { params: { statuses: 'in_progress,accepted', per_page: 1 } }),
      api.get<ApiResponse<PaginatedResponse<Task>>>('/helpers/available-tasks', { params: { per_page: 5 } }),
      api.get<ApiResponse<HelperProfile>>('/helpers/profile'),
      api.get<ApiResponse<{ total_earned?: number; balance?: number }>>('/wallet').catch(() => ({ data: { data: { total_earned: 0, balance: 0 } } }))
    ])
    
    const statsData = statsRes.data.data
    const currentTask = currentRes.data.data?.data?.[0] || null
    const nearbyTasks = availableRes.data.data?.data || []
    const profile = profileRes.data.data
    const wallet = walletRes.data.data
    
    const stats: HelperStats = {
      completed_tasks: statsData?.completed_tasks ?? 0,
      current_tasks: statsData?.in_progress_tasks ?? 0,
      total_earnings: wallet?.total_earned ?? wallet?.balance ?? 0,
      rating: profile?.user?.rating ?? 0,
      total_reviews: statsData?.completed_tasks ?? 0
    }
    
    return {
      stats,
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
    const response = await api.get<ApiResponse<PaginatedResponse<Task>>>('/helpers/my-tasks', { params })
    return response.data.data
  },

  async getCurrentTask(): Promise<Task | null> {
    const response = await api.get<ApiResponse<PaginatedResponse<Task>>>('/helpers/my-tasks', { 
      params: { statuses: 'in_progress,accepted', per_page: 1 } 
    })
    return response.data.data?.data?.[0] || null
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
    const response = await api.post<ApiResponse<Task>>(`/helpers/${taskId}/submit`)
    return response.data.data
  },

  async createProgress(taskId: number, data: CreateProgressRequest): Promise<void> {
    await api.post(`/helpers/tasks/${taskId}/progress`, data)
  },

  async getProgress(taskId: number): Promise<TaskProgress[]> {
    const response = await api.get<ApiResponse<PaginatedResponse<TaskProgress>>>(`/helpers/tasks/${taskId}/progress`)
    return response.data.data.data
  },

  async uploadAttachment(taskId: number, file: File): Promise<TaskAttachment> {
    const formData = new FormData()
    formData.append('file', file)
    
    const response = await api.post<ApiResponse<TaskAttachment>>(
      `/helpers/tasks/${taskId}/attachments`,
      formData,
      { headers: { 'Content-Type': 'multipart/form-data' } }
    )
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
    const response = await api.get<ApiResponse<RatingSummary>>('/helpers/rating-summary')
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
