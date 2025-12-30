@extends('layouts.app')

@section('content')
    <div class="space-y-8 px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-[26px] font-bold text-white mt-3">Permintaan Barang</h1>
                <p class="text-sm text-slate-300 mt-1.5">
                    @if(auth()->user()->hasRole('PetugasOperasional'))
                        Lihat dan ajukan permintaan barang.
                    @else
                        Kelola semua permintaan barang dari petugas.
                    @endif
                </p>
            </div>
            @if(auth()->user()->hasRole('PetugasOperasional'))
                <a href="{{ route('permintaan-barang.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#B69364] px-6 py-2.5 sm:py-3 text-[13px] sm:text-sm font-semibold text-white shadow-md shadow-[#B69364]/40 hover:bg-[#a67f4f] w-full sm:w-auto transition-all active:scale-95">
                    + Ajukan Permintaan
                </a>
            @endif
        </div>

        <div class="bg-[#0F2536] rounded-2xl shadow-[0_18px_45px_rgba(0,0,0,0.45)] border border-white/5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/5 text-[14px] text-slate-100 whitespace-nowrap">
                    <thead class="bg-[#152c3f] text-left text-slate-300 text-[12px] uppercase tracking-wide">
                        <tr>
                            <th class="px-4 py-3 md:px-7 md:py-4 font-semibold">Tanggal</th>
                            <th class="px-4 py-3 md:px-7 md:py-4 font-semibold">Nama Barang</th>
                            <th class="px-4 py-3 md:px-7 md:py-4 font-semibold hidden md:table-cell">Petugas</th>
                            <th class="px-4 py-3 md:px-7 md:py-4 font-semibold text-center">Jumlah</th>
                            <th class="px-4 py-3 md:px-7 md:py-4 font-semibold text-center">Status</th>
                            <th class="px-4 py-3 md:px-7 md:py-4 font-semibold hidden lg:table-cell">Keterangan</th>
                            @if(auth()->user()->hasRole('AdminGudang'))
                                <th class="px-4 py-3 md:px-7 md:py-4 font-semibold text-right">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-slate-100">
                        @forelse ($permintaan as $item)
                            <tr class="hover:bg-white/5 transition">
                                <td class="px-4 py-3 md:px-7 md:py-3.5 text-slate-300">{{ \Carbon\Carbon::parse($item->created_at ?? now())->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 md:px-7 md:py-3.5">
                                    <p class="font-semibold text-white">{{ $item->barang->nama_barang ?? 'Barang Dihapus' }}</p>
                                    <p class="text-[12px] text-slate-400 mt-0.5">Kode: {{ $item->barang->kode_barang ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3 md:px-7 md:py-3.5 text-slate-300 hidden md:table-cell">{{ $item->user->username ?? '-' }}</td>
                                <td class="px-4 py-3 md:px-7 md:py-3.5 text-center font-semibold text-white">{{ number_format($item->jumlah_diminta) }} pcs</td>
                                <td class="px-4 py-3 md:px-7 md:py-3.5 text-center">
                                    @php
                                        $statusStyles = [
                                            'Menunggu Persetujuan' => 'bg-amber-100/80 text-amber-700',
                                            'Disetujui' => 'bg-emerald-100/80 text-emerald-700',
                                            'Ditolak' => 'bg-rose-100/80 text-rose-700',
                                            'Selesai' => 'bg-blue-100/80 text-blue-700',
                                        ];
                                    @endphp
                                    <span class="px-3 py-1 text-[12px] font-semibold rounded-full {{ $statusStyles[$item->status] ?? 'bg-slate-100/80 text-slate-700' }}">
                                        {{ $item->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 md:px-7 md:py-3.5 text-slate-300 hidden lg:table-cell">{{ $item->keterangan ?: '-' }}</td>
                                @if(auth()->user()->hasRole('AdminGudang'))
                                    <td class="px-4 py-3 md:px-7 md:py-3.5 text-right space-x-1 md:space-x-2">
                                        @if($item->status === 'Menunggu Persetujuan')
                                            <form method="POST" action="{{ route('permintaan-barang.approve', $item->id_permintaan) }}" class="inline" data-loading="true">
                                                @csrf
                                                <button type="submit"
                                                    class="inline-flex items-center rounded-md border border-emerald-200/40 px-3 py-1.5 text-[12px] font-semibold text-emerald-200 hover:bg-emerald-200/10 transition">
                                                    Setujui
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('permintaan-barang.reject', $item->id_permintaan) }}" class="inline" data-loading="true">
                                                @csrf
                                                <button type="submit"
                                                    class="inline-flex items-center rounded-md border border-rose-200/40 px-3 py-1.5 text-[12px] font-semibold text-rose-200 hover:bg-rose-200/10 transition">
                                                    Tolak
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-slate-400">Sudah diproses</span>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-6 text-center text-slate-400" colspan="{{ auth()->user()->hasRole('AdminGudang') ? 7 : 6 }}">
                                    Belum ada permintaan barang.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-white/5">
                {{ $permintaan->links() }}
            </div>
        </div>
    </div>
 @endsection
