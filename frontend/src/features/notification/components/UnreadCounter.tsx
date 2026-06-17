import { useUnreadCount } from '../hooks/useNotification'

export function UnreadCounter() {
  const { data: count = 0 } = useUnreadCount()
  
  if (count === 0) return null
  
  return (
    <span 
      className="bg-primary text-white text-xs font-bold px-2 py-0.5 rounded-full" 
      aria-label={`${count} unread notifications`}
    >
      {count > 99 ? '99+' : count}
    </span>
  )
}
