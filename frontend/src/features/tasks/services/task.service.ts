import api from '@/lib/api'
import type { ApiResponse, PaginatedResponse } from '@/types/api.types'
import type { 
  Task, 
  TaskListParams, 
  CreateTaskRequest, 
  TaskAttachment,
  TaskProgress,
  Category,
  DashboardData
} from '../task.types'

export const taskService = {
  async getDashboard(): Promise<DashboardData> {
    const [tasksRes, walletRes] = await Promise.all([
      api.get<ApiResponse<PaginatedResponse<Task>>>('/tasks/my', { params: { per_page: 100 } }),
      api.get<ApiResponse<{ balance: number; available_balance: number; pending_balance: number }>>('/wallet')
    ])
    
    const tasks = tasksRes.data.data.data
    const wallet = walletRes.data.data
    
    return {
      stats: {
        total_tasks: tasksRes.data.data.total,
        active_tasks: tasks.filter(t => 
          ['open', 'accepted', 'in_progress'].includes(t.status)
        ).length,
        completed_tasks: tasks.filter(t => t.status === 'completed').length,
        cancelled_tasks: tasks.filter(t => t.status === 'cancelled').length
      },
      recent_tasks: tasks.slice(0, 5),
      wallet_summary: {
        balance: wallet.balance,
        available_balance: wallet.available_balance,
        pending_balance: wallet.pending_balance
      }
    }
  },

  async getTasks(params?: TaskListParams): Promise<PaginatedResponse<Task>> {
    const response = await api.get<ApiResponse<PaginatedResponse<Task>>>('/tasks/my', { params })
    return response.data.data
  },

  async getTaskById(id: number): Promise<Task> {
    const response = await api.get<ApiResponse<Task>>(`/tasks/${id}`)
    return response.data.data
  },

  async createTask(data: CreateTaskRequest): Promise<Task> {
    const response = await api.post<ApiResponse<Task>>('/tasks', data)
    return response.data.data
  },

  async updateTask(id: number, data: Partial<CreateTaskRequest>): Promise<Task> {
    const response = await api.put<ApiResponse<Task>>(`/tasks/${id}`, data)
    return response.data.data
  },

  async cancelTask(id: number): Promise<void> {
    await api.delete(`/tasks/${id}`)
  },

  async completeTask(id: number): Promise<Task> {
    const response = await api.post<ApiResponse<Task>>(`/tasks/${id}/complete`)
    return response.data.data
  },

  async getTaskAttachments(taskId: number): Promise<TaskAttachment[]> {
    const response = await api.get<ApiResponse<TaskAttachment[]>>(`/tasks/${taskId}/attachments`)
    return response.data.data
  },

  async uploadAttachment(taskId: number, file: File): Promise<TaskAttachment> {
    const formData = new FormData()
    formData.append('file', file)
    
    const response = await api.post<ApiResponse<TaskAttachment>>(
      `/tasks/${taskId}/attachments`,
      formData,
      { headers: { 'Content-Type': 'multipart/form-data' } }
    )
    return response.data.data
  },

  async deleteAttachment(attachmentId: number): Promise<void> {
    await api.delete(`/attachments/${attachmentId}`)
  },

  async getTaskProgress(taskId: number): Promise<TaskProgress[]> {
    const response = await api.get<ApiResponse<TaskProgress[]>>(`/tasks/${taskId}/progress`)
    return response.data.data
  },

  async getCategories(): Promise<Category[]> {
    try {
      const response = await api.get<ApiResponse<Category[]>>('/admin/categories')
      return response.data.data
    } catch {
      return [
        { id: 1, name: 'Bangunan', description: 'Construction services' },
        { id: 2, name: 'Pembersihan', description: 'Cleaning services' },
        { id: 3, name: 'Pindahan', description: 'Moving services' },
        { id: 4, name: 'Perbaikan', description: 'Repair services' },
        { id: 5, name: 'Lainnya', description: 'Other services' }
      ]
    }
  }
}
