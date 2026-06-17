import { useNavigate } from 'react-router-dom'
import { Bell } from 'lucide-react'
import { useMarkAsRead } from '../hooks/useNotification'
import { useAuthStore } from '@/features/auth/stores/auth.store'
import { formatDateTime } from '@/lib/utils'
import { cn } from '@/lib/utils'
import type { Notification } from '../types/notification.types'

interface NotificationItemProps {
  notification: Notification
}

export function NotificationItem({ notification }: NotificationItemProps) {
  const navigate = useNavigate()
  const markAsRead = useMarkAsRead()
  const { user } = useAuthStore()
  
  const handleItemClick = async () => {
    if (notification.is_read === 0) {
      await markAsRead.mutateAsync(notification.id)
    }
    
    // Navigation routing based on parsed JSON payload
    try {
      if (notification.data) {
        const parsedData = typeof notification.data === 'string' 
          ? JSON.parse(notification.data) 
          : notification.data
        const taskId = parsedData.task_id
        
        if (taskId) {
          const role = user?.role || 'user'
          navigate(`/${role}/tasks/${taskId}`)
        }
      }
    } catch (e) {
      // Ignore JSON parsing errors
    }
  }

  return (
    <div 
      onClick={handleItemClick}
      className={cn(
        "flex gap-3 p-4 border-b border-gray-100 last:border-0 cursor-pointer transition-colors focus:outline-none focus:bg-gray-50",
        notification.is_read === 0 ? "bg-primary-light/10 hover:bg-primary-light/20" : "bg-white hover:bg-gray-50"
      )}
      role="button"
      tabIndex={0}
      aria-label={`Notification: ${notification.title}. ${notification.message}`}
      onKeyDown={(e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault()
          handleItemClick()
        }
      }}
    >
      <div className={cn(
        "w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0",
        notification.is_read === 0 ? "bg-primary text-white" : "bg-gray-100 text-gray-500"
      )}>
        <Bell size={16} />
      </div>
      
      <div className="flex-1 min-w-0">
        <div className="flex items-start justify-between gap-2">
          <h4 className={cn(
            "text-sm font-semibold truncate",
            notification.is_read === 0 ? "text-gray-900" : "text-gray-600"
          )}>
            {notification.title}
          </h4>
          <span className="text-[10px] text-gray-400 flex-shrink-0 mt-0.5">
            {formatDateTime(notification.created_at)}
          </span>
        </div>
        
        <p className={cn(
          "text-xs mt-1 line-clamp-2",
          notification.is_read === 0 ? "text-gray-700" : "text-gray-500"
        )}>
          {notification.message}
        </p>
      </div>
      
      {notification.is_read === 0 && (
        <div className="flex items-center flex-shrink-0" aria-label="Unread">
          <div className="w-2.5 h-2.5 bg-primary rounded-full" />
        </div>
      )}
    </div>
  )
}
