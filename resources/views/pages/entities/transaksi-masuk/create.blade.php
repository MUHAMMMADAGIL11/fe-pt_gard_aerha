@extends('layouts.app')

@section('content')
    <div class="space-y-8 px-2 sm:px-4">
        <div>
            <a href="{{ route('transaksi-masuk.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-300 hover:text-white mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Daftar Barang Masuk
            </a>
            <p class="text-[11px] uppercase tracking-[0.35em] text-slate-400 font-semibold">Transaksi</p>
            <h1 class="text-[26px] font-bold text-white mt-3">Catat Barang Masuk</h1>
            <p class="text-[14px] text-slate-300 mt-1.5">Tambah stok barang dengan mencatat transaksi masuk.</p>
        </div>

        <div class="bg-[#0F2536] rounded-2xl shadow-[0_18px_45px_rgba(0,0,0,0.45)] border border-white/5 p-6 sm:p-8">
            <form method="POST" action="{{ route('transaksi-masuk.store') }}" class="space-y-6" data-loading="true">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="id_barang" class="block text-sm font-semibold text-white">Pilih Barang <span class="text-rose-400">*</span></label>
                        <select id="id_barang" name="id_barang"
                            class="w-full rounded-xl border border-white/10 bg-[#152c3f] px-4 py-3 text-sm text-white focus:border-[#B69364] focus:ring-2 focus:ring-[#B69364]/20"
                            required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach ($barang as $item)
                                <option value="{{ $item->id_barang }}" {{ old('id_barang') == $item->id_barang ? 'selected' : '' }}>
                                    {{ $item->nama_barang }} ({{ $item->kode_barang }}) - Stok: {{ $item->stok }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_barang')
                            <p class="text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="tanggal" class="block text-sm font-semibold text-white">Tanggal <span class="text-rose-400">*</span></label>
                        <input type="date" id="tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}"
                            class="w-full rounded-xl border border-white/10 bg-[#152c3f] px-4 py-3 text-sm text-white focus:border-[#B69364] focus:ring-2 focus:ring-[#B69364]/20"
                            required>
                        @error('tanggal')
                            <p class="text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="jumlah" class="block text-sm font-semibold text-white">Jumlah <span class="text-rose-400">*</span></label>
                        <input type="number" id="jumlah" name="jumlah" value="{{ old('jumlah') }}" min="1"
                            placeholder="Masukkan jumlah barang"
                            class="w-full rounded-xl border border-white/10 bg-[#152c3f] px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-[#B69364] focus:ring-2 focus:ring-[#B69364]/20"
                            required>
                        @error('jumlah')
                            <p class="text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="supplier" class="block text-sm font-semibold text-white">Supplier</label>
                        <input type="text" id="supplier" name="supplier" value="{{ old('supplier') }}"
                            placeholder="Nama supplier (opsional)"
                            class="w-full rounded-xl border border-white/10 bg-[#152c3f] px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-[#B69364] focus:ring-2 focus:ring-[#B69364]/20">
                        @error('supplier')
                            <p class="text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:justify-end gap-3 pt-4">
                    <a href="{{ route('transaksi-masuk.index') }}"
                        class="inline-flex justify-center rounded-xl border border-white/10 bg-transparent px-6 py-3 text-sm font-semibold text-white hover:bg-white/5">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex justify-center rounded-xl bg-[#B69364] px-6 py-3 text-sm font-semibold text-white shadow-md shadow-[#B69364]/40 hover:bg-[#a67f4f]">
                        Simpan Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

