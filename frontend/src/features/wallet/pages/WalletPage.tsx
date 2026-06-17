import { useState } from 'react'
import { PageHeader } from '@/components/layout/PageHeader'
import { BalanceCard } from '../components/BalanceCard'
import { WalletCard } from '../components/WalletCard'
import { TransactionList } from '../components/TransactionList'
import { WithdrawDialog } from '../components/WithdrawDialog'
import { useWalletSummary } from '../hooks/useWallet'
import { Button } from '@/components/ui/Button'
import { SkeletonCard } from '@/components/shared/SkeletonCard'
import { ErrorState } from '@/components/shared/ErrorState'
import { Download } from 'lucide-react'

export function WalletPage() {
  const [isWithdrawOpen, setIsWithdrawOpen] = useState(false)
  const { data: summary, isLoading, error, refetch } = useWalletSummary()
  
  if (isLoading) {
    return (
      <div className="space-y-4">
        <PageHeader title="Wallet" subtitle="Manage your balance and transactions" />
        <SkeletonCard />
      </div>
    )
  }
  
  if (error || !summary) {
    return <ErrorState message="Failed to load wallet data" onRetry={refetch} />
  }

  // The balance that is withdrawable
  const withdrawableAmount = summary.available_balance
  
  return (
    <div className="space-y-6">
      <PageHeader
        title="Wallet"
        subtitle="Manage your balance and transactions"
        actions={
          <Button
            size="sm"
            onClick={() => setIsWithdrawOpen(true)}
            disabled={withdrawableAmount <= 0}
            aria-label="Request Withdraw"
          >
            <Download size={14} className="mr-1" />
            Withdraw
          </Button>
        }
      />
      
      {/* Balance Details */}
      <BalanceCard
        balance={summary.balance}
        availableBalance={withdrawableAmount}
        pendingBalance={summary.pending_balance + summary.pending_withdrawals}
      />
      
      {/* Historical Earnings Stats */}
      <WalletCard
        totalEarned={summary.total_earned}
        totalWithdrawn={summary.total_withdrawn}
        totalRefunded={summary.total_refunded}
      />
      
      {/* Withdraw Action (Mobile view) */}
      <div className="lg:hidden">
        <Button
          className="w-full min-h-[44px]"
          onClick={() => setIsWithdrawOpen(true)}
          disabled={withdrawableAmount <= 0}
          aria-label="Request Withdraw"
        >
          <Download size={14} className="mr-1" />
          Request Withdraw
        </Button>
      </div>

      {/* Transaction History */}
      <div>
        <h3 className="font-semibold text-gray-900 mb-3 text-lg">Transaction History</h3>
        <TransactionList />
      </div>

      {/* Withdraw Dialog Modal */}
      <WithdrawDialog
        availableBalance={withdrawableAmount}
        isOpen={isWithdrawOpen}
        onClose={() => setIsWithdrawOpen(false)}
      />
    </div>
  )
}
