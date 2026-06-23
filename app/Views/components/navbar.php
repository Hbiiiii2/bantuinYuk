<nav x-data="{ scrolled: false, mobileMenuOpen: false }" 
     @scroll.window="scrolled = (window.pageYOffset > 10) ? true : false"
     :class="{ 'glass shadow-sm': scrolled, 'bg-transparent': !scrolled }" 
     class="fixed w-full top-0 z-50 transition-all duration-300 border-b border-transparent"
     x-bind:class="{ 'border-slate-200/50': scrolled }">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20 transition-all duration-300" :class="{ 'h-16': scrolled }">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="<?= base_url('/') ?>" class="flex items-center gap-2.5 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-primary-600 to-accent-500 flex items-center justify-center text-white shadow-lg shadow-primary-500/30 group-hover:shadow-primary-500/50 group-hover:scale-105 transition-all duration-300">
                        <i class="ph-fill ph-handshake text-xl"></i>
                    </div>
                    <span class="font-display font-bold text-2xl tracking-tight text-slate-900 group-hover:text-primary-600 transition-colors">Bantuin<span class="text-primary-500">Yuk</span></span>
                </a>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex md:items-center md:gap-8">
                <a href="<?= base_url('/#layanan') ?>" class="text-sm font-semibold text-slate-600 hover:text-primary-600 transition-colors">Layanan</a>
                <a href="<?= base_url('/#cara-kerja') ?>" class="text-sm font-semibold text-slate-600 hover:text-primary-600 transition-colors">Cara Kerja</a>
                
                <?php if (auth('session')->loggedIn()): ?>
                    <?php 
                        $user = auth('session')->user(); 
                        $role = $user->getGroups()[0] ?? 'user';
                        $dashboardUrl = $role === 'admin' ? '/admin/dashboard' : ($role === 'helper' ? '/helper/dashboard' : '/user/dashboard');
                    ?>
                    
                    <div class="w-px h-6 bg-slate-200"></div>

                    <a href="<?= base_url($dashboardUrl) ?>" class="text-sm font-semibold text-slate-600 hover:text-primary-600 transition-colors">Dashboard</a>
                    
                    <a href="<?= base_url('/notifications') ?>" class="text-slate-500 hover:text-primary-600 transition-colors relative ml-2">
                        <i class="ph-bold ph-bell text-xl"></i>
                        <?php 
                            $unreadCount = (new \App\Models\NotificationModel())->getUnreadCount($user->id);
                            if ($unreadCount > 0): 
                        ?>
                            <span class="absolute -top-1 -right-1 flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500 border border-white"></span>
                            </span>
                        <?php endif; ?>
                    </a>
                    
                    <div x-data="{ open: false }" class="relative" @keydown.escape.prevent.stop="open = false" @click.away="open = false">
                        <button @click="open = !open" class="flex items-center gap-2.5 focus:outline-none group">
                            <?php $avatarUrl = $user->photo ? base_url('uploads/profiles/' . $user->photo) : "https://api.dicebear.com/7.x/avataaars/svg?seed=" . urlencode($user->name) . "&backgroundColor=e2e8f0"; ?>
                            <img src="<?= esc($avatarUrl) ?>" alt="Avatar" class="w-9 h-9 rounded-full ring-2 ring-transparent group-hover:ring-primary-500 transition-all duration-300 object-cover">
                            <div class="text-left hidden lg:block">
                                <p class="text-xs font-medium text-slate-500 leading-tight">Halo,</p>
                                <p class="text-sm font-bold text-slate-800 leading-tight group-hover:text-primary-600 transition-colors"><?= esc(strtok($user->name, " ")) ?></p>
                            </div>
                            <i class="ph ph-caret-down text-slate-400 text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                        </button>
                        
                        <!-- Dropdown -->
                        <div x-show="open" x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-2"
                             class="absolute right-0 mt-3 w-56 bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 py-2 origin-top-right ring-1 ring-black/5 z-50">
                            
                            <div class="px-4 py-3 border-b border-slate-100 mb-1 lg:hidden">
                                <p class="text-sm text-slate-500">Masuk sebagai</p>
                                <p class="text-sm font-bold text-slate-900 truncate"><?= esc($user->email) ?></p>
                            </div>

                            <a href="<?= base_url('/profile') ?>" class="flex items-center px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-primary-50 hover:text-primary-600 transition-colors">
                                <i class="ph ph-user-circle text-lg mr-3 text-slate-400"></i> Profil Saya
                            </a>
                            <a href="<?= base_url('/profile/edit') ?>" class="flex items-center px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-primary-50 hover:text-primary-600 transition-colors">
                                <i class="ph ph-gear text-lg mr-3 text-slate-400"></i> Pengaturan
                            </a>
                            <a href="<?= base_url('/disputes') ?>" class="flex items-center px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-primary-50 hover:text-primary-600 transition-colors">
                                <i class="ph ph-shield-warning text-lg mr-3 text-slate-400"></i> Pusat Resolusi
                            </a>
                            
                            <div class="h-px bg-slate-100 my-2"></div>
                            
                            <form action="<?= base_url('/logout') ?>" method="POST" class="block w-full">
                                <button type="submit" class="w-full flex items-center px-4 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50 transition-colors">
                                    <i class="ph ph-sign-out text-lg mr-3 text-red-400"></i> Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?= base_url('/login') ?>" class="text-sm font-bold text-slate-700 hover:text-primary-600 transition-colors">Masuk</a>
                    <a href="<?= base_url('/register') ?>" class="relative inline-flex items-center justify-center px-6 py-2.5 text-sm font-bold text-white transition-all duration-300 bg-slate-900 rounded-full hover:bg-primary-600 hover:shadow-lg hover:shadow-primary-500/30 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Daftar Gratis
                    </a>
                <?php endif; ?>
            </div>

            <!-- Mobile menu button -->
            <div class="flex items-center md:hidden">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-slate-600 hover:text-primary-600 focus:outline-none transition-colors p-2 -mr-2">
                    <i class="ph text-2xl" :class="mobileMenuOpen ? 'ph-x' : 'ph-list'"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Overlay -->
    <div x-show="mobileMenuOpen" x-cloak
         class="md:hidden glass absolute top-full left-0 w-full border-b border-slate-200/50 shadow-lg"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-4">
        
        <div class="px-4 pt-2 pb-6 space-y-1 bg-white/50">
            <?php if (auth('session')->loggedIn()): ?>
                <div class="flex items-center gap-3 px-3 py-4 border-b border-slate-200/50 mb-2">
                    <?php $avatarUrlMobile = auth('session')->user()->photo ? base_url('uploads/profiles/' . auth('session')->user()->photo) : "https://api.dicebear.com/7.x/avataaars/svg?seed=" . urlencode(auth('session')->user()->name) . "&backgroundColor=e2e8f0"; ?>
                    <img src="<?= esc($avatarUrlMobile) ?>" alt="Avatar" class="w-10 h-10 rounded-full object-cover">
                    <div>
                        <p class="text-sm font-bold text-slate-900"><?= esc(auth('session')->user()->name) ?></p>
                        <p class="text-xs font-medium text-slate-500"><?= esc(auth('session')->user()->email) ?></p>
                    </div>
                </div>
                <a href="<?= base_url($dashboardUrl ?? '/user/dashboard') ?>" class="block px-3 py-2.5 rounded-lg text-base font-semibold text-slate-800 hover:bg-primary-50 hover:text-primary-600">Dashboard</a>
                <a href="<?= base_url('/profile') ?>" class="block px-3 py-2.5 rounded-lg text-base font-semibold text-slate-800 hover:bg-primary-50 hover:text-primary-600">Profil Saya</a>
                <form action="<?= base_url('/logout') ?>" method="POST" class="mt-2">
                    <button type="submit" class="w-full text-left px-3 py-2.5 rounded-lg text-base font-semibold text-red-600 hover:bg-red-50">Keluar</button>
                </form>
            <?php else: ?>
                <a href="<?= base_url('/#layanan') ?>" class="block px-3 py-2.5 rounded-lg text-base font-semibold text-slate-800 hover:bg-primary-50 hover:text-primary-600">Layanan</a>
                <a href="<?= base_url('/#cara-kerja') ?>" class="block px-3 py-2.5 rounded-lg text-base font-semibold text-slate-800 hover:bg-primary-50 hover:text-primary-600">Cara Kerja</a>
                <div class="h-px bg-slate-200/50 my-4"></div>
                <div class="flex flex-col gap-3 px-3">
                    <a href="<?= base_url('/login') ?>" class="flex justify-center w-full px-4 py-3 text-sm font-bold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50">Masuk</a>
                    <a href="<?= base_url('/register') ?>" class="flex justify-center w-full px-4 py-3 text-sm font-bold text-white bg-slate-900 rounded-xl hover:bg-primary-600">Daftar Gratis</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>
