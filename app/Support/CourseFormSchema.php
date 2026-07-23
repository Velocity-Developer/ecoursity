<?php

namespace Ecoursity\App\Support;

class CourseFormSchema
{
    public static function sections(): array
    {
        return apply_filters('ecoursity_course_form_sections', self::defaultSections());
    }

    public static function defaults(array $sections): array
    {
        $defaults = [];

        foreach ($sections as $section) {
            if (($section['type'] ?? '') === 'row') {
                $defaults = array_merge($defaults, self::defaults($section['fields'] ?? []));
                continue;
            }

            if (($section['type'] ?? '') !== 'field' || empty($section['name'])) {
                continue;
            }

            $defaults[$section['name']] = $section['default'] ?? '';
        }

        return $defaults;
    }

    public static function sortableTextListFields(array $sections): array
    {
        return array_keys(array_filter(
            self::fieldInputs($sections),
            static fn(string $input): bool => $input === 'sortable_text_list'
        ));
    }

    public static function metaFieldInputs(array $sections): array
    {
        $inputs = self::fieldInputs($sections);

        if (self::hasSpecial($sections, 'duration')) {
            $inputs['duration'] = 'duration';
        }

        $reservedFields = [
            'title',
            'content',
            'excerpt',
            'slug',
            'status',
            'thumbnail',
            'thumbnail_id',
            'course_category_ids',
            'course_tags',
            'course_tags_input',
            'curriculum_sections',
        ];

        return array_diff_key(
            $inputs,
            array_flip($reservedFields)
        );
    }

    public static function metaKeys(array $sections): array
    {
        return array_map(
            static fn(string $field): string => "_ecoursity_{$field}",
            array_keys(self::metaFieldInputs($sections))
        );
    }

    public static function fieldInputs(array $sections): array
    {
        $inputs = [];

        foreach ($sections as $section) {
            if (($section['type'] ?? '') === 'row') {
                $inputs = array_merge($inputs, self::fieldInputs($section['fields'] ?? []));
                continue;
            }

            if (($section['type'] ?? '') !== 'field' || empty($section['name'])) {
                continue;
            }

            $inputs[$section['name']] = $section['input'] ?? 'text';
        }

        return $inputs;
    }

    private static function hasSpecial(array $sections, string $name): bool
    {
        foreach ($sections as $section) {
            if (($section['type'] ?? '') === 'row' && self::hasSpecial($section['fields'] ?? [], $name)) {
                return true;
            }

            if (($section['type'] ?? '') === 'special' && ($section['name'] ?? '') === $name) {
                return true;
            }
        }

        return false;
    }

    public static function defaultSections(): array
    {
        return [
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
            [
                'type' => 'field',
                'name' => 'requirements',
                'label' => 'Persyaratan',
                'input' => 'sortable_text_list',
                'placeholder' => 'Contoh: Memahami dasar WordPress',
                'button_label' => 'Tambah Persyaratan',
                'default' => [''],
            ],
            [
                'type' => 'field',
                'name' => 'target_audiences',
                'label' => 'Untuk siapa kursus ini',
                'input' => 'sortable_text_list',
                'placeholder' => 'Contoh: Pemula yang ingin membuat website',
                'button_label' => 'Tambah Target Peserta',
                'default' => [''],
            ],
            [
                'type' => 'field',
                'name' => 'key_features',
                'label' => 'Fitur',
                'input' => 'sortable_text_list',
                'placeholder' => 'Contoh: Modul Materi PDF',
                'button_label' => 'Tambah Fitur',
                'default' => [''],
            ],
        ];
    }
}
