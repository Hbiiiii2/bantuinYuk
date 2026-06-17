import { useState, useMemo, useCallback } from 'react'
import { useAdminWallets } from '../hooks/useAdmin'
import {
  AdminTable,
  Column,
  SearchBar,
  DetailDialog
} from '../components'
import { PageHeader } from '@/components/layout/PageHeader'
import { Button } from '@/components/ui/Button'
import { Eye } from 'lucide-react'

export function WalletManagementPage() {
  // Pagination, search states
  const [search, setSearch] = useState('')
  const [page, setPage] = useState(1)

  // Selected Detail Modal states
  const [selectedWallet, setSelectedWallet] = useState<any | null>(null)
  const [isDetailOpen, setIsDetailOpen] = useState(false)

  // Fetch wallets list
  const walletParams = useMemo(() => ({
    page,
    per_page: 10,
    search: search || undefined
  }), [page, search])

  const { data: walletsResponse, isLoading, error, refetch } = useAdminWallets(walletParams)

  const handleOpenDetail = (wallet: any) => {
    setSelectedWallet(wallet)
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

  const totalPages = walletsResponse ? Math.ceil((walletsResponse.total || 0) / 10) : 1

  // Define Columns
  const columns = useMemo<Column<any>[]>(() => [
    {
      key: 'user',
      title: 'User',
      render: (w) => (
        <div>
          <span className="font-semibold text-gray-900 block">
            {w.user_name || `User ID #${w.user_id}`}
          </span>
          {w.user_email && (
            <span className="text-xs text-gray-400 block">{w.user_email}</span>
          )}
        </div>
      )
    },
    {
      key: 'balance',
      title: 'Balance',
      render: (w) => (
        <span className="font-bold text-gray-950">
          {formatCurrency(w.balance)}
        </span>
      )
    },
    {
      key: 'pending_balance',
      title: 'Pending Balance',
      render: (w) => (
        <span className="font-semibold text-amber-600">
          {formatCurrency(w.pending_balance)}
        </span>
      )
    },
    {
      key: 'created_at',
      title: 'Last Activity',
      render: (w) => new Date(w.created_at).toLocaleDateString()
    },
    {
      key: 'actions',
      title: 'Actions',
      className: 'px-6 py-4 text-right',
      render: (w) => (
        <div className="flex justify-end">
          <button
            onClick={() => handleOpenDetail(w)}
            className="p-1.5 text-gray-500 hover:text-primary hover:bg-gray-100 rounded-lg transition-colors min-w-[36px] min-h-[36px] flex items-center justify-center border border-transparent hover:border-gray-200"
            title="View Wallet details"
            aria-label="View Wallet details"
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
        title="Wallet Management"
        subtitle="Audit user wallet accounts, balances, and held escrow balances"
      />

      {/* Control Panel */}
      <div className="flex gap-4 items-center justify-between">
        <SearchBar
          value={search}
          onChange={(val) => {
            setSearch(val)
            setPage(1)
          }}
          placeholder="Search wallets by user name..."
        />
      </div>

      {/* Table */}
      <AdminTable
        columns={columns}
        data={walletsResponse?.data}
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
        title="User Wallet Ledger Audit"
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
        {selectedWallet && (
          <div className="space-y-6">
            {/* Header info */}
            <div>
              <h4 className="text-lg font-bold text-gray-900">
                Wallet Ledger Summary
              </h4>
              <p className="text-sm text-gray-500 mt-1">Wallet ID #{selectedWallet.id}</p>
            </div>

            {/* Wallet Balances Grid */}
            <div className="grid grid-cols-2 gap-4 border-t border-b border-gray-150 py-4">
              <div className="bg-gray-50 p-4 border border-gray-200 rounded-xl">
                <span className="text-xs text-gray-400 font-semibold block uppercase">Available Balance</span>
                <span className="text-2xl font-bold text-gray-900 block mt-1">
                  {formatCurrency(selectedWallet.balance)}
                </span>
              </div>
              <div className="bg-gray-50 p-4 border border-gray-200 rounded-xl">
                <span className="text-xs text-gray-400 font-semibold block uppercase">Escrow / Pending</span>
                <span className="text-2xl font-bold text-amber-600 block mt-1">
                  {formatCurrency(selectedWallet.pending_balance)}
                </span>
              </div>
            </div>

            {/* Associated user details */}
            <div className="space-y-3">
              <h5 className="text-xs font-bold uppercase text-gray-400 tracking-wider">Account Ownership</h5>
              <div className="bg-gray-50 rounded-xl p-4 border border-gray-200/50 space-y-2 text-sm">
                <div className="flex justify-between">
                  <span className="text-gray-500 font-medium">User Name</span>
                  <span className="text-gray-900 font-semibold">{selectedWallet.user_name || `User ID #${selectedWallet.user_id}`}</span>
                </div>
                {selectedWallet.user_email && (
                  <div className="flex justify-between">
                    <span className="text-gray-500 font-medium">Email Address</span>
                    <span className="text-gray-900 font-semibold">{selectedWallet.user_email}</span>
                  </div>
                )}
                <div className="flex justify-between">
                  <span className="text-gray-500 font-medium">Wallet Created</span>
                  <span className="text-gray-900 font-semibold">
                    {new Date(selectedWallet.created_at).toLocaleString()}
                  </span>
                </div>
              </div>
            </div>
          </div>
        )}
      </DetailDialog>
    </div>
  )
}
export default WalletManagementPage
