<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="relative min-h-[90vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 overflow-hidden bg-slate-50">
    <!-- Abstract Premium Background -->
    <div class="absolute inset-0 z-0">
        <div class="absolute top-[-20%] left-[-10%] w-[50%] h-[50%] rounded-full bg-primary-200/50 mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-accent-200/40 mix-blend-multiply filter blur-3xl opacity-70 animate-blob" style="animation-delay: 2s;"></div>
    </div>

    <!-- Login Card -->
    <div class="relative z-10 w-full max-w-md animate-fade-in-up">
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl shadow-slate-200/50 border border-white p-8 sm:p-10">
            <div class="text-center mb-10">
                <div class="w-16 h-16 bg-gradient-to-tr from-primary-600 to-accent-500 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-primary-500/30">
                    <i class="ph-fill ph-handshake text-white text-3xl"></i>
                </div>
                <h2 class="text-3xl font-extrabold text-slate-900 font-display tracking-tight">Selamat Datang</h2>
                <p class="mt-3 text-sm text-slate-500">Masuk untuk melanjutkan ke dashboard Anda</p>
            </div>

            <?php if (session()->has('error')): ?>
                <div class="bg-red-50/80 backdrop-blur-sm border border-red-100 p-4 mb-6 rounded-2xl flex items-start gap-3">
                    <i class="ph-fill ph-warning-circle text-red-500 text-xl mt-0.5"></i>
                    <p class="text-sm font-medium text-red-800"><?= session('error') ?></p>
                </div>
            <?php endif; ?>

            <form class="space-y-6" action="<?= base_url('/login') ?>" method="POST">
                <?= csrf_field() ?>
                
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-bold text-slate-700 mb-1.5">Email Address</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-primary-600 text-slate-400">
                                <i class="ph-fill ph-envelope-simple text-lg"></i>
                            </div>
                            <input id="email" name="email" type="email" autocomplete="email" required 
                                   class="block w-full pl-11 pr-4 py-3.5 bg-slate-50 border-transparent rounded-xl text-sm transition-all duration-200 focus:bg-white focus:ring-2 focus:ring-primary-500 focus:border-transparent hover:bg-slate-100/80" 
                                   placeholder="nama@email.com" value="<?= old('email') ?>">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-bold text-slate-700 mb-1.5">Password</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-primary-600 text-slate-400">
                                <i class="ph-fill ph-lock-key text-lg"></i>
                            </div>
                            <input id="password" name="password" type="password" autocomplete="current-password" required 
                                   class="block w-full pl-11 pr-4 py-3.5 bg-slate-50 border-transparent rounded-xl text-sm transition-all duration-200 focus:bg-white focus:ring-2 focus:ring-primary-500 focus:border-transparent hover:bg-slate-100/80" 
                                   placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-6">
                    <label class="flex items-center cursor-pointer group">
                        <div class="relative flex items-center">
                            <input id="remember" name="remember" type="checkbox" class="peer sr-only">
                            <div class="w-5 h-5 bg-slate-100 border-2 border-slate-300 rounded transition-all peer-checked:bg-primary-600 peer-checked:border-primary-600 group-hover:border-primary-400"></div>
                            <i class="ph-bold ph-check absolute inset-0 text-white flex items-center justify-center text-xs opacity-0 scale-50 peer-checked:opacity-100 peer-checked:scale-100 transition-all duration-200"></i>
                        </div>
                        <span class="ml-3 text-sm font-medium text-slate-600 group-hover:text-slate-900 transition-colors">Ingat Saya</span>
                    </label>
                    
                    <a href="#" class="text-sm font-bold text-primary-600 hover:text-primary-700 transition-colors">Lupa sandi?</a>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full flex justify-center py-4 px-4 rounded-xl text-sm font-bold text-white bg-slate-900 hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-300 hover:shadow-lg hover:shadow-primary-500/30 hover:-translate-y-0.5">
                        Masuk Sekarang
                    </button>
                </div>
            </form>
            
            <div class="mt-8 text-center">
                <p class="text-sm font-medium text-slate-600">
                    Belum punya akun? 
                    <a href="<?= base_url('/register') ?>" class="text-primary-600 hover:text-primary-700 font-bold transition-colors">Daftar Gratis</a>
                </p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
