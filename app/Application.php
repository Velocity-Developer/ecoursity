<?php

namespace Ecoursity\App;

use Ecoursity\App\Providers\PostTypeProvider;
use Ecoursity\App\Providers\TaxonomyProvider;
use Ecoursity\App\Providers\AdminServiceProvider;
use Ecoursity\App\Providers\EnqueueProvider;
use Ecoursity\App\Providers\UserServiceProvider;

class Application
{
    public function boot(): void
    {
        (new PostTypeProvider())->boot();
        (new TaxonomyProvider())->boot();
        (new AdminServiceProvider())->register();
        (new EnqueueProvider())->register();
        (new UserServiceProvider())->boot();
    }
}
