<?php

use Ecoursity\App\Models\Course;

$props = $props ?? [];
$id_course = $props['id'] ?? null;
$course = Course::find((int) $id_course);
?>

<div class="course-preview tw:min-h-40">
    <?php echo $course->id ?? 'N/A'; ?>
    <?php echo $course->title ?? 'Course Preview'; ?>
    <div class="tw:text-sm tw:text-slate-500">
        <?php echo $course->description ?? 'No description available'; ?>
    </div>
    <div class="tw:text-sm tw:text-slate-500">
        <?php echo $course->price ?? 'Free'; ?>
    </div>
    <div class="tw:text-sm tw:text-slate-500">
        <?php echo $course->created_at ?? 'N/A'; ?>
    </div>
</div>