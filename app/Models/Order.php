<?php

namespace Ecoursity\App\Models;

use Ecoursity\App\Helpers\Str;
use WP_Query;

defined('ABSPATH') || exit;

class Order
{
    public const POST_TYPE = 'ecoursity_order';
    public const METHOD_MANUALLY = 'manually';
    public const METHOD_CHECKOUT = 'checkout';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_REFUNDED = 'refunded';
    public const STATUS_FAILED = 'failed';

    public const META_ORDER_NUMBER = '_ecoursity_order_number';
    public const META_ORDER_DATE = '_ecoursity_order_date';
    public const META_ORDER_METHOD = '_ecoursity_order_method';
    public const META_ORDER_STATUS = '_ecoursity_order_status';
    public const META_ORDER_USER = '_ecoursity_order_user';
    public const META_ORDER_PAYMENT = '_ecoursity_order_payment';
    public const META_ORDER_ITEMS = '_ecoursity_order_items';
    public const META_ORDER_SUBTOTAL = '_ecoursity_order_subtotal';
    public const META_ORDER_TOTAL = '_ecoursity_order_total';

    public ?int $id = null;
    public string $title = '';
    public string $slug = '';
    public string $status = 'publish';
    public string $content = '';
    public string $excerpt = '';
    public int $author = 0;
    public string $order_number = '';
    public string $order_date = '';
    public string $order_method = self::METHOD_MANUALLY;
    public string $order_status = self::STATUS_PENDING;
    public int $order_user = 0;
    public string $order_payment = '';
    public array $order_items = [];
    public float $order_subtotal = 0;
    public float $order_total = 0;

    public array $meta_keys = [
        self::META_ORDER_NUMBER,
        self::META_ORDER_DATE,
        self::META_ORDER_METHOD,
        self::META_ORDER_STATUS,
        self::META_ORDER_USER,
        self::META_ORDER_PAYMENT,
        self::META_ORDER_ITEMS,
        self::META_ORDER_SUBTOTAL,
        self::META_ORDER_TOTAL,
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
        $isNew = !$this->id;
        $this->prepareOrderMeta($isNew);

        $data = [
            'ID' => $this->id,
            'post_type' => self::POST_TYPE,
            'post_title' => $this->defaultTitle(),
            'post_name' => $this->slug,
            'post_content' => $this->content,
            'post_excerpt' => $this->excerpt,
            'post_status' => $this->status,
            'post_author' => $this->author ?: $this->order_user,
        ];

        $result = $this->id ? wp_update_post($data) : wp_insert_post($data);

        if (is_wp_error($result)) {
            return 0;
        }

        $this->id = (int) $result;

        if ($this->id) {
            $this->saveMeta($isNew);
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
            'order_number' => (string) $meta(self::META_ORDER_NUMBER),
            'order_date' => (string) $meta(self::META_ORDER_DATE),
            'order_method' => (string) $meta(self::META_ORDER_METHOD),
            'order_status' => (string) $meta(self::META_ORDER_STATUS),
            'order_user' => (int) $meta(self::META_ORDER_USER),
            'order_payment' => (string) $meta(self::META_ORDER_PAYMENT),
            'order_items' => self::normalizeItems($meta(self::META_ORDER_ITEMS)),
            'order_subtotal' => (float) $meta(self::META_ORDER_SUBTOTAL),
            'order_total' => (float) $meta(self::META_ORDER_TOTAL),
        ]);
    }

    private function prepareOrderMeta(bool $isNew): void
    {
        if ($isNew && $this->order_number === '') {
            $this->order_number = $this->generateOrderNumber();
        }

        if ($isNew && $this->order_date === '') {
            $this->order_date = date_i18n('YmdHis');
        }

        $this->order_method = $this->validOrderMethod($this->order_method);
        $this->order_status = $this->validOrderStatus($this->order_status);
        $this->order_items = self::normalizeItems($this->order_items);
        $this->order_subtotal = $this->order_subtotal > 0
            ? $this->order_subtotal
            : $this->calculateSubtotal();

        if ($this->order_total < 0) {
            $this->order_total = 0;
        }
    }

    private function saveMeta(bool $isNew): void
    {
        $this->updateMeta(self::META_ORDER_NUMBER, $this->order_number);
        $this->updateMeta(self::META_ORDER_DATE, $this->order_date);

        if ($isNew) {
            $this->updateMeta(self::META_ORDER_METHOD, $this->order_method);
        }

        $this->updateMeta(self::META_ORDER_STATUS, $this->order_status);
        $this->updateMeta(self::META_ORDER_USER, $this->order_user);
        $this->updateMeta(self::META_ORDER_PAYMENT, $this->order_payment);
        $this->updateMeta(self::META_ORDER_ITEMS, $this->order_items);
        $this->updateMeta(self::META_ORDER_SUBTOTAL, $this->order_subtotal);
        $this->updateMeta(self::META_ORDER_TOTAL, $this->order_total);
    }

    private function calculateSubtotal(): float
    {
        return array_reduce(
            $this->order_items,
            static fn(float $subtotal, array $item): float => $subtotal + (float) $item['price_real'],
            0.0
        );
    }

    private function defaultTitle(): string
    {
        return $this->order_number ?: 'Order';
    }

    private function generateOrderNumber(): string
    {
        return sprintf(
            'ORDER%s%s',
            date_i18n('ymdH'),
            strtoupper(Str::random(6))
        );
    }

    private function validOrderMethod(string $method): string
    {
        return in_array($method, [self::METHOD_MANUALLY, self::METHOD_CHECKOUT], true)
            ? $method
            : self::METHOD_MANUALLY;
    }

    private function validOrderStatus(string $status): string
    {
        return in_array($status, [
            self::STATUS_COMPLETED,
            self::STATUS_PENDING,
            self::STATUS_PROCESSING,
            self::STATUS_CANCELLED,
            self::STATUS_REFUNDED,
            self::STATUS_FAILED,
        ], true) ? $status : self::STATUS_PENDING;
    }

    private static function normalizeItems(mixed $items): array
    {
        if (!is_array($items)) {
            return [];
        }

        return array_values(array_filter(array_map(
            static function (mixed $item): array {
                if (!is_array($item)) {
                    return [];
                }

                $id = absint($item['id'] ?? 0);

                if ($id < 1) {
                    return [];
                }

                return [
                    'id' => $id,
                    'name' => sanitize_text_field((string) ($item['name'] ?? '')),
                    'price' => (float) ($item['price'] ?? 0),
                    'price_sale' => (float) ($item['price_sale'] ?? 0),
                    'price_real' => (float) ($item['price_real'] ?? 0),
                ];
            },
            $items
        )));
    }
}
