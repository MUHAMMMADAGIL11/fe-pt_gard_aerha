<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label QR - {{ $barang->nama_barang }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">

    <div class="flex flex-col gap-6 items-center">
        <!-- Label Container -->
        <div id="label-area" class="bg-white w-full max-w-[400px] h-[220px] border-2 border-slate-800 rounded-xl p-5 relative flex items-center justify-between shadow-2xl overflow-hidden shrink-0">
            <!-- Decorative Background -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-[#B69364]/10 rounded-bl-full -mr-12 -mt-12 z-0"></div>
            <div class="absolute bottom-0 left-0 w-20 h-20 bg-slate-100 rounded-tr-full -ml-10 -mb-10 z-0"></div>

            <div class="z-10 flex-1 pr-6 flex flex-col h-full justify-between py-1">
                <div>
                    <p class="text-[10px] font-bold text-[#B69364] uppercase tracking-[0.2em]">Inventory Asset</p>
                    <h1 class="text-xl font-black text-slate-900 leading-tight mt-1 line-clamp-2">{{ $barang->nama_barang }}</h1>
                </div>
                
                <div class="space-y-1.5 border-l-2 border-[#B69364] pl-3">
                    <div>
                        <p class="text-[10px] text-slate-500 uppercase font-semibold">Kategori</p>
                        <p class="text-sm font-bold text-slate-800">{{ $barang->kategori->nama_kategori ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-500 uppercase font-semibold">Kode Barang</p>
                        <p class="font-mono text-sm font-bold text-slate-900">{{ $barang->kode_barang }}</p>
                    </div>
                </div>
            </div>

            <div class="z-10 flex flex-col items-center justify-center bg-white p-3 rounded-lg border-2 border-slate-100 shadow-sm">
                <div id="qrcode"></div>
                <p class="text-[9px] font-mono text-center mt-1 text-slate-400">SCAN ME</p>
            </div>
        </div>

        <!-- Controls -->
        <div class="flex gap-3 no-print">
            <button onclick="window.print()" class="px-6 py-2 bg-slate-900 text-white rounded-lg font-semibold shadow hover:bg-slate-800 transition">
                Cetak Label
            </button>
            <button onclick="window.close()" class="px-6 py-2 bg-white text-slate-700 border border-slate-200 rounded-lg font-semibold shadow-sm hover:bg-slate-50 transition">
                Tutup
            </button>
        </div>
    </div>

    <script>
        new QRCode(document.getElementById("qrcode"), {
            text: "{{ $barang->kode_barang }}",
            width: 90,
            height: 90,
            colorDark : "#1e293b",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    </script>
</body>
</html>
