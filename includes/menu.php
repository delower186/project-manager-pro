<?php 
if (!defined('ABSPATH')) exit;
// Admin Menu
function wppm_admin_menu() {
    // Top-level Dashboard menu
    add_menu_page(
        'WP Project Manager',      // Page title
        'WP Project Manager',      // Menu title
        'manage_options',          // Capability
        'wppm_dashboard',          // Menu slug
        'wppm_dashboard_page',     // Callback
        'dashicons-clipboard',     // Icon
        6                          // Position
    );

    // Submenus
    add_submenu_page(
        'wppm_dashboard',
        'Projects',
        'Projects',
        'manage_options',
        'edit.php?post_type=wppm_project'
    );

    add_submenu_page(
        'wppm_dashboard',
        'Tasks',
        'Tasks',
        'manage_options',
        'edit.php?post_type=wppm_task'
    );
}
add_action('admin_menu', 'wppm_admin_menu');

// No redirect for parent menu — now dashboard loads


// Dashboard Page Callback
function wppm_dashboard_page() {
    // Load Dashboard Page
    require_once WPPM_PLUGIN_DIR_PATH . 'includes/dashboard.php';
}





