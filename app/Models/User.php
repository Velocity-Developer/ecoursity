<?php

declare(strict_types=1);

namespace Ecoursity\App\Models;

use WP_User;

defined('ABSPATH') || exit;

class User
{
    public ?int $id = null;
    public string $email = '';
    public string $displayName = '';
    public string $display_name = '';
    public string $firstName = '';
    public string $first_name = '';
    public string $lastName = '';
    public string $last_name = '';
    public string $userLogin = '';
    public string $user_login = '';
    public string $userRegistered = '';
    public string $user_registered = '';
    public array $roles = [];

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    public static function find(int $id): ?static
    {
        $user = get_user_by('id', $id);

        if (!$user instanceof WP_User || !static::hasModelRole($user)) {
            return null;
        }

        return static::fromUser($user);
    }

    public static function current(): ?static
    {
        $user = wp_get_current_user();

        if (!$user instanceof WP_User || !$user->exists() || !static::hasModelRole($user)) {
            return null;
        }

        return static::fromUser($user);
    }

    public static function all(array $args = []): array
    {
        $users = get_users(array_merge([
            'role' => static::role(),
            'orderby' => 'display_name',
            'order' => 'ASC',
            'count' => 25,
        ], $args));

        return array_map(
            static fn(WP_User $user): static => static::fromUser($user),
            $users
        );
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles, true);
    }

    protected static function fromUser(WP_User $user): static
    {
        $displayName = (string) $user->display_name;
        $firstName = (string) $user->first_name;
        $lastName = (string) $user->last_name;
        $userLogin = (string) $user->user_login;
        $userRegistered = (string) $user->user_registered;

        return new static([
            'id' => (int) $user->ID,
            'email' => (string) $user->user_email,
            'displayName' => $displayName,
            'display_name' => $displayName,
            'firstName' => $firstName,
            'first_name' => $firstName,
            'lastName' => $lastName,
            'last_name' => $lastName,
            'userLogin' => $userLogin,
            'user_login' => $userLogin,
            'userRegistered' => $userRegistered,
            'user_registered' => $userRegistered,
            'roles' => $user->roles,
        ]);
    }

    protected static function hasModelRole(WP_User $user): bool
    {
        return in_array(static::role(), $user->roles, true);
    }

    protected static function role(): string
    {
        $constant = static::class . '::ROLE';

        return defined($constant) ? (string) constant($constant) : '';
    }
}
