<?php

declare(strict_types=1);

namespace Ecoursity\App\Models;

use Ecoursity\App\Services\UploadService;

defined('ABSPATH') || exit;

class File
{
    public const TABLE = 'ecoursity_files';
    public const METHOD_UPLOAD = 'upload';
    public const METHOD_EXTERNAL = 'external';

    public ?int $file_id = null;
    public string $file_name = '';
    public string $file_type = '';
    public int $item_id = 0;
    public string $item_type = '';
    public string $method = self::METHOD_UPLOAD;
    public string $file_path = '';
    public int $orders = 0;
    public string $created_at = '';

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    public static function tableName(): string
    {
        global $wpdb;

        return $wpdb->prefix . self::TABLE;
    }

    public static function createTable(): void
    {
        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charsetCollate = $wpdb->get_charset_collate();
        $table = self::tableName();

        $sql = "CREATE TABLE {$table} (
            file_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            file_name VARCHAR(255) NOT NULL,
            file_type VARCHAR(100) NOT NULL,
            item_id BIGINT(20) UNSIGNED NOT NULL,
            item_type VARCHAR(100) NOT NULL,
            method VARCHAR(20) NOT NULL DEFAULT 'upload',
            file_path TEXT NOT NULL,
            orders INT(11) UNSIGNED NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL,
            PRIMARY KEY  (file_id),
            KEY item_id (item_id),
            KEY item_type (item_type),
            KEY method (method),
            KEY orders (orders)
        ) {$charsetCollate};";

        dbDelta($sql);
    }

    public static function find(int $fileId): ?self
    {
        global $wpdb;

        $row = $wpdb->get_row(
            $wpdb->prepare(
                'SELECT * FROM ' . self::tableName() . ' WHERE file_id = %d',
                $fileId
            ),
            ARRAY_A
        );

        if (! is_array($row)) {
            return null;
        }

        return self::fromArray($row);
    }

    public static function allByItem(int $itemId, string $itemType = ''): array
    {
        global $wpdb;

        if ($itemId < 1) {
            return [];
        }

        if ($itemType !== '') {
            $rows = $wpdb->get_results(
                $wpdb->prepare(
                    'SELECT * FROM ' . self::tableName() . ' WHERE item_id = %d AND item_type = %s ORDER BY orders ASC, file_id ASC',
                    $itemId,
                    sanitize_key($itemType)
                ),
                ARRAY_A
            );
        } else {
            $rows = $wpdb->get_results(
                $wpdb->prepare(
                    'SELECT * FROM ' . self::tableName() . ' WHERE item_id = %d ORDER BY orders ASC, file_id ASC',
                    $itemId
                ),
                ARRAY_A
            );
        }

        return array_map(
            static fn(array $row): self => self::fromArray($row),
            is_array($rows) ? $rows : []
        );
    }

    public static function createFromUpload(
        array $uploadedFile,
        int $itemId,
        string $itemType,
        int $orders = 0,
        ?UploadService $uploadService = null
    ): self {
        $uploadService ??= new UploadService();
        $uploaded = $uploadService->uploadToSubfolder($uploadedFile, UploadService::FILES_DIRECTORY);

        $file = new self([
            'file_name' => (string) ($uploaded['name'] ?? ''),
            'file_type' => (string) ($uploaded['mime_type'] ?? ''),
            'item_id' => $itemId,
            'item_type' => $itemType,
            'method' => self::METHOD_UPLOAD,
            'file_path' => (string) ($uploaded['path'] ?? ''),
            'orders' => $orders,
        ]);

        $file->save();

        return $file;
    }

    public static function syncOrders(array $fileIds, int $itemId, string $itemType = ''): void
    {
        global $wpdb;

        if ($itemId < 1) {
            return;
        }

        foreach (array_values($fileIds) as $order => $fileId) {
            $where = [
                'file_id' => absint($fileId),
                'item_id' => $itemId,
            ];
            $whereFormat = ['%d', '%d'];

            if ($itemType !== '') {
                $where['item_type'] = sanitize_key($itemType);
                $whereFormat[] = '%s';
            }

            $wpdb->update(
                self::tableName(),
                ['orders' => absint($order)],
                $where,
                ['%d'],
                $whereFormat
            );
        }
    }

    public function save(): int
    {
        global $wpdb;

        $data = [
            'file_name' => sanitize_text_field($this->file_name),
            'file_type' => sanitize_mime_type($this->file_type),
            'item_id' => absint($this->item_id),
            'item_type' => sanitize_key($this->item_type),
            'method' => $this->sanitizeMethod($this->method),
            'file_path' => $this->sanitizeFilePath($this->file_path, $this->method),
            'orders' => absint($this->orders),
        ];

        $format = ['%s', '%s', '%d', '%s', '%s', '%s', '%d'];

        if ($this->file_id) {
            $wpdb->update(
                self::tableName(),
                $data,
                ['file_id' => $this->file_id],
                $format,
                ['%d']
            );

            return $this->file_id;
        }

        $data['created_at'] = $this->created_at !== '' ? $this->created_at : current_time('mysql');
        $format[] = '%s';

        $wpdb->insert(self::tableName(), $data, $format);
        $this->file_id = (int) $wpdb->insert_id;

        return $this->file_id;
    }

    public function delete(): bool
    {
        global $wpdb;

        if (! $this->file_id) {
            return false;
        }

        $deleted = $wpdb->delete(self::tableName(), ['file_id' => $this->file_id], ['%d']);

        return $deleted !== false;
    }

    public function url(): string
    {
        if ($this->method === self::METHOD_EXTERNAL) {
            return esc_url_raw($this->file_path);
        }

        $uploads = wp_upload_dir();

        if (! empty($uploads['error'])) {
            return '';
        }

        return trailingslashit((string) $uploads['baseurl'])
            . 'ecoursity-storage/'
            . ltrim(str_replace('\\', '/', $this->file_path), '/');
    }

    public function toArray(): array
    {
        return [
            'file_id' => $this->file_id,
            'file_name' => $this->file_name,
            'file_type' => $this->file_type,
            'item_id' => $this->item_id,
            'item_type' => $this->item_type,
            'method' => $this->method,
            'file_path' => $this->file_path,
            'orders' => $this->orders,
            'created_at' => $this->created_at,
            'url' => $this->url(),
        ];
    }

    private static function fromArray(array $row): self
    {
        return new self([
            'file_id' => (int) ($row['file_id'] ?? 0),
            'file_name' => (string) ($row['file_name'] ?? ''),
            'file_type' => (string) ($row['file_type'] ?? ''),
            'item_id' => (int) ($row['item_id'] ?? 0),
            'item_type' => (string) ($row['item_type'] ?? ''),
            'method' => (string) ($row['method'] ?? self::METHOD_UPLOAD),
            'file_path' => (string) ($row['file_path'] ?? ''),
            'orders' => (int) ($row['orders'] ?? 0),
            'created_at' => (string) ($row['created_at'] ?? ''),
        ]);
    }

    private function sanitizeMethod(string $method): string
    {
        return in_array($method, [self::METHOD_UPLOAD, self::METHOD_EXTERNAL], true)
            ? $method
            : self::METHOD_UPLOAD;
    }

    private function sanitizeFilePath(string $filePath, string $method): string
    {
        if ($this->sanitizeMethod($method) === self::METHOD_EXTERNAL) {
            return esc_url_raw($filePath);
        }

        return sanitize_text_field(wp_normalize_path($filePath));
    }
}
