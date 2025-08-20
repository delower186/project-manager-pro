<?php 
if (!defined('ABSPATH')) exit;
// Enqueue Countdown JS & CSS
function pmp_countdown_assets($hook) {
    // Only load on Projects and Tasks list pages
    if (!in_array($hook, ['edit.php'])) return;

    global $typenow;
    if(!in_array($typenow, ['pmp_project','pmp_task'])) return;

    // JS
    wp_enqueue_script('pmp-countdown', PMP_PLUGIN_DIR_URL . 'assets/js/pmp_countdown.js', ['jquery'], '1.0', true);
    // CSS
    wp_enqueue_style('pmp-countdown', PMP_PLUGIN_DIR_URL . 'assets/css/pmp_countdown.css',false,'1.0', true);
}
add_action('admin_enqueue_scripts', 'pmp_countdown_assets');

function pmp_modal_assets($hook) {
    // Only load for the project list screen

    if ($hook !== 'edit.php') return;
    $screen = get_current_screen();
    if(in_array($screen->post_type, ['pmp_project', 'pmp_task'])){
        // CSS
        wp_enqueue_style('jquery-ui-css', '//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css', [], '1.13.2');

        // JS
        wp_enqueue_script('jquery-ui-dialog'); // required for jQuery UI modal
        wp_enqueue_script('pmp_project_modal', PMP_PLUGIN_DIR_URL . 'assets/js/pmp_project_modal.js', ['jquery','jquery-ui-dialog'], '1.0', true);
        wp_enqueue_script('pmp_task_modal', PMP_PLUGIN_DIR_URL . 'assets/js/pmp_task_modal.js', ['jquery','jquery-ui-dialog'], '1.0', true);

        // Localize for both scripts
        $localize = [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('pmp_action')
        ];
        wp_localize_script('pmp_project_modal', 'pmp_ajax', $localize);
        wp_localize_script('pmp_task_modal', 'pmp_ajax', $localize);
    }
}
add_action('admin_enqueue_scripts', 'pmp_modal_assets');


function pmp_dashboard_assets($hook) {
    // Only load on WP Project Manager dashboard
    if ($hook !== 'toplevel_page_pmp_dashboard') return;

    // JS
    wp_enqueue_script('pmp-dashboard-js', PMP_PLUGIN_DIR_URL . 'assets/js/pmp_dashboard.js', ['jquery'], '1.0', true);

    // CSS
    wp_enqueue_style('pmp-dashboard-css', PMP_PLUGIN_DIR_URL . 'assets/css/pmp_dashboard.css',false,'1.0',true);
}
add_action('admin_enqueue_scripts', 'pmp_dashboard_assets');