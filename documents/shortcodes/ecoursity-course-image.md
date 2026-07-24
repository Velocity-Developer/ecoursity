# [ecoursity-course-image]

## Ringkas

Menampilkan featured image course menggunakan `get_the_post_thumbnail()`.

- Lokasi: `app/Shortcodes/CourseImageShortcode.php`
- Terdaftar di: `app/Shortcode.php`

## Penggunaan

```text
[ecoursity-course-image]
```

```text
[ecoursity-course-image course_id="123" size="large" ratio="16/9"]
```

```text
[ecoursity-course-image size="medium_large" ratio="4:3"]
```

## Attribute

| Attribute | Default | Deskripsi |
| --- | --- | --- |
| `course_id` | `0` | ID course. Jika kosong atau `0`, shortcode memakai post ID saat ini. |
| `size` | `large` | Ukuran image WordPress, misalnya `thumbnail`, `medium`, `medium_large`, `large`, atau custom image size. |
| `ratio` | `16/9` | Aspect ratio wrapper. Mendukung format `16/9`, `16:9`, `4/3`, atau `1/1`. |

## Output

Menghasilkan `<figure class="ecoursity-course-image">` berisi featured image course.

Jika course atau featured image tidak ditemukan, shortcode mengembalikan string kosong.
