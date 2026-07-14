# ecoursity_course_form_sections

## Ringkas

Filter ini dipakai untuk mengubah daftar section/field pada form kursus.

- Lokasi: [CourseForm.php](templates/components/CourseForm.php#L7-L160)
- Hook: `ecoursity_course_form_sections`
- Tipe: filter

## Parameter

### `$course_form_sections`

Array struktur section form kursus. Nilai awal berisi kombinasi item `field`, `special`, dan `row`.

Bentuk umum:

```php
[
    [
        'type' => 'field',
        'name' => 'title',
        'label' => 'Judul Kursus',
        'input' => 'text',
        'class' => 'ecoursity-form-input',
        'required' => true,
        'placeholder' => 'e.g. Belajar Laravel dari Nol',
        'default' => '',
    ],
    [
        'type' => 'special',
        'name' => 'slug',
    ],
    [
        'type' => 'row',
        'fields' => [
            // field items
        ],
    ],
]
```

## Tipe item

### `field`

Dipakai oleh renderer form untuk input standar.

Field umum yang didukung dari implementasi saat ini:

- `name`
- `label`
- `input`
- `class`
- `default`
- `placeholder`
- `required`
- `rows`
- `min`
- `max`
- `step`
- `options`

### `special`

Dipakai untuk bagian form yang dirender khusus oleh template. Dari default sekarang:

- `slug`
- `thumbnail`
- `duration`
- `content`

### `row`

Wrapper layout untuk beberapa `field` di dalam key `fields`.

## Efek filter

Hasil filter dipakai untuk 2 hal:

1. Render tampilan form.
2. Kumpulkan default value untuk semua item dengan `type = field` dan `name` valid.

Artinya, jika menambah `field` baru dan memberi `default`, nilai default itu ikut masuk ke state awal form.

## Contoh penggunaan

```php
add_filter('ecoursity_course_form_sections', function (array $sections): array {
    $sections[] = [
        'type' => 'field',
        'name' => 'subtitle',
        'label' => 'Subjudul',
        'input' => 'text',
        'class' => 'ecoursity-form-input',
        'default' => '',
        'placeholder' => 'Subjudul kursus',
    ];

    return $sections;
});
```

## Catatan

- Gunakan struktur array yang konsisten. Template tidak punya validasi schema mendalam.
- Item `special` baru perlu dukungan render di `templates/components/CourseForm.php`.
- Item non-`field` tidak ikut masuk ke default collector, kecuali dibungkus `row` yang berisi `field`.
