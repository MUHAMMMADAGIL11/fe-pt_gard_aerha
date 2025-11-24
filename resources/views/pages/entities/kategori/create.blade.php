@extends('layouts.app')

@section('content')
    <div class="space-y-8 px-2 sm:px-4">
        <div>
            <a href="{{ route('kategori.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-300 hover:text-white mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Daftar Kategori
            </a>
            <p class="text-[11px] uppercase tracking-[0.35em] text-slate-400 font-semibold">Master Data</p>
            <h1 class="text-[26px] font-bold text-white mt-3">Tambah Kategori Baru</h1>
            <p class="text-[14px] text-slate-300 mt-1.5">Tambah kategori baru untuk mengelompokkan barang.</p>
        </div>

        <div class="bg-[#0F2536] rounded-2xl shadow-[0_18px_45px_rgba(0,0,0,0.45)] border border-white/5 p-6 sm:p-8">
            <form method="POST" action="{{ route('kategori.store') }}" class="space-y-6" data-loading="true">
                @csrf

                <div class="space-y-2">
                    <label for="nama_kategori" class="block text-sm font-semibold text-white">Nama Kategori <span class="text-rose-400">*</span></label>
                    <input type="text" id="nama_kategori" name="nama_kategori" value="{{ old('nama_kategori') }}"
                        placeholder="Contoh: Alat Keamanan, Seragam"
                        class="w-full rounded-xl border border-white/10 bg-[#152c3f] px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-[#B69364] focus:ring-2 focus:ring-[#B69364]/20"
                        required autofocus>
                    @error('nama_kategori')
                        <p class="text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col sm:flex-row sm:justify-end gap-3 pt-4">
                    <a href="{{ route('kategori.index') }}"
                        class="inline-flex justify-center rounded-xl border border-white/10 bg-transparent px-6 py-3 text-sm font-semibold text-white hover:bg-white/5">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex justify-center rounded-xl bg-[#B69364] px-6 py-3 text-sm font-semibold text-white shadow-md shadow-[#B69364]/40 hover:bg-[#a67f4f]">
                        Simpan Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

