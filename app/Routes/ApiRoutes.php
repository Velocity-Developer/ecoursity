<?php

namespace Ecoursity\App\Routes;

use Ecoursity\App\Controllers\Admin\DashboardController;

class ApiRoutes
{
    public function namespace(): string
    {
        return 'ecoursity/v1';
    }

    public function routes(): array
    {
        return [
            [
                'route' => '/admin/dashboard',
                'callback' => [DashboardController::class, 'index'],
                'methods' => 'GET',
                'permission_callback' => static fn(): bool => current_user_can('manage_options'),
            ],
        ];
    }
}
