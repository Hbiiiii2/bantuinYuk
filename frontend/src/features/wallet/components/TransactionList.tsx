import { useState } from 'react'
import { TransactionItem } from './TransactionItem'
import { useTransactions } from '../hooks/useWallet'
import { Button } from '@/components/ui/Button'
import { Card, CardContent } from '@/components/ui/Card'
import { SkeletonList } from '@/components/shared/SkeletonCard'
import { EmptyState } from '@/components/shared/EmptyState'
import { ErrorState } from '@/components/shared/ErrorState'
import { Wallet } from 'lucide-react'
import { cn } from '@/lib/utils'

export function TransactionList() {
  const [filterType, setFilterType] = useState<string>('all')
  const [page, setPage] = useState(1)
  
  const { data, isLoading, error, refetch } = useTransactions({
    page,
    per_page: 10,
    type: filterType === 'all' ? undefined : filterType
  })
  
  const filters = [
    { label: 'All', value: 'all' },
    { label: 'Payments', value: 'task_payment' },
    { label: 'Withdraws', value: 'withdraw' },
    { label: 'Refunds', value: 'refund' },
    { label: 'Adjustments', value: 'adjustment' }
  ]
  
  if (error) {
    return <ErrorState message="Failed to load transaction history" onRetry={refetch} />
  }
  
  return (
    <div className="space-y-4">
      {/* Filters */}
      <div className="flex items-center gap-2 overflow-x-auto pb-1" aria-label="Transaction Type Filters">
        {filters.map((f) => (
          <button
            key={f.value}
            onClick={() => {
              setFilterType(f.value)
              setPage(1)
            }}
            className={cn(
              "px-3 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-colors min-h-[44px] min-w-[60px]",
              filterType === f.value
                ? "bg-primary text-white"
                : "bg-gray-100 text-gray-600 hover:bg-gray-200"
            )}
            aria-current={filterType === f.value ? 'true' : undefined}
          >
            {f.label}
          </button>
        ))}
      </div>
      
      {/* Transactions */}
      {isLoading ? (
        <SkeletonList count={4} />
      ) : data?.data && data.data.length > 0 ? (
        <div className="space-y-4">
          <Card>
            <CardContent className="divide-y divide-gray-100 p-0 px-4">
              {data.data.map((tx) => (
                <TransactionItem key={tx.id} transaction={tx} />
              ))}
            </CardContent>
          </Card>
          
          {/* Pagination */}
          {data.total > 10 && (
            <div className="flex items-center justify-center gap-3 py-2">
              <Button
                variant="secondary"
                size="sm"
                disabled={page === 1}
                onClick={() => setPage(page - 1)}
                aria-label="Previous Page"
                className="min-h-[44px]"
              >
                Previous
              </Button>
              <span className="text-xs text-gray-500 font-medium">
                Page {page} of {Math.ceil(data.total / 10)}
              </span>
              <Button
                variant="secondary"
                size="sm"
                disabled={page >= Math.ceil(data.total / 10)}
                onClick={() => setPage(page + 1)}
                aria-label="Next Page"
                className="min-h-[44px]"
              >
                Next
              </Button>
            </div>
          )}
        </div>
      ) : (
        <EmptyState
          icon={<Wallet size={48} />}
          title="No transactions"
          description={filterType !== 'all' ? "Try changing your filter to view other transactions." : "You haven't made any transactions yet."}
        />
      )}
    </div>
  )
}
