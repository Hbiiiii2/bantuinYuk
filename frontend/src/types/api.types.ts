export interface ApiResponse<T = unknown> {
  success: boolean
  message: string
  data: T
}

export interface ApiError {
  success: false
  message: string
  errors?: Record<string, string>
}

export interface PaginationMeta {
  total: number
  page: number
  per_page: number
}

export interface PaginatedResponse<T> {
  data: T[]
  total: number
  page: number
  per_page: number
}

export interface ListParams {
  page?: number
  per_page?: number
  search?: string
  sort_by?: string
  sort_order?: 'ASC' | 'DESC'
}

export type UserRole = 'user' | 'helper' | 'admin'

export interface User {
  id: number
  name: string
  email: string
  phone: string
  role: UserRole
  photo: string | null
  rating: number
  created_at: string
}

export interface HelperProfile {
  id: number
  user_id: number
  bio: string | null
  skills: string | null
  ktp_number: string | null
  verification_status: 'pending' | 'verified' | 'rejected'
  completed_tasks: number
  created_at: string
}

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

export type TaskStatus = 
  | 'draft'
  | 'open'
  | 'accepted'
  | 'in_progress'
  | 'waiting_approval'
  | 'completed'
  | 'cancelled'

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

export interface Review {
  id: number
  task_id: number
  user_id: number
  helper_id: number
  rating: number
  review: string | null
  user_name: string
  task_title: string
  created_at: string
}

export interface RatingSummary {
  average_rating: number
  total_reviews: number
  completed_tasks: number
  distribution: {
    1: number
    2: number
    3: number
    4: number
    5: number
  }
}

export interface Wallet {
  id: number
  user_id: number
  balance: number
  pending_balance: number
  created_at: string
}

export interface WalletSummary {
  balance: number
  available_balance: number
  pending_balance: number
  total_earned: number
  total_withdrawn: number
  total_refunded: number
  pending_withdrawals: number
}

export interface Transaction {
  id: number
  user_id: number
  task_id: number | null
  amount: number
  type: TransactionType
  status: TransactionStatus
  reference_id: string
  description: string | null
  user_name: string
  created_at: string
}

export type TransactionType = 'task_payment' | 'withdraw' | 'refund' | 'adjustment'
export type TransactionStatus = 'pending' | 'completed' | 'failed' | 'cancelled'

export interface Notification {
  id: number
  user_id: number
  type: NotificationType
  title: string
  message: string
  data: string | null
  is_read: 0 | 1
  created_at: string
}

export type NotificationType = 
  | 'task_created'
  | 'task_accepted'
  | 'task_started'
  | 'task_progress'
  | 'task_submitted'
  | 'task_completed'
  | 'task_cancelled'
  | 'review_received'
  | 'payment_released'
  | 'withdraw_requested'
  | 'withdraw_approved'
  | 'withdraw_rejected'
  | 'dispute_created'
  | 'dispute_under_review'
  | 'dispute_resolved'
  | 'dispute_rejected'

export interface Dispute {
  id: number
  task_id: number
  user_id: number
  helper_id: number | null
  reason: string
  evidence_file: string | null
  admin_note: string | null
  status: DisputeStatus
  resolved_by: number | null
  resolved_at: string | null
  task_title: string
  creator_name: string
  helper_name: string | null
  created_at: string
}

export type DisputeStatus = 'open' | 'under_review' | 'resolved' | 'rejected'

export interface Location {
  id: number
  helper_id: number
  latitude: number
  longitude: number
  updated_at: string
}

export interface Category {
  id: number
  name: string
  description: string | null
}

export interface DashboardSummary {
  users: number
  helpers: number
  tasks: number
  open_tasks: number
  completed_tasks: number
  wallet_transactions: number
  disputes: number
  pending_disputes: number
  notifications: number
}

export interface Analytics {
  total_users: number
  total_helpers: number
  verified_helpers: number
  total_tasks: number
  completed_tasks: number
  completion_rate: number
  total_disputes: number
  resolved_disputes: number
  dispute_rate: number
  total_transaction_amount: number
}
