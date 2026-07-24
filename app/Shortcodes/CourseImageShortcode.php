<?php

declare(strict_types=1);

namespace Ecoursity\App\Shortcodes;

defined('ABSPATH') || exit;

class CourseImageShortcode extends CourseSingleShortcodeSupport
{
    public static function render(array|string $atts = []): string
    {
        $attributes = shortcode_atts(
            [
                'course_id' => 0,
                'size' => 'large',
                'ratio' => '16/9',
            ],
            is_array($atts) ? $atts : [],
            'ecoursity-course-image'
        );

        $course = self::resolveCourse($attributes, 'ecoursity-course-image');

        if (!$course) {
            return '';
        }

        $size = sanitize_key((string) $attributes['size']);
        $ratio = self::sanitizeRatio((string) $attributes['ratio']);
        $image = get_the_post_thumbnail(
            (int) $course->id,
            $size ?: 'large',
            ['class' => 'ecoursity-course-image__img']
        );

        ob_start();
        ?>
        <figure class="ecoursity-course-image" style="--ecoursity-course-image-ratio: <?php echo esc_attr($ratio); ?>;">
            <?php if ($image !== '') : ?>
                <?php echo wp_kses_post($image); ?>
            <?php else : ?>
                <span class="ecoursity-course-card__image-placeholder" aria-hidden="true"></span>
            <?php endif; ?>
        </figure>
        <?php

        return (string) ob_get_clean();
    }

    private static function sanitizeRatio(string $ratio): string
    {
        $ratio = trim(str_replace(':', '/', $ratio));

        if (!preg_match('/^\d+(?:\.\d+)?\s*\/\s*\d+(?:\.\d+)?$/', $ratio)) {
            return '16 / 9';
        }

        [$width, $height] = array_map('trim', explode('/', $ratio, 2));

        if ((float) $width <= 0 || (float) $height <= 0) {
            return '16 / 9';
        }

        return $width . ' / ' . $height;
    }
}
