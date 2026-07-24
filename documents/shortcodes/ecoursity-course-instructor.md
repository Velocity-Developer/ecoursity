# [ecoursity-course-instructor]

## Ringkas

Menampilkan informasi instruktur course, termasuk avatar, nama, dan bio author.

- Lokasi: `app/Shortcodes/CourseInstructorShortcode.php`
- Terdaftar di: `app/Shortcode.php`

## Penggunaan

```text
[ecoursity-course-instructor]
```

```text
[ecoursity-course-instructor course_id="123"]
```

## Attribute

| Attribute | Default | Deskripsi |
| --- | --- | --- |
| `course_id` | `0` | ID course. Jika kosong atau `0`, shortcode memakai post ID saat ini. |

## Output

Menghasilkan `<div class="ecoursity-instructor">` berisi avatar author, nama author, dan deskripsi author WordPress. Jika deskripsi author kosong, teks fallback yang dipakai adalah `Instruktur kursus ini.`

Jika course tidak ditemukan, shortcode mengembalikan string kosong.
