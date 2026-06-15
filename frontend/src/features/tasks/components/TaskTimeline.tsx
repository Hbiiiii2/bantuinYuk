import { Check, Clock, Circle } from 'lucide-react'
import { cn } from '@/lib/utils'
import { formatDateTime } from '@/lib/utils'
import type { StatusHistory } from '../task.types'

interface TaskTimelineProps {
  history: StatusHistory[]
  className?: string
}

const statusOrder = ['open', 'accepted', 'in_progress', 'waiting_approval', 'completed']

export function TaskTimeline({ history, className }: TaskTimelineProps) {
  const getStepStatus = (_step: string, index: number) => {
    const latestStatus = history[history.length - 1]?.status
    const currentIndex = statusOrder.indexOf(latestStatus || 'open')
    
    if (index < currentIndex) return 'completed'
    if (index === currentIndex) return 'current'
    return 'upcoming'
  }
  
  return (
    <div className={cn("space-y-0", className)}>
      {statusOrder.map((status, index) => {
        const stepStatus = getStepStatus(status, index)
        const historyEntry = history.find(h => h.status === status)
        
        return (
          <div key={status} className="flex gap-3">
            <div className="flex flex-col items-center">
              <div className={cn(
                "w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0",
                stepStatus === 'completed' && "bg-success text-white",
                stepStatus === 'current' && "bg-primary text-white",
                stepStatus === 'upcoming' && "bg-gray-200 text-gray-400"
              )}>
                {stepStatus === 'completed' ? (
                  <Check size={16} />
                ) : stepStatus === 'current' ? (
                  <Clock size={16} />
                ) : (
                  <Circle size={16} />
                )}
              </div>
              {index < statusOrder.length - 1 && (
                <div className={cn(
                  "w-0.5 h-8",
                  stepStatus === 'completed' ? "bg-success" : "bg-gray-200"
                )} />
              )}
            </div>
            
            <div className="pb-8">
              <p className={cn(
                "font-medium capitalize",
                stepStatus === 'completed' && "text-success",
                stepStatus === 'current' && "text-primary",
                stepStatus === 'upcoming' && "text-gray-400"
              )}>
                {status.replace('_', ' ')}
              </p>
              {historyEntry && (
                <p className="text-xs text-gray-500 mt-0.5">
                  {formatDateTime(historyEntry.created_at)}
                  {historyEntry.note && ` - ${historyEntry.note}`}
                </p>
              )}
            </div>
          </div>
        )
      })}
    </div>
  )
}
