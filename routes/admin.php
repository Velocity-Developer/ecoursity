<?php

return [

    [
        'page_title' => 'Ecoursity',
        'menu_title' => 'Ecoursity',
        'capability' => 'manage_options',
        'slug' => 'dashboard-ecoursity',
        'controller' => Ecoursity\App\Http\Controllers\Admin\DashboardController::class,
        'method' => 'index',
        'icon' => 'dashicons-welcome-learn-more',
        'position' => 25,
    ],

];
