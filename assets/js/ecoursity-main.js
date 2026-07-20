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
                this.lesson.status = 'publish';
                this.parseDuration();
                this.normalizeAssigned();
                this.loading = false;
                this.$nextTick(() => this.initTinyMce());
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
                this.lesson.section_id = parseInt(this.lesson.section_id, 10) || 0;
            },
            getAuthHeaders(includeJson = false) {
                const headers = {
                    'X-Requested-With': 'XMLHttpRequest',
                };

                if (includeJson) {
                    headers['Content-Type'] = 'application/json';
                }

                if (window.ecoursity?.restNonce) {
                    headers['X-WP-Nonce'] = window.ecoursity.restNonce;
                }

                return headers;
            },
            initTinyMce() {
                const id = 'ecoursity_lesson_content';
                const textarea = document.getElementById(id);

                if (typeof tinymce !== 'undefined' && tinymce.get(id)) {
                    this.tinyMceInitialized = true;
                    this.syncEditorContent();
                    return;
                }

                if (textarea) {
                    textarea.value = this.lesson.content || '';
                    this.tinyMceInitialized = true;
                }
            },
            syncEditorContent() {
                const id = 'ecoursity_lesson_content';

                if (typeof tinymce !== 'undefined' && tinymce.get(id)) {
                    const editor = tinymce.get(id);
                    const content = this.lesson.content || '';

                    if (editor.getContent() !== content) {
                        editor.setContent(content);
                    }

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
                    section_id: parseInt(this.lesson.section_id, 10) || 0,
                    duration: [parseInt(this.lesson.duration_value, 10) || 35, this.lesson.duration_unit || 'minute'],
                    preview: !!this.lesson.preview,
                    status: 'publish',
                };

                try {
                    const endpoint = this.currentLessonId ? `${restUrl}${this.currentLessonId}` : restUrl;
                    const method = this.currentLessonId ? 'PUT' : 'POST';
                    const res = await fetch(endpoint, {
                        method,
                        headers: this.getAuthHeaders(true),
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
                            window.dispatchEvent(new CustomEvent('ecoursity:lesson-saved', {
                                detail: {
                                    lesson: { ...json.data },
                                },
                            }));
                        }

                        this.message = json.message || 'Lesson berhasil disimpan.';
                        this.message_type = 'success';

                        if (window.Alpine?.store('EcoursityUiModal')) {
                            window.Alpine.store('EcoursityUiModal').close();
                        }
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
