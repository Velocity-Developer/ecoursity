<?php

namespace Ecoursity\App\Providers;

class UserServiceProvider
{
    public function boot(): void
    {
        add_action('init', [$this, 'registerRoles']);
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
}
