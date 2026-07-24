# [ecoursity-course-hero]

## Ringkas

Menampilkan hero detail course berisi breadcrumb, kategori, judul, excerpt, instruktur, level, durasi, dan jumlah materi.

- Lokasi: `app/Shortcodes/CourseHeroShortcode.php`
- Terdaftar di: `app/Shortcode.php`

## Penggunaan

```text
[ecoursity-course-hero]
```

```text
[ecoursity-course-hero course_id="123"]
```

## Attribute

| Attribute | Default | Deskripsi |
| --- | --- | --- |
| `course_id` | `0` | ID course. Jika kosong atau `0`, shortcode memakai post ID saat ini. |

## Output

Menghasilkan `<section class="ecoursity-course-hero">` dengan data yang diambil dari model course, taxonomy `ecoursity_course_category`, author WordPress, dan section lesson.

Jika course tidak ditemukan, shortcode mengembalikan string kosong.
