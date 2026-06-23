<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    
    <!-- Welcome Header -->
    <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <div class="inline-flex items-center px-3 py-1 rounded-full bg-accent-50 text-accent-600 text-xs font-bold tracking-wide uppercase mb-3 border border-accent-100">
                Mitra Helper Dashboard
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-display tracking-tight">Siap beraksi, <?= esc(strtok(auth()->user()->name, " ")) ?>? 🚀</h1>
            <p class="mt-2 text-base text-slate-500">Temukan pekerjaan baru dan pantau performa Anda.</p>
        </div>
        <div>
            <a href="<?= base_url('/helper/tasks/explore') ?>" class="w-full md:w-auto inline-flex items-center justify-center px-6 py-3.5 text-sm font-bold text-slate-900 transition-all duration-300 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:shadow-lg hover:shadow-slate-200/50 hover:-translate-y-0.5 group">
                <i class="ph-bold ph-magnifying-glass mr-2 group-hover:text-accent-500 transition-colors"></i>
                Cari Pekerjaan
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <!-- Earnings Card -->
        <div class="relative bg-gradient-to-br from-accent-600 to-accent-800 rounded-3xl p-6 shadow-xl shadow-accent-500/20 overflow-hidden group">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white/10 rounded-full filter blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10 flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-accent-100 mb-1">Saldo Pendapatan</p>
                    <h3 class="text-3xl font-display font-bold text-white tracking-tight">Rp <?= number_format($wallet['balance'] ?? 0, 0, ',', '.') ?></h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-sm flex items-center justify-center border border-white/20">
                    <i class="ph-fill ph-money text-white text-2xl"></i>
                </div>
            </div>
            <div class="mt-6 pt-5 border-t border-white/20">
                <a href="<?= base_url('/wallet') ?>" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-white/20 hover:bg-white/30 text-white font-bold text-sm rounded-xl transition-colors backdrop-blur-md">
                    Tarik Dana Sekarang
                </a>
            </div>
        </div>

        <!-- Completed Jobs -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-slate-500 mb-1">Pekerjaan Selesai</p>
                    <h3 class="text-3xl font-display font-bold text-slate-900 tracking-tight"><?= $completedTasks ?></h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center border border-emerald-100/50 text-emerald-600">
                    <i class="ph-fill ph-check-circle text-2xl"></i>
                </div>
            </div>
            <div class="mt-8">
                <div class="w-full bg-slate-100 rounded-full h-1.5">
                    <div class="bg-emerald-500 h-1.5 rounded-full" style="width: <?= $totalTasks > 0 ? min(100, ($completedTasks / $totalTasks) * 100) : 0 ?>%"></div>
                </div>
                <p class="text-xs font-medium text-slate-500 mt-2 text-right"><?= $activeTasks ?> pekerjaan sedang berjalan</p>
            </div>
        </div>

        <!-- Rating -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 flex flex-col justify-between hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-sm font-medium text-slate-500 mb-1">Rating Rata-rata</p>
                    <h3 class="text-3xl font-display font-bold text-slate-900 tracking-tight"><?= number_format($rating, 1, ',', '.') ?> <span class="text-lg font-medium text-slate-400">/ 5.0</span></h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-orange-50 flex items-center justify-center border border-orange-100/50 text-orange-500">
                    <i class="ph-fill ph-star text-2xl"></i>
                </div>
            </div>
            <div class="flex gap-1 mb-2">
                <?php for($i = 1; $i <= 5; $i++): ?>
                    <?php if ($rating >= $i): ?>
                        <i class="ph-fill ph-star text-orange-400 text-lg"></i>
                    <?php elseif ($rating >= $i - 0.5): ?>
                        <i class="ph-fill ph-star-half text-orange-400 text-lg"></i>
                    <?php else: ?>
                        <i class="ph-fill ph-star text-slate-200 text-lg"></i>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
            <p class="text-xs font-medium text-slate-500">Berdasarkan ulasan pelanggan</p>
        </div>
    </div>

    <!-- Open Jobs Section -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden mb-10">
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-lg font-bold text-slate-900 font-display">Peluang Pekerjaan Baru</h3>
            <a href="<?= base_url('/helper/tasks/explore') ?>" class="text-sm font-bold text-primary-600 hover:text-primary-700 transition-colors inline-flex items-center">
                Eksplor Semua <i class="ph-bold ph-arrow-right ml-1"></i>
            </a>
        </div>
        
        <?php if (empty($openTasks)): ?>
            <div class="p-12 text-center">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-5 border border-slate-100">
                    <i class="ph-fill ph-magnifying-glass text-slate-300 text-3xl"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-1">Belum ada pekerjaan baru</h3>
                <p class="text-sm text-slate-500 mb-6 max-w-sm mx-auto">Saat ini belum ada tugas baru yang tersedia untuk dikerjakan.</p>
            </div>
        <?php else: ?>
            <ul class="divide-y divide-slate-100">
                <?php foreach ($openTasks as $task): ?>
                    <li class="group hover:bg-slate-50/80 transition-colors">
                        <a href="<?= base_url('/helper/tasks/' . $task['id']) ?>" class="block px-6 py-5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-5">
                                    <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-center text-slate-400 group-hover:border-primary-200 group-hover:text-primary-500 transition-colors">
                                        <i class="ph-fill ph-briefcase text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-base font-bold text-slate-900 truncate group-hover:text-primary-700 transition-colors"><?= esc($task['title']) ?></p>
                                        <div class="flex items-center gap-3 mt-1">
                                            <span class="inline-flex items-center justify-center px-2 py-0.5 rounded text-[10px] font-bold bg-primary-50 text-primary-700 border border-primary-100">
                                                <?= esc($task['category_name']) ?>
                                            </span>
                                            <p class="text-sm font-semibold text-emerald-600">
                                                Rp <?= number_format($task['price'], 0, ',', '.') ?>
                                            </p>
                                            <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                            <p class="text-xs font-medium text-slate-500 flex items-center">
                                                <i class="ph-fill ph-user mr-1 text-slate-400"></i>
                                                <?= esc($task['user_name']) ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-primary-600 text-white hover:bg-primary-700 transition-colors">
                                        Ambil Pekerjaan
                                    </span>
                                </div>
                            </div>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <!-- Active Jobs Section -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-lg font-bold text-slate-900 font-display">Pekerjaan Saat Ini</h3>
            <a href="<?= base_url('/helper/tasks/my-tasks') ?>" class="text-sm font-bold text-accent-600 hover:text-accent-700 transition-colors inline-flex items-center">
                Semua Pekerjaan <i class="ph-bold ph-arrow-right ml-1"></i>
            </a>
        </div>
        
        <?php if (empty($tasks)): ?>
            <div class="p-12 text-center">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-5 border border-slate-100">
                    <i class="ph-fill ph-toolbox text-slate-300 text-3xl"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-1">Belum ada pekerjaan aktif</h3>
                <p class="text-sm text-slate-500 mb-6 max-w-sm mx-auto">Anda belum mengambil pekerjaan apapun saat ini. Segera cari pekerjaan yang cocok dengan keahlian Anda.</p>
                <a href="<?= base_url('/helper/tasks/explore') ?>" class="inline-flex items-center px-6 py-2.5 rounded-full text-sm font-bold text-accent-700 bg-accent-50 hover:bg-accent-100 transition-colors">
                    Cari Pekerjaan Sekarang
                </a>
            </div>
        <?php else: ?>
            <ul class="divide-y divide-slate-100">
                <?php foreach ($tasks as $task): ?>
                    <li class="group hover:bg-slate-50/80 transition-colors">
                        <a href="<?= base_url('/helper/tasks/' . $task['id']) ?>" class="block px-6 py-5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-5">
                                    <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-center text-slate-400 group-hover:border-accent-200 group-hover:text-accent-500 transition-colors">
                                        <i class="ph-fill ph-briefcase text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-base font-bold text-slate-900 truncate group-hover:text-accent-700 transition-colors"><?= esc($task['title']) ?></p>
                                        <div class="flex items-center gap-3 mt-1">
                                            <p class="text-sm font-semibold text-emerald-600">
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
                                            'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'accepted' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'in_progress' => 'bg-orange-50 text-orange-700 border-orange-200',
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
