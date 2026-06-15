import { ArrowLeft } from 'lucide-react'
import { useNavigate } from 'react-router-dom'
import { cn } from '@/lib/utils'

interface PageHeaderProps {
  title: string
  subtitle?: string
  showBack?: boolean
  onBack?: () => void
  actions?: React.ReactNode
  className?: string
}

export function PageHeader({ 
  title, 
  subtitle, 
  showBack = false, 
  onBack,
  actions,
  className 
}: PageHeaderProps) {
  const navigate = useNavigate()
  
  const handleBack = () => {
    if (onBack) {
      onBack()
    } else {
      navigate(-1)
    }
  }
  
  return (
    <div className={cn("flex items-start justify-between gap-4 mb-4 lg:mb-6", className)}>
      <div className="flex items-center gap-3">
        {showBack && (
          <button
            onClick={handleBack}
            className="p-2 text-gray-600 hover:bg-gray-100 rounded-lg -ml-2"
            aria-label="Go back"
          >
            <ArrowLeft size={20} />
          </button>
        )}
        <div>
          <h1 className="text-xl lg:text-2xl font-semibold text-gray-900">{title}</h1>
          {subtitle && (
            <p className="text-sm text-gray-500 mt-0.5">{subtitle}</p>
          )}
        </div>
      </div>
      
      {actions && (
        <div className="flex items-center gap-2">
          {actions}
        </div>
      )}
    </div>
  )
}
