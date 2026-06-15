import { useLocation, useNavigate } from 'react-router-dom'
import { 
  Home, 
  List, 
  PlusCircle, 
  Bell, 
  User,
  ClipboardList,
  Search
} from 'lucide-react'
import { cn } from '@/lib/utils'

const iconMap: Record<string, React.ComponentType<{ size?: number }>> = {
  Home,
  List,
  PlusCircle,
  Bell,
  User,
  ClipboardList,
  Search
}

interface NavItem {
  icon: string
  label: string
  path: string
}

interface BottomNavProps {
  items: NavItem[]
}

export function BottomNav({ items }: BottomNavProps) {
  const location = useLocation()
  const navigate = useNavigate()
  
  return (
    <nav className="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-40">
      <div className="flex items-center justify-around py-2">
        {items.map((item) => {
          const Icon = iconMap[item.icon]
          const isActive = location.pathname === item.path
          
          return (
            <button
              key={item.path}
              onClick={() => navigate(item.path)}
              className={cn(
                "flex flex-col items-center gap-1 px-3 py-1 min-w-[60px]",
                isActive ? "text-primary" : "text-gray-500"
              )}
            >
              {Icon && <Icon size={20} />}
              <span className="text-xs font-medium">{item.label}</span>
            </button>
          )
        })}
      </div>
    </nav>
  )
}
