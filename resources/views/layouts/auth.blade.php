<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'RPL Inventory') }} &mdash; Login</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font modern (match sample: Poppins) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        /* Base */
        body { font-family: 'Poppins', var(--font-sans); }
        .auth-screen { position: relative; min-height: 100vh; overflow: hidden; }

        /* Animated panels (kept, but background image is plain) */
        .dark-panel { position: absolute; top: 0; bottom: 0; width: 50%; left: -250%; background: #0f172a; opacity: .75; backdrop-filter: blur(2px); transition: left .6s ease .6s; }
        .signup-active .dark-panel { left: 50%; }
        .toggle-panel { position: absolute; top: 0; bottom: 0; width: 50%; display: flex; align-items: center; justify-content: center; text-align: left; padding: 40px; }
        .toggle-left { left: 0; transition: left .6s ease 0s; color: #fff; }
        .signup-active .toggle-left { left: -50%; }
        .toggle-right { right: -50%; transition: right .6s ease .6s; color: #fff; }
        .signup-active .toggle-right { right: 0; }
        .auth-card { position: absolute; right: 0; top: 50%; transform: translateY(-50%); width: 420px; max-width: 92vw; transition: right .6s ease 1.2s; }
        .signup-active .auth-card { right: 50%; }

        /* Responsive */
        @media (max-width: 768px) {
            .toggle-left, .dark-panel, .left-overlay, .toggle-right { display: none; }
            .auth-card { position: relative; right: auto; top: auto; transform: none; margin: 0 auto; }
        }
    </style>
</head>

<body class="min-h-screen bg-[#0e1d2a] text-slate-900 antialiased">
    <div id="authScreen" class="auth-screen">
        <!-- Background (warehouse image) -->
        <div class="absolute inset-0 overflow-hidden" id="authBg">
            <img src="https://image2url.com/images/1763967903998-36cfe893-299a-4b2a-ae7b-d5922f960386.png" onerror="if(!this.dataset.fallback1){this.dataset.fallback1=1;this.src='{{ asset('images/latar.png') }}';}else{this.src='{{ asset('images/warehouse-blur.jpg') }}';}" alt="Background" class="w-full h-full object-cover">
        </div>

        <!-- Moving dark panel (animation only) -->
        <div class="dark-panel"></div>

        <!-- Left content (Sign Up prompt) -->
        <div class="toggle-panel toggle-left">
            <div class="max-w-sm">
                <h2 class="text-[26px] font-semibold leading-tight">SELAMAT DATANG </h2>
                <p class="mt-2 text-[14px] text-white/80">PT GARDA ERHA!</p>
                <button id="toSignup" class="mt-6 inline-flex w-auto rounded-xl bg-[#FF354C] px-5 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-[#E02D42] transition-all duration-150">Sign Up</button>
            </div>
        </div>

        <!-- Toggle right (Sign In) -->
        <div class="toggle-panel toggle-right text-white">
            <div class="px-6">
                <h2 class="text-xl font-semibold mb-2">Masukan akun yang sudah terdaftar </h2>
                <p class="text-sm text-white/80 mb-4"></p>
                <button id="toSignin" class="rounded-2xl bg-white/15 px-5 py-2 text-sm font-semibold hover:bg:white/25">Sign In</button>
            </div>
        </div>

        <!-- Auth card (form content) -->
        <div class="auth-card">
            <div class="relative bg-white rounded-[24px] shadow-[0_20px_40px_rgba(0,0,0,0.08)] border border-white/70 px-10 py-10 space-y-6">
                @yield('content')
            </div>
        </div>
    </div>

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Plain background: no fade-in animation

            const screen = document.getElementById('authScreen');
            document.getElementById('toSignup')?.addEventListener('click', () => screen.classList.add('signup-active'));
            document.getElementById('toSignin')?.addEventListener('click', () => screen.classList.remove('signup-active'));
        });
    </script>
</body>

</html>
