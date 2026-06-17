import { useState } from 'react'
import { Search, Filter, X } from 'lucide-react'
import { useAvailableTasks } from '../hooks'
import { TaskCard } from '@/features/tasks/components/TaskCard'
import { PageHeader } from '@/components/layout/PageHeader'
import { Button } from '@/components/ui/Button'
import { SkeletonList } from '@/components/shared/SkeletonCard'
import { ErrorState } from '@/components/shared/ErrorState'
import { EmptyState } from '@/components/shared/EmptyState'
import { cn } from '@/lib/utils'

export function AvailableTasksPage() {
  const [search, setSearch] = useState('')
  const [categoryId, setCategoryId] = useState<number | undefined>()
  const [page, setPage] = useState(1)
  
  const { data, isLoading, error, refetch } = useAvailableTasks({
    search: search || undefined,
    category_id: categoryId,
    page,
    per_page: 10
  })
  
  const categories = [
    { id: 1, name: 'Bangunan' },
    { id: 2, name: 'Pembersihan' },
    { id: 3, name: 'Pindahan' },
    { id: 4, name: 'Perbaikan' },
    { id: 5, name: 'Lainnya' }
  ]
  
  if (error) {
    return <ErrorState message="Failed to load tasks" onRetry={refetch} />
  }
  
  return (
    <div>
      <PageHeader 
        title="Available Tasks"
        subtitle={`${data?.total || 0} tasks available`}
      />
      
      <div className="space-y-3 mb-4">
        {/* Search */}
        <div className="relative">
          <Search size={18} className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
          <input
            type="text"
            placeholder="Search tasks..."
            value={search}
            onChange={(e) => {
              setSearch(e.target.value)
              setPage(1)
            }}
            className="w-full h-11 pl-10 pr-4 rounded-lg border border-gray-200 bg-white text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
          />
          {search && (
            <button
              onClick={() => setSearch('')}
              className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
            >
              <X size={16} />
            </button>
          )}
        </div>
        
        {/* Category Filter */}
        <div className="flex items-center gap-2 overflow-x-auto pb-2">
          <Filter size={16} className="text-gray-500 flex-shrink-0" />
          <button
            onClick={() => {
              setCategoryId(undefined)
              setPage(1)
            }}
            className={cn(
              "px-3 py-1.5 rounded-full text-sm font-medium transition-colors flex-shrink-0",
              !categoryId
                ? "bg-primary text-white"
                : "bg-gray-100 text-gray-600 hover:bg-gray-200"
            )}
          >
            All
          </button>
          {categories.map((cat) => (
            <button
              key={cat.id}
              onClick={() => {
                setCategoryId(cat.id)
                setPage(1)
              }}
              className={cn(
                "px-3 py-1.5 rounded-full text-sm font-medium transition-colors flex-shrink-0",
                categoryId === cat.id
                  ? "bg-primary text-white"
                  : "bg-gray-100 text-gray-600 hover:bg-gray-200"
              )}
            >
              {cat.name}
            </button>
          ))}
        </div>
      </div>
      
      {isLoading ? (
        <SkeletonList count={5} />
      ) : data?.data && data.data.length > 0 ? (
        <div className="space-y-3">
          {data.data.map((task) => (
            <TaskCard key={task.id} task={task} basePath="helper" />
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
          icon={<Search size={48} />}
          title="No tasks found"
          description={search || categoryId ? "Try adjusting your filters" : "No tasks available at the moment"}
        />
      )}
    </div>
  )
}
