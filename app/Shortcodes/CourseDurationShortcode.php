<?php

declare(strict_types=1);

namespace Ecoursity\App\Shortcodes;

defined('ABSPATH') || exit;

class CourseDurationShortcode extends CourseSingleShortcodeSupport
{
    public static function render(array|string $atts = []): string
    {
        $course = self::resolveCourse($atts, 'ecoursity-course-duration');

        return $course ? esc_html(self::formatDuration($course->duration)) : '';
    }
}
