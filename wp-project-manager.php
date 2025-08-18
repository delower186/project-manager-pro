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
    // Only load on WP Project Manager dashboard
    if ($hook !== 'toplevel_page_wppm_dashboard') return;

    // JS
    wp_enqueue_script('wppm-dashboard-js', WPPM_PLUGIN_DIR_URL . 'assets/js/dashboard.js', ['jquery'], '1.0', true);

    // CSS
    wp_enqueue_style('wppm-dashboard-css', WPPM_PLUGIN_DIR_URL . 'assets/css/dashboard.css');
}
add_action('admin_enqueue_scripts', 'wppm_dashboard_assets');


// Include CPTs
require_once WPPM_PLUGIN_DIR_PATH . 'includes/cpt-projects.php';
require_once WPPM_PLUGIN_DIR_PATH . 'includes/cpt-tasks.php';
require_once WPPM_PLUGIN_DIR_PATH . 'includes/menu.php';

