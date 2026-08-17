# ☕ BrewNest Coffee — Sistem Informasi Manajemen Coffee Shop

> **Sistem Informasi Coffee Shop Terintegrasi dengan Manajemen Multi-Role, Absensi Karyawan, dan Laporan Keuangan Berbasis Laravel**
>
> Tugas Mata Kuliah Web Lanjut — Universitas Malikussaleh

---

## 📋 Daftar Isi

- [Tentang Proyek](#tentang-proyek)
- [Fitur Utama](#fitur-utama)
- [Teknologi](#teknologi)
- [Struktur Database](#struktur-database)
- [Role & Hak Akses](#role--hak-akses)
- [Instalasi](#instalasi)
- [Konfigurasi](#konfigurasi)
- [Akun Default](#akun-default)
- [Alur Penggunaan](#alur-penggunaan)
- [Struktur Proyek](#struktur-proyek)
- [Screenshot](#screenshot)
- [Kontributor](#kontributor)

---

## 🏪 Tentang Proyek

**BrewNest Coffee** adalah sistem informasi manajemen coffee shop berbasis web yang dibangun menggunakan **Laravel 13**. Sistem ini dirancang untuk coffee shop skala menengah-besar dengan berbagai role karyawan yang memiliki tugas dan akses berbeda.

Sistem mencakup seluruh alur operasional coffee shop secara digital — mulai dari pelanggan memesan, kasir memproses transaksi, dapur/barista menerima antrian, kurir mengantarkan pesanan, hingga laporan keuangan untuk pemilik (bos).

### Konteks Bisnis

Coffee shop modern yang mewah membutuhkan sistem yang:
- Memisahkan tanggung jawab tiap karyawan secara jelas
- Meminimalkan komunikasi manual antar bagian
- Memberikan visibilitas real-time kepada manajemen
- Mendokumentasikan seluruh aktivitas operasional

---

## ✨ Fitur Utama

### 🌐 Publik (Pelanggan)
- Lihat menu lengkap dengan filter kategori & pencarian
- Tambah produk ke keranjang
- Checkout dengan pilihan **Dine In / Take Away / Delivery** per item
- Dalam 1 transaksi bisa campuran Dine In dan Take Away
- Pilihan pembayaran: Cash, QRIS, Transfer (DANA, OVO, BSI, Bank Aceh)
- Download struk PDF
- Lacak status pesanan dengan kode order

### 🏧 Kasir
- Dashboard antrian pesanan real-time (auto-refresh 30 detik)
- Konfirmasi pembayaran semua metode (Cash, QRIS, Transfer)
- Kirim pesanan ke Barista / Dapur per item
- Assign kurir untuk pesanan delivery
- Lihat status kurir (Kosong / Sibuk)
- Manajemen semua order (filter, struk, update status)

### ☕ Barista
- Antrian minuman real-time (auto-refresh 20 detik)
- Update status: Menunggu → Sedang Dibuat → Siap
- Notifikasi ke kasir otomatis saat semua item siap
- Riwayat item selesai hari ini
- Menu karyawan (absensi, gaji, cuti)

### 🍳 Dapur
- Antrian makanan real-time (auto-refresh 20 detik)
- Update status: Menunggu → Sedang Dimasak → Siap
- Notifikasi ke kasir otomatis saat semua item siap
- Riwayat item selesai hari ini
- Menu karyawan (absensi, gaji, cuti)

### 🛵 Kurir
- Dashboard delivery aktif
- Update status pengantaran: Dijemput → Sedang Diantar → Selesai / Gagal
- Toggle status diri: Kosong / Sedang Antar
- Riwayat delivery hari ini
- Notifikasi otomatis saat di-assign pesanan baru

### 🧹 Cleaning Service
- Checklist jadwal kebersihan hari ini
- Update status area: Selesai dibersihkan
- Progress bar kebersihan harian
- Menu karyawan (absensi, gaji, cuti)

### 👨‍💼 Admin (Manager)
- Dashboard statistik operasional
- Manajemen menu & produk (CRUD + upload gambar)
- Manajemen stok produk + alert stok kritis
- Generate gaji bulanan otomatis (berdasarkan absensi)
- Tambah bonus per karyawan
- Kirim slip gaji ke Bos untuk disetujui
- Approve / tolak pengajuan cuti karyawan
- Kelola jadwal kebersihan & assign ke Cleaning Service
- Lihat semua order

### 👑 Bos (Owner)
- Dashboard ringkasan bisnis (pendapatan, order, karyawan)
- Laporan keuangan bulanan & tahunan
- Download laporan PDF & Excel (CSV)
- Top 5 produk terlaris
- Approve gaji karyawan (termasuk Admin & IT)
- Generate & approve gaji Admin & IT langsung
- Lihat data karyawan lengkap
- Rekap absensi semua karyawan (filter per tanggal/karyawan)

### 💻 IT (System Administrator)
- Manajemen semua user (lihat, tambah, edit, hapus)
- Edit nama, email, no HP, password, posisi karyawan
- Verifikasi / tolak karyawan baru
- Aktif / nonaktifkan akun (semua role termasuk Admin & Bos)
- Reset password user (password baru tampil di layar)
- Kelola absensi manual (tambah, edit, hapus)
- Log aktivitas sistem (siapa melakukan apa, kapan, dari IP mana)
- Export semua data ke CSV: User, Order, Absensi, Gaji

### 🪪 Sistem Absensi
- QR Code statis bisa diprint & ditempel di toko
- Karyawan scan QR → pilih nama dari daftar → konfirmasi absen
- Auto-detect absen masuk / keluar berdasarkan waktu
- Status otomatis: Hadir / Telat (setelah 08:30) / Izin / Alpha
- Cuti yang disetujui Admin otomatis mengisi absensi sebagai "Izin"

### 📋 Sistem Cuti
- Karyawan ajukan cuti/izin/sakit dengan tanggal & alasan
- Admin menerima notifikasi pengajuan baru
- Admin approve → absensi otomatis terisi
- Admin tolak → karyawan menerima notifikasi dengan alasan
- Admin & IT ajukan cuti → diapprove oleh Bos

### 💰 Sistem Gaji
- Generate gaji otomatis dari data absensi
- Potongan otomatis: Alpha = 2% gaji pokok/hari, Telat = 0.5%/hari
- Gaji default per posisi (bisa diubah Admin)
- Admin tambah bonus manual
- Admin kirim ke Bos untuk diapprove
- Bos approve semua / per karyawan
- Karyawan menerima notifikasi saat gaji dibayar
- Download slip gaji PDF

### 🔔 Notifikasi In-App
- Notifikasi real-time dalam sistem
- Badge jumlah notif belum dibaca di navbar
- Auto-cek setiap 30 detik

---

## 🛠 Teknologi

| Komponen | Teknologi |
|---|---|
| Backend Framework | Laravel 13.x |
| PHP | 8.2+ |
| Database | MySQL 8.x |
| Frontend | Blade Templates + CSS Custom |
| PDF | barryvdh/laravel-dompdf |
| QR Code | simplesoftwareio/simple-qrcode |
| Web Server | Apache (XAMPP / Laragon) |
| Font | Playfair Display + DM Sans (Google Fonts) |

---

## 🗄 Struktur Database

### Tabel Utama (12 tabel)

| Tabel | Deskripsi |
|---|---|
| `users` | Semua akun (semua role) |
| `employee_profiles` | Profil detail karyawan (posisi, kode, gaji pokok) |
| `categories` | Kategori menu |
| `products` | Produk / menu |
| `orders` | Pesanan |
| `order_items` | Detail item per pesanan (dengan tipe per item) |
| `deliveries` | Data pengantaran kurir |
| `attendances` | Data absensi karyawan |
| `salaries` | Slip gaji per bulan per karyawan |
| `leave_requests` | Pengajuan cuti / izin |
| `user_notifications` | Notifikasi in-app |
| `activity_logs` | Log aktivitas sistem |
| `cleaning_schedules` | Jadwal kebersihan |

### Relasi Utama

```
users 1─── 1 employee_profiles
users 1───* attendances
users 1───* salaries
users 1───* leave_requests
users 1───* user_notifications
users 1───* activity_logs
categories 1───* products
orders 1───* order_items
orders 1─── 1 deliveries
order_items *───1 products
```

---

## 👥 Role & Hak Akses

| Role | Login URL | Deskripsi |
|---|---|---|
| **Pelanggan** | Tidak perlu login | Memesan menu |
| **Kasir** | `/karyawan/login` | Transaksi & antrian |
| **Barista** | `/karyawan/login` | Antrian minuman |
| **Dapur** | `/karyawan/login` | Antrian makanan |
| **Kurir** | `/karyawan/login` | Pengantaran |
| **Cleaning** | `/karyawan/login` | Kebersihan |
| **Admin** | `/staff/login` | Manajemen operasional |
| **Bos** | `/staff/login` | Laporan & persetujuan |
| **IT** | `/staff/login` | Manajemen sistem |

### Pembagian Tugas

```
KASIR          → Transaksi, antrian, assign kurir
BARISTA        → Antrian minuman (dari kasir)
DAPUR          → Antrian makanan (dari kasir)
KURIR          → Antar pesanan delivery
CLEANING       → Kebersihan area toko
ADMIN          → Menu, stok, gaji, cuti, jadwal cleaning
BOS            → Laporan keuangan, approve gaji
IT             → User management, log, export data
```

---

## ⚙️ Instalasi

### Prasyarat

- PHP >= 8.2
- Composer
- MySQL 8.x
- XAMPP / Laragon (atau web server lain)
- Node.js (opsional, untuk asset compilation)

### Langkah Instalasi

**1. Clone repository**
```bash
git clone https://github.com/username/coffeeshop.git
cd coffeeshop
```

**2. Install dependencies PHP**
```bash
composer install
```

**3. Copy file environment**
```bash
cp .env.example .env
```

**4. Generate application key**
```bash
php artisan key:generate
```

**5. Konfigurasi database** (edit `.env`):
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=coffeeshop
DB_USERNAME=root
DB_PASSWORD=
```

**6. Buat database**

Buka phpMyAdmin atau MySQL CLI:
```sql
CREATE DATABASE coffeeshop;
```

**7. Jalankan migrasi & seeder**
```bash
php artisan migrate --seed
```

**8. Buat symlink storage**
```bash
php artisan storage:link
```

**9. Jalankan server**
```bash
php artisan serve
```

Akses di browser: `http://localhost:8000`

---

## 🔧 Konfigurasi

### Timezone

Di `config/app.php`:
```php
'timezone' => 'Asia/Jakarta',
```

### Jam Batas Telat

Di `app/Http/Controllers/AttendanceController.php`:
```php
$attendance->status = $now->format('H:i') > '08:30' ? 'telat' : 'hadir';
```
Ubah `'08:30'` sesuai kebijakan toko.

### Ongkos Kirim Delivery

Di `app/Http/Controllers/OrderController.php`:
```php
$deliveryFee = $hasDelivery ? 8000 : 0;
```
Ubah `8000` sesuai tarif toko.

### Gaji Default per Posisi

Di `app/Http/Controllers/Admin/SalaryManagementController.php`:
```php
$defaultSalary = match($employee->employeeProfile->position ?? '') {
    'Kasir'    => 2500000,
    'Barista'  => 2800000,
    'Kurir'    => 2300000,
    'Dapur'    => 2500000,
    'Cleaning' => 2000000,
    default    => 2000000,
};
```

### Potongan Gaji

```php
// Alpha = 2% gaji pokok per hari
// Telat = 0.5% gaji pokok per hari
$deduction = ($totalAlpha * ($baseSalary * 0.02))
           + ($totalTelat * ($baseSalary * 0.005));
```

---

## 🔑 Akun Default

Setelah menjalankan seeder, akun berikut tersedia:

### Staff (Login di `/staff/login`)

| Role | Email | Password |
|---|---|---|
| Admin | admin@brewnest.com | password123 |
| Bos | bos@brewnest.com | password123 |
| IT | it@brewnest.com | password123 |

### Karyawan (Login di `/karyawan/login`)

Karyawan didaftarkan melalui `/karyawan/register` kemudian diverifikasi oleh IT. Atau IT bisa langsung tambahkan akun dari dashboard IT.

Contoh akun karyawan untuk testing (buat via IT dashboard atau tinker):

```php
// Buka tinker: php artisan tinker

$user = \App\Models\User::create([
    'name'      => 'Kasir 1',
    'email'     => 'kasir@brewnest.com',
    'phone'     => '081200000010',
    'password'  => bcrypt('password123'),
    'role'      => 'karyawan',
    'is_active' => true,
]);

\App\Models\EmployeeProfile::create([
    'user_id'             => $user->id,
    'position'            => 'Kasir',  // Kasir / Barista / Dapur / Kurir / Cleaning Service
    'verification_status' => 'verified',
    'joined_at'           => now(),
]);
```

---

## 🔄 Alur Penggunaan

### Alur Pemesanan Pelanggan

```
1. Buka website → Lihat menu
2. Tambah item ke keranjang
3. Checkout → Isi nama → Pilih tipe per item (Dine In / Take Away)
4. Pilih metode pembayaran → Konfirmasi
5. Dapat kode order → Bisa lacak status
6. Download struk PDF
```

### Alur Operasional Kasir

```
1. Pesanan masuk → muncul di antrian kasir
2. Konfirmasi pembayaran (Cash/QRIS langsung, Transfer setelah cek mutasi)
3. Pilih tujuan tiap item (Barista / Dapur)
4. Kirim ke dapur/barista
5. Tunggu notifikasi "Pesanan Siap"
6. Untuk Delivery → Assign kurir yang kosong
7. Tandai pesanan selesai
```

### Alur Barista / Dapur

```
1. Item muncul di dashboard (auto-refresh 20 detik)
2. Klik "Mulai Buat / Mulai Masak"
3. Klik "Siap Disajikan"
4. Kasir otomatis mendapat notifikasi
```

### Alur Kurir

```
1. Kasir assign pesanan → Kurir dapat notifikasi
2. Buka dashboard → Lihat detail pesanan & alamat
3. Klik "Mulai Antar"
4. Setelah sampai → Klik "Selesai Diantar"
5. Status kurir otomatis kembali jadi "Kosong"
```

### Alur Absensi

```
1. Print QR Code dari /absensi/scan
2. Karyawan scan QR dengan HP
3. Pilih nama dari daftar
4. Klik "Konfirmasi Absen"
5. Sistem catat jam masuk (atau jam keluar kalau sudah absen masuk)
```

### Alur Gaji Bulanan

```
Admin:
1. Admin → Manajemen Gaji → Generate Gaji Otomatis
2. Sistem hitung berdasarkan absensi + potongan alpha/telat
3. Admin tambah bonus jika ada
4. Klik "Kirim ke Bos"

Bos:
5. Bos menerima notifikasi
6. Bos → Persetujuan Gaji → Review
7. Klik "Setujui Semua" atau approve per karyawan
8. Karyawan menerima notifikasi "Gaji Dibayar"
9. Karyawan bisa download slip gaji PDF
```

---

## 📁 Struktur Proyek

```
coffeeshop/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # DashboardController, ProductController, SalaryManagementController, dll
│   │   │   ├── Auth/           # EmployeeAuthController, StaffAuthController
│   │   │   ├── Barista/        # DashboardController
│   │   │   ├── Bos/            # DashboardController, ReportController, SalaryApprovalController
│   │   │   ├── Cleaning/       # DashboardController
│   │   │   ├── Dapur/          # DashboardController
│   │   │   ├── Employee/       # SalaryController, LeaveController, DeliveryController
│   │   │   ├── IT/             # UserManagementController, UserEditController, ActivityLogController, ExportController
│   │   │   ├── Kasir/          # DashboardController, OrderQueueController
│   │   │   ├── Kurir/          # DashboardController
│   │   │   ├── AttendanceController.php
│   │   │   ├── CartController.php
│   │   │   ├── HomeController.php
│   │   │   ├── MenuController.php
│   │   │   ├── NotificationController.php
│   │   │   └── OrderController.php
│   │   └── Middleware/
│   │       ├── RoleMiddleware.php
│   │       └── ActivityLogger.php
│   └── Models/
│       ├── ActivityLog.php
│       ├── Attendance.php
│       ├── Category.php
│       ├── CleaningSchedule.php
│       ├── Delivery.php
│       ├── EmployeeProfile.php
│       ├── LeaveRequest.php
│       ├── Order.php
│       ├── OrderItem.php
│       ├── Product.php
│       ├── Salary.php
│       ├── User.php
│       └── UserNotification.php
├── database/
│   ├── migrations/             # 15+ migration files
│   └── seeders/
│       ├── CategorySeeder.php
│       ├── DatabaseSeeder.php
│       ├── ProductSeeder.php
│       ├── TransactionSeeder.php
│       └── UserSeeder.php
├── resources/
│   └── views/
│       ├── admin/              # dashboard, employees, orders, products, salary, leave, cleaning
│       ├── attendance/         # scan, select-employee, face
│       ├── auth/               # customer, employee, staff login/register
│       ├── barista/            # dashboard
│       ├── bos/                # dashboard, report, salary, employees, attendances
│       ├── cart/               # index
│       ├── cleaning/           # dashboard
│       ├── dapur/              # dashboard
│       ├── employee/           # dashboard, barcode, salary, attendance, leave
│       ├── it/                 # dashboard, users, attendance, logs, export
│       ├── kasir/              # dashboard, queue, orders
│       ├── kurir/              # dashboard
│       ├── layouts/            # app.blade.php
│       ├── menu/               # index, show
│       ├── notifications/      # index
│       └── order/              # checkout, success, track, receipt-pdf
└── routes/
    └── web.php                 # Semua route (150+ routes)
```

---

## 🌐 Daftar URL Penting

| URL | Akses | Deskripsi |
|---|---|---|
| `/` | Publik | Halaman beranda |
| `/menu` | Publik | Daftar menu |
| `/cart` | Publik | Keranjang belanja |
| `/checkout` | Publik | Halaman checkout |
| `/order/track` | Publik | Lacak pesanan |
| `/absensi/scan` | Publik | Halaman scan QR absensi |
| `/karyawan/login` | Karyawan | Login karyawan |
| `/karyawan/register` | Karyawan | Daftar karyawan baru |
| `/staff/login` | Admin/Bos/IT | Login staff |
| `/kasir/dashboard` | Kasir | Dashboard kasir |
| `/kasir/antrian` | Kasir | Antrian pesanan |
| `/barista/dashboard` | Barista | Dashboard barista |
| `/dapur/dashboard` | Dapur | Dashboard dapur |
| `/kurir/dashboard` | Kurir | Dashboard kurir |
| `/cleaning/dashboard` | Cleaning | Dashboard cleaning |
| `/admin/dashboard` | Admin | Dashboard admin |
| `/admin/products` | Admin | Manajemen produk |
| `/admin/gaji` | Admin | Manajemen gaji |
| `/admin/cuti` | Admin | Manajemen cuti |
| `/bos/dashboard` | Bos | Dashboard bos |
| `/bos/laporan` | Bos | Laporan keuangan |
| `/bos/gaji` | Bos | Persetujuan gaji |
| `/it/dashboard` | IT | Dashboard IT |
| `/it/logs` | IT | Log aktivitas |
| `/it/export` | IT | Export data |

---

## 📊 Fitur Laporan

### Untuk Bos
- Laporan keuangan bulanan & tahunan
- Grafik pendapatan per bulan (visual bar chart)
- Top 5 produk terlaris
- Total transaksi & pendapatan
- Download **PDF** — format struk profesional
- Download **Excel (CSV)** — bisa dibuka di Microsoft Excel / Google Sheets

### Untuk IT
- Export **Data User** — semua akun dengan role dan status
- Export **Data Order** — semua transaksi dengan detail
- Export **Data Absensi** — riwayat absensi semua karyawan
- Export **Data Gaji** — slip gaji semua periode

---

## 🔐 Keamanan

- Password di-hash menggunakan `bcrypt`
- CSRF protection pada semua form
- Role-based access control (Middleware `RoleMiddleware`)
- Session-based authentication
- Aktivitas sensitif dicatat di `activity_logs`
- IT tidak bisa menghapus akun Bos
- IT tidak bisa menonaktifkan akun sendiri

---

## 📱 Akses dari HP / Device Lain

Untuk akses dari HP di jaringan WiFi yang sama:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

Akses dari HP: `http://[IP_LAPTOP]:8000`

Cek IP laptop:
```bash
ipconfig  # Windows
```

---

## 🐛 Troubleshooting

### Error: `View not found`
```bash
php artisan view:clear
```

### Error: `Route not defined`
```bash
php artisan route:clear
php artisan cache:clear
```

### Error: `CSRF token mismatch`
Refresh halaman (F5). Session expired.

### Error: `Table not found`
```bash
php artisan migrate
```

### Kamera tidak bisa diakses
Gunakan `http://localhost:8000` bukan domain `.test`. Browser memerlukan HTTPS atau localhost untuk akses kamera.

### GD Extension tidak aktif (untuk QR Code)
Buka `php.ini`, cari `;extension=gd`, hapus tanda `;`, restart server.

---

## 📝 Catatan Pengembangan

### Fitur yang Masih Simulasi
- **OTP SMS** — kode OTP ditampilkan di layar (simulasi). Untuk production perlu integrasi SMS Gateway (Twilio, Zenziva, dll.)
- **Verifikasi Wajah** — sistem capture foto sebagai bukti absen, bukan face recognition AI

### Pengembangan Selanjutnya (Roadmap)
- [ ] Integrasi payment gateway (Midtrans/Xendit)
- [ ] Notifikasi real-time dengan WebSocket (Pusher/Soketi)
- [ ] Face recognition dengan face-api.js
- [ ] Printer tiket otomatis untuk dapur/barista
- [ ] Loyalty point pelanggan
- [ ] Sistem promo & voucher diskon
- [ ] Manajemen meja visual (layout toko)
- [ ] Mobile app (React Native / Flutter)



**Universitas Malikussaleh**
Program Studi Teknik Informatika
Mata Kuliah: Pemrograman Web Lanjut
Tahun: 2026

---

## 📄 Lisensi

Proyek ini dibuat untuk keperluan akademik (tugas kuliah).

---

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) — PHP Framework
- [barryvdh/laravel-dompdf](https://github.com/barryvdh/laravel-dompdf) — PDF Generation
- [simplesoftwareio/simple-qrcode](https://github.com/SimpleSoftwareIO/simple-qrcode) — QR Code Generation
- [Google Fonts](https://fonts.google.com) — Playfair Display & DM Sans
- [html5-qrcode](https://github.com/mebjas/html5-qrcode) — Browser QR Scanner

---

<div align="center">
  <p>☕ Dibuat dengan ❤️ untuk BrewNest Coffee — Lhokseumawe, Aceh</p>
  <p><strong>BrewNest Coffee</strong> — Setiap tegukan adalah cerita yang baru.</p>
</div>
