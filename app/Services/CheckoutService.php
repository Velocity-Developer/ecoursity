<?php

declare(strict_types=1);

namespace Ecoursity\App\Services;

use Ecoursity\App\Models\Cart;
use Ecoursity\App\Models\Course;
use Ecoursity\App\Models\Order;
use InvalidArgumentException;
use RuntimeException;

defined('ABSPATH') || exit;

class CheckoutService
{
    public function checkout(int $userId, string $payment = ''): Order
    {
        if ($userId < 1) {
            throw new InvalidArgumentException('You must login before checkout.');
        }

        $items = $this->orderItemsFromCart();

        if ($items === []) {
            throw new InvalidArgumentException('Cart is empty.');
        }

        $subtotal = $this->calculateSubtotal($items);
        $order = new Order([
            'order_method' => Order::METHOD_CHECKOUT,
            'order_status' => Order::STATUS_PENDING,
            'order_user' => $userId,
            'order_payment' => $payment,
            'order_items' => $items,
            'order_subtotal' => $subtotal,
            'order_total' => $subtotal,
            'author' => $userId,
        ]);

        if ($order->save() < 1) {
            throw new RuntimeException('Failed to create order.');
        }

        Cart::clear();

        return $order;
    }

    private function orderItemsFromCart(): array
    {
        return array_values(array_filter(array_map(
            fn(int $courseId): ?array => $this->orderItemFromCourse(Course::find($courseId)),
            Cart::all()
        )));
    }

    private function orderItemFromCourse(?Course $course): ?array
    {
        if (!$course || !$course->id || get_post_status((int) $course->id) !== 'publish') {
            return null;
        }

        $price = max(0, (float) $course->price);
        $priceSale = max(0, (float) $course->price_sale);

        return [
            'id' => (int) $course->id,
            'name' => (string) $course->title,
            'price' => $price,
            'price_sale' => $priceSale,
            'price_real' => $priceSale > 0 ? $priceSale : $price,
        ];
    }

    private function calculateSubtotal(array $items): float
    {
        return array_reduce(
            $items,
            static fn(float $subtotal, array $item): float => $subtotal + (float) $item['price_real'],
            0.0
        );
    }
}
