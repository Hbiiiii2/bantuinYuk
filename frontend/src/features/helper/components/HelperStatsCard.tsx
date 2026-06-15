import { Card, CardContent } from '@/components/ui/Card'
import { formatCurrency } from '@/lib/utils'
import { cn } from '@/lib/utils'

interface HelperStatsCardProps {
  stats: {
    completed_tasks: number
    current_tasks: number
    rating: number
    total_earnings: number
  }
  className?: string
}

export function HelperStatsCard({ stats, className }: HelperStatsCardProps) {
  return (
    <div className={cn("grid grid-cols-2 gap-3", className)}>
      <Card>
        <CardContent className="p-3 text-center">
          <p className="text-2xl font-bold text-gray-900">{stats.completed_tasks}</p>
          <p className="text-xs text-gray-500">Completed</p>
        </CardContent>
      </Card>
      
      <Card>
        <CardContent className="p-3 text-center">
          <p className="text-2xl font-bold text-gray-900">{stats.current_tasks}</p>
          <p className="text-xs text-gray-500">Current</p>
        </CardContent>
      </Card>
      
      <Card>
        <CardContent className="p-3 text-center">
          <p className="text-2xl font-bold text-warning">★ {stats.rating.toFixed(1)}</p>
          <p className="text-xs text-gray-500">Rating</p>
        </CardContent>
      </Card>
      
      <Card>
        <CardContent className="p-3 text-center">
          <p className="text-lg font-bold text-success">{formatCurrency(stats.total_earnings)}</p>
          <p className="text-xs text-gray-500">Earnings</p>
        </CardContent>
      </Card>
    </div>
  )
}
