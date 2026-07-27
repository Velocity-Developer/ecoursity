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
  "payment": "bank_transfer"
}
```

`payment` opsional dan disimpan sebagai teks sederhana pada meta order.

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
    "order_payment": "bank_transfer",
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
    "order_total": 75000
  }
}
```

Setelah order berhasil dibuat, cart user akan dikosongkan.

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
