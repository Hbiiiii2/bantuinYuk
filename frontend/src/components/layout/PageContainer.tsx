import { cn } from '@/lib/utils'

interface PageContainerProps {
  children: React.ReactNode
  className?: string
  noPadding?: boolean
}

export function PageContainer({ children, className, noPadding = false }: PageContainerProps) {
  return (
    <div className={cn(
      "min-h-[calc(100vh-3.5rem)] lg:min-h-[calc(100vh-3.5rem)]",
      !noPadding && "p-4 lg:p-6",
      className
    )}>
      {children}
    </div>
  )
}
