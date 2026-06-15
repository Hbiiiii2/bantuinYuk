import { useLocation, Link } from 'react-router-dom'
import { ChevronRight, Home } from 'lucide-react'
import { cn } from '@/lib/utils'

interface BreadcrumbItem {
  label: string
  path?: string
}

interface BreadcrumbProps {
  items?: BreadcrumbItem[]
  className?: string
}

export function Breadcrumb({ items, className }: BreadcrumbProps) {
  const location = useLocation()
  
  const generateBreadcrumbs = (): BreadcrumbItem[] => {
    if (items) return items
    
    const pathSegments = location.pathname.split('/').filter(Boolean)
    const breadcrumbs: BreadcrumbItem[] = []
    
    pathSegments.forEach((segment, index) => {
      const path = '/' + pathSegments.slice(0, index + 1).join('/')
      const label = segment.charAt(0).toUpperCase() + segment.slice(1).replace(/-/g, ' ')
      breadcrumbs.push({ label, path })
    })
    
    return breadcrumbs
  }
  
  const breadcrumbs = generateBreadcrumbs()
  
  if (breadcrumbs.length <= 1) return null
  
  return (
    <nav aria-label="Breadcrumb" className={cn("mb-4", className)}>
      <ol className="flex items-center gap-1 text-sm">
        <li>
          <Link 
            to="/" 
            className="text-gray-500 hover:text-gray-700 transition-colors"
          >
            <Home size={14} />
          </Link>
        </li>
        
        {breadcrumbs.map((item, index) => (
          <li key={item.path || index} className="flex items-center gap-1">
            <ChevronRight size={12} className="text-gray-400" />
            {item.path && index < breadcrumbs.length - 1 ? (
              <Link 
                to={item.path}
                className="text-gray-500 hover:text-gray-700 transition-colors"
              >
                {item.label}
              </Link>
            ) : (
              <span className="text-gray-900 font-medium">{item.label}</span>
            )}
          </li>
        ))}
      </ol>
    </nav>
  )
}
