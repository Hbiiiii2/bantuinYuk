import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { taskService } from '../services/task.service'
import type { TaskListParams, CreateTaskRequest } from '../task.types'

export const taskKeys = {
  all: ['tasks'] as const,
  lists: () => [...taskKeys.all, 'list'] as const,
  list: (params: TaskListParams) => [...taskKeys.lists(), params] as const,
  details: () => [...taskKeys.all, 'detail'] as const,
  detail: (id: number) => [...taskKeys.details(), id] as const,
  dashboard: () => [...taskKeys.all, 'dashboard'] as const,
  categories: () => ['categories'] as const
}

export function useTasks(params?: TaskListParams) {
  return useQuery({
    queryKey: taskKeys.list(params || {}),
    queryFn: () => taskService.getTasks(params),
    staleTime: 30000
  })
}

export function useTask(id: number) {
  return useQuery({
    queryKey: taskKeys.detail(id),
    queryFn: () => taskService.getTaskById(id),
    enabled: !!id
  })
}

export function useDashboard() {
  return useQuery({
    queryKey: taskKeys.dashboard(),
    queryFn: () => taskService.getDashboard(),
    staleTime: 60000
  })
}

export function useCategories() {
  return useQuery({
    queryKey: taskKeys.categories(),
    queryFn: () => taskService.getCategories(),
    staleTime: 300000
  })
}

export function useCreateTask() {
  const queryClient = useQueryClient()
  
  return useMutation({
    mutationFn: (data: CreateTaskRequest) => taskService.createTask(data),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: taskKeys.lists() })
      queryClient.invalidateQueries({ queryKey: taskKeys.dashboard() })
    }
  })
}

export function useCompleteTask() {
  const queryClient = useQueryClient()
  
  return useMutation({
    mutationFn: (taskId: number) => taskService.completeTask(taskId),
    onSuccess: (_, taskId) => {
      queryClient.invalidateQueries({ queryKey: taskKeys.detail(taskId) })
      queryClient.invalidateQueries({ queryKey: taskKeys.lists() })
      queryClient.invalidateQueries({ queryKey: taskKeys.dashboard() })
    }
  })
}

export function useCancelTask() {
  const queryClient = useQueryClient()
  
  return useMutation({
    mutationFn: (taskId: number) => taskService.cancelTask(taskId),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: taskKeys.lists() })
      queryClient.invalidateQueries({ queryKey: taskKeys.dashboard() })
    }
  })
}
