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

        

        $backgroundImage = "linear-gradient(120deg, rgba(4,27,34,0.9) 0%, rgba(4,27,34,0.75) 40%, rgba(182,147,102,0.55) 75%), url('https://images.unsplash.com/photo-1588423771073-b8903fbb85b5?auto=format&fit=crop&w=1800&q=80')";
    @endphp

    <div class="space-y-8">
                <div class="text-center text-white space-y-2">
                    <p class="text-xs uppercase tracking-[0.5em] text-white/70">PT. Garda Erha</p>
                    <h1 class="text-2xl sm:text-3xl font-semibold">Dashboard Operasional Gudang</h1>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    @foreach ($statCards as $card)
                        <div class="rounded-[20px] bg-white/95 border border-white/60 px-6 py-4 shadow-md min-h-[110px] flex flex-col justify-between">
                            <p class="text-xs uppercase tracking-[0.3em] text-slate-500">{{ $card['label'] }}</p>
                            <p class="text-[24px] font-bold text-[#041B22]">{{ $card['value'] }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-12 gap-6">
                    {{-- Left Column (Analytics & Transactions) --}}
                    <div class="col-span-12 lg:col-span-8 space-y-6">
                        {{-- Analytics Chart --}}
                        <div class="bg-white rounded-[22px] border border-slate-200 shadow-sm p-6">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h3 class="text-lg font-bold text-[#0B2E4F]">Analitik Transaksi</h3>
                                    <p class="text-sm text-slate-500">Tren barang masuk vs barang keluar (6 Bulan Terakhir)</p>
                                </div>
                            </div>
                            <div class="relative h-[320px] w-full">
                                <canvas id="transactionChart"></canvas>
                            </div>
                        </div>

                        {{-- Recent Transactions Tables --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Masuk --}}
                            <div class="bg-[#F3F8FF] rounded-[22px] border border-[#D7E7FF] shadow-[0_18px_35px_rgba(0,0,0,0.15)] h-full">
                                <div class="px-6 py-4 border-b border-[#D7E7FF] bg-[#E7F1FF] rounded-t-[22px]">
                                    <h3 class="text-base font-semibold text-[#0B2E4F]">Transaksi Masuk Terbaru</h3>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-[12px] text-slate-700">
                                        <thead class="text-[10px] uppercase text-slate-500">
                                            <tr>
                                                <th class="px-4 py-2 text-left">Tanggal</th>
                                                <th class="px-4 py-2 text-left">Barang</th>
                                                <th class="px-4 py-2 text-right">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-[#D7E7FF]">
                                            @forelse ($transaksiMasukTerbaru as $transaksi)
                                                <tr class="hover:bg-[#ECF4FF]">
                                                    <td class="px-4 py-2 text-slate-600 whitespace-nowrap">{{ optional($transaksi->tanggal)->format('d/m/Y') ?? '-' }}</td>
                                                    <td class="px-4 py-2">
                                                        <p class="font-semibold text-[#0B2E4F] line-clamp-1">{{ $transaksi->barang->nama_barang ?? '-' }}</p>
                                                    </td>
                                                    <td class="px-4 py-2 text-right font-semibold text-emerald-600">
                                                        +{{ number_format($transaksi->jumlah) }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="3" class="px-4 py-4 text-center text-slate-500">Belum ada data.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Keluar --}}
                            <div class="bg-[#F3F8FF] rounded-[22px] border border-[#D7E7FF] shadow-[0_18px_35px_rgba(0,0,0,0.15)] h-full">
                                <div class="px-6 py-4 border-b border-[#D7E7FF] bg-[#E7F1FF] rounded-t-[22px]">
                                    <h3 class="text-base font-semibold text-[#0B2E4F]">Transaksi Keluar Terbaru</h3>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-[12px] text-slate-700">
                                        <thead class="text-[10px] uppercase text-slate-500">
                                            <tr>
                                                <th class="px-4 py-2 text-left">Tanggal</th>
                                                <th class="px-4 py-2 text-left">Barang</th>
                                                <th class="px-4 py-2 text-right">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-[#D7E7FF]">
                                            @forelse ($transaksiKeluarTerbaru as $transaksi)
                                                <tr class="hover:bg-[#ECF4FF]">
                                                    <td class="px-4 py-2 text-slate-600 whitespace-nowrap">{{ optional($transaksi->tanggal)->format('d/m/Y') ?? '-' }}</td>
                                                    <td class="px-4 py-2">
                                                        <p class="font-semibold text-[#0B2E4F] line-clamp-1">{{ $transaksi->barang->nama_barang ?? '-' }}</p>
                                                    </td>
                                                    <td class="px-4 py-2 text-right font-semibold text-rose-600">
                                                        -{{ number_format($transaksi->jumlah) }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="3" class="px-4 py-4 text-center text-slate-500">Belum ada data.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column (Alerts & Top Items) --}}
                    <div class="col-span-12 lg:col-span-4 space-y-6">
                        {{-- Low Stock Alert --}}
                        @if($lowStockItems->count() > 0)
                        <div class="bg-white rounded-[20px] border border-red-200 shadow-sm overflow-hidden">
                            <div class="bg-red-50 px-5 py-4 border-b border-red-100 flex items-center gap-3">
                                <div class="p-1.5 bg-red-100 rounded-lg text-red-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <h3 class="font-bold text-red-900 text-sm">Perhatian: Stok Menipis!</h3>
                            </div>
                            <div class="divide-y divide-red-50 max-h-[300px] overflow-y-auto">
                                @foreach($lowStockItems as $item)
                                    <div class="px-5 py-3 hover:bg-red-50/50 transition-colors">
                                        <div class="flex justify-between items-start mb-1">
                                            <p class="font-semibold text-slate-800 text-sm line-clamp-1">{{ $item->nama_barang }}</p>
                                            <span class="text-[10px] font-bold text-red-600 bg-red-100 px-2 py-0.5 rounded-full">Sisa: {{ $item->stok }}</span>
                                        </div>
                                        <p class="text-[11px] text-slate-500">Min: {{ $item->stok_minimum }} • Kode: {{ $item->kode_barang }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- Top Selling Items --}}
                        <div class="bg-white rounded-[20px] border border-slate-200 shadow-sm overflow-hidden">
                            <div class="px-5 py-4 border-b border-slate-100">
                                <h3 class="font-bold text-slate-800 text-sm">Barang Paling Laku</h3>
                            </div>
                            <div class="divide-y divide-slate-50">
                                @forelse($topBarangLaku as $index => $item)
                                    <div class="px-5 py-3 flex items-center gap-3 hover:bg-slate-50 transition-colors">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-xs">
                                            #{{ $index + 1 }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-slate-800 truncate">{{ $item->barang->nama_barang ?? 'Unknown' }}</p>
                                            <p class="text-[11px] text-slate-500">{{ $item->barang->kode_barang ?? '-' }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-bold text-slate-800">{{ number_format($item->total_keluar) }}</p>
                                            <p class="text-[10px] text-slate-400">Terjual</p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="px-5 py-4 text-center text-sm text-slate-500">Belum ada data penjualan.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
 @endsection

 @push('scripts')
 <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 <script>
     document.addEventListener('DOMContentLoaded', function() {
         const ctx = document.getElementById('transactionChart').getContext('2d');
         new Chart(ctx, {
             type: 'line',
             data: {
                 labels: @json($chartData['labels']),
                 datasets: [
                     {
                         label: 'Barang Masuk',
                         data: @json($chartData['masuk']),
                         borderColor: '#10B981', // Emerald 500
                         backgroundColor: 'rgba(16, 185, 129, 0.1)',
                         borderWidth: 2,
                         tension: 0.4,
                         fill: true,
                         pointBackgroundColor: '#fff',
                         pointBorderColor: '#10B981',
                         pointHoverBackgroundColor: '#10B981',
                         pointHoverBorderColor: '#fff'
                     },
                     {
                         label: 'Barang Keluar',
                         data: @json($chartData['keluar']),
                         borderColor: '#F43F5E', // Rose 500
                         backgroundColor: 'rgba(244, 63, 94, 0.1)',
                         borderWidth: 2,
                         tension: 0.4,
                         fill: true,
                         pointBackgroundColor: '#fff',
                         pointBorderColor: '#F43F5E',
                         pointHoverBackgroundColor: '#F43F5E',
                         pointHoverBorderColor: '#fff'
                     }
                 ]
             },
             options: {
                 responsive: true,
                 maintainAspectRatio: false,
                 plugins: {
                     legend: {
                         position: 'top',
                         labels: {
                             font: {
                                 family: "'Inter', sans-serif",
                                 size: 12
                             },
                             usePointStyle: true,
                             boxWidth: 8
                         }
                     },
                     tooltip: {
                         mode: 'index',
                         intersect: false,
                         backgroundColor: 'rgba(255, 255, 255, 0.9)',
                         titleColor: '#1e293b',
                         bodyColor: '#475569',
                         borderColor: '#e2e8f0',
                         borderWidth: 1,
                         padding: 10,
                         displayColors: true
                     }
                 },
                 scales: {
                     y: {
                         beginAtZero: true,
                         grid: {
                             color: '#f1f5f9',
                             drawBorder: false
                         },
                         ticks: {
                             font: {
                                 family: "'Inter', sans-serif",
                                 size: 11
                             },
                             color: '#64748b'
                         }
                     },
                     x: {
                         grid: {
                             display: false,
                             drawBorder: false
                         },
                         ticks: {
                             font: {
                                 family: "'Inter', sans-serif",
                                 size: 11
                             },
                             color: '#64748b'
                         }
                     }
                 },
                 interaction: {
                     mode: 'nearest',
                     axis: 'x',
                     intersect: false
                 }
             }
         });
     });
 </script>
 @endpush
