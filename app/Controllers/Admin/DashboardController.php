<?php

namespace Ecoursity\App\Controllers\Admin;

use Ecoursity\App\Services\Admin\DashboardService;
use Ecoursity\App\Template;

class DashboardController
{
    public function index(): void
    {
        wp_enqueue_script(
            'chartjs',
            'https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js',
            [],
            '4.4.7',
            true
        );

        $dashboard = new DashboardService();
        $stats = $dashboard->stats();
        $chartData = $dashboard->purchaseChartData();
        $list_newest_courses = $dashboard->newestCourses();

        Template::view('pages/admin/dashboard', compact('stats', 'chartData', 'list_newest_courses'));
    }
}
