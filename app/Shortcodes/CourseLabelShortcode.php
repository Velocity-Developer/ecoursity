<?php

declare(strict_types=1);

namespace Ecoursity\App\Shortcodes;

defined('ABSPATH') || exit;

class CourseLabelShortcode extends CourseSingleShortcodeSupport
{
    public static function render(array|string $atts = []): string
    {
        $course = self::resolveCourse($atts, 'ecoursity-course-label');

        return $course ? esc_html(self::categoryLabel($course)) : '';
    }
}
