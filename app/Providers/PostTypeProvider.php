<?php

namespace Ecoursity\App\Providers;

use Ecoursity\App\Models\Course;

class PostTypeProvider
{
    public function boot()
    {
        add_action('init', [$this, 'register']);
        add_action('add_meta_boxes', [$this, 'registerCourseMetaBox']);
        add_action('save_post_' . Course::POST_TYPE, [$this, 'saveCourseMeta'], 10, 2);
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
        ]);
    }

    public function registerCourseMetaBox(): void
    {
        add_meta_box(
            'ecoursity-course-meta',
            __('Detail Kursus'),
            [$this, 'renderCourseMetaBox'],
            Course::POST_TYPE,
            'normal',
            'default'
        );
    }

    public function renderCourseMetaBox(\WP_Post $post): void
    {
        $course = Course::find($post->ID);

        if (!$course) {
            return;
        }

        wp_nonce_field('ecoursity_course_meta', 'ecoursity_course_meta_nonce');

        $duration = $course->meta('_ecoursity_duration', []);
        $duration = is_array($duration) ? $duration : [];
        $durationValue = isset($duration[0]) ? (string) $duration[0] : '';
        $durationUnit = isset($duration[1]) ? (string) $duration[1] : 'week';

        $level = (string) $course->meta('_ecoursity_level', 'all');
        $levelOptions = [
            'all' => 'All',
            'beginner' => 'Beginner',
            'intermediate' => 'Intermediate',
            'expert' => 'Expert',
        ];

        $priceSaleStart = (string) $course->meta('_ecoursity_price_sale_start', '');
        $priceSaleEnd = (string) $course->meta('_ecoursity_price_sale_end', '');
        $courseEvaluation = (string) $course->meta('_ecoursity_course_evaluation', '');
        $evaluationOptions = [
            'evaluate_lesson' => 'Evaluate Lesson',
            'evaluate_final_quiz' => 'Evaluate Final Quiz',
            'evaluate_quiz' => 'Evaluate Quiz',
            'evaluate_questions' => 'Evaluate Questions',
            'evaluate_mark' => 'Evaluate Mark',
        ];
        $passingGrade = (string) $course->meta('_ecoursity_passing_grade', '');

        echo '<table class="form-table" role="presentation"><tbody>';

        echo '<tr>';
        echo '<th scope="row">' . esc_html__('Durasi') . '</th>';
        echo '<td style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">';
        echo '<input type="number" min="1" step="1" class="small-text" name="ecoursity_course_meta[_ecoursity_duration][0]" value="' . esc_attr($durationValue) . '">';
        echo '<select name="ecoursity_course_meta[_ecoursity_duration][1]">';

        foreach (['day' => 'Hari', 'week' => 'Minggu', 'month' => 'Bulan', 'year' => 'Tahun'] as $value => $label) {
            echo '<option value="' . esc_attr($value) . '" ' . selected($durationUnit, $value, false) . '>' . esc_html($label) . '</option>';
        }

        echo '</select>';
        echo '</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th scope="row"><label for="_ecoursity_level">' . esc_html__('Level') . '</label></th>';
        echo '<td><select id="_ecoursity_level" name="ecoursity_course_meta[_ecoursity_level]">';

        foreach ($levelOptions as $value => $label) {
            echo '<option value="' . esc_attr($value) . '" ' . selected($level, $value, false) . '>' . esc_html($label) . '</option>';
        }

        echo '</select></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th scope="row"><label for="_ecoursity_price_sale_start">' . esc_html__('Mulai Promo') . '</label></th>';
        echo '<td><input type="datetime-local" id="_ecoursity_price_sale_start" name="ecoursity_course_meta[_ecoursity_price_sale_start]" value="' . esc_attr($this->formatDateTimeLocal($priceSaleStart)) . '"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th scope="row"><label for="_ecoursity_price_sale_end">' . esc_html__('Akhir Promo') . '</label></th>';
        echo '<td><input type="datetime-local" id="_ecoursity_price_sale_end" name="ecoursity_course_meta[_ecoursity_price_sale_end]" value="' . esc_attr($this->formatDateTimeLocal($priceSaleEnd)) . '"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th scope="row">' . esc_html__('Evaluasi Kursus') . '</th>';
        echo '<td>';

        foreach ($evaluationOptions as $value => $label) {
            echo '<label style="display:block;margin-bottom:6px;">';
            echo '<input type="radio" name="ecoursity_course_meta[_ecoursity_course_evaluation]" value="' . esc_attr($value) . '" ' . checked($courseEvaluation, $value, false) . '> ';
            echo esc_html($label);
            echo '</label>';
        }

        echo '</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th scope="row"><label for="_ecoursity_passing_grade">' . esc_html__('Nilai Kelulusan') . '</label></th>';
        echo '<td><input type="number" id="_ecoursity_passing_grade" name="ecoursity_course_meta[_ecoursity_passing_grade]" min="1" max="100" step="0.01" value="' . esc_attr($passingGrade) . '"></td>';
        echo '</tr>';

        echo '</tbody></table>';
    }

    public function saveCourseMeta(int $postId, \WP_Post $post): void
    {
        if (!isset($_POST['ecoursity_course_meta_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['ecoursity_course_meta_nonce'])), 'ecoursity_course_meta')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if ($post->post_type !== Course::POST_TYPE) {
            return;
        }

        if (!current_user_can('edit_post', $postId)) {
            return;
        }

        $course = Course::find($postId);

        if (!$course || !isset($_POST['ecoursity_course_meta']) || !is_array($_POST['ecoursity_course_meta'])) {
            return;
        }

        $submittedMeta = wp_unslash($_POST['ecoursity_course_meta']);

        foreach ($course->meta_keys as $metaKey) {
            $value = $submittedMeta[$metaKey] ?? '';
            $course->updateMeta($metaKey, $this->sanitizeMetaValue($metaKey, $value));
        }
    }

    private function sanitizeMetaValue(string $metaKey, mixed $value): mixed
    {
        return match ($metaKey) {
            '_ecoursity_duration' => $this->sanitizeDuration($value),
            '_ecoursity_level' => $this->sanitizeLevel($value),
            '_ecoursity_price_sale_start', '_ecoursity_price_sale_end' => $this->sanitizeDateTimeLocal($value),
            '_ecoursity_course_evaluation' => $this->sanitizeCourseEvaluation($value),
            '_ecoursity_passing_grade' => $this->sanitizePassingGrade($value),
            default => sanitize_text_field((string) $value),
        };
    }

    private function sanitizeDuration(mixed $value): array
    {
        $amount = is_array($value) && isset($value[0]) ? absint($value[0]) : 0;
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

    private function sanitizeLevel(mixed $value): string
    {
        $level = sanitize_key((string) $value);
        $allowed = ['all', 'beginner', 'intermediate', 'expert'];

        return in_array($level, $allowed, true) ? $level : 'all';
    }

    private function sanitizeDateTimeLocal(mixed $value): string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '';
        }

        return preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $value) ? $value : '';
    }

    private function sanitizeCourseEvaluation(mixed $value): string
    {
        $evaluation = sanitize_key((string) $value);
        $allowed = [
            'evaluate_lesson',
            'evaluate_final_quiz',
            'evaluate_quiz',
            'evaluate_questions',
            'evaluate_mark',
        ];

        return in_array($evaluation, $allowed, true) ? $evaluation : '';
    }

    private function sanitizePassingGrade(mixed $value): string
    {
        $grade = (float) $value;
        $grade = min(100, max(1, $grade));

        return number_format($grade, 2, '.', '');
    }

    private function formatDateTimeLocal(string $value): string
    {
        if ($value === '') {
            return '';
        }

        return preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $value) ? $value : '';
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
