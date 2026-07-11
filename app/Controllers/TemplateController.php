<?php

namespace Ecoursity\App\Controllers;

use Ecoursity\App\Template;
use WP_Error;
use WP_REST_Request;

class TemplateController
{
    public function view(WP_REST_Request $request): array|WP_Error
    {
        ob_start();
        $props = $request->get_params();
        $html = Template::view($request->get_param('template'), compact('props'));
        $html = ob_get_clean();

        if ($html === false) {
            return new WP_Error('template_not_found', 'Template not found.', ['status' => 404]);
        }

        return [
            'is_template'   => true,
            'template'  => $request->get_param('template'),
            'html' => $html,
        ];
    }

    public function component(WP_REST_Request $request): array|WP_Error
    {
        ob_start();
        $props = $request->get_params();
        $html = Template::component($request->get_param('component_name'), compact('props'));
        $html = ob_get_clean();

        if ($html === false) {
            return new WP_Error('component_not_found', 'Component not found.', ['status' => 404]);
        }

        return [
            'is_template'   => true,
            'request' => $request->get_params(),
            'component' => $request->get_param('component_name'),
            'html' => $html,
        ];
    }
}
