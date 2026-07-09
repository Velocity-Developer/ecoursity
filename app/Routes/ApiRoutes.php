<?php

namespace Ecoursity\App\Routes;

use Ecoursity\App\Controllers\Admin\DashboardController;

class ApiRoutes
{

    public function routes(): array
    {
        return [
            [
                'route' => '/admin/dashboard',
                'callback' => [DashboardController::class, 'index'],
                'methods' => 'GET',
            ],
        ];
    }
}
