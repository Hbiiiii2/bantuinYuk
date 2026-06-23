<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-2xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-primary-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="ph-fill ph-identification-card text-3xl text-primary-600"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 font-display">Verifikasi Identitas Anda</h1>
            <p class="text-slate-500 mt-2">Untuk menjaga keamanan pengguna, kami mewajibkan seluruh mitra Helper untuk mengunggah dokumen identitas diri sebelum mulai bekerja.</p>
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
                    <input type="text" name="ktp_name" id="ktp_name" value="<?= old('ktp_name') ?>" required class="block w-full pl-11 pr-4 py-3 bg-slate-50 border-transparent rounded-xl text-sm transition-all duration-200 focus:bg-white focus:ring-2 focus:ring-primary-500 focus:border-transparent hover:bg-slate-100/80">
                </div>
            </div>

            <!-- KTP Number -->
            <div>
                <label for="ktp_number" class="block text-sm font-bold text-slate-700 mb-2">Nomor Induk Kependudukan (NIK) <span class="text-red-500">*</span></label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="ph-bold ph-identification-card text-slate-400 group-focus-within:text-primary-500 transition-colors"></i>
                    </div>
                    <input type="number" name="ktp_number" id="ktp_number" value="<?= old('ktp_number') ?>" required class="block w-full pl-11 pr-4 py-3 bg-slate-50 border-transparent rounded-xl text-sm transition-all duration-200 focus:bg-white focus:ring-2 focus:ring-primary-500 focus:border-transparent hover:bg-slate-100/80" placeholder="16 Digit NIK KTP Anda">
                </div>
            </div>

            <!-- Address -->
            <div>
                <label for="address" class="block text-sm font-bold text-slate-700 mb-2">Alamat Tinggal Lengkap <span class="text-red-500">*</span></label>
                <div class="relative group">
                    <div class="absolute top-3 left-0 pl-4 pointer-events-none">
                        <i class="ph-bold ph-map-pin text-slate-400 group-focus-within:text-primary-500 transition-colors"></i>
                    </div>
                    <textarea name="address" id="address" rows="3" required class="block w-full pl-11 pr-4 py-3 bg-slate-50 border-transparent rounded-xl text-sm transition-all duration-200 focus:bg-white focus:ring-2 focus:ring-primary-500 focus:border-transparent hover:bg-slate-100/80" placeholder="Jalan, RT/RW, Kelurahan, Kecamatan..."><?= old('address') ?></textarea>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6" x-data="{ ktpPreview: null, selfiePreview: null }">
                <!-- KTP Photo -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Foto KTP Fisik <span class="text-red-500">*</span></label>
                    <label for="ktp_photo" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-200 border-dashed rounded-2xl hover:border-primary-400 transition-colors bg-slate-50 relative overflow-hidden group cursor-pointer h-48">
                        
                        <input id="ktp_photo" name="ktp_photo" type="file" class="sr-only" accept=".jpg,.jpeg,.png" required @change="ktpPreview = URL.createObjectURL($event.target.files[0])">

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
                    <label for="selfie_photo" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-200 border-dashed rounded-2xl hover:border-primary-400 transition-colors bg-slate-50 relative overflow-hidden group cursor-pointer h-48">
                        
                        <input id="selfie_photo" name="selfie_photo" type="file" class="sr-only" accept=".jpg,.jpeg,.png" required @change="selfiePreview = URL.createObjectURL($event.target.files[0])">

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

            <div class="pt-4 border-t border-slate-100">
                <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-3.5 text-sm font-bold text-white transition-all duration-300 bg-primary-600 rounded-xl hover:bg-primary-700 focus:ring-4 focus:ring-primary-200">
                    <i class="ph-bold ph-check-circle mr-2 text-lg"></i>
                    Kirim Verifikasi KYC
                </button>
                <p class="text-center text-xs text-slate-400 mt-4">
                    Data Anda aman dan hanya digunakan jika ada keperluan hukum terkait laporan kriminal.
                </p>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
