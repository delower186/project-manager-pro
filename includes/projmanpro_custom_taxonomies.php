<?php 
/**
 * Register custom taxonomies for Status & Priority
 * CPT - projmanpro_project
 */
function projmanpro_register_taxonomies() {
    // Project Status
    register_taxonomy('projmanpro_project_status', 'projmanpro_project', [
        'labels' => [
            'name'          => 'Statuses',
            'singular_name' => 'Status',
        ],
        'public'       => false,
        'show_ui'      => false, // hide default taxonomy meta box
        'hierarchical' => false,
    ]);

    // Task Status
    register_taxonomy('projmanpro_task_status', 'projmanpro_task', [
        'labels' => [
            'name'          => 'Statuses',
            'singular_name' => 'Status',
        ],
        'public'       => false,
        'show_ui'      => false, // hide default taxonomy meta box
        'hierarchical' => false,
    ]);

    // Project Priority
    register_taxonomy('projmanpro_project_priority', 'projmanpro_project', [
        'labels' => [
            'name'          => 'Priorities',
            'singular_name' => 'Priority',
        ],
        'public'       => false,
        'show_ui'      => false, // hide default taxonomy meta box
        'hierarchical' => false,
    ]);

    // Task Priority
    register_taxonomy('projmanpro_task_priority', 'projmanpro_task', [
        'labels' => [
            'name'          => 'Priorities',
            'singular_name' => 'Priority',
        ],
        'public'       => false,
        'show_ui'      => false, // hide default taxonomy meta box
        'hierarchical' => false,
    ]);
}
add_action('init', 'projmanpro_register_taxonomies');


/**
 * Pre-populate default taxonomy terms on plugin activation
 */
function projmanpro_register_default_terms() {
    // Project Statuses
    $statuses = ['pending','in_progress','completed'];
    foreach ($statuses as $status) {
        if (!term_exists($status, 'projmanpro_project_status')) {
            wp_insert_term($status, 'projmanpro_project_status');
        }
    }

    // Task Statuses
    $statuses = ['pending','in_progress','completed'];
    foreach ($statuses as $status) {
        if (!term_exists($status, 'projmanpro_task_status')) {
            wp_insert_term($status, 'projmanpro_task_status');
        }
    }

    // Project Priorities
    $priorities = ['low','medium','high'];
    foreach ($priorities as $priority) {
        if (!term_exists($priority, 'projmanpro_project_priority')) {
            wp_insert_term($priority, 'projmanpro_project_priority');
        }
    }

    // Task Priorities
    $priorities = ['low','medium','high'];
    foreach ($priorities as $priority) {
        if (!term_exists($priority, 'projmanpro_task_priority')) {
            wp_insert_term($priority, 'projmanpro_task_priority');
        }
    }
}
add_action('init', 'projmanpro_register_default_terms');