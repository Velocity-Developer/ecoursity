<?php

namespace Ecoursity\App\Controllers;

use Ecoursity\App\Models\Lesson;
use WP_REST_Request;
use WP_REST_Response;

class LessonController
{
    public function index(): array
    {
        return Lesson::all([
            'post_status' => ['publish', 'draft', 'pending', 'private'],
        ]);
    }

    public function show(WP_REST_Request $request): WP_REST_Response
    {
        $lesson = Lesson::find((int) $request->get_param('id'));

        if (! $lesson) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Lesson not found.',
            ], 404);
        }

        return new WP_REST_Response([
            'success' => true,
            'data' => $this->transformLesson($lesson),
        ]);
    }

    public function store(WP_REST_Request $request): WP_REST_Response
    {
        $lesson = new Lesson([
            'title' => sanitize_text_field((string) $request->get_param('title')),
            'content' => (string) $request->get_param('content'),
            'excerpt' => (string) $request->get_param('excerpt'),
            'slug' => sanitize_title((string) $request->get_param('slug')),
            'status' => 'publish',
            'author' => get_current_user_id(),
        ]);

        $id = $lesson->save();

        if (! $id || is_wp_error($id)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Failed to create lesson.',
            ], 500);
        }

        $this->saveMeta($lesson, $request);

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Lesson created.',
            'data' => $this->transformLesson(Lesson::find($id)),
        ], 201);
    }

    public function update(WP_REST_Request $request): WP_REST_Response
    {
        $id = (int) $request->get_param('id');
        $lesson = Lesson::find($id);

        if (! $lesson) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Lesson not found.',
            ], 404);
        }

        if ($request->has_param('title')) {
            $lesson->title = sanitize_text_field((string) $request->get_param('title'));
        }

        if ($request->has_param('content')) {
            $lesson->content = (string) $request->get_param('content');
        }

        if ($request->has_param('excerpt')) {
            $lesson->excerpt = (string) $request->get_param('excerpt');
        }

        if ($request->has_param('slug')) {
            $lesson->slug = sanitize_title((string) $request->get_param('slug'));
        }

        $lesson->status = 'publish';
        $lesson->save();
        $this->saveMeta($lesson, $request);

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Lesson updated.',
            'data' => $this->transformLesson(Lesson::find($id)),
        ]);
    }

    public function delete(WP_REST_Request $request): WP_REST_Response
    {
        $lesson = Lesson::find((int) $request->get_param('id'));

        if (! $lesson) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Lesson not found.',
            ], 404);
        }

        $lesson->delete();

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Lesson deleted.',
        ]);
    }

    private function saveMeta(Lesson $lesson, WP_REST_Request $request): void
    {
        if ($request->has_param('duration')) {
            $duration = $request->get_param('duration');
            $amount = is_array($duration) ? absint($duration[0] ?? 1) : absint($duration);
            $unit = is_array($duration) ? sanitize_key((string) ($duration[1] ?? 'minute')) : 'minute';

            $lesson->updateMeta('_ecoursity_duration', [$amount ?: 1, $unit ?: 'minute']);
        }

        if ($request->has_param('preview')) {
            $lesson->updateMeta('_ecoursity_preview', rest_sanitize_boolean($request->get_param('preview')));
        }

        if ($request->has_param('assigned')) {
            $lesson->updateMeta('_ecoursity_assigned', absint($request->get_param('assigned')));
        }
    }

    private function transformLesson(?Lesson $lesson): array
    {
        if (! $lesson) {
            return [];
        }

        $assignedId = (int) $lesson->assigned;

        return [
            'id' => (int) $lesson->id,
            'title' => (string) $lesson->title,
            'slug' => (string) $lesson->slug,
            'content' => (string) $lesson->content,
            'excerpt' => (string) $lesson->excerpt,
            'status' => 'publish',
            'author' => (int) $lesson->author,
            'duration' => $lesson->duration,
            'preview' => (bool) $lesson->preview,
            'assigned' => $assignedId,
            'assigned_title' => $assignedId > 0 ? (string) get_the_title($assignedId) : '',
            'permalink' => $lesson->id ? (string) get_permalink($lesson->id) : '',
            'thumbnail' => $lesson->thumbnail(),
        ];
    }
}
