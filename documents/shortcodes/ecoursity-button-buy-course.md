# [ecoursity-button-buy-course]

## Ringkas

Menampilkan tombol untuk membeli course berbayar, mengambil course gratis, atau mengarahkan user yang belum login ke halaman login.

- Lokasi: `app/Shortcodes/ButtonBuyCourseShortcode.php`
- Terdaftar di: `app/Shortcode.php`

## Penggunaan

```text
[ecoursity-button-buy-course]
```

```text
[ecoursity-button-buy-course course_id="123" label="Daftar Sekarang"]
```

```text
[ecoursity-button-buy-course course_id="123" login_label="Login untuk Daftar" free_label="Ambil Gratis" class="btn btn-primary" require_login="yes"]
```

## Attribute

| Attribute | Default | Deskripsi |
| --- | --- | --- |
| `course_id` | `0` | ID course. Jika kosong atau `0`, shortcode memakai post ID saat ini. |
| `label` | kosong | Teks tombol saat user boleh lanjut. Jika kosong, label otomatis menjadi `Ambil Course Gratis` untuk course gratis atau `Beli Course` untuk course berbayar. |
| `login_label` | `Login untuk Beli Course` | Teks tombol ketika `require_login` aktif dan user belum login. |
| `free_label` | `Ambil Course Gratis` | Teks tombol otomatis untuk course gratis jika `label` kosong. |
| `class` | kosong | Class CSS tambahan. Class utama `ecoursity-buy-course-button` selalu ditambahkan. |
| `url` | kosong | URL tujuan custom. Jika kosong, URL default memakai permalink course dengan query `ecoursity_buy_course={course_id}`. |
| `require_login` | `yes` | Menentukan apakah user harus login. Nilai truthy: `1`, `true`, `yes`, `on`. |

## Output

Menghasilkan tag `<a>` dengan atribut:

- `href`
- `class`
- `data-ecoursity-buy-course`
- `data-course-id`
- `data-course-price`

Jika course tidak ditemukan, shortcode mengembalikan string kosong.

## Filter

URL beli default dapat diubah melalui filter:

```php
apply_filters('ecoursity_buy_course_url', $defaultUrl, $course);
```
