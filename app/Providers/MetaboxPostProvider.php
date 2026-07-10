<?php

namespace Ecoursity\App\Providers;

use Ecoursity\App\Models\Course;
use WP_Post;

class MetaboxPostProvider
{
    public function boot()
    {
        add_action('add_meta_boxes', [$this, 'registerCourseMetaBox']);
        add_action('save_post_' . Course::POST_TYPE, [$this, 'saveCourseMeta'], 10, 2);
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

        $fields = [
            '_ecoursity_duration' => 'Durasi',
            '_ecoursity_level' => 'Level',
            '_ecoursity_max_students' => 'Maksimal Siswa',
            '_ecoursity_price' => 'Harga',
            '_ecoursity_price_sale' => 'Harga Promo',
            '_ecoursity_price_sale_start' => 'Mulai Promo',
            '_ecoursity_price_sale_end' => 'Akhir Promo',
            '_ecoursity_course_evaluation' => 'Evaluasi Kursus',
            '_ecoursity_passing_grade' => 'Nilai Kelulusan',
        ];

        echo '<table class="form-table" role="presentation"><tbody>';

        foreach ($fields as $metaKey => $label) {
            $value = $course->meta($metaKey, '');

            echo '<tr>';
            echo '<th scope="row"><label for="' . esc_attr($metaKey) . '">' . esc_html($label) . '</label></th>';
            echo '<td>';
            echo '<input type="text" class="regular-text" id="' . esc_attr($metaKey) . '" name="ecoursity_course_meta[' . esc_attr($metaKey) . ']" value="' . esc_attr((string) $value) . '">';
            echo '</td>';
            echo '</tr>';
        }

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
            $course->updateMeta($metaKey, sanitize_text_field((string) $value));
        }
    }
}
