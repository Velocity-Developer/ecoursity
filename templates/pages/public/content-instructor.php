<?php

/**
 * Template part for displaying an instructor card.
 *
 * @author  Velocity Developer Team
 * @package Ecoursity/Template
 * @version 1.0.0
 */

use Ecoursity\App\Models\Instructor;

defined('ABSPATH') || exit;

$instructor = $instructor ?? null;

if (!$instructor instanceof Instructor) {
    return;
}

$instructorId = (int) $instructor->id;
$displayName  = $instructor->displayName ?: ($instructor->display_name ?: '');
$firstName    = $instructor->firstName ?: ($instructor->first_name ?: '');
$lastName     = $instructor->lastName ?: ($instructor->last_name ?: '');
$fullName     = $displayName ?: trim($firstName . ' ' . $lastName);

$avatarUrl = get_avatar_url($instructorId, ['size' => 160]);
$profileUrl = get_author_posts_url($instructorId);

// Course count
$courseQuery = new WP_Query([
    'post_type'      => 'ecoursity_course',
    'post_status'    => 'publish',
    'author'         => $instructorId,
    'posts_per_page' => -1,
    'fields'         => 'ids',
]);
$courseCount = (int) $courseQuery->found_posts;

// Student count: sum _ecoursity_enrolled_count across all courses
$studentCount = 0;
if (!empty($courseQuery->posts)) {
    global $wpdb;
    $ids  = implode(',', array_map('intval', $courseQuery->posts));
    $meta = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id IN ({$ids}) AND meta_key = %s",
            '_ecoursity_enrolled_count'
        )
    );
    foreach ($meta as $row) {
        $studentCount += (int) $row->meta_value;
    }
}
?>

<article class="ecoursity-instructor-card" itemscope itemtype="https://schema.org/Person">
    <div class="ecoursity-instructor-card__media">
        <?php if ($avatarUrl): ?>
            <img
                src="<?php echo esc_url($avatarUrl); ?>"
                alt="<?php echo esc_attr($fullName); ?>"
                class="ecoursity-instructor-card__avatar"
                itemprop="image">
        <?php else: ?>
            <div class="ecoursity-instructor-card__avatar-fallback" aria-hidden="true">
                <span class="ecoursity-instructor-card__avatar-initial">
                    <?php echo esc_html(mb_substr($fullName, 0, 1) ?: '?'); ?>
                </span>
            </div>
        <?php endif; ?>
    </div>

    <div class="ecoursity-instructor-card__body">
        <h3 class="ecoursity-instructor-card__name" itemprop="name">
            <?php echo esc_html($fullName ?: __('Instruktur', 'ecoursity')); ?>
        </h3>

        <div class="ecoursity-instructor-card__stats">
            <div class="ecoursity-instructor-card__stat">
                <span class="ecoursity-instructor-card__stat-value"><?php echo esc_html((string) $courseCount); ?></span>
                <span class="ecoursity-instructor-card__stat-label"><?php esc_html_e('Kursus', 'ecoursity'); ?></span>
            </div>
            <div class="ecoursity-instructor-card__stat">
                <span class="ecoursity-instructor-card__stat-value"><?php echo esc_html((string) $studentCount); ?></span>
                <span class="ecoursity-instructor-card__stat-label"><?php esc_html_e('Siswa', 'ecoursity'); ?></span>
            </div>
        </div>
    </div>

    <div class="ecoursity-instructor-card__footer">
        <a
            href="<?php echo esc_url($profileUrl); ?>"
            class="ecoursity-instructor-card__link"
            itemprop="url">
            <?php esc_html_e('Lihat Profil', 'ecoursity'); ?>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
        </a>
    </div>
</article>