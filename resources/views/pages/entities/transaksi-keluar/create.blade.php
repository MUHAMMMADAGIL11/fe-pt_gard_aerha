@extends('layouts.app')

@section('content')
    <div class="space-y-8 px-2 sm:px-4">
        <div>
            <a href="{{ route('transaksi-keluar.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-300 hover:text-white mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Daftar Barang Keluar
            </a>
            <h1 class="text-[26px] font-bold text-white mt-3">Catat Barang Keluar</h1>
            <p class="text-[14px] text-slate-300 mt-1.5">Kurangi stok barang dengan mencatat transaksi keluar.</p>
        </div>

        <style>
            input.custom-date[type="date"]::-webkit-calendar-picker-indicator { opacity: 0; pointer-events: none; }
            #jumlah { -moz-appearance: textfield; }
            #jumlah::-webkit-outer-spin-button,
            #jumlah::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
            .select-with-icon select { -webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: none; }
        </style>
        <div class="bg-[#0F2536] rounded-2xl shadow-[0_18px_45px_rgba(0,0,0,0.45)] border border-white/5 p-6 sm:p-8">
            <form method="POST" action="{{ route('transaksi-keluar.store') }}" class="space-y-6" data-loading="true">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="id_barang" class="block text-sm font-semibold text-white">Pilih Barang <span class="text-rose-400">*</span></label>
                        <div class="relative select-with-icon">
                            <select id="id_barang" name="id_barang"
                                class="w-full rounded-xl border border-white/10 bg-[#152c3f] pl-4 pr-12 py-3 text-sm text-white appearance-none focus:border-[#B69364] focus:ring-2 focus:ring-[#B69364]/20"
                                required>
                                <option value=""> Pilih Barang </option>
                                @foreach ($barang as $item)
                                    <option value="{{ $item->id_barang }}" {{ old('id_barang') == $item->id_barang ? 'selected' : '' }}
                                        data-stok="{{ $item->stok }}">
                                        {{ $item->nama_barang }} ({{ $item->kode_barang }}) - Stok: {{ $item->stok }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                                <svg class="h-4 w-4 text-slate-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.25 8.29a.75.75 0 01-.02-1.08z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </div>
                        @error('id_barang')
                            <p class="text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                        <p id="stok-info" class="text-xs text-slate-400 hidden">Stok tersedia: <span id="stok-value">0</span></p>
                    </div>

                    <div class="space-y-2">
                        <label for="tanggal" class="block text-sm font-semibold text-white">Tanggal <span class="text-rose-400">*</span></label>
                        <div class="relative">
                            <input type="text" id="tanggal" name="tanggal" value="{{ old('tanggal') }}" placeholder="DD/MM/YYYY"
                                onfocus="this.type='date'; this.showPicker && this.showPicker(); if(!this.value) this.value='{{ date('Y-m-d') }}'"
                                onblur="if(!this.value) { this.type='text'; this.value=''; }"
                                class="w-full rounded-xl border border-white/10 bg-[#152c3f] px-4 pr-12 py-3 text-sm text-white placeholder:text-slate-300 placeholder:uppercase focus:border-[#B69364] focus:ring-2 focus:ring-[#B69364]/20 custom-date"
                                required>
                            <button type="button" class="absolute inset-y-0 right-3 flex items-center text-white/90"
                                onclick="const el=this.previousElementSibling; el.type='date'; el.showPicker && el.showPicker(); el.focus();" aria-label="Pilih tanggal">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="3" y="4" width="18" height="16" rx="2" />
                                    <path d="M16 2v4M8 2v4" />
                                    <path d="M3 10h18" />
                                </svg>
                            </button>
                        </div>
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
                        <p id="stok-warning" class="text-xs text-rose-400 hidden">Jumlah melebihi stok tersedia!</p>
                    </div>

                    <div class="space-y-2">
                        <label for="tujuan" class="block text-sm font-semibold text-white">Tujuan</label>
                        <input type="text" id="tujuan" name="tujuan" value="{{ old('tujuan') }}"
                            placeholder="Tujuan pengeluaran (opsional)"
                            class="w-full rounded-xl border border-white/10 bg-[#152c3f] px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-[#B69364] focus:ring-2 focus:ring-[#B69364]/20">
                        @error('tujuan')
                            <p class="text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:justify-end gap-3 pt-4">
                    <a href="{{ route('transaksi-keluar.index') }}"
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const barangSelect = document.getElementById('id_barang');
            const jumlahInput = document.getElementById('jumlah');
            const stokInfo = document.getElementById('stok-info');
            const stokValue = document.getElementById('stok-value');
            const stokWarning = document.getElementById('stok-warning');

            const updateStokInfo = () => {
                const selectedOption = barangSelect.options[barangSelect.selectedIndex];
                if (selectedOption && selectedOption.value) {
                    const stok = parseInt(selectedOption.dataset.stok || 0);
                    stokValue.textContent = stok;
                    stokInfo.classList.remove('hidden');
                } else {
                    stokInfo.classList.add('hidden');
                }
                checkStok();
            };

            const checkStok = () => {
                const selectedOption = barangSelect.options[barangSelect.selectedIndex];
                const jumlah = parseInt(jumlahInput.value || 0);
                
                if (selectedOption && selectedOption.value && jumlah > 0) {
                    const stok = parseInt(selectedOption.dataset.stok || 0);
                    if (jumlah > stok) {
                        stokWarning.classList.remove('hidden');
                    } else {
                        stokWarning.classList.add('hidden');
                    }
                } else {
                    stokWarning.classList.add('hidden');
                }
            };

            barangSelect.addEventListener('change', updateStokInfo);
            jumlahInput.addEventListener('input', checkStok);
            
            updateStokInfo();
        });
    </script
 @endpush
