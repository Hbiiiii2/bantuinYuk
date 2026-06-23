<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900 font-display tracking-tight">Pekerjaan Saya</h1>
        <p class="mt-1 text-sm text-slate-500">Daftar pekerjaan yang sedang atau sudah Anda selesaikan.</p>
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
                    <i class="ph-fill ph-briefcase text-slate-300 text-4xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Belum ada pekerjaan</h3>
                <p class="text-sm text-slate-500 mb-8 max-w-sm mx-auto">Anda belum mengambil pekerjaan apapun. Temukan pekerjaan yang cocok untuk Anda sekarang.</p>
                <a href="<?= base_url('/helper/tasks/explore') ?>" class="inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-white transition-all duration-300 bg-primary-600 rounded-xl hover:bg-primary-700 hover:shadow-lg hover:-translate-y-0.5">
                    <i class="ph-bold ph-magnifying-glass mr-2"></i> Eksplor Pekerjaan
                </a>
            </div>
        <?php else: ?>
            <ul role="list" class="divide-y divide-slate-100">
                <?php foreach ($tasks as $task): ?>
                    <li>
                        <a href="<?= base_url('/helper/tasks/' . $task['id']) ?>" class="block hover:bg-slate-50 transition-colors">
                            <div class="px-6 py-6 sm:px-8">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-lg font-bold text-slate-900 truncate"><?= esc($task['title']) ?></p>
                                    <div class="ml-2 flex-shrink-0 flex">
                                        <?php if ($task['status'] === 'in_progress'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                                Sedang Dikerjakan
                                            </span>
                                        <?php elseif ($task['status'] === 'completed'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                                <i class="ph-bold ph-check mr-1.5"></i> Selesai
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="mt-2 sm:flex sm:justify-between">
                                    <div class="sm:flex sm:gap-6 text-sm text-slate-500">
                                        <p class="flex items-center mb-2 sm:mb-0">
                                            <i class="ph-fill ph-tag text-slate-400 mr-1.5"></i>
                                            <?= esc($task['category_name']) ?>
                                        </p>
                                        <p class="flex items-center mb-2 sm:mb-0">
                                            <i class="ph-fill ph-map-pin text-slate-400 mr-1.5"></i>
                                            <span class="truncate max-w-[200px]"><?= esc($task['location']) ?></span>
                                        </p>
                                    </div>
                                    <div class="mt-2 flex items-center text-sm font-bold text-slate-900 sm:mt-0">
                                        Rp <?= number_format($task['price'], 0, ',', '.') ?>
                                    </div>
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
