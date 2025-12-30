@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <style>
            #stok, #stok_minimum { -moz-appearance: textfield; }
            #stok::-webkit-outer-spin-button,
            #stok::-webkit-inner-spin-button,
            #stok_minimum::-webkit-outer-spin-button,
            #stok_minimum::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
            .select-with-icon select { -webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: none; }
        </style>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-[26px] font-bold text-white">Ubah Barang</h1>
                <p class="text-sm text-slate-300 mt-1">Perbarui informasi barang sesuai kebutuhan.</p>
            </div>
            <a href="{{ route('barang.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-300 hover:text-white" aria-label="Kembali">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>
        </div>

        <div class="bg-[#0F2536] rounded-2xl shadow-[0_18px_45px_rgba(0,0,0,0.45)] border border-white/5 p-6 sm:p-8">
            <form method="POST" action="{{ route('barang.update', $barang->id_barang) }}" class="space-y-6" data-loading="true">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label for="id_kategori" class="block text-sm font-semibold text-white mb-1">Kategori</label>
                        <div class="relative select-with-icon">
                            <select id="id_kategori" name="id_kategori"
                                class="w-full rounded-xl border {{ $errors->has('id_kategori') ? 'border-rose-300' : 'border-white/10' }} bg-[#152c3f] pl-4 pr-12 py-3 text-sm text-white appearance-none focus:border-[#B69364] focus:ring-2 focus:ring-[#B69364]/20">
                                <option value="">Pilih kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id_kategori }}"
                                        {{ old('id_kategori', $barang->id_kategori) == $category->id_kategori ? 'selected' : '' }}>
                                        {{ $category->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                                <svg class="h-4 w-4 text-slate-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.25 8.29a.75.75 0 01-.02-1.08z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </div>
                        @error('id_kategori')
                            <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="kode_barang" class="block text-sm font-semibold text-white">Kode Barang <span class="text-rose-400">*</span></label>
                        <input type="text" id="kode_barang" name="kode_barang" value="{{ old('kode_barang', $barang->kode_barang) }}"
                            placeholder="Masukkan kode barang"
                            class="w-full rounded-xl border {{ $errors->first('kode_barang') ? 'border-rose-300' : 'border-white/10' }} bg-[#152c3f] px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-[#B69364] focus:ring-2 focus:ring-[#B69364]/20" required>
                        <p class="text-xs text-slate-400">Gunakan kode unik untuk memudahkan pencarian.</p>
                        @if ($errors->first('kode_barang'))
                            <p class="text-xs text-rose-400">{{ $errors->first('kode_barang') }}</p>
                        @endif
                    </div>

                    <div class="space-y-2">
                        <label for="nama_barang" class="block text-sm font-semibold text-white">Nama Barang <span class="text-rose-400">*</span></label>
                        <input type="text" id="nama_barang" name="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}" placeholder="Masukkan nama barang"
                            class="w-full rounded-xl border {{ $errors->first('nama_barang') ? 'border-rose-300' : 'border-white/10' }} bg-[#152c3f] px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-[#B69364] focus:ring-2 focus:ring-[#B69364]/20" required>
                        @if ($errors->first('nama_barang'))
                            <p class="text-xs text-rose-400">{{ $errors->first('nama_barang') }}</p>
                        @endif
                    </div>

                    <div class="space-y-2">
                        <label for="stok" class="block text-sm font-semibold text-white">Stok Saat Ini <span class="text-rose-400">*</span></label>
                        <input type="number" id="stok" name="stok" value="{{ old('stok', $barang->stok) }}" placeholder="Masukkan stok sekarang"
                            class="w-full rounded-xl border {{ $errors->first('stok') ? 'border-rose-300' : 'border-white/10' }} bg-[#152c3f] px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-[#B69364] focus:ring-2 focus:ring-[#B69364]/20" required>
                        @if ($errors->first('stok'))
                            <p class="text-xs text-rose-400">{{ $errors->first('stok') }}</p>
                        @endif
                    </div>

                    <div class="space-y-2">
                        <label for="stok_minimum" class="block text-sm font-semibold text-white">Stok Minimum <span class="text-rose-400">*</span></label>
                        <input type="number" id="stok_minimum" name="stok_minimum" value="{{ old('stok_minimum', $barang->stok_minimum) }}" placeholder="Masukkan stok minimum"
                            class="w-full rounded-xl border {{ $errors->first('stok_minimum') ? 'border-rose-300' : 'border-white/10' }} bg-[#152c3f] px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-[#B69364] focus:ring-2 focus:ring-[#B69364]/20" required>
                        @if ($errors->first('stok_minimum'))
                            <p class="text-xs text-rose-400">{{ $errors->first('stok_minimum') }}</p>
                        @endif
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('barang.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-[rgba(255,255,255,0.1)] bg-transparent px-6 py-3 text-sm font-semibold text-[#FFFFFF] hover:bg-[rgba(255,255,255,0.05)]">Batal</a>
                    @include('components.button', [
                        'label' => 'Perbarui',
                        'type' => 'submit',
                        'variant' => 'raw',
                        'class' => 'rounded-xl bg-[#B69364] px-6 py-3 text-sm font-semibold text-[#FFFFFF] shadow-md shadow-[#B69364]/40 hover:bg-[#a67f4f]'
                    ])
                </div>
            </form>
        </div>
    </div>
 @endsection
