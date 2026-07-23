<?php

namespace Ecoursity\App\Providers;

use Ecoursity\App\Template;

class TemplateProvider
{
    public function boot()
    {
        add_filter('single_template', [$this, 'single_template']);
    }

    public function single_template($templates)
    {
        global $post;

        ///if post type 'ecoursity_course'
        if ($post->post_type === 'ecoursity_course') {
            $templates = Template::get('pages/public/single-course');
        }

        return $templates;
    }
}
