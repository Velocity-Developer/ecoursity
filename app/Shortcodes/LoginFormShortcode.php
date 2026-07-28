<?php

declare(strict_types=1);

namespace Ecoursity\App\Shortcodes;

use Ecoursity\App\Template;

defined('ABSPATH') || exit;

class LoginFormShortcode
{
    public static function render(array|string $atts = []): string
    {
        if (is_user_logged_in()) {
            return 'Already logged in.';
        }

        $attributes = shortcode_atts(
            [
                'redirect' => '',
                'class' => '',
            ],
            is_array($atts) ? $atts : [],
            'ecoursity-form-login'
        );

        ob_start();
        echo Template::component('LoginForm', [
            'redirect' => trim((string) $attributes['redirect']),
            'class' => (string) $attributes['class'],
        ]);

        return (string) ob_get_clean();
    }
}
