<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-3xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    
    <div class="mb-8">
        <a href="javascript:history.back()" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-primary-600 transition-colors mb-4">
            <i class="ph-bold ph-arrow-left mr-2"></i> Kembali
        </a>
        <h1 class="text-3xl font-extrabold text-slate-900 font-display tracking-tight">Ajukan Komplain</h1>
        <p class="mt-1 text-sm text-slate-500">Laporkan masalah pada pekerjaan "<?= esc($task['title']) ?>"</p>
    </div>

    <?php if (session()->has('error')): ?>
        <div class="bg-red-50 border border-red-100 p-4 mb-6 rounded-2xl flex items-start gap-3">
            <i class="ph-fill ph-warning-circle text-red-500 text-xl mt-0.5"></i>
            <p class="text-sm font-bold text-red-800"><?= session('error') ?></p>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 md:p-8">
            <div class="bg-amber-50 rounded-2xl p-4 mb-8 flex items-start gap-3 border border-amber-100">
                <i class="ph-fill ph-info text-amber-500 text-xl mt-0.5"></i>
                <div class="text-sm text-amber-800">
                    <p class="font-bold mb-1">Perhatian sebelum mengajukan komplain:</p>
                    <ul class="list-disc ml-4 space-y-1 opacity-90">
                        <li>Komplain akan membekukan pembayaran sementara waktu.</li>
                        <li>Pastikan Anda telah mencoba berkomunikasi dengan pihak terkait terlebih dahulu.</li>
                        <li>Berikan alasan yang jelas agar Admin dapat memberikan keputusan yang adil.</li>
                    </ul>
                </div>
            </div>

            <form action="<?= base_url('/disputes/store/' . $task['id']) ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <div class="mb-6">
                    <label for="reason" class="block text-sm font-bold text-slate-700 mb-2">Alasan Komplain</label>
                    <textarea name="reason" id="reason" rows="5" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all resize-none text-sm" placeholder="Jelaskan secara detail apa masalah yang terjadi pada pekerjaan ini..." required><?= old('reason') ?></textarea>
                    <?php if(session('errors.reason')): ?>
                        <p class="text-xs text-red-500 mt-2 font-medium"><?= session('errors.reason') ?></p>
                    <?php endif; ?>
                </div>

                <!-- Lampiran Bukti -->
                <div class="mb-8">
                    <label for="evidence" class="block text-sm font-bold text-slate-700 mb-2">Lampiran Bukti (Opsional, bisa lebih dari 1 file)</label>
                    <input type="file" name="evidence[]" id="evidence" accept=".jpg,.jpeg,.png,.pdf" multiple class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer border border-slate-200 rounded-xl bg-slate-50">
                    <p class="text-xs text-slate-400 mt-2">Maks. 2MB per file. Format: JPG, PNG, PDF.</p>
                    <?php if(session('errors.evidence')): ?>
                        <p class="text-xs text-red-500 mt-2 font-medium"><?= session('errors.evidence') ?></p>
                    <?php endif; ?>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                    <a href="javascript:history.back()" class="px-6 py-3 text-sm font-bold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-3 text-sm font-bold text-white bg-red-600 rounded-xl hover:bg-red-700 transition-colors shadow-sm shadow-red-500/30">
                        Kirim Komplain
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
