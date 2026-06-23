<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    
    <div class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 font-display tracking-tight">Notifikasi</h1>
            <p class="mt-1 text-sm text-slate-500">Pemberitahuan terbaru tentang aktivitas akun dan pekerjaan Anda.</p>
        </div>
        <?php if (!empty($notifications)): ?>
            <div class="flex items-center gap-2">
                <form action="<?= base_url('/notifications/mark-all-read') ?>" method="post">
                    <?= csrf_field() ?>
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 text-sm font-bold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors shadow-sm">
                        <i class="ph-bold ph-checks mr-2"></i> Tandai Semua Dibaca
                    </button>
                </form>
                <form action="<?= base_url('/notifications/delete-read') ?>" method="post" onsubmit="return confirm('Yakin ingin menghapus semua notifikasi yang sudah dibaca?')">
                    <?= csrf_field() ?>
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 text-sm font-bold text-red-600 bg-white border border-red-200 rounded-xl hover:bg-red-50 transition-colors shadow-sm">
                        <i class="ph-bold ph-trash mr-2"></i> Hapus Dibaca
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <?php if (session()->has('message')): ?>
        <div class="bg-green-50 border border-green-100 p-4 mb-6 rounded-2xl flex items-start gap-3">
            <i class="ph-fill ph-check-circle text-green-500 text-xl mt-0.5"></i>
            <p class="text-sm font-bold text-green-800"><?= session('message') ?></p>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <?php if (empty($notifications)): ?>
            <div class="p-16 text-center">
                <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 border border-slate-100">
                    <i class="ph-fill ph-bell-ringing text-slate-300 text-4xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Belum ada notifikasi</h3>
                <p class="text-sm text-slate-500 mb-8 max-w-sm mx-auto">Kami akan memberitahu Anda di sini jika ada pembaruan mengenai pekerjaan atau akun Anda.</p>
            </div>
        <?php else: ?>
            <ul class="divide-y divide-slate-100">
                <?php foreach ($notifications as $notification): ?>
                    <li class="relative hover:bg-slate-50 transition-colors <?= $notification['is_read'] == 0 ? 'bg-primary-50/30' : '' ?>">
                        <a href="<?= base_url('/notifications/' . $notification['id'] . '/read-and-redirect') ?>" class="flex items-start gap-4 p-6 w-full text-left block">
                            <?php 
                                $iconClass = match($notification['type']) {
                                    'task_created', 'task_accepted', 'task_started' => 'bg-blue-100 text-blue-600',
                                    'task_completed', 'payment_released' => 'bg-green-100 text-green-600',
                                    'task_cancelled', 'withdraw_rejected' => 'bg-red-100 text-red-600',
                                    default => 'bg-slate-100 text-slate-600'
                                };
                                $icon = match($notification['type']) {
                                    'task_created' => 'ph-briefcase',
                                    'task_completed' => 'ph-check-circle',
                                    'payment_released' => 'ph-money',
                                    default => 'ph-bell'
                                };
                            ?>
                            <div class="w-12 h-12 rounded-full flex items-center justify-center shrink-0 <?= $iconClass ?>">
                                <i class="ph-fill <?= $icon ?> text-xl"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-slate-900 <?= $notification['is_read'] == 0 ? 'text-primary-900' : '' ?>">
                                    <?= esc($notification['title']) ?>
                                </p>
                                <p class="text-sm text-slate-500 mt-1">
                                    <?= esc($notification['message']) ?>
                                </p>
                                <p class="text-xs font-medium text-slate-400 mt-2">
                                    <?= date('d M Y, H:i', strtotime($notification['created_at'])) ?>
                                </p>
                            </div>
                            <?php if ($notification['is_read'] == 0): ?>
                                <div class="shrink-0 flex flex-col items-end gap-2">
                                    <span class="w-3 h-3 bg-primary-500 rounded-full"></span>
                                </div>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
