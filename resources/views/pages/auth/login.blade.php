@extends('layouts.auth')

@section('content')
    <div class="space-y-8">
        <div class="text-center space-y-3">
            <img src="https://image2url.com/images/1763968652947-ca796092-4a28-4c78-9565-acca57c494b9.png" alt="Logo PT Garda Erha" class="mx-auto w-20 h-20 object-contain" style="background: transparent;">
            <h1 class="text-[26px] font-semibold text-[#1A1A1A]">PT. Garda Erha</h1>
            <p class="text-[13px] text-slate-500">Sistem Inventori Gudang</p>
        </div>

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('status'))
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.process') }}" class="space-y-6" data-loading="true">
            @csrf
            <div class="space-y-2">
                <label for="username" class="block text-[14px] font-semibold text-[#1A1A1A]">Username</label>
                <input id="username" type="text" name="username" value="{{ old('username') }}" autocomplete="username"
                    placeholder="Masukkan Username"
                    class="w-full h-12 rounded-xl border border-[#1F2937] bg-white px-4 text-[14px] text-[#1A1A1A] placeholder:text-[#B3B3B3] focus:border-[#FF354C] focus:ring-[#FF354C] mt-2"
                    required autofocus>
                @error('username')
                    <p class="text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="password" class="block text-[14px] font-semibold text-[#1A1A1A]">Password</label>
                <div class="relative mt-2">
                    <input id="password" type="password" name="password" placeholder="Masukkan password"
                        class="w-full h-12 rounded-xl border border-[#1F2937] bg-white px-4 pr-12 text-[14px] text-[#1A1A1A] placeholder:text-[#B3B3B3] focus:border-[#FF354C] focus:ring-[#FF354C]"
                        required>
                    <button type="button" id="togglePassword"
                        class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-[#FF354C]"
                        aria-label="Toggle password visibility">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                            <circle cx="12" cy="12" r="2.25" />
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full h-12 rounded-xl bg-[#FF354C] text-[14px] font-semibold text-white shadow-md hover:bg-[#E02D42] transition duration-150"
                data-loading-button>
                Masuk
            </button>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const passwordInput = document.querySelector('#password');
            const toggleButton = document.querySelector('#togglePassword');

            toggleButton?.addEventListener('click', () => {
                if (!passwordInput) return;
                const isHidden = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isHidden ? 'text' : 'password');
                toggleButton.classList.toggle('text-[#B69364]', isHidden);
            });
        });
    </script>
@endpush
