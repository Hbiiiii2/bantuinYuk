import { useNavigate } from 'react-router-dom'
import { useState } from 'react'
import { 
  Calendar, MapPin, DollarSign, Clock, Send, 
  CheckCircle, AlertCircle
} from 'lucide-react'
import { useCurrentTask, useStartTask, useSubmitTask, useTaskProgress } from '../hooks'
import { TaskStatusBadge } from '@/features/tasks/components/TaskStatusBadge'
import { ProgressForm } from '../components/ProgressForm'
import { PageHeader } from '@/components/layout/PageHeader'
import { Card, CardContent, CardHeader } from '@/components/ui/Card'
import { Button } from '@/components/ui/Button'
import { SkeletonCard } from '@/components/shared/SkeletonCard'
import { ErrorState } from '@/components/shared/ErrorState'
import { EmptyState } from '@/components/shared/EmptyState'
import { formatCurrency, formatDate, formatDateTime } from '@/lib/utils'

export function CurrentTaskPage() {
  const navigate = useNavigate()
  const { data: task, isLoading: taskLoading, error: taskError, refetch: refetchTask } = useCurrentTask()
  const { data: progress, refetch: refetchProgress } = useTaskProgress(task?.id || 0)
  
  const startMutation = useStartTask()
  const submitMutation = useSubmitTask()
  const [showSubmitConfirm, setShowSubmitConfirm] = useState(false)
  
  const handleStart = async () => {
    if (!task) return
    await startMutation.mutateAsync(task.id)
    refetchTask()
  }
  
  const handleSubmit = async () => {
    if (!task) return
    await submitMutation.mutateAsync(task.id)
    setShowSubmitConfirm(false)
    navigate('/helper/dashboard')
  }
  
  if (taskLoading) {
    return (
      <div>
        <PageHeader title="Current Task" />
        <SkeletonCard />
      </div>
    )
  }
  
  if (taskError) {
    return <ErrorState message="Failed to load task" onRetry={refetchTask} />
  }
  
  if (!task) {
    return (
      <div>
        <PageHeader title="Current Task" />
        <EmptyState
          icon={<AlertCircle size={48} />}
          title="No current task"
          description="You don't have any active tasks. Find tasks to get started!"
          actionLabel="Find Tasks"
          onAction={() => navigate('/helper/tasks')}
        />
      </div>
    )
  }
  
  const canStart = task.status === 'accepted'
  const canSubmit = task.status === 'in_progress'
  const canAddProgress = task.status === 'in_progress'
  
  return (
    <div>
      <PageHeader 
        title="Current Task" 
        showBack
        actions={
          canSubmit && (
            <Button onClick={() => setShowSubmitConfirm(true)}>
              <Send size={16} className="mr-2" />
              Submit Work
            </Button>
          )
        }
      />
      
      {/* Submit Confirmation Dialog */}
      {showSubmitConfirm && (
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
          <div className="bg-white rounded-xl p-6 max-w-sm w-full">
            <div className="w-12 h-12 rounded-full bg-success-light flex items-center justify-center mx-auto mb-4">
              <CheckCircle size={24} className="text-success" />
            </div>
            <h3 className="text-lg font-semibold text-gray-900 mb-2 text-center">Submit Work?</h3>
            <p className="text-gray-600 mb-4 text-center">
              Once submitted, the task owner will review your work. Make sure you've completed all progress updates.
            </p>
            <div className="flex gap-2">
              <Button
                variant="secondary"
                className="flex-1"
                onClick={() => setShowSubmitConfirm(false)}
              >
                Cancel
              </Button>
              <Button
                className="flex-1"
                loading={submitMutation.isPending}
                onClick={handleSubmit}
              >
                Submit
              </Button>
            </div>
          </div>
        </div>
      )}
      
      <div className="space-y-4">
        {/* Task Summary */}
        <Card>
          <CardContent>
            <div className="flex items-start justify-between gap-2 mb-3">
              <h2 className="text-lg font-semibold text-gray-900">{task.title}</h2>
              <TaskStatusBadge status={task.status} />
            </div>
            
            <div className="grid grid-cols-2 gap-3 text-sm">
              <div className="flex items-center gap-2 text-gray-600">
                <DollarSign size={16} className="text-gray-400" />
                <span className="font-semibold text-primary">{formatCurrency(task.price)}</span>
              </div>
              
              <div className="flex items-center gap-2 text-gray-600">
                <Calendar size={16} className="text-gray-400" />
                <span>Due: {formatDate(task.deadline_end)}</span>
              </div>
              
              {task.location && (
                <div className="flex items-center gap-2 text-gray-600 col-span-2">
                  <MapPin size={16} className="text-gray-400" />
                  <span>{task.location}</span>
                </div>
              )}
            </div>
          </CardContent>
        </Card>
        
        {/* Start Button */}
        {canStart && (
          <Button 
            className="w-full"
            loading={startMutation.isPending}
            onClick={handleStart}
          >
            <Clock size={16} className="mr-2" />
            Start Working
          </Button>
        )}
        
        {/* Progress Section */}
        {task.status === 'in_progress' || task.status === 'waiting_approval' || task.status === 'completed' ? (
          <>
            {/* Progress Timeline */}
            <Card>
              <CardHeader>
                <h3 className="font-medium text-gray-900">Progress Updates</h3>
              </CardHeader>
              <CardContent>
                {progress && progress.length > 0 ? (
                  <div className="space-y-4">
                    {progress.map((item: any) => (
                      <div key={item.id} className="border-l-2 border-primary pl-4 pb-4">
                        <div className="flex items-center gap-2 mb-1">
                          <span className="text-sm font-medium text-gray-900">
                            {item.helper_name}
                          </span>
                          <span className="text-xs text-gray-500">
                            {formatDateTime(item.created_at)}
                          </span>
                        </div>
                        <p className="text-sm text-gray-600">{item.description}</p>
                      </div>
                    ))}
                  </div>
                ) : (
                  <p className="text-gray-500 text-center py-4">No progress updates yet</p>
                )}
              </CardContent>
            </Card>
            
            {/* Add Progress Form */}
            {canAddProgress && (
              <ProgressForm 
                taskId={task.id} 
                onSuccess={() => refetchProgress()}
              />
            )}
          </>
        ) : null}
      </div>
    </div>
  )
}
