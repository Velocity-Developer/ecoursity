<?php

namespace Ecoursity\App\Providers;

use Ecoursity\App\Models\Course;

class PostTypeProvider
{
    public function boot()
    {
        add_action('init', [$this, 'register']);
    }

    public function register(): void
    {
        register_post_type(Course::POST_TYPE, [
            'labels' => $this->makeLabels(__('Kursus')),
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'kursus'],
            'show_in_menu' => false,
            'show_in_rest' => false,
            'supports' => ['title', 'editor', 'thumbnail', 'comments'],
        ]);
    }

    private function makeLabels(string $name): array
    {
        return [
            'name' => $name,
            'singular_name' => $name,
            'menu_name' => $name,
            'add_new' => __('Tambah ' . $name),
            'add_new_item' => __('Tambah ' . $name . ' baru'),
            'edit_item' => __('Edit ' . $name),
            'new_item' => __('New ' . $name),
            'view_item' => __('Lihat ' . $name),
            'search_items' => __('Cari ' . $name),
            'not_found' => __('Tidak ada ' . $name),
            'not_found_in_trash' => __('Tidak ada ' . $name . ' dalam sampah'),
            'parent_item_colon' => __('Induk ' . $name . ':'),
        ];
    }
}
