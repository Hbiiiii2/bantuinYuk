<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="relative min-h-[90vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 overflow-hidden bg-slate-50">
    <!-- Abstract Premium Background -->
    <div class="absolute inset-0 z-0">
        <div class="absolute top-[-20%] left-[-10%] w-[50%] h-[50%] rounded-full bg-primary-200/50 mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-accent-200/40 mix-blend-multiply filter blur-3xl opacity-70 animate-blob" style="animation-delay: 2s;"></div>
    </div>

    <!-- Register Card -->
    <div class="relative z-10 w-full max-w-lg animate-fade-in-up">
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl shadow-slate-200/50 border border-white p-8 sm:p-10">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-gradient-to-tr from-slate-800 to-slate-900 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-slate-900/20">
                    <i class="ph-fill ph-user-plus text-white text-3xl"></i>
                </div>
                <h2 class="text-3xl font-extrabold text-slate-900 font-display tracking-tight">Buat Akun Baru</h2>
                <p class="mt-2 text-sm text-slate-500">Bergabung dengan BantuinYuk sekarang</p>
            </div>

            <?php if (session()->has('errors')): ?>
                <div class="bg-red-50/80 backdrop-blur-sm border border-red-100 p-4 mb-6 rounded-2xl flex items-start gap-3">
                    <i class="ph-fill ph-warning-circle text-red-500 text-xl mt-0.5"></i>
                    <div>
                        <h3 class="text-sm font-bold text-red-800">Ada kesalahan input:</h3>
                        <ul class="mt-1 text-sm font-medium text-red-700 list-disc list-inside">
                            <?php foreach (session('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <form class="space-y-5" action="<?= base_url('/register') ?>" method="POST">
                <?= csrf_field() ?>
                
                <div>
                    <label for="name" class="block text-sm font-bold text-slate-700 mb-1.5">Nama Lengkap</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-primary-600 text-slate-400">
                            <i class="ph-fill ph-user text-lg"></i>
                        </div>
                        <input id="name" name="name" type="text" required 
                               class="block w-full pl-11 pr-4 py-3.5 bg-slate-50 border-transparent rounded-xl text-sm transition-all duration-200 focus:bg-white focus:ring-2 focus:ring-primary-500 focus:border-transparent hover:bg-slate-100/80" 
                               placeholder="Budi Santoso" value="<?= old('name') ?>">
                    </div>
                </div>

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
                    <label for="phone" class="block text-sm font-bold text-slate-700 mb-1.5">Nomor Telepon/WA</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-primary-600 text-slate-400">
                            <i class="ph-fill ph-phone text-lg"></i>
                        </div>
                        <input id="phone" name="phone" type="text" required 
                               class="block w-full pl-11 pr-4 py-3.5 bg-slate-50 border-transparent rounded-xl text-sm transition-all duration-200 focus:bg-white focus:ring-2 focus:ring-primary-500 focus:border-transparent hover:bg-slate-100/80" 
                               placeholder="081234567890" value="<?= old('phone') ?>">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-bold text-slate-700 mb-1.5">Password</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-primary-600 text-slate-400">
                            <i class="ph-fill ph-lock-key text-lg"></i>
                        </div>
                        <input id="password" name="password" type="password" required minlength="8"
                               class="block w-full pl-11 pr-4 py-3.5 bg-slate-50 border-transparent rounded-xl text-sm transition-all duration-200 focus:bg-white focus:ring-2 focus:ring-primary-500 focus:border-transparent hover:bg-slate-100/80" 
                               placeholder="Minimal 8 karakter">
                    </div>
                </div>
                
                <div class="pt-2">
                    <label class="block text-sm font-bold text-slate-700 mb-3">Pilih Peran Anda</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="relative flex cursor-pointer rounded-2xl border-2 border-transparent bg-slate-50 p-4 hover:bg-slate-100 transition-all has-[:checked]:bg-white has-[:checked]:border-primary-500 has-[:checked]:shadow-lg has-[:checked]:shadow-primary-500/20 group">
                            <input type="radio" name="role" value="user" class="sr-only" <?= old('role', 'user') === 'user' ? 'checked' : '' ?>>
                            <span class="flex flex-col flex-1 relative z-10">
                                <span class="block text-base font-bold text-slate-900 group-has-[:checked]:text-primary-700">Customer</span>
                                <span class="mt-1 block text-xs font-medium text-slate-500">Mencari bantuan</span>
                            </span>
                            <div class="absolute right-4 top-4 w-5 h-5 rounded-full border-2 border-slate-300 group-has-[:checked]:border-primary-500 flex items-center justify-center transition-colors">
                                <div class="w-2.5 h-2.5 rounded-full bg-primary-500 scale-0 group-has-[:checked]:scale-100 transition-transform duration-200"></div>
                            </div>
                        </label>
                        
                        <label class="relative flex cursor-pointer rounded-2xl border-2 border-transparent bg-slate-50 p-4 hover:bg-slate-100 transition-all has-[:checked]:bg-white has-[:checked]:border-accent-500 has-[:checked]:shadow-lg has-[:checked]:shadow-accent-500/20 group">
                            <input type="radio" name="role" value="helper" class="sr-only" <?= old('role') === 'helper' ? 'checked' : '' ?>>
                            <span class="flex flex-col flex-1 relative z-10">
                                <span class="block text-base font-bold text-slate-900 group-has-[:checked]:text-accent-700">Helper</span>
                                <span class="mt-1 block text-xs font-medium text-slate-500">Memberi bantuan</span>
                            </span>
                            <div class="absolute right-4 top-4 w-5 h-5 rounded-full border-2 border-slate-300 group-has-[:checked]:border-accent-500 flex items-center justify-center transition-colors">
                                <div class="w-2.5 h-2.5 rounded-full bg-accent-500 scale-0 group-has-[:checked]:scale-100 transition-transform duration-200"></div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full flex justify-center py-4 px-4 rounded-xl text-sm font-bold text-white bg-slate-900 hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-300 hover:shadow-lg hover:shadow-primary-500/30 hover:-translate-y-0.5">
                        Daftar Sekarang
                    </button>
                </div>
            </form>
            
            <div class="mt-8 text-center">
                <p class="text-sm font-medium text-slate-600">
                    Sudah punya akun? 
                    <a href="<?= base_url('/login') ?>" class="text-primary-600 hover:text-primary-700 font-bold transition-colors">Masuk di sini</a>
                </p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
