<?php

declare(strict_types=1);

use Ecoursity\App\Models\Section;

/**
 * Plugin Name: Ecoursity
 * Plugin URI: https://ecourse.velocitydeveloper.com
 * Description: Plugin LMS modern untuk WordPress.
 * Version: 0.1.0
 * Requires at least: 6.0
 * Requires PHP: 8.2
 * Author: Velocity Developer
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 * Text Domain: ecoursity
 */

if (! defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

if (! defined('ECOURSITY_FILE')) {
    define('ECOURSITY_FILE', __FILE__);
}

if (! defined('ECOURSITY_PATH')) {
    define('ECOURSITY_PATH', plugin_dir_path(__FILE__));
}

if (! defined('ECOURSITY_URL')) {
    define('ECOURSITY_URL', plugin_dir_url(__FILE__));
}

if (! defined('ECOURSITY_VERSION')) {
    define('ECOURSITY_VERSION', '0.1.0');
}

final class EcoursityPlugin
{
    public function boot(): void
    {
        add_action('plugins_loaded', [$this, 'loadTextDomain']);
    }

    public function loadTextDomain(): void
    {
        load_plugin_textdomain('ecoursity', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    public static function activate(): void
    {
        Section::createTables();
        flush_rewrite_rules();
    }

    public static function deactivate(): void
    {
        flush_rewrite_rules();
    }
}

register_activation_hook(__FILE__, [EcoursityPlugin::class, 'activate']);
register_deactivation_hook(__FILE__, [EcoursityPlugin::class, 'deactivate']);

(new EcoursityPlugin())->boot();
(new Ecoursity\App\Init())->boot();
