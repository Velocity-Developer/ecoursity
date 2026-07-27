<?php

namespace Ecoursity\App\Services\Admin;

use Ecoursity\App\Models\Course;
use Ecoursity\App\Models\Order;
use WP_Query;

class DashboardService
{
    public function stats(): array
    {
        $courseCounts = wp_count_posts(Course::POST_TYPE);
        $orderCounts = wp_count_posts(Order::POST_TYPE);
        $userCounts = count_users();
        $roleCounts = $userCounts['avail_roles'] ?? [];

        $publishedCourses = (int) ($courseCounts->publish ?? 0);
        $draftCourses = (int) ($courseCounts->draft ?? 0);
        $totalOrders = array_sum(array_map('intval', (array) $orderCounts));
        $completedOrders = $this->countOrdersByStatus(Order::STATUS_COMPLETED);
        $totalRevenue = $this->sumOrderRevenue();

        return [
            'courses' => [
                'title' => __('Total Kursus', 'ecoursity'),
                'value' => $publishedCourses + $draftCourses,
                'meta' => sprintf(
                    /* translators: 1: published course count, 2: draft course count */
                    __('%1$d terbit, %2$d draf', 'ecoursity'),
                    $publishedCourses,
                    $draftCourses
                ),
                'icon' => 'dashicons dashicons-book',
            ],
            'orders' => [
                'title' => __('Total Order', 'ecoursity'),
                'value' => $totalOrders,
                'meta' => sprintf(
                    /* translators: %d: completed order count */
                    __('%d selesai', 'ecoursity'),
                    $completedOrders
                ),
                'icon' => 'dashicons dashicons-cart',
            ],
            'revenue' => [
                'title' => __('Pendapatan', 'ecoursity'),
                'value' => $this->formatCurrency($totalRevenue),
                'meta' => __('Dari pesanan selesai', 'ecoursity'),
                'icon' => 'dashicons dashicons-money-alt',
            ],
            'students' => [
                'title' => __('Total Siswa', 'ecoursity'),
                'value' => (int) ($roleCounts['ecoursity_student'] ?? 0),
                'meta' => __('User role siswa', 'ecoursity'),
                'icon' => 'dashicons dashicons-admin-users',
            ],
            'instructors' => [
                'title' => __('Total Guru', 'ecoursity'),
                'value' => (int) ($roleCounts['ecoursity_instructor'] ?? 0),
                'meta' => __('User role instruktur', 'ecoursity'),
                'icon' => 'dashicons dashicons-admin-users',
            ],
        ];
    }

    public function purchaseChartData(int $days = 30): array
    {
        $days = max(1, $days);
        $today = current_time('timestamp');
        $start = strtotime('-' . ($days - 1) . ' days', $today);
        $series = [];

        for ($index = 0; $index < $days; $index++) {
            $timestamp = strtotime("+{$index} days", $start);
            $key = date_i18n('Y-m-d', $timestamp);

            $series[$key] = [
                'label' => date_i18n('d M', $timestamp),
                'orders' => 0,
                'revenue' => 0.0,
            ];
        }

        $orders = Order::all([
            'posts_per_page' => -1,
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => Order::META_ORDER_STATUS,
                    'value' => [Order::STATUS_COMPLETED, Order::STATUS_PROCESSING],
                    'compare' => 'IN',
                ],
                [
                    'key' => Order::META_ORDER_DATE,
                    'value' => [
                        date_i18n('Ymd000000', $start),
                        date_i18n('Ymd235959', $today),
                    ],
                    'type' => 'NUMERIC',
                    'compare' => 'BETWEEN',
                ],
            ],
        ]);

        foreach ($orders as $order) {
            $timestamp = $this->timestampFromOrderDate($order->order_date);

            if ($timestamp === null) {
                continue;
            }

            $key = date_i18n('Y-m-d', $timestamp);

            if (!isset($series[$key])) {
                continue;
            }

            $series[$key]['orders']++;
            $series[$key]['revenue'] += max(0, $order->order_total);
        }

        return [
            'labels' => array_column($series, 'label'),
            'orders' => array_column($series, 'orders'),
            'revenue' => array_map(
                static fn(array $item): float => round((float) $item['revenue'], 2),
                array_values($series)
            ),
        ];
    }

    public function newestCourses(int $limit = 6): array
    {
        return Course::all([
            'posts_per_page' => max(1, $limit),
            'orderby' => 'date',
            'order' => 'DESC',
        ]);
    }

    private function countOrdersByStatus(string $status): int
    {
        $query = new WP_Query([
            'post_type' => Order::POST_TYPE,
            'post_status' => ['publish', 'draft', 'pending', 'private'],
            'posts_per_page' => 1,
            'fields' => 'ids',
            'no_found_rows' => false,
            'meta_query' => [
                [
                    'key' => Order::META_ORDER_STATUS,
                    'value' => $status,
                    'compare' => '=',
                ],
            ],
        ]);

        return (int) $query->found_posts;
    }

    private function sumOrderRevenue(): float
    {
        $orders = Order::all([
            'posts_per_page' => -1,
            'meta_query' => [
                [
                    'key' => Order::META_ORDER_STATUS,
                    'value' => Order::STATUS_COMPLETED,
                    'compare' => '=',
                ],
            ],
        ]);

        return array_reduce(
            $orders,
            static fn(float $total, Order $order): float => $total + max(0, $order->order_total),
            0.0
        );
    }

    private function timestampFromOrderDate(string $date): ?int
    {
        if (!preg_match('/^\d{14}$/', $date)) {
            return null;
        }

        $timestamp = strtotime(sprintf(
            '%s-%s-%s %s:%s:%s',
            substr($date, 0, 4),
            substr($date, 4, 2),
            substr($date, 6, 2),
            substr($date, 8, 2),
            substr($date, 10, 2),
            substr($date, 12, 2)
        ));

        return $timestamp === false ? null : $timestamp;
    }

    private function formatCurrency(float $value): string
    {
        return 'Rp ' . number_format($value, 0, ',', '.');
    }
}
