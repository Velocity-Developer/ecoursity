<?php

namespace Ecoursity\App\Providers;

use Ecoursity\App\Models\Cart;

class UserServiceProvider
{
    public function boot(): void
    {
        add_action('init', [$this, 'registerRoles']);
        add_action('register_form', [$this, 'renderRegistrationPasswordFields']);
        add_filter('registration_errors', [$this, 'validateRegistrationPasswordFields'], 10, 3);
        add_filter('random_password', [$this, 'useSubmittedRegistrationPassword'], 10, 4);
        add_filter('pre_option_default_role', [$this, 'useStudentRoleForRegistration'], 10, 3);
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

    public function renderRegistrationPasswordFields(): void
    {
        $password = $this->postedPassword('ecoursity_password');
        $confirmPassword = $this->postedPassword('ecoursity_confirm_password');
?>
        <p>
            <label for="ecoursity_password">
                <?php echo esc_html__('Password', 'ecoursity'); ?>
            </label>
            <input
                type="password"
                name="ecoursity_password"
                id="ecoursity_password"
                class="input"
                value="<?php echo esc_attr($password); ?>"
                size="25"
                autocomplete="new-password"
                required>
        </p>
        <p>
            <label for="ecoursity_confirm_password">
                <?php echo esc_html__('Confirm Password', 'ecoursity'); ?>
            </label>
            <input
                type="password"
                name="ecoursity_confirm_password"
                id="ecoursity_confirm_password"
                class="input"
                value="<?php echo esc_attr($confirmPassword); ?>"
                size="25"
                autocomplete="new-password"
                required>
        </p>
<?php
    }

    public function validateRegistrationPasswordFields(
        \WP_Error $errors,
        string $sanitizedUserLogin,
        string $userEmail
    ): \WP_Error {
        $password = $this->postedPassword('ecoursity_password');
        $confirmPassword = $this->postedPassword('ecoursity_confirm_password');

        if ($password === '') {
            $errors->add(
                'ecoursity_password_required',
                __('<strong>Error:</strong> Password wajib diisi.', 'ecoursity')
            );
        }

        if ($confirmPassword === '') {
            $errors->add(
                'ecoursity_confirm_password_required',
                __('<strong>Error:</strong> Konfirmasi password wajib diisi.', 'ecoursity')
            );
        }

        if ($password !== '' && strlen($password) < 8) {
            $errors->add(
                'ecoursity_password_too_short',
                __('<strong>Error:</strong> Password minimal 8 karakter.', 'ecoursity')
            );
        }

        if ($password !== '' && $confirmPassword !== '' && $password !== $confirmPassword) {
            $errors->add(
                'ecoursity_password_mismatch',
                __('<strong>Error:</strong> Password dan konfirmasi password tidak sama.', 'ecoursity')
            );
        }

        return $errors;
    }

    public function useSubmittedRegistrationPassword(
        string $password,
        int $length,
        bool $specialChars,
        bool $extraSpecialChars
    ): string {
        if (! $this->isCoreRegistrationRequest()) {
            return $password;
        }

        $submittedPassword = $this->postedPassword('ecoursity_password');

        return $submittedPassword !== '' ? $submittedPassword : $password;
    }

    public function useStudentRoleForRegistration(mixed $defaultRole, string $option, mixed $defaultValue): mixed
    {
        if (! $this->isCoreRegistrationRequest()) {
            return $defaultRole;
        }

        return 'ecoursity_student';
    }

    public function syncCartSessionToUser(string $userLogin, \WP_User $user): void
    {
        Cart::syncSessionToUser((int) $user->ID);
    }

    private function postedPassword(string $key): string
    {
        if (! isset($_POST[$key])) {
            return '';
        }

        return (string) wp_unslash($_POST[$key]);
    }

    private function isCoreRegistrationRequest(): bool
    {
        $action = isset($_REQUEST['action']) ? sanitize_key(wp_unslash($_REQUEST['action'])) : '';

        return $action === 'register';
    }
}
