<?php

namespace Ecoursity\App\Controllers\Admin;

use Ecoursity\App\Models\Course;
use Ecoursity\App\Services\TemplateService;

class DashboardController
{
    public function index()
    {
        $courseCounts = wp_count_posts(Course::POST_TYPE);
        $userCounts = count_users();
        $roleCounts = $userCounts['avail_roles'] ?? [];

        $publishedCourses = (int) ($courseCounts->publish ?? 0);
        $draftCourses = (int) ($courseCounts->draft ?? 0);

        $stats = [
            'courses' => [
                'title' => __('Total Kursus'),
                'value' => $publishedCourses + $draftCourses,
            ],
            'courses_published' => [
                'title' => __('Kursus Terbit'),
                'value' => $publishedCourses,
            ],
            'courses_draft' => [
                'title' => __('Kursus Draf'),
                'value' => $draftCourses,
            ],
            'students' => [
                'title' => __('Total Siswa'),
                'value' => (int) ($roleCounts['ecoursity_student'] ?? 0),
            ],
            'instructors' => [
                'title' => __('Total Guru'),
                'value' => (int) ($roleCounts['ecoursity_instructor'] ?? 0),
            ],
        ];

        return TemplateService::view('admin/dashboard', compact('stats'));
    }
}
