<?php

namespace Ecoursity\App\Models;

use WP_Query;

defined('ABSPATH') || exit;

class Order
{
    public const POST_TYPE = 'ecoursity_order';

    public ?int $id = null;
    public string $title = '';
    public string $slug = '';
    public string $status = 'publish';
    public string $content = '';
    public string $excerpt = '';
    public int $author = 0;
    public int $user_id = 0;
    public int $course_id = 0;

    public array $meta_keys = [
        '_ecoursity_user_id',
        '_ecoursity_course_id',
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

    public static function all(array $args = []): array
    {
        $query = new WP_Query(array_merge([
            'post_type' => self::POST_TYPE,
            'post_status' => ['publish', 'draft', 'pending', 'private'],
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
            'post_title' => $this->title ?: $this->defaultTitle(),
            'post_name' => $this->slug,
            'post_content' => $this->content,
            'post_excerpt' => $this->excerpt,
            'post_status' => $this->status,
            'post_author' => $this->author ?: $this->user_id,
        ];

        $result = $this->id ? wp_update_post($data) : wp_insert_post($data);

        if (is_wp_error($result)) {
            return 0;
        }

        $this->id = (int) $result;

        if ($this->id) {
            $this->updateMeta('_ecoursity_user_id', $this->user_id);
            $this->updateMeta('_ecoursity_course_id', $this->course_id);
        }

        return (int) $this->id;
    }

    public function delete(bool $force = false): bool
    {
        if (!$this->id) {
            return false;
        }

        return (bool) wp_delete_post($this->id, $force);
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
            'user_id' => (int) $meta('_ecoursity_user_id'),
            'course_id' => (int) $meta('_ecoursity_course_id'),
        ]);
    }

    private function defaultTitle(): string
    {
        return sprintf(
            'Order User #%d Course #%d',
            absint($this->user_id),
            absint($this->course_id)
        );
    }
}
