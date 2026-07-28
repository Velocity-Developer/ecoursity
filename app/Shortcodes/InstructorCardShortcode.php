<?php

declare(strict_types=1);

namespace Ecoursity\App\Shortcodes;

use Ecoursity\App\Models\Instructor;
use Ecoursity\App\Template;

defined('ABSPATH') || exit;

class InstructorCardShortcode
{
    public static function render(array|string $atts = []): string
    {
        $attributes = shortcode_atts(
            ['instructor_id' => 0],
            is_array($atts) ? $atts : [],
            'ecoursity-instructor-card'
        );

        $instructorId = absint($attributes['instructor_id']);

        if ($instructorId < 1) {
            return '';
        }

        $instructor = Instructor::find($instructorId);

        if (!$instructor) {
            return '';
        }

        ob_start();
        Template::view('pages/public/content-instructor', ['instructor' => $instructor]);

        return (string) ob_get_clean();
    }
}
