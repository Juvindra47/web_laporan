# SIMAK-SARPRAS - Sistem Informasi Manajemen Kerusakan Sekolah
SMK Ma'arif NU 1 Ajibarang. Laravel 11 / 12, PHP 8.2+, MySQL, Blade, Bootstrap 5, Chart.js.

Paket ini adalah **file tambahan untuk project Laravel baru**. Folder `app`, `bootstrap`, `database`,
`lang`, `public`, `resources`, `routes` di dalam zip ini ditimpa/digabung ke project Laravel Anda.

## 1. Pasang project Laravel baru
```bash
composer create-project laravel/laravel simak-sarpras
cd simak-sarpras
composer require barryvdh/laravel-dompdf
```

## 2. Salin isi paket ini
Ekstrak zip, lalu **copy semua folder** (`app`, `bootstrap`, `database`, `lang`, `public`, `resources`, `routes`)
ke folder `simak-sarpras` dan pilih **Replace / Timpa** bila diminta. File yang tertimpa:
`bootstrap/app.php`, `app/Models/User.php`, `app/Providers/AppServiceProvider.php`,
`routes/web.php`, `database/seeders/DatabaseSeeder.php`, dan migration `0001_01_01_000000_create_users_table.php`.

## 3. Atur .env
Buka `.env.simak-sarpras.example`, samakan nilainya dengan `.env` project (terutama DB_*, APP_LOCALE, APP_TIMEZONE).
Buat database kosong bernama `simak_sarpras` di MySQL/phpMyAdmin.

## 4. Jalankan
```bash
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```
Buka http://localhost:8000

## 5. Akun awal (hanya untuk pengembangan)
| Role    | Email                  | Username | Password        |
|---------|------------------------|----------|-----------------|
| Admin   | admin@simaksek.test    | admin    | Simaksek@2026   |
| Petugas | petugas@simaksek.test  | petugas  | Simaksek@2026   |
| Pelapor | pelapor@simaksek.test  | pelapor  | Simaksek@2026   |

Cara mengganti password: login > menu **Profil** > **Ubah Password**, atau ganti konstanta
`DEFAULT_PASSWORD` di `database/seeders/UserSeeder.php` sebelum menjalankan seeder.
Admin juga dapat mengubah password pengguna lain di **Data Pengguna**. Ganti semua password
sebelum website dipakai sungguhan.

## 6. Isi data sekolah (WAJIB oleh admin)
Alamat, koordinat, dan Google Maps sengaja dikosongkan agar tidak ada data karangan.
Login sebagai admin > **Pengaturan Website**: isi alamat, latitude/longitude, URL Google Maps,
logo, foto/video hero. Ruangan contoh di seeder bersifat generik, ganti di **Data Ruangan**.

## 7. Batas upload (video hero)
Edit `php.ini` (XAMPP: `C:\xampp\php\php.ini`), lalu restart Apache/server:
```
upload_max_filesize = 64M
post_max_size = 70M
```

## 8. Alur uji cepat
1. Login **pelapor** > Buat Laporan (foto JPG/PNG maks 2 MB) > dapat nomor tiket.
2. Login **admin** > Verifikasi Laporan > Terima (atau Tolak dengan alasan) > tentukan petugas.
3. Login **petugas** > Tugas Saya > Mulai Pekerjaan > Tambah Catatan (foto sebelum/sesudah) > Kirim Hasil.
4. Login **admin** > Verifikasi Laporan > status "Menunggu Verifikasi Hasil" > Setujui / Kembalikan.
5. Login **pelapor** > status Selesai + catatan perbaikan tampil. Lonceng notifikasi muncul di tiap tahap.
6. Admin > Laporan & Cetak PDF > unduh PDF. Admin > Pengaturan Website > ganti hero, cek landing page.

## 9. Catatan desain teknis
- Status berubah **hanya** lewat `app/Services/ReportWorkflow.php` (alur + hak akses di konstanta
  `Report::TRANSITIONS`), setiap perubahan masuk tabel `report_status_histories` dan memicu notifikasi.
- Pelapor hanya bisa membuka laporannya sendiri, petugas hanya tugas miliknya (query dibatasi, selain itu 404).
  Middleware `role:` mencegah akses dashboard role lain lewat URL (403).
- Soft delete dipakai pada users, categories, rooms, reports, repair_logs. Kategori/ruangan yang sudah
  dipakai laporan tidak bisa dihapus. Admin hanya dapat menghapus laporan berstatus Ditolak/Selesai.
- "Riwayat Aktivitas" diambil dari riwayat status laporan (termasuk penugasan petugas).
- Tabel `website_settings` memakai kolom `hero_photo` dan `hero_video` (bukan satu `hero_media`)
  supaya foto bisa menjadi cadangan video dan admin bisa berpindah jenis tanpa kehilangan file.
- File lama pengaturan dihapus hanya setelah file baru tersimpan dan hanya jika tidak dipakai kolom lain.
- Peta: OpenStreetMap dari koordinat (tanpa API key), atau iframe Google jika admin menempel link *embed*.
  Tombol "Buka di Google Maps" memakai URL yang diisi admin.

## 10. Tambahan pada paket ini (sesi lanjutan)
- **Riwayat Aktivitas Pengguna**: tabel baru `activity_logs` (migration `2026_01_01_000009_create_activity_logs_table.php`),
  model `ActivityLog`, dan `App\Services\ActivityLogger`. Mencatat login, login gagal, logout, tambah/ubah/hapus
  pengguna, kategori, ruangan, pengaturan website, profil/password, laporan, dan unduhan PDF.
  Menu **Riwayat Aktivitas** kini punya dua tab: *Aktivitas Pengguna* dan *Perubahan Status Laporan*, lengkap dengan
  pencarian, filter jenis aktivitas, filter tanggal, dan pagination.
- **Pengaman petugas**: petugas yang masih punya tugas belum selesai tidak bisa dihapus, dinonaktifkan, atau diubah
  rolenya sebelum penugasannya dipindahkan.

Setelah menimpa file, jalankan migration baru:
```bash
php artisan migrate
```
(atau `php artisan migrate:fresh --seed` jika database masih data uji).
