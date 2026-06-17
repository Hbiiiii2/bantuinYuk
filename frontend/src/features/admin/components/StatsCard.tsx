import { LucideIcon } from 'lucide-react'

interface StatsCardProps {
  title: string
  value: string | number | undefined
  icon: LucideIcon
  description?: string
  trend?: {
    value: string
    isPositive: boolean
  }
  isLoading?: boolean
}

export function StatsCard({
  title,
  value,
  icon: Icon,
  description,
  trend,
  isLoading = false
}: StatsCardProps) {
  if (isLoading) {
    return (
      <div className="bg-white rounded-xl border border-gray-200 p-6 animate-pulse" aria-hidden="true">
        <div className="flex justify-between items-start mb-4">
          <div className="h-4 bg-gray-200 rounded w-24"></div>
          <div className="h-8 w-8 bg-gray-200 rounded-lg"></div>
        </div>
        <div className="h-8 bg-gray-200 rounded w-20 mb-2"></div>
        <div className="h-3 bg-gray-200 rounded w-32"></div>
      </div>
    )
  }

  return (
    <div className="bg-white rounded-xl border border-gray-200 p-6 flex flex-col justify-between shadow-sm">
      <div className="flex justify-between items-start">
        <span className="text-sm font-medium text-gray-500">{title}</span>
        <div className="p-2 bg-primary/5 text-primary rounded-lg">
          <Icon size={20} />
        </div>
      </div>
      
      <div className="mt-4">
        <h3 className="text-2xl font-bold text-gray-900 leading-none">
          {value !== undefined ? value : '-'}
        </h3>
        
        {trend && (
          <div className="flex items-center gap-1 mt-2">
            <span className={`text-xs font-semibold ${trend.isPositive ? 'text-emerald-600' : 'text-rose-600'}`}>
              {trend.value}
            </span>
            {description && <span className="text-xs text-gray-400">{description}</span>}
          </div>
        )}
        
        {!trend && description && (
          <p className="text-xs text-gray-400 mt-2">{description}</p>
        )}
      </div>
    </div>
  )
}
