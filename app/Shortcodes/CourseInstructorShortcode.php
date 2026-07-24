<?php

declare(strict_types=1);

namespace Ecoursity\App\Shortcodes;

defined('ABSPATH') || exit;

class CourseInstructorShortcode extends CourseSingleShortcodeSupport
{
    public static function render(array|string $atts = []): string
    {
        $course = self::resolveCourse($atts, 'ecoursity-course-instructor');

        if (!$course) {
            return '';
        }

        $authorName = self::authorName($course);
        $avatar = get_avatar_url((int) $course->author, ['size' => 96]);

        ob_start();
        ?>
        <div class="ecoursity-instructor">
            <?php if ($avatar) : ?>
                <img src="<?php echo esc_url($avatar); ?>" alt="<?php echo esc_attr($authorName); ?>">
            <?php endif; ?>
            <div>
                <h3><?php echo esc_html($authorName); ?></h3>
                <p><?php echo esc_html(get_the_author_meta('description', (int) $course->author) ?: __('Instruktur kursus ini.', 'ecoursity')); ?></p>
            </div>
        </div>
        <?php

        return (string) ob_get_clean();
    }
}
