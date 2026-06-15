import { useNavigate } from 'react-router-dom'
import { Search, List, Wallet } from 'lucide-react'
import { useHelperDashboard } from '../hooks'
import { HelperStatsCard } from '../components/HelperStatsCard'
import { CurrentTaskCard } from '../components/CurrentTaskCard'
import { TaskCard } from '@/features/tasks/components/TaskCard'
import { PageHeader } from '@/components/layout/PageHeader'
import { Button } from '@/components/ui/Button'
import { SkeletonList } from '@/components/shared/SkeletonCard'
import { ErrorState } from '@/components/shared/ErrorState'
import { EmptyState } from '@/components/shared/EmptyState'

export function HelperDashboard() {
  const navigate = useNavigate()
  const { data, isLoading, error, refetch } = useHelperDashboard()
  
  if (error) {
    return <ErrorState message="Failed to load dashboard" onRetry={refetch} />
  }
  
  return (
    <div>
      <PageHeader 
        title="Dashboard"
        subtitle="Find and complete tasks"
      />
      
      {isLoading ? (
        <SkeletonList count={3} />
      ) : (
        <div className="space-y-6">
          {/* Stats */}
          {data?.stats && (
            <HelperStatsCard stats={data.stats} />
          )}
          
          {/* Quick Actions */}
          <div className="grid grid-cols-2 gap-3">
            <Button
              onClick={() => navigate('/helper/tasks')}
              className="h-auto py-4 flex-col gap-2"
            >
              <Search size={24} />
              <span>Find Tasks</span>
            </Button>
            
            <Button
              variant="secondary"
              onClick={() => navigate('/helper/wallet')}
              className="h-auto py-4 flex-col gap-2"
            >
              <Wallet size={24} />
              <span>Wallet</span>
            </Button>
          </div>
          
          {/* Current Task */}
          <div>
            <div className="flex items-center justify-between mb-3">
              <h2 className="font-semibold text-gray-900">Current Task</h2>
              <Button
                variant="ghost"
                size="sm"
                onClick={() => navigate('/helper/current-task')}
              >
                View All
              </Button>
            </div>
            
            {data?.current_task ? (
              <CurrentTaskCard task={data.current_task} />
            ) : (
              <div className="bg-white rounded-xl border border-gray-200 p-6 text-center">
                <p className="text-gray-500 mb-2">No active task</p>
                <Button 
                  size="sm"
                  onClick={() => navigate('/helper/tasks')}
                >
                  Find Tasks
                </Button>
              </div>
            )}
          </div>
          
          {/* Nearby Tasks */}
          <div>
            <div className="flex items-center justify-between mb-3">
              <h2 className="font-semibold text-gray-900">Available Tasks</h2>
              <Button
                variant="ghost"
                size="sm"
                onClick={() => navigate('/helper/tasks')}
              >
                View All
              </Button>
            </div>
            
            {data?.nearby_tasks && data.nearby_tasks.length > 0 ? (
              <div className="space-y-3">
                {data.nearby_tasks.slice(0, 3).map((task) => (
                  <TaskCard key={task.id} task={task} />
                ))}
              </div>
            ) : (
              <EmptyState
                icon={<List size={48} />}
                title="No tasks available"
                description="Check back later for new tasks"
              />
            )}
          </div>
        </div>
      )}
    </div>
  )
}
