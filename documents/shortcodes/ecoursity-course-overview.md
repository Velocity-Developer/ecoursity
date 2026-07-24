# [ecoursity-course-overview]

## Ringkas

Menampilkan bagian overview course yang berisi konten utama, key features, requirements, dan target audiences.

- Lokasi: `app/Shortcodes/CourseOverviewShortcode.php`
- Terdaftar di: `app/Shortcode.php`

## Penggunaan

```text
[ecoursity-course-overview]
```

```text
[ecoursity-course-overview course_id="123"]
```

## Attribute

| Attribute | Default | Deskripsi |
| --- | --- | --- |
| `course_id` | `0` | ID course. Jika kosong atau `0`, shortcode memakai post ID saat ini. |

## Output

Menampilkan:

- Konten utama course dengan filter `the_content`.
- Daftar `key_features` jika tersedia.
- Daftar `requirements` jika tersedia.
- Daftar `target_audiences` jika tersedia.

Jika course tidak ditemukan, shortcode mengembalikan string kosong.
