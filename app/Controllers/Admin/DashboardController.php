<?php

namespace Ecoursity\App\Controllers\Admin;

use Ecoursity\App\Services\TemplateService;

class DashboardController
{
    public function index()
    {
        $stats = [
            'courses' => [
                'title' => __('Total Kursus'),
                'value' => 0,
            ],
            'courses_published' => [
                'title' => __('Kursus Terbit'),
                'value' => 0,
            ],
            'courses_draft' => [
                'title' => __('Kursus Draf'),
                'value' => 0,
            ],
            'students' => [
                'title' => __('Total Siswa'),
                'value' => 0,
            ],
            'instructors' => [
                'title' => __('Total Guru'),
                'value' => 0,
            ],
        ];

        return TemplateService::view('admin/dashboard', compact('stats'));
    }
}
