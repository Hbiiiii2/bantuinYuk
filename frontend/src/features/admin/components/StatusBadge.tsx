import { cn } from '@/lib/utils'

interface StatusBadgeProps {
  status: string | number
  className?: string
}

export function StatusBadge({ status, className }: StatusBadgeProps) {
  const normStatus = String(status).toLowerCase()

  const styles: Record<string, string> = {
    // Users active status
    '1': 'bg-emerald-50 text-emerald-700 border-emerald-200', // active
    '0': 'bg-rose-50 text-rose-700 border-rose-200', // suspended
    'active': 'bg-emerald-50 text-emerald-700 border-emerald-200',
    'suspended': 'bg-rose-50 text-rose-700 border-rose-200',

    // Helper KTP status
    'verified': 'bg-emerald-50 text-emerald-700 border-emerald-200',
    'pending': 'bg-amber-50 text-amber-700 border-amber-200',
    'rejected': 'bg-rose-50 text-rose-700 border-rose-200',

    // Disputes status
    'open': 'bg-blue-50 text-blue-700 border-blue-200',
    'under_review': 'bg-purple-50 text-purple-700 border-purple-200',
    'resolved': 'bg-emerald-50 text-emerald-700 border-emerald-200',

    // Task status
    'draft': 'bg-gray-50 text-gray-700 border-gray-200',
    'accepted': 'bg-indigo-50 text-indigo-700 border-indigo-200',
    'in_progress': 'bg-indigo-50 text-indigo-700 border-indigo-200',
    'waiting_approval': 'bg-amber-50 text-amber-700 border-amber-200',
    'completed': 'bg-emerald-50 text-emerald-700 border-emerald-200',
    'cancelled': 'bg-rose-50 text-rose-700 border-rose-200',

    // Transaction status
    'completed_txn': 'bg-emerald-50 text-emerald-700 border-emerald-200',
    'failed': 'bg-rose-50 text-rose-700 border-rose-200',
  }

  const labelMap: Record<string, string> = {
    '1': 'Active',
    '0': 'Suspended',
    'active': 'Active',
    'suspended': 'Suspended',
    'verified': 'Verified',
    'pending': 'Pending',
    'rejected': 'Rejected',
    'open': 'Open',
    'under_review': 'Under Review',
    'resolved': 'Resolved',
    'draft': 'Draft',
    'accepted': 'Accepted',
    'in_progress': 'In Progress',
    'waiting_approval': 'Waiting Approval',
    'completed': 'Completed',
    'cancelled': 'Cancelled',
    'failed': 'Failed',
  }

  const matchedStyle = styles[normStatus] || 'bg-gray-50 text-gray-600 border-gray-200'
  const displayLabel = labelMap[normStatus] || status

  return (
    <span
      className={cn(
        "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border",
        matchedStyle,
        className
      )}
    >
      {displayLabel}
    </span>
  )
}
