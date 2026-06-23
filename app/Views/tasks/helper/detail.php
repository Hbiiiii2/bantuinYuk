<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    
    <div class="mb-8 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <button onclick="history.back()" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-primary-600 transition-colors mb-4 focus:outline-none">
                <i class="ph-bold ph-arrow-left mr-2"></i> Kembali
            </button>
            <h1 class="text-3xl font-extrabold text-slate-900 font-display tracking-tight"><?= esc($task['title']) ?></h1>
            <p class="mt-2 text-sm text-slate-500 flex items-center gap-2">
                <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-bold bg-primary-50 text-primary-700 border border-primary-100">
                    <?= esc($task['category_name']) ?>
                </span>
                <span>•</span>
                <i class="ph-fill ph-calendar-blank text-slate-400"></i>
                <?= date('d M Y, H:i', strtotime($task['created_at'])) ?>
            </p>
        </div>
        
        <div>
            <?php if ($task['status'] === 'open'): ?>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-green-50 text-green-700 border border-green-200">
                    <span class="w-2 h-2 rounded-full bg-green-500 mr-2 animate-pulse"></span>
                    Tersedia
                </span>
            <?php elseif ($task['status'] === 'in_progress'): ?>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-amber-50 text-amber-700 border border-amber-200">
                    <span class="w-2 h-2 rounded-full bg-amber-500 mr-2"></span>
                    Sedang Dikerjakan
                </span>
            <?php elseif ($task['status'] === 'completed'): ?>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-slate-100 text-slate-600 border border-slate-200">
                    <i class="ph-bold ph-check mr-2"></i> Selesai
                </span>
            <?php endif; ?>
        </div>
    </div>

    <?php if (session()->has('error')): ?>
        <div class="bg-red-50/80 backdrop-blur-sm border border-red-100 p-4 mb-6 rounded-2xl flex items-start gap-3 animate-fade-in-up">
            <i class="ph-fill ph-warning-circle text-red-500 text-xl mt-0.5"></i>
            <p class="text-sm font-bold text-red-800"><?= session('error') ?></p>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                <h3 class="text-lg font-bold text-slate-900 mb-4 border-b border-slate-100 pb-4">Deskripsi Pekerjaan</h3>
                <div class="prose prose-sm prose-slate max-w-none text-slate-700 whitespace-pre-line">
                    <?= esc($task['description']) ?>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                <h3 class="text-lg font-bold text-slate-900 mb-4 border-b border-slate-100 pb-4">Lokasi</h3>
                <div class="flex items-start gap-3 text-slate-700">
                    <i class="ph-fill ph-map-pin text-primary-500 text-xl"></i>
                    <p><?= esc($task['location']) ?></p>
                </div>
            </div>

            <?php if ($task['helper_id'] == auth()->id() && in_array($task['status'], ['in_progress', 'completed'])): ?>
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                    <h3 class="text-lg font-bold text-slate-900 mb-4 border-b border-slate-100 pb-4">Progress Pekerjaan (Validasi)</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Foto Mulai Kerja -->
                        <div class="border border-slate-200 rounded-2xl p-5 relative">
                            <p class="text-sm font-bold text-slate-900 mb-3 flex items-center">
                                1. Foto Mulai Kerja
                                <?php if (!empty($task['photo_start'])): ?>
                                    <i class="ph-fill ph-check-circle text-emerald-500 ml-2"></i>
                                <?php endif; ?>
                            </p>
                            
                            <?php if (empty($task['photo_start'])): ?>
                                <p class="text-xs text-slate-500 mb-4">Wajib diunggah saat Anda tiba di lokasi dan mulai bekerja.</p>
                                <form action="<?= base_url('/helper/tasks/' . $task['id'] . '/upload-progress') ?>" method="post" enctype="multipart/form-data" class="flex flex-col gap-3">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="type" value="start">
                                    <input type="file" name="photo" accept=".jpg,.jpeg,.png,.pdf" class="text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" required>
                                    <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition-colors w-full">Upload Foto Mulai</button>
                                </form>
                            <?php else: ?>
                                <div class="rounded-xl overflow-hidden mb-2 aspect-video bg-slate-100 relative group cursor-pointer" onclick="window.open('<?= base_url($task['photo_start']) ?>', '_blank')">
                                    <img src="<?= base_url($task['photo_start']) ?>" alt="Foto Mulai" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <i class="ph-bold ph-arrows-out text-white text-2xl"></i>
                                    </div>
                                </div>
                                <p class="text-[10px] text-center text-emerald-600 font-bold bg-emerald-50 py-1 rounded-lg">Berhasil diunggah</p>
                            <?php endif; ?>
                        </div>

                        <!-- Foto Hasil Akhir -->
                        <div class="border border-slate-200 rounded-2xl p-5 relative <?= empty($task['photo_start']) ? 'opacity-50 pointer-events-none' : '' ?>">
                            <p class="text-sm font-bold text-slate-900 mb-3 flex items-center">
                                2. Foto Hasil Akhir
                                <?php if (!empty($task['photo_end'])): ?>
                                    <i class="ph-fill ph-check-circle text-emerald-500 ml-2"></i>
                                <?php endif; ?>
                            </p>
                            
                            <?php if (empty($task['photo_end'])): ?>
                                <p class="text-xs text-slate-500 mb-4">Wajib diunggah saat pekerjaan selesai agar *User* dapat melakukan penyelesaian.</p>
                                <?php if (!empty($task['photo_start'])): ?>
                                    <form action="<?= base_url('/helper/tasks/' . $task['id'] . '/upload-progress') ?>" method="post" enctype="multipart/form-data" class="flex flex-col gap-3">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="type" value="end">
                                        <input type="file" name="photo" accept=".jpg,.jpeg,.png,.pdf" class="text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" required>
                                        <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition-colors w-full">Upload Hasil Akhir</button>
                                    </form>
                                <?php else: ?>
                                    <div class="px-4 py-2 bg-slate-100 text-slate-400 rounded-xl text-xs font-bold text-center border border-slate-200">Unggah foto mulai terlebih dahulu</div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="rounded-xl overflow-hidden mb-2 aspect-video bg-slate-100 relative group cursor-pointer" onclick="window.open('<?= base_url($task['photo_end']) ?>', '_blank')">
                                    <img src="<?= base_url($task['photo_end']) ?>" alt="Foto Akhir" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <i class="ph-bold ph-arrows-out text-white text-2xl"></i>
                                    </div>
                                </div>
                                <p class="text-[10px] text-center text-emerald-600 font-bold bg-emerald-50 py-1 rounded-lg">Berhasil diunggah</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-slate-100">
                        <p class="text-sm text-slate-500 mb-4">Ada masalah dari pihak User? Ajukan komplain untuk mediasi admin.</p>
                        <a href="<?= base_url('/disputes/create/' . $task['id']) ?>" class="inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-red-600 transition-all duration-300 bg-red-50 rounded-xl hover:bg-red-100 border border-red-100 w-full sm:w-auto">
                            <i class="ph-bold ph-warning-octagon mr-2"></i> Laporkan Masalah
                        </a>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($task['status'] === 'open'): ?>
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                    <h3 class="text-lg font-bold text-slate-900 mb-4 border-b border-slate-100 pb-4">Ambil Pekerjaan Ini</h3>
                    <p class="text-sm text-slate-500 mb-4">Pastikan Anda dapat menyelesaikan pekerjaan ini sebelum mengambilnya. Setelah mengambil pekerjaan, Anda akan dapat melihat kontak pembuat task.</p>
                    <form action="<?= base_url('/helper/tasks/' . $task['id'] . '/take') ?>" method="post">
                        <?= csrf_field() ?>
                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin mengambil pekerjaan ini?')" class="inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-white transition-all duration-300 bg-primary-600 rounded-xl hover:bg-primary-700 hover:shadow-lg hover:-translate-y-0.5 w-full sm:w-auto">
                            <i class="ph-bold ph-handshake mr-2"></i> Ambil Pekerjaan
                        </button>
                    </form>
                </div>
            <?php endif; ?>

            <?php if ($task['status'] === 'completed' && $task['helper_id'] == auth()->id()): ?>
                <?php
                    $reviewModel = new \App\Models\TaskReviewModel();
                    $existingReview = $reviewModel->getReview($task['id'], auth()->id());
                ?>
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                    <h3 class="text-lg font-bold text-slate-900 mb-4 border-b border-slate-100 pb-4">Ulasan untuk Pembuat Task</h3>
                    
                    <?php if ($existingReview): ?>
                        <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100">
                            <div class="flex items-center gap-1 mb-3">
                                <?php for($i=1; $i<=5; $i++): ?>
                                    <i class="ph-fill ph-star text-xl <?= $i <= $existingReview['rating'] ? 'text-amber-400' : 'text-slate-300' ?>"></i>
                                <?php endfor; ?>
                                <span class="ml-2 text-sm font-bold text-slate-700"><?= $existingReview['rating'] ?>/5 Bintang</span>
                            </div>
                            <p class="text-slate-600 text-sm italic">"<?= esc($existingReview['review']) ?>"</p>
                        </div>
                    <?php else: ?>
                        <form action="<?= base_url('/helper/tasks/' . $task['id'] . '/rate') ?>" method="post">
                            <?= csrf_field() ?>
                            
                            <div class="mb-5">
                                <label class="block text-sm font-bold text-slate-700 mb-2">Penilaian Bintang</label>
                                <div class="flex items-center gap-2 flex-row-reverse justify-end peer-group">
                                    <style>
                                        .star-rating input { display: none; }
                                        .star-rating label { cursor: pointer; color: #cbd5e1; font-size: 2rem; transition: color 0.2s; }
                                        .star-rating label:hover,
                                        .star-rating label:hover ~ label,
                                        .star-rating input:checked ~ label { color: #fbbf24; }
                                    </style>
                                    <div class="star-rating flex flex-row-reverse justify-end">
                                        <input type="radio" id="star5" name="rating" value="5" required /><label for="star5" title="5 stars"><i class="ph-fill ph-star"></i></label>
                                        <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="4 stars"><i class="ph-fill ph-star"></i></label>
                                        <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="3 stars"><i class="ph-fill ph-star"></i></label>
                                        <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="2 stars"><i class="ph-fill ph-star"></i></label>
                                        <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="1 star"><i class="ph-fill ph-star"></i></label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-5">
                                <label for="review" class="block text-sm font-bold text-slate-700 mb-2">Ulasan (Opsional)</label>
                                <textarea id="review" name="review" rows="3" class="block w-full px-4 py-3 bg-slate-50 border-transparent rounded-xl text-sm transition-all duration-200 focus:bg-white focus:ring-2 focus:ring-primary-500 focus:border-transparent hover:bg-slate-100/80" placeholder="Ceritakan pengalaman Anda bekerja dengan pengguna ini..."></textarea>
                            </div>
                            
                            <button type="submit" class="inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-white transition-all duration-300 bg-slate-900 rounded-xl hover:bg-primary-600 hover:shadow-lg hover:-translate-y-0.5">
                                <i class="ph-bold ph-paper-plane-tilt mr-2"></i> Kirim Ulasan
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                <p class="text-sm font-semibold text-slate-500 mb-1">Upah</p>
                <div class="text-3xl font-extrabold text-slate-900 mb-6">
                    Rp <?= number_format($task['price'], 0, ',', '.') ?>
                </div>

                <div class="border-t border-slate-100 pt-4">
                    <p class="text-sm font-semibold text-slate-500 mb-3">Informasi Pembuat Task</p>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold">
                            <?= substr($task['user_name'], 0, 1) ?>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-900"><?= esc($task['user_name']) ?></p>
                            <p class="text-xs text-slate-500">Pemilik Task</p>
                        </div>
                    </div>
                    
                    <?php if ($task['helper_id'] == auth()->id() && $task['status'] === 'in_progress'): ?>
                        <div class="bg-green-50 rounded-xl p-4 border border-green-100">
                            <p class="text-xs font-bold text-green-800 mb-1">Kontak yang dapat dihubungi:</p>
                            <div class="flex items-center gap-2 text-green-700 font-medium">
                                <i class="ph-fill ph-phone"></i>
                                <?= esc($task['user_phone'] ?? 'Tidak ada nomor telepon') ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if ($task['helper_id'] == auth()->id() && $task['status'] === 'in_progress'): ?>
                <div class="bg-amber-50 rounded-3xl border border-amber-100 p-6">
                    <div class="flex items-start gap-3">
                        <i class="ph-fill ph-info text-amber-500 text-xl shrink-0"></i>
                        <p class="text-sm text-amber-800">Anda sedang mengerjakan task ini. Silakan hubungi pembuat task dan selesaikan pekerjaan. Pembuat task akan menandai pekerjaan ini sebagai selesai.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
