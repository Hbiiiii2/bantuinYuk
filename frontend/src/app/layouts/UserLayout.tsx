import { Outlet } from 'react-router-dom'
import { AppHeader } from '@/components/layout/AppHeader'
import { BottomNavigation, type NavItem } from '@/components/layout/BottomNavigation'
import { PageContainer } from '@/components/layout/PageContainer'

const userNavItems: NavItem[] = [
  { icon: 'Home', label: 'Home', path: '/user/dashboard' },
  { icon: 'List', label: 'Tasks', path: '/user/tasks' },
  { icon: 'PlusCircle', label: 'Create', path: '/user/tasks/create' },
  { icon: 'Wallet', label: 'Wallet', path: '/user/wallet' },
  { icon: 'User', label: 'Profile', path: '/user/profile' }
]

export function UserLayout() {
  return (
    <div className="min-h-screen bg-gray-50 flex flex-col">
      <AppHeader />
      <main className="flex-1 pb-16">
        <PageContainer>
          <Outlet />
        </PageContainer>
      </main>
      <BottomNavigation items={userNavItems} />
    </div>
  )
}
