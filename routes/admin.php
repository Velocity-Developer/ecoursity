<?php

return [

    [
        'page_title' => 'Ecoursity',
        'menu_title' => 'Ecoursity',
        'capability' => 'manage_options',
        'slug' => 'ecoursity-dashboard',
        'controller' => Ecoursity\App\Http\Controllers\Admin\DashboardController::class,
        'method' => 'index',
        'icon' => 'dashicons-welcome-learn-more',
        'position' => 25,
    ],
    [
        'page_title' => 'Siswa Ecoursity',
        'menu_title' => 'Siswa',
        'capability' => 'manage_options',
        'slug' => 'ecoursity-student',
        'controller' => Ecoursity\App\Http\Controllers\Admin\StudentController::class,
        'method' => 'index',
        'icon' => 'dashicons-admin-users',
        'position' => 25,
    ],

];
