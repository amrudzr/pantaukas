# Pantaukas

Pantaukas adalah aplikasi **pencatatan iuran & kas** berbasis web untuk skala kecil  
(RT / RW, komunitas, koperasi sederhana).

| Fitur Utama | Keterangan |
|-------------|-------------------|
| **Auth**    | Login & register (nomor telepon + password) |
| **Dashboard** | Ringkasan total kas, total iuran, jumlah anggota (aktif / pasif / keluar) |
| **Anggota** | CRUD data warga (+ status) |
| **Iuran & Kas** | Catat pemasukan / pengeluaran, laporan bulanan |
| **Laporan** | Unduh PDF / bagikan link laporan (bulan + tahun) |
| **Contoh Modul (Example)** | Tabel dummy + create / edit / delete untuk demo arsitektur |
| **Routing Regex** | Routing super‑ringan di `app/routes.php` + flag middleware `requires_auth` |
| **Blade‑like Layout** | Header, sidebar, breadcrumb, footer terpisah – 100 % Bootstrap (tanpa CSS kustom) |

---

## Minimum Requirements
| Software | Versi minimum | Catatan |
|----------|---------------|---------|
| PHP      | 8.0           | Dibangun & dites di PHP 8.2 |
| MySQL    | 5.7 / 8       | atau MariaDB setara |
| Composer | opsional      | Hanya untuk autoload, **tidak ada dependensi eksternal** |
| Node/NPM | _tidak perlu_ | Bootstrap & Icons via CDN |

---

## Instalasi Cepat

#### 1. Clone repo
```cmd
git clone https://github.com/amrudzr/pantaukas.git
cd pantaukas
```
#### 2. Konfigurasi file database
```cmd
cp config/database.example.php
config/database.php
```
##### lalu sesuaikan host, user, password, dbname

#### 3. Jalankan setup tabel dummy (dev only)
```cmd
php -S localhost:8000 -t public
```
- Akses `http://localhost:8000/db/init`  ➜ membuat tabel & dummy seed

---

## Menjalankan 🟢
```cmd
# Pastikan berada di root proyek
php -S localhost:8000 -t public
```

- Seluruh request akan masuk ke `public/index.php`, diteruskan ke `app/routes.php`, lalu controller / closure.
- Login via `http://localhost:8000/login` (user & password dummy dibuat di step 3).

## Cara Kerja Singkat
##### 1. public/index.php
└─ memanggil dispatchRequest() (di app/routes.php).

##### 2. routes.php

- Pencocokan regex URI → handler
- Middleware sederhana requires_auth (cek $_SESSION['user_id'], redirect ke /login).

##### 3. Controller

- Dummy: hanya array; pada implementasi nyata tinggal ganti dengan query PDO (// TODO: sudah disiapkan).

##### 4. View

- 100 % Bootstrap 5 (CDN), no custom CSS → tampilan konsisten & ringan.

## Struktur Folder
```
app/
 ├─ controllers/      # AuthController, ExampleController (dummy CRUD), ...
 ├─ helpers/          # helpers.php (fungsi kecil flash, redirect, dll)
 ├─ views/
 │   ├─ layout/       # header.php, sidebar.php, app.php, ...
 │   ├─ auth/         # login.php, register.php
 │   ├─ dashboard.php
 │   ├─ example/      # index.php, create.php, edit.php
 │   └─ errors/404.php
 └─ routes.php        # regex router + middleware flag
 
config/
 └─ database.php      # kredensial PDO

database/
 └─ setup.sql         # schema & seed (dieksekusi via /db/init)

public/
 ├─ index.php         # front‑controller
 └─ assets/           # gambar, favicon, custom JS (jika ada)

```

## 📌 Petunjuk Kontribusi

Terima kasih telah tertarik untuk berkontribusi! 🎉  
Agar kolaborasi berjalan lancar, silakan ikuti panduan kontribusi berikut:

---

#### 🔀 1. Buat Branch Fitur/Perbaikan

Sebelum memulai coding, buat branch baru berdasarkan fitur yang ingin kamu tambahkan.  
Gunakan format:
```cmd
feature/nama-fitur
bugfix/penjelasan-singkat
refactor/penjelasan
```

**Contoh:**
- `feature/tambah-filter-transaksi`
- `bugfix/perbaiki-login-gagal`
- `refactor/struktur-folder-model`

---

#### 🛠️ 2. Coding & Commit

Setelah selesai melakukan perubahan, lakukan commit secara bertahap dan gunakan format pesan commit yang rapi dan deskriptif.

**Format pesan commit:**
```cmd
[type]: [judul singkat perubahan]

[opsional] Penjelasan lebih detail jika perlu.
```

**Contoh:**
- `feat: Tambah fitur filter transaksi berdasarkan bulan`
- `fix: Perbaiki validasi nomor telepon saat registrasi`
- `refactor: Pisahkan fungsi helper untuk format_rupiah`

---

#### 🚀 3. Push dan Pull Request

- Push branch kamu ke remote:

```bash
git push origin nama-branch
```
- Buat Pull Request (PR) ke branch main atau dev (sesuai instruksi repo).
- Sertakan deskripsi perubahan yang kamu lakukan di kolom deskripsi PR.

---

#### ✅ 4. Review & Merge
- PR akan direview oleh maintainer.
- Jika ada saran revisi, silakan update PR-mu.
- Setelah disetujui, PR akan di-merge oleh maintainer.

---

## 💡 Tips Tambahan
- Pastikan project tetap berjalan (tidak error) setelah perubahanmu.
- Gunakan komentar atau TODO jika kamu butuh bantuan atau ingin diskusi.
- Selalu pull branch utama (main/dev) sebelum memulai pekerjaan agar branch kamu up-to-date:
```cmd
git checkout main
git pull origin main
git checkout nama-branch
git merge main
```

---

### Terima kasih atas kontribusinya! 🙌
Untuk pertanyaan atau diskusi, silakan buka Issue atau hubungi maintainer repo ini.

Kalau kamu ingin saya tambahkan badge contributor atau template PR / issue juga, tinggal bilang ya!