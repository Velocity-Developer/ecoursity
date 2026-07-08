<?php

namespace Ecoursity\App;

use Ecoursity\App\Providers\PostTypeProvider;
use Ecoursity\App\Providers\TaxonomyProvider;

class Application
{
    public function boot(): void
    {
        (new PostTypeProvider())->boot();
        (new TaxonomyProvider())->boot();
    }
}
