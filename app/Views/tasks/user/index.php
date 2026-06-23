<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 font-display tracking-tight">Daftar Task Saya</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola semua tugas yang Anda delegasikan</p>
        </div>
        <div>
            <a href="<?= base_url('/user/tasks/create') ?>" class="inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-white transition-all duration-300 bg-slate-900 rounded-xl hover:bg-primary-600 hover:shadow-lg hover:-translate-y-0.5">
                <i class="ph-bold ph-plus mr-2"></i> Buat Task Baru
            </a>
        </div>
    </div>

    <?php if (session()->has('message')): ?>
        <div class="bg-green-50/80 backdrop-blur-sm border border-green-100 p-4 mb-6 rounded-2xl flex items-start gap-3 animate-fade-in-up">
            <i class="ph-fill ph-check-circle text-green-500 text-xl mt-0.5"></i>
            <p class="text-sm font-bold text-green-800"><?= session('message') ?></p>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <?php if (empty($tasks)): ?>
            <div class="p-16 text-center">
                <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 border border-slate-100">
                    <i class="ph-fill ph-clipboard-text text-slate-300 text-4xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Belum ada task</h3>
                <p class="text-sm text-slate-500 mb-8 max-w-sm mx-auto">Anda belum membuat tugas apapun. Coba delegasikan pekerjaan pertama Anda sekarang.</p>
                <a href="<?= base_url('/user/tasks/create') ?>" class="inline-flex items-center px-8 py-3 rounded-xl text-sm font-bold text-white bg-primary-600 hover:bg-primary-700 transition-colors shadow-lg shadow-primary-500/30 hover:-translate-y-0.5">
                    Buat Task Pertama
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 divide-y divide-slate-100">
                <?php foreach ($tasks as $task): ?>
                    <a href="<?= base_url('/user/tasks/' . $task['id']) ?>" class="group p-6 sm:p-8 hover:bg-slate-50 transition-colors block">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-start gap-5">
                                <div class="w-14 h-14 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-shrink-0 items-center justify-center text-slate-400 group-hover:border-primary-200 group-hover:text-primary-500 transition-colors">
                                    <i class="ph-fill ph-briefcase text-2xl"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-3 mb-1.5">
                                        <span class="text-xs font-bold uppercase tracking-wider text-primary-600 bg-primary-50 px-2.5 py-0.5 rounded-full">
                                            <?= esc($task['category_name'] ?? 'Umum') ?>
                                        </span>
                                        <span class="text-xs font-bold text-slate-400 flex items-center">
                                            <i class="ph-fill ph-clock mr-1"></i>
                                            <?= date('d M Y, H:i', strtotime($task['created_at'])) ?>
                                        </span>
                                    </div>
                                    <h4 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-primary-600 transition-colors"><?= esc($task['title']) ?></h4>
                                    <p class="text-sm text-slate-500 line-clamp-1 mb-3"><?= esc($task['description']) ?></p>
                                    
                                    <div class="flex flex-wrap items-center gap-4 text-sm">
                                        <div class="font-bold text-emerald-600 flex items-center">
                                            <i class="ph-fill ph-money mr-1.5"></i> Rp <?= number_format($task['price'], 0, ',', '.') ?>
                                        </div>
                                        <div class="font-medium text-slate-500 flex items-center">
                                            <i class="ph-fill ph-map-pin mr-1.5 text-slate-400"></i> <?= esc($task['location']) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center gap-4 border-t sm:border-t-0 pt-4 sm:pt-0 border-slate-100">
                                <?php 
                                    $statusConfig = match($task['status']) {
                                        'open' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'icon' => 'ph-dots-three-circle'],
                                        'accepted' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'border' => 'border-indigo-200', 'icon' => 'ph-handshake'],
                                        'in_progress' => ['bg' => 'bg-orange-50', 'text' => 'text-orange-700', 'border' => 'border-orange-200', 'icon' => 'ph-spinner gap'],
                                        'completed' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'icon' => 'ph-check-circle'],
                                        'cancelled' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-200', 'icon' => 'ph-x-circle'],
                                        default => ['bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'border' => 'border-slate-200', 'icon' => 'ph-info']
                                    };
                                ?>
                                <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-bold border <?= $statusConfig['bg'] ?> <?= $statusConfig['text'] ?> <?= $statusConfig['border'] ?>">
                                    <i class="ph-fill <?= $statusConfig['icon'] ?> mr-1.5 text-sm"></i>
                                    <?= ucfirst(str_replace('_', ' ', $task['status'])) ?>
                                </span>
                                
                                <span class="text-sm font-bold text-slate-400 group-hover:text-primary-600 transition-colors flex items-center">
                                    Detail <i class="ph-bold ph-caret-right ml-1"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
