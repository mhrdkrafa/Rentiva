<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if(isset($seo) && $seo instanceof \App\Support\SeoData)
        <x-seo :data="$seo" />
    @elseif(isset($seo))
        {!! $seo !!}
    @else
        <x-seo />
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="font-sans antialiased text-slate-900 bg-slate-50 flex flex-col min-h-full selection:bg-emerald-500 selection:text-white pb-16 md:pb-0">

    <!-- Global Toast / Flash Notifications -->
    <x-flash-message />

    <!-- Navigation Header -->
    <header class="sticky top-0 z-40 w-full glass-nav border-b border-slate-200/80 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                
                <!-- Logo & Brand -->
                <div class="flex items-center gap-8">
                    <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white shadow-md shadow-emerald-500/20 group-hover:scale-105 transition-transform duration-200">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xl font-bold tracking-tight text-slate-900 leading-none">Rentiva<span class="text-emerald-600">.</span></span>
                            <span class="text-[10px] font-medium tracking-wider text-slate-500 uppercase mt-0.5">Rental Marketplace</span>
                        </div>
                    </a>

                    <!-- Desktop Nav Links -->
                    <nav class="hidden lg:flex items-center gap-6">
                        <a href="{{ url('/search') }}" class="text-sm font-medium text-slate-600 hover:text-emerald-600 transition-colors">Cari Kost</a>
                        <a href="{{ url('/search?type=apartment') }}" class="text-sm font-medium text-slate-600 hover:text-emerald-600 transition-colors">Apartemen</a>
                        <a href="{{ url('/search?type=house') }}" class="text-sm font-medium text-slate-600 hover:text-emerald-600 transition-colors">Rumah Sewa</a>
                        <a href="{{ url('/promotions') }}" class="text-sm font-medium text-slate-600 hover:text-emerald-600 transition-colors flex items-center gap-1.5">
                            Promo
                            <span class="bg-rose-500 text-white text-[10px] font-bold px-1.5 py-0.2 rounded-full">HOT</span>
                        </a>
                    </nav>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-3">
                    @if(!auth()->check() || auth()->user()->isOwner() || auth()->user()->isAdmin())
                        <x-button variant="outline" size="sm" href="{{ url('/owner/dashboard') }}" class="hidden sm:inline-flex text-emerald-700 border-emerald-200 hover:bg-emerald-50">
                            <svg class="w-4 h-4 mr-1 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Pasang Iklan Properti
                        </x-button>
                    @endif

                    @auth
                        <!-- User Dropdown -->
                        <x-dropdown align="right" width="56">
                            <x-slot name="trigger">
                                <button class="flex items-center gap-2 p-1.5 rounded-full hover:bg-slate-100 transition-colors focus:outline-none">
                                    <x-avatar :name="auth()->user()->name" size="sm" status="online" />
                                    <span class="hidden md:inline-block text-sm font-medium text-slate-700 max-w-[120px] truncate">
                                        {{ auth()->user()->name }}
                                    </span>
                                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="px-4 py-3 border-b border-slate-100 bg-slate-50">
                                    <p class="text-xs text-slate-500">Masuk sebagai</p>
                                    <p class="text-sm font-semibold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-emerald-600 font-medium capitalize mt-0.5">{{ auth()->user()->role?->label() ?? 'User' }}</p>
                                </div>

                                @if(auth()->user()->isAdmin())
                                    <a href="{{ url('/admin') }}" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 font-medium">
                                        Admin Panel CMS
                                    </a>
                                @endif

                                @if(auth()->user()->isTenant() || auth()->user()->isAdmin())
                                    <a href="{{ route('tenant.dashboard') }}" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                                        Dashboard Penyewa
                                    </a>
                                    <a href="{{ route('tenant.favorites') }}" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                                        Kost Favorit Saya
                                    </a>
                                    <a href="{{ route('tenant.invoices.index') }}" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                                        Tagihan & Sewa Saya
                                    </a>
                                @endif

                                @if(auth()->user()->isOwner() || auth()->user()->isAdmin() || auth()->user()->managerAssignmentsAsManager()->exists())
                                    <a href="{{ route('owner.dashboard') }}" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                                        Dashboard Pemilik (Owner)
                                    </a>
                                    <a href="{{ route('owner.properties.index') }}" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                                        Kelola Properti Kost
                                    </a>
                                    <a href="{{ route('owner.bookings.index') }}" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                                        Pengajuan Sewa Masuk
                                    </a>
                                @endif

                                <div class="border-t border-slate-100 my-1"></div>

                                <form method="POST" action="{{ route('logout') ?? url('/logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-rose-600 hover:bg-rose-50 font-medium">
                                        Keluar (Log out)
                                    </button>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    @else
                        <div class="flex items-center gap-2">
                            <x-button variant="ghost" size="sm" href="{{ url('/login') }}">
                                Masuk
                            </x-button>
                            <x-button variant="primary" size="sm" href="{{ url('/register') }}">
                                Daftar
                            </x-button>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1">
        @if(isset($slot))
            {{ $slot }}
        @else
            @yield('content')
        @endif
    </main>

    <!-- Public Footer -->
    <footer class="bg-slate-900 text-slate-400 pt-16 pb-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 pb-12 border-b border-slate-800">
                
                <!-- Brand & Info -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center text-white font-bold">
                            R
                        </div>
                        <span class="text-xl font-bold tracking-tight text-white">Rentiva<span class="text-emerald-500">.</span></span>
                    </div>
                    <p class="text-sm text-slate-400 max-w-sm leading-relaxed">
                        Platform rental marketplace terdepan untuk mencari, menyewa, dan mengelola kamar kost, apartemen, serta properti hunian dengan proses yang transparan, aman, dan tanpa repot.
                    </p>
                    <div class="flex items-center gap-3 pt-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-800 text-xs text-emerald-400 font-medium border border-slate-700">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            100% Properti Terverifikasi
                        </span>
                    </div>
                </div>

                <!-- Column 1: Kategori -->
                <div class="space-y-3">
                    <h4 class="text-xs font-semibold text-white uppercase tracking-wider">Kategori Sewa</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ url('/search?gender=putri') }}" class="hover:text-emerald-400 transition-colors">Kost Putri</a></li>
                        <li><a href="{{ url('/search?gender=putra') }}" class="hover:text-emerald-400 transition-colors">Kost Putra</a></li>
                        <li><a href="{{ url('/search?gender=campur') }}" class="hover:text-emerald-400 transition-colors">Kost Campur / Pasutri</a></li>
                        <li><a href="{{ url('/search?type=apartment') }}" class="hover:text-emerald-400 transition-colors">Apartemen Studio & 2BR</a></li>
                        <li><a href="{{ url('/search?type=house') }}" class="hover:text-emerald-400 transition-colors">Rumah Kontrakan</a></li>
                    </ul>
                </div>

                <!-- Column 2: Kota Populer -->
                <div class="space-y-3">
                    <h4 class="text-xs font-semibold text-white uppercase tracking-wider">Kota Populer</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ url('/search?city=jakarta') }}" class="hover:text-emerald-400 transition-colors">Kost Jakarta</a></li>
                        <li><a href="{{ url('/search?city=bandung') }}" class="hover:text-emerald-400 transition-colors">Kost Bandung</a></li>
                        <li><a href="{{ url('/search?city=yogyakarta') }}" class="hover:text-emerald-400 transition-colors">Kost Yogyakarta</a></li>
                        <li><a href="{{ url('/search?city=surabaya') }}" class="hover:text-emerald-400 transition-colors">Kost Surabaya</a></li>
                        <li><a href="{{ url('/search?city=malang') }}" class="hover:text-emerald-400 transition-colors">Kost Malang</a></li>
                    </ul>
                </div>

                <!-- Column 3: Bantuan & Mitra -->
                <div class="space-y-3">
                    <h4 class="text-xs font-semibold text-white uppercase tracking-wider">Bantuan & Mitra</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ url('/owner/dashboard') }}" class="hover:text-emerald-400 transition-colors">Pusat Pemilik (Owner)</a></li>
                        <li><a href="{{ url('/faq') }}" class="hover:text-emerald-400 transition-colors">Tanya Jawab (FAQ)</a></li>
                        <li><a href="{{ url('/terms') }}" class="hover:text-emerald-400 transition-colors">Syarat & Ketentuan</a></li>
                        <li><a href="{{ url('/privacy') }}" class="hover:text-emerald-400 transition-colors">Kebijakan Privasi</a></li>
                        <li><a href="{{ url('/contact') }}" class="hover:text-emerald-400 transition-colors">Hubungi Kami</a></li>
                    </ul>
                </div>
            </div>

            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} Rentiva Marketplace. Seluruh hak cipta dilindungi undang-undang.</p>
                <p class="text-slate-500">Original Rental Marketplace Platform</p>
            </div>
        </div>
    </footer>

    <!-- Mobile Bottom Navigation Bar -->
    <nav class="md:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200 px-2 py-2 flex items-center justify-around">
        <a href="{{ url('/') }}" class="flex flex-col items-center gap-1 text-slate-600 hover:text-emerald-600 px-3 py-1">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="text-[10px] font-medium">Beranda</span>
        </a>
        <a href="{{ url('/search') }}" class="flex flex-col items-center gap-1 text-slate-600 hover:text-emerald-600 px-3 py-1">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <span class="text-[10px] font-medium">Cari</span>
        </a>
        <a href="{{ url('/tenant/dashboard') }}" class="flex flex-col items-center gap-1 text-slate-600 hover:text-emerald-600 px-3 py-1">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <span class="text-[10px] font-medium">Sewa Saya</span>
        </a>
        <a href="{{ url('/owner/dashboard') }}" class="flex flex-col items-center gap-1 text-slate-600 hover:text-emerald-600 px-3 py-1">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
            </svg>
            <span class="text-[10px] font-medium">Pemilik</span>
        </a>
    </nav>

    @livewireScripts
    @stack('scripts')
</body>
</html>
