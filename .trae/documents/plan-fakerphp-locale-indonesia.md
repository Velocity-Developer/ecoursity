# Rencana: FakerPHP pakai bahasa Indonesia

## Summary
Tujuan: membuat data seed dari FakerPHP memakai locale Indonesia (`id_ID`) pada alur seeding plugin ini.

Pendekatan paling kecil dan sesuai kondisi codebase sekarang: ubah inisialisasi Faker di dua seeder agar memakai `Factory::create('id_ID')`.

## Current State Analysis
Hasil eksplorasi codebase:

- Faker sudah terpasang sebagai dependency dev di [composer.json](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/composer.json#L16-L20).
- Alur seeding berjalan lewat script Composer `seed`, yang mengeksekusi [seed.php](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/tests/seed.php#L24-L31).
- Tidak ada bootstrap PHPUnit global atau config test level plugin yang bisa dipakai sebagai tempat set locale Faker secara terpusat.
- Pemakaian Faker saat ini hanya ada di dua file:
  - [StudentSeeder.php](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/tests/Seeders/StudentSeeder.php#L16-L24)
  - [InstructorSeeder.php](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/tests/Seeders/InstructorSeeder.php#L15-L23)
- Kedua file masih memakai `Factory::create()` tanpa locale, jadi Faker jatuh ke locale default.
- Username dan email dibentuk dari slug nama lewat helper [Str.php](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/app/Helpers/Str.php). Ini tidak perlu diubah untuk kebutuhan locale Faker, tetapi tetap relevan karena nama hasil Faker akan dipakai untuk `user_login` dan `user_email`.

## Assumptions & Decisions
- Locale target: `id_ID`.
- Scope hanya untuk data seeding/test, bukan runtime plugin.
- Tidak perlu tambah helper/shared factory baru karena penggunaan Faker masih cuma dua titik. Diff paling pendek lebih tepat.
- Tidak perlu ubah [seed.php](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/tests/seed.php) karena file itu hanya orchestration, bukan tempat pembuatan instance Faker.
- Tidak perlu ubah `composer.json` karena package `fakerphp/faker` sudah tersedia.

## Proposed Changes

### 1) Ubah Faker locale di StudentSeeder
File: [StudentSeeder.php](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/tests/Seeders/StudentSeeder.php)

Apa:
- Ganti `Factory::create()` menjadi `Factory::create('id_ID')`.

Kenapa:
- Supaya `firstName()` dan `lastName()` menghasilkan data berbahasa/bernuansa Indonesia saat seeding student.

Bagaimana:
- Edit satu baris inisialisasi Faker pada method `seed()`.

### 2) Ubah Faker locale di InstructorSeeder
File: [InstructorSeeder.php](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/tests/Seeders/InstructorSeeder.php)

Apa:
- Ganti `Factory::create()` menjadi `Factory::create('id_ID')`.

Kenapa:
- Menjaga konsistensi locale untuk data instructor.

Bagaimana:
- Edit satu baris inisialisasi Faker pada method `seed()`.

### 3) Rapikan import yang tidak terpakai bila memang tersentuh
File:
- [StudentSeeder.php](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/tests/Seeders/StudentSeeder.php)
- [InstructorSeeder.php](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/tests/Seeders/InstructorSeeder.php)

Apa:
- Evaluasi `use Faker\Generator;`, `use WP_Error;`, dan model import yang tampak tidak dipakai.

Keputusan:
- Hanya hapus jika implementasi memang menyentuh file itu dan tidak menambah scope berlebihan.
- Prioritas utama tetap locale Faker. Pembersihan import bukan kebutuhan wajib.

## Verification Steps
Setelah plan disetujui dan implementasi boleh jalan:

1. Baca ulang file target untuk memastikan titik edit masih sama.
2. Ubah dua pemanggilan `Factory::create()` menjadi `Factory::create('id_ID')`.
3. Jalankan seeding lewat script yang sudah ada:
   - `composer seed`
4. Verifikasi hasil:
   - proses seed selesai tanpa error
   - user student dan instructor baru terbentuk
   - `first_name` / `last_name` hasil seed memakai nama Indonesia atau format locale `id_ID`
5. Jika muncul bentrok `user_login` akibat slug nama sama, catat sebagai isu terpisah. Itu bukan bagian dari perubahan locale kecuali user minta diperbaiki juga.

## Expected Diff Size
Sangat kecil. Inti perubahan hanya dua baris.
