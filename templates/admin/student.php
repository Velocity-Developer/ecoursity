<div class="ecoursity-admin-layout tw:p-6">
    <div class="tw:mb-6">
        <h1 class="tw:text-2xl tw:font-bold tw:text-slate-900">Siswa Ecoursity</h1>
        <p class="tw:mt-1 tw:text-sm tw:text-slate-500">Daftar semua user dengan role student.</p>
    </div>

    <div class="tw:overflow-hidden tw:rounded-xl tw:border tw:border-slate-200 tw:bg-white tw:shadow-sm">
        <div class="tw:overflow-x-auto">
            <table class="tw:min-w-full tw:divide-y tw:divide-slate-200 tw:text-sm">
                <thead class="tw:bg-slate-50">
                    <tr>
                        <th class="tw:px-4 tw:py-3 tw:text-left tw:font-semibold tw:text-slate-600">No</th>
                        <th class="tw:px-4 tw:py-3 tw:text-left tw:font-semibold tw:text-slate-600">Nama</th>
                        <th class="tw:px-4 tw:py-3 tw:text-left tw:font-semibold tw:text-slate-600">Username</th>
                        <th class="tw:px-4 tw:py-3 tw:text-left tw:font-semibold tw:text-slate-600">Email</th>
                        <th class="tw:px-4 tw:py-3 tw:text-left tw:font-semibold tw:text-slate-600">Terdaftar</th>
                    </tr>
                </thead>
                <tbody class="tw:divide-y tw:divide-slate-100 tw:bg-white">
                    <?php if (empty($students)) : ?>
                        <tr>
                            <td colspan="5" class="tw:px-4 tw:py-6 tw:text-center tw:text-slate-500">Belum ada data siswa.</td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($students as $index => $student) : ?>
                            <tr class="tw:hover:bg-slate-50">
                                <td class="tw:px-4 tw:py-3 tw:text-slate-600"><?php echo esc_html($index + 1); ?></td>
                                <td class="tw:px-4 tw:py-3 tw:font-medium tw:text-slate-900"><?php echo esc_html($student->displayName ?: '-'); ?></td>
                                <td class="tw:px-4 tw:py-3 tw:text-slate-600"><?php echo esc_html($student->userLogin); ?></td>
                                <td class="tw:px-4 tw:py-3 tw:text-slate-600"><?php echo esc_html($student->email); ?></td>
                                <td class="tw:px-4 tw:py-3 tw:text-slate-600"><?php echo esc_html(wp_date('d M Y H:i', strtotime($student->userRegistered))); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>