import { useNavigate } from 'react-router-dom'
import { Plus, History, Wallet, List, CheckCircle, XCircle, Clock } from 'lucide-react'
import { useDashboard } from '../hooks'
import { TaskCard } from '../components/TaskCard'
import { PageHeader } from '@/components/layout/PageHeader'
import { Card, CardContent } from '@/components/ui/Card'
import { Button } from '@/components/ui/Button'
import { SkeletonList } from '@/components/shared/SkeletonCard'
import { ErrorState } from '@/components/shared/ErrorState'
import { EmptyState } from '@/components/shared/EmptyState'
import { formatCurrency } from '@/lib/utils'

export function UserDashboard() {
  const navigate = useNavigate()
  const { data, isLoading, error, refetch } = useDashboard()
  
  if (error) {
    return <ErrorState message="Failed to load dashboard" onRetry={refetch} />
  }
  
  return (
    <div>
      <PageHeader 
        title="Dashboard"
        subtitle="Manage your tasks"
      />
      
      {isLoading ? (
        <SkeletonList count={3} />
      ) : (
        <div className="space-y-6">
          {/* Wallet Summary */}
          <Card>
            <CardContent>
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm text-gray-500">Your Balance</p>
                  <p className="text-2xl font-bold text-gray-900">
                    {formatCurrency(data?.wallet_summary?.balance || 0)}
                  </p>
                  <p className="text-xs text-gray-400 mt-1">
                    Available: {formatCurrency(data?.wallet_summary?.available_balance || 0)}
                  </p>
                </div>
                <Button
                  variant="secondary"
                  size="sm"
                  onClick={() => navigate('/user/wallet')}
                >
                  <Wallet size={16} className="mr-2" />
                  View Wallet
                </Button>
              </div>
            </CardContent>
          </Card>
          
          {/* Quick Actions */}
          <div className="grid grid-cols-2 gap-3">
            <Button
              onClick={() => navigate('/user/tasks/create')}
              className="h-auto py-4 flex-col gap-2"
            >
              <Plus size={24} />
              <span>Create Task</span>
            </Button>
            
            <Button
              variant="secondary"
              onClick={() => navigate('/user/history')}
              className="h-auto py-4 flex-col gap-2"
            >
              <History size={24} />
              <span>View History</span>
            </Button>
          </div>
          
          {/* Task Statistics */}
          <div className="grid grid-cols-4 gap-2">
            <div className="bg-white rounded-lg border border-gray-200 p-3 text-center">
              <List size={20} className="mx-auto mb-1 text-gray-500" />
              <p className="text-xl font-bold text-gray-900">{data?.stats?.total_tasks || 0}</p>
              <p className="text-xs text-gray-500">Total</p>
            </div>
            
            <div className="bg-white rounded-lg border border-gray-200 p-3 text-center">
              <Clock size={20} className="mx-auto mb-1 text-warning" />
              <p className="text-xl font-bold text-gray-900">{data?.stats?.active_tasks || 0}</p>
              <p className="text-xs text-gray-500">Active</p>
            </div>
            
            <div className="bg-white rounded-lg border border-gray-200 p-3 text-center">
              <CheckCircle size={20} className="mx-auto mb-1 text-success" />
              <p className="text-xl font-bold text-gray-900">{data?.stats?.completed_tasks || 0}</p>
              <p className="text-xs text-gray-500">Done</p>
            </div>
            
            <div className="bg-white rounded-lg border border-gray-200 p-3 text-center">
              <XCircle size={20} className="mx-auto mb-1 text-danger" />
              <p className="text-xl font-bold text-gray-900">{data?.stats?.cancelled_tasks || 0}</p>
              <p className="text-xs text-gray-500">Cancelled</p>
            </div>
          </div>
          
          {/* Recent Tasks */}
          <div>
            <div className="flex items-center justify-between mb-3">
              <h2 className="font-semibold text-gray-900">Recent Tasks</h2>
              <Button
                variant="ghost"
                size="sm"
                onClick={() => navigate('/user/tasks')}
              >
                View All
              </Button>
            </div>
            
            {data?.recent_tasks && data.recent_tasks.length > 0 ? (
              <div className="space-y-3">
                {data.recent_tasks.slice(0, 5).map((task) => (
                  <TaskCard key={task.id} task={task} />
                ))}
              </div>
            ) : (
              <EmptyState
                icon={<List size={48} />}
                title="No tasks yet"
                description="Create your first task to get started"
                actionLabel="Create Task"
                onAction={() => navigate('/user/tasks/create')}
              />
            )}
          </div>
        </div>
      )}
    </div>
  )
}
