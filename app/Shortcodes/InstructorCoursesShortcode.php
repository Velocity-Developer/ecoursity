<?php

/**
 * Shortcode for displaying instructor's published courses.
 *
 * @author  Velocity Developer Team
 * @package Ecoursity/Shortcodes
 * @version 1.0.0
 */

declare(strict_types=1);

namespace Ecoursity\App\Shortcodes;

use Ecoursity\App\Models\Course;

defined('ABSPATH') || exit;

class InstructorCoursesShortcode
{
    public static function render(array|string $atts = []): string
    {
        $attributes = shortcode_atts(
            ['instructor_id' => 0],
            is_array($atts) ? $atts : [],
            'ecoursity-instructor-courses'
        );

        $instructorId = absint($attributes['instructor_id']);

        if ($instructorId < 1) {
            // Fallback to current author context (e.g., author archive page)
            $authorId = get_the_author_meta('ID');
            if ($authorId) {
                $instructorId = absint($authorId);
            }
        }

        $courses = Course::all([
            'author'         => $instructorId,
            'posts_per_page' => 25,
        ]);

        ob_start();
?>
        <section class="ecoursity-instructor-courses">
            <div class="ecoursity-instructor-courses__header">
                <h2 class="ecoursity-instructor-courses__title">
                    <?php esc_html_e('Kursus oleh Instruktur Ini', 'ecoursity'); ?>
                </h2>
                <span class="ecoursity-instructor-courses__count">
                    <?php echo esc_html((string) count($courses)); ?> <?php esc_html_e('Kursus', 'ecoursity'); ?>
                </span>
            </div>

            <?php if (!empty($courses)): ?>
                <div class="ecoursity-instructor-courses__grid">
                    <?php foreach ($courses as $course): ?>
                        <?php echo do_shortcode(sprintf(
                            '[ecoursity-course-card course_id="%d"]',
                            (int) $course->id
                        )); ?>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="ecoursity-instructor-courses__empty">
                    <p><?php esc_html_e('Belum ada kursus dari instruktur ini.', 'ecoursity'); ?></p>
                </div>
            <?php endif; ?>
        </section>
<?php

        return (string) ob_get_clean();
    }
}
