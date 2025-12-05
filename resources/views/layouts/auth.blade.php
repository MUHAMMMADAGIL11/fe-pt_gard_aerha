<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'RPL Inventory') }} &mdash; Login</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Poppins', var(--font-sans); }
        .auth-screen { position: relative; min-height: 100vh; overflow: hidden; }
        .dark-panel { position: absolute; top: 0; bottom: 0; width: 50%; left: -250%; background: #0f172a; opacity: .75; backdrop-filter: blur(2px); transition: left .6s ease .6s; }
        .signup-active .dark-panel { left: 50%; }
        .toggle-panel { position: absolute; top: 0; bottom: 0; width: 50%; display: flex; align-items: center; justify-content: center; text-align: left; padding: 40px; }
        .toggle-left { left: 0; transition: left .6s ease 0s; color: #fff; }
        .signup-active .toggle-left { left: -50%; }
        .toggle-right { right: -50%; transition: right .6s ease .6s; color: #fff; }
        .signup-active .toggle-right { right: 0; }
        .auth-card { position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); width: 420px; max-width: 92vw; }
        .auth-card input, .auth-card textarea, .auth-card select { background-color: #ffffff; }
        .auth-card input::-webkit-input-placeholder { color: #B3B3B3; }
        .auth-card input:-webkit-autofill,
        .auth-card input:-webkit-autofill:hover,
        .auth-card input:-webkit-autofill:focus,
        .auth-card textarea:-webkit-autofill,
        .auth-card select:-webkit-autofill { -webkit-box-shadow: 0 0 0px 1000px #ffffff inset; -webkit-text-fill-color: #1A1A1A; }
        @media (max-width: 768px) {
            .toggle-left, .dark-panel, .left-overlay, .toggle-right { display: none; }
            .auth-card { position: relative; right: auto; top: auto; transform: none; margin: 0 auto; }
        }
    </style>
</head>

<body class="min-h-screen bg-[#0e1d2a] text-slate-900 antialiased">
    <div id="authScreen" class="auth-screen">
        <div class="absolute inset-0 overflow-hidden" id="authBg">
            <img src="https://image2url.com/images/1763967903998-36cfe893-299a-4b2a-ae7b-d5922f960386.png" onerror="if(!this.dataset.fallback1){this.dataset.fallback1=1;this.src='{{ asset('images/latar.png') }}';}else{this.src='{{ asset('images/warehouse-blur.jpg') }}';}" alt="Background" class="w-full h-full object-cover">
        </div>
        <div class="dark-panel"></div>
        

        <div class="toggle-panel toggle-right text-white">
            <div class="px-6">
                <h2 class="text-xl font-semibold mb-2">Masukan akun yang sudah terdaftar </h2>
                <p class="text-sm text-white/80 mb-4"></p>
                <button id="toSignin" class="rounded-2xl bg-white/15 px-5 py-2 text-sm font-semibold hover:bg:white/25">Sign In</button>
            </div>
        </div>

        <div class="auth-card">
            <div class="relative bg-white rounded-[24px] shadow-[0_20px_40px_rgba(0,0,0,0.08)] border border-white/70 px-10 py-10 space-y-6">
                @yield('content')
            </div>
        </div>
    </div>

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const screen = document.getElementById('authScreen');
            
            document.getElementById('toSignin')?.addEventListener('click', () => screen.classList.remove('signup-active'));
        });
    </script>
</body>

</html>
