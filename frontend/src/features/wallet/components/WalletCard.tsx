import { Card, CardContent } from '@/components/ui/Card'
import { formatCurrency } from '@/lib/utils'
import { TrendingUp, ArrowDownCircle, RotateCcw } from 'lucide-react'

interface WalletCardProps {
  totalEarned: number
  totalWithdrawn: number
  totalRefunded: number
}

export function WalletCard({ totalEarned, totalWithdrawn, totalRefunded }: WalletCardProps) {
  return (
    <div className="grid grid-cols-3 gap-3">
      <Card>
        <CardContent className="p-3 text-center flex flex-col items-center justify-center">
          <div className="w-8 h-8 rounded-full bg-success/10 text-success flex items-center justify-center mb-1">
            <TrendingUp size={16} />
          </div>
          <p className="text-sm font-bold text-gray-900 truncate w-full">{formatCurrency(totalEarned)}</p>
          <p className="text-[9px] text-gray-500 uppercase tracking-wider mt-0.5">Earned</p>
        </CardContent>
      </Card>
      
      <Card>
        <CardContent className="p-3 text-center flex flex-col items-center justify-center">
          <div className="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center mb-1">
            <ArrowDownCircle size={16} />
          </div>
          <p className="text-sm font-bold text-gray-900 truncate w-full">{formatCurrency(totalWithdrawn)}</p>
          <p className="text-[9px] text-gray-500 uppercase tracking-wider mt-0.5">Withdrawn</p>
        </CardContent>
      </Card>
      
      <Card>
        <CardContent className="p-3 text-center flex flex-col items-center justify-center">
          <div className="w-8 h-8 rounded-full bg-warning/10 text-warning flex items-center justify-center mb-1">
            <RotateCcw size={16} />
          </div>
          <p className="text-sm font-bold text-gray-900 truncate w-full">{formatCurrency(totalRefunded)}</p>
          <p className="text-[9px] text-gray-500 uppercase tracking-wider mt-0.5">Refunded</p>
        </CardContent>
      </Card>
    </div>
  )
}
