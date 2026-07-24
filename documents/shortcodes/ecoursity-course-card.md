# [ecoursity-course-card]

## Ringkas

Menampilkan card course berisi thumbnail, kategori, level, judul, excerpt, harga, dan durasi.

- Lokasi: `app/Shortcodes/CourseCardShortcode.php`
- Template: `templates/pages/public/content-course.php`
- Terdaftar di: `app/Shortcode.php`

## Penggunaan

```text
[ecoursity-course-card]
```

```text
[ecoursity-course-card course_id="123"]
```

## Attribute

| Attribute | Default | Deskripsi |
| --- | --- | --- |
| `course_id` | `0` | ID course. Jika kosong atau `0`, shortcode memakai post ID saat ini. |

## Output

Menghasilkan `<article class="ecoursity-course-card">` yang dapat dipakai di archive course, page builder, atau konten WordPress.

Jika course tidak ditemukan, shortcode mengembalikan string kosong.
