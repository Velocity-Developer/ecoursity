<?php

declare(strict_types=1);

namespace Ecoursity\App\Controllers;

use Ecoursity\App\Models\Section;
use Ecoursity\App\Models\Lesson;
use WP_REST_Request;
use WP_REST_Response;

class SectionController
{
    public function store(WP_REST_Request $request): WP_REST_Response
    {
        $courseId = absint($request->get_param('course_id'));
        $title = sanitize_text_field((string) $request->get_param('title'));

        if ($courseId < 1) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Course wajib ada.',
            ], 422);
        }

        if ($title === '') {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Title section wajib diisi.',
            ], 422);
        }

        $existingSections = Section::allByCourse($courseId);

        $section = new Section([
            'section_course_id' => $courseId,
            'section_name' => $title,
            'section_order' => count($existingSections),
            'section_description' => '',
        ]);

        $sectionId = $section->save();
        $savedSection = Section::find($sectionId);

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Section berhasil dibuat.',
            'data' => $this->transformSection($savedSection),
        ], 201);
    }

    public function update(WP_REST_Request $request): WP_REST_Response
    {
        $sectionId = absint($request->get_param('id'));
        $section = Section::find($sectionId);

        if (! $section) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Section tidak ditemukan.',
            ], 404);
        }

        if ($request->has_param('title')) {
            $title = sanitize_text_field((string) $request->get_param('title'));

            if ($title === '') {
                return new WP_REST_Response([
                    'success' => false,
                    'message' => 'Title section wajib diisi.',
                ], 422);
            }

            $section->section_name = $title;
        }

        if ($request->has_param('description')) {
            $section->section_description = (string) $request->get_param('description');
        }

        if ($request->has_param('section_order')) {
            $section->section_order = absint($request->get_param('section_order'));
        }

        $section->save();

        if ($request->has_param('items')) {
            $items = $request->get_param('items');
            $section->saveItems(is_array($items) ? $items : []);
        }

        $savedSection = Section::find($sectionId);

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Section berhasil diupdate.',
            'data' => $this->transformSection($savedSection),
        ]);
    }

    private function transformSection(?Section $section): array
    {
        if (! $section) {
            return [];
        }

        return [
            'section_id' => (int) $section->section_id,
            'section_name' => (string) $section->section_name,
            'section_course_id' => (int) $section->section_course_id,
            'section_order' => (int) $section->section_order,
            'section_description' => (string) $section->section_description,
            'items' => array_map(static function (array $item): array {
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
            }, $section->items ?? []),
        ];
    }
}
