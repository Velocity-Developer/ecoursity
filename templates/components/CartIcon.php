<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

$count = isset($count) ? absint($count) : 0;
$url = isset($url) ? (string) $url : '#';
$label = isset($label) && (string) $label !== '' ? (string) $label : __('Cart', 'ecoursity');
$classes = isset($classes) ? (string) $classes : 'ecoursity-cart-icon';
?>

<a
    href="<?php echo esc_url($url); ?>"
    class="<?php echo esc_attr($classes); ?>"
    x-data
    x-bind:aria-label="'<?php echo esc_js($label); ?> (' + ($store.EcoursityCart?.count ?? <?php echo esc_js((string) $count); ?>) + ')'"
    aria-label="<?php echo esc_attr(sprintf('%s (%d)', $label, $count)); ?>"
>
    <span class="ecoursity-cart-icon__glyph" aria-hidden="true">
        <svg viewBox="0 0 24 24" focusable="false">
            <path d="M7 18c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2Zm10 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2ZM6.2 6l.9 4.6c.2 1 1.1 1.8 2.1 1.8h6.9c.9 0 1.7-.5 2-1.4L20 6H6.2ZM4.5 2H2v2h1.2c.5 0 .9.3 1 .8l1.8 9c.3 1.5 1.6 2.6 3.1 2.6H18v-2H9.2c-.6 0-1.1-.4-1.2-1L7.8 12h8.3c1.8 0 3.4-1.1 4-2.8L22 4H5L4.5 2Z" />
        </svg>
    </span>
    <span
        class="ecoursity-cart-icon__count"
        x-text="$store.EcoursityCart?.count ?? <?php echo esc_attr((string) $count); ?>"
    ><?php echo esc_html((string) $count); ?></span>
</a>
