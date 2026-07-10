<?php

use Ecoursity\App\Models\Course;
use Ecoursity\App\Models\Instructor;

$list_courses = Course::all();
?>

<div class="ecoursity-table-list-courses tw:overflow-auto tw:bg-white tw:p-4 tw:rounded-md">
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
                $modal_body = sprintf(
                    '<div class="tw:space-y-4"><div><div class="tw:text-sm tw:text-slate-500">Instruktur</div><div class="tw:font-medium tw:text-slate-900">%s</div></div><div><div class="tw:text-sm tw:text-slate-500">Harga</div><div class="tw:font-medium tw:text-slate-900">%s</div></div><div><div class="tw:text-sm tw:text-slate-500">Durasi</div><div class="tw:font-medium tw:text-slate-900">%s</div></div><div><div class="tw:text-sm tw:text-slate-500">Deskripsi</div><div class="tw:prose tw:max-w-none tw:text-slate-700">%s</div></div></div>',
                    esc_html($instructor->display_name ?? '-'),
                    esc_html($course->price ?: '-'),
                    esc_html($course->duration ?: '-'),
                    wp_kses_post($course->excerpt ?: $course->content ?: '-')
                );
                $modal_footer = sprintf(
                    '<a href="%s" class="tw:inline-flex tw:items-center tw:rounded-md tw:bg-slate-900 tw:px-4 tw:py-2 tw:text-sm tw:font-medium tw:text-white">Edit kursus</a>',
                    esc_url(get_edit_post_link($course->id))
                );
            ?>
                <tr x-data>
                    <td class="tw:text-left tw:px-4 tw:py-2">
                        <?php echo esc_html($course->title); ?>
                    </td>
                    <td class="tw:text-left tw:px-4 tw:py-2">
                        <?php echo esc_html($instructor->display_name ?? '-'); ?>
                    </td>
                    <td class=" tw:text-right tw:px-4 tw:py-2">
                        <?php echo esc_html($course->price); ?>
                    </td>
                    <td class="tw:text-right tw:px-4 tw:py-2 tw:space-x-2">
                        <button
                            type="button"
                            class="tw:inline-flex tw:items-center tw:rounded-md tw:border tw:border-slate-300 tw:px-3 tw:py-2 tw:text-sm tw:font-medium tw:text-slate-700"
                            x-on:click='$store.EcoursityUiModal.open({ title: <?php echo wp_json_encode($course->title); ?>, body: <?php echo wp_json_encode($modal_body); ?>, footer: <?php echo wp_json_encode($modal_footer); ?> })'>
                            Detail
                        </button>
                        <a href="<?php echo esc_url(get_edit_post_link($course->id)); ?>">
                            Edit
                        </a>
                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</div>