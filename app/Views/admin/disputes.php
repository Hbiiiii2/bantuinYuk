<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    
    <div class="mb-8 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <a href="<?= base_url('/admin/dashboard') ?>" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-primary-600 transition-colors mb-4">
                <i class="ph-bold ph-arrow-left mr-2"></i> Kembali ke Dashboard
            </a>
            <h1 class="text-3xl font-extrabold text-slate-900 font-display tracking-tight">Manajemen Komplain</h1>
            <p class="mt-1 text-sm text-slate-500">Tinjau dan ambil keputusan terhadap sengketa *Task* (Disputes).</p>
        </div>
    </div>

    <!-- Quick Navigation (Admin specific) -->
    <div class="mb-10 bg-white p-2 rounded-2xl shadow-sm border border-slate-100 flex overflow-x-auto gap-2">
        <a href="<?= base_url('/admin/dashboard') ?>" class="px-5 py-2.5 rounded-xl text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-bold text-sm whitespace-nowrap transition-colors">
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
        <a href="<?= base_url('/admin/disputes') ?>" class="px-5 py-2.5 rounded-xl bg-primary-50 text-primary-700 font-bold text-sm whitespace-nowrap transition-colors">
            <i class="ph-bold ph-shield-warning mr-1.5"></i> Komplain
        </a>
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
                        <th class="p-4 pl-6">Alasan Komplain</th>
                        <th class="p-4">Pelapor</th>
                        <th class="p-4">Tanggal</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 pr-6 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($disputes)): ?>
                        <tr>
                            <td colspan="5" class="p-16 text-center text-slate-500">
                                Tidak ada komplain yang perlu ditinjau.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($disputes as $dispute): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="p-4 pl-6 max-w-sm">
                                    <p class="font-bold text-slate-900 line-clamp-2 text-sm leading-snug"><?= esc($dispute['reason']) ?></p>
                                    <p class="text-xs text-slate-500 mt-1 font-medium truncate">Tugas: <?= esc($dispute['task_title'] ?? 'N/A') ?></p>
                                </td>
                                <td class="p-4">
                                    <p class="text-sm font-bold text-slate-900"><?= esc($dispute['creator_name']) ?></p>
                                </td>
                                <td class="p-4 text-sm text-slate-600">
                                    <?= date('d M Y, H:i', strtotime($dispute['created_at'])) ?>
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
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] uppercase tracking-wider font-bold border <?= $statusClass ?>">
                                        <?= str_replace('_', ' ', $dispute['status']) ?>
                                    </span>
                                </td>
                                <td class="p-4 pr-6 text-right">
                                    <?php if (in_array($dispute['status'], ['open', 'under_review'])): ?>
                                        <a href="<?= base_url('/admin/disputes/' . $dispute['id']) ?>" class="inline-flex items-center px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-primary-600 hover:bg-primary-50 hover:border-primary-200 transition-colors">
                                            Tinjau & Putuskan
                                        </a>

                                    <?php else: ?>
                                        <span class="text-xs text-slate-400 font-bold">Closed</span>
                                    <?php endif; ?>
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
