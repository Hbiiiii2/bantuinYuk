import api from '@/lib/api'
import type { ApiResponse, PaginatedResponse } from '@/types/api.types'
import type { Notification, UnreadCountResponse } from '../types/notification.types'

export const notificationService = {
  async getNotifications(params?: { page?: number; per_page?: number; unread?: string }): Promise<PaginatedResponse<Notification> & { unread_count: number }> {
    const response = await api.get<ApiResponse<PaginatedResponse<Notification> & { unread_count: number }>>('/notifications', { params })
    return response.data.data
  },

  async getUnreadCount(): Promise<number> {
    const response = await api.get<ApiResponse<UnreadCountResponse>>('/notifications/unread-count')
    return response.data.data.unread_count
  },

  async markAsRead(id: number): Promise<void> {
    await api.post(`/notifications/${id}/read`)
  },

  async markAllAsRead(): Promise<void> {
    await api.post('/notifications/read-all')
  }
}
