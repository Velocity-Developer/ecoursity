<?php

namespace Ecoursity\App\Providers;

use Ecoursity\App\Models\Cart;

class UserServiceProvider
{
    public function boot(): void
    {
        add_action('init', [$this, 'registerRoles']);
        add_action('wp_login', [$this, 'syncCartSessionToUser'], 10, 2);
    }

    public function registerRoles(): void
    {
        add_role('ecoursity_student', 'Siswa Ecoursity', [
            'read' => true,
        ]);

        add_role('ecoursity_instructor', 'Instruktur Ecoursity', [
            'read' => true,
            'upload_files' => true,
        ]);
    }

    public function syncCartSessionToUser(string $userLogin, \WP_User $user): void
    {
        Cart::syncSessionToUser((int) $user->ID);
    }
}
