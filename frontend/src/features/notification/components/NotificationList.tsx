import { useState } from 'react'
import { NotificationItem } from './NotificationItem'
import { useNotifications } from '../hooks/useNotification'
import { Button } from '@/components/ui/Button'
import { Card, CardContent } from '@/components/ui/Card'
import { SkeletonList } from '@/components/shared/SkeletonCard'
import { EmptyState } from '@/components/shared/EmptyState'
import { ErrorState } from '@/components/shared/ErrorState'
import { Bell } from 'lucide-react'
import { cn } from '@/lib/utils'

export function NotificationList() {
  const [activeTab, setActiveTab] = useState<'all' | 'unread' | 'read'>('all')
  const [page, setPage] = useState(1)

  const { data, isLoading, error, refetch } = useNotifications({
    page,
    per_page: 20,
    unread: activeTab === 'unread' ? 'true' : undefined
  })

  const handleTabChange = (tab: 'all' | 'unread' | 'read') => {
    setActiveTab(tab)
    setPage(1)
  }

  if (error) {
    return <ErrorState message="Failed to load notifications" onRetry={refetch} />
  }

  const rawNotifications = data?.data || []
  const displayedNotifications = activeTab === 'read' 
    ? rawNotifications.filter(n => n.is_read === 1)
    : rawNotifications

  return (
    <div className="space-y-4">
      {/* Tabs */}
      <div className="flex border-b border-gray-200" aria-label="Notification Filters">
        {(['all', 'unread', 'read'] as const).map((tab) => {
          const isActive = activeTab === tab
          return (
            <button
              key={tab}
              onClick={() => handleTabChange(tab)}
              className={cn(
                "flex-1 py-3 text-center text-sm font-semibold border-b-2 capitalize transition-colors min-h-[44px]",
                isActive
                  ? "border-primary text-primary"
                  : "border-transparent text-gray-500 hover:text-gray-700"
              )}
              aria-current={isActive ? 'page' : undefined}
            >
              {tab}
            </button>
          )
        })}
      </div>

      {/* List */}
      {isLoading ? (
        <SkeletonList count={5} />
      ) : displayedNotifications.length > 0 ? (
        <div className="space-y-4">
          <Card>
            <CardContent className="divide-y divide-gray-100 p-0">
              {displayedNotifications.map((notif) => (
                <NotificationItem key={notif.id} notification={notif} />
              ))}
            </CardContent>
          </Card>

          {/* Pagination */}
          {activeTab !== 'read' && data && data.total > 20 && (
            <div className="flex items-center justify-center gap-3 py-2">
              <Button
                variant="secondary"
                size="sm"
                disabled={page === 1}
                onClick={() => setPage(page - 1)}
                aria-label="Previous Page"
                className="min-h-[44px]"
              >
                Previous
              </Button>
              <span className="text-xs text-gray-500 font-medium">
                Page {page} of {Math.ceil(data.total / 20)}
              </span>
              <Button
                variant="secondary"
                size="sm"
                disabled={page >= Math.ceil(data.total / 20)}
                onClick={() => setPage(page + 1)}
                aria-label="Next Page"
                className="min-h-[44px]"
              >
                Next
              </Button>
            </div>
          )}
        </div>
      ) : (
        <EmptyState
          icon={<Bell size={48} />}
          title={`No ${activeTab} notifications`}
          description={
            activeTab === 'unread' 
              ? "You've read all your notifications!" 
              : activeTab === 'read'
              ? "You don't have any read notifications yet."
              : "No notifications to show."
          }
        />
      )}
    </div>
  )
}
