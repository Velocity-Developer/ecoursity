<div class="ecoursity-admin-layout ecoursity-dashboard">
    <h1 class="ecoursity-dashboard__heading">
        <span class="ecoursity-dashboard__heading-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24" fill="currentColor">
                <path d="M11.7 2.805a.75.75 0 0 1 .6 0A60.65 60.65 0 0 1 22.83 8.72a.75.75 0 0 1-.231 1.337 49.948 49.948 0 0 0-9.902 3.912l-.003.002c-.114.06-.227.119-.34.18a.75.75 0 0 1-.707 0A50.88 50.88 0 0 0 7.5 12.173v-.224c0-.131.067-.248.172-.311a54.615 54.615 0 0 1 4.653-2.52.75.75 0 0 0-.65-1.352 56.123 56.123 0 0 0-4.78 2.589 1.858 1.858 0 0 0-.859 1.228 49.803 49.803 0 0 0-4.634-1.527.75.75 0 0 1-.231-1.337A60.653 60.653 0 0 1 11.7 2.805Z" />
                <path d="M13.06 15.473a48.45 48.45 0 0 1 7.666-3.282c.134 1.414.22 2.843.255 4.284a.75.75 0 0 1-.46.711 47.87 47.87 0 0 0-8.105 4.342.75.75 0 0 1-.832 0 47.87 47.87 0 0 0-8.104-4.342.75.75 0 0 1-.461-.71c.035-1.442.121-2.87.255-4.286.921.304 1.83.634 2.726.99v1.27a1.5 1.5 0 0 0-.14 2.508c-.09.38-.222.753-.397 1.11.452.213.901.434 1.346.66a6.727 6.727 0 0 0 .551-1.607 1.5 1.5 0 0 0 .14-2.67v-.645a48.549 48.549 0 0 1 3.44 1.667 2.25 2.25 0 0 0 2.12 0Z" />
                <path d="M4.462 19.462c.42-.419.753-.89 1-1.395.453.214.902.435 1.347.662a6.742 6.742 0 0 1-1.286 1.794.75.75 0 0 1-1.06-1.06Z" />
            </svg>
        </span>
        Ecoursity
    </h1>

    <div class="ecoursity-dashboard__layout">
        <div class="ecoursity-dashboard__stats">
            <?php if ($stats) :
                foreach ($stats as $key => $value) : ?>
                    <div class="ecoursity-dashboard__stat">
                        <i class="<?php echo $value['icon']; ?>"></i>
                        <p class="ecoursity-dashboard__stat-title"><?php echo $value['title']; ?></p>
                        <h2 class="ecoursity-dashboard__stat-value"><?php echo $value['value']; ?></h2>
                    </div>
            <?php endforeach;
            endif; ?>
        </div>

        <div class="ecoursity-dashboard__grid">
            <div class="ecoursity-dashboard__col ecoursity-dashboard__col--main">
                <div class="ecoursity-dashboard__card">
                    <div class="ecoursity-dashboard__card-title">Grafik Pembelian Kursus 30 Hari Terakhir</div>
                    <div class="ecoursity-dashboard__card-body ecoursity-dashboard__card-body--chart">
                        ---- ---- ----
                    </div>
                </div>
            </div>

            <div class="ecoursity-dashboard__col ecoursity-dashboard__col--side">
                <div class="ecoursity-dashboard__card">
                    <div class="ecoursity-dashboard__card-title">Kursus Terbaru</div>
                    <div class="ecoursity-dashboard__card-body">
                        <?php if ($list_newest_courses) : ?>
                            <ol class="ecoursity-dashboard__list">
                                <?php foreach ($list_newest_courses as $course) : ?>
                                    <li class="ecoursity-dashboard__list-item">
                                        <a href="<?php echo get_permalink($course->id); ?>" class="ecoursity-dashboard__list-link">
                                            <?php echo $course->title; ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>