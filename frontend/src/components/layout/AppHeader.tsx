import { Menu } from 'lucide-react'
import { NotificationBadge } from './NotificationBadge'
import { UserMenu } from './UserMenu'

interface AppHeaderProps {
  showMenu?: boolean
  onMenuClick?: () => void
}

export function AppHeader({ showMenu = false, onMenuClick }: AppHeaderProps) {
  return (
    <header className="sticky top-0 z-40 bg-white border-b border-gray-200">
      <div className="flex items-center justify-between h-14 px-4">
        <div className="flex items-center gap-3">
          {showMenu && (
            <button
              onClick={onMenuClick}
              className="p-2 text-gray-600 hover:bg-gray-100 rounded-lg lg:hidden"
              aria-label="Toggle menu"
            >
              <Menu size={20} />
            </button>
          )}
          <h1 className="text-lg font-semibold text-gray-900">BantuinYuk</h1>
        </div>
        
        <div className="flex items-center gap-2">
          <NotificationBadge />
          <UserMenu />
        </div>
      </div>
    </header>
  )
}
