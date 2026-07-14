# Rencana Perubahan Tailwind 4 + Vite

## Summary
Ubah pipeline frontend agar Tailwind 4 dibangun lewat Vite dari entry CSS yang sudah ada di `src/css/main.css`, lalu hasil CSS final keluar ke `assets/css/main.css`. Selain itu, konfigurasi source/watch harus mencakup folder `templates` agar perubahan class di template PHP ikut memicu rebuild dan terdeteksi oleh Tailwind.

## Current State Analysis

### File dan pola yang ditemukan
- Konfigurasi Vite sekarang sangat minimal di [vite.config.ts](ecoursity/vite.config.ts#L1-L8). Hanya memuat plugin `@tailwindcss/vite` tanpa `build`, `input`, `outDir`, atau `watch`.
- `package.json` di [package.json](ecoursity/package.json#L1-L32) sudah punya dependency `tailwindcss` dan `@tailwindcss/vite`, tetapi belum punya script `dev` dan `build`.
- Entry CSS sudah ada di [main.css](ecoursity/src/css/main.css#L1-L1) dan saat ini hanya berisi `@import "tailwindcss";`.
- Runtime WordPress masih enqueue file CSS lama dari folder assets lewat [EnqueueProvider.php](ecoursity/app/Providers/EnqueueProvider.php#L16-L36):
  - admin: `assets/css/ecoursity-admin.css`
  - public: `assets/css/ecoursity-public.css`
- Template PHP aktif dirender dari folder `templates` berdasarkan [TemplateService.php](ecoursity/app/Services/TemplateService.php). Contoh file nyata: [dashboard.php](ecoursity/templates/admin/dashboard.php) dan [student.php](ecoursity/templates/admin/student.php).

### Implikasi teknis
- Agar CSS keluar ke path stabil `assets/css/main.css`, Vite perlu dikonfigurasi untuk build CSS-only dari `src/css/main.css` ke folder `assets` dengan nama file tetap, bukan hashed filename bawaan.
- Karena `assets/` juga dipakai file plugin lain, `emptyOutDir` tidak boleh menghapus isi folder secara agresif.
- Tailwind 4 perlu tahu lokasi file PHP yang mengandung class utility. Folder `templates/**/*.php` harus masuk source scanning.
- Bila output baru ingin dipakai runtime WordPress, enqueue di [EnqueueProvider.php](ecoursity/app/Providers/EnqueueProvider.php#L16-L36) harus diarahkan ke `assets/css/main.css`.

## Proposed Changes

### 1) Perbarui konfigurasi Vite
**File:** [vite.config.ts](ecoursity/vite.config.ts)

**Perubahan:**
- Tetapkan input build ke `src/css/main.css`.
- Tetapkan output build ke folder `assets`.
- Override nama file asset CSS agar hasil final menjadi `css/main.css`.
- Set `emptyOutDir: false` supaya Vite tidak menghapus seluruh folder `assets/`.
- Tambahkan konfigurasi watch agar perubahan file di `templates/**/*.php` ikut memicu rebuild saat mode dev.

**Kenapa:**
- Konfigurasi sekarang belum punya pipeline build nyata.
- WordPress butuh path file yang stabil untuk enqueue.
- Folder `templates` adalah sumber markup utama yang harus dipantau.

**How:**
- Tambahkan `build.rollupOptions.input` mengarah ke file CSS entry.
- Tambahkan `build.outDir = 'assets'`.
- Tambahkan `build.assetsDir = 'css'` hanya jika tetap sesuai hasil output final; bila bentrok dengan output Rollup, gunakan `assetFileNames` untuk paksa nama `css/main.css`.
- Tambahkan aturan output Rollup yang hanya memaksa CSS ke `css/main.css` dan membiarkan asset non-CSS tetap default.
- Tambahkan `server.watch` untuk perubahan file PHP di folder `templates`.

### 2) Tambahkan source Tailwind ke template PHP
**File:** [main.css](ecoursity/src/css/main.css)

**Perubahan:**
- Pertahankan `@import "tailwindcss";`.
- Tambahkan deklarasi `@source` yang mengarah ke folder `templates/**/*.php`.
- Opsional minimal: tambahkan juga `@source` ke `app/**/*.php` bila markup HTML disisipkan dari class PHP.

**Kenapa:**
- Tailwind 4 perlu source scanning eksplisit untuk proyek PHP/WordPress seperti ini.
- Tanpa source ke `templates`, utility class di template bisa tidak masuk output CSS.

**How:**
- Tambahkan source relatif dari `src/css/main.css` ke folder template plugin.
- Jaga file tetap minimal, tanpa config file Tailwind tambahan selama belum dibutuhkan.

### 3) Tambahkan script npm untuk workflow build
**File:** [package.json](ecoursity/package.json)

**Perubahan:**
- Tambahkan script `dev` untuk menjalankan Vite.
- Tambahkan script `build` untuk build produksi.
- Bila perlu, tambahkan `vite` sebagai dependency/devDependency eksplisit jika memang belum dideklarasikan walau sudah ada di lockfile.

**Kenapa:**
- Saat ini belum ada workflow standar untuk menjalankan Vite.
- Executor butuh perintah konsisten untuk build dan watch.

**How:**
- Update bagian `scripts`.
- Pertahankan perubahan seminimal mungkin; tidak perlu tambah `preview` jika tidak dipakai.

### 4) Sinkronkan enqueue WordPress ke output CSS baru
**File:** [EnqueueProvider.php](ecoursity/app/Providers/EnqueueProvider.php)

**Perubahan:**
- Ganti referensi CSS admin dan public agar mengarah ke `assets/css/main.css`.
- Pertahankan handle existing bila tidak ada alasan ganti nama handle.

**Kenapa:**
- Kalau build sudah pindah ke `assets/css/main.css`, runtime WordPress harus membaca file yang sama.
- Ini diff paling pendek dibanding menambah manifest loader atau integrasi dev server.

**How:**
- Ubah URI di `wp_enqueue_style(...)` untuk admin dan public.
- Jangan tambahkan abstraksi baru; cukup ganti path CSS.

## Assumptions & Decisions
- Gunakan struktur repo nyata, bukan struktur dokumentasi lama. Repo ini memakai `src/`, `assets/`, dan `templates/`, bukan `resources/` atau `build/`.
- Target output tunggal adalah `assets/css/main.css` sesuai permintaan user.
- File CSS lama `assets/css/ecoursity-admin.css` dan `assets/css/ecoursity-public.css` tidak dipertahankan sebagai output utama.
- Belum perlu `tailwind.config.js`, PostCSS config, manifest Vite, atau integrasi dev server WordPress. Itu di luar scope permintaan.
- Watch utama wajib `templates/**/*.php`. Source tambahan `app/**/*.php` bersifat defensif dan boleh dipakai hanya jika executor menemukan markup di sana.

## Verification Steps
1. Jalankan script build Vite.
2. Pastikan file output terbentuk di `assets/css/main.css`.
3. Pastikan file lama lain di folder `assets/` tidak ikut terhapus.
4. Tambahkan sementara satu utility class Tailwind di salah satu file template, misalnya [dashboard.php](ecoursity/templates/admin/dashboard.php), lalu jalankan mode watch/dev.
5. Pastikan perubahan di folder `templates` memicu rebuild dan utility class baru muncul di `assets/css/main.css`.
6. Buka halaman admin/public yang memakai enqueue plugin, lalu pastikan stylesheet yang dimuat sudah mengarah ke `assets/css/main.css`.

## Batasan yang sengaja tidak dikerjakan
- Tidak menambahkan multi-entry CSS admin/public terpisah.
- Tidak menambahkan integrasi hot reload Vite ke runtime WordPress.
- Tidak memindahkan file/folder frontend agar cocok dengan dokumentasi lama, karena itu di luar kebutuhan perubahan ini.
