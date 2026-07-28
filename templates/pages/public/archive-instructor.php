<?php

/**
 * Template for displaying the public instructor archive.
 *
 * @author  Velocity Developer Team
 * @package Ecoursity/Template
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

?>
<section class="ecoursity-instructor-archive__content">
    <?php
    $instructors = \Ecoursity\App\Models\Instructor::all(['count' => -1]);

    if (!empty($instructors)) : ?>
        <div class="ecoursity-instructor-archive__grid">
            <?php foreach ($instructors as $instructor) : ?>
                <?php echo do_shortcode(sprintf('[ecoursity-instructor-card instructor_id="%d"]', $instructor->id));
                ?>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <div class="ecoursity-instructor-archive__empty">
            <h2><?php esc_html_e('Belum ada instruktur', 'ecoursity'); ?></h2>
            <p><?php esc_html_e('Instruktur yang terdaftar akan tampil di halaman ini.', 'ecoursity'); ?></p>
        </div>
    <?php endif; ?>
</section>

<?php
