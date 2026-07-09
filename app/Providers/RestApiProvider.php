<?php

namespace Ecoursity\App\Providers;

use Ecoursity\App\Routes\ApiRoutes;

class RestApiProvider
{
    private $prefix = 'ecoursity';

    public function register()
    {
        add_action('rest_api_init', function () {

            $routes = (new ApiRoutes())->routes();
            foreach ($routes as $route) {
                register_rest_route($this->prefix, $route['route'], [
                    'methods' => $route['methods'],
                    'callback' => $route['callback'],
                ]);
            }
        });
    }
}
