<?php

declare(strict_types=1);

namespace Ecoursity\App\Shortcodes;

defined('ABSPATH') || exit;

class CourseTabsShortcode extends CourseSingleShortcodeSupport
{
    public static function render(array|string $atts = []): string
    {
        $course = self::resolveCourse($atts, 'ecoursity-course-tabs');

        if (!$course) {
            return '';
        }

        $faqs = self::faqs($course);

        ob_start();
        ?>
        <nav class="ecoursity-course-tabs" aria-label="<?php esc_attr_e('Navigasi kursus', 'ecoursity'); ?>">
            <button type="button" :class="{ 'is-active': tab === 'overview' }" @click="tab = 'overview'">
                <?php esc_html_e('Overview', 'ecoursity'); ?>
            </button>
            <button type="button" :class="{ 'is-active': tab === 'curriculum' }" @click="tab = 'curriculum'">
                <?php esc_html_e('Kurikulum', 'ecoursity'); ?>
            </button>
            <button type="button" :class="{ 'is-active': tab === 'instructor' }" @click="tab = 'instructor'">
                <?php esc_html_e('Instruktur', 'ecoursity'); ?>
            </button>
            <?php if (!empty($faqs)) : ?>
                <button type="button" :class="{ 'is-active': tab === 'faq' }" @click="tab = 'faq'">
                    <?php esc_html_e('FAQ', 'ecoursity'); ?>
                </button>
            <?php endif; ?>
        </nav>
        <?php

        return (string) ob_get_clean();
    }
}
