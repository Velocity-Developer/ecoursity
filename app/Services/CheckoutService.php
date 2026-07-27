<?php

declare(strict_types=1);

namespace Ecoursity\App\Services;

use Ecoursity\App\Models\Cart;
use Ecoursity\App\Models\Course;
use Ecoursity\App\Models\Order;
use Ecoursity\App\Models\Setting;
use InvalidArgumentException;
use RuntimeException;

defined('ABSPATH') || exit;

class CheckoutService
{
    public const PAYMENT_TRANSFER_BANK = 'transfer_bank';
    public const PAYMENT_QRIS = 'qris';

    public function checkout(int $userId, string $payment = ''): Order
    {
        if ($userId < 1) {
            throw new InvalidArgumentException('You must login before checkout.');
        }

        $items = $this->orderItemsFromCart();

        if ($items === []) {
            throw new InvalidArgumentException('Cart is empty.');
        }

        $payment = $this->normalizePayment($payment);
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

    public function paymentInstructions(string $payment): array
    {
        $payment = sanitize_key($payment);

        if ($payment === self::PAYMENT_TRANSFER_BANK) {
            return [
                'type' => self::PAYMENT_TRANSFER_BANK,
                'label' => __('Transfer Bank', 'ecoursity'),
                'banks' => $this->bankAccounts(),
            ];
        }

        if ($payment === self::PAYMENT_QRIS) {
            return [
                'type' => self::PAYMENT_QRIS,
                'label' => __('QRIS', 'ecoursity'),
                'qris_image' => esc_url_raw((string) Setting::get('qris_image', '')),
                'qris_nmid' => sanitize_text_field((string) Setting::get('qris_nmid', '')),
            ];
        }

        return [];
    }

    private function normalizePayment(string $payment): string
    {
        $payment = sanitize_key($payment);
        $availablePayments = $this->availablePayments();

        if ($payment === '') {
            $payment = (string) ($availablePayments[0] ?? '');
        }

        if (!in_array($payment, $availablePayments, true)) {
            throw new InvalidArgumentException('Payment method is unavailable.');
        }

        return $payment;
    }

    private function availablePayments(): array
    {
        $payments = [];

        if ($this->bankAccounts() !== []) {
            $payments[] = self::PAYMENT_TRANSFER_BANK;
        }

        if (esc_url_raw((string) Setting::get('qris_image', '')) !== '') {
            $payments[] = self::PAYMENT_QRIS;
        }

        return $payments;
    }

    private function bankAccounts(): array
    {
        $accounts = Setting::get('bank_transfer_accounts', []);

        if (!is_array($accounts)) {
            return [];
        }

        return array_values(array_filter(array_map(
            static function (mixed $account): array {
                if (!is_array($account)) {
                    return [];
                }

                return [
                    'bank' => sanitize_text_field((string) ($account['bank'] ?? '')),
                    'atasnama' => sanitize_text_field((string) ($account['atasnama'] ?? '')),
                    'norek' => sanitize_text_field((string) ($account['norek'] ?? '')),
                ];
            },
            $accounts
        ), static function (array $account): bool {
            return $account['bank'] !== '' || $account['atasnama'] !== '' || $account['norek'] !== '';
        }));
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
