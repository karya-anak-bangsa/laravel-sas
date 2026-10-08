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

Hierarki disimpan sebagai tiga tabel: bidang → program → konsentrasi keahlian (`tb_bidang_keahlian`, `tb_program_keahlian`, `tb_konsentrasi_keahlian` dengan `singkatan` unik). Data di atas diisi oleh `DataAwalSeeder`.

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
- Implementasi: kode peran adalah enum `App\Enums\KodePeran` (disimpan di `tb_peran.kode`; baris `tb_peran` diisi oleh migration-nya, jadi menambah peran = enum + migration baru). Gate di `AppServiceProvider`: `Gate::before` meloloskan Administrator aktif; `akses-admin` = akun aktif dengan ≥1 peran efektif.
- **Peran tetap vs kontekstual** (keputusan): `tb_pengguna_peran` hanya untuk peran tetap (Administrator, Tenaga Pendidik, Tenaga Kependidikan — `KodePeran::daftarTetap()`). Peran kontekstual (Kepala/Wakil Kepala Sekolah, Ketua Jurusan, Wali Kelas — `KodePeran::daftarKontekstual()`) **berasal dari `tb_penugasan` pada tahun ajaran aktif** lewat data tenaga pendidik yang tertaut ke akun. Saat semester aktif pindah tahun ajaran, peran kontekstual ikut berganti.
- Selalu cek peran dengan `Pengguna::memilikiPeran(KodePeran ...)` / `kodePeran()` (gabungan peran tetap + penugasan aktif), jangan membaca relasi `peran` langsung.
- `tb_penugasan`: tenaga pendidik + tahun ajaran + `id_peran` + konteks: Wali Kelas → `id_rombel` (satu wali per rombel), Ketua Jurusan → `id_konsentrasi_keahlian` (satu per konsentrasi per tahun ajaran), Kepala Sekolah → `id_satuan_pendidikan` (satu per satuan per tahun ajaran), Wakil Kepala Sekolah → `id_satuan_pendidikan` + `bidang` teks bebas (boleh beberapa).

## Master data

**Pengelola: hanya Administrator** yang boleh menambah/mengubah/menghapus master data. Peran lain akan diberi akses baca per modul bila dibutuhkan (override method di Policy-nya).

### Satuan Pendidikan
- `tb_satuan_pendidikan`: nama, `bentuk_pendidikan` (enum `BentukPendidikan`: smp, smk, mts — istilah Dapodik), NPSN (8 digit, opsional), alamat.
- Tingkat per bentuk pendidikan: `BentukPendidikan::tingkat()` (SMP/MTs: VII–IX, SMK: X–XII); enum `Tingkat` disimpan sebagai angka 7–12.
- Data awal (SMP & SMK Puspita Bangsa) diisi oleh `DataAwalSeeder`.

### Tahun Ajaran dan Semester
- `tb_tahun_ajaran` (nama `TTTT/TTTT`, tahun kedua = tahun pertama + 1) memiliki tepat dua `tb_semester` (enum `JenisSemester`: ganjil, genap) dengan tanggal mulai/selesai; keduanya disimpan bersama lewat Action `SimpanTahunAjaran`.
- **Hanya satu semester aktif** (`tb_semester.aktif`), diatur lewat Action `AktifkanSemester`. Tahun ajaran aktif = tahun ajaran dari semester aktif (tidak ada kolom aktif di tahun ajaran). Ambil dengan `Semester::query()->aktif()`.
- Tahun ajaran yang semesternya sedang aktif tidak dapat dihapus.

### Rombel
- `tb_rombel`: tahun ajaran, satuan pendidikan, tingkat (sesuai `BentukPendidikan::tingkat()`), konsentrasi keahlian (**wajib untuk SMK, dilarang untuk SMP/MTs**), dan **nama bebas** diisi Administrator (mis. "X PH 1", "VII"), unik per satuan pendidikan + tahun ajaran.
- Daftar rombel secara bawaan disaring ke tahun ajaran aktif.
- Satuan pendidikan, tahun ajaran, dan konsentrasi keahlian yang masih memiliki rombel tidak dapat dihapus.
- **Anggota rombel** (`tb_rombel_murid`): satu murid hanya satu rombel per tahun ajaran (divalidasi di `AnggotaRombelRequest`). Rombel aktif murid: `Murid::rombelAktif()`. Rombel yang masih memiliki anggota tidak dapat dihapus. Dikelola di halaman Anggota tiap rombel.

### Tenaga Pendidik
- Kolom: nama lengkap, NUPTK, tempat lahir, tanggal lahir, pendidikan terakhir, status, TMT GTT, TMT GTY, masa kerja, satuan pendidikan.
- Status: **GTT** (Guru Tidak Tetap) atau **GTY** (Guru Tetap Yayasan). Tidak ada status honorer.
- **Masa kerja diisi manual**, tidak dihitung dari TMT (`masa_kerja_tahun`, `masa_kerja_bulan`).
- Terhubung opsional ke akun pengguna (`id_pengguna` nullable).
- **NUPTK opsional**; jika diisi 16 digit dan unik. Wajib: nama lengkap, satuan pendidikan, status, pendidikan terakhir, masa kerja.
- **Pendidikan terakhir** = enum `JenjangPendidikan`: SMA/Sederajat, D1, D2, D3, D4, S1, S2, S3.

### Tenaga Kependidikan — TBD
- Status dan cara pencatatan **belum diketahui**. Sementara kolom mengikuti data KTP: NIK, nama lengkap, tempat lahir, tanggal lahir, jenis kelamin, alamat (RT/RW, kelurahan/desa, kecamatan), agama.
- Terhubung opsional ke akun pengguna (`id_pengguna` nullable).
- Jangan menambah status atau aturan lain sampai ada informasi dari yayasan.
- Implementasi sementara (`tb_tenaga_kependidikan`): hanya nama lengkap dan jenis kelamin yang wajib; NIK opsional (16 digit, unik). Alamat: `alamat`, `rt`, `rw`, `kelurahan_desa`, `kecamatan`. Enum `JenisKelamin` (L/P) dan `Agama`.

### Murid
- Identitas: nama lengkap, jenis kelamin, NISN, **NIK**, **Nomor KK**, no. seri ijazah, no. seri SKHUS, tempat lahir, tanggal lahir, agama, berkebutuhan khusus, alamat, moda transportasi, tempat tinggal, nomor HP (WA), email, no. KPS/PKH, nomor KIP.
- Berkebutuhan khusus boleh **lebih dari satu** (tabel relasi), sehingga kebutuhan ganda tetap tercatat.
- Implementasi: `tb_murid` + `tb_murid_berkebutuhan_khusus` (kode enum `BerkebutuhanKhusus`; **tanpa baris = "Tidak Ada"**). Alamat rinci ala Dapodik: `alamat_jalan`, `rt`, `rw`, `dusun`, `kelurahan_desa`, `kecamatan`, `kode_pos`.
- Isian wajib di admin **minimal**: nama lengkap, jenis kelamin, tanggal lahir. NISN (10 digit) dan NIK (16 digit) opsional tetapi unik bila diisi; No. KK (16 digit) boleh sama antar-murid bersaudara. Nomor HP dinormalkan (tanpa spasi/tanda hubung) dan divalidasi sebagai nomor ponsel Indonesia. Aturan wajib PPDB ditentukan terpisah di tahap 3.

### Orang Tua/Wali
- **Tabel terpisah dari murid**, dihubungkan lewat tabel relasi dengan kolom `hubungan` (ayah kandung / ibu kandung / wali). Murid bersaudara dapat memakai data orang tua yang sama.
- Kolom: nama, pendidikan, pekerjaan, penghasilan, nomor HP (WA).
- Implementasi: `tb_orang_tua_wali` + `tb_murid_orang_tua` (pivot `MuridOrangTua`, kolom `hubungan` enum `HubunganOrangTua`). Satu murid paling banyak satu baris per hubungan. Dikelola dari halaman ubah murid (tautkan data yang ada / tambah baru langsung tertaut) dan menu Orang Tua/Wali. Orang tua/wali yang masih tertaut ke murid tidak dapat dihapus.
- Nomor HP murid dan orang tua/wali divalidasi dengan Rule `App\Rules\NomorPonselIndonesia` (normalkan dulu dengan `NomorPonselIndonesia::normalkan()`).

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
- **Pendidikan, pekerjaan, penghasilan orang tua/wali**: mengikuti referensi Dapodik — enum `PendidikanOrangTua`, `PekerjaanOrangTua`, `PenghasilanOrangTua`

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

Pola CRUD area admin (ikuti modul Satuan Pendidikan sebagai contoh):
- Route `Route::resource(...)->except('show')` dengan nama parameter camelCase (`satuanPendidikan`); otorisasi `Gate::authorize()` di controller dan `authorize()` di Form Request (satu Request untuk store & update).
- Policy master data meng-extend `App\Policies\KebijakanMasterData` (semua aksi ditolak; Administrator lolos lewat `Gate::before`).
- Komponen Blade `resources/views/components/admin/`: `header-halaman`, `input`, `textarea`, `select`, `tombol-hapus` (konfirmasi via `data-konfirmasi`), `kosong`. Paginasi: `->links('layouts.partials.admin-paginasi')`. Pesan sukses `session('status')`, pesan gagal `session('galat')`.
- Menu baru ditambahkan ke array di `layouts/partials/admin-sidebar.blade.php` (otomatis disaring dengan Policy `viewAny`).
- Validasi unik pada tabel ber-soft-delete memakai `Rule::unique(...)->withoutTrashed()` dan **tanpa** unique index di database (index biasa), agar data yang sudah dihapus tidak menghalangi isian baru.
- Data yang masih dirujuk data lain tidak boleh dihapus: Action `Hapus<Entitas>` melempar `App\Exceptions\DataMasihDipakai`, yang otomatis dirender sebagai redirect kembali dengan `session('galat')` (lihat `bootstrap/app.php`).
- Data awal yang dibutuhkan di semua lingkungan masuk `DataAwalSeeder` (idempoten, aman di production); data contoh hanya di `DatabaseSeeder` (non-production).

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
- Laravel Boost terpasang (dev): MCP server `laravel-boost` (`.mcp.json`), skills di `.claude/skills/`, konfigurasi `boost.json`. Blok `<laravel-boost-guidelines>` di akhir file ini dibuat ulang oleh `php artisan boost:update` — jangan menulis aturan proyek di dalam blok itu. Jika bertentangan, aturan proyek di atas yang berlaku.
- Test: PHPUnit dengan database MySQL khusus `sas_testing` (diatur di `phpunit.xml`), agar perilakunya sama dengan production. Database ini harus dibuat dulu di MySQL lokal.
- Perintah:
  - `composer dev`: server + Vite
  - `php artisan test`: jalankan test
  - `vendor/bin/pint --dirty`: format kode
  - `npm run build`: build aset
  - `php artisan pengguna:buat-administrator`: buat akun Administrator (instalasi awal/production)
  - `php artisan migrate:fresh --seed`: database lokal dengan akun Administrator `aryajaya.alamsyah` / `12341234` (seeder tidak berjalan di production)
- Cara deploy ke production: **TBD**.

## Autentikasi

- Halaman masuk `/admin/masuk` (nama pengguna **atau** email + kata sandi), keluar lewat POST `/admin/keluar`. Tidak ada pendaftaran mandiri; akun dibuat oleh Administrator.
- Hanya akun `aktif` yang dapat masuk; percobaan gagal dibatasi 5 kali per login + IP (`App\Actions\Autentikasi\AutentikasiPengguna`).
- Seluruh route admin selain masuk/keluar memakai middleware `auth` + `can:akses-admin` (lihat `routes/web.php`).
- Lupa kata sandi: sementara diatur ulang oleh Administrator (belum ada reset via email).
- Akun dikelola di menu **Pengguna** (`/admin/pengguna`, khusus Administrator): nama pengguna (huruf, angka, titik, `-`, `_` — `Pengguna::POLA_NAMA_PENGGUNA`), email opsional, kata sandi, aktif, peran tetap, dan tautan ke data tenaga pendidik yang belum punya akun. Nama pengguna/email unik terhadap semua akun termasuk yang sudah dihapus.
- Administrator tidak dapat menghapus, menonaktifkan, atau mencabut peran Administrator dari akunnya sendiri.

## Tahapan pengembangan (usulan urutan)

0. **Fondasi** ✅: konfigurasi `.env`/locale/timezone, `tb_pengguna`, autentikasi, peran, layout admin (Gentelella) dan publik (Tailwind).
1. **Master data** ✅: satuan pendidikan, spektrum keahlian, tahun ajaran + semester, rombel + anggota rombel, tenaga pendidik, penugasan, tenaga kependidikan (kolom sementara), murid, orang tua/wali, menu Pengguna.
2. **Company profile** (publik).
3. **PPDB online**: formulir dua lembar, data pendaftar menjadi data murid.
4. **Administrasi tenaga pendidik**: prota, prosem, RPP, silabus, dll.
5. **Rapor digital**.
6. Modul lanjutan sesuai kebutuhan yayasan: absensi, CBT, SPP.

## Belum diputuskan (TBD)

- Status dan kolom data Tenaga Kependidikan.
- Library komponen Tailwind untuk area publik.
- Jenis dan format dokumen administrasi tenaga pendidik.
- Format dan aturan penilaian rapor.
- Cara rapor digital sampai ke murid/orang tua, karena mereka tidak login (mis. PDF dicetak atau dikirim oleh wali kelas).
- Konten company profile.
- Cara deploy ke production.

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

## Foundational Context

This application is a Laravel application running on PHP 8.3. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If a frontend change doesn't show in the UI or you get a "Unable to locate file in Vite manifest" error, run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists, including path-scoped framework guidelines under `.ai/rules/boost`. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.
- Activate the `deploying-to-cloud` skill whenever deploying to Laravel Cloud, configuring Cloud environments or resources, using the Cloud CLI, or troubleshooting Cloud deployments.

=== tests rules ===

# Test Enforcement

- Add or update tests for behavior and logic changes when a test provides meaningful regression coverage.
- Pure copy, styling, and layout-only changes do not require new or updated tests.
- When test coverage applies, run the affected tests and ensure they pass.
- Test the changed behavior and its important failure modes, but do not add tests beyond them.
- Read the `testing-best-practices` skill before writing tests.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This project uses PHPUnit. Create tests with `php artisan make:test --phpunit {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/phpunit` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.

</laravel-boost-guidelines>
