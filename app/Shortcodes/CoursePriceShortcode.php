<?php

declare(strict_types=1);

namespace Ecoursity\App\Shortcodes;

defined('ABSPATH') || exit;

class CoursePriceShortcode extends CourseSingleShortcodeSupport
{
    public static function render(array|string $atts = []): string
    {
        $course = self::resolveCourse($atts, 'ecoursity-course-price');

        if (!$course) {
            return '';
        }

        $saleActive = self::hasPositivePrice($course->price_sale);
        $mainPrice = $saleActive ? self::formatPrice($course->price_sale) : self::formatPrice($course->price);
        $regularPrice = $saleActive ? self::formatPrice($course->price) : '';

        ob_start();
        ?>
        <div class="ecoursity-course-card__price">
            <strong><?php echo esc_html($mainPrice); ?></strong>
            <?php if ($regularPrice !== '') : ?>
                <del><?php echo esc_html($regularPrice); ?></del>
            <?php endif; ?>
        </div>
        <?php

        return (string) ob_get_clean();
    }
}
