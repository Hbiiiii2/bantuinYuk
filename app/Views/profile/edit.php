<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-3xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    
    <div class="mb-8 flex items-center justify-between">
        <div>
            <a href="<?= base_url('/profile') ?>" class="inline-flex items-center text-sm font-bold text-slate-500 hover:text-primary-600 transition-colors mb-2">
                <i class="ph-bold ph-arrow-left mr-1.5"></i> Kembali ke Profil
            </a>
            <h1 class="text-3xl font-extrabold text-slate-900 font-display tracking-tight">Edit Profil</h1>
        </div>
    </div>

    <?php if (session()->has('errors')): ?>
        <div class="bg-red-50/80 backdrop-blur-sm border border-red-100 p-4 mb-6 rounded-2xl flex items-start gap-3 animate-fade-in-up">
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

    <form action="<?= base_url('/profile/update') ?>" method="POST" enctype="multipart/form-data" class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
        <?= csrf_field() ?>
        
        <div x-data="{ 
                imageUrl: '<?= !empty($user->photo) ? base_url('uploads/profiles/' . esc($user->photo)) : "https://api.dicebear.com/7.x/avataaars/svg?seed=" . esc($user->name) . "&backgroundColor=e2e8f0" ?>',
                fileChosen(event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    
                    if (file.type.match('image.*')) {
                        const reader = new FileReader();
                        reader.readAsDataURL(file);
                        reader.onload = e => {
                            this.imageUrl = e.target.result;
                        };
                    }
                }
             }" class="flex flex-col sm:flex-row items-start sm:items-center gap-6 mb-8 pb-8 border-b border-slate-100">
            <div class="relative">
                <img :src="imageUrl" alt="Profile" class="w-24 h-24 rounded-full border-2 border-slate-100 object-cover bg-white">
                <div class="absolute -bottom-2 -right-2 bg-white rounded-full p-1 shadow-sm border border-slate-100">
                    <label class="w-8 h-8 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center cursor-pointer hover:bg-primary-100 transition-colors">
                        <i class="ph-bold ph-camera text-sm"></i>
                        <input type="file" name="photo" class="hidden" accept=".jpg,.jpeg,.png,.pdf" @change="fileChosen">
                    </label>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-900">Foto Profil</h3>
                <p class="text-sm text-slate-500 mt-1">Format JPG, JPEG, atau PNG. Maksimal 2MB.</p>
            </div>
        </div>

        <div class="space-y-6">
            <div>
                <label for="name" class="block text-sm font-bold text-slate-700 mb-1.5">Nama Lengkap</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-primary-600 text-slate-400">
                        <i class="ph-fill ph-user text-lg"></i>
                    </div>
                    <input id="name" name="name" type="text" required 
                           class="block w-full pl-11 pr-4 py-3 bg-slate-50 border-transparent rounded-xl text-sm transition-all duration-200 focus:bg-white focus:ring-2 focus:ring-primary-500 focus:border-transparent hover:bg-slate-100/80" 
                           value="<?= old('name', $user->name) ?>">
                </div>
            </div>

            <div>
                <label for="email" class="block text-sm font-bold text-slate-700 mb-1.5">Email Address</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <i class="ph-fill ph-envelope-simple text-lg"></i>
                    </div>
                    <!-- Email is usually disabled for editing unless handling verification flow -->
                    <input id="email" type="email" disabled
                           class="block w-full pl-11 pr-4 py-3 bg-slate-100 border-transparent rounded-xl text-sm text-slate-500 cursor-not-allowed" 
                           value="<?= esc($user->email) ?>">
                </div>
                <p class="text-xs text-slate-500 mt-1.5"><i class="ph-fill ph-info"></i> Email tidak dapat diubah saat ini.</p>
            </div>
            
            <div>
                <label for="phone" class="block text-sm font-bold text-slate-700 mb-1.5">Nomor Telepon/WA</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-primary-600 text-slate-400">
                        <i class="ph-fill ph-phone text-lg"></i>
                    </div>
                    <input id="phone" name="phone" type="text" required 
                           class="block w-full pl-11 pr-4 py-3 bg-slate-50 border-transparent rounded-xl text-sm transition-all duration-200 focus:bg-white focus:ring-2 focus:ring-primary-500 focus:border-transparent hover:bg-slate-100/80" 
                           value="<?= old('phone', $user->phone) ?>">
                </div>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end gap-3">
            <a href="<?= base_url('/profile') ?>" class="inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors">
                Batal
            </a>
            <button type="submit" class="inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-white bg-slate-900 rounded-xl hover:bg-primary-600 transition-colors shadow-lg shadow-slate-900/10 hover:shadow-primary-500/30 hover:-translate-y-0.5">
                Simpan Perubahan
            </button>
        </div>
    </form>

</div>
<?= $this->endSection() ?>
