<?php

namespace Ecoursity\Tests;

// 1. Load Composer Autoloader (Termasuk Autoload-Dev)
require_once dirname(__DIR__) . '/vendor/autoload.php';

if (PHP_SAPI === 'cli') {
    $_SERVER['REQUEST_SCHEME'] ??= 'http';
    $_SERVER['HTTP_HOST'] ??= 'localhost';
}

// 2. Load Lingkungan WordPress (Ubah path jika WP Anda di folder berbeda)
// Skrip ini berasumsi plugin berada di wp-content/plugins/nama-plugin/
$wp_load_path = dirname(__DIR__, 4) . '/wp-load.php';

if (!file_exists($wp_load_path)) {
    echo "Error: wp-load.php tidak ditemukan. Pastikan plugin berada di folder WordPress.\n";
    exit(1);
}

require_once $wp_load_path;

// 3. Panggil Class Seeder Anda
use Ecoursity\Tests\Seeders\StudentSeeder;
use Ecoursity\Tests\Seeders\InstructorSeeder;

echo "Memulai proses seeding data...\n";
StudentSeeder::seed(10);
InstructorSeeder::seed(10);
echo "Seeding selesai dengan sukses!\n";
