import { useParams, useNavigate } from 'react-router-dom'
import { 
  Calendar, MapPin, Tag, DollarSign, User,
  FileText, Check
} from 'lucide-react'
import { useState } from 'react'
import { useHelperTask, useAcceptTask } from '../hooks'
import { TaskStatusBadge } from '@/features/tasks/components/TaskStatusBadge'
import { TaskTimeline } from '@/features/tasks/components/TaskTimeline'
import { TaskAttachmentList } from '@/features/tasks/components/TaskAttachmentList'
import { PageHeader } from '@/components/layout/PageHeader'
import { Card, CardContent, CardHeader } from '@/components/ui/Card'
import { Button } from '@/components/ui/Button'
import { SkeletonCard } from '@/components/shared/SkeletonCard'
import { ErrorState } from '@/components/shared/ErrorState'
import { formatCurrency, formatDate, getInitials } from '@/lib/utils'

export function HelperTaskDetail() {
  const { id } = useParams<{ id: string }>()
  const navigate = useNavigate()
  const taskId = Number(id)
  
  const { data: task, isLoading, error, refetch } = useHelperTask(taskId)
  const acceptMutation = useAcceptTask()
  const [showConfirm, setShowConfirm] = useState(false)
  
  const handleAccept = async () => {
    if (!task) return
    await acceptMutation.mutateAsync(task.id)
    setShowConfirm(false)
    navigate('/helper/current-task')
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
  
  const canAccept = task.status === 'open'
  
  return (
    <div>
      <PageHeader 
        title="Task Details" 
        showBack
        actions={
          canAccept && (
            <Button onClick={() => setShowConfirm(true)}>
              <Check size={16} className="mr-2" />
              Accept Task
            </Button>
          )
        }
      />
      
      {/* Confirmation Dialog */}
      {showConfirm && (
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
          <div className="bg-white rounded-xl p-6 max-w-sm w-full">
            <h3 className="text-lg font-semibold text-gray-900 mb-2">Accept Task?</h3>
            <p className="text-gray-600 mb-4">
              Are you sure you want to accept this task? You'll be responsible for completing it.
            </p>
            <div className="flex gap-2">
              <Button
                variant="secondary"
                className="flex-1"
                onClick={() => setShowConfirm(false)}
              >
                Cancel
              </Button>
              <Button
                className="flex-1"
                loading={acceptMutation.isPending}
                onClick={handleAccept}
              >
                Accept
              </Button>
            </div>
          </div>
        </div>
      )}
      
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
        
        {/* User Information */}
        <Card>
          <CardHeader>
            <h3 className="font-medium text-gray-900 flex items-center gap-2">
              <User size={16} />
              Posted by
            </h3>
          </CardHeader>
          <CardContent>
            <div className="flex items-center gap-3">
              <div className="w-12 h-12 rounded-full bg-primary flex items-center justify-center text-white font-medium">
                {getInitials(task.user_name)}
              </div>
              <div>
                <p className="font-medium text-gray-900">{task.user_name}</p>
              </div>
            </div>
          </CardContent>
        </Card>
        
        {/* Attachments */}
        <Card>
          <CardHeader>
            <h3 className="font-medium text-gray-900">Attachments</h3>
          </CardHeader>
          <CardContent>
            <TaskAttachmentList attachments={[]} />
          </CardContent>
        </Card>
        
        {/* Timeline */}
        <Card>
          <CardHeader>
            <h3 className="font-medium text-gray-900">Status Timeline</h3>
          </CardHeader>
          <CardContent>
            <TaskTimeline history={task.status_history} />
          </CardContent>
        </Card>
        
        {/* Accept Button (Mobile) */}
        {canAccept && (
          <div className="lg:hidden">
            <Button 
              className="w-full"
              onClick={() => setShowConfirm(true)}
            >
              <Check size={16} className="mr-2" />
              Accept Task
            </Button>
          </div>
        )}
      </div>
    </div>
  )
}
