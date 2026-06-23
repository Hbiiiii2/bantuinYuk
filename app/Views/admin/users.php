<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    
    <div class="mb-8 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <a href="<?= base_url('/admin/dashboard') ?>" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-primary-600 transition-colors mb-4">
                <i class="ph-bold ph-arrow-left mr-2"></i> Kembali ke Dashboard
            </a>
            <h1 class="text-3xl font-extrabold text-slate-900 font-display tracking-tight">Manajemen User</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola akun pengguna reguler (Requester) di platform.</p>
        </div>
    </div>

    <?php if (session()->has('message')): ?>
        <div class="bg-green-50 border border-green-100 p-4 mb-6 rounded-2xl flex items-start gap-3">
            <i class="ph-fill ph-check-circle text-green-500 text-xl mt-0.5"></i>
            <p class="text-sm font-bold text-green-800"><?= session('message') ?></p>
        </div>
    <?php endif; ?>

    <?php if (session()->has('error')): ?>
        <div class="bg-red-50 border border-red-100 p-4 mb-6 rounded-2xl flex items-start gap-3">
            <i class="ph-fill ph-warning-circle text-red-500 text-xl mt-0.5"></i>
            <p class="text-sm font-bold text-red-800"><?= session('error') ?></p>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-xs uppercase tracking-wider text-slate-500 font-semibold">
                        <th class="p-4 pl-6">Pengguna</th>
                        <th class="p-4">Kontak</th>
                        <th class="p-4">Bergabung Pada</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 pr-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-500">Belum ada data user.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="p-4 pl-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold">
                                            <?= substr($user['name'] ?? 'U', 0, 1) ?>
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900"><?= esc($user['name'] ?? 'Tanpa Nama') ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <p class="text-sm font-medium text-slate-700"><?= esc($user['phone'] ?? '-') ?></p>
                                </td>
                                <td class="p-4 text-sm text-slate-600">
                                    <?= date('d M Y', strtotime($user['created_at'])) ?>
                                </td>
                                <td class="p-4">
                                    <?php if ($user['active'] == 1): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-50 text-green-700">Aktif</span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-50 text-red-700">Diblokir</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4 pr-6 text-right">
                                    <form action="<?= base_url('/admin/toggle-user/' . $user['id']) ?>" method="post">
                                        <?= csrf_field() ?>
                                        <button type="submit" onclick="return confirm('Anda yakin ingin mengubah status pengguna ini?')" class="inline-flex items-center px-3 py-1.5 border border-slate-200 rounded-lg text-xs font-bold <?= $user['active'] == 1 ? 'text-red-600 hover:bg-red-50 border-red-200' : 'text-green-600 hover:bg-green-50 border-green-200' ?> transition-colors">
                                            <?= $user['active'] == 1 ? 'Blokir' : 'Aktifkan' ?>
                                        </button>
                                    </form>
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
