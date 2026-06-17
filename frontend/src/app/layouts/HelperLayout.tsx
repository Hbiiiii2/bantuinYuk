import { Outlet } from 'react-router-dom'
import { AppHeader } from '@/components/layout/AppHeader'
import { BottomNavigation, type NavItem } from '@/components/layout/BottomNavigation'
import { PageContainer } from '@/components/layout/PageContainer'

const helperNavItems: NavItem[] = [
  { icon: 'Home', label: 'Home', path: '/helper/dashboard' },
  { icon: 'Search', label: 'Find', path: '/helper/tasks' },
  { icon: 'ClipboardList', label: 'Tasks', path: '/helper/current-task' },
  { icon: 'Wallet', label: 'Wallet', path: '/helper/wallet' },
  { icon: 'User', label: 'Profile', path: '/helper/profile' }
]

export function HelperLayout() {
  return (
    <div className="min-h-screen bg-gray-50 flex flex-col">
      <AppHeader />
      <main className="flex-1 pb-16">
        <PageContainer>
          <Outlet />
        </PageContainer>
      </main>
      <BottomNavigation items={helperNavItems} />
    </div>
  )
}
