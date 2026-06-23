<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    
    <div class="mb-8 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 font-display tracking-tight">Eksplor Pekerjaan</h1>
            <p class="mt-1 text-sm text-slate-500">Temukan pekerjaan di sekitar Anda dan mulai hasilkan uang tambahan.</p>
        </div>
        
        <form action="" method="get" class="flex w-full md:w-auto gap-2">
            <div class="relative w-full md:w-64">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="ph-bold ph-magnifying-glass text-slate-400"></i>
                </div>
                <input type="text" name="keyword" value="<?= esc(request()->getGet('keyword') ?? '') ?>" class="block w-full pl-10 pr-3 py-2 border border-slate-200 rounded-xl leading-5 bg-white placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500 sm:text-sm transition-shadow shadow-sm" placeholder="Cari pekerjaan...">
            </div>
            <select name="category_id" class="block w-full md:w-40 pl-3 pr-10 py-2 text-base border-slate-200 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-xl shadow-sm">
                <option value="">Semua Kategori</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>" <?= (request()->getGet('category_id') == $category['id']) ? 'selected' : '' ?>>
                        <?= esc($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-bold rounded-xl shadow-sm text-white bg-slate-900 hover:bg-primary-600 transition-colors">
                Filter
            </button>
        </form>
    </div>

    <?php if (empty($tasks)): ?>
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-16 text-center">
            <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 border border-slate-100">
                <i class="ph-fill ph-magnifying-glass text-slate-300 text-4xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">Pekerjaan tidak ditemukan</h3>
            <p class="text-sm text-slate-500">Belum ada pekerjaan yang tersedia atau coba ubah kata kunci pencarian Anda.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <?php foreach ($tasks as $task): ?>
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden flex flex-col hover:shadow-md transition-shadow group relative">
                    <div class="p-6 flex-1">
                        <div class="flex items-start justify-between mb-4">
                            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-bold bg-primary-50 text-primary-700 border border-primary-100">
                                <?= esc($task['category_name']) ?>
                            </span>
                            <span class="text-lg font-extrabold text-slate-900">
                                Rp <?= number_format($task['price'], 0, ',', '.') ?>
                            </span>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2 group-hover:text-primary-600 transition-colors">
                            <a href="<?= base_url('/helper/tasks/' . $task['id']) ?>" class="focus:outline-none">
                                <span class="absolute inset-0" aria-hidden="true"></span>
                                <?= esc($task['title']) ?>
                            </a>
                        </h3>
                        <p class="text-sm text-slate-500 line-clamp-2 mb-4">
                            <?= esc($task['description']) ?>
                        </p>
                        
                        <div class="flex items-center gap-2 text-xs text-slate-500 mb-2">
                            <i class="ph-fill ph-map-pin text-slate-400"></i>
                            <span class="truncate"><?= esc($task['location']) ?></span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <i class="ph-fill ph-user text-slate-400"></i>
                            <span>Oleh <?= esc($task['user_name']) ?></span>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-xs font-medium text-slate-500">
                            <i class="ph-bold ph-clock mr-1"></i> <?= date('d M Y', strtotime($task['created_at'])) ?>
                        </span>
                        <span class="text-sm font-bold text-primary-600 group-hover:text-primary-700 flex items-center">
                            Lihat Detail <i class="ph-bold ph-arrow-right ml-1 transition-transform group-hover:translate-x-1"></i>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>
<?= $this->endSection() ?>
