<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    
    <div class="mb-8 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <a href="<?= base_url('/user/tasks') ?>" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-primary-600 transition-colors mb-4">
                <i class="ph-bold ph-arrow-left mr-2"></i> Kembali ke Daftar Task
            </a>
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
                    Mencari Helper
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

    <?php if (session()->has('message')): ?>
        <div class="bg-green-50/80 backdrop-blur-sm border border-green-100 p-4 mb-6 rounded-2xl flex items-start gap-3 animate-fade-in-up">
            <i class="ph-fill ph-check-circle text-green-500 text-xl mt-0.5"></i>
            <p class="text-sm font-bold text-green-800"><?= session('message') ?></p>
        </div>
    <?php endif; ?>

    <?php if (session()->has('error')): ?>
        <div class="bg-red-50/80 backdrop-blur-sm border border-red-100 p-4 mb-6 rounded-2xl flex items-start gap-3 animate-fade-in-up">
            <i class="ph-fill ph-warning-circle text-red-500 text-xl mt-0.5"></i>
            <p class="text-sm font-bold text-red-800"><?= session('error') ?></p>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                <h3 class="text-lg font-bold text-slate-900 mb-4 border-b border-slate-100 pb-4">Deskripsi</h3>
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

            <?php if (in_array($task['status'], ['in_progress', 'completed']) && $task['helper_id']): ?>
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                    <h3 class="text-lg font-bold text-slate-900 mb-4 border-b border-slate-100 pb-4">Pantauan Progress Pekerjaan</h3>
                    
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
                                <div class="px-4 py-6 bg-slate-50 text-slate-400 rounded-xl text-xs font-bold text-center border border-dashed border-slate-200 flex flex-col items-center gap-2">
                                    <i class="ph ph-image text-2xl text-slate-300"></i>
                                    Menunggu Helper...
                                </div>
                            <?php else: ?>
                                <div class="rounded-xl overflow-hidden mb-2 aspect-video bg-slate-100 relative group cursor-pointer" onclick="window.open('<?= base_url($task['photo_start']) ?>', '_blank')">
                                    <img src="<?= base_url($task['photo_start']) ?>" alt="Foto Mulai" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <i class="ph-bold ph-arrows-out text-white text-2xl"></i>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Foto Hasil Akhir -->
                        <div class="border border-slate-200 rounded-2xl p-5 relative">
                            <p class="text-sm font-bold text-slate-900 mb-3 flex items-center">
                                2. Foto Hasil Akhir
                                <?php if (!empty($task['photo_end'])): ?>
                                    <i class="ph-fill ph-check-circle text-emerald-500 ml-2"></i>
                                <?php endif; ?>
                            </p>
                            
                            <?php if (empty($task['photo_end'])): ?>
                                <div class="px-4 py-6 bg-slate-50 text-slate-400 rounded-xl text-xs font-bold text-center border border-dashed border-slate-200 flex flex-col items-center gap-2">
                                    <i class="ph ph-image text-2xl text-slate-300"></i>
                                    Menunggu Helper...
                                </div>
                            <?php else: ?>
                                <div class="rounded-xl overflow-hidden mb-2 aspect-video bg-slate-100 relative group cursor-pointer" onclick="window.open('<?= base_url($task['photo_end']) ?>', '_blank')">
                                    <img src="<?= base_url($task['photo_end']) ?>" alt="Foto Akhir" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <i class="ph-bold ph-arrows-out text-white text-2xl"></i>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($task['status'] === 'in_progress'): ?>
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                    <h3 class="text-lg font-bold text-slate-900 mb-4 border-b border-slate-100 pb-4">Aksi</h3>
                    
                    <?php if (!empty($task['photo_start']) && !empty($task['photo_end'])): ?>
                        <p class="text-sm text-slate-500 mb-4">Jika Anda merasa puas dengan hasil pekerjaan yang difoto, silakan selesaikan task ini.</p>
                        <form action="<?= base_url('/user/tasks/' . $task['id'] . '/complete') ?>" method="post">
                            <?= csrf_field() ?>
                            <button type="submit" class="inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-white transition-all duration-300 bg-green-600 rounded-xl hover:bg-green-700 hover:shadow-lg hover:-translate-y-0.5 w-full sm:w-auto">
                                <i class="ph-bold ph-check-circle mr-2"></i> Selesaikan Pekerjaan
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="bg-amber-50 rounded-xl p-4 border border-amber-100">
                            <div class="flex items-start gap-3">
                                <i class="ph-fill ph-lock-key text-amber-500 text-xl shrink-0 mt-0.5"></i>
                                <div>
                                    <p class="text-sm font-bold text-amber-800 mb-1">Penyelesaian Terkunci</p>
                                    <p class="text-xs text-amber-700">Tombol penyelesaian pekerjaan akan aktif setelah Helper mengunggah <b>Foto Mulai Kerja</b> dan <b>Foto Hasil Akhir</b>.</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($task['status'] === 'completed'): ?>
                <?php
                    $reviewModel = new \App\Models\TaskReviewModel();
                    $existingReview = $reviewModel->getReview($task['id'], auth()->id());
                ?>
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                    <h3 class="text-lg font-bold text-slate-900 mb-4 border-b border-slate-100 pb-4">Ulasan untuk Helper</h3>
                    
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
                        <form action="<?= base_url('/user/tasks/' . $task['id'] . '/rate') ?>" method="post">
                            <?= csrf_field() ?>
                            
                            <div class="mb-5">
                                <label class="block text-sm font-bold text-slate-700 mb-2">Penilaian Bintang</label>
                                <div class="flex items-center gap-2 flex-row-reverse justify-end peer-group">
                                    <!-- Simple CSS-based star rating -->
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
                                <textarea id="review" name="review" rows="3" class="block w-full px-4 py-3 bg-slate-50 border-transparent rounded-xl text-sm transition-all duration-200 focus:bg-white focus:ring-2 focus:ring-primary-500 focus:border-transparent hover:bg-slate-100/80" placeholder="Ceritakan pengalaman Anda dibantu oleh helper ini..."></textarea>
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
                    <p class="text-sm font-semibold text-slate-500 mb-3">Informasi Helper</p>
                    <?php if ($task['helper_id']): ?>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold">
                                <?= substr($task['helper_name'], 0, 1) ?>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900"><?= esc($task['helper_name']) ?></p>
                                <p class="text-xs text-slate-500">Helper Terpilih</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                            <i class="ph-fill ph-user-circle-dashed text-slate-300 text-3xl mb-1"></i>
                            <p class="text-sm text-slate-500 font-medium">Belum ada helper</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if ($task['status'] === 'open'): ?>
                <div class="bg-primary-50 rounded-3xl border border-primary-100 p-6">
                    <div class="flex items-start gap-3">
                        <i class="ph-fill ph-info text-primary-500 text-xl shrink-0"></i>
                        <p class="text-sm text-primary-800">Task Anda sedang terbuka. Helper di sekitar Anda dapat melihat dan mengambil pekerjaan ini.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
