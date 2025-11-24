@extends('layouts.app')

@section('content')
    <div class="space-y-8 px-2 sm:px-4">
        <div>
            <a href="{{ route('permintaan-barang.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-300 hover:text-white mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Daftar Permintaan
            </a>
            <p class="text-[11px] uppercase tracking-[0.35em] text-slate-400 font-semibold">Permintaan</p>
            <h1 class="text-[26px] font-bold text-white mt-3">Ajukan Permintaan Barang</h1>
            <p class="text-[14px] text-slate-300 mt-1.5">Ajukan permintaan barang untuk keperluan operasional.</p>
        </div>

        <div class="bg-[#0F2536] rounded-2xl shadow-[0_18px_45px_rgba(0,0,0,0.45)] border border-white/5 p-6 sm:p-8">
            <form method="POST" action="{{ route('permintaan-barang.store') }}" class="space-y-6" data-loading="true">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="id_barang" class="block text-sm font-semibold text-white">Pilih Barang <span class="text-rose-400">*</span></label>
                        <select id="id_barang" name="id_barang"
                            class="w-full rounded-xl border border-white/10 bg-[#152c3f] px-4 py-3 text-sm text-white focus:border-[#B69364] focus:ring-2 focus:ring-[#B69364]/20"
                            required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach ($barang as $item)
                                <option value="{{ $item->id_barang }}" {{ old('id_barang') == $item->id_barang ? 'selected' : '' }}
                                    data-stok="{{ $item->stok }}">
                                    {{ $item->nama_barang }} ({{ $item->kode_barang }}) - Stok: {{ $item->stok }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_barang')
                            <p class="text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                        <p id="stok-info" class="text-xs text-slate-400">Stok tersedia akan ditampilkan setelah memilih barang</p>
                    </div>

                    <div class="space-y-2">
                        <label for="jumlah_diminta" class="block text-sm font-semibold text-white">Jumlah Diminta <span class="text-rose-400">*</span></label>
                        <input type="number" id="jumlah_diminta" name="jumlah_diminta" value="{{ old('jumlah_diminta') }}" min="1"
                            placeholder="Masukkan jumlah yang diminta"
                            class="w-full rounded-xl border border-white/10 bg-[#152c3f] px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-[#B69364] focus:ring-2 focus:ring-[#B69364]/20"
                            required>
                        @error('jumlah_diminta')
                            <p class="text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2 md:col-span-2">
                        <label for="keterangan" class="block text-sm font-semibold text-white">Keterangan</label>
                        <textarea id="keterangan" name="keterangan" rows="3"
                            placeholder="Jelaskan keperluan permintaan barang ini (opsional)"
                            class="w-full rounded-xl border border-white/10 bg-[#152c3f] px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-[#B69364] focus:ring-2 focus:ring-[#B69364]/20">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <p class="text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:justify-end gap-3 pt-4">
                    <a href="{{ route('permintaan-barang.index') }}"
                        class="inline-flex justify-center rounded-xl border border-white/10 bg-transparent px-6 py-3 text-sm font-semibold text-white hover:bg-white/5">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex justify-center rounded-xl bg-[#B69364] px-6 py-3 text-sm font-semibold text-white shadow-md shadow-[#B69364]/40 hover:bg-[#a67f4f]">
                        Ajukan Permintaan
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
            const stokInfo = document.getElementById('stok-info');

            barangSelect.addEventListener('change', () => {
                const selectedOption = barangSelect.options[barangSelect.selectedIndex];
                if (selectedOption && selectedOption.value) {
                    const stok = parseInt(selectedOption.dataset.stok || 0);
                    stokInfo.textContent = `Stok tersedia: ${stok} pcs`;
                    if (stok === 0) {
                        stokInfo.classList.add('text-amber-400');
                        stokInfo.textContent += ' (Stok habis - permintaan tetap bisa diajukan)';
                    } else {
                        stokInfo.classList.remove('text-amber-400');
                    }
                } else {
                    stokInfo.textContent = 'Stok tersedia akan ditampilkan setelah memilih barang';
                }
            });
        });
    </script>
@endpush

