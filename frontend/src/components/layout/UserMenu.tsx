import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { User, LogOut, ChevronDown } from 'lucide-react'
import { useAuthStore } from '@/stores/auth.store'
import { getInitials } from '@/lib/utils'

export function UserMenu() {
  const [isOpen, setIsOpen] = useState(false)
  const navigate = useNavigate()
  const { user, logout } = useAuthStore()
  
  const handleLogout = async () => {
    await logout()
    navigate('/login', { replace: true })
  }
  
  const getProfilePath = () => {
    if (!user) return '/login'
    switch (user.role) {
      case 'helper':
        return '/helper/profile'
      case 'admin':
        return '/admin/settings'
      default:
        return '/user/profile'
    }
  }
  
  if (!user) return null
  
  return (
    <div className="relative">
      <button
        onClick={() => setIsOpen(!isOpen)}
        className="flex items-center gap-2 p-1 hover:bg-gray-100 rounded-lg"
        aria-label="User menu"
        aria-expanded={isOpen}
      >
        {user.photo ? (
          <img 
            src={user.photo} 
            alt={user.name}
            className="w-8 h-8 rounded-full object-cover"
          />
        ) : (
          <div className="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white text-sm font-medium">
            {getInitials(user.name)}
          </div>
        )}
        <ChevronDown size={14} className="text-gray-500 hidden sm:block" />
      </button>
      
      {isOpen && (
        <>
          <div 
            className="fixed inset-0 z-40" 
            onClick={() => setIsOpen(false)}
          />
          <div className="absolute right-0 top-full mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
            <div className="p-3 border-b border-gray-100">
              <p className="font-medium text-gray-900 text-sm">{user.name}</p>
              <p className="text-xs text-gray-500">{user.email}</p>
              <span className="inline-block mt-1 px-2 py-0.5 text-xs font-medium bg-primary-light text-primary rounded-full capitalize">
                {user.role}
              </span>
            </div>
            
            <div className="p-1">
              <button
                onClick={() => {
                  navigate(getProfilePath())
                  setIsOpen(false)
                }}
                className="w-full flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md"
              >
                <User size={16} />
                <span>Profile</span>
              </button>
              
              <button
                onClick={handleLogout}
                className="w-full flex items-center gap-2 px-3 py-2 text-sm text-danger hover:bg-danger-light rounded-md"
              >
                <LogOut size={16} />
                <span>Logout</span>
              </button>
            </div>
          </div>
        </>
      )}
    </div>
  )
}
