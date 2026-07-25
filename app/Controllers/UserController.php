<?php

namespace Ecoursity\App\Controllers;

use WP_REST_Response;
use WP_REST_Request;

class UserController
{
    public function orderOptions(WP_REST_Request $request): WP_REST_Response
    {
        $page = max(1, absint($request->get_param('page') ?: 1));
        $perPage = 15;
        $search = trim((string) ($request->get_param('search') ?: ''));

        $queryArgs = [
            'fields' => ['ID', 'display_name', 'user_login', 'user_email'],
            'orderby' => 'display_name',
            'order' => 'ASC',
            'number' => $perPage,
            'offset' => ($page - 1) * $perPage,
            'count_total' => true,
        ];

        if ($search !== '') {
            $queryArgs['search'] = '*' . esc_attr($search) . '*';
            $queryArgs['search_columns'] = ['user_login', 'user_email', 'display_name'];
        }

        $query = new \WP_User_Query($queryArgs);
        $users = $query->get_results();
        $total = (int) $query->get_total();
        $totalPages = max(1, (int) ceil($total / $perPage));

        return new WP_REST_Response([
            'success' => true,
            'data' => array_map(
                static function (\stdClass $user): array {
                    $displayName = trim((string) $user->display_name);
                    $username = (string) $user->user_login;

                    return [
                        'id' => (int) $user->ID,
                        'label' => $displayName !== '' ? $displayName : $username,
                        'username' => $username,
                        'email' => (string) $user->user_email,
                    ];
                },
                $users
            ),
            'meta' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages,
            ],
        ]);
    }
}
