<?php
$lesson_id = $props['lesson_id'] ?? 0;
$course_id = $props['course_id'] ?? 0;
$rest_url  = get_rest_url(null, 'ecoursity/v1/lessons/');

$course_options = get_posts([
    'post_type'      => 'ecoursity_course',
    'posts_per_page' => -1,
    'post_status'    => ['publish', 'draft', 'pending', 'private'],
    'orderby'        => 'title',
    'order'          => 'ASC',
]);

$lesson_defaults = [
    'title'          => '',
    'slug'           => '',
    'assigned'       => (int) $course_id,
    'content'        => '',
    'status'         => 'draft',
    'duration_value' => 35,
    'duration_unit'  => 'minute',
    'preview'        => false,
];
?>

<div
    x-data="lessonForm(<?php echo (int) $lesson_id; ?>, '<?php echo esc_js($rest_url); ?>', <?php echo esc_attr(wp_json_encode($lesson_defaults)); ?>)"
    x-cloak>
    <template x-if="loading">
        <p class="ecoursity-form-loading">Memuat data lesson...</p>
    </template>

    <form x-show="!loading" @submit.prevent="submit" class="ecoursity-course-form">
        <div x-show="message" class="ecoursity-form-message" :class="'ecoursity-form-message--' + message_type" x-text="message"></div>

        <div class="ecoursity-form-group">
            <label class="ecoursity-form-label">
                Judul Lesson
                <span class="ecoursity-required">*</span>
            </label>
            <input type="text" class="ecoursity-form-input" x-model="lesson.title" required placeholder="e.g. Pengenalan Laravel">
        </div>

        <div class="ecoursity-form-group">
            <label class="ecoursity-form-label">Slug</label>
            <div class="ecoursity-form-slug">
                <span x-show="!slugEditable" @click="slugEditable = true" class="ecoursity-form-slug__text" x-text="lesson.slug || '(kosong)'"></span>
                <input x-show="slugEditable" type="text" class="ecoursity-form-input" x-model="lesson.slug" @click.outside="slugEditable = false" @keydown.enter="slugEditable = false" @keydown.escape="slugEditable = false" placeholder="Otomatis jika kosong">
            </div>
        </div>

        <div class="ecoursity-form-group">
            <label class="ecoursity-form-label">
                Course
                <span class="ecoursity-required">*</span>
            </label>
            <select class="ecoursity-form-select" x-model="lesson.assigned" required>
                <option value="0">Pilih Course</option>
                <?php foreach ($course_options as $course_option) : ?>
                    <option value="<?php echo esc_attr((string) $course_option->ID); ?>"><?php echo esc_html($course_option->post_title); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="ecoursity-form-group">
            <label class="ecoursity-form-label">Konten</label>
            <?php
            wp_editor('', 'ecoursity_lesson_content', [
                'textarea_name' => 'lesson_content',
                'textarea_rows' => 20,
                'editor_height' => 360,
                'media_buttons' => true,
                'teeny'         => false,
                'quicktags'     => true,
            ]);
            ?>
        </div>

        <div class="ecoursity-form-row">
            <div class="ecoursity-form-group">
                <label class="ecoursity-form-label">Status</label>
                <select class="ecoursity-form-select" x-model="lesson.status">
                    <option value="draft">Draft</option>
                    <option value="publish">Publik</option>
                    <option value="pending">Pending</option>
                </select>
            </div>

            <div class="ecoursity-form-group">
                <label class="ecoursity-form-label">Durasi</label>
                <div class="ecoursity-form-duration">
                    <input type="number" class="ecoursity-form-input ecoursity-form-duration__input" x-model="lesson.duration_value" min="1" placeholder="35">
                    <select class="ecoursity-form-select ecoursity-form-duration__select" x-model="lesson.duration_unit">
                        <option value="minute">Menit</option>
                        <option value="hour">Jam</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="ecoursity-form-group ecoursity-form-group--checkbox">
            <label class="ecoursity-checkbox-option">
                <input type="checkbox" x-model="lesson.preview">
                <span>Izinkan preview gratis</span>
            </label>
        </div>

        <div class="ecoursity-form-actions">
            <button type="submit" class="ecoursity-button ecoursity-button--primary" :disabled="saving" x-text="saving ? 'Menyimpan...' : 'Simpan Lesson'"></button>
        </div>
    </form>
</div>