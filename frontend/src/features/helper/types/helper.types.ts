import type { Task } from '@/features/tasks/task.types'

export interface HelperProfile {
  id: number
  user_id: number
  bio: string | null
  skills: string | null
  ktp_number: string | null
  verification_status: 'pending' | 'verified' | 'rejected'
  completed_tasks: number
  created_at: string
  user?: {
    id: number
    name: string
    email: string
    phone: string
    photo: string | null
    rating: number
  }
  location?: {
    id: number
    latitude: number
    longitude: number
    updated_at: string
  }
}

export interface HelperStats {
  completed_tasks: number
  current_tasks: number
  total_earnings: number
  rating: number
  total_reviews: number
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

export interface AvailableTask extends Task {
  distance?: number
}

export interface HelperDashboardData {
  stats: HelperStats
  current_task: Task | null
  nearby_tasks: AvailableTask[]
}

export interface UpdateProfileRequest {
  bio?: string
  skills?: string
}

export interface UpdateLocationRequest {
  latitude: number
  longitude: number
}

export interface CreateProgressRequest {
  description: string
  attachment_ids?: number[]
}

export interface TaskListParams {
  page?: number
  per_page?: number
  search?: string
  category_id?: number
  status?: string
}
