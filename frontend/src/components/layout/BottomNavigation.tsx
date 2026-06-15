import { useLocation, useNavigate } from 'react-router-dom'
import { 
  Home, 
  List, 
  PlusCircle, 
  Bell, 
  User,
  ClipboardList,
  Search,
  Wallet
} from 'lucide-react'
import { cn } from '@/lib/utils'

const iconMap: Record<string, React.ComponentType<{ size?: number; className?: string }>> = {
  Home,
  List,
  PlusCircle,
  Bell,
  User,
  ClipboardList,
  Search,
  Wallet
}

export interface NavItem {
  icon: string
  label: string
  path: string
}

interface BottomNavigationProps {
  items: NavItem[]
}

export function BottomNavigation({ items }: BottomNavigationProps) {
  const location = useLocation()
  const navigate = useNavigate()
  
  return (
    <nav className="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-40 lg:hidden">
      <div className="flex items-center justify-around h-16 px-2">
        {items.map((item) => {
          const Icon = iconMap[item.icon]
          const isActive = location.pathname === item.path || 
                          (item.path !== '/user/dashboard' && item.path !== '/helper/dashboard' && 
                           location.pathname.startsWith(item.path))
          
          return (
            <button
              key={item.path}
              onClick={() => navigate(item.path)}
              className={cn(
                "flex flex-col items-center justify-center gap-1 min-w-[60px] py-2 rounded-lg transition-colors",
                isActive 
                  ? "text-primary" 
                  : "text-gray-500 hover:text-gray-700 hover:bg-gray-50"
              )}
              aria-label={item.label}
              aria-current={isActive ? 'page' : undefined}
            >
              {Icon && <Icon size={20} className={cn(isActive && "text-primary")} />}
              <span className={cn(
                "text-xs font-medium",
                isActive ? "text-primary" : "text-gray-500"
              )}>
                {item.label}
              </span>
            </button>
          )
        })}
      </div>
    </nav>
  )
}
