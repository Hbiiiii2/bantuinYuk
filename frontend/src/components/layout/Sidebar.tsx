import { useLocation, useNavigate } from 'react-router-dom'
import {
  LayoutDashboard,
  Users,
  UserCheck,
  ClipboardList,
  AlertTriangle,
  CreditCard,
  Wallet,
  Star,
  Tag
} from 'lucide-react'
import { cn } from '@/lib/utils'

const iconMap: Record<string, React.ComponentType<{ size?: number }>> = {
  LayoutDashboard,
  Users,
  UserCheck,
  ClipboardList,
  AlertTriangle,
  CreditCard,
  Wallet,
  Star,
  Tag
}

interface SidebarItem {
  icon: string
  label: string
  path: string
}

interface SidebarProps {
  items: SidebarItem[]
}

export function Sidebar({ items }: SidebarProps) {
  const location = useLocation()
  const navigate = useNavigate()
  
  return (
    <>
      {/* Mobile sidebar overlay */}
      <div className="lg:hidden fixed inset-0 bg-black/50 z-40 hidden" />
      
      {/* Sidebar */}
      <aside className="fixed left-0 top-0 bottom-0 w-64 bg-white border-r border-gray-200 z-50 hidden lg:flex lg:flex-col">
        <div className="p-4 border-b border-gray-200">
          <h1 className="text-xl font-bold text-primary">BantuinYuk</h1>
          <p className="text-xs text-gray-500">Admin Dashboard</p>
        </div>
        
        <nav className="flex-1 p-4 space-y-1 overflow-y-auto">
          {items.map((item) => {
            const Icon = iconMap[item.icon]
            const isActive = location.pathname === item.path
            
            return (
              <button
                key={item.path}
                onClick={() => navigate(item.path)}
                className={cn(
                  "w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors",
                  isActive 
                    ? "bg-primary text-white" 
                    : "text-gray-600 hover:bg-gray-100"
                )}
              >
                {Icon && <Icon size={18} />}
                <span>{item.label}</span>
              </button>
            )
          })}
        </nav>
        
        <div className="p-4 border-t border-gray-200">
          <p className="text-xs text-gray-400 text-center">v1.0.0</p>
        </div>
      </aside>
    </>
  )
}
