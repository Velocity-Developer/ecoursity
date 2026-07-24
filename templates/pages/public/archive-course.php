<?php

/**
 * Template for displaying the public course archive.
 *
 * @author  Velocity Developer Team
 * @package Ecoursity/Template
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

$formatPrice = static function (mixed $price): string {
    $normalized = trim((string) $price);

    if ($normalized === '' || $normalized === '0') {
        return __('Gratis', 'ecoursity');
    }

    $numericValue = preg_replace('/[^\d.,]/', '', $normalized);
    $numericValue = str_contains($numericValue, ',') && str_contains($numericValue, '.')
        ? str_replace('.', '', str_replace(',', '.', $numericValue))
        : str_replace(',', '.', $numericValue);

    if ($numericValue === '' || !is_numeric($numericValue)) {
        return $normalized;
    }

    return 'Rp' . number_format((float) $numericValue, 0, ',', '.');
};

$hasPrice = static function (mixed $price): bool {
    $numericValue = preg_replace('/[^\d.,]/', '', (string) $price);
    $numericValue = str_contains($numericValue, ',') && str_contains($numericValue, '.')
        ? str_replace('.', '', str_replace(',', '.', $numericValue))
        : str_replace(',', '.', $numericValue);

    return $numericValue !== '' && is_numeric($numericValue) && (float) $numericValue > 0;
};

$formatDuration = static function (mixed $duration): string {
    if (!is_array($duration) || empty($duration[0]) || empty($duration[1])) {
        return '';
    }

    $amount = absint($duration[0]);
    $unit = sanitize_key((string) $duration[1]);
    $labels = [
        'day' => _n('hari', 'hari', $amount, 'ecoursity'),
        'week' => _n('minggu', 'minggu', $amount, 'ecoursity'),
        'month' => _n('bulan', 'bulan', $amount, 'ecoursity'),
        'year' => _n('tahun', 'tahun', $amount, 'ecoursity'),
    ];

    if ($amount < 1 || !isset($labels[$unit])) {
        return '';
    }

    return sprintf('%d %s', $amount, $labels[$unit]);
};

$formatLevel = static function (string $level): string {
    return [
        'all' => __('Semua level', 'ecoursity'),
        'beginner' => __('Pemula', 'ecoursity'),
        'intermediate' => __('Menengah', 'ecoursity'),
        'expert' => __('Mahir', 'ecoursity'),
    ][$level] ?? '';
};

get_header();
?>

<main class="ecoursity-course-archive">
    <section class="ecoursity-course-archive__hero">
        <div class="ecoursity-course-archive__hero-inner">
            <p class="ecoursity-course-archive__eyebrow"><?php esc_html_e('Katalog Kursus', 'ecoursity'); ?></p>
            <h1 class="ecoursity-course-archive__title"><?php post_type_archive_title(); ?></h1>
            <p class="ecoursity-course-archive__description">
                <?php esc_html_e('Temukan materi belajar yang dirancang untuk membantu Anda naik level dengan alur yang jelas dan praktis.', 'ecoursity'); ?>
            </p>
        </div>
    </section>

    <section class="ecoursity-course-archive__content">
        <?php if (have_posts()) : ?>
            <div class="ecoursity-course-archive__grid">
                <?php while (have_posts()) : ?>
                    <?php
                    the_post();

                    $courseId = get_the_ID();
                    $duration = get_post_meta($courseId, '_ecoursity_duration', true);
                    $level = (string) get_post_meta($courseId, '_ecoursity_level', true);
                    $price = (string) get_post_meta($courseId, '_ecoursity_price', true);
                    $salePrice = (string) get_post_meta($courseId, '_ecoursity_price_sale', true);
                    $saleActive = $hasPrice($salePrice);
                    $categories = wp_get_post_terms($courseId, 'ecoursity_course_category', ['fields' => 'names']);
                    $categoryLabel = !is_wp_error($categories) && !empty($categories)
                        ? implode(', ', $categories)
                        : __('Kursus', 'ecoursity');
                    $levelLabel = $formatLevel($level);
                    $durationLabel = $formatDuration($duration);
                    ?>

                    <article class="ecoursity-course-card">
                        <a class="ecoursity-course-card__media" href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('large', ['class' => 'ecoursity-course-card__image']); ?>
                            <?php else : ?>
                                <span class="ecoursity-course-card__image-placeholder" aria-hidden="true"></span>
                            <?php endif; ?>
                        </a>

                        <div class="ecoursity-course-card__body">
                            <div class="ecoursity-course-card__topline">
                                <span><?php echo esc_html($categoryLabel); ?></span>
                                <?php if ($levelLabel !== '') : ?>
                                    <span><?php echo esc_html($levelLabel); ?></span>
                                <?php endif; ?>
                            </div>

                            <h2 class="ecoursity-course-card__title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>

                            <?php if (has_excerpt()) : ?>
                                <p class="ecoursity-course-card__excerpt"><?php echo esc_html(get_the_excerpt()); ?></p>
                            <?php endif; ?>

                            <div class="ecoursity-course-card__footer">
                                <div class="ecoursity-course-card__price">
                                    <strong><?php echo esc_html($formatPrice($saleActive ? $salePrice : $price)); ?></strong>
                                    <?php if ($saleActive) : ?>
                                        <del><?php echo esc_html($formatPrice($price)); ?></del>
                                    <?php endif; ?>
                                </div>

                                <?php if ($durationLabel !== '') : ?>
                                    <span class="ecoursity-course-card__duration"><?php echo esc_html($durationLabel); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <nav class="ecoursity-course-archive__pagination" aria-label="<?php esc_attr_e('Navigasi kursus', 'ecoursity'); ?>">
                <?php
                echo wp_kses_post(paginate_links([
                    'prev_text' => __('Sebelumnya', 'ecoursity'),
                    'next_text' => __('Berikutnya', 'ecoursity'),
                ]));
                ?>
            </nav>
        <?php else : ?>
            <div class="ecoursity-course-archive__empty">
                <h2><?php esc_html_e('Belum ada kursus', 'ecoursity'); ?></h2>
                <p><?php esc_html_e('Kursus yang sudah diterbitkan akan tampil di halaman ini.', 'ecoursity'); ?></p>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php
get_footer();
