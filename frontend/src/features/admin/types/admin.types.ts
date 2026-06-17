import type { Task } from '@/types/api.types'

export type { Task }

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

export interface User {
  id: number
  name: string
  email: string
  phone: string
  role: 'user' | 'helper' | 'admin'
  photo?: string | null
  rating?: number
  active: number // 0 = suspended, 1 = active
  created_at: string
}

export interface UserProfile extends User {
  stats: {
    total_tasks: number
    completed_tasks: number
  }
}

export interface HelperProfile {
  id: number
  user_id: number
  bio: string
  skills: string
  ktp_number: string
  verification_status: 'pending' | 'verified' | 'rejected'
  completed_tasks: number
  created_at: string
}

export interface HelperList extends HelperProfile {
  name: string
  email: string
  phone: string
  rating: number
}

export interface Dispute {
  id: number
  task_id: number
  user_id: number
  helper_id: number
  status: 'open' | 'under_review' | 'resolved' | 'rejected'
  reason: string
  description: string
  resolution?: string | null
  created_at: string
  task_title?: string
  creator_name?: string
  helper_name?: string
  evidence_file?: string | null
  admin_note?: string | null
  resolved_by?: number | null
  resolved_at?: string | null
}

export interface Transaction {
  id: number
  type: 'task_payment' | 'withdraw' | 'refund' | 'adjustment'
  amount: number
  status: 'pending' | 'completed' | 'failed' | 'cancelled'
  user_id: number
  created_at: string
  description?: string
  user_name?: string
  user_email?: string
  reference_id?: string | null
}

export interface Wallet {
  id: number
  user_id: number
  balance: number
  pending_balance: number
  created_at: string
  user_name?: string
  user_email?: string
}
