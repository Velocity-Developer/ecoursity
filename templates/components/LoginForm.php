<?php
$props = isset($props) ? $props : [];

$wrapperClass = 'ecoursity-login-form';
if (!empty($props['class'])) {
    $wrapperClass .= ' ' . $props['class'];
}
?>
<div class="<?php echo esc_attr($wrapperClass); ?>">
    <?php
    wp_login_form([
        'redirect'       => $props['redirect'] ?? admin_url(),
        'form_id'        => $props['form_id'] ?? 'ecoursity-login-form',
        'label_username' => $props['label_username'] ?? __('Username or Email', 'ecoursity'),
        'label_password' => $props['label_password'] ?? __('Password', 'ecoursity'),
        'label_remember' => $props['label_remember'] ?? __('Remember Me', 'ecoursity'),
        'label_log_in'   => $props['label_log_in'] ?? __('Log In', 'ecoursity'),
    ]);
    ?>
    <p class="ecoursity-login-form__links">
        <a href="<?php echo esc_url(wp_lostpassword_url()); ?>" class="ecoursity-login-form__link">
            <?php esc_html_e('Lost your password?', 'ecoursity'); ?>
        </a>
    </p>
</div>