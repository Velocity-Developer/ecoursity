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
            ?>
                <tr>
                    <td class="tw:text-left tw:px-4 tw:py-2">
                        <?php echo $course->title; ?>
                    </td>
                    <td class="tw:text-left tw:px-4 tw:py-2">
                        <?php echo Instructor::find($course->author)->display_name ?? '-'; ?>
                    </td>
                    <td class="tw:text-right tw:px-4 tw:py-2">
                        <?php echo $course->price; ?>
                    </td>
                    <td class="tw:text-right tw:px-4 tw:py-2">
                        <a href="<?php echo get_edit_post_link($course->id); ?>">
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