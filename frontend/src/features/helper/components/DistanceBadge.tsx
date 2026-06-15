import { MapPin } from 'lucide-react'
import { Badge } from '@/components/ui/Badge'
import { cn } from '@/lib/utils'

interface DistanceBadgeProps {
  distance?: number
  className?: string
}

export function DistanceBadge({ distance, className }: DistanceBadgeProps) {
  if (distance === undefined || distance === null) return null
  
  const formatDistance = (km: number): string => {
    if (km < 1) return `${Math.round(km * 1000)}m`
    return `${km.toFixed(1)}km`
  }
  
  return (
    <Badge variant="default" className={cn("flex items-center gap-1", className)}>
      <MapPin size={12} />
      {formatDistance(distance)}
    </Badge>
  )
}
