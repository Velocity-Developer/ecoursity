# `[ecoursity-cart-icon]`

Shortcode ini menampilkan icon cart dengan badge jumlah isi cart.

- Lokasi: `app/Shortcodes/CartIconShortcode.php`
- Komponen: `templates/components/CartIcon.php`
- Terdaftar di: `app/Shortcode.php`

## Contoh

```text
[ecoursity-cart-icon]
```

```text
[ecoursity-cart-icon url="/checkout/" label="Keranjang"]
```

## Atribut

| Atribut | Default | Deskripsi |
| --- | --- | --- |
| `url` | `/checkout/` | URL tujuan saat icon diklik. |
| `label` | `Cart` | Label aksesibilitas untuk icon. |
| `class` | `` | Class CSS tambahan. |

## Catatan

Jumlah item mengambil nilai awal dari server melalui `Ecoursity\App\Models\Cart::count()`. Jika Alpine.js aktif, badge akan otomatis sinkron dengan store `$store.EcoursityCart.count` setelah cart berubah.

URL default bisa diubah melalui filter `ecoursity_cart_icon_url`.
