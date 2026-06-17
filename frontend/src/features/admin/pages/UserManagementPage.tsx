import { useState, useMemo, useCallback } from 'react'
import {
  useAdminUsers,
  useAdminUserDetail,
  useUpdateUserStatus
} from '../hooks/useAdmin'
import { useToastStore } from '../hooks/useToast'
import {
  AdminTable,
  Column,
  SearchBar,
  FilterBar,
  StatusBadge,
  DetailDialog,
  ConfirmationDialog
} from '../components'
import { PageHeader } from '@/components/layout/PageHeader'
import { Button } from '@/components/ui/Button'
import { Eye, ShieldAlert, ShieldCheck, User as UserIcon } from 'lucide-react'

export function UserManagementPage() {
  const { addToast } = useToastStore()
  
  // Search, Pagination, Filter states
  const [search, setSearch] = useState('')
  const [page, setPage] = useState(1)
  const [statusFilter, setStatusFilter] = useState('all') // 'all', 'active' (1), 'suspended' (0)

  // Details Modal states
  const [selectedUserId, setSelectedUserId] = useState<number | null>(null)
  const [isDetailOpen, setIsDetailOpen] = useState(false)

  // Confirmation dialog states
  const [confirmState, setConfirmState] = useState<{
    isOpen: boolean
    title: string
    message: string
    userId: number
    nextActiveState: number
  } | null>(null)

  // Fetch Users
  const usersParams = useMemo(() => {
    // API uses active parameter? Let's check. 
    // In OpenAPI /admin/users doesn't list active status parameter, but status update is supported.
    // If status filter is active/suspended, we can filter client-side or check if endpoint supports filtering.
    // To be perfectly robust, we'll fetch all matching search/role and apply filter on UI or pass to API.
    return {
      page,
      per_page: 10,
      search: search || undefined
    }
  }, [page, search])

  const { data: usersResponse, isLoading, error, refetch } = useAdminUsers(usersParams)
  
  // Fetch details of selected user
  const { data: userDetail, isLoading: isDetailLoading } = useAdminUserDetail(
    selectedUserId || 0,
    isDetailOpen
  )

  // Mutation
  const updateStatusMutation = useUpdateUserStatus()

  // Handler for toggle suspension
  const handleToggleStatus = (userId: number, currentActive: number) => {
    const nextActive = currentActive === 1 ? 0 : 1
    const actionLabel = nextActive === 1 ? 'activate' : 'suspend'
    
    setConfirmState({
      isOpen: true,
      title: `${nextActive === 1 ? 'Activate' : 'Suspend'} User Account`,
      message: `Are you sure you want to ${actionLabel} this user account? Suspended users cannot log in or submit tasks.`,
      userId,
      nextActiveState: nextActive
    })
  }

  const handleConfirmStatusChange = async () => {
    if (!confirmState) return

    try {
      await updateStatusMutation.mutateAsync({
        id: confirmState.userId,
        active: confirmState.nextActiveState
      })
      
      addToast(
        'success',
        `User account successfully ${confirmState.nextActiveState === 1 ? 'activated' : 'suspended'}.`
      )
    } catch (err: any) {
      addToast(
        'error',
        err?.message || 'Failed to update user account status. Please try again.'
      )
    } finally {
      setConfirmState(null)
    }
  }

  const handleOpenDetail = (userId: number) => {
    setSelectedUserId(userId)
    setIsDetailOpen(true)
  }

  const handlePageChange = useCallback((newPage: number) => {
    setPage(newPage)
  }, [])

  // Filter local data if status filter is selected (fallback to make sure filtering is 100% correct)
  const filteredUsers = useMemo(() => {
    if (!usersResponse?.data) return []
    if (statusFilter === 'all') return usersResponse.data
    const target = statusFilter === 'active' ? 1 : 0
    return usersResponse.data.filter((u) => u.active === target)
  }, [usersResponse, statusFilter])

  const totalPages = usersResponse ? Math.ceil((usersResponse.total || 0) / 10) : 1

  // Define Columns
  const columns = useMemo<Column<any>[]>(() => [
    {
      key: 'id',
      title: 'ID',
      className: 'px-6 py-4 font-semibold w-16'
    },
    {
      key: 'name',
      title: 'Name',
      render: (u) => (
        <div className="flex items-center gap-3">
          <div className="h-9 w-9 bg-primary/10 text-primary rounded-full flex items-center justify-center font-bold text-xs uppercase flex-shrink-0">
            {u.name.substring(0, 2)}
          </div>
          <div>
            <span className="font-semibold text-gray-900 block">{u.name}</span>
            <span className="text-xs text-gray-400 block">{u.phone}</span>
          </div>
        </div>
      )
    },
    {
      key: 'email',
      title: 'Email'
    },
    {
      key: 'role',
      title: 'Role',
      render: (u) => (
        <span className="capitalize text-xs font-semibold text-gray-600 bg-gray-150 px-2.5 py-1 rounded-md border border-gray-200">
          {u.role}
        </span>
      )
    },
    {
      key: 'status',
      title: 'Status',
      render: (u) => <StatusBadge status={u.active} />
    },
    {
      key: 'created_at',
      title: 'Created Date',
      render: (u) => new Date(u.created_at).toLocaleDateString()
    },
    {
      key: 'actions',
      title: 'Actions',
      className: 'px-6 py-4 text-right',
      render: (u) => (
        <div className="flex justify-end gap-2">
          <button
            onClick={() => handleOpenDetail(u.id)}
            className="p-1.5 text-gray-500 hover:text-primary hover:bg-gray-100 rounded-lg transition-colors min-w-[36px] min-h-[36px] flex items-center justify-center border border-transparent hover:border-gray-200"
            title="View Details"
            aria-label="View Details"
          >
            <Eye size={18} />
          </button>
          {u.active === 1 ? (
            <button
              onClick={() => handleToggleStatus(u.id, u.active)}
              className="p-1.5 text-gray-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors min-w-[36px] min-h-[36px] flex items-center justify-center border border-transparent hover:border-rose-100"
              title="Suspend User"
              aria-label="Suspend User"
            >
              <ShieldAlert size={18} />
            </button>
          ) : (
            <button
              onClick={() => handleToggleStatus(u.id, u.active)}
              className="p-1.5 text-gray-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors min-w-[36px] min-h-[36px] flex items-center justify-center border border-transparent hover:border-emerald-100"
              title="Activate User"
              aria-label="Activate User"
            >
              <ShieldCheck size={18} />
            </button>
          )}
        </div>
      )
    }
  ], [])

  return (
    <div className="space-y-6">
      <PageHeader
        title="User Management"
        subtitle="Manage platform users, roles, and suspension states"
      />

      {/* Control Panel */}
      <div className="flex flex-col sm:flex-row gap-4 items-stretch sm:items-center justify-between">
        <SearchBar
          value={search}
          onChange={(val) => {
            setSearch(val)
            setPage(1)
          }}
          placeholder="Search name or email..."
        />
        <FilterBar
          options={[
            { value: 'all', label: 'All Users' },
            { value: 'active', label: 'Active' },
            { value: 'suspended', label: 'Suspended' }
          ]}
          selectedValue={statusFilter}
          onChange={(val) => {
            setStatusFilter(val)
            setPage(1)
          }}
          label="Filter"
        />
      </div>

      {/* Table */}
      <AdminTable
        columns={columns}
        data={filteredUsers}
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
        title="User Account Details"
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
        ) : userDetail ? (
          <div className="space-y-6">
            {/* Header info */}
            <div className="flex items-start gap-4">
              <div className="h-16 w-16 bg-primary/10 text-primary rounded-xl flex items-center justify-center font-bold text-xl uppercase">
                {userDetail.name.substring(0, 2)}
              </div>
              <div className="flex-1">
                <h4 className="text-lg font-bold text-gray-900">{userDetail.name}</h4>
                <p className="text-sm text-gray-500">{userDetail.email}</p>
                <div className="flex items-center gap-2 mt-2">
                  <span className="capitalize text-xs font-semibold px-2 py-0.5 border rounded bg-gray-50 text-gray-600">
                    {userDetail.role}
                  </span>
                  <StatusBadge status={userDetail.active} />
                </div>
              </div>
            </div>

            {/* Profile Statistics Grid */}
            <div className="grid grid-cols-2 gap-4 border-t border-b border-gray-150 py-4">
              <div className="text-center bg-gray-50 p-4 rounded-xl border border-gray-200">
                <span className="text-xs text-gray-400 font-semibold block uppercase">Total Requests</span>
                <span className="text-2xl font-bold text-gray-900 block mt-1">
                  {userDetail.stats?.total_tasks ?? 0}
                </span>
              </div>
              <div className="text-center bg-gray-50 p-4 rounded-xl border border-gray-200">
                <span className="text-xs text-gray-400 font-semibold block uppercase">Completed Requests</span>
                <span className="text-2xl font-bold text-emerald-600 block mt-1">
                  {userDetail.stats?.completed_tasks ?? 0}
                </span>
              </div>
            </div>

            {/* Contact Details */}
            <div className="space-y-3">
              <h5 className="text-xs font-bold uppercase text-gray-400 tracking-wider">Account Information</h5>
              <div className="bg-gray-50 rounded-xl p-4 border border-gray-200/50 space-y-2 text-sm">
                <div className="flex justify-between">
                  <span className="text-gray-500 font-medium">Phone Number</span>
                  <span className="text-gray-900 font-semibold">{userDetail.phone || '-'}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-gray-500 font-medium">Joined Date</span>
                  <span className="text-gray-900 font-semibold">
                    {new Date(userDetail.created_at).toLocaleDateString()}
                  </span>
                </div>
                {userDetail.rating !== undefined && (
                  <div className="flex justify-between">
                    <span className="text-gray-500 font-medium">Rating</span>
                    <span className="text-gray-900 font-semibold">⭐ {userDetail.rating.toFixed(1)} / 5.0</span>
                  </div>
                )}
              </div>
            </div>
          </div>
        ) : (
          <div className="text-center py-8 text-gray-400">
            <UserIcon size={36} className="mx-auto mb-2 text-gray-300" />
            <p className="text-sm">User details failed to load.</p>
          </div>
        )}
      </DetailDialog>

      {/* Confirmation Dialog */}
      <ConfirmationDialog
        isOpen={confirmState?.isOpen || false}
        title={confirmState?.title || ''}
        message={confirmState?.message || ''}
        confirmText={confirmState?.nextActiveState === 1 ? 'Activate' : 'Suspend'}
        type={confirmState?.nextActiveState === 1 ? 'info' : 'danger'}
        isLoading={updateStatusMutation.isPending}
        onConfirm={handleConfirmStatusChange}
        onCancel={() => setConfirmState(null)}
      />
    </div>
  )
}
export default UserManagementPage
