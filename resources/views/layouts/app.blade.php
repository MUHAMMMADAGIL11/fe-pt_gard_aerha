<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'RPL Inventory') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        .app-shell { position: relative; min-height: 100vh; }
        input[placeholder="DD/MM/YYYY"]:focus::placeholder { text-transform: lowercase; }
        input[placeholder="DD/MM/YYYY"]:focus { text-transform: lowercase; }
        input[placeholder="HH/BB/TTTT"]:focus::placeholder { text-transform: lowercase; }
        input[placeholder="HH/BB/TTTT"]:focus { text-transform: lowercase; }
    </style>
</head>

@php
    $user = auth()->user();
    $isAdminGudang = $user && $user->hasRole('AdminGudang');
    $isPetugas = $user && $user->hasRole('PetugasOperasional');
    $isKepalaDivisi = $user && $user->hasRole('KepalaDivisi');

    $navigation = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'grid', 'roles' => ['all']],
        ['label' => 'Manajemen Barang', 'route' => 'barang.index', 'icon' => 'boxes', 'roles' => ['all']],
        ['label' => 'Kategori', 'route' => 'kategori.index', 'icon' => 'tag', 'roles' => ['AdminGudang']],
        ['label' => 'Stok Masuk', 'route' => 'transaksi-masuk.index', 'icon' => 'download', 'roles' => ['AdminGudang']],
        ['label' => 'Stok Keluar', 'route' => 'transaksi-keluar.index', 'icon' => 'upload', 'roles' => ['AdminGudang']],
        ['label' => 'Permintaan Barang', 'route' => 'permintaan-barang.index', 'icon' => 'clipboard', 'roles' => ['all']],
        ['label' => 'Laporan', 'route' => 'laporan.index', 'icon' => 'file', 'roles' => ['AdminGudang', 'KepalaDivisi']],
        ['label' => 'Pengguna', 'route' => 'user.index', 'icon' => 'users', 'roles' => ['KepalaDivisi', 'AdminGudang']],
        ['label' => 'Log Aktivitas', 'route' => 'log-aktivitas.index', 'icon' => 'activity', 'roles' => ['all']],
    ];

    $navigation = array_filter($navigation, function($item) use ($isAdminGudang, $isPetugas, $isKepalaDivisi) {
        if (in_array('all', $item['roles'] ?? [])) {
            return true;
        }
        if ($isAdminGudang && in_array('AdminGudang', $item['roles'] ?? [])) {
            return true;
        }
        if ($isPetugas && in_array('PetugasOperasional', $item['roles'] ?? [])) {
            return true;
        }
        if ($isKepalaDivisi && in_array('KepalaDivisi', $item['roles'] ?? [])) {
            return true;
        }
        return false;
    });
    $notifCount = 0;
    $notifPreview = collect();
    if ($user) {
        $notifCount = \App\Models\Notifikasi::where('id_user', $user->id_user)->where('is_read', false)->count();
        $notifPreview = \App\Models\Notifikasi::where('id_user', $user->id_user)->orderByDesc('id_notifikasi')->take(5)->get();
    }
@endphp

<body class="font-sans antialiased bg-[#0f1924] text-slate-900">
    <div class="fixed inset-0" id="appBg" style="z-index:0; pointer-events:none;">
        <img src="https://image2url.com/images/1763967903998-36cfe893-299a-4b2a-ae7b-d5922f960386.png" onerror="if(!this.dataset.fallback1){this.dataset.fallback1=1;this.src='{{ asset('images/latar.png') }}';}else{this.src='{{ asset('images/warehouse-blur.jpg') }}';}" alt="Background" class="w-full h-full object-cover">
    </div>

    <div class="app-shell relative z-[1] min-h-screen flex">
        <aside id="sidebar"
            class="bg-[#041B26] text-white w-[240px] shrink-0 hidden md:flex md:flex-col transition-all duration-200">
            <div class="px-6 py-3.5 -mt-2.5 border-b border-white/10 flex items-center gap-3 logo-wrapper">
                <img src="https://image2url.com/images/1763968652947-ca796092-4a28-4c78-9565-acca57c494b9.png" alt="Logo" class="w-[64px] h-[60px] object-contain"
                     onerror="this.style.display='none'; this.parentElement.querySelector('.logo-fallback').classList.remove('hidden');">
                <div class="logo-fallback hidden w-[64px] h-[60px] rounded-xl bg-white/10 flex items-center justify-center text-xl font-semibold text-white">PT</div>
                <div class="w-[128px] h-[22px] flex items-center mt-1" style="font-family: 'Inter', sans-serif;">
                    <p class="font-semibold text-white text-[14px] leading-[22px]">PT. Garda Erha</p>
                </div>
            </div>
            <nav class="flex-1 overflow-y-auto py-5 space-y-2">
                @foreach ($navigation as $item)
                    @php
                        $isRoute = isset($item['route']);
                        $href = $isRoute ? route($item['route']) : ($item['url'] ?? '#');
                        $isActive = $isRoute ? request()->routeIs($item['route'] . '*') : false;
                    @endphp
                    <a href="{{ $href }}"
                        class="mx-4 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-[13px] font-medium transition {{ $isActive ? 'bg-[#B69364] text-white shadow-md shadow-[#B69364]/40' : 'text-white/80 hover:bg-white/10 hover:text-white' }} {{ $item['disabled'] ?? false ? 'cursor-not-allowed opacity-60' : '' }}">
                        <span class="inline-flex items-center justify-center rounded-xl w-9 h-9 bg-white/10 border border-white/10 {{ $isActive ? 'bg-white/20' : '' }}">
                            @switch($item['icon'])
                                @case('grid')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4 4h6v6H4zm10 0h6v6h-6zM4 14h6v6H4zm10 0h6v6h-6z" />
                                    </svg>
                                @break

                                @case('boxes')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 3H4.5A1.5 1.5 0 0 0 3 4.5V9h6zm12 0h-6v6h6zM3 15v4.5A1.5 1.5 0 0 0 4.5 21H9v-6zm9 0h6v6h-6z" />
                                    </svg>
                                @break

                                @case('tag')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M20.59 13.41 11 3.83a2 2 0 0 0-1.42-.58H4v5.59a2 2 0 0 0 .59 1.41l9.59 9.59a2 2 0 0 0 2.83 0l3.58-3.59a2 2 0 0 0 0-2.84ZM7 7h.01" />
                                    </svg>
                                @break

                                @case('download')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 5v10m0 0 4-4m-4 4-4-4M4 19h16" />
                                    </svg>
                                @break

                                @case('upload')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 19V9m0 0 4 4m-4-4-4 4M4 5h16" />
                                    </svg>
                                @break

                                @case('clipboard')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2m-4 0h4v3H9z" />
                                    </svg>
                                @break

                                @case('file')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M7 3h8l4 4v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z" />
                                    </svg>
                                @break

                                @case('users')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M17 20v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8m10-1a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2 10v-1a3 3 0 0 0-2.25-2.9" />
                                    </svg>
                                @break

                                @case('activity')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4 12h4l2-7 4 14 2-7h4" />
                                    </svg>
                                @break

                                @case('settings')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m10.325 4.317-.675-1.017a1 1 0 0 0-1.693 0L6.282 5.333a1 1 0 0 1-.758.382l-1.214.09a1 1 0 0 0-.92 1.09l.27 2.134a1 1 0 0 1-.276.805l-.884.884a1 1 0 0 0 0 1.414l.884.884a1 1 0 0 1 .276.805l-.27 2.134a1 1 0 0 0 .92 1.09l1.214.09a1 1 0 0 1 .758.382l1.675 2.033a1 1 0 0 0 1.693 0l1.675-2.033a1 1 0 0 1 .758-.382l1.214-.09a1 1 0 0 0 .92-1.09l-.27-2.134a1 1 0 0 1 .276-.805l.884-.884a1 1 0 0 0 0-1.414l-.884-.884a1 1 0 0 1-.276-.805l.27-2.134a1 1 0 0 0-.92-1.09l-1.214-.09a1 1 0 0 1-.758-.382L12 3.3a1 1 0 0 0-1.675 0Z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                @break
                            @endswitch
                        </span>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
            <div class="px-6 py-6 border-t border-white/10 text-xs text-white/70">
                <p>&copy; {{ date('Y') }} PT. Garda Erha</p>
                <p>Sistem Manajemen Gudang</p>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-h-screen">
            <header class="bg-white border-b border-slate-100 shadow-sm">
                <div class="px-4 sm:px-6 lg:px-10 py-3.5 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <button id="sidebarToggle"
                            class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-slate-600 hover:bg-slate-100 focus:outline-hidden"
                            aria-label="Toggle sidebar">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <div class="flex items-center">
                            <h1 class="text-lg font-semibold text-[#041B22]">Sistem Manajemen Gudang</h1>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 relative">
                        <button id="searchBtn" class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-[#041B22] hover:bg-slate-50">
                            <svg class="w-4 h-4" fill="none" stroke="#041B22" stroke-width="2.4" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21 16.65 16.65M19 11A8 8 0 1 1 3 11a8 8 0 0 1 16 0Z" />
                            </svg>
                        </button>
                        <button id="notifBtn" class="relative w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-[#041B22] hover:bg-slate-50">
                            <svg class="w-4 h-4" fill="none" stroke="#041B22" stroke-width="2.4" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0 1 18 14.158V11a6 6 0 1 0-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m1 0v1a2 2 0 1 0 4 0v-1" />
                            </svg>
                            @if($notifCount > 0)
                                <span class="absolute -top-1 -right-1 inline-flex items-center justify-center w-4 h-4 text-[10px] rounded-full bg-rose-500 text-white">{{ $notifCount }}</span>
                            @endif
                        </button>
                        <div id="notifDropdown" class="hidden absolute right-44 top-12 z-50 w-96 bg-[#F3F8FF] rounded-2xl shadow-[0_18px_35px_rgba(0,0,0,0.15)] border border-[#D7E7FF]">
                            <div class="px-4 py-3 border-b border-[#D7E7FF] bg-[#E7F1FF] rounded-t-2xl flex items-center justify-between">
                                <p class="text-sm font-semibold text-[#0B2E4F]">Notifikasi</p>
                                <button id="markAllReadBtn" class="inline-flex items-center gap-1 rounded-md border border-[#D7E7FF] px-2.5 py-1 text-xs font-semibold text-[#0B2E4F] hover:bg-[#ECF4FF]">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    Tandai semua dibaca
                                </button>
                            </div>
                            <div class="max-h-72 overflow-y-auto" id="notifList">
                                @forelse($notifPreview as $n)
                                    <div class="px-4 py-3 border-b border-[#D7E7FF] hover:bg-[#ECF4FF] text-sm cursor-pointer flex items-start gap-3" data-id="{{ $n->id_notifikasi }}">
                                        <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0 1 18 14.158V11a6 6 0 1 0-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m1 0v1a2 2 0 1 0 4 0v-1"/></svg>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-slate-800">{{ $n->pesan }}</p>
                                            <p class="text-[11px] text-slate-400 mt-0.5">ID #{{ $n->id_notifikasi }}</p>
                                        </div>
                                        <span class="mt-1 w-2 h-2 rounded-full bg-rose-500 {{ $n->is_read ? 'opacity-0' : '' }}"></span>
                                    </div>
                                @empty
                                    <div class="px-6 py-8 text-center text-slate-600 text-sm">
                                        <div class="mx-auto w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0 1 18 14.158V11a6 6 0 1 0-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m1 0v1a2 2 0 1 0 4 0v-1"/></svg>
                                        </div>
                                        <p class="mt-3">Belum ada notifikasi.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                        @auth
                            <div class="flex items-center gap-4 rounded-2xl border border-slate-200 bg-white px-3.5 py-1.5">
                                <div class="w-9 h-9 rounded-full bg-[#B69364] text-white flex items-center justify-center font-semibold uppercase ring-2 ring-[#041B22]/60">
                                    {{ \Illuminate\Support\Str::substr(auth()->user()->username ?? 'AG', 0, 2) }}
                                </div>
                                <div class="text-sm">
                                    <p class="font-semibold text-[#041B22]">{{ auth()->user()->nama_lengkap ?? auth()->user()->username ?? 'User' }}</p>
                                    @php
                                        $uname = strtolower(auth()->user()->username ?? '');
                                        $rawRole = \App\Models\User::normalizeRole(auth()->user()->role ?? null);
                                        if (!$rawRole || !in_array($rawRole, ['AdminGudang','PetugasOperasional','KepalaDivisi'])) {
                                            if (str_contains($uname, 'admin') || str_contains($uname, 'gudang')) {
                                                $rawRole = 'AdminGudang';
                                            } elseif (str_contains($uname, 'kepala') || str_contains($uname, 'kadiv') || str_contains($uname, 'divisi')) {
                                                $rawRole = 'KepalaDivisi';
                                            } else {
                                                $rawRole = 'PetugasOperasional';
                                            }
                                        }
                                        $roleLabelMap = [
                                            'AdminGudang' => 'Admin Gudang',
                                            'PetugasOperasional' => 'Petugas Operasional',
                                            'KepalaDivisi' => 'Kepala Divisi',
                                        ];
                                        $roleLabel = $roleLabelMap[$rawRole] ?? 'Pengguna';
                                        $roleStyles = [
                                            'AdminGudang' => 'bg-[#B69364]/15 text-[#B69364] border border-[#B69364]/30',
                                            'PetugasOperasional' => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
                                            'KepalaDivisi' => 'bg-indigo-100 text-indigo-700 border border-indigo-200',
                                        ];
                                        $roleClass = $roleStyles[$rawRole] ?? 'bg-slate-100 text-slate-700 border border-slate-200';
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $roleClass }}">{{ $roleLabel }}</span>
                                </div>
                                <button type="button" id="logoutBtn"
                                    class="inline-flex items-center rounded-md border border-slate-200 px-3 py-1 text-xs font-semibold text-[#041B22] hover:bg-slate-50">Logout</button>
                                <form id="logoutForm" action="{{ route('logout') }}" method="POST" data-loading="true" class="hidden">
                                    @csrf
                                </form>
                            </div>
                        @endauth
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto">
                <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-10 py-6 space-y-4">
                    @include('components.alert', ['type' => 'success', 'message' => session('success')])
                    @include('components.alert', ['type' => 'error', 'message' => session('error')])
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <div id="mobileSidebar"
        class="fixed inset-0 z-40 bg-black/30 backdrop-blur-sm hidden aria-hidden">
        <div class="absolute left-0 top-0 bottom-0 w-64 bg-white shadow-xl">
            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
                <p class="font-semibold">Navigasi</p>
                <button id="sidebarClose" class="p-1 rounded hover:bg-slate-100" aria-label="Close sidebar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6 18 18M6 18 18 6" />
                    </svg>
                </button>
            </div>
            <nav class="py-4">
                @foreach ($navigation as $item)
                    @php
                        $isRoute = isset($item['route']);
                        $href = $isRoute ? route($item['route']) : ($item['url'] ?? '#');
                        $isActive = $isRoute ? request()->routeIs($item['route'] . '*') : false;
                    @endphp
                    <a href="{{ $href }}"
                        class="mx-3 block rounded-lg px-3 py-2 text-sm font-medium {{ $isActive ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50' }} {{ $item['disabled'] ?? false ? 'opacity-60 cursor-not-allowed' : '' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
                @auth
                    <form id="mobileLogoutForm" action="{{ route('logout') }}" method="POST" class="mx-3 mt-4 hidden" data-loading="true">
                        @csrf
                    </form>
                    <button type="button" id="mobileLogoutBtn" class="mx-3 mt-4 w-full rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Logout
                    </button>
                    <script>
                        document.getElementById('mobileLogoutBtn')?.addEventListener('click', () => {
                            if (confirm('Yakin ingin logout dari sistem?')) {
                                document.getElementById('mobileLogoutForm')?.submit();
                            }
                        });
                    </script>
                @endauth
            </nav>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const appBg = document.getElementById('appBg');
            requestAnimationFrame(() => appBg?.classList.add('app-bg-ready'));

            const sidebar = document.getElementById('sidebar');
            const mobileSidebar = document.getElementById('mobileSidebar');
            const toggle = document.getElementById('sidebarToggle');
            const closeBtn = document.getElementById('sidebarClose');

            const closeMobile = () => mobileSidebar.classList.add('hidden');
            const openMobile = () => mobileSidebar.classList.remove('hidden');

            toggle?.addEventListener('click', () => {
                if (window.innerWidth >= 768) {
                    sidebar?.classList.toggle('-translate-x-full');
                } else {
                    openMobile();
                }
            });

            closeBtn?.addEventListener('click', closeMobile);
            mobileSidebar?.addEventListener('click', (event) => {
                if (event.target === mobileSidebar) {
                    closeMobile();
                }
            });

            const logoutBtn = document.getElementById('logoutBtn');
            const logoutForm = document.getElementById('logoutForm');
            
            logoutBtn?.addEventListener('click', () => {
                if (confirm('Yakin ingin logout dari sistem?')) {
                    logoutForm?.submit();
                }
            });

            document.querySelectorAll('form[data-loading]').forEach((form) => {
                form.addEventListener('submit', () => {
                    const submitButton = form.querySelector('button[type="submit"]');
                    if (!submitButton) return;

                    submitButton.setAttribute('disabled', 'disabled');
                    submitButton.classList.add('opacity-75', 'cursor-wait');

                    const spinner = submitButton.querySelector('[data-spinner]');
                    spinner?.classList.remove('hidden');
                });
            });

            const searchBtn = document.getElementById('searchBtn');
            const searchModal = document.createElement('div');
            searchModal.className = 'fixed inset-0 z-50 hidden';
            searchModal.innerHTML = `
                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
                <div class="relative max-w-lg mx-auto mt-40 bg-white rounded-2xl shadow-2xl p-6 space-y-4">
                    <input id="globalSearchInput" type="text" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-[#B69364] focus:ring-2 focus:ring-[#B69364]/20" placeholder="Cari barang berdasarkan nama atau kode..." />
                    <div class="flex justify-end">
                        <button id="globalSearchClose" class="inline-flex justify-center rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Tutup</button>
                    </div>
                </div>
            `;
            document.body.appendChild(searchModal);
            const openSearch = () => { searchModal.classList.remove('hidden'); setTimeout(() => document.getElementById('globalSearchInput')?.focus(), 10); };
            const closeSearch = () => searchModal.classList.add('hidden');
            searchBtn?.addEventListener('click', openSearch);
            searchModal.addEventListener('click', (e) => { if (e.target === searchModal) closeSearch(); });
            document.getElementById('globalSearchClose')?.addEventListener('click', closeSearch);
            document.getElementById('globalSearchInput')?.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    const q = e.target.value.trim();
                    const url = `{{ route('barang.index') }}` + (q ? (`?q=` + encodeURIComponent(q)) : '');
                    window.location.href = url;
                }
            });

            const notifBtn = document.getElementById('notifBtn');
            const notifDropdown = document.getElementById('notifDropdown');
            const notifList = document.getElementById('notifList');
            const badge = notifBtn?.querySelector('span');
            const toggleNotif = () => notifDropdown.classList.toggle('hidden');
            notifBtn?.addEventListener('click', (e) => { e.stopPropagation(); toggleNotif(); });
            document.addEventListener('click', (e) => {
                if (!notifDropdown.contains(e.target) && e.target !== notifBtn) {
                    notifDropdown.classList.add('hidden');
                }
            });
            let lastNotifs = [];

                const renderNotifs = (items) => {
                if (!notifList) return;
                if (!Array.isArray(items) || items.length === 0) {
                    notifList.innerHTML = '<div class="px-4 py-6 text-center text-slate-500 text-sm">Belum ada notifikasi.</div>';
                    if (badge) { badge.remove(); }
                    return;
                }
                const unread = items.filter(i => !i.is_read).length;
                if (unread > 0) {
                    if (badge) { badge.textContent = String(unread); }
                    else {
                        const b = document.createElement('span');
                        b.className = 'absolute -top-1 -right-1 inline-flex items-center justify-center w-4 h-4 text-[10px] rounded-full bg-rose-500 text-white';
                        b.textContent = String(unread);
                        notifBtn.appendChild(b);
                    }
                } else if (badge) {
                    badge.remove();
                }
                const pickType = (msg) => {
                    const m = (msg || '').toLowerCase();
                    if (m.includes('di bawah minimum')) return 'warning';
                    if (m.includes('ditolak')) return 'danger';
                    if (m.includes('disetujui')) return 'success';
                    return 'info';
                };
                const iconSvg = (type) => {
                    if (type === 'success') return '<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>';
                    if (type === 'danger') return '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M12 5a7 7 0 1 0 0 14 7 7 0 0 0 0-14z"/>';
                    if (type === 'warning') return '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 4h.01M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>';
                    return '<path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0 1 18 14.158V11a6 6 0 1 0-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m1 0v1a2 2 0 1 0 4 0v-1"/>';
                };
                const cls = (type) => ({
                    success: { iconBg: 'bg-emerald-100', iconText: 'text-emerald-700', dot: 'bg-emerald-500' },
                    danger: { iconBg: 'bg-rose-100', iconText: 'text-rose-700', dot: 'bg-rose-500' },
                    warning: { iconBg: 'bg-amber-100', iconText: 'text-amber-700', dot: 'bg-amber-500' },
                    info: { iconBg: 'bg-indigo-100', iconText: 'text-indigo-700', dot: 'bg-indigo-500' },
                })[type] || { iconBg: 'bg-slate-100', iconText: 'text-slate-700', dot: 'bg-slate-400' };
                notifList.innerHTML = items.slice(0, 10).map(n => {
                    const type = pickType(n.pesan);
                    const c = cls(type);
                    return `
                        <div class="px-4 py-3 border-b border-[#D7E7FF] text-sm cursor-pointer flex items-start gap-3 hover:bg-[#ECF4FF] ${n.is_read ? 'opacity-60' : ''}" data-id="${n.id_notifikasi}">
                            <div class="w-8 h-8 rounded-full ${c.iconBg} flex items-center justify-center ${c.iconText}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">${iconSvg(type)}</svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-slate-800">${n.pesan}</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">ID #${n.id_notifikasi}</p>
                            </div>
                            <span class="mt-1 w-2 h-2 rounded-full ${c.dot} ${n.is_read ? 'opacity-0' : ''}"></span>
                        </div>
                    `;
                }).join('');
            };

            const fetchNotifs = async () => {
                try {
                    const res = await fetch('/notifikasi', { credentials: 'include' });
                    const json = await res.json();
                    if (json && json.success) { lastNotifs = json.data || []; renderNotifs(lastNotifs); }
                } catch {}
            };

            notifBtn?.addEventListener('click', fetchNotifs);
            notifList?.addEventListener('click', async (e) => {
                const target = e.target.closest('[data-id]');
                if (!target) return;
                const id = target.getAttribute('data-id');
                try {
                    await fetch(`/notifikasi/${id}/read`, { method: 'PATCH', credentials: 'include', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') } });
                } catch {}
                target.classList.add('opacity-60');
                fetchNotifs();
            });
            document.getElementById('markAllReadBtn')?.addEventListener('click', async () => {
                const unreadIds = (lastNotifs || []).filter(n => !n.is_read).map(n => n.id_notifikasi);
                await Promise.all(unreadIds.map(id => fetch(`/notifikasi/${id}/read`, { method: 'PATCH', credentials: 'include', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') } })));
                fetchNotifs();
            });
        });
    </script>

    @stack('scripts')
</body>

</html>

