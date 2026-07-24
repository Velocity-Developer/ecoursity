<?php

namespace Ecoursity\App\Providers;

use Ecoursity\App\Models\Course;
use Ecoursity\App\Template;

class TemplateProvider
{
    public function boot()
    {
        add_filter('single_template', [$this, 'single_template']);
        add_filter('archive_template', [$this, 'archive_template']);
    }

    public function single_template($templates)
    {
        global $post;

        ///if post type 'ecoursity_course'
        if ($post && $post->post_type === Course::POST_TYPE) {
            $templates = Template::get('pages/public/single-course');
        }

        return $templates;
    }

    public function archive_template($templates)
    {
        if (is_post_type_archive(Course::POST_TYPE)) {
            $templates = Template::get('pages/public/archive-course');
        }

        return $templates;
    }
}
