import { PageHeader } from '@/components/layout/PageHeader'
import { NotificationList } from '../components/NotificationList'
import { useMarkAllAsRead, useUnreadCount } from '../hooks/useNotification'
import { Button } from '@/components/ui/Button'
import { CheckCheck } from 'lucide-react'

export function NotificationPage() {
  const { data: count = 0 } = useUnreadCount()
  const markAllAsRead = useMarkAllAsRead()
  
  const handleMarkAllRead = async () => {
    await markAllAsRead.mutateAsync()
  }

  return (
    <div className="space-y-6">
      <PageHeader
        title="Notifications"
        subtitle="Stay updated on your task activities"
        actions={
          count > 0 && (
            <Button
              variant="secondary"
              size="sm"
              onClick={handleMarkAllRead}
              loading={markAllAsRead.isPending}
              disabled={markAllAsRead.isPending}
              aria-label="Mark all as read"
            >
              <CheckCheck size={16} className="mr-1" />
              Mark all read
            </Button>
          )
        }
      />
      
      {/* Mobile action bar */}
      {count > 0 && (
        <div className="lg:hidden">
          <Button
            variant="secondary"
            className="w-full min-h-[44px]"
            onClick={handleMarkAllRead}
            loading={markAllAsRead.isPending}
            disabled={markAllAsRead.isPending}
            aria-label="Mark all as read"
          >
            <CheckCheck size={16} className="mr-1" />
            Mark all as read
          </Button>
        </div>
      )}

      <NotificationList />
    </div>
  )
}
