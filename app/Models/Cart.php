<?php

declare(strict_types=1);

namespace Ecoursity\App\Models;

defined('ABSPATH') || exit;

class Cart
{
    public const META_KEY = '_ecoursity_cart';
    public const SESSION_KEY = 'ecoursity_cart';

    /**
     * Ambil semua course ID di cart.
     */
    public static function all(): array
    {
        if (is_user_logged_in()) {
            return self::normalizeCourseIds(
                get_user_meta(get_current_user_id(), self::META_KEY, true)
            );
        }

        self::startSession();

        return self::normalizeCourseIds($_SESSION[self::SESSION_KEY] ?? []);
    }

    /**
     * Alias agar enak dipakai dari template/controller.
     */
    public static function items(): array
    {
        return self::all();
    }

    public static function count(): int
    {
        return count(self::all());
    }

    public static function has(int $courseId): bool
    {
        return in_array(absint($courseId), self::all(), true);
    }

    public static function add(int $courseId): bool
    {
        $courseId = absint($courseId);

        if (!self::isCourse($courseId)) {
            return false;
        }

        $items = self::all();

        if (!in_array($courseId, $items, true)) {
            $items[] = $courseId;
        }

        self::save($items);

        return true;
    }

    public static function remove(int $courseId): bool
    {
        $courseId = absint($courseId);
        $items = array_values(array_filter(
            self::all(),
            static fn(int $item): bool => $item !== $courseId
        ));

        self::save($items);

        return true;
    }

    public static function clear(): void
    {
        self::save([]);
    }

    public static function replace(array $courseIds): void
    {
        self::save(self::normalizeCourseIds($courseIds));
    }

    /**
     * Pindahkan cart session guest ke user meta setelah user login.
     */
    public static function syncSessionToUser(?int $userId = null): void
    {
        $userId = $userId ? absint($userId) : get_current_user_id();

        if ($userId < 1) {
            return;
        }

        self::startSession();

        $sessionItems = self::normalizeCourseIds($_SESSION[self::SESSION_KEY] ?? []);

        if ($sessionItems === []) {
            return;
        }

        $userItems = self::normalizeCourseIds(
            get_user_meta($userId, self::META_KEY, true)
        );

        update_user_meta(
            $userId,
            self::META_KEY,
            array_values(array_unique(array_merge($userItems, $sessionItems)))
        );

        unset($_SESSION[self::SESSION_KEY]);
    }

    private static function save(array $courseIds): void
    {
        $courseIds = self::normalizeCourseIds($courseIds);

        if (is_user_logged_in()) {
            update_user_meta(get_current_user_id(), self::META_KEY, $courseIds);

            return;
        }

        self::startSession();
        $_SESSION[self::SESSION_KEY] = $courseIds;
    }

    private static function normalizeCourseIds(mixed $courseIds): array
    {
        if (!is_array($courseIds)) {
            return [];
        }

        $ids = array_map('absint', $courseIds);
        $ids = array_filter($ids, static fn(int $courseId): bool => self::isCourse($courseId));

        return array_values(array_unique($ids));
    }

    private static function isCourse(int $courseId): bool
    {
        return $courseId > 0 && get_post_type($courseId) === Course::POST_TYPE;
    }

    private static function startSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        if (headers_sent()) {
            return;
        }

        session_start();
    }
}
