<?php

namespace Ecoursity\App\Providers;

use Ecoursity\App\Models\Course;
use Ecoursity\App\Template;

class TemplateProvider
{
    private const EMPTY_PAGE_TEMPLATE = 'ecoursity-empty-page-template.php';

    public function boot()
    {
        add_filter('single_template', [$this, 'single_template']);
        add_filter('archive_template', [$this, 'archive_template']);
        add_filter('theme_page_templates', [$this, 'register_page_templates']);
        add_filter('template_include', [$this, 'template_include']);
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

    public function register_page_templates($post_templates)
    {
        $post_templates[self::EMPTY_PAGE_TEMPLATE] = __('Ecoursity Empty Page Template', 'ecoursity');

        return $post_templates;
    }

    public function template_include($template)
    {
        if (is_page() && get_page_template_slug() === self::EMPTY_PAGE_TEMPLATE) {
            $ecoursity_template = Template::get('pages/public/empty-page-template');

            if ($ecoursity_template !== false) {
                return $ecoursity_template;
            }
        }

        return $template;
    }
}
