<?php

namespace Ecoursity\App\Providers;

class EnqueueProvider
{

    private $resource_uri;

    public function __construct()
    {
        $this->resource_uri = ECOURSITY_URL . 'resources/css/';
    }

    public function register()
    {
        add_action('admin_enqueue_scripts', function () {
            wp_enqueue_style('ecoursity-admin-style', $this->resource_uri . '/ecoursity-admin.css');
        });
    }
}
