<?php
defined('ABSPATH') or die('Hey, What are you doing here? You Silly Man!');

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

