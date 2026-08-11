<?php

/**
 * Template for displaying the public user profile dashboard.
 *
 * @author  Velocity Developer Team
 * @package Ecoursity/Template
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

use Ecoursity\App\Models\Course;
use Ecoursity\App\Models\Instructor;
use Ecoursity\App\Models\Order;

$currentUser = wp_get_current_user();
$isLoggedIn = $currentUser instanceof WP_User && $currentUser->exists();
$loginUrl = wp_login_url(get_permalink());
$registerUrl = wp_registration_url();
$coursesUrl = get_post_type_archive_link(Course::POST_TYPE) ?: home_url('/');

if (!$isLoggedIn) :
?>
    <section class="ecoursity-profile ecoursity-profile--guest">
        <div class="ecoursity-profile__auth">
            <p class="ecoursity-profile__eyebrow"><?php esc_html_e('Dashboard Ecoursity', 'ecoursity'); ?></p>
            <h2><?php esc_html_e('Masuk untuk melihat dashboard belajar Anda.', 'ecoursity'); ?></h2>
            <p><?php esc_html_e('Dashboard ini disiapkan untuk siswa dan instruktur agar aktivitas kursus bisa dikelola dari halaman publik.', 'ecoursity'); ?></p>
            <div class="ecoursity-profile__actions">
                <a class="ecoursity-profile__button" href="<?php echo esc_url($loginUrl); ?>">
                    <?php esc_html_e('Login', 'ecoursity'); ?>
                </a>
                <a class="ecoursity-profile__button ecoursity-profile__button--secondary" href="<?php echo esc_url($registerUrl); ?>">
                    <?php esc_html_e('Daftar', 'ecoursity'); ?>
                </a>
            </div>
        </div>
    </section>
<?php
    return;
endif;

$roles = (array) $currentUser->roles;
$isInstructor = in_array(Instructor::ROLE, $roles, true);
$isStudent = in_array('ecoursity_student', $roles, true);
$roleLabel = $isInstructor ? __('Instruktur', 'ecoursity') : ($isStudent ? __('Siswa', 'ecoursity') : __('Member', 'ecoursity'));
$displayName = $currentUser->display_name ?: $currentUser->user_login;
$avatar = get_avatar_url((int) $currentUser->ID, ['size' => 144]);
$logoutUrl = wp_logout_url(home_url('/'));
$registeredDate = mysql2date(get_option('date_format'), (string) $currentUser->user_registered);
$studentOrders = [];
$studentCourseCount = 0;
$instructorCourseCount = 0;
$instructorStudentCount = 0;

if ($isInstructor) {
    $instructorCourseCount = Instructor::countCourses((int) $currentUser->ID);
    $instructorStudentCount = Instructor::countStudents((int) $currentUser->ID);
}

if ($isStudent) {
    $studentOrders = Order::all([
        'posts_per_page' => -1,
        'meta_query' => [
            [
                'key' => Order::META_ORDER_USER,
                'value' => (int) $currentUser->ID,
                'compare' => '=',
            ],
        ],
    ]);

    $completedCourseIds = [];

    foreach ($studentOrders as $order) {
        if ($order->order_status !== Order::STATUS_COMPLETED) {
            continue;
        }

        foreach ($order->order_items as $item) {
            $completedCourseIds[] = absint($item['id'] ?? 0);
        }
    }

    $studentCourseCount = count(array_unique(array_filter($completedCourseIds)));
}

$stats = $isInstructor
    ? [
        ['label' => __('Kursus', 'ecoursity'), 'value' => $instructorCourseCount],
        ['label' => __('Siswa', 'ecoursity'), 'value' => $instructorStudentCount],
        ['label' => __('Status', 'ecoursity'), 'value' => __('Aktif', 'ecoursity')],
    ]
    : [
        ['label' => __('Kursus Aktif', 'ecoursity'), 'value' => $studentCourseCount],
        ['label' => __('Pesanan', 'ecoursity'), 'value' => count($studentOrders)],
        ['label' => __('Status', 'ecoursity'), 'value' => __('Aktif', 'ecoursity')],
    ];
?>

<section class="ecoursity-profile">
    <div class="ecoursity-profile__hero">
        <div class="ecoursity-profile__identity">
            <img class="ecoursity-profile__avatar" src="<?php echo esc_url($avatar); ?>" alt="<?php echo esc_attr($displayName); ?>">
            <div>
                <p class="ecoursity-profile__eyebrow"><?php echo esc_html($roleLabel); ?></p>
                <h1><?php echo esc_html($displayName); ?></h1>
                <p><?php echo esc_html($currentUser->user_email); ?></p>
            </div>
        </div>

        <div class="ecoursity-profile__actions">
            <a class="ecoursity-profile__button" href="<?php echo esc_url($coursesUrl); ?>">
                <?php esc_html_e('Lihat Kursus', 'ecoursity'); ?>
            </a>
            <a class="ecoursity-profile__button ecoursity-profile__button--secondary" href="<?php echo esc_url($logoutUrl); ?>">
                <?php esc_html_e('Logout', 'ecoursity'); ?>
            </a>
        </div>
    </div>

    <div class="ecoursity-profile__stats">
        <?php foreach ($stats as $stat) : ?>
            <div class="ecoursity-profile__stat">
                <span><?php echo esc_html($stat['label']); ?></span>
                <strong><?php echo esc_html((string) $stat['value']); ?></strong>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="ecoursity-profile__layout">
        <main class="ecoursity-profile__main">
            <?php if ($isInstructor) : ?>
                <section class="ecoursity-profile__panel">
                    <div class="ecoursity-profile__panel-heading">
                        <h2><?php esc_html_e('Kursus Saya', 'ecoursity'); ?></h2>
                        <span><?php echo esc_html((string) $instructorCourseCount); ?> <?php esc_html_e('kursus', 'ecoursity'); ?></span>
                    </div>
                    <?php echo do_shortcode(sprintf('[ecoursity-instructor-courses instructor_id="%d"]', (int) $currentUser->ID)); ?>
                </section>
            <?php else : ?>
                <section class="ecoursity-profile__panel">
                    <div class="ecoursity-profile__panel-heading">
                        <h2><?php esc_html_e('Kursus Saya', 'ecoursity'); ?></h2>
                        <span><?php echo esc_html((string) $studentCourseCount); ?> <?php esc_html_e('kursus aktif', 'ecoursity'); ?></span>
                    </div>
                    <div class="ecoursity-profile__empty">
                        <p><?php esc_html_e('Daftar kursus siswa akan tampil di sini setelah data enrollment/progress tersedia.', 'ecoursity'); ?></p>
                        <a href="<?php echo esc_url($coursesUrl); ?>"><?php esc_html_e('Lihat Katalog Kursus', 'ecoursity'); ?></a>
                    </div>
                </section>
            <?php endif; ?>
        </main>

        <aside class="ecoursity-profile__sidebar">
            <section class="ecoursity-profile__panel">
                <h2><?php esc_html_e('Informasi Akun', 'ecoursity'); ?></h2>
                <dl class="ecoursity-profile__details">
                    <div>
                        <dt><?php esc_html_e('Username', 'ecoursity'); ?></dt>
                        <dd><?php echo esc_html($currentUser->user_login); ?></dd>
                    </div>
                    <div>
                        <dt><?php esc_html_e('Bergabung', 'ecoursity'); ?></dt>
                        <dd><?php echo esc_html($registeredDate); ?></dd>
                    </div>
                    <div>
                        <dt><?php esc_html_e('Role', 'ecoursity'); ?></dt>
                        <dd><?php echo esc_html($roleLabel); ?></dd>
                    </div>
                </dl>
            </section>
        </aside>
    </div>
</section>
