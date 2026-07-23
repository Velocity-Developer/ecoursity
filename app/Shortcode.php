<?php

namespace Ecoursity\App;

use Ecoursity\App\Shortcodes\ButtonBuyCourseShortcode;

class Shortcode
{
    public function boot(): void
    {
        /**
         * [ecoursity-button-buy-course course_id="123" label="Buy Course" login_label="Login untuk Beli Course" free_label="Ambil Course Gratis" class="btn btn-primary"require_login="yes"]
         * **/
        add_shortcode('ecoursity-button-buy-course', [ButtonBuyCourseShortcode::class, 'render']);
    }
}
