<?php

declare(strict_types=1);

namespace Ecoursity\App\Shortcodes;

use Ecoursity\App\Models\Course;

defined('ABSPATH') || exit;

class ButtonBuyCourseShortcode
{
    public static function render(array|string $atts = []): string
    {
        $attributes = shortcode_atts(
            [
                'course_id' => 0,
                'label' => '',
                'login_label' => __('Login untuk Beli Course', 'ecoursity'),
                'free_label' => __('Ambil Course Gratis', 'ecoursity'),
                'class' => '',
                'url' => '',
                'require_login' => 'yes',
            ],
            is_array($atts) ? $atts : [],
            'ecoursity-button-buy-course'
        );

        $course = self::resolveCourse(absint($attributes['course_id']));

        if (!$course || !$course->id) {
            return '';
        }

        $requiresLogin = self::truthy((string) $attributes['require_login']);
        $isLoggedIn = is_user_logged_in();
        $isFree = self::effectivePrice($course) <= 0;
        $currentUrl = get_permalink((int) $course->id);
        $targetUrl = trim((string) $attributes['url']);

        if ($requiresLogin && !$isLoggedIn) {
            $targetUrl = wp_login_url($currentUrl);
            $label = (string) $attributes['login_label'];
        } else {
            $targetUrl = $targetUrl !== '' ? $targetUrl : self::buyUrl($course);
            $label = (string) ($attributes['label'] ?: ($isFree ? $attributes['free_label'] : __('Beli Course', 'ecoursity')));
        }

        $classes = self::classes((string) $attributes['class']);

        return sprintf(
            '<a href="%s" class="%s" data-ecoursity-buy-course data-course-id="%d" data-course-price="%s">%s</a>',
            esc_url($targetUrl),
            esc_attr($classes),
            (int) $course->id,
            esc_attr((string) self::effectivePrice($course)),
            esc_html($label)
        );
    }

    private static function resolveCourse(int $courseId): ?Course
    {
        if ($courseId > 0) {
            return Course::find($courseId);
        }

        $postId = get_the_ID();

        if ($postId < 1) {
            return null;
        }

        return Course::find((int) $postId);
    }

    private static function effectivePrice(Course $course): float
    {
        $salePrice = (float) $course->price_sale;

        if ($salePrice > 0) {
            return $salePrice;
        }

        return (float) $course->price;
    }

    private static function buyUrl(Course $course): string
    {
        $defaultUrl = add_query_arg(
            [
                'ecoursity_buy_course' => (int) $course->id,
            ],
            get_permalink((int) $course->id)
        );

        return (string) apply_filters('ecoursity_buy_course_url', $defaultUrl, $course);
    }

    private static function truthy(string $value): bool
    {
        return in_array(strtolower($value), ['1', 'true', 'yes', 'on'], true);
    }

    private static function classes(string $class): string
    {
        $classes = array_filter(array_map(
            'sanitize_html_class',
            preg_split('/\s+/', trim($class)) ?: []
        ));

        array_unshift($classes, 'ecoursity-buy-course-button');

        return implode(' ', array_unique($classes));
    }
}
