export interface WalletSummary {
  balance: number
  available_balance: number
  pending_balance: number
  total_earned: number
  total_withdrawn: number
  total_refunded: number
  pending_withdrawals: number
}

export type TransactionType = 'task_payment' | 'withdraw' | 'refund' | 'adjustment'
export type TransactionStatus = 'pending' | 'completed' | 'cancelled'

export interface Transaction {
  id: number
  user_id: number
  task_id: number | null
  amount: number
  type: TransactionType
  status: TransactionStatus
  reference_id: string
  description: string
  created_at: string
}

export interface WithdrawRequest {
  amount: number
  description: string
  bank_name: string
  account_number: string
  account_holder: string
}
