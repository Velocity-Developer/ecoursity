<?php
$course_id = $props['course_id'] ?? 0;
$lesson_id = $props['lesson_id'] ?? 0;
$rest_url = get_rest_url(null, 'ecoursity/v1/lessons/');

wp_enqueue_media();

$courses = get_posts([
    'post_type' => 'ecoursity_course',
    'post_status' => ['publish', 'draft', 'pending', 'private'],
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
]);

$lesson_defaults = [
    'title' => '',
    'slug' => '',
    'content' => '',
    'excerpt' => '',
    'status' => 'draft',
    'duration_value' => 35,
    'duration_unit' => 'minute',
    'preview' => false,
    'assigned' => (int) $course_id,
];
?>

<div
    x-data="lessonForm(
        <?php echo (int) $lesson_id; ?>,
        '<?php echo esc_js($rest_url); ?>',
        <?php echo esc_attr(wp_json_encode($lesson_defaults)); ?>
    )"
    x-cloak>
    <template x-if="loading">
        <p class="ecoursity-form-loading">Memuat data lesson...</p>
    </template>

    <form x-show="!loading" @submit.prevent="submit" class="ecoursity-course-form">
        <div x-show="message" class="ecoursity-form-message" :class="'ecoursity-form-message--' + message_type" x-text="message"></div>

        <div class="ecoursity-course-form__layout">
            <div class="ecoursity-course-form__main">
                <div class="ecoursity-form-group">
                    <label class="ecoursity-form-label">
                        Judul Lesson
                        <span class="ecoursity-required">*</span>
                    </label>
                    <input
                        type="text"
                        class="ecoursity-form-input"
                        x-model="lesson.title"
                        placeholder="e.g. Pengenalan Laravel"
                        required>
                </div>

                <div class="ecoursity-form-group">
                    <label class="ecoursity-form-label">Konten</label>
                    <?php
                    wp_editor('', 'ecoursity_lesson_content', [
                        'textarea_name' => 'lesson_content',
                        'textarea_rows' => 30,
                        'editor_height' => 500,
                        'media_buttons' => true,
                        'teeny'         => false,
                        'quicktags'     => true,
                    ]);
                    ?>
                </div>
            </div>

            <aside class="ecoursity-course-form__aside">
                <div class="ecoursity-form-group ecoursity-form-group--aside">
                    <label class="ecoursity-form-label">Slug</label>
                    <div class="ecoursity-form-slug">
                        <span x-show="!slugEditable" @click="slugEditable = true" class="ecoursity-form-slug__text" x-text="lesson.slug || '(kosong)'"></span>
                        <input
                            x-show="slugEditable"
                            type="text"
                            class="ecoursity-form-input"
                            x-model="lesson.slug"
                            @click.outside="slugEditable = false"
                            @keydown.enter="slugEditable = false"
                            @keydown.escape="slugEditable = false"
                            placeholder="Otomatis jika kosong">
                    </div>
                </div>

                <div class="ecoursity-form-group ecoursity-form-group--aside">
                    <label class="ecoursity-form-label">Status</label>
                    <select class="ecoursity-form-select" x-model="lesson.status">
                        <option value="draft">Draft</option>
                        <option value="publish">Publik</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>

                <div class="ecoursity-form-group ecoursity-form-group--aside">
                    <label class="ecoursity-form-label">Ringkasan</label>
                    <textarea
                        class="ecoursity-form-textarea"
                        x-model="lesson.excerpt"
                        rows="4"
                        placeholder="Ringkasan singkat..."></textarea>
                </div>

                <div class="ecoursity-form-group ecoursity-form-group--aside">
                    <label class="ecoursity-form-label">Durasi</label>
                    <div class="ecoursity-form-duration">
                        <input
                            type="number"
                            class="ecoursity-form-input ecoursity-form-duration__input"
                            x-model="lesson.duration_value"
                            min="1"
                            step="1"
                            placeholder="35">
                        <select class="ecoursity-form-select ecoursity-form-duration__select" x-model="lesson.duration_unit">
                            <option value="minute">Menit</option>
                            <option value="hour">Jam</option>
                            <option value="day">Hari</option>
                            <option value="week">Minggu</option>
                        </select>
                    </div>
                </div>

                <div class="ecoursity-form-group ecoursity-form-group--aside">
                    <label class="ecoursity-form-label">Assigned Course</label>
                    <select class="ecoursity-form-select" x-model="lesson.assigned">
                        <option value="0">Pilih Kursus</option>
                        <?php foreach ($courses as $course) : ?>
                            <option value="<?php echo esc_attr((string) $course->ID); ?>"><?php echo esc_html($course->post_title); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="ecoursity-form-group ecoursity-form-group--aside">
                    <label class="ecoursity-checkbox-option">
                        <input type="checkbox" x-model="lesson.preview">
                        <span>Aktifkan preview</span>
                    </label>
                </div>
            </aside>
        </div>

        <div class="ecoursity-form-actions">
            <button type="submit" class="ecoursity-button ecoursity-button--primary" :disabled="saving" x-text="saving ? 'Menyimpan...' : 'Simpan'"></button>
            <a href="<?php echo esc_url(get_admin_url(null, 'edit.php?post_type=ecoursity_lesson')); ?>" class="ecoursity-button ecoursity-button--outline">Batal</a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('lessonForm', (lessonId, restUrl, defaults) => ({
            loading: true,
            saving: false,
            slugEditable: false,
            message: '',
            message_type: 'success',
            currentLessonId: parseInt(lessonId, 10) || 0,
            lesson: {
                ...defaults,
            },
            async init() {
                if (!this.currentLessonId) {
                    this.loading = false;
                    this.$nextTick(() => this.syncEditorContent());
                    return;
                }

                await this.loadLesson();
            },
            parseDuration() {
                const duration = this.lesson.duration;

                if (Array.isArray(duration)) {
                    this.lesson.duration_value = parseInt(duration[0], 10) || 35;
                    this.lesson.duration_unit = duration[1] || 'minute';
                }

                delete this.lesson.duration;
            },
            normalizeAssigned() {
                this.lesson.assigned = String(parseInt(this.lesson.assigned, 10) || 0);
            },
            async loadLesson() {
                this.loading = true;

                try {
                    const res = await fetch(`${restUrl}${this.currentLessonId}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });
                    const json = await res.json();

                    if (json.success && json.data) {
                        Object.assign(this.lesson, json.data);
                        this.parseDuration();
                        this.normalizeAssigned();
                    } else {
                        this.message = json.message || 'Gagal memuat data lesson.';
                        this.message_type = 'error';
                    }
                } catch (e) {
                    this.message = 'Gagal memuat data lesson.';
                    this.message_type = 'error';
                } finally {
                    this.loading = false;
                    this.$nextTick(() => this.syncEditorContent());
                }
            },
            syncEditorContent() {
                const id = 'ecoursity_lesson_content';

                if (typeof tinymce !== 'undefined' && tinymce.get(id)) {
                    tinymce.get(id).setContent(this.lesson.content || '');
                    return;
                }

                const textarea = document.getElementById(id);
                if (textarea) {
                    textarea.value = this.lesson.content || '';
                }
            },
            syncEditorToModel() {
                const id = 'ecoursity_lesson_content';

                if (typeof tinymce !== 'undefined' && tinymce.get(id)) {
                    this.lesson.content = tinymce.get(id).getContent();
                    return;
                }

                const textarea = document.getElementById(id);
                if (textarea) {
                    this.lesson.content = textarea.value;
                }
            },
            async submit() {
                this.syncEditorToModel();
                this.saving = true;
                this.message = '';

                const payload = {
                    ...this.lesson,
                    assigned: parseInt(this.lesson.assigned, 10) || 0,
                    duration: [parseInt(this.lesson.duration_value, 10) || 35, this.lesson.duration_unit || 'minute'],
                    preview: !!this.lesson.preview,
                };

                try {
                    const endpoint = this.currentLessonId ? `${restUrl}${this.currentLessonId}` : restUrl;
                    const method = this.currentLessonId ? 'PUT' : 'POST';
                    const res = await fetch(endpoint, {
                        method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify(payload),
                    });
                    const json = await res.json();

                    if (json.success) {
                        if (json.data?.id) {
                            this.currentLessonId = parseInt(json.data.id, 10) || this.currentLessonId;
                            Object.assign(this.lesson, json.data);
                            this.parseDuration();
                            this.normalizeAssigned();
                            this.$nextTick(() => this.syncEditorContent());
                        }

                        this.message = json.message || 'Lesson berhasil disimpan.';
                        this.message_type = 'success';
                    } else {
                        this.message = json.message || 'Gagal menyimpan lesson.';
                        this.message_type = 'error';
                    }
                } catch (e) {
                    this.message = 'Gagal menyimpan lesson.';
                    this.message_type = 'error';
                } finally {
                    this.saving = false;
                }
            },
        }));
    });
</script>