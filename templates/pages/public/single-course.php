<?php

/**
 * Template for displaying a single public course.
 *
 * @author  Velocity Developer Team
 * @package Ecoursity/Template
 * @version 1.2.0
 */

defined('ABSPATH') || exit;

get_header();
?>

<?php while (have_posts()) : ?>
    <?php the_post(); ?>

    <main class="ecoursity-single-course" x-data="{ tab: 'overview' }">
        <?php echo do_shortcode('[ecoursity-course-hero]'); ?>

        <div class="ecoursity-course-layout">
            <div class="ecoursity-course-layout__main">
                <?php echo do_shortcode('[ecoursity-course-tabs]'); ?>

                <section class="ecoursity-course-panel" x-show="tab === 'overview'">
                    <h2><?php esc_html_e('Tentang Kursus Ini', 'ecoursity'); ?></h2>
                    <?php echo do_shortcode('[ecoursity-course-overview]'); ?>
                </section>

                <section id="ecoursity-course-curriculum" class="ecoursity-course-panel" x-show="tab === 'curriculum'" x-cloak>
                    <div class="ecoursity-course-panel__heading">
                        <h2><?php esc_html_e('Kurikulum Kursus', 'ecoursity'); ?></h2>
                    </div>
                    <?php echo do_shortcode('[ecoursity-course-curriculum]'); ?>
                </section>

                <section class="ecoursity-course-panel" x-show="tab === 'instructor'" x-cloak>
                    <h2><?php esc_html_e('Tentang Instruktur', 'ecoursity'); ?></h2>
                    <?php echo do_shortcode('[ecoursity-course-instructor]'); ?>
                </section>

                <section class="ecoursity-course-panel" x-show="tab === 'faq'" x-cloak>
                    <h2><?php esc_html_e('Pertanyaan Umum', 'ecoursity'); ?></h2>
                    <?php echo do_shortcode('[ecoursity-course-faq]'); ?>
                </section>
            </div>

            <aside class="ecoursity-course-sidebar">
                <?php echo do_shortcode('[ecoursity-course-sidebar]'); ?>
            </aside>
        </div>
    </main>
<?php endwhile; ?>

<?php
get_footer();
