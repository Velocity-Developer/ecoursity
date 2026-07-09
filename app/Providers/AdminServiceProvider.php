<?php

namespace Ecoursity\App\Providers;

use Ecoursity\App\Routes\AdminRoutes;
use Ecoursity\App\Services\TemplateService;

class AdminServiceProvider
{
    public function register()
    {

        add_action('admin_menu', function () {
            foreach ((new AdminRoutes())->routes() as $route) {
                $callback = function () use ($route) {
                    TemplateService::view($route['view']);
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
