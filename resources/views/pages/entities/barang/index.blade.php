@extends('layouts.app')

@section('content')
    <div class="space-y-8 px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-[26px] font-bold text-white mt-3">Daftar Barang</h1>
                <p class="text-sm text-slate-300 mt-1.5">Kelola stok, kategori, dan detail barang dengan cepat.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <form action="{{ route('barang.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
                    @if(request('q'))
                        <input type="hidden" name="q" value="{{ request('q') }}">
                    @endif
                    <select name="kategori" onchange="this.form.submit()" 
                        class="h-10 sm:h-11 rounded-lg border border-white/10 bg-[#B69364] text-white text-sm px-3 focus:ring-blue-500 focus:border-blue-500 w-full sm:w-48">
                        <option value="" class="bg-[#152c3f] text-white">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id_kategori }}" class="bg-[#152c3f] text-white" {{ request('kategori') == $cat->id_kategori ? 'selected' : '' }}>
                                {{ $cat->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </form>

                @if(auth()->user()->hasRole('AdminGudang'))
                <a href="{{ route('barang.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#B69364] px-6 py-2.5 sm:py-3 text-[13px] sm:text-sm font-semibold text-white shadow-md shadow-[#B69364]/40 hover:bg-[#a67f4f] w-full sm:w-auto transition-all active:scale-95">
                    + Tambah Barang
                </a>
                @endif
            </div>
        </div>

        @if(session('error'))
            <div class="rounded-lg bg-red-500/10 border border-red-500/20 p-4 text-red-200 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-[#0F2536] rounded-2xl shadow-[0_18px_45px_rgba(0,0,0,0.45)] border border-white/5">
            <!-- Mobile Card View -->
            <div class="grid grid-cols-1 gap-4 p-4 md:hidden">
                @forelse ($barang as $item)
                    <div class="bg-[#152c3f] rounded-xl p-4 border border-white/5 shadow-sm space-y-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-white/10 text-slate-300 mb-1">
                                    {{ $item->kode_barang }}
                                </span>
                                <h3 class="text-lg font-bold text-white leading-tight">{{ $item->nama_barang }}</h3>
                                <p class="text-xs text-slate-400 mt-1">Min. Stok: {{ $item->stok_minimum }}</p>
                            </div>
                            <div class="text-right">
                                <span class="px-3 py-1 text-xs font-bold rounded-full {{ $item->stok < $item->stok_minimum ? 'bg-rose-500/20 text-rose-300' : 'bg-emerald-500/20 text-emerald-300' }}">
                                    {{ $item->stok }} Unit
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-sm pt-3 border-t border-white/5">
                            <span class="text-slate-400 text-xs">{{ $item->kategori->nama_kategori ?? '-' }}</span>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('barang.print-label', $item->id_barang) }}" target="_blank"
                                    class="p-2 rounded-lg bg-white/5 text-slate-300 hover:bg-white/10 transition" title="Cetak Label">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4h2v-4zm-6 0H6.414a1 1 0 00-.707.293L4.293 16.707A1 1 0 005 17h3m10-13a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12V4z" /></svg>
                                </a>
                                @if(auth()->user()->hasRole('AdminGudang'))
                                    <a href="{{ route('barang.edit', $item->id_barang) }}"
                                        class="p-2 rounded-lg bg-blue-500/10 text-blue-300 hover:bg-blue-500/20 transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </a>
                                    <button type="button" data-delete data-name="{{ $item->nama_barang }}"
                                        data-action="{{ route('barang.destroy', $item->id_barang) }}"
                                        class="p-2 rounded-lg bg-rose-500/10 text-rose-300 hover:bg-rose-500/20 transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10">
                        <div class="inline-flex justify-center items-center w-16 h-16 rounded-full bg-white/5 mb-4">
                            <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                        <h3 class="text-white font-medium mb-1">Belum ada barang</h3>
                        <p class="text-slate-400 text-sm">Silakan tambahkan barang baru.</p>
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-white/5 text-[14px] text-slate-100 whitespace-nowrap">
                    <thead class="bg-[#152c3f] text-left text-slate-300 text-[12px] uppercase tracking-wide">
                        <tr>
                            <th class="px-4 py-3 md:px-7 md:py-4 font-semibold">Kode</th>
                            <th class="px-4 py-3 md:px-7 md:py-4 font-semibold">Nama Barang</th>
                            <th class="px-4 py-3 md:px-7 md:py-4 font-semibold hidden md:table-cell">Kategori</th>
                            <th class="px-4 py-3 md:px-7 md:py-4 font-semibold text-right">Stok</th>
                            <th class="px-4 py-3 md:px-7 md:py-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-slate-100">
                        @forelse ($barang as $item)
                            <tr class="hover:bg-white/5 transition">
                                <td class="px-4 py-3 md:px-7 md:py-3.5 font-semibold text-white">{{ $item->kode_barang }}</td>
                                <td class="px-4 py-3 md:px-7 md:py-3.5">
                                    <p class="font-semibold text-white">{{ $item->nama_barang }}</p>
                                    <p class="text-[12px] text-slate-400 mt-0.5">Stok minimum: {{ $item->stok_minimum }}</p>
                                </td>
                                <td class="px-4 py-3 md:px-7 md:py-3.5 text-slate-300 hidden md:table-cell">{{ $item->kategori->nama_kategori ?? '-' }}</td>
                                <td class="px-4 py-3 md:px-7 md:py-3.5 text-right">
                                    <span
                                        class="px-3 py-1 text-[12px] font-semibold rounded-full {{ $item->stok < $item->stok_minimum ? 'bg-rose-100/80 text-rose-700' : 'bg-emerald-100/80 text-emerald-700' }}">
                                        {{ $item->stok }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 md:px-7 md:py-3.5 text-right space-x-1 md:space-x-2">
                                    <a href="{{ route('barang.print-label', $item->id_barang) }}" target="_blank"
                                        class="inline-flex items-center justify-center rounded-md border border-slate-600/40 w-8 h-8 md:w-auto md:h-auto md:px-3 md:py-1.5 text-[12px] font-semibold text-slate-300 hover:bg-white/10 hover:text-white transition" title="Cetak Label QR">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4h2v-4zm-6 0H6.414a1 1 0 00-.707.293L4.293 16.707A1 1 0 005 17h3m10-13a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12V4z" /></svg>
                                    </a>
                                    @if(auth()->user()->hasRole('AdminGudang'))
                                    <a href="{{ route('barang.edit', $item->id_barang) }}"
                                        class="inline-flex items-center rounded-md border border-blue-200/40 px-3 py-1.5 text-[12px] font-semibold text-blue-200 hover:bg-blue-200/10 transition">Edit</a>
                                    @endif
                                    @if(auth()->user()->hasRole('AdminGudang'))
                                        <button type="button" data-delete data-name="{{ $item->nama_barang }}"
                                            data-action="{{ route('barang.destroy', $item->id_barang) }}"
                                            class="inline-flex items-center rounded-md border border-rose-200/40 px-3 py-1.5 text-[12px] font-semibold text-rose-200 hover:bg-rose-200/10 transition">
                                            Hapus
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-6 text-center text-slate-400" colspan="5">Belum ada data barang.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-white/5">
                {{ $barang->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Script sudah menggunakan global handler di layouts/app.blade.php -->
@endpush
