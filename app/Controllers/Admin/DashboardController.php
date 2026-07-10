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
                'icon' => 'dashicons dashicons-book',
            ],
            'courses_published' => [
                'title' => __('Kursus Terbit'),
                'value' => $publishedCourses,
                'icon' => 'dashicons dashicons-book',
            ],
            'courses_draft' => [
                'title' => __('Kursus Draf'),
                'value' => $draftCourses,
                'icon' => 'dashicons dashicons-book',
            ],
            'students' => [
                'title' => __('Total Siswa'),
                'value' => (int) ($roleCounts['ecoursity_student'] ?? 0),
                'icon' => 'dashicons dashicons-admin-users',
            ],
            'instructors' => [
                'title' => __('Total Guru'),
                'value' => (int) ($roleCounts['ecoursity_instructor'] ?? 0),
                'icon' => 'dashicons dashicons-admin-users',
            ],
        ];

        $list_newest_courses = Course::all();

        return TemplateService::view('pages/admin/dashboard', compact('stats', 'list_newest_courses'));
    }
}
