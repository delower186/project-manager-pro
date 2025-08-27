<?php 
if (!defined('ABSPATH')) exit;
// Admin Menu
function projmanpro_admin_menu() {
    // Top-level Dashboard menu
    add_menu_page(
        'Project Manager Pro',      // Page title
        'Project Manager Pro',      // Menu title
        'manage_options',          // Capability
        'projmanpro_dashboard',          // Menu slug
        'projmanpro_dashboard_page',     // Callback
        'dashicons-clipboard',     // Icon
        6                          // Position
    );

    // Submenus
    add_submenu_page(
        'projmanpro_dashboard',
        'Projects',
        'Projects',
        'manage_options',
        'edit.php?post_type=projmanpro_project'
    );

    add_submenu_page(
        'projmanpro_dashboard',
        'Tasks',
        'Tasks',
        'manage_options',
        'edit.php?post_type=projmanpro_task'
    );
}
add_action('admin_menu', 'projmanpro_admin_menu');

// No redirect for parent menu — now dashboard loads


// Dashboard Page Callback
function projmanpro_dashboard_page() {
    // Load Dashboard Page
    require_once PROJMANPRO_DIR_PATH . 'includes/projmanpro_dashboard.php';
}





