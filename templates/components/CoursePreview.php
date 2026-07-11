<?php

use Ecoursity\App\Models\Course;

$props = $props ?? [];
$id_course = $props['id'] ?? null;
$course = Course::find((int) $id_course);
$thumbnail = $course?->thumbnail() ?: '';
$courseId = $course?->id;
$title = $course?->title ?? '';
$slug = $course?->slug ?? '';
$status = $course?->status ?? '';
$price = $course?->price ?? '';
$priceSale = $course?->price_sale ?? '';
$duration = $course?->duration ?? '';
$level = $course?->level ?? '';
$excerpt = $course?->excerpt ?? '';
$content = $course?->content ?? '';
?>

<div class="course-preview tw:overflow-hidden tw:rounded-[12px] tw:bg-[#fbfbf5]">
    <div class="tw:grid tw:grid-cols-1 lg:tw:grid-cols-[1.4fr_0.9fr]">
        <div class="tw:bg-[#fbfbf5] tw:px-12 tw:py-12 tw:text-black sm:tw:px-8 lg:tw:px-12">
            <div class="tw:mb-6 tw:flex tw:flex-wrap tw:items-center tw:gap-3">
                <span class="tw:inline-flex tw:items-center tw:rounded-full tw:bg-zinc-200 tw:px-3 tw:py-1 tw:text-xs tw:font-normal tw:uppercase tw:tracking-[0.72px] tw:text-black">
                    <?php echo $courseId !== null ? 'Course #' . esc_html((string) $courseId) : 'Course Preview'; ?>
                </span>
                <span class="tw:inline-flex tw:items-center tw:rounded-full tw:bg-[#c1fbd4] tw:px-3 tw:py-1 tw:text-xs tw:font-normal tw:uppercase tw:tracking-[0.72px] tw:text-black">
                    <?php echo !empty($level) ? esc_html((string) $level) : 'All Levels'; ?>
                </span>
                <span class="tw:inline-flex tw:items-center tw:rounded-full tw:bg-zinc-200 tw:px-3 tw:py-1 tw:text-xs tw:font-normal tw:uppercase tw:tracking-[0.72px] tw:text-black">
                    <?php echo !empty($duration) ? esc_html((string) $duration) : 'Self-paced'; ?>
                </span>
            </div>

            <h1 class="tw:max-w-4xl tw:text-[55px] tw:font-[330] tw:leading-[1.16] sm:tw:text-[48px]">
                <?php echo $title !== '' ? esc_html((string) $title) : 'Course Preview'; ?>
            </h1>

            <p class="tw:mt-4 tw:max-w-3xl tw:text-lg tw:font-semibold tw:leading-[1.56] tw:text-black/60">
                <?php echo !empty($excerpt) ? esc_html((string) $excerpt) : 'Build practical skills with structured lessons, guided materials, and focused outcomes.'; ?>
            </p>

            <div class="tw:mt-8 tw:flex tw:flex-wrap tw:items-center tw:gap-x-8 tw:gap-y-3 tw:text-sm tw:text-black/50">
                <span class="tw:flex tw:items-center tw:gap-2">
                    <span class="tw:text-black/30">★★★★★</span>
                    <span>Top course preview</span>
                </span>
                <span>Updated for modern learners</span>
                <span><?php echo $status !== '' ? esc_html(ucfirst((string) $status)) : 'Draft'; ?></span>
            </div>
        </div>

        <div class="tw:bg-[#fbfbf5] tw:p-5 sm:tw:p-6 lg:tw:-mb-20 lg:tw:translate-y-8 lg:tw:pr-8">
            <div class="tw:overflow-hidden tw:rounded-[12px] tw:border tw:border-zinc-200 tw:bg-white"
                style="box-shadow: 0 8px 8px rgba(0,0,0,0.1), 0 4px 4px rgba(0,0,0,0.1), 0 2px 2px rgba(0,0,0,0.1), 0 0 0 1px rgba(0,0,0,0.1);">
                <div class="tw:aspect-video tw:bg-zinc-100">
                    <?php if ($thumbnail !== ''): ?>
                        <img
                            src="<?php echo esc_url($thumbnail); ?>"
                            alt="<?php echo $title !== '' ? esc_attr((string) $title) : 'Course thumbnail'; ?>"
                            class="tw:h-full tw:w-full tw:object-cover">
                    <?php else: ?>
                        <div class="tw:flex tw:h-full tw:w-full tw:items-center tw:justify-center tw:bg-white">
                            <div class="tw:p-6 tw:text-center tw:text-black">
                                <p class="tw:text-xs tw:font-normal tw:uppercase tw:tracking-[0.72px] tw:text-black/30">Course Preview</p>
                                <p class="tw:mt-3 tw:text-2xl tw:font-[330] tw:leading-tight">
                                    <?php echo $title !== '' ? esc_html((string) $title) : 'Start learning today'; ?>
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="tw:space-y-6 tw:p-8">
                    <div class="tw:flex tw:items-end tw:gap-3">
                        <?php if ($priceSale !== ''): ?>
                            <span class="tw:text-5xl tw:font-[330] tw:text-black"><?php echo esc_html((string) $priceSale); ?></span>
                            <span class="tw:text-lg tw:text-black/30 tw:line-through"><?php echo esc_html((string) $price); ?></span>
                        <?php else: ?>
                            <span class="tw:text-5xl tw:font-[330] tw:text-black"><?php echo $price !== '' ? esc_html((string) $price) : 'Free'; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="tw:grid tw:grid-cols-1 tw:gap-3">
                        <button type="button" class="tw:w-full tw:rounded-full tw:bg-black tw:px-6 tw:py-3 tw:text-base tw:font-medium tw:text-white hover:tw:bg-zinc-700">
                            Enroll now
                        </button>
                        <button type="button" class="tw:w-full tw:rounded-full tw:border tw:border-black tw:bg-white tw:px-6 tw:py-3 tw:text-base tw:font-medium tw:text-black hover:tw:bg-zinc-50">
                            Add to wishlist
                        </button>
                    </div>

                    <div class="tw:space-y-3 tw:border-t tw:border-zinc-200 tw:pt-6">
                        <h3 class="tw:text-sm tw:font-medium tw:uppercase tw:tracking-[0.72px] tw:text-black/50">This course includes</h3>
                        <ul class="tw:space-y-2 tw:text-sm tw:text-black/50">
                            <li class="tw:flex tw:items-start tw:justify-between tw:gap-3">
                                <span>Duration</span>
                                <span class="tw:font-medium tw:text-black"><?php echo $duration !== '' ? esc_html((string) $duration) : 'Self-paced'; ?></span>
                            </li>
                            <li class="tw:flex tw:items-start tw:justify-between tw:gap-3">
                                <span>Level</span>
                                <span class="tw:font-medium tw:text-black"><?php echo $level !== '' ? esc_html((string) $level) : 'All Levels'; ?></span>
                            </li>
                            <li class="tw:flex tw:items-start tw:justify-between tw:gap-3">
                                <span>Status</span>
                                <span class="tw:font-medium tw:text-black"><?php echo $status !== '' ? esc_html(ucfirst((string) $status)) : 'Draft'; ?></span>
                            </li>
                            <li class="tw:flex tw:items-start tw:justify-between tw:gap-3">
                                <span>Course ID</span>
                                <span class="tw:font-medium tw:text-black"><?php echo $courseId !== null ? esc_html((string) $courseId) : 'N/A'; ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tw:px-12 tw:py-12 sm:tw:px-8 lg:tw:px-12">
        <div class="tw:grid tw:grid-cols-1 tw:gap-8 xl:tw:grid-cols-[1.2fr_0.8fr]">
            <div class="tw:space-y-8">
                <section class="tw:rounded-[12px] tw:border tw:border-zinc-200 tw:bg-white tw:p-8">
                    <h2 class="tw:text-[28px] tw:font-medium tw:leading-[1.28] tw:tracking-[0.42px] tw:text-black">About this course</h2>
                    <div class="tw:mt-4 tw:max-w-none tw:text-base tw:leading-[1.56] tw:text-black/60">
                        <?php echo $content !== '' ? wp_kses_post(wpautop($content)) : '<p>No course content available yet.</p>'; ?>
                    </div>
                </section>
            </div>

            <aside class="tw:space-y-6">
                <section class="tw:rounded-[12px] tw:border tw:border-zinc-200 tw:bg-white tw:p-8">
                    <h2 class="tw:text-[20px] tw:font-medium tw:leading-[1.4] tw:tracking-[0.3px] tw:text-black">Quick facts</h2>
                    <dl class="tw:mt-4 tw:space-y-4">
                        <div class="tw:flex tw:items-start tw:justify-between tw:gap-4">
                            <dt class="tw:text-sm tw:text-black/40">Title</dt>
                            <dd class="tw:text-right tw:text-sm tw:font-medium tw:text-black"><?php echo $title !== '' ? esc_html((string) $title) : 'Course Preview'; ?></dd>
                        </div>
                        <div class="tw:flex tw:items-start tw:justify-between tw:gap-4">
                            <dt class="tw:text-sm tw:text-black/40">Slug</dt>
                            <dd class="tw:text-right tw:text-sm tw:font-medium tw:text-black"><?php echo $slug !== '' ? esc_html((string) $slug) : 'N/A'; ?></dd>
                        </div>
                        <div class="tw:flex tw:items-start tw:justify-between tw:gap-4">
                            <dt class="tw:text-sm tw:text-black/40">Regular price</dt>
                            <dd class="tw:text-right tw:text-sm tw:font-medium tw:text-black"><?php echo $price !== '' ? esc_html((string) $price) : 'Free'; ?></dd>
                        </div>
                        <div class="tw:flex tw:items-start tw:justify-between tw:gap-4">
                            <dt class="tw:text-sm tw:text-black/40">Sale price</dt>
                            <dd class="tw:text-right tw:text-sm tw:font-medium tw:text-black"><?php echo $priceSale !== '' ? esc_html((string) $priceSale) : '—'; ?></dd>
                        </div>
                    </dl>
                </section>
            </aside>
        </div>
    </div>
</div>