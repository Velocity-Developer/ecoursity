# Filters

Daftar filter yang tersedia di Ecoursity.

## `ecoursity_course_form_sections`

- Lokasi: `templates/components/CourseForm.php`
- Deskripsi: Mengubah struktur section form kursus sebelum dipakai untuk render dan collect default value.
- Dokumentasi detail: `documents/filters/ecoursity_course_form_sections.md`

## `ecoursity_checkout_payment_options`

- Lokasi: `app/Services/CheckoutService.php`
- Deskripsi: Mengubah atau menambah pilihan metode pembayaran checkout.
- Dokumentasi detail: `documents/filters/ecoursity_checkout_payment_options.md`

## `ecoursity_checkout_payment_instructions`

- Lokasi: `app/Services/CheckoutService.php`
- Deskripsi: Mengubah instruksi pembayaran yang dikirim setelah checkout berhasil diproses.
- Dokumentasi detail: `documents/filters/ecoursity_checkout_payment_options.md`

## `ecoursity_checkout_nonce_lifetime`

- Lokasi: `app/Services/CheckoutService.php`
- Deskripsi: Mengubah durasi valid one-time checkout nonce dalam satuan detik.
