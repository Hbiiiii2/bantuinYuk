<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    
    <div class="mb-8 flex items-center justify-between">
        <h1 class="text-3xl font-extrabold text-slate-900 font-display tracking-tight">Profil Saya</h1>
        <a href="<?= base_url('/profile/edit') ?>" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-bold text-slate-900 transition-all duration-300 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:shadow-md hover:-translate-y-0.5">
            <i class="ph-bold ph-pencil-simple mr-2 text-slate-500"></i> Edit Profil
        </a>
    </div>

    <?php if (session()->has('message')): ?>
        <div class="bg-green-50/80 backdrop-blur-sm border border-green-100 p-4 mb-6 rounded-2xl flex items-start gap-3 animate-fade-in-up">
            <i class="ph-fill ph-check-circle text-green-500 text-xl mt-0.5"></i>
            <p class="text-sm font-bold text-green-800"><?= session('message') ?></p>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden mb-8">
        <!-- Banner/Header -->
        <div class="h-32 bg-gradient-to-r from-primary-600 to-accent-600"></div>
        
        <div class="px-8 pb-8 relative">
            <!-- Avatar -->
            <div class="absolute -top-16 left-8">
                <?php if (!empty($user->photo)): ?>
                    <img src="<?= base_url('uploads/profiles/' . esc($user->photo)) ?>" alt="Profile" class="w-32 h-32 rounded-full border-4 border-white shadow-lg object-cover bg-white">
                <?php else: ?>
                    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=<?= esc($user->name) ?>&backgroundColor=e2e8f0" alt="Avatar" class="w-32 h-32 rounded-full border-4 border-white shadow-lg bg-white">
                <?php endif; ?>
            </div>
            
            <div class="pt-20">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900 font-display"><?= esc($user->name) ?></h2>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-slate-100 text-slate-600 uppercase tracking-wide">
                                <?= esc($user->getGroups()[0] ?? 'User') ?>
                            </span>
                            <?php if ($user->is_verified): ?>
                                <span class="inline-flex items-center text-xs font-bold text-blue-600">
                                    <i class="ph-fill ph-patch-check mr-1 text-lg"></i> Terverifikasi
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if (($user->getGroups()[0] ?? 'user') === 'helper'): ?>
                        <div class="mt-4 sm:mt-0 flex flex-col items-end">
                            <div class="flex items-center gap-1 text-orange-400">
                                <i class="ph-fill ph-star text-xl"></i>
                                <span class="text-xl font-bold text-slate-900"><?= number_format($user->rating ?? 0, 1) ?></span>
                            </div>
                            <p class="text-xs text-slate-500 font-medium">Rating Helper</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 py-6 border-t border-slate-100">
                    <div>
                        <p class="text-sm font-medium text-slate-500 mb-1">Email Address</p>
                        <p class="text-base font-bold text-slate-900 flex items-center">
                            <i class="ph-fill ph-envelope-simple text-slate-400 mr-2"></i> <?= esc($user->email) ?>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 mb-1">Nomor Telepon/WA</p>
                        <p class="text-base font-bold text-slate-900 flex items-center">
                            <i class="ph-fill ph-phone text-slate-400 mr-2"></i> <?= esc($user->phone ?? '-') ?>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 mb-1">Bergabung Sejak</p>
                        <p class="text-base font-bold text-slate-900 flex items-center">
                            <i class="ph-fill ph-calendar-blank text-slate-400 mr-2"></i> <?= date('d F Y', strtotime($user->created_at)) ?>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 mb-1">Status Akun</p>
                        <p class="text-base font-bold text-green-600 flex items-center">
                            <i class="ph-fill ph-check-circle mr-2"></i> Aktif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
