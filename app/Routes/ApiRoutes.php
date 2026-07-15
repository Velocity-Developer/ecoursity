<?php

namespace Ecoursity\App\Routes;

use Ecoursity\App\Controllers\CourseController;
use Ecoursity\App\Controllers\LessonController;
use Ecoursity\App\Controllers\SectionController;
use Ecoursity\App\Controllers\TemplateController;

class ApiRoutes
{
    public $namespace_v1 = 'ecoursity/v1';

    public function boot(): void
    {
        add_action('rest_api_init', function () {
            foreach ($this->routes() as $route) {
                $callback = function ($request) use ($route) {
                    if (! $route['callback']) {
                        return;
                    }

                    [$class, $method] = $route['callback'];

                    return call_user_func([
                        new $class(),
                        $method,
                    ], $request);
                };

                register_rest_route($this->namespace_v1, $route['route'], [
                    'methods' => $route['methods'] ?? 'GET',
                    'callback' => $callback,
                    'permission_callback' => $route['permission_callback'] ?? '__return_true',
                ]);
            }
        });
    }

    public function routes(): array
    {
        return [
            [
                'route' => '/template_view/(?P<template>\d+)',
                'callback' => [TemplateController::class, 'view'],
                'methods' => 'GET',
                'permission_callback' => '__return_true',
            ],
            [
                'route' => '/template_component/(?P<component_name>\w+)',
                'callback' => [TemplateController::class, 'component'],
                'methods' => 'GET',
                'permission_callback' => '__return_true',
            ],
            [
                'route' => '/courses/',
                'callback' => [CourseController::class, 'index'],
                'methods' => 'GET',
                'permission_callback' => '__return_true',
            ],
            [
                'route' => '/courses/(?P<id>\d+)',
                'callback' => [CourseController::class, 'show'],
                'methods' => 'GET',
                'permission_callback' => '__return_true',
            ],
            [
                'route' => '/courses/',
                'callback' => [CourseController::class, 'store'],
                'methods' => 'POST',
                'permission_callback' => '__return_true',
            ],
            [
                'route' => '/courses/(?P<id>\d+)',
                'callback' => [CourseController::class, 'update'],
                'methods' => 'PUT',
                'permission_callback' => '__return_true',
            ],
            [
                'route' => '/courses/(?P<id>\d+)',
                'callback' => [CourseController::class, 'delete'],
                'methods' => 'DELETE',
                'permission_callback' => '__return_true',
            ],
            [
                'route' => '/sections/',
                'callback' => [SectionController::class, 'store'],
                'methods' => 'POST',
                'permission_callback' => '__return_true',
            ],
            [
                'route' => '/sections/(?P<id>\d+)',
                'callback' => [SectionController::class, 'update'],
                'methods' => 'PUT',
                'permission_callback' => '__return_true',
            ],
            [
                'route' => '/lessons/',
                'callback' => [LessonController::class, 'index'],
                'methods' => 'GET',
                'permission_callback' => '__return_true',
            ],
            [
                'route' => '/lessons/(?P<id>\d+)',
                'callback' => [LessonController::class, 'show'],
                'methods' => 'GET',
                'permission_callback' => '__return_true',
            ],
            [
                'route' => '/lessons/',
                'callback' => [LessonController::class, 'store'],
                'methods' => 'POST',
                'permission_callback' => fn() => current_user_can('edit_posts'),
            ],
            [
                'route' => '/lessons/(?P<id>\d+)',
                'callback' => [LessonController::class, 'update'],
                'methods' => 'PUT',
                'permission_callback' => fn() => current_user_can('edit_posts'),
            ],
            [
                'route' => '/lessons/(?P<id>\d+)',
                'callback' => [LessonController::class, 'delete'],
                'methods' => 'DELETE',
                'permission_callback' => fn() => current_user_can('delete_posts'),
            ],
        ];
    }
}
