<?php

/**
 * Template for displaying a single public course.
 *
 * @author  Ecoursity Team
 * @package Ecoursity/Template
 * @version 1.1.0
 */

use Ecoursity\App\Models\Course;
use Ecoursity\App\Models\Lesson;
use Ecoursity\App\Models\Section;

defined('ABSPATH') || exit;

$format_price = static function (string $price): string {
    $normalized = trim($price);

    if ($normalized === '' || (float) $normalized <= 0) {
        return __('Gratis', 'ecoursity');
    }

    return function_exists('number_format_i18n')
        ? 'Rp ' . number_format_i18n((float) $normalized)
        : 'Rp ' . number_format((float) $normalized, 0, ',', '.');
};

$format_duration = static function (mixed $duration): string {
    if (!is_array($duration) || empty($duration[0]) || empty($duration[1])) {
        return __('Akses fleksibel', 'ecoursity');
    }

    $units = [
        'day' => __('hari', 'ecoursity'),
        'week' => __('minggu', 'ecoursity'),
        'month' => __('bulan', 'ecoursity'),
        'year' => __('tahun', 'ecoursity'),
        'hour' => __('jam', 'ecoursity'),
        'minute' => __('menit', 'ecoursity'),
    ];

    $amount = absint($duration[0]);
    $unit = sanitize_key((string) $duration[1]);

    return sprintf(
        '%s %s',
        number_format_i18n($amount > 0 ? $amount : 1),
        $units[$unit] ?? $unit
    );
};

$format_level = static function (string $level): string {
    return [
        'all' => __('Semua level', 'ecoursity'),
        'beginner' => __('Pemula', 'ecoursity'),
        'intermediate' => __('Menengah', 'ecoursity'),
        'advanced' => __('Lanjutan', 'ecoursity'),
    ][$level] ?? __('Semua level', 'ecoursity');
};

$count_lessons = static function (array $sections): int {
    return array_reduce(
        $sections,
        static fn(int $total, Section $section): int => $total + count($section->items),
        0
    );
};

get_header();
?>

<?php while (have_posts()) : ?>
    <?php
    the_post();

    $course = Course::find((int) get_the_ID());

    if (!$course) {
        continue;
    }

    $sections = Section::allByCourse((int) $course->id);
    $lesson_count = $count_lessons($sections);
    $thumbnail = $course->thumbnail();
    $categories = wp_get_post_terms((int) $course->id, 'ecoursity_course_category', ['fields' => 'names']);
    $category_label = !is_wp_error($categories) && !empty($categories)
        ? implode(', ', array_map('sanitize_text_field', $categories))
        : __('Kursus Online', 'ecoursity');
    $author = get_userdata((int) $course->author);
    $author_name = $author ? $author->display_name : get_the_author();
    $avatar = get_avatar_url((int) $course->author, ['size' => 96]);
    $sale_active = $course->price_sale !== '' && (float) $course->price_sale > 0;
    $main_price = $sale_active ? $format_price($course->price_sale) : $format_price($course->price);
    $regular_price = $sale_active ? $format_price($course->price) : '';
    $requirements = array_filter($course->requirements);
    $target_audiences = array_filter($course->target_audiences);
    $key_features = array_filter($course->key_features);
    $faqs = array_filter($course->faqs, static fn(array $faq): bool => !empty($faq['question']) || !empty($faq['answer']));
    $enroll_url = is_user_logged_in() ? '#ecoursity-course-curriculum' : wp_login_url(get_permalink((int) $course->id));
    $enroll_label = is_user_logged_in() ? __('Mulai Belajar', 'ecoursity') : __('Login untuk Belajar', 'ecoursity');
    ?>

    <main class="ecoursity-single-course" x-data="{ tab: 'overview' }">
        <section class="ecoursity-course-hero">
            <div class="ecoursity-course-hero__inner">
                <div class="ecoursity-course-hero__content">
                    <div class="ecoursity-course-hero__breadcrumb">
                        <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Beranda', 'ecoursity'); ?></a>
                        <span>/</span>
                        <span><?php echo esc_html($category_label); ?></span>
                    </div>

                    <p class="ecoursity-course-hero__category"><?php echo esc_html($category_label); ?></p>
                    <h1 class="ecoursity-course-hero__title"><?php the_title(); ?></h1>

                    <?php if ($course->excerpt !== '') : ?>
                        <p class="ecoursity-course-hero__excerpt"><?php echo esc_html($course->excerpt); ?></p>
                    <?php endif; ?>

                    <div class="ecoursity-course-hero__meta">
                        <div class="ecoursity-course-hero__instructor">
                            <?php if ($avatar) : ?>
                                <img src="<?php echo esc_url($avatar); ?>" alt="<?php echo esc_attr($author_name); ?>">
                            <?php endif; ?>
                            <span><?php echo esc_html(sprintf(__('Oleh %s', 'ecoursity'), $author_name)); ?></span>
                        </div>
                        <span><?php echo esc_html($format_level($course->level)); ?></span>
                        <span><?php echo esc_html($format_duration($course->duration)); ?></span>
                        <span><?php echo esc_html(sprintf(_n('%s materi', '%s materi', $lesson_count, 'ecoursity'), number_format_i18n($lesson_count))); ?></span>
                    </div>
                </div>
            </div>
        </section>

        <div class="ecoursity-course-layout">
            <div class="ecoursity-course-layout__main">
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

                <section class="ecoursity-course-panel" x-show="tab === 'overview'">
                    <h2><?php esc_html_e('Tentang Kursus Ini', 'ecoursity'); ?></h2>
                    <div class="ecoursity-course-content">
                        <?php the_content(); ?>
                    </div>

                    <?php if (!empty($key_features)) : ?>
                        <div class="ecoursity-course-list-block">
                            <h3><?php esc_html_e('Yang Akan Kamu Dapatkan', 'ecoursity'); ?></h3>
                            <ul class="ecoursity-course-check-list">
                                <?php foreach ($key_features as $feature) : ?>
                                    <li><?php echo esc_html($feature); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($requirements) || !empty($target_audiences)) : ?>
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

                            <?php if (!empty($target_audiences)) : ?>
                                <div class="ecoursity-course-list-block">
                                    <h3><?php esc_html_e('Untuk Siapa', 'ecoursity'); ?></h3>
                                    <ul>
                                        <?php foreach ($target_audiences as $audience) : ?>
                                            <li><?php echo esc_html($audience); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </section>

                <section id="ecoursity-course-curriculum" class="ecoursity-course-panel" x-show="tab === 'curriculum'" x-cloak>
                    <div class="ecoursity-course-panel__heading">
                        <h2><?php esc_html_e('Kurikulum Kursus', 'ecoursity'); ?></h2>
                        <span><?php echo esc_html(sprintf(_n('%s materi', '%s materi', $lesson_count, 'ecoursity'), number_format_i18n($lesson_count))); ?></span>
                    </div>

                    <?php if (!empty($sections)) : ?>
                        <div class="ecoursity-curriculum">
                            <?php foreach ($sections as $index => $section) : ?>
                                <details class="ecoursity-curriculum__section" <?php echo $index === 0 ? 'open' : ''; ?>>
                                    <summary>
                                        <span><?php echo esc_html($section->section_name); ?></span>
                                        <small><?php echo esc_html(sprintf(_n('%s materi', '%s materi', count($section->items), 'ecoursity'), number_format_i18n(count($section->items)))); ?></small>
                                    </summary>

                                    <?php if ($section->section_description !== '') : ?>
                                        <div class="ecoursity-curriculum__description">
                                            <?php echo wp_kses_post(wpautop($section->section_description)); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($section->items)) : ?>
                                        <ul class="ecoursity-curriculum__lessons">
                                            <?php foreach ($section->items as $item) : ?>
                                                <?php
                                                $lesson = Lesson::find((int) ($item['item_id'] ?? 0));

                                                if (!$lesson) {
                                                    continue;
                                                }

                                                $lesson_url = $lesson->preview ? get_permalink((int) $lesson->id) : '';
                                                ?>
                                                <li>
                                                    <div>
                                                        <span class="ecoursity-curriculum__lesson-icon" aria-hidden="true"></span>
                                                        <?php if ($lesson_url) : ?>
                                                            <a href="<?php echo esc_url($lesson_url); ?>"><?php echo esc_html($lesson->title); ?></a>
                                                        <?php else : ?>
                                                            <span><?php echo esc_html($lesson->title); ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <span><?php echo esc_html($lesson->preview ? __('Preview', 'ecoursity') : $format_duration($lesson->duration)); ?></span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </details>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <p class="ecoursity-course-empty"><?php esc_html_e('Kurikulum belum tersedia.', 'ecoursity'); ?></p>
                    <?php endif; ?>
                </section>

                <section class="ecoursity-course-panel" x-show="tab === 'instructor'" x-cloak>
                    <h2><?php esc_html_e('Tentang Instruktur', 'ecoursity'); ?></h2>
                    <div class="ecoursity-instructor">
                        <?php if ($avatar) : ?>
                            <img src="<?php echo esc_url($avatar); ?>" alt="<?php echo esc_attr($author_name); ?>">
                        <?php endif; ?>
                        <div>
                            <h3><?php echo esc_html($author_name); ?></h3>
                            <p><?php echo esc_html(get_the_author_meta('description', (int) $course->author) ?: __('Instruktur kursus ini.', 'ecoursity')); ?></p>
                        </div>
                    </div>
                </section>

                <?php if (!empty($faqs)) : ?>
                    <section class="ecoursity-course-panel" x-show="tab === 'faq'" x-cloak>
                        <h2><?php esc_html_e('Pertanyaan Umum', 'ecoursity'); ?></h2>
                        <div class="ecoursity-faq">
                            <?php foreach ($faqs as $faq) : ?>
                                <details>
                                    <summary><?php echo esc_html($faq['question'] ?? ''); ?></summary>
                                    <div><?php echo wp_kses_post(wpautop((string) ($faq['answer'] ?? ''))); ?></div>
                                </details>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>
            </div>

            <aside class="ecoursity-course-sidebar">
                <div class="ecoursity-course-sidebar__box">
                    <?php if ($thumbnail) : ?>
                        <img class="ecoursity-course-sidebar__thumb" src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                    <?php endif; ?>

                    <div class="ecoursity-course-sidebar__price">
                        <strong><?php echo esc_html($main_price); ?></strong>
                        <?php if ($regular_price !== '') : ?>
                            <del><?php echo esc_html($regular_price); ?></del>
                        <?php endif; ?>
                    </div>

                    <a class="ecoursity-course-sidebar__button" href="<?php echo esc_url($enroll_url); ?>" <?php echo is_user_logged_in() ? '@click="tab = \'curriculum\'"' : ''; ?>>
                        <?php echo esc_html($enroll_label); ?>
                    </a>

                    <ul class="ecoursity-course-sidebar__meta">
                        <li>
                            <span><?php esc_html_e('Durasi', 'ecoursity'); ?></span>
                            <strong><?php echo esc_html($format_duration($course->duration)); ?></strong>
                        </li>
                        <li>
                            <span><?php esc_html_e('Level', 'ecoursity'); ?></span>
                            <strong><?php echo esc_html($format_level($course->level)); ?></strong>
                        </li>
                        <li>
                            <span><?php esc_html_e('Materi', 'ecoursity'); ?></span>
                            <strong><?php echo esc_html(number_format_i18n($lesson_count)); ?></strong>
                        </li>
                        <?php if ($course->max_students !== '') : ?>
                            <li>
                                <span><?php esc_html_e('Kapasitas', 'ecoursity'); ?></span>
                                <strong><?php echo esc_html($course->max_students); ?></strong>
                            </li>
                        <?php endif; ?>
                        <?php if ($course->passing_grade !== '') : ?>
                            <li>
                                <span><?php esc_html_e('Nilai Lulus', 'ecoursity'); ?></span>
                                <strong><?php echo esc_html($course->passing_grade); ?>%</strong>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </aside>
        </div>
    </main>
<?php endwhile; ?>

<?php
get_footer();
