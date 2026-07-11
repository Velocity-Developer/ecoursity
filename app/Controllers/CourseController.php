<?php

namespace Ecoursity\App\Controllers;

use Ecoursity\App\Models\Course;
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
            'data'    => $course,
        ]);
    }

    public function store(WP_REST_Request $request): WP_REST_Response
    {
        $course = new Course([
            'title'   => $request->get_param('title'),
            'content' => $request->get_param('content'),
            'excerpt' => $request->get_param('excerpt'),
            'slug'    => $request->get_param('slug'),
            'status'  => $request->get_param('status') ?? 'draft',
            'author'  => get_current_user_id(),
        ]);

        $id = $course->save();

        if (! $id) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Failed to create course.',
            ], 500);
        }

        $this->saveMeta($course, $request);

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Course created.',
            'data'    => Course::find($id),
        ], 201);
    }

    public function update(WP_REST_Request $request): WP_REST_Response
    {
        $id     = $request->get_param('id');
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

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Course updated.',
            'data'    => Course::find($id),
        ], 200);
    }

    public function delete(WP_REST_Request $request): WP_REST_Response
    {
        $id     = $request->get_param('id');
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
    }
}
