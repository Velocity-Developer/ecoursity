<?php

declare(strict_types=1);

namespace Ecoursity\App\Services\Admin;

use Ecoursity\App\Models\Setting;
use Ecoursity\App\Support\SettingSchema;

defined('ABSPATH') || exit;

class SettingService
{
    public function valuesForTab(string $tab): array
    {
        $schema = SettingSchema::tab($tab);
        $values = [];

        foreach ($schema['fields'] as $field) {
            $values[$field['key']] = Setting::get($field['key'], $field['default'] ?? null);
        }

        return $values;
    }

    public function saveTab(string $tab, array $input): array
    {
        $schema = SettingSchema::tab($tab);
        $settings = [];

        foreach ($schema['fields'] as $field) {
            $key = (string) $field['key'];
            $settings[$key] = $this->sanitizeField($field, $input[$key] ?? null);
        }

        return Setting::setMany($settings);
    }

    private function sanitizeField(array $field, mixed $value): mixed
    {
        $type = (string) ($field['type'] ?? 'text');

        return match ($type) {
            'checkbox' => !empty($value),
            'editor' => wp_kses_post((string) $value),
            'email' => sanitize_email((string) $value),
            'image' => esc_url_raw((string) $value),
            'number' => $this->sanitizeNumber($field, $value),
            'repeater' => $this->sanitizeRepeater($field, $value),
            'select' => $this->sanitizeSelect($field, $value),
            'textarea' => sanitize_textarea_field((string) $value),
            default => sanitize_text_field((string) $value),
        };
    }

    private function sanitizeNumber(array $field, mixed $value): int
    {
        $number = absint($value);

        if (isset($field['min'])) {
            $number = max((int) $field['min'], $number);
        }

        if (isset($field['max'])) {
            $number = min((int) $field['max'], $number);
        }

        return $number;
    }

    private function sanitizeSelect(array $field, mixed $value): string
    {
        $value = sanitize_text_field((string) $value);
        $options = $field['options'] ?? [];

        if (is_array($options) && array_key_exists($value, $options)) {
            return $value;
        }

        return (string) ($field['default'] ?? '');
    }

    private function sanitizeRepeater(array $field, mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $subFields = $field['fields'] ?? [];

        if (!is_array($subFields)) {
            return [];
        }

        $rows = [];

        foreach ($value as $row) {
            if (!is_array($row)) {
                continue;
            }

            $sanitizedRow = [];

            foreach ($subFields as $subField) {
                if (!is_array($subField) || empty($subField['key'])) {
                    continue;
                }

                $key = (string) $subField['key'];
                $sanitizedRow[$key] = sanitize_text_field((string) ($row[$key] ?? ''));
            }

            if (array_filter($sanitizedRow, static fn (string $item): bool => $item !== '')) {
                $rows[] = $sanitizedRow;
            }
        }

        return array_values($rows);
    }
}
