<?php

declare(strict_types=1);

namespace Ecoursity\App\Shortcodes;

defined('ABSPATH') || exit;

class CourseCardShortcode extends CourseSingleShortcodeSupport
{
    public static function render(array|string $atts = []): string
    {
        $course = self::resolveCourse($atts, 'ecoursity-course-card');

        if (!$course) {
            return '';
        }

        $categoryLabel = self::categoryLabel($course);
        $levelLabel = self::formatLevel($course->level);
        $durationLabel = self::formatDuration($course->duration);
        $saleActive = self::hasPositivePrice($course->price_sale);
        $mainPrice = $saleActive ? self::formatPrice($course->price_sale) : self::formatPrice($course->price);
        $regularPrice = $saleActive ? self::formatPrice($course->price) : '';
        $title = get_the_title((int) $course->id);
        $permalink = get_permalink((int) $course->id);
        ob_start();
        ?>
        <article class="ecoursity-course-card">
            <a class="ecoursity-course-card__media" href="<?php echo esc_url($permalink); ?>">
                <?php
                echo do_shortcode(sprintf(
                    '[ecoursity-course-image course_id="%d" size="large" ratio="16/10"]',
                    (int) $course->id
                ));
                ?>
            </a>

            <div class="ecoursity-course-card__body">
                <div class="ecoursity-course-card__topline">
                    <span><?php echo esc_html($categoryLabel); ?></span>
                    <?php if ($levelLabel !== '') : ?>
                        <span><?php echo esc_html($levelLabel); ?></span>
                    <?php endif; ?>
                </div>

                <h2 class="ecoursity-course-card__title">
                    <a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a>
                </h2>

                <?php if ($course->excerpt !== '') : ?>
                    <p class="ecoursity-course-card__excerpt"><?php echo esc_html($course->excerpt); ?></p>
                <?php endif; ?>

                <div class="ecoursity-course-card__footer">
                    <div class="ecoursity-course-card__price">
                        <strong><?php echo esc_html($mainPrice); ?></strong>
                        <?php if ($regularPrice !== '') : ?>
                            <del><?php echo esc_html($regularPrice); ?></del>
                        <?php endif; ?>
                    </div>

                    <?php if ($durationLabel !== '') : ?>
                        <span class="ecoursity-course-card__duration"><?php echo esc_html($durationLabel); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </article>
        <?php

        return (string) ob_get_clean();
    }
}
