# Plan: Composer Build & Zip Script

## Summary

Tambahkan command Composer untuk build plugin Ecoursity menjadi distribution zip. Script akan meng-exclude file development (tests, .trae, AGENTS.md, dll) dan menyertakan vendor autoloader tanpa dev dependencies.

---

## Current State

- **composer.json** hanya punya 1 script: `seed`
- **Tidak ada build tooling** — no Makefile, Webpack, Grunt, dll.
- **`/vendor/` di-gitignore** — tidak tercommit, harus dihasilkan saat build.
- **Zero production dependencies** — vendor setelah `--no-dev` hanya berisi Composer autoloader.
- **`ecoursity.php` require `vendor/autoload.php`** — vendor wajib ada di distribution zip.
- **File/dir yang harus di-exclude dari zip:** `.trae/`, `AGENTS.md`, `.gitignore`, `tests/`, `node_modules/`, `README.md` (opsional), file plan, dll.

---

## Proposed Changes

### 1. `composer.json` — Tambah Script & Archive Exclude

**File:** `composer.json`

**Apa:**
- Tambah `"type": "wordpress-plugin"` (best practice)
- Tambah `archive.exclude` patterns (dibaca oleh `composer archive`)
- Tambah script `build` dan `build:zip`

**Kenapa:**
- `type` memudahkan identifikasi jenis package.
- `archive.exclude` memberi tahu Composer file mana yang harus di-skip saat archive.
- `build` dan `build:zip` sebagai entry point user-friendly.

**Bagaimana:**

```json
{
    "name": "ecoursity/ecoursity",
    "type": "wordpress-plugin",
    "description": "WordPress LMS Plugin",
    "license": "GPL-2.0-or-later",
    "autoload": { ... },
    "autoload-dev": { ... },
    "require-dev": { ... },
    "scripts": {
        "seed": "php tests/seed.php",
        "build:zip": "php build.php",
        "build": [
            "@composer install --no-dev --no-interaction",
            "@build:zip",
            "@composer install --no-interaction"
        ]
    },
    "archive": {
        "exclude": [
            ".gitignore",
            ".trae",
            "AGENTS.md",
            "build.php",
            "composer.lock",
            "node_modules",
            "README.md",
            "tests"
        ]
    }
}
```

**Catatan:** `archive.exclude` tidak 100% dihormati oleh `composer archive` untuk yang bukan root package. Untuk itu kita buat `build.php` sendiri sebagai fallback.

---

### 2. `build.php` — Script Builder (File Baru)

**File:** `build.php` (root plugin)

**Apa:** Script PHP yang:
1. Hapus file `dist/ecoursity.zip` jika sudah ada.
2. Tentukan daftar file/directory yang akan dimasukkan (include list).
3. Gunakan `ZipArchive` untuk membuat zip.
4. Simpan zip di folder `dist/ecoursity.zip`.

**Kenapa:**
- Cross-platform (Windows compatible).
- Zero dependency — pakai `ZipArchive` bawaan PHP.
- Kontrol penuh atas isi zip.

**Bagaimana — include list:**

| Include dari root | Keterangan |
|---|---|
| `ecoursity.php` | Main plugin file |
| `LICENSE` | License file |
| `app/` | Semua source code |
| `templates/` | Template files |
| `assets/` | CSS & assets |
| `vendor/` | Autoloader (setelah --no-dev) |

**Bagaimana — exclude dari folder yang di-include:**
- `vendor/` sudah otomatis `--no-dev` dari script build.
- Tidak ada exclude lain untuk folder di atas.

**Pseudo-code:**

```php
$root = __DIR__;
$zipPath = $root . '/dist/ecoursity.zip';

$zip = new ZipArchive();
$zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

$files = [
    'ecoursity.php',
    'LICENSE',
];

$dirs = [
    'app',
    'templates',
    'assets',
    'vendor',
];

// Tambah files
foreach ($files as $file) {
    $zip->addFile($root . '/' . $file, $file);
}

// Tambah directories (rekursif)
foreach ($dirs as $dir) {
    addDirToZip($zip, $root . '/' . $dir, $dir);
}

$zip->close();
```

Function `addDirToZip` akan rekurif menambahkan semua file dalam directory.

---

### 3. Folder `dist/` — Output Directory

**Apa:** Buat folder `dist/` di root plugin.

**Kenapa:** Tempat output zip, standard practice.

**Bagaimana:** Folder ini ditambahkan ke `.gitignore`:

```
dist/
```

---

### 4. `.gitignore` — Update

**File:** `.gitignore`

**Apa:** Tambah `dist/` ke gitignore.

**Kenapa:** Build artifact tidak perlu di-commit.

---

## Files Affected

| File | Action |
|---|---|
| `composer.json` | Edit — tambah scripts, type, archive.exclude |
| `build.php` | Create baru — script builder |
| `.gitignore` | Edit — tambah `dist/` |

---

## Usage

```bash
# Full build: install --no-dev → create zip → restore dev
composer build

# Atau hanya zip (setelah manual --no-dev)
composer build:zip
```

Output: `dist/ecoursity.zip`

---

## Assumptions & Decisions

1. **PHP ZipArchive tersedia** — Extension `zip` wajib aktif di PHP. Ini sudah standard di sebagian besar hosting/Laragon.
2. **Vendor autoloader cukup** — Plugin tidak punya production dep, jadi vendor setelah `--no-dev` hanya berisi autoloader. Itu sudah cukup.
3. **build.php dieksekusi dari root plugin** — Composer jalankan script dari `$PROJECT_ROOT`.
4. **Tidak gunakan `composer archive`** — Karena exclude behavior tidak konsisten untuk root package, `build.php` memberikan kontrol lebih baik.
5. **Nama zip: `ecoursity.zip`** — Nama package sesuai plugin slug.

---

## Verification

1. Jalankan `composer build` — harus sukses tanpa error.
2. Cek `dist/ecoursity.zip` sudah terbuat.
3. Ekstrak zip ke folder kosong, verifikasi:
   - File `ecoursity.php` ada di root.
   - Folder `app/`, `templates/`, `assets/`, `vendor/` ada.
   - File `vendor/autoload.php` ada.
   - File `.trae/` atau `tests/` **tidak** ikut.
4. Jalankan `php -r "require 'vendor/autoload.php';"` di folder hasil ekstrak — tidak error.
