<?php
if (!defined('ABSPATH')) exit;
// Register Projects CPT
function projmanpro_register_project_cpt() {
    $labels = [
      'name' => __('Projects','project-manager-pro') ,
      'singular_name' => __('Project','project-manager-pro') ,
      'add_new'                  => __( 'Add New', 'project-manager-pro' ),
      'add_new_item'             => __( 'Add New Project', 'project-manager-pro' ),
      'edit_item'                => __( 'Edit Project', 'project-manager-pro' ),
      'new_item'                 => __( 'New Project', 'project-manager-pro' ),
      'view_item'                => __( 'View Project', 'project-manager-pro' ),
      'view_items'               => __( 'View Projects', 'project-manager-pro' ),
      'search_items'             => __( 'Search Projects', 'project-manager-pro' ),
      'not_found'                => __( 'No Projects found.', 'project-manager-pro' ),
      'not_found_in_trash'       => __( 'No Projects found in Trash.', 'project-manager-pro' ),
      'parent_item_colon'        => __( 'Parent Projects:', 'project-manager-pro' ),
      'all_items'                => __( 'All Projects', 'project-manager-pro' ),
      'archives'                 => __( 'Project Archives', 'project-manager-pro' ),
      'attributes'               => __( 'Project Attributes', 'project-manager-pro' ),
      'insert_into_item'         => __( 'Insert into Project', 'project-manager-pro' ),
      'uploaded_to_this_item'    => __( 'Uploaded to this Project', 'project-manager-pro' ),
      'featured_image'           => __( 'Featured Image', 'project-manager-pro' ),
      'set_featured_image'       => __( 'Set featured image', 'project-manager-pro' ),
      'remove_featured_image'    => __( 'Remove featured image', 'project-manager-pro' ),
      'use_featured_image'       => __( 'Use as featured image', 'project-manager-pro' ),
      'menu_name'                => __( 'WP Project', 'project-manager-pro' ),
      'filter_items_list'        => __( 'Filter Project list', 'project-manager-pro' ),
      'filter_by_date'           => __( 'Filter by date', 'project-manager-pro' ),
      'items_list_navigation'    => __( 'Projects list navigation', 'project-manager-pro' ),
      'items_list'               => __( 'Projects list', 'project-manager-pro' ),
      'item_published'           => __( 'Project published.', 'project-manager-pro' ),
      'item_published_privately' => __( 'Project published privately.', 'project-manager-pro' ),
      'item_reverted_to_draft'   => __( 'Project reverted to draft.', 'project-manager-pro' ),
      'item_scheduled'           => __( 'Project scheduled.', 'project-manager-pro' ),
      'item_updated'             => __( 'Project updated.', 'project-manager-pro' ),
      'item_link'                => __( 'Project Link', 'project-manager-pro' ),
      'item_link_description'    => __( 'A link to an Project.', 'project-manager-pro' ),
    ];
    $args = [
        'labels' => $labels,
        'description'           => __( 'organize and manage company Projects', 'project-manager-pro' ),
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
    register_post_type('projmanpro_project', $args);
}
add_action('init', 'projmanpro_register_project_cpt');

// Add Meta Boxes for Projects
function projmanpro_project_meta_boxes() {
    add_meta_box('projmanpro_project_details', 'Project Details', 'projmanpro_project_meta_callback', 'projmanpro_project', 'normal', 'default');
}
add_action('add_meta_boxes', 'projmanpro_project_meta_boxes');

function projmanpro_project_meta_callback($post) {
    
     wp_nonce_field('projmanpro_save_project_meta_action', 'projmanpro_project_meta_nonce');

    $status    = get_post_meta($post->ID, '_projmanpro_project_status', true);
    $priority  = get_post_meta($post->ID, '_projmanpro_project_priority', true);
    $due_date  = get_post_meta($post->ID, '_projmanpro_project_due_date', true);
    $assigned  = get_post_meta($post->ID, '_projmanpro_project_assigned', true);

    // Get users
    $users = get_users();

    // Check if project has incomplete tasks
    $incomplete_tasks = new WP_Query([
        'post_type'      => 'projmanpro_task',
        'posts_per_page' => 1,
        'fields'         => 'ids', // ✅ Faster, only fetch IDs
        'meta_query'     => [
            'relation' => 'AND',
            [
                'key'   => '_projmanpro_related_project',
                'value' => $post->ID,
                'compare' => '='
            ],
            [
                'key'     => '_projmanpro_task_status',
                'value'   => ['pending', 'in_progress'],
                'compare' => 'IN'
            ]
        ],
    ]);



    $disable_completed = $incomplete_tasks->found_posts > 0 ? 'disabled' : '';
    $completed_note = $disable_completed ? ' (Cannot complete, tasks pending)' : '';
    ?>
    <p>
        <label>Status:</label><br>
        <select name="projmanpro_project_status">
            <option value="pending" <?php selected($status, 'pending'); ?>>Pending</option>
            <option value="in_progress" <?php selected($status, 'in_progress'); ?>>In Progress</option>
            <option value="completed" <?php selected($status, 'completed'); ?> <?php echo esc_html($disable_completed); ?>>
                Completed<?php echo esc_html($completed_note); ?>
            </option>
        </select>
    </p>

    <p>
        <label>Priority:</label><br>
        <select name="projmanpro_project_priority">
            <option value="low" <?php selected($priority, 'low'); ?>>Low</option>
            <option value="medium" <?php selected($priority, 'medium'); ?>>Medium</option>
            <option value="high" <?php selected($priority, 'high'); ?>>High</option>
        </select>
    </p>

    <p>
        <label>Due Date:</label><br>
        <input type="date" name="projmanpro_project_due_date" value="<?php echo esc_attr($due_date); ?>">
    </p>

    <p>
        <label>Assigned User:</label><br>
        <select name="projmanpro_project_assigned">
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


function projmanpro_save_project_meta($post_id) {
    // 1. Check nonce
    if (!isset($_POST['projmanpro_project_meta_nonce']) ||
        !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['projmanpro_project_meta_nonce'])), 'projmanpro_save_project_meta_action')) {
        return;
    }

    // 2. Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // 3. Check user capability
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // 4. Process & sanitize fields
    if (isset($_POST['projmanpro_project_status'])) {
        update_post_meta($post_id, '_projmanpro_project_status', sanitize_text_field(wp_unslash($_POST['projmanpro_project_status'])));
    }
    if (isset($_POST['projmanpro_project_priority'])) {
        update_post_meta($post_id, '_projmanpro_project_priority', sanitize_text_field(wp_unslash($_POST['projmanpro_project_priority'])));
    }
    if (isset($_POST['projmanpro_project_due_date'])) {
        update_post_meta($post_id, '_projmanpro_project_due_date', sanitize_text_field(wp_unslash($_POST['projmanpro_project_due_date'])));
    }
    if (isset($_POST['projmanpro_project_assigned'])) {
        update_post_meta($post_id, '_projmanpro_project_assigned', intval($_POST['projmanpro_project_assigned']));
    }
}
add_action('save_post', 'projmanpro_save_project_meta');




// custom columns
// Add columns to Projects list
function projmanpro_project_columns($columns) {
    $columns['status']   = 'Status';
    $columns['priority'] = 'Priority';
    $columns['due_date'] = 'Due Date';
    $columns['assigned'] = 'Assigned To'; // new column
    $columns['countdown'] = 'Time Left';
    return $columns;
}
add_filter('manage_projmanpro_project_posts_columns', 'projmanpro_project_columns');

// Render column values for Projects with color badges
function projmanpro_project_column_content($column, $post_id) {
    switch ($column) {
        case 'status':
            $status = get_post_meta($post_id, '_projmanpro_project_status', true);
            $color = 'gray';
            if ($status === 'pending') $color = '#ff9800';
            elseif ($status === 'in_progress') $color = '#2196f3';
            elseif ($status === 'completed') $color = '#4caf50';
            echo '<span style="display:inline-block;padding:2px 6px;border-radius:4px;background:' . esc_attr($color) . ';color:#fff;font-weight:bold;">' . esc_html(ucfirst($status)) . '</span>';
            break;

        case 'priority':
            $priority = get_post_meta($post_id, '_projmanpro_project_priority', true);
            $color = 'gray';
            if ($priority === 'low') $color = '#4caf50';
            elseif ($priority === 'medium') $color = '#ff9800';
            elseif ($priority === 'high') $color = '#f44336';
            echo '<span style="display:inline-block;padding:2px 6px;border-radius:4px;background:' . esc_attr($color) . ';color:#fff;font-weight:bold;">' . esc_html(ucfirst($priority)) . '</span>';
            break;

        case 'due_date':
            echo esc_html(get_post_meta($post_id, '_projmanpro_project_due_date', true));
            break;

        case 'assigned':
            $user_id = get_post_meta($post_id, '_projmanpro_project_assigned', true);
            $user    = $user_id ? get_userdata($user_id) : null;
            echo $user ? esc_html($user->display_name) : '—';
            break;

        case 'countdown':
            $due_date = get_post_meta($post_id, '_projmanpro_project_due_date', true);
            $status   = get_post_meta($post_id,'_projmanpro_project_status',true);
            $color = 'gray';
            if ($status === 'pending') $color = '#ff9800';
            elseif ($status === 'in_progress') $color = '#2196f3';
            elseif ($status === 'completed') $color = '#4caf50';

            if ($due_date) {
                echo '<span class="pmp-countdown" data-due="' . esc_attr($due_date) . '" data-status="'.esc_html($status).'" style="display:inline-block;padding:2px 6px;border-radius:4px;background:' . esc_attr($color) . ';color:#fff;font-weight:bold;">&nbsp;</span>';
            } else {
                echo '—';
            }
            break;
    }
}
add_action('manage_projmanpro_project_posts_custom_column', 'projmanpro_project_column_content', 10, 2);



// Make Projects Columns Sortable
// Make columns sortable in Projects table
function projmanpro_project_sortable_columns($columns) {
    $columns['status']   = 'status';
    $columns['priority'] = 'priority';
    $columns['due_date'] = 'due_date';
    $columns['assigned'] = 'assigned';
    return $columns;
}
add_filter('manage_edit-projmanpro_project_sortable_columns', 'projmanpro_project_sortable_columns');

// Handle sorting by meta
function projmanpro_project_orderby($query) {
    if(!is_admin()) return;

    $orderby = $query->get('orderby');

    if($orderby == 'status') {
        $query->set('meta_key', '_projmanpro_project_status');
        $query->set('orderby', 'meta_value');
    }
    elseif($orderby == 'priority') {
        $query->set('meta_key', '_projmanpro_project_priority');
        $query->set('orderby', 'meta_value');
    }
    elseif($orderby == 'due_date') {
        $query->set('meta_key', '_projmanpro_project_due_date');
        $query->set('orderby', 'meta_value');
    }
    elseif($orderby == 'assigned') {
        $query->set('meta_key', '_projmanpro_project_assigned');
        $query->set('orderby', 'meta_value_num');
    }
}
add_action('pre_get_posts', 'projmanpro_project_orderby');

// Add Filters for Projects Table
// Add dropdown filters above Projects table
function projmanpro_project_filters() {
    global $typenow;
    if ($typenow !== 'projmanpro_project') {
        return;
    }

    // ✅ First verify nonce before reading $_GET
    $nonce_valid = (
        isset($_GET['projmanpro_filter_nonce']) &&
        wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['projmanpro_filter_nonce'])), 'projmanpro_filter_projects')
    );

    // Status filter
    $statuses = [
        'pending'     => 'Pending',
        'in_progress' => 'In Progress',
        'completed'   => 'Completed'
    ];

    $current_status = '';
    if ($nonce_valid && isset($_GET['_projmanpro_project_status'])) {
        $current_status = sanitize_text_field(wp_unslash($_GET['_projmanpro_project_status']));
    }

    // Output dropdown
    echo '<select name="_projmanpro_project_status"><option value="">All Statuses</option>';
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

    $current_user = '';
    if ($nonce_valid && isset($_GET['_projmanpro_project_assigned'])) {
        $current_user = intval($_GET['_projmanpro_project_assigned']);
    }

    echo '<select name="_projmanpro_project_assigned"><option value="">All Users</option>';
    foreach ($users as $user) {
        printf(
            '<option value="%d"%s>%s</option>',
            esc_attr($user->ID),
            selected($current_user, $user->ID, false),
            esc_html($user->display_name)
        );
    }
    echo '</select>';

    // ✅ Add nonce field
    wp_nonce_field('projmanpro_filter_projects', 'projmanpro_filter_nonce');
}
add_action('restrict_manage_posts', 'projmanpro_project_filters');



// Filter query by selected status or assigned user
function projmanpro_project_filter_query($query) {
    global $pagenow, $typenow;

    if ($typenow === 'projmanpro_project' && $pagenow === 'edit.php' && $query->is_main_query()) {

        // ✅ Verify nonce
        if (
            !isset($_GET['projmanpro_filter_nonce']) ||
            !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['projmanpro_filter_nonce'])), 'projmanpro_filter_projects')
        ) {
            return; // Nonce invalid → skip filtering
        }

        $meta_query = $query->get('meta_query') ?: [];

        // Status filter
        if (!empty($_GET['_projmanpro_project_status'])) {
            $meta_query[] = [
                'key'     => '_projmanpro_project_status',
                'value'   => sanitize_text_field(wp_unslash($_GET['_projmanpro_project_status'])),
                'compare' => '='
            ];
        }

        // Assigned User filter
        if (!empty($_GET['_projmanpro_project_assigned'])) {
            $meta_query[] = [
                'key'     => '_projmanpro_project_assigned',
                'value'   => intval($_GET['_projmanpro_project_assigned']),
                'compare' => '='
            ];
        }

        if ($meta_query) {
            $query->set('meta_query', $meta_query);
        }
    }
}
add_action('pre_get_posts', 'projmanpro_project_filter_query');




// Remove Comments column from Project CPT list table
function projmanpro_remove_project_comments_column($columns) {
    if (isset($columns['comments'])) {
        unset($columns['comments']);
    }
    return $columns;
}
add_filter('manage_projmanpro_project_posts_columns', 'projmanpro_remove_project_comments_column');