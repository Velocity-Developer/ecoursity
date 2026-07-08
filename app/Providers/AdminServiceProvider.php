<?php

namespace Ecoursity\App\Providers;

class AdminServiceProvider
{
    public function register()
    {
        $routes = require ECOURSITY_PATH . '/routes/admin.php';

        add_action('admin_menu', function () use ($routes) {
            foreach ($routes as $route) {
                $callback = function () use ($route) {
                    $controller = new $route['controller'];

                    call_user_func([
                        $controller,
                        $route['method'],
                    ]);
                };

                if (isset($route['parent_slug'])) {
                    add_submenu_page(
                        $route['parent_slug'],
                        $route['page_title'],
                        $route['menu_title'],
                        $route['capability'],
                        $route['slug'],
                        $callback
                    );

                    continue;
                }

                add_menu_page(
                    $route['page_title'],
                    $route['menu_title'],
                    $route['capability'],
                    $route['slug'],
                    $callback,
                    $route['icon'],
                    $route['position']
                );
            }

            add_submenu_page(
                'ecoursity-dashboard',
                __('Kursus'),
                __('Kursus'),
                'edit_posts',
                'edit.php?post_type=ecoursity_course'
            );
        });
    }
}
