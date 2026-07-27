<?php

declare(strict_types=1);

namespace Ecoursity\App\Shortcodes;

use Ecoursity\App\Models\Cart;
use Ecoursity\App\Template;

defined('ABSPATH') || exit;

class CartIconShortcode
{
    public static function render(array|string $atts = []): string
    {
        $attributes = shortcode_atts(
            [
                'url' => '',
                'label' => __('Cart', 'ecoursity'),
                'class' => '',
            ],
            is_array($atts) ? $atts : [],
            'ecoursity-cart-icon'
        );

        $url = trim((string) $attributes['url']);
        $url = $url !== '' ? $url : self::defaultUrl();

        ob_start();
        Template::component('CartIcon', [
            'count' => Cart::count(),
            'url' => $url,
            'label' => (string) $attributes['label'],
            'classes' => self::classes((string) $attributes['class']),
        ]);

        return (string) ob_get_clean();
    }

    private static function defaultUrl(): string
    {
        $defaultUrl = home_url('/checkout/');

        return (string) apply_filters('ecoursity_cart_icon_url', $defaultUrl);
    }

    private static function classes(string $class): string
    {
        $classes = array_filter(array_map(
            'sanitize_html_class',
            preg_split('/\s+/', trim($class)) ?: []
        ));

        array_unshift($classes, 'ecoursity-cart-icon');

        return implode(' ', array_unique($classes));
    }
}
