<?php

namespace Ecoursity\App\Providers;

use Ecoursity\App\Models\Course;
use Ecoursity\App\Models\Lesson;
use Ecoursity\App\Models\Order;
use Ecoursity\App\Support\CourseFormSchema;

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

        register_post_type(Order::POST_TYPE, [
            'labels' => $this->makeLabels(__('Order Kursus')),
            'public' => false,
            'has_archive' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'show_in_rest' => false,
            'supports' => ['custom-fields'],
            'capability_type' => 'post',
        ]);

        foreach (CourseFormSchema::metaFieldInputs(CourseFormSchema::sections()) as $field => $input) {
            $isTextList = $input === 'sortable_text_list';
            $isRepeatableGroup = $input === 'repeatable_group';
            $isArray = $isTextList || $isRepeatableGroup || $input === 'duration';

            register_post_meta(Course::POST_TYPE, "_ecoursity_{$field}", [
                'type' => $isArray ? 'array' : 'string',
                'single' => true,
                'show_in_rest' => false,
                'sanitize_callback' => match ($input) {
                    'duration' => [$this, 'sanitizeCourseDurationMeta'],
                    'sortable_text_list' => [$this, 'sanitizeTextListMeta'],
                    'repeatable_group' => [$this, 'sanitizeRepeatableGroupMeta'],
                    default => 'sanitize_text_field',
                },
                'auth_callback' => [$this, 'canEditPostMeta'],
            ]);
        }

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

        register_post_meta(Order::POST_TYPE, Order::META_ORDER_NUMBER, [
            'type' => 'string',
            'single' => true,
            'show_in_rest' => false,
            'sanitize_callback' => 'sanitize_text_field',
            'auth_callback' => [$this, 'canEditPostMeta'],
        ]);

        register_post_meta(Order::POST_TYPE, Order::META_ORDER_DATE, [
            'type' => 'string',
            'single' => true,
            'show_in_rest' => false,
            'sanitize_callback' => [$this, 'sanitizeOrderDateMeta'],
            'auth_callback' => [$this, 'canEditPostMeta'],
        ]);

        register_post_meta(Order::POST_TYPE, Order::META_ORDER_METHOD, [
            'type' => 'string',
            'single' => true,
            'show_in_rest' => false,
            'sanitize_callback' => [$this, 'sanitizeOrderMethodMeta'],
            'auth_callback' => [$this, 'canEditPostMeta'],
        ]);

        register_post_meta(Order::POST_TYPE, Order::META_ORDER_STATUS, [
            'type' => 'string',
            'single' => true,
            'show_in_rest' => false,
            'sanitize_callback' => [$this, 'sanitizeOrderStatusMeta'],
            'auth_callback' => [$this, 'canEditPostMeta'],
        ]);

        register_post_meta(Order::POST_TYPE, Order::META_ORDER_USER, [
            'type' => 'integer',
            'single' => true,
            'show_in_rest' => false,
            'sanitize_callback' => [$this, 'sanitizeOrderUserMeta'],
            'auth_callback' => [$this, 'canEditPostMeta'],
        ]);

        register_post_meta(Order::POST_TYPE, Order::META_ORDER_PAYMENT, [
            'type' => 'string',
            'single' => true,
            'show_in_rest' => false,
            'sanitize_callback' => 'sanitize_text_field',
            'auth_callback' => [$this, 'canEditPostMeta'],
        ]);

        register_post_meta(Order::POST_TYPE, Order::META_ORDER_ITEMS, [
            'type' => 'array',
            'single' => true,
            'show_in_rest' => false,
            'sanitize_callback' => [$this, 'sanitizeOrderItemsMeta'],
            'auth_callback' => [$this, 'canEditPostMeta'],
        ]);

        register_post_meta(Order::POST_TYPE, Order::META_ORDER_SUBTOTAL, [
            'type' => 'number',
            'single' => true,
            'show_in_rest' => false,
            'sanitize_callback' => [$this, 'sanitizeOrderAmountMeta'],
            'auth_callback' => [$this, 'canEditPostMeta'],
        ]);

        register_post_meta(Order::POST_TYPE, Order::META_ORDER_TOTAL, [
            'type' => 'number',
            'single' => true,
            'show_in_rest' => false,
            'sanitize_callback' => [$this, 'sanitizeOrderAmountMeta'],
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

    public function sanitizeCourseDurationMeta(mixed $value): array
    {
        $amount = is_array($value) && isset($value[0]) ? absint($value[0]) : 1;
        $unit = is_array($value) && isset($value[1]) ? sanitize_key((string) $value[1]) : 'week';
        $allowedUnits = ['day', 'week', 'month', 'year'];

        if (!in_array($unit, $allowedUnits, true)) {
            $unit = 'week';
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

    public function sanitizeRepeatableGroupMeta(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        return array_values(array_filter(array_map(
            static function (mixed $item): array {
                if (!is_array($item)) {
                    return [];
                }

                return array_map(
                    static fn(mixed $subValue): string => sanitize_text_field((string) $subValue),
                    $item
                );
            },
            $value
        )));
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

    public function sanitizeOrderUserMeta(mixed $value): int
    {
        $userId = absint($value);

        return $userId > 0 && get_user_by('id', $userId) ? $userId : 0;
    }

    public function sanitizeOrderDateMeta(mixed $value): string
    {
        $date = preg_replace('/\D/', '', (string) $value);

        return strlen($date) === 14 ? $date : date_i18n('YmdHis');
    }

    public function sanitizeOrderMethodMeta(mixed $value): string
    {
        $method = sanitize_key((string) $value);

        return in_array($method, [Order::METHOD_MANUALLY, Order::METHOD_CHECKOUT], true)
            ? $method
            : Order::METHOD_MANUALLY;
    }

    public function sanitizeOrderStatusMeta(mixed $value): string
    {
        $status = sanitize_key((string) $value);
        $allowedStatuses = [
            Order::STATUS_COMPLETED,
            Order::STATUS_PENDING,
            Order::STATUS_PROCESSING,
            Order::STATUS_CANCELLED,
            Order::STATUS_REFUNDED,
            Order::STATUS_FAILED,
        ];

        return in_array($status, $allowedStatuses, true) ? $status : Order::STATUS_PENDING;
    }

    public function sanitizeOrderItemsMeta(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        return array_values(array_filter(array_map(
            static function (mixed $item): array {
                if (!is_array($item)) {
                    return [];
                }

                $courseId = absint($item['id'] ?? 0);

                if ($courseId < 1) {
                    return [];
                }

                return [
                    'id' => $courseId,
                    'name' => sanitize_text_field((string) ($item['name'] ?? '')),
                    'price' => max(0, (float) ($item['price'] ?? 0)),
                    'price_sale' => max(0, (float) ($item['price_sale'] ?? 0)),
                    'price_real' => max(0, (float) ($item['price_real'] ?? 0)),
                ];
            },
            $value
        )));
    }

    public function sanitizeOrderAmountMeta(mixed $value): float
    {
        return max(0, (float) $value);
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
