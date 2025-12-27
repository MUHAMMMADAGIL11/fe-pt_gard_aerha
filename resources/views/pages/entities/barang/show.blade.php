@extends('layouts.app')

@section('title', 'Detail Barang')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white -800">Detail Barang</h1>
            <p class="text-sm text-white -500">Informasi lengkap mengenai barang inventaris.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('scan.index') }}"
                class="inline-flex items-center rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-slate-900">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4h-4v-4H8m1-6V4m0 0H5.4M12 4h6.6M12 20v1m-6-1h2m-2-4H4m0 4h1.4M16 20h2.6" />
                </svg>
                Scan Lagi
            </a>
            <a href="{{ route('barang.index') }}"
                class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">
                Kembali
            </a>
            @if(auth()->user()->hasRole(['AdminGudang', 'KepalaDivisi']))
                <a href="{{ route('barang.edit', $barang->id_barang) }}"
                    class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
                    Edit Barang
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h2 class="font-semibold text-slate-800">Informasi Utama</h2>
                </div>
                <div class="p-6 grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-500">Kode Barang</label>
                        <div class="mt-1 text-lg font-semibold text-slate-900">{{ $barang->kode_barang }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-500">Kategori</label>
                        <div class="mt-1 inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-sm font-medium text-slate-800">
                            {{ $barang->kategori->nama_kategori ?? '-' }}
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-500">Nama Barang</label>
                        <div class="mt-1 text-lg text-slate-900">{{ $barang->nama_barang }}</div>
                    </div>
                </div>
            </div>

            <!-- Stok Info -->
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h2 class="font-semibold text-slate-800">Informasi Stok</h2>
                </div>
                <div class="p-6 grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div class="rounded-lg bg-emerald-50 p-4 border border-emerald-100">
                        <label class="block text-sm font-medium text-emerald-600">Stok Saat Ini</label>
                        <div class="mt-1 text-3xl font-bold text-emerald-700">{{ $barang->stok }}</div>
                    </div>
                    <div class="rounded-lg bg-rose-50 p-4 border border-rose-100">
                        <label class="block text-sm font-medium text-rose-600">Stok Minimum</label>
                        <div class="mt-1 text-3xl font-bold text-rose-700">{{ $barang->stok_minimum }}</div>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-4 border border-slate-100 flex items-center justify-center">
                        <div class="text-center">
                            <label class="block text-sm font-medium text-slate-500">Status</label>
                            @if($barang->stok <= $barang->stok_minimum)
                                <span class="mt-1 inline-flex items-center gap-1 rounded-full bg-rose-100 px-3 py-1 text-sm font-semibold text-rose-700">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                    Stok Menipis
                                </span>
                            @else
                                <span class="mt-1 inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-sm font-semibold text-emerald-700">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    Aman
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- QR Code Side -->
        <div class="space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h2 class="font-semibold text-slate-800">QR Code</h2>
                </div>
                <div class="p-6 flex flex-col items-center gap-4">
                    <div id="qrcode" class="p-2 bg-white rounded-lg border border-slate-100 shadow-sm"></div>
                    <p class="text-xs text-slate-400 text-center">Scan untuk melihat detail barang ini.</p>
                    <a href="{{ route('barang.print-label', $barang->id_barang) }}" target="_blank" 
                       class="w-full inline-flex justify-center items-center gap-2 rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-900 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                        Cetak Label
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        new QRCode(document.getElementById("qrcode"), {
            text: "{{ $barang->kode_barang }}",
            width: 150,
            height: 150,
            colorDark : "#1e293b",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    </script>
@endpush
