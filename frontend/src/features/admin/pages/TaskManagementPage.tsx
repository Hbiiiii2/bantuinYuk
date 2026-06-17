import { useState, useMemo, useCallback } from 'react'
import { useAdminTasks, useAdminTaskDetail } from '../hooks/useAdmin'
import {
  AdminTable,
  Column,
  SearchBar,
  FilterBar,
  StatusBadge,
  DetailDialog
} from '../components'
import { PageHeader } from '@/components/layout/PageHeader'
import { Button } from '@/components/ui/Button'
import { Eye, ClipboardList, Calendar } from 'lucide-react'

export function TaskManagementPage() {
  // Filters, Pagination, Search states
  const [search, setSearch] = useState('')
  const [page, setPage] = useState(1)
  const [statusFilter, setStatusFilter] = useState('all') // 'all', 'open', 'in_progress', 'completed', etc.
  const [dateFilter, setDateFilter] = useState('') // YYYY-MM-DD or empty

  // Selected Detail Modal states
  const [selectedTaskId, setSelectedTaskId] = useState<number | null>(null)
  const [isDetailOpen, setIsDetailOpen] = useState(false)

  // Fetch task lists
  const tasksParams = useMemo(() => ({
    page,
    per_page: 10,
    search: search || undefined,
    status: statusFilter === 'all' ? undefined : statusFilter
  }), [page, search, statusFilter])

  const { data: tasksResponse, isLoading, error, refetch } = useAdminTasks(tasksParams)

  // Fetch task details
  const { data: taskDetail, isLoading: isDetailLoading } = useAdminTaskDetail(
    selectedTaskId || 0,
    isDetailOpen
  )

  const handleOpenDetail = (taskId: number) => {
    setSelectedTaskId(taskId)
    setIsDetailOpen(true)
  }

  const handlePageChange = useCallback((newPage: number) => {
    setPage(newPage)
  }, [])

  const formatCurrency = (amount: number | undefined) => {
    if (amount === undefined) return '-'
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0
    }).format(amount)
  }

  // Filter tasks client-side by date if date filter is set
  const tasksData = useMemo(() => {
    if (!tasksResponse?.data) return []
    if (!dateFilter) return tasksResponse.data

    return tasksResponse.data.filter((task) => {
      const taskDate = new Date(task.created_at).toISOString().split('T')[0]
      return taskDate === dateFilter
    })
  }, [tasksResponse, dateFilter])

  const totalPages = tasksResponse ? Math.ceil((tasksResponse.total || 0) / 10) : 1

  // Define Columns
  const columns = useMemo<Column<any>[]>(() => [
    {
      key: 'id',
      title: 'ID',
      className: 'px-6 py-4 font-semibold w-16'
    },
    {
      key: 'title',
      title: 'Title',
      render: (t) => (
        <span className="font-semibold text-gray-900 block truncate max-w-xs" title={t.title}>
          {t.title}
        </span>
      )
    },
    {
      key: 'user_name',
      title: 'User',
      render: (t) => t.user_name || '-'
    },
    {
      key: 'helper_name',
      title: 'Helper',
      render: (t) => t.helper_name || (
        <span className="text-gray-400 text-xs italic">Unassigned</span>
      )
    },
    {
      key: 'status',
      title: 'Status',
      render: (t) => <StatusBadge status={t.status} />
    },
    {
      key: 'price',
      title: 'Price',
      render: (t) => (
        <span className="font-bold text-gray-900">
          {formatCurrency(t.price)}
        </span>
      )
    },
    {
      key: 'created_at',
      title: 'Created Date',
      render: (t) => new Date(t.created_at).toLocaleDateString()
    },
    {
      key: 'actions',
      title: 'Actions',
      className: 'px-6 py-4 text-right',
      render: (t) => (
        <div className="flex justify-end">
          <button
            onClick={() => handleOpenDetail(t.id)}
            className="p-1.5 text-gray-500 hover:text-primary hover:bg-gray-100 rounded-lg transition-colors min-w-[36px] min-h-[36px] flex items-center justify-center border border-transparent hover:border-gray-200"
            title="View Task Details"
            aria-label="View Task Details"
          >
            <Eye size={18} />
          </button>
        </div>
      )
    }
  ], [])

  return (
    <div className="space-y-6">
      <PageHeader
        title="Task Management"
        subtitle="Monitor, review, and filter all platform tasks"
      />

      {/* Control Panel */}
      <div className="flex flex-col lg:flex-row gap-4 items-stretch lg:items-center justify-between">
        <div className="flex-1 flex flex-col sm:flex-row gap-3">
          <SearchBar
            value={search}
            onChange={(val) => {
              setSearch(val)
              setPage(1)
            }}
            placeholder="Search task title..."
          />
          {/* Custom Date Input for Date Filter */}
          <div className="flex items-center gap-2 border border-gray-200 rounded-lg px-3 bg-white min-h-[44px]">
            <Calendar size={16} className="text-gray-400" />
            <input
              type="date"
              value={dateFilter}
              onChange={(e) => {
                setDateFilter(e.target.value)
                setPage(1)
              }}
              className="text-sm text-gray-700 bg-transparent focus:outline-none cursor-pointer"
              title="Filter by creation date"
            />
            {dateFilter && (
              <button
                onClick={() => {
                  setDateFilter('')
                  setPage(1)
                }}
                className="text-xs text-gray-400 hover:text-gray-600 font-semibold px-1 rounded"
              >
                Clear
              </button>
            )}
          </div>
        </div>

        <FilterBar
          options={[
            { value: 'all', label: 'All Tasks' },
            { value: 'open', label: 'Open' },
            { value: 'in_progress', label: 'Active' },
            { value: 'completed', label: 'Completed' },
            { value: 'cancelled', label: 'Cancelled' }
          ]}
          selectedValue={statusFilter}
          onChange={(val) => {
            setStatusFilter(val)
            setPage(1)
          }}
          label="Status"
        />
      </div>

      {/* Table */}
      <AdminTable
        columns={columns}
        data={tasksData}
        isLoading={isLoading}
        error={error}
        refetch={refetch}
        pagination={{
          currentPage: page,
          totalPages,
          onPageChange: handlePageChange
        }}
      />

      {/* Detail Dialog */}
      <DetailDialog
        isOpen={isDetailOpen}
        onClose={() => setIsDetailOpen(false)}
        title="Task Information & Status Details"
        footer={
          <Button
            variant="secondary"
            onClick={() => setIsDetailOpen(false)}
            className="min-h-[44px]"
          >
            Close
          </Button>
        }
      >
        {isDetailLoading ? (
          <div className="space-y-4 animate-pulse">
            <div className="h-10 bg-gray-150 rounded w-1/3"></div>
            <div className="h-6 bg-gray-150 rounded w-2/3"></div>
            <div className="h-24 bg-gray-150 rounded"></div>
          </div>
        ) : taskDetail ? (
          <div className="space-y-6">
            {/* Header info */}
            <div>
              <div className="flex items-start justify-between gap-4">
                <h4 className="text-lg font-bold text-gray-900">{taskDetail.title}</h4>
                <StatusBadge status={taskDetail.status} />
              </div>
              <p className="text-sm text-gray-500 mt-1">Category ID #{taskDetail.category_id}</p>
            </div>

            {/* Description */}
            <div className="bg-gray-50 rounded-xl p-4 border border-gray-200">
              <span className="text-xs font-semibold text-gray-400 block uppercase">Task Description</span>
              <p className="text-sm text-gray-700 mt-2 whitespace-pre-wrap leading-relaxed">
                {taskDetail.description}
              </p>
            </div>

            {/* Budget Details */}
            <div className="grid grid-cols-2 gap-4 border-t border-b border-gray-150 py-4">
              <div>
                <span className="text-xs text-gray-400 font-semibold block uppercase">Task Price</span>
                <span className="text-xl font-bold text-gray-900 block mt-1">
                  {formatCurrency(taskDetail.price)}
                </span>
              </div>
              <div>
                <span className="text-xs text-gray-400 font-semibold block uppercase">Deadline</span>
                <span className="text-sm font-semibold text-gray-800 block mt-2">
                  {new Date(taskDetail.deadline_end).toLocaleDateString()}
                </span>
              </div>
            </div>

            {/* Parties */}
            <div className="space-y-4">
              <h5 className="text-xs font-bold uppercase text-gray-400 tracking-wider">Involved Parties</h5>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div className="bg-gray-50 border border-gray-200 rounded-xl p-4">
                  <span className="text-xs font-semibold text-gray-400 block uppercase">Task Creator</span>
                  <span className="text-sm font-bold text-gray-900 block mt-1">
                    {taskDetail.user_name || `User ID #${taskDetail.user_id}`}
                  </span>
                </div>
                <div className="bg-gray-50 border border-gray-200 rounded-xl p-4">
                  <span className="text-xs font-semibold text-gray-400 block uppercase">Assigned Helper</span>
                  <span className="text-sm font-bold text-gray-900 block mt-1">
                    {taskDetail.helper_name || (
                      <span className="text-gray-400 italic font-normal text-xs">Unassigned</span>
                    )}
                  </span>
                </div>
              </div>
            </div>

            {/* Status History Timeline */}
            {taskDetail.status_history && taskDetail.status_history.length > 0 && (
              <div className="space-y-4">
                <h5 className="text-xs font-bold uppercase text-gray-400 tracking-wider">Status History</h5>
                <div className="relative border-l border-gray-200 pl-4 space-y-4 ml-2">
                  {taskDetail.status_history.map((hist: any) => (
                    <div key={hist.id} className="relative">
                      {/* Bullet icon */}
                      <span className="absolute -left-[22px] top-1 bg-white border border-gray-300 rounded-full h-3.5 w-3.5 flex items-center justify-center">
                        <span className="h-1.5 w-1.5 bg-primary rounded-full"></span>
                      </span>
                      <div>
                        <div className="flex items-center gap-2">
                          <span className="capitalize text-xs font-bold text-gray-800 bg-gray-150 px-2 py-0.5 rounded border border-gray-200">
                            {hist.status}
                          </span>
                          <span className="text-xs text-gray-400">
                            {new Date(hist.created_at).toLocaleString()}
                          </span>
                        </div>
                        {hist.note && (
                          <p className="text-xs text-gray-500 mt-1 italic">
                            Note: "{hist.note}"
                          </p>
                        )}
                        <p className="text-xs text-gray-400 mt-0.5">
                          Updated by: {hist.created_by_name}
                        </p>
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            )}
          </div>
        ) : (
          <div className="text-center py-8 text-gray-400">
            <ClipboardList size={36} className="mx-auto mb-2 text-gray-300" />
            <p className="text-sm">Task details failed to load.</p>
          </div>
        )}
      </DetailDialog>
    </div>
  )
}
export default TaskManagementPage
