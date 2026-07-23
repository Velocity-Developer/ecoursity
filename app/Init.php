<?php

namespace Ecoursity\App;

use Ecoursity\App\Providers\PostTypeProvider;
use Ecoursity\App\Providers\TaxonomyProvider;
use Ecoursity\App\Providers\EnqueueProvider;
use Ecoursity\App\Providers\UserServiceProvider;
use Ecoursity\App\Providers\MetaboxPostProvider;
use Ecoursity\App\Providers\TemplateProvider;
use Ecoursity\App\Routes\AdminRoutes;
use Ecoursity\App\Routes\ApiRoutes;
use Ecoursity\App\Shortcode;

class Init
{
    public function boot(): void
    {
        (new PostTypeProvider())->boot();
        (new TaxonomyProvider())->boot();
        (new EnqueueProvider())->register();
        (new UserServiceProvider())->boot();
        (new AdminRoutes())->register();
        (new MetaboxPostProvider())->boot();
        (new ApiRoutes())->boot();
        (new TemplateProvider())->boot();
        (new Shortcode())->boot();
    }
}
