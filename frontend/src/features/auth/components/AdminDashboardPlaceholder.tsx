import { PageHeader } from '@/components/layout/PageHeader'

export function AdminDashboardPage() {
  return (
    <div>
      <PageHeader 
        title="Dashboard"
        subtitle="Overview of your platform"
      />
      
      <div className="space-y-4">
        <div className="bg-white rounded-xl border border-gray-200 p-6">
          <h3 className="font-medium text-gray-900 mb-2">Statistics</h3>
          <p className="text-sm text-gray-500">Dashboard content coming in Sprint 13.6</p>
        </div>
      </div>
    </div>
  )
}
