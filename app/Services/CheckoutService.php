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
    private const CHECKOUT_NONCE_TRANSIENT_PREFIX = 'ecoursity_checkout_nonce_';

    public function paymentOptions(): array
    {
        $options = [];

        if ($this->bankAccounts() !== []) {
            $options[self::PAYMENT_TRANSFER_BANK] = [
                'key' => self::PAYMENT_TRANSFER_BANK,
                'label' => __('Transfer Bank', 'ecoursity'),
                'description' => __('Detail rekening akan ditampilkan setelah checkout diproses.', 'ecoursity'),
            ];
        }

        if (esc_url_raw((string) Setting::get('qris_image', '')) !== '') {
            $options[self::PAYMENT_QRIS] = [
                'key' => self::PAYMENT_QRIS,
                'label' => __('QRIS', 'ecoursity'),
                'description' => __('Gambar QRIS akan ditampilkan setelah checkout diproses.', 'ecoursity'),
            ];
        }

        return $this->sanitizePaymentOptions((array) apply_filters(
            'ecoursity_checkout_payment_options',
            $options
        ));
    }

    public function checkoutNonce(int $userId): string
    {
        if ($userId < 1) {
            return '';
        }

        $nonce = wp_generate_password(32, false, false);
        $lifetime = (int) apply_filters('ecoursity_checkout_nonce_lifetime', 30 * MINUTE_IN_SECONDS);

        set_transient(
            $this->checkoutNonceTransientKey($userId),
            wp_hash($nonce),
            max(MINUTE_IN_SECONDS, $lifetime)
        );

        return $nonce;
    }

    public function checkout(int $userId, string $payment = '', string $checkoutNonce = ''): Order
    {
        if ($userId < 1) {
            throw new InvalidArgumentException('You must login before checkout.');
        }

        $items = $this->orderItemsFromCart();

        if ($items === []) {
            throw new InvalidArgumentException('Cart is empty.');
        }

        $payment = $this->normalizePayment($payment);
        $this->verifyAndConsumeCheckoutNonce($userId, $checkoutNonce);
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
            $instructions = [
                'type' => self::PAYMENT_TRANSFER_BANK,
                'label' => __('Transfer Bank', 'ecoursity'),
                'banks' => $this->bankAccounts(),
            ];

            return (array) apply_filters(
                'ecoursity_checkout_payment_instructions',
                $instructions,
                $payment
            );
        }

        if ($payment === self::PAYMENT_QRIS) {
            $instructions = [
                'type' => self::PAYMENT_QRIS,
                'label' => __('QRIS', 'ecoursity'),
                'qris_image' => esc_url_raw((string) Setting::get('qris_image', '')),
                'qris_nmid' => sanitize_text_field((string) Setting::get('qris_nmid', '')),
            ];

            return (array) apply_filters(
                'ecoursity_checkout_payment_instructions',
                $instructions,
                $payment
            );
        }

        return (array) apply_filters(
            'ecoursity_checkout_payment_instructions',
            [],
            $payment
        );
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

    private function verifyAndConsumeCheckoutNonce(int $userId, string $nonce): void
    {
        $nonce = sanitize_text_field($nonce);
        $transientKey = $this->checkoutNonceTransientKey($userId);
        $storedHash = get_transient($transientKey);

        if (!is_string($storedHash) || $storedHash === '' || $nonce === '' || !hash_equals($storedHash, wp_hash($nonce))) {
            throw new InvalidArgumentException('Checkout session is expired. Please reload the checkout page.');
        }

        delete_transient($transientKey);
    }

    private function checkoutNonceTransientKey(int $userId): string
    {
        return self::CHECKOUT_NONCE_TRANSIENT_PREFIX . absint($userId);
    }

    private function availablePayments(): array
    {
        return array_keys($this->paymentOptions());
    }

    private function sanitizePaymentOptions(array $options): array
    {
        $sanitizedOptions = [];

        foreach ($options as $key => $option) {
            if (!is_array($option)) {
                continue;
            }

            $paymentKey = sanitize_key((string) ($option['key'] ?? $key));

            if ($paymentKey === '') {
                continue;
            }

            $label = sanitize_text_field((string) ($option['label'] ?? $paymentKey));

            if ($label === '') {
                $label = $paymentKey;
            }

            $sanitizedOptions[$paymentKey] = [
                'key' => $paymentKey,
                'label' => $label,
                'description' => sanitize_text_field((string) ($option['description'] ?? '')),
            ];
        }

        return $sanitizedOptions;
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
