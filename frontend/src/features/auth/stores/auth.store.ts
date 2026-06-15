import { create } from 'zustand'
import { persist } from 'zustand/middleware'
import type { User, UserRole } from '@/types'
import { authService } from '../services/auth.service'

interface AuthState {
  user: User | null
  token: string | null
  isAuthenticated: boolean
  loading: boolean
  initialized: boolean
}

interface AuthActions {
  login: (email: string, password: string) => Promise<void>
  register: (name: string, email: string, phone: string, password: string) => Promise<void>
  logout: () => Promise<void>
  setUser: (user: User) => void
  clearAuth: () => void
  hydrate: () => Promise<void>
  isRole: (role: UserRole) => boolean
  getDashboardPath: () => string
}

export type AuthStore = AuthState & AuthActions

const initialState: AuthState = {
  user: null,
  token: null,
  isAuthenticated: false,
  loading: false,
  initialized: false
}

export const useAuthStore = create<AuthStore>()(
  persist(
    (set, get) => ({
      ...initialState,
      
      login: async (email: string, password: string) => {
        set({ loading: true })
        try {
          const result = await authService.login({ email, password })
          set({
            user: result.user,
            token: result.token.access_token,
            isAuthenticated: true,
            loading: false
          })
        } catch (error) {
          set({ loading: false })
          throw error
        }
      },
      
      register: async (name: string, email: string, phone: string, password: string) => {
        set({ loading: true })
        try {
          await authService.register({ name, email, phone, password })
          set({ loading: false })
        } catch (error) {
          set({ loading: false })
          throw error
        }
      },
      
      logout: async () => {
        const { token } = get()
        if (token) {
          await authService.logout()
        }
        set({
          user: null,
          token: null,
          isAuthenticated: false,
          loading: false
        })
      },
      
      setUser: (user: User) => {
        set({ user })
      },
      
      clearAuth: () => {
        set({
          user: null,
          token: null,
          isAuthenticated: false,
          loading: false
        })
      },
      
      hydrate: async () => {
        const { token, initialized } = get()
        
        if (initialized) {
          return
        }
        
        if (!token) {
          set({ initialized: true, loading: false })
          return
        }
        
        set({ loading: true })
        try {
          const user = await authService.getCurrentUser()
          set({
            user,
            isAuthenticated: true,
            loading: false,
            initialized: true
          })
        } catch {
          set({
            user: null,
            token: null,
            isAuthenticated: false,
            loading: false,
            initialized: true
          })
        }
      },
      
      isRole: (role: UserRole) => {
        const { user } = get()
        return user?.role === role
      },
      
      getDashboardPath: () => {
        const { user } = get()
        if (!user) return '/login'
        
        switch (user.role) {
          case 'helper':
            return '/helper/dashboard'
          case 'admin':
            return '/admin/dashboard'
          default:
            return '/user/dashboard'
        }
      }
    }),
    {
      name: 'bantuin-auth',
      partialize: (state) => ({
        user: state.user,
        token: state.token,
        isAuthenticated: state.isAuthenticated
      })
    }
  )
)
