<?php
$ecoursity_course_id = $_GET['ecoursity_course_id'] ?? '';
$action = $_GET['action'] ?? '';
?>

<div class="ecoursity-admin-layout">

    <?php if ($action == 'edit' && $ecoursity_course_id): ?>
        <div class="ecoursity-admin-page__header">
            <h1 class="ecoursity-admin-page__title">Edit Kursus</h1>
        </div>

        <a
            href="<?php echo get_admin_url(null, 'admin.php?page=ecoursity-courses'); ?>"
            class="course-preview__btn course-preview__btn--primary ecoursity-table-courses__btn">
            Daftar Kursus
        </a>

        <?php
        $props = [
            'course_id' => $ecoursity_course_id,
        ];
        Ecoursity\App\Template::component('CourseForm', compact('props'));
        ?>

    <?php else: ?>

        <div class="ecoursity-admin-page__header">
            <h1 class="ecoursity-admin-page__title">Kursus</h1>
            <p class="ecoursity-admin-page__desc">Daftar semua kursus.</p>
        </div>

        <?php Ecoursity\App\Template::component('CourseTableLists'); ?>

    <?php endif; ?>


</div>