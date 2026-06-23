<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\WalletModel;
use App\Models\TransactionModel;

class WalletController extends BaseController
{
    protected $walletModel;
    protected $transactionModel;

    public function __construct()
    {
        $this->walletModel = new WalletModel();
        $this->transactionModel = new TransactionModel();
    }

    public function index()
    {
        $userId = auth()->id();
        
        // Ensure wallet exists
        $wallet = $this->walletModel->getByUserId($userId);
        if (!$wallet) {
            $this->walletModel->createWallet($userId);
            $wallet = $this->walletModel->getByUserId($userId);
        }

        // Get transactions
        $transactionsResult = $this->transactionModel->getByUserId($userId, 1, 50);

        return view('wallet/index', [
            'title'        => 'Dompet Saya - Bantuin Yuk',
            'wallet'       => $wallet,
            'transactions' => $transactionsResult['data']
        ]);
    }

    public function topup()
    {
        // For demonstration purpose: mock topup
        $userId = auth()->id();
        $amount = (float) $this->request->getPost('amount');

        if ($amount < 10000) {
            return redirect()->back()->with('error', 'Minimal topup Rp 10.000');
        }

        // Ensure wallet exists
        if (!$this->walletModel->getByUserId($userId)) {
            $this->walletModel->createWallet($userId);
        }

        $this->walletModel->incrementBalance($userId, $amount);

        // Record transaction
        $this->transactionModel->insert([
            'user_id'      => $userId,
            'task_id'      => null,
            'amount'       => $amount,
            'type'         => TransactionModel::TYPE_ADJUSTMENT,
            'status'       => TransactionModel::STATUS_COMPLETED,
            'reference_id' => 'TOPUP-' . time() . '-' . rand(1000, 9999),
            'description'  => 'Topup Saldo (Simulasi)'
        ]);

        return redirect()->back()->with('message', 'Topup berhasil dilakukan!');
    }
}
