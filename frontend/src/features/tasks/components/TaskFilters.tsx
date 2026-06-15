import { Search, Filter, X } from 'lucide-react'
import { Button } from '@/components/ui/Button'
import { cn } from '@/lib/utils'
import type { TaskStatus } from '../task.types'

interface TaskFiltersProps {
  search: string
  status: TaskStatus | ''
  onSearchChange: (value: string) => void
  onStatusChange: (value: TaskStatus | '') => void
  className?: string
}

const statusOptions: { value: TaskStatus | ''; label: string }[] = [
  { value: '', label: 'All Status' },
  { value: 'open', label: 'Open' },
  { value: 'accepted', label: 'Accepted' },
  { value: 'in_progress', label: 'In Progress' },
  { value: 'waiting_approval', label: 'Waiting Approval' },
  { value: 'completed', label: 'Completed' },
  { value: 'cancelled', label: 'Cancelled' }
]

export function TaskFilters({ 
  search, 
  status, 
  onSearchChange, 
  onStatusChange,
  className 
}: TaskFiltersProps) {
  const hasFilters = search || status
  
  return (
    <div className={cn("space-y-3", className)}>
      <div className="relative">
        <Search size={18} className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
        <input
          type="text"
          placeholder="Search tasks..."
          value={search}
          onChange={(e) => onSearchChange(e.target.value)}
          className="w-full h-11 pl-10 pr-4 rounded-lg border border-gray-200 bg-white text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
        />
        {search && (
          <button
            onClick={() => onSearchChange('')}
            className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
          >
            <X size={16} />
          </button>
        )}
      </div>
      
      <div className="flex items-center gap-2 flex-wrap">
        <Filter size={16} className="text-gray-500" />
        {statusOptions.map((option) => (
          <button
            key={option.value}
            onClick={() => onStatusChange(option.value)}
            className={cn(
              "px-3 py-1.5 rounded-full text-sm font-medium transition-colors",
              status === option.value
                ? "bg-primary text-white"
                : "bg-gray-100 text-gray-600 hover:bg-gray-200"
            )}
          >
            {option.label}
          </button>
        ))}
      </div>
      
      {hasFilters && (
        <Button
          variant="ghost"
          size="sm"
          onClick={() => {
            onSearchChange('')
            onStatusChange('')
          }}
        >
          Clear filters
        </Button>
      )}
    </div>
  )
}
