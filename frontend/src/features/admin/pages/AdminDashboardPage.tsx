import { useNavigate } from 'react-router-dom'
import {
  Users as UsersIcon,
  UserCheck as HelpersIcon,
  ClipboardList as TasksIcon,
  AlertTriangle as DisputesIcon,
  CreditCard as TransactionsIcon,
  CheckCircle,
  Clock,
  ArrowRight,
  Inbox
} from 'lucide-react'
import { PageHeader } from '@/components/layout/PageHeader'
import { Button } from '@/components/ui/Button'
import {
  useAdminDashboard,
  useAdminTasks,
  useAdminTransactions,
  useAdminDisputes,
  useAdminHelpers
} from '../hooks/useAdmin'
import { StatsCard, StatusBadge } from '../components'

export function AdminDashboardPage() {
  const navigate = useNavigate()

  // Queries
  const { data: dashboard, isLoading: isDashboardLoading } = useAdminDashboard()
  const { data: tasksData, isLoading: isTasksLoading } = useAdminTasks({ per_page: 5 })
  const { data: transactionsData, isLoading: isTxnsLoading } = useAdminTransactions({ per_page: 5 })
  const { data: disputesData, isLoading: isDisputesLoading } = useAdminDisputes({ per_page: 5 })
  const { data: helpersData, isLoading: isHelpersLoading } = useAdminHelpers({ per_page: 5, verification_status: 'pending' })

  const formatNumber = (num: number | undefined) => {
    if (num === undefined) return '-'
    return new Intl.NumberFormat().format(num)
  }

  const formatCurrency = (amount: number | undefined) => {
    if (amount === undefined) return '-'
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0
    }).format(amount)
  }

  const stats = [
    {
      title: 'Total Users',
      value: dashboard?.users,
      icon: UsersIcon,
      trend: { value: '+4%', isPositive: true },
      description: 'registered accounts'
    },
    {
      title: 'Total Helpers',
      value: dashboard?.helpers,
      icon: HelpersIcon,
      trend: { value: '+8%', isPositive: true },
      description: 'onboarded helpers'
    },
    {
      title: 'Total Tasks',
      value: dashboard?.tasks,
      icon: TasksIcon,
      trend: { value: '+12%', isPositive: true },
      description: 'platform requests'
    },
    {
      title: 'Completed Tasks',
      value: dashboard?.completed_tasks,
      icon: CheckCircle,
      trend: { value: '92% rate', isPositive: true },
      description: 'successfully resolved'
    },
    {
      title: 'Active Tasks',
      value: dashboard?.open_tasks,
      icon: Clock,
      trend: { value: 'In Progress', isPositive: true },
      description: 'currently ongoing'
    },
    {
      title: 'Open Disputes',
      value: dashboard?.disputes,
      icon: DisputesIcon,
      trend: { value: 'Requires Action', isPositive: false },
      description: 'active user disputes'
    },
    {
      title: 'Pending Verifications',
      value: helpersData?.total,
      icon: HelpersIcon,
      trend: { value: 'ID review', isPositive: false },
      description: 'helpers awaiting check'
    },
    {
      title: 'Total Transactions',
      value: dashboard?.wallet_transactions,
      icon: TransactionsIcon,
      trend: { value: 'Wallet flows', isPositive: true },
      description: 'processed transfers'
    }
  ]

  return (
    <div className="space-y-6">
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <PageHeader
          title="Admin Dashboard"
          subtitle="Real-time system overview and actions"
        />
        
        {/* Quick Actions Panel */}
        <div className="flex flex-wrap gap-2">
          <Button
            variant="secondary"
            onClick={() => navigate('/admin/disputes')}
            className="min-h-[44px] text-xs font-semibold"
          >
            Review Disputes
          </Button>
          <Button
            variant="secondary"
            onClick={() => navigate('/admin/helpers?verification_status=pending')}
            className="min-h-[44px] text-xs font-semibold"
          >
            Verify Helpers
          </Button>
          <Button
            variant="secondary"
            onClick={() => navigate('/admin/transactions')}
            className="min-h-[44px] text-xs font-semibold"
          >
            View Transactions
          </Button>
        </div>
      </div>

      {/* Stats Grid */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {stats.map((s, idx) => (
          <StatsCard
            key={idx}
            title={s.title}
            value={formatNumber(s.value)}
            icon={s.icon}
            trend={s.trend}
            description={s.description}
            isLoading={isDashboardLoading}
          />
        ))}
      </div>

      {/* Main Content Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {/* Verification Queue Card */}
        <div className="bg-white rounded-xl border border-gray-200 p-6 flex flex-col shadow-sm">
          <div className="flex items-center justify-between mb-4 border-b border-gray-100 pb-3">
            <h3 className="font-semibold text-gray-900 text-sm flex items-center gap-2">
              <HelpersIcon size={18} className="text-gray-500" />
              Pending Helper Verification
            </h3>
            <button
              onClick={() => navigate('/admin/helpers?verification_status=pending')}
              className="text-primary hover:text-primary-hover text-xs font-semibold flex items-center gap-1 min-h-[36px]"
            >
              View All <ArrowRight size={14} />
            </button>
          </div>
          <div className="flex-1 space-y-3">
            {isHelpersLoading ? (
              Array.from({ length: 3 }).map((_, idx) => (
                <div key={idx} className="h-14 bg-gray-100 rounded-lg animate-pulse" />
              ))
            ) : !helpersData?.data || helpersData.data.length === 0 ? (
              <div className="text-center py-6 text-gray-400 flex flex-col items-center">
                <Inbox size={28} className="mb-2 text-gray-300" />
                <span className="text-xs">No pending helper verification requests.</span>
              </div>
            ) : (
              helpersData.data.map((helper) => (
                <div
                  key={helper.id}
                  onClick={() => navigate('/admin/helpers')}
                  className="flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 border border-gray-200/50 rounded-xl cursor-pointer transition-colors"
                >
                  <div>
                    <h4 className="text-sm font-semibold text-gray-900">{helper.name}</h4>
                    <p className="text-xs text-gray-500 mt-0.5">Joined {new Date(helper.created_at).toLocaleDateString()}</p>
                  </div>
                  <StatusBadge status={helper.verification_status} />
                </div>
              ))
            )}
          </div>
        </div>

        {/* Recent Disputes Card */}
        <div className="bg-white rounded-xl border border-gray-200 p-6 flex flex-col shadow-sm">
          <div className="flex items-center justify-between mb-4 border-b border-gray-100 pb-3">
            <h3 className="font-semibold text-gray-900 text-sm flex items-center gap-2">
              <DisputesIcon size={18} className="text-gray-500" />
              Recent Disputes
            </h3>
            <button
              onClick={() => navigate('/admin/disputes')}
              className="text-primary hover:text-primary-hover text-xs font-semibold flex items-center gap-1 min-h-[36px]"
            >
              View All <ArrowRight size={14} />
            </button>
          </div>
          <div className="flex-1 space-y-3">
            {isDisputesLoading ? (
              Array.from({ length: 3 }).map((_, idx) => (
                <div key={idx} className="h-14 bg-gray-100 rounded-lg animate-pulse" />
              ))
            ) : !disputesData?.data || disputesData.data.length === 0 ? (
              <div className="text-center py-6 text-gray-400 flex flex-col items-center">
                <Inbox size={28} className="mb-2 text-gray-300" />
                <span className="text-xs">No active dispute cases.</span>
              </div>
            ) : (
              disputesData.data.map((dispute) => (
                <div
                  key={dispute.id}
                  onClick={() => navigate('/admin/disputes')}
                  className="flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 border border-gray-200/50 rounded-xl cursor-pointer transition-colors"
                >
                  <div className="flex-1 min-w-0 pr-4">
                    <h4 className="text-sm font-semibold text-gray-900 truncate">
                      {dispute.task_title || `Dispute #${dispute.id}`}
                    </h4>
                    <p className="text-xs text-gray-500 mt-0.5 truncate">Reason: {dispute.reason}</p>
                  </div>
                  <StatusBadge status={dispute.status} />
                </div>
              ))
            )}
          </div>
        </div>

        {/* Recent Tasks Card */}
        <div className="bg-white rounded-xl border border-gray-200 p-6 flex flex-col shadow-sm">
          <div className="flex items-center justify-between mb-4 border-b border-gray-100 pb-3">
            <h3 className="font-semibold text-gray-900 text-sm flex items-center gap-2">
              <TasksIcon size={18} className="text-gray-500" />
              Recent Tasks
            </h3>
            <button
              onClick={() => navigate('/admin/tasks')}
              className="text-primary hover:text-primary-hover text-xs font-semibold flex items-center gap-1 min-h-[36px]"
            >
              View All <ArrowRight size={14} />
            </button>
          </div>
          <div className="flex-1 space-y-3">
            {isTasksLoading ? (
              Array.from({ length: 3 }).map((_, idx) => (
                <div key={idx} className="h-14 bg-gray-100 rounded-lg animate-pulse" />
              ))
            ) : !tasksData?.data || tasksData.data.length === 0 ? (
              <div className="text-center py-6 text-gray-400 flex flex-col items-center">
                <Inbox size={28} className="mb-2 text-gray-300" />
                <span className="text-xs">No task requests posted yet.</span>
              </div>
            ) : (
              tasksData.data.map((task) => (
                <div
                  key={task.id}
                  onClick={() => navigate('/admin/tasks')}
                  className="flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 border border-gray-200/50 rounded-xl cursor-pointer transition-colors"
                >
                  <div className="flex-1 min-w-0 pr-4">
                    <h4 className="text-sm font-semibold text-gray-900 truncate">{task.title}</h4>
                    <p className="text-xs text-gray-500 mt-0.5">Budget: {formatCurrency(task.price)}</p>
                  </div>
                  <StatusBadge status={task.status} />
                </div>
              ))
            )}
          </div>
        </div>

        {/* Recent Transactions Card */}
        <div className="bg-white rounded-xl border border-gray-200 p-6 flex flex-col shadow-sm">
          <div className="flex items-center justify-between mb-4 border-b border-gray-100 pb-3">
            <h3 className="font-semibold text-gray-900 text-sm flex items-center gap-2">
              <TransactionsIcon size={18} className="text-gray-500" />
              Recent Transactions
            </h3>
            <button
              onClick={() => navigate('/admin/transactions')}
              className="text-primary hover:text-primary-hover text-xs font-semibold flex items-center gap-1 min-h-[36px]"
            >
              View All <ArrowRight size={14} />
            </button>
          </div>
          <div className="flex-1 space-y-3">
            {isTxnsLoading ? (
              Array.from({ length: 3 }).map((_, idx) => (
                <div key={idx} className="h-14 bg-gray-100 rounded-lg animate-pulse" />
              ))
            ) : !transactionsData?.data || transactionsData.data.length === 0 ? (
              <div className="text-center py-6 text-gray-400 flex flex-col items-center">
                <Inbox size={28} className="mb-2 text-gray-300" />
                <span className="text-xs">No transaction records found.</span>
              </div>
            ) : (
              transactionsData.data.map((txn) => (
                <div
                  key={txn.id}
                  onClick={() => navigate('/admin/transactions')}
                  className="flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 border border-gray-200/50 rounded-xl cursor-pointer transition-colors"
                >
                  <div className="flex-1 min-w-0 pr-4">
                    <h4 className="text-sm font-semibold text-gray-900 truncate">{txn.description || `Txn #${txn.id}`}</h4>
                    <p className={`text-xs font-bold mt-0.5 ${txn.type === 'withdraw' ? 'text-rose-600' : 'text-emerald-600'}`}>
                      {txn.type === 'withdraw' ? '-' : '+'}{formatCurrency(txn.amount)}
                    </p>
                  </div>
                  <span className="text-xs font-medium text-gray-500">
                    {new Date(txn.created_at).toLocaleDateString()}
                  </span>
                </div>
              ))
            )}
          </div>
        </div>

      </div>
    </div>
  )
}
export default AdminDashboardPage
