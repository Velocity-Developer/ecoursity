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

<div class="course-preview tw:overflow-hidden tw:rounded-3xl tw:border tw:border-slate-200 tw:bg-white tw:shadow-xl">
    <div class="tw:grid tw:grid-cols-1 lg:tw:grid-cols-[1.4fr_0.9fr]">
        <div class="tw:bg-[#1c1d1f] tw:px-6 tw:py-8 tw:text-white sm:tw:px-8 lg:tw:px-10 lg:tw:py-10">
            <div class="tw:mb-4 tw:flex tw:flex-wrap tw:items-center tw:gap-3">
                <span class="tw:inline-flex tw:items-center tw:rounded-full tw:bg-white/10 tw:px-3 tw:py-1 tw:text-xs tw:font-bold tw:uppercase tw:tracking-[0.18em] tw:text-violet-200">
                    <?php echo $courseId !== null ? 'Course #' . esc_html((string) $courseId) : 'Course Preview'; ?>
                </span>
                <span class="tw:inline-flex tw:items-center tw:rounded-full tw:bg-amber-400/15 tw:px-3 tw:py-1 tw:text-xs tw:font-semibold tw:text-amber-300">
                    <?php echo !empty($level) ? esc_html((string) $level) : 'All Levels'; ?>
                </span>
                <span class="tw:inline-flex tw:items-center tw:rounded-full tw:bg-emerald-400/15 tw:px-3 tw:py-1 tw:text-xs tw:font-semibold tw:text-emerald-300">
                    <?php echo !empty($duration) ? esc_html((string) $duration) : 'Self-paced'; ?>
                </span>
            </div>

            <h1 class="tw:max-w-4xl tw:text-3xl tw:font-bold tw:leading-tight sm:tw:text-4xl">
                <?php echo $title !== '' ? esc_html((string) $title) : 'Course Preview'; ?>
            </h1>

            <p class="tw:mt-4 tw:max-w-3xl tw:text-base tw:leading-7 tw:text-slate-300 sm:tw:text-lg">
                <?php echo !empty($excerpt) ? esc_html((string) $excerpt) : 'Build practical skills with structured lessons, guided materials, and focused outcomes.'; ?>
            </p>

            <div class="tw:mt-6 tw:flex tw:flex-wrap tw:items-center tw:gap-x-6 tw:gap-y-3 tw:text-sm tw:text-slate-300">
                <span class="tw:flex tw:items-center tw:gap-2">
                    <span class="tw:text-amber-400">★★★★★</span>
                    <span>Top course preview</span>
                </span>
                <span>Updated for modern learners</span>
                <span><?php echo $status !== '' ? esc_html(ucfirst((string) $status)) : 'Draft'; ?></span>
            </div>
        </div>

        <div class="tw:bg-slate-50 tw:p-5 sm:tw:p-6 lg:tw:-mb-20 lg:tw:translate-y-8 lg:tw:pr-8">
            <div class="tw:overflow-hidden tw:rounded-2xl tw:border tw:border-slate-200 tw:bg-white tw:shadow-2xl">
                <div class="tw:aspect-video tw:bg-slate-200">
                    <?php if ($thumbnail !== ''): ?>
                        <img
                            src="<?php echo esc_url($thumbnail); ?>"
                            alt="<?php echo $title !== '' ? esc_attr((string) $title) : 'Course thumbnail'; ?>"
                            class="tw:h-full tw:w-full tw:object-cover">
                    <?php else: ?>
                        <div class="tw:flex tw:h-full tw:w-full tw:items-center tw:justify-center tw:bg-gradient-to-br tw:from-violet-600 tw:to-indigo-700 tw:p-6 tw:text-center tw:text-white">
                            <div>
                                <p class="tw:text-xs tw:font-bold tw:uppercase tw:tracking-[0.2em] tw:text-violet-100">Course Preview</p>
                                <p class="tw:mt-3 tw:text-2xl tw:font-bold tw:leading-tight">
                                    <?php echo $title !== '' ? esc_html((string) $title) : 'Start learning today'; ?>
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="tw:space-y-5 tw:p-6">
                    <div class="tw:flex tw:items-end tw:gap-3">
                        <?php if ($priceSale !== ''): ?>
                            <span class="tw:text-4xl tw:font-bold tw:text-slate-900"><?php echo esc_html((string) $priceSale); ?></span>
                            <span class="tw:text-lg tw:text-slate-400 tw:line-through"><?php echo esc_html((string) $price); ?></span>
                        <?php else: ?>
                            <span class="tw:text-4xl tw:font-bold tw:text-slate-900"><?php echo $price !== '' ? esc_html((string) $price) : 'Free'; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="tw:grid tw:grid-cols-1 tw:gap-3">
                        <button type="button" class="tw:w-full tw:rounded-xl tw:bg-[#a435f0] tw:px-5 tw:py-3.5 tw:text-sm tw:font-bold tw:text-white hover:tw:bg-[#8710d8]">
                            Enroll now
                        </button>
                        <button type="button" class="tw:w-full tw:rounded-xl tw:border tw:border-slate-300 tw:bg-white tw:px-5 tw:py-3.5 tw:text-sm tw:font-bold tw:text-slate-900 hover:tw:bg-slate-50">
                            Add to wishlist
                        </button>
                    </div>

                    <div class="tw:space-y-3 tw:border-t tw:border-slate-200 tw:pt-5">
                        <h3 class="tw:text-sm tw:font-bold tw:text-slate-900">This course includes</h3>
                        <ul class="tw:space-y-2 tw:text-sm tw:text-slate-600">
                            <li class="tw:flex tw:items-start tw:justify-between tw:gap-3">
                                <span>Duration</span>
                                <span class="tw:font-medium tw:text-slate-900"><?php echo $duration !== '' ? esc_html((string) $duration) : 'Self-paced'; ?></span>
                            </li>
                            <li class="tw:flex tw:items-start tw:justify-between tw:gap-3">
                                <span>Level</span>
                                <span class="tw:font-medium tw:text-slate-900"><?php echo $level !== '' ? esc_html((string) $level) : 'All Levels'; ?></span>
                            </li>
                            <li class="tw:flex tw:items-start tw:justify-between tw:gap-3">
                                <span>Status</span>
                                <span class="tw:font-medium tw:text-slate-900"><?php echo $status !== '' ? esc_html(ucfirst((string) $status)) : 'Draft'; ?></span>
                            </li>
                            <li class="tw:flex tw:items-start tw:justify-between tw:gap-3">
                                <span>Course ID</span>
                                <span class="tw:font-medium tw:text-slate-900"><?php echo $courseId !== null ? esc_html((string) $courseId) : 'N/A'; ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tw:px-6 tw:py-8 sm:tw:px-8 lg:tw:px-10 lg:tw:pt-16">
        <div class="tw:grid tw:grid-cols-1 tw:gap-8 xl:tw:grid-cols-[1.2fr_0.8fr]">
            <div class="tw:space-y-8">
                <section class="tw:rounded-2xl tw:border tw:border-slate-200 tw:bg-white tw:p-6">
                    <h2 class="tw:text-2xl tw:font-bold tw:text-slate-900">About this course</h2>
                    <div class="tw:prose tw:prose-slate tw:mt-4 tw:max-w-none tw:text-sm sm:tw:text-base">
                        <?php echo $content !== '' ? wp_kses_post(wpautop($content)) : '<p>No course content available yet.</p>'; ?>
                    </div>
                </section>
            </div>

            <aside class="tw:space-y-6">
                <section class="tw:rounded-2xl tw:border tw:border-slate-200 tw:bg-slate-50 tw:p-6">
                    <h2 class="tw:text-lg tw:font-bold tw:text-slate-900">Quick facts</h2>
                    <dl class="tw:mt-4 tw:space-y-4">
                        <div class="tw:flex tw:items-start tw:justify-between tw:gap-4">
                            <dt class="tw:text-sm tw:text-slate-500">Title</dt>
                            <dd class="tw:text-right tw:text-sm tw:font-medium tw:text-slate-900"><?php echo $title !== '' ? esc_html((string) $title) : 'Course Preview'; ?></dd>
                        </div>
                        <div class="tw:flex tw:items-start tw:justify-between tw:gap-4">
                            <dt class="tw:text-sm tw:text-slate-500">Slug</dt>
                            <dd class="tw:text-right tw:text-sm tw:font-medium tw:text-slate-900"><?php echo $slug !== '' ? esc_html((string) $slug) : 'N/A'; ?></dd>
                        </div>
                        <div class="tw:flex tw:items-start tw:justify-between tw:gap-4">
                            <dt class="tw:text-sm tw:text-slate-500">Regular price</dt>
                            <dd class="tw:text-right tw:text-sm tw:font-medium tw:text-slate-900"><?php echo $price !== '' ? esc_html((string) $price) : 'Free'; ?></dd>
                        </div>
                        <div class="tw:flex tw:items-start tw:justify-between tw:gap-4">
                            <dt class="tw:text-sm tw:text-slate-500">Sale price</dt>
                            <dd class="tw:text-right tw:text-sm tw:font-medium tw:text-slate-900"><?php echo $priceSale !== '' ? esc_html((string) $priceSale) : '—'; ?></dd>
                        </div>
                    </dl>
                </section>
            </aside>
        </div>
    </div>
</div>