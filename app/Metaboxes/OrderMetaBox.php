<?php

namespace Ecoursity\App\Metaboxes;

use Ecoursity\App\Models\Order;
use WP_Post;

defined('ABSPATH') || exit;

class OrderMetaBox
{
    public const NONCE_ACTION = 'ecoursity_order_meta';
    public const NONCE_NAME = 'ecoursity_order_meta_nonce';
    public const FIELD_NAME = 'ecoursity_order_meta';

    public function render(WP_Post $post): void
    {
        $order = Order::find($post->ID);

        if (!$order) {
            $order = new Order(['id' => $post->ID]);
        }

        wp_nonce_field(self::NONCE_ACTION, self::NONCE_NAME);

        $methodOptions = [
            Order::METHOD_MANUALLY => __('Manually'),
            Order::METHOD_CHECKOUT => __('Checkout'),
        ];
        $statusOptions = [
            Order::STATUS_COMPLETED => __('Completed'),
            Order::STATUS_PENDING => __('Pending'),
            Order::STATUS_PROCESSING => __('Processing'),
            Order::STATUS_CANCELLED => __('Cancelled'),
            Order::STATUS_REFUNDED => __('Refunded'),
            Order::STATUS_FAILED => __('Failed'),
        ];
        $users = get_users([
            'fields' => ['ID', 'display_name', 'user_email'],
            'orderby' => 'display_name',
            'order' => 'ASC',
        ]);

        echo '<table class="form-table" role="presentation"><tbody>';
        $this->renderReadonlyTextField(Order::META_ORDER_NUMBER, __('Order Number'), $order->order_number);
        $this->renderReadonlyTextField(Order::META_ORDER_DATE, __('Order Date'), $order->order_date);
        $this->renderReadonlyTextField(Order::META_ORDER_METHOD, __('Order Method'), $methodOptions[$order->order_method] ?? $order->order_method);

        echo '<tr>';
        echo '<th scope="row"><label for="' . esc_attr(Order::META_ORDER_STATUS) . '">' . esc_html__('Order Status') . '</label></th>';
        echo '<td><select id="' . esc_attr(Order::META_ORDER_STATUS) . '" name="' . esc_attr(self::FIELD_NAME) . '[' . esc_attr(Order::META_ORDER_STATUS) . ']">';

        foreach ($statusOptions as $value => $label) {
            echo '<option value="' . esc_attr($value) . '" ' . selected($order->order_status, $value, false) . '>' . esc_html($label) . '</option>';
        }

        echo '</select></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th scope="row"><label for="' . esc_attr(Order::META_ORDER_USER) . '">' . esc_html__('Order User') . '</label></th>';
        echo '<td><select id="' . esc_attr(Order::META_ORDER_USER) . '" name="' . esc_attr(self::FIELD_NAME) . '[' . esc_attr(Order::META_ORDER_USER) . ']">';
        echo '<option value="0">' . esc_html__('Pilih User') . '</option>';

        foreach ($users as $user) {
            $label = sprintf('%s (%s)', $user->display_name, $user->user_email);
            echo '<option value="' . esc_attr((string) $user->ID) . '" ' . selected($order->order_user, (int) $user->ID, false) . '>' . esc_html($label) . '</option>';
        }

        echo '</select></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th scope="row"><label for="' . esc_attr(Order::META_ORDER_PAYMENT) . '">' . esc_html__('Order Payment') . '</label></th>';
        echo '<td><input type="text" class="regular-text" id="' . esc_attr(Order::META_ORDER_PAYMENT) . '" name="' . esc_attr(self::FIELD_NAME) . '[' . esc_attr(Order::META_ORDER_PAYMENT) . ']" value="' . esc_attr($order->order_payment) . '"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th scope="row"><label for="' . esc_attr(Order::META_ORDER_ITEMS) . '">' . esc_html__('Order Items') . '</label></th>';
        echo '<td>';
        echo '<textarea class="large-text code" rows="8" id="' . esc_attr(Order::META_ORDER_ITEMS) . '" name="' . esc_attr(self::FIELD_NAME) . '[' . esc_attr(Order::META_ORDER_ITEMS) . ']">' . esc_textarea($this->encodeItems($order->order_items)) . '</textarea>';
        echo '<p class="description">' . esc_html__('Isi dengan JSON array. Setiap item: id, name, price, price_sale, price_real.') . '</p>';
        echo '</td>';
        echo '</tr>';

        $this->renderAmountField(Order::META_ORDER_SUBTOTAL, __('Subtotal'), $order->order_subtotal);
        $this->renderAmountField(Order::META_ORDER_TOTAL, __('Total'), $order->order_total);
        echo '</tbody></table>';
    }

    public function save(int $postId, WP_Post $post): void
    {
        if (!$this->canSave($postId, $post)) {
            return;
        }

        $order = Order::find($postId);

        if (!$order || !isset($_POST[self::FIELD_NAME]) || !is_array($_POST[self::FIELD_NAME])) {
            return;
        }

        $submittedMeta = wp_unslash($_POST[self::FIELD_NAME]);
        $this->ensureGeneratedMeta($order);
        $order->updateMeta(Order::META_ORDER_STATUS, $this->sanitizeStatus($submittedMeta[Order::META_ORDER_STATUS] ?? ''));
        $order->updateMeta(Order::META_ORDER_USER, $this->sanitizeUser($submittedMeta[Order::META_ORDER_USER] ?? 0));
        $order->updateMeta(Order::META_ORDER_PAYMENT, sanitize_text_field((string) ($submittedMeta[Order::META_ORDER_PAYMENT] ?? '')));
        $order->updateMeta(Order::META_ORDER_ITEMS, $this->sanitizeItems($submittedMeta[Order::META_ORDER_ITEMS] ?? []));
        $order->updateMeta(Order::META_ORDER_SUBTOTAL, $this->sanitizeAmount($submittedMeta[Order::META_ORDER_SUBTOTAL] ?? 0));
        $order->updateMeta(Order::META_ORDER_TOTAL, $this->sanitizeAmount($submittedMeta[Order::META_ORDER_TOTAL] ?? 0));
    }

    private function canSave(int $postId, WP_Post $post): bool
    {
        if (!isset($_POST[self::NONCE_NAME]) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST[self::NONCE_NAME])), self::NONCE_ACTION)) {
            return false;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return false;
        }

        return $post->post_type === Order::POST_TYPE && current_user_can('edit_post', $postId);
    }

    private function ensureGeneratedMeta(Order $order): void
    {
        if ($order->order_number === '') {
            $order->order_number = sprintf(
                'ECO-%s-%s',
                date_i18n('YmdHis'),
                strtoupper(wp_generate_password(6, false, false))
            );
            $order->updateMeta(Order::META_ORDER_NUMBER, $order->order_number);
        }

        if ($order->order_date === '') {
            $order->order_date = date_i18n('YmdHis');
            $order->updateMeta(Order::META_ORDER_DATE, $order->order_date);
        }

        if ($order->order_method === '') {
            $order->order_method = Order::METHOD_MANUALLY;
            $order->updateMeta(Order::META_ORDER_METHOD, $order->order_method);
        }
    }

    private function renderReadonlyTextField(string $id, string $label, string $value): void
    {
        echo '<tr>';
        echo '<th scope="row"><label for="' . esc_attr($id) . '">' . esc_html($label) . '</label></th>';
        echo '<td><input type="text" class="regular-text" id="' . esc_attr($id) . '" value="' . esc_attr($value) . '" readonly></td>';
        echo '</tr>';
    }

    private function renderAmountField(string $id, string $label, float $value): void
    {
        echo '<tr>';
        echo '<th scope="row"><label for="' . esc_attr($id) . '">' . esc_html($label) . '</label></th>';
        echo '<td><input type="number" min="0" step="0.01" class="regular-text" id="' . esc_attr($id) . '" name="' . esc_attr(self::FIELD_NAME) . '[' . esc_attr($id) . ']" value="' . esc_attr((string) $value) . '"></td>';
        echo '</tr>';
    }

    private function encodeItems(array $items): string
    {
        if ($items === []) {
            return '';
        }

        return (string) wp_json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    private function sanitizeStatus(mixed $value): string
    {
        $status = sanitize_key((string) $value);
        $allowed = [
            Order::STATUS_COMPLETED,
            Order::STATUS_PENDING,
            Order::STATUS_PROCESSING,
            Order::STATUS_CANCELLED,
            Order::STATUS_REFUNDED,
            Order::STATUS_FAILED,
        ];

        return in_array($status, $allowed, true) ? $status : Order::STATUS_PENDING;
    }

    private function sanitizeUser(mixed $value): int
    {
        $userId = absint($value);

        return $userId > 0 && get_user_by('id', $userId) ? $userId : 0;
    }

    private function sanitizeItems(mixed $value): array
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($value)) {
            return [];
        }

        return array_values(array_filter(array_map(
            static function (mixed $item): array {
                if (!is_array($item)) {
                    return [];
                }

                $courseId = absint($item['id'] ?? 0);

                if ($courseId < 1) {
                    return [];
                }

                return [
                    'id' => $courseId,
                    'name' => sanitize_text_field((string) ($item['name'] ?? '')),
                    'price' => max(0, (float) ($item['price'] ?? 0)),
                    'price_sale' => max(0, (float) ($item['price_sale'] ?? 0)),
                    'price_real' => max(0, (float) ($item['price_real'] ?? 0)),
                ];
            },
            $value
        )));
    }

    private function sanitizeAmount(mixed $value): float
    {
        return max(0, (float) $value);
    }
}
