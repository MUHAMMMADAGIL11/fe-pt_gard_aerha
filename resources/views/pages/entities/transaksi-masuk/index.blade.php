@extends('layouts.app')

@section('content')
    <div class="space-y-8 px-2 sm:px-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-[11px] uppercase tracking-[0.35em] text-slate-400 font-semibold">Transaksi</p>
                <h1 class="text-[26px] font-bold text-white mt-3">Barang Masuk</h1>
                <p class="text-[14px] text-slate-300 mt-1.5">Catat semua transaksi barang masuk ke gudang.</p>
            </div>
            <a href="{{ route('transaksi-masuk.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#B69364] px-6 py-2.5 text-[13px] font-semibold text-white shadow-md shadow-[#B69364]/40 hover:bg-[#a67f4f]">
                + Catat Barang Masuk
            </a>
        </div>

        <div class="bg-[#0F2536] rounded-2xl shadow-[0_18px_45px_rgba(0,0,0,0.45)] border border-white/5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/5 text-[14px] text-slate-100">
                    <thead class="bg-[#152c3f] text-left text-slate-300 text-[12px] uppercase tracking-wide">
                        <tr>
                            <th class="px-7 py-4 font-semibold">Tanggal</th>
                            <th class="px-7 py-4 font-semibold">Nama Barang</th>
                            <th class="px-7 py-4 font-semibold">Kategori</th>
                            <th class="px-7 py-4 font-semibold text-center">Jumlah</th>
                            <th class="px-7 py-4 font-semibold">Supplier</th>
                            <th class="px-7 py-4 font-semibold">Admin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-slate-100">
                        @forelse ($transaksi as $item)
                            <tr class="hover:bg-white/5 transition">
                                <td class="px-7 py-3.5 text-slate-300">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                <td class="px-7 py-3.5">
                                    <p class="font-semibold text-white">{{ $item->barang->nama_barang }}</p>
                                    <p class="text-[12px] text-slate-400 mt-0.5">Kode: {{ $item->barang->kode_barang }}</p>
                                </td>
                                <td class="px-7 py-3.5 text-slate-300">{{ $item->barang->kategori->nama_kategori ?? '-' }}</td>
                                <td class="px-7 py-3.5 text-center">
                                    <span class="px-3 py-1 text-[12px] font-semibold rounded-full bg-emerald-100/80 text-emerald-700">
                                        +{{ number_format($item->jumlah) }} pcs
                                    </span>
                                </td>
                                <td class="px-7 py-3.5 text-slate-300">{{ $item->transaksiMasuk->supplier ?? '-' }}</td>
                                <td class="px-7 py-3.5 text-slate-300">{{ $item->user->username ?? '-' }}</td>
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

