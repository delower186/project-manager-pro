<?php 

// Enqueue Countdown JS & CSS
function wppm_countdown_assets($hook) {
    // Only load on Projects and Tasks list pages
    if (!in_array($hook, ['edit.php'])) return;

    global $typenow;
    if(!in_array($typenow, ['wppm_project','wppm_task'])) return;

    // JS
    wp_enqueue_script('wppm-countdown', WPPM_PLUGIN_DIR_URL . 'assets/js/countdown.js', ['jquery'], '1.0', true);
    // CSS
    wp_enqueue_style('wppm-countdown', WPPM_PLUGIN_DIR_URL . 'assets/css/countdown.css');
}
add_action('admin_enqueue_scripts', 'wppm_countdown_assets');

function wppm_project_assets($hook) {
    // Only load for the project list screen
    if ($hook !== 'edit.php' || get_post_type() !== 'wppm_project') return;

    // CSS
    wp_enqueue_style('jquery-ui-css', '//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css', [], '1.13.2');

    // JS
    wp_enqueue_script('jquery-ui-dialog'); // required for jQuery UI modal
    wp_enqueue_script('wppm_project_modal', WPPM_PLUGIN_DIR_URL . 'assets/js/project_modal.js', ['jquery','jquery-ui-dialog'], '1.0', true);

    // Localize nonce & ajax URL for project_modal.js
    wp_localize_script('wppm_project_modal', 'wppm_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('wppm_action')
    ]);
}
add_action('admin_enqueue_scripts', 'wppm_project_assets');


function wppm_dashboard_assets($hook) {
    // Only load on WP Project Manager dashboard
    if ($hook !== 'toplevel_page_wppm_dashboard') return;

    // JS
    wp_enqueue_script('wppm-dashboard-js', WPPM_PLUGIN_DIR_URL . 'assets/js/dashboard.js', ['jquery'], '1.0', true);

    // CSS
    wp_enqueue_style('wppm-dashboard-css', WPPM_PLUGIN_DIR_URL . 'assets/css/dashboard.css');
}
add_action('admin_enqueue_scripts', 'wppm_dashboard_assets');