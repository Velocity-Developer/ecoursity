<?php

declare(strict_types=1);

namespace Ecoursity\App\Shortcodes;

use Ecoursity\App\Template;

defined('ABSPATH') || exit;

class InstructorsArchiveShortcode
{
    public static function render(array|string $atts = []): string
    {
        ob_start();
        echo Template::view('pages/public/archive-instructor');

        return (string) ob_get_clean();
    }
}
