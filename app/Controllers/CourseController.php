<?php

namespace Ecoursity\App\Controllers;

use Ecoursity\App\Models\Course;
use Ecoursity\App\Models\Lesson;
use Ecoursity\App\Models\Section;
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

        $course->thumbnail = $course->thumbnail();
        $course->thumbnail_id = get_post_thumbnail_id($course->id);
        $course->curriculum_sections = $this->transformSections((int) $course->id);

        return new WP_REST_Response([
            'success' => true,
            'data' => $course,
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

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Course created.',
            'data' => Course::find($id),
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

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Course updated.',
            'data' => Course::find($id),
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
        $keys = [
            'duration',
            'level',
            'max_students',
            'price',
            'price_sale',
            'price_sale_start',
            'price_sale_end',
            'course_evaluation',
            'passing_grade',
        ];

        foreach ($keys as $key) {
            if ($request->has_param($key)) {
                $course->updateMeta("_ecoursity_{$key}", $request->get_param($key));
            }
        }

        if ($request->has_param('thumbnail_id')) {
            $course->updateMeta('_thumbnail_id', (int) $request->get_param('thumbnail_id'));
        }
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
}
