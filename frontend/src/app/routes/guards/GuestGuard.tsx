import { Navigate, Outlet } from 'react-router-dom'
import { useAuthStore } from '@/stores/auth.store'

export function GuestGuard() {
  const { isAuthenticated, user } = useAuthStore()
  
  if (isAuthenticated && user) {
    const dashboardPath = getDashboardPath(user.role)
    return <Navigate to={dashboardPath} replace />
  }
  
  return <Outlet />
}

function getDashboardPath(role: string): string {
  switch (role) {
    case 'helper':
      return '/helper/dashboard'
    case 'admin':
      return '/admin/dashboard'
    default:
      return '/user/dashboard'
  }
}
