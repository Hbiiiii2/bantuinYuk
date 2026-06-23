<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    
    <div class="mb-8 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 font-display tracking-tight">Pusat Resolusi</h1>
            <p class="mt-1 text-sm text-slate-500">Pantau komplain dan sengketa pekerjaan Anda di sini.</p>
        </div>
    </div>

    <?php if (session()->has('message')): ?>
        <div class="bg-green-50 border border-green-100 p-4 mb-6 rounded-2xl flex items-start gap-3">
            <i class="ph-fill ph-check-circle text-green-500 text-xl mt-0.5"></i>
            <p class="text-sm font-bold text-green-800"><?= session('message') ?></p>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-xs uppercase tracking-wider text-slate-500 font-semibold">
                        <th class="p-4 pl-6">Kasus</th>
                        <th class="p-4">Terkait Pekerjaan</th>
                        <th class="p-4">Tanggal</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 pr-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($disputes)): ?>
                        <tr>
                            <td colspan="5" class="p-16 text-center">
                                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-5 border border-slate-100">
                                    <i class="ph-fill ph-shield-check text-slate-300 text-3xl"></i>
                                </div>
                                <p class="text-sm font-bold text-slate-900">Tidak ada komplain</p>
                                <p class="text-sm text-slate-500 mt-1">Anda tidak memiliki masalah atau komplain aktif.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($disputes as $dispute): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="p-4 pl-6 max-w-xs">
                                    <p class="font-bold text-slate-900 line-clamp-1"><?= esc($dispute['reason']) ?></p>
                                </td>
                                <td class="p-4">
                                    <p class="text-sm font-medium text-slate-900"><?= esc($dispute['task_title'] ?? 'Tugas Dihapus') ?></p>
                                </td>
                                <td class="p-4 text-sm text-slate-600">
                                    <?= date('d M Y', strtotime($dispute['created_at'])) ?>
                                </td>
                                <td class="p-4">
                                    <?php 
                                        $statusClass = match($dispute['status']) {
                                            'open' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'under_review' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'resolved' => 'bg-green-50 text-green-700 border-green-200',
                                            'rejected' => 'bg-red-50 text-red-700 border-red-200',
                                            default => 'bg-slate-50 text-slate-700 border-slate-200'
                                        };
                                        $statusText = match($dispute['status']) {
                                            'open' => 'Menunggu',
                                            'under_review' => 'Ditinjau Admin',
                                            'resolved' => 'Selesai',
                                            'rejected' => 'Ditolak',
                                            default => 'Unknown'
                                        };
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border <?= $statusClass ?>">
                                        <?= $statusText ?>
                                    </span>
                                </td>
                                <td class="p-4 pr-6 text-right">
                                    <a href="<?= base_url('/disputes/' . $dispute['id']) ?>" class="inline-flex items-center px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-primary-600 transition-colors">
                                        Detail
                                    </a>
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
