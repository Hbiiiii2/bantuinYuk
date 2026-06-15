import { useEffect } from 'react'
import { useNavigate } from 'react-router-dom'
import { useAuthStore } from '@/stores/auth.store'
import type { UserRole } from '@/types'

export function useAuth() {
  const store = useAuthStore()
  const navigate = useNavigate()
  
  const isUser = store.user?.role === 'user'
  const isHelper = store.user?.role === 'helper'
  const isAdmin = store.user?.role === 'admin'
  
  const requireAuth = (allowedRoles?: UserRole[]) => {
    useEffect(() => {
      if (!store.loading && !store.initialized) {
        return
      }
      
      if (!store.loading && !store.isAuthenticated) {
        navigate('/login', { replace: true })
        return
      }
      
      if (allowedRoles && store.user && !allowedRoles.includes(store.user.role)) {
        navigate(store.getDashboardPath(), { replace: true })
      }
    }, [store.loading, store.initialized, store.isAuthenticated, store.user])
  }
  
  const redirectIfAuthenticated = () => {
    useEffect(() => {
      if (store.isAuthenticated) {
        navigate(store.getDashboardPath(), { replace: true })
      }
    }, [store.isAuthenticated])
  }
  
  return {
    ...store,
    isUser,
    isHelper,
    isAdmin,
    requireAuth,
    redirectIfAuthenticated
  }
}
