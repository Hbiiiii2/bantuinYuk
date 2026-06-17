import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { z } from 'zod'
import { X } from 'lucide-react'
import { Input } from '@/components/ui/Input'
import { Button } from '@/components/ui/Button'
import { useRequestWithdraw } from '../hooks/useWallet'
import { useState } from 'react'

interface WithdrawDialogProps {
  availableBalance: number
  isOpen: boolean
  onClose: () => void
}

export function WithdrawDialog({ availableBalance, isOpen, onClose }: WithdrawDialogProps) {
  const [errorMsg, setErrorMsg] = useState<string | null>(null)
  const withdrawMutation = useRequestWithdraw()
  
  const withdrawSchema = z.object({
    amount: z.number()
      .positive('Amount must be positive')
      .max(availableBalance, 'Amount cannot exceed available balance'),
    bank_name: z.string().min(1, 'Bank name is required'),
    account_number: z.string().min(1, 'Account number is required'),
    account_holder: z.string().min(1, 'Account holder name is required'),
    description: z.string().optional()
  })

  type WithdrawFormData = z.infer<typeof withdrawSchema>

  const {
    register,
    handleSubmit,
    reset,
    formState: { errors }
  } = useForm<WithdrawFormData>({
    resolver: zodResolver(withdrawSchema),
    defaultValues: {
      amount: 0,
      bank_name: '',
      account_number: '',
      account_holder: '',
      description: ''
    }
  })

  const onSubmit = async (data: WithdrawFormData) => {
    try {
      setErrorMsg(null)
      await withdrawMutation.mutateAsync({
        amount: data.amount,
        description: data.description || `Withdrawal to ${data.bank_name}`,
        bank_name: data.bank_name,
        account_number: data.account_number,
        account_holder: data.account_holder
      })
      reset()
      onClose()
    } catch (err: any) {
      setErrorMsg(err?.message || 'Withdrawal failed. Please check your inputs and try again.')
    }
  }

  if (!isOpen) return null

  return (
    <div 
      className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" 
      role="dialog" 
      aria-modal="true" 
      aria-labelledby="withdraw-dialog-title"
    >
      <div className="bg-white rounded-xl p-6 max-w-md w-full relative">
        <button
          onClick={onClose}
          className="absolute right-4 top-4 text-gray-400 hover:text-gray-600 min-h-[44px] min-w-[44px] flex items-center justify-center rounded-lg"
          aria-label="Close dialog"
        >
          <X size={20} />
        </button>
        
        <h3 className="text-lg font-bold text-gray-900 mb-4" id="withdraw-dialog-title">
          Request Withdrawal
        </h3>
        
        <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
          <Input
            label="Amount (Rp)"
            type="number"
            placeholder="0"
            error={errors.amount?.message}
            {...register('amount', { valueAsNumber: true })}
            aria-required="true"
          />
          
          <Input
            label="Bank Name"
            type="text"
            placeholder="e.g. Bank Central Asia"
            error={errors.bank_name?.message}
            {...register('bank_name')}
            aria-required="true"
          />
          
          <Input
            label="Account Number"
            type="text"
            placeholder="e.g. 1234567890"
            error={errors.account_number?.message}
            {...register('account_number')}
            aria-required="true"
          />
          
          <Input
            label="Account Holder Name"
            type="text"
            placeholder="e.g. John Doe"
            error={errors.account_holder?.message}
            {...register('account_holder')}
            aria-required="true"
          />

          <Input
            label="Notes / Description (Optional)"
            type="text"
            placeholder="e.g. Withdraw for June"
            error={errors.description?.message}
            {...register('description')}
          />
          
          {errorMsg && (
            <div className="text-sm font-medium text-danger" role="alert">
              {errorMsg}
            </div>
          )}
          
          <div className="flex gap-3 pt-2">
            <Button
              type="button"
              variant="secondary"
              className="flex-1 min-h-[44px]"
              onClick={onClose}
            >
              Cancel
            </Button>
            <Button
              type="submit"
              className="flex-1 min-h-[44px]"
              loading={withdrawMutation.isPending}
              disabled={withdrawMutation.isPending}
            >
              Request
            </Button>
          </div>
        </form>
      </div>
    </div>
  )
}
