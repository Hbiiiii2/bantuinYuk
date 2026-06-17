import { useToastStore } from '../hooks/useToast'
import { CheckCircle, XCircle, Info, X } from 'lucide-react'
import { cn } from '@/lib/utils'

export function ToastContainer() {
  const { toasts, removeToast } = useToastStore()

  if (toasts.length === 0) return null

  return (
    <div className="fixed bottom-5 right-5 z-55 flex flex-col gap-2 max-w-sm w-full pointer-events-none">
      {toasts.map((toast) => {
        const Icon = {
          success: CheckCircle,
          error: XCircle,
          info: Info
        }[toast.type]

        return (
          <div
            key={toast.id}
            className={cn(
              "flex items-center gap-3 p-4 rounded-xl border shadow-lg pointer-events-auto transition-all duration-300 transform translate-y-0",
              toast.type === 'success' && "bg-emerald-50 border-emerald-200 text-emerald-800",
              toast.type === 'error' && "bg-rose-50 border-rose-200 text-rose-800",
              toast.type === 'info' && "bg-sky-50 border-sky-200 text-sky-800"
            )}
            role="alert"
          >
            <Icon size={20} className={cn(
              toast.type === 'success' && "text-emerald-500",
              toast.type === 'error' && "text-rose-500",
              toast.type === 'info' && "text-sky-500"
            )} />
            <p className="text-sm font-medium flex-1">{toast.message}</p>
            <button
              onClick={() => removeToast(toast.id)}
              className="text-gray-400 hover:text-gray-600 rounded-lg p-1 min-h-[32px] min-w-[32px] flex items-center justify-center"
              aria-label="Close notification"
            >
              <X size={16} />
            </button>
          </div>
        )
      })}
    </div>
  )
}
