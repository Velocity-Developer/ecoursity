<?php

namespace Ecoursity\App\Http\Controllers\Admin;

use Ecoursity\App\Helpers\LayoutHelper;

class DashboardController
{
    public function index()
    {
        $stats = [
            'courses' => [
                'title' => __('Total Kursus'),
                'value' => 15,
            ],
            'courses_published' => [
                'title' => __('Kursus Terbit'),
                'value' => 5,
            ],
            'courses_draft' => [
                'title' => __('Kursus Draf'),
                'value' => 3,
            ],
            'students' => [
                'title' => __('Total Siswa'),
                'value' => 235,
            ],
            'instructors' => [
                'title' => __('Total Guru'),
                'value' => 12,
            ],
        ];

        return LayoutHelper::view('admin.dashboard', compact('stats'));
    }
}
