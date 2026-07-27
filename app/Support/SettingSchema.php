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
                        'key' => 'brand_logo',
                        'label' => __('Logo Brand', 'ecoursity'),
                        'type' => 'image',
                        'default' => '',
                        'description' => __('Upload logo yang digunakan untuk identitas Ecoursity.', 'ecoursity'),
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
                    [
                        'key' => 'bank_transfer_accounts',
                        'label' => __('Daftar Bank Transfer', 'ecoursity'),
                        'type' => 'repeater',
                        'default' => [],
                        'description' => __('Rekening bank yang ditampilkan untuk metode pembayaran transfer bank.', 'ecoursity'),
                        'button_label' => __('Tambah Bank', 'ecoursity'),
                        'empty_label' => __('Belum ada rekening bank.', 'ecoursity'),
                        'fields' => [
                            [
                                'key' => 'bank',
                                'label' => __('Bank', 'ecoursity'),
                                'type' => 'text',
                                'placeholder' => __('BCA', 'ecoursity'),
                            ],
                            [
                                'key' => 'atasnama',
                                'label' => __('Atas Nama', 'ecoursity'),
                                'type' => 'text',
                                'placeholder' => __('PT Ecoursity Indonesia', 'ecoursity'),
                            ],
                            [
                                'key' => 'norek',
                                'label' => __('No. Rekening', 'ecoursity'),
                                'type' => 'text',
                                'placeholder' => __('1234567890', 'ecoursity'),
                            ],
                        ],
                    ],
                ],
            ],
            'email' => [
                'label' => __('Email', 'ecoursity'),
                'description' => __('Identitas pengirim email dari LMS.', 'ecoursity'),
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
                    [
                        'key' => 'email_template_order_course_student',
                        'label' => __('Template Email Order Course ke Siswa', 'ecoursity'),
                        'type' => 'editor',
                        'default' => self::defaultOrderCourseStudentTemplate(),
                        'description' => __('Template email yang dikirim ke siswa setelah order kursus dibuat.', 'ecoursity'),
                    ],
                    [
                        'key' => 'email_template_order_course_instructor',
                        'label' => __('Template Email Order Course ke Instruktur', 'ecoursity'),
                        'type' => 'editor',
                        'default' => self::defaultOrderCourseInstructorTemplate(),
                        'description' => __('Template email yang dikirim ke instruktur saat ada order kursus baru.', 'ecoursity'),
                    ],
                    [
                        'key' => 'email_template_register_user',
                        'label' => __('Template Email Register User Baru', 'ecoursity'),
                        'type' => 'editor',
                        'default' => self::defaultRegisterUserTemplate(),
                        'description' => __('Template email sambutan untuk user baru setelah registrasi.', 'ecoursity'),
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

    private static function defaultOrderCourseStudentTemplate(): string
    {
        return '<p>Halo {student_name},</p>'
            . '<p>Order kursus <strong>{course_title}</strong> berhasil dibuat.</p>'
            . '<p>Nomor order: <strong>{order_number}</strong></p>'
            . '<p>Terima kasih telah belajar bersama kami.</p>';
    }

    private static function defaultOrderCourseInstructorTemplate(): string
    {
        return '<p>Halo {instructor_name},</p>'
            . '<p>Ada order baru untuk kursus <strong>{course_title}</strong>.</p>'
            . '<p>Siswa: <strong>{student_name}</strong></p>'
            . '<p>Nomor order: <strong>{order_number}</strong></p>';
    }

    private static function defaultRegisterUserTemplate(): string
    {
        return '<p>Halo {user_name},</p>'
            . '<p>Selamat datang di {site_name}. Akun Anda sudah berhasil dibuat.</p>'
            . '<p>Silakan login dan mulai belajar.</p>';
    }
}
