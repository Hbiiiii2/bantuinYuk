import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { walletService } from '../services/wallet.service'
import type { WithdrawRequest } from '../types/wallet.types'

export const walletKeys = {
  all: ['wallet'] as const,
  summary: () => [...walletKeys.all, 'summary'] as const,
  transactions: (params?: any) => [...walletKeys.all, 'transactions', params] as const
}

export function useWalletSummary() {
  return useQuery({
    queryKey: walletKeys.summary(),
    queryFn: () => walletService.getWalletSummary(),
    staleTime: 30000
  })
}

export function useTransactions(params?: { page?: number; per_page?: number; type?: string }) {
  return useQuery({
    queryKey: walletKeys.transactions(params),
    queryFn: () => walletService.getTransactions(params),
    staleTime: 30000
  })
}

export function useRequestWithdraw() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: (data: WithdrawRequest) => walletService.requestWithdraw(data),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: walletKeys.summary() })
      queryClient.invalidateQueries({ queryKey: walletKeys.transactions() })
    }
  })
}
