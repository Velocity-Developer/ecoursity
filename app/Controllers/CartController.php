<?php

declare(strict_types=1);

namespace Ecoursity\App\Controllers;

use Ecoursity\App\Models\Cart;
use Ecoursity\App\Models\Course;
use WP_REST_Request;
use WP_REST_Response;

defined('ABSPATH') || exit;

class CartController
{
    public function index(): WP_REST_Response
    {
        return new WP_REST_Response([
            'success' => true,
            'data' => $this->cartResponse(),
        ]);
    }

    public function store(WP_REST_Request $request): WP_REST_Response
    {
        $courseId = $this->courseIdFromRequest($request);

        if ($courseId < 1 || !Cart::add($courseId)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Course not found.',
                'errors' => [
                    'course_id' => 'Invalid course ID.',
                ],
            ], 404);
        }

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Course added to cart.',
            'data' => $this->cartResponse(),
        ], 201);
    }

    public function replace(WP_REST_Request $request): WP_REST_Response
    {
        $courseIds = $request->get_param('course_ids');

        if (!is_array($courseIds)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Invalid cart payload.',
                'errors' => [
                    'course_ids' => 'Course IDs must be an array.',
                ],
            ], 422);
        }

        Cart::replace($courseIds);

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Cart updated.',
            'data' => $this->cartResponse(),
        ]);
    }

    public function delete(WP_REST_Request $request): WP_REST_Response
    {
        $courseId = absint($request->get_param('course_id'));

        if ($courseId < 1) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Invalid course ID.',
                'errors' => [
                    'course_id' => 'Course ID is required.',
                ],
            ], 422);
        }

        Cart::remove($courseId);

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Course removed from cart.',
            'data' => $this->cartResponse(),
        ]);
    }

    public function clear(): WP_REST_Response
    {
        Cart::clear();

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Cart cleared.',
            'data' => $this->cartResponse(),
        ]);
    }

    private function cartResponse(): array
    {
        $items = Cart::all();

        return [
            'items' => $items,
            'courses' => array_values(array_filter(array_map(
                fn(int $courseId): ?array => $this->courseResponse(Course::find($courseId)),
                $items
            ))),
            'count' => count($items),
        ];
    }

    private function courseResponse(?Course $course): ?array
    {
        if (!$course || !$course->id) {
            return null;
        }

        return [
            'id' => (int) $course->id,
            'title' => (string) $course->title,
            'slug' => (string) $course->slug,
            'price' => (string) $course->price,
            'price_sale' => (string) $course->price_sale,
            'thumbnail' => $course->thumbnail(),
            'permalink' => (string) get_permalink((int) $course->id),
        ];
    }

    private function courseIdFromRequest(WP_REST_Request $request): int
    {
        $courseId = $request->get_param('course_id');

        if ($courseId === null) {
            $courseId = $request->get_param('id');
        }

        return absint($courseId);
    }
}
