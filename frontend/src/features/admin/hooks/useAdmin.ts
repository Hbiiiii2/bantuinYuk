import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { adminService } from '../services/admin.service'

export const adminKeys = {
  all: ['admin'] as const,
  dashboard: () => [...adminKeys.all, 'dashboard'] as const,
  analytics: () => [...adminKeys.all, 'analytics'] as const,
  users: (params?: any) => [...adminKeys.all, 'users', params] as const,
  userDetail: (id: number) => [...adminKeys.all, 'userDetail', id] as const,
  helpers: (params?: any) => [...adminKeys.all, 'helpers', params] as const,
  helperDetail: (id: number) => [...adminKeys.all, 'helperDetail', id] as const,
  tasks: (params?: any) => [...adminKeys.all, 'tasks', params] as const,
  taskDetail: (id: number) => [...adminKeys.all, 'taskDetail', id] as const,
  disputes: (params?: any) => [...adminKeys.all, 'disputes', params] as const,
  disputeDetail: (id: number) => [...adminKeys.all, 'disputeDetail', id] as const,
  transactions: (params?: any) => [...adminKeys.all, 'transactions', params] as const,
  transactionDetail: (id: number) => [...adminKeys.all, 'transactionDetail', id] as const,
  wallets: (params?: any) => [...adminKeys.all, 'wallets', params] as const
}

// Dashboard & Analytics Queries
export function useAdminDashboard() {
  return useQuery({
    queryKey: adminKeys.dashboard(),
    queryFn: () => adminService.getDashboardSummary(),
    staleTime: 30000
  })
}

export function useAdminAnalytics() {
  return useQuery({
    queryKey: adminKeys.analytics(),
    queryFn: () => adminService.getAnalytics(),
    staleTime: 60000
  })
}

// User Management Hooks
export function useAdminUsers(params?: {
  page?: number
  per_page?: number
  search?: string
  role?: string
  sort_by?: string
}) {
  return useQuery({
    queryKey: adminKeys.users(params),
    queryFn: () => adminService.getUsers(params),
    placeholderData: (prev) => prev,
    staleTime: 15000
  })
}

export function useAdminUserDetail(id: number, enabled = true) {
  return useQuery({
    queryKey: adminKeys.userDetail(id),
    queryFn: () => adminService.getUserDetail(id),
    enabled: enabled && id > 0,
    staleTime: 30000
  })
}

export function useUpdateUserStatus() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: ({ id, active }: { id: number; active: number }) =>
      adminService.updateUserStatus(id, active),
    onSuccess: (_, variables) => {
      queryClient.invalidateQueries({ queryKey: adminKeys.all })
      queryClient.invalidateQueries({ queryKey: adminKeys.userDetail(variables.id) })
    }
  })
}

// Helper Management Hooks
export function useAdminHelpers(params?: {
  page?: number
  per_page?: number
  search?: string
  verification_status?: string
}) {
  return useQuery({
    queryKey: adminKeys.helpers(params),
    queryFn: () => adminService.getHelpers(params),
    placeholderData: (prev) => prev,
    staleTime: 15000
  })
}

export function useAdminHelperDetail(id: number, enabled = true) {
  return useQuery({
    queryKey: adminKeys.helperDetail(id),
    queryFn: () => adminService.getHelperDetail(id),
    enabled: enabled && id > 0,
    staleTime: 30000
  })
}

export function useVerifyHelper() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: (id: number) => adminService.verifyHelper(id),
    onSuccess: (_, id) => {
      queryClient.invalidateQueries({ queryKey: adminKeys.all })
      queryClient.invalidateQueries({ queryKey: adminKeys.helperDetail(id) })
    }
  })
}

export function useRejectHelper() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: ({ id, reason }: { id: number; reason: string }) =>
      adminService.rejectHelper(id, reason),
    onSuccess: (_, variables) => {
      queryClient.invalidateQueries({ queryKey: adminKeys.all })
      queryClient.invalidateQueries({ queryKey: adminKeys.helperDetail(variables.id) })
    }
  })
}

// Task Management Hooks
export function useAdminTasks(params?: {
  page?: number
  per_page?: number
  search?: string
  status?: string
  category_id?: number
}) {
  return useQuery({
    queryKey: adminKeys.tasks(params),
    queryFn: () => adminService.getTasks(params),
    placeholderData: (prev) => prev,
    staleTime: 15000
  })
}

export function useAdminTaskDetail(id: number, enabled = true) {
  return useQuery({
    queryKey: adminKeys.taskDetail(id),
    queryFn: () => adminService.getTaskDetail(id),
    enabled: enabled && id > 0,
    staleTime: 30000
  })
}

// Dispute Management Hooks
export function useAdminDisputes(params?: {
  page?: number
  per_page?: number
  search?: string
  status?: string
}) {
  return useQuery({
    queryKey: adminKeys.disputes(params),
    queryFn: () => adminService.getDisputes(params),
    placeholderData: (prev) => prev,
    staleTime: 15000
  })
}

export function useAdminDisputeDetail(id: number, enabled = true) {
  return useQuery({
    queryKey: adminKeys.disputeDetail(id),
    queryFn: () => adminService.getDisputeDetail(id),
    enabled: enabled && id > 0,
    staleTime: 30000
  })
}

export function useReviewDispute() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: (id: number) => adminService.reviewDispute(id),
    onSuccess: (_, id) => {
      queryClient.invalidateQueries({ queryKey: adminKeys.all })
      queryClient.invalidateQueries({ queryKey: adminKeys.disputeDetail(id) })
    }
  })
}

export function useResolveDispute() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: ({ id, resolution }: { id: number; resolution: string }) =>
      adminService.resolveDispute(id, resolution),
    onSuccess: (_, variables) => {
      queryClient.invalidateQueries({ queryKey: adminKeys.all })
      queryClient.invalidateQueries({ queryKey: adminKeys.disputeDetail(variables.id) })
    }
  })
}

export function useRejectDispute() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: ({ id, resolution }: { id: number; resolution: string }) =>
      adminService.rejectDispute(id, resolution),
    onSuccess: (_, variables) => {
      queryClient.invalidateQueries({ queryKey: adminKeys.all })
      queryClient.invalidateQueries({ queryKey: adminKeys.disputeDetail(variables.id) })
    }
  })
}

// Transaction Management Hooks
export function useAdminTransactions(params?: {
  page?: number
  per_page?: number
  search?: string
  type?: string
  status?: string
}) {
  return useQuery({
    queryKey: adminKeys.transactions(params),
    queryFn: () => adminService.getTransactions(params),
    placeholderData: (prev) => prev,
    staleTime: 30000
  })
}

export function useAdminTransactionDetail(id: number, enabled = true) {
  return useQuery({
    queryKey: adminKeys.transactionDetail(id),
    queryFn: () => adminService.getTransactionDetail(id),
    enabled: enabled && id > 0,
    staleTime: 30000
  })
}

// Wallet Management Hooks
export function useAdminWallets(params?: {
  page?: number
  per_page?: number
  search?: string
}) {
  return useQuery({
    queryKey: adminKeys.wallets(params),
    queryFn: () => adminService.getWallets(params),
    placeholderData: (prev) => prev,
    staleTime: 30000
  })
}
