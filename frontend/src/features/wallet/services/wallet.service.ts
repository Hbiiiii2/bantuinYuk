import api from '@/lib/api'
import type { ApiResponse, PaginatedResponse } from '@/types/api.types'
import type { WalletSummary, Transaction, WithdrawRequest } from '../types/wallet.types'

export const walletService = {
  async getWalletSummary(): Promise<WalletSummary> {
    const response = await api.get<ApiResponse<WalletSummary>>('/wallet')
    return response.data.data
  },

  async getTransactions(params?: { page?: number; per_page?: number; type?: string }): Promise<PaginatedResponse<Transaction>> {
    const response = await api.get<ApiResponse<PaginatedResponse<Transaction>>>('/wallet/transactions', { params })
    return response.data.data
  },

  async requestWithdraw(data: WithdrawRequest): Promise<Transaction> {
    const response = await api.post<ApiResponse<Transaction>>('/wallet/withdraw', data)
    return response.data.data
  }
}
