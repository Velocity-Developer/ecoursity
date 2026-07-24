<?php

declare(strict_types=1);

namespace Ecoursity\App\Shortcodes;

defined('ABSPATH') || exit;

class CourseOverviewShortcode extends CourseSingleShortcodeSupport
{
    public static function render(array|string $atts = []): string
    {
        $course = self::resolveCourse($atts, 'ecoursity-course-overview');

        if (!$course) {
            return '';
        }

        $requirements = array_filter($course->requirements);
        $targetAudiences = array_filter($course->target_audiences);
        $keyFeatures = array_filter($course->key_features);

        ob_start();
        ?>
        <div class="ecoursity-course-content">
            <?php echo apply_filters('the_content', get_post_field('post_content', (int) $course->id)); ?>
        </div>

        <?php if (!empty($keyFeatures)) : ?>
            <div class="ecoursity-course-list-block">
                <h3><?php esc_html_e('Yang Akan Kamu Dapatkan', 'ecoursity'); ?></h3>
                <ul class="ecoursity-course-check-list">
                    <?php foreach ($keyFeatures as $feature) : ?>
                        <li><?php echo esc_html($feature); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($requirements) || !empty($targetAudiences)) : ?>
            <div class="ecoursity-course-two-col">
                <?php if (!empty($requirements)) : ?>
                    <div class="ecoursity-course-list-block">
                        <h3><?php esc_html_e('Persyaratan', 'ecoursity'); ?></h3>
                        <ul>
                            <?php foreach ($requirements as $requirement) : ?>
                                <li><?php echo esc_html($requirement); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!empty($targetAudiences)) : ?>
                    <div class="ecoursity-course-list-block">
                        <h3><?php esc_html_e('Untuk Siapa', 'ecoursity'); ?></h3>
                        <ul>
                            <?php foreach ($targetAudiences as $audience) : ?>
                                <li><?php echo esc_html($audience); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <?php

        return (string) ob_get_clean();
    }
}
