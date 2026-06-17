import { BarChart } from 'lucide-react'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  ArcElement,
  Title,
  Tooltip,
  Legend,
  Filler
} from 'chart.js'
import { Line, Bar, Doughnut } from 'react-chartjs-2'

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  ArcElement,
  Title,
  Tooltip,
  Legend,
  Filler
)

interface ChartDataPoint {
  label: string
  value: number
}

interface AnalyticsChartProps {
  type: 'line' | 'bar' | 'doughnut'
  title: string
  data: ChartDataPoint[] | undefined
  label?: string
  color?: string
}

export function AnalyticsChart({
  type,
  title,
  data,
  label = 'Value',
  color = 'rgba(59, 130, 246, 0.85)' // default premium primary blue
}: AnalyticsChartProps) {
  const isEmpty = !data || data.length === 0 || data.every((d) => d.value === 0)

  if (isEmpty) {
    return (
      <div className="bg-white rounded-xl border border-gray-200 p-6 flex flex-col items-center justify-center min-h-[300px] shadow-sm" aria-hidden="true">
        <h4 className="text-sm font-semibold text-gray-900 mb-6 self-start">{title}</h4>
        <BarChart size={40} className="text-gray-300 mb-3" />
        <p className="text-sm text-gray-400 font-medium">No chart data available</p>
      </div>
    )
  }

  const chartData = {
    labels: data.map((d) => d.label),
    datasets: [
      {
        label,
        data: data.map((d) => d.value),
        backgroundColor: type === 'doughnut'
          ? [
              'rgba(59, 130, 246, 0.8)',
              'rgba(16, 185, 129, 0.8)',
              'rgba(245, 158, 11, 0.8)',
              'rgba(239, 68, 68, 0.8)',
              'rgba(139, 92, 246, 0.8)'
            ]
          : color,
        borderColor: type === 'doughnut'
          ? [
              'rgb(59, 130, 246)',
              'rgb(16, 185, 129)',
              'rgb(245, 158, 11)',
              'rgb(239, 68, 68)',
              'rgb(139, 92, 246)'
            ]
          : color.replace('0.85', '1').replace('0.15', '1'), // darken borders
        borderWidth: 2,
        tension: 0.3,
        fill: type === 'line' ? 'origin' : false
      }
    ]
  }

  const options = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        display: type === 'doughnut',
        position: 'bottom' as const,
        labels: {
          usePointStyle: true,
          boxWidth: 8,
          font: { size: 11, weight: 'bold' }
        }
      },
      tooltip: {
        backgroundColor: 'rgba(17, 24, 39, 0.9)',
        padding: 12,
        titleFont: { size: 12, weight: 'bold' },
        bodyFont: { size: 12 }
      }
    },
    scales: type === 'doughnut' ? {} : {
      y: {
        beginAtZero: true,
        grid: { color: 'rgba(243, 244, 246, 1)' },
        ticks: { font: { size: 10 } }
      },
      x: {
        grid: { display: false },
        ticks: { font: { size: 10 } }
      }
    }
  }

  return (
    <div className="bg-white rounded-xl border border-gray-200 p-6 shadow-sm flex flex-col h-[300px]">
      <h4 className="text-sm font-semibold text-gray-900 mb-4">{title}</h4>
      <div className="flex-1 relative min-h-0">
        {type === 'line' && <Line data={chartData} options={options as any} />}
        {type === 'bar' && <Bar data={chartData} options={options as any} />}
        {type === 'doughnut' && <Doughnut data={chartData} options={options as any} />}
      </div>
    </div>
  )
}
