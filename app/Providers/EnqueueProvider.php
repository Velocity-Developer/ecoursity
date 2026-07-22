<?php

namespace Ecoursity\App\Providers;

use Ecoursity\App\Template;

class EnqueueProvider
{
    private string $resourceUri;

    public function __construct()
    {
        $this->resourceUri = ECOURSITY_URL . 'assets/';
    }

    public function register(): void
    {
        add_action('admin_enqueue_scripts', function (): void {
            wp_enqueue_editor();
            wp_enqueue_media();

            wp_enqueue_style('ecoursity-main-style', $this->resourceUri . 'css/ecoursity-main.css');
            wp_enqueue_style('ecoursity-admin-style', $this->resourceUri . 'css/ecoursity-admin.css');
            wp_enqueue_script(
                'alpinejs',
                'https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js',
                [],
                '3.14.9',
                true
            );
            wp_enqueue_script(
                'ecoursity-main-script',
                $this->resourceUri . 'js/ecoursity-main.js',
                ['alpinejs', 'editor', 'quicktags'],
                null,
                true
            );
            wp_localize_script('ecoursity-main-script', 'ecoursity', [
                'restNonce' => wp_create_nonce('wp_rest'),
            ]);
        });

        add_action('wp_enqueue_scripts', function (): void {
            wp_enqueue_style('ecoursity-main-style', $this->resourceUri . 'css/ecoursity-main.css');
            wp_enqueue_style('ecoursity-public-style', $this->resourceUri . 'css/ecoursity-public.css');
            wp_enqueue_script(
                'alpinejs',
                'https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js',
                [],
                '3.14.9',
                true
            );
            wp_enqueue_script(
                'ecoursity-main-script',
                $this->resourceUri . 'js/ecoursity-main.js',
                ['alpinejs'],
                null,
                true
            );
            wp_localize_script('ecoursity-main-script', 'ecoursity', [
                'restNonce' => wp_create_nonce('wp_rest'),
            ]);
        });

        add_action('admin_footer', function (): void {
            Template::component('UiModal');
        });
    }
}
