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

        $authorId = self::authorId($course);
        $authorName = self::authorName($course);
        $avatar = $authorId > 0 ? get_avatar_url($authorId, ['size' => 96]) : '';
        $description = $authorId > 0 ? get_the_author_meta('description', $authorId) : '';

        ob_start();
        ?>
        <div class="ecoursity-instructor">
            <?php if ($avatar) : ?>
                <img src="<?php echo esc_url($avatar); ?>" alt="<?php echo esc_attr($authorName); ?>">
            <?php endif; ?>
            <div>
                <h3><?php echo esc_html($authorName); ?></h3>
                <p><?php echo esc_html($description ?: __('Instruktur kursus ini.', 'ecoursity')); ?></p>
            </div>
        </div>
        <?php

        return (string) ob_get_clean();
    }
}
