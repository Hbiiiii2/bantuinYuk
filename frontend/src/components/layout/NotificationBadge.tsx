import { Bell } from 'lucide-react'
import { useNavigate } from 'react-router-dom'
import { cn } from '@/lib/utils'

interface NotificationBadgeProps {
  count?: number
  className?: string
}

export function NotificationBadge({ count = 0, className }: NotificationBadgeProps) {
  const navigate = useNavigate()
  
  const getNotificationPath = () => {
    // Default to user notifications, will be updated based on role
    return '/user/notifications'
  }
  
  const formatCount = (num: number): string => {
    if (num === 0) return ''
    if (num > 99) return '99+'
    return num.toString()
  }
  
  return (
    <button
      onClick={() => navigate(getNotificationPath())}
      className={cn(
        "relative p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors",
        className
      )}
      aria-label={`Notifications${count > 0 ? ` (${count} unread)` : ''}`}
    >
      <Bell size={20} />
      {count > 0 && (
        <span className="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 flex items-center justify-center text-[10px] font-bold text-white bg-danger rounded-full">
          {formatCount(count)}
        </span>
      )}
    </button>
  )
}
