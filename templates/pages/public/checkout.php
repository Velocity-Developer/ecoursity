<?php

/**
 * Template for displaying the public checkout page.
 *
 * @author  Velocity Developer Team
 * @package Ecoursity/Template
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

$coursesUrl = get_post_type_archive_link('ecoursity_course') ?: home_url('/');
$loginUrl = wp_login_url(get_permalink());

?>

<div
    class="ecoursity-checkout"
    x-data="{
        payment: 'bank_transfer',
        processing: false,
        message: '',
        messageType: 'success',
        order: null,
        emptyCart: {
            items: [],
            courses: [],
            count: 0,
            loading: true,
            saving: false,
            remove() {
                return false;
            },
        },
        get cart() {
            return $store.EcoursityCart || this.emptyCart;
        },
        get subtotal() {
            return this.cart.courses.reduce((total, course) => total + this.coursePrice(course), 0);
        },
        get canCheckout() {
            return !this.processing && !this.cart.loading && this.cart.count > 0;
        },
        coursePrice(course) {
            const sale = Number(course.price_sale || 0);
            const price = Number(course.price || 0);

            return sale > 0 ? sale : price;
        },
        money(value) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0,
            }).format(Number(value || 0));
        },
        async submit() {
            if (!this.canCheckout) {
                return;
            }

            this.processing = true;
            this.message = '';
            this.order = null;

            const order = await chekout(this.payment);

            this.processing = false;
            this.message = window.EcoursityCheckout?.message || '';
            this.messageType = window.EcoursityCheckout?.messageType || 'success';
            this.order = order || null;
        },
    }">
    <section class="ecoursity-checkout__content">
        <div class="ecoursity-checkout__layout">
            <div class="ecoursity-checkout__main">
                <div class="ecoursity-checkout__section">
                    <div class="ecoursity-checkout__section-heading">
                        <h2><?php esc_html_e('Keranjang', 'ecoursity'); ?></h2>
                        <span x-text="cart.count + ' item'"></span>
                    </div>

                    <div class="ecoursity-checkout__notice" x-show="cart.loading">
                        <?php esc_html_e('Memuat keranjang...', 'ecoursity'); ?>
                    </div>

                    <div class="ecoursity-checkout__empty" x-show="!cart.loading && cart.count === 0 && !order" x-cloak>
                        <h2><?php esc_html_e('Keranjang masih kosong', 'ecoursity'); ?></h2>
                        <p><?php esc_html_e('Pilih kursus terlebih dahulu sebelum melanjutkan checkout.', 'ecoursity'); ?></p>
                        <a class="ecoursity-checkout__link-button" href="<?php echo esc_url($coursesUrl); ?>">
                            <?php esc_html_e('Lihat Kursus', 'ecoursity'); ?>
                        </a>
                    </div>

                    <div class="ecoursity-checkout__items" x-show="cart.count > 0" x-cloak>
                        <template x-for="course in cart.courses" x-bind:key="course.id">
                            <article class="ecoursity-checkout__item">
                                <a class="ecoursity-checkout__item-media" x-bind:href="course.permalink">
                                    <template x-if="course.thumbnail">
                                        <img x-bind:src="course.thumbnail" x-bind:alt="course.title">
                                    </template>
                                    <span x-show="!course.thumbnail"></span>
                                </a>

                                <div class="ecoursity-checkout__item-body">
                                    <h3>
                                        <a x-bind:href="course.permalink" x-text="course.title"></a>
                                    </h3>
                                    <div class="ecoursity-checkout__item-meta">
                                        <strong x-text="money(coursePrice(course))"></strong>
                                        <del x-show="Number(course.price_sale || 0) > 0" x-text="money(course.price)"></del>
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    class="ecoursity-checkout__remove"
                                    x-bind:disabled="cart.saving || processing"
                                    x-on:click="cart.remove(course.id)">
                                    <?php esc_html_e('Hapus', 'ecoursity'); ?>
                                </button>
                            </article>
                        </template>
                    </div>
                </div>

                <div class="ecoursity-checkout__section" x-show="cart.count > 0" x-cloak>
                    <div class="ecoursity-checkout__section-heading">
                        <h2><?php esc_html_e('Metode Pembayaran', 'ecoursity'); ?></h2>
                    </div>

                    <div class="ecoursity-checkout__payments">
                        <label class="ecoursity-checkout__payment">
                            <input type="radio" name="ecoursity_payment" value="bank_transfer" x-model="payment">
                            <span>
                                <strong><?php esc_html_e('Transfer Bank', 'ecoursity'); ?></strong>
                                <small><?php esc_html_e('Pesanan dibuat dengan status pending untuk dikonfirmasi admin.', 'ecoursity'); ?></small>
                            </span>
                        </label>

                        <label class="ecoursity-checkout__payment">
                            <input type="radio" name="ecoursity_payment" value="manual_confirmation" x-model="payment">
                            <span>
                                <strong><?php esc_html_e('Konfirmasi Manual', 'ecoursity'); ?></strong>
                                <small><?php esc_html_e('Gunakan pilihan ini jika pembayaran akan diatur di luar sistem.', 'ecoursity'); ?></small>
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <aside class="ecoursity-checkout__summary">
                <div class="ecoursity-checkout__summary-box">
                    <h2><?php esc_html_e('Ringkasan', 'ecoursity'); ?></h2>

                    <div class="ecoursity-checkout__summary-row">
                        <span><?php esc_html_e('Jumlah Kursus', 'ecoursity'); ?></span>
                        <strong x-text="cart.count"></strong>
                    </div>

                    <div class="ecoursity-checkout__summary-row">
                        <span><?php esc_html_e('Subtotal', 'ecoursity'); ?></span>
                        <strong x-text="money(subtotal)"></strong>
                    </div>

                    <div class="ecoursity-checkout__summary-total">
                        <span><?php esc_html_e('Total', 'ecoursity'); ?></span>
                        <strong x-text="money(subtotal)"></strong>
                    </div>

                    <?php if (is_user_logged_in()) : ?>
                        <button
                            type="button"
                            class="ecoursity-checkout__button"
                            x-bind:disabled="!canCheckout"
                            x-bind:aria-busy="processing ? 'true' : 'false'"
                            x-on:click="submit()">
                            <span x-show="!processing"><?php esc_html_e('Buat Pesanan', 'ecoursity'); ?></span>
                            <span x-show="processing"><?php esc_html_e('Memproses...', 'ecoursity'); ?></span>
                        </button>
                    <?php else : ?>
                        <a class="ecoursity-checkout__button" href="<?php echo esc_url($loginUrl); ?>">
                            <?php esc_html_e('Login untuk Checkout', 'ecoursity'); ?>
                        </a>
                    <?php endif; ?>

                    <p
                        class="ecoursity-checkout__message"
                        x-bind:class="messageType === 'error' ? 'is-error' : 'is-success'"
                        x-show="message"
                        x-text="message"
                        x-cloak></p>

                    <div class="ecoursity-checkout__success" x-show="order" x-cloak>
                        <span><?php esc_html_e('Nomor Pesanan', 'ecoursity'); ?></span>
                        <strong x-text="order?.order_number || ''"></strong>
                    </div>
                </div>
            </aside>
        </div>
    </section>
</div>
