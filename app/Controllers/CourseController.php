<?php

namespace Ecoursity\App\Controllers;

use Ecoursity\App\Models\Course;
use Ecoursity\App\Models\Lesson;
use Ecoursity\App\Models\Section;
use Ecoursity\App\Support\CourseFormSchema;
use WP_REST_Request;
use WP_REST_Response;

class CourseController
{
    public function index(): array
    {
        return Course::all();
    }

    public function show(WP_REST_Request $request): WP_REST_Response
    {
        $course = Course::find($request->get_param('id'));

        if (! $course) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Course not found.',
            ], 404);
        }

        return new WP_REST_Response([
            'success' => true,
            'data' => $this->prepareCourseResponse($course),
        ]);
    }

    public function store(WP_REST_Request $request): WP_REST_Response
    {
        $course = new Course([
            'title' => $request->get_param('title'),
            'content' => $request->get_param('content'),
            'excerpt' => $request->get_param('excerpt'),
            'slug' => $request->get_param('slug'),
            'status' => $request->get_param('status') ?? 'draft',
            'author' => get_current_user_id(),
        ]);

        $id = $course->save();

        if (! $id) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Failed to create course.',
            ], 500);
        }

        $this->saveMeta($course, $request);
        $this->saveTaxonomies($course, $request);
        $this->saveCurriculumOrder($course, $request);

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Course created.',
            'data' => $this->prepareCourseResponse(Course::find($id)),
        ], 201);
    }

    public function update(WP_REST_Request $request): WP_REST_Response
    {
        $id = $request->get_param('id');
        $course = Course::find($id);

        if (! $course) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Course not found.',
            ], 404);
        }

        foreach (['title', 'content', 'excerpt', 'slug', 'status'] as $field) {
            if ($request->has_param($field)) {
                $course->{$field} = $request->get_param($field);
            }
        }

        $course->save();
        $this->saveMeta($course, $request);
        $this->saveTaxonomies($course, $request);
        $this->saveCurriculumOrder($course, $request);

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Course updated.',
            'data' => $this->prepareCourseResponse(Course::find($id)),
        ], 200);
    }

    public function delete(WP_REST_Request $request): WP_REST_Response
    {
        $id = $request->get_param('id');
        $course = Course::find($id);

        if (! $course) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Course not found.',
            ], 404);
        }

        $course->delete();

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Course deleted.',
        ], 200);
    }

    private function prepareCourseResponse(?Course $course): ?array
    {
        if (! $course) {
            return null;
        }

        $course->thumbnail = $course->thumbnail();
        $course->thumbnail_id = get_post_thumbnail_id($course->id);
        $course->curriculum_sections = $this->transformSections((int) $course->id);

        $data = get_object_vars($course);

        foreach (CourseFormSchema::metaFieldInputs(CourseFormSchema::sections()) as $field => $input) {
            $data[$field] = $input === 'sortable_text_list'
                ? $this->sanitizeTextList($course->meta("_ecoursity_{$field}", []))
                : $course->meta("_ecoursity_{$field}", '');
        }

        return $data;
    }

    private function transformSections(int $courseId): array
    {
        return array_map(static function (Section $section): array {
            return [
                'section_id' => (int) ($section->section_id ?? 0),
                'section_name' => (string) ($section->section_name ?? ''),
                'section_course_id' => (int) ($section->section_course_id ?? 0),
                'section_order' => (int) ($section->section_order ?? 0),
                'section_description' => (string) ($section->section_description ?? ''),
                'items' => array_values(array_map(static function (array $item): array {
                    $lesson = null;

                    if (($item['item_type'] ?? '') === 'lesson' && ! empty($item['item_id'])) {
                        $lesson = Lesson::find((int) $item['item_id']);
                    }

                    return [
                        'section_item_id' => (int) ($item['section_item_id'] ?? 0),
                        'section_id' => (int) ($item['section_id'] ?? 0),
                        'item_id' => (int) ($item['item_id'] ?? 0),
                        'item_order' => (int) ($item['item_order'] ?? 0),
                        'item_type' => (string) ($item['item_type'] ?? ''),
                        'title' => $lesson?->title ?: 'Lesson #' . (int) ($item['item_id'] ?? 0),
                        'status' => $lesson?->status ?: '',
                    ];
                }, $section->items ?? [])),
            ];
        }, Section::allByCourse($courseId));
    }

    private function saveMeta(Course $course, WP_REST_Request $request): void
    {
        foreach (CourseFormSchema::metaFieldInputs(CourseFormSchema::sections()) as $key => $input) {
            if ($request->has_param($key)) {
                $value = $this->sanitizeMetaValue($input, $request->get_param($key));

                $course->updateMeta("_ecoursity_{$key}", $value);
            }
        }

        if ($request->has_param('thumbnail_id')) {
            $course->updateMeta('_thumbnail_id', (int) $request->get_param('thumbnail_id'));
        }
    }

    private function sanitizeMetaValue(string $input, mixed $value): mixed
    {
        return match ($input) {
            'duration' => $this->sanitizeDuration($value),
            'sortable_text_list' => $this->sanitizeTextList($value),
            default => sanitize_text_field((string) $value),
        };
    }

    private function sanitizeDuration(mixed $value): array
    {
        $amount = is_array($value) && isset($value[0]) ? absint($value[0]) : 1;
        $unit = is_array($value) && isset($value[1]) ? sanitize_key((string) $value[1]) : 'week';
        $allowedUnits = ['day', 'week', 'month', 'year'];

        if (! in_array($unit, $allowedUnits, true)) {
            $unit = 'week';
        }

        if ($amount < 1) {
            $amount = 1;
        }

        return [$amount, $unit];
    }

    private function sanitizeTextList(mixed $items): array
    {
        if (! is_array($items)) {
            return [];
        }

        $items = array_map(
            static fn($item): string => sanitize_text_field((string) $item),
            $items
        );

        return array_values(array_filter(
            $items,
            static fn(string $item): bool => $item !== ''
        ));
    }

    private function saveTaxonomies(Course $course, WP_REST_Request $request): void
    {
        $category_ids = array_map('intval', (array) ($request->get_param('course_category_ids') ?? []));
        $category_ids = array_values(array_filter($category_ids));
        wp_set_object_terms($course->id, $category_ids, 'ecoursity_course_category');

        $tags = $request->get_param('course_tags');

        if ($tags === null) {
            $tags = $request->get_param('course_tags_input');
            $tags = is_string($tags) ? array_map('trim', explode(',', $tags)) : [];
        }

        $tags = array_values(array_filter(array_map('sanitize_text_field', (array) $tags)));
        wp_set_object_terms($course->id, $tags, 'ecoursity_course_tag');
    }

    private function saveCurriculumOrder(Course $course, WP_REST_Request $request): void
    {
        if (! $request->has_param('curriculum_sections')) {
            return;
        }

        $sections = $request->get_param('curriculum_sections');

        if (! is_array($sections)) {
            return;
        }

        foreach ($sections as $index => $sectionData) {
            if (! is_array($sectionData)) {
                continue;
            }

            $sectionId = absint($sectionData['section_id'] ?? 0);
            $section = Section::find($sectionId);

            if (! $section || (int) $section->section_course_id !== (int) $course->id) {
                continue;
            }

            $section->section_order = isset($sectionData['section_order'])
                ? absint($sectionData['section_order'])
                : $index;
            $section->save();

            if (isset($sectionData['items']) && is_array($sectionData['items'])) {
                $section->saveItems($sectionData['items']);
            }
        }
    }
}
