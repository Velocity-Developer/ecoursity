<?php

namespace Ecoursity\App\Providers;

class AdminServiceProvider
{
    public function register()
    {
        $routes = require ECOURSITY_PATH . '/routes/admin.php';

        add_action('admin_menu', function () use ($routes) {
            foreach ($routes as $route) {

                add_menu_page(

                    $route['page_title'],
                    $route['menu_title'],
                    $route['capability'],
                    $route['slug'],

                    function () use ($route) {

                        $controller = new $route['controller'];

                        call_user_func([
                            $controller,
                            $route['method']
                        ]);
                    },

                    $route['icon'],
                    $route['position']
                );
            }
        });
    }
}
