<?php

/**
 * Template for displaying the public instructor archive.
 *
 * @author  Velocity Developer Team
 * @package Ecoursity/Template
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

get_header();
?>

<main class="ecoursity-instructor-archive">
    <section class="ecoursity-instructor-archive__hero">
        <div class="ecoursity-instructor-archive__hero-inner">
            <p class="ecoursity-instructor-archive__eyebrow"><?php esc_html_e('Instruktur', 'ecoursity'); ?></p>
            <h1 class="ecoursity-instructor-archive__title"><?php post_type_archive_title(); ?></h1>
            <p class="ecoursity-instructor-archive__description">
                <?php esc_html_e('Temukan instruktur berpengalaman yang siap membimbing perjalanan belajar Anda.', 'ecoursity'); ?>
            </p>
        </div>
    </section>

    <section class="ecoursity-instructor-archive__content">
        <?php
        $instructors = \Ecoursity\App\Models\Instructor::all(['count' => -1]);

        if (!empty($instructors)) : ?>
            <div class="ecoursity-instructor-archive__grid">
                <?php foreach ($instructors as $instructor) : ?>
                    <?php echo do_shortcode(sprintf('[ecoursity-instructor-card instructor_id="%d"]', $instructor->id)); ?>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <div class="ecoursity-instructor-archive__empty">
                <h2><?php esc_html_e('Belum ada instruktur', 'ecoursity'); ?></h2>
                <p><?php esc_html_e('Instruktur yang terdaftar akan tampil di halaman ini.', 'ecoursity'); ?></p>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php
get_footer();
