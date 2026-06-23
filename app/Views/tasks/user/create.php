<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    
    <div class="mb-8">
        <a href="<?= base_url('/user/tasks') ?>" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-primary-600 transition-colors mb-4">
            <i class="ph-bold ph-arrow-left mr-2"></i> Kembali ke Daftar Task
        </a>
        <h1 class="text-3xl font-extrabold text-slate-900 font-display tracking-tight">Buat Task Baru</h1>
        <p class="mt-1 text-sm text-slate-500">Isi detail pekerjaan yang Anda butuhkan bantuan.</p>
    </div>

    <?php if (session()->has('errors')): ?>
        <div class="bg-red-50/80 backdrop-blur-sm border border-red-100 p-4 mb-6 rounded-2xl flex items-start gap-3 animate-fade-in-up">
            <i class="ph-fill ph-warning-circle text-red-500 text-xl mt-0.5"></i>
            <div>
                <p class="text-sm font-bold text-red-800 mb-1">Terdapat kesalahan pada input Anda:</p>
                <ul class="text-sm text-red-700 list-disc list-inside">
                    <?php foreach (session('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <?php if (session()->has('error')): ?>
        <div class="bg-red-50/80 backdrop-blur-sm border border-red-100 p-4 mb-6 rounded-2xl flex items-start gap-3 animate-fade-in-up">
            <i class="ph-fill ph-warning-circle text-red-500 text-xl mt-0.5"></i>
            <p class="text-sm font-bold text-red-800"><?= session('error') ?></p>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden p-8">
        <form action="<?= base_url('/user/tasks/store') ?>" method="post" class="space-y-6" x-data="{ price: <?= old('price') ?? 0 ?>, balance: <?= $wallet['balance'] ?? 0 ?> }">
            <?= csrf_field() ?>

            <div>
                <label for="title" class="block text-sm font-semibold text-slate-900 mb-2">Judul Task</label>
                <input type="text" name="title" id="title" value="<?= old('title') ?>" placeholder="Contoh: Bersihkan kebun belakang" class="w-full rounded-xl border-slate-200 focus:border-primary-500 focus:ring-primary-500 text-slate-900 shadow-sm transition-shadow">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="category_id" class="block text-sm font-semibold text-slate-900 mb-2">Kategori</label>
                    <select name="category_id" id="category_id" class="w-full rounded-xl border-slate-200 focus:border-primary-500 focus:ring-primary-500 text-slate-900 shadow-sm transition-shadow">
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>" <?= old('category_id') == $category['id'] ? 'selected' : '' ?>>
                                <?= esc($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label for="price" class="block text-sm font-semibold text-slate-900 mb-2">Upah (Rp) <span class="text-slate-400 font-normal ml-1">(Saldo Anda: Rp <?= number_format($wallet['balance'] ?? 0, 0, ',', '.') ?>)</span></label>
                    <input type="number" name="price" id="price" x-model="price" min="10000" step="1000" placeholder="Min. 10000" class="w-full rounded-xl border-slate-200 focus:border-primary-500 focus:ring-primary-500 text-slate-900 shadow-sm transition-shadow">
                    
                    <!-- Insufficient Balance Warning -->
                    <div x-show="price > balance" x-transition class="mt-2 text-sm text-red-600 flex items-start gap-1">
                        <i class="ph-fill ph-warning-circle mt-0.5"></i>
                        <span>Saldo Anda tidak mencukupi untuk upah ini. Silakan <a href="<?= base_url('/wallet') ?>" class="font-bold underline hover:text-red-700">Top Up</a> terlebih dahulu.</span>
                    </div>
                </div>
            </div>

            <div>
                <label for="location" class="block text-sm font-semibold text-slate-900 mb-2">Lokasi / Alamat Lengkap</label>
                <textarea name="location" id="location" rows="2" placeholder="Jl. Contoh No. 123..." class="w-full rounded-xl border-slate-200 focus:border-primary-500 focus:ring-primary-500 text-slate-900 shadow-sm transition-shadow"><?= old('location') ?></textarea>
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-slate-900 mb-2">Deskripsi Detail</label>
                <textarea name="description" id="description" rows="4" placeholder="Jelaskan secara detail apa yang harus dilakukan..." class="w-full rounded-xl border-slate-200 focus:border-primary-500 focus:ring-primary-500 text-slate-900 shadow-sm transition-shadow"><?= old('description') ?></textarea>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="inline-flex items-center justify-center px-8 py-3 text-sm font-bold text-white transition-all duration-300 bg-primary-600 rounded-xl hover:bg-primary-700 hover:shadow-lg hover:-translate-y-0.5">
                    <i class="ph-bold ph-paper-plane-tilt mr-2"></i> Posting Task
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
