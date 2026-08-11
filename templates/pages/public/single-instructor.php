<?php

/**
 * Template for displaying a single public instructor profile.
 *
 * @author  Velocity Developer Team
 * @package Ecoursity/Template
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

get_header();
?>

<main class="ecoursity-single-instructor">
    <?php echo do_shortcode('[ecoursity-instructor-hero]'); ?>

    <?php echo do_shortcode('[ecoursity-instructor-courses]'); ?>
</main>

<?php
get_footer();
