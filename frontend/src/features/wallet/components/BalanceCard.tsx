import { Card, CardContent } from '@/components/ui/Card'
import { formatCurrency } from '@/lib/utils'

interface BalanceCardProps {
  balance: number
  availableBalance: number
  pendingBalance: number
}

export function BalanceCard({ balance, availableBalance, pendingBalance }: BalanceCardProps) {
  return (
    <Card className="bg-primary text-white overflow-hidden relative border-none">
      <div className="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -mr-8 -mt-8" />
      <CardContent className="p-6 relative">
        <p className="text-white/85 text-xs font-medium uppercase tracking-wider">Total Balance</p>
        <h2 className="text-3xl font-bold mt-1 tracking-tight">{formatCurrency(balance)}</h2>
        
        <div className="grid grid-cols-2 gap-4 mt-6 pt-4 border-t border-white/10 text-sm">
          <div>
            <p className="text-white/70 text-xs">Available Balance</p>
            <p className="font-semibold text-base mt-0.5">{formatCurrency(availableBalance)}</p>
          </div>
          <div>
            <p className="text-white/70 text-xs">Pending / Escrow</p>
            <p className="font-semibold text-base mt-0.5">{formatCurrency(pendingBalance)}</p>
          </div>
        </div>
      </CardContent>
    </Card>
  )
}
