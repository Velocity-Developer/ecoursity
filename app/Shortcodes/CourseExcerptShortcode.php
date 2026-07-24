<?php

declare(strict_types=1);

namespace Ecoursity\App\Shortcodes;

defined('ABSPATH') || exit;

class CourseExcerptShortcode extends CourseSingleShortcodeSupport
{
    public static function render(array|string $atts = []): string
    {
        $course = self::resolveCourse($atts, 'ecoursity-course-excerpt');

        if (!$course || $course->excerpt === '') {
            return '';
        }

        return '<p class="ecoursity-course-card__excerpt">' . esc_html($course->excerpt) . '</p>';
    }
}
