<?php

declare(strict_types=1);

namespace Ecoursity\App\Shortcodes;

defined('ABSPATH') || exit;

class CourseFaqShortcode extends CourseSingleShortcodeSupport
{
    public static function render(array|string $atts = []): string
    {
        $course = self::resolveCourse($atts, 'ecoursity-course-faq');

        if (!$course) {
            return '';
        }

        $faqs = self::faqs($course);

        if (empty($faqs)) {
            return '';
        }

        ob_start();
        ?>
        <div class="ecoursity-faq">
            <?php foreach ($faqs as $faq) : ?>
                <details>
                    <summary><?php echo esc_html($faq['question'] ?? ''); ?></summary>
                    <div><?php echo wp_kses_post(wpautop((string) ($faq['answer'] ?? ''))); ?></div>
                </details>
            <?php endforeach; ?>
        </div>
        <?php

        return (string) ob_get_clean();
    }
}
