<?php

use Ecoursity\App\Models\Course;

$props = $props ?? [];
$id_course = $props['id'] ?? null;
$course = Course::find((int) $id_course);

if (!$course) {
    echo '<p class="course-preview__error">Kursus tidak ditemukan.</p>';
    return;
}

$formatCurrency = static function ($value): string {
    if ($value === '' || $value === null) {
        return 'Gratis';
    }

    $numericValue = preg_replace('/[^\d.,]/', '', (string) $value);
    $normalizedValue = str_contains($numericValue, ',') && str_contains($numericValue, '.')
        ? str_replace('.', '', str_replace(',', '.', $numericValue))
        : str_replace(',', '.', $numericValue);

    if ($normalizedValue === '' || !is_numeric($normalizedValue)) {
        return (string) $value;
    }

    return 'Rp' . number_format((float) $normalizedValue, 0, ',', '.');
};

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
$formattedPrice = $formatCurrency($price);
$formattedPriceSale = $formatCurrency($priceSale);

if ($priceSale !== '') {
    $displayPrice = '<span class="course-preview__price-sale">' . esc_html($formattedPriceSale) . '</span>'
        . ' <span class="course-preview__price-original">' . esc_html($formattedPrice) . '</span>';
} else {
    $displayPrice = esc_html($formattedPrice);
}

$editUrl = $canManage ? get_edit_post_link($courseId) : '';
$viewUrl = get_permalink($courseId);
$metaLabels = [
    '_ecoursity_duration'          => 'Durasi',
    '_ecoursity_level'             => 'Level',
    '_ecoursity_max_students'      => 'Kapasitas siswa',
    '_ecoursity_price'             => 'Harga',
    '_ecoursity_price_sale'        => 'Harga diskon',
    '_ecoursity_price_sale_start'  => 'Diskon mulai',
    '_ecoursity_price_sale_end'    => 'Diskon berakhir',
    '_ecoursity_course_evaluation' => 'Evaluasi',
    '_ecoursity_passing_grade'     => 'Nilai kelulusan',
    '_ecoursity_requirements'      => 'Persyaratan',
    '_ecoursity_target_audiences'  => 'Untuk siapa kursus ini',
];
?>

<article class="course-preview">
    <div class="course-preview__media">
        <div class="course-preview__thumb">
            <?php if ($thumbnail !== ''): ?>
                <img
                    src="<?php echo esc_url($thumbnail); ?>"
                    alt="<?php echo esc_attr($title ?: 'Thumbnail kursus'); ?>"
                    class="course-preview__thumb-img">
            <?php else: ?>
                <div class="course-preview__thumb-fallback" aria-hidden="true">
                    <span class="course-preview__thumb-fallback-icon">📖</span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="course-preview__body">
        <header class="course-preview__header">
            <div class="course-preview__eyebrow">
                <div class="course-preview__badges">
                    <?php if (!empty($level)): ?>
                        <span class="course-preview__badge course-preview__badge--level"><?php echo esc_html((string) $level); ?></span>
                    <?php endif; ?>
                    <?php if (!empty($duration)): ?>
                        <span class="course-preview__badge course-preview__badge--duration"><?php echo esc_html((string) $duration ? $duration[0] . ' ' . $duration[1] : '—'); ?></span>
                    <?php endif; ?>
                    <span class="course-preview__badge course-preview__badge--status course-preview__badge--status-<?php echo esc_attr($status); ?>"><?php echo esc_html($statusLabel); ?></span>
                </div>
                <div class="course-preview__price"><?php echo $displayPrice; ?></div>
            </div>

            <div class="course-preview__headline">
                <h3 class="course-preview__title"><?php echo esc_html($title ?: 'Pratinjau Kursus'); ?></h3>
                <?php if (!empty($slug)): ?>
                    <p class="course-preview__slug">/<?php echo esc_html((string) $slug); ?></p>
                <?php endif; ?>
            </div>

        </header>

        <?php if (!empty($content)): ?>
            <section class="course-preview__section course-preview__section--content">
                <h4 class="course-preview__section-title">Deskripsi</h4>
                <div class="course-preview__content-text">
                    <?php echo wp_kses_post(wpautop($content)); ?>
                </div>
            </section>
        <?php endif; ?>

        <section class="course-preview__section course-preview__section--meta">
            <h4 class="course-preview__section-title">Ringkasan</h4>
            <div class="course-preview__meta-grid">
                <div class="course-preview__meta-card">
                    <span class="course-preview__meta-label">Slug</span>
                    <span class="course-preview__meta-value"><?php echo $slug ? esc_html((string) $slug) : '—'; ?></span>
                </div>
                <div class="course-preview__meta-card">
                    <span class="course-preview__meta-label">Harga</span>
                    <span class="course-preview__meta-value"><?php echo $displayPrice; ?></span>
                </div>
                <?php if ($rating !== ''): ?>
                    <div class="course-preview__meta-card">
                        <span class="course-preview__meta-label">Rating</span>
                        <span class="course-preview__meta-value course-preview__meta-value--rating">
                            <span class="course-preview__stars">★★★★★</span>
                            <span class="course-preview__rating-num"><?php echo esc_html((string) $rating); ?></span>
                        </span>
                    </div>
                <?php endif; ?>
                <?php if ($enrolled !== ''): ?>
                    <div class="course-preview__meta-card">
                        <span class="course-preview__meta-label">Terdaftar</span>
                        <span class="course-preview__meta-value"><?php echo esc_html((string) $enrolled); ?> peserta</span>
                    </div>
                <?php endif; ?>
                <?php if ($maxStudents !== ''): ?>
                    <div class="course-preview__meta-card">
                        <span class="course-preview__meta-label">Kapasitas</span>
                        <span class="course-preview__meta-value"><?php echo esc_html((string) $maxStudents); ?> siswa</span>
                    </div>
                <?php endif; ?>
                <?php if ($passingGrade !== ''): ?>
                    <div class="course-preview__meta-card">
                        <span class="course-preview__meta-label">Nilai lulus</span>
                        <span class="course-preview__meta-value"><?php echo esc_html((string) $passingGrade); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="course-preview__section course-preview__section--settings">
            <h4 class="course-preview__section-title">Detail kursus</h4>
            <div class="course-preview__settings-grid">
                <?php foreach ($course->meta_keys as $metaKey): ?>
                    <?php
                    $metaValue = $course->meta($metaKey);
                    if ($metaValue === null || $metaValue === '' || (is_array($metaValue) && empty($metaValue))) {
                        continue;
                    }
                    $label = $metaLabels[$metaKey] ?? str_replace('_ecoursity_', '', $metaKey);
                    ?>
                    <div class="course-preview__settings-item">
                        <span class="course-preview__settings-label"><?php echo esc_html($label); ?></span>
                        <span class="course-preview__settings-value">
                            <?php
                            if ($metaKey === '_ecoursity_duration') {
                                echo esc_html((string) $metaValue[0] . ' ' . $metaValue[1]);
                            } elseif (in_array($metaKey, ['_ecoursity_requirements', '_ecoursity_target_audiences'], true) && is_array($metaValue)) {
                                echo esc_html(implode(', ', array_map('strval', $metaValue)));
                            } elseif (in_array($metaKey, ['_ecoursity_price', '_ecoursity_price_sale'], true)) {
                                echo esc_html($formatCurrency($metaValue));
                            } else {
                                echo esc_html((string) $metaValue);
                            }
                            ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <footer class="course-preview__footer">
            <?php if ($authorName || $createdAt): ?>
                <div class="course-preview__author">
                    <?php if ($authorId && $authorAvatar): ?>
                        <img src="<?php echo esc_url($authorAvatar); ?>"
                            alt="<?php echo esc_attr($authorName); ?>"
                            class="course-preview__author-avatar">
                    <?php endif; ?>
                    <div class="course-preview__author-text">
                        <?php if ($authorName): ?>
                            <span class="course-preview__author-name">Oleh <?php echo esc_html($authorName); ?></span>
                        <?php endif; ?>
                        <?php if ($createdAt): ?>
                            <span class="course-preview__author-meta">Dibuat <?php echo esc_html($createdAt); ?></span>
                        <?php endif; ?>
                        <?php if ($updatedAt && $updatedAt !== $createdAt): ?>
                            <span class="course-preview__author-meta">Diubah <?php echo esc_html($updatedAt); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="course-preview__actions">
                <?php if ($editUrl): ?>
                    <a href="<?php echo esc_url($editUrl); ?>" class="course-preview__action course-preview__action--primary" target="_blank" rel="noopener noreferrer">
                        Edit kursus
                    </a>
                <?php endif; ?>
                <?php if ($viewUrl): ?>
                    <a href="<?php echo esc_url($viewUrl); ?>" class="course-preview__action course-preview__action--outline" target="_blank" rel="noopener noreferrer">
                        Lihat kursus
                    </a>
                <?php endif; ?>
            </div>
        </footer>
    </div>
</article>
