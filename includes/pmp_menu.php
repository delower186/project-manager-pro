<?php 
if (!defined('ABSPATH')) exit;
// Admin Menu
function pmp_admin_menu() {
    // Top-level Dashboard menu
    add_menu_page(
        'Project Manager Pro',      // Page title
        'Project Manager Pro',      // Menu title
        'manage_options',          // Capability
        'pmp_dashboard',          // Menu slug
        'pmp_dashboard_page',     // Callback
        'dashicons-clipboard',     // Icon
        6                          // Position
    );

    // Submenus
    add_submenu_page(
        'pmp_dashboard',
        'Projects',
        'Projects',
        'manage_options',
        'edit.php?post_type=pmp_project'
    );

    add_submenu_page(
        'pmp_dashboard',
        'Tasks',
        'Tasks',
        'manage_options',
        'edit.php?post_type=pmp_task'
    );
}
add_action('admin_menu', 'pmp_admin_menu');

// No redirect for parent menu — now dashboard loads


// Dashboard Page Callback
function pmp_dashboard_page() {
    // Load Dashboard Page
    require_once PMP_PLUGIN_DIR_PATH . 'includes/pmp_dashboard.php';
}





