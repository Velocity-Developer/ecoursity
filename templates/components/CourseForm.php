<?php
$course_id = $props['course_id'] ?? 0;
$rest_url  = get_rest_url(null, 'ecoursity/v1/courses/');
$sections_rest_url = get_rest_url(null, 'ecoursity/v1/sections/');
$course_view_base_url = trailingslashit(home_url('kursus'));

wp_enqueue_media();

$course_category_options = get_terms([
    'taxonomy'   => 'ecoursity_course_category',
    'hide_empty' => false,
]);

$curriculum_sections = [];

if ((int) $course_id > 0 && class_exists(\Ecoursity\App\Models\Section::class)) {
    $curriculum_sections = \Ecoursity\App\Models\Section::allByCourse((int) $course_id);

    foreach ($curriculum_sections as $section) {
        $section->items = array_map(static function (array $item): array {
            $lesson = null;

            if (($item['item_type'] ?? '') === 'lesson' && ! empty($item['item_id']) && class_exists(\Ecoursity\App\Models\Lesson::class)) {
                $lesson = \Ecoursity\App\Models\Lesson::find((int) $item['item_id']);
            }

            $item['title'] = $lesson?->title ?: 'Lesson #' . (int) ($item['item_id'] ?? 0);
            $item['status'] = $lesson?->status ?: '';

            return $item;
        }, $section->items ?? []);
    }
}

$curriculum_section_payload = array_map(static function ($section): array {
    return [
        'section_id' => (int) ($section->section_id ?? 0),
        'section_name' => (string) ($section->section_name ?? ''),
        'section_course_id' => (int) ($section->section_course_id ?? 0),
        'section_order' => (int) ($section->section_order ?? 0),
        'section_description' => (string) ($section->section_description ?? ''),
        'items' => array_values(array_map(static function (array $item): array {
            return [
                'section_item_id' => (int) ($item['section_item_id'] ?? 0),
                'section_id' => (int) ($item['section_id'] ?? 0),
                'item_id' => (int) ($item['item_id'] ?? 0),
                'item_order' => (int) ($item['item_order'] ?? 0),
                'item_type' => (string) ($item['item_type'] ?? ''),
                'title' => (string) ($item['title'] ?? ''),
                'status' => (string) ($item['status'] ?? ''),
            ];
        }, $section->items ?? [])),
    ];
}, $curriculum_sections);

$course_form_sections = apply_filters('ecoursity_course_form_sections', [
    [
        'type'   => 'field',
        'name'   => 'title',
        'label'  => 'Judul Kursus',
        'input'  => 'text',
        'class'  => 'ecoursity-form-input',
        'required' => true,
        'placeholder' => 'e.g. Belajar Laravel dari Nol',
        'default' => '',
    ],
    [
        'type' => 'special',
        'name' => 'slug',
    ],
    [
        'type' => 'special',
        'name' => 'thumbnail',
    ],
    [
        'type' => 'special',
        'name' => 'course_category',
    ],
    [
        'type' => 'special',
        'name' => 'course_tag',
    ],
    [
        'type'    => 'field',
        'name'    => 'status',
        'label'   => 'Status',
        'input'   => 'select',
        'class'   => 'ecoursity-form-select',
        'default' => 'draft',
        'options' => [
            ['value' => 'draft', 'label' => 'Draft'],
            ['value' => 'publish', 'label' => 'Publik'],
            ['value' => 'pending', 'label' => 'Pending'],
        ],
    ],
    [
        'type'   => 'row',
        'fields' => [
            [
                'type'    => 'field',
                'name'    => 'level',
                'label'   => 'Level',
                'input'   => 'select',
                'class'   => 'ecoursity-form-select',
                'default' => '',
                'options' => [
                    ['value' => '', 'label' => 'Pilih Level'],
                    ['value' => 'beginner', 'label' => 'Pemula'],
                    ['value' => 'intermediate', 'label' => 'Menengah'],
                    ['value' => 'advanced', 'label' => 'Lanjutan'],
                ],
            ],
            [
                'type' => 'special',
                'name' => 'duration',
            ],
        ],
    ],
    [
        'type' => 'special',
        'name' => 'content',
    ],
    [
        'type'   => 'field',
        'name'   => 'excerpt',
        'label'  => 'Ringkasan',
        'input'  => 'textarea',
        'class'  => 'ecoursity-form-textarea',
        'rows'   => 3,
        'placeholder' => 'Ringkasan singkat...',
        'default' => '',
    ],
    [
        'type'   => 'row',
        'fields' => [
            [
                'type' => 'field',
                'name' => 'price',
                'label' => 'Harga',
                'input' => 'text',
                'class' => 'ecoursity-form-input',
                'placeholder' => '0',
                'default' => '0',
            ],
            [
                'type' => 'field',
                'name' => 'price_sale',
                'label' => 'Harga Diskon',
                'input' => 'text',
                'class' => 'ecoursity-form-input',
                'placeholder' => 'Kosongkan jika tidak ada',
                'default' => '',
            ],
        ],
    ],
    [
        'type'   => 'row',
        'fields' => [
            [
                'type' => 'field',
                'name' => 'price_sale_start',
                'label' => 'Diskon Mulai',
                'input' => 'datetime-local',
                'class' => 'ecoursity-form-input',
                'default' => '',
            ],
            [
                'type' => 'field',
                'name' => 'price_sale_end',
                'label' => 'Diskon Berakhir',
                'input' => 'datetime-local',
                'class' => 'ecoursity-form-input',
                'default' => '',
            ],
        ],
    ],
    [
        'type'   => 'row',
        'fields' => [
            [
                'type' => 'field',
                'name' => 'max_students',
                'label' => 'Max Siswa',
                'input' => 'number',
                'class' => 'ecoursity-form-input',
                'min' => 0,
                'placeholder' => '0 = tidak terbatas',
                'default' => '',
            ],
            [
                'type'    => 'field',
                'name'    => 'course_evaluation',
                'label'   => 'Evaluasi',
                'input'   => 'select',
                'class'   => 'ecoursity-form-select',
                'default' => '',
                'options' => [
                    ['value' => '', 'label' => 'Pilih Evaluasi'],
                    ['value' => 'none', 'label' => 'Tidak Ada'],
                    ['value' => 'quiz', 'label' => 'Kuis'],
                    ['value' => 'assignment', 'label' => 'Tugas'],
                ],
            ],
            [
                'type' => 'field',
                'name' => 'passing_grade',
                'label' => 'Nilai Lulus',
                'input' => 'number',
                'class' => 'ecoursity-form-input',
                'min' => 0,
                'max' => 100,
                'placeholder' => 'e.g. 70',
                'default' => '',
            ],
        ],
    ],
]);

$collect_defaults = static function (array $items) use (&$collect_defaults): array {
    $defaults = [];

    foreach ($items as $item) {
        if (($item['type'] ?? '') === 'row') {
            $defaults = array_merge($defaults, $collect_defaults($item['fields'] ?? []));
            continue;
        }

        if (($item['type'] ?? '') !== 'field' || empty($item['name'])) {
            continue;
        }

        $defaults[$item['name']] = $item['default'] ?? '';
    }

    return $defaults;
};

$course_defaults = array_merge([
    'slug' => '',
    'content' => '',
    'thumbnail' => '',
    'thumbnail_id' => 0,
    'duration_value' => 1,
    'duration_unit' => 'week',
    'course_category_ids' => [],
    'course_tags' => [],
    'course_tags_input' => '',
], $collect_defaults($course_form_sections));
?>

<div
    x-data="courseForm(
        <?php echo (int) $course_id; ?>,
        '<?php echo esc_js($rest_url); ?>',
        '<?php echo esc_js($sections_rest_url); ?>',
        <?php echo esc_attr(wp_json_encode($course_defaults)); ?>,
        <?php echo esc_attr(wp_json_encode($curriculum_section_payload)); ?>,
        '<?php echo esc_js($course_view_base_url); ?>'
    )"
    x-cloak>
    <template x-if="loading">
        <p class="ecoursity-form-loading">Memuat data kursus...</p>
    </template>

    <form x-show="!loading" @submit.prevent="submit" class="ecoursity-course-form">

        <div x-show="message" class="ecoursity-form-message" :class="'ecoursity-form-message--' + message_type" x-text="message"></div>

        <?php
        $render_field = static function (array $field): void {
            $name        = $field['name'] ?? '';
            $label       = $field['label'] ?? '';
            $input       = $field['input'] ?? 'text';
            $class       = $field['class'] ?? 'ecoursity-form-input';
            $placeholder = $field['placeholder'] ?? '';
            $required    = !empty($field['required']);
            $rows        = (int) ($field['rows'] ?? 3);
            $min         = $field['min'] ?? null;
            $max         = $field['max'] ?? null;
            $step        = $field['step'] ?? null;
            $options     = $field['options'] ?? [];

            if ($name === '' || $label === '') {
                return;
            }
        ?>
            <div class="ecoursity-form-group">
                <label class="ecoursity-form-label">
                    <?php echo esc_html($label); ?>
                    <?php if ($required) : ?>
                        <span class="ecoursity-required">*</span>
                    <?php endif; ?>
                </label>

                <?php if ($input === 'textarea') : ?>
                    <textarea
                        class="<?php echo esc_attr($class); ?>"
                        x-model="course.<?php echo esc_attr($name); ?>"
                        rows="<?php echo esc_attr((string) $rows); ?>"
                        <?php if ($placeholder !== '') : ?>placeholder="<?php echo esc_attr($placeholder); ?>" <?php endif; ?>
                        <?php if ($required) : ?>required<?php endif; ?>></textarea>
                <?php elseif ($input === 'select') : ?>
                    <select
                        class="<?php echo esc_attr($class); ?>"
                        x-model="course.<?php echo esc_attr($name); ?>"
                        <?php if ($required) : ?>required<?php endif; ?>>
                        <?php foreach ($options as $option) : ?>
                            <option value="<?php echo esc_attr((string) ($option['value'] ?? '')); ?>"><?php echo esc_html((string) ($option['label'] ?? '')); ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php else : ?>
                    <input
                        type="<?php echo esc_attr($input); ?>"
                        class="<?php echo esc_attr($class); ?>"
                        x-model="course.<?php echo esc_attr($name); ?>"
                        <?php if ($placeholder !== '') : ?>placeholder="<?php echo esc_attr($placeholder); ?>" <?php endif; ?>
                        <?php if ($min !== null) : ?>min="<?php echo esc_attr((string) $min); ?>" <?php endif; ?>
                        <?php if ($max !== null) : ?>max="<?php echo esc_attr((string) $max); ?>" <?php endif; ?>
                        <?php if ($step !== null) : ?>step="<?php echo esc_attr((string) $step); ?>" <?php endif; ?>
                        <?php if ($required) : ?>required<?php endif; ?>>
                <?php endif; ?>

                <?php do_action('ecoursity_course_form_after_field', $field); ?>
            </div>
        <?php
        };

        ?>
        <div class="ecoursity-course-form__tabs" role="tablist" aria-label="Tab form kursus">
            <button type="button" class="ecoursity-course-form__tab" :class="{ 'is-active': currentTab === 'summary' }" @click="currentTab = 'summary'">Ringkasan</button>
            <button type="button" class="ecoursity-course-form__tab" :class="{ 'is-active': currentTab === 'curriculum' }" @click="currentTab = 'curriculum'">Kurikulum</button>
        </div>

        <div x-show="currentTab === 'summary'" class="ecoursity-course-form__tab-panel">
            <div class="ecoursity-course-form__layout">
                <div class="ecoursity-course-form__main">
                    <?php
                    foreach ($course_form_sections as $section) :
                        $section_type = $section['type'] ?? '';
                        $section_name = $section['name'] ?? '';

                        if (in_array($section_name, ['slug', 'thumbnail', 'course_category', 'course_tag', 'status', 'excerpt'], true)) {
                            continue;
                        }

                        if ($section_type === 'field') {
                            $render_field($section);
                            continue;
                        }

                        if ($section_type === 'row') :
                    ?>
                            <div class="ecoursity-form-row">
                                <?php
                                foreach (($section['fields'] ?? []) as $field) {
                                    $field_type = $field['type'] ?? 'field';

                                    if ($field_type === 'field') {
                                        $render_field($field);
                                        continue;
                                    }

                                    if ($field_type !== 'special') {
                                        continue;
                                    }

                                    $special_name = $field['name'] ?? '';

                                    if ($special_name === 'duration') :
                                ?>
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
                                <?php
                                    endif;
                                }
                                ?>
                            </div>
                        <?php
                            continue;
                        endif;

                        if ($section_type !== 'special') {
                            continue;
                        }

                        if ($section_name === 'content') :
                        ?>
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
                    <?php
                        endif;
                    endforeach;
                    ?>
                </div>

                <aside class="ecoursity-course-form__aside">
                    <?php foreach ($course_form_sections as $section) : ?>
                        <?php
                        $section_type = $section['type'] ?? '';
                        $section_name = $section['name'] ?? '';

                        if ($section_type === 'field' && in_array($section_name, ['status', 'excerpt'], true)) {
                            $render_field($section);
                            continue;
                        }

                        if ($section_type !== 'special') {
                            continue;
                        }

                        if ($section_name === 'slug') :
                        ?>
                            <div class="ecoursity-form-group ecoursity-form-group--aside">
                                <label class="ecoursity-form-label">Slug</label>
                                <div class="ecoursity-form-slug">
                                    <span x-show="!slugEditable" @click="slugEditable = true" class="ecoursity-form-slug__text" x-text="course.slug || '(kosong)'"></span>
                                    <input x-show="slugEditable" type="text" class="ecoursity-form-input" x-model="course.slug" @click.outside="slugEditable = false" @keydown.enter="slugEditable = false" @keydown.escape="slugEditable = false" placeholder="Otomatis jika kosong">
                                </div>
                            </div>
                        <?php
                            continue;
                        endif;

                        if ($section_name === 'thumbnail') :
                        ?>
                            <div class="ecoursity-form-group ecoursity-form-group--aside">
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
                        <?php
                            continue;
                        endif;

                        if ($section_name === 'course_category') :
                        ?>
                            <div class="ecoursity-form-group ecoursity-form-group--aside">
                                <label class="ecoursity-form-label">Kategori Kursus</label>
                                <div class="ecoursity-form-select--multiple">
                                    <?php foreach ($course_category_options as $category) : ?>
                                        <label class="ecoursity-checkbox-option">
                                            <input type="checkbox" value="<?php echo esc_attr((string) $category->term_id); ?>" x-model="course.course_category_ids">
                                            <span><?php echo esc_html($category->name); ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php
                            continue;
                        endif;

                        if ($section_name === 'course_tag') :
                        ?>
                            <div class="ecoursity-form-group ecoursity-form-group--aside">
                                <label class="ecoursity-form-label">Tag Kursus</label>
                                <input type="text" class="ecoursity-form-input" x-model="course.course_tags_input" placeholder="Pisahkan dengan koma">
                            </div>
                    <?php
                        endif;
                    endforeach;
                    ?>
                </aside>
            </div>
        </div>

        <div x-show="currentTab === 'curriculum'" class="ecoursity-course-form__tab-panel">
            <div class="ecoursity-curriculum">
                <template x-if="!currentCourseId">
                    <div class="ecoursity-curriculum__empty">
                        Simpan kursus dulu. Setelah itu section dan lesson bisa tampil di sini.
                    </div>
                </template>

                <template x-if="currentCourseId">
                    <div class="ecoursity-curriculum__body">
                        <div class="ecoursity-curriculum__create-card">
                            <div class="ecoursity-curriculum__create-fields">
                                <div class="ecoursity-form-group">
                                    <label class="ecoursity-form-label">Title Sesi</label>
                                    <input type="text" class="ecoursity-form-input" x-model="newSectionTitle" placeholder="Masukkan title sesi">
                                </div>
                                <button type="button" class="ecoursity-button ecoursity-button--primary ecoursity-button--fit" :disabled="sectionCreating" @click="createSection()" x-text="sectionCreating ? 'Menyimpan...' : 'Tambah Sesi'"></button>
                            </div>
                        </div>

                        <template x-if="!curriculumSections.length">
                            <div class="ecoursity-curriculum__empty">
                                Belum ada section untuk kursus ini.
                            </div>
                        </template>

                        <div class="ecoursity-curriculum__accordion" x-show="curriculumSections.length">
                            <template x-for="(section, index) in curriculumSections" :key="section.section_id">
                                <div class="ecoursity-curriculum__section">
                                    <button type="button" class="ecoursity-curriculum__section-toggle" @click="toggleSection(section.section_id)" :aria-expanded="isSectionOpen(section.section_id).toString()">
                                        <span>
                                            <strong x-text="section.section_name"></strong>
                                            <small x-text="`${section.items.length} lesson`"></small>
                                        </span>
                                        <span class="ecoursity-curriculum__chevron" :class="{ 'is-open': isSectionOpen(section.section_id) }">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708" />
                                            </svg>
                                        </span>
                                    </button>

                                    <div x-show="isSectionOpen(section.section_id)" x-collapse>
                                        <div class="ecoursity-curriculum__section-form">
                                            <div class="ecoursity-form-group">
                                                <label class="ecoursity-form-label">Deskripsi Section</label>
                                                <textarea class="ecoursity-form-textarea" rows="4" x-model="section.section_description" placeholder="Tulis deskripsi section"></textarea>
                                            </div>

                                            <div class="ecoursity-curriculum__section-actions">
                                                <button type="button" class="ecoursity-button ecoursity-button--primary ecoursity-button--fit" :disabled="sectionUpdatingId === section.section_id" @click="updateSection(section)" x-text="sectionUpdatingId === section.section_id ? 'Menyimpan...' : 'Update'"></button>
                                            </div>
                                        </div>

                                        <template x-if="!section.items.length">
                                            <div class="ecoursity-curriculum__empty ecoursity-curriculum__empty--inner">Belum ada lesson di section ini.</div>
                                        </template>

                                        <ol class="ecoursity-curriculum__lessons" x-show="section.items.length">
                                            <template x-for="item in section.items" :key="`${section.section_id}-${item.section_item_id}-${item.item_id}`">
                                                <li class="ecoursity-curriculum__lesson">
                                                    <span class="ecoursity-curriculum__lesson-title" x-text="item.title || 'Lesson'"></span>
                                                    <span class="ecoursity-curriculum__lesson-status" x-show="item.status" x-text="formatStatus(item.status)"></span>
                                                </li>
                                            </template>
                                        </ol>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="ecoursity-form-actions">
            <button type="submit" class="ecoursity-button ecoursity-button--primary" :disabled="saving" x-text="saving ? 'Menyimpan...' : 'Simpan'"></button>
            <a
                x-show="viewUrl"
                :href="viewUrl"
                target="_blank"
                rel="noopener noreferrer"
                class="ecoursity-button ecoursity-button--outline">
                Lihat Kursus
            </a>
            <a href="<?php echo esc_url(get_admin_url(null, 'admin.php?page=ecoursity-courses')); ?>" class="ecoursity-button ecoursity-button--outline">Batal</a>
        </div>
    </form>
</div>

<style>
    .ecoursity-course-form__tabs {
        display: flex;
        gap: 12px;
        padding-bottom: 8px;
        border-bottom: 1px solid #e8e8e8;
    }

    .ecoursity-course-form__tab {
        padding: 10px 16px;
        border: 1px solid #c2c2c2;
        border-radius: 999px;
        background: #ffffff;
        color: #1a1a1a;
        cursor: pointer;
        font: inherit;
    }

    .ecoursity-course-form__tab.is-active {
        background: #024ad8;
        border-color: #024ad8;
        color: #ffffff;
    }

    .ecoursity-course-form__tab-panel {
        min-width: 0;
    }

    .ecoursity-curriculum,
    .ecoursity-curriculum__body,
    .ecoursity-curriculum__accordion {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .ecoursity-curriculum__create-card {
        padding: 20px;
        border: 1px solid #e8e8e8;
        border-radius: 12px;
        background: #ffffff;
    }

    .ecoursity-curriculum__create-fields {
        display: flex;
        align-items: end;
        gap: 16px;
    }

    .ecoursity-curriculum__section {
        border: 1px solid #e8e8e8;
        border-radius: 12px;
        background: #ffffff;
        overflow: hidden;
    }

    .ecoursity-curriculum__section-toggle {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 16px 20px;
        border: 0;
        background: #f7f7f7;
        color: #1a1a1a;
        cursor: pointer;
        text-align: left;
        font: inherit;
    }

    .ecoursity-curriculum__section-toggle strong,
    .ecoursity-curriculum__section-toggle small {
        display: block;
    }

    .ecoursity-curriculum__section-toggle small {
        margin-top: 4px;
        color: #636363;
    }

    .ecoursity-curriculum__chevron {
        font-size: 20px;
        line-height: 1;
        transition: transform .2s ease;
    }

    .ecoursity-curriculum__chevron.is-open {
        transform: rotate(180deg);
    }

    .ecoursity-curriculum__section-form {
        padding: 16px 20px 0;
    }

    .ecoursity-curriculum__section-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 12px;
    }

    .ecoursity-curriculum__lessons {
        margin: 0;
        padding: 16px 20px 20px 40px;
    }

    .ecoursity-curriculum__lesson {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .ecoursity-curriculum__lesson:last-child {
        border-bottom: 0;
    }

    .ecoursity-curriculum__lesson-status {
        padding: 4px 10px;
        border-radius: 999px;
        background: #f1f5f9;
        color: #334155;
        font-size: 12px;
        white-space: nowrap;
    }

    .ecoursity-curriculum__empty {
        padding: 20px;
        border: 1px dashed #c2c2c2;
        border-radius: 12px;
        background: #f7f7f7;
        color: #636363;
    }

    .ecoursity-curriculum__empty--inner {
        margin: 16px 20px 20px;
        padding: 12px 16px;
    }

    .ecoursity-button--fit {
        width: fit-content;
        height: fit-content;
    }

    @media (max-width: 640px) {
        .ecoursity-curriculum__create-fields {
            flex-direction: column;
            align-items: stretch;
        }

        .ecoursity-button--fit {
            width: 100%;
        }
    }
</style>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('courseForm', (courseId, restUrl, sectionsRestUrl, defaults, initialSections, courseViewBaseUrl) => ({
            loading: true,
            saving: false,
            sectionCreating: false,
            sectionUpdatingId: 0,
            slugEditable: false,
            mediaUploader: null,
            message: '',
            message_type: 'success',
            currentTab: 'summary',
            currentCourseId: parseInt(courseId, 10) || 0,
            courseViewBaseUrl,
            newSectionTitle: '',
            openSectionIds: [],
            curriculumSections: Array.isArray(initialSections) ?
                initialSections.map((section) => ({
                    ...section,
                    section_id: parseInt(section.section_id, 10) || 0,
                    items: Array.isArray(section.items) ? section.items : [],
                })) : [],
            get viewUrl() {
                if (!this.currentCourseId || !this.course.slug) {
                    return '';
                }

                return `${this.courseViewBaseUrl}${this.course.slug}/`;
            },
            course: {
                ...defaults,
            },
            async init() {
                if (this.curriculumSections.length) {
                    this.openSectionIds = [this.curriculumSections[0].section_id];
                }

                if (!this.currentCourseId) {
                    this.parseDuration();
                    this.syncTaxonomies();
                    this.loading = false;
                    this.$nextTick(() => this.syncEditorContent());
                    return;
                }

                await this.loadCourse();
            },
            isSectionOpen(sectionId) {
                return this.openSectionIds.includes(sectionId);
            },
            toggleSection(sectionId) {
                if (this.isSectionOpen(sectionId)) {
                    this.openSectionIds = this.openSectionIds.filter((id) => id !== sectionId);
                    return;
                }

                this.openSectionIds = [...this.openSectionIds, sectionId];
            },
            formatStatus(status) {
                if (!status) {
                    return '';
                }

                return status.charAt(0).toUpperCase() + status.slice(1);
            },
            async createSection() {
                const title = this.newSectionTitle.trim();

                if (!this.currentCourseId) {
                    this.message = 'Simpan kursus dulu sebelum tambah sesi.';
                    this.message_type = 'error';
                    return;
                }

                if (!title) {
                    this.message = 'Title sesi wajib diisi.';
                    this.message_type = 'error';
                    return;
                }

                this.sectionCreating = true;
                this.message = '';

                try {
                    const res = await fetch(sectionsRestUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            course_id: this.currentCourseId,
                            title,
                        }),
                    });
                    const json = await res.json();

                    if (json.success && json.data) {
                        this.curriculumSections = [
                            ...this.curriculumSections,
                            {
                                ...json.data,
                                items: Array.isArray(json.data.items) ? json.data.items : [],
                            },
                        ];
                        this.newSectionTitle = '';
                        this.openSectionIds = [...this.openSectionIds, json.data.section_id];
                        this.message = json.message || 'Section berhasil dibuat.';
                        this.message_type = 'success';
                        this.currentTab = 'curriculum';
                    } else {
                        this.message = json.message || 'Gagal membuat section.';
                        this.message_type = 'error';
                    }
                } catch (e) {
                    this.message = 'Gagal membuat section.';
                    this.message_type = 'error';
                } finally {
                    this.sectionCreating = false;
                }
            },
            async updateSection(section) {
                if (!section?.section_id) {
                    return;
                }

                this.sectionUpdatingId = section.section_id;
                this.message = '';

                try {
                    const res = await fetch(`${sectionsRestUrl}${section.section_id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            description: section.section_description || '',
                            title: section.section_name || '',
                        }),
                    });
                    const json = await res.json();

                    if (json.success && json.data) {
                        this.curriculumSections = this.curriculumSections.map((item) => {
                            if (item.section_id !== json.data.section_id) {
                                return item;
                            }

                            return {
                                ...item,
                                ...json.data,
                                items: Array.isArray(item.items) && item.items.length ? item.items : (Array.isArray(json.data.items) ? json.data.items : []),
                            };
                        });
                        this.message = json.message || 'Section berhasil diupdate.';
                        this.message_type = 'success';
                    } else {
                        this.message = json.message || 'Gagal update section.';
                        this.message_type = 'error';
                    }
                } catch (e) {
                    this.message = 'Gagal update section.';
                    this.message_type = 'error';
                } finally {
                    this.sectionUpdatingId = 0;
                }
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
            syncTaxonomies() {
                this.course.course_category_ids = Array.isArray(this.course.course_category_ids) ?
                    this.course.course_category_ids.map((id) => String(id)) : [];
                this.course.course_tags = Array.isArray(this.course.course_tags) ?
                    this.course.course_tags : [];
                this.course.course_tags_input = this.course.course_tags.join(', ');
            },
            async loadCourse() {
                this.loading = true;
                try {
                    const res = await fetch(`${restUrl}${this.currentCourseId}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                    });
                    const json = await res.json();
                    if (json.success && json.data) {
                        Object.assign(this.course, json.data);
                        this.parseDuration();
                        this.syncTaxonomies();
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
                this.course.course_category_ids = Array.isArray(this.course.course_category_ids) ?
                    this.course.course_category_ids
                    .map((id) => parseInt(id, 10))
                    .filter((id) => Number.isInteger(id) && id > 0) : [];
                this.course.course_tags = this.course.course_tags_input
                    .split(',')
                    .map((tag) => tag.trim())
                    .filter(Boolean);
                this.syncEditorToModel();
                this.saving = true;
                this.message = '';
                try {
                    const endpoint = this.currentCourseId ? `${restUrl}${this.currentCourseId}` : restUrl;
                    const method = this.currentCourseId ? 'PUT' : 'POST';
                    const res = await fetch(endpoint, {
                        method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify(this.course),
                    });
                    const json = await res.json();
                    if (json.success) {
                        if (json.data?.id) {
                            this.currentCourseId = parseInt(json.data.id, 10) || this.currentCourseId;
                            Object.assign(this.course, json.data);
                            this.parseDuration();
                            this.syncTaxonomies();
                            this.$nextTick(() => this.syncEditorContent());
                        }
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