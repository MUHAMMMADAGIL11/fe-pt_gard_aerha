## Tujuan
- Semua endpoint backend (API) berada di `App\Http\Controllers` (root) sesuai class diagram.
- Controller halaman Blade berada di `App\Http\Controllers\Web`.
- Folder `App\Http\Controllers\Api` dikosongkan agar tidak membingungkan.

## Langkah Implementasi
1. Pastikan semua controller API berada di root:
   - Barang, PermintaanBarang, TransaksiMasuk, TransaksiKeluar, Notifikasi, Kategori, Laporan, User, LogAktivitas.
2. Tambahkan method `lihatStokBarang(Request)` ke `App\Http\Controllers\BarangController` (root) untuk rute `petugas-operasional/lihat-stok-barang`.
3. Update `routes/api.php` agar seluruh rute mengarah ke controller root (bukan namespace `Api`).
4. Pindahkan controller web ke `App\Http\Controllers\Web`:
   - `Web\DashboardController`, `Web\AuthController`, dan controller web lain tetap di folder `Web`.
5. Update `routes/web.php` untuk memakai namespace `Web` pada `AuthController`, `DashboardController`, dsb.
6. Hapus controller duplikat di folder `App\Http\Controllers\Api` (BarangController, TransaksiMasukController) agar struktur bersih.

## Verifikasi
- Jalankan `php -l` untuk semua controller, dan `php artisan route:list` memastikan rute mengarah ke controller yang benar.
- Uji endpoint penting: barang (`GET/POST/PUT/DELETE/PATCH`), permintaan barang (ajukan/approve/reject/update), transaksi masuk/keluar.
- Cek notifikasi otomatis stok minimum setelah pengurangan/ubah stok.
- Buka `/dashboard` memastikan ikon pencarian & lonceng berfungsi (pencarian menuju `barang.index?q=...`, dropdown notifikasi muncul).

## Hasil Diharapkan
- Struktur folder bersih: API di root, Web di `Web`.
- Rute dan fitur sesuai class diagram, tanpa duplikasi di folder `Api`.

Konfirmasi untuk mengeksekusi langkah-langkah di atas sekarang.