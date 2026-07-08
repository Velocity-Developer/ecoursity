<?php

namespace Ecoursity\App\Http\Controllers\Admin;

use Ecoursity\App\Helpers\LayoutHelper;

class DashboardController
{
    public function index()
    {
        $stats = [
            'courses' => 15,
            'students' => 235,
            'teachers' => 12,
        ];

        return LayoutHelper::view('admin.dashboard', compact('stats'));
    }
}
