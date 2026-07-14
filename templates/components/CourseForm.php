<?php
$course_id = $props['course_id'] ?? 0;
$rest_url  = get_rest_url(null, 'ecoursity/v1/courses/');
wp_enqueue_media();
?>

<div x-data="courseForm(<?php echo (int) $course_id; ?>, '<?php echo esc_js($rest_url); ?>')" x-cloak>
    <template x-if="loading">
        <p class="ecoursity-form-loading">Memuat data kursus...</p>
    </template>

    <form x-show="!loading" @submit.prevent="submit" class="ecoursity-course-form">

        <div x-show="message" class="ecoursity-form-message" :class="'ecoursity-form-message--' + message_type" x-text="message"></div>

        <div class="ecoursity-form-group">
            <label class="ecoursity-form-label">Judul Kursus <span class="ecoursity-required">*</span></label>
            <input type="text" class="ecoursity-form-input" x-model="course.title" required placeholder="e.g. Belajar Laravel dari Nol">
        </div>

        <div class="ecoursity-form-group">
            <label class="ecoursity-form-label">Slug</label>
            <div class="ecoursity-form-slug">
                <span x-show="!slugEditable" @click="slugEditable = true" class="ecoursity-form-slug__text" x-text="course.slug || '(kosong)'"></span>
                <input x-show="slugEditable" type="text" class="ecoursity-form-input" x-model="course.slug" @click.outside="slugEditable = false" @keydown.enter="slugEditable = false" @keydown.escape="slugEditable = false" placeholder="Otomatis jika kosong">
            </div>
        </div>

        <div class="ecoursity-form-group">
            <label class="ecoursity-form-label">Gambar Unggulan</label>
            <div class="ecoursity-form-featured-image">
                <template x-if="course.thumbnail">
                    <div class="ecoursity-form-featured-image__preview">
                        <img :src="course.thumbnail" alt="Featured image">
                    </div>
                </template>
                <template x-if="!course.thumbnail">
                    <div class="ecoursity-form-featured-image__placeholder">
                        <span>Belum ada gambar</span>
                    </div>
                </template>
                <div class="ecoursity-form-featured-image__actions">
                    <button type="button" class="ecoursity-button ecoursity-button--outline ecoursity-button--sm" @click="openMediaUploader()">Pilih Gambar</button>
                    <button type="button" class="ecoursity-button ecoursity-button--ghost ecoursity-button--sm" x-show="course.thumbnail" @click="removeFeaturedImage()">Hapus</button>
                </div>
            </div>
        </div>

        <div class="ecoursity-form-row">
            <div class="ecoursity-form-group">
                <label class="ecoursity-form-label">Status</label>
                <select class="ecoursity-form-select" x-model="course.status">
                    <option value="draft">Draft</option>
                    <option value="publish">Publik</option>
                    <option value="pending">Pending</option>
                </select>
            </div>

            <div class="ecoursity-form-group">
                <label class="ecoursity-form-label">Level</label>
                <select class="ecoursity-form-select" x-model="course.level">
                    <option value="">Pilih Level</option>
                    <option value="beginner">Pemula</option>
                    <option value="intermediate">Menengah</option>
                    <option value="advanced">Lanjutan</option>
                </select>
            </div>

            <div class="ecoursity-form-group">
                <label class="ecoursity-form-label">Durasi</label>
                <div class="ecoursity-form-duration">
                    <input type="number" class="ecoursity-form-input ecoursity-form-duration__input" x-model="course.duration_value" min="1" placeholder="1">
                    <select class="ecoursity-form-select ecoursity-form-duration__select" x-model="course.duration_unit">
                        <option value="day">Hari</option>
                        <option value="week">Minggu</option>
                        <option value="month">Bulan</option>
                        <option value="year">Tahun</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="ecoursity-form-group">
            <label class="ecoursity-form-label">Konten</label>
            <?php
            wp_editor('', 'ecoursity_course_content', [
                'textarea_name' => 'course_content',
                'textarea_rows' => 40,
                'editor_height' => 600,
                'media_buttons' => true,
                'teeny'         => false,
                'quicktags'     => true,
            ]);
            ?>
        </div>

        <div class="ecoursity-form-group">
            <label class="ecoursity-form-label">Ringkasan</label>
            <textarea class="ecoursity-form-textarea" x-model="course.excerpt" rows="3" placeholder="Ringkasan singkat..."></textarea>
        </div>

        <div class="ecoursity-form-row">
            <div class="ecoursity-form-group">
                <label class="ecoursity-form-label">Harga</label>
                <input type="text" class="ecoursity-form-input" x-model="course.price" placeholder="0">
            </div>

            <div class="ecoursity-form-group">
                <label class="ecoursity-form-label">Harga Diskon</label>
                <input type="text" class="ecoursity-form-input" x-model="course.price_sale" placeholder="Kosongkan jika tidak ada">
            </div>
        </div>

        <div class="ecoursity-form-row">
            <div class="ecoursity-form-group">
                <label class="ecoursity-form-label">Diskon Mulai</label>
                <input type="datetime-local" class="ecoursity-form-input" x-model="course.price_sale_start">
            </div>

            <div class="ecoursity-form-group">
                <label class="ecoursity-form-label">Diskon Berakhir</label>
                <input type="datetime-local" class="ecoursity-form-input" x-model="course.price_sale_end">
            </div>
        </div>

        <div class="ecoursity-form-row">
            <div class="ecoursity-form-group">
                <label class="ecoursity-form-label">Max Siswa</label>
                <input type="number" class="ecoursity-form-input" x-model="course.max_students" min="0" placeholder="0 = tidak terbatas">
            </div>

            <div class="ecoursity-form-group">
                <label class="ecoursity-form-label">Evaluasi</label>
                <select class="ecoursity-form-select" x-model="course.course_evaluation">
                    <option value="">Pilih Evaluasi</option>
                    <option value="none">Tidak Ada</option>
                    <option value="quiz">Kuis</option>
                    <option value="assignment">Tugas</option>
                </select>
            </div>

            <div class="ecoursity-form-group">
                <label class="ecoursity-form-label">Nilai Lulus</label>
                <input type="number" class="ecoursity-form-input" x-model="course.passing_grade" min="0" max="100" placeholder="e.g. 70">
            </div>
        </div>

        <div class="ecoursity-form-actions">
            <button type="submit" class="ecoursity-button ecoursity-button--primary" :disabled="saving" x-text="saving ? 'Menyimpan...' : 'Simpan'"></button>
            <a href="<?php echo esc_url(get_admin_url(null, 'admin.php?page=ecoursity-courses')); ?>" class="ecoursity-button ecoursity-button--outline">Batal</a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('courseForm', (courseId, restUrl) => ({
            loading: true,
            saving: false,
            slugEditable: false,
            mediaUploader: null,
            message: '',
            message_type: 'success',
            course: {
                title: '',
                slug: '',
                status: 'draft',
                content: '',
                excerpt: '',
                duration_value: 1,
                duration_unit: 'week',
                level: '',
                max_students: '',
                price: '0',
                price_sale: '',
                price_sale_start: '',
                price_sale_end: '',
                course_evaluation: '',
                passing_grade: '',
            },
            async init() {
                await this.loadCourse();
            },
            openMediaUploader() {
                if (typeof wp === 'undefined' || typeof wp.media === 'undefined') return;
                if (this.mediaUploader) {
                    this.mediaUploader.open();
                    return;
                }
                this.mediaUploader = wp.media({
                    title: 'Pilih Gambar Unggulan',
                    button: {
                        text: 'Gunakan sebagai Gambar Unggulan'
                    },
                    multiple: false,
                });
                this.mediaUploader.on('select', () => {
                    const attachment = this.mediaUploader.state().get('selection').first().toJSON();
                    this.course.thumbnail_id = attachment.id;
                    this.course.thumbnail = attachment.url;
                });
                this.mediaUploader.open();
            },
            removeFeaturedImage() {
                this.course.thumbnail_id = 0;
                this.course.thumbnail = '';
            },
            parseDuration() {
                const d = this.course.duration;
                if (Array.isArray(d)) {
                    this.course.duration_value = d[0] || 1;
                    this.course.duration_unit = d[1] || 'week';
                } else if (typeof d === 'string' && d.includes(' ')) {
                    const parts = d.split(' ');
                    this.course.duration_value = parseInt(parts[0]) || 1;
                    this.course.duration_unit = parts[1] || 'week';
                }
                delete this.course.duration;
            },
            async loadCourse() {
                this.loading = true;
                try {
                    const res = await fetch(`${restUrl}${courseId}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                    });
                    const json = await res.json();
                    if (json.success && json.data) {
                        Object.assign(this.course, json.data);
                        this.parseDuration();
                    } else {
                        this.message = json.message || 'Gagal memuat data kursus.';
                        this.message_type = 'error';
                    }
                } catch (e) {
                    this.message = 'Gagal memuat data kursus.';
                    this.message_type = 'error';
                } finally {
                    this.loading = false;
                    this.$nextTick(() => this.syncEditorContent());
                }
            },
            syncEditorContent() {
                const id = 'ecoursity_course_content';
                if (typeof tinymce !== 'undefined' && tinymce.get(id)) {
                    tinymce.get(id).setContent(this.course.content || '');
                } else {
                    const ta = document.getElementById(id);
                    if (ta) ta.value = this.course.content || '';
                }
            },
            syncEditorToModel() {
                const id = 'ecoursity_course_content';
                if (typeof tinymce !== 'undefined' && tinymce.get(id)) {
                    this.course.content = tinymce.get(id).getContent();
                } else {
                    const ta = document.getElementById(id);
                    if (ta) this.course.content = ta.value;
                }
            },
            async submit() {
                this.course.duration = [this.course.duration_value, this.course.duration_unit];
                this.syncEditorToModel();
                this.saving = true;
                this.message = '';
                try {
                    const res = await fetch(`${restUrl}${courseId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify(this.course),
                    });
                    const json = await res.json();
                    if (json.success) {
                        this.message = json.message || 'Kursus berhasil disimpan.';
                        this.message_type = 'success';
                    } else {
                        this.message = json.message || 'Gagal menyimpan kursus.';
                        this.message_type = 'error';
                    }
                } catch (e) {
                    this.message = 'Gagal menyimpan kursus.';
                    this.message_type = 'error';
                } finally {
                    this.saving = false;
                }
            },
        }));
    });
</script>