import { useState } from 'react'
import { List } from 'lucide-react'
import { useTasks } from '../hooks'
import { TaskCard } from '../components/TaskCard'
import { PageHeader } from '@/components/layout/PageHeader'
import { SkeletonList } from '@/components/shared/SkeletonCard'
import { ErrorState } from '@/components/shared/ErrorState'
import { EmptyState } from '@/components/shared/EmptyState'
import { cn } from '@/lib/utils'
import type { TaskStatus } from '../task.types'

type TabType = 'all' | 'completed' | 'cancelled'

export function TaskHistoryPage() {
  const [activeTab, setActiveTab] = useState<TabType>('all')
  const [page, setPage] = useState(1)
  
  const statusFilter: Record<TabType, TaskStatus | undefined> = {
    all: undefined,
    completed: 'completed',
    cancelled: 'cancelled'
  }
  
  const { data, isLoading, error, refetch } = useTasks({
    status: statusFilter[activeTab],
    page,
    per_page: 10
  })
  
  if (error) {
    return <ErrorState message="Failed to load history" onRetry={refetch} />
  }
  
  const tabs: { value: TabType; label: string }[] = [
    { value: 'all', label: 'All' },
    { value: 'completed', label: 'Completed' },
    { value: 'cancelled', label: 'Cancelled' }
  ]
  
  return (
    <div>
      <PageHeader 
        title="Task History"
        subtitle={`${data?.total || 0} tasks`}
      />
      
      {/* Tabs */}
      <div className="flex gap-1 p-1 bg-gray-100 rounded-lg mb-4">
        {tabs.map((tab) => (
          <button
            key={tab.value}
            onClick={() => {
              setActiveTab(tab.value)
              setPage(1)
            }}
            className={cn(
              "flex-1 py-2 px-4 text-sm font-medium rounded-md transition-colors",
              activeTab === tab.value
                ? "bg-white text-gray-900 shadow-sm"
                : "text-gray-500 hover:text-gray-700"
            )}
          >
            {tab.label}
          </button>
        ))}
      </div>
      
      {isLoading ? (
        <SkeletonList count={5} />
      ) : data?.data && data.data.length > 0 ? (
        <div className="space-y-3">
          {data.data.map((task) => (
            <TaskCard key={task.id} task={task} />
          ))}
          
          {/* Pagination */}
          {data.total > 10 && (
            <div className="flex items-center justify-center gap-2 pt-4">
              <button
                disabled={page === 1}
                onClick={() => setPage(page - 1)}
                className="px-3 py-1.5 text-sm border border-gray-200 rounded-lg disabled:opacity-50"
              >
                Previous
              </button>
              <span className="text-sm text-gray-600">
                Page {page} of {Math.ceil(data.total / 10)}
              </span>
              <button
                disabled={page >= Math.ceil(data.total / 10)}
                onClick={() => setPage(page + 1)}
                className="px-3 py-1.5 text-sm border border-gray-200 rounded-lg disabled:opacity-50"
              >
                Next
              </button>
            </div>
          )}
        </div>
      ) : (
        <EmptyState
          icon={<List size={48} />}
          title="No tasks found"
          description={
            activeTab === 'all'
              ? "You haven't created any tasks yet"
              : `No ${activeTab} tasks`
          }
        />
      )}
    </div>
  )
}
