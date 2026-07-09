<?php

namespace Ecoursity\App\Services;

class TemplateService
{
    public static function view(string $view, array $data = [])
    {
        extract($data);

        $file = ECOURSITY_PATH .
            '/templates/' .
            str_replace('.', '/', $view) .
            '.php';

        require $file;
    }
}
