<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    
    <div class="mb-8 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <a href="<?= base_url('/disputes') ?>" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-primary-600 transition-colors mb-4">
                <i class="ph-bold ph-arrow-left mr-2"></i> Kembali ke Daftar Komplain
            </a>
            <h1 class="text-3xl font-extrabold text-slate-900 font-display tracking-tight">Detail Komplain</h1>
            <p class="mt-1 text-sm text-slate-500">Kasus ID #<?= str_pad($dispute['id'], 5, '0', STR_PAD_LEFT) ?></p>
        </div>
        <div>
            <?php 
                $statusClass = match($dispute['status']) {
                    'open' => 'bg-amber-50 text-amber-700 border-amber-200',
                    'under_review' => 'bg-blue-50 text-blue-700 border-blue-200',
                    'resolved' => 'bg-green-50 text-green-700 border-green-200',
                    'rejected' => 'bg-red-50 text-red-700 border-red-200',
                    default => 'bg-slate-50 text-slate-700 border-slate-200'
                };
                $statusText = match($dispute['status']) {
                    'open' => 'Menunggu Tinjauan',
                    'under_review' => 'Sedang Ditinjau Admin',
                    'resolved' => 'Kasus Selesai',
                    'rejected' => 'Komplain Ditolak',
                    default => 'Unknown'
                };
            ?>
            <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold border <?= $statusClass ?>">
                <?= $statusText ?>
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            <!-- Dispute Details -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 md:p-8">
                <h3 class="text-lg font-bold text-slate-900 mb-6 font-display">Informasi Laporan</h3>
                
                <div class="space-y-6">
                    <div>
                        <p class="text-sm font-medium text-slate-500 mb-1">Pelapor</p>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-xs">
                                <?= substr($dispute['creator_name'] ?? 'U', 0, 1) ?>
                            </div>
                            <p class="text-sm font-bold text-slate-900"><?= esc($dispute['creator_name'] ?? 'Unknown User') ?></p>
                            <?php if ($dispute['user_id'] == auth()->id()): ?>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-primary-50 text-primary-600">Anda</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-slate-500 mb-2">Alasan Komplain</p>
                        <div class="bg-slate-50 rounded-xl p-5 text-sm text-slate-700 whitespace-pre-wrap leading-relaxed border border-slate-100"><?= esc($dispute['reason']) ?></div>
                    </div>

                    <?php if ($dispute['admin_note']): ?>
                        <div class="border-t border-slate-100 pt-6">
                            <div class="flex items-center gap-2 mb-3">
                                <i class="ph-fill ph-shield-check text-primary-500 text-xl"></i>
                                <h4 class="text-base font-bold text-slate-900">Keputusan Admin</h4>
                            </div>
                            <div class="bg-primary-50/50 rounded-xl p-5 text-sm text-slate-700 whitespace-pre-wrap leading-relaxed border border-primary-100"><?= esc($dispute['admin_note']) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Task Overview -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                <h3 class="text-base font-bold text-slate-900 mb-4 font-display">Terkait Pekerjaan</h3>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-xs font-medium text-slate-500 mb-1">Judul Pekerjaan</p>
                        <p class="text-sm font-bold text-slate-900"><?= esc($dispute['task_title'] ?? 'N/A') ?></p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-500 mb-1">Status Pekerjaan</p>
                        <p class="text-sm font-bold text-slate-900 uppercase"><?= esc($dispute['task_status'] ?? 'N/A') ?></p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-500 mb-1">Tanggal Dilaporkan</p>
                        <p class="text-sm font-bold text-slate-900"><?= date('d M Y, H:i', strtotime($dispute['created_at'])) ?></p>
                    </div>
                </div>

                <div class="mt-6 pt-5 border-t border-slate-100">
                    <a href="<?= base_url('/user/tasks/' . $dispute['task_id']) ?>" class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-bold text-primary-600 bg-primary-50 rounded-xl hover:bg-primary-100 transition-colors">
                        Lihat Pekerjaan
                    </a>
                </div>
            </div>
            
            <!-- Support Notice -->
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl shadow-xl p-6 relative overflow-hidden">
                <div class="absolute -right-4 -bottom-4 opacity-10">
                    <i class="ph-fill ph-lifebuoy text-8xl text-white"></i>
                </div>
                <div class="relative z-10">
                    <h4 class="text-white font-bold mb-2">Butuh Bantuan Lain?</h4>
                    <p class="text-slate-400 text-xs leading-relaxed mb-4">Jika Anda merasa keputusan tidak adil atau butuh bantuan lebih lanjut, silakan hubungi tim Support kami.</p>
                    <button class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-xs font-bold rounded-lg transition-colors backdrop-blur-md">
                        Hubungi Support
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
