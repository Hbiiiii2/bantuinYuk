export interface Notification {
  id: number
  user_id: number
  type: string
  title: string
  message: string
  data: string // JSON string
  is_read: number // 0 or 1
  created_at: string
}

export interface UnreadCountResponse {
  unread_count: number
}
