import { useState } from 'react'
import { Outlet } from 'react-router-dom'
import { AppHeader } from '@/components/layout/AppHeader'
import { SidebarNavigation, type SidebarItem } from '@/components/layout/SidebarNavigation'
import { PageContainer } from '@/components/layout/PageContainer'

const adminNavItems: SidebarItem[] = [
  { icon: 'LayoutDashboard', label: 'Dashboard', path: '/admin/dashboard' },
  { icon: 'Users', label: 'Users', path: '/admin/users' },
  { icon: 'UserCheck', label: 'Helpers', path: '/admin/helpers' },
  { icon: 'ClipboardList', label: 'Tasks', path: '/admin/tasks' },
  { icon: 'AlertTriangle', label: 'Disputes', path: '/admin/disputes' },
  { icon: 'CreditCard', label: 'Transactions', path: '/admin/transactions' },
  { icon: 'Wallet', label: 'Wallets', path: '/admin/wallets' },
  { icon: 'Star', label: 'Reviews', path: '/admin/reviews' },
  { icon: 'Tag', label: 'Categories', path: '/admin/categories' }
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
            <Outlet />
          </PageContainer>
        </main>
      </div>
    </div>
  )
}
