import { useState, useMemo, useCallback } from 'react'
import {
  useAdminHelpers,
  useAdminHelperDetail,
  useVerifyHelper,
  useRejectHelper
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
import { Input } from '@/components/ui/Input'
import { Eye, Check, X, ShieldAlert, Award } from 'lucide-react'

export function HelperManagementPage() {
  const { addToast } = useToastStore()

  // Search, Pagination, Filter states
  const [search, setSearch] = useState('')
  const [page, setPage] = useState(1)
  const [statusFilter, setStatusFilter] = useState('pending') // default to pending for queue view

  // Detail Modal states
  const [selectedHelperId, setSelectedHelperId] = useState<number | null>(null)
  const [isDetailOpen, setIsDetailOpen] = useState(false)

  // Verification actions states
  const [confirmVerifyId, setConfirmVerifyId] = useState<number | null>(null)
  
  // Rejection prompts
  const [rejectId, setRejectId] = useState<number | null>(null)
  const [rejectReason, setRejectReason] = useState('')

  // Fetch helpers
  const helpersParams = useMemo(() => ({
    page,
    per_page: 10,
    search: search || undefined,
    verification_status: statusFilter === 'all' ? undefined : statusFilter
  }), [page, search, statusFilter])

  const { data: helpersResponse, isLoading, error, refetch } = useAdminHelpers(helpersParams)

  // Fetch helper details
  const { data: helperDetail, isLoading: isDetailLoading } = useAdminHelperDetail(
    selectedHelperId || 0,
    isDetailOpen
  )

  // Mutations
  const verifyMutation = useVerifyHelper()
  const rejectMutation = useRejectHelper()

  // Actions handlers
  const handleVerify = async (id: number) => {
    setConfirmVerifyId(id)
  }

  const handleConfirmVerify = async () => {
    if (!confirmVerifyId) return

    try {
      await verifyMutation.mutateAsync(confirmVerifyId)
      addToast('success', 'Helper account verified successfully.')
    } catch (err: any) {
      addToast('error', err?.message || 'Verification failed. Please try again.')
    } finally {
      setConfirmVerifyId(null)
    }
  }

  const handleReject = (id: number) => {
    setRejectId(id)
    setRejectReason('')
  }

  const handleConfirmReject = async () => {
    if (!rejectId) return
    if (!rejectReason.trim()) {
      addToast('error', 'Rejection reason is required.')
      return
    }

    try {
      await rejectMutation.mutateAsync({
        id: rejectId,
        reason: rejectReason
      })
      addToast('success', 'Helper application rejected successfully.')
      setRejectId(null)
    } catch (err: any) {
      addToast('error', err?.message || 'Rejection failed. Please try again.')
    }
  }

  const handleOpenDetail = (id: number) => {
    setSelectedHelperId(id)
    setIsDetailOpen(true)
  }

  const handlePageChange = useCallback((newPage: number) => {
    setPage(newPage)
  }, [])

  // Columns definition
  const columns = useMemo<Column<any>[]>(() => [
    {
      key: 'name',
      title: 'Name',
      render: (h) => (
        <div className="flex items-center gap-3">
          <div className="h-9 w-9 bg-primary/10 text-primary rounded-full flex items-center justify-center font-bold text-xs uppercase flex-shrink-0">
            {h.name.substring(0, 2)}
          </div>
          <div>
            <span className="font-semibold text-gray-900 block">{h.name}</span>
            <span className="text-xs text-gray-400 block">{h.email}</span>
          </div>
        </div>
      )
    },
    {
      key: 'verification_status',
      title: 'Verification Status',
      render: (h) => <StatusBadge status={h.verification_status} />
    },
    {
      key: 'completed_tasks',
      title: 'Completed Tasks',
      className: 'text-center w-32',
      render: (h) => h.completed_tasks ?? 0
    },
    {
      key: 'rating',
      title: 'Rating',
      render: (h) => (
        <span className="font-semibold text-gray-900 flex items-center gap-1">
          ⭐ {h.rating !== undefined ? Number(h.rating).toFixed(1) : '0.0'}
        </span>
      )
    },
    {
      key: 'created_at',
      title: 'Join Date',
      render: (h) => new Date(h.created_at).toLocaleDateString()
    },
    {
      key: 'actions',
      title: 'Actions',
      className: 'px-6 py-4 text-right',
      render: (h) => (
        <div className="flex justify-end gap-2">
          <button
            onClick={() => handleOpenDetail(h.id)}
            className="p-1.5 text-gray-500 hover:text-primary hover:bg-gray-100 rounded-lg transition-colors min-w-[36px] min-h-[36px] flex items-center justify-center border border-transparent hover:border-gray-200"
            title="View Profile Details"
            aria-label="View Profile Details"
          >
            <Eye size={18} />
          </button>
          
          {h.verification_status === 'pending' && (
            <>
              <button
                onClick={() => handleVerify(h.id)}
                className="p-1.5 text-gray-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors min-w-[36px] min-h-[36px] flex items-center justify-center border border-transparent hover:border-emerald-100"
                title="Verify Helper"
                aria-label="Verify Helper"
              >
                <Check size={18} />
              </button>
              <button
                onClick={() => handleReject(h.id)}
                className="p-1.5 text-gray-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors min-w-[36px] min-h-[36px] flex items-center justify-center border border-transparent hover:border-rose-100"
                title="Reject Helper"
                aria-label="Reject Helper"
              >
                <X size={18} />
              </button>
            </>
          )}
        </div>
      )
    }
  ], [])

  const totalPages = helpersResponse ? Math.ceil((helpersResponse.total || 0) / 10) : 1

  return (
    <div className="space-y-6">
      <PageHeader
        title="Helper Management"
        subtitle="Verify helper applications and manage credential reviews"
      />

      {/* Control Panel */}
      <div className="flex flex-col sm:flex-row gap-4 items-stretch sm:items-center justify-between">
        <SearchBar
          value={search}
          onChange={(val) => {
            setSearch(val)
            setPage(1)
          }}
          placeholder="Search helpers by name..."
        />
        <FilterBar
          options={[
            { value: 'pending', label: 'Queue (Pending)' },
            { value: 'verified', label: 'Verified' },
            { value: 'rejected', label: 'Rejected' },
            { value: 'all', label: 'All Helpers' }
          ]}
          selectedValue={statusFilter}
          onChange={(val) => {
            setStatusFilter(val)
            setPage(1)
          }}
          label="Queue"
        />
      </div>

      {/* Helper Table */}
      <AdminTable
        columns={columns}
        data={helpersResponse?.data}
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
        title="Helper Profile & Verification Review"
        footer={
          <div className="flex gap-2 justify-end w-full">
            <Button
              variant="secondary"
              onClick={() => setIsDetailOpen(false)}
              className="min-h-[44px]"
            >
              Close
            </Button>
            {helperDetail?.verification_status === 'pending' && (
              <>
                <Button
                  variant="secondary"
                  className="min-h-[44px] text-rose-600 hover:text-rose-700 bg-rose-50 border border-rose-200"
                  onClick={() => {
                    setIsDetailOpen(false)
                    handleReject(helperDetail.id)
                  }}
                >
                  Reject Application
                </Button>
                <Button
                  className="min-h-[44px]"
                  onClick={() => {
                    setIsDetailOpen(false)
                    handleVerify(helperDetail.id)
                  }}
                >
                  Verify Account
                </Button>
              </>
            )}
          </div>
        }
      >
        {isDetailLoading ? (
          <div className="space-y-4 animate-pulse">
            <div className="h-10 bg-gray-150 rounded w-1/3"></div>
            <div className="h-6 bg-gray-150 rounded w-2/3"></div>
            <div className="h-24 bg-gray-150 rounded"></div>
          </div>
        ) : helperDetail ? (
          <div className="space-y-6">
            {/* Header profile cards */}
            <div className="flex items-start gap-4">
              <div className="h-16 w-16 bg-primary/10 text-primary rounded-xl flex items-center justify-center font-bold text-xl uppercase">
                {helperDetail.user_id ? 'H' : 'U'}
              </div>
              <div className="flex-1">
                <h4 className="text-lg font-bold text-gray-900">Helper ID #{helperDetail.id}</h4>
                <p className="text-sm text-gray-500">Linked User Account #{helperDetail.user_id}</p>
                <div className="flex items-center gap-2 mt-2">
                  <StatusBadge status={helperDetail.verification_status} />
                </div>
              </div>
            </div>

            {/* Profile Statistics Grid */}
            <div className="grid grid-cols-2 gap-4 border-t border-b border-gray-150 py-4">
              <div className="text-center bg-gray-50 p-4 rounded-xl border border-gray-200">
                <span className="text-xs text-gray-400 font-semibold block uppercase">Completed Tasks</span>
                <span className="text-2xl font-bold text-emerald-600 block mt-1">
                  {helperDetail.completed_tasks ?? 0}
                </span>
              </div>
              <div className="text-center bg-gray-50 p-4 rounded-xl border border-gray-200">
                <span className="text-xs text-gray-400 font-semibold block uppercase">Verification Status</span>
                <span className="text-base font-bold text-gray-800 block mt-2 capitalize">
                  {helperDetail.verification_status}
                </span>
              </div>
            </div>

            {/* Verification Credentials */}
            <div className="space-y-4">
              <h5 className="text-xs font-bold uppercase text-gray-400 tracking-wider">Verification Credentials</h5>
              
              <div className="bg-gray-50 rounded-xl p-4 border border-gray-200 space-y-3">
                <div>
                  <span className="text-xs font-semibold text-gray-400 block uppercase">NIK / KTP Number</span>
                  <span className="text-sm font-bold text-gray-900 font-mono mt-1 block">
                    {helperDetail.ktp_number || 'No KTP Number provided'}
                  </span>
                </div>
                <div>
                  <span className="text-xs font-semibold text-gray-400 block uppercase">Bio</span>
                  <p className="text-sm text-gray-700 mt-1 whitespace-pre-wrap leading-relaxed">
                    {helperDetail.bio || 'No bio summary provided.'}
                  </p>
                </div>
                <div>
                  <span className="text-xs font-semibold text-gray-400 block uppercase">Specialized Skills</span>
                  <p className="text-sm text-gray-700 mt-1 block">
                    {helperDetail.skills || 'No skills listed.'}
                  </p>
                </div>
              </div>
            </div>
          </div>
        ) : (
          <div className="text-center py-8 text-gray-400">
            <Award size={36} className="mx-auto mb-2 text-gray-300" />
            <p className="text-sm">Helper details failed to load.</p>
          </div>
        )}
      </DetailDialog>

      {/* Verify Confirmation Modal */}
      <ConfirmationDialog
        isOpen={confirmVerifyId !== null}
        title="Verify Helper Application"
        message="Are you sure you want to verify this helper? Verifying this account will allow the helper to accept and complete tasks on the platform."
        confirmText="Verify Helper"
        type="info"
        isLoading={verifyMutation.isPending}
        onConfirm={handleConfirmVerify}
        onCancel={() => setConfirmVerifyId(null)}
      />

      {/* Reject Modal with Input Reason */}
      {rejectId !== null && (
        <div
          className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
          role="dialog"
          aria-modal="true"
          aria-labelledby="reject-dialog-title"
        >
          <div className="bg-white rounded-xl p-6 max-w-md w-full relative shadow-2xl">
            <button
              onClick={() => setRejectId(null)}
              className="absolute right-4 top-4 text-gray-400 hover:text-gray-600 min-h-[44px] min-w-[44px] flex items-center justify-center rounded-lg"
              aria-label="Close dialog"
              disabled={rejectMutation.isPending}
            >
              <X size={20} />
            </button>

            <div className="flex gap-4 items-start mb-4">
              <div className="p-3 rounded-lg flex-shrink-0 bg-rose-50 text-rose-600">
                <ShieldAlert size={24} />
              </div>
              <div className="flex-1">
                <h3 className="text-lg font-bold text-gray-900" id="reject-dialog-title">
                  Reject Helper Application
                </h3>
                <p className="text-sm text-gray-500 mt-1">
                  Please provide a reason for rejecting this application. This helps the applicant improve and submit again.
                </p>
              </div>
            </div>

            <div className="space-y-4">
              <Input
                label="Rejection Reason"
                placeholder="e.g. KTP photo is blurred, NIK does not match identity records"
                value={rejectReason}
                onChange={(e) => setRejectReason(e.target.value)}
                error={rejectReason === '' ? 'Rejection reason is required' : undefined}
                aria-required="true"
                disabled={rejectMutation.isPending}
              />

              <div className="flex gap-3 justify-end pt-2">
                <Button
                  type="button"
                  variant="secondary"
                  className="min-h-[44px] px-5"
                  onClick={() => setRejectId(null)}
                  disabled={rejectMutation.isPending}
                >
                  Cancel
                </Button>
                <Button
                  type="button"
                  variant="danger"
                  className="min-h-[44px] px-5"
                  onClick={handleConfirmReject}
                  loading={rejectMutation.isPending}
                  disabled={rejectMutation.isPending}
                >
                  Confirm Reject
                </Button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  )
}
export default HelperManagementPage
