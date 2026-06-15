import { useParams } from 'react-router-dom'
import { 
  Calendar, MapPin, Tag, DollarSign, User, Star, 
  FileText, Clock, MessageSquare
} from 'lucide-react'
import { useTask, useCompleteTask } from '../hooks'
import { TaskStatusBadge } from '../components/TaskStatusBadge'
import { TaskTimeline } from '../components/TaskTimeline'
import { TaskAttachmentList } from '../components/TaskAttachmentList'
import { TaskProgressList } from '../components/TaskProgressList'
import { PageHeader } from '@/components/layout/PageHeader'
import { Card, CardContent, CardHeader } from '@/components/ui/Card'
import { Button } from '@/components/ui/Button'
import { SkeletonCard } from '@/components/shared/SkeletonCard'
import { ErrorState } from '@/components/shared/ErrorState'
import { formatCurrency, formatDate, getInitials } from '@/lib/utils'

export function TaskDetailPage() {
  const { id } = useParams<{ id: string }>()
  const taskId = Number(id)
  
  const { data: task, isLoading, error, refetch } = useTask(taskId)
  const completeMutation = useCompleteTask()
  
  const handleComplete = async () => {
    if (!task) return
    await completeMutation.mutateAsync(task.id)
  }
  
  if (error) {
    return <ErrorState message="Failed to load task" onRetry={refetch} />
  }
  
  if (isLoading) {
    return (
      <div>
        <PageHeader title="Task Details" showBack />
        <SkeletonCard />
      </div>
    )
  }
  
  if (!task) {
    return <ErrorState message="Task not found" />
  }
  
  return (
    <div>
      <PageHeader 
        title="Task Details" 
        showBack
        actions={
          task.status === 'waiting_approval' && (
            <Button onClick={handleComplete} loading={completeMutation.isPending}>
              Approve Task
            </Button>
          )
        }
      />
      
      <div className="space-y-4">
        {/* Task Header */}
        <Card>
          <CardContent>
            <div className="flex items-start justify-between gap-2 mb-3">
              <h2 className="text-lg font-semibold text-gray-900">{task.title}</h2>
              <TaskStatusBadge status={task.status} />
            </div>
            
            <div className="grid grid-cols-2 gap-3 text-sm">
              <div className="flex items-center gap-2 text-gray-600">
                <Tag size={16} className="text-gray-400" />
                <span>{task.category_name}</span>
              </div>
              
              <div className="flex items-center gap-2 text-gray-600">
                <DollarSign size={16} className="text-gray-400" />
                <span className="font-semibold text-primary">{formatCurrency(task.price)}</span>
              </div>
              
              <div className="flex items-center gap-2 text-gray-600">
                <Calendar size={16} className="text-gray-400" />
                <span>{formatDate(task.deadline_start)} - {formatDate(task.deadline_end)}</span>
              </div>
              
              {task.location && (
                <div className="flex items-center gap-2 text-gray-600">
                  <MapPin size={16} className="text-gray-400" />
                  <span className="truncate">{task.location}</span>
                </div>
              )}
            </div>
          </CardContent>
        </Card>
        
        {/* Description */}
        <Card>
          <CardHeader>
            <h3 className="font-medium text-gray-900 flex items-center gap-2">
              <FileText size={16} />
              Description
            </h3>
          </CardHeader>
          <CardContent>
            <p className="text-gray-600 whitespace-pre-wrap">{task.description}</p>
          </CardContent>
        </Card>
        
        {/* Helper Information */}
        {task.helper_name && (
          <Card>
            <CardHeader>
              <h3 className="font-medium text-gray-900 flex items-center gap-2">
                <User size={16} />
                Helper
              </h3>
            </CardHeader>
            <CardContent>
              <div className="flex items-center gap-3">
                <div className="w-12 h-12 rounded-full bg-primary flex items-center justify-center text-white font-medium">
                  {getInitials(task.helper_name)}
                </div>
                <div>
                  <p className="font-medium text-gray-900">{task.helper_name}</p>
                  <div className="flex items-center gap-1 text-sm text-gray-500">
                    <Star size={14} className="text-warning fill-warning" />
                    <span>4.5</span>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>
        )}
        
        {/* Attachments */}
        <Card>
          <CardHeader>
            <h3 className="font-medium text-gray-900">Attachments</h3>
          </CardHeader>
          <CardContent>
            <TaskAttachmentList attachments={[]} />
          </CardContent>
        </Card>
        
        {/* Progress */}
        {task.status === 'in_progress' || task.status === 'waiting_approval' || task.status === 'completed' ? (
          <Card>
            <CardHeader>
              <h3 className="font-medium text-gray-900 flex items-center gap-2">
                <Clock size={16} />
                Progress Updates
              </h3>
            </CardHeader>
            <CardContent>
              <TaskProgressList progress={[]} />
            </CardContent>
          </Card>
        ) : null}
        
        {/* Timeline */}
        <Card>
          <CardHeader>
            <h3 className="font-medium text-gray-900">Status Timeline</h3>
          </CardHeader>
          <CardContent>
            <TaskTimeline history={task.status_history} />
          </CardContent>
        </Card>
        
        {/* Actions */}
        {task.status === 'completed' && (
          <Button
            variant="secondary"
            className="w-full"
            onClick={() => {/* TODO: Open review modal */}}
          >
            <MessageSquare size={16} className="mr-2" />
            Leave a Review
          </Button>
        )}
      </div>
    </div>
  )
}
