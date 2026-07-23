<?php
wp_enqueue_editor();
$course_id = $props['course_id'] ?? 0;
$rest_url  = get_rest_url(null, 'ecoursity/v1/courses/');
$sections_rest_url = get_rest_url(null, 'ecoursity/v1/sections/');
$lessons_rest_url = get_rest_url(null, 'ecoursity/v1/lessons/');
$lesson_form_component_url = get_rest_url(null, 'ecoursity/v1/template_component/LessonForm');
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
        '<?php echo esc_js($lessons_rest_url); ?>',
        '<?php echo esc_js($lesson_form_component_url); ?>',
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
                                    <input type="text" class="ecoursity-form-input" x-model="newSectionTitle" placeholder="Buat sesi baru">
                                </div>
                                <button type="button" class="ecoursity-button ecoursity-button--primary ecoursity-button--fit" :disabled="sectionCreating" @click="createSection()" x-text="sectionCreating ? 'Menyimpan...' : 'Tambah Sesi'"></button>
                            </div>
                        </div>

                        <template x-if="!curriculumSections.length">
                            <div class="ecoursity-curriculum__empty">
                                Belum ada section untuk kursus ini.
                            </div>
                        </template>

                        <div
                            class="ecoursity-curriculum__accordion"
                            x-show="curriculumSections.length"
                            x-sort="sortSectionsFromDom($el)"
                            x-sort:config="{ handle: '.ecoursity-curriculum__section-sort-handle' }">
                            <template x-for="section in curriculumSections" :key="section.section_id">
                                <div class="ecoursity-curriculum__section" x-sort:item="section.section_id" :data-section-id="section.section_id">
                                    <button type="button" class="ecoursity-curriculum__section-toggle" @click="toggleSection(section.section_id)" :aria-expanded="isSectionOpen(section.section_id).toString()">
                                        <span class="ecoursity-curriculum__sort-handle ecoursity-curriculum__section-sort-handle" @click.stop.prevent aria-label="Urutkan sesi" title="Urutkan sesi">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 16 16" aria-hidden="true">
                                                <path d="M5.333 3.333H5.34M10.667 3.333H10.674M5.333 8H5.34M10.667 8H10.674M5.333 12.667H5.34M10.667 12.667H10.674" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                            </svg>
                                        </span>
                                        <span class="ecoursity-curriculum__section-summary">
                                            <strong x-text="section.section_name"></strong>
                                            <small x-text="`${section.items.length} lesson`"></small>
                                        </span>
                                        <span class="ecoursity-curriculum__chevron" :class="{ 'is-open': isSectionOpen(section.section_id) }">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708" />
                                            </svg>
                                        </span>
                                    </button>

                                    <div x-show="isSectionOpen(section.section_id)">
                                        <div class="ecoursity-curriculum__section-form">
                                            <div class="ecoursity-form-group">
                                                <label class="ecoursity-form-label">Deskripsi Section</label>
                                                <textarea class="ecoursity-form-textarea" rows="4" x-model="section.section_description" placeholder="Tulis deskripsi section"></textarea>
                                            </div>

                                            <div class="ecoursity-curriculum__section-actions">
                                                <button type="button" class="ecoursity-button ecoursity-button--outline ecoursity-button--fit" @click="openLessonFormModal(section)">Tambah Materi</button>
                                                <button type="button" class="ecoursity-button ecoursity-button--primary ecoursity-button--fit" :disabled="sectionUpdatingId === section.section_id" @click="updateSection(section)" x-text="sectionUpdatingId === section.section_id ? 'Menyimpan...' : 'Update'"></button>
                                            </div>
                                        </div>

                                        <template x-if="!section.items.length">
                                            <div class="ecoursity-curriculum__empty ecoursity-curriculum__empty--inner">Belum ada lesson di section ini.</div>
                                        </template>

                                        <ol
                                            class="ecoursity-curriculum__lessons"
                                            x-show="section.items.length"
                                            x-sort="sortLessonsFromDom(section, $el)"
                                            x-sort:config="{ handle: '.ecoursity-curriculum__lesson-sort-handle' }">
                                            <template x-for="item in section.items" :key="`${section.section_id}-${item.section_item_id}-${item.item_id}`">
                                                <li class="ecoursity-curriculum__lesson" x-sort:item="item.section_item_id || item.item_id" :data-section-item-key="item.section_item_id || item.item_id">
                                                    <div class="ecoursity-curriculum__lesson-main">
                                                        <span class="ecoursity-curriculum__sort-handle ecoursity-curriculum__sort-handle--lesson ecoursity-curriculum__lesson-sort-handle" @click.stop.prevent aria-label="Urutkan lesson" title="Urutkan lesson">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 16 16" aria-hidden="true">
                                                                <path d="M5.333 3.333H5.34M10.667 3.333H10.674M5.333 8H5.34M10.667 8H10.674M5.333 12.667H5.34M10.667 12.667H10.674" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                                            </svg>
                                                        </span>
                                                        <span class="ecoursity-curriculum__lesson-title" x-text="item.title || 'Lesson'"></span>
                                                        <span class="ecoursity-curriculum__lesson-status" x-show="item.status" x-text="formatStatus(item.status)"></span>
                                                    </div>

                                                    <div class="ecoursity-curriculum__lesson-actions">
                                                        <button type="button" class="ecoursity-curriculum__lesson-action" @click="editLesson(section, item)" aria-label="Edit lesson" title="Edit lesson">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 16 16" aria-hidden="true">
                                                                <path d="M11.333 2.00013C11.5081 1.82403 11.7162 1.6843 11.9454 1.58895C12.1747 1.49359 12.4205 1.44446 12.6688 1.44434C12.9171 1.44421 13.1629 1.49309 13.3922 1.58821C13.6215 1.68333 13.8297 1.82285 14.005 1.99876C14.1802 2.17467 14.3192 2.38346 14.414 2.61328C14.5088 2.84311 14.5575 3.08943 14.5573 3.33818C14.5571 3.58693 14.5081 3.83317 14.413 4.06284C14.3179 4.29252 14.1785 4.5011 14.003 4.67676L5.17033 13.5101L1.33301 14.6668L2.48967 10.8295L11.333 2.00013Z" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>
                                                        </button>
                                                        <button type="button" class="ecoursity-curriculum__lesson-action ecoursity-curriculum__lesson-action--danger" @click="deleteLesson(item)" aria-label="Hapus lesson" title="Hapus lesson">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 16 16" aria-hidden="true">
                                                                <path d="M2.66699 4.00016H13.3337" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                                                <path d="M6.66699 7.3335V11.3335" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                                                <path d="M9.33301 7.3335V11.3335" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                                                <path d="M3.33301 4.00016L4.00004 12.0002C4.02842 12.3874 4.20239 12.7494 4.48691 13.0136C4.77143 13.2778 5.14515 13.4246 5.53301 13.4252H10.467C10.8549 13.4246 11.2286 13.2778 11.5131 13.0136C11.7976 12.7494 11.9716 12.3874 12 12.0002L12.667 4.00016" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                                                <path d="M6 4.00016V2.66683C6 2.49002 6.07024 2.32045 6.19526 2.19542C6.32029 2.0704 6.48986 2.00016 6.66667 2.00016H9.33333C9.51014 2.00016 9.67971 2.0704 9.80474 2.19542C9.92976 2.32045 10 2.49002 10 2.66683V4.00016" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>
                                                        </button>
                                                    </div>
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
        display: inline-flex;
        gap: 4px;
        padding: 4px;
        margin-bottom: 16px;
        border: 1px solid #e8e8e8;
        border-radius: 9999px;
        background: #f7f7f7;
    }

    .ecoursity-course-form__tab {
        padding: 10px 18px;
        border: 0;
        border-radius: 9999px;
        background: transparent;
        color: #636363;
        cursor: pointer;
        font: inherit;
        font-weight: 600;
        line-height: 1.2;
        transition: background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
    }

    .ecoursity-course-form__tab:hover {
        color: #1a1a1a;
    }

    .ecoursity-course-form__tab.is-active {
        background: #ffffff;
        color: #024ad8;
        box-shadow: 0 1px 2px rgba(26, 26, 26, 0.08);
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

    .ecoursity-curriculum__section-summary {
        flex: 1 1 auto;
        min-width: 0;
    }

    .ecoursity-curriculum__chevron {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
        transition: transform .2s ease;
    }

    .ecoursity-curriculum__chevron.is-open {
        transform: rotate(180deg);
    }

    .ecoursity-curriculum__sort-handle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        flex: 0 0 28px;
        border-radius: 4px;
        color: #858585;
        cursor: grab;
    }

    .ecoursity-curriculum__sort-handle:active {
        cursor: grabbing;
    }

    .ecoursity-curriculum__sort-handle:hover {
        color: #024ad8;
        background: #ffffff;
    }

    .ecoursity-curriculum__section-form {
        padding: 16px 20px 0;
    }

    .ecoursity-curriculum__section-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
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

    .ecoursity-curriculum__lesson-main {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .ecoursity-curriculum__lesson-title {
        color: #1a1a1a;
    }

    .ecoursity-curriculum__sort-handle--lesson:hover {
        background: #f7f7f7;
    }

    .ecoursity-curriculum__lesson:last-child {
        border-bottom: 0;
    }

    .ecoursity-curriculum__lesson-actions {
        display: inline-flex;
        gap: 8px;
        flex-shrink: 0;
    }

    .ecoursity-curriculum__lesson-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        padding: 0;
        border: 1px solid #e8e8e8;
        border-radius: 4px;
        background: #ffffff;
        color: #636363;
        cursor: pointer;
    }

    .ecoursity-curriculum__lesson-action:hover {
        color: #024ad8;
        border-color: #c9e0fc;
        background: #f7f7f7;
    }

    .ecoursity-curriculum__lesson-action--danger:hover {
        color: #b3262b;
        border-color: #f1c7ca;
        background: #fff5f5;
    }

    .ecoursity-curriculum__lesson-status {
        padding: 4px 10px;
        border-radius: 999px;
        background: #f1f5f9;
        color: #334155;
        font-size: 12px;
        white-space: nowrap;
    }

    .sortable-ghost {
        opacity: .45 !important;
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

        .ecoursity-curriculum__create-fields,
        .ecoursity-curriculum__section-actions,
        .ecoursity-curriculum__lesson {
            flex-direction: column;
            align-items: stretch;
        }

        .ecoursity-curriculum__lesson-main,
        .ecoursity-curriculum__lesson-actions {
            justify-content: space-between;
        }

        .ecoursity-button--fit {
            width: 100%;
        }
    }
</style>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('courseForm', (courseId, restUrl, sectionsRestUrl, lessonsRestUrl, lessonFormComponentUrl, defaults, initialSections, courseViewBaseUrl) => ({
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
            lessonFormComponentUrl,
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
                window.addEventListener('ecoursity:lesson-saved', async () => {
                    await this.reloadCurriculum();
                });

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
            reorderByKeys(items, orderedKeys, keyResolver) {
                const keyedItems = new Map(items.map((item) => [String(keyResolver(item)), item]));
                const orderedItems = orderedKeys
                    .map((key) => keyedItems.get(String(key)))
                    .filter(Boolean);

                return orderedItems.length === items.length ? orderedItems : items;
            },
            readSortableKeys(sortableElement, dataKey) {
                const ignoredClasses = ['sortable-ghost', 'sortable-drag', 'sortable-fallback'];

                return Array.from(sortableElement.children)
                    .filter((element) => !ignoredClasses.some((className) => element.classList.contains(className)))
                    .map((element) => element.dataset[dataKey])
                    .filter(Boolean);
            },
            afterSortSettled(callback) {
                window.requestAnimationFrame(() => {
                    window.requestAnimationFrame(callback);
                });
            },
            normalizeSectionOrders() {
                this.curriculumSections = this.curriculumSections.map((section, index) => ({
                    ...section,
                    section_order: index,
                }));
            },
            normalizeLessonOrders(section) {
                section.items = Array.isArray(section.items) ? section.items.map((item, index) => ({
                    ...item,
                    section_id: section.section_id,
                    item_order: index,
                })) : [];
            },
            sortSectionsFromDom(sortableElement) {
                this.afterSortSettled(() => {
                    const orderedSectionIds = this.readSortableKeys(sortableElement, 'sectionId');

                    this.curriculumSections = this.reorderByKeys(
                        this.curriculumSections,
                        orderedSectionIds,
                        (section) => section.section_id
                    );
                    this.normalizeSectionOrders();
                });
            },
            sortLessonsFromDom(section, sortableElement) {
                if (!section || !Array.isArray(section.items)) {
                    return;
                }

                this.afterSortSettled(() => {
                    const orderedItemKeys = this.readSortableKeys(sortableElement, 'sectionItemKey');

                    section.items = this.reorderByKeys(
                        section.items,
                        orderedItemKeys,
                        (item) => item.section_item_id || item.item_id
                    );
                    this.normalizeLessonOrders(section);
                });
            },
            getCurriculumOrderPayload() {
                this.normalizeSectionOrders();
                this.curriculumSections.forEach((section) => this.normalizeLessonOrders(section));

                return this.curriculumSections.map((section) => ({
                    section_id: section.section_id,
                    section_order: section.section_order,
                    items: Array.isArray(section.items) ?
                        section.items.map((item, index) => ({
                            item_id: item.item_id,
                            item_type: item.item_type || 'lesson',
                            item_order: index,
                        })) : [],
                }));
            },
            openLessonFormModal(section) {
                if (!window.Alpine?.store('EcoursityUiModal')) {
                    return;
                }

                const params = new URLSearchParams({
                    course_id: String(this.currentCourseId),
                    section_id: String(section.section_id || 0),
                });

                window.Alpine.store('EcoursityUiModal').open({
                    title: `Tambah Materi - ${section.section_name || 'Section'}`,
                    url: `${this.lessonFormComponentUrl}?${params.toString()}`,
                });
            },
            editLesson(section, item) {
                if (!window.Alpine?.store('EcoursityUiModal') || !item?.item_id) {
                    return;
                }

                const params = new URLSearchParams({
                    lesson_id: String(item.item_id),
                    course_id: String(this.currentCourseId),
                    section_id: String(section?.section_id || item.section_id || 0),
                });

                window.Alpine.store('EcoursityUiModal').open({
                    title: `Edit Lesson - ${item.title || 'Lesson'}`,
                    url: `${this.lessonFormComponentUrl}?${params.toString()}`,
                });
            },
            async deleteLesson(item) {
                const lessonId = parseInt(item?.item_id, 10) || 0;

                if (lessonId < 1) {
                    return;
                }

                if (!window.confirm(`Hapus lesson "${item.title || 'Lesson'}"?`)) {
                    return;
                }

                this.message = '';

                try {
                    const res = await fetch(`${lessonsRestUrl}${lessonId}`, {
                        method: 'DELETE',
                        headers: this.getAuthHeaders(),
                    });
                    const json = await res.json();

                    if (json.success) {
                        await this.reloadCurriculum();
                        this.message = json.message || 'Lesson berhasil dihapus.';
                        this.message_type = 'success';
                    } else {
                        this.message = json.message || 'Gagal menghapus lesson.';
                        this.message_type = 'error';
                    }
                } catch (e) {
                    this.message = 'Gagal menghapus lesson.';
                    this.message_type = 'error';
                }
            },
            async reloadCurriculum() {
                if (!this.currentCourseId) {
                    return;
                }

                try {
                    const res = await fetch(`${restUrl}${this.currentCourseId}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });
                    const json = await res.json();

                    if (!json.success || !json.data) {
                        return;
                    }

                    const sections = Array.isArray(json.data.curriculum_sections) ? json.data.curriculum_sections : [];
                    this.curriculumSections = sections.map((section) => ({
                        ...section,
                        section_id: parseInt(section.section_id, 10) || 0,
                        items: Array.isArray(section.items) ? section.items : [],
                    }));
                    this.openSectionIds = this.openSectionIds.filter((sectionId) => (
                        this.curriculumSections.some((section) => section.section_id === sectionId)
                    ));
                    this.currentTab = 'curriculum';
                } catch (e) {
                    this.message = 'Gagal memuat kurikulum.';
                    this.message_type = 'error';
                }
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
                        headers: this.getAuthHeaders(true),
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
                        headers: this.getAuthHeaders(true),
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
                this.course.curriculum_sections = this.getCurriculumOrderPayload();
                this.syncEditorToModel();
                this.saving = true;
                this.message = '';
                try {
                    const endpoint = this.currentCourseId ? `${restUrl}${this.currentCourseId}` : restUrl;
                    const method = this.currentCourseId ? 'PUT' : 'POST';
                    const res = await fetch(endpoint, {
                        method,
                        headers: this.getAuthHeaders(true),
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