(() => {
    const registerLessonForm = () => {
        if (!window.Alpine) {
            return;
        }

        if (window.__ecoursityLessonFormRegistered) {
            return;
        }

        window.__ecoursityLessonFormRegistered = true;

        window.Alpine.data('lessonForm', (lessonId, restUrl, defaults) => ({
            loading: true,
            saving: false,
            message: '',
            message_type: 'success',
            currentLessonId: parseInt(lessonId, 10) || 0,
            lesson: {
                ...defaults,
            },
            tinyMceInitialized: false,
            async init() {
                if (!this.currentLessonId) {
                    this.lesson.status = 'publish';
                    this.loading = false;
                    this.$nextTick(() => this.initTinyMce());
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
                this.lesson.assigned = parseInt(this.lesson.assigned, 10) || 0;
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
                        this.lesson.status = 'publish';
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
                    this.$nextTick(() => this.initTinyMce());
                }
            },
            initTinyMce() {
                if (this.tinyMceInitialized) {
                    this.syncEditorContent();
                    return;
                }

                const id = 'ecoursity_lesson_content';

                if (typeof tinymce !== 'undefined') {
                    if (tinymce.get(id)) {
                        tinymce.get(id).setContent(this.lesson.content || '');
                        this.tinyMceInitialized = true;
                        return;
                    }

                    tinymce.init({
                        selector: '#' + id,
                        height: 360,
                        menubar: false,
                        plugins: 'link image media lists table',
                        toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | link image media | table',
                        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 14px; line-height: 1.6; }',
                        setup: (editor) => {
                            editor.on('change', () => {
                                this.lesson.content = editor.getContent();
                            });
                        },
                    });

                    tinymce.on('init', () => {
                        if (!this.tinyMceInitialized && tinymce.get(id)) {
                            this.tinyMceInitialized = true;
                            this.syncEditorContent();
                        }
                    });
                } else {
                    const textarea = document.getElementById(id);
                    if (textarea) {
                        textarea.value = this.lesson.content || '';
                    }
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
                    status: 'publish',
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
                            this.lesson.status = 'publish';
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
    };

    registerLessonForm();
    document.addEventListener('alpine:init', registerLessonForm);
})();