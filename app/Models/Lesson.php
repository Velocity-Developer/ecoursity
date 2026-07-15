<?php

namespace Ecoursity\App\Models;

use WP_Query;

defined('ABSPATH') || exit;

class Lesson
{
    public const POST_TYPE = 'ecoursity_lesson';

    public ?int $id = null;
    public string $title = '';
    public string $slug = '';
    public string $status = 'draft';
    public string $content = '';
    public string $excerpt = '';
    public string $thumbnail = '';
    public int $author = 0;
    public mixed $duration = '';
    public bool $preview = false;
    public int $assigned = 0;

    public array $meta_keys = [
        '_ecoursity_duration',
        '_ecoursity_preview',
        '_ecoursity_assigned',
    ];

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    public static function find(int $id): ?self
    {
        $post = get_post($id);

        if (!$post || $post->post_type !== self::POST_TYPE) {
            return null;
        }

        return self::fromPost($post);
    }

    public static function findBySlug(string $slug): ?self
    {
        $post = get_page_by_path($slug, OBJECT, self::POST_TYPE);

        if (!$post) {
            return null;
        }

        return self::fromPost($post);
    }

    public static function all(array $args = []): array
    {
        $query = new WP_Query(array_merge([
            'post_type' => self::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => 25,
        ], $args));

        return array_map(
            fn($post) => self::fromPost($post),
            $query->posts
        );
    }

    public function save(): int
    {
        $data = [
            'ID' => $this->id,
            'post_type' => self::POST_TYPE,
            'post_title' => $this->title,
            'post_name' => $this->slug,
            'post_content' => $this->content,
            'post_excerpt' => $this->excerpt,
            'post_status' => $this->status,
            'post_author' => $this->author,
        ];

        if ($this->id) {
            $this->id = wp_update_post($data);
        } else {
            $this->id = wp_insert_post($data);
        }

        return $this->id;
    }

    public function delete(bool $force = false): bool
    {
        if (!$this->id) {
            return false;
        }

        return (bool) wp_delete_post($this->id, $force);
    }

    public function thumbnail(): string
    {
        return get_the_post_thumbnail_url($this->id, 'large') ?: '';
    }

    public function meta(string $key, $default = null)
    {
        $value = get_post_meta($this->id, $key, true);

        return $value === '' ? $default : $value;
    }

    public function updateMeta(string $key, $value): void
    {
        update_post_meta($this->id, $key, $value);
    }

    protected static function fromPost(\WP_Post $post): self
    {
        $meta = fn($key) => get_post_meta($post->ID, $key, true);

        return new self([
            'id' => $post->ID,
            'title' => $post->post_title,
            'slug' => $post->post_name,
            'content' => $post->post_content,
            'excerpt' => $post->post_excerpt,
            'status' => $post->post_status,
            'author' => (int) $post->post_author,
            'duration' => $meta('_ecoursity_duration') ?: '',
            'preview' => (bool) $meta('_ecoursity_preview'),
            'assigned' => (int) $meta('_ecoursity_assigned'),
        ]);
    }
}
