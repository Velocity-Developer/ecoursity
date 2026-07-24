<?php

declare(strict_types=1);

namespace Ecoursity\App\Shortcodes;

use Ecoursity\App\Models\Lesson;
use Ecoursity\App\Models\Section;

defined('ABSPATH') || exit;

class CourseCurriculumShortcode extends CourseSingleShortcodeSupport
{
    public static function render(array|string $atts = []): string
    {
        $course = self::resolveCourse($atts, 'ecoursity-course-curriculum');

        if (!$course) {
            return '';
        }

        $sections = Section::allByCourse((int) $course->id);

        ob_start();
        ?>
        <?php if (!empty($sections)) : ?>
            <div class="ecoursity-curriculum">
                <?php foreach ($sections as $index => $section) : ?>
                    <details class="ecoursity-curriculum__section" <?php echo $index === 0 ? 'open' : ''; ?>>
                        <summary>
                            <span><?php echo esc_html($section->section_name); ?></span>
                            <small><?php echo esc_html(self::formatLessonCount(count($section->items))); ?></small>
                        </summary>

                        <?php if ($section->section_description !== '') : ?>
                            <div class="ecoursity-curriculum__description">
                                <?php echo wp_kses_post(wpautop($section->section_description)); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($section->items)) : ?>
                            <ul class="ecoursity-curriculum__lessons">
                                <?php foreach ($section->items as $item) : ?>
                                    <?php echo self::renderLesson($item); ?>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </details>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p class="ecoursity-course-empty"><?php esc_html_e('Kurikulum belum tersedia.', 'ecoursity'); ?></p>
        <?php endif; ?>
        <?php

        return (string) ob_get_clean();
    }

    private static function renderLesson(array $item): string
    {
        $lesson = Lesson::find((int) ($item['item_id'] ?? 0));

        if (!$lesson) {
            return '';
        }

        $lessonUrl = $lesson->preview ? get_permalink((int) $lesson->id) : '';

        ob_start();
        ?>
        <li>
            <div>
                <span class="ecoursity-curriculum__lesson-icon" aria-hidden="true"></span>
                <?php if ($lessonUrl) : ?>
                    <a href="<?php echo esc_url($lessonUrl); ?>"><?php echo esc_html($lesson->title); ?></a>
                <?php else : ?>
                    <span><?php echo esc_html($lesson->title); ?></span>
                <?php endif; ?>
            </div>
            <span><?php echo esc_html($lesson->preview ? __('Preview', 'ecoursity') : self::formatDuration($lesson->duration)); ?></span>
        </li>
        <?php

        return (string) ob_get_clean();
    }
}
