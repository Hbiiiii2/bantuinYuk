import React from 'react'
import { AlertCircle, ChevronLeft, ChevronRight, Inbox } from 'lucide-react'
import { Button } from '@/components/ui/Button'

export interface Column<T> {
  key: string
  title: string
  render?: (item: T) => React.ReactNode
  className?: string
}

interface AdminTableProps<T> {
  columns: Column<T>[]
  data: T[] | undefined
  isLoading?: boolean
  error: Error | null
  refetch?: () => void
  pagination?: {
    currentPage: number
    totalPages: number
    onPageChange: (page: number) => void
  }
}

function AdminTableInner<T extends { id?: number | string }>({
  columns,
  data,
  isLoading = false,
  error,
  refetch,
  pagination
}: AdminTableProps<T>) {
  if (error) {
    return (
      <div className="bg-white rounded-xl border border-gray-200 p-12 text-center flex flex-col items-center justify-center">
        <AlertCircle className="text-rose-500 mb-4" size={48} />
        <h4 className="text-lg font-bold text-gray-900 mb-1">Failed to load data</h4>
        <p className="text-sm text-gray-500 mb-4 max-w-sm">
          {error.message || 'An error occurred while fetching data. Please try again.'}
        </p>
        {refetch && (
          <Button variant="secondary" onClick={refetch} className="min-h-[44px]">
            Retry Loading
          </Button>
        )}
      </div>
    )
  }

  if (isLoading) {
    return (
      <div className="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <div className="overflow-x-auto">
          <table className="w-full border-collapse text-left text-sm text-gray-500">
            <thead className="bg-gray-50 text-xs uppercase text-gray-700">
              <tr>
                {columns.map((col) => (
                  <th key={col.key} scope="col" className={col.className || "px-6 py-4 font-semibold"}>
                    {col.title}
                  </th>
                ))}
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200 bg-white">
              {Array.from({ length: 5 }).map((_, rowIndex) => (
                <tr key={rowIndex} className="animate-pulse">
                  {columns.map((col) => (
                    <td key={col.key} className="px-6 py-4">
                      <div className="h-4 bg-gray-200 rounded w-3/4"></div>
                    </td>
                  ))}
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    )
  }

  if (!data || data.length === 0) {
    return (
      <div className="bg-white rounded-xl border border-gray-200 p-12 text-center flex flex-col items-center justify-center">
        <Inbox className="text-gray-300 mb-4" size={48} />
        <h4 className="text-base font-bold text-gray-900 mb-1">No Data Available</h4>
        <p className="text-sm text-gray-500 max-w-sm">
          There are currently no records found matching your filters.
        </p>
      </div>
    )
  }

  return (
    <div className="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm flex flex-col">
      <div className="overflow-x-auto flex-1">
        <table className="w-full border-collapse text-left text-sm text-gray-500">
          <thead className="bg-gray-50 text-xs uppercase text-gray-700 font-semibold border-b border-gray-200">
            <tr>
              {columns.map((col) => (
                <th key={col.key} scope="col" className={col.className || "px-6 py-4 font-semibold"}>
                  {col.title}
                </th>
              ))}
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-100 bg-white">
            {data.map((item, index) => (
              <tr key={item.id !== undefined ? item.id : index} className="hover:bg-gray-50 transition-colors">
                {columns.map((col) => (
                  <td key={col.key} className={col.className || "px-6 py-4 whitespace-nowrap text-gray-600"}>
                    {col.render ? col.render(item) : (item as any)[col.key]}
                  </td>
                ))}
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {pagination && pagination.totalPages > 1 && (
        <div className="flex items-center justify-between border-t border-gray-200 px-6 py-4 bg-gray-50">
          <span className="text-xs font-medium text-gray-500">
            Page {pagination.currentPage} of {pagination.totalPages}
          </span>
          <div className="flex items-center gap-2">
            <button
              onClick={() => pagination.onPageChange(pagination.currentPage - 1)}
              disabled={pagination.currentPage === 1}
              className="p-1 text-gray-500 hover:bg-gray-100 hover:text-gray-700 disabled:opacity-40 disabled:hover:bg-transparent rounded-lg min-h-[36px] min-w-[36px] flex items-center justify-center border border-gray-200"
              aria-label="Previous page"
            >
              <ChevronLeft size={18} />
            </button>
            <button
              onClick={() => pagination.onPageChange(pagination.currentPage + 1)}
              disabled={pagination.currentPage === pagination.totalPages}
              className="p-1 text-gray-500 hover:bg-gray-100 hover:text-gray-700 disabled:opacity-40 disabled:hover:bg-transparent rounded-lg min-h-[36px] min-w-[36px] flex items-center justify-center border border-gray-200"
              aria-label="Next page"
            >
              <ChevronRight size={18} />
            </button>
          </div>
        </div>
      )}
    </div>
  )
}

export const AdminTable = React.memo(AdminTableInner) as typeof AdminTableInner
