# Checkout API

## Ringkas

API checkout digunakan untuk membuat order dari isi cart user yang sedang login.

- Lokasi: `app/Routes/ApiRoutes.php`
- Controller: `app/Controllers/CheckoutController.php`
- Service: `app/Services/CheckoutService.php`
- Permission: public route, tetapi service mewajibkan user login

Frontend yang memakai cookie login WordPress perlu mengirim header `X-WP-Nonce`
agar user login terbaca oleh REST API.

## `POST /wp-json/ecoursity/v1/checkout/`

Membuat order dengan metode `checkout`, status awal `pending`, dan item dari cart user.

### Body

```json
{
  "payment": "transfer_bank",
  "checkout_nonce": "one-time-checkout-token"
}
```

`payment` menerima key metode pembayaran yang tersedia dari `CheckoutService::paymentOptions()`.
Default bawaan adalah `transfer_bank` dan `qris` jika masing-masing sudah dikonfigurasi.
Jika dikosongkan, service memakai metode pertama yang tersedia dari pengaturan pembayaran.
`checkout_nonce` wajib dikirim dari halaman checkout dan hanya dapat digunakan satu kali untuk mencegah checkout ganda.

### Response sukses

```json
{
  "success": true,
  "message": "Checkout created.",
  "data": {
    "id": 101,
    "order_number": "ORDER260727AB12CD",
    "order_date": "20260727153000",
    "order_method": "checkout",
    "order_status": "pending",
    "order_user": 7,
    "order_payment": "transfer_bank",
    "order_items": [
      {
        "id": 12,
        "name": "Course Title",
        "price": 100000,
        "price_sale": 75000,
        "price_real": 75000
      }
    ],
    "order_subtotal": 75000,
    "order_total": 75000,
    "payment_instructions": {
      "type": "transfer_bank",
      "label": "Transfer Bank",
      "banks": [
        {
          "bank": "BCA",
          "atasnama": "PT Ecoursity Indonesia",
          "norek": "1234567890"
        }
      ]
    }
  }
}
```

Setelah order berhasil dibuat, cart user akan dikosongkan.
Daftar rekening bank atau gambar QRIS dikirim pada `payment_instructions` setelah checkout berhasil dibuat.
Metode pembayaran dapat ditambah melalui filter `ecoursity_checkout_payment_options`, dan instruksi setelah checkout dapat diubah melalui `ecoursity_checkout_payment_instructions`.

### Response gagal

User belum login:

```json
{
  "success": false,
  "message": "You must login before checkout.",
  "errors": {}
}
```

Cart kosong:

```json
{
  "success": false,
  "message": "Cart is empty.",
  "errors": {}
}
```

Nonce checkout tidak valid atau sudah terpakai:

```json
{
  "success": false,
  "message": "Checkout session is expired. Please reload the checkout page.",
  "errors": {}
}
```
