import { useNavigate } from 'react-router-dom'
import { Clock, MapPin, DollarSign } from 'lucide-react'
import { Card, CardContent } from '@/components/ui/Card'
import { Button } from '@/components/ui/Button'
import { TaskStatusBadge } from '@/features/tasks/components/TaskStatusBadge'
import { formatCurrency, formatDate } from '@/lib/utils'
import type { Task } from '@/features/tasks/task.types'

interface CurrentTaskCardProps {
  task: Task
}

export function CurrentTaskCard({ task }: CurrentTaskCardProps) {
  const navigate = useNavigate()
  
  return (
    <Card className="border-primary">
      <CardContent>
        <div className="flex items-start justify-between gap-2 mb-2">
          <h3 className="font-medium text-gray-900 line-clamp-1">{task.title}</h3>
          <TaskStatusBadge status={task.status} />
        </div>
        
        <div className="space-y-1 text-sm text-gray-500 mb-3">
          <div className="flex items-center gap-2">
            <DollarSign size={14} />
            <span className="font-semibold text-primary">{formatCurrency(task.price)}</span>
          </div>
          <div className="flex items-center gap-2">
            <Clock size={14} />
            <span>Due: {formatDate(task.deadline_end)}</span>
          </div>
          {task.location && (
            <div className="flex items-center gap-2">
              <MapPin size={14} />
              <span className="truncate">{task.location}</span>
            </div>
          )}
        </div>
        
        <Button 
          className="w-full" 
          onClick={() => navigate(`/helper/tasks/${task.id}`)}
        >
          View Task
        </Button>
      </CardContent>
    </Card>
  )
}
