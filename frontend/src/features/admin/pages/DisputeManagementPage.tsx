import { useState, useMemo, useCallback } from 'react'
import {
  useAdminDisputes,
  useAdminDisputeDetail,
  useReviewDispute,
  useResolveDispute,
  useRejectDispute
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
import { AlertTriangle, Eye, Gavel, X, CheckCircle, Scale } from 'lucide-react'

export function DisputeManagementPage() {
  const { addToast } = useToastStore()

  // Filters, Pagination, Search states
  const [search, setSearch] = useState('')
  const [page, setPage] = useState(1)
  const [statusFilter, setStatusFilter] = useState('all') // 'all', 'open', 'under_review', 'resolved', 'rejected'

  // Detail Modal states
  const [selectedDisputeId, setSelectedDisputeId] = useState<number | null>(null)
  const [isDetailOpen, setIsDetailOpen] = useState(false)

  // Review confirmation states
  const [confirmReviewId, setConfirmReviewId] = useState<number | null>(null)

  // Resolve/Reject dialog states
  const [actionState, setActionState] = useState<{
    type: 'resolve' | 'reject'
    id: number
    resolution: string
  } | null>(null)

  // Fetch disputes list
  const disputesParams = useMemo(() => ({
    page,
    per_page: 10,
    search: search || undefined,
    status: statusFilter === 'all' ? undefined : statusFilter
  }), [page, search, statusFilter])

  const { data: disputesResponse, isLoading, error, refetch } = useAdminDisputes(disputesParams)

  // Fetch dispute details (using the verified GET /disputes/{id})
  const { data: disputeDetail, isLoading: isDetailLoading } = useAdminDisputeDetail(
    selectedDisputeId || 0,
    isDetailOpen
  )

  // Mutations
  const reviewMutation = useReviewDispute()
  const resolveMutation = useResolveDispute()
  const rejectMutation = useRejectDispute()

  // Actions triggers
  const handleReview = (id: number) => {
    setConfirmReviewId(id)
  }

  const handleConfirmReview = async () => {
    if (!confirmReviewId) return

    try {
      await reviewMutation.mutateAsync(confirmReviewId)
      addToast('success', 'Dispute is now under review.')
    } catch (err: any) {
      addToast('error', err?.message || 'Failed to review dispute. Please try again.')
    } finally {
      setConfirmReviewId(null)
    }
  }

  const handleAction = (type: 'resolve' | 'reject', id: number) => {
    setActionState({ type, id, resolution: '' })
  }

  const handleConfirmAction = async () => {
    if (!actionState) return
    if (!actionState.resolution.trim()) {
      addToast('error', 'Resolution note is required.')
      return
    }

    try {
      if (actionState.type === 'resolve') {
        await resolveMutation.mutateAsync({
          id: actionState.id,
          resolution: actionState.resolution
        })
        addToast('success', 'Dispute resolved successfully.')
      } else {
        await rejectMutation.mutateAsync({
          id: actionState.id,
          resolution: actionState.resolution
        })
        addToast('success', 'Dispute rejected successfully.')
      }
      setActionState(null)
    } catch (err: any) {
      addToast('error', err?.message || `Failed to ${actionState.type} dispute.`)
    }
  }

  const handleOpenDetail = (id: number) => {
    setSelectedDisputeId(id)
    setIsDetailOpen(true)
  }

  const handlePageChange = useCallback((newPage: number) => {
    setPage(newPage)
  }, [])

  const totalPages = disputesResponse ? Math.ceil((disputesResponse.total || 0) / 10) : 1

  // Define Columns
  const columns = useMemo<Column<any>[]>(() => [
    {
      key: 'id',
      title: 'ID',
      className: 'px-6 py-4 font-semibold w-16'
    },
    {
      key: 'task_title',
      title: 'Task Title',
      render: (d) => (
        <span className="font-semibold text-gray-900 block truncate max-w-xs" title={d.task_title}>
          {d.task_title || `Task #${d.task_id}`}
        </span>
      )
    },
    {
      key: 'creator_name',
      title: 'Creator / User',
      render: (d) => d.creator_name || `User #${d.user_id}`
    },
    {
      key: 'helper_name',
      title: 'Helper',
      render: (d) => d.helper_name || `Helper #${d.helper_id}`
    },
    {
      key: 'status',
      title: 'Status',
      render: (d) => <StatusBadge status={d.status} />
    },
    {
      key: 'created_at',
      title: 'Date Created',
      render: (d) => new Date(d.created_at).toLocaleDateString()
    },
    {
      key: 'actions',
      title: 'Actions',
      className: 'px-6 py-4 text-right',
      render: (d) => (
        <div className="flex justify-end gap-2">
          <button
            onClick={() => handleOpenDetail(d.id)}
            className="p-1.5 text-gray-500 hover:text-primary hover:bg-gray-100 rounded-lg transition-colors min-w-[36px] min-h-[36px] flex items-center justify-center border border-transparent hover:border-gray-200"
            title="View Dispute Details"
            aria-label="View Dispute Details"
          >
            <Eye size={18} />
          </button>
          
          {d.status === 'open' && (
            <button
              onClick={() => handleReview(d.id)}
              className="p-1.5 text-gray-500 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors min-w-[36px] min-h-[36px] flex items-center justify-center border border-transparent hover:border-purple-100"
              title="Mark Under Review"
              aria-label="Mark Under Review"
            >
              <Scale size={18} />
            </button>
          )}

          {d.status === 'under_review' && (
            <>
              <button
                onClick={() => handleAction('resolve', d.id)}
                className="p-1.5 text-gray-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors min-w-[36px] min-h-[36px] flex items-center justify-center border border-transparent hover:border-emerald-100"
                title="Resolve Dispute"
                aria-label="Resolve Dispute"
              >
                <Gavel size={18} />
              </button>
              <button
                onClick={() => handleAction('reject', d.id)}
                className="p-1.5 text-gray-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors min-w-[36px] min-h-[36px] flex items-center justify-center border border-transparent hover:border-rose-100"
                title="Reject Dispute"
                aria-label="Reject Dispute"
              >
                <X size={18} />
              </button>
            </>
          )}
        </div>
      )
    }
  ], [])

  return (
    <div className="space-y-6">
      <PageHeader
        title="Dispute Management"
        subtitle="Review dispute claims, verify evidence, and arbitrate resolutions"
      />

      {/* Control Panel */}
      <div className="flex flex-col sm:flex-row gap-4 items-stretch sm:items-center justify-between">
        <SearchBar
          value={search}
          onChange={(val) => {
            setSearch(val)
            setPage(1)
          }}
          placeholder="Search by reason or task..."
        />
        <FilterBar
          options={[
            { value: 'all', label: 'All Disputes' },
            { value: 'open', label: 'Open' },
            { value: 'under_review', label: 'Under Review' },
            { value: 'resolved', label: 'Resolved' },
            { value: 'rejected', label: 'Rejected' }
          ]}
          selectedValue={statusFilter}
          onChange={(val) => {
            setStatusFilter(val)
            setPage(1)
          }}
          label="Status"
        />
      </div>

      {/* Disputes Table */}
      <AdminTable
        columns={columns}
        data={disputesResponse?.data}
        isLoading={isLoading}
        error={error}
        refetch={refetch}
        pagination={{
          currentPage: page,
          totalPages,
          onPageChange: handlePageChange
        }}
      />

      {/* Details Dialog */}
      <DetailDialog
        isOpen={isDetailOpen}
        onClose={() => setIsDetailOpen(false)}
        title="Dispute Case Arbiter Detail"
        footer={
          <div className="flex gap-2 justify-end w-full">
            <Button
              variant="secondary"
              onClick={() => setIsDetailOpen(false)}
              className="min-h-[44px]"
            >
              Close
            </Button>
            {disputeDetail?.status === 'open' && (
              <Button
                className="min-h-[44px]"
                onClick={() => {
                  setIsDetailOpen(false)
                  handleReview(disputeDetail.id)
                }}
              >
                Start Reviewing
              </Button>
            )}
            {disputeDetail?.status === 'under_review' && (
              <>
                <Button
                  variant="secondary"
                  className="min-h-[44px] text-rose-600 hover:text-rose-700 bg-rose-50 border border-rose-200"
                  onClick={() => {
                    setIsDetailOpen(false)
                    handleAction('reject', disputeDetail.id)
                  }}
                >
                  Reject Dispute
                </Button>
                <Button
                  className="min-h-[44px]"
                  onClick={() => {
                    setIsDetailOpen(false)
                    handleAction('resolve', disputeDetail.id)
                  }}
                >
                  Resolve Case
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
        ) : disputeDetail ? (
          <div className="space-y-6">
            {/* Header case summary */}
            <div className="flex items-start justify-between gap-4">
              <div>
                <h4 className="text-lg font-bold text-gray-900">
                  {disputeDetail.task_title || `Task ID #${disputeDetail.task_id}`}
                </h4>
                <p className="text-sm text-gray-500 mt-1">Dispute Case #{disputeDetail.id}</p>
              </div>
              <StatusBadge status={disputeDetail.status} />
            </div>

            {/* Case Details */}
            <div className="bg-gray-50 rounded-xl p-4 border border-gray-200 space-y-3">
              <div>
                <span className="text-xs font-semibold text-gray-400 block uppercase">Dispute Reason</span>
                <p className="text-sm font-bold text-gray-800 mt-1">{disputeDetail.reason}</p>
              </div>
              <div>
                <span className="text-xs font-semibold text-gray-400 block uppercase">Description</span>
                <p className="text-sm text-gray-700 mt-1 whitespace-pre-wrap leading-relaxed">
                  {disputeDetail.description || 'No description summary provided.'}
                </p>
              </div>
              {disputeDetail.evidence_file && (
                <div>
                  <span className="text-xs font-semibold text-gray-400 block uppercase">Evidence Reference</span>
                  <span className="text-xs text-gray-600 font-mono mt-1 block break-all">
                    {disputeDetail.evidence_file}
                  </span>
                </div>
              )}
            </div>

            {/* Arbiters & Parties */}
            <div className="space-y-4">
              <h5 className="text-xs font-bold uppercase text-gray-400 tracking-wider">Parties & Actions</h5>
              <div className="grid grid-cols-2 gap-4 text-sm bg-gray-50 p-4 border border-gray-200/50 rounded-xl">
                <div>
                  <span className="text-xs font-semibold text-gray-400 block uppercase">Creator / Client</span>
                  <span className="font-semibold text-gray-900 mt-0.5 block">
                    {disputeDetail.creator_name || `User ID #${disputeDetail.user_id}`}
                  </span>
                </div>
                <div>
                  <span className="text-xs font-semibold text-gray-400 block uppercase">Contract Helper</span>
                  <span className="font-semibold text-gray-900 mt-0.5 block">
                    {disputeDetail.helper_name || `Helper ID #${disputeDetail.helper_id}`}
                  </span>
                </div>
              </div>
            </div>

            {/* Resolution outcome */}
            {(disputeDetail.status === 'resolved' || disputeDetail.status === 'rejected') && (
              <div className="space-y-3">
                <h5 className="text-xs font-bold uppercase text-gray-400 tracking-wider">Resolution Outcome</h5>
                <div className="bg-emerald-50/50 border border-emerald-200 rounded-xl p-4 text-sm">
                  <span className="text-xs font-semibold text-emerald-800 block uppercase">Resolution Note</span>
                  <p className="text-emerald-900 font-medium mt-1 leading-relaxed">
                    {disputeDetail.resolution || disputeDetail.admin_note || 'No resolution notes provided.'}
                  </p>
                  <div className="flex gap-4 mt-3 text-xs text-emerald-700/80 border-t border-emerald-200/50 pt-2">
                    <span>Arbiter ID: {disputeDetail.resolved_by || 'Admin'}</span>
                    <span>Date: {disputeDetail.resolved_at ? new Date(disputeDetail.resolved_at).toLocaleDateString() : '-'}</span>
                  </div>
                </div>
              </div>
            )}
          </div>
        ) : (
          <div className="text-center py-8 text-gray-400">
            <AlertTriangle size={36} className="mx-auto mb-2 text-gray-300" />
            <p className="text-sm">Dispute details failed to load.</p>
          </div>
        )}
      </DetailDialog>

      {/* Mark Review Confirmation */}
      <ConfirmationDialog
        isOpen={confirmReviewId !== null}
        title="Start Dispute Review"
        message="Are you sure you want to transition this dispute to 'Under Review'? This indicates to the disputing parties that an admin is investigating the claim."
        confirmText="Start Review"
        type="info"
        isLoading={reviewMutation.isPending}
        onConfirm={handleConfirmReview}
        onCancel={() => setConfirmReviewId(null)}
      />

      {/* Resolve / Reject Decision Modal with input text */}
      {actionState !== null && (
        <div
          className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
          role="dialog"
          aria-modal="true"
          aria-labelledby="dispute-action-title"
        >
          <div className="bg-white rounded-xl p-6 max-w-md w-full relative shadow-2xl border border-gray-100">
            <button
              onClick={() => setActionState(null)}
              className="absolute right-4 top-4 text-gray-400 hover:text-gray-600 min-h-[44px] min-w-[44px] flex items-center justify-center rounded-lg"
              aria-label="Close dialog"
              disabled={resolveMutation.isPending || rejectMutation.isPending}
            >
              <X size={20} />
            </button>

            <div className="flex gap-4 items-start mb-4">
              <div className={`p-3 rounded-lg flex-shrink-0 ${
                actionState.type === 'resolve' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'
              }`}>
                {actionState.type === 'resolve' ? <CheckCircle size={24} /> : <AlertTriangle size={24} />}
              </div>
              <div className="flex-1">
                <h3 className="text-lg font-bold text-gray-900" id="dispute-action-title">
                  {actionState.type === 'resolve' ? 'Resolve Dispute Case' : 'Reject Dispute Claim'}
                </h3>
                <p className="text-sm text-gray-500 mt-1">
                  Specify the resolution terms. This note will be recorded as the official outcome of this dispute.
                </p>
              </div>
            </div>

            <div className="space-y-4">
              <Input
                label="Resolution Notes"
                placeholder={actionState.type === 'resolve' 
                  ? "e.g. Work verified completed, releasing payment to helper."
                  : "e.g. Invalid claim, refunding tasks budget to user."}
                value={actionState.resolution}
                onChange={(e) => setActionState({ ...actionState, resolution: e.target.value })}
                error={actionState.resolution === '' ? 'Resolution note is required' : undefined}
                aria-required="true"
                disabled={resolveMutation.isPending || rejectMutation.isPending}
              />

              <div className="flex gap-3 justify-end pt-2">
                <Button
                  type="button"
                  variant="secondary"
                  className="min-h-[44px] px-5"
                  onClick={() => setActionState(null)}
                  disabled={resolveMutation.isPending || rejectMutation.isPending}
                >
                  Cancel
                </Button>
                <Button
                  type="button"
                  variant={actionState.type === 'resolve' ? 'primary' : 'danger'}
                  className="min-h-[44px] px-5"
                  onClick={handleConfirmAction}
                  loading={resolveMutation.isPending || rejectMutation.isPending}
                  disabled={resolveMutation.isPending || rejectMutation.isPending}
                >
                  Submit Arbitrate
                </Button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  )
}
export default DisputeManagementPage
