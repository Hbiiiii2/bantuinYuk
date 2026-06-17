import React, { Suspense } from 'react'
import { useAdminAnalytics } from '../hooks/useAdmin'
import { PageHeader } from '@/components/layout/PageHeader'
import { BarChart, Percent, CreditCard, ShieldAlert } from 'lucide-react'
import { StatsCard } from '../components'

// Lazy loaded chart container
const AnalyticsChartContainer = React.lazy(() => import('../components/AnalyticsChartContainer'))

export function AnalyticsPage() {
  const { data: analytics, isLoading, error, refetch } = useAdminAnalytics()

  const formatCurrency = (amount: number | undefined) => {
    if (amount === undefined) return '-'
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0
    }).format(amount)
  }

  const formatPercentage = (rate: number | undefined) => {
    if (rate === undefined) return '-'
    return `${(rate * 100).toFixed(1)}%`
  }

  return (
    <div className="space-y-6">
      <PageHeader
        title="System Analytics"
        subtitle="Detailed statistics and performance tracking charts"
      />

      {/* Summary Row */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <StatsCard
          title="Task Completion Rate"
          value={formatPercentage(analytics?.completion_rate)}
          icon={Percent}
          description="successful requests ratio"
          isLoading={isLoading}
        />
        <StatsCard
          title="Dispute Rate"
          value={formatPercentage(analytics?.dispute_rate)}
          icon={ShieldAlert}
          description="dispute cases ratio"
          isLoading={isLoading}
        />
        <StatsCard
          title="Transaction Volume"
          value={formatCurrency(analytics?.total_transaction_amount)}
          icon={CreditCard}
          description="cumulative processed funds"
          isLoading={isLoading}
        />
        <StatsCard
          title="Verified Helpers Ratio"
          value={analytics ? `${analytics.verified_helpers} / ${analytics.total_helpers}` : '-'}
          icon={BarChart}
          description="helpers verified successfully"
          isLoading={isLoading}
        />
      </div>

      {/* Charts Panel (Lazy Loaded) */}
      <div className="pt-2">
        {error ? (
          <div className="bg-white rounded-xl border border-gray-200 p-12 text-center flex flex-col items-center justify-center">
            <h4 className="text-sm font-semibold text-gray-900 mb-2">Failed to load analytics charts</h4>
            <p className="text-xs text-gray-500 mb-4">
              {error.message || 'An error occurred while fetching system metrics.'}
            </p>
            <button
              onClick={() => refetch()}
              className="px-4 py-2 bg-primary text-white text-xs font-semibold rounded-lg hover:bg-primary-hover min-h-[44px]"
            >
              Retry
            </button>
          </div>
        ) : (
          <Suspense
            fallback={
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                {Array.from({ length: 4 }).map((_, idx) => (
                  <div
                    key={idx}
                    className="bg-white rounded-xl border border-gray-200 p-6 h-[300px] flex items-center justify-center animate-pulse"
                  >
                    <div className="h-6 bg-gray-200 rounded w-1/4 self-start absolute top-6 left-6"></div>
                    <div className="h-40 bg-gray-150 rounded w-full mt-8"></div>
                  </div>
                ))}
              </div>
            }
          >
            <AnalyticsChartContainer analytics={analytics} />
          </Suspense>
        )}
      </div>
    </div>
  )
}
export default AnalyticsPage
