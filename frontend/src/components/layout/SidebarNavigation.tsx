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
  Tag,
  X,
  Settings
} from 'lucide-react'
import { cn } from '@/lib/utils'

const iconMap: Record<string, React.ComponentType<{ size?: number; className?: string }>> = {
  LayoutDashboard,
  Users,
  UserCheck,
  ClipboardList,
  AlertTriangle,
  CreditCard,
  Wallet,
  Star,
  Tag,
  Settings
}

export interface SidebarItem {
  icon: string
  label: string
  path: string
}

interface SidebarNavigationProps {
  items: SidebarItem[]
  isOpen: boolean
  onClose: () => void
}

export function SidebarNavigation({ items, isOpen, onClose }: SidebarNavigationProps) {
  const location = useLocation()
  const navigate = useNavigate()
  
  const handleItemClick = (path: string) => {
    navigate(path)
    onClose()
  }
  
  return (
    <>
      {/* Mobile overlay */}
      {isOpen && (
        <div 
          className="fixed inset-0 bg-black/50 z-40 lg:hidden"
          onClick={onClose}
          aria-hidden="true"
        />
      )}
      
      {/* Sidebar */}
      <aside
        className={cn(
          "fixed left-0 top-0 bottom-0 w-64 bg-white border-r border-gray-200 z-50 transition-transform duration-300",
          "lg:translate-x-0",
          isOpen ? "translate-x-0" : "-translate-x-full"
        )}
      >
        {/* Logo */}
        <div className="flex items-center justify-between h-14 px-4 border-b border-gray-200">
          <div>
            <h1 className="text-xl font-bold text-primary">BantuinYuk</h1>
            <p className="text-xs text-gray-500">Admin Dashboard</p>
          </div>
          <button
            onClick={onClose}
            className="p-2 text-gray-500 hover:bg-gray-100 rounded-lg lg:hidden"
            aria-label="Close menu"
          >
            <X size={20} />
          </button>
        </div>
        
        {/* Navigation */}
        <nav className="flex-1 p-4 space-y-1 overflow-y-auto h-[calc(100%-3.5rem-3rem)]">
          {items.map((item) => {
            const Icon = iconMap[item.icon]
            const isActive = location.pathname === item.path
            
            return (
              <button
                key={item.path}
                onClick={() => handleItemClick(item.path)}
                className={cn(
                  "w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors",
                  isActive 
                    ? "bg-primary text-white" 
                    : "text-gray-600 hover:bg-gray-100"
                )}
                aria-current={isActive ? 'page' : undefined}
              >
                {Icon && <Icon size={18} />}
                <span>{item.label}</span>
              </button>
            )
          })}
        </nav>
        
        {/* Footer */}
        <div className="p-4 border-t border-gray-200">
          <p className="text-xs text-gray-400 text-center">v1.0.0</p>
        </div>
      </aside>
    </>
  )
}
