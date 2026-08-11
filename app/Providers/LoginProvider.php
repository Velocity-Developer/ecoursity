<?php

declare(strict_types=1);

namespace Ecoursity\App\Providers;

use Ecoursity\App\Models\Setting;

defined('ABSPATH') || exit;

class LoginProvider
{
    public function boot(): void
    {
        add_action('login_enqueue_scripts', [$this, 'renderBrandLogoStyles']);
        add_filter('login_headerurl', [$this, 'loginHeaderUrl']);
        add_filter('login_headertext', [$this, 'loginHeaderText']);
    }

    public function renderBrandLogoStyles(): void
    {
        $logoUrl = esc_url((string) Setting::get('brand_logo', ''));

        if ($logoUrl === '') {
            return;
        }

?>
        <style id="ecoursity-login-brand-logo">
            body.login div#login h1 a {
                background-image: url('<?php echo $logoUrl; ?>');
                background-position: center;
                background-repeat: no-repeat;
                background-size: contain;
                width: 100%;
                max-width: 200px;
            }

            body.login #wp-submit {
                width: 100%;
                margin-top: 1rem;
            }
        </style>
<?php
    }

    public function loginHeaderUrl(string $url): string
    {
        if ((string) Setting::get('brand_logo', '') === '') {
            return $url;
        }

        return home_url('/');
    }

    public function loginHeaderText(string $text): string
    {
        if ((string) Setting::get('brand_logo', '') === '') {
            return $text;
        }

        return get_bloginfo('name');
    }
}
