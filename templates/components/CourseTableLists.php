<?php

use Ecoursity\App\Models\Course;
use Ecoursity\App\Models\Instructor;

$list_courses = Course::all();

$formatCurrency = static function ($value): string {
    if ($value === '' || $value === null) {
        return 'Gratis';
    }

    $numericValue = preg_replace('/[^\d.,]/', '', (string) $value);
    $normalizedValue = str_contains($numericValue, ',') && str_contains($numericValue, '.')
        ? str_replace('.', '', str_replace(',', '.', $numericValue))
        : str_replace(',', '.', $numericValue);

    if ($normalizedValue === '' || !is_numeric($normalizedValue)) {
        return (string) $value;
    }

    return 'Rp' . number_format((float) $normalizedValue, 0, ',', '.');
};
?>

<div x-data class="ecoursity-table-courses">
    <table class="ecoursity-table-courses__table">
        <thead>
            <tr class="ecoursity-table-courses__header">
                <th class="ecoursity-table-courses__th ecoursity-table-courses__th--thumb"></th>
                <th class="ecoursity-table-courses__th">Kursus</th>
                <th class="ecoursity-table-courses__th">Kategori</th>
                <th class="ecoursity-table-courses__th">Instruktur</th>
                <th class="ecoursity-table-courses__th">Harga</th>
                <th class="ecoursity-table-courses__th ecoursity-table-courses__th--actions-head">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($list_courses)): ?>
                <tr>
                    <td colspan="5" class="ecoursity-table-courses__empty">Belum ada kursus.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($list_courses as $course): ?>
                    <?php
                    $instructor = Instructor::find($course->author);
                    $thumb = $course->thumbnail();
                    $categories = wp_get_post_terms($course->id, 'ecoursity_course_category', ['fields' => 'names']);
                    $categoryLabel = !is_wp_error($categories) && !empty($categories) ? implode(', ', $categories) : '-';
                    $price = $formatCurrency($course->price);
                    $salePrice = $formatCurrency($course->price_sale);
                    $hasSalePrice = $course->price_sale !== '' && $course->price_sale !== null;
                    ?>
                    <tr class="ecoursity-table-courses__row">
                        <td class="ecoursity-table-courses__td ecoursity-table-courses__td--thumb">
                            <?php if ($thumb): ?>
                                <img src="<?php echo esc_url($thumb); ?>" alt="" class="ecoursity-table-courses__thumb">
                            <?php else: ?>
                                <span class="ecoursity-table-courses__thumb ecoursity-table-courses__thumb--empty">📖</span>
                            <?php endif; ?>
                        </td>
                        <td class="ecoursity-table-courses__td ecoursity-table-courses__td--course">
                            <div class="ecoursity-table-courses__title">
                                <a href="<?php echo esc_url(get_post_permalink($course->id)); ?>" target="_blank">
                                    <?php echo esc_html($course->title); ?>
                                </a>
                            </div>
                        </td>
                        <td class="ecoursity-table-courses__td">
                            <div class="ecoursity-table-courses__category"><?php echo esc_html($categoryLabel); ?></div>
                        </td>
                        <td class="ecoursity-table-courses__td">
                            <div class="ecoursity-table-courses__instructor"><?php echo esc_html($instructor->display_name ?? '-'); ?></div>
                        </td>
                        <td class="ecoursity-table-courses__td">
                            <div class="ecoursity-table-courses__price">
                                <?php if ($hasSalePrice): ?>
                                    <del><small style="color: #f00000;"><?php echo esc_html((string) $price); ?></small></del>
                                    <div><?php echo esc_html((string) $salePrice); ?></div>
                                <?php else: ?>
                                    <?php echo esc_html((string) $price); ?>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="ecoursity-table-courses__td ecoursity-table-courses__td--actions">
                            <div class="ecoursity-table-courses__actions">
                                <button
                                    type="button"
                                    class="course-preview__btn course-preview__btn--outline ecoursity-table-courses__btn"
                                    x-on:click='$store.EcoursityUiModal.open({ title: "Detail kursus", url:"<?php echo get_rest_url(null, 'ecoursity/v1/template_component/CoursePreview'); ?>?id=<?php echo $course->id; ?>"})'>
                                    Detail
                                </button>
                                <a
                                    href="<?php echo get_admin_url(null, 'admin.php?page=ecoursity-courses&action=edit&ecoursity_course_id=' . $course->id); ?>"
                                    class="course-preview__btn course-preview__btn--primary ecoursity-table-courses__btn">
                                    Edit
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>