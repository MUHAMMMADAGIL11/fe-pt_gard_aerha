@extends('layouts.app')

@section('content')
    <div class="space-y-8 px-2 sm:px-4">
        <div>
            <a href="{{ route('user.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-300 hover:text-white mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Daftar User
            </a>
            <p class="text-[11px] uppercase tracking-[0.35em] text-slate-400 font-semibold">Manajemen</p>
            <h1 class="text-[26px] font-bold text-white mt-3">Tambah User Baru</h1>
            <p class="text-[14px] text-slate-300 mt-1.5">Tambah pengguna baru ke sistem.</p>
        </div>

        <style>
            .select-with-icon select { -webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: none; }
        </style>
        <div class="bg-[#0F2536] rounded-2xl shadow-[0_18px_45px_rgba(0,0,0,0.45)] border border-white/5 p-6 sm:p-8">
            <form method="POST" action="{{ route('user.store') }}" class="space-y-6" data-loading="true">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="username" class="block text-sm font-semibold text-white">Username <span class="text-rose-400">*</span></label>
                        <input type="text" id="username" name="username" value="{{ old('username') }}"
                            placeholder="Masukkan username"
                            class="w-full rounded-xl border border-white/10 bg-[#152c3f] px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-[#B69364] focus:ring-2 focus:ring-[#B69364]/20"
                            required autofocus>
                        @error('username')
                            <p class="text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="password" class="block text-sm font-semibold text-white">Password <span class="text-rose-400">*</span></label>
                        <input type="password" id="password" name="password"
                            placeholder="Minimal 6 karakter"
                            class="w-full rounded-xl border border-white/10 bg-[#152c3f] px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-[#B69364] focus:ring-2 focus:ring-[#B69364]/20"
                            required>
                        @error('password')
                            <p class="text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="nama_lengkap" class="block text-sm font-semibold text-white">Nama Lengkap</label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}"
                            placeholder="Nama lengkap (opsional)"
                            class="w-full rounded-xl border border-white/10 bg-[#152c3f] px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-[#B69364] focus:ring-2 focus:ring-[#B69364]/20">
                        @error('nama_lengkap')
                            <p class="text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="role" class="block text-sm font-semibold text-white">Role <span class="text-rose-400">*</span></label>
                        <div class="relative select-with-icon">
                            <select id="role" name="role"
                                class="w-full rounded-xl border border-white/10 bg-[#152c3f] pl-4 pr-12 py-3 text-sm text-white appearance-none focus:border-[#B69364] focus:ring-2 focus:ring-[#B69364]/20"
                                required>
                                <option value="">-- Pilih Role --</option>
                                @php
                                    $actor = auth()->user();
                                    $allowedRoles = (isset($allowedRoles) && is_array($allowedRoles))
                                        ? $allowedRoles
                                        : ($actor && $actor->hasRole('KepalaDivisi')
                                            ? ['AdminGudang','PetugasOperasional','KepalaDivisi']
                                            : ['PetugasOperasional']);
                                @endphp
                                @foreach ($allowedRoles as $r)
                                    <option value="{{ $r }}" {{ old('role') === $r ? 'selected' : '' }}>
                                        {{ $r === 'AdminGudang' ? 'Admin Gudang' : ($r === 'PetugasOperasional' ? 'Petugas Operasional' : 'Kepala Divisi') }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                                <svg class="h-4 w-4 text-slate-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.25 8.29a.75.75 0 01-.02-1.08z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </div>
                        @error('role')
                            <p class="text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:justify-end gap-3 pt-4">
                    <a href="{{ route('user.index') }}"
                        class="inline-flex justify-center rounded-xl border border-white/10 bg-transparent px-6 py-3 text-sm font-semibold text-white hover:bg-white/5">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex justify-center rounded-xl bg-[#B69364] px-6 py-3 text-sm font-semibold text-white shadow-md shadow-[#B69364]/40 hover:bg-[#a67f4f]">
                        Simpan User
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

