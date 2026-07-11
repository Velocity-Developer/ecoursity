<?php

use Ecoursity\App\Models\Course;
use Ecoursity\App\Models\Instructor;

$list_courses = Course::all();
?>

<div x-data class="ecoursity-table-courses">
    <table class="ecoursity-table-courses__table">
        <thead>
            <tr class="ecoursity-table-courses__header">
                <th class="ecoursity-table-courses__th ecoursity-table-courses__th--thumb"></th>
                <th class="ecoursity-table-courses__th">Kursus</th>
                <th class="ecoursity-table-courses__th">Instruktur</th>
                <th class="ecoursity-table-courses__th ecoursity-table-courses__th--right">Harga</th>
                <th class="ecoursity-table-courses__th ecoursity-table-courses__th--right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($list_courses as $course) {
                $instructor = Instructor::find($course->author);
            ?>
                <tr class="ecoursity-table-courses__row">
                    <td class="ecoursity-table-courses__td ecoursity-table-courses__td--thumb">
                        <?php $thumb = $course->thumbnail(); ?>
                        <?php if ($thumb): ?>
                            <img src="<?php echo esc_url($thumb); ?>" alt="" class="ecoursity-table-courses__thumb">
                        <?php else: ?>
                            <span class="ecoursity-table-courses__thumb ecoursity-table-courses__thumb--empty">📖</span>
                        <?php endif; ?>
                    </td>
                    <td class="ecoursity-table-courses__td"><?php echo esc_html($course->title); ?></td>
                    <td class="ecoursity-table-courses__td"><?php echo esc_html($instructor->display_name ?? '-'); ?></td>
                    <td class="ecoursity-table-courses__td ecoursity-table-courses__td--right"><?php echo esc_html($course->price); ?></td>
                    <td class="ecoursity-table-courses__td ecoursity-table-courses__td--right ecoursity-table-courses__td--actions">
                        <button
                            type="button"
                            class="course-preview__btn course-preview__btn--outline ecoursity-table-courses__btn"
                            x-on:click='$store.EcoursityUiModal.open({ title: "Detail kursus", url:"<?php echo get_rest_url(null, 'ecoursity/v1/template_component/CoursePreview'); ?>?id=<?php echo $course->id; ?>"})'>
                            Detail
                        </button>
                        <button
                            type="button"
                            class="course-preview__btn course-preview__btn--primary ecoursity-table-courses__btn"
                            x-on:click='$store.EcoursityUiModal.open({ title: "Edit kursus", url:"<?php echo get_rest_url(null, 'ecoursity/v1/template_component/CoursePreview'); ?>?id=<?php echo $course->id; ?>"})'>
                            Edit
                        </button>
                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</div>