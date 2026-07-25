# Cart API

## Ringkas

API cart digunakan untuk menyimpan daftar ID post `ecoursity_course` yang akan dibeli.

- Lokasi: [ApiRoutes.php](ecoursity/app/Routes/ApiRoutes.php)
- Controller: [CartController.php](ecoursity/app/Controllers/CartController.php)
- Model: [Cart.php](ecoursity/app/Models/Cart.php)
- Permission: public

## Penyimpanan

- User login: cart disimpan di `user_meta` dengan key `_ecoursity_cart`.
- User guest: cart disimpan di PHP session dengan key `ecoursity_cart`.
- Data cart berupa array ID post valid dari post type `ecoursity_course`.

## Format Response Cart

```json
{
  "items": [12, 34],
  "courses": [
    {
      "id": 12,
      "title": "Course Title",
      "slug": "course-title",
      "price": "100000",
      "price_sale": "75000",
      "thumbnail": "https://example.test/image.jpg",
      "permalink": "https://example.test/course/course-title/"
    }
  ],
  "count": 2
}
```

## GET /wp-json/ecoursity/v1/cart/

Ambil isi cart saat ini.

### Response sukses

```json
{
  "success": true,
  "data": {
    "items": [12],
    "courses": [],
    "count": 1
  }
}
```

## POST /wp-json/ecoursity/v1/cart/

Tambah course ke cart.

### Body

```json
{
  "course_id": 12
}
```

### Response sukses

```json
{
  "success": true,
  "message": "Course added to cart.",
  "data": {
    "items": [12],
    "courses": [],
    "count": 1
  }
}
```

### Response gagal

```json
{
  "success": false,
  "message": "Course not found.",
  "errors": {
    "course_id": "Invalid course ID."
  }
}
```

## PUT /wp-json/ecoursity/v1/cart/

Ganti seluruh isi cart.

### Body

```json
{
  "course_ids": [12, 34]
}
```

ID yang bukan post type `ecoursity_course` akan diabaikan.

### Response sukses

```json
{
  "success": true,
  "message": "Cart updated.",
  "data": {
    "items": [12, 34],
    "courses": [],
    "count": 2
  }
}
```

## DELETE /wp-json/ecoursity/v1/cart/

Kosongkan cart.

### Response sukses

```json
{
  "success": true,
  "message": "Cart cleared.",
  "data": {
    "items": [],
    "courses": [],
    "count": 0
  }
}
```

## DELETE /wp-json/ecoursity/v1/cart/{course_id}

Hapus satu course dari cart.

### Response sukses

```json
{
  "success": true,
  "message": "Course removed from cart.",
  "data": {
    "items": [],
    "courses": [],
    "count": 0
  }
}
```

## Catatan Login

Saat user guest login, cart dari PHP session otomatis digabungkan ke user meta `_ecoursity_cart`.
