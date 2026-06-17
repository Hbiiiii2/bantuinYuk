import { Button } from '@/components/ui/Button'
import { AlertTriangle, X } from 'lucide-react'

interface ConfirmationDialogProps {
  isOpen: boolean
  title: string
  message: string
  confirmText?: string
  cancelText?: string
  onConfirm: () => void
  onCancel: () => void
  isLoading?: boolean
  type?: 'danger' | 'warning' | 'info'
}

export function ConfirmationDialog({
  isOpen,
  title,
  message,
  confirmText = 'Confirm',
  cancelText = 'Cancel',
  onConfirm,
  onCancel,
  isLoading = false,
  type = 'warning'
}: ConfirmationDialogProps) {
  if (!isOpen) return null

  return (
    <div
      className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
      role="dialog"
      aria-modal="true"
      aria-labelledby="confirm-dialog-title"
    >
      <div className="bg-white rounded-xl p-6 max-w-md w-full relative">
        <button
          onClick={onCancel}
          className="absolute right-4 top-4 text-gray-400 hover:text-gray-600 min-h-[44px] min-w-[44px] flex items-center justify-center rounded-lg"
          aria-label="Close dialog"
          disabled={isLoading}
        >
          <X size={20} />
        </button>

        <div className="flex gap-4 items-start mb-4">
          <div className={`p-3 rounded-lg flex-shrink-0 ${
            type === 'danger' ? 'bg-rose-50 text-rose-600' :
            type === 'warning' ? 'bg-amber-50 text-amber-600' :
            'bg-sky-50 text-sky-600'
          }`}>
            <AlertTriangle size={24} />
          </div>
          <div>
            <h3 className="text-lg font-bold text-gray-900" id="confirm-dialog-title">
              {title}
            </h3>
            <p className="text-sm text-gray-500 mt-1">
              {message}
            </p>
          </div>
        </div>

        <div className="flex gap-3 justify-end pt-2">
          <Button
            type="button"
            variant="secondary"
            className="min-h-[44px] px-5"
            onClick={onCancel}
            disabled={isLoading}
          >
            {cancelText}
          </Button>
          <Button
            type="button"
            variant={type === 'danger' ? 'danger' : 'primary'}
            className="min-h-[44px] px-5"
            onClick={onConfirm}
            loading={isLoading}
            disabled={isLoading}
          >
            {confirmText}
          </Button>
        </div>
      </div>
    </div>
  )
}
