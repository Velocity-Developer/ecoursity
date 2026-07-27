# Checkout Payment Filters

## `ecoursity_checkout_payment_options`

Mengubah daftar pilihan metode pembayaran yang muncul di halaman checkout.

Lokasi: `app/Services/CheckoutService.php`

```php
add_filter('ecoursity_checkout_payment_options', function (array $options): array {
    $options['virtual_account'] = [
        'key' => 'virtual_account',
        'label' => 'Virtual Account',
        'description' => 'Nomor virtual account akan ditampilkan setelah checkout diproses.',
    ];

    return $options;
});
```

Setiap opsi memakai struktur:

```php
[
    'key' => 'payment_key',
    'label' => 'Payment Label',
    'description' => 'Deskripsi singkat di pilihan checkout.',
]
```

## `ecoursity_checkout_payment_instructions`

Mengubah instruksi pembayaran yang dikirim pada response checkout.

Lokasi: `app/Services/CheckoutService.php`

```php
add_filter('ecoursity_checkout_payment_instructions', function (array $instructions, string $payment): array {
    if ($payment !== 'virtual_account') {
        return $instructions;
    }

    return [
        'type' => 'virtual_account',
        'label' => 'Virtual Account',
        'message' => 'Selesaikan pembayaran melalui virtual account berikut.',
        'details' => [
            [
                'label' => 'Bank',
                'value' => 'BCA',
            ],
            [
                'label' => 'Nomor VA',
                'value' => '88081234567890',
            ],
        ],
    ];
}, 10, 2);
```

Template checkout mendukung tampilan bawaan untuk `transfer_bank` dan `qris`.
Metode custom dapat memakai `message` dan `details`.
