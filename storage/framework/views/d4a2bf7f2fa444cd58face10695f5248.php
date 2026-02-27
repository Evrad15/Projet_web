<nav x-data="{ open: false, scrolled: false }" 
     x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 10 })"
     :class="scrolled ? 'shadow-md' : 'shadow-sm'"
     class="bg-white border-b border-gray-100 sticky top-0 z-50 transition-shadow duration-300">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap');

        .nav-root { font-family: 'DM Sans', sans-serif; }

        .nav-link-item {
            position: relative;
            font-size: 0.875rem;
            font-weight: 500;
            color: #6b7280;
            padding: 0.25rem 0;
            transition: color 0.2s;
        }
        .nav-link-item::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 2px;
            transition: width 0.25s ease;
        }
        .nav-link-item:hover { color: #1f2937; }
        .nav-link-item:hover::after,
        .nav-link-item.active::after { width: 100%; }
        .nav-link-item.active { color: #1f2937; }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.8rem;
            flex-shrink: 0;
        }

        .dropdown-trigger {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            border-radius: 10px;
            border: 1px solid transparent;
            background: transparent;
            cursor: pointer;
            transition: background 0.2s, border-color 0.2s;
            font-family: 'DM Sans', sans-serif;
        }
        .dropdown-trigger:hover {
            background: #f9fafb;
            border-color: #e5e7eb;
        }

        .dropdown-panel {
            position: absolute;
            right: 0;
            top: calc(100% + 10px);
            width: 220px;
            background: white;
            border-radius: 14px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.12), 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid #f0f0f0;
            overflow: hidden;
            transform-origin: top right;
        }

        .dropdown-header {
            padding: 14px 16px 10px;
            border-bottom: 1px solid #f3f4f6;
        }

        .dropdown-item-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            font-size: 0.875rem;
            color: #374151;
            transition: background 0.15s;
            cursor: pointer;
            text-decoration: none;
            font-family: 'DM Sans', sans-serif;
        }
        .dropdown-item-link:hover { background: #f9fafb; color: #111827; }
        .dropdown-item-link.danger { color: #ef4444; }
        .dropdown-item-link.danger:hover { background: #fff5f5; }

        .dropdown-item-link svg {
            width: 16px;
            height: 16px;
            opacity: 0.6;
        }

        /* Mobile menu */
        .mobile-nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 500;
            color: #6b7280;
            transition: background 0.15s, color 0.15s;
            text-decoration: none;
        }
        .mobile-nav-link:hover, .mobile-nav-link.active {
            background: #f5f3ff;
            color: #7c3aed;
        }

        .hamburger-btn {
            padding: 8px;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            background: white;
            cursor: pointer;
            transition: background 0.15s;
        }
        .hamburger-btn:hover { background: #f9fafb; }

        .logo-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 8px rgba(102,126,234,0.4);
        }
    </style>

    <div class="nav-root max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            <!-- Logo + Liens -->
            <div class="flex items-center gap-8">
                <a href="<?php echo e(route('dashboard')); ?>" class="flex items-center gap-2.5">
                    <div class="logo-badge">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <span style="font-family:'DM Sans',sans-serif; font-weight:700; font-size:1.05rem; color:#111827; letter-spacing:-0.02em;">
                        <?php echo e(config('app.name', 'App')); ?>

                    </span>
                </a>

                <!-- Liens desktop -->
                <div class="hidden sm:flex items-center gap-6">
                    <a href="<?php echo e(route('dashboard')); ?>"
                       class="nav-link-item <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                        Dashboard
                    </a>
                    
                </div>
            </div>

            <!-- Profil dropdown desktop -->
            <div class="hidden sm:block">
                <div x-data="{ dropOpen: false }" class="relative">
                    <button @click="dropOpen = !dropOpen" @keydown.escape="dropOpen = false" class="dropdown-trigger">
                        <div class="user-avatar">
                            <?php echo e(strtoupper(substr(Auth::user()->name, 0, 1))); ?>

                        </div>
                        <span style="font-size:0.875rem; font-weight:500; color:#374151; max-width:120px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                            <?php echo e(Auth::user()->name); ?>

                        </span>
                        <svg :class="{'rotate-180': dropOpen}" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <!-- Panel dropdown -->
                    <div x-show="dropOpen"
                         @click.outside="dropOpen = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="dropdown-panel"
                         style="display:none;">

                        <!-- Header utilisateur -->
                        <div class="dropdown-header">
                            <p style="font-size:0.875rem; font-weight:600; color:#111827; margin:0 0 2px;"><?php echo e(Auth::user()->name); ?></p>
                            <p style="font-size:0.75rem; color:#9ca3af; margin:0;"><?php echo e(Auth::user()->email); ?></p>
                        </div>

                        <!-- Liens -->
                        <div style="padding: 6px 0;">
                            <a href="<?php echo e(route('profile.edit')); ?>" class="dropdown-item-link">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Mon profil
                            </a>

                            <div style="margin: 4px 8px; height:1px; background:#f3f4f6;"></div>

                            <a href="<?php echo e(route('logout')); ?>"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                               class="dropdown-item-link danger">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Déconnexion
                            </a>
                            <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display:none;"><?php echo csrf_field(); ?></form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hamburger mobile -->
            <div class="sm:hidden">
                <button @click="open = !open" class="hamburger-btn">
                    <svg class="h-5 w-5 text-gray-500" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" style="display:none;"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Menu mobile -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="sm:hidden border-t border-gray-100"
         style="display:none;">

        <div class="nav-root px-4 pt-3 pb-4 space-y-1">
            <a href="<?php echo e(route('dashboard')); ?>" class="mobile-nav-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>
        </div>

        <!-- Profil mobile -->
        <div class="nav-root border-t border-gray-100 px-4 pt-3 pb-4">
            <div class="flex items-center gap-3 mb-3 px-2">
                <div class="user-avatar"><?php echo e(strtoupper(substr(Auth::user()->name, 0, 1))); ?></div>
                <div>
                    <p style="font-size:0.875rem; font-weight:600; color:#111827; margin:0;"><?php echo e(Auth::user()->name); ?></p>
                    <p style="font-size:0.75rem; color:#9ca3af; margin:0;"><?php echo e(Auth::user()->email); ?></p>
                </div>
            </div>

            <a href="<?php echo e(route('profile.edit')); ?>" class="mobile-nav-link">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Mon profil
            </a>

            <a href="<?php echo e(route('logout')); ?>"
               onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();"
               class="mobile-nav-link" style="color:#ef4444;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Déconnexion
            </a>
            <form id="logout-form-mobile" action="<?php echo e(route('logout')); ?>" method="POST" style="display:none;"><?php echo csrf_field(); ?></form>
        </div>
    </div>
</nav><?php /**PATH C:\Users\mopao\Application_Web_de_gestion_des_ventes_et_inventaire\resources\views/layouts/navigation.blade.php ENDPATH**/ ?>