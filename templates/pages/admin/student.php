<div class="ecoursity-admin-layout">
    <div class="ecoursity-admin-page__header">
        <h1 class="ecoursity-admin-page__title">Siswa Ecoursity</h1>
        <p class="ecoursity-admin-page__desc">Daftar semua member Siswa.</p>
    </div>

    <div class="ecoursity-admin-page__card">
        <div class="ecoursity-admin-page__table-wrap">
            <table class="ecoursity-admin-page__table">
                <thead>
                    <tr>
                        <th class="ecoursity-admin-page__th">No</th>
                        <th class="ecoursity-admin-page__th">Nama</th>
                        <th class="ecoursity-admin-page__th">Username</th>
                        <th class="ecoursity-admin-page__th">Email</th>
                        <th class="ecoursity-admin-page__th">Terdaftar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($students)) : ?>
                        <tr>
                            <td colspan="5" class="ecoursity-admin-page__empty">Belum ada data siswa.</td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($students as $index => $student) : ?>
                            <tr class="ecoursity-admin-page__row">
                                <td class="ecoursity-admin-page__td"><?php echo esc_html($index + 1); ?></td>
                                <td class="ecoursity-admin-page__td ecoursity-admin-page__td--name"><?php echo esc_html($student->displayName ?: '-'); ?></td>
                                <td class="ecoursity-admin-page__td"><?php echo esc_html($student->userLogin); ?></td>
                                <td class="ecoursity-admin-page__td"><?php echo esc_html($student->email); ?></td>
                                <td class="ecoursity-admin-page__td"><?php echo esc_html(wp_date('d M Y H:i', strtotime($student->userRegistered))); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>