<?php

declare(strict_types=1);

namespace Ecoursity\App\Shortcodes;

defined('ABSPATH') || exit;

class CourseSidebarShortcode extends CourseSingleShortcodeSupport
{
    public static function render(array|string $atts = []): string
    {
        $course = self::resolveCourse($atts, 'ecoursity-course-sidebar');

        if (!$course) {
            return '';
        }

        $thumbnail = $course->thumbnail();
        $saleActive = self::hasPositivePrice($course->price_sale);
        $mainPrice = $saleActive ? self::formatPrice($course->price_sale) : self::formatPrice($course->price);
        $regularPrice = $saleActive ? self::formatPrice($course->price) : '';
        $lessonCount = self::lessonCount($course);

        ob_start();
        ?>
        <div class="ecoursity-course-sidebar__box">
            <?php if ($thumbnail) : ?>
                <img class="ecoursity-course-sidebar__thumb" src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr(get_the_title((int) $course->id)); ?>">
            <?php endif; ?>

            <div class="ecoursity-course-sidebar__price">
                <strong><?php echo esc_html($mainPrice); ?></strong>
                <?php if ($regularPrice !== '') : ?>
                    <del><?php echo esc_html($regularPrice); ?></del>
                <?php endif; ?>
            </div>

            <?php
            echo do_shortcode(sprintf(
                '[ecoursity-button-buy-course course_id="%d" class="ecoursity-course-sidebar__button"]',
                (int) $course->id
            ));
            ?>

            <ul class="ecoursity-course-sidebar__meta">
                <li>
                    <span><?php esc_html_e('Durasi', 'ecoursity'); ?></span>
                    <strong><?php echo esc_html(self::formatDuration($course->duration)); ?></strong>
                </li>
                <li>
                    <span><?php esc_html_e('Level', 'ecoursity'); ?></span>
                    <strong><?php echo esc_html(self::formatLevel($course->level)); ?></strong>
                </li>
                <li>
                    <span><?php esc_html_e('Materi', 'ecoursity'); ?></span>
                    <strong><?php echo esc_html(number_format_i18n($lessonCount)); ?></strong>
                </li>
                <?php if ($course->max_students !== '') : ?>
                    <li>
                        <span><?php esc_html_e('Kapasitas', 'ecoursity'); ?></span>
                        <strong><?php echo esc_html($course->max_students); ?></strong>
                    </li>
                <?php endif; ?>
                <?php if ($course->passing_grade !== '') : ?>
                    <li>
                        <span><?php esc_html_e('Nilai Lulus', 'ecoursity'); ?></span>
                        <strong><?php echo esc_html($course->passing_grade); ?>%</strong>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
        <?php

        return (string) ob_get_clean();
    }
}
