import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { notificationService } from '../services/notification.service'

export const notificationKeys = {
  all: ['notifications'] as const,
  lists: (params?: any) => [...notificationKeys.all, 'list', params] as const,
  unreadCount: () => [...notificationKeys.all, 'unreadCount'] as const
}

export function useNotifications(params?: { page?: number; per_page?: number; unread?: string }) {
  return useQuery({
    queryKey: notificationKeys.lists(params),
    queryFn: () => notificationService.getNotifications(params),
    staleTime: 10000
  })
}

export function useUnreadCount() {
  return useQuery({
    queryKey: notificationKeys.unreadCount(),
    queryFn: () => notificationService.getUnreadCount(),
    refetchInterval: 15000, // auto refetch every 15s for responsive updates
    staleTime: 5000
  })
}

export function useMarkAsRead() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: (id: number) => notificationService.markAsRead(id),
    onMutate: async (_id) => {
      // Cancel outstanding queries
      await queryClient.cancelQueries({ queryKey: notificationKeys.all })

      // Snapshot previous unread count
      const previousCount = queryClient.getQueryData<number>(notificationKeys.unreadCount())

      // Optimistically decrement count
      if (previousCount !== undefined) {
        queryClient.setQueryData(notificationKeys.unreadCount(), Math.max(0, previousCount - 1))
      }

      return { previousCount }
    },
    onError: (_err, _id, context) => {
      if (context?.previousCount !== undefined) {
        queryClient.setQueryData(notificationKeys.unreadCount(), context.previousCount)
      }
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: notificationKeys.all })
    }
  })
}

export function useMarkAllAsRead() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: () => notificationService.markAllAsRead(),
    onMutate: async () => {
      await queryClient.cancelQueries({ queryKey: notificationKeys.all })
      const previousCount = queryClient.getQueryData<number>(notificationKeys.unreadCount())
      queryClient.setQueryData(notificationKeys.unreadCount(), 0)
      return { previousCount }
    },
    onError: (_err, _variables, context) => {
      if (context?.previousCount !== undefined) {
        queryClient.setQueryData(notificationKeys.unreadCount(), context.previousCount)
      }
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: notificationKeys.all })
    }
  })
}
