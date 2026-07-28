<?php

declare(strict_types=1);

namespace Ecoursity\App\Models;

defined('ABSPATH') || exit;

use Ecoursity\App\Models\Course;

class Instructor extends User
{
    public const ROLE = 'ecoursity_instructor';

    public static function countCourses(int $instructorId): int
    {
        $query = new WP_Query([
            'post_type'      => Course::POST_TYPE,
            'post_status'    => 'publish',
            'author'         => $instructorId,
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ]);

        return $query->found_posts;
    }

    public static function countStudents(int $instructorId): int
    {
        // Get instructor's published course IDs
        $courseQuery = new WP_Query([
            'post_type'      => Course::POST_TYPE,
            'post_status'    => 'publish',
            'author'         => $instructorId,
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ]);

        if (empty($courseQuery->posts)) {
            return 0;
        }

        global $wpdb;
        $ids = implode(',', array_map('intval', $courseQuery->posts));

        // Sum _ecoursity_enrolled_count for all instructor's course IDs
        $result = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT SUM(CAST(pm.meta_value AS DECIMAL(10,0))) 
                 FROM {$wpdb->postmeta} pm
                 JOIN {$wpdb->posts} p ON p.ID = pm.post_id
                 WHERE p.ID IN ({$ids}) AND pm.meta_key = %s AND p.post_status = %s",
                '_ecoursity_enrolled_count',
                'publish'
            )
        );

        return (int) ($result ?? 0);
    }
}
