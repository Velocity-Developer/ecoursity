<?php

namespace Ecoursity\App\Controllers;

use Ecoursity\App\Models\Lesson;
use Ecoursity\App\Models\Section;
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
        $this->syncSectionItem($lesson, $request);

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
        $this->syncSectionItem($lesson, $request);

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

        $this->removeLessonFromSections((int) $lesson->id);
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

    private function syncSectionItem(Lesson $lesson, WP_REST_Request $request): void
    {
        $lessonId = (int) $lesson->id;
        $sectionId = absint($request->get_param('section_id'));

        if ($lessonId < 1) {
            return;
        }

        $this->removeLessonFromSections($lessonId);

        if ($sectionId < 1) {
            return;
        }

        $section = Section::find($sectionId);

        if (! $section) {
            return;
        }

        $items = $section->items;
        $items[] = [
            'item_id' => $lessonId,
            'item_order' => count($items),
            'item_type' => 'lesson',
        ];

        $section->saveItems($items);
    }

    private function removeLessonFromSections(int $lessonId): void
    {
        $assignedCourseId = (int) get_post_meta($lessonId, '_ecoursity_assigned', true);

        if ($assignedCourseId < 1) {
            return;
        }

        foreach (Section::allByCourse($assignedCourseId) as $section) {
            $items = array_values(array_filter(
                $section->items,
                static fn(array $item): bool => (int) ($item['item_id'] ?? 0) !== $lessonId
            ));

            if (count($items) === count($section->items)) {
                continue;
            }

            $normalizedItems = array_map(
                static fn(array $item, int $index): array => [
                    'item_id' => (int) ($item['item_id'] ?? 0),
                    'item_order' => $index,
                    'item_type' => (string) ($item['item_type'] ?? ''),
                ],
                $items,
                array_keys($items)
            );

            $section->saveItems($normalizedItems);
        }
    }

    private function resolveSectionId(int $lessonId, int $courseId): int
    {
        if ($lessonId < 1 || $courseId < 1) {
            return 0;
        }

        foreach (Section::allByCourse($courseId) as $section) {
            foreach ($section->items as $item) {
                if ((int) ($item['item_id'] ?? 0) === $lessonId && (string) ($item['item_type'] ?? '') === 'lesson') {
                    return (int) ($section->section_id ?? 0);
                }
            }
        }

        return 0;
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
            'section_id' => $this->resolveSectionId((int) $lesson->id, $assignedId),
            'permalink' => $lesson->id ? (string) get_permalink($lesson->id) : '',
            'thumbnail' => $lesson->thumbnail(),
        ];
    }
}
