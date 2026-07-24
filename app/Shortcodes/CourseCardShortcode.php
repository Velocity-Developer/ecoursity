<?php

declare(strict_types=1);

namespace Ecoursity\App\Shortcodes;

use Ecoursity\App\Template;

defined('ABSPATH') || exit;

class CourseCardShortcode extends CourseSingleShortcodeSupport
{
    public static function render(array|string $atts = []): string
    {
        $course = self::resolveCourse($atts, 'ecoursity-course-card');

        if (!$course) {
            return '';
        }

        ob_start();
        Template::view('pages/public/content-course', ['course' => $course]);

        return (string) ob_get_clean();
    }
}
