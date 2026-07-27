<?php

declare(strict_types=1);

namespace Ecoursity\App\Controllers;

use Ecoursity\App\Models\Order;
use Ecoursity\App\Services\CheckoutService;
use InvalidArgumentException;
use RuntimeException;
use WP_REST_Request;
use WP_REST_Response;

defined('ABSPATH') || exit;

class CheckoutController
{
    public function __construct(
        private readonly CheckoutService $checkoutService = new CheckoutService()
    ) {}

    public function store(WP_REST_Request $request): WP_REST_Response
    {
        try {
            $order = $this->checkoutService->checkout(
                get_current_user_id(),
                sanitize_text_field((string) ($request->get_param('payment') ?? ''))
            );

            return new WP_REST_Response([
                'success' => true,
                'message' => 'Checkout created.',
                'data' => $this->orderResponse($order),
            ], 201);
        } catch (InvalidArgumentException $exception) {
            return new WP_REST_Response([
                'success' => false,
                'message' => $exception->getMessage(),
                'errors' => [],
            ], is_user_logged_in() ? 422 : 401);
        } catch (RuntimeException $exception) {
            return new WP_REST_Response([
                'success' => false,
                'message' => $exception->getMessage(),
                'errors' => [],
            ], 500);
        }
    }

    private function orderResponse(Order $order): array
    {
        return [
            'id' => (int) $order->id,
            'order_number' => $order->order_number,
            'order_date' => $order->order_date,
            'order_method' => $order->order_method,
            'order_status' => $order->order_status,
            'order_user' => (int) $order->order_user,
            'order_payment' => $order->order_payment,
            'order_items' => $order->order_items,
            'order_subtotal' => (float) $order->order_subtotal,
            'order_total' => (float) $order->order_total,
            'payment_instructions' => $this->checkoutService->paymentInstructions($order->order_payment),
        ];
    }
}
