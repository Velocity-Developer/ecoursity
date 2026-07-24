<?php

declare(strict_types=1);

namespace Ecoursity\App\Shortcodes;

use Ecoursity\App\Models\Course;

defined('ABSPATH') || exit;

class CourseTabsShortcode extends CourseSingleShortcodeSupport
{
    public static function render(array|string $atts = []): string
    {
        $course = self::resolveCourse($atts, 'ecoursity-course-tabs');

        if (!$course) {
            return '';
        }

        $tabs = self::tabs($course);

        if (empty($tabs)) {
            return '';
        }

        ob_start();
        ?>
        <nav class="ecoursity-course-tabs" aria-label="<?php esc_attr_e('Navigasi kursus', 'ecoursity'); ?>">
            <?php foreach ($tabs as $key => $label) : ?>
                <button
                    type="button"
                    :class="{ 'is-active': tab === '<?php echo esc_js($key); ?>' }"
                    @click="tab = '<?php echo esc_js($key); ?>'"
                >
                    <?php echo esc_html($label); ?>
                </button>
            <?php endforeach; ?>
        </nav>
        <?php

        return (string) ob_get_clean();
    }

    private static function tabs(Course $course): array
    {
        $tabs = [
            'overview' => __('Overview', 'ecoursity'),
            'curriculum' => __('Kurikulum', 'ecoursity'),
            'instructor' => __('Instruktur', 'ecoursity'),
        ];

        if (!empty(self::faqs($course))) {
            $tabs['faq'] = __('FAQ', 'ecoursity');
        }

        return (array) apply_filters('ecoursity_course_single_tabs', $tabs, $course);
    }
}
