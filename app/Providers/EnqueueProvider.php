<?php

namespace Ecoursity\App\Providers;

class EnqueueProvider
{
    private string $resourceUri;

    public function __construct()
    {
        $this->resourceUri = ECOURSITY_URL . 'resources/css/';
    }

    public function register(): void
    {
        add_action('admin_enqueue_scripts', function (): void {
            wp_enqueue_style('ecoursity-admin-style', $this->resourceUri . '/ecoursity-admin.css');
            wp_enqueue_script(
                'alpinejs',
                'https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js',
                [],
                '3.14.9',
                true
            );
        });

        add_action('wp_enqueue_scripts', function (): void {
            wp_enqueue_style('ecoursity-style', $this->resourceUri . '/ecoursity-public.css');
            wp_enqueue_script(
                'alpinejs',
                'https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js',
                [],
                '3.14.9',
                true
            );
        });
    }
}
