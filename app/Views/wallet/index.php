<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-5xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900 font-display tracking-tight">Dompet Saya</h1>
        <p class="mt-1 text-sm text-slate-500">Kelola saldo dan lihat riwayat transaksi Anda.</p>
    </div>

    <?php if (session()->has('message')): ?>
        <div class="bg-green-50/80 backdrop-blur-sm border border-green-100 p-4 mb-6 rounded-2xl flex items-start gap-3 animate-fade-in-up">
            <i class="ph-fill ph-check-circle text-green-500 text-xl mt-0.5"></i>
            <p class="text-sm font-bold text-green-800"><?= session('message') ?></p>
        </div>
    <?php endif; ?>

    <?php if (session()->has('error')): ?>
        <div class="bg-red-50/80 backdrop-blur-sm border border-red-100 p-4 mb-6 rounded-2xl flex items-start gap-3 animate-fade-in-up">
            <i class="ph-fill ph-warning-circle text-red-500 text-xl mt-0.5"></i>
            <p class="text-sm font-bold text-red-800"><?= session('error') ?></p>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Saldo Card -->
        <div class="lg:col-span-1">
            <div class="bg-slate-900 rounded-3xl shadow-xl overflow-hidden relative group">
                <div class="absolute inset-0 bg-gradient-to-br from-primary-600 to-slate-900 opacity-50 group-hover:opacity-70 transition-opacity"></div>
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-white opacity-5 rounded-full blur-2xl"></div>
                
                <div class="relative p-8">
                    <p class="text-slate-300 text-sm font-medium mb-1">Saldo Tersedia</p>
                    <h2 class="text-4xl font-extrabold text-white mb-6">
                        Rp <?= number_format($wallet['balance'] - ($wallet['pending_balance'] ?? 0), 0, ',', '.') ?>
                    </h2>
                    
                    <?php if (($wallet['pending_balance'] ?? 0) > 0): ?>
                        <div class="bg-white/10 backdrop-blur-md rounded-xl p-3 mb-6 flex justify-between items-center border border-white/10">
                            <span class="text-slate-300 text-xs">Saldo Tertahan</span>
                            <span class="text-white font-bold text-sm">Rp <?= number_format($wallet['pending_balance'], 0, ',', '.') ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="pt-6 border-t border-white/20">
                        <p class="text-white text-sm font-bold mb-3">Simulasi Topup</p>
                        <form action="<?= base_url('/wallet/topup') ?>" method="post" class="flex gap-2">
                            <?= csrf_field() ?>
                            <input type="number" name="amount" min="10000" step="10000" placeholder="Nominal" class="w-full bg-white/10 border border-white/20 rounded-xl px-3 py-2 text-sm text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                            <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-xl text-sm font-bold transition-colors">
                                Topup
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Riwayat Transaksi -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden h-full flex flex-col">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900">Riwayat Transaksi</h3>
                </div>
                
                <div class="flex-1 p-0">
                    <?php if (empty($transactions)): ?>
                        <div class="p-12 text-center h-full flex flex-col items-center justify-center">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
                                <i class="ph-fill ph-receipt text-slate-300 text-2xl"></i>
                            </div>
                            <h4 class="text-base font-bold text-slate-900 mb-1">Belum ada transaksi</h4>
                            <p class="text-sm text-slate-500 max-w-xs mx-auto">Riwayat topup, pembayaran, dan pencairan dana akan tampil di sini.</p>
                        </div>
                    <?php else: ?>
                        <ul class="divide-y divide-slate-100 max-h-[500px] overflow-y-auto">
                            <?php foreach ($transactions as $trx): ?>
                                <li class="p-6 hover:bg-slate-50 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-4">
                                            <?php if ($trx['type'] === 'adjustment' || $trx['type'] === 'task_payment'): ?>
                                                <div class="w-10 h-10 rounded-full bg-green-50 text-green-600 flex items-center justify-center shrink-0">
                                                    <i class="ph-bold ph-arrow-down-left"></i>
                                                </div>
                                            <?php else: ?>
                                                <div class="w-10 h-10 rounded-full bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                                                    <i class="ph-bold ph-arrow-up-right"></i>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div>
                                                <p class="text-sm font-bold text-slate-900"><?= esc($trx['description'] ?: 'Transaksi') ?></p>
                                                <div class="flex items-center gap-2 text-xs text-slate-500 mt-1">
                                                    <span><?= date('d M Y, H:i', strtotime($trx['created_at'])) ?></span>
                                                    <span>•</span>
                                                    <span class="uppercase font-mono text-[10px] text-slate-400"><?= esc($trx['reference_id']) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="text-right">
                                            <?php if ($trx['type'] === 'adjustment' || $trx['type'] === 'task_payment'): ?>
                                                <p class="text-sm font-bold text-green-600">+ Rp <?= number_format($trx['amount'], 0, ',', '.') ?></p>
                                            <?php else: ?>
                                                <p class="text-sm font-bold text-slate-900">- Rp <?= number_format($trx['amount'], 0, ',', '.') ?></p>
                                            <?php endif; ?>
                                            
                                            <?php if ($trx['status'] === 'completed'): ?>
                                                <p class="text-xs font-bold text-green-600 mt-1">Berhasil</p>
                                            <?php elseif ($trx['status'] === 'pending'): ?>
                                                <p class="text-xs font-bold text-amber-600 mt-1">Pending</p>
                                            <?php else: ?>
                                                <p class="text-xs font-bold text-red-600 mt-1">Gagal</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
