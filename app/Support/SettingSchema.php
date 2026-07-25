<?php

declare(strict_types=1);

namespace Ecoursity\App\Support;

defined('ABSPATH') || exit;

class SettingSchema
{
    public static function tabs(): array
    {
        return [
            'general' => [
                'label' => __('Umum', 'ecoursity'),
                'description' => __('Pengaturan dasar identitas dan format LMS.', 'ecoursity'),
                'fields' => [
                    [
                        'key' => 'brand_name',
                        'label' => __('Nama Brand', 'ecoursity'),
                        'type' => 'text',
                        'default' => get_bloginfo('name'),
                        'description' => __('Nama yang ditampilkan pada area pembelajaran LMS.', 'ecoursity'),
                    ],
                    [
                        'key' => 'support_email',
                        'label' => __('Email Dukungan', 'ecoursity'),
                        'type' => 'email',
                        'default' => get_option('admin_email'),
                        'description' => __('Alamat email untuk bantuan siswa dan notifikasi dasar.', 'ecoursity'),
                    ],
                    [
                        'key' => 'enable_instructor_registration',
                        'label' => __('Aktifkan Pendaftaran Instruktur', 'ecoursity'),
                        'type' => 'checkbox',
                        'default' => true,
                        'description' => __('Izinkan instruktur mendaftar di LMS Anda.', 'ecoursity'),
                    ],
                ],
            ],
            'courses' => [
                'label' => __('Kursus', 'ecoursity'),
                'description' => __('Atur perilaku daftar dan konten kursus.', 'ecoursity'),
                'fields' => [
                    [
                        'key' => 'courses_per_page',
                        'label' => __('Kursus Per Halaman', 'ecoursity'),
                        'type' => 'number',
                        'default' => 12,
                        'min' => 1,
                        'max' => 100,
                    ],
                    [
                        'key' => 'default_course_status',
                        'label' => __('Status Kursus Default', 'ecoursity'),
                        'type' => 'select',
                        'default' => 'draft',
                        'options' => [
                            'draft' => __('Draft', 'ecoursity'),
                            'publish' => __('Terbit', 'ecoursity'),
                        ],
                    ],
                ],
            ],
            'payments' => [
                'label' => __('Pembayaran', 'ecoursity'),
                'description' => __('Pengaturan checkout dan tampilan harga.', 'ecoursity'),
                'fields' => [
                    [
                        'key' => 'enable_checkout',
                        'label' => __('Checkout', 'ecoursity'),
                        'type' => 'checkbox',
                        'default' => true,
                        'description' => __('Aktifkan proses checkout untuk kursus berbayar.', 'ecoursity'),
                    ],
                    [
                        'key' => 'currency',
                        'label' => __('Mata Uang', 'ecoursity'),
                        'type' => 'select',
                        'default' => 'IDR',
                        'options' => [
                            'IDR' => __('Rupiah Indonesia (IDR)', 'ecoursity'),
                            'USD' => __('Dollar Amerika (USD)', 'ecoursity'),
                            'MYR' => __('Ringgit Malaysia (MYR)', 'ecoursity'),
                            'SGD' => __('Dollar Singapura (SGD)', 'ecoursity'),
                        ],
                    ],
                    [
                        'key' => 'currency_position',
                        'label' => __('Posisi Mata Uang', 'ecoursity'),
                        'type' => 'select',
                        'default' => 'before',
                        'options' => [
                            'before' => __('Sebelum harga', 'ecoursity'),
                            'after' => __('Setelah harga', 'ecoursity'),
                        ],
                    ],
                ],
            ],
            'email' => [
                'label' => __('Email', 'ecoursity'),
                'description' => __('Identitas pengirim email dari Ecoursity.', 'ecoursity'),
                'fields' => [
                    [
                        'key' => 'email_sender_name',
                        'label' => __('Nama Pengirim', 'ecoursity'),
                        'type' => 'text',
                        'default' => get_bloginfo('name'),
                    ],
                    [
                        'key' => 'email_sender_email',
                        'label' => __('Email Pengirim', 'ecoursity'),
                        'type' => 'email',
                        'default' => get_option('admin_email'),
                    ],
                ],
            ],
        ];
    }

    public static function tab(string $tab): array
    {
        $tabs = self::tabs();

        return $tabs[$tab] ?? reset($tabs);
    }

    public static function defaultTab(): string
    {
        return (string) array_key_first(self::tabs());
    }

    public static function hasTab(string $tab): bool
    {
        return array_key_exists($tab, self::tabs());
    }
}
