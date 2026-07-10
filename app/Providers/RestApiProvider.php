<?php

namespace Ecoursity\App\Providers;

use Ecoursity\App\Routes\ApiRoutes;

class RestApiProvider
{
    public function register(): void
    {
        $apiRoutes = new ApiRoutes();

        add_action('rest_api_init', static function () use ($apiRoutes): void {
            foreach ($apiRoutes->routes() as $route) {
                register_rest_route($apiRoutes->namespace(), $route['route'], [
                    'methods' => $route['methods'],
                    'callback' => $route['callback'],
                    'permission_callback' => $route['permission_callback'],
                ]);
            }
        });
    }
}
