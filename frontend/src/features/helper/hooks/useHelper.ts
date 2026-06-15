import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { helperService } from '../services/helper.service'
import type { TaskListParams, UpdateProfileRequest, CreateProgressRequest } from '../types/helper.types'

export const helperKeys = {
  all: ['helper'] as const,
  dashboard: () => [...helperKeys.all, 'dashboard'] as const,
  availableTasks: (params?: TaskListParams) => [...helperKeys.all, 'availableTasks', params] as const,
  myTasks: (params?: TaskListParams) => [...helperKeys.all, 'myTasks', params] as const,
  currentTask: () => [...helperKeys.all, 'currentTask'] as const,
  taskDetail: (id: number) => [...helperKeys.all, 'task', id] as const,
  progress: (taskId: number) => [...helperKeys.all, 'progress', taskId] as const,
  profile: () => [...helperKeys.all, 'profile'] as const,
  ratingSummary: () => [...helperKeys.all, 'ratingSummary'] as const,
  categories: () => [...helperKeys.all, 'categories'] as const
}

export function useHelperDashboard() {
  return useQuery({
    queryKey: helperKeys.dashboard(),
    queryFn: () => helperService.getDashboard(),
    staleTime: 30000
  })
}

export function useAvailableTasks(params?: TaskListParams) {
  return useQuery({
    queryKey: helperKeys.availableTasks(params),
    queryFn: () => helperService.getAvailableTasks(params),
    staleTime: 30000
  })
}

export function useHelperTask(id: number) {
  return useQuery({
    queryKey: helperKeys.taskDetail(id),
    queryFn: () => helperService.getTaskById(id),
    enabled: !!id
  })
}

export function useMyTasks(params?: TaskListParams) {
  return useQuery({
    queryKey: helperKeys.myTasks(params),
    queryFn: () => helperService.getMyTasks(params),
    staleTime: 30000
  })
}

export function useCurrentTask() {
  return useQuery({
    queryKey: helperKeys.currentTask(),
    queryFn: () => helperService.getCurrentTask(),
    staleTime: 10000
  })
}

export function useTaskProgress(taskId: number) {
  return useQuery({
    queryKey: helperKeys.progress(taskId),
    queryFn: () => helperService.getProgress(taskId),
    enabled: !!taskId
  })
}

export function useHelperProfile() {
  return useQuery({
    queryKey: helperKeys.profile(),
    queryFn: () => helperService.getProfile(),
    staleTime: 60000
  })
}

export function useRatingSummary() {
  return useQuery({
    queryKey: helperKeys.ratingSummary(),
    queryFn: () => helperService.getRatingSummary(),
    staleTime: 60000
  })
}

export function useCategories() {
  return useQuery({
    queryKey: helperKeys.categories(),
    queryFn: () => helperService.getCategories(),
    staleTime: 300000
  })
}

export function useAcceptTask() {
  const queryClient = useQueryClient()
  
  return useMutation({
    mutationFn: (taskId: number) => helperService.acceptTask(taskId),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: helperKeys.dashboard() })
      queryClient.invalidateQueries({ queryKey: helperKeys.availableTasks() })
      queryClient.invalidateQueries({ queryKey: helperKeys.currentTask() })
      queryClient.invalidateQueries({ queryKey: helperKeys.myTasks() })
    }
  })
}

export function useStartTask() {
  const queryClient = useQueryClient()
  
  return useMutation({
    mutationFn: (taskId: number) => helperService.startTask(taskId),
    onSuccess: (_, taskId) => {
      queryClient.invalidateQueries({ queryKey: helperKeys.taskDetail(taskId) })
      queryClient.invalidateQueries({ queryKey: helperKeys.currentTask() })
      queryClient.invalidateQueries({ queryKey: helperKeys.myTasks() })
    }
  })
}

export function useSubmitTask() {
  const queryClient = useQueryClient()
  
  return useMutation({
    mutationFn: (taskId: number) => helperService.submitTask(taskId),
    onSuccess: (_, taskId) => {
      queryClient.invalidateQueries({ queryKey: helperKeys.taskDetail(taskId) })
      queryClient.invalidateQueries({ queryKey: helperKeys.currentTask() })
      queryClient.invalidateQueries({ queryKey: helperKeys.myTasks() })
      queryClient.invalidateQueries({ queryKey: helperKeys.dashboard() })
    }
  })
}

export function useCreateProgress() {
  const queryClient = useQueryClient()
  
  return useMutation({
    mutationFn: ({ taskId, data }: { taskId: number; data: CreateProgressRequest }) => 
      helperService.createProgress(taskId, data),
    onSuccess: (_, { taskId }) => {
      queryClient.invalidateQueries({ queryKey: helperKeys.progress(taskId) })
    }
  })
}

export function useUpdateProfile() {
  const queryClient = useQueryClient()
  
  return useMutation({
    mutationFn: (data: UpdateProfileRequest) => helperService.updateProfile(data),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: helperKeys.profile() })
    }
  })
}
