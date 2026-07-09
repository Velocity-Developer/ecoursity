<?php
$stats = [
    'courses' => [
        'title' => __('Total Kursus'),
        'value' => 15,
    ],
    'courses_published' => [
        'title' => __('Kursus Terbit'),
        'value' => 5,
    ],
    'courses_draft' => [
        'title' => __('Kursus Draf'),
        'value' => 3,
    ],
    'students' => [
        'title' => __('Total Siswa'),
        'value' => 235,
    ],
    'instructors' => [
        'title' => __('Total Guru'),
        'value' => 12,
    ],
];
?>

<div class="ecoursity-admin-layout">
    <h1>Dashboard Ecoursity</h1>

    <div class="ecoursity-flex">
        <?php if ($stats) :
            foreach ($stats as $key => $value) : ?>
                <div class="card">
                    <p><?php echo $value['title']; ?></p>

                    <h2><?php echo $value['value']; ?></h2>
                </div>
        <?php endforeach;
        endif; ?>
    </div>

</div>