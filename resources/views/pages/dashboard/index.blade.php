@extends('layouts.app')

@section('content')
    @php
        $statCards = [
            ['label' => 'Jumlah Barang', 'value' => number_format($totalBarang)],
            ['label' => 'Jumlah Kategori', 'value' => number_format($kategoriCount)],
            ['label' => 'Total Stok Tersedia', 'value' => number_format($totalStok)],
            ['label' => 'Stok Kritis', 'value' => number_format($lowStockCount)],
            ['label' => 'Permintaan Pending', 'value' => number_format($permintaanPending)],
        ];

        $statusStyles = [
            'Disetujui' => 'bg-emerald-50 text-emerald-700',
            'Pending' => 'bg-amber-50 text-amber-700',
            'Ditolak' => 'bg-rose-50 text-rose-600',
            'Selesai' => 'bg-blue-50 text-blue-700',
            'Dibatalkan' => 'bg-rose-50 text-rose-600',
        ];

        $backgroundImage = "linear-gradient(120deg, rgba(4,27,34,0.9) 0%, rgba(4,27,34,0.75) 40%, rgba(182,147,102,0.55) 75%), url('https://images.unsplash.com/photo-1588423771073-b8903fbb85b5?auto=format&fit=crop&w=1800&q=80')";
    @endphp

    <div class="space-y-8">
                <div class="text-center text-white space-y-2">
                    <p class="text-xs uppercase tracking-[0.5em] text-white/70">PT. Garda Erha</p>
                    <h1 class="text-2xl sm:text-3xl font-semibold">Sistem Manajemen Gudang</h1>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    @foreach ($statCards as $card)
                        <div class="rounded-[20px] bg-white/95 border border-white/60 px-6 py-4 shadow-md min-h-[110px] flex flex-col justify-between">
                            <p class="text-xs uppercase tracking-[0.3em] text-slate-500">{{ $card['label'] }}</p>
                            <p class="text-[24px] font-bold text-[#041B22]">{{ $card['value'] }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white/95 backdrop-blur-sm rounded-[22px] shadow-[0_18px_35px_rgba(0,0,0,0.15)]">
                        <div class="px-6 py-4 border-b border-slate-100">
                            <h3 class="text-lg font-semibold text-[#041B22]">Transaksi Masuk Terbaru</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-[13px] text-slate-700">
                                <thead class="text-[11px] uppercase text-slate-400">
                                    <tr>
                                        <th class="px-5 py-2.5 text-left">Tanggal</th>
                                        <th class="px-5 py-2.5 text-left">Nama Barang</th>
                                        <th class="px-5 py-2.5 text-center">Jumlah</th>
                                        <th class="px-5 py-2.5 text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse ($transaksiMasukTerbaru as $transaksi)
                                        @php
                                            $status = $transaksi->jumlah >= 300 ? 'Disetujui' : 'Pending';
                                        @endphp
                                        <tr>
                                            <td class="px-5 py-3 text-slate-600">{{ optional($transaksi->tanggal)->format('d/m/Y') ?? '-' }}</td>
                                            <td class="px-5 py-3">
                                                <p class="font-semibold text-[#041B22]">{{ $transaksi->barang->nama_barang ?? '-' }}</p>
                                                <p class="text-xs text-slate-400">ID #{{ $transaksi->barang->kode_barang ?? 'N/A' }}</p>
                                            </td>
                                            <td class="px-5 py-3 text-center font-semibold text-[#041B22]">
                                                {{ number_format($transaksi->jumlah) }} pcs
                                            </td>
                                            <td class="px-5 py-3 text-right">
                                                <span class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-semibold {{ $statusStyles[$status] ?? 'bg-slate-100 text-slate-700' }}">
                                                    {{ $status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-5 py-6 text-center text-slate-500 text-sm">
                                                Belum ada transaksi masuk terbaru.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bg-white/95 backdrop-blur-sm rounded-[22px] shadow-[0_18px_35px_rgba(0,0,0,0.15)]">
                        <div class="px-6 py-4 border-b border-slate-100">
                            <h3 class="text-lg font-semibold text-[#041B22]">Transaksi Keluar Terbaru</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-[13px] text-slate-700">
                                <thead class="text-[11px] uppercase text-slate-400">
                                    <tr>
                                        <th class="px-5 py-2.5 text-left">Tanggal</th>
                                        <th class="px-5 py-2.5 text-left">Nama Barang</th>
                                        <th class="px-5 py-2.5 text-center">Jumlah</th>
                                        <th class="px-5 py-2.5 text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse ($transaksiKeluarTerbaru as $transaksi)
                                        @php
                                            $status = $transaksi->jumlah >= 300 ? 'Selesai' : 'Dibatalkan';
                                        @endphp
                                        <tr>
                                            <td class="px-5 py-3 text-slate-600">{{ optional($transaksi->tanggal)->format('d/m/Y') ?? '-' }}</td>
                                            <td class="px-5 py-3">
                                                <p class="font-semibold text-[#041B22]">{{ $transaksi->barang->nama_barang ?? '-' }}</p>
                                                <p class="text-xs text-slate-400">ID #{{ $transaksi->barang->kode_barang ?? 'N/A' }}</p>
                                            </td>
                                            <td class="px-5 py-3 text-center font-semibold text-[#041B22]">
                                                {{ number_format($transaksi->jumlah) }} pcs
                                            </td>
                                            <td class="px-5 py-3 text-right">
                                                <span class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-semibold {{ $statusStyles[$status] ?? 'bg-slate-100 text-slate-700' }}">
                                                    {{ $status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-5 py-6 text-center text-slate-500 text-sm">
                                                Belum ada transaksi keluar terbaru.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
        </div>
    </div>
@endsection

