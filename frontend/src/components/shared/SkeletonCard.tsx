import { cn } from '@/lib/utils'

interface SkeletonCardProps {
  className?: string
}

export function SkeletonCard({ className }: SkeletonCardProps) {
  return (
    <div className={cn("rounded-xl bg-white border border-gray-200 p-4", className)}>
      <div className="animate-pulse space-y-3">
        <div className="flex items-center justify-between">
          <div className="h-4 bg-gray-200 rounded w-1/3"></div>
          <div className="h-5 bg-gray-200 rounded-full w-16"></div>
        </div>
        <div className="h-3 bg-gray-200 rounded w-2/3"></div>
        <div className="flex gap-2">
          <div className="h-3 bg-gray-200 rounded w-20"></div>
          <div className="h-3 bg-gray-200 rounded w-16"></div>
        </div>
      </div>
    </div>
  )
}

export function SkeletonList({ count = 3 }: { count?: number }) {
  return (
    <div className="space-y-3">
      {Array.from({ length: count }).map((_, i) => (
        <SkeletonCard key={i} />
      ))}
    </div>
  )
}

export function SkeletonTable({ rows = 5, cols = 4 }: { rows?: number; cols?: number }) {
  return (
    <div className="rounded-xl bg-white border border-gray-200 overflow-hidden">
      <div className="animate-pulse">
        <div className="h-12 bg-gray-100 border-b border-gray-200 flex items-center px-4 gap-4">
          {Array.from({ length: cols }).map((_, i) => (
            <div key={i} className="h-3 bg-gray-300 rounded flex-1"></div>
          ))}
        </div>
        {Array.from({ length: rows }).map((_, i) => (
          <div key={i} className="h-14 border-b border-gray-100 flex items-center px-4 gap-4">
            {Array.from({ length: cols }).map((_, j) => (
              <div key={j} className="h-3 bg-gray-200 rounded flex-1"></div>
            ))}
          </div>
        ))}
      </div>
    </div>
  )
}
