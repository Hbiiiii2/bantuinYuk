import { Navigate, Outlet } from 'react-router-dom'
import { useAuthStore } from '@/stores/auth.store'
import type { UserRole } from '@/types'

interface RoleGuardProps {
  allowedRoles: UserRole[]
}

export function RoleGuard({ allowedRoles }: RoleGuardProps) {
  const { user, isAuthenticated } = useAuthStore()
  
  if (!isAuthenticated || !user) {
    return <Navigate to="/login" replace />
  }
  
  if (!allowedRoles.includes(user.role)) {
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
