import { useState, useMemo, useCallback } from 'react'
import { useAdminTransactions, useAdminTransactionDetail } from '../hooks/useAdmin'
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
import { Eye, CreditCard, ArrowUpRight, ArrowDownLeft } from 'lucide-react'

export function TransactionManagementPage() {
  // Filter, pagination, search states
  const [search, setSearch] = useState('')
  const [page, setPage] = useState(1)
  const [typeFilter, setTypeFilter] = useState('all') // 'all', 'task_payment', 'withdraw', 'refund', 'adjustment'

  // Selected Detail Modal states
  const [selectedTxnId, setSelectedTxnId] = useState<number | null>(null)
  const [isDetailOpen, setIsDetailOpen] = useState(false)

  // Fetch transactions list
  const txnParams = useMemo(() => ({
    page,
    per_page: 10,
    search: search || undefined,
    type: typeFilter === 'all' ? undefined : typeFilter
  }), [page, search, typeFilter])

  const { data: txnsResponse, isLoading, error, refetch } = useAdminTransactions(txnParams)

  // Fetch transaction details
  const { data: txnDetail, isLoading: isDetailLoading } = useAdminTransactionDetail(
    selectedTxnId || 0,
    isDetailOpen
  )

  const handleOpenDetail = (id: number) => {
    setSelectedTxnId(id)
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

  const getTxnTypeLabel = (type: string) => {
    const labels: Record<string, string> = {
      task_payment: 'Task Payment',
      withdraw: 'Withdrawal',
      refund: 'Refund',
      adjustment: 'Adjustment'
    }
    return labels[type] || type
  }

  const totalPages = txnsResponse ? Math.ceil((txnsResponse.total || 0) / 10) : 1

  // Define Columns
  const columns = useMemo<Column<any>[]>(() => [
    {
      key: 'id',
      title: 'ID',
      className: 'px-6 py-4 font-semibold w-16'
    },
    {
      key: 'type',
      title: 'Type',
      render: (t) => (
        <div className="flex items-center gap-2">
          {t.type === 'withdraw' ? (
            <ArrowUpRight size={16} className="text-rose-500" />
          ) : (
            <ArrowDownLeft size={16} className="text-emerald-500" />
          )}
          <span className="font-semibold text-gray-900 capitalize">
            {getTxnTypeLabel(t.type)}
          </span>
        </div>
      )
    },
    {
      key: 'amount',
      title: 'Amount',
      render: (t) => (
        <span className={`font-bold ${t.type === 'withdraw' ? 'text-rose-600' : 'text-emerald-600'}`}>
          {t.type === 'withdraw' ? '-' : '+'}{formatCurrency(t.amount)}
        </span>
      )
    },
    {
      key: 'status',
      title: 'Status',
      render: (t) => <StatusBadge status={t.status === 'completed' ? 'completed_txn' : t.status} />
    },
    {
      key: 'user_name',
      title: 'User Name',
      render: (t) => t.user_name || `User ID #${t.user_id}`
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
            title="View Transaction Details"
            aria-label="View Transaction Details"
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
        title="Transaction Management"
        subtitle="View and audit all financial transactions on the platform"
      />

      {/* Control Panel */}
      <div className="flex flex-col lg:flex-row gap-4 items-stretch lg:items-center justify-between">
        <SearchBar
          value={search}
          onChange={(val) => {
            setSearch(val)
            setPage(1)
          }}
          placeholder="Search by transaction note reference..."
        />
        <FilterBar
          options={[
            { value: 'all', label: 'All Types' },
            { value: 'task_payment', label: 'Payments' },
            { value: 'withdraw', label: 'Withdrawals' },
            { value: 'refund', label: 'Refunds' },
            { value: 'adjustment', label: 'Adjustments' }
          ]}
          selectedValue={typeFilter}
          onChange={(val) => {
            setTypeFilter(val)
            setPage(1)
          }}
          label="Type"
        />
      </div>

      {/* Table */}
      <AdminTable
        columns={columns}
        data={txnsResponse?.data}
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
        title="Transaction Audit Record"
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
        ) : txnDetail ? (
          <div className="space-y-6">
            {/* Header transaction info */}
            <div>
              <div className="flex items-start justify-between gap-4">
                <div>
                  <h4 className="text-lg font-bold text-gray-900 capitalize">
                    {getTxnTypeLabel(txnDetail.type)} Record
                  </h4>
                  <p className="text-sm text-gray-500 mt-1">Transaction ID #{txnDetail.id}</p>
                </div>
                <StatusBadge status={txnDetail.status === 'completed' ? 'completed_txn' : txnDetail.status} />
              </div>
            </div>

            {/* Description / Summary */}
            <div className="bg-gray-50 rounded-xl p-4 border border-gray-200">
              <span className="text-xs font-semibold text-gray-400 block uppercase">Transaction Description</span>
              <p className="text-sm text-gray-700 mt-2 font-medium">
                {txnDetail.description || 'No transaction summary description provided.'}
              </p>
            </div>

            {/* Amount details */}
            <div className="grid grid-cols-2 gap-4 border-t border-b border-gray-150 py-4">
              <div>
                <span className="text-xs text-gray-400 font-semibold block uppercase">Amount</span>
                <span className={`text-xl font-bold block mt-1 ${
                  txnDetail.type === 'withdraw' ? 'text-rose-600' : 'text-emerald-600'
                }`}>
                  {txnDetail.type === 'withdraw' ? '-' : '+'}{formatCurrency(txnDetail.amount)}
                </span>
              </div>
              <div>
                <span className="text-xs text-gray-400 font-semibold block uppercase">Date Created</span>
                <span className="text-sm font-semibold text-gray-800 block mt-2">
                  {new Date(txnDetail.created_at).toLocaleString()}
                </span>
              </div>
            </div>

            {/* User Details */}
            <div className="space-y-3">
              <h5 className="text-xs font-bold uppercase text-gray-400 tracking-wider">Associated Account</h5>
              <div className="bg-gray-50 rounded-xl p-4 border border-gray-200/50 space-y-2 text-sm">
                <div className="flex justify-between">
                  <span className="text-gray-500 font-medium">User Name</span>
                  <span className="text-gray-900 font-semibold">{txnDetail.user_name || '-'}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-gray-500 font-medium">User ID</span>
                  <span className="text-gray-900 font-semibold">#{txnDetail.user_id}</span>
                </div>
                {txnDetail.reference_id && (
                  <div className="flex justify-between">
                    <span className="text-gray-500 font-medium">Reference ID</span>
                    <span className="text-gray-900 font-mono text-xs font-semibold">{txnDetail.reference_id}</span>
                  </div>
                )}
              </div>
            </div>
          </div>
        ) : (
          <div className="text-center py-8 text-gray-400">
            <CreditCard size={36} className="mx-auto mb-2 text-gray-300" />
            <p className="text-sm">Transaction details failed to load.</p>
          </div>
        )}
      </DetailDialog>
    </div>
  )
}
export default TransactionManagementPage
