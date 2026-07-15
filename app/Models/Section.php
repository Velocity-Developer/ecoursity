<?php

declare(strict_types=1);

namespace Ecoursity\App\Models;

use wpdb;

defined('ABSPATH') || exit;

class Section
{
    public const TABLE = 'ecoursity_sections';
    public const ITEMS_TABLE = 'ecoursity_section_items';

    public ?int $section_id = null;
    public string $section_name = '';
    public int $section_course_id = 0;
    public int $section_order = 0;
    public string $section_description = '';
    public array $items = [];

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

    public static function itemsTableName(): string
    {
        global $wpdb;

        return $wpdb->prefix . self::ITEMS_TABLE;
    }

    public static function createTables(): void
    {
        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charsetCollate = $wpdb->get_charset_collate();
        $sectionsTable = self::tableName();
        $itemsTable = self::itemsTableName();

        $sectionsSql = "CREATE TABLE {$sectionsTable} (
            section_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            section_name VARCHAR(255) NOT NULL,
            section_course_id BIGINT(20) UNSIGNED NOT NULL,
            section_order INT(11) UNSIGNED NOT NULL DEFAULT 0,
            section_description LONGTEXT NULL,
            PRIMARY KEY  (section_id),
            KEY section_course_id (section_course_id),
            KEY section_order (section_order)
        ) {$charsetCollate};";

        $itemsSql = "CREATE TABLE {$itemsTable} (
            section_item_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            section_id BIGINT(20) UNSIGNED NOT NULL,
            item_id BIGINT(20) UNSIGNED NOT NULL,
            item_order INT(11) UNSIGNED NOT NULL DEFAULT 0,
            item_type VARCHAR(100) NOT NULL,
            PRIMARY KEY  (section_item_id),
            KEY section_id (section_id),
            KEY item_id (item_id),
            KEY item_type (item_type)
        ) {$charsetCollate};";

        dbDelta($sectionsSql);
        dbDelta($itemsSql);
    }

    public static function find(int $sectionId): ?self
    {
        global $wpdb;

        $row = $wpdb->get_row(
            $wpdb->prepare(
                'SELECT * FROM ' . self::tableName() . ' WHERE section_id = %d',
                $sectionId
            ),
            ARRAY_A
        );

        if (!is_array($row)) {
            return null;
        }

        $section = self::fromArray($row);
        $section->items = self::getItems($section->section_id ?? 0);

        return $section;
    }

    public static function allByCourse(int $courseId): array
    {
        global $wpdb;

        $rows = $wpdb->get_results(
            $wpdb->prepare(
                'SELECT * FROM ' . self::tableName() . ' WHERE section_course_id = %d ORDER BY section_order ASC, section_id ASC',
                $courseId
            ),
            ARRAY_A
        );

        return array_map(function (array $row): self {
            $section = self::fromArray($row);
            $section->items = self::getItems($section->section_id ?? 0);

            return $section;
        }, is_array($rows) ? $rows : []);
    }

    public function save(): int
    {
        global $wpdb;

        $data = [
            'section_name' => sanitize_text_field($this->section_name),
            'section_course_id' => absint($this->section_course_id),
            'section_order' => absint($this->section_order),
            'section_description' => wp_kses_post($this->section_description),
        ];

        $format = ['%s', '%d', '%d', '%s'];

        if ($this->section_id) {
            $wpdb->update(
                self::tableName(),
                $data,
                ['section_id' => $this->section_id],
                $format,
                ['%d']
            );

            return $this->section_id;
        }

        $wpdb->insert(self::tableName(), $data, $format);
        $this->section_id = (int) $wpdb->insert_id;

        return $this->section_id;
    }

    public function saveItems(array $items): void
    {
        global $wpdb;

        if (!$this->section_id) {
            return;
        }

        $wpdb->delete(self::itemsTableName(), ['section_id' => $this->section_id], ['%d']);

        foreach ($items as $index => $item) {
            $itemId = absint($item['item_id'] ?? 0);
            $itemType = sanitize_key((string) ($item['item_type'] ?? ''));
            $itemOrder = isset($item['item_order']) ? absint($item['item_order']) : $index;

            if ($itemId < 1 || $itemType === '') {
                continue;
            }

            $wpdb->insert(
                self::itemsTableName(),
                [
                    'section_id' => $this->section_id,
                    'item_id' => $itemId,
                    'item_order' => $itemOrder,
                    'item_type' => $itemType,
                ],
                ['%d', '%d', '%d', '%s']
            );
        }
    }

    public function delete(): bool
    {
        global $wpdb;

        if (!$this->section_id) {
            return false;
        }

        $wpdb->delete(self::itemsTableName(), ['section_id' => $this->section_id], ['%d']);
        $deleted = $wpdb->delete(self::tableName(), ['section_id' => $this->section_id], ['%d']);

        return $deleted !== false;
    }

    public static function getItems(int $sectionId): array
    {
        global $wpdb;

        if ($sectionId < 1) {
            return [];
        }

        $rows = $wpdb->get_results(
            $wpdb->prepare(
                'SELECT * FROM ' . self::itemsTableName() . ' WHERE section_id = %d ORDER BY item_order ASC, section_item_id ASC',
                $sectionId
            ),
            ARRAY_A
        );

        return array_map(static function (array $row): array {
            return [
                'section_item_id' => (int) ($row['section_item_id'] ?? 0),
                'section_id' => (int) ($row['section_id'] ?? 0),
                'item_id' => (int) ($row['item_id'] ?? 0),
                'item_order' => (int) ($row['item_order'] ?? 0),
                'item_type' => (string) ($row['item_type'] ?? ''),
            ];
        }, is_array($rows) ? $rows : []);
    }

    private static function fromArray(array $row): self
    {
        return new self([
            'section_id' => (int) ($row['section_id'] ?? 0),
            'section_name' => (string) ($row['section_name'] ?? ''),
            'section_course_id' => (int) ($row['section_course_id'] ?? 0),
            'section_order' => (int) ($row['section_order'] ?? 0),
            'section_description' => (string) ($row['section_description'] ?? ''),
        ]);
    }
}
