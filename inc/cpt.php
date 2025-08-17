<?php
if (!defined('ABSPATH')) exit;

// Register Project CPT
function wppm_register_project_cpt() {
    $labels = array(
        'name'               => 'Projects',
        'singular_name'      => 'Project',
        'menu_name'          => 'Projects',
        'name_admin_bar'     => 'Project',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Project',
        'new_item'           => 'New Project',
        'edit_item'          => 'Edit Project',
        'view_item'          => 'View Project',
        'all_items'          => 'All Projects',
        'search_items'       => 'Search Projects',
        'not_found'          => 'No projects found.',
        'not_found_in_trash' => 'No projects found in Trash.'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'show_in_menu'       => false,
        'supports'           => array('title', 'editor', 'author'),
        'menu_icon'          => 'dashicons-clipboard',
        'capability_type'    => 'post',
    );

    register_post_type('wppm_project', $args);
}
add_action('init', 'wppm_register_project_cpt');

// Register Task CPT
function wppm_register_task_cpt() {
    $labels = array(
        'name'               => 'Tasks',
        'singular_name'      => 'Task',
        'menu_name'          => 'Tasks',
        'name_admin_bar'     => 'Task',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Task',
        'new_item'           => 'New Task',
        'edit_item'          => 'Edit Task',
        'view_item'          => 'View Task',
        'all_items'          => 'All Tasks',
        'search_items'       => 'Search Tasks',
        'not_found'          => 'No tasks found.',
        'not_found_in_trash' => 'No tasks found in Trash.'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => false,
        'show_in_menu'       => false,
        'supports'           => array('title', 'editor', 'author'),
        'menu_icon'          => 'dashicons-list-view',
        'capability_type'    => 'post',
    );

    register_post_type('wppm_task', $args);
}
add_action('init', 'wppm_register_task_cpt');


// add meta box
// Add Project Meta Box to Task
function wppm_task_project_metabox() {
    add_meta_box(
        'wppm_task_project',
        'Project',
        'wppm_task_project_callback',
        'wppm_task',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'wppm_task_project_metabox');

function wppm_task_project_callback($post) {
    $projects = get_posts(array('post_type' => 'wppm_project', 'numberposts' => -1));
    $selected = get_post_meta($post->ID, '_wppm_project', true);

    echo '<select name="wppm_project" style="width:100%">';
    echo '<option value="">Select Project</option>';
    foreach ($projects as $project) {
        $sel = $selected == $project->ID ? 'selected' : '';
        echo "<option value='{$project->ID}' $sel>{$project->post_title}</option>";
    }
    echo '</select>';
}

// Save Task Project
function wppm_save_task_project($post_id) {
    if (isset($_POST['wppm_project'])) {
        update_post_meta($post_id, '_wppm_project', intval($_POST['wppm_project']));
    }
}
add_action('save_post_wppm_task', 'wppm_save_task_project');

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

