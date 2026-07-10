<?php

namespace Ecoursity\App;

use Ecoursity\App\Providers\PostTypeProvider;
use Ecoursity\App\Providers\TaxonomyProvider;
use Ecoursity\App\Providers\EnqueueProvider;
use Ecoursity\App\Providers\UserServiceProvider;
use Ecoursity\App\Providers\RestApiProvider;
use Ecoursity\App\Providers\MetaboxPostProvider;
use Ecoursity\App\Routes\AdminRoutes;

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
    }
}
