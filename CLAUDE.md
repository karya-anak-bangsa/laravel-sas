# Sistem Akademik Sekolah (SAS) — Yayasan Puspita Bangsa Ciputat

Aplikasi Laravel 13 untuk **SMP Puspita Bangsa** dan **SMK Puspita Bangsa**. Modul utama: company profile, administrasi tenaga pendidik (prota, prosem, RPP, silabus, dll.), penerimaan murid baru (PPDB) online, dan rapor digital. Sistem harus siap diperluas dengan modul lain (absensi, ujian berbasis komputer, pembayaran SPP, dll.) tanpa merombak modul yang sudah ada.

- Repo: https://github.com/karya-anak-bangsa/laravel-sas
- Production: https://sas.karyaanakbangsa.co.id
- Developer tunggal (fullstack). Semua commit langsung ke `main`.

## Aturan kerja untuk Claude

- Komunikasi, teks antarmuka, pesan validasi, dan pesan commit menggunakan **Bahasa Indonesia**.
- Kerjakan **bertahap**: satu fitur/perubahan yang utuh per commit. Jangan mengubah modul lain di luar lingkup tugas.
- Jangan mengarang aturan domain. Jika sesuatu ditandai **TBD** di file ini atau belum disebut, tanyakan dulu.
- Sebelum commit: `vendor/bin/pint --dirty` dan `php artisan test` harus lolos.
- Format commit: Conventional Commits, mis. `feat(murid): tambah form data orang tua/wali`, `fix(ppdb): ...`.
- Jika keputusan baru disepakati, perbarui file ini pada commit yang sama.

## Istilah wajib

| Gunakan | Jangan gunakan |
|---|---|
| Tenaga Pendidik | guru |
| Tenaga Kependidikan | tata usaha, TU |
| Murid | siswa |
| Orang Tua/Wali | wali siswa |
| Konsentrasi Keahlian | jurusan (kecuali nama peran "Ketua Jurusan") |
| Rombel (rombongan belajar) | kelas (untuk entitas data) |

Berlaku untuk UI, nama tabel/kolom, nama model, dan dokumentasi.

## Profil yayasan

- Yayasan memiliki tiga satuan pendidikan: MTs, SMP, SMK. **Cakupan SAS: SMP dan SMK.** MTs di luar cakupan, tetapi struktur data jangan menutup kemungkinan menambahkannya.
- **SMP**: tingkat VII–IX, 1 rombel per tingkat (3 rombel).
- **SMK**: tingkat X–XII, 5 konsentrasi keahlian, **6 rombel per tingkat** karena Perhotelan memiliki 2 rombel (18 rombel).

Spektrum keahlian SMK (Kepmendikbudristek No. 244/M/2024, Kurikulum Merdeka):

| Bidang Keahlian | Program Keahlian | Konsentrasi Keahlian | Singkatan | Rombel/tingkat |
|---|---|---|---|---|
| Pariwisata | Perhotelan | Perhotelan | PH | 2 |
| Teknologi Informasi | Pengembangan Perangkat Lunak dan Gim | Rekayasa Perangkat Lunak | RPL | 1 |
| Bisnis dan Manajemen | Manajemen Perkantoran dan Layanan Bisnis | Manajemen Perkantoran | MP | 1 |
| Bisnis dan Manajemen | Pemasaran | Bisnis Retail | BR | 1 |
| Bisnis dan Manajemen | Akuntansi dan Keuangan Lembaga | Akuntansi | AK | 1 |

Hierarki disimpan sebagai tiga tabel: bidang → program → konsentrasi keahlian.

## Pengguna dan peran

**Sistem hanya untuk internal sekolah.** Murid dan orang tua/wali **tidak memiliki akun dan tidak login**. Halaman publik (company profile, formulir PPDB) diakses tanpa login.

Peran: Administrator, Kepala Sekolah, Wakil Kepala Sekolah, Ketua Jurusan (khusus SMK), Wali Kelas, Tenaga Pendidik, Tenaga Kependidikan.

Akun:
- `tb_pengguna` adalah **satu-satunya tabel akun** untuk semua peran di atas. Tabel ini hanya berisi data login (username/email, password, status aktif), bukan data pribadi.
- Data pribadi tetap di `tb_tenaga_pendidik` / `tb_tenaga_kependidikan`. Keduanya memiliki `id_pengguna` nullable, sehingga data bisa diinput dulu sebelum akunnya dibuat.
- Administrator boleh hanya memiliki akun, tanpa data tenaga pendidik/kependidikan.
- Kepala Sekolah, Wakil Kepala Sekolah, Ketua Jurusan, dan Wali Kelas adalah tenaga pendidik dengan tugas tambahan: satu akun, satu data di `tb_tenaga_pendidik`, ditambah penugasan.

Peran:
- **Satu pengguna dapat memiliki banyak peran.** Contoh: sebagian tenaga pendidik juga wali kelas, sebagian tidak.
- Peran yang terikat konteks dicatat sebagai **penugasan per tahun ajaran**, bukan atribut tetap orang:
  - Wali Kelas → rombel + tahun ajaran
  - Ketua Jurusan → konsentrasi keahlian + tahun ajaran
  - Kepala/Wakil Kepala Sekolah → satuan pendidikan + tahun ajaran
- Otorisasi memakai Gate/Policy Laravel. Tabel peran mengikuti konvensi penamaan proyek (`tb_peran`, `tb_pengguna_peran`). Jangan memasang paket RBAC yang memaksakan nama tabel/PK sendiri tanpa persetujuan.
- Implementasi: kode peran adalah enum `App\Enums\KodePeran` (disimpan di `tb_peran.kode`; baris `tb_peran` diisi oleh migration-nya, jadi menambah peran = enum + migration baru). `Pengguna::memilikiPeran(KodePeran ...)` untuk pengecekan. Gate di `AppServiceProvider`: `Gate::before` meloloskan Administrator aktif; `akses-admin` = akun aktif dengan ≥1 peran.
- Tabel penugasan per tahun ajaran dibuat setelah master data yang dirujuknya (tahun ajaran, rombel, konsentrasi keahlian, satuan pendidikan, tenaga pendidik) tersedia di tahap 1.

## Master data

### Tenaga Pendidik
- Kolom: nama lengkap, NUPTK, tempat lahir, tanggal lahir, pendidikan terakhir, status, TMT GTT, TMT GTY, masa kerja, satuan pendidikan.
- Status: **GTT** (Guru Tidak Tetap) atau **GTY** (Guru Tetap Yayasan). Tidak ada status honorer.
- **Masa kerja diisi manual**, tidak dihitung dari TMT (`masa_kerja_tahun`, `masa_kerja_bulan`).
- Terhubung opsional ke akun pengguna (`id_pengguna` nullable).

### Tenaga Kependidikan — TBD
- Status dan cara pencatatan **belum diketahui**. Sementara kolom mengikuti data KTP: NIK, nama lengkap, tempat lahir, tanggal lahir, jenis kelamin, alamat (RT/RW, kelurahan/desa, kecamatan), agama.
- Terhubung opsional ke akun pengguna (`id_pengguna` nullable).
- Jangan menambah status atau aturan lain sampai ada informasi dari yayasan.

### Murid
- Identitas: nama lengkap, jenis kelamin, NISN, **NIK**, **Nomor KK**, no. seri ijazah, no. seri SKHUS, tempat lahir, tanggal lahir, agama, berkebutuhan khusus, alamat, moda transportasi, tempat tinggal, nomor HP (WA), email, no. KPS/PKH, nomor KIP.
- Berkebutuhan khusus boleh **lebih dari satu** (tabel relasi), sehingga kebutuhan ganda tetap tercatat.

### Orang Tua/Wali
- **Tabel terpisah dari murid**, dihubungkan lewat tabel relasi dengan kolom `hubungan` (ayah kandung / ibu kandung / wali). Murid bersaudara dapat memakai data orang tua yang sama.
- Kolom: nama, pendidikan, pekerjaan, penghasilan, nomor HP (WA).

### PPDB
- Formulir terdiri dari dua lembar: (1) lembar peserta didik (identitas murid + ayah, ibu, wali), (2) lembar pernyataan kesanggupan murid dan orang tua/wali.
- Lembar (2) berupa persetujuan, bukan isian. Simpan siapa yang menyetujui dan waktu persetujuannya.
- **Tidak meminta nilai rapor dan tidak ada seleksi.** Semua pendaftar diasumsikan diterima.

### Nilai referensi (PHP backed enum, simpan kodenya)
- **Jenis kelamin**: L, P
- **Agama**: Islam, Katolik, Kristen, Hindu, Buddha, Konghucu
- **Berkebutuhan khusus** (referensi Kemendikdasmen/Dapodik): Tidak Ada; A Tuna Netra; B Tuna Rungu; C Tuna Grahita Ringan; C1 Tuna Grahita Sedang; D Tuna Daksa Ringan; D1 Tuna Daksa Sedang; E Tuna Laras; F Tuna Wicara; H Hiperaktif; I Cerdas Istimewa; J Bakat Istimewa; K Kesulitan Belajar; N Narkoba; O Indigo; P Down Syndrome; Q Autis; Lainnya
- **Moda transportasi**: jalan kaki, kendaraan pribadi, kendaraan umum, jemputan sekolah
- **Tempat tinggal**: bersama orang tua, bersama wali, kos, asrama, panti asuhan, lainnya
- **Status tenaga pendidik**: GTT, GTY
- **Pendidikan, pekerjaan, penghasilan orang tua/wali**: TBD (usulan: ikuti referensi Dapodik)

## Konvensi kode

Ikuti seluruh konvensi Laravel, **kecuali penamaan tabel dan kolom**:

- Tabel domain: `tb_` + nama entitas, snake_case, bentuk tunggal, Bahasa Indonesia: `tb_murid`, `tb_tenaga_pendidik`, `tb_rombel`.
- Primary key: `id_` + nama entitas (tanpa `tb_`): `tb_murid.id_murid`.
- Foreign key memakai nama yang sama dengan PK yang dirujuk: `tb_rombel.id_tahun_ajaran`.
- Tabel relasi: `tb_<entitas_a>_<entitas_b>`, mis. `tb_murid_orang_tua`.
- Tabel `users` diganti `tb_pengguna` (PK `id_pengguna`). Sesuaikan `config/auth.php`, model, factory, dan seeder.
- Tabel infrastruktur framework (`migrations`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `sessions`, `password_reset_tokens`) **tetap memakai nama bawaan**.
- `created_at`/`updated_at` tetap bawaan Laravel. Master data memakai soft delete (`deleted_at`).

Konsekuensi di Eloquent (wajib):
- Setiap model mendefinisikan `protected $table` dan `protected $primaryKey`.
- Setiap relasi menyebutkan foreign key dan owner/local key secara eksplisit, karena tebakan bawaan Laravel (`murid_id`) tidak berlaku. Contoh: `$this->belongsTo(Rombel::class, 'id_rombel', 'id_rombel')`.
- Nama model, enum, dan class domain dalam Bahasa Indonesia, StudlyCase: `Murid`, `TenagaPendidik`, `OrangTuaWali`.

Struktur kode (agar modul baru mudah ditambahkan):
- Kelompokkan per modul di dalam direktori standar Laravel: `app/Http/Controllers/Admin/<Modul>/`, `app/Http/Requests/<Modul>/`, `app/Actions/<Modul>/`, `app/Policies/`, `app/Enums/`, `resources/views/admin/<modul>/`.
- Route per modul di `routes/admin/<modul>.php` dan `routes/publik/<modul>.php`, di-include dari `routes/web.php`.
- Controller tipis: validasi di Form Request, otorisasi di Policy, logika bisnis di Action. Operasi multi-tabel dibungkus `DB::transaction`.
- Hindari query N+1 (eager loading) dan beri index pada setiap foreign key serta kolom pencarian (NISN, NIK, NUPTK).

Data pribadi (NIK, No. KK, NUPTK, data orang tua):
- Simpan sebagai string dengan panjang tetap dan validasi digit (NIK/No. KK 16 digit, NISN 10 digit, NUPTK 16 digit).
- Akses dibatasi Policy. Jangan pernah menulisnya ke log.

## Frontend: backend dan publik dipisah

- **Area admin (`/admin`)**: template **Gentelella v4** (paket npm `gentelella`). Catatan: v4 **tidak memakai Bootstrap maupun jQuery** (vanilla JS + SCSS, grid `.row/.col-*` sendiri); jangan memasang Bootstrap. Entry Vite `resources/css/admin.css` (penyesuaian proyek) + `resources/js/admin.js` (mengimpor SCSS Gentelella dan `mountShell`), layout `resources/views/layouts/admin.blade.php` (halaman berlogin) dan `layouts/admin-tamu.blade.php` (halaman masuk).
  - Sidebar/topbar/footer dirender di Blade (`layouts/partials/admin-sidebar.blade.php`), bukan oleh JS Gentelella. Menu modul baru ditambahkan di partial itu dan dibungkus `@can`.
  - Entry demo Gentelella (`gentelella` / `main-v4.js`: command palette, data contoh, form palsu) **tidak** dimuat. Modul JS lain diimpor per kebutuhan dari `gentelella/v4/*`.
  - Referensi markup komponen: halaman `node_modules/gentelella/production/*.html`.
- **Area publik (company profile, PPDB)**: **Tailwind CSS v4**. Entry Vite `resources/css/app.css` + `resources/js/app.js`, layout `resources/views/layouts/publik.blade.php`.
- Kedua bundel **tidak boleh dimuat bersama** dalam satu layout. Jangan memakai class Tailwind di view admin, dan jangan memakai class Gentelella di view publik.
- Font di-host sendiri lewat opsi `fonts` laravel-vite-plugin dan direktif `@fonts`: Inter (admin), Instrument Sans (publik).
- Library komponen Tailwind (daisyUI / shadcn / Flowbite / Preline / HyperUI): **TBD**. Jangan memasang salah satunya sebelum diputuskan.

## Kualitas — ISO/IEC 25010

Setiap fitur memperhatikan karakteristik berikut:
- **Kesesuaian fungsional**: feature test untuk setiap alur utama (CRUD, PPDB, otorisasi per peran).
- **Efisiensi kinerja**: eager loading, index, paginasi pada setiap daftar.
- **Kompatibilitas**: modul baru terhubung lewat master data yang sama (murid, tenaga pendidik, rombel, tahun ajaran), bukan dengan menduplikasinya.
- **Kemampuan interaksi (usability)**: UI Bahasa Indonesia, pesan validasi jelas, form PPDB nyaman di ponsel.
- **Keandalan**: transaksi database, soft delete, backup database production.
- **Keamanan**: Policy di setiap aksi, Form Request validation, proteksi CSRF, data pribadi tidak masuk log.
- **Kemudahan pemeliharaan**: Pint, konvensi di file ini, Action kecil yang bisa diuji.
- **Fleksibilitas/portabilitas**: semua konfigurasi lewat `.env`, tanpa path atau URL yang di-hardcode.

## Lingkungan

- Lokal: Laragon 8.4.0 (Apache 2.4.62, PHP 8.3.28, MySQL 8.0.40, Node 24.12, Git 2.47.1, Composer 2.10.1). Editor: VS Code + Claude Code.
- `.env` lokal/production: `DB_CONNECTION=mysql`, `DB_DATABASE=sas`, `APP_LOCALE=id`, `APP_FAKER_LOCALE=id_ID`, `APP_TIMEZONE=Asia/Jakarta` (lihat `.env.example`).
- Zona waktu aplikasi: `Asia/Jakarta` (`config/app.php`, dapat diubah lewat `APP_TIMEZONE`).
- Terjemahan Bahasa Indonesia di `lang/id/` (auth, pagination, passwords, validation). Nama kolom khusus modul diatur di `attributes()` Form Request.
- Test: PHPUnit dengan database MySQL khusus `sas_testing` (diatur di `phpunit.xml`), agar perilakunya sama dengan production. Database ini harus dibuat dulu di MySQL lokal.
- Perintah:
  - `composer dev`: server + Vite
  - `php artisan test`: jalankan test
  - `vendor/bin/pint --dirty`: format kode
  - `npm run build`: build aset
  - `php artisan pengguna:buat-administrator`: buat akun Administrator (instalasi awal/production)
  - `php artisan migrate:fresh --seed`: database lokal dengan akun contoh `admin` / `password` (seeder tidak berjalan di production)
- Cara deploy ke production: **TBD**.

## Autentikasi

- Halaman masuk `/admin/masuk` (nama pengguna **atau** email + kata sandi), keluar lewat POST `/admin/keluar`. Tidak ada pendaftaran mandiri; akun dibuat oleh Administrator.
- Hanya akun `aktif` yang dapat masuk; percobaan gagal dibatasi 5 kali per login + IP (`App\Actions\Autentikasi\AutentikasiPengguna`).
- Seluruh route admin selain masuk/keluar memakai middleware `auth` + `can:akses-admin` (lihat `routes/web.php`).
- Lupa kata sandi: sementara diatur ulang oleh Administrator (belum ada reset via email).

## Tahapan pengembangan (usulan urutan)

0. **Fondasi**: konfigurasi `.env`/locale/timezone, `tb_pengguna`, autentikasi, peran dan penugasan, layout admin (Gentelella) dan publik (Tailwind).
1. **Master data**: satuan pendidikan, spektrum keahlian, tahun ajaran, rombel, tenaga pendidik, tenaga kependidikan, murid, orang tua/wali.
2. **Company profile** (publik).
3. **PPDB online**: formulir dua lembar, data pendaftar menjadi data murid.
4. **Administrasi tenaga pendidik**: prota, prosem, RPP, silabus, dll.
5. **Rapor digital**.
6. Modul lanjutan sesuai kebutuhan yayasan: absensi, CBT, SPP.

## Belum diputuskan (TBD)

- Status dan kolom data Tenaga Kependidikan.
- Referensi pendidikan, pekerjaan, dan penghasilan orang tua/wali.
- Library komponen Tailwind untuk area publik.
- Jenis dan format dokumen administrasi tenaga pendidik.
- Format dan aturan penilaian rapor.
- Cara rapor digital sampai ke murid/orang tua, karena mereka tidak login (mis. PDF dicetak atau dikirim oleh wali kelas).
- Konten company profile.
- Cara deploy ke production.

<laravel-boost-guidelines>
# Laravel Application

This repository contains a Laravel application. Complete the following setup before working on the user's request.

## Prerequisites

Verify that PHP and Composer are available:

```sh
php -v
composer -V
```

If either command is unavailable, detect the user's operating system and install the prerequisites with the appropriate command:

macOS:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/mac/8.5)"
```

Windows PowerShell:

```powershell
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.5'))
```

Linux:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.5)"
```

After installation, ask the user to restart their terminal. If the agent needs the restarted shell to continue, ask the user to reopen their terminal and rerun their original prompt.

## Agent Setup

Install Laravel Boost from the application root before making application changes:

```sh
composer require laravel/boost --dev
php artisan boost:install
```

Boost replaces these bootstrap instructions with guidelines tailored to the application. After installation, read `AGENTS.md` again and continue with the user's original request using the generated guidelines.
</laravel-boost-guidelines>
