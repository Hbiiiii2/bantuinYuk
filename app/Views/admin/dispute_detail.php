<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    
    <div class="mb-8 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <a href="<?= base_url('/admin/disputes') ?>" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-primary-600 transition-colors mb-4">
                <i class="ph-bold ph-arrow-left mr-2"></i> Kembali ke Daftar Komplain
            </a>
            <h1 class="text-3xl font-extrabold text-slate-900 font-display tracking-tight">Detail Komplain #<?= esc($dispute['id']) ?></h1>
            <p class="mt-1 text-sm text-slate-500">Tinjau kronologi dan putuskan penyelesaian dana untuk komplain ini.</p>
        </div>
        <div>
            <?php 
                $statusClass = match($dispute['status']) {
                    'open' => 'bg-amber-100 text-amber-800',
                    'under_review' => 'bg-blue-100 text-blue-800',
                    'resolved' => 'bg-green-100 text-green-800',
                    'rejected' => 'bg-red-100 text-red-800',
                    default => 'bg-slate-100 text-slate-800'
                };
            ?>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider <?= $statusClass ?>">
                Status: <?= str_replace('_', ' ', $dispute['status']) ?>
            </span>
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

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <!-- Informasi Komplain -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                <h2 class="text-xl font-bold text-slate-900 mb-6 border-b border-slate-100 pb-4">Informasi Laporan</h2>
                
                <div class="space-y-6">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Pekerjaan Terkait (Task)</p>
                        <p class="text-base font-medium text-slate-900"><?= esc($dispute['task_title'] ?? 'N/A') ?></p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Pelapor (User)</p>
                            <p class="text-base font-medium text-slate-900"><?= esc($dispute['creator_name'] ?? 'N/A') ?></p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Terlapor (Helper)</p>
                            <p class="text-base font-medium text-slate-900"><?= esc($dispute['helper_name'] ?? 'N/A') ?></p>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Alasan Komplain</p>
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 text-slate-700 text-sm leading-relaxed">
                            <?= nl2br(esc($dispute['reason'])) ?>
                        </div>
                    </div>

                    <?php if (!empty($dispute['evidence_file'])): ?>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Bukti Lampiran</p>
                        <?php 
                            $evidenceFiles = json_decode($dispute['evidence_file'], true) ?? [$dispute['evidence_file']]; 
                        ?>
                        <div class="flex flex-wrap gap-3">
                            <?php foreach($evidenceFiles as $file): ?>
                                <?php 
                                    $ext = pathinfo($file, PATHINFO_EXTENSION); 
                                    $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png']);
                                ?>
                                <a href="<?= base_url(esc($file)) ?>" target="_blank" class="block w-24 h-24 rounded-xl border border-slate-200 overflow-hidden group relative hover:border-primary-500 transition-colors">
                                    <?php if($isImage): ?>
                                        <img src="<?= base_url(esc($file)) ?>" class="w-full h-full object-cover" alt="Bukti">
                                    <?php else: ?>
                                        <div class="w-full h-full bg-slate-50 flex items-center justify-center">
                                            <i class="ph-bold ph-file-pdf text-3xl text-red-400"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <i class="ph-bold ph-magnifying-glass-plus text-white text-xl"></i>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- KYC Data Helper -->
            <?php if (isset($helperProfile) && isset($helperUser)): ?>
            <div class="bg-red-50 rounded-3xl shadow-sm border border-red-100 p-8">
                <h2 class="text-xl font-bold text-red-900 mb-4 border-b border-red-200 pb-4 flex items-center gap-2">
                    <i class="ph-bold ph-identification-card"></i> Data Identitas Terlapor (Helper)
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm font-bold text-red-800 mb-1">Nama Lengkap (Sesuai KTP)</p>
                        <p class="text-sm text-red-900 bg-white px-3 py-2 rounded-lg border border-red-200"><?= esc($helperProfile['ktp_name'] ?? $helperUser->name) ?></p>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-red-800 mb-1">Nomor Induk Kependudukan (NIK)</p>
                        <p class="text-sm text-red-900 bg-white px-3 py-2 rounded-lg border border-red-200 font-mono"><?= esc($helperProfile['ktp_number'] ?? '-') ?></p>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-red-800 mb-1">Nomor Telepon / Kontak</p>
                        <p class="text-sm text-red-900 bg-white px-3 py-2 rounded-lg border border-red-200"><?= esc($helperUser->phone ?? '-') ?></p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-sm font-bold text-red-800 mb-1">Alamat Lengkap</p>
                        <p class="text-sm text-red-900 bg-white px-3 py-2 rounded-lg border border-red-200"><?= esc($helperProfile['address'] ?? '-') ?></p>
                    </div>
                </div>
                <div class="mt-6 flex gap-4">
                    <?php if (!empty($helperProfile['ktp_photo'])): ?>
                    <div>
                        <p class="text-sm font-bold text-red-800 mb-2">Foto KTP</p>
                        <a href="<?= base_url(esc($helperProfile['ktp_photo'])) ?>" target="_blank" class="block w-40 aspect-video rounded-xl border-2 border-red-200 overflow-hidden group relative hover:border-red-400 transition-colors">
                            <img src="<?= base_url(esc($helperProfile['ktp_photo'])) ?>" class="w-full h-full object-cover" alt="KTP">
                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <i class="ph-bold ph-magnifying-glass-plus text-white text-2xl"></i>
                            </div>
                        </a>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($helperProfile['selfie_photo'])): ?>
                    <div>
                        <p class="text-sm font-bold text-red-800 mb-2">Selfie KTP</p>
                        <a href="<?= base_url(esc($helperProfile['selfie_photo'])) ?>" target="_blank" class="block w-40 aspect-video rounded-xl border-2 border-red-200 overflow-hidden group relative hover:border-red-400 transition-colors">
                            <img src="<?= base_url(esc($helperProfile['selfie_photo'])) ?>" class="w-full h-full object-cover" alt="Selfie KTP">
                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <i class="ph-bold ph-magnifying-glass-plus text-white text-2xl"></i>
                            </div>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                <p class="text-xs text-red-600 mt-4 italic">*Data identitas ini bersifat rahasia dan hanya diperuntukkan bagi keperluan laporan administratif/kepolisian bila Terlapor terbukti melakukan tindak pidana.</p>
            </div>
            <?php endif; ?>

            <?php if (in_array($dispute['status'], ['open', 'under_review'])): ?>
            <!-- Keputusan Admin Form -->
            <div class="bg-white rounded-3xl shadow-sm border-2 border-primary-100 p-8">
                <h2 class="text-xl font-bold text-slate-900 mb-2 border-b border-slate-100 pb-4">Tinjau & Ambil Keputusan</h2>
                <p class="text-sm text-slate-500 mb-6">Tentukan penyelesaian dana dan berikan catatan keputusan akhir Anda.</p>

                <form method="post" action="<?= base_url('/admin/disputes/' . $dispute['id'] . '/resolve') ?>" class="space-y-6">
                    <?= csrf_field() ?>
                    
                    <div>
                        <p class="text-sm font-bold text-slate-800 mb-3">Tindakan Saldo Tertahan (Rp <?= number_format($task['price'] ?? 0, 0, ',', '.') ?>):</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="relative flex items-start p-4 cursor-pointer rounded-xl border border-slate-200 hover:bg-slate-50 transition-colors has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50/50 has-[:checked]:ring-1 has-[:checked]:ring-primary-500">
                                <div class="flex items-center h-5">
                                    <input name="fund_action" type="radio" value="refund" class="w-4 h-4 text-primary-600 bg-slate-100 border-slate-300 focus:ring-primary-500" required>
                                </div>
                                <div class="ml-3 text-sm">
                                    <span class="font-bold text-slate-900 block">Kembalikan ke User</span>
                                    <span class="text-slate-500 text-xs">Pekerjaan dianggap gagal/batal. Saldo di-refund ke User.</span>
                                </div>
                            </label>
                            
                            <label class="relative flex items-start p-4 cursor-pointer rounded-xl border border-slate-200 hover:bg-slate-50 transition-colors has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50/50 has-[:checked]:ring-1 has-[:checked]:ring-primary-500">
                                <div class="flex items-center h-5">
                                    <input name="fund_action" type="radio" value="release" class="w-4 h-4 text-primary-600 bg-slate-100 border-slate-300 focus:ring-primary-500" required>
                                </div>
                                <div class="ml-3 text-sm">
                                    <span class="font-bold text-slate-900 block">Cairkan ke Helper</span>
                                    <span class="text-slate-500 text-xs">Pekerjaan dianggap selesai. Saldo dicairkan ke Helper.</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Suspend Helper -->
                    <?php if (isset($helperUser) && $helperUser->active == 1): ?>
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                        <label class="relative flex items-start cursor-pointer">
                            <div class="flex items-center h-5">
                                <input name="suspend_helper" type="checkbox" value="1" class="w-5 h-5 text-red-600 bg-white border-red-300 rounded focus:ring-red-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <span class="font-bold text-red-900 block flex items-center gap-1"><i class="ph-bold ph-prohibit"></i> Blokir Akun Helper Permanen (Banned)</span>
                                <span class="text-red-700 text-xs">Centang ini jika Helper terbukti melakukan pelanggaran berat (pencurian, penipuan, kekerasan). Akun Helper akan dibekukan dan mereka tidak dapat lagi menggunakan aplikasi.</span>
                            </div>
                        </label>
                    </div>
                    <?php endif; ?>

                    <div>
                        <label class="text-sm font-bold text-slate-800 mb-2 block">Catatan Admin</label>
                        <textarea name="admin_note" rows="3" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Jelaskan alasan keputusan ini..." required></textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 pt-2">
                        <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-xl text-sm transition-colors flex items-center justify-center gap-2">
                            <i class="ph-bold ph-check-circle"></i> Eksekusi & Selesaikan
                        </button>
                    </div>
                </form>
            </div>
            <?php else: ?>
                <div class="bg-slate-50 rounded-3xl border border-slate-200 p-8">
                    <h2 class="text-xl font-bold text-slate-900 mb-6 border-b border-slate-200 pb-4">Keputusan Final Admin</h2>
                    
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Catatan Penyelesaian</p>
                        <div class="bg-white p-4 rounded-xl border border-slate-200 text-slate-700 text-sm leading-relaxed">
                            <?= nl2br(esc($dispute['admin_note'] ?? 'Tidak ada catatan.')) ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                <h3 class="font-bold text-slate-900 mb-4 border-b border-slate-100 pb-3">Ringkasan Sengketa</h3>
                <div class="space-y-4 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500">ID Komplain</span>
                        <span class="font-bold text-slate-900">#<?= esc($dispute['id']) ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500">Tanggal Lapor</span>
                        <span class="font-bold text-slate-900"><?= date('d M Y, H:i', strtotime($dispute['created_at'])) ?></span>
                    </div>
                    <?php if(!empty($dispute['resolved_at'])): ?>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500">Tgl Selesai</span>
                        <span class="font-bold text-slate-900"><?= date('d M Y, H:i', strtotime($dispute['resolved_at'])) ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="pt-3 border-t border-slate-100 flex justify-between items-center">
                        <span class="text-slate-500">Nilai Pekerjaan</span>
                        <span class="font-bold text-primary-600 text-lg">Rp <?= number_format($task['price'] ?? 0, 0, ',', '.') ?></span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<?= $this->endSection() ?>
