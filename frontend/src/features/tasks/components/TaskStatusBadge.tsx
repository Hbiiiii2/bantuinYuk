import { Badge } from '@/components/ui/Badge'
import type { TaskStatus } from '../task.types'

interface TaskStatusBadgeProps {
  status: TaskStatus
  className?: string
}

const statusConfig: Record<TaskStatus, { label: string; variant: 'default' | 'primary' | 'success' | 'warning' | 'danger' | 'info' }> = {
  draft: { label: 'Draft', variant: 'default' },
  open: { label: 'Open', variant: 'info' },
  accepted: { label: 'Accepted', variant: 'primary' },
  in_progress: { label: 'In Progress', variant: 'warning' },
  waiting_approval: { label: 'Waiting Approval', variant: 'warning' },
  completed: { label: 'Completed', variant: 'success' },
  cancelled: { label: 'Cancelled', variant: 'danger' }
}

export function TaskStatusBadge({ status, className }: TaskStatusBadgeProps) {
  const config = statusConfig[status] || statusConfig.open
  
  return (
    <Badge variant={config.variant} className={className}>
      {config.label}
    </Badge>
  )
}
