import { useState, Suspense } from 'react'
import { Outlet } from 'react-router-dom'
import { AppHeader } from '@/components/layout/AppHeader'
import { SidebarNavigation, type SidebarItem } from '@/components/layout/SidebarNavigation'
import { PageContainer } from '@/components/layout/PageContainer'
import { ToastContainer } from '@/features/admin/components/ToastContainer'

const adminNavItems: SidebarItem[] = [
  { icon: 'LayoutDashboard', label: 'Dashboard', path: '/admin/dashboard' },
  { icon: 'Users', label: 'Users', path: '/admin/users' },
  { icon: 'UserCheck', label: 'Helpers', path: '/admin/helpers' },
  { icon: 'ClipboardList', label: 'Tasks', path: '/admin/tasks' },
  { icon: 'AlertTriangle', label: 'Disputes', path: '/admin/disputes' },
  { icon: 'CreditCard', label: 'Transactions', path: '/admin/transactions' },
  { icon: 'Wallet', label: 'Wallets', path: '/admin/wallets' },
  { icon: 'Tag', label: 'Analytics', path: '/admin/analytics' }
]

export function AdminLayout() {
  const [sidebarOpen, setSidebarOpen] = useState(false)
  
  return (
    <div className="min-h-screen bg-gray-50 flex">
      <SidebarNavigation 
        items={adminNavItems}
        isOpen={sidebarOpen}
        onClose={() => setSidebarOpen(false)}
      />
      
      <div className="flex-1 flex flex-col lg:ml-64">
        <AppHeader 
          showMenu 
          onMenuClick={() => setSidebarOpen(true)}
        />
        <main className="flex-1">
          <PageContainer>
            <Suspense fallback={<div className="p-8 text-center text-sm font-semibold text-gray-500">Loading page...</div>}>
              <Outlet />
            </Suspense>
          </PageContainer>
        </main>
      </div>

      <ToastContainer />
    </div>
  )
}
