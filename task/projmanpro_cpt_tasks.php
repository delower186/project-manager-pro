<?php
if (!defined('ABSPATH')) exit;
// Register Tasks CPT
function projmanpro_register_task_cpt() {
    $labels = [
      'name' => __('Tasks','project-manager-pro') ,
      'singular_name' => __('Task','project-manager-pro') ,
      'add_new'                  => __( 'Add New', 'project-manager-pro' ),
      'add_new_item'             => __( 'Add New Task', 'project-manager-pro' ),
      'edit_item'                => __( 'Edit Task', 'project-manager-pro' ),
      'new_item'                 => __( 'New Task', 'project-manager-pro' ),
      'view_item'                => __( 'View Task', 'project-manager-pro' ),
      'view_items'               => __( 'View Tasks', 'project-manager-pro' ),
      'search_items'             => __( 'Search Tasks', 'project-manager-pro' ),
      'not_found'                => __( 'No Tasks found.', 'project-manager-pro' ),
      'not_found_in_trash'       => __( 'No Tasks found in Trash.', 'project-manager-pro' ),
      'parent_item_colon'        => __( 'Parent Tasks:', 'project-manager-pro' ),
      'all_items'                => __( 'All Tasks', 'project-manager-pro' ),
      'archives'                 => __( 'Task Archives', 'project-manager-pro' ),
      'attributes'               => __( 'Task Attributes', 'project-manager-pro' ),
      'insert_into_item'         => __( 'Insert into Task', 'project-manager-pro' ),
      'uploaded_to_this_item'    => __( 'Uploaded to this Task', 'project-manager-pro' ),
      'featured_image'           => __( 'Featured Image', 'project-manager-pro' ),
      'set_featured_image'       => __( 'Set featured image', 'project-manager-pro' ),
      'remove_featured_image'    => __( 'Remove featured image', 'project-manager-pro' ),
      'use_featured_image'       => __( 'Use as featured image', 'project-manager-pro' ),
      'menu_name'                => __( 'WP Task', 'project-manager-pro' ),
      'filter_items_list'        => __( 'Filter Task list', 'project-manager-pro' ),
      'filter_by_date'           => __( 'Filter by date', 'project-manager-pro' ),
      'items_list_navigation'    => __( 'Tasks list navigation', 'project-manager-pro' ),
      'items_list'               => __( 'Tasks list', 'project-manager-pro' ),
      'item_published'           => __( 'Task published.', 'project-manager-pro' ),
      'item_published_privately' => __( 'Task published privately.', 'project-manager-pro' ),
      'item_reverted_to_draft'   => __( 'Task reverted to draft.', 'project-manager-pro' ),
      'item_scheduled'           => __( 'Task scheduled.', 'project-manager-pro' ),
      'item_updated'             => __( 'Task updated.', 'project-manager-pro' ),
      'item_link'                => __( 'Task Link', 'project-manager-pro' ),
      'item_link_description'    => __( 'A link to an Task.', 'project-manager-pro' ),
    ];
    $args = [
        'labels' => $labels,
        'description'           => __( 'organize and manage company Tasks', 'project-manager-pro' ),
        'public'                => false,
        'hierarchical'          => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => false,
        'show_ui'               => true,
        'show_in_menu'          => false,
        'show_in_nav_menus'     => false,
        'show_in_admin_bar'     => false,
        'show_in_rest'          => true,
        'menu_position'         => 7,
        'menu_icon'             => 'dashicons-list-view',
        'capability_type'       => 'post',
        'capabilities'          => array(),
        'supports'              => array( 'title', 'editor', 'revisions', 'author', 'comments' ),
        'taxonomies'            => array(),
        'has_archive'           => true,
        'query_var'             => true,
        'can_export'            => true,
        'delete_with_user'      => false,
        'template'              => array(),
        'template_lock'         => false,
    ];
    register_post_type('projmanpro_task', $args);
}
add_action('init', 'projmanpro_register_task_cpt');

// Add Meta Boxes for Tasks
function projmanpro_task_meta_boxes() {
    add_meta_box('projmanpro_task_details', 'Task Details', 'projmanpro_task_meta_callback', 'projmanpro_task', 'normal', 'default');
}
add_action('add_meta_boxes', 'projmanpro_task_meta_boxes');

function projmanpro_task_meta_callback($post) {

    wp_nonce_field('projmanpro_save_task_meta_action', 'projmanpro_task_meta_nonce');

    $status   = get_post_meta($post->ID, '_projmanpro_task_status', true);
    $priority = get_post_meta($post->ID, '_projmanpro_task_priority', true);
    $due_date = get_post_meta($post->ID, '_projmanpro_task_due_date', true);
    $assigned = get_post_meta($post->ID, '_projmanpro_task_assigned', true);
    $related_project = get_post_meta($post->ID, '_projmanpro_related_project', true);

    // Fetch all users
    $users = get_users();
    // Fetch all projects
    $projects = get_posts(['post_type' => 'projmanpro_project', 'numberposts' => -1]);
    ?>
    <p>
        <label>Status:</label><br>
        <select name="projmanpro_task_status">
            <option value="pending" <?php selected($status, 'pending'); ?>>Pending</option>
            <option value="in_progress" <?php selected($status, 'in_progress'); ?>>In Progress</option>
            <option value="completed" <?php selected($status, 'completed'); ?>>Completed</option>
        </select>
    </p>
    <p>
        <label>Priority:</label><br>
        <select name="projmanpro_task_priority">
            <option value="low" <?php selected($priority, 'low'); ?>>Low</option>
            <option value="medium" <?php selected($priority, 'medium'); ?>>Medium</option>
            <option value="high" <?php selected($priority, 'high'); ?>>High</option>
        </select>
    </p>
    <p>
        <label>Due Date:</label><br>
        <input type="date" name="projmanpro_task_due_date" value="<?php echo esc_attr($due_date); ?>">
    </p>
    <p>
        <label>Assigned User:</label><br>
        <select name="projmanpro_task_assigned">
            <option value="">-- Select User --</option>
            <?php foreach ($users as $user): ?>
                <option value="<?php echo esc_attr($user->ID); ?>" <?php selected($assigned, $user->ID); ?>>
                    <?php echo esc_html($user->display_name); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    <p>
        <label>Related Project:</label><br>
        <select name="projmanpro_related_project">
            <option value="">-- Select Project --</option>
            <?php foreach ($projects as $project): ?>
                <option value="<?php echo esc_attr($project->ID); ?>" <?php selected($related_project, $project->ID); ?>>
                    <?php echo esc_html($project->post_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    <?php
}

function projmanpro_save_task_meta($post_id) {
    // Check if our nonce is set.
    if (!isset($_POST['projmanpro_task_meta_nonce'])) {
        return;
    }

    // Verify the nonce.
    if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['projmanpro_task_meta_nonce'])), 'projmanpro_save_task_meta_action')) {
        return;
    }

    // Prevent autosave from overwriting.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check user capability (optional but recommended).
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Now process fields safely.
    if (isset($_POST['projmanpro_task_status'])) {
        update_post_meta($post_id, '_projmanpro_task_status', sanitize_text_field(wp_unslash($_POST['projmanpro_task_status'])));
    }

    if (isset($_POST['projmanpro_task_priority'])) {
        update_post_meta($post_id, '_projmanpro_task_priority', sanitize_text_field(wp_unslash($_POST['projmanpro_task_priority'])));
    }

    if (isset($_POST['projmanpro_task_due_date'])) {
        update_post_meta($post_id, '_projmanpro_task_due_date', sanitize_text_field(wp_unslash($_POST['projmanpro_task_due_date'])));
    }

    if (isset($_POST['projmanpro_task_assigned'])) {
        update_post_meta($post_id, '_projmanpro_task_assigned', intval($_POST['projmanpro_task_assigned']));
    }

    if (isset($_POST['projmanpro_related_project'])) {
        update_post_meta($post_id, '_projmanpro_related_project', intval($_POST['projmanpro_related_project']));
    }
}
add_action('save_post', 'projmanpro_save_task_meta');



// custom columns
// Add columns to Tasks list
function projmanpro_task_columns($columns) {
    $columns['status']   = 'Status';
    $columns['priority'] = 'Priority';
    $columns['due_date'] = 'Due Date';
    $columns['assigned'] = 'Assigned To';
    $columns['related']  = 'Related Project';
    $columns['countdown'] = 'Time Left';
    return $columns;
}
add_filter('manage_projmanpro_task_posts_columns', 'projmanpro_task_columns');

// Render column values for Tasks
function projmanpro_task_column_content($column, $post_id) {
    switch ($column) {
        case 'status':
            $status = get_post_meta($post_id, '_projmanpro_task_status', true);
            $color = 'gray';
            if ($status === 'pending') $color = '#ff9800';
            elseif ($status === 'in_progress') $color = '#2196f3';
            elseif ($status === 'completed') $color = '#4caf50';
            elseif ($status === 'on_hold') $color = '#9c27b0';

            echo '<span style="display:inline-block;padding:2px 6px;border-radius:4px;background:' 
                . esc_attr($color) . ';color:#fff;font-weight:bold;">' 
                . esc_html(ucfirst($status)) . '</span>';
            break;

        case 'priority':
            $priority = get_post_meta($post_id, '_projmanpro_task_priority', true);
            $color = 'gray';
            if ($priority === 'low') $color = '#4caf50';
            elseif ($priority === 'medium') $color = '#ff9800';
            elseif ($priority === 'high') $color = '#f44336';
            elseif ($priority === 'urgent') $color = '#e91e63';

            echo '<span style="display:inline-block;padding:2px 6px;border-radius:4px;background:' 
                . esc_attr($color) . ';color:#fff;font-weight:bold;">' 
                . esc_html(ucfirst($priority)) . '</span>';
            break;

        case 'due_date':
            echo esc_html(get_post_meta($post_id, '_projmanpro_task_due_date', true));
            break;

        case 'assigned':
            $user_id = get_post_meta($post_id, '_projmanpro_task_assigned', true);
            $user    = $user_id ? get_userdata($user_id) : null;
            echo $user ? esc_html($user->display_name) : '—';
            break;

        case 'related':
            $proj_id = get_post_meta($post_id, '_projmanpro_related_project', true);
            $proj    = $proj_id ? get_post($proj_id) : null;
            echo $proj ? esc_html($proj->post_title) : '—';
            break;

        case 'countdown':
            $due_date = get_post_meta($post_id, '_projmanpro_task_due_date', true);
            $status   = get_post_meta($post_id, '_projmanpro_task_status', true);
            $color = 'gray';
            if ($status === 'pending') $color = '#ff9800';
            elseif ($status === 'in_progress') $color = '#2196f3';
            elseif ($status === 'completed') $color = '#4caf50';
            elseif ($status === 'on_hold') $color = '#9c27b0';

            if ($due_date) {
                echo '<span class="pmp-countdown" data-due="' . esc_attr($due_date) 
                    . '" data-status="' . esc_attr($status) 
                    . '" style="display:inline-block;padding:2px 6px;border-radius:4px;background:' 
                    . esc_attr($color) . ';color:#fff;font-weight:bold;">&nbsp;</span>';
            } else {
                echo '—';
            }
            break;
    }
}
add_action('manage_projmanpro_task_posts_custom_column', 'projmanpro_task_column_content', 10, 2);


// Make columns sortable in Tasks table
// 1. Register sortable columns
function projmanpro_task_sortable_columns($columns) {
    $columns['status']   = 'projmanpro_task_status';
    $columns['priority'] = 'projmanpro_task_priority';
    $columns['due_date'] = 'projmanpro_task_due_date';
    $columns['assigned'] = 'projmanpro_task_assigned';
    $columns['related']  = 'projmanpro_related_project';
    return $columns;
}
add_filter('manage_edit-projmanpro_task_sortable_columns', 'projmanpro_task_sortable_columns');

// 2. Modify query for sorting
function projmanpro_task_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    $orderby = $query->get('orderby');

    switch ($orderby) {
        case 'projmanpro_task_status':
            $query->set('meta_key', '_projmanpro_task_status');
            $query->set('orderby', 'meta_value');
            break;
        case 'projmanpro_task_priority':
            $query->set('meta_key', '_projmanpro_task_priority');
            $query->set('orderby', 'meta_value');
            break;
        case 'projmanpro_task_due_date':
            $query->set('meta_key', '_projmanpro_task_due_date');
            $query->set('orderby', 'meta_value');
            break;
        case 'projmanpro_task_assigned':
            $query->set('meta_key', '_projmanpro_task_assigned');
            $query->set('orderby', 'meta_value_num');
            break;
        case 'projmanpro_related_project':
            $query->set('meta_key', '_projmanpro_related_project');
            $query->set('orderby', 'meta_value_num');
            break;
    }
}
add_action('pre_get_posts', 'projmanpro_task_orderby');


// Add filters above Tasks table
function projmanpro_task_filters() {
    global $typenow;
    if ($typenow !== 'projmanpro_task') {
        return;
    }

    // Add nonce field (printed in the filter form)
    wp_nonce_field('projmanpro_task_filters_action', 'projmanpro_task_filters_nonce');

    // Initialize defaults
    $current_status = '';
    $current_user   = '';
    $current_proj   = '';

    // Verify nonce before processing $_GET values
    if (
        isset($_GET['projmanpro_task_filters_nonce']) &&
        wp_verify_nonce(
            sanitize_text_field(wp_unslash($_GET['projmanpro_task_filters_nonce'])),
            'projmanpro_task_filters_action'
        )
    ) {
        $current_status = isset($_GET['_projmanpro_task_status'])
            ? sanitize_text_field(wp_unslash($_GET['_projmanpro_task_status']))
            : '';

        $current_user = isset($_GET['_projmanpro_task_assigned'])
            ? intval($_GET['_projmanpro_task_assigned'])
            : '';

        $current_proj = isset($_GET['_projmanpro_related_project'])
            ? intval($_GET['_projmanpro_related_project'])
            : '';
    }

    // Status filter
    $statuses = [
        'pending'     => 'Pending',
        'in_progress' => 'In Progress',
        'completed'   => 'Completed',
    ];
    echo '<select name="_projmanpro_task_status"><option value="">All Statuses</option>';
    foreach ($statuses as $key => $label) {
        printf(
            '<option value="%s"%s>%s</option>',
            esc_attr($key),
            selected($current_status, $key, false),
            esc_html($label)
        );
    }
    echo '</select>';

    // Assigned User filter
    $users = get_users();
    echo '<select name="_projmanpro_task_assigned"><option value="">All Users</option>';
    foreach ($users as $user) {
        printf(
            '<option value="%d"%s>%s</option>',
            esc_attr($user->ID),
            selected($current_user, $user->ID, false),
            esc_html($user->display_name)
        );
    }
    echo '</select>';

    // Related Project filter
    $projects = get_posts([
        'post_type'   => 'projmanpro_project',
        'numberposts' => -1,
    ]);
    echo '<select name="_projmanpro_related_project"><option value="">All Projects</option>';
    foreach ($projects as $proj) {
        printf(
            '<option value="%d"%s>%s</option>',
            esc_attr($proj->ID),
            selected($current_proj, $proj->ID, false),
            esc_html($proj->post_title)
        );
    }
    echo '</select>';
}
add_action('restrict_manage_posts', 'projmanpro_task_filters');



// Filter Tasks query
function projmanpro_task_filters_query($query) {
    global $pagenow, $typenow;

    if ($pagenow === 'edit.php' && $typenow === 'projmanpro_task' && $query->is_main_query()) {

        // Verify nonce
        if (!isset($_GET['projmanpro_task_filters_nonce']) || 
            !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['projmanpro_task_filters_nonce'])), 'projmanpro_task_filters_action')) {
            return; // Nonce failed, bail out
        }

        if (!empty($_GET['_projmanpro_task_status'])) {
            $query->set('meta_query', [
                [
                    'key'   => '_projmanpro_task_status',
                    'value' => sanitize_text_field(wp_unslash($_GET['_projmanpro_task_status']))
                ]
            ]);
        }

        if (!empty($_GET['_projmanpro_task_assigned'])) {
            $query->set('meta_query', [
                [
                    'key'   => '_projmanpro_task_assigned',
                    'value' => intval($_GET['_projmanpro_task_assigned'])
                ]
            ]);
        }

        if (!empty($_GET['_projmanpro_related_project'])) {
            $query->set('meta_query', [
                [
                    'key'   => '_projmanpro_related_project',
                    'value' => intval($_GET['_projmanpro_related_project'])
                ]
            ]);
        }
    }
}
add_action('pre_get_posts', 'projmanpro_task_filters_query');


// Remove Comments column from Project CPT list table
function projmanpro_remove_task_comments_column($columns) {
    if (isset($columns['comments'])) {
        unset($columns['comments']);
    }
    return $columns;
}
add_filter('manage_projmanpro_task_posts_columns', 'projmanpro_remove_task_comments_column');
