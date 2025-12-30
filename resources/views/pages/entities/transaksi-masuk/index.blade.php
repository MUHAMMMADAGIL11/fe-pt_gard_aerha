@extends('layouts.app')

@section('content')
    <div class="space-y-8 px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-[26px] font-bold text-white mt-3">Barang Masuk</h1>
                <p class="text-sm text-slate-300 mt-1.5">Catat semua transaksi barang masuk ke gudang.</p>
            </div>
            <a href="{{ route('transaksi-masuk.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#B69364] px-6 py-2.5 sm:py-3 text-[13px] sm:text-sm font-semibold text-white shadow-md shadow-[#B69364]/40 hover:bg-[#a67f4f] w-full sm:w-auto transition-all active:scale-95">
                + Catat Barang Masuk
            </a>
        </div>

        <div class="bg-[#0F2536] rounded-2xl shadow-[0_18px_45px_rgba(0,0,0,0.45)] border border-white/5">
            <!-- Mobile Card View -->
            <div class="grid grid-cols-1 gap-4 p-4 md:hidden">
                @forelse ($transaksi as $item)
                    <div class="bg-[#152c3f] rounded-xl p-4 border border-white/5 shadow-sm space-y-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-xs text-slate-400 block mb-0.5">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</span>
                                <h3 class="text-lg font-bold text-white leading-tight">{{ $item->barang->nama_barang ?? 'Barang Dihapus' }}</h3>
                                <p class="text-xs text-slate-400 mt-1">Kode: {{ $item->barang->kode_barang ?? '-' }}</p>
                            </div>
                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-emerald-500/20 text-emerald-300">
                                +{{ number_format($item->jumlah) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-sm pt-3 border-t border-white/5">
                            <div>
                                <p class="text-xs text-slate-400">Supplier</p>
                                <p class="text-white truncate">{{ $item->transaksiMasuk->supplier ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">Admin</p>
                                <p class="text-white truncate">{{ $item->user->username ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10">
                        <div class="inline-flex justify-center items-center w-16 h-16 rounded-full bg-white/5 mb-4">
                            <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <h3 class="text-white font-medium mb-1">Belum ada transaksi</h3>
                        <p class="text-slate-400 text-sm">Silakan catat barang masuk baru.</p>
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-white/5 text-[14px] text-slate-100 whitespace-nowrap">
                    <thead class="bg-[#152c3f] text-left text-slate-300 text-[12px] uppercase tracking-wide">
                        <tr>
                            <th class="px-4 py-3 md:px-7 md:py-4 font-semibold">Tanggal</th>
                            <th class="px-4 py-3 md:px-7 md:py-4 font-semibold">Nama Barang</th>
                            <th class="px-4 py-3 md:px-7 md:py-4 font-semibold hidden md:table-cell">Kategori</th>
                            <th class="px-4 py-3 md:px-7 md:py-4 font-semibold text-center">Jumlah</th>
                            <th class="px-4 py-3 md:px-7 md:py-4 font-semibold hidden md:table-cell">Supplier</th>
                            <th class="px-4 py-3 md:px-7 md:py-4 font-semibold hidden lg:table-cell">Admin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-slate-100">
                        @forelse ($transaksi as $item)
                            <tr class="hover:bg-white/5 transition">
                                <td class="px-4 py-3 md:px-7 md:py-3.5 text-slate-300">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 md:px-7 md:py-3.5">
                                    <p class="font-semibold text-white">{{ $item->barang->nama_barang ?? 'Barang Dihapus' }}</p>
                                    <p class="text-[12px] text-slate-400 mt-0.5">Kode: {{ $item->barang->kode_barang ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3 md:px-7 md:py-3.5 text-slate-300 hidden md:table-cell">{{ $item->barang->kategori->nama_kategori ?? '-' }}</td>
                                <td class="px-4 py-3 md:px-7 md:py-3.5 text-center">
                                    <span class="px-3 py-1 text-[12px] font-semibold rounded-full bg-emerald-100/80 text-emerald-700">
                                        +{{ number_format($item->jumlah) }} pcs
                                    </span>
                                </td>
                                <td class="px-4 py-3 md:px-7 md:py-3.5 text-slate-300 hidden md:table-cell">{{ $item->transaksiMasuk->supplier ?? '-' }}</td>
                                <td class="px-4 py-3 md:px-7 md:py-3.5 text-slate-300 hidden lg:table-cell">{{ $item->user->username ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-6 text-center text-slate-400" colspan="6">Belum ada transaksi masuk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-white/5">
                {{ $transaksi->links() }}
            </div>
        </div>
    </div>
@endsection