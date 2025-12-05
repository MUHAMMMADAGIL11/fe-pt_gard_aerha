@extends('layouts.app')

@section('content')
    <div class="space-y-8 px-2 sm:px-4">
        <div>
            <h1 class="text-[26px] font-bold text-white mt-3">Laporan Inventori</h1>
            <p class="text-[14px] text-slate-300 mt-1.5">Lihat dan cetak laporan transaksi inventori.</p>
        </div>

        <style>
            input.custom-date[type="date"]::-webkit-calendar-picker-indicator { opacity: 0; pointer-events: none; }
            .select-with-icon select { -webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: none; }
        </style>
        <div class="bg-[#0F2536] rounded-2xl shadow-[0_18px_45px_rgba(0,0,0,0.45)] border border-white/5 p-6 sm:p-8">
            <form method="GET" action="{{ route('laporan.index') }}" class="space-y-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="space-y-2">
                        <label for="tanggal_mulai" class="block text-sm font-semibold text-white">Tanggal Mulai</label>
                        <div class="relative">
                            <input type="text" id="tanggal_mulai" name="tanggal_mulai" value="{{ $tanggalMulai }}" placeholder="DD/MM/YYYY"
                                onfocus="this.type='date'; this.showPicker && this.showPicker();"
                                onblur="if(!this.value) { this.type='text'; this.value=''; }"
                                class="w-full rounded-xl border border-white/10 bg-[#152c3f] px-4 pr-12 py-3 text-sm text-white placeholder:text-slate-300 placeholder:uppercase focus:border-[#B69364] focus:ring-2 focus:ring-[#B69364]/20 custom-date">
                            <button type="button" class="absolute inset-y-0 right-3 flex items-center text-white/90"
                                onclick="const el=this.previousElementSibling; el.type='date'; el.showPicker && el.showPicker(); el.focus();" aria-label="Pilih tanggal">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="3" y="4" width="18" height="16" rx="2" />
                                    <path d="M16 2v4M8 2v4" />
                                    <path d="M3 10h18" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="tanggal_akhir" class="block text-sm font-semibold text-white">Tanggal Akhir</label>
                        <div class="relative">
                            <input type="text" id="tanggal_akhir" name="tanggal_akhir" value="{{ $tanggalAkhir }}" placeholder="DD/MM/YYYY"
                                onfocus="this.type='date'; this.showPicker && this.showPicker();"
                                onblur="if(!this.value) { this.type='text'; this.value=''; }"
                                class="w-full rounded-xl border border-white/10 bg-[#152c3f] px-4 pr-12 py-3 text-sm text-white placeholder:text-slate-300 placeholder:uppercase focus:border-[#B69364] focus:ring-2 focus:ring-[#B69364]/20 custom-date">
                            <button type="button" class="absolute inset-y-0 right-3 flex items-center text-white/90"
                                onclick="const el=this.previousElementSibling; el.type='date'; el.showPicker && el.showPicker(); el.focus();" aria-label="Pilih tanggal">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="3" y="4" width="18" height="16" rx="2" />
                                    <path d="M16 2v4M8 2v4" />
                                    <path d="M3 10h18" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="jenis" class="block text-sm font-semibold text-white">Jenis Transaksi</label>
                        <div class="relative select-with-icon">
                            <select id="jenis" name="jenis"
                                class="w-full rounded-xl border border-white/10 bg-[#152c3f] pl-4 pr-12 py-3 text-sm text-white appearance-none focus:border-[#B69364] focus:ring-2 focus:ring-[#B69364]/20">
                                <option value="semua" {{ $jenis === 'semua' ? 'selected' : '' }}>Semua</option>
                                <option value="MASUK" {{ $jenis === 'MASUK' ? 'selected' : '' }}>Masuk</option>
                                <option value="KELUAR" {{ $jenis === 'KELUAR' ? 'selected' : '' }}>Keluar</option>
                            </select>
                            <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                                <svg class="h-4 w-4 text-slate-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.25 8.29a.75.75 0 01-.02-1.08z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <div class="space-y-2 flex items-end">
                        <button type="submit"
                            class="w-full rounded-xl bg-[#B69364] px-4 py-3 text-sm font-semibold text-white shadow-md shadow-[#B69364]/40 hover:bg-[#a67f4f]">
                            Tampilkan
                        </button>
                    </div>
                </div>
            </form>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="bg-[#152c3f] rounded-xl p-4 border border-white/5">
                    <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Total Masuk</p>
                    <p class="text-2xl font-bold text-emerald-400">+{{ number_format($totalMasuk) }} pcs</p>
                </div>
                <div class="bg-[#152c3f] rounded-xl p-4 border border-white/5">
                    <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Total Keluar</p>
                    <p class="text-2xl font-bold text-rose-400">-{{ number_format($totalKeluar) }} pcs</p>
                </div>
            </div>

            <div class="flex justify-end gap-3 mb-4">
                <form method="GET" action="{{ route('laporan.export-pdf') }}" class="inline">
                    <input type="hidden" name="tanggal_mulai" value="{{ $tanggalMulai }}">
                    <input type="hidden" name="tanggal_akhir" value="{{ $tanggalAkhir }}">
                    <input type="hidden" name="jenis" value="{{ $jenis }}">
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-rose-600 px-6 py-2.5 text-[13px] font-semibold text-white shadow-md hover:bg-rose-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        Unduh PDF
                    </button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/5 text-[14px] text-slate-100">
                    <thead class="bg-[#152c3f] text-left text-slate-300 text-[12px] uppercase tracking-wide">
                        <tr>
                            <th class="px-7 py-4 font-semibold">Tanggal</th>
                            <th class="px-7 py-4 font-semibold">Jenis</th>
                            <th class="px-7 py-4 font-semibold">Nama Barang</th>
                            <th class="px-7 py-4 font-semibold">Kategori</th>
                            <th class="px-7 py-4 font-semibold text-center">Jumlah</th>
                            <th class="px-7 py-4 font-semibold">Admin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-slate-100">
                        @forelse ($transaksi as $item)
                            <tr class="hover:bg-white/5 transition">
                                <td class="px-7 py-3.5 text-slate-300">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                <td class="px-7 py-3.5">
                                    <span class="px-3 py-1 text-[12px] font-semibold rounded-full {{ $item->jenis_transaksi === 'MASUK' ? 'bg-emerald-100/80 text-emerald-700' : 'bg-rose-100/80 text-rose-700' }}">
                                        {{ $item->jenis_transaksi }}
                                    </span>
                                </td>
                                <td class="px-7 py-3.5">
                                    <p class="font-semibold text-white">{{ $item->barang->nama_barang }}</p>
                                    <p class="text-[12px] text-slate-400 mt-0.5">Kode: {{ $item->barang->kode_barang }}</p>
                                </td>
                                <td class="px-7 py-3.5 text-slate-300">{{ $item->barang->kategori->nama_kategori ?? '-' }}</td>
                                <td class="px-7 py-3.5 text-center font-semibold text-white">
                                    {{ $item->jenis_transaksi === 'MASUK' ? '+' : '-' }}{{ number_format($item->jumlah) }} pcs
                                </td>
                                <td class="px-7 py-3.5 text-slate-300">{{ $item->user->username ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-6 text-center text-slate-400" colspan="6">Tidak ada transaksi pada rentang tanggal ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
