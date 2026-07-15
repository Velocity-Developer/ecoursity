<?php

namespace Ecoursity\App\Providers;

use Ecoursity\App\Models\Course;
use Ecoursity\App\Models\Lesson;
use WP_Post;

class MetaboxPostProvider
{
    public function boot()
    {
        add_action('add_meta_boxes', [$this, 'registerCourseMetaBox']);
        add_action('add_meta_boxes', [$this, 'registerLessonMetaBox']);
        add_action('save_post_' . Course::POST_TYPE, [$this, 'saveCourseMeta'], 10, 2);
        add_action('save_post_' . Lesson::POST_TYPE, [$this, 'saveLessonMeta'], 10, 2);
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

    public function registerLessonMetaBox(): void
    {
        add_meta_box(
            'ecoursity-lesson-meta',
            __('Detail Materi Kursus'),
            [$this, 'renderLessonMetaBox'],
            Lesson::POST_TYPE,
            'normal',
            'default'
        );
    }

    public function renderCourseMetaBox(WP_Post $post): void
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
            'all' => 'Semua',
            'beginner' => 'Pemula',
            'intermediate' => 'Menengah',
            'expert' => 'Master',
        ];

        $maxStudents = (string) $course->meta('_ecoursity_max_students', '');
        $price = (string) $course->meta('_ecoursity_price', '');
        $priceSale = (string) $course->meta('_ecoursity_price_sale', '');
        $priceSaleStart = (string) $course->meta('_ecoursity_price_sale_start', '');
        $priceSaleEnd = (string) $course->meta('_ecoursity_price_sale_end', '');
        $courseEvaluation = (string) $course->meta('_ecoursity_course_evaluation', '');
        $evaluationOptions = [
            'evaluate_lesson' => 'Evaluasi Materi Kursus',
            'evaluate_final_quiz' => 'Evaluasi Final Quiz',
            'evaluate_quiz' => 'Evaluasi Quiz',
            'evaluate_questions' => 'Evaluasi Soal',
            'evaluate_mark' => 'Evaluasi Berdasarkan Poin',
        ];
        $passingGrade = (string) $course->meta('_ecoursity_passing_grade', '');

        echo '<table class="form-table" role="presentation"><tbody>';

        echo '<tr>';
        echo '<th scope="row">' . esc_html__('Durasi') . '</th>';
        echo '<td style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">';
        echo '<input type="number" min="1" step="1" class="small-text" name="ecoursity_course_meta[_ecoursity_duration][0]" value="' . esc_attr($durationValue) . '">';
        echo '<select name="ecoursity_course_meta[_ecoursity_duration][1]">';

        foreach (['day' => 'Day', 'week' => 'Week', 'month' => 'Month', 'year' => 'Year'] as $value => $label) {
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
        echo '<th scope="row"><label for="_ecoursity_max_students">' . esc_html__('Maksimal Siswa') . '</label></th>';
        echo '<td><input type="text" class="regular-text" id="_ecoursity_max_students" name="ecoursity_course_meta[_ecoursity_max_students]" value="' . esc_attr($maxStudents) . '"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th scope="row"><label for="_ecoursity_price">' . esc_html__('Harga') . '</label></th>';
        echo '<td><input type="text" class="regular-text" id="_ecoursity_price" name="ecoursity_course_meta[_ecoursity_price]" value="' . esc_attr($price) . '"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th scope="row"><label for="_ecoursity_price_sale">' . esc_html__('Harga Promo') . '</label></th>';
        echo '<td><input type="text" class="regular-text" id="_ecoursity_price_sale" name="ecoursity_course_meta[_ecoursity_price_sale]" value="' . esc_attr($priceSale) . '"></td>';
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
        echo '<td><input type="number" id="_ecoursity_passing_grade" name="ecoursity_course_meta[_ecoursity_passing_grade]" min="1" max="100" step="0.01" value="' . esc_attr($passingGrade) . '">%</td>';
        echo '</tr>';

        echo '</tbody></table>';
    }

    public function renderLessonMetaBox(WP_Post $post): void
    {
        $lesson = Lesson::find($post->ID);

        if (!$lesson) {
            return;
        }

        wp_nonce_field('ecoursity_lesson_meta', 'ecoursity_lesson_meta_nonce');

        $duration = $lesson->meta('_ecoursity_duration', [35, 'minute']);
        $duration = is_array($duration) ? $duration : [35, 'minute'];
        $durationValue = isset($duration[0]) ? (string) $duration[0] : '35';
        $durationUnit = isset($duration[1]) ? (string) $duration[1] : 'minute';
        $preview = (bool) $lesson->meta('_ecoursity_preview', false);
        $assigned = (int) $lesson->meta('_ecoursity_assigned', 0);
        $courses = Course::all([
            'post_status' => ['publish', 'draft', 'pending', 'private'],
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
        ]);

        echo '<table class="form-table" role="presentation"><tbody>';

        echo '<tr>';
        echo '<th scope="row">' . esc_html__('Durasi') . '</th>';
        echo '<td style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">';
        echo '<input type="number" min="1" step="1" class="small-text" name="ecoursity_lesson_meta[_ecoursity_duration][0]" value="' . esc_attr($durationValue) . '">';
        echo '<select name="ecoursity_lesson_meta[_ecoursity_duration][1]">';

        foreach (['minute' => 'Minute', 'hour' => 'Hour', 'day' => 'Day', 'week' => 'Week'] as $value => $label) {
            echo '<option value="' . esc_attr($value) . '" ' . selected($durationUnit, $value, false) . '>' . esc_html($label) . '</option>';
        }

        echo '</select>';
        echo '</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th scope="row"><label for="_ecoursity_preview">' . esc_html__('Preview') . '</label></th>';
        echo '<td><label><input type="checkbox" id="_ecoursity_preview" name="ecoursity_lesson_meta[_ecoursity_preview]" value="1" ' . checked($preview, true, false) . '> ' . esc_html__('Aktifkan preview') . '</label></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th scope="row"><label for="_ecoursity_assigned">' . esc_html__('Assigned Course') . '</label></th>';
        echo '<td><select id="_ecoursity_assigned" name="ecoursity_lesson_meta[_ecoursity_assigned]">';
        echo '<option value="0">' . esc_html__('Pilih Kursus') . '</option>';

        foreach ($courses as $course) {
            echo '<option value="' . esc_attr((string) $course->id) . '" ' . selected($assigned, $course->id, false) . '>' . esc_html($course->title) . '</option>';
        }

        echo '</select></td>';
        echo '</tr>';

        echo '</tbody></table>';
    }

    public function saveCourseMeta(int $postId, WP_Post $post): void
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

    public function saveLessonMeta(int $postId, WP_Post $post): void
    {
        if (!isset($_POST['ecoursity_lesson_meta_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['ecoursity_lesson_meta_nonce'])), 'ecoursity_lesson_meta')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if ($post->post_type !== Lesson::POST_TYPE) {
            return;
        }

        if (!current_user_can('edit_post', $postId)) {
            return;
        }

        $lesson = Lesson::find($postId);

        if (!$lesson || !isset($_POST['ecoursity_lesson_meta']) || !is_array($_POST['ecoursity_lesson_meta'])) {
            return;
        }

        $submittedMeta = wp_unslash($_POST['ecoursity_lesson_meta']);

        foreach ($lesson->meta_keys as $metaKey) {
            $value = $submittedMeta[$metaKey] ?? '';
            $lesson->updateMeta($metaKey, $this->sanitizeLessonMetaValue($metaKey, $value));
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

    private function sanitizeLessonMetaValue(string $metaKey, mixed $value): mixed
    {
        return match ($metaKey) {
            '_ecoursity_duration' => $this->sanitizeLessonDuration($value),
            '_ecoursity_preview' => !empty($value),
            '_ecoursity_assigned' => $this->sanitizeAssignedCourse($value),
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

    private function sanitizeLessonDuration(mixed $value): array
    {
        $amount = is_array($value) && isset($value[0]) ? absint($value[0]) : 35;
        $unit = is_array($value) && isset($value[1]) ? sanitize_key((string) $value[1]) : 'minute';
        $allowedUnits = ['minute', 'hour', 'day', 'week'];

        if (!in_array($unit, $allowedUnits, true)) {
            $unit = 'minute';
        }

        if ($amount < 1) {
            $amount = 35;
        }

        return [$amount, $unit];
    }

    private function sanitizeAssignedCourse(mixed $value): int
    {
        $courseId = absint($value);

        if ($courseId < 1) {
            return 0;
        }

        $course = get_post($courseId);

        return $course && $course->post_type === Course::POST_TYPE ? $courseId : 0;
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
        $value = trim((string) $value);

        if ($value === '') {
            return '';
        }

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
}
