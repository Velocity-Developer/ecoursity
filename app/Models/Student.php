<?php

namespace Ecoursity\App\Models;

use WP_User;

defined('ABSPATH') || exit;

class Student
{
    public const ROLE = 'ecoursity_student';

    public ?int $id = null;
    public string $email = '';
    public string $displayName = '';
    public string $firstName = '';
    public string $lastName = '';
    public array $roles = [];

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
        $user = get_user_by('id', $id);

        if (!$user instanceof WP_User || !in_array(self::ROLE, $user->roles, true)) {
            return null;
        }

        return self::fromUser($user);
    }

    public static function current(): ?self
    {
        $user = wp_get_current_user();

        if (!$user instanceof WP_User || !$user->exists() || !in_array(self::ROLE, $user->roles, true)) {
            return null;
        }

        return self::fromUser($user);
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles, true);
    }

    protected static function fromUser(WP_User $user): self
    {
        return new self([
            'id' => $user->ID,
            'email' => (string) $user->user_email,
            'displayName' => (string) $user->display_name,
            'firstName' => (string) $user->first_name,
            'lastName' => (string) $user->last_name,
            'roles' => $user->roles,
        ]);
    }
}
