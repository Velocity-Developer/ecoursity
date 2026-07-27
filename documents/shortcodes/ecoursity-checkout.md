# `[ecoursity-checkout]`

Menampilkan layout checkout publik Ecoursity dari template `templates/pages/public/checkout.php`.

## Contoh

```text
[ecoursity-checkout]
```

Shortcode ini memakai Alpine store `EcoursityCart` dan global function `chekout()` untuk membuat order checkout.
Pilihan pembayaran diambil dari `CheckoutService::paymentOptions()`.
Pilihan bawaan adalah `transfer_bank` dan `qris` jika masing-masing sudah dikonfigurasi.
Detail rekening bank atau gambar QRIS baru ditampilkan setelah checkout berhasil diproses.
