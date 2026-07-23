<?php

namespace Ecoursity\App;

use WP_Error;

class Template
{
    public static function view(string $view, array $data = [])
    {
        extract($data);

        $file = ECOURSITY_PATH .
            'templates/' .
            str_replace('.', '/', $view) .
            '.php';

        //if file not exists
        if (!file_exists($file)) {
            return false;
        }

        require $file;
    }

    public static function get(string $view)
    {

        return ECOURSITY_PATH .
            'templates/' .
            str_replace('.', '/', $view) .
            '.php';
    }

    public static function component(string $component, array $data = [])
    {
        extract($data);

        $file = ECOURSITY_PATH .
            'templates/components/' .
            str_replace('.', '/', $component) .
            '.php';

        //if file not exists
        if (!file_exists($file)) {
            return false;
        }

        require $file;
    }
}
