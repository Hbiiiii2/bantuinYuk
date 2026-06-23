<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    
    <div class="mb-8 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <a href="<?= base_url('/admin/dashboard') ?>" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-primary-600 transition-colors mb-4">
                <i class="ph-bold ph-arrow-left mr-2"></i> Kembali ke Dashboard
            </a>
            <h1 class="text-3xl font-extrabold text-slate-900 font-display tracking-tight">Manajemen Pekerjaan</h1>
            <p class="mt-1 text-sm text-slate-500">Pantau seluruh aktivitas pekerjaan dan statusnya.</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-xs uppercase tracking-wider text-slate-500 font-semibold">
                        <th class="p-4 pl-6">Detail Pekerjaan</th>
                        <th class="p-4">Pembuat (User)</th>
                        <th class="p-4">Dikerjakan Oleh</th>
                        <th class="p-4">Nilai</th>
                        <th class="p-4 pr-6 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($tasks)): ?>
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-500">Belum ada aktivitas pekerjaan.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tasks as $task): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="p-4 pl-6 max-w-xs">
                                    <p class="font-bold text-slate-900 truncate"><?= esc($task['title']) ?></p>
                                    <div class="flex items-center text-xs text-slate-500 mt-1">
                                        <i class="ph-fill ph-calendar-blank mr-1"></i>
                                        <?= date('d M Y', strtotime($task['created_at'])) ?>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <p class="text-sm font-medium text-slate-900"><?= esc($task['user_name'] ?? 'Unknown') ?></p>
                                </td>
                                <td class="p-4">
                                    <?php if ($task['helper_id']): ?>
                                        <p class="text-sm font-medium text-accent-600"><?= esc($task['helper_name'] ?? 'Unknown') ?></p>
                                    <?php else: ?>
                                        <span class="text-xs text-slate-400 italic">Belum ada</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4">
                                    <p class="text-sm font-bold text-slate-900">Rp <?= number_format($task['price'], 0, ',', '.') ?></p>
                                </td>
                                <td class="p-4 pr-6 text-right">
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
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
