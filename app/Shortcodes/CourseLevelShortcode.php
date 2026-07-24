<?php

declare(strict_types=1);

namespace Ecoursity\App\Shortcodes;

defined('ABSPATH') || exit;

class CourseLevelShortcode extends CourseSingleShortcodeSupport
{
    public static function render(array|string $atts = []): string
    {
        $course = self::resolveCourse($atts, 'ecoursity-course-level');

        return $course ? esc_html(self::formatLevel($course->level)) : '';
    }
}
