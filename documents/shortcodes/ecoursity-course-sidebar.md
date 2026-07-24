# [ecoursity-course-sidebar]

## Ringkas

Menampilkan box sidebar course berisi thumbnail, harga, tombol beli, durasi, level, jumlah materi, kapasitas, dan nilai lulus.

- Lokasi: `app/Shortcodes/CourseSidebarShortcode.php`
- Terdaftar di: `app/Shortcode.php`

## Penggunaan

```text
[ecoursity-course-sidebar]
```

```text
[ecoursity-course-sidebar course_id="123"]
```

## Attribute

| Attribute | Default | Deskripsi |
| --- | --- | --- |
| `course_id` | `0` | ID course. Jika kosong atau `0`, shortcode memakai post ID saat ini. |

## Output

Menghasilkan `<div class="ecoursity-course-sidebar__box">` dengan isi:

- Thumbnail course jika tersedia.
- Harga utama. Jika ada harga promo, harga promo menjadi harga utama dan harga reguler ditampilkan sebagai `<del>`.
- Tombol beli dari shortcode `[ecoursity-button-buy-course]`.
- Metadata durasi, level, dan jumlah materi.
- Kapasitas jika `max_students` tersedia.
- Nilai lulus jika `passing_grade` tersedia.

Jika course tidak ditemukan, shortcode mengembalikan string kosong.
