<?php
// Register Projects CPT
function wppm_register_project_cpt() {
    $labels = [
        'name' => 'Projects',
        'singular_name' => 'Project',
    ];
    $args = [
        'labels' => $labels,
        'public' => false,
        'show_ui' => true,
        'menu_position' => 6,
        'menu_icon' => 'dashicons-portfolio',
        'supports' => ['title', 'editor'],
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
    $assigned  = get_post_meta($post->ID, '_wppm_project_assigned', true); // new field

    $users = get_users();
    ?>
    <p>
        <label>Status:</label><br>
        <select name="wppm_project_status">
            <option value="pending" <?php selected($status, 'pending'); ?>>Pending</option>
            <option value="in_progress" <?php selected($status, 'in_progress'); ?>>In Progress</option>
            <option value="completed" <?php selected($status, 'completed'); ?>>Completed</option>
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

// Render column values for Projects
function wppm_project_column_content($column, $post_id) {
    switch ($column) {
        case 'status':
            echo esc_html(get_post_meta($post_id, '_wppm_project_status', true));
            break;
        case 'priority':
            echo esc_html(get_post_meta($post_id, '_wppm_project_priority', true));
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
            if ($due_date) {
                echo '<span class="wppm-countdown" data-due="' . esc_attr($due_date) . '"></span>';
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


// Enqueue Countdown JS & CSS
function wppm_countdown_assets($hook) {
    // Only load on Projects and Tasks list pages
    if (!in_array($hook, ['edit.php'])) return;

    global $typenow;
    if(!in_array($typenow, ['wppm_project','wppm_task'])) return;

    // JS
    wp_enqueue_script('wppm-countdown', WPPM_PLUGIN_DIR_URL . 'assets/js/countdown.js', ['jquery'], '1.0', true);
    // CSS
    wp_enqueue_style('wppm-countdown', WPPM_PLUGIN_DIR_URL . 'assets/css/countdown.css');
}
add_action('admin_enqueue_scripts', 'wppm_countdown_assets');

