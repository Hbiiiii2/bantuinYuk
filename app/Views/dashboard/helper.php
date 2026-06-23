<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="{ showKycModal: <?= (isset($helperProfile) && $helperProfile['verification_status'] === 'pending' && !session()->has('error')) ? 'true' : (session()->has('error') ? 'true' : 'false') ?> }" class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    
    <!-- Welcome Header -->
    <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <div class="inline-flex items-center px-3 py-1 rounded-full bg-accent-50 text-accent-600 text-xs font-bold tracking-wide uppercase mb-3 border border-accent-100">
                Mitra Helper Dashboard
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-display tracking-tight">Siap beraksi, <?= esc(strtok(auth()->user()->name, " ")) ?>? 🚀</h1>
            <p class="mt-2 text-base text-slate-500">Temukan pekerjaan baru dan pantau performa Anda.</p>
        </div>
        <div>
            <a href="<?= base_url('/helper/tasks/explore') ?>" class="w-full md:w-auto inline-flex items-center justify-center px-6 py-3.5 text-sm font-bold text-slate-900 transition-all duration-300 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:shadow-lg hover:shadow-slate-200/50 hover:-translate-y-0.5 group">
                <i class="ph-bold ph-magnifying-glass mr-2 group-hover:text-accent-500 transition-colors"></i>
                Cari Pekerjaan
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <!-- Earnings Card -->
        <div class="relative bg-gradient-to-br from-accent-600 to-accent-800 rounded-3xl p-6 shadow-xl shadow-accent-500/20 overflow-hidden group">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white/10 rounded-full filter blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10 flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-accent-100 mb-1">Saldo Pendapatan</p>
                    <h3 class="text-3xl font-display font-bold text-white tracking-tight">Rp <?= number_format($wallet['balance'] ?? 0, 0, ',', '.') ?></h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-sm flex items-center justify-center border border-white/20">
                    <i class="ph-fill ph-money text-white text-2xl"></i>
                </div>
            </div>
            <div class="mt-6 pt-5 border-t border-white/20">
                <a href="<?= base_url('/wallet') ?>" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-white/20 hover:bg-white/30 text-white font-bold text-sm rounded-xl transition-colors backdrop-blur-md">
                    Tarik Dana Sekarang
                </a>
            </div>
        </div>

        <!-- Completed Jobs -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-slate-500 mb-1">Pekerjaan Selesai</p>
                    <h3 class="text-3xl font-display font-bold text-slate-900 tracking-tight"><?= $completedTasks ?></h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center border border-emerald-100/50 text-emerald-600">
                    <i class="ph-fill ph-check-circle text-2xl"></i>
                </div>
            </div>
            <div class="mt-8">
                <div class="w-full bg-slate-100 rounded-full h-1.5">
                    <div class="bg-emerald-500 h-1.5 rounded-full" style="width: <?= $totalTasks > 0 ? min(100, ($completedTasks / $totalTasks) * 100) : 0 ?>%"></div>
                </div>
                <p class="text-xs font-medium text-slate-500 mt-2 text-right"><?= $activeTasks ?> pekerjaan sedang berjalan</p>
            </div>
        </div>

        <!-- Rating -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 flex flex-col justify-between hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-sm font-medium text-slate-500 mb-1">Rating Rata-rata</p>
                    <h3 class="text-3xl font-display font-bold text-slate-900 tracking-tight"><?= number_format($rating, 1, ',', '.') ?> <span class="text-lg font-medium text-slate-400">/ 5.0</span></h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-orange-50 flex items-center justify-center border border-orange-100/50 text-orange-500">
                    <i class="ph-fill ph-star text-2xl"></i>
                </div>
            </div>
            <div class="flex gap-1 mb-2">
                <?php for($i = 1; $i <= 5; $i++): ?>
                    <?php if ($rating >= $i): ?>
                        <i class="ph-fill ph-star text-orange-400 text-lg"></i>
                    <?php elseif ($rating >= $i - 0.5): ?>
                        <i class="ph-fill ph-star-half text-orange-400 text-lg"></i>
                    <?php else: ?>
                        <i class="ph-fill ph-star text-slate-200 text-lg"></i>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
            <p class="text-xs font-medium text-slate-500">Berdasarkan ulasan pelanggan</p>
        </div>
    </div>

    <!-- Open Jobs Section -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden mb-10">
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-lg font-bold text-slate-900 font-display">Peluang Pekerjaan Baru</h3>
            <a href="<?= base_url('/helper/tasks/explore') ?>" class="text-sm font-bold text-primary-600 hover:text-primary-700 transition-colors inline-flex items-center">
                Eksplor Semua <i class="ph-bold ph-arrow-right ml-1"></i>
            </a>
        </div>
        
        <?php if (empty($openTasks)): ?>
            <div class="p-12 text-center">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-5 border border-slate-100">
                    <i class="ph-fill ph-magnifying-glass text-slate-300 text-3xl"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-1">Belum ada pekerjaan baru</h3>
                <p class="text-sm text-slate-500 mb-6 max-w-sm mx-auto">Saat ini belum ada tugas baru yang tersedia untuk dikerjakan.</p>
            </div>
        <?php else: ?>
            <ul class="divide-y divide-slate-100">
                <?php foreach ($openTasks as $task): ?>
                    <li class="group hover:bg-slate-50/80 transition-colors">
                        <a href="<?= base_url('/helper/tasks/' . $task['id']) ?>" class="block px-6 py-5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-5">
                                    <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-center text-slate-400 group-hover:border-primary-200 group-hover:text-primary-500 transition-colors">
                                        <i class="ph-fill ph-briefcase text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-base font-bold text-slate-900 truncate group-hover:text-primary-700 transition-colors"><?= esc($task['title']) ?></p>
                                        <div class="flex items-center gap-3 mt-1">
                                            <span class="inline-flex items-center justify-center px-2 py-0.5 rounded text-[10px] font-bold bg-primary-50 text-primary-700 border border-primary-100">
                                                <?= esc($task['category_name']) ?>
                                            </span>
                                            <p class="text-sm font-semibold text-emerald-600">
                                                Rp <?= number_format($task['price'], 0, ',', '.') ?>
                                            </p>
                                            <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                            <p class="text-xs font-medium text-slate-500 flex items-center">
                                                <i class="ph-fill ph-user mr-1 text-slate-400"></i>
                                                <?= esc($task['user_name']) ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-primary-600 text-white hover:bg-primary-700 transition-colors">
                                        Ambil Pekerjaan
                                    </span>
                                </div>
                            </div>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <!-- Active Jobs Section -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-lg font-bold text-slate-900 font-display">Pekerjaan Saat Ini</h3>
            <a href="<?= base_url('/helper/tasks/my-tasks') ?>" class="text-sm font-bold text-accent-600 hover:text-accent-700 transition-colors inline-flex items-center">
                Semua Pekerjaan <i class="ph-bold ph-arrow-right ml-1"></i>
            </a>
        </div>
        
        <?php if (empty($tasks)): ?>
            <div class="p-12 text-center">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-5 border border-slate-100">
                    <i class="ph-fill ph-toolbox text-slate-300 text-3xl"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-1">Belum ada pekerjaan aktif</h3>
                <p class="text-sm text-slate-500 mb-6 max-w-sm mx-auto">Anda belum mengambil pekerjaan apapun saat ini. Segera cari pekerjaan yang cocok dengan keahlian Anda.</p>
                <a href="<?= base_url('/helper/tasks/explore') ?>" class="inline-flex items-center px-6 py-2.5 rounded-full text-sm font-bold text-accent-700 bg-accent-50 hover:bg-accent-100 transition-colors">
                    Cari Pekerjaan Sekarang
                </a>
            </div>
        <?php else: ?>
            <ul class="divide-y divide-slate-100">
                <?php foreach ($tasks as $task): ?>
                    <li class="group hover:bg-slate-50/80 transition-colors">
                        <a href="<?= base_url('/helper/tasks/' . $task['id']) ?>" class="block px-6 py-5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-5">
                                    <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-center text-slate-400 group-hover:border-accent-200 group-hover:text-accent-500 transition-colors">
                                        <i class="ph-fill ph-briefcase text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-base font-bold text-slate-900 truncate group-hover:text-accent-700 transition-colors"><?= esc($task['title']) ?></p>
                                        <div class="flex items-center gap-3 mt-1">
                                            <p class="text-sm font-semibold text-emerald-600">
                                                Rp <?= number_format($task['price'], 0, ',', '.') ?>
                                            </p>
                                            <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                            <p class="text-xs font-medium text-slate-500 flex items-center">
                                                <i class="ph-fill ph-calendar-blank mr-1 text-slate-400"></i>
                                                <?= date('d M Y', strtotime($task['created_at'])) ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <?php 
                                        $statusClass = match($task['status']) {
                                            'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'accepted' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'in_progress' => 'bg-orange-50 text-orange-700 border-orange-200',
                                            default => 'bg-slate-50 text-slate-700 border-slate-200'
                                        };
                                    ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border <?= $statusClass ?>">
                                        <?= ucfirst(str_replace('_', ' ', $task['status'])) ?>
                                    </span>
                                </div>
                            </div>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

    <!-- KYC Modal Popup -->
    <div x-show="showKycModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div x-show="showKycModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity" aria-hidden="true" @click="showKycModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div x-show="showKycModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full border border-slate-100">
                
                <div class="absolute top-0 right-0 pt-4 pr-4 z-10">
                    <button @click="showKycModal = false" type="button" class="bg-white rounded-xl p-2 text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus:outline-none transition-colors">
                        <span class="sr-only">Tutup</span>
                        <i class="ph-bold ph-x text-xl"></i>
                    </button>
                </div>

                <div class="px-6 py-8 sm:p-10">
                    <div class="text-center mb-8">
                        <div class="w-16 h-16 bg-primary-50 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-primary-100">
                            <i class="ph-fill ph-identification-card text-3xl text-primary-600"></i>
                        </div>
                        <h3 class="text-2xl font-extrabold text-slate-900 font-display" id="modal-title">Verifikasi Identitas Anda</h3>
                        <p class="text-slate-500 mt-2 text-sm">Untuk dapat mengambil pekerjaan, Anda diwajibkan untuk memverifikasi identitas terlebih dahulu.</p>
                    </div>

                    <?php if (session()->has('error')): ?>
                        <div class="bg-red-50/80 backdrop-blur-sm border border-red-100 p-4 mb-6 rounded-2xl flex items-start gap-3">
                            <i class="ph-fill ph-warning-circle text-red-500 text-xl mt-0.5"></i>
                            <p class="text-sm font-bold text-red-800"><?= session('error') ?></p>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('helper/kyc/submit') ?>" method="post" enctype="multipart/form-data" class="space-y-6">
                        <?= csrf_field() ?>

                        <!-- KTP Name -->
                        <div>
                            <label for="ktp_name" class="block text-sm font-bold text-slate-700 mb-2">Nama Lengkap (Sesuai KTP) <span class="text-red-500">*</span></label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="ph-bold ph-user text-slate-400 group-focus-within:text-primary-500 transition-colors"></i>
                                </div>
                                <input type="text" name="ktp_name" id="ktp_name" value="<?= old('ktp_name') ?>" required class="block w-full pl-11 pr-4 py-3 bg-slate-50 border-slate-200 rounded-xl text-sm transition-all duration-200 focus:bg-white focus:ring-2 focus:ring-primary-500 focus:border-transparent hover:bg-slate-100/80">
                            </div>
                        </div>

                        <!-- KTP Number -->
                        <div>
                            <label for="ktp_number" class="block text-sm font-bold text-slate-700 mb-2">Nomor Induk Kependudukan (NIK) <span class="text-red-500">*</span></label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="ph-bold ph-identification-card text-slate-400 group-focus-within:text-primary-500 transition-colors"></i>
                                </div>
                                <input type="number" name="ktp_number" id="ktp_number" value="<?= old('ktp_number') ?>" required class="block w-full pl-11 pr-4 py-3 bg-slate-50 border-slate-200 rounded-xl text-sm transition-all duration-200 focus:bg-white focus:ring-2 focus:ring-primary-500 focus:border-transparent hover:bg-slate-100/80" placeholder="16 Digit NIK KTP Anda">
                            </div>
                        </div>

                        <!-- Address -->
                        <div>
                            <label for="address" class="block text-sm font-bold text-slate-700 mb-2">Alamat Tinggal Lengkap <span class="text-red-500">*</span></label>
                            <div class="relative group">
                                <div class="absolute top-3 left-0 pl-4 pointer-events-none">
                                    <i class="ph-bold ph-map-pin text-slate-400 group-focus-within:text-primary-500 transition-colors"></i>
                                </div>
                                <textarea name="address" id="address" rows="3" required class="block w-full pl-11 pr-4 py-3 bg-slate-50 border-slate-200 rounded-xl text-sm transition-all duration-200 focus:bg-white focus:ring-2 focus:ring-primary-500 focus:border-transparent hover:bg-slate-100/80" placeholder="Jalan, RT/RW, Kelurahan, Kecamatan..."><?= old('address') ?></textarea>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6" x-data="{ ktpPreview: null, selfiePreview: null }">
                            <!-- KTP Photo -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Foto KTP Fisik <span class="text-red-500">*</span></label>
                                <label for="modal_ktp_photo" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-200 border-dashed rounded-2xl hover:border-primary-400 transition-colors bg-slate-50 relative overflow-hidden group cursor-pointer h-48">
                                    
                                    <input id="modal_ktp_photo" name="ktp_photo" type="file" class="sr-only" accept=".jpg,.jpeg,.png" required @change="ktpPreview = URL.createObjectURL($event.target.files[0])">

                                    <!-- IF NO PREVIEW -->
                                    <div x-show="!ktpPreview" class="space-y-2 text-center flex flex-col items-center justify-center w-full h-full">
                                        <i class="ph-bold ph-camera text-3xl text-slate-400 group-hover:text-primary-500 transition-colors"></i>
                                        <div class="flex text-sm text-slate-600 justify-center">
                                            <span class="bg-white rounded-md font-bold text-primary-600 px-3 py-1 shadow-sm border border-slate-100 pointer-events-none group-hover:bg-primary-50 transition-colors">Pilih File</span>
                                        </div>
                                        <p class="text-xs text-slate-500 mt-2">JPG, PNG up to 2MB</p>
                                    </div>

                                    <!-- IF PREVIEW -->
                                    <template x-if="ktpPreview">
                                        <div class="absolute inset-0 w-full h-full">
                                            <img :src="ktpPreview" class="w-full h-full object-cover">
                                            <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-sm">
                                                <span class="bg-white/20 text-white border border-white/50 px-4 py-2 rounded-xl font-bold backdrop-blur-md flex items-center gap-2 pointer-events-none">
                                                    <i class="ph-bold ph-arrows-clockwise"></i> Ganti File
                                                </span>
                                            </div>
                                        </div>
                                    </template>
                                </label>
                            </div>

                            <!-- Selfie Photo -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Selfie Memegang KTP <span class="text-red-500">*</span></label>
                                <label for="modal_selfie_photo" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-200 border-dashed rounded-2xl hover:border-primary-400 transition-colors bg-slate-50 relative overflow-hidden group cursor-pointer h-48">
                                    
                                    <input id="modal_selfie_photo" name="selfie_photo" type="file" class="sr-only" accept=".jpg,.jpeg,.png" required @change="selfiePreview = URL.createObjectURL($event.target.files[0])">

                                    <!-- IF NO PREVIEW -->
                                    <div x-show="!selfiePreview" class="space-y-2 text-center flex flex-col items-center justify-center w-full h-full">
                                        <i class="ph-bold ph-user-focus text-3xl text-slate-400 group-hover:text-primary-500 transition-colors"></i>
                                        <div class="flex text-sm text-slate-600 justify-center">
                                            <span class="bg-white rounded-md font-bold text-primary-600 px-3 py-1 shadow-sm border border-slate-100 pointer-events-none group-hover:bg-primary-50 transition-colors">Pilih File</span>
                                        </div>
                                        <p class="text-xs text-slate-500 mt-2">JPG, PNG up to 2MB</p>
                                    </div>

                                    <!-- IF PREVIEW -->
                                    <template x-if="selfiePreview">
                                        <div class="absolute inset-0 w-full h-full">
                                            <img :src="selfiePreview" class="w-full h-full object-cover">
                                            <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-sm">
                                                <span class="bg-white/20 text-white border border-white/50 px-4 py-2 rounded-xl font-bold backdrop-blur-md flex items-center gap-2 pointer-events-none">
                                                    <i class="ph-bold ph-arrows-clockwise"></i> Ganti File
                                                </span>
                                            </div>
                                        </div>
                                    </template>
                                </label>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex gap-3">
                            <button @click="showKycModal = false" type="button" class="w-1/3 inline-flex items-center justify-center px-6 py-3.5 text-sm font-bold text-slate-700 transition-all duration-300 bg-slate-100 rounded-xl hover:bg-slate-200">
                                Nanti Saja
                            </button>
                            <button type="submit" class="w-2/3 inline-flex items-center justify-center px-6 py-3.5 text-sm font-bold text-white transition-all duration-300 bg-primary-600 rounded-xl hover:bg-primary-700 focus:ring-4 focus:ring-primary-200">
                                Kirim Verifikasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
