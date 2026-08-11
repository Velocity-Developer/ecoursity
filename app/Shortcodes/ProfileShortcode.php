<?php

declare(strict_types=1);

namespace Ecoursity\App\Shortcodes;

use Ecoursity\App\Template;

defined('ABSPATH') || exit;

class ProfileShortcode
{
    public static function render(array|string $atts = []): string
    {
        ob_start();
        Template::view('pages/public/profile');

        return (string) ob_get_clean();
    }
}
