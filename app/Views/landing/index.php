<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Hero Section with Background Blobs -->
<div class="relative min-h-[90vh] flex items-center justify-center pt-20 pb-16 overflow-hidden">
    <!-- Animated Background Blobs -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10 bg-slate-50">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[50%] rounded-full bg-primary-200/50 mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
        <div class="absolute top-[20%] right-[-10%] w-[35%] h-[40%] rounded-full bg-accent-200/40 mix-blend-multiply filter blur-3xl opacity-70 animate-blob" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-[-20%] left-[20%] w-[45%] h-[45%] rounded-full bg-blue-200/40 mix-blend-multiply filter blur-3xl opacity-70 animate-blob" style="animation-delay: 4s;"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-4xl mx-auto">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/60 backdrop-blur-md border border-white/80 shadow-sm mb-8 animate-fade-in-up" style="animation-delay: 0.1s;">
                <span class="flex h-2 w-2 rounded-full bg-green-500 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                </span>
                <span class="text-sm font-semibold text-slate-700 tracking-wide">Tersedia di Jabodetabek</span>
            </div>
            
            <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight text-slate-900 mb-6 animate-fade-in-up" style="animation-delay: 0.2s;">
                Urusan rumah beres, <br class="hidden md:block" />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-accent-500">waktu luang nambah.</span>
            </h1>
            
            <p class="text-lg md:text-xl text-slate-600 mb-10 max-w-2xl mx-auto animate-fade-in-up" style="animation-delay: 0.3s;">
                Temukan ratusan Helper terpercaya untuk bantu bersih-bersih, perbaikan rumah, hingga belanja bulanan. Pesan hari ini, datang hari ini.
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 animate-fade-in-up" style="animation-delay: 0.4s;">
                <a href="<?= base_url('/register') ?>" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 text-base font-bold text-white transition-all duration-300 bg-slate-900 rounded-full hover:bg-primary-600 hover:shadow-xl hover:shadow-primary-500/30 hover:-translate-y-1">
                    Cari Helper Sekarang
                    <i class="ph-bold ph-arrow-right ml-2"></i>
                </a>
                <a href="#layanan" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 text-base font-bold text-slate-700 transition-all duration-300 bg-white border-2 border-slate-200 rounded-full hover:border-slate-300 hover:bg-slate-50">
                    Eksplorasi Layanan
                </a>
            </div>

            <!-- Stats/Trust -->
            <div class="mt-16 pt-8 border-t border-slate-200/60 grid grid-cols-2 md:grid-cols-4 gap-8 animate-fade-in-up" style="animation-delay: 0.5s;">
                <div class="text-center">
                    <p class="text-3xl font-bold text-slate-900 font-display">10k+</p>
                    <p class="text-sm font-medium text-slate-500 mt-1">Pengguna Aktif</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-slate-900 font-display">5k+</p>
                    <p class="text-sm font-medium text-slate-500 mt-1">Mitra Helper</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-slate-900 font-display">4.8</p>
                    <p class="text-sm font-medium text-slate-500 mt-1">Rating Rata-rata</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-slate-900 font-display">24/7</p>
                    <p class="text-sm font-medium text-slate-500 mt-1">Customer Support</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features/Services Section -->
<div id="layanan" class="py-24 bg-white relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h2 class="text-primary-600 font-bold tracking-wide uppercase text-sm mb-3">Layanan Kami</h2>
            <h3 class="text-3xl md:text-5xl font-extrabold text-slate-900 mb-6">Apapun kebutuhanmu, kami ada solusinya</h3>
            <p class="text-lg text-slate-600">Pilih dari berbagai kategori layanan yang dikerjakan oleh profesional yang telah melewati proses verifikasi ketat.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Card 1 -->
            <div class="group relative bg-white rounded-3xl p-8 border border-slate-100 shadow-sm hover:shadow-2xl hover:shadow-primary-500/10 transition-all duration-300 hover:-translate-y-2 overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-primary-50 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out z-0"></div>
                <div class="relative z-10">
                    <div class="w-14 h-14 bg-white rounded-2xl shadow-md border border-slate-50 flex items-center justify-center mb-6 text-primary-600 group-hover:text-white group-hover:bg-primary-600 transition-colors duration-300">
                        <i class="ph-fill ph-sparkle text-3xl"></i>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-3">Cleaning Service</h4>
                    <p class="text-slate-600 mb-6 line-clamp-2">Pembersihan rumah, apartemen, atau kos secara menyeluruh. Bebas debu, bebas stres.</p>
                    <a href="#" class="inline-flex items-center text-sm font-bold text-primary-600 hover:text-primary-700">
                        Pesan Sekarang <i class="ph-bold ph-arrow-right ml-1 transition-transform group-hover:translate-x-1"></i>
                    </a>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="group relative bg-white rounded-3xl p-8 border border-slate-100 shadow-sm hover:shadow-2xl hover:shadow-accent-500/10 transition-all duration-300 hover:-translate-y-2 overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-accent-50 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out z-0"></div>
                <div class="relative z-10">
                    <div class="w-14 h-14 bg-white rounded-2xl shadow-md border border-slate-50 flex items-center justify-center mb-6 text-accent-600 group-hover:text-white group-hover:bg-accent-600 transition-colors duration-300">
                        <i class="ph-fill ph-wrench text-3xl"></i>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-3">Perbaikan & Plumbing</h4>
                    <p class="text-slate-600 mb-6 line-clamp-2">Atasi keran bocor, lampu mati, atau perbaikan ringan perabotan rumah Anda.</p>
                    <a href="#" class="inline-flex items-center text-sm font-bold text-accent-600 hover:text-accent-700">
                        Pesan Sekarang <i class="ph-bold ph-arrow-right ml-1 transition-transform group-hover:translate-x-1"></i>
                    </a>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="group relative bg-white rounded-3xl p-8 border border-slate-100 shadow-sm hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-300 hover:-translate-y-2 overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-blue-50 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out z-0"></div>
                <div class="relative z-10">
                    <div class="w-14 h-14 bg-white rounded-2xl shadow-md border border-slate-50 flex items-center justify-center mb-6 text-blue-600 group-hover:text-white group-hover:bg-blue-600 transition-colors duration-300">
                        <i class="ph-fill ph-shopping-bag text-3xl"></i>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-3">Personal Shopper</h4>
                    <p class="text-slate-600 mb-6 line-clamp-2">Titip belanja kebutuhan dapur atau barang spesifik tanpa harus keluar rumah.</p>
                    <a href="#" class="inline-flex items-center text-sm font-bold text-blue-600 hover:text-blue-700">
                        Pesan Sekarang <i class="ph-bold ph-arrow-right ml-1 transition-transform group-hover:translate-x-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contact Section -->
<div class="py-24 bg-slate-50 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-primary-600 font-bold tracking-wide uppercase text-sm mb-3">Hubungi Kami</h2>
        <h3 class="text-3xl md:text-5xl font-extrabold text-slate-900 mb-6">Butuh Bantuan Lebih Lanjut?</h3>
        <p class="text-lg text-slate-600 mb-12 max-w-2xl mx-auto">Tim admin kami siap membantu menjawab pertanyaan atau menyelesaikan kendala yang Anda hadapi.</p>
        
        <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
            <a href="https://wa.me/6281998043342" target="_blank" class="flex items-center justify-center gap-3 w-full sm:w-auto px-8 py-4 bg-green-500 text-white rounded-full font-bold shadow-lg shadow-green-500/30 hover:bg-green-600 hover:-translate-y-1 transition-all duration-300">
                <i class="ph-bold ph-whatsapp-logo text-2xl"></i>
                wa.me/6281998043342
            </a>
            <a href="mailto:cs@bantuinyuk.com" class="flex items-center justify-center gap-3 w-full sm:w-auto px-8 py-4 bg-white text-slate-700 border-2 border-slate-200 rounded-full font-bold hover:border-slate-300 hover:bg-slate-100 hover:-translate-y-1 transition-all duration-300">
                <i class="ph-bold ph-envelope-simple text-2xl"></i>
                cs@bantuinyuk.com
            </a>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="relative py-20 bg-slate-900 overflow-hidden rounded-t-[3rem]">
    <div class="absolute inset-0 w-full h-full opacity-30 mix-blend-overlay">
        <!-- Abstract gradient background for premium dark mode -->
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-primary-500 rounded-full filter blur-[100px]"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-accent-500 rounded-full filter blur-[100px]"></div>
    </div>
    
    <div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl md:text-5xl font-extrabold text-white mb-6">Siap untuk hidup lebih santai?</h2>
        <p class="text-xl text-slate-300 mb-10 max-w-2xl mx-auto">Bergabung dengan ribuan pengguna lain yang telah mendelegasikan tugas harian mereka ke Helper terpercaya.</p>
        <a href="<?= base_url('/register') ?>" class="inline-flex items-center justify-center px-10 py-5 text-lg font-bold text-slate-900 transition-all duration-300 bg-white rounded-full hover:bg-primary-50 hover:shadow-2xl hover:shadow-white/20 hover:-translate-y-1">
            Mulai Bebas Repot Sekarang
        </a>
    </div>
</div>
<?= $this->endSection() ?>
