<?php

declare(strict_types=1);

namespace Ecoursity\App\Shortcodes;

defined('ABSPATH') || exit;

class CourseHeroShortcode extends CourseSingleShortcodeSupport
{
    public static function render(array|string $atts = []): string
    {
        $course = self::resolveCourse($atts, 'ecoursity-course-hero');

        if (!$course) {
            return '';
        }

        $categoryLabel = self::categoryLabel($course);
        $authorName = self::authorName($course);
        $avatar = get_avatar_url((int) $course->author, ['size' => 96]);
        $lessonCount = self::lessonCount($course);

        ob_start();
        ?>
        <section class="ecoursity-course-hero">
            <div class="ecoursity-course-hero__inner">
                <div class="ecoursity-course-hero__content">
                    <div class="ecoursity-course-hero__breadcrumb">
                        <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Beranda', 'ecoursity'); ?></a>
                        <span>/</span>
                        <span><?php echo esc_html($categoryLabel); ?></span>
                    </div>

                    <p class="ecoursity-course-hero__category"><?php echo esc_html($categoryLabel); ?></p>
                    <h1 class="ecoursity-course-hero__title"><?php echo esc_html(get_the_title((int) $course->id)); ?></h1>

                    <?php if ($course->excerpt !== '') : ?>
                        <p class="ecoursity-course-hero__excerpt"><?php echo esc_html($course->excerpt); ?></p>
                    <?php endif; ?>

                    <div class="ecoursity-course-hero__meta">
                        <div class="ecoursity-course-hero__instructor">
                            <?php if ($avatar) : ?>
                                <img src="<?php echo esc_url($avatar); ?>" alt="<?php echo esc_attr($authorName); ?>">
                            <?php endif; ?>
                            <span><?php echo esc_html(sprintf(__('Oleh %s', 'ecoursity'), $authorName)); ?></span>
                        </div>
                        <span><?php echo esc_html(self::formatLevel($course->level)); ?></span>
                        <span><?php echo esc_html(self::formatDuration($course->duration)); ?></span>
                        <span><?php echo esc_html(self::formatLessonCount($lessonCount)); ?></span>
                    </div>
                </div>
            </div>
        </section>
        <?php

        return (string) ob_get_clean();
    }
}
