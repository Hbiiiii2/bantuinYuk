import { Bell, User } from 'lucide-react'
import { useAuthStore } from '@/stores/auth.store'
import { getInitials } from '@/lib/utils'

export function Header() {
  const { user } = useAuthStore()
  
  return (
    <header className="sticky top-0 z-40 bg-white border-b border-gray-200 px-4 py-3">
      <div className="flex items-center justify-between">
        <h1 className="text-lg font-semibold text-gray-900">BantuinYuk</h1>
        
        <div className="flex items-center gap-3">
          <button className="relative p-2 text-gray-600 hover:bg-gray-100 rounded-full">
            <Bell size={20} />
            <span className="absolute top-1 right-1 w-2 h-2 bg-danger rounded-full"></span>
          </button>
          
          <div className="flex items-center gap-2">
            {user?.photo ? (
              <img 
                src={user.photo} 
                alt={user.name}
                className="w-8 h-8 rounded-full object-cover"
              />
            ) : (
              <div className="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white text-sm font-medium">
                {user?.name ? getInitials(user.name) : <User size={16} />}
              </div>
            )}
          </div>
        </div>
      </div>
    </header>
  )
}
