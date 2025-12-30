@extends('layouts.app')

@section('content')
    <div class="space-y-8 px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-[26px] font-bold text-white mt-3">Daftar Kategori</h1>
                <p class="text-sm text-slate-300 mt-1.5">Kelola kategori barang dengan cepat.</p>
            </div>
            <a href="{{ route('kategori.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#B69364] px-6 py-2.5 sm:py-3 text-[13px] sm:text-sm font-semibold text-white shadow-md shadow-[#B69364]/40 hover:bg-[#a67f4f] w-full sm:w-auto transition-all active:scale-95">
                + Tambah Kategori
            </a>
        </div>

        <div class="bg-[#0F2536] rounded-2xl shadow-[0_18px_45px_rgba(0,0,0,0.45)] border border-white/5">
            <!-- Mobile Card View -->
            <div class="grid grid-cols-1 gap-4 p-4 md:hidden">
                @forelse ($kategori as $item)
                    <div class="bg-[#152c3f] rounded-xl p-4 border border-white/5 shadow-sm space-y-4">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-bold text-white leading-tight">{{ $item->nama_kategori }}</h3>
                            <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-blue-500/20 text-blue-300">
                                {{ $item->barang_count }} barang
                            </span>
                        </div>

                        <div class="flex items-center gap-2 pt-3 border-t border-white/5">
                            <a href="{{ route('kategori.edit', $item->id_kategori) }}"
                                class="flex-1 inline-flex justify-center items-center py-2 rounded-lg bg-blue-500/10 text-blue-300 hover:bg-blue-500/20 text-xs font-semibold transition">
                                Edit
                            </a>
                            <button type="button" data-delete data-name="{{ $item->nama_kategori }}"
                                data-action="{{ route('kategori.destroy', $item->id_kategori) }}"
                                class="flex-1 inline-flex justify-center items-center py-2 rounded-lg bg-rose-500/10 text-rose-300 hover:bg-rose-500/20 text-xs font-semibold transition">
                                Hapus
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10">
                        <div class="inline-flex justify-center items-center w-16 h-16 rounded-full bg-white/5 mb-4">
                            <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <h3 class="text-white font-medium mb-1">Belum ada kategori</h3>
                        <p class="text-slate-400 text-sm">Silakan tambah kategori baru.</p>
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-white/5 text-[14px] text-slate-100 whitespace-nowrap">
                    <thead class="bg-[#152c3f] text-left text-slate-300 text-[12px] uppercase tracking-wide">
                        <tr>
                            <th class="px-4 py-3 md:px-7 md:py-4 font-semibold">Nama Kategori</th>
                            <th class="px-4 py-3 md:px-7 md:py-4 font-semibold text-center">Jumlah Barang</th>
                            <th class="px-4 py-3 md:px-7 md:py-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-slate-100">
                        @forelse ($kategori as $item)
                            <tr class="hover:bg-white/5 transition">
                                <td class="px-4 py-3 md:px-7 md:py-3.5 font-semibold text-white">{{ $item->nama_kategori }}</td>
                                <td class="px-4 py-3 md:px-7 md:py-3.5 text-center">
                                    <span class="px-3 py-1 text-[12px] font-semibold rounded-full bg-blue-100/80 text-blue-700">
                                        {{ $item->barang_count }} barang
                                    </span>
                                </td>
                                <td class="px-4 py-3 md:px-7 md:py-3.5 text-right space-x-1 md:space-x-2">
                                    <a href="{{ route('kategori.edit', $item->id_kategori) }}"
                                        class="inline-flex items-center rounded-md border border-blue-200/40 px-3 py-1.5 text-[12px] font-semibold text-blue-200 hover:bg-blue-200/10 transition">Edit</a>
                                    <button type="button" data-delete data-name="{{ $item->nama_kategori }}"
                                        data-action="{{ route('kategori.destroy', $item->id_kategori) }}"
                                        class="inline-flex items-center rounded-md border border-rose-200/40 px-3 py-1.5 text-[12px] font-semibold text-rose-200 hover:bg-rose-200/10 transition">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-6 text-center text-slate-400" colspan="3">Belum ada kategori.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-white/5">
                {{ $kategori->links() }}
            </div>
        </div>
    </div>
@endsection