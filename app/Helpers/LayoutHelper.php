<?php

namespace Ecoursity\App\Helpers;

class LayoutHelper
{
    public static function view(string $view, array $data = [])
    {
        extract($data);

        $file = ECOURSITY_PATH .
            '/resources/views/' .
            str_replace('.', '/', $view) .
            '.php';

        require $file;
    }
}
