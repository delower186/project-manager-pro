<?php
/**
 * Plugin Name: WP Project Manager
 * Description: A lightweight Project & Task Management plugin for WordPress.
 * Version: 1.0.0
 * Author: Your Name
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

define('WPPM_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__));
define('WPPM_PLUGIN_DIR_URL', plugin_dir_url( __FILE__));


function wppm_dashboard_assets($hook) {
    if ($hook !== 'edit.php' && get_post_type() !== 'wppm_project') return;

    wp_enqueue_script('jquery-ui-dialog');
    wp_enqueue_style('jquery-ui-css', '//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css', [], '1.13.2');

    wp_enqueue_script('wppm_project_modal', WPPM_PLUGIN_DIR_URL . 'assets/js/project_modal.js', ['jquery','jquery-ui-dialog'], '1.0', true);

    wp_localize_script('wppm_project_modal', 'wppm_ajax', [
        'nonce' => wp_create_nonce('wppm_action')
    ]);
}
add_action('admin_enqueue_scripts', 'wppm_dashboard_assets');




// Include CPTs
require_once WPPM_PLUGIN_DIR_PATH . 'includes/cpt-projects.php';
require_once WPPM_PLUGIN_DIR_PATH . 'includes/cpt-tasks.php';
require_once WPPM_PLUGIN_DIR_PATH . 'includes/menu.php';
require_once WPPM_PLUGIN_DIR_PATH . 'project/project_view.php';

