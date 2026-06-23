<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    
    <!-- Welcome Header -->
    <div class="mb-10">
        <div class="inline-flex items-center px-3 py-1 rounded-full bg-slate-900 text-white text-xs font-bold tracking-wide uppercase mb-3">
            Admin Portal
        </div>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-display tracking-tight">System Dashboard</h1>
        <p class="mt-2 text-base text-slate-500">Pantau aktivitas platform Bantuin Yuk secara menyeluruh.</p>
    </div>

    <!-- Quick Navigation (Admin specific) -->
    <div class="mb-10 bg-white p-2 rounded-2xl shadow-sm border border-slate-100 flex overflow-x-auto gap-2">
        <a href="<?= base_url('/admin/dashboard') ?>" class="px-5 py-2.5 rounded-xl bg-primary-50 text-primary-700 font-bold text-sm whitespace-nowrap transition-colors">
            <i class="ph-bold ph-squares-four mr-1.5"></i> Ikhtisar
        </a>
        <a href="<?= base_url('/admin/users') ?>" class="px-5 py-2.5 rounded-xl text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-bold text-sm whitespace-nowrap transition-colors">
            <i class="ph-bold ph-users mr-1.5"></i> Users
        </a>
        <a href="<?= base_url('/admin/helpers') ?>" class="px-5 py-2.5 rounded-xl text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-bold text-sm whitespace-nowrap transition-colors">
            <i class="ph-bold ph-user-gear mr-1.5"></i> Helpers
        </a>
        <a href="<?= base_url('/admin/tasks') ?>" class="px-5 py-2.5 rounded-xl text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-bold text-sm whitespace-nowrap transition-colors">
            <i class="ph-bold ph-briefcase mr-1.5"></i> Pekerjaan
        </a>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-slate-500 mb-1">Total Pengguna</p>
                    <h3 class="text-3xl font-display font-bold text-slate-900 tracking-tight"><?= number_format($totalUsers) ?></h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600">
                    <i class="ph-fill ph-users text-2xl"></i>
                </div>
            </div>
            <p class="text-xs font-medium text-slate-500 mt-4 flex items-center">
                <i class="ph-bold ph-trend-up text-green-500 mr-1.5"></i> Active accounts
            </p>
        </div>

        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-slate-500 mb-1">Total Pekerjaan</p>
                    <h3 class="text-3xl font-display font-bold text-slate-900 tracking-tight"><?= number_format($totalTasks) ?></h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-purple-50 flex items-center justify-center text-purple-600">
                    <i class="ph-fill ph-briefcase text-2xl"></i>
                </div>
            </div>
            <p class="text-xs font-medium text-slate-500 mt-4 flex items-center">
                <i class="ph-bold ph-chart-line-up text-green-500 mr-1.5"></i> Across all categories
            </p>
        </div>

        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-slate-500 mb-1">Total Transaksi</p>
                    <h3 class="text-3xl font-display font-bold text-slate-900 tracking-tight"><?= number_format($totalTransactions) ?></h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-orange-50 flex items-center justify-center text-orange-600">
                    <i class="ph-fill ph-receipt text-2xl"></i>
                </div>
            </div>
            <p class="text-xs font-medium text-slate-500 mt-4 flex items-center">
                <i class="ph-bold ph-arrows-left-right text-slate-400 mr-1.5"></i> System wide
            </p>
        </div>

        <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-6 shadow-xl relative overflow-hidden group">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white/5 rounded-full filter blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10 flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-slate-400 mb-1">Pendapatan Platform</p>
                    <h3 class="text-2xl font-display font-bold text-white tracking-tight">Rp <?= number_format($platformRevenue, 0, ',', '.') ?></h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-sm flex items-center justify-center border border-white/10">
                    <i class="ph-fill ph-vault text-white text-2xl"></i>
                </div>
            </div>
            <p class="text-xs font-medium text-slate-400 mt-4 flex items-center relative z-10">
                <i class="ph-bold ph-info text-slate-400 mr-1.5"></i> 10% dari Task Selesai
            </p>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-lg font-bold text-slate-900 font-display">Pekerjaan Terbaru</h3>
            <a href="<?= base_url('/admin/tasks') ?>" class="text-sm font-bold text-primary-600 hover:text-primary-700 transition-colors inline-flex items-center">
                Lihat Semua <i class="ph-bold ph-arrow-right ml-1"></i>
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-slate-100 text-xs uppercase tracking-wider text-slate-500 font-semibold">
                        <th class="p-4 pl-6">Pekerjaan</th>
                        <th class="p-4">Tanggal</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 pr-6 text-right">Nilai Transaksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($recentTasks)): ?>
                        <tr>
                            <td colspan="4" class="p-8 text-center text-slate-500">Belum ada aktivitas.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentTasks as $task): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="p-4 pl-6">
                                    <p class="font-bold text-slate-900"><?= esc($task['title']) ?></p>
                                    <p class="text-xs text-slate-500 line-clamp-1 max-w-xs"><?= esc($task['description']) ?></p>
                                </td>
                                <td class="p-4 text-sm text-slate-600">
                                    <?= date('d M Y, H:i', strtotime($task['created_at'])) ?>
                                </td>
                                <td class="p-4">
                                    <?php 
                                        $statusClass = match($task['status']) {
                                            'completed' => 'bg-green-50 text-green-700',
                                            'open' => 'bg-blue-50 text-blue-700',
                                            'accepted', 'in_progress' => 'bg-amber-50 text-amber-700',
                                            'cancelled' => 'bg-red-50 text-red-700',
                                            default => 'bg-slate-50 text-slate-700'
                                        };
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold <?= $statusClass ?>">
                                        <?= ucfirst(str_replace('_', ' ', $task['status'])) ?>
                                    </span>
                                </td>
                                <td class="p-4 pr-6 text-right font-bold text-slate-900">
                                    Rp <?= number_format($task['price'], 0, ',', '.') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
