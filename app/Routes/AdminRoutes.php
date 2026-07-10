<?php

namespace Ecoursity\App\Routes;

use Ecoursity\App\Controllers\Admin\DashboardController;
use Ecoursity\App\Controllers\Admin\StudentController;

class AdminRoutes
{

    public function register()
    {

        add_action('admin_menu', function () {
            foreach ($this->routes() as $route) {
                $callback = function () use ($route) {
                    $controller = new $route['controller'];
                    call_user_func([
                        $controller,
                        $route['method']
                    ]);
                };

                if (isset($route['parent_slug'])) {
                    add_submenu_page(
                        $route['parent_slug'],
                        $route['page_title'],
                        $route['menu_title'],
                        'manage_options',
                        $route['slug'],
                        $callback
                    );

                    continue;
                }

                add_menu_page(
                    $route['page_title'],
                    $route['menu_title'],
                    'manage_options',
                    $route['slug'],
                    $callback,
                    $route['icon'],
                    25,
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

    public function routes(): array
    {
        return [
            [
                'page_title' => 'Ecoursity',
                'menu_title' => 'Ecoursity',
                'slug' => 'ecoursity-dashboard',
                'controller' => DashboardController::class,
                'method' => 'index',
                'icon' => 'dashicons-welcome-learn-more',
            ],
            [
                'page_title' => 'Siswa Ecoursity',
                'menu_title' => 'Siswa',
                'slug' => 'ecoursity-student',
                'controller' => StudentController::class,
                'method' => 'index',
                'parent_slug' => 'ecoursity-dashboard',
            ],

        ];
    }
}
