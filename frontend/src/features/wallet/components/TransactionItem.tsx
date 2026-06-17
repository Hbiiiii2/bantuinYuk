import { ArrowUpRight, ArrowDownLeft, RotateCcw, AlertTriangle } from 'lucide-react'
import { formatCurrency, formatDateTime } from '@/lib/utils'
import { Badge } from '@/components/ui/Badge'
import type { Transaction } from '../types/wallet.types'

interface TransactionItemProps {
  transaction: Transaction
}

export function TransactionItem({ transaction }: TransactionItemProps) {
  // Withdrawals are negative (money leaving wallet), others are positive (credits)
  const isCredit = transaction.type !== 'withdraw'
  
  const iconConfig = {
    task_payment: {
      icon: ArrowUpRight,
      bg: 'bg-success/10 text-success'
    },
    withdraw: {
      icon: ArrowDownLeft,
      bg: 'bg-danger/10 text-danger'
    },
    refund: {
      icon: RotateCcw,
      bg: 'bg-warning/10 text-warning'
    },
    adjustment: {
      icon: AlertTriangle,
      bg: 'bg-gray-100 text-gray-600'
    }
  }

  const statusColors = {
    pending: 'warning' as const,
    completed: 'success' as const,
    cancelled: 'danger' as const
  }

  const { icon: Icon, bg } = iconConfig[transaction.type] || iconConfig.adjustment
  
  return (
    <div className="flex items-center gap-3 py-3 border-b border-gray-100 last:border-0">
      <div className={`w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 ${bg}`}>
        <Icon size={18} />
      </div>
      
      <div className="flex-1 min-w-0">
        <div className="flex items-center justify-between gap-2">
          <p className="text-sm font-semibold text-gray-900 truncate">
            {transaction.description || 'Transaction'}
          </p>
          <p className={`text-sm font-bold flex-shrink-0 ${isCredit ? 'text-success' : 'text-danger'}`}>
            {isCredit ? '+' : '-'}{formatCurrency(transaction.amount)}
          </p>
        </div>
        
        <div className="flex items-center justify-between gap-2 mt-1">
          <p className="text-xs text-gray-400">
            {formatDateTime(transaction.created_at)} • Ref: {transaction.reference_id}
          </p>
          <Badge variant={statusColors[transaction.status]} className="text-[10px] py-0 px-1.5 uppercase font-semibold">
            {transaction.status}
          </Badge>
        </div>
      </div>
    </div>
  )
}
