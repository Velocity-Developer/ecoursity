<?php

namespace Ecoursity\App\Routes;


class AdminRoutes
{

    public function routes(): array
    {
        return [
            [
                'page_title' => 'Ecoursity',
                'menu_title' => 'Ecoursity',
                'capability' => 'manage_options',
                'slug' => 'ecoursity-dashboard',
                'view' => 'admin/dashboard',
                'icon' => 'dashicons-welcome-learn-more',
                'position' => 25,
            ],
            [
                'page_title' => 'Siswa Ecoursity',
                'menu_title' => 'Siswa',
                'capability' => 'manage_options',
                'slug' => 'ecoursity-student',
                'view' => 'admin/student',
                'parent_slug' => 'ecoursity-dashboard',
            ],

        ];
    }
}
