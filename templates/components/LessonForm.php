<?php

wp_enqueue_editor();
wp_enqueue_media();

use Ecoursity\App\Models\Lesson;
use Ecoursity\App\Models\Section;

$lesson_id = (int) ($props['lesson_id'] ?? 0);
$course_id = (int) ($props['course_id'] ?? 0);
$section_id = (int) ($props['section_id'] ?? 0);
$rest_url = get_rest_url(null, 'ecoursity/v1/lessons/');
$lesson = $lesson_id > 0 ? Lesson::find($lesson_id) : null;

if ($lesson instanceof Lesson) {
    $course_id = (int) $lesson->assigned;

    if ($section_id < 1 && $course_id > 0) {
        foreach (Section::allByCourse($course_id) as $section) {
            foreach ($section->items as $item) {
                if ((int) ($item['item_id'] ?? 0) === $lesson_id && (string) ($item['item_type'] ?? '') === 'lesson') {
                    $section_id = (int) ($section->section_id ?? 0);
                    break 2;
                }
            }
        }
    }
}

$course_title = $course_id > 0 ? get_the_title($course_id) : '';
$course_permalink = $course_id > 0 ? get_permalink($course_id) : '';
$lesson_permalink = $lesson_id > 0 ? get_permalink($lesson_id) : '';
$duration = $lesson instanceof Lesson && is_array($lesson->duration) ? $lesson->duration : [35, 'minute'];
$duration_value = isset($duration[0]) ? (int) $duration[0] : 35;
$duration_unit = isset($duration[1]) ? (string) $duration[1] : 'minute';

$lesson_defaults = [
    'title' => $lesson?->title ?? '',
    'slug' => $lesson?->slug ?? '',
    'assigned' => $course_id,
    'assigned_title' => $course_title ?: '',
    'section_id' => $section_id,
    'content' => $lesson?->content ?? '',
    'status' => 'publish',
    'duration_value' => $duration_value > 0 ? $duration_value : 35,
    'duration_unit' => $duration_unit ?: 'minute',
    'preview' => (bool) ($lesson?->preview ?? false),
    'permalink' => $lesson_permalink ?: '',
];
?>

<div
    x-data="lessonForm(<?php echo (int) $lesson_id; ?>, <?php echo esc_attr(wp_json_encode($rest_url)); ?>, JSON.parse($refs.lessonDefaults.textContent))"
    x-cloak>
    <script type="application/json" x-ref="lessonDefaults">
        <?php echo wp_json_encode($lesson_defaults); ?>
    </script>
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
            <?php
            wp_editor($lesson_defaults['content'], 'ecoursity_lesson_content', [
                'textarea_name' => 'lesson_content',
                'textarea_rows' => 30,
                'editor_height' => 420,
                'media_buttons' => true,
                'teeny' => false,
                'quicktags' => true,
            ]);
            ?>
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