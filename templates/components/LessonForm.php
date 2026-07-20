<?php
$lesson_id = $props['lesson_id'] ?? 0;
$course_id = (int) ($props['course_id'] ?? 0);
$section_id = (int) ($props['section_id'] ?? 0);
$rest_url  = get_rest_url(null, 'ecoursity/v1/lessons/');
$course_title = $course_id > 0 ? get_the_title($course_id) : '';
$course_permalink = $course_id > 0 ? get_permalink($course_id) : '';
$lesson_permalink = $lesson_id > 0 ? get_permalink($lesson_id) : '';

$lesson_defaults = [
    'title'          => '',
    'slug'           => '',
    'assigned'       => $course_id,
    'assigned_title' => $course_title ?: '',
    'section_id'     => $section_id,
    'content'        => '',
    'status'         => 'publish',
    'duration_value' => 35,
    'duration_unit'  => 'minute',
    'preview'        => false,
    'permalink'      => $lesson_permalink ?: '',
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
                Judul
                <span class="ecoursity-required">*</span>
            </label>
            <input type="text" class="ecoursity-form-input" x-model="lesson.title" required placeholder="e.g. Pengenalan Laravel">
        </div>

        <div class="ecoursity-form-group">
            <label class="ecoursity-form-label">Permalink</label>
            <template x-if="lesson.permalink">
                <a :href="lesson.permalink" target="_blank" rel="noopener noreferrer" class="ecoursity-form-slug__text" x-text="lesson.permalink"></a>
            </template>
            <template x-if="!lesson.permalink">
                <span class="ecoursity-form-slug__text">Permalink tersedia setelah lesson disimpan.</span>
            </template>
        </div>

        <div class="ecoursity-form-group">
            <label class="ecoursity-form-label">Course</label>
            <?php if ($course_permalink) : ?>
                <a href="<?php echo esc_url($course_permalink); ?>" target="_blank" rel="noopener noreferrer" class="ecoursity-form-slug__text" x-text="lesson.assigned_title || '<?php echo esc_js($course_title); ?>'"></a>
            <?php else : ?>
                <span class="ecoursity-form-slug__text" x-text="lesson.assigned_title || 'Course tidak ditemukan'"></span>
            <?php endif; ?>
        </div>

        <div class="ecoursity-form-group">
            <label class="ecoursity-form-label">Konten</label>
            <textarea id="ecoursity_lesson_content" class="ecoursity-form-textarea" rows="20" x-model="lesson.content" placeholder="Tulis konten lesson..."></textarea>
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