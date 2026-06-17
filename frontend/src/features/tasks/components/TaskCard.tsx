import { useNavigate } from 'react-router-dom'
import { Calendar, MapPin, Tag } from 'lucide-react'
import { Card } from '@/components/ui/Card'
import { TaskStatusBadge } from './TaskStatusBadge'
import { formatCurrency, formatDate } from '@/lib/utils'
import { cn } from '@/lib/utils'
import type { Task } from '../task.types'

interface TaskCardProps {
  task: Task
  basePath?: 'user' | 'helper'
  className?: string
}

export function TaskCard({ task, basePath = 'user', className }: TaskCardProps) {
  const navigate = useNavigate()
  
  return (
    <Card 
      className={cn(
        "cursor-pointer hover:shadow-md transition-shadow focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent",
        className
      )}
      onClick={() => navigate(`/${basePath}/tasks/${task.id}`)}
      tabIndex={0}
      role="button"
      aria-label={`Task: ${task.title}. Price: ${formatCurrency(task.price)}. Status: ${task.status}.`}
      onKeyDown={(e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault()
          navigate(`/${basePath}/tasks/${task.id}`)
        }
      }}
    >
      <div className="p-4">
        <div className="flex items-start justify-between gap-2 mb-2">
          <h3 className="font-medium text-gray-900 line-clamp-1">{task.title}</h3>
          <TaskStatusBadge status={task.status} />
        </div>
        
        <div className="flex items-center gap-2 text-sm text-gray-500 mb-2">
          <Tag size={14} />
          <span>{task.category_name}</span>
        </div>
        
        {task.location && (
          <div className="flex items-center gap-2 text-sm text-gray-500 mb-2">
            <MapPin size={14} />
            <span className="truncate">{task.location}</span>
          </div>
        )}
        
        <div className="flex items-center gap-2 text-sm text-gray-500 mb-3">
          <Calendar size={14} />
          <span>Due: {formatDate(task.deadline_end)}</span>
        </div>
        
        <div className="flex items-center justify-between pt-3 border-t border-gray-100">
          <span className="text-lg font-semibold text-primary">
            {formatCurrency(task.price)}
          </span>
          <span className="text-xs text-gray-400">
            {formatDate(task.created_at)}
          </span>
        </div>
      </div>
    </Card>
  )
}
