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
                'permission_callback' => static fn(): bool => current_user_can('manage_options'),
            ],
        ];
    }

    public function register(): void
    {
        add_action('rest_api_init', function () {
            foreach ($this->routes() as $route) {
                register_rest_route($this->namespace(), $route['route'], [
                    'methods' => $route['methods'],
                    'callback' => $route['callback'],
                    'permission_callback' => $route['permission_callback'],
                ]);
            }
        });
    }
}
