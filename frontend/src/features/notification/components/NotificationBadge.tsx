import { useUnreadCount } from '../hooks/useNotification'

interface NotificationBadgeProps {
  className?: string
}

export function NotificationBadge({ className }: NotificationBadgeProps) {
  const { data: count = 0 } = useUnreadCount()
  
  if (count === 0) return null
  
  return (
    <span className={className}>
      {count > 99 ? '99+' : count}
    </span>
  )
}
