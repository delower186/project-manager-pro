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

    $due_date  = get_post_meta($post->ID, '_projmanpro_project_due_date', true);
    $assigned  = get_post_meta($post->ID, '_projmanpro_project_assigned', true);

    // Get users
    $users = get_users();

    // Check if project has incomplete tasks (meta for project, taxonomy for status)
    $incomplete_tasks = new WP_Query([
        'post_type'      => 'projmanpro_task',
        'posts_per_page' => 1,
        'fields'         => 'ids', // ✅ Faster, only fetch IDs
        'meta_query'     => [
            [
                'key'     => '_projmanpro_related_project',
                'value'   => $post->ID,
                'compare' => '='
            ]
        ],
        'tax_query'      => [
            [
                'taxonomy' => 'projmanpro_task_status',
                'field'    => 'slug', // or 'term_id' if using IDs
                'terms'    => ['pending', 'in_progress'],
                'operator' => 'IN'
            ]
        ],
    ]);




    $disable_completed = $incomplete_tasks->found_posts > 0 ? 'disabled' : '';
    $completed_note = $disable_completed ? ' (Cannot complete, tasks pending)' : '';
    ?>
    <p>
        <label>Status:</label><br>
        <select name="projmanpro_project_status">
            <?php 
                $terms   = get_terms(['taxonomy' => 'projmanpro_project_status', 'hide_empty' => false]);
                $current = wp_get_post_terms($post->ID, 'projmanpro_project_status', ['fields' => 'ids']);
                $current = $current ? $current[0] : '';

                foreach ($terms as $term) {
                    if($term->name !== 'completed'){
                        echo '<option value="' . esc_attr($term->term_id) . '" ' . selected($current, $term->term_id, false) . '>' . esc_html($term->name) . '</option>';
                    }else{
                        echo '<option value="' . esc_attr($term->term_id) . '" ' . selected($current, $term->term_id, false) . ' ' .esc_html($disable_completed). '>' . esc_html($term->name) .' ' .esc_html($completed_note). '</option>';
                    }
                }
            ?>
        </select>
    </p>

    <p>
        <label>Priority:</label><br>
        <select name="projmanpro_project_priority">
            <?php 
                $terms   = get_terms(['taxonomy' => 'projmanpro_project_priority', 'hide_empty' => false]);
                $current = wp_get_post_terms($post->ID, 'projmanpro_project_priority', ['fields' => 'ids']);
                $current = $current ? $current[0] : '';

                foreach ($terms as $term) {
                    echo '<option value="' . esc_attr($term->term_id) . '" ' . selected($current, $term->term_id, false) . '>' . esc_html($term->name) . '</option>';
                }
            ?>
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

    // Save taxonomy terms
    if (isset($_POST['projmanpro_project_status'])) {
        wp_set_post_terms($post_id, [(int) $_POST['projmanpro_project_status']], 'projmanpro_project_status', false);
    }
    if (isset($_POST['projmanpro_project_priority'])) {
        wp_set_post_terms($post_id, [(int) $_POST['projmanpro_project_priority']], 'projmanpro_project_priority', false);
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
            $terms = wp_get_post_terms($post_id, 'projmanpro_project_status');
            $status = $terms[0]->slug; 
            $color = 'gray';
            if ($status === 'pending') $color = '#ff9800';
            elseif ($status === 'in_progress') $color = '#2196f3';
            elseif ($status === 'completed') $color = '#4caf50';
            echo '<span style="display:inline-block;padding:2px 6px;border-radius:4px;background:' . esc_attr($color) . ';color:#fff;font-weight:bold;">' . esc_html(ucfirst($status)) . '</span>';
            break;

        case 'priority':
            $terms = wp_get_post_terms($post_id, 'projmanpro_project_priority');
            $priority = $terms[0]->slug;
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
    if (!is_admin() || !$query->is_main_query() || $query->get('post_type') !== 'projmanpro_project') {
        return;
    }

    $orderby = $query->get('orderby');

    if ($orderby === 'status') {
        $query->set('orderby', 'projmanpro_project_status');
    } 
    elseif ($orderby === 'priority') {
        $query->set('orderby', 'projmanpro_project_priority');
    }
    elseif ($orderby === 'due_date') {
        $query->set('meta_key', '_projmanpro_project_due_date');
        $query->set('orderby', 'meta_value');
    }
    elseif ($orderby === 'assigned') {
        $query->set('meta_key', '_projmanpro_project_assigned');
        $query->set('orderby', 'meta_value_num');
    }
}
add_action('pre_get_posts', 'projmanpro_project_orderby');


// Order By columns
add_filter('posts_clauses', function ($clauses, $query) {
    global $wpdb;

    if (!is_admin() || !$query->is_main_query() || $query->get('post_type') !== 'projmanpro_project') {
        return $clauses;
    }

    $orderby = $query->get('orderby');

    if ($orderby === 'projmanpro_project_status' || $orderby === 'projmanpro_project_priority') {
        $taxonomy = ($orderby === 'projmanpro_project_status') ? 'projmanpro_project_status' : 'projmanpro_project_priority';

        // Join term tables dynamically based on taxonomy
        $clauses['join'] .= "
            LEFT JOIN {$wpdb->term_relationships} AS tr ON ({$wpdb->posts}.ID = tr.object_id)
            LEFT JOIN {$wpdb->term_taxonomy} AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = '{$taxonomy}')
            LEFT JOIN {$wpdb->terms} AS t ON (tt.term_id = t.term_id)
        ";

        // Order by term name
        $clauses['orderby'] = "t.name " . ($query->get('order') === 'DESC' ? 'DESC' : 'ASC');
        $clauses['groupby'] = "{$wpdb->posts}.ID"; // Prevent duplicate rows
    }

    return $clauses;
}, 10, 2);



// Add Filters for Projects Table
// Add dropdown filters above Projects table
function projmanpro_project_filters() {
    global $typenow;
    if ($typenow !== 'projmanpro_project') {
        return;
    }

    // Verify nonce before reading $_GET
    $nonce_valid = (
        isset($_GET['projmanpro_filter_nonce']) &&
        wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['projmanpro_filter_nonce'])), 'projmanpro_filter_projects')
    );

    // --- Status Filter (Taxonomy) ---
    $current_status = ($nonce_valid && isset($_GET['projmanpro_project_status']))
        ? sanitize_text_field(wp_unslash($_GET['projmanpro_project_status']))
        : '';

    $statuses = get_terms([
        'taxonomy'   => 'projmanpro_project_status',
        'hide_empty' => false,
    ]);

    echo '<select name="projmanpro_project_status"><option value="">All Statuses</option>';
    if (!is_wp_error($statuses)) {
        foreach ($statuses as $status) {
            printf(
                '<option value="%s"%s>%s</option>',
                esc_attr($status->slug),
                selected($current_status, $status->slug, false),
                esc_html($status->name)
            );
        }
    }
    echo '</select>';

    // --- Priority Filter (Taxonomy) ---
    $current_priority = ($nonce_valid && isset($_GET['projmanpro_project_priority']))
        ? sanitize_text_field(wp_unslash($_GET['projmanpro_project_priority']))
        : '';

    $priorities = get_terms([
        'taxonomy'   => 'projmanpro_project_priority',
        'hide_empty' => false,
    ]);

    echo '<select name="projmanpro_project_priority"><option value="">All Priorities</option>';
    if (!is_wp_error($priorities)) {
        foreach ($priorities as $priority) {
            printf(
                '<option value="%s"%s>%s</option>',
                esc_attr($priority->slug),
                selected($current_priority, $priority->slug, false),
                esc_html($priority->name)
            );
        }
    }
    echo '</select>';

    // --- Assigned User Filter (Meta) ---
    $current_user = ($nonce_valid && isset($_GET['_projmanpro_project_assigned']))
        ? intval($_GET['_projmanpro_project_assigned'])
        : '';

    $users = get_users();

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

    // Nonce field
    wp_nonce_field('projmanpro_filter_projects', 'projmanpro_filter_nonce');
}
add_action('restrict_manage_posts', 'projmanpro_project_filters');


// Apply filters to admin query
function projmanpro_project_filter_query($query) {
    global $pagenow, $typenow;

    if ($typenow === 'projmanpro_project' && $pagenow === 'edit.php' && $query->is_main_query()) {

        // Verify nonce
        if (
            !isset($_GET['projmanpro_filter_nonce']) ||
            !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['projmanpro_filter_nonce'])), 'projmanpro_filter_projects')
        ) {
            return; // Nonce invalid → skip filtering
        }

        // --- Taxonomy Query ---
        $tax_query = [];

        // Filter by Status
        if (!empty($_GET['projmanpro_project_status'])) {
            $tax_query[] = [
                'taxonomy' => 'projmanpro_project_status',
                'field'    => 'slug',
                'terms'    => sanitize_text_field(wp_unslash($_GET['projmanpro_project_status'])),
            ];
        }

        // Filter by Priority
        if (!empty($_GET['projmanpro_project_priority'])) {
            $tax_query[] = [
                'taxonomy' => 'projmanpro_project_priority',
                'field'    => 'slug',
                'terms'    => sanitize_text_field(wp_unslash($_GET['projmanpro_project_priority'])),
            ];
        }

        if ($tax_query) {
            $query->set('tax_query', $tax_query);
        }

        // --- Meta Query (Assigned User) ---
        $meta_query = $query->get('meta_query') ?: [];

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