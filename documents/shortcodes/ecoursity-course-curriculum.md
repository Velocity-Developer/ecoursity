# [ecoursity-course-curriculum]

## Ringkas

Menampilkan kurikulum course berupa daftar section dan lesson. Section pertama terbuka secara default.

- Lokasi: `app/Shortcodes/CourseCurriculumShortcode.php`
- Terdaftar di: `app/Shortcode.php`

## Penggunaan

```text
[ecoursity-course-curriculum]
```

```text
[ecoursity-course-curriculum course_id="123"]
```

## Attribute

| Attribute | Default | Deskripsi |
| --- | --- | --- |
| `course_id` | `0` | ID course. Jika kosong atau `0`, shortcode memakai post ID saat ini. |

## Output

Menghasilkan `<div class="ecoursity-curriculum">` berisi daftar section dari `Section::allByCourse()`. Setiap lesson akan menampilkan judul dan:

- Link permalink jika lesson memiliki preview.
- Label `Preview` untuk lesson preview.
- Durasi lesson untuk lesson non-preview.

Jika course tidak ditemukan, shortcode mengembalikan string kosong. Jika course ditemukan tetapi belum memiliki section, shortcode menampilkan pesan `Kurikulum belum tersedia.`
