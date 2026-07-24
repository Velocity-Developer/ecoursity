# [ecoursity-course-tabs]

## Ringkas

Menampilkan navigasi tab untuk halaman detail course. Tab default terdiri dari Overview, Kurikulum, dan Instruktur. Tab FAQ hanya ditampilkan jika course memiliki FAQ.

- Lokasi: `app/Shortcodes/CourseTabsShortcode.php`
- Terdaftar di: `app/Shortcode.php`

## Penggunaan

```text
[ecoursity-course-tabs]
```

```text
[ecoursity-course-tabs course_id="123"]
```

## Attribute

| Attribute | Default | Deskripsi |
| --- | --- | --- |
| `course_id` | `0` | ID course. Jika kosong atau `0`, shortcode memakai post ID saat ini. |

## Output

Menghasilkan `<nav class="ecoursity-course-tabs">` berisi tombol Alpine.js dengan binding `tab`.

Jika course tidak ditemukan atau daftar tab kosong, shortcode mengembalikan string kosong.

## Filter

Daftar tab dapat diubah melalui filter:

```php
apply_filters('ecoursity_course_single_tabs', $tabs, $course);
```
