import { Badge } from '@/components/ui/Badge'
import { Shield, ShieldCheck, ShieldX } from 'lucide-react'
import { cn } from '@/lib/utils'

interface VerificationBadgeProps {
  status: 'pending' | 'verified' | 'rejected'
  className?: string
}

const statusConfig = {
  pending: { 
    label: 'Pending Verification', 
    variant: 'warning' as const,
    icon: Shield
  },
  verified: { 
    label: 'Verified', 
    variant: 'success' as const,
    icon: ShieldCheck
  },
  rejected: { 
    label: 'Rejected', 
    variant: 'danger' as const,
    icon: ShieldX
  }
}

export function VerificationBadge({ status, className }: VerificationBadgeProps) {
  const config = statusConfig[status]
  const Icon = config.icon
  
  return (
    <Badge variant={config.variant} className={cn("flex items-center gap-1", className)}>
      <Icon size={12} />
      {config.label}
    </Badge>
  )
}
