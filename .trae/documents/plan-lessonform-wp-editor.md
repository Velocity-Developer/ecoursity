# Plan: Ubah LessonForm ke wp_editor + prefill dari model

## Summary

Ubah komponen [LessonForm.php](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/templates/components/LessonForm.php) agar field `ecoursity_lesson_content` memakai `wp_editor()` seperti pattern di [CourseForm.php](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/templates/components/CourseForm.php#L399-L410). Untuk lesson existing, value form akan diprefill langsung saat render server-side dari model [Lesson.php](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/app/Models/Lesson.php), bukan lagi di-load dari `GET /ecoursity/v1/lessons/{id}`. REST tetap dipakai hanya untuk simpan (`POST`/`PUT`).

## Current State Analysis

### 1. Template lesson sekarang masih textarea biasa
- [LessonForm.php](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/templates/components/LessonForm.php#L10-L22) hanya menyiapkan `lesson_defaults` server-side.
- Konten lesson dirender sebagai `<textarea>` di [LessonForm.php](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/templates/components/LessonForm.php#L62-L65).
- `x-data="lessonForm(...)"` masih kirim `restUrl` + `defaults` ke Alpine di [LessonForm.php](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/templates/components/LessonForm.php#L25-L27).

### 2. Edit lesson existing sekarang bergantung pada REST GET
- Di [ecoursity-main.js](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/assets/js/ecoursity-main.js#L23-L33), jika `currentLessonId > 0`, Alpine memanggil `loadLesson()`.
- `loadLesson()` fetch `GET ${restUrl}${currentLessonId}` di [ecoursity-main.js](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/assets/js/ecoursity-main.js#L63-L88).
- `show()` pada [LessonController.php](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/app/Controllers/LessonController.php#L19-L34) mengambil data existing lewat model dan mengembalikan JSON transform.

### 3. Save lesson sudah lewat REST dan masih relevan
- Submit Alpine tetap kirim payload ke `POST` / `PUT` di [ecoursity-main.js](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/assets/js/ecoursity-main.js#L157-L211).
- `store()` dan `update()` di [LessonController.php](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/app/Controllers/LessonController.php#L36-L104) sudah cukup untuk create/update lesson + meta + section.

### 4. Pattern wp_editor sudah ada di plugin
- [CourseForm.php](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/templates/components/CourseForm.php#L399-L410) sudah memakai `wp_editor()` untuk field content.
- Ini jadi pattern paling dekat dan aman untuk diikuti.

### 5. Pattern prefill server-side dari model juga sudah ada
- [MetaboxPostProvider.php](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/app/Providers/MetaboxPostProvider.php#L155-L212) memanggil `Lesson::find($post->ID)` lalu isi field langsung dari model/meta.
- Artinya pendekatan “render value dari model, bukan fetch lagi di browser” sudah konsisten dengan bagian lain plugin.

## Proposed Changes

### 1. [LessonForm.php](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/templates/components/LessonForm.php)

#### Ubah sumber data awal
- Tambah lookup lesson existing dengan `Lesson::find((int) $lesson_id)` bila `lesson_id > 0`.
- Bangun `lesson_defaults` dari model saat edit, bukan kosong lalu menunggu REST GET.
- Isi minimal:
  - `title`
  - `slug`
  - `assigned`
  - `assigned_title`
  - `section_id`
  - `content`
  - `status`
  - `duration_value`
  - `duration_unit`
  - `preview`
  - `permalink`
- Untuk `section_id`, template perlu resolve id section existing dengan logic yang sama seperti transform controller sekarang. Pilihan paling kecil diff:
  - panggil helper/loop section langsung di template mengikuti pola [LessonController::resolveSectionId()](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/app/Controllers/LessonController.php#L208-L223), atau
  - ekstrak logic itu ke method reusable bila saat implementasi ternyata perlu dipakai dua tempat.
- Karena user minta “ambil ke model langsung saja”, prioritas implementasi paling pendek: render data existing langsung di template tanpa REST GET.

#### Ganti textarea ke wp_editor
- Ganti blok textarea di [LessonForm.php](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/templates/components/LessonForm.php#L62-L65) dengan `wp_editor()`.
- Gunakan id editor tetap `ecoursity_lesson_content` agar JS submit sink tetap sederhana.
- Prefill content dari `lesson_defaults['content']`.
- Opsi editor ikuti pattern [CourseForm.php](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/templates/components/CourseForm.php#L402-L409): `media_buttons`, `quicktags`, tinggi editor memadai.
- `textarea_name` tidak dipakai untuk submit native form, tapi tetap bisa diisi agar markup valid.

#### Sederhanakan contract ke Alpine
- `restUrl` tetap dibutuhkan untuk simpan.
- `defaults` tetap dikirim, tetapi sekarang sudah final untuk create maupun edit.
- `loading` UI untuk load existing bisa dihapus/sederhanakan karena tidak ada fetch awal lagi.

### 2. [ecoursity-main.js](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/assets/js/ecoursity-main.js)

#### Hapus load existing via REST
- Buang dependency `loadLesson()` dari init edit mode.
- `init()` cukup:
  - normalize `status`, `section_id`, `assigned`
  - parse duration bila ada
  - set `loading = false`
  - init/sync editor
- Fungsi `loadLesson()` kemungkinan bisa dihapus seluruhnya jika tidak dipakai lagi.

#### Pertahankan submit via REST
- `submit()` tetap kirim `POST` / `PUT` ke endpoint existing.
- Sebelum submit, sinkronisasi isi `wp_editor` ke `lesson.content` tetap perlu.

#### Sesuaikan sink editor dengan wp_editor
- Cek apakah init manual TinyMCE masih perlu.
- Karena `wp_editor()` sudah menginisialisasi editor WordPress, target perubahan paling kecil:
  - jangan `tinymce.init(...)` manual lagi untuk lesson editor,
  - cukup baca/tulis instance editor existing (`tinymce.get('ecoursity_lesson_content')`) atau fallback ke textarea.
- `syncEditorContent()` dan `syncEditorToModel()` dipertahankan, tapi `initTinyMce()` disederhanakan jadi “attach ke editor yang sudah ada”, bukan membuat editor baru.
- Perlu aman untuk modal yang inject HTML dinamis: setelah template masuk dan `Alpine.initTree(...)` dijalankan oleh modal loader, logic JS harus memaksa sync konten ke instance editor WordPress yang sudah tersedia atau fallback ke textarea jika editor belum siap.

### 3. [LessonController.php](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/app/Controllers/LessonController.php)

#### Tidak ubah alur simpan
- `store()` dan `update()` tetap dipakai apa adanya kecuali implementasi menemukan mismatch payload minor.

#### Kandidat refactor kecil bila perlu
- Jika server-side template perlu `section_id` existing dan implementasi ingin hindari duplikasi logic, ekstrak `resolveSectionId()` ke tempat reusable.
- Contoh paling kecil diff:
  - jadikan method public/static util di model/kelas terkait, atau
  - tetap duplikasi terbatas di template bila itu paling kecil dan aman.
- Karena user hanya minta plan untuk perubahan form/editor, refactor ini opsional dan hanya dilakukan bila benar-benar perlu untuk mengisi data edit server-side.

### 4. [Lesson.php](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/app/Models/Lesson.php) / [Section.php](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/app/Models/Section.php)

#### Hanya bila dibutuhkan implementasi
- Jika perlu helper reusable untuk resolve `section_id`, tempat paling cocok adalah model/utility yang bisa dipakai oleh template dan controller.
- Jika tidak perlu, file ini tidak disentuh.

## Assumptions & Decisions

1. User ingin menghapus fetch awal `GET /lessons/{id}` sepenuhnya dari flow modal lesson edit.
2. REST endpoint `show()` boleh tetap dibiarkan ada walau tidak lagi dipakai oleh `LessonForm`, karena request hanya menyebut “rest hanya untuk simpan”, bukan hapus endpoint.
3. `wp_editor()` layak dipakai di modal component ini karena plugin sudah memakai pattern yang sama di [CourseForm.php](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/templates/components/CourseForm.php#L399-L410).
4. Nilai existing lesson akan dirender saat server-side template dibuat dari model [Lesson.php](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/app/Models/Lesson.php#L40-L49).
5. Bila inisialisasi `wp_editor()` di modal dinamis ternyata butuh trigger tambahan WordPress JS, implementasi akan ambil diff minimum untuk memastikan editor tampil dan nilainya bisa dibaca/ditulis tanpa mengubah arsitektur save REST.

## Verification Steps

1. Buka modal tambah lesson dari course builder.
   - Pastikan editor tampil sebagai `wp_editor`, bukan textarea polos.
   - Pastikan content kosong/default untuk lesson baru.
2. Buka modal edit lesson existing.
   - Pastikan judul, konten, durasi, preview, permalink, assigned course, dan section terisi langsung tanpa network request `GET /ecoursity/v1/lessons/{id}`.
3. Simpan lesson baru.
   - Pastikan request tetap `POST /ecoursity/v1/lessons/`.
   - Pastikan content dari editor terkirim benar.
4. Simpan perubahan lesson existing.
   - Pastikan request tetap `PUT /ecoursity/v1/lessons/{id}`.
   - Pastikan isi editor terupdate di post content dan section tetap sinkron.
5. Reopen modal lesson yang baru disimpan.
   - Pastikan value datang dari render model server-side, bukan fetch GET.
6. Cek tidak ada regression pada modal loader dan event `ecoursity:lesson-saved` di [ecoursity-main.js](file:///c:/laragon/www/ecoursity/wp-content/plugins/ecoursity/assets/js/ecoursity-main.js#L189-L200).
