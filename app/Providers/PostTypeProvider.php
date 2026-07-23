<?php

namespace Ecoursity\App\Providers;

use Ecoursity\App\Models\Course;
use Ecoursity\App\Models\Lesson;

class PostTypeProvider
{
    public function boot()
    {
        add_action('init', [$this, 'register']);
    }

    public function register(): void
    {
        register_post_type(Course::POST_TYPE, [
            'labels' => $this->makeLabels(__('Kursus')),
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'kursus'],
            'show_in_menu' => false,
            'show_in_rest' => false,
            'supports' => ['title', 'editor', 'thumbnail', 'comments'],
            'taxonomies' => ['ecoursity_course_category', 'ecoursity_course_tag'],
        ]);

        register_post_type(Lesson::POST_TYPE, [
            'labels' => $this->makeLabels(__('Materi Kursus')),
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'lesson'],
            'show_in_menu' => false,
            'show_in_rest' => false,
            'supports' => ['title', 'editor'],
        ]);

        register_post_meta(Course::POST_TYPE, '_ecoursity_requirements', [
            'type' => 'array',
            'single' => true,
            'show_in_rest' => false,
            'sanitize_callback' => [$this, 'sanitizeTextListMeta'],
            'auth_callback' => [$this, 'canEditPostMeta'],
        ]);

        register_post_meta(Course::POST_TYPE, '_ecoursity_target_audiences', [
            'type' => 'array',
            'single' => true,
            'show_in_rest' => false,
            'sanitize_callback' => [$this, 'sanitizeTextListMeta'],
            'auth_callback' => [$this, 'canEditPostMeta'],
        ]);

        register_post_meta(Lesson::POST_TYPE, '_ecoursity_duration', [
            'type' => 'array',
            'single' => true,
            'show_in_rest' => false,
            'sanitize_callback' => [$this, 'sanitizeLessonDurationMeta'],
            'auth_callback' => [$this, 'canEditPostMeta'],
        ]);

        register_post_meta(Lesson::POST_TYPE, '_ecoursity_preview', [
            'type' => 'boolean',
            'single' => true,
            'show_in_rest' => false,
            'sanitize_callback' => 'rest_sanitize_boolean',
            'auth_callback' => [$this, 'canEditPostMeta'],
        ]);

        register_post_meta(Lesson::POST_TYPE, '_ecoursity_assigned', [
            'type' => 'integer',
            'single' => true,
            'show_in_rest' => false,
            'sanitize_callback' => [$this, 'sanitizeLessonAssignedMeta'],
            'auth_callback' => [$this, 'canEditPostMeta'],
        ]);
    }

    public function sanitizeLessonDurationMeta(mixed $value): array
    {
        $amount = is_array($value) && isset($value[0]) ? absint($value[0]) : 1;
        $unit = is_array($value) && isset($value[1]) ? sanitize_key((string) $value[1]) : 'minute';
        $allowedUnits = ['minute', 'hour'];

        if (!in_array($unit, $allowedUnits, true)) {
            $unit = 'minute';
        }

        if ($amount < 1) {
            $amount = 1;
        }

        return [$amount, $unit];
    }

    public function sanitizeTextListMeta(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $items = array_map(
            static fn($item): string => sanitize_text_field((string) $item),
            $value
        );

        return array_values(array_filter(
            $items,
            static fn(string $item): bool => $item !== ''
        ));
    }

    public function sanitizeLessonAssignedMeta(mixed $value): int
    {
        $courseId = absint($value);

        if ($courseId < 1) {
            return 0;
        }

        $course = get_post($courseId);

        return $course && $course->post_type === Course::POST_TYPE ? $courseId : 0;
    }

    public function canEditPostMeta(): bool
    {
        return current_user_can('edit_posts');
    }

    private function makeLabels(string $name): array
    {
        return [
            'name' => $name,
            'singular_name' => $name,
            'menu_name' => $name,
            'add_new' => __('Tambah ' . $name),
            'add_new_item' => __('Tambah ' . $name . ' baru'),
            'edit_item' => __('Edit ' . $name),
            'new_item' => __('New ' . $name),
            'view_item' => __('Lihat ' . $name),
            'search_items' => __('Cari ' . $name),
            'not_found' => __('Tidak ada ' . $name),
            'not_found_in_trash' => __('Tidak ada ' . $name . ' dalam sampah'),
            'parent_item_colon' => __('Induk ' . $name . ':'),
        ];
    }
}
