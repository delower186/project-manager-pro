<?php
// Register Projects CPT
function wppm_register_project_cpt() {
    $labels = [
      'name' => __('Projects','wp-project-manager') ,
      'singular_name' => __('Project','wp-project-manager') ,
      'add_new'                  => __( 'Add New', 'wp-project-manager' ),
      'add_new_item'             => __( 'Add New Project', 'wp-project-manager' ),
      'edit_item'                => __( 'Edit Project', 'wp-project-manager' ),
      'new_item'                 => __( 'New Project', 'wp-project-manager' ),
      'view_item'                => __( 'View Project', 'wp-project-manager' ),
      'view_items'               => __( 'View Projects', 'wp-project-manager' ),
      'search_items'             => __( 'Search Projects', 'wp-project-manager' ),
      'not_found'                => __( 'No Projects found.', 'wp-project-manager' ),
      'not_found_in_trash'       => __( 'No Projects found in Trash.', 'wp-project-manager' ),
      'parent_item_colon'        => __( 'Parent Projects:', 'wp-project-manager' ),
      'all_items'                => __( 'All Projects', 'wp-project-manager' ),
      'archives'                 => __( 'Project Archives', 'wp-project-manager' ),
      'attributes'               => __( 'Project Attributes', 'wp-project-manager' ),
      'insert_into_item'         => __( 'Insert into Project', 'wp-project-manager' ),
      'uploaded_to_this_item'    => __( 'Uploaded to this Project', 'wp-project-manager' ),
      'featured_image'           => __( 'Featured Image', 'wp-project-manager' ),
      'set_featured_image'       => __( 'Set featured image', 'wp-project-manager' ),
      'remove_featured_image'    => __( 'Remove featured image', 'wp-project-manager' ),
      'use_featured_image'       => __( 'Use as featured image', 'wp-project-manager' ),
      'menu_name'                => __( 'WP Project', 'wp-project-manager' ),
      'filter_items_list'        => __( 'Filter Project list', 'wp-project-manager' ),
      'filter_by_date'           => __( 'Filter by date', 'wp-project-manager' ),
      'items_list_navigation'    => __( 'Projects list navigation', 'wp-project-manager' ),
      'items_list'               => __( 'Projects list', 'wp-project-manager' ),
      'item_published'           => __( 'Project published.', 'wp-project-manager' ),
      'item_published_privately' => __( 'Project published privately.', 'wp-project-manager' ),
      'item_reverted_to_draft'   => __( 'Project reverted to draft.', 'wp-project-manager' ),
      'item_scheduled'           => __( 'Project scheduled.', 'wp-project-manager' ),
      'item_updated'             => __( 'Project updated.', 'wp-project-manager' ),
      'item_link'                => __( 'Project Link', 'wp-project-manager' ),
      'item_link_description'    => __( 'A link to an Project.', 'wp-project-manager' ),
    ];
    $args = [
        'labels' => $labels,
        'description'           => __( 'organize and manage company Projects', 'wp-project-manager' ),
        'public'                => false,
        'hierarchical'          => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => false,
        'show_ui'               => true,
        'show_in_menu'          => false,
        'show_in_nav_menus'     => false,
        'show_in_admin_bar'     => false,
        'show_in_rest'          => true,
        'menu_position'         => 6,
        'menu_icon'             => 'dashicons-portfolio',
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
    register_post_type('wppm_project', $args);
}
add_action('init', 'wppm_register_project_cpt');

// Add Meta Boxes for Projects
function wppm_project_meta_boxes() {
    add_meta_box('wppm_project_details', 'Project Details', 'wppm_project_meta_callback', 'wppm_project', 'normal', 'default');
}
add_action('add_meta_boxes', 'wppm_project_meta_boxes');

function wppm_project_meta_callback($post) {
    $status    = get_post_meta($post->ID, '_wppm_project_status', true);
    $priority  = get_post_meta($post->ID, '_wppm_project_priority', true);
    $due_date  = get_post_meta($post->ID, '_wppm_project_due_date', true);
    $assigned  = get_post_meta($post->ID, '_wppm_project_assigned', true);

    // Get users
    $users = get_users();

    // Check if project has incomplete tasks
    $incomplete_tasks = new WP_Query([
        'post_type' => 'wppm_task',
        'meta_key' => '_wppm_related_project',
        'meta_value' => $post->ID,
        'meta_query' => [
            [
                'key' => '_wppm_task_status',
                'value' => ['pending', 'in_progress'],
                'compare' => 'IN'
            ]
        ],
        'posts_per_page' => 1
    ]);

    $disable_completed = $incomplete_tasks->found_posts > 0 ? 'disabled' : '';
    $completed_note = $disable_completed ? ' (Cannot complete, tasks pending)' : '';
    ?>
    <p>
        <label>Status:</label><br>
        <select name="wppm_project_status">
            <option value="pending" <?php selected($status, 'pending'); ?>>Pending</option>
            <option value="in_progress" <?php selected($status, 'in_progress'); ?>>In Progress</option>
            <option value="completed" <?php selected($status, 'completed'); ?> <?php echo $disable_completed; ?>>
                Completed<?php echo $completed_note; ?>
            </option>
        </select>
    </p>

    <p>
        <label>Priority:</label><br>
        <select name="wppm_project_priority">
            <option value="low" <?php selected($priority, 'low'); ?>>Low</option>
            <option value="medium" <?php selected($priority, 'medium'); ?>>Medium</option>
            <option value="high" <?php selected($priority, 'high'); ?>>High</option>
        </select>
    </p>

    <p>
        <label>Due Date:</label><br>
        <input type="date" name="wppm_project_due_date" value="<?php echo esc_attr($due_date); ?>">
    </p>

    <p>
        <label>Assigned User:</label><br>
        <select name="wppm_project_assigned">
            <option value="">-- Select User --</option>
            <?php foreach ($users as $user): ?>
                <option value="<?php echo esc_attr($user->ID); ?>" <?php selected($assigned, $user->ID); ?>>
                    <?php echo esc_html($user->display_name); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    <?php
}


function wppm_save_project_meta($post_id) {
    if (array_key_exists('wppm_project_status', $_POST)) {
        update_post_meta($post_id, '_wppm_project_status', sanitize_text_field($_POST['wppm_project_status']));
    }
    if (array_key_exists('wppm_project_priority', $_POST)) {
        update_post_meta($post_id, '_wppm_project_priority', sanitize_text_field($_POST['wppm_project_priority']));
    }
    if (array_key_exists('wppm_project_due_date', $_POST)) {
        update_post_meta($post_id, '_wppm_project_due_date', sanitize_text_field($_POST['wppm_project_due_date']));
    }
    if (array_key_exists('wppm_project_assigned', $_POST)) {
        update_post_meta($post_id, '_wppm_project_assigned', intval($_POST['wppm_project_assigned']));
    }
}
add_action('save_post', 'wppm_save_project_meta');



// custom columns
// Add columns to Projects list
function wppm_project_columns($columns) {
    $columns['status']   = 'Status';
    $columns['priority'] = 'Priority';
    $columns['due_date'] = 'Due Date';
    $columns['assigned'] = 'Assigned To'; // new column
    $columns['countdown'] = 'Time Left';
    return $columns;
}
add_filter('manage_wppm_project_posts_columns', 'wppm_project_columns');

// Render column values for Projects with color badges
function wppm_project_column_content($column, $post_id) {
    switch ($column) {
        case 'status':
            $status = get_post_meta($post_id, '_wppm_project_status', true);
            $color = 'gray';
            if ($status === 'pending') $color = '#ff9800';
            elseif ($status === 'in_progress') $color = '#2196f3';
            elseif ($status === 'completed') $color = '#4caf50';
            echo '<span style="display:inline-block;padding:2px 6px;border-radius:4px;background:' . esc_attr($color) . ';color:#fff;font-weight:bold;">' . ucfirst($status) . '</span>';
            break;

        case 'priority':
            $priority = get_post_meta($post_id, '_wppm_project_priority', true);
            $color = 'gray';
            if ($priority === 'low') $color = '#4caf50';
            elseif ($priority === 'medium') $color = '#ff9800';
            elseif ($priority === 'high') $color = '#f44336';
            echo '<span style="display:inline-block;padding:2px 6px;border-radius:4px;background:' . esc_attr($color) . ';color:#fff;font-weight:bold;">' . ucfirst($priority) . '</span>';
            break;

        case 'due_date':
            echo esc_html(get_post_meta($post_id, '_wppm_project_due_date', true));
            break;

        case 'assigned':
            $user_id = get_post_meta($post_id, '_wppm_project_assigned', true);
            $user    = $user_id ? get_userdata($user_id) : null;
            echo $user ? esc_html($user->display_name) : '—';
            break;

        case 'countdown':
            $due_date = get_post_meta($post_id, '_wppm_project_due_date', true);
            $status   = get_post_meta($post_id,'_wppm_project_status',true);
            $color = 'gray';
            if ($status === 'pending') $color = '#ff9800';
            elseif ($status === 'in_progress') $color = '#2196f3';
            elseif ($status === 'completed') $color = '#4caf50';

            if ($due_date) {
                echo '<span class="wppm-countdown" data-due="' . esc_attr($due_date) . '" data-status="'.$status.'" style="display:inline-block;padding:2px 6px;border-radius:4px;background:' . esc_attr($color) . ';color:#fff;font-weight:bold;">&nbsp;</span>';
            } else {
                echo '—';
            }
            break;
    }
}
add_action('manage_wppm_project_posts_custom_column', 'wppm_project_column_content', 10, 2);



// Make Projects Columns Sortable
// Make columns sortable in Projects table
function wppm_project_sortable_columns($columns) {
    $columns['status']   = 'status';
    $columns['priority'] = 'priority';
    $columns['due_date'] = 'due_date';
    $columns['assigned'] = 'assigned';
    return $columns;
}
add_filter('manage_edit-wppm_project_sortable_columns', 'wppm_project_sortable_columns');

// Handle sorting by meta
function wppm_project_orderby($query) {
    if(!is_admin()) return;

    $orderby = $query->get('orderby');

    if($orderby == 'status') {
        $query->set('meta_key', '_wppm_project_status');
        $query->set('orderby', 'meta_value');
    }
    elseif($orderby == 'priority') {
        $query->set('meta_key', '_wppm_project_priority');
        $query->set('orderby', 'meta_value');
    }
    elseif($orderby == 'due_date') {
        $query->set('meta_key', '_wppm_project_due_date');
        $query->set('orderby', 'meta_value');
    }
    elseif($orderby == 'assigned') {
        $query->set('meta_key', '_wppm_project_assigned');
        $query->set('orderby', 'meta_value_num');
    }
}
add_action('pre_get_posts', 'wppm_project_orderby');

// Add Filters for Projects Table
// Add dropdown filters above Projects table
function wppm_project_filters() {
    global $typenow;
    if ($typenow !== 'wppm_project') return;

    // Status filter
    $statuses = ['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed'];
    $current_status = isset($_GET['_wppm_project_status']) ? $_GET['_wppm_project_status'] : '';
    echo '<select name="_wppm_project_status"><option value="">All Statuses</option>';
    foreach($statuses as $key => $label) {
        printf('<option value="%s"%s>%s</option>', $key, selected($current_status, $key, false), $label);
    }
    echo '</select>';

    // Assigned User filter
    $users = get_users();
    $current_user = isset($_GET['_wppm_project_assigned']) ? $_GET['_wppm_project_assigned'] : '';
    echo '<select name="_wppm_project_assigned"><option value="">All Users</option>';
    foreach($users as $user) {
        printf('<option value="%d"%s>%s</option>', $user->ID, selected($current_user, $user->ID, false), $user->display_name);
    }
    echo '</select>';
}
add_action('restrict_manage_posts', 'wppm_project_filters');

// Filter query by selected status or assigned user
function wppm_project_filter_query($query) {
    global $pagenow, $typenow;
    if ($typenow === 'wppm_project' && $pagenow === 'edit.php' && $query->is_main_query()) {
        // Status filter
        if (!empty($_GET['_wppm_project_status'])) {
            $query->set('meta_query', array(
                array(
                    'key' => '_wppm_project_status',
                    'value' => $_GET['_wppm_project_status'],
                    'compare' => '='
                )
            ));
        }
        // Assigned User filter
        if (!empty($_GET['_wppm_project_assigned'])) {
            $meta_query = $query->get('meta_query') ?: [];
            $meta_query[] = array(
                'key' => '_wppm_project_assigned',
                'value' => intval($_GET['_wppm_project_assigned']),
                'compare' => '='
            );
            $query->set('meta_query', $meta_query);
        }
    }
}
add_action('pre_get_posts', 'wppm_project_filter_query');



// Remove Comments column from Project CPT list table
function wppm_remove_project_comments_column($columns) {
    if (isset($columns['comments'])) {
        unset($columns['comments']);
    }
    return $columns;
}
add_filter('manage_wppm_project_posts_columns', 'wppm_remove_project_comments_column');