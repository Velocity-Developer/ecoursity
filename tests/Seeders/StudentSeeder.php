<?php

namespace Ecoursity\Tests\Seeders;

use Ecoursity\App\Models\Student;
use Faker\Factory;
use Faker\Generator;
use WP_Error;
use Ecoursity\App\Helpers\Str;

defined('ABSPATH') || exit;


class StudentSeeder
{
    public static function seed($count = 10)
    {
        $faker = Factory::create('id_ID');

        for ($i = 0; $i < $count; $i++) {

            $first_name = $faker->firstName();
            $last_name = $faker->lastName();
            $name = Str::slug($first_name . ' ' . $last_name);
            $username = Str::slug($first_name . ' ' . $last_name);

            // Menggunakan fungsi bawaan WordPress untuk create user student
            wp_insert_user([
                'user_login' => $username,
                'user_email' => $username . '@ecoursity-student-example.com',
                'user_pass' => wp_generate_password(),
                'role' => 'ecoursity_student',
                'first_name' => $first_name,
                'last_name' => $last_name,
                'nickname' => $first_name,
            ]);
        }
    }
}
