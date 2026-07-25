<?php

defined('ABSPATH') || exit;

$fieldValue = static function (array $values, string $key, mixed $default = ''): mixed {
    return array_key_exists($key, $values) ? $values[$key] : $default;
};
?>

<div class="ecoursity-admin-layout ecoursity-settings">
    <div class="ecoursity-admin-page__header">
        <h1 class="ecoursity-admin-page__title"><?php echo esc_html__('Pengaturan Ecoursity', 'ecoursity'); ?></h1>
        <p class="ecoursity-admin-page__desc"><?php echo esc_html__('Kelola konfigurasi utama LMS Ecoursity.', 'ecoursity'); ?></p>
    </div>

    <?php if (!empty($updated)) : ?>
        <div class="notice notice-success is-dismissible">
            <p><?php echo esc_html__('Pengaturan berhasil disimpan.', 'ecoursity'); ?></p>
        </div>
    <?php endif; ?>

    <nav class="ecoursity-settings__tabs" aria-label="<?php echo esc_attr__('Tab Pengaturan', 'ecoursity'); ?>">
        <?php foreach ($tabs as $tabKey => $tab) : ?>
            <?php
            $tabUrl = add_query_arg([
                'page' => 'ecoursity-settings',
                'tab' => $tabKey,
            ], admin_url('admin.php'));
            ?>
            <a
                class="ecoursity-settings__tab <?php echo $activeTab === $tabKey ? 'is-active' : ''; ?>"
                href="<?php echo esc_url($tabUrl); ?>"
            >
                <?php echo esc_html($tab['label']); ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <form class="ecoursity-settings__panel" method="post" action="<?php echo esc_url(add_query_arg([
        'page' => 'ecoursity-settings',
        'tab' => $activeTab,
    ], admin_url('admin.php'))); ?>">
        <?php wp_nonce_field('ecoursity_save_settings'); ?>

        <div class="ecoursity-settings__section-header">
            <h2 class="ecoursity-settings__section-title"><?php echo esc_html($activeSchema['label']); ?></h2>
            <?php if (!empty($activeSchema['description'])) : ?>
                <p class="ecoursity-settings__section-desc"><?php echo esc_html($activeSchema['description']); ?></p>
            <?php endif; ?>
        </div>

        <table class="form-table ecoursity-settings__table" role="presentation">
            <tbody>
                <?php foreach ($activeSchema['fields'] as $field) : ?>
                    <?php
                    $key = (string) $field['key'];
                    $type = (string) ($field['type'] ?? 'text');
                    $value = $fieldValue($values, $key, $field['default'] ?? '');
                    $inputId = 'ecoursity_setting_' . sanitize_key($key);
                    ?>
                    <tr>
                        <th scope="row">
                            <label for="<?php echo esc_attr($inputId); ?>">
                                <?php echo esc_html($field['label']); ?>
                            </label>
                        </th>
                        <td>
                            <?php if ($type === 'select') : ?>
                                <select
                                    id="<?php echo esc_attr($inputId); ?>"
                                    name="ecoursity_settings[<?php echo esc_attr($key); ?>]"
                                >
                                    <?php foreach (($field['options'] ?? []) as $optionValue => $optionLabel) : ?>
                                        <option value="<?php echo esc_attr((string) $optionValue); ?>" <?php selected((string) $value, (string) $optionValue); ?>>
                                            <?php echo esc_html($optionLabel); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php elseif ($type === 'checkbox') : ?>
                                <label class="ecoursity-settings__checkbox">
                                    <input
                                        id="<?php echo esc_attr($inputId); ?>"
                                        type="checkbox"
                                        name="ecoursity_settings[<?php echo esc_attr($key); ?>]"
                                        value="1"
                                        <?php checked((bool) $value); ?>
                                    >
                                    <span><?php echo esc_html($field['description'] ?? $field['label']); ?></span>
                                </label>
                            <?php elseif ($type === 'textarea') : ?>
                                <textarea
                                    id="<?php echo esc_attr($inputId); ?>"
                                    name="ecoursity_settings[<?php echo esc_attr($key); ?>]"
                                    rows="4"
                                    class="large-text"
                                ><?php echo esc_textarea((string) $value); ?></textarea>
                            <?php else : ?>
                                <input
                                    id="<?php echo esc_attr($inputId); ?>"
                                    type="<?php echo esc_attr($type === 'number' ? 'number' : $type); ?>"
                                    name="ecoursity_settings[<?php echo esc_attr($key); ?>]"
                                    value="<?php echo esc_attr((string) $value); ?>"
                                    class="regular-text"
                                    <?php echo isset($field['min']) ? 'min="' . esc_attr((string) $field['min']) . '"' : ''; ?>
                                    <?php echo isset($field['max']) ? 'max="' . esc_attr((string) $field['max']) . '"' : ''; ?>
                                >
                            <?php endif; ?>

                            <?php if ($type !== 'checkbox' && !empty($field['description'])) : ?>
                                <p class="description"><?php echo esc_html($field['description']); ?></p>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php submit_button(__('Simpan Pengaturan', 'ecoursity')); ?>
    </form>
</div>
