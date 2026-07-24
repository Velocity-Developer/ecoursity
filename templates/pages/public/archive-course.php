<?php

/**
 * Template for displaying the public course archive.
 *
 * @author  Velocity Developer Team
 * @package Ecoursity/Template
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

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
                    <?php the_post(); ?>
                    <?php echo do_shortcode(sprintf('[ecoursity-course-card course_id="%d"]', get_the_ID())); ?>
                <?php endwhile; ?>
            </div>

            <nav class="ecoursity-course-archive__pagination" aria-label="<?php esc_attr_e('Navigasi kursus', 'ecoursity'); ?>">
                <?php
                echo (paginate_links([
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
