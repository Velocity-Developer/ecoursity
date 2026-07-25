<?php

declare(strict_types=1);

namespace Ecoursity\App\Models;

defined('ABSPATH') || exit;

class Setting
{
    public const PREFIX = '_ecoursity_';

    public string $key = '';
    public mixed $value = null;

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }

        if ($this->key !== '') {
            $this->key = self::key($this->key);
        }
    }

    public static function make(string $key, mixed $value = null): self
    {
        return new self([
            'key' => $key,
            'value' => $value,
        ]);
    }

    public static function find(string $key, mixed $default = null): ?self
    {
        $optionKey = self::key($key);
        $value = get_option($optionKey, null);

        if ($value === null) {
            return $default === null ? null : self::make($optionKey, $default);
        }

        return self::make($optionKey, $value);
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return get_option(self::key($key), $default);
    }

    public static function getMany(array $keys, mixed $default = null): array
    {
        $settings = [];

        foreach ($keys as $key) {
            if (!is_string($key)) {
                continue;
            }

            $settings[$key] = self::get($key, $default);
        }

        return $settings;
    }

    public static function set(string $key, mixed $value, bool $autoload = false): bool
    {
        return update_option(self::key($key), $value, $autoload);
    }

    public static function setMany(array $settings, bool $autoload = false): array
    {
        $results = [];

        foreach ($settings as $key => $value) {
            if (!is_string($key)) {
                continue;
            }

            $results[$key] = self::set($key, $value, $autoload);
        }

        return $results;
    }

    public static function delete(string $key): bool
    {
        return delete_option(self::key($key));
    }

    public function save(bool $autoload = false): bool
    {
        if ($this->key === '') {
            return false;
        }

        return self::set($this->key, $this->value, $autoload);
    }

    public function remove(): bool
    {
        if ($this->key === '') {
            return false;
        }

        return self::delete($this->key);
    }

    public static function key(string $key): string
    {
        $key = sanitize_key($key);

        if (str_starts_with($key, self::PREFIX)) {
            return $key;
        }

        return self::PREFIX . ltrim($key, '_');
    }
}
