<?php

/**
 * Shortcode for displaying instructor hero profile.
 *
 * @author  Velocity Developer Team
 * @package Ecoursity/Shortcodes
 * @version 1.0.0
 */

declare(strict_types=1);

namespace Ecoursity\App\Shortcodes;

use Ecoursity\App\Models\Instructor;

class InstructorHeroShortcode
{
    public static function render(array|string $atts = []): string
    {
        $attributes = shortcode_atts(
            ['instructor_id' => 0],
            is_array($atts) ? $atts : [],
            'ecoursity-instructor-hero'
        );

        $instructorId = absint($attributes['instructor_id']);

        if ($instructorId < 1) {
            // Fallback to current author context (e.g., author archive page)
            $authorId = get_the_author_meta('ID');
            if ($authorId) {
                $instructorId = absint($authorId);
            }
        }

        $instructor = Instructor::find($instructorId);

        if (!$instructor) {
            return '';
        }

        $avatarUrl = get_avatar_url($instructor->id, ['size' => 160]);
        $displayName = $instructor->displayName ?: ($instructor->display_name ?: '');
        $firstName = $instructor->firstName ?: ($instructor->first_name ?: '');
        $lastName = $instructor->lastName ?: ($instructor->last_name ?: '');
        $fullName = $displayName ?: trim($firstName . ' ' . $lastName);
        $profileUrl = get_author_posts_url($instructorId);
        $bio = get_the_author_meta('description', $instructorId);

        ob_start();
?>
        <section class="ecoursity-instructor-hero">
            <div class="ecoursity-instructor-hero__inner">
                <div class="ecoursity-instructor-hero__media">
                    <?php if ($avatarUrl): ?>
                        <img src="<?php echo esc_url($avatarUrl); ?>"
                            alt="<?php echo esc_attr($fullName); ?>"
                            class="ecoursity-instructor-hero__avatar">
                    <?php else: ?>
                        <div class="ecoursity-instructor-hero__avatar-fallback">
                            <span class="ecoursity-instructor-hero__avatar-initial">
                                <?php echo esc_html(mb_substr($fullName, 0, 1) ?: '?'); ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="ecoursity-instructor-hero__content">
                    <h1 class="ecoursity-instructor-hero__name">
                        <?php echo esc_html($fullName ?: __('Instruktur', 'ecoursity')); ?>
                    </h1>
                    <div class="ecoursity-instructor-hero__stats">
                        <span class="ecoursity-instructor-hero__stat">
                            <span class="ecoursity-instructor-hero__stat-value">
                                <?php echo esc_html((string) Instructor::countCourses($instructorId)); ?>
                            </span>
                            <span class="ecoursity-instructor-hero__stat-label">
                                <?php esc_html_e('Kursus', 'ecoursity'); ?>
                            </span>
                        </span>
                        <span class="ecoursity-instructor-hero__stat">
                            <span class="ecoursity-instructor-hero__stat-value">
                                <?php echo esc_html((string) Instructor::countStudents($instructorId)); ?>
                            </span>
                            <span class="ecoursity-instructor-hero__stat-label">
                                <?php esc_html_e('Siswa', 'ecoursity'); ?>
                            </span>
                        </span>
                    </div>
                </div>

                <div class="ecoursity-instructor-hero__bio">
                    <?php if ($bio): ?>
                        <p class="ecoursity-instructor-hero__bio-text">
                            <?php echo wp_kses_post(wpautop($bio)); ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div class="ecoursity-instructor-hero__actions">
                    <a href="<?php echo esc_url($profileUrl); ?>"
                        class="ecoursity-instructor-hero__link">
                        <?php esc_html_e('Lihat Profil', 'ecoursity'); ?>
                    </a>
                </div>
            </div>
        </section>
<?php

        return (string) ob_get_clean();
    }
}
