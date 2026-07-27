<?php

declare(strict_types=1);

namespace Ecoursity\App\Controllers\Admin;

use Ecoursity\App\Services\Admin\SettingService;
use Ecoursity\App\Support\SettingSchema;
use Ecoursity\App\Template;

defined('ABSPATH') || exit;

class SettingController
{
    public function index(): void
    {
        $activeTab = $this->activeTab();
        $service = new SettingService();
        $saved = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $saved = $this->save($service, $activeTab, false);
        }

        $tabs = SettingSchema::tabs();
        $activeSchema = SettingSchema::tab($activeTab);
        $values = $service->valuesForTab($activeTab);
        $updated = $saved || (isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true');

        Template::view('pages/admin/settings', compact(
            'tabs',
            'activeTab',
            'activeSchema',
            'values',
            'updated'
        ));
    }

    public function handlePost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $this->save(new SettingService(), $this->activeTab(), true);
    }

    private function save(SettingService $service, string $activeTab, bool $redirect): bool
    {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Anda tidak memiliki akses untuk mengubah pengaturan Ecoursity.', 'ecoursity'));
        }

        check_admin_referer('ecoursity_save_settings');

        $settings = [];

        if (isset($_POST['ecoursity_settings']) && is_array($_POST['ecoursity_settings'])) {
            $settings = wp_unslash($_POST['ecoursity_settings']);
        }

        $service->saveTab($activeTab, $settings);

        if ($redirect && !headers_sent() && wp_safe_redirect(add_query_arg([
            'page' => 'ecoursity-settings',
            'tab' => $activeTab,
            'settings-updated' => 'true',
        ], admin_url('admin.php')))) {
            exit;
        }

        return true;
    }

    private function activeTab(): string
    {
        $rawTab = $_REQUEST['tab'] ?? '';
        $tab = is_string($rawTab) ? sanitize_key(wp_unslash($rawTab)) : '';

        if (!SettingSchema::hasTab($tab)) {
            return SettingSchema::defaultTab();
        }

        return $tab;
    }
}
