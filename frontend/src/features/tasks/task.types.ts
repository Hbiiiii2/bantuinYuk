export type TaskStatus = 
  | 'draft'
  | 'open'
  | 'accepted'
  | 'in_progress'
  | 'waiting_approval'
  | 'completed'
  | 'cancelled'

export interface Task {
  id: number
  user_id: number
  helper_id: number | null
  category_id: number
  title: string
  description: string
  price: number
  location: string | null
  deadline_start: string
  deadline_end: string
  status: TaskStatus
  category_name: string
  user_name: string
  helper_name: string | null
  status_history: StatusHistory[]
  created_at: string
  updated_at: string
}

export interface StatusHistory {
  id: number
  task_id: number
  status: TaskStatus
  note: string | null
  created_by: number
  created_by_name: string
  created_at: string
}

export interface TaskAttachment {
  id: number
  task_id: number
  user_id: number
  file_name: string
  file_path: string
  file_type: string
  file_size: number
  created_at: string
}

export interface TaskProgress {
  id: number
  task_id: number
  helper_id: number
  helper_name: string
  description: string
  attachment: string | null
  attachment_ids: number[]
  attachments: TaskAttachment[]
  status: 'active' | 'deleted'
  created_at: string
}

export interface Category {
  id: number
  name: string
  description: string | null
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

export interface TaskListParams {
  page?: number
  per_page?: number
  search?: string
  status?: TaskStatus
  category_id?: number
  sort_by?: string
  sort_order?: 'ASC' | 'DESC'
}

export interface TaskStats {
  total_tasks: number
  active_tasks: number
  completed_tasks: number
  cancelled_tasks: number
}

export interface DashboardData {
  stats: TaskStats
  recent_tasks: Task[]
  wallet_summary: {
    balance: number
    available_balance: number
    pending_balance: number
  }
}
