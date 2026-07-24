<?php

declare(strict_types=1);

namespace Ecoursity\App\Shortcodes;

use Ecoursity\App\Models\Course;
use Ecoursity\App\Models\Section;

defined('ABSPATH') || exit;

abstract class CourseSingleShortcodeSupport
{
    protected static function resolveCourse(array|string $atts, string $shortcode): ?Course
    {
        $attributes = shortcode_atts(
            ['course_id' => 0],
            is_array($atts) ? $atts : [],
            $shortcode
        );

        $courseId = absint($attributes['course_id']);

        if ($courseId > 0) {
            return Course::find($courseId);
        }

        $postId = get_the_ID();

        return $postId > 0 ? Course::find((int) $postId) : null;
    }

    protected static function formatPrice(string $price): string
    {
        $normalized = trim($price);

        $numericValue = self::normalizePrice($normalized);

        if ($numericValue === null || $numericValue <= 0) {
            return __('Gratis', 'ecoursity');
        }

        return function_exists('number_format_i18n')
            ? 'Rp ' . number_format_i18n($numericValue)
            : 'Rp ' . number_format($numericValue, 0, ',', '.');
    }

    protected static function hasPositivePrice(string $price): bool
    {
        $numericValue = self::normalizePrice($price);

        return $numericValue !== null && $numericValue > 0;
    }

    protected static function normalizePrice(string $price): ?float
    {
        $numericValue = preg_replace('/[^\d.,]/', '', trim($price));

        if (!is_string($numericValue) || $numericValue === '') {
            return null;
        }

        if (str_contains($numericValue, ',') && str_contains($numericValue, '.')) {
            $numericValue = str_replace('.', '', str_replace(',', '.', $numericValue));
        } elseif (str_contains($numericValue, '.')) {
            $parts = explode('.', $numericValue);
            $hasThousandsGroups = count($parts) > 1
                && array_reduce(
                    array_slice($parts, 1),
                    static fn(bool $valid, string $part): bool => $valid && strlen($part) === 3,
                    true
                );

            $numericValue = $hasThousandsGroups ? str_replace('.', '', $numericValue) : $numericValue;
        } else {
            $numericValue = str_replace(',', '.', $numericValue);
        }

        return is_numeric($numericValue) ? (float) $numericValue : null;
    }

    protected static function formatDuration(mixed $duration): string
    {
        if (!is_array($duration) || empty($duration[0]) || empty($duration[1])) {
            return __('Akses fleksibel', 'ecoursity');
        }

        $units = [
            'day' => __('hari', 'ecoursity'),
            'week' => __('minggu', 'ecoursity'),
            'month' => __('bulan', 'ecoursity'),
            'year' => __('tahun', 'ecoursity'),
            'hour' => __('jam', 'ecoursity'),
            'minute' => __('menit', 'ecoursity'),
        ];

        $amount = absint($duration[0]);
        $unit = sanitize_key((string) $duration[1]);

        return sprintf(
            '%s %s',
            number_format_i18n($amount > 0 ? $amount : 1),
            $units[$unit] ?? $unit
        );
    }

    protected static function formatLevel(string $level): string
    {
        return [
            'all' => __('Semua level', 'ecoursity'),
            'beginner' => __('Pemula', 'ecoursity'),
            'intermediate' => __('Menengah', 'ecoursity'),
            'advanced' => __('Lanjutan', 'ecoursity'),
            'expert' => __('Master', 'ecoursity'),
        ][$level] ?? __('Semua level', 'ecoursity');
    }

    protected static function formatLessonCount(int $count): string
    {
        return sprintf(
            _n('%s materi', '%s materi', $count, 'ecoursity'),
            number_format_i18n($count)
        );
    }

    /**
     * @param array<int, Section> $sections
     */
    protected static function countLessons(array $sections): int
    {
        return array_reduce(
            $sections,
            static fn(int $total, Section $section): int => $total + count($section->items),
            0
        );
    }

    protected static function lessonCount(Course $course): int
    {
        return self::countLessons(Section::allByCourse((int) $course->id));
    }

    protected static function categoryLabel(Course $course): string
    {
        $categories = wp_get_post_terms((int) $course->id, 'ecoursity_course_category', ['fields' => 'names']);

        if (is_wp_error($categories) || empty($categories)) {
            return __('Kursus Online', 'ecoursity');
        }

        return implode(', ', array_map('sanitize_text_field', $categories));
    }

    protected static function authorName(Course $course): string
    {
        $author = get_userdata((int) $course->author);

        return $author ? $author->display_name : get_the_author();
    }

    protected static function faqs(Course $course): array
    {
        return array_filter(
            $course->faqs,
            static fn(array $faq): bool => !empty($faq['question']) || !empty($faq['answer'])
        );
    }
}
