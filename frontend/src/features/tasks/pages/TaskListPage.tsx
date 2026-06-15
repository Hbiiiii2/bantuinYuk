import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { Plus, List } from 'lucide-react'
import { useTasks } from '../hooks'
import { TaskCard } from '../components/TaskCard'
import { TaskFilters } from '../components/TaskFilters'
import { PageHeader } from '@/components/layout/PageHeader'
import { Button } from '@/components/ui/Button'
import { SkeletonList } from '@/components/shared/SkeletonCard'
import { ErrorState } from '@/components/shared/ErrorState'
import { EmptyState } from '@/components/shared/EmptyState'
import type { TaskStatus } from '../task.types'

export function TaskListPage() {
  const navigate = useNavigate()
  const [search, setSearch] = useState('')
  const [status, setStatus] = useState<TaskStatus | ''>('')
  const [page, setPage] = useState(1)
  
  const { data, isLoading, error, refetch } = useTasks({
    search: search || undefined,
    status: (status as TaskStatus) || undefined,
    page,
    per_page: 10
  })
  
  if (error) {
    return <ErrorState message="Failed to load tasks" onRetry={refetch} />
  }
  
  return (
    <div>
      <PageHeader 
        title="My Tasks"
        subtitle={`${data?.total || 0} tasks`}
        actions={
          <Button onClick={() => navigate('/user/tasks/create')}>
            <Plus size={16} className="mr-2" />
            Create Task
          </Button>
        }
      />
      
      <TaskFilters
        search={search}
        status={status}
        onSearchChange={(value) => {
          setSearch(value)
          setPage(1)
        }}
        onStatusChange={(value) => {
          setStatus(value)
          setPage(1)
        }}
        className="mb-4"
      />
      
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
              <Button
                variant="secondary"
                size="sm"
                disabled={page === 1}
                onClick={() => setPage(page - 1)}
              >
                Previous
              </Button>
              <span className="text-sm text-gray-600">
                Page {page} of {Math.ceil(data.total / 10)}
              </span>
              <Button
                variant="secondary"
                size="sm"
                disabled={page >= Math.ceil(data.total / 10)}
                onClick={() => setPage(page + 1)}
              >
                Next
              </Button>
            </div>
          )}
        </div>
      ) : (
        <EmptyState
          icon={<List size={48} />}
          title="No tasks found"
          description={search || status ? "Try adjusting your filters" : "Create your first task to get started"}
          actionLabel={!search && !status ? "Create Task" : undefined}
          onAction={!search && !status ? () => navigate('/user/tasks/create') : undefined}
        />
      )}
    </div>
  )
}
