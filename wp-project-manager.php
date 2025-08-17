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

// Include CPTs
require_once WPPM_PLUGIN_DIR_PATH . 'includes/cpt-projects.php';
require_once WPPM_PLUGIN_DIR_PATH . 'includes/cpt-tasks.php';

// Admin Menu
function wppm_admin_menu() {
    add_menu_page(
        'WP Project Manager',
        'WP Project Manager',
        'manage_options',
        'wppm_main_menu',
        '__return_null',
        'dashicons-clipboard',
        6
    );

    // Submenus
    add_submenu_page(
        'wppm_main_menu',
        'Projects',
        'Projects',
        'manage_options',
        'edit.php?post_type=wppm_project'
    );

    add_submenu_page(
        'wppm_main_menu',
        'Tasks',
        'Tasks',
        'manage_options',
        'edit.php?post_type=wppm_task'
    );
}
add_action('admin_menu', 'wppm_admin_menu');

// Redirect parent menu to Projects
function wppm_redirect_main_menu() {
    global $pagenow;
    if ($pagenow === 'admin.php' && isset($_GET['page']) && $_GET['page'] === 'wppm_main_menu') {
        wp_safe_redirect(admin_url('edit.php?post_type=wppm_project'));
        exit;
    }
}
add_action('admin_init', 'wppm_redirect_main_menu');
