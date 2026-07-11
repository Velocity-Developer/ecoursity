<?php

use Ecoursity\App\Models\Course;

$props = $props ?? [];
$id_course = $props['id'] ?? null;
$course = Course::find((int) $id_course);

if (!$course) {
    echo '<p class="course-preview__error">Kursus tidak ditemukan.</p>';
    return;
}

$courseId       = $course->id;
$thumbnail      = $course->thumbnail() ?: '';
$title          = $course->title ?? '';
$slug           = $course->slug ?? '';
$status         = $course->status ?? '';
$price          = $course->price ?? '';
$priceSale      = $course->price_sale ?? '';
$priceSaleStart = $course->price_sale_start ?? '';
$priceSaleEnd   = $course->price_sale_end ?? '';
$duration       = $course->duration ?? '';
$level          = $course->level ?? '';
$excerpt        = $course->excerpt ?? '';
$content        = $course->content ?? '';
$authorId       = $course->author ?? 0;
$enrolled       = $course->meta('_ecoursity_enrolled_count') ?: '';
$rating         = $course->course_evaluation ?: '';
$maxStudents    = $course->max_students ?: '';
$passingGrade   = $course->passing_grade ?: '';

// Admin/author check
$isAdmin   = current_user_can('manage_options');
$isAuthor  = (int) get_current_user_id() === $authorId;
$canManage = $isAdmin || $isAuthor || current_user_can('edit_post', $courseId);

// Author name
$authorName   = $authorId ? get_the_author_meta('display_name', $authorId) : '';
$authorAvatar = $authorId ? get_avatar_url($authorId, ['size' => 28]) : '';

// Dates from WP post
$postObj   = get_post($courseId);
$createdAt = $postObj ? get_the_date('j F Y', $postObj) : '';
$updatedAt = $postObj ? get_the_modified_date('j F Y', $postObj) : '';

// Status label (Indonesia)
$statusLabels = [
    'publish' => 'Terbit',
    'draft'   => 'Draf',
    'pending' => 'Tertunda',
    'private' => 'Pribadi',
    'trash'   => 'Sampah',
];
$statusLabel = $statusLabels[$status] ?? ucfirst($status);

// Price display
if ($priceSale !== '') {
    $displayPrice = '<span class="course-preview__price-sale">' . esc_html((string) $priceSale) . '</span>'
        . ' <span class="course-preview__price-original">' . esc_html((string) $price) . '</span>';
} elseif ($price !== '') {
    $displayPrice = esc_html((string) $price);
} else {
    $displayPrice = 'Gratis';
}

$editUrl = $canManage ? get_edit_post_link($courseId) : '';
$viewUrl = get_permalink($courseId);
?>

<div class="course-preview">
    <div class="course-preview__thumb">
        <?php if ($thumbnail !== ''): ?>
            <img
                src="<?php echo esc_url($thumbnail); ?>"
                alt="<?php echo esc_attr($title ?: 'Thumbnail kursus'); ?>"
                class="course-preview__thumb-img">
        <?php else: ?>
            <div class="course-preview__thumb-fallback">
                <span class="course-preview__thumb-fallback-icon">📖</span>
            </div>
        <?php endif; ?>
    </div>

    <div class="course-preview__info">
        <!-- Badges -->
        <div class="course-preview__badges">
            <?php if (!empty($level)): ?>
                <span class="course-preview__badge course-preview__badge--level">
                    <?php echo esc_html((string) $level); ?>
                </span>
            <?php endif; ?>
            <?php if (!empty($duration)): ?>
                <span class="course-preview__badge course-preview__badge--duration">
                    <?php echo esc_html((string) $duration); ?>
                </span>
            <?php endif; ?>
            <span class="course-preview__badge course-preview__badge--status course-preview__badge--status-<?php echo esc_attr($status); ?>">
                <?php echo esc_html($statusLabel); ?>
            </span>
        </div>

        <!-- Title -->
        <h3 class="course-preview__title">
            <?php echo esc_html($title ?: 'Pratinjau Kursus'); ?>
        </h3>

        <!-- Excerpt -->
        <?php if (!empty($excerpt)): ?>
            <p class="course-preview__excerpt"><?php echo esc_html((string) $excerpt); ?></p>
        <?php endif; ?>

        <!-- Content -->
        <?php if (!empty($content)): ?>
            <div class="course-preview__content-text">
                <?php echo wp_kses_post(wpautop($content)); ?>
            </div>
        <?php endif; ?>

        <!-- Meta Data -->
        <div class="course-preview__meta">
            <div class="course-preview__meta-row">
                <span class="course-preview__meta-label">Slug</span>
                <span class="course-preview__meta-value"><?php echo $slug ?: '—'; ?></span>
            </div>
            <div class="course-preview__meta-row">
                <span class="course-preview__meta-label">Harga</span>
                <span class="course-preview__meta-value"><?php echo $displayPrice; ?></span>
            </div>
            <?php if ($rating !== ''): ?>
                <div class="course-preview__meta-row">
                    <span class="course-preview__meta-label">Rating</span>
                    <span class="course-preview__meta-value">
                        <span class="course-preview__stars">★★★★★</span>
                        <span class="course-preview__rating-num"><?php echo esc_html((string) $rating); ?></span>
                    </span>
                </div>
            <?php endif; ?>
            <?php if ($enrolled !== ''): ?>
                <div class="course-preview__meta-row">
                    <span class="course-preview__meta-label">Terdaftar</span>
                    <span class="course-preview__meta-value"><?php echo esc_html((string) $enrolled); ?> peserta</span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Meta Keys (dinamis) -->
        <div class="course-preview__settings">
            <h4 class="course-preview__settings-title">Meta keys</h4>
            <div class="course-preview__settings-grid">
                <?php
                $metaLabels = [
                    '_ecoursity_duration'           => 'Durasi',
                    '_ecoursity_level'              => 'Level',
                    '_ecoursity_max_students'       => 'Kapasitas siswa',
                    '_ecoursity_price'              => 'Harga',
                    '_ecoursity_price_sale'         => 'Harga diskon',
                    '_ecoursity_price_sale_start'   => 'Diskon mulai',
                    '_ecoursity_price_sale_end'     => 'Diskon berakhir',
                    '_ecoursity_course_evaluation'  => 'Evaluasi',
                    '_ecoursity_passing_grade'      => 'Nilai kelulusan',
                ];
                foreach ($course->meta_keys as $metaKey):
                    $metaValue = $course->meta($metaKey);
                    if ($metaValue === null || $metaValue === ''):
                        continue;
                    endif;
                    $label = $metaLabels[$metaKey] ?? str_replace('_ecoursity_', '', $metaKey);
                ?>
                    <div class="course-preview__settings-item">
                        <span class="course-preview__settings-label"><?php echo esc_html($label); ?></span>
                        <span class="course-preview__settings-value"><?php echo esc_html((string) $metaValue); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Admin / Author Section -->
        <div class="course-preview__admin">
            <?php if ($authorName || $createdAt): ?>
                <div class="course-preview__admin-meta">
                    <?php if ($authorId && $authorAvatar): ?>
                        <img src="<?php echo esc_url($authorAvatar); ?>"
                            alt="<?php echo esc_attr($authorName); ?>"
                            class="course-preview__admin-avatar">
                    <?php endif; ?>
                    <span class="course-preview__admin-detail">
                        <?php if ($authorName): ?>
                            <span class="course-preview__admin-author">Oleh <?php echo esc_html($authorName); ?></span>
                        <?php endif; ?>
                        <?php if ($createdAt): ?>
                            <span class="course-preview__admin-date">Dibuat <?php echo esc_html($createdAt); ?></span>
                        <?php endif; ?>
                        <?php if ($updatedAt && $updatedAt !== $createdAt): ?>
                            <span class="course-preview__admin-date">Diubah <?php echo esc_html($updatedAt); ?></span>
                        <?php endif; ?>
                    </span>
                </div>
            <?php endif; ?>

            <div class="course-preview__admin-actions">
                <?php if ($editUrl): ?>
                    <a href="<?php echo esc_url($editUrl); ?>" class="course-preview__admin-link course-preview__admin-link--edit" target="_blank">
                        Edit kursus
                    </a>
                <?php endif; ?>
                <?php if ($viewUrl): ?>
                    <a href="<?php echo esc_url($viewUrl); ?>" class="course-preview__admin-link course-preview__admin-link--view" target="_blank">
                        Lihat
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>