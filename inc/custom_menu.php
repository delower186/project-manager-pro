<?php 
defined('ABSPATH') or die('Hey, What are you doing here? You Silly Man!');
// customize menu
// Add WP Project Manager top-level menu
function wppm_admin_menu() {
    // Main menu points to Projects CPT
    add_menu_page(
        'WP Project Manager',       // Page title
        'WP Project Manager',       // Menu title
        'manage_options',           // Capability
        'edit.php?post_type=wppm_project', // Menu slug points directly to Projects
        '',                         // Callback not needed
        'dashicons-clipboard',      // Icon
        6                           // Position
    );

    // Submenu: Projects (first submenu)
    add_submenu_page(
        'edit.php?post_type=wppm_project', // Parent slug same as menu slug
        'Projects',                        // Page title
        'Projects',                        // Menu title
        'manage_options',                   // Capability
        'edit.php?post_type=wppm_project'  // Link to Projects CPT
    );

    // Submenu: Tasks
    add_submenu_page(
        'edit.php?post_type=wppm_project', // Parent slug
        'Tasks',                            // Page title
        'Tasks',                            // Menu title
        'manage_options',                   // Capability
        'edit.php?post_type=wppm_task'     // Link to Tasks CPT
    );
}
add_action('admin_menu', 'wppm_admin_menu');