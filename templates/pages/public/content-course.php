<?php

/**
 * Template part for displaying a course card.
 *
 * @author  Velocity Developer Team
 * @package Ecoursity/Template
 * @version 1.0.0
 */

use Ecoursity\App\Models\Course;

defined('ABSPATH') || exit;

if (!$course instanceof Course) {
    return;
}

$courseId = (int) $course->id;
$courseUrl = do_shortcode(sprintf('[ecoursity-course-url course_id="%d"]', $courseId));
?>

<article class="ecoursity-course-card">
    <a class="ecoursity-course-card__media" href="<?php echo esc_url($courseUrl); ?>">
        <?php
        echo do_shortcode(sprintf(
            '[ecoursity-course-image course_id="%d" size="large" ratio="16/10"]',
            $courseId
        ));
        ?>
    </a>

    <div class="ecoursity-course-card__body">
        <div class="ecoursity-course-card__topline">
            <span><?php echo do_shortcode(sprintf('[ecoursity-course-label course_id="%d"]', $courseId)); ?></span>
            <span><?php echo do_shortcode(sprintf('[ecoursity-course-level course_id="%d"]', $courseId)); ?></span>
        </div>

        <h2 class="ecoursity-course-card__title">
            <a href="<?php echo esc_url($courseUrl); ?>">
                <?php echo do_shortcode(sprintf('[ecoursity-course-title course_id="%d"]', $courseId)); ?>
            </a>
        </h2>

        <?php echo do_shortcode(sprintf('[ecoursity-course-excerpt course_id="%d"]', $courseId)); ?>

        <div class="ecoursity-course-card__footer">
            <?php echo do_shortcode(sprintf('[ecoursity-course-price course_id="%d"]', $courseId)); ?>
            <span class="ecoursity-course-card__duration">
                <?php echo do_shortcode(sprintf('[ecoursity-course-duration course_id="%d"]', $courseId)); ?>
            </span>
        </div>
    </div>
</article>
