<?php

$chart_data = $chartData ?? [
    'labels' => [],
    'orders' => [],
    'revenue' => [],
];
$chart_id = 'ecoursity-purchase-chart';
$has_chart_data = !empty(array_filter($chart_data['orders'] ?? [])) || !empty(array_filter($chart_data['revenue'] ?? []));
?>

<div class="ecoursity-admin-layout ecoursity-dashboard">
    <div class="ecoursity-dashboard__hero">
        <div>
            <p class="ecoursity-dashboard__eyebrow"><?php echo esc_html__('Dashboard LMS', 'ecoursity'); ?></p>
            <h1 class="ecoursity-dashboard__heading">
                <span class="ecoursity-dashboard__heading-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">
                        <path d="M11.7 2.805a.75.75 0 0 1 .6 0A60.65 60.65 0 0 1 22.83 8.72a.75.75 0 0 1-.231 1.337 49.948 49.948 0 0 0-9.902 3.912l-.003.002c-.114.06-.227.119-.34.18a.75.75 0 0 1-.707 0A50.88 50.88 0 0 0 7.5 12.173v-.224c0-.131.067-.248.172-.311a54.615 54.615 0 0 1 4.653-2.52.75.75 0 0 0-.65-1.352 56.123 56.123 0 0 0-4.78 2.589 1.858 1.858 0 0 0-.859 1.228 49.803 49.803 0 0 0-4.634-1.527.75.75 0 0 1-.231-1.337A60.653 60.653 0 0 1 11.7 2.805Z" />
                        <path d="M13.06 15.473a48.45 48.45 0 0 1 7.666-3.282c.134 1.414.22 2.843.255 4.284a.75.75 0 0 1-.46.711 47.87 47.87 0 0 0-8.105 4.342.75.75 0 0 1-.832 0 47.87 47.87 0 0 0-8.104-4.342.75.75 0 0 1-.461-.71c.035-1.442.121-2.87.255-4.286.921.304 1.83.634 2.726.99v1.27a1.5 1.5 0 0 0-.14 2.508c-.09.38-.222.753-.397 1.11.452.213.901.434 1.346.66a6.727 6.727 0 0 0 .551-1.607 1.5 1.5 0 0 0 .14-2.67v-.645a48.549 48.549 0 0 1 3.44 1.667 2.25 2.25 0 0 0 2.12 0Z" />
                        <path d="M4.462 19.462c.42-.419.753-.89 1-1.395.453.214.902.435 1.347.662a6.742 6.742 0 0 1-1.286 1.794.75.75 0 0 1-1.06-1.06Z" />
                    </svg>
                </span>
                <?php echo esc_html__('Ecoursity', 'ecoursity'); ?>
            </h1>
        </div>

        <a class="ecoursity-dashboard__action" href="<?php echo esc_url(admin_url('admin.php?page=ecoursity-courses')); ?>">
            <span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span>
            <?php echo esc_html__('Kelola Kursus', 'ecoursity'); ?>
        </a>
    </div>

    <div class="ecoursity-dashboard__stats" aria-label="<?php echo esc_attr__('Ringkasan Ecoursity', 'ecoursity'); ?>">
        <?php foreach (($stats ?? []) as $value) : ?>
            <div class="ecoursity-dashboard__stat">
                <div class="ecoursity-dashboard__stat-icon">
                    <span class="<?php echo esc_attr((string) ($value['icon'] ?? 'dashicons dashicons-chart-bar')); ?>" aria-hidden="true"></span>
                </div>
                <div>
                    <p class="ecoursity-dashboard__stat-title"><?php echo esc_html((string) ($value['title'] ?? '')); ?></p>
                    <h2 class="ecoursity-dashboard__stat-value"><?php echo esc_html((string) ($value['value'] ?? 0)); ?></h2>
                    <?php if (!empty($value['meta'])) : ?>
                        <p class="ecoursity-dashboard__stat-meta"><?php echo esc_html((string) $value['meta']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="ecoursity-dashboard__grid">
        <section class="ecoursity-dashboard__card ecoursity-dashboard__card--chart">
            <div class="ecoursity-dashboard__card-header">
                <div>
                    <p class="ecoursity-dashboard__eyebrow"><?php echo esc_html__('30 hari terakhir', 'ecoursity'); ?></p>
                    <h2 class="ecoursity-dashboard__card-title"><?php echo esc_html__('Grafik Pembelian Kursus', 'ecoursity'); ?></h2>
                </div>
                <span class="ecoursity-dashboard__badge"><?php echo esc_html__('Pesanan', 'ecoursity'); ?></span>
            </div>

            <div class="ecoursity-dashboard__chart-wrap">
                <canvas id="<?php echo esc_attr($chart_id); ?>" height="120"></canvas>

                <?php if (!$has_chart_data) : ?>
                    <div class="ecoursity-dashboard__empty ecoursity-dashboard__empty--chart">
                        <?php echo esc_html__('Belum ada pembelian selesai atau diproses dalam 30 hari terakhir.', 'ecoursity'); ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="ecoursity-dashboard__card">
            <div class="ecoursity-dashboard__card-header">
                <div>
                    <p class="ecoursity-dashboard__eyebrow"><?php echo esc_html__('Konten', 'ecoursity'); ?></p>
                    <h2 class="ecoursity-dashboard__card-title"><?php echo esc_html__('Kursus Terbaru', 'ecoursity'); ?></h2>
                </div>
            </div>

            <?php if (!empty($list_newest_courses)) : ?>
                <ol class="ecoursity-dashboard__list">
                    <?php foreach ($list_newest_courses as $course) : ?>
                        <li class="ecoursity-dashboard__list-item">
                            <a href="<?php echo esc_url(get_permalink($course->id)); ?>" class="ecoursity-dashboard__list-link">
                                <?php echo esc_html($course->title); ?>
                            </a>
                            <span class="ecoursity-dashboard__list-meta">
                                <?php echo esc_html(sprintf(__('ID #%d', 'ecoursity'), (int) $course->id)); ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ol>
            <?php else : ?>
                <div class="ecoursity-dashboard__empty">
                    <?php echo esc_html__('Belum ada kursus yang diterbitkan.', 'ecoursity'); ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>

<script>
    window.addEventListener('load', () => {
        const chartElement = document.getElementById(<?php echo wp_json_encode($chart_id); ?>);

        if (!chartElement || typeof Chart === 'undefined') {
            return;
        }

        const chartData = <?php echo wp_json_encode($chart_data); ?>;
        const currency = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        });

        new Chart(chartElement, {
            type: 'line',
            data: {
                labels: chartData.labels || [],
                datasets: [{
                        type: 'bar',
                        label: '<?php echo esc_js(__('Pesanan', 'ecoursity')); ?>',
                        data: chartData.orders || [],
                        backgroundColor: 'rgba(2, 74, 216, 0.14)',
                        borderColor: '#024ad8',
                        borderWidth: 1,
                        borderRadius: 4,
                        yAxisID: 'orders',
                    },
                    {
                        label: '<?php echo esc_js(__('Pendapatan', 'ecoursity')); ?>',
                        data: chartData.revenue || [],
                        borderColor: '#15803d',
                        backgroundColor: 'rgba(21, 128, 61, 0.12)',
                        borderWidth: 2,
                        pointRadius: 2,
                        pointHoverRadius: 4,
                        tension: 0.35,
                        fill: true,
                        yAxisID: 'revenue',
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 10,
                            usePointStyle: true,
                        },
                    },
                    tooltip: {
                        callbacks: {
                            label(context) {
                                if (context.dataset.yAxisID === 'revenue') {
                                    return `${context.dataset.label}: ${currency.format(context.parsed.y || 0)}`;
                                }

                                return `${context.dataset.label}: ${context.parsed.y || 0}`;
                            },
                        },
                    },
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                        },
                        ticks: {
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 8,
                        },
                    },
                    orders: {
                        beginAtZero: true,
                        position: 'left',
                        ticks: {
                            precision: 0,
                        },
                    },
                    revenue: {
                        beginAtZero: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false,
                        },
                        ticks: {
                            callback(value) {
                                return currency.format(value).replace(',00', '');
                            },
                        },
                    },
                },
            },
        });
    });
</script>