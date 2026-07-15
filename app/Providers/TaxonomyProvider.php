<?php

namespace Ecoursity\App\Providers;

class TaxonomyProvider
{
    public function boot()
    {
        add_action('init', [$this, 'register']);
    }

    public function register(): void
    {
        register_taxonomy('ecoursity_course_category', 'ecoursity_course', [
            'labels' => $this->makeLabels(__('Kategori Kursus')),
            'public' => true,
            'hierarchical' => true,
            'rewrite' => ['slug' => 'kategori-kursus'],
        ]);
        register_taxonomy('ecoursity_course_tag', 'ecoursity_course', [
            'labels' => $this->makeLabels(__('Tag Kursus')),
            'public' => true,
            'hierarchical' => false,
            'rewrite' => ['slug' => 'tag-kursus'],
        ]);

        register_taxonomy_for_object_type('ecoursity_course_category', 'ecoursity_course');
        register_taxonomy_for_object_type('ecoursity_course_tag', 'ecoursity_course');
    }
    private function makeLabels(string $name): array
    {
        return [
            'name' => $name,
            'singular_name' => $name,
            'menu_name' => $name,
            'search_items' => __('Cari ' . $name),
            'all_items' => __('Semua ' . $name),
            'add_new_item' => __('Tambah ' . $name . ' baru'),
            'new_item' => __('New ' . $name),
            'view_item' => __('Lihat ' . $name),
        ];
    }
}
