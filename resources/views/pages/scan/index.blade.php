@extends('layouts.app')

@section('title', 'Scan QR Code')

@section('content')
    <div class="w-full px-6 py-6 space-y-6">
    <div class="flex items-center">
        <!-- Judul & Deskripsi -->
        <div>
            <h1 class="text-2xl font-bold text-white">
                Scan QR Code
            </h1>
            <p class="text-sm text-white/80">
                Arahkan kamera ke label QR barang.
            </p>
        </div>

        <!-- Tombol Kembali (Ujung Kanan) -->
        <a href="{{ route('dashboard') }}"
           class="ml-auto text-sm text-white/80 hover:text-white transition">
            Kembali
        </a>
    </div>
</div>


        @if(session('error'))
            <div class="rounded-lg bg-red-50 p-4 border border-red-200 text-red-700 text-sm mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Manual Input Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-base font-semibold text-slate-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Input Kode Manual
            </h2>
            <form action="{{ route('scan.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="code" 
                        placeholder="Contoh: BRG-001" 
                        class="block w-full pl-10 rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500 h-11"
                        required>
                </div>
                <button type="submit" 
                    class="inline-flex justify-center items-center px-6 py-2.5 border border-transparent text-sm font-medium rounded-xl text-white bg-slate-800 hover:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-900 shadow-lg shadow-slate-200 transition-all">
                    Cari Barang
                </button>
            </form>
            <p class="text-xs text-slate-500 mt-3 ml-1">Gunakan fitur ini jika kamera tidak tersedia atau kode QR tidak terbaca.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden max-w-[280px] mx-auto">
            <div id="reader" class="w-full bg-slate-900"></div>
            <div class="p-3 text-center">
                <p id="status" class="text-xs text-slate-500">Menunggu kamera...</p>
            </div>
        </div>

        <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
            <div class="flex gap-3">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-blue-800">Tips Scanning</h3>
                    <p class="mt-1 text-sm text-blue-600">
                        Pastikan pencahayaan cukup dan QR Code terlihat jelas di dalam kotak kamera.
                        Sistem akan otomatis mengarahkan ke halaman detail barang setelah berhasil scan.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const statusElem = document.getElementById('status');
            
            function onScanSuccess(decodedText, decodedResult) {
                html5QrcodeScanner.clear();
                statusElem.textContent = "QR Code terdeteksi! Mengalihkan...";
                statusElem.className = "text-sm font-bold text-emerald-600";
                window.location.href = "{{ route('scan.index') }}?code=" + encodeURIComponent(decodedText);
            }

            function onScanFailure(error) {
                // Biarkan kosong agar tidak spam console
            }

            // Ganti inisialisasi agar lebih eksplisit
            const html5QrcodeScanner = new Html5QrcodeScanner(
                "reader",
                { 
                    fps: 10, 
                    qrbox: { width: 220, height: 220 }, // Ukuran pas untuk desain baru
                    aspectRatio: 1.0,
                    showTorchButtonIfSupported: true 
                },
                /* verbose= */ false);
            
            // Tambahkan delay sedikit untuk memastikan elemen siap
            setTimeout(() => {
                html5QrcodeScanner.render(onScanSuccess, onScanFailure);
                statusElem.textContent = "Kamera aktif. Silakan scan.";
            }, 500);
        });
    </script>
@endpush
