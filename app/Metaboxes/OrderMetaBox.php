<?php

namespace Ecoursity\App\Metaboxes;

use Ecoursity\App\Helpers\Str;
use Ecoursity\App\Models\Order;
use WP_Post;

defined('ABSPATH') || exit;

class OrderMetaBox
{
    public const NONCE_ACTION = 'ecoursity_order_meta';
    public const NONCE_NAME = 'ecoursity_order_meta_nonce';
    public const FIELD_NAME = 'ecoursity_order_meta';

    public function renderDetails(WP_Post $post): void
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
        $this->renderOrderDateField($order);
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

        echo '</tbody></table>';
    }

    public function renderItems(WP_Post $post): void
    {
        $order = Order::find($post->ID);

        if (!$order) {
            $order = new Order(['id' => $post->ID]);
        }

        wp_nonce_field(self::NONCE_ACTION, self::NONCE_NAME);

        $editable = $order->order_method === '' || $order->order_method === Order::METHOD_MANUALLY;
        $payload = [
            'editable' => $editable,
            'items' => $order->order_items,
            'coursesUrl' => esc_url_raw(rest_url('ecoursity/v1/order-course-options/')),
            'restNonce' => wp_create_nonce('wp_rest'),
        ];

        echo '<div class="ecoursity-order-items" x-data="ecoursityOrderItems(' . esc_attr(wp_json_encode($payload)) . ')">';
        echo '<input type="hidden" name="' . esc_attr(self::FIELD_NAME) . '[' . esc_attr(Order::META_ORDER_ITEMS) . ']" x-bind:value="itemsJson">';
        echo '<input type="hidden" name="' . esc_attr(self::FIELD_NAME) . '[' . esc_attr(Order::META_ORDER_SUBTOTAL) . ']" x-bind:value="subtotal">';
        echo '<input type="hidden" name="' . esc_attr(self::FIELD_NAME) . '[' . esc_attr(Order::META_ORDER_TOTAL) . ']" x-bind:value="total">';

        echo '<div class="ecoursity-order-items__toolbar">';
        echo '<p class="description">' . esc_html($editable ? __('Order manual dapat diedit.') : __('Order dari checkout tidak dapat diedit.')) . '</p>';
        echo '<button type="button" class="button button-primary" x-show="editable" x-on:click="openModal()">' . esc_html__('Tambah Item') . '</button>';
        echo '</div>';

        echo '<table class="widefat striped ecoursity-order-items__table">';
        echo '<thead><tr>';
        echo '<th>' . esc_html__('Course') . '</th>';
        echo '<th style="width:120px;">' . esc_html__('Price') . '</th>';
        echo '<th style="width:120px;">' . esc_html__('Sale') . '</th>';
        echo '<th style="width:120px;">' . esc_html__('Real') . '</th>';
        echo '<th style="width:80px;" x-show="editable">' . esc_html__('Action') . '</th>';
        echo '</tr></thead>';
        echo '<tbody>';
        echo '<template x-if="items.length === 0"><tr><td colspan="5">' . esc_html__('Belum ada item.') . '</td></tr></template>';
        echo '<template x-for="item in items" x-bind:key="item.id">';
        echo '<tr>';
        echo '<td><strong x-text="item.name"></strong><div class="row-actions">ID: <span x-text="item.id"></span></div></td>';
        echo '<td x-text="money(item.price)"></td>';
        echo '<td x-text="money(item.price_sale)"></td>';
        echo '<td x-text="money(item.price_real)"></td>';
        echo '<td x-show="editable"><button type="button" class="button-link-delete" x-on:click="removeItem(item.id)">' . esc_html__('Hapus') . '</button></td>';
        echo '</tr>';
        echo '</template>';
        echo '</tbody>';
        echo '<tfoot>';
        echo '<tr><th colspan="3" style="text-align:right;">' . esc_html__('Subtotal') . '</th><th colspan="2" x-text="money(subtotal)"></th></tr>';
        echo '<tr><th colspan="3" style="text-align:right;">' . esc_html__('Total') . '</th><th colspan="2" x-text="money(total)"></th></tr>';
        echo '</tfoot>';
        echo '</table>';

        echo '<div class="ecoursity-order-items__modal" x-cloak x-show="modalOpen" x-on:keydown.escape.window="closeModal()">';
        echo '<div class="ecoursity-order-items__backdrop" x-on:click="closeModal()"></div>';
        echo '<div class="ecoursity-order-items__dialog" role="dialog" aria-modal="true">';
        echo '<div class="ecoursity-order-items__dialog-header">';
        echo '<h3>' . esc_html__('Pilih Course') . '</h3>';
        echo '<button type="button" class="button" x-on:click="closeModal()">' . esc_html__('Tutup') . '</button>';
        echo '</div>';
        echo '<input type="search" class="widefat" placeholder="' . esc_attr__('Cari course') . '" x-model="search">';
        echo '<div class="ecoursity-order-items__course-list">';
        echo '<p class="description" x-show="coursesLoading">' . esc_html__('Memuat course...') . '</p>';
        echo '<p class="description" x-show="coursesError" x-text="coursesError"></p>';
        echo '<template x-if="!coursesLoading && !coursesError && filteredCourses().length === 0"><p class="description">' . esc_html__('Course tidak ditemukan.') . '</p></template>';
        echo '<template x-for="course in filteredCourses()" x-bind:key="course.id">';
        echo '<div class="ecoursity-order-items__course">';
        echo '<div><strong x-text="course.name"></strong><div class="description" x-text="money(course.price_real)"></div></div>';
        echo '<button type="button" class="button" x-bind:disabled="hasItem(course.id)" x-on:click="addItem(course)" x-text="hasItem(course.id) ? \'' . esc_js(__('Sudah Ditambahkan')) . '\' : \'' . esc_js(__('Tambahkan')) . '\'"></button>';
        echo '</div>';
        echo '</template>';
        echo '</div>';
        echo '</div>';
        echo '</div>';

        $this->renderOrderItemsScript();
        $this->renderOrderItemsStyle();
        echo '</div>';
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

        if ($order->order_method === Order::METHOD_MANUALLY) {
            $order->updateMeta(Order::META_ORDER_DATE, $this->sanitizeOrderDate($submittedMeta[Order::META_ORDER_DATE] ?? $order->order_date, $order->order_date));

            $items = $this->sanitizeItems($submittedMeta[Order::META_ORDER_ITEMS] ?? []);
            $subtotal = $this->calculateSubtotal($items);

            $order->updateMeta(Order::META_ORDER_ITEMS, $items);
            $order->updateMeta(Order::META_ORDER_SUBTOTAL, $subtotal);
            $order->updateMeta(Order::META_ORDER_TOTAL, $subtotal);
        }
    }

    public function ensureGeneratedMetaForPost(int $postId, WP_Post $post): void
    {
        if ($post->post_type !== Order::POST_TYPE) {
            return;
        }

        if (wp_is_post_autosave($postId) || wp_is_post_revision($postId)) {
            return;
        }

        $order = Order::find($postId);

        if (!$order) {
            return;
        }

        $this->ensureGeneratedMeta($order);
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
                'ORDER%s%s',
                date_i18n('ymdH'),
                strtoupper(Str::random(6))
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

    private function renderOrderDateField(Order $order): void
    {
        $isManual = $order->order_method === '' || $order->order_method === Order::METHOD_MANUALLY;

        echo '<tr>';
        echo '<th scope="row"><label for="' . esc_attr(Order::META_ORDER_DATE) . '">' . esc_html__('Order Date') . '</label></th>';
        echo '<td>';

        if ($isManual) {
            echo '<input type="datetime-local" class="regular-text" id="' . esc_attr(Order::META_ORDER_DATE) . '" name="' . esc_attr(self::FIELD_NAME) . '[' . esc_attr(Order::META_ORDER_DATE) . ']" value="' . esc_attr($this->formatOrderDateInput($order->order_date)) . '">';
            echo '<p class="description">' . esc_html__('Tanggal akan disimpan dengan format YmdHis.') . '</p>';
        } else {
            echo '<input type="text" class="regular-text" id="' . esc_attr(Order::META_ORDER_DATE) . '" value="' . esc_attr($order->order_date) . '" readonly>';
        }

        echo '</td>';
        echo '</tr>';
    }

    private function calculateSubtotal(array $items): float
    {
        return array_reduce(
            $items,
            static fn(float $subtotal, array $item): float => $subtotal + (float) $item['price_real'],
            0.0
        );
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

    private function sanitizeOrderDate(mixed $value, string $fallback): string
    {
        $value = trim((string) $value);

        if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $value)) {
            return str_replace(['-', 'T', ':'], '', $value) . '00';
        }

        $date = preg_replace('/\D/', '', (string) $value);

        return strlen($date) === 14 ? $date : $fallback;
    }

    private function formatOrderDateInput(string $value): string
    {
        if (!preg_match('/^\d{14}$/', $value)) {
            return '';
        }

        return sprintf(
            '%s-%s-%sT%s:%s',
            substr($value, 0, 4),
            substr($value, 4, 2),
            substr($value, 6, 2),
            substr($value, 8, 2),
            substr($value, 10, 2)
        );
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

    private function renderOrderItemsScript(): void
    {
        static $rendered = false;

        if ($rendered) {
            return;
        }

        $rendered = true;
?>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('ecoursityOrderItems', (payload) => ({
                    editable: Boolean(payload.editable),
                    items: Array.isArray(payload.items) ? payload.items : [],
                    courses: [],
                    coursesLoaded: false,
                    coursesLoading: false,
                    coursesError: '',
                    coursesUrl: String(payload.coursesUrl || ''),
                    restNonce: String(payload.restNonce || ''),
                    modalOpen: false,
                    search: '',
                    get subtotal() {
                        return this.items.reduce((sum, item) => sum + Number(item.price_real || 0), 0);
                    },
                    get total() {
                        return this.subtotal;
                    },
                    get itemsJson() {
                        return JSON.stringify(this.items);
                    },
                    async openModal() {
                        if (!this.editable) {
                            return;
                        }

                        this.modalOpen = true;
                        await this.loadCourses();
                    },
                    closeModal() {
                        this.modalOpen = false;
                    },
                    filteredCourses() {
                        const keyword = this.search.trim().toLowerCase();

                        if (!keyword) {
                            return this.courses;
                        }

                        return this.courses.filter((course) => String(course.name || '').toLowerCase().includes(keyword));
                    },
                    async loadCourses() {
                        if (this.coursesLoaded || this.coursesLoading) {
                            return;
                        }

                        this.coursesLoading = true;
                        this.coursesError = '';

                        try {
                            const response = await fetch(this.coursesUrl, {
                                headers: {
                                    'X-WP-Nonce': this.restNonce,
                                    'Accept': 'application/json',
                                },
                            });
                            const result = await response.json();

                            if (!response.ok || !result.success) {
                                throw new Error(result.message || 'Failed to load courses.');
                            }

                            this.courses = Array.isArray(result.data) ? result.data : [];
                            this.coursesLoaded = true;
                        } catch (error) {
                            this.coursesError = error.message || 'Failed to load courses.';
                        } finally {
                            this.coursesLoading = false;
                        }
                    },
                    hasItem(courseId) {
                        return this.items.some((item) => Number(item.id) === Number(courseId));
                    },
                    addItem(course) {
                        if (!this.editable || this.hasItem(course.id)) {
                            return;
                        }

                        this.items.push({
                            id: Number(course.id),
                            name: String(course.name || ''),
                            price: Number(course.price || 0),
                            price_sale: Number(course.price_sale || 0),
                            price_real: Number(course.price_real || 0),
                        });
                    },
                    removeItem(courseId) {
                        if (!this.editable) {
                            return;
                        }

                        this.items = this.items.filter((item) => Number(item.id) !== Number(courseId));
                    },
                    money(value) {
                        return new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            maximumFractionDigits: 0,
                        }).format(Number(value || 0));
                    },
                }));
            });
        </script>
    <?php
    }

    private function renderOrderItemsStyle(): void
    {
        static $rendered = false;

        if ($rendered) {
            return;
        }

        $rendered = true;
    ?>
        <style>
            .ecoursity-order-items__toolbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                margin-bottom: 12px;
            }

            .ecoursity-order-items__modal {
                position: fixed;
                inset: 0;
                z-index: 100000;
            }

            .ecoursity-order-items__backdrop {
                position: absolute;
                inset: 0;
                background: rgba(0, 0, 0, 0.45);
            }

            .ecoursity-order-items__dialog {
                position: relative;
                width: min(720px, calc(100vw - 32px));
                max-height: calc(100vh - 80px);
                overflow: auto;
                margin: 40px auto;
                padding: 20px;
                background: #fff;
                border-radius: 4px;
                box-shadow: 0 20px 45px rgba(0, 0, 0, 0.25);
            }

            .ecoursity-order-items__dialog-header,
            .ecoursity-order-items__course {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
            }

            .ecoursity-order-items__dialog-header {
                margin-bottom: 16px;
            }

            .ecoursity-order-items__dialog-header h3 {
                margin: 0;
            }

            .ecoursity-order-items__course-list {
                display: grid;
                gap: 8px;
                margin-top: 12px;
            }

            .ecoursity-order-items__course {
                padding: 12px;
                border: 1px solid #dcdcde;
                border-radius: 4px;
                background: #fff;
            }
        </style>
<?php
    }
}
