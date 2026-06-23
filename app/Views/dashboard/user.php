<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    
    <!-- Welcome Header -->
    <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <div class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50 text-primary-600 text-xs font-bold tracking-wide uppercase mb-3 border border-blue-100">
                Customer Dashboard
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-display tracking-tight">Halo, <?= esc(strtok(auth()->user()->name, " ")) ?>! 👋</h1>
            <p class="mt-2 text-base text-slate-500">Siap untuk mendelegasikan tugas Anda hari ini?</p>
        </div>
        <div>
            <a href="<?= base_url('/user/tasks/create') ?>" class="w-full md:w-auto inline-flex items-center justify-center px-6 py-3.5 text-sm font-bold text-white transition-all duration-300 bg-primary-600 rounded-xl hover:bg-primary-700 hover:shadow-lg hover:shadow-primary-500/30 hover:-translate-y-0.5 group">
                <i class="ph-bold ph-plus mr-2 group-hover:rotate-90 transition-transform"></i>
                Buat Task Baru
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <!-- Balance Card -->
        <div class="relative bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-6 shadow-xl shadow-slate-900/20 overflow-hidden group">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white/5 rounded-full filter blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10 flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-slate-400 mb-1">Saldo Tersedia</p>
                    <h3 class="text-3xl font-display font-bold text-white tracking-tight">Rp <?= number_format($wallet['balance'] ?? 0, 0, ',', '.') ?></h3>
                    <p class="text-xs font-medium text-slate-500 mt-2 flex items-center">
                        <i class="ph-fill ph-clock-counter-clockwise mr-1.5"></i>
                        Pending: Rp <?= number_format($wallet['pending_balance'] ?? 0, 0, ',', '.') ?>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-sm flex items-center justify-center border border-white/10">
                    <i class="ph-fill ph-wallet text-white text-2xl"></i>
                </div>
            </div>
            <div class="mt-6 pt-5 border-t border-white/10 flex justify-between items-center">
                <a href="<?= base_url('/wallet') ?>" class="text-sm font-bold text-primary-400 hover:text-primary-300 transition-colors">Top Up Saldo</a>
                <i class="ph-bold ph-arrow-right text-primary-400"></i>
            </div>
        </div>

        <!-- Total Tasks -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-slate-500 mb-1">Total Task Dibuat</p>
                    <h3 class="text-3xl font-display font-bold text-slate-900 tracking-tight"><?= $totalTasks ?></h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center border border-indigo-100/50 text-indigo-600">
                    <i class="ph-fill ph-check-square-offset text-2xl"></i>
                </div>
            </div>
            <div class="mt-8">
                <div class="w-full bg-slate-100 rounded-full h-1.5">
                    <div class="bg-indigo-500 h-1.5 rounded-full" style="width: <?= $totalTasks > 0 ? min(100, ($activeTasks / $totalTasks) * 100) : 0 ?>%"></div>
                </div>
                <p class="text-xs font-medium text-slate-500 mt-2 text-right"><?= $activeTasks ?> sedang aktif</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 flex flex-col justify-between hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300">
            <div>
                <p class="text-sm font-medium text-slate-500 mb-4">Aksi Cepat</p>
                <div class="grid grid-cols-2 gap-3">
                    <a href="<?= base_url('/user/tasks') ?>" class="flex flex-col items-center justify-center p-3 rounded-2xl bg-slate-50 hover:bg-primary-50 text-slate-600 hover:text-primary-600 transition-colors border border-transparent hover:border-primary-100">
                        <i class="ph-fill ph-clock-counter-clockwise text-xl mb-1.5"></i>
                        <span class="text-xs font-bold">Riwayat</span>
                    </a>
                    <a href="<?= base_url('/profile') ?>" class="flex flex-col items-center justify-center p-3 rounded-2xl bg-slate-50 hover:bg-accent-50 text-slate-600 hover:text-accent-600 transition-colors border border-transparent hover:border-accent-100">
                        <i class="ph-fill ph-user-circle text-xl mb-1.5"></i>
                        <span class="text-xs font-bold">Profil</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Tasks Section -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-lg font-bold text-slate-900 font-display">Task Terbaru</h3>
            <a href="<?= base_url('/user/tasks') ?>" class="text-sm font-bold text-primary-600 hover:text-primary-700 transition-colors inline-flex items-center">
                Lihat Semua <i class="ph-bold ph-arrow-right ml-1"></i>
            </a>
        </div>
        
        <?php if (empty($tasks)): ?>
            <div class="p-12 text-center">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-5 border border-slate-100">
                    <i class="ph-fill ph-clipboard-text text-slate-300 text-3xl"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-1">Belum ada task</h3>
                <p class="text-sm text-slate-500 mb-6 max-w-sm mx-auto">Anda belum membuat tugas apapun. Delegasikan pekerjaan pertama Anda sekarang.</p>
                <a href="<?= base_url('/user/tasks/create') ?>" class="inline-flex items-center px-6 py-2.5 rounded-full text-sm font-bold text-primary-700 bg-primary-50 hover:bg-primary-100 transition-colors">
                    Buat Task Pertama
                </a>
            </div>
        <?php else: ?>
            <ul class="divide-y divide-slate-100">
                <?php foreach ($tasks as $task): ?>
                    <li class="group hover:bg-slate-50/80 transition-colors">
                        <a href="<?= base_url('/user/tasks/' . $task['id']) ?>" class="block px-6 py-5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-5">
                                    <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-center text-slate-400 group-hover:border-primary-200 group-hover:text-primary-500 transition-colors">
                                        <i class="ph-fill ph-briefcase text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-base font-bold text-slate-900 truncate group-hover:text-primary-700 transition-colors"><?= esc($task['title']) ?></p>
                                        <div class="flex items-center gap-3 mt-1">
                                            <p class="text-sm font-semibold text-slate-600">
                                                Rp <?= number_format($task['price'], 0, ',', '.') ?>
                                            </p>
                                            <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                            <p class="text-xs font-medium text-slate-500 flex items-center">
                                                <i class="ph-fill ph-calendar-blank mr-1 text-slate-400"></i>
                                                <?= date('d M Y', strtotime($task['created_at'])) ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <?php 
                                        $statusClass = match($task['status']) {
                                            'completed' => 'bg-green-50 text-green-700 border-green-200',
                                            'open' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'accepted', 'in_progress' => 'bg-purple-50 text-purple-700 border-purple-200',
                                            'cancelled' => 'bg-red-50 text-red-700 border-red-200',
                                            default => 'bg-slate-50 text-slate-700 border-slate-200'
                                        };
                                    ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border <?= $statusClass ?>">
                                        <?= ucfirst(str_replace('_', ' ', $task['status'])) ?>
                                    </span>
                                </div>
                            </div>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
