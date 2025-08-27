<?php 
if (!defined('ABSPATH')) exit;
// Enqueue Countdown JS & CSS
function projmanpro_countdown_assets($hook) {
    // Only load on Projects and Tasks list pages
    if (!in_array($hook, ['edit.php'])) return;

    global $typenow;
    if(!in_array($typenow, ['projmanpro_project','projmanpro_task'])) return;

    // JS
    wp_enqueue_script('projmanpro_countdown_js', PROJMANPRO_DIR_URL . 'assets/js/projmanpro_countdown.js', ['jquery'], '1.0', true);
    // CSS
    wp_enqueue_style('projmanpro_countdown_css', PROJMANPRO_DIR_URL . 'assets/css/projmanpro_countdown.css',false,'1.0', true);
}
add_action('admin_enqueue_scripts', 'projmanpro_countdown_assets');

function projmanpro_modal_assets($hook) {
    // Only load for the project list screen

    if ($hook !== 'edit.php') return;
    $screen = get_current_screen();
    if(in_array($screen->post_type, ['projmanpro_project', 'projmanpro_task'])){
        // CSS
        wp_enqueue_style('jquery-ui-css', PROJMANPRO_DIR_URL . 'assets/vendors/jquery-ui-1.14.1/jquery-ui.css', [], '1.14.1');

        // JS
        wp_enqueue_script('jquery-ui-dialog'); // required for jQuery UI modal
        wp_enqueue_script('projmanpro_project_modal', PROJMANPRO_DIR_URL . 'assets/js/projmanpro_project_modal.js', ['jquery','jquery-ui-dialog'], '1.0', true);
        wp_enqueue_script('projmanpro_task_modal', PROJMANPRO_DIR_URL . 'assets/js/projmanpro_task_modal.js', ['jquery','jquery-ui-dialog'], '1.0', true);

        // Localize for both scripts
        $localize = [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('projmanpro_action')
        ];
        wp_localize_script('projmanpro_project_modal', 'projmanpro_ajax', $localize);
        wp_localize_script('projmanpro_task_modal', 'projmanpro_ajax', $localize);
    }
}
add_action('admin_enqueue_scripts', 'projmanpro_modal_assets');


function projmanpro_dashboard_assets($hook) {
    // Only load on WP Project Manager dashboard
    if ($hook !== 'toplevel_page_projmanpro_dashboard') return;

    // JS
    wp_enqueue_script('projmanpro_dashboard_js', PROJMANPRO_DIR_URL . 'assets/js/projmanpro_dashboard.js', ['jquery'], '1.0', true);

    // CSS
    wp_enqueue_style('projmanpro_dashboard_css', PROJMANPRO_DIR_URL . 'assets/css/projmanpro_dashboard.css',false,'1.0',true);
}
add_action('admin_enqueue_scripts', 'projmanpro_dashboard_assets');