<?php

use Ecoursity\App\Models\Course;
use Ecoursity\App\Models\Instructor;

$list_courses = Course::all();
?>

<div x-data class="ecoursity-table-list-courses tw:overflow-auto tw:bg-white tw:p-4 tw:rounded-md">
    <table class="tw:table tw:table-fixed tw:table-stripped tw:table-zebra tw:w-full">
        <thead>
            <tr class="tw:bg-gray-100">
                <th class="tw:text-left tw:px-4 tw:py-2">
                    Kursus
                </th>
                <th class="tw:text-left tw:px-4 tw:py-2">
                    Instruktur
                </th>
                <th class="tw:text-right tw:px-4 tw:py-2">
                    Harga
                </th>
                <th class="tw:text-right tw:px-4 tw:py-2">
                    Aksi
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($list_courses as $course) {
                $instructor = Instructor::find($course->author);
            ?>
                <tr>
                    <td class="tw:text-left tw:px-4 tw:py-2">
                        <?php echo esc_html($course->title); ?>
                    </td>
                    <td class="tw:text-left tw:px-4 tw:py-2">
                        <?php echo esc_html($instructor->display_name ?? '-'); ?>
                    </td>
                    <td class=" tw:text-right tw:px-4 tw:py-2">
                        <?php echo esc_html($course->price); ?>
                    </td>
                    <td class="tw:text-right tw:px-4 tw:py-2 tw:space-x-1">
                        <button
                            type="button"
                            class="tw:inline-flex tw:items-center tw:rounded-md tw:border tw:border-slate-300 tw:px-3 tw:py-2 tw:text-sm tw:font-medium tw:text-slate-700 tw:bg-white tw:hover:bg-slate-50"
                            x-on:click='$store.EcoursityUiModal.open({ title: <?php echo wp_json_encode($course->title); ?>, url:"<?php echo get_rest_url(null, 'ecoursity/v1/template_component/CoursePreview'); ?>?id=<?php echo $course->id; ?>"})'>
                            Detail
                        </button>
                        <button
                            type="button"
                            class="tw:inline-flex tw:items-center tw:rounded-md tw:border tw:border-slate-300 tw:px-3 tw:py-2 tw:text-sm tw:font-medium tw:text-slate-700 tw:bg-white tw:hover:bg-slate-50"
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