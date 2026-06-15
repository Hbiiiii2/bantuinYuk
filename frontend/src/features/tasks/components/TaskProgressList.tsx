import { Clock, User } from 'lucide-react'
import { EmptyState } from '@/components/shared/EmptyState'
import { formatDateTime } from '@/lib/utils'
import { cn } from '@/lib/utils'
import type { TaskProgress } from '../task.types'

interface TaskProgressListProps {
  progress: TaskProgress[]
  className?: string
}

export function TaskProgressList({ progress, className }: TaskProgressListProps) {
  if (progress.length === 0) {
    return (
      <EmptyState
        icon={<Clock size={48} />}
        title="No progress updates"
        description="The helper hasn't posted any updates yet"
        className="py-8"
      />
    )
  }
  
  return (
    <div className={cn("space-y-4", className)}>
      {progress.map((item) => (
        <div
          key={item.id}
          className="border-l-2 border-primary pl-4 pb-4"
        >
          <div className="flex items-center gap-2 mb-1">
            <div className="w-6 h-6 rounded-full bg-primary-light flex items-center justify-center">
              <User size={12} className="text-primary" />
            </div>
            <span className="text-sm font-medium text-gray-900">
              {item.helper_name}
            </span>
            <span className="text-xs text-gray-500">
              {formatDateTime(item.created_at)}
            </span>
          </div>
          
          <p className="text-sm text-gray-600 ml-8">
            {item.description}
          </p>
          
          {item.attachments && item.attachments.length > 0 && (
            <div className="mt-2 ml-8 flex gap-2 flex-wrap">
              {item.attachments.map((att) => (
                <div
                  key={att.id}
                  className="px-2 py-1 bg-gray-100 rounded text-xs text-gray-600"
                >
                  📎 {att.file_name}
                </div>
              ))}
            </div>
          )}
        </div>
      ))}
    </div>
  )
}
