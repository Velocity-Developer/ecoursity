# [ecoursity-course-faq]

## Ringkas

Menampilkan daftar FAQ course dalam elemen `<details>`.

- Lokasi: `app/Shortcodes/CourseFaqShortcode.php`
- Terdaftar di: `app/Shortcode.php`

## Penggunaan

```text
[ecoursity-course-faq]
```

```text
[ecoursity-course-faq course_id="123"]
```

## Attribute

| Attribute | Default | Deskripsi |
| --- | --- | --- |
| `course_id` | `0` | ID course. Jika kosong atau `0`, shortcode memakai post ID saat ini. |

## Output

Menghasilkan `<div class="ecoursity-faq">` berisi daftar pertanyaan dan jawaban dari data FAQ course.

FAQ yang ditampilkan adalah item yang memiliki `question` atau `answer`. Jawaban diproses dengan `wpautop()` dan `wp_kses_post()`.

Jika course tidak ditemukan atau course tidak memiliki FAQ, shortcode mengembalikan string kosong.
